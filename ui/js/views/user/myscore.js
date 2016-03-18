define([
    'handlebars',
    'text!../../../html/user/myscore.html',
    'text!../../../html/user/myscorehorse.html',
    'text!../../../html/user/actionCongrats.html',
    'myscorechart'
], function(Handlebars, myscoreTemplate, myscorehorseT, congratsTemplate) {
    var renderInProgress = false;
    var myscoreView = Backbone.View.extend({
        el: $("#body"),
        render: function() {
            $.getJSON(userRiskGetDataURL, function(data) {
                var risk_local_data = $.map(data.riskdata, function(value, index) {
                    return [value];
                });

                // Global Variable
                riskdata = risk_local_data;
            });

            //get the details from the getuseritem
            var source = $(myscoreTemplate).html();
            var template = Handlebars.compile(source);
            var obj = {};
            if (typeof (userData) != 'undefined' && typeof (userData.user) != 'undefined' && typeof (userData.user.image) != 'undefined') {
                obj.image = userData.user.image;
            }

            if (typeof (userData.user) != 'undefined' && typeof (userData.advisor) != 'undefined') {
                obj.impersonation = true;
            }
            

            if ((typeof (bDown) != 'undefined') && bDown == true && typeof (userData) != 'undefined' && typeof (userData.user) != 'undefined' && userData.user.retirementstatus != 1) {
                obj.breakDown = 1;
            } else {
                obj.breakDown = 0;
            }

            $('#mainBody').html(template(obj));

            $('.myscoreHorseshoe').trigger('change');
            $('#ActionStepContent').trigger('change');

            init();
        },
        events: {
            //"click .financialDetails": "openFinancialDetailDialog",
            "change .myscoreHorseshoe": "fnChangeScoreRender",
            "change #ActionStepContent": "fnActionStepRender",
            "change .PeerRankContent": "fnPeerRender",
            "click .popLayerButton": "fnActionPages",
            "click .actionStepOrderDiv": "fnActionStepOrderRender",
            "click .popCongrats": "fnActionCongrats",
            "click .popCongratsSpecial": "fnActionCongratsSpecial",
            "click .popLinkCongrats": "fnActionLinkCongrats",
            "click .advisorPopLinkCongrats": "fnAdvisorActionLinkCongrats",
            "click .popDid": "fnActionDid",
            "click .articlelink": "fnActionArticleUpdate",
            "click .milesDateRange": "fnMilestonesChart",
            "click #PrintinPopup": "fnPrintPopup",
            "click #DownloadinPopup": "fnDownloadPopup",
            "hover #PrintinPopup": "fnRenderHtml",
            "hover #DownloadinPopup": "fnRenderHtml",
            "click #tab-2": "fnChartLoad"
        },
        fnChartLoad: function(event) {
            event.preventDefault();
            currentMyScoreTab = 'tab-2';
            try {
                google.load('visualization', '1', {
                    'callback': drawBothCharts,
                    'packages': ['corechart']
                });
                $("#milesButtonSpan").html('7 Days');
            } catch (err) {
                //alert(err);
            }

            $("#tab-2").click(function(event) {
                if ($('#tab-2').attr('class') == 'selected') {
                    event.stopPropagation();
                }
                // Do something
            });
        },
        fnRenderHtml: function(event) {
            event.preventDefault();
            if(renderInProgress) {
               return;
            }

            var chartContainer = document.getElementById('tab-content-1-chart');
            var doc = chartContainer.ownerDocument;

            var img = doc.createElement('img');
            if (chartContainer.getElementsByTagName('svg')[0] != undefined) {
                img.src = getImgData(chartContainer);
            }
            //var graphcontent = $('#tab-content-1').html();

            $('#tab-content-1-chart').hide();
            $('#tab-content-1-chart-img').html(img);
            $('#tab-content-1-chart-img').show();

            renderInProgress = true;           

            html2canvas($('#mainBody'), {
                onrendered: function(canvas) {
                    renderInProgress = false;           
                    globalCanvas = canvas;
                    $('#tab-content-1-chart-img').hide();
                    $('#tab-content-1-chart').show();
                }
            });
        },
        fnPrintPopup: function(event) {
            event.preventDefault();
            downloadElement('print');
        },
        fnDownloadPopup: function(event) {
            event.preventDefault();
            downloadElement('pdf');
        },
        fnMilestonesChart: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("DateRange"));
            drawMilestonesChart(key);
            $("#milesButtonSpan").html($("#" + name).html());
        },
        fnChangeScoreRender: function(event) {
            //get the score from database
            init();
            event.preventDefault();
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
                        currentScore = scoreData.score;
                        var source = $(myscorehorseT).html();
                        var template = Handlebars.compile(source);
                        if (typeof (userData.advisor) == 'undefined') {
                            scoreData.score.printMode = 1;
                        } else {
                            scoreData.score.printMode = 0;
                        }
                        if (typeof (showScoreLinks) != 'undefined')
                            scoreData.score.showScoreLinks = showScoreLinks;
                        $('.myscoreHorseshoe').html(template(scoreData.score));
                        $('.floatedProfileComplete').css('font-size', '2.5em');
                        $('.floatedProfileComplete').css('width', '85px');
                        $('.floatedProfileComplete').css('text-align', 'center');

                        Modernizr.addTest("blobconstructor", function() {
                            try {
                                return!!(new Blob)
                            } catch (a) {
                                return!1
                            }
                        });
                        if (Modernizr.blobconstructor) {
                            $("#DownloadinPopup").show();
                        }
                        $("#totalScoreValue").hide();
                        var value = scoreData.score.point38;
                        value = Math.round(value * 2);
                        var imageId = Math.round(value / 5);
                        imageId = (imageId > 0) ? imageId : 0;
                        imageId = (imageId < 20) ? imageId : 20;
                        $(".floatedProfileComplete").html('<a href="#" onclick="javascript:LoadPCDialog();return false;">' + value + '<span class="small">%</span><div class="small">Complete</div></a>')
                        $(".floatedProfileCompleteImage").attr("src", "./ui/images/horseshoes/variations/profile/ProfileHorseShoe" + imageId + ".png")

                        $('.PeerRankContent').trigger('change');

                        if (userData.retirementstatus != 1 && (financialData.accountsdownloading || typeof (financialData.goals[0]) == 'undefined' || financialData.goals[0].goaltype != 'RETIREMENT'))
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
                                        scoreData.score.totalscore = financialData.totalscore;
                                        scoreData.score.livingCosts = financialData.livingCosts;
                                        scoreData.score.income = financialData.income;
                                        scoreData.score.debtsTotal = financialData.debtsTotal;
                                        scoreData.score.retage = financialData.retage;
                                        scoreData.score.age = financialData.age;
                                        scoreData.score.assetsTotal = financialData.assetsTotal;
                                        scoreData.score.savingsTotal = financialData.savingsTotal;

                                        $("#totalScoreValue").html(financialData.totalscore);
                                        $("#totalScoreValue").show();
                                        financialData.breakdowndata = data.lsacc.breakdownData;
                                        require(
                                                ['views/user/break'],
                                                function(breakV) {
                                                    breakV.render(financialData);
                                                }
                                        );
                                        require(
                                                ['views/user/goals'],
                                                function(goalsV) {
                                                    goalsV.render();
                                                }
                                        );
                                    }
                                }
                            });
                        } else {
                            financialData.totalscore = scoreData.score.totalscore;
                            if (scoreData.score.livingCosts != null) {
                                financialData.livingCosts = commaSeparateNumber(scoreData.score.livingCosts.toString().replace(/,/g, ''), 0);
                            }
                            else
                            {
                                financialData.livingCosts = "0";
                            }
                            financialData.income = scoreData.score.income;
                            if (scoreData.score.debtsTotal != null) {
                                financialData.debtsTotal = commaSeparateNumber(scoreData.score.debtsTotal.toString().replace(/,/g, ''), 0);
                            }
                            else
                            {
                                financialData.debtsTotal = "0";
                            }
                            financialData.retage = scoreData.score.retage;
                            financialData.age = scoreData.score.age;
                            if (scoreData.score.assetsTotal != null) {
                                financialData.assetsTotal = commaSeparateNumber(scoreData.score.assetsTotal.toString().replace(/,/g, ''), 0);
                            }
                            else
                            {
                                financialData.assetsTotal = "0";
                            }
                            if (scoreData.score.savingsTotal != null) {
                                financialData.savingsTotal = commaSeparateNumber(scoreData.score.savingsTotal.toString().replace(/,/g, ''), 0);
                            }
                            else
                            {
                                financialData.savingsTotal = "0";
                            }
                            $("#totalScoreValue").html(financialData.totalscore);
                            $("#totalScoreValue").show();
                            require(
                                    ['views/user/break'],
                                    function(breakV) {
                                        breakV.render(financialData);
                                    }
                            );
                            require(
                                    ['views/user/goals'],
                                    function(goalsV) {
                                        goalsV.render();
                                    }
                            );
                        }
                    }
                }
            });
        },
        fnAdvisorActionLinkCongrats: function(event) {
            init();
            event.preventDefault();
            var eventid = event.target.id;
            var formFields = {
                id: eventid
                        //, type: 'over'
            }

            $.ajax({
                url: actionOverlayURL,
                async: false,
                type: 'GET',
                dataType: "json",
                data: formFields,
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(scoreData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    parent.window.open($(event.target).attr('href'));
                    return false; // use return false, to make pop up active tough any other action taken place, IE version won't support if return false not sets - Vinoth
                }
            });
        },
        fnActionLinkCongrats: function(event) {
            init();
            event.preventDefault();
            var eventid = event.target.id;
            var formFields = {
                id: eventid
                        //, type: 'over'
            }
            $.ajax({
                url: actionOverlayURL,
                async: false,
                type: 'GET',
                dataType: "json",
                data: formFields,
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(scoreData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (scoreData.lnk != '') {
                        parent.window.open(scoreData.lnk);
                        //      window.parent.removeLayover();
                        //      $('#ActionStepContent').trigger('change');
                    }
                }
            });
        },
        fnActionCongrats: function(event) {
            init();
            event.preventDefault();
            var eventid = event.target.id;
            if (videokey != "") {
                var formFields = {
                    name: videokey
                }
                $.ajax({
                    url: addeditlearningURL,
                    type: 'POST',
                    dataType: "json",
                    data: formFields,
                    success: function(scoreData) {
                        timeoutPeriod = defaultTimeoutPeriod;
                        if (scoreData.status == "OK") {
                            var source = $(congratsTemplate).html();
                            var template = Handlebars.compile(source);
                            scoreData.points = 5;
                            var actionpoints = scoreData.points;
                            $('#comparisonBox').html(template(scoreData));
                            var simScore = parseInt(scoreData.totalscore);
                            var imageId = Math.round((simScore * 20) / 1000);
                            imageId = (imageId > 0) ? imageId : 0;
                            imageId = (imageId < 20) ? imageId : 20;
                            alignCongratsScore('congratsScore', 'congratsHorseshoe', simScore, imageId);
                            if (parseInt(actionpoints) > 0) {
                                $('#numOfPointsid').html(actionpoints + '<sup>pts</sup>');
                            } else {
                                $('#numOfPointsid').html('');
                            }
 
                            var oldvalue = parseInt($("#totalScoreValue").html());
                            $("#totalScoreValue").html(scoreData.totalscore);
                            if($("#onemonth").html() != "NA") {
                                var currentvalue = parseInt($("#onemonth").html()) + simScore - oldvalue;
                                var sign = "";
                                if(currentvalue > 0) { sign = "+"; }
                                $("#onemonth").html(sign + currentvalue + " pts");
                            }
                            if($("#sevenday").html() != "NA") {
                                var currentvalue = parseInt($("#sevenday").html()) + simScore - oldvalue;
                                var sign = "";
                                if(currentvalue > 0) { sign = "+"; }
                                $("#sevenday").html(sign + currentvalue + " pts");
                            }
                            if($("#fifteenday").html() != "NA") {
                                var currentvalue = parseInt($("#fifteenday").html()) + simScore - oldvalue;
                                var sign = "";
                                if(currentvalue > 0) { sign = "+"; }
                                $("#fifteenday").html(sign + currentvalue + " pts");
                            }

                            $('.floatedProfileComplete').css('font-size', '2.5em');
                            $('.floatedProfileComplete').css('width', '85px');
                            $('.floatedProfileComplete').css('text-align', 'center');
                            $('#ActionStepContent').trigger('change');
                            $('.externallink_tooltip').remove();
                        }
                    }
                });
            }
            else {
                var formFields = {
                    id: eventid,
                    type: 'over'
                }
                $.ajax({
                    url: actionOverlayURL,
                    async: false,
                    type: 'GET',
                    dataType: "json",
                    data: formFields,
                    cache: false,
                    beforeSend: function(request) {
                        request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                    },
                    success: function(scoreData) {
                        timeoutPeriod = defaultTimeoutPeriod;
                        if (scoreData.lnktyp == 'link') {
                            if (scoreData.lnk != '') {
                                parent.window.open(scoreData.lnk);
                            }
                        } else {
                            var actionpoints = scoreData.points;
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
                                    if (scoreData.status == "OK" || scoreData.status == "Success") {
                                        var source = $(congratsTemplate).html();
                                        var template = Handlebars.compile(source);
                                        $('#comparisonBox').html(template(scoreData.score));
                                        var simScore = parseInt(scoreData.score.totalscore);
                                        var imageId = Math.round((simScore * 20) / 1000);
                                        imageId = (imageId > 0) ? imageId : 0;
                                        imageId = (imageId < 20) ? imageId : 20;
                                        alignCongratsScore('congratsScore', 'congratsHorseshoe', simScore, imageId);
                                        if (parseInt(actionpoints) > 0) {
                                            $('#numOfPointsid').html(actionpoints + '<sup>pts</sup>');
                                        } else {
                                            $('#numOfPointsid').html('');
                                        }
                                        var source1 = $(myscorehorseT).html();
                                        var template1 = Handlebars.compile(source1);

                                        if (typeof (showScoreLinks) != 'undefined')
                                            scoreData.score.showScoreLinks = showScoreLinks;
                                        $('.myscoreHorseshoe').html(template1(scoreData.score));
                                        $('.floatedProfileComplete').css('font-size', '2.5em');
                                        $('.floatedProfileComplete').css('width', '85px');
                                        $('.floatedProfileComplete').css('text-align', 'center');
                                        $('#ActionStepContent').trigger('change');
                                        $('.externallink_tooltip').remove();
                                    }
                                }
                            });
                        }
                    }
                });
            }
        },
        fnActionCongratsSpecial: function(event) {
            init();
            event.preventDefault();
            var eventid = event.target.name;
            var formFields = {
                id: eventid,
                type: 'over'
            }
            $.ajax({
                url: actionOverlayURL,
                async: false,
                type: 'GET',
                dataType: "json",
                data: formFields,
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(scoreData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                }
            });
            var eventid = event.target.id;
            require(
                    ['views/profile/profile', 'views/profile/financialdetails'],
                    function(profileV, financialdetailsV) {
                        window.parent.removeLayover();
                        profileV.render();
                        financialdetailsV.render();
                        // Track connect accounts for score engine
                        if (eventid == 'planestate') {
                            currentState = 'addestate';
                            $("#miscellaneousLink").click();
                            $("#estateplanningAddAccount").click();
                        } else if (eventid == 'reviewrisk') {
                            $("#riskLink").click();
                        }
                        popUpProfile();
                        init();
                        //close this popup and open congrats popup
                    }
            );

        },
        fnActionDid: function(event) {
            event.preventDefault();
            window.parent.removeLayover();
            $('#ActionStepContent').trigger('change');
        },
        fnActionArticleUpdate: function(event) {
            event.preventDefault();
            if (event.target.href != '') {
                localStorage.updatelearning = false;
                parent.window.open(event.target.href);
                var formFields = {
                    articleid: event.target.id,
                    actionid: event.target.name
                }
                if (userData.permission != "RO") {
                    $.ajax({
                        url: updateArticleViewURL,
                        type: 'POST',
                        dataType: "json",
                        data: formFields,
                        success: function(response) {
                            timeoutPeriod = defaultTimeoutPeriod;
                            if (response.status == 'OK') {
                                if (response.message == 'Completed') {
                                    window.location = "./myscore";
                                } else {
                                    loadfakeactionstep = true;
                                    $('#ActionStepContent').trigger('change');
                                }
                            }
                        }
                    });
                }
            }
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
        },
        fnActionStepRender: function(event) {
            //get the score from database
            init(false);
            event.preventDefault();
            var formValues = {};
            var catName = $('#categorySearch').val();
            if (typeof (stepscount) != 'undefined') {
                formValues['stepscount'] = stepscount;
            } else {
                formValues['stepscount'] = 6;
            }

            if (catName != "") {
                formValues['catName'] = catName;
            }

            $.ajax({
                url: userGetActionStepURL,
                type: 'GET',
                dataType: "json",
                data: formValues,
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(scoreData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/user/actionstep'],
                            function(actionstepV) {
                                actionstepV.render(scoreData);
                                if (loadfakeactionstep) {
                                    $('#fakeActionStep').val(currentactionstepid);
                                    $("#fakeActionStep").click();
                                    loadfakeactionstep = false;
                                }
                            }
                    );
                }
            });
        },
        fnPeerRender: function(event) {
            //get the score from database
            init(false);
            event.preventDefault();
            $.ajax({
                url: getPeerRankUrl,
                type: 'GET',
                dataType: "json",
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(scoreData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/user/peer'],
                            function(peerV) {
                                peerV.render(scoreData);
                            }
                    );
                }
            });
        },
        fnBreakRender: function(event) {
            //get the score from database
            init();
            event.preventDefault();
            require(
                    ['views/user/break'],
                    function(breakV) {
                        breakV.render();
                    }
            );
        },
        fnActionStepOrderRender: function(event) {
            //get the score from database
            init();
            event.preventDefault();
            var fFlds = {
                type: event.target.id,
                stepscount: 6
            }
            $.ajax({
                url: userGetActionStepURL,
                type: 'GET',
                dataType: "json",
                data: fFlds,
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(scoreData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/user/actionstep'],
                            function(actionstepV) {
                                actionstepV.render(scoreData);
                            }
                    );
                }
            });
        },
        openFinancialDetailDialog: function(event) {
            event.preventDefault();
            require(
                    ['views/profile/profile', 'views/profile/financialdetails'],
                    function(profileV, financialDetailsV) {
                        profileV.render();
                        financialDetailsV.render();
                        $("#connectLink").click();
                        $("#connectSection").addClass("posRel profileNavOn");
                        $("#connectSelected").removeClass("hdn");
                        $("#connectUnselected").addClass("hdn");
                        popUpProfile();
                        init();
                    });
        }
    });
    return new myscoreView;
});
