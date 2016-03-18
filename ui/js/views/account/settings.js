define([
    'handlebars',
    'backbone',
    'text!../../../html/account/settings.html',
], function(Handlebars, Backbone, profileTemplate) {

    var profileView = Backbone.View.extend({
        el: $("body"),
        render: function(obj) {
		    var source = $(profileTemplate).html();
            var template = Handlebars.compile(source);
            $("#accountDetails").html(template(userData));            
			$(".accOverlayTabOn").removeClass("accOverlayTabOn");
            $("#tabSettings").addClass("accOverlayTabOn");
            $("#tabNotificationsDiv").removeClass("notificationsIconOn");
            $("#tabNotificationsDiv").addClass("notificationsIconOff");
            $("#tabSettingsDiv").addClass("settingsIconOn");
            $("#tabSettingsDiv").removeClass("settingsIconOff");
            $("#tabSubscriptionDiv").addClass("subscriptionIconOff");
            $("#tabSubscriptionDiv").removeClass("subscriptionIconOn");
            if(typeof(userData.advisor) != 'undefined') {
                if(userData.user == undefined || ($("#currentPage").val() != "myscore" && $("#currentPage").val() != "financialsnapshot")) {
                    return false;
                }
                else{
                    userData.user.impersonationMode = true;
                }
            }
            
			timeoutPeriod = defaultTimeoutPeriod;
            init();
        },
        events: {
            "click #tabCredentialsLink": "openTabCredentialsDialog",
            "click #tabCommunicationLink": "openTabCommunicationDialog",
            "click #tabPhotoLink": "openTabPhotoDialog",
            "click #tabDeleteLink": "openTabDeleteDialog",
		},
        openTabCommunicationDialog: function(event) {
            event.preventDefault();
            require(
                    ['views/account/communication'],
                    function(communication) {
                        communication.render();
                        init();
                    }
            );
        },
        openTabCredentialsDialog: function(event) {
            event.preventDefault();
            require(
                    ['views/account/credentials'],
                    function(credentials) {
                        credentials.render();
                        init();
                    }
            );
        },
        openTabPhotoDialog: function(event) {
            event.preventDefault();
			require(
                    ['views/account/photo'],
                    function(photo) {
                        photo.render(userData);
                        init();
                    }
            );
        },
        openTabDeleteDialog: function(event) {
            event.preventDefault();
            require(
                    ['views/account/delete'],
                    function(deleteV) {
                        deleteV.render();
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
    return new profileView;
});