define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/profile.html',
], function(Handlebars, Backbone, profileTemplate) {

    var profileView = Backbone.View.extend({
        el: $("#profileContents"),
        render: function(obj) {
            var source = $(profileTemplate).html();
            var template = Handlebars.compile(source);
            if(typeof(userData.advisor) != 'undefined') {
                userData.user.impersonationMode = true;
                if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                    userData.user.permission = true;
                }
            }
            $("#profileContents").html(template(userData.user));
        },
        events: {
            "click .cancelProfilePopup": "closeProfileDialog",
            "click .tabFinancial": "openTabFinancialDialog",
            "click .tabAbout": "openTabAboutDialog",
            "click .tabGoals": "openTabGoalsDialog",
        },
        openTabGoalsDialog: function(event) {
            event.preventDefault();

            require(
                    ['views/profile/profile', 'views/profile/goals'],
                    function(profileV, goalsV) {
                        if (financialData.accountsdownloading)
                        {
                            $.ajax({
                                url: getAllItem,
                                type: 'GET',
                                dataType: "json",
                                cache: false,
                                beforeSend: function(request) {
                                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                                },
                                success: function(data) {
									timeoutPeriod = defaultTimeoutPeriod;
                                    if (data.status == "OK") {
                                        fnUpdateAllData(data);
                                        fnUpdateFinancialData();
                                        profileV.render();
                                        goalsV.render("#profileDetails");
                                        $(".goalsIconOff").addClass("goalsIconOn");
                                        $(".accOverlayTabOn").removeClass('accOverlayTabOn');
                                        $(".goalsIconOn").removeClass("goalsIconOff");
                                        $(".goalsIconOn").parents("li").addClass('accOverlayTabOn');
                                        $(".stepOneHeader").hide();
                                        popUpProfile();
                                        init();
                                    }
                                }
                            });
                        }
                        else
                        {
                            profileV.render();
                            goalsV.render("#profileDetails");
                            $(".goalsIconOff").addClass("goalsIconOn");
                            $(".accOverlayTabOn").removeClass('accOverlayTabOn');
                            $(".goalsIconOn").removeClass("goalsIconOff");
                            $(".goalsIconOn").parents("li").addClass('accOverlayTabOn');
                            $(".stepOneHeader").hide();
                            popUpProfile();
                            init();
                        }
                    }
            );
        },
        openTabAboutDialog: function(event) {
            event.preventDefault();
            require(
                    ['views/profile/profile', 'views/user/createAccountStepOne'],
                    function(profileV, stepOneV) {
                        profileV.render();
                        stepOneV.render("#profileDetails", "about");
                        popUpProfile();
                        init();
                    }
            );
        },
        openTabFinancialDialog: function(event) {
            event.preventDefault();

            require(
                    ['views/profile/profile', 'views/profile/financialdetails'],
                    function(profileV, financialDetailsV) {
                        profileV.render();
                        financialDetailsV.render();
                        if(typeof(userData.advisor) != 'undefined') {
                            userData.user.impersonationMode = true;
                            if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                                $("#incomeLink").click();//to show income tab default.
                                $("#incomeSection").addClass("posRel profileNavOn");
                                $("#incomeSelected").removeClass("hdn");
                                $("#incomeUnselected").addClass("hdn");
                            }
                            $("#connectLink").click();
                            $("#connectSection").addClass("posRel profileNavOn");
                            $("#connectSelected").removeClass("hdn");
                            $("#connectUnselected").addClass("hdn");
                        }else{
                             $("#connectLink").click();
                            $("#connectSection").addClass("posRel profileNavOn");
                            $("#connectSelected").removeClass("hdn");
                            $("#connectUnselected").addClass("hdn");
                        }
                        popUpProfile();
                        init();
                    }
            );
        },
        initialize: function() {
//                this.signupButton = $("#signup");
        },
        // use this for close overlay after click close(x) link.
        closeProfileDialog: function(event) {
            event.preventDefault();
            removeLayover();

            $.ajax({
                url: getNotificationDataURL + "?forceUser=" + forceUserNotifications,
                type: 'GET',
                dataType: "json",
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
					timeoutPeriod = defaultTimeoutPeriod;
                    if (data.total != 0) {
                        $('#headNotifyTags').html(data.total);
                        $('#menuNotifyTags').html(data.total);
                    }
                }
            });
            if (window.location.pathname.indexOf('financialsnapshot') != -1)
            {
                require(
                        ['views/user/financialsnapshot'],
                        function(financialV) {
                            financialV.render();
                        }
                );
            }
            else if (window.location.pathname.indexOf('myscore') != -1)
            {
                require(
                        ['views/user/myscore'],
                        function(myscoreV) {
                            myscoreV.render();
                        }
                );
            }
        },
    });
    return new profileView;
});