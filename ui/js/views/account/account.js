define([
    'handlebars',
    'backbone',
    'text!../../../html/account/account.html',
], function(Handlebars, Backbone, profileTemplate) {

    var profileView = Backbone.View.extend({
        el: $("body"),
        render: function(obj) {
            var source = $(profileTemplate).html();
            var template = Handlebars.compile(source);
            if (typeof(obj) != 'undefined' && typeof (userData.user) != 'undefined' && typeof (userData.advisor) != 'undefined') {
            	if ($("#currentPage").val() == "myscore" || $("#currentPage").val() == "financialsnapshot") {
                    obj.impersonationMode = true;
                }
            }
            $("#notificationBox").html(template(obj));
            popUpNotification();
        },
        events: {
            "click .cancelAccountPopup": "closeAccountDialog",
            "click .tabNotifications": "openTabNotificationsDialog",
            "click .tabSettings": "openTabSettingsDialog",
            "click .tabSubscription": "openTabSubscriptionDialog",
        },
        openTabNotificationsDialog: function(event) {
            event.preventDefault();
            require(
                    ['views/account/notification'],
                    function(notification) {
                        notification.render();
                        init();
                    }
            );
        },
        openTabSubscriptionDialog: function(event) {
            event.preventDefault();
            require(
                    ['views/account/subscriptiondetails', 'views/account/billingsummary'],
                    function(subscription, billingsummary) {
                        subscription.render();
                        billingsummary.render();
                        init();
                    }
            );
        },
        openTabSettingsDialog: function(event) {
            event.preventDefault();
            require(
                    ['views/account/settings', 'views/account/credentials'],
                    function(settings, credentials) {
                        settings.render();
                        credentials.render();
                        init();
                    }
            );
        },
        openTabPhotoDialog: function(event) {
            event.preventDefault();
            require(
                    ['views/account/settings', 'views/account/photo'],
                    function(settings, photo) {
                        settings.render();
                        photo.render();
                        init();
                        $("#tabCredentials").removeClass("selected");
                        $("#tabPhoto").addClass("selected");
                    }
            );
        },
        initialize: function() {
        },
        // use this for close overlay after click close(x) link.
        closeAccountDialog: function(event) {
            event.preventDefault();
            removeLayover();
        }
    });
    return new profileView;
});