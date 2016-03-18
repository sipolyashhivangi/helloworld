// Filename: views/base/header

define([
    'handlebars',
    'text!../../../html/base/header.html',
    'text!../../../html/user/actionReport.html',
], function(Handlebars, headerTemplate, congratsTemplate) {
    var headerView = Backbone.View.extend({
        el: $("#body"),
        render: function(data) {
            var source = $(headerTemplate).html();
            var template = Handlebars.compile(source);

            data.emailshort = data.email;
            var maxLength = 15;
            if (data.urole == '999') {
                maxLength = 20;
            }
            if (typeof (data.firstname) != 'undefined' && data.firstname != null && data.firstname.length > maxLength - 1)
            {
                data.lastname = "";
                data.firstname = data.firstname.substr(0, maxLength);
            }
            else if (typeof (data.firstname) != ' undefined' && data.firstname != null
                    && typeof (data.lastname) != ' undefined' && data.lastname != null
                    && data.firstname.length > 0 && data.firstname.length + data.lastname.length > maxLength - 1)
            {
                data.lastname = data.lastname[0];
            }
            else if (typeof (data.lastname) != 'undefined' && data.lastname != null && data.lastname.length > maxLength - 1)
            {
                data.lastname = data.lastname.substr(0, maxLength);
            }
            else if (data.email.length > maxLength)
            {
                data.emailshort = data.email.substr(0, maxLength);
            }
            profileUserData.id = data.id;
            profileUserData.email = data.email;
            profileUserData.firstname = data.firstname;
            profileUserData.lastname = data.lastname;
            profileUserData.retirementstatus = data.retirementstatus;
            profileUserData.connectAccountstatus = data.connectAccountstatus;
            profileUserData.debtstatus = data.debtstatus;
            profileUserData.insurancestatus = data.insurancestatus;

            $("#mainHeader").html(template(data));
            if (data.urole == '999') {
                $("#gnav_user").css('width', '250px');
            }

            if (typeof (userData.advisor) == 'undefined' && typeof (userData.user) != 'undefined' && userData.user.urole == "777") {
                $('#reports').show();
                $('#advisorlists').show();
                $('#specificproducts').show();
                $.ajax({
                    url: unassignedAdvisorCount,
                    dataType: "JSON",
                    cache: false,
                    beforeSend: function(request) {
                        request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                    },
                    success: function(data) {
                        if (data.status == 'ERROR') {
                            timeoutPeriod = 0;
                        }
                        if (data.total != 0) {
                            $('#unassignedNotifyTags').html(data.total);
                        } else {
                            $('#unassignedNotifyTags').html(0);
                        }
                    }
                });
            } else {
                $('#reports').hide();
                $('#specificproducts').hide();
            }

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
                    if (data.status == 'ERROR') {
                        timeoutPeriod = 0;
                    }
                    if (data.total != 0) {
                        $('#headNotifyTags').html(data.total);
                        $('#menuNotifyTags').html(data.total);
                    } else {
                        $('#headNotifyTags').html(0);
                        $('#menuNotifyTags').html(0);
                    }
                }
            });

            if (typeof (userData.user) != 'undefined') {
                this.fnNodeNotification(userData.user.id);
            }

            function updatetimeout() {
                timeoutPeriod--;
                if (timeoutPeriod <= 0 && !timeoutDialogShown)
                {
                    removeLayover();
                    require(
                            ['views/base/timeout'],
                            function(timeout) {
                                timeout.render();
                                popUpActionStep();
                                timeoutDialogShown = true;
                                localStorage[serverSess] = '';
                            }
                    );
                }
            }
            setInterval(updatetimeout, 1000);


            var s = document.createElement('script');
            var code = "(function(){var uv=document.createElement('script');uv.type='text/javascript';uv.async=true;uv.src='//widget.uservoice.com/hQDyHUuR4szDPkQigLGkbw.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(uv,s)})()";
            try {
                s.appendChild(document.createTextNode(code));
                document.body.appendChild(s);
            } catch (e) {
                s.text = code;
                document.body.appendChild(s);
            }

            var s = document.createElement('script');
            var code = "UserVoice = window.UserVoice || [];";
            code += "UserVoice.push(['showTab', 'classic_widget', {";
            code += "mode: 'full',";
            code += "primary_color: '#6763a6',";
            code += "link_color: '#5fa439',";
            code += "default_mode: 'feedback',";
            code += "forum_id: 212424,";
            code += "tab_label: 'Feedback & Support',";
            code += "tab_color: '#6763a6',";
            code += "tab_position: 'middle-right',";
            code += "tab_inverted: false";
            code += "}]);";

            try {
                s.appendChild(document.createTextNode(code));
                document.body.appendChild(s);
            } catch (e) {
                s.text = code;
                document.body.appendChild(s);
            }
        },
        events: {
            "click .financialDetailsHeader": "openFinancialDetailDialog",
            "click .aboutHeader": "openAboutDialog",
            "click .goalsHeader": "openGoalsDialog",
            "click .cancelNotificationPopup": "closeNotificationDialog",
            "click #logout": "fnLogoutUser",
            "click #refresh": "fnRefreshAllAccount",
            "click #notifications": "fnNotification",
            // "click #settings": "fnSettings",
            "click .settings": "fnSettings",
            "click .cancelProfilePopupBox": "fnCloseProfileDialog",
            "click .nextProfilePopupBox": "fnNextProfileDialog",
            "click .addGoals": "openGoalsDialog",
            "click .addDebts": "openDebtsDialog",
            "click .addInsurance": "openInsuranceDialog",
            "click .addAssets": "openAssetsDialog",
            "click .financialDetails": "openFinancialDetailDialog",
            "click #myadvisors": "fnMyadvisors", //function to show list of advisor associated with user.
            "click .aboutHeaderadvisor": "openAboutAdvisorDialog", //function to view About You of advisor
            "click .btndeleteClient": "deleteClient", //function to delete client association by advisor
            "click .acceptRequest": "fnAcceptUserRequest", //function for advisor to accept user request
            "click .todashboards": "fnToAdvisorDashboard", //function for advisor to redirect back to dashboard
            "click .searchadvback": "fnToSearchAdvisor", //function for redirect to advisor search result
        },
        fnToSearchAdvisor: function(event) {
            $('.searchCSS').hide();
            $('.allAdvisors').hide();
            $('#search-contents').hide();
            location.href = "./searchadvisor";
        },
        fnMyadvisors: function(event) {
            event.preventDefault();
            $.ajax({
                url: getUseradvisorlist,
                type: 'GET',
                dataType: "json",
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                    require(
                            ['views/profile/myadvisors'],
                            function(myadvisorsV) {
                                myadvisorsV.render(data.userdata);
                                if (data.connectedAdv == 'NULL') {
                                    $('#noadv').show();
                                    //$('#noadv').html(data.message);
                                    $('.twentypx').hide();
                                }
                                else {
                                    for (var i = 0; i < data.userdata.length; i++) {
                                        if (data.userdata[i].permission == "RW") {
                                            $(".advper" + data.userdata[i].advisor_id).text("View+Edit");//show the permission text.
                                        }
                                        else if (data.userdata[i].permission == "RO") {
                                            $(".advper" + data.userdata[i].advisor_id).text("View Only");
                                        }
                                        else {
                                            $(".advper" + data.userdata[i].advisor_id).text("None");
                                        }
                                    }
                                }
                                $('#permitIndem').show();
                                popUpMyadvisor();
                                if (data.loggedin_user_created_by == 'advisor') {
                                    $('.nextProfilePopupBox').hide();
                                } else {
                                    $('.nextProfilePopupBox').show();
                                }
                            }
                    );
                }
            });
        },
        fnCloseProfileDialog: function(event) {
            event.preventDefault();
            if ($(".aboutIconOn").length > 0) {
                $("#stepOneCompleteButton").click();
            }
            removeLayover();
            if (window.location.pathname.indexOf('financialsnapshot') != -1) {
                require(
                        ['views/user/financialsnapshot'],
                        function(financialV) {
                            financialV.render();
                        }
                );
            } else if (window.location.pathname.indexOf('myscore') != -1) {
                require(
                        ['views/user/myscore'],
                        function(myscoreV) {
                            myscoreV.render();
                        }
                );
            }
            // Completed. Need to check actionsteps status
            $.ajax({
                url: finalscoreURL,
                type: 'POST',
                dataType: "json",
                success: function(getAll) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (getAll.status == "OK") {
                        $.ajax({
                            url: userGetScoreURL,
                            type: 'GET',
                            dataType: "json",
                            cache: false,
                            beforeSend: function(request) {
                                request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                            },
                            success: function(scoreData) {
                                timeoutPeriod = defaultTimeoutPeriod;
                                if (scoreData.status == "OK") {
                                    window.parent.removeLayover();
                                    var source = $(congratsTemplate).html();
                                    var template = Handlebars.compile(source);
                                    $.scrollTo($('#body'), 200);
                                    $('#comparisonBox').show();
                                    $('#darkBackground').show();
                                    $('#darkBackground').fadeTo("fast", 0.6);
                                    $('#comparisonBox').css("height", 'auto');
                                    $('#comparisonBox').html(template(getAll));
                                    var simScore = parseInt(scoreData.score.totalscore);
                                    var imageId = Math.round((simScore * 20) / 1000);
                                    imageId = (imageId > 0) ? imageId : 0;
                                    imageId = (imageId < 20) ? imageId : 20;
                                    alignCongratsScore('reportScore', 'reportHorseshoe', simScore, imageId);
                                    $('#ActionStepContent').trigger('change');
                                }
                            }
                        });
                    }
                }
            });
            ///
        },
        // Use this to go Next section in Profile Box
        fnNextProfileDialog: function() {
            var selectItems = {
                about: "#connectLink",
                connect: "#incomeLink",
                income: "#expensesLink",
                expenses: "#debtsLink",
                debts: "#assetsLink",
                assets: "#insuranceLink",
                insurance: "#riskLink",
                risk: "#taxesAddAccount",
                tax: "#estateplanningAddAccount",
                estateplanning: "#moreAddAccount",
                more: ".tabGoals"
            }

            var current = $("#ProfileTracker").val();
            if (current == "risk") {
                $("#miscellaneousLink").click();
            }
            if (current == "about") {
                $(".tabFinancial").click();
            }
            $(selectItems[current]).click();
            if (current == "about") {
                $("#connectSection").addClass("posRel profileNavOn");
                $("#connectSelected").removeClass("hdn");
                $("#connectUnselected").addClass("hdn");
            }
        },
        fnLogoutUser: function() {
            $.ajax({
                url: logoutUrl,
                type: 'GET',
                dataType: "json",
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function() {
                    localStorage[serverSess] = '';
                    window.location = "./";
                }
            });
        },
        fnRefreshAllAccount: function() {
            financialData.accountsdownloading = true;
            $.ajax({
                url: refreshAllUrl,
                type: 'POST',
                dataType: "json",
                success: function() {
                    timeoutPeriod = defaultTimeoutPeriod;
                    //once refresh is done
                    //need to reload the contents
                }
            });
        },
        fnNotification: function(event) {
            event.preventDefault();

            require(
                    ['views/account/account', 'views/account/notification'],
                    function(accountV, notificationV) {
                        accountV.render(userData);
                        notificationV.render();
                    }
            );
        },
        fnSettings: function(event) {
            event.preventDefault();
            var key = "credentials"
            if (event.currentTarget.id == "profileAvatar") {
                key = "photo";
            }
            require(
                    ['views/account/account', 'views/account/settings', 'views/account/' + key],
                    function(accountV, settingsV, innerSettingsV) {
                        accountV.render(userData);
                        settingsV.render(userData);
                        innerSettingsV.render();
                        init();
                        $("#tabCredentials").removeClass("selected");
                        $("#tab" + toTitleCase(key)).addClass("selected");
                    }
            );
        },
        openAboutDialog: function(event) {
            event.preventDefault();
            removeLayover();
            RemoveScoreDialog();
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
        openGoalsDialog: function(event) {
            RemoveScoreDialog();
            event.preventDefault();
            var name = event.target.id;
            if (name.indexOf('addGoals') > -1) {
                currentOpenField = name.substring(0, name.indexOf('addGoals'));
                throughActionStep = $("#" + event.target.id).hasClass('actionStep');
                currentActionEvent = 'addgoal';
            }
            removeLayover();
            require(
                    ['views/profile/profile', 'views/profile/goals'],
                    function(profileV, goalsV) {
                        if (financialData.accountsdownloading) {
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
        openFinancialDetailDialog: function(event) {
            event.preventDefault();
            removeLayover();
            RemoveScoreDialog();
            require(
                    ['views/profile/profile', 'views/profile/financialdetails'],
                    function(profileV, financialDetailsV) {
                        profileV.render();
                        financialDetailsV.render();

                        if (typeof (userData.advisor) != 'undefined') {
                            userData.user.impersonationMode = true;
                            if (userData.permission == 'RO') {// if advisor has RO permission during impersonation.
                                $("#incomeLink").click();//to show income tab default.
                                $("#incomeSection").addClass("posRel profileNavOn");
                                $("#incomeSelected").removeClass("hdn");
                                $("#incomeUnselected").addClass("hdn");
                            }
                            $("#connectLink").click();
                            $("#connectSection").addClass("posRel profileNavOn");
                            $("#connectSelected").removeClass("hdn");
                            $("#connectUnselected").addClass("hdn");
                        } else {
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
        closeNotificationDialog: function(event) {
            event.preventDefault();
            removeLayover();
        },
        openDebtsDialog: function(event) {
            RemoveScoreDialog();
            event.preventDefault();
            var name = event.target.id;
            currentOpenField = name.substring(0, name.indexOf('addDebts'));
            throughActionStep = $("#" + event.target.id).hasClass('actionStep');
            currentActionEvent = 'adddebt';
            removeLayover();
            require(
                    ['views/profile/profile', 'views/profile/financialdetails'],
                    function(profileV, financialdetailsV) {
                        profileV.render();
                        financialdetailsV.render();
                        $("#debtsLink").click();
                    }
            );
        },
        openAssetsDialog: function(event) {
            RemoveScoreDialog();
            event.preventDefault();
            var name = event.target.id;
            currentOpenField = name.substring(0, name.indexOf('addAssets'));
            throughActionStep = $("#" + event.target.id).hasClass('actionStep');
            currentActionEvent = 'addasset';
            removeLayover();
            require(
                    ['views/profile/profile', 'views/profile/financialdetails'],
                    function(profileV, financialdetailsV) {
                        profileV.render();
                        financialdetailsV.render();
                        $("#assetsLink").click();
                    }
            );
        },
        openInsuranceDialog: function(event) {
            RemoveScoreDialog();
            event.preventDefault();
            var name = event.target.id;
            currentActionEvent = 'addinsurance';
            currentOpenField = name.substring(0, name.indexOf('addInsurance'));
            currentOpenType = name.substring(currentActionEvent.length)
            throughActionStep = $("#" + event.target.id).hasClass('actionStep');
            removeLayover();
            require(
                    ['views/profile/profile', 'views/profile/financialdetails'],
                    function(profileV, financialdetailsV) {
                        profileV.render();
                        financialdetailsV.render();
                        $("#insuranceLink").click();
                    }
            );
        },
        fnCloseProfileDialog: function(event) {
            event.preventDefault();
            if ($(".aboutIconOn").length > 0) {
                $("#stepOneCompleteButton").click();
            }
            removeLayover();
            if (window.location.pathname.indexOf('financialsnapshot') != -1) {
                require(
                        ['views/user/financialsnapshot'],
                        function(financialV) {
                            financialV.render();
                        }
                );
            } else if (window.location.pathname.indexOf('myscore') != -1) {
                require(
                        ['views/user/myscore'],
                        function(myscoreV) {
                            myscoreV.render();
                        }
                );
            }
            // Completed. Need to check actionsteps status
            $.ajax({
                url: finalscoreURL,
                type: 'POST',
                dataType: "json",
                success: function(getAll) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (getAll.status == "OK") {
                        $.ajax({
                            url: userGetScoreURL,
                            type: 'GET',
                            dataType: "json",
                            cache: false,
                            beforeSend: function(request) {
                                request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                            },
                            success: function(scoreData) {
                                timeoutPeriod = defaultTimeoutPeriod;
                                if (scoreData.status == "OK") {
                                    window.parent.removeLayover();
                                    var source = $(congratsTemplate).html();
                                    var template = Handlebars.compile(source);
                                    $.scrollTo($('#body'), 200);
                                    $('#comparisonBox').show();
                                    $('#darkBackground').show();
                                    $('#darkBackground').fadeTo("fast", 0.6);
                                    $('#comparisonBox').css("height", 'auto');
                                    $('#comparisonBox').html(template(getAll));
                                    var simScore = parseInt(scoreData.score.totalscore);
                                    var imageId = Math.round((simScore * 20) / 1000);
                                    imageId = (imageId > 0) ? imageId : 0;
                                    imageId = (imageId < 20) ? imageId : 20;
                                    alignCongratsScore('reportScore', 'reportHorseshoe', simScore, imageId);
                                    $('#ActionStepContent').trigger('change');
                                }
                            }
                        });
                    }
                }
            });
            ///
        },
                // Use this to go Next section in Profile Box

                fnLogoutUser: function() {
                    $.ajax({
                        url: logoutUrl,
                        type: 'GET',
                        dataType: "json",
                        cache: false,
                        beforeSend: function(request) {
                            request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                        },
                        success: function() {
                            localStorage[serverSess] = '';
                            window.location = "./";
                        }
                    });
                },
                fnRefreshAllAccount: function() {
                    $.ajax({
                        url: refreshAllUrl,
                        type: 'POST',
                        dataType: "json",
                        success: function() {
                            timeoutPeriod = defaultTimeoutPeriod;
                            //once refresh is done
                            //need to reload the contents
                        }
                    });
                },
                fnNodeNotification: function(id) {
                    if (typeof (io) != 'undefined' && io != null) {

                        var isMyScorePage = false;
                        if (window.location.pathname.indexOf('myscore') != -1) {
                            isMyScorePage = true;
                        }
                        socket = io.connect(nodeNotificationUrl, {'reconnect': false, 'query': 'uid=' + id + "&isScorePage=" + isMyScorePage + "&session=" + localStorage[serverSess]});
                        var currentSocket = socket.on('time', function(data) {
                            HandleNodeResponse(data, congratsTemplate, Handlebars);
                        });
                        delete currentSocket;
                    }
                },
        fnToAdvisorDashboard: function(event) {
            event.preventDefault();
            $.ajax({
                url: destroyclientsession,
                type: 'GET',
                dataType: "json",
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                    window.location = baseUrl + '/dashboard';

                }
            });

        },
        fnAcceptUserRequest: function(event) {
            event.preventDefault();

            var clientId = event.currentTarget.attributes.getNamedItem('clientId').nodeValue;
            var clientEmail = event.currentTarget.attributes.getNamedItem('clientEmail').nodeValue;
            timeoutPeriod = defaultTimeoutPeriod;
            require(
                    ['views/advisor/acceptconnection'],
                    function(acceptView) {
                        acceptView.render(clientId, clientEmail);
                        popUpDeleteclient();
                    }
            );

        },
        deleteClient: function(event) {
            event.preventDefault();
            var delete_id = event.currentTarget.attributes.getNamedItem('deleteId').nodeValue;
            var status = event.currentTarget.attributes.getNamedItem('status').nodeValue;
            var clientEmail = event.currentTarget.attributes.getNamedItem('clientemail').nodeValue;
            require(
                    ['views/profile/deleteclient'],
                    function(deletecliView) {
                        deletecliView.render(delete_id, status, clientEmail);
                    }
            );
        },
        openAboutAdvisorDialog: function(event) {
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
    });
    return new headerView;
});
