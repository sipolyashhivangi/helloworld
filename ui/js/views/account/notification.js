define([
    'handlebars',
    'backbone',
    'text!../../../html/account/notification.html',
], function(Handlebars, Backbone, notificationTemplate) {

    var notificationView = Backbone.View.extend({
        el: $("#body"),
        render: function() {
            $.ajax({
                url: getNotificationDataURL + "?forceUser=" + forceUserNotifications,
                dataType: "JSON",
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {

                    var Month = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                    var Day = new Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
                    if (data.total > 0) {
                        for (var i = 0; i < data.total; i++) {
                            var serverTime = new Date(data.notification[i].created);
                            var intDay = serverTime.getDay();
                            var intMonth = serverTime.getMonth();
                            var hours = serverTime.getHours();
                            var minutes = serverTime.getMinutes();
                            var ampm = hours >= 12 ? 'pm' : 'am';
                            hours = hours % 12;
                            hours = hours ? hours : 12; // the hour '0' should be '12'
                            minutes = minutes < 10 ? '0' + minutes : minutes;
                            var strTime = hours + ':' + minutes + ' ' + ampm;

                            var browserFullDate = Day[intDay] + " " + Month[intMonth] + " " + serverTime.getDate() + ", " + serverTime.getFullYear() + ", " + strTime;
                            data.notification[i].created = browserFullDate;
                        }
                    }
                    timeoutPeriod = defaultTimeoutPeriod;
                    var source = $(notificationTemplate).html();
                    var template = Handlebars.compile(source);
                    if (data.urole == "777") {
                        $('.admin').show();
                        $(".userShow").hide();
                    } else {
                        $('.admin').hide();
                        $(".userShow").show();
                    }
                     if(typeof(userData.advisor) != 'undefined' && typeof(userData.user) != 'undefined' 
                      && ($("#currentPage").val() == "myscore" || $("#currentPage").val() == "financialsnapshot")) {
                        userData.user.impersonationMode = true;
                        if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                            var totalnotification = data.notification.length;
                            for(var i =0; i< totalnotification; i++){
                                data.notification[i].permission = true;
                            }
                        }
                    }
                   
                    $("#accountDetails").html(template(data));
					$(".accOverlayTabOn").removeClass("accOverlayTabOn");
                    $("#tabNotifications").addClass("accOverlayTabOn");                    
                    $("#tabNotificationsDiv").addClass("notificationsIconOn");
                    $("#tabNotificationsDiv").removeClass("notificationsIconOff");
                    $("#tabSettingsDiv").removeClass("settingsIconOn");
                    $("#tabSettingsDiv").addClass("settingsIconOff");
                    $("#tabSubscriptionDiv").addClass("subscriptionIconOff");
                    $("#tabSubscriptionDiv").removeClass("subscriptionIconOn");
                }
            });
        },
        events: {
            "click .retryNotification": "fnRetryNotification",
            "click .deleteHarvesting": "fnAccountRemove",
            "click .notificationRead": "fnNotificationDelete",
            "click .refreshAccounts": "fnRefreshAccounts",
            "click .tabPhoto": "fnShowPhotoTab",
            "click .aboutyou": "fnShowAboutTab",
            "click .creditcard": "fnUpdateCreditCard"
        },
        fnNotificationDelete: function(event) {
            event.preventDefault();
            var itemId = event.target.id;
            var key = itemId.substring(0, itemId.indexOf("DelNotification"));
            var formValues = {
                id: key,
                forceUser: forceUserNotifications
            };
            $.ajax({
                url: notificationUpdateUrl,
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
                    if (parseInt(data.notification) == 0) {
                        $("#clearNotificationDiv").addClass("hdn");
                        $("#clearNotificationDivs").addClass("hdn");
                        $("#noNotificationDiv").removeClass("hdn");
                    }
                }
            });
        },
        fnRefreshAccounts: function(event) {
            event.preventDefault();
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
        fnRetryNotification: function(event) {
            event.preventDefault();
            var itemIdCid = event.target.id;
            var key = itemIdCid.substring(0, itemIdCid.indexOf("RetryNotification"));
            $(".cancelAccountPopup").click();
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
            event.preventDefault();
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
        fnShowPhotoTab: function(event) {
            event.preventDefault();
            $(".cancelAccountPopup").click();
            require(
                    ['views/account/account','views/account/settings', 'views/account/photo'],
                    function(account,settings,photo) {
                        account.render(userData);
                        settings.render();
                        photo.render();
                        $("#tabCredentials").removeClass("selected");                        
                        $("#tabPhoto").addClass("selected");
                        init();
                    }
            );
        },
        fnShowAboutTab: function(event) {
            event.preventDefault();
            removeLayover();
            require(
                    ['views/advisor/updateprofile', 'views/advisor/createAccountStepOne'],
                    function(profileV, stepOneV) {
                        profileV.render();
                        stepOneV.render("#profileDetails", "about");
                        popUpProfile();
                        init();
                    }
            );
        },
        fnUpdateCreditCard: function(event) {
            event.preventDefault();
            removeLayover();
            require(
                ['views/account/account','views/account/subscriptiondetails','views/account/creditcard'],
                function(account,subscriptiondetails,creditcard) {
                    account.render(userData);
                    subscriptiondetails.render();
                    creditcard.render();
                    $("#tabCreditCardLink").click();
                    init();
                }
            );
        }
    });
    return new notificationView;
});
