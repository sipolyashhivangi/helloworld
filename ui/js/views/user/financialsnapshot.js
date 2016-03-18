define([
    'handlebars',
    'text!../../../html/user/financialsnapshot.html',
    'financialsnapshotchart'
], function(Handlebars, financialsnapshotTemplate)
{

    var financialsnapshotView = Backbone.View.extend({
        el: $("#body"),
        render: function() {
            //get the details from the getuseritem

            var source = $(financialsnapshotTemplate).html();
            var template = Handlebars.compile(source);

            if (typeof (userData) != 'undefined' && typeof (userData.user) != 'undefined' && typeof (userData.user.image) != 'undefined') {
                var userimage = userData.user.image;
            }

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
                            if (typeof (userData.user) != 'undefined' && typeof (userData.advisor) != 'undefined') {
                                financialData.impersonation = true;
                            }
                            $('#mainBody').html(template(financialData));
                            fnCleanUpFinancialData();

                            var oldSrc = './ui/images/genericAvatar.png';
                            var newSrc = userimage;
                            //$('img[src="' + oldSrc + '"]').attr('src', newSrc);
                            if (userimage != "" && userimage != null) {
                                jQuery("#profileUser").attr("src", newSrc);
                            } else {
                                jQuery("#profileUser").attr("src", oldSrc);
                            }

                            currentScore = data.lsacc.totalscore;
                            var value = data.lsacc.wfPoint38;
                            value = Math.round(value * 2);
                            var imageId = Math.round(value / 5);
                            imageId = (imageId > 0) ? imageId : 0;
                            imageId = (imageId < 20) ? imageId : 20;
                            $(".floatedProfileComplete").html('<a href="#" onclick="javascript:LoadPCDialog();return false;">' + value + '<span class="small">%</span><div class="small">Complete</div></a>')
                            $(".floatedProfileCompleteImage").attr("src", "./ui/images/horseshoes/variations/profile/ProfileHorseShoe" + imageId + ".png")

                            try {
                                google.load('visualization', '1', {
                                    'callback': drawAllCharts,
                                    'packages': ['corechart']
                                });
                            } catch (err) {
                                alert(err);
                            }
                            initFinancial();
                        }
                    }
                });
            }
            else
            {
                fnUpdateFinancialData();
                if (typeof (userData.user) != 'undefined' && typeof (userData.advisor) != 'undefined') {
                    financialData.impersonation = true;
                }
                $('#mainBody').html(template(financialData));
                fnCleanUpFinancialData();

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

                            var oldSrc = './ui/images/genericAvatar.png';
                            var newSrc = userimage;
                            //$('img[src="' + oldSrc + '"]').attr('src', newSrc);
                            if (userimage != "" && userimage != null) {
                                jQuery("#profileUser").attr("src", newSrc);
                            } else {
                                jQuery("#profileUser").attr("src", oldSrc);
                            }

                            currentScore = scoreData.score;
                            var value = scoreData.score.point38;
                            value = Math.round(value * 2);
                            var imageId = Math.round(value / 5);
                            imageId = (imageId > 0) ? imageId : 0;
                            imageId = (imageId < 20) ? imageId : 20;
                            $(".floatedProfileComplete").html('<a href="#" onclick="javascript:LoadPCDialog();return false;">' + value + '<span class="small">%</span><div class="small">Complete</div></a>')
                            $(".floatedProfileCompleteImage").attr("src", "./ui/images/horseshoes/variations/profile/ProfileHorseShoe" + imageId + ".png")
                        }
                    }
                });

                try {
                    google.load('visualization', '1', {
                        'callback': drawAllCharts,
                        'packages': ['corechart']
                    });
                } catch (err) {
                    alert(err);
                }
                initFinancial();
            }
        },
        events: {
            "click .popLayerButton": "fnActionPages",
            "click .networthDateRange": "fnNetWorthChart"
        },
        fnNetWorthChart: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("DateRange"));
            drawNetWorthBarChart(key);
            $("#networthButtonSpan").html($("#" + name).html());
        },
        fnActionPages: function(event) {
            init();
            event.preventDefault();
            window.parent.removeLayover();
            var eventid = event.target.id;
            var actionid = event.target.name;
            if (eventid == 'learnmore') {
                parent.window.open(baseUrl + "/learningcenter");
                $('#ActionStepContent').trigger('change');
            } else {
                var etype = event.target.title;
                //var evalue = event.target.value;
                require(
                        ['views/profile/profile', 'views/profile/financialdetails'],
                        function(profileV, financialdetailsV) {
                            profileV.render();
                            financialdetailsV.render();
                            // Track connect accounts for score engine
                            var formFields = {
                                event: eventid,
                                id: actionid
                            }
                            $.ajax({
                                url: addTrackuserURL,
                                type: 'POST',
                                dataType: "json",
                                data: formFields
                            });
                            // Track
                            if (eventid == 'connectaccount') {
                                $("#connectLink").click();
                                $("#connectSection").addClass("posRel profileNavOn");
                                $("#connectSelected").removeClass("hdn");
                                $("#connectUnselected").addClass("hdn");
                            } else if (eventid == 'addinsurance') {
                                $("#insuranceLink").click();
                            } else if (eventid == 'addincome') {
                                $("#incomeLink").click();
                            } else if (eventid == 'addgoal') {
                                $(".tabGoals").click();
                            } else if (eventid == 'addexpense') {
                                $("#expensesLink").click();
                            } else if (eventid == 'adddebt') {
                                $("#debtsLink").click();
                            } else if (eventid == 'addasset') {
                                $("#assetsLink").click();
                            } else if (eventid == 'reviewasset') {
                                $("#assetsLink").click();
                            } else if (eventid == 'editdebt') {
                                $("#debtsLink").click();
                                if (etype == 'BANK') {
                                } else if (etype == 'IRA') {
                                }
                            } else if (eventid == 'editasset') {
                                $("#assetsLink").click();
                                if (etype == 'LOAN') {
                                } else if (etype == 'CC') {
                                }
                            } else if (eventid == 'addmisc') {
                                $("#miscellaneousLink").click();
                                $("#taxesAddAccount").click();
                            } else if (eventid == 'addestate') {
                                currentState = 'addestate';
                                $("#miscellaneousLink").click();
                                $("#estateplanningAddAccount").click();
                            } else if (eventid == 'planestate') {
                                currentState = 'addestate';
                                $("#miscellaneousLink").click();
                                $("#estateplanningAddAccount").click();
                            } else if (eventid == 'addmore') {
                                currentState = 'addmore';
                                $("#miscellaneousLink").click();
                                $("#moreAddAccount").click();
                            } else if (eventid == 'addtax') {
                                currentState = 'addtax';
                                $("#miscellaneousLink").click();
                                $("#taxesAddAccount").click();
                            } else if (eventid == 'addrisk') {
                                $("#riskLink").click();
                            } else if (eventid == 'reviewrisk') {
                                $("#riskLink").click();
                            }
                            popUpProfile();
                            init();
                            //close this popup and open congrats popup
                        }
                )
            }
        }
    });
    return new financialsnapshotView;
});