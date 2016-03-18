define([
    'handlebars',
    'backbone',
    'text!../../../html/account/creditcard.html',
], function(Handlebars, Backbone, creditcardTemplate) {

    var creditcardView = Backbone.View.extend({
        el: $("body"),
        render: function(showForm) {
            $.ajax({
                url: getCreditCardUrl,
                type: 'GET',
                dataType: "JSON",
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                    if (data.status == "OK") {
                        timeoutPeriod = defaultTimeoutPeriod;
                        var source = $(creditcardTemplate).html();
                        var template = Handlebars.compile(source);
                        $("#subscriptionDetails").html(template(data));
                        $("#creditcardContent").removeClass("hdn");
                        $("#formUpdateCreditCard").addClass('hdn');
                        $("#openUpdateCreditCardPage").removeClass("hdn");
                        $("#creditcardMessage").addClass('hdn');
                        if(typeof(showForm)!= 'undefined' && showForm) {
                            $("#openUpdateCreditCardPage").click();
                        }
                    }
                    else if (data.status == "ERROR") {
                        timeoutPeriod = defaultTimeoutPeriod;
                        var source = $(creditcardTemplate).html();
                        var template = Handlebars.compile(source);
                        $("#subscriptionDetails").html(template(data));
                        $("#creditcardContent").addClass('hdn');
                        $("#creditcardMessage").removeClass("hdn");
                        if (typeof (data.hasSubscription) != 'undefined' && data.hasSubscription) {
                            $("#openUpdateCreditCardPage").removeClass("hdn");
                            $("#showCardSubscriptionLink").addClass("hdn");
                        } else {
                            $("#openUpdateCreditCardPage").addClass("hdn");
                            $("#showCardSubscriptionLink").removeClass("hdn");
                        }
                    }
                }
            });
            init();
        },
        events: {
            "click #updateCreditCardButton": "fnUpdateCreditCard",
            "click #openUpdateCreditCardPage": "fnOpenUpdateForm", //update form
            "click #backToUpdateCreditCard": "fnBackToUpdateCreditCard", //update subscription detail
            "keypress .cczip": "submitCC",
            "keypress .ccamount": "checkCC",
            "click #openSubscriptionDialog": "fnOpenSubscriptionDialog"
        },
        initialize: function() {
        },
        fnOpenUpdateForm: function(event) {
            event.preventDefault();
            $("#formUpdateCreditCard").removeClass('hdn');
            $('#updateCreditCard').removeClass('hdn');
            $('#backToBilling').removeClass('hdn');
            $("#creditcardContent").addClass('hdn');
            $("#openUpdateCreditCardPage").addClass("hdn");
            $("#creditcardMessage").addClass("hdn");
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
        fnUpdateCreditCard: function(event) {
            event.preventDefault();
            var error = false;

            $("#updateCreditCardButton").attr("disabled", true);
            var ccNum = $('#ccNum').val();
            var cvcNum = $('#cvcNum').val();
            var expMonth = $('#expMonth').val();
            var expYear = $('#expYear').val();
            var zipCode = $('#zipCode').val();

            Stripe.setPublishableKey(stripe_key);

            // Validate the number:
            if (!Stripe.validateCardNumber($('#ccNum').val())) {
                error = true;
                $("#updateCreditCardButton").removeAttr("disabled");
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
                $("#updateCreditCardButton").removeAttr("disabled");
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
                $("#updateCreditCardButton").removeAttr("disabled");
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
                    event.preventDefault();
                    $("#creditcardMessage").html("There was a processing error.  Please try again later.");
                    $("#creditcardMessage").html(data.message);
                    $("#creditcardMessage").show();
                    $("#creditcardContent").addClass('hdn');
                    $("#formUpdateCreditCard").addClass('hdn');
                    $("#openUpdateCreditCardPage").addClass("hdn");
                    $("#creditcardMessage").removeClass('hdn');
                    $("#updateCreditCardButton").removeAttr("disabled");
                } else { // No errors, submit the form.
                    var token = response.id;
                    var params = {};
                    params["stripeToken"] = token;
                    params["zipCode"] = zipCode;
                    $.ajax({
                        url: updateCreditCardUrl,
                        type: 'POST',
                        dataType: "json",
                        data: params,
                        success: function(data) {
                            $("#updateCreditCardButton").removeAttr("disabled");
                            if (data.status == "OK") {
                                timeoutPeriod = defaultTimeoutPeriod;
                                $.ajax({
                                    url: advisorDetails,
                                    cache: false,
                                    type: 'POST',
                                    dataType: "json",
                                    data: {sort_order: 'ASC',
                                        sort_by: 'status',
                                        current_page: '1',
                                        tabname: 'clientlist',
                                    },
                                    success: function(getAll) {
                                        timeoutPeriod = defaultTimeoutPeriod;
                                        require(['views/advisor/advisorhome'],
                                        function(advisorhomeV) {
                                            advisorhomeV.render(getAll);
                                            if (getAll.status == "OK") {
                                                $('.pagination').html(getAll.pagination);
                                                $('#allAdvisors').html(getAll.userSortdata);
                                                $('#total_clients').html('(' + getAll.totalClient + ')');
                                                $(".rppDD1").show();

                                            } else if (getAll.status == "ERROR") {
                                                $('.norecorderror').show();
                                                $('.norecorderror').html(getAll.msg);
                                                $('.sorting').removeClass('sorting');
                                            }
                                        });
                                        var source = $(creditcardTemplate).html();
                                        var template = Handlebars.compile(source);
                                        $("#subscriptionDetails").html(template(data));
                                        $("#creditcardContent").removeClass("hdn");
                                        $("#openUpdateCreditCardPage").removeClass("hdn");
                                        $("#formUpdateCreditCard").addClass('hdn');
                                        $("#creditcardMessage").removeClass('hdn');
                                        $("#creditcardMessage").addClass('greentext');
                                    }
                                });
                            }
                            else if (data.status == "ERROR") {
                                timeoutPeriod = defaultTimeoutPeriod;
                                var source = $(creditcardTemplate).html();
                                var template = Handlebars.compile(source);
                                $("#subscriptionDetails").html(template(data));
                                $("#creditcardContent").addClass('hdn');
                                $("#openUpdateCreditCardPage").addClass("hdn");
                                $("#formUpdateCreditCard").removeClass('hdn');
                                $("#creditcardMessage").html(data.message);
                                $("#creditcardMessage").removeClass("hdn");
                                $("#creditcardMessage").addClass('redtext');
                                $("#creditcardMessage").show();
                            }
                        },
                        error: function(data) {
                            $("#updateCreditCardButton").removeAttr("disabled");
                            timeoutPeriod = defaultTimeoutPeriod;
                        }
                    });
                }
            }
            return false;
        },
        fnBackToUpdateCreditCard: function(event) {
            timeoutPeriod = defaultTimeoutPeriod;
            require(['views/account/creditcard'],
                function(creditcardV) {
                    creditcardV.render();
                });
        },
        fnOpenSubscriptionDialog: function(event) {
            event.preventDefault();
            removeLayover();
            window.parent.openSubscriptionDialog();
        }
    });
    return new creditcardView;
});