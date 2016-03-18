define([
    'handlebars',
    'backbone',
    'text!../../../html/account/billingsummary.html',
    'text!../../../html/account/creditcard.html'
], function(Handlebars, Backbone, billingSummaryTemplate, creditcardTemplate) {

    var infoView = Backbone.View.extend({
        el: $("body"),
        render: function() {
            $.ajax({
                url: getSubscriptionURL,
                type: 'GET',
                dataType: "JSON",
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                    if (data.status == "OK") {
                        var Month = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                        timeoutPeriod = defaultTimeoutPeriod;
                        var subscriptionCreated = new Date(data.subscription.created);
                        var intMonth = subscriptionCreated.getMonth();
                        var browserCreatedDate = Month[intMonth] + " " + subscriptionCreated.getDate() + ", " + subscriptionCreated.getFullYear();
                        data.subscription.created = browserCreatedDate;

                        var subscriptionStart = new Date(data.subscription.start);
                        var intMonth = subscriptionStart.getMonth();
                        var browserStartDate = Month[intMonth] + " " + subscriptionStart.getDate() + ", " + subscriptionStart.getFullYear();
                        data.subscription.start = browserStartDate;

                        var subscriptionEnd = new Date(data.subscription.end);
                        var intMonth = subscriptionEnd.getMonth();
                        var browserEndDate = Month[intMonth] + " " + subscriptionEnd.getDate() + ", " + subscriptionEnd.getFullYear();
                        data.subscription.end = browserEndDate;

                        var source = $(billingSummaryTemplate).html();
                        var template = Handlebars.compile(source);
                        $("#subscriptionDetails").html(template(data));
                        $("#summaryContent").removeClass("hdn");
                        $("#openCancelSubscriptionBlock").removeClass('hdn');
                    }
                    else if (data.status == "ERROR") {
                        var source = $(billingSummaryTemplate).html();
                        var template = Handlebars.compile(source);
                        $("#subscriptionDetails").html(template(data));
                        $("#summaryContent").addClass('hdn');
                        $("#cancelSubscription").addClass('hdn');
                        $("#summaryMessage").removeClass('hdn');
                        $("#summaryMessage").removeClass('hdn');
                        $("#summaryMessage").show();
                        if (typeof (data.hasSubscription) != 'undefined' && data.hasSubscription) {
                            $("#showSummarySubscriptionLink").addClass('hdn');
                        } else {
                            $("#showSummarySubscriptionLink").removeClass('hdn');
                        }
                   }
                }
            });
            init();
        },
        events: {
            "click #openCancelSubscription": "fnOpenCancelPage",
            "click #cancelSubscription": "fnCancelSubscription",
            "click #backToSummary": "fnBackToSummary",
            "click #openSubscriptionDialog": "fnOpenSubscriptionDialog",
        },
        initialize: function() {
        },
        fnOpenCancelPage: function(event) {
            event.preventDefault();
            $("#summaryContent").addClass('hdn');
            $("#summaryMessage").addClass('hdn');
            $("#openCancelSubscriptionBlock").addClass('hdn');
            $("#openCancelSubscriptionPage").addClass('hdn');
            $("#cancelSubscriptionPage").removeClass('hdn');
            $("#cancelSubscription").removeClass('hdn');
            $("#backToSummary").removeClass('hdn');
        },
        fnBackToSummary: function(event) {
            event.preventDefault();
            $("#summaryContent").removeClass('hdn');
            $("#summaryMessage").addClass('hdn');
            $("#openCancelSubscriptionBlock").removeClass('hdn');
            $("#openCancelSubscriptionPage").addClass('hdn');
            $("#cancelSubscriptionPage").addClass('hdn');
            $("#cancelSubscription").removeClass('hdn');
            $("#backToSummary").removeClass('hdn');
        },
        fnCancelSubscription: function() {
            $("#cancelSubscription").attr("disabled", true);
            $.ajax({
                url: cancelSubscriptionURL,
                type: 'POST',
                dataType: "json",
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == "OK") {
                        $("#summaryMessage").html(data.message);
                        $('#summaryMessage').show();
                        $("#cancelSubscriptionPage").addClass('hdn');
                        $("#cancelSubscription").addClass('hdn');
                        $("#backToSummary").addClass('hdn');
                    }
                }
            });
        },
        fnOpenSubscriptionDialog: function(event) {
            event.preventDefault();
            removeLayover();
            window.parent.openSubscriptionDialog();
        }
    });
    return new infoView;
});