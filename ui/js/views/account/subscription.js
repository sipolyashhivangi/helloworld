define([
    'handlebars',
    'backbone',
    'text!../../../html/account/subscription.html',
], function(Handlebars, Backbone, subscriptionTemplate) {

    var subscriptionView = Backbone.View.extend({
        el: $("#body"),
        render: function() {
            var source = $(subscriptionTemplate).html();
            var template = Handlebars.compile(source);

            var Month = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
            var trialEnd = new Date(userData.advisor.trialend);
            var intMonth = trialEnd.getMonth();
            var browserCreatedDate = Month[intMonth] + " " + trialEnd.getDate() + ", " + trialEnd.getFullYear();
            userData.advisor.trialend = browserCreatedDate;

            $("#comparisonBox").html(template(userData.advisor));
            $("#subscriptionMessage").hide();
            $("#closeSubscriptionButton").hide();
            $("#closeSubscriptionButton").hide();
            $("#cancelSubscribe").removeClass("hdn");
            $("#subscribeButton").removeClass("hdn");

            var today = new Date();
            var newyear = new Date("Thu 01 Jan 2015 00:00:00 UTC");
            if (today < newyear) {
                $("#2014text").removeClass("hdn");
                $("#2015text").addClass("hdn");
                if (trialEnd >= today) {
                    $("#2014trialText").removeClass("hdn");
                }
            }
            else {
                $("#2014text").addClass("hdn");
                $("#2015text").removeClass("hdn");
                if (trialEnd >= today) {
                    $("#2015trialText").removeClass("hdn");
                }
            }
            init();
        },
        events: {
            "click #subscribeButton": "createSubscription",
            "click #cancelSubscribe": "cancelSubscribeForm",
            "click #closeSubscriptionButton": "closeSubscriptionForm",
            "keypress .cczip": "submitCC",
            "keypress .ccamount": "checkCC"
        },
        initialize: function() {
        },
        validated: function(valid) {
            if (valid) {
                this.view.subscribeButton.removeAttr("disabled");
            } else {
                this.view.subscribeButton.attr("disabled", "true");
            }
        },
        checkCC: function(event) {
            $("#ccNumdiv").removeClass("error");
            $("#ccNumbubble").addClass("hdn");
            $("#cvcNumdiv").removeClass("error");
            $("#cvcNumbubble").addClass("hdn");
            $("#expMonthiv").removeClass("error");
            $("#expMonthbubble").addClass("hdn");
            var keycode = (event.keyCode ? event.keyCode : event.which);
            keycode = parseInt(keycode);
            if (keycode == 13) {
                event.preventDefault();
                $("#subscribeButton").click();
            }
            else if ((keycode < 48 || keycode > 57) && event.keyCode != 37 && (keycode != 39 || event.which == 39) && keycode != 44 && keycode != 8 && keycode != 9)
            {
                event.preventDefault();
            }
        },
        submitCC: function(event) {
            $("#ccNumdiv").removeClass("error");
            $("#ccNumbubble").addClass("hdn");
            $("#cvcNumdiv").removeClass("error");
            $("#cvcNumbubble").addClass("hdn");
            $("#expMonthiv").removeClass("error");
            $("#expMonthbubble").addClass("hdn");
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                event.preventDefault();
                $("#subscribeButton").click();
            }
        },
        createSubscription: function(event) {
            event.preventDefault();
            var error = false;

            $("#subscribeButton").attr("disabled", "true");
            var ccNum = $('#ccNum').val();
            var cvcNum = $('#cvcNum').val();
            var expMonth = $('#expMonth').val();
            var expYear = $('#expYear').val();
            var zipCode = $('#zipCode').val();

            Stripe.setPublishableKey(stripe_key);

            // Validate the number:
            if (!Stripe.validateCardNumber($('#ccNum').val())) {
                error = true;
                $("#subscribeButton").removeAttr("disabled");
                $('#ccNumerror').html("Please enter a correct credit card number.");
                $('#ccNumbubble').removeClass("hdn");
                $("#ccNumdiv").addClass('error');
                $("#subscribeButton").removeAttr("disabled");
                PositionErrorMessage("#ccNum", "#ccNumbubble");
                $("#cvcNumdiv").removeClass('error');
                $("#cvcNumdiv").addClass('controls');
                $('#cvcNumbubble').addClass("hdn");
                $("#expMonthdiv").removeClass('error');
                $("#expMonthdiv").addClass('controls');
                $('#expMonthbubble').addClass("hdn");
                return false;
            }

            // Validate the CVC:
            if (!Stripe.validateCVC($('#cvcNum').val())) {
                error = true;
                $("#subscribeButton").removeAttr("disabled");
                $('#cvcNumerror').html("Please enter a correct CVC number.");
                $('#cvcNumbubble').removeClass("hdn");
                $("#cvcNumdiv").addClass('error');
                $("#subscribeButton").removeAttr("disabled");
                PositionErrorMessage("#cvcNum", "#cvcNumbubble");
                $("#ccNumdiv").removeClass('error');
                $("#ccNumdiv").addClass('controls');
                $('#ccNumbubble').addClass("hdn");
                $("#expMonthdiv").removeClass('error');
                $("#expMonthdiv").addClass('controls');
                $('#expMonthbubble').addClass("hdn");
                return false;
            }

            // Validate the expiration:
            if (!Stripe.validateExpiry($('#expMonth').val(), $('#expYear').val())) {
                error = true;
                $("#subscribeButton").removeAttr("disabled");
                $('#expMontherror').html('The expiration date is invalid.');
                $('#expMonthbubble').removeClass("hdn");
                $("#expMonthdiv").addClass('error');
                $("#subscribeButton").removeAttr("disabled");
                PositionErrorMessage("#expMonth", "#expMonthbubble");
                $("#ccNumdiv").removeClass('error');
                $("#ccNumdiv").addClass('controls');
                $('#ccNumbubble').addClass("hdn");
                $("#cvcNumdiv").removeClass('error');
                $("#cvcNumdiv").addClass('controls');
                $('#cvcNumbubble').addClass("hdn");
                return false;
            }

            if (!error) {
                // Get the Stripe token:
                Stripe.createToken({
                    number: ccNum,
                    cvc: cvcNum,
                    exp_month: expMonth,
                    exp_year: expYear,
                    address_zip: zipCode
                }, stripeResponseHandler);
            }

            function stripeResponseHandler(status, response) {
                if (response.error) {
                    require(['views/account/subscription'],
                            function(subscriptionV) {
                                subscriptionV.render();
                                $("#subscriptionMessage").html("There was a processing error.  Please try again later.");
                                $('#subscriptionMessage').show();
                                $('#subscriptionMessage').addClass('redtext');
                            }
                    );
                } else { // No errors, submit the form.
                    var token = response.id;
                    var params = {};
                    params["stripeToken"] = token;
                    params["zipCode"] = zipCode;
                    $.ajax({
                        url: createAdvisorSubscriptionURL,
                        type: 'POST',
                        dataType: "json",
                        data: params,
                        success: function(data) {
                            timeoutPeriod = defaultTimeoutPeriod;
                            $("#subscribeButton").removeAttr("disabled");
                            if (data.status == "ERROR") {
                                require(['views/account/subscription'],
                                        function(subscriptionV) {
                                            subscriptionV.render(data);
                                            if (data.subscription_status == 'already_exists') {
                                                $('#cancelSubscribe').hide();
                                                $('#subscriptionFormContent').hide();
                                                $('#subscribeButton').hide();
                                                $("#subscriptionMessage").html(data.message);
                                                $("#subscriptionMessage").show();
                                                $('#subscriptionMessage').removeClass('hdn');
                                                $('#closeSubscriptionButton').show();
                                                $('#closeSubscriptionButton').removeClass('hdn');
                                            }
                                            else {
                                                $("#subscriptionMessage").html(data.message);
                                                $('#subscriptionMessage').show();
                                                $('#subscriptionMessage').removeClass('hdn');
                                                $('#subscriptionMessage').addClass('redtext');
                                            }
                                        }
                                );
                            }
                            else if (data.status == "OK") {
                                require(['views/account/subscription'],
                                        function(subscriptionV) {
                                            subscriptionV.render(data);
                                            $('#cancelSubscribe').hide();
                                            $('#subscriptionFormContent').hide();
                                            $('#subscribeButton').hide();
                                            $("#subscriptionMessage").html(data.message);
                                            $("#subscriptionMessage").show();
                                            $('#subscriptionMessage').removeClass('hdn');
                                            $('#subscriptionMessage').addClass('fiftypx');
                                            $('#subscriptionMessage').addClass('greentext');
                                            $('#closeSubscriptionButton').show();
                                            $('#closeSubscriptionButton').removeClass('hdn');
                                        }
                                );
                            }
                        },
                        error: function(data) {
                            timeoutPeriod = defaultTimeoutPeriod;
                            $("#subscribeButton").removeAttr("disabled");
                        }
                    });
                }
            }
            return false;
        },
        cancelSubscribeForm: function(event) {
            event.preventDefault();
            removeLayover();
            $("#comparisonBox").hide();
        },
        closeSubscriptionForm: function(event) {
            event.preventDefault();
            removeLayover();
            $("#comparisonBox").hide();
            window.location = "./dashboard";
        }
    });

    return new subscriptionView;
});
