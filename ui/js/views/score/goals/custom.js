// Filename: views/score/goals/custom
define([
    'handlebars',
    'backbone',
    'text!../../../../html/score/goals/custom.html',
], function(Handlebars, Backbone, customTemplate) {
    var customView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(customTemplate).html();
            var template = Handlebars.compile(source);
            var id = '';
            if (typeof(obj) != 'undefined')
            {
                obj = SetupGoalDate("custom", obj);
                if(typeof(obj.id) != 'undefined') {
                    id = obj.id;
                }
            }
            if(typeof(userData.advisor) != 'undefined') {
                userData.user.impersonationMode = true;
                if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                    obj.permission = true;
                }
            }
            $(element).append(template(obj));
            $("#" + id + "CustomCalculateButton").click();
            $('#' + id + 'customSaveNeeded').val(false);
        },
        events: {
            "click .createCustomButton": "createCustom",
            "click .updateCustomButton": "updateCustom",
            "click .cancelCustomButton": "resetCustom",
            "change .customAccomplishGoal": "toggleCustomAccomplishGoal",
            "change .customSelectType": "calculateCustom",
            "click .calculateCustomButton": "calculateCustom"
        },
        // Creating the Custom Goal:
        //------------------------------

        createCustom: function(event) {
            event.preventDefault();
            $('#customSaveNeeded').val(false);

            var customname = $('#CustomInputName').val();

            var customMonth = $('#CustomMonth option:selected').val();
            var customDay = $('#CustomDay option:selected').val();
            var customYear = $('#CustomYear option:selected').val();
            var customdate = customYear + '-' + customMonth + '-' + customDay;

            var customAchieve = $('input[name=CustomAchieve]:checked').val();
            var customcontribution = ""
            if (customAchieve == "0")
                customcontribution = $('#CustomContribution').val().replace(/,/g, '');
            var customneeded = $('#CustomNeeded').val().replace(/,/g, '');
            var customsaved = $('#CustomSaved').val().replace(/,/g, '');
            var customcostincrease = $('#CustomCostIncrease').val().replace(/,/g,'');

            if($("#customCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#customFAQArrow").click();
                updateCollapse = true;
            }
            $("#customLoading").show();

            var formValues = {
                goalname: customname,
                goalenddate: customdate,
                saved: customsaved,
                goalamount: customneeded,
                permonth: customcontribution,
                goalassumptions_1:customcostincrease,
                action: 'ADD',
                goaltype: 'CUSTOM'
            };

            $.ajax({
                url: userGoalAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/goals/custom'],
                            function(addGoalV) {
                                $("#newGoals").html('');
                                $("#customLoading").hide();
                                var nameSummary = "Custom";
                                if (jsonData.goal.goalname != "")
                                    nameSummary = jsonData.goal.goalname;

                                var obj = {
                                    id: jsonData.goal.id,
                                    user_id: jsonData.goal.user_id,
                                    index: goalIndex,
                                    goalname: jsonData.goal.goalname,
                                    goalnamesummary: nameSummary,
                                    goaldescription: jsonData.goal.goaldescription,
                                    goaltype: jsonData.goal.goaltype,
                                    goalpriority: jsonData.goal.goalpriority,
                                    goalamount: commaSeparateNumber(jsonData.goal.goalamount),
                                    permonth: commaSeparateNumber(jsonData.goal.permonth),
                                    goalstartdate: jsonData.goal.goalstartdate,
                                    goalenddate: jsonData.goal.goalenddate,
                                    goalstartDay: jsonData.goal.goalstartDay,
                                    goalendDay: jsonData.goal.goalendDay,
                                    goalstartMonth: jsonData.goal.goalstartMonth,
                                    goalendMonth: jsonData.goal.goalendMonth,
                                    goalstartYear: jsonData.goal.goalstartYear,
                                    goalendYear: jsonData.goal.goalendYear,
                                    saved: commaSeparateNumber(jsonData.goal.saved),
                                    goalassumptions_1:jsonData.goal.goalassumptions_1,
                                    goalstatus: jsonData.goal.goalstatus
                                };
                                obj = SetupGoalDate("custom", obj);

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
                                $("#customAddGoal").removeClass("active");
                            }
                    );



                }
            });
        },
        // Updating the Custom Goal:
        //------------------------------

        updateCustom: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateCustomButton"));
            $('#' + key + 'customSaveNeeded').val(false);

            var customname = $('#' + key + 'CustomInputName').val();

            var customMonth = $('#' + key + 'CustomMonth option:selected').val();
            var customDay = $('#' + key + 'CustomDay option:selected').val();
            var customYear = $('#' + key + 'CustomYear option:selected').val();
            var customdate = customYear + '-' + customMonth + '-' + customDay;

            var customAchieve = $('input[name=' + key + 'CustomAchieve]:checked').val();
            var customcontribution = ""
            if (customAchieve == "0")
                customcontribution = $('#' + key + 'CustomContribution').val().replace(/,/g, '');
            var customneeded = $('#' + key + 'CustomNeeded').val().replace(/,/g, '');
            var customsaved = $('#' + key + 'CustomSaved').val().replace(/,/g, '');
            var customcostincrease = $('#' + key + 'CustomCostIncrease').val().replace(/,/g,'');

            if($("#" + key + "customCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "customFAQArrow").click();
                updateCollapse = true;
            }
            $("#" + key + "customLoading").show();

            var formValues = {
                id: key,
                goalname: customname,
                goalenddate: customdate,
                saved: customsaved,
                goalamount: customneeded,
                permonth: customcontribution,
                goalassumptions_1: customcostincrease,
                action: 'UPDATE',
                goaltype: 'CUSTOM'
            };


            $.ajax({
                url: userGoalAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                }
            });
            $("#" + key + "customLoading").hide();
            var i = 0;
            var currentDate = new Date();
            var ageBreakdown = customdate.split('-');
            var nameSummary = "Custom";
            if (customname != "")
                nameSummary = customname;
            for (i = 0; i < financialData.goals.length; i++)
            {
                if (financialData.goals[i].id == key)
                {
                    financialData.goals[i].goalamount = commaSeparateNumber(customneeded);
                    financialData.goals[i].goalname = customname;
                    financialData.goals[i].goalnamesummary = nameSummary;
                    financialData.goals[i].goalstartdate = currentDate.getFullYear() + "-" + (currentDate.getMonth() + 1) + "-" + currentDate.getDate();
                    financialData.goals[i].goalenddate = ageBreakdown[0] + "-" + ageBreakdown[1] + "-" + ageBreakdown[2];
                    financialData.goals[i].goalstartDay = currentDate.getDate();
                    financialData.goals[i].goalendDay = ageBreakdown[2];
                    financialData.goals[i].goalstartMonth = currentDate.getMonth() + 1;
                    financialData.goals[i].goalendMonth = ageBreakdown[1];
                    financialData.goals[i].goalstartYear = currentDate.getFullYear();
                    financialData.goals[i].goalendYear = ageBreakdown[0];
                    financialData.goals[i].saved = commaSeparateNumber(customsaved);
                    financialData.goals[i].permonth = commaSeparateNumber(customcontribution);
                    financialData.goals[i].goalassumptions_1 = customcostincrease;

                }
            }
            $("#" + key + 'CustomNameSummary').html(nameSummary);
            $("#" + key + 'CustomNeeded').val(commaSeparateNumber(customneeded));
            $("#" + key + 'CustomSaved').val(commaSeparateNumber(customsaved));
            $("#" + key + 'CustomContribution').val(commaSeparateNumber(customcontribution));
            $('#' + key + 'CustomCostIncrease').val(customcostincrease);
        },
        resetCustom: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelCustomButton"));
            var i = 0;
            for (i = 0; i < financialData.goals.length; i++)
            {
                if (financialData.goals[i].id == key)
                {
                    $('#' + key + 'CustomMonth').val(financialData.goals[i].goalendMonth);
                    $('#' + key + 'CustomDay').val(financialData.goals[i].goalendDay);
                    $('#' + key + 'CustomYear').val(financialData.goals[i].goalendYear);
                    $('#' + key + 'CustomContribution').val(financialData.goals[i].permonth);
                    $('#' + key + 'CustomCostIncrease').val(financialData.goals[i].goalassumptions_1);

                    if (financialData.goals[i].permonth.replace(/,/g, '') > 0)
                    {
                        $("input:radio[name=" + key + "CustomAchieve]")[0].checked = false;
                        $("input:radio[name=" + key + "CustomAchieve]")[1].checked = true;
                        $('#' + key + 'CustomDateDiv').hide();
                        $('#' + key + 'CustomContributionDiv').show();
                    }
                    else
                    {
                        $("input:radio[name=" + key + "CustomAchieve]")[0].checked = true;
                        $("input:radio[name=" + key + "CustomAchieve]")[1].checked = false;
                        $('#' + key + 'CustomDateDiv').show();
                        $('#' + key + 'CustomContributionDiv').hide();
                    }

                    $('#' + key + 'CustomSaved').val(financialData.goals[i].saved);
                    $('#' + key + 'CustomContri').val(financialData.goals[i].contributions);
                    $('#' + key + 'CustomNeeded').val(financialData.goals[i].goalamount);
                    $('#' + key + 'CustomInputName').val(financialData.goals[i].goalname);
                    if($("#" + key + "customCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "customFAQArrow").click();
                        updateCollapse = true;
                    }
                }
            }
            $("#" + key + "CustomCalculateButton").click();
            $('#' + key + 'customSaveNeeded').val(false);
        },
        toggleCustomAccomplishGoal: function(event) {
            event.preventDefault();
            var name = event.target.name;
            var key = name.substring(0, name.indexOf("CustomAchieve"));

            if ($('#' + key + 'CustomDateGoal').is(":checked"))
            {
                $('#' + key + 'CustomContributionDiv').hide();
                $('#' + key + 'CustomDateDiv').show();
            }
            else
            {
                $('#' + key + 'CustomDateDiv').hide();
                $('#' + key + 'CustomContributionDiv').show();
            }
            $("#" + key + "CustomCalculateButton").click();
        },
        calculateCustom: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("Custom"));
            $('#' + key + 'customSaveNeeded').val(true);
            var customname = $('#' + key + 'CustomInputName').val();
            var customMonth = $('#' + key + 'CustomMonth option:selected').val();
            var customDay = $('#' + key + 'CustomDay option:selected').val();
            var customYear = $('#' + key + 'CustomYear option:selected').val();
            if($('#' + key + 'CustomMonth').is('input')) {
                customMonth = $('#' + key + 'CustomMonth').val();
                customDay = $('#' + key + 'CustomDay').val();
                customYear = $('#' + key + 'CustomYear').val();
            }

            var customdate = customYear + '-' + customMonth + '-' + customDay;

            var customAchieve = $('input[name=' + key + 'CustomAchieve]:checked').val();
            var customcontribution = "";
            if (customAchieve == "0")
                customcontribution = $('#' + key + 'CustomContribution').val().replace(/,/g, '');
            var customneeded = $('#' + key + 'CustomNeeded').val().replace(/,/g, '');
            var customcostincrease = $('#' + key + 'CustomCostIncrease').val().replace(/,/g, '');

            if(calculateGoals) {
                for(var i=0;i<goalSnapshot.length; i++) {
                    if(goalSnapshot[i].goalstatus == 1 && goalSnapshot[i].goaltype != 'DEBT' && goalSnapshot[i].id == key) {
                        goalSnapshot[i].goalamount = customneeded;
                        goalSnapshot[i].goalendYear = customYear;
                        goalSnapshot[i].goalendMonth = customMonth;
                        goalSnapshot[i].goalendDay = customDay;
                        goalSnapshot[i].permonth = customcontribution;
                        goalSnapshot[i].goalassumptions_1 = customcostincrease;
                    }
                }
                RecalculateGoalAmounts(false);
            }
            SetCalculatedGoalAmounts();				         
        },
        getKey: function() {
            return "custom";
        }

    });
    return new customView;
});