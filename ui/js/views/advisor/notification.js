define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/notification.html',
], function(Handlebars, Backbone, notificationTemplate) {

    var notificationView = Backbone.View.extend({
        el: $("#body"),
        render: function(id) {
		    $.ajax({
                url: getAdvisorNotificationDataURL,
                dataType: "JSON",
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
				timeoutPeriod = defaultTimeoutPeriod;
					var source = $(notificationTemplate).html();
					var template = Handlebars.compile(source);
					$("#notificationContents").html(template(data));
					popUpNotification(data);
                }
            });
        },
        events: {
            "click .retryHarvesting": "fnRetryHarvesting",
            "click .deleteHarvesting": "fnAccountRemove",
            "click .notificationRead": "fnNotificationDelete",
            "click .refreshAccounts": "fnRefreshAccounts",
        },
        fnNotificationDelete: function(event) {
            var itemId = event.target.id;
            var key = itemId.substring(0, itemId.indexOf("DelNotification"));
            var formValues = {
                id: key
            };
            $.ajax({
                url: notificationAdvisorUpdateUrl,
                type: 'GET',
                dataType: "json",
                data: formValues,
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
				    timeoutPeriod = defaultTimeoutPeriod;
                    $("#" + key + "accDatabox").hide();
                    $('#headNotifyTags').html(data.notification);
                    $('#menuNotifyTags').html(data.notification);
                    if(parseInt(data.notification) == 0) {
                    	$("#clearNotificationDiv").addClass("hdn");
						$("#clearNotificationDivs").addClass("hdn");
                    	$("#noNotificationDiv").removeClass("hdn");
                    }
                }
            });
        },
        fnRefreshAccounts: function(event) {
            var itemIdCid = event.target.id;
            var key = itemIdCid.substring(0, itemIdCid.indexOf("RefreshAccounts"));
            $(".cancelNotificationPopup").click();
            require(
                    ['views/profile/profile', 'views/profile/financialdetails'],
                    function(profileV, financialdetailsV) {
                        profileV.render();
                        financialdetailsV.render();
                        currentNotificationKey = key;
                        $(".tabFinancial").click();
                        init();
                    }
            )
        },
        fnRetryHarvesting: function(event) {
            var itemIdCid = event.target.id;
            var key = itemIdCid.substring(0, itemIdCid.indexOf("RetryHarvesting"));
            $(".cancelNotificationPopup").click();
            require(
                    ['views/profile/accountstatus', 'views/profile/profile', 'views/profile/financialdetails'],
                    function(addAccountV, profileV, financialdetailsV) {
                        profileV.render();
                        financialdetailsV.render();
                        currentNotificationKey = key;
                        $("#connectLink").click();
                        $('#connectSelected').removeClass("hdn");
                        $('#connectUnselected').addClass("hdn");
                        popUpProfile();
                        init();
                        // close this popup and open profilepopup
                    }
            )
        },
        fnAccountRemove: function(event) {
            var itemIdCid = event.target.id;
            var key = itemIdCid.substring(0, itemIdCid.indexOf("DelHarvesting"));

            var splitVal = key.split("#");
            var itemId = splitVal[0];
            var cid = splitVal[1];
            var formValues = {
                fiacctid: itemId
            };

            $.ajax({
                url: accountRemoveUrl,
                type: 'GET',
                dataType: "json",
                data: formValues,
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == "OK") {
                        $("#" + cid + "accDatabox").hide();
                    } else {
                        $("#" + cid + "ErrorMsg").html("Unable to delete");
                    }
                }
            });
        },
    });
    return new notificationView;
});