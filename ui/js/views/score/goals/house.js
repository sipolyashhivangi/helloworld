// Filename: views/score/goals/house
define([
    'handlebars',
    'backbone',
    'text!../../../../html/score/goals/house.html',
], function(Handlebars, Backbone, houseTemplate) {
    var houseView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(houseTemplate).html();
            var template = Handlebars.compile(source);
            var id = '';
            if (typeof(obj) != 'undefined')
            {
                obj = SetupGoalDate("house", obj);
                if (obj.downpayment > 0)
                    obj.goalincome = commaSeparateNumber(obj.goalamount.replace(/,/g, '') * 100 / obj.downpayment.replace(/,/g, ''));
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
            $("#" + id + "HouseCalculateButton").click();
            $('#' + id + 'houseSaveNeeded').val(false);
        },
        events: {
            "click .createHouseButton": "createHouse",
            "click .updateHouseButton": "updateHouse",
            "click .cancelHouseButton": "resetHouse",
            "change .houseAccomplishGoal": "toggleHouseAccomplishGoal",
            "change .houseSelectType": "calculateHouse",
            "click .calculateHouseButton": "calculateHouse"
        },
        // Creating the House Goal:
        //------------------------------

        createHouse: function(event) {
            event.preventDefault();
            var housename = $('#HouseInputName').val();

            var houseMonth = $('#HouseMonth option:selected').val();
            var houseDay = $('#HouseDay option:selected').val();
            var houseYear = $('#HouseYear option:selected').val();
            var housedate = houseYear + '-' + houseMonth + '-' + houseDay;
            $('#houseSaveNeeded').val(false);

            var houseAchieve = $('input[name=HouseAchieve]:checked').val();
            var housecontribution = "";
            if (houseAchieve == "0")
                housecontribution = $('#HouseContribution').val().replace(/,/g, '');

            var houseprice = $('#HousePrice').val().replace(/,/g, '');
            var housesaved = $('#HouseSaved').val().replace(/,/g, '');
            var housedownpayment = $('#HouseDownpayment option:selected').val();

            var housecostincrease = $('#HouseCostIncrease').val().replace(/,/g,'');

            if($("#houseCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#houseFAQArrow").click();
                updateCollapse = true;
            }
            $("#houseLoading").show();

            var formValues = {
                goalname: housename,
                goalenddate: housedate,
                goalincome: houseprice,
                saved: housesaved,
                permonth: housecontribution,
                downpayment: housedownpayment,
                goalassumptions_1:housecostincrease,
                action: 'ADD',
                goaltype: 'HOUSE'
            };

            $.ajax({
                url: userGoalAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/goals/house'],
                            function(addGoalV) {
                                $("#newGoals").html('');
                                $("#houseLoading").hide();
                                var nameSummary = "Buy a House";
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
                                    downpayment: jsonData.goal.downpayment,
                                    goalimage: jsonData.goal.goalimage,
                                    goalassumptions_1:jsonData.goal.goalassumptions_1,
                                    goalstatus: jsonData.goal.goalstatus
                                };

                                obj = SetupGoalDate("house", obj);
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
                                $("#houseAddGoal").removeClass("active");
                            }
                    );
                }
            });
        },
        // Updating the House Goal:
        //------------------------------

        updateHouse: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateHouseButton"));
            $('#' + key + 'houseSaveNeeded').val(false);

            var housename = $('#' + key + 'HouseInputName').val();

            var houseMonth = $('#' + key + 'HouseMonth option:selected').val();
            var houseDay = $('#' + key + 'HouseDay option:selected').val();
            var houseYear = $('#' + key + 'HouseYear option:selected').val();
            var housedate = houseYear + '-' + houseMonth + '-' + houseDay;

            var houseAchieve = $('input[name=' + key + 'HouseAchieve]:checked').val();
            var housecontribution = "";
            if (houseAchieve == "0")
                housecontribution = $('#' + key + 'HouseContribution').val().replace(/,/g, '');

            var houseprice = $('#' + key + 'HousePrice').val().replace(/,/g, '');
            var housesaved = $('#' + key + 'HouseSaved').val().replace(/,/g, '');
            var housedownpayment = $('#' + key + 'HouseDownpayment option:selected').val();
            var housecostincrease = $('#' + key + 'HouseCostIncrease').val().replace(/,/g,'');

            if($("#" + key + "houseCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "houseFAQArrow").click();
                updateCollapse = true;
            }
            $("#" + key + "houseLoading").show();

            var formValues = {
                id: key,
                goalname: housename,
                goalenddate: housedate,
                goalincome: houseprice,
                saved: housesaved,
                permonth: housecontribution,
                downpayment: housedownpayment,
                goalassumptions_1:housecostincrease,
                action: 'UPDATE',
                goaltype: 'HOUSE'
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
            $("#" + key + "houseLoading").hide();
            var i = 0;
            var currentDate = new Date();
            var ageBreakdown = housedate.split('-');
            var nameSummary = "Buy a House";
            if (housename != "")
                nameSummary = housename;
            for (i = 0; i < financialData.goals.length; i++)
            {
                if (financialData.goals[i].id == key)
                {
                    financialData.goals[i].goalamount = commaSeparateNumber(houseprice * housedownpayment / 100);
                    financialData.goals[i].goalname = housename;
                    financialData.goals[i].goalnamesummary = nameSummary;
                    financialData.goals[i].goalstartdate = currentDate.getFullYear() + "-" + (currentDate.getMonth() + 1) + "-" + currentDate.getDate();
                    financialData.goals[i].goalenddate = ageBreakdown[0] + "-" + ageBreakdown[1] + "-" + ageBreakdown[2];
                    financialData.goals[i].goalstartDay = currentDate.getDate();
                    financialData.goals[i].goalendDay = ageBreakdown[2];
                    financialData.goals[i].goalstartMonth = currentDate.getMonth() + 1;
                    financialData.goals[i].goalendMonth = ageBreakdown[1];
                    financialData.goals[i].goalstartYear = currentDate.getFullYear();
                    financialData.goals[i].goalendYear = ageBreakdown[0];
                    financialData.goals[i].saved = commaSeparateNumber(housesaved);
                    financialData.goals[i].permonth = commaSeparateNumber(housecontribution);
                    financialData.goals[i].downpayment = housedownpayment;
                    financialData.goals[i].goalassumptions_1 = housecostincrease;
                }
            }
            $("#" + key + 'HouseNameSummary').html(nameSummary);
            $("#" + key + 'HousePrice').val(commaSeparateNumber(houseprice));
            $("#" + key + 'HouseSaved').val(commaSeparateNumber(housesaved));
            $("#" + key + 'HouseContribution').val(commaSeparateNumber(housecontribution));
            $('#' + key + 'HouseCostIncrease').val(housecostincrease);
        },
        resetHouse: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelHouseButton"));
            var i = 0;
            for (i = 0; i < financialData.goals.length; i++)
            {
                if (financialData.goals[i].id == key)
                {
                    $('#' + key + 'HouseMonth').val(financialData.goals[i].goalendMonth);
                    $('#' + key + 'HouseDay').val(financialData.goals[i].goalendDay);
                    $('#' + key + 'HouseYear').val(financialData.goals[i].goalendYear);
                    $('#' + key + 'HouseContribution').val(financialData.goals[i].permonth);
                    $('#' + key + 'HouseSaved').val(financialData.goals[i].saved);
                    $('#' + key + 'HouseContri').val(financialData.goals[i].contributions);
                    $('#' + key + 'HouseDownpayment').val(financialData.goals[i].downpayment);
                    $('#' + key + 'HouseCostIncrease').val(financialData.goals[i].goalassumptions_1);

                    if (financialData.goals[i].permonth.replace(/,/g, '') > 0)
                    {
                        $("input:radio[name=" + key + "HouseAchieve]")[0].checked = false;
                        $("input:radio[name=" + key + "HouseAchieve]")[1].checked = true;
                        $('#' + key + 'HouseDateDiv').hide();
                        $('#' + key + 'HouseContributionDiv').show();
                    }
                    else
                    {
                        $("input:radio[name=" + key + "HouseAchieve]")[0].checked = true;
                        $("input:radio[name=" + key + "HouseAchieve]")[1].checked = false;
                        $('#' + key + 'HouseDateDiv').show();
                        $('#' + key + 'HouseContributionDiv').hide();
                    }

                    if (financialData.goals[i].downpayment > 0)
                        $('#' + key + 'HousePrice').val(commaSeparateNumber(financialData.goals[i].goalamount.replace(/,/g, '') * 100 / financialData.goals[i].downpayment.replace(/,/g, '')));
                    else
                        $('#' + key + 'HousePrice').val(financialData.goals[i].goalamount);
                    $('#' + key + 'HouseInputName').val(financialData.goals[i].goalname);
                    if($("#" + key + "houseCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "houseFAQArrow").click();
                        updateCollapse = true;
                    }

                }
            }
            $("#" + key + "HouseCalculateButton").click();
            $('#' + key + 'houseSaveNeeded').val(false);
        },
        toggleHouseAccomplishGoal: function(event) {
            event.preventDefault();
            var name = event.target.name;
            var key = name.substring(0, name.indexOf("HouseAchieve"));

            if ($('#' + key + 'HouseDateGoal').is(":checked"))
            {
                $('#' + key + 'HouseContributionDiv').hide();
                $('#' + key + 'HouseDateDiv').show();
            }
            else
            {
                $('#' + key + 'HouseDateDiv').hide();
                $('#' + key + 'HouseContributionDiv').show();
            }
            $("#" + key+ "HouseCalculateButton").click();
        },
        calculateHouse: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("House"));
            $('#' + key + 'houseSaveNeeded').val(true);
            var houseMonth = $('#' + key + 'HouseMonth option:selected').val();
            var houseDay = $('#' + key + 'HouseDay option:selected').val();
            var houseYear = $('#' + key + 'HouseYear option:selected').val();
            if($('#' + key + 'HouseMonth').is('input')) {
                houseMonth = $('#' + key + 'HouseMonth').val();
                houseDay = $('#' + key + 'HouseDay').val();
                houseYear = $('#' + key + 'HouseYear').val();
            }

            var housedate = houseYear + '-' + houseMonth + '-' + houseDay;

            var houseAchieve = $('input[name=' + key + 'HouseAchieve]:checked').val();
            var housecontribution = "";
            if (houseAchieve == "0")
                housecontribution = $('#' + key + 'HouseContribution').val().replace(/,/g, '');

            var houseprice = $('#' + key + 'HousePrice').val().replace(/,/g, '');
            var housedownpayment = $('#' + key + 'HouseDownpayment option:selected').val();
            if($('#' + key + 'HouseDownpayment').is('input')) {
                housedownpayment = $('#' + key + 'HouseDownpayment').val();
            }
            var goalamount = (houseprice * housedownpayment / 100);
            var housecostincrease = $('#' + key + 'HouseCostIncrease').val().replace(/,/g, '');

            if(calculateGoals) {
                for(var i=0;i<goalSnapshot.length; i++) {
                    if(goalSnapshot[i].goalstatus == 1 && goalSnapshot[i].goaltype != 'DEBT' && goalSnapshot[i].id == key) {
                        goalSnapshot[i].goalamount = "" + goalamount;
                        goalSnapshot[i].goalendYear = houseYear;
                        goalSnapshot[i].goalendMonth = houseMonth;
                        goalSnapshot[i].goalendDay = houseDay;
                        goalSnapshot[i].permonth = housecontribution;
                        goalSnapshot[i].goalassumptions_1 = housecostincrease;
                    }
                }
                RecalculateGoalAmounts(false);
            }
            SetCalculatedGoalAmounts();				         
        },
        getKey: function() {
            return "house";
        }

    });
    return new houseView;
});