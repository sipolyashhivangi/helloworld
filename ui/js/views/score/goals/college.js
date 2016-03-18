// Filename: views/score/goals/college
define([
    'handlebars',
    'backbone',
    'text!../../../../html/score/goals/college.html',
], function(Handlebars, Backbone, collegeTemplate) {
    var collegeView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {

            var source = $(collegeTemplate).html();
            var template = Handlebars.compile(source);
            var id = "";
            if (typeof(obj) != 'undefined')
            {
                obj = SetupGoalDate("college", obj);
                if (obj.collegeyears > 0)
                    obj.goalincome = commaSeparateNumber(obj.goalamount.replace(/,/g, '') / obj.collegeyears.replace(/,/g, ''));
                else
                    obj.goalincome = obj.goalamount;
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
            $("#" + id + "CollegeCalculateButton").click();
            $('#' + id + 'collegeSaveNeeded').val(false);
        },
        events: {
            "click .createCollegeButton": "createCollege",
            "click .updateCollegeButton": "updateCollege",
            "click .cancelCollegeButton": "resetCollege",
            "change .collegeSelectType": "calculateCollege",
            "click .calculateCollegeButton": "calculateCollege"
        },
        // Creating the College Goal:
        //------------------------------

        createCollege: function(event) {
            event.preventDefault();
            $('#collegeSaveNeeded').val(false);
            var collegename = $('#CollegeInputName').val();
            var collegecost = $('#CollegeCost').val().replace(/,/g, '');

            var collegeMonth = $('#CollegeMonth option:selected').val();
            var collegeDay = 1;
            var collegeYear = $('#CollegeYear option:selected').val();
            var collegedate = collegeYear + '-' + collegeMonth + '-' + collegeDay;

            var collegeyears = $('#CollegeYears').val();
            var collegesaved = $('#CollegeSaved').val().replace(/,/g, '');
            var collegecontribution = $('#CollegeContribution').val().replace(/,/g, '');
            var collegecostincrease = $('#CollegeCostIncrease').val().replace(/,/g,'');

            if($("#collegeCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#collegeFAQArrow").click();
                updateCollapse = true;
            }
            $("#collegeLoading").show();

            var formValues = {
                goalname: collegename,
                goalincome: collegecost,
                saved: collegesaved,
                permonth: collegecontribution,
                goalenddate: collegedate,
                collegeyears: collegeyears,
                goalassumptions_1:collegecostincrease,
                action: 'ADD',
                goaltype: 'COLLEGE'
            };

            $.ajax({
                url: userGoalAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/goals/college'],
                            function(addGoalV) {
                                $("#collegeLoading").hide();
                                $("#newGoals").html('');
                                var nameSummary = "Save For College";
                                if (jsonData.goal.goalname != "")
                                    nameSummary = jsonData.goal.goalname;

                                var obj = {
                                    id: jsonData.goal.id,
                                    user_id: jsonData.goal.user_id,
                                    index: goalIndex,
                                    goalname: jsonData.goal.goalname,
                                    goalnamesummary:nameSummary,
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
                                    downpayment: jsonData.goal.downpayment,
                                    collegeyears: jsonData.goal.collegeyears,
                                    goalimage: jsonData.goal.goalimage,
                                    goalassumptions_1: jsonData.goal.goalassumptions_1,
                                    goalstatus: jsonData.goal.goalstatus
                                };
                                obj = SetupGoalDate("college", obj);

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
                                $("#collegeAddGoal").removeClass("active");
                            }
                    );


                }
            });
        },
        // Updating the College Goal:
        //------------------------------

        updateCollege: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateCollegeButton"));
            $('#' + key + 'collegeSaveNeeded').val(false);

            var collegename = $('#' + key + 'CollegeInputName').val();
            var collegecost = $('#' + key + 'CollegeCost').val().replace(/,/g, '');

            var collegeMonth = $('#' + key + 'CollegeMonth option:selected').val();
            var collegeDay = 1;
            var collegeYear = $('#' + key + 'CollegeYear option:selected').val();
            var collegedate = collegeYear + '-' + collegeMonth + '-' + collegeDay;

            var collegeyears = $('#' + key + 'CollegeYears').val();
            var collegesaved = $('#' + key + 'CollegeSaved').val().replace(/,/g, '');
            var collegecontribution = $('#' + key + 'CollegeContribution').val().replace(/,/g, '');
            var collegecostincrease = $('#' + key + 'CollegeCostIncrease').val().replace(/,/g,'');

            if($("#" + key + "collegeCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "collegeFAQArrow").click();
                updateCollapse = true;
            }
            $("#" + key + "collegeLoading").show();

            var formValues = {
                id: key,
                goalname: collegename,
                goalincome: collegecost,
                saved: collegesaved,
                goalenddate: collegedate,
                collegeyears: collegeyears,
                permonth: collegecontribution,
                goalassumptions_1:collegecostincrease,
                action: 'UPDATE',
                goaltype: 'COLLEGE'
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
            $("#" + key + "collegeLoading").hide();
            var i = 0;
            var currentDate = new Date();
            var ageBreakdown = collegedate.split('-');
            var nameSummary = "Save For College";
            if (collegename != "")
                nameSummary = collegename;
            for (i = 0; i < financialData.goals.length; i++)
            {
                if (financialData.goals[i].id == key)
                {
                    financialData.goals[i].goalamount = commaSeparateNumber(collegeyears*collegecost);
                    financialData.goals[i].goalname = collegename;
                    financialData.goals[i].goalnamesummary = nameSummary;
                    financialData.goals[i].goalstartdate = currentDate.getFullYear() + "-" + (currentDate.getMonth() + 1) + "-" + currentDate.getDate();
                    financialData.goals[i].goalenddate = ageBreakdown[0] + "-" + ageBreakdown[1] + "-" + ageBreakdown[2];
                    financialData.goals[i].goalstartDay = currentDate.getDate();
                    financialData.goals[i].goalendDay = ageBreakdown[2];
                    financialData.goals[i].goalstartMonth = currentDate.getMonth() + 1;
                    financialData.goals[i].goalendMonth = ageBreakdown[1];
                    financialData.goals[i].goalstartYear = currentDate.getFullYear();
                    financialData.goals[i].goalendYear = ageBreakdown[0];
                    financialData.goals[i].collegeyears = collegeyears;
                    financialData.goals[i].saved = commaSeparateNumber(collegesaved);
                    financialData.goals[i].permonth = commaSeparateNumber(collegecontribution);
                    financialData.goals[i].goalassumptions_1 = collegecostincrease;
                }
            }
            $("#" + key + 'CollegeNameSummary').html(nameSummary);
            $("#" + key + 'CollegeCost').val(commaSeparateNumber(collegecost));
            $("#" + key + 'CollegeSaved').val(commaSeparateNumber(collegesaved));
            $("#" + key + 'CollegeContribution').val(commaSeparateNumber(collegecontribution));
            $('#' + key + 'CollegeCostIncrease').val(collegecostincrease);
        },
        resetCollege: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelCollegeButton"));
            var i = 0;
            for (i = 0; i < financialData.goals.length; i++)
            {
                if (financialData.goals[i].id == key)
                {
                    $('#' + key + 'CollegeMonth').val(financialData.goals[i].goalendMonth);
                    $('#' + key + 'CollegeYear').val(financialData.goals[i].goalendYear);
                    $('#' + key + 'CollegeYears').val(financialData.goals[i].collegeyears);
                    $('#' + key + 'CollegeContribution').val(financialData.goals[i].contributions);
                    $('#' + key + 'CollegeSaved').val(financialData.goals[i].saved);
                    $('#' + key + 'CollegeCostIncrease').val(financialData.goals[i].goalassumptions_1);

                    if (financialData.goals[i].collegeyears > 0)
                        $('#' + key + 'CollegeCost').val(commaSeparateNumber(financialData.goals[i].goalamount.replace(/,/g, '') / financialData.goals[i].collegeyears.replace(/,/g, '')));
                    else
                        $('#' + key + 'CollegeCost').val(financialData.goals[i].goalamount);
                    $('#' + key + 'CollegeInputName').val(financialData.goals[i].goalname);
                    if($("#" + key + "collegeCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "collegeFAQArrow").click();
                        updateCollapse = true;
                    }
                }
            }
            $("#" + key + "CollegeCalculateButton").click();
            $('#' + key + 'collegeSaveNeeded').val(false);
        },
        calculateCollege: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("College"));
            $('#' + key + 'collegeSaveNeeded').val(true);


            var collegename = $('#' + key + 'CollegeInputName').val();

            var collegecost = $('#' + key + 'CollegeCost').val().replace(/,/g, '');

            var collegeMonth = $('#' + key + 'CollegeMonth option:selected').val();
            var collegeDay = 1;
            var collegeYear = $('#' + key + 'CollegeYear option:selected').val();
            if($('#' + key + 'CollegeMonth').is('input')) {
                collegeMonth = $('#' + key + 'CollegeMonth').val();
                collegeYear = $('#' + key + 'CollegeYear').val();
            }
            var collegedate = collegeYear + '-' + collegeMonth + '-' + collegeDay;

            var collegeyears = $('#' + key + 'CollegeYears').val();
            var collegecostincrease = $('#' + key + 'CollegeCostIncrease').val().replace(/,/g, '');
            var goalamount = collegecost * collegeyears;

            if(calculateGoals) {
                for(var i=0;i<goalSnapshot.length; i++) {
                    if(goalSnapshot[i].goalstatus == 1 && goalSnapshot[i].goaltype != 'DEBT' && goalSnapshot[i].id == key) {
                        goalSnapshot[i].goalamount = "" + goalamount;
                        goalSnapshot[i].goalendDay = 1;
                        goalSnapshot[i].goalendYear = collegeYear;
                        goalSnapshot[i].goalendMonth = collegeMonth;
                        goalSnapshot[i].goalassumptions_1 = collegecostincrease;
                    }
                }
                RecalculateGoalAmounts(false);
            }
            SetCalculatedGoalAmounts();				         
        },
        getKey: function() {
            return "college";
        }

    });
    return new collegeView;
});