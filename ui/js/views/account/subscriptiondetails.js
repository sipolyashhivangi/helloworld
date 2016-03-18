define([
    'handlebars',
    'backbone',
    'text!../../../html/account/subscriptiondetails.html',
], function(Handlebars, Backbone, profileTemplate) {

    var subscriptionDetailsView = Backbone.View.extend({
        el: $("body"),
        render: function(obj) {
            var source = $(profileTemplate).html();
            var template = Handlebars.compile(source);
            $("#accountDetails").html(template(userData));
            $(".accOverlayTabOn").removeClass("accOverlayTabOn");
            $("#tabSubscription").addClass("accOverlayTabOn");
            $("#tabNotificationsDiv").removeClass("notificationsIconOn");
            $("#tabNotificationsDiv").addClass("notificationsIconOff");
            $("#tabSettingsDiv").removeClass("settingsIconOn");
            $("#tabSettingsDiv").addClass("settingsIconOff");
            $("#tabSubscriptionDiv").removeClass("subscriptionIconOff");
            $("#tabSubscriptionDiv").addClass("subscriptionIconOn");
            if (typeof (userData.advisor) != 'undefined') {
                if (userData.user == undefined || ($("#currentPage").val() != "myscore" && $("#currentPage").val() != "financialsnapshot")) {
                    return false;
                }
                else {
                    userData.user.impersonationMode = true;
                }
            }

            timeoutPeriod = defaultTimeoutPeriod;
            init();
        },
        events: {
            "click #tabBillingSummaryLink": "openTabBillingSummary",
            "click #tabCreditCardLink": "openTabCreditCard",
            "click #tabBillingHistoryLink": "openTabBillingHistory",
        },
        openTabBillingSummary: function(event) {
            event.preventDefault();
            require(
                    ['views/account/billingsummary'],
                    function(billingsummary) {
                        billingsummary.render();
                        init();
                    }
            );
        },
        openTabCreditCard: function(event) {
            event.preventDefault();
            require(
                    ['views/account/creditcard'],
                    function(creditcard) {
                        creditcard.render();
                        init();
                    }
            );
        },
        openTabBillingHistory: function(event) {
            event.preventDefault();
            require(
                    ['views/account/billinghistory'],
                    function(billinghistory) {
                        billinghistory.render();
                        init();
                    }
            );
        },
        initialize: function() {
        },
        // use this for close overlay after click close(x) link.
        closeAccountDialog: function(event) {
            event.preventDefault();
            removeLayover();
        },
    });

    return new subscriptionDetailsView;
});