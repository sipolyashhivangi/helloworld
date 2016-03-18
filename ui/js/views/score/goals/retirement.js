// Filename: views/score/goals/retirement
define([
    'handlebars',
    'backbone',
    'text!../../../../html/score/goals/retirement.html',
    ], function(Handlebars, Backbone, retirementTemplate){
        var retirementView = Backbone.View.extend({
            el: $("#body"),
            render: function(element, obj){
                var source = $(retirementTemplate).html();
                var template = Handlebars.compile(source);

                var id = "";
                obj.retage = 65;
                if(typeof(profileUserData.retirementage) != 'undefined' && profileUserData.retirementage != null) {
                    obj.retage = profileUserData.retirementage;
                }
                if(typeof(obj.id) != 'undefined') {
                    id = obj.id;
                }
                if(typeof(userData.advisor) != 'undefined') {
                    userData.user.impersonationMode = true;
                    if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                        obj.permission = true;
                    }
                }
                $(element).append(template(obj));
                $("#" + id + "RetirementCalculateButton").click();
                $('#' + id + 'retirementSaveNeeded').val(false);
            },
            events: {
                "click .createRetirementButton": "createRetirement",
                "click .updateRetirementButton": "updateRetirement",
                "click .cancelRetirementButton": "resetRetirement",
                "click .calculateRetirementButton": "calculateRetirement"
            },

            // Creating the Retirement Goal:
            //------------------------------

            createRetirement: function(event){
                event.preventDefault();
                var retirementname = $('#RetirementInputName').val();
                var retirementage= $('#RetirementAge').val();
                profileUserData.retirementage = retirementage;

                var retirementincome = $('#RetirementIncome').val().replace(/,/g,'');
                var retirementsaved = $('#RetirementSaved').val().replace(/,/g,'');
                var retirementcontribution = $('#RetirementContribution').val().replace(/,/g,'');
                $('#retirementSaveNeeded').val(false);

                var retirementcostincrease = $('#RetirementCostIncrease').val().replace(/,/g,'');
                var retirementlifeexpectancy = $('#RetirementLifeExpectancy').val().replace(/,/g,'');

                if($("#retirementCollapseBox").height() > 0 && needsToClose) {
                    updateCollapse = false;
                    $("#retirementFAQArrow").click();
                    updateCollapse = true;
                }
                $("#retirementLoading").show();

                var formValues = {
                    goalname:retirementname,
                    retirementage:retirementage,
                    monthlyincome:retirementincome,
                    saved:retirementsaved,
                    permonth:retirementcontribution,
                    goalassumptions_1:retirementcostincrease,
                    goalassumptions_2:retirementlifeexpectancy,
                    action:'ADD',
                    goaltype:'RETIREMENT'
                };
                $.ajax({
                    url:userGoalAddUpdateURL,
                    dataType:"json",
                    data:formValues,
                    type:'POST',
                    success:function (jsonData){
                        timeoutPeriod = defaultTimeoutPeriod;
                        require(
                            ['views/score/goals/retirement'],
                            function(addGoalV){
                                $("#newGoals").html('');
                                $("#retirementLoading").hide();
                                var nameSummary = "Retirement Goal";
                                if (jsonData.goal.goalname != "")
                                    nameSummary = jsonData.goal.goalname;

                                var obj = {
                                    id:jsonData.goal.id,
                                    user_id:jsonData.goal.user_id,
                                    index: goalIndex,
                                    goalname:jsonData.goal.goalname,
                                    goalnamesummary:nameSummary,
                                    goaldescription:jsonData.goal.goaldescription,
                                    goaltype:jsonData.goal.goaltype,
                                    goalpriority:jsonData.goal.goalpriority,
                                    goalamount:commaSeparateNumber(jsonData.goal.goalamount),
                                    monthlyincome:commaSeparateNumber(jsonData.goal.monthlyincome),
                                    permonth:commaSeparateNumber(jsonData.goal.permonth),
                                    goalstartdate:jsonData.goal.goalstartdate,
                                    goalenddate:jsonData.goal.goalenddate,
                                    goalstartDay:jsonData.goal.goalstartDay,
                                    goalendDay:jsonData.goal.goalendDay,
                                    goalstartMonth:jsonData.goal.goalstartMonth,
                                    goalendMonth:jsonData.goal.goalendMonth,
                                    goalstartYear:jsonData.goal.goalstartYear,
                                    goalendYear:jsonData.goal.goalendYear,
                                    saved:commaSeparateNumber(jsonData.goal.saved),
                                    downpayment:jsonData.goal.downpayment,
                                    goalimage:jsonData.goal.goalimage,
                                    goalstatus:jsonData.goal.goalstatus,
                                    goalassumptions_1:jsonData.goal.goalassumptions_1,
                                    goalassumptions_2:jsonData.goal.goalassumptions_2,
                                    retage:profileUserData.retirementage
                                };
                                for(var i=0;i<goalSnapshot.length; i++) {
                                    if(goalSnapshot[i].id == "") {
                                        goalSnapshot[i] = obj;
                                    }
                                }
                                addGoalV.render('#existingGoals', obj);
                                init();
                                goalIndex++;

                                financialData.goals[financialData.goals.length] = obj;
                                $("#existingHeader").show();
                                $("#retirementAddGoal").removeClass("active");
                            }
                            );
                    }
                });
            },

            // Updating the Retirement Goal:
            //------------------------------

            updateRetirement: function(event){
                event.preventDefault();
                var name = event.target.id;
                var key = name.substring(0, name.indexOf("UpdateRetirementButton"));
                var retirementname = $('#' + key + 'RetirementInputName').val();
                var retirementage= $('#' + key + 'RetirementAge').val();
                $('#' + key + 'retirementSaveNeeded').val(false);
                profileUserData.retirementage = retirementage;

                var retirementincome = $('#' + key + 'RetirementIncome').val().replace(/,/g,'');
                var retirementsaved = $('#' + key + 'RetirementSaved').val().replace(/,/g,'');
                var retirementcontribution = $('#' + key + 'RetirementContribution').val().replace(/,/g,'');
                var retirementcostincrease = $('#' + key + 'RetirementCostIncrease').val().replace(/,/g,'');
                var retirementlifeexpectancy = $('#' + key + 'RetirementLifeExpectancy').val().replace(/,/g,'');


                if($("#" + key + "retirementCollapseBox").height() > 0 && needsToClose) {
                    updateCollapse = false;
                    $("#" + key + "retirementFAQArrow").click();
                    updateCollapse = true;
                }
                $("#" + key + "retirementLoading").show();

                var formValues = {
                    id:key,
                    goalname:retirementname,
                    retirementage:retirementage,
                    monthlyincome:retirementincome,
                    saved:retirementsaved,
                    permonth:retirementcontribution,
                    goalassumptions_1:retirementcostincrease,
                    goalassumptions_2:retirementlifeexpectancy,
                    action:'UPDATE',
                    goaltype:'RETIREMENT'
                };
                $.ajax({
                    url:userGoalAddUpdateURL,
                    dataType:"json",
                    data:formValues,
                    type:'POST',
                    success:function (jsonData){
                        timeoutPeriod = defaultTimeoutPeriod;
                        for(i=0;i< financialData.goals.length;i++)
                        {
                            if( financialData.goals[i].id == key)
                            {
                                financialData.goals[i].goalamount = commaSeparateNumber(jsonData.goal.goalamount);
                            }
                        }
                    }
                });
                $("#" + key + "retirementLoading").hide();
                var i=0;
                var currentDate = new Date();
                var currentAge = "0000-00-00";
                if(profileUserData.age != null) {
                    currentAge = profileUserData.age;
                }
                var ageBreakdown = currentAge.split('-');
                if(ageBreakdown[0] == "0000") {
                    ageBreakdown[0] = currentDate.getFullYear() - 30;
                }
                if(ageBreakdown[1] == "00") {
                    ageBreakdown[1] = "01";
                }
                if(ageBreakdown[2] == "00") {
                    ageBreakdown[2] = "01";
                }

                var nameSummary = "Retirement Goal";
                if (retirementname != "")
                    nameSummary = retirementname;

                for(i=0;i< financialData.goals.length;i++)
                {
                    if( financialData.goals[i].id == key)
                    {
                        financialData.goals[i].monthlyincome = commaSeparateNumber(retirementincome);
                        financialData.goals[i].goalname = retirementname;
                        financialData.goals[i].goalnamesummary = nameSummary;
                        financialData.goals[i].goalstartdate = currentDate.getFullYear() + "-" + (currentDate.getMonth() + 1) + "-" + currentDate.getDate();
                        financialData.goals[i].goalenddate = (parseInt(ageBreakdown[0]) + parseInt(retirementage)) + "-" + ageBreakdown[1] + "-" + ageBreakdown[2];
                        financialData.goals[i].goalstartDay = currentDate.getDate();
                        financialData.goals[i].goalendDay = parseInt(ageBreakdown[2]);
                        financialData.goals[i].goalstartMonth = currentDate.getMonth() + 1;
                        financialData.goals[i].goalendMonth = parseInt(ageBreakdown[1]);
                        financialData.goals[i].goalstartYear = currentDate.getFullYear();
                        financialData.goals[i].goalendYear =  parseInt(ageBreakdown[0]) + parseInt(retirementage);
                        financialData.goals[i].saved = commaSeparateNumber(retirementsaved);
                        financialData.goals[i].permonth = commaSeparateNumber(retirementcontribution);
                        financialData.goals[i].goalassumptions_1 = retirementcostincrease;
                        financialData.goals[i].goalassumptions_2 = retirementlifeexpectancy;
                        financialData.goals[i].retage = profileUserData.retirementage;
                    }
                }
                $('#' + key + 'RetirementNameSummary').html(nameSummary);
                $("#" + key + 'RetirementIncome').val(commaSeparateNumber(retirementincome));
                $("#" + key + 'RetirementSaved').val(commaSeparateNumber(retirementsaved));
                $("#" + key + 'RetirementContribution').val(commaSeparateNumber(retirementcontribution));
                $('#' + key + 'RetirementCostIncrease').val(retirementcostincrease);
                $('#' + key + 'RetirementLifeExpectancy').val(retirementlifeexpectancy);
            },

            resetRetirement: function(event){
                event.preventDefault();
                var name = event.target.id;
                var key = name.substring(0, name.indexOf("CancelRetirementButton"));
                var i=0;
                for(i=0;i< financialData.goals.length;i++)
                {
                    if( financialData.goals[i].id == key)
                    {
                        $('#' + key + 'RetirementSaved').val(financialData.goals[i].saved);
                        $('#' + key + 'RetirementContribution').val(financialData.goals[i].contributions);
                        $('#' + key + 'RetirementIncome').val( financialData.goals[i].monthlyincome);
                        var retage = 65;
                        if(typeof(profileUserData.retirementage) != 'undefined' && profileUserData.retirementage != null) {
                            retage = profileUserData.retirementage;
                        }
                        $('#' + key + 'RetirementAge').val(retage);
                        $('#' + key + 'RetirementInputName').val( financialData.goals[i].goalname);
                        $('#' + key + 'RetirementCostIncrease').val(financialData.goals[i].goalassumptions_1);
                        if(financialData.goals[i].goalassumptions_2 != null && financialData.goals[i].goalassumptions_2 != "") {
                            $('#' + key + 'RetirementLifeExpectancy').val(financialData.goals[i].goalassumptions_2);
                        }
                        else
                        {
                            $('#' + key + 'RetirementLifeExpectancy').val(financialData.goals[i].lifeEC);
                        }
                        if($("#" + key + "retirementCollapseBox").height() > 0 && needsToClose) {
                            updateCollapse = false;
                            $("#" + key + "retirementFAQArrow").click();
                            updateCollapse = true;
                        }
                    }
                }
                $("#" + key + "RetirementCalculateButton").click();
                $('#' + key + 'retirementSaveNeeded').val(false);

            },
            calculateRetirement: function(event) {
                event.preventDefault();
                var name = event.target.id;
                var key = name.substring(0, name.indexOf("Retirement"));
                $('#' + key + 'retirementSaveNeeded').val(true);
                var retirementname = $('#' + key + 'RetirementInputName').val();
                var retirementage= $('#' + key + 'RetirementAge').val();
                var retirementincome = $('#' + key + 'RetirementIncome').val().replace(/,/g,'');
                var retirementcostincrease = $('#' + key + 'RetirementCostIncrease').val().replace(/,/g, '');

                var silentincome = 0;
                for(var i = 0; i<financialData.silent.length; i++) {
                    if ((financialData.silent[i].accttype == "PENS" || financialData.silent[i].accttype == "SS") && financialData.silent[i].status == 0) {
                        silentincome += parseFloat(financialData.silent[i].amount.replace(/,/g, ''));
                    }
                }

                var currentDate = new Date();
                var ageStr = "0000-00-00";
                if(profileUserData.age != null) {
                    ageStr = profileUserData.age;
                }
                ageArray = ageStr.split('-');
                var retirementYear = parseInt(ageArray[0]);
                var retirementMonth = parseInt(ageArray[1]);
                var retirementDay = parseInt(ageArray[2]);
                if(retirementYear == 0) {
                    retirementYear = currentDate.getFullYear() - 30;
                }
                if(retirementMonth == 0) {
                    retirementMonth = 1;
                }
                if(retirementDay == 0) {
                    retirementDay = 1;
                }

                var startDate = new Date(retirementYear, retirementMonth - 1, retirementDay);
                var endDate = new Date(retirementYear + parseInt(retirementage), retirementMonth - 1, retirementDay);
                if(endDate < currentDate)
                {
                    endDate = currentDate;
                }
                var diff = flexDateDiff(currentDate, endDate);
                var months = diff.years * 12 + diff.months;
                months += ((diff.days > 0) ? 1 : 0);
                months = (months > 0) ? months : 0;


                var diff = flexDateDiff(startDate, endDate);
                var sustainablerate = 1;
                var isSet = false;
                if(profileUserData.rates[0]["age"] > diff.years)
                {
                    sustainablerate = parseFloat(profileUserData.rates[0]["withdrawal"]);
                }
                else if(profileUserData.rates[profileUserData.rates.length - 1]["age"] < diff.years)
                {
                    sustainablerate = parseFloat(profileUserData.rates[profileUserData.rates.length - 1]["withdrawal"]);
                }
                else {
                    for(var i = 0; i<profileUserData.rates.length; i++) {
                        if(profileUserData.rates[i]["age"] == diff.years) {
                            sustainablerate = parseFloat(profileUserData.rates[i]["withdrawal"]);
                            break;
                        }
                    }
                }

                var goalamount = ((retirementincome - silentincome) * 1200 / sustainablerate);

                if(calculateGoals) {
                    for(var i=0;i<goalSnapshot.length; i++) {
                        if(goalSnapshot[i].goalstatus == 1 && goalSnapshot[i].goaltype != 'DEBT' && goalSnapshot[i].id == key) {
                            goalSnapshot[i].goalamount = "" + goalamount;
                            goalSnapshot[i].goalendYear = retirementYear + parseInt(retirementage);
                            goalSnapshot[i].goalendMonth = retirementMonth;
                            goalSnapshot[i].goalendDay = retirementDay;
                            goalSnapshot[i].goalassumptions_1 = retirementcostincrease;
                        }
                    }
                    RecalculateGoalAmounts(false);
                }
                SetCalculatedGoalAmounts();                      
            },
            getKey: function() {
                return "retirement";
            }

        });
        return new retirementView;
    });
