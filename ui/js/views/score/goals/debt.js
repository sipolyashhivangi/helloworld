// Filename: views/score/goals/debt
define([
    'handlebars',
    'backbone',
    'text!../../../../html/score/goals/debt.html',
    ], function(Handlebars, Backbone, debtTemplate){
        var debtView = Backbone.View.extend({
            el: $("#body"),
            render: function(element, obj){
                var source = $(debtTemplate).html();
                var template = Handlebars.compile(source);
                var id = "";
                if(typeof(obj) != 'undefined')
                {
                    obj = SetupGoalDate("debt", obj);
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
                $("#" + id + "DebtCalculateButton").click();
                $('#' + id + 'debtSaveNeeded').val(false);
            },
            events: {
                "click .createDebtButton": "createDebt",
                "click .updateDebtButton": "updateDebt",
                "click .cancelDebtButton": "resetDebt",
                "change .debtButton": "toggleDebt",
                "change .debtSelection":"debtDebt",
                "change .debtAccomplishGoal": "toggleDebtAccomplishGoal",
                "change .debtSelectType": "calculateDebt",
                "click .calculateDebtButton": "calculateDebt"
            },

            // Creating the Debt Goal:
            //------------------------------

            createDebt: function(event){
                event.preventDefault();
                $('#debtSaveNeeded').val(false);
                var debtname = $('#DebtInputName').val();

                var debtChoice = $('input[name=DebtChoice]:checked').val();
                var payoffdebt = "";
                if (debtChoice == 1){
                    $("input[name=DebtCheck]").each(function () {
                        if($(this).is(':checked')){
                            var idAmt = $(this).val().toString().split('#');
                            var id = idAmt[0];
                            payoffdebt = payoffdebt+""+id+",";
                        }
                    });
                }
                var debtMonth = $('#DebtMonth option:selected').val();
                var debtDay = $('#DebtDay option:selected').val();
                var debtYear = $('#DebtYear option:selected').val();
                var debtdate = "";
                if (debtMonth != "" && debtDay != "" && debtYear != ""){
                    debtdate = debtYear + '-' + debtMonth + '-' + debtDay;
                }

                var debtminimumpayments = $('#DebtMinimumPayments').val().replace(/,/g,'');
                var debtorderofpay = $('#DebtPayoffType option:selected').val();

                var debtAchieve = $('input[name=DebtAchieve]:checked').val();
                var debtcontribution = "";
                if(debtAchieve == "0")
                    debtcontribution = $('#DebtContribution').val().replace(/,/g,'');

                if($("#debtCollapseBox").height() > 0 && needsToClose) {
                    updateCollapse = false;
                    $("#debtFAQArrow").click();
                    updateCollapse = true;
                }
                $("#debtLoading").show();
                $("#debtAddGoal").hide();

                var formValues = {
                    goalname:debtname,
                    payoffdebts:payoffdebt,
                    goalenddate:debtdate,
                    permonth:debtcontribution,
                    goalassumptions_1:debtminimumpayments,
                    goalassumptions_2:debtorderofpay,
                    action:'ADD',
                    goaltype:'DEBT'
                };
                $.ajax({
                    url:userGoalAddUpdateURL,
                    dataType:"json",
                    data:formValues,
                    type:'POST',
                    success:function (jsonData){
                        timeoutPeriod = defaultTimeoutPeriod;
                        require(
                            ['views/score/goals/debt'],
                            function(addGoalV){
                                $("#newGoals").html('');
                                $("#debtLoading").hide();
                                var nameSummary = "Pay Off Debt";
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
                                    permonth:commaSeparateNumber(jsonData.goal.permonth),
                                    payoffdebts:jsonData.goal.payoffdebts,
                                    goalstartdate:jsonData.goal.goalstartdate,
                                    goalenddate:jsonData.goal.goalenddate,
                                    goalstartDay:jsonData.goal.goalstartDay,
                                    goalendDay:jsonData.goal.goalendDay,
                                    goalstartMonth:jsonData.goal.goalstartMonth,
                                    goalendMonth:jsonData.goal.goalendMonth,
                                    goalstartYear:jsonData.goal.goalstartYear,
                                    goalendYear:jsonData.goal.goalendYear,
                                    goalstatus:jsonData.goal.goalstatus,
                                    goalassumptions_1:jsonData.goal.goalassumptions_1,
                                    goalassumptions_2:jsonData.goal.goalassumptions_2,
                                    debts:financialData.debts,
                                    debtTotal:financialData.debtTotal
                                };

                                var debtTotal = 0;
                                for(var i = 0; i<obj.debts.length; i++)
                                {
                                    var payoffdebts = jsonData.goal.payoffdebts.split(',');
                                    if(obj.debts[i].status == 0 && obj.debts[i].monthly_payoff_balances == 0 && (payoffdebts.indexOf(obj.debts[i].id) > -1 || jsonData.goal.payoffdebts == ""))
                                    {
                                        debtTotal += obj.debts[i].amount.replace(/,/g,'');
                                        obj.debts[i].payoffdebts = true;
                                    }
                                    else
                                    {
                                        obj.debts[i].payoffdebts = false;
                                    }
                                    obj.debts[i].goalid = obj.id;
                                }
                                obj["debtTotal"] = commaSeparateNumber(debtTotal, 0);
                                obj = SetupGoalDate("debt", obj);
                                addGoalV.render('#existingGoals', obj);
                                init();
                                goalIndex++;
                                financialData.goals[financialData.goals.length] = obj;
                                $("#existingHeader").show();
                                $("#debtAddGoal").removeClass("active");
                            }
                        );
                    }
                });
            },

            // Updating the Debt Goal:
            //------------------------------

            updateDebt: function(event){
                event.preventDefault();
                var name = event.target.id;
                var key = name.substring(0, name.indexOf("UpdateDebtButton"));
                $('#' + key + 'debtSaveNeeded').val(false);
                var debtname = $("#" + key + 'DebtInputName').val();
                var debtChoice = $('input[name=' + key + 'DebtChoice]:checked').val();

                var payoffdebt = "";
                var totalDebt = 0;
                $("input[name=" + key + "DebtCheck]").each(function () {
                    var idAmt = $(this).val().toString().split('#');
                    if (debtChoice == 1){
                        if($(this).is(':checked')){
                            var id = idAmt[0];
                            totalDebt += parseFloat(idAmt[1].replace(/,/g, ''));
                            payoffdebt = payoffdebt+""+id+",";
                        }
                    }
                    else
                    {
                        totalDebt += parseFloat(idAmt[1].replace(/,/g, ''));
                    }
                });
                var debtMonth = $('#' + key + 'DebtMonth option:selected').val();
                var debtDay = $('#' + key + 'DebtDay option:selected').val();
                var debtYear = $('#' + key + 'DebtYear option:selected').val();
                var debtdate = debtYear + '-' + debtMonth + '-' + debtDay;

                var debtminimumpayments = $('#' + key + 'DebtMinimumPayments').val().replace(/,/g,'');
                var debtorderofpay = $('#' + key + 'DebtPayoffType option:selected').val();

                var debtAchieve = $('input[name=' + key + 'DebtAchieve]:checked').val();
                var debtcontribution = "";
                if(debtAchieve == "0")
                    debtcontribution = $('#' + key + 'DebtContribution').val().replace(/,/g,'');

                if($("#" + key + "debtCollapseBox").height() > 0 && needsToClose) {
                    updateCollapse = false;
                    $("#" + key + "debtFAQArrow").click();
                    updateCollapse = true;
                }
                $("#" + key + "debtLoading").show();

                var formValues = {
                    id:key,
                    goalname:debtname,
                    payoffdebts:payoffdebt,
                    goalenddate:debtdate,
                    permonth:debtcontribution,
                    goalassumptions_1:debtminimumpayments,
                    goalassumptions_2:debtorderofpay,
                    action:'UPDATE',
                    goaltype:'DEBT'
                };

                $.ajax({
                    url:userGoalAddUpdateURL,
                    dataType:"json",
                    data:formValues,
                    type:'POST',
                    success:function (jsonData){
                        timeoutPeriod = defaultTimeoutPeriod;
                    }
                });
                $("#" + key + "debtLoading").hide();
                var i=0;
                var currentDate = new Date();
                var ageBreakdown = debtdate.split('-');
                var nameSummary = "Pay Off Debt";
                if (debtname != "")
                    nameSummary = debtname;
                for(i=0;i<financialData.goals.length;i++)
                {
                    if(financialData.goals[i].id == key)
                    {
                        financialData.goals[i].goalamount = commaSeparateNumber(totalDebt);
                        financialData.goals[i].goalname = debtname;
                        financialData.goals[i].goalnamesummary = nameSummary;
                        financialData.goals[i].payoffdebts = payoffdebt;
                        financialData.goals[i].goalstartdate = currentDate.getFullYear() + "-" + (currentDate.getMonth() + 1) + "-" + currentDate.getDate();
                        financialData.goals[i].goalenddate = ageBreakdown[0] + "-" + ageBreakdown[1] + "-" + ageBreakdown[2];
                        financialData.goals[i].goalstartDay = currentDate.getDate();
                        financialData.goals[i].goalendDay = ageBreakdown[2];
                        financialData.goals[i].goalstartMonth = currentDate.getMonth() + 1;
                        financialData.goals[i].goalendMonth = ageBreakdown[1];
                        financialData.goals[i].goalstartYear = currentDate.getFullYear();
                        financialData.goals[i].goalendYear = ageBreakdown[0];
                        financialData.goals[i].permonth = commaSeparateNumber(debtcontribution);
                        financialData.goals[i].debtTotal = commaSeparateNumber(totalDebt, 0);
                        financialData.goals[i].goalassumptions_1 = debtminimumpayments;
                        financialData.goals[i].goalassumptions_2 = debtorderofpay;

                    }
                }
                $("#" + key + 'DebtNameSummary').html(nameSummary)
                $("#" + key + 'DebtInputName').html(debtname)
                $("#" + key + 'debtAmountSummary').html('#' + commaSeparateNumber(totalDebt));
            },


            resetDebt: function(event){
                event.preventDefault();
                var name = event.target.id;
                var key = name.substring(0, name.indexOf("CancelDebtButton"));
                var i=0;
                for(i=0;i<financialData.goals.length;i++)
                {
                    if(financialData.goals[i].id == key)
                    {
                        $('#' + key + 'DebtMonth').val(financialData.goals[i].goalendMonth);
                        $('#' + key + 'DebtDay').val(financialData.goals[i].goalendDay);
                        $('#' + key + 'DebtYear').val(financialData.goals[i].goalendYear);
                        $('#' + key + 'DebtContribution').val(financialData.goals[i].permonth);
                        $('#' + key + 'DebtMinimumPayments').val(financialData.goals[i].goalassumptions_1);
                        $('#' + key + 'DebtPayoffType').val(financialData.goals[i].goalassumptions_2);

                        if(financialData.goals[i].permonth.replace(/,/g,'') > 0)
                        {
                            $("input:radio[name=" + key + "DebtAchieve]")[0].checked = false;
                            $("input:radio[name=" + key + "DebtAchieve]")[1].checked = true;
                            $('#' + key + 'DebtDateDiv').hide();
                            $('#' + key + 'DebtContributionDiv').show();
                        }
                        else
                        {
                            $("input:radio[name=" + key + "DebtAchieve]")[0].checked = true;
                            $("input:radio[name=" + key + "DebtAchieve]")[1].checked = false;
                            $('#' + key + 'DebtDateDiv').show();
                            $('#' + key + 'DebtContributionDiv').hide();
                        }

                        $('#' + key + 'DebtInputName').val(financialData.goals[i].goalname);
                        $('#' + key + 'DebtInputBalance').val(financialData.goals[i].goalamount);
                        if($("#" + key + "debtCollapseBox").height() > 0 && needsToClose) {
                            updateCollapse = false;
                            $("#" + key + "debtFAQArrow").click();
                            updateCollapse = true;
                        }
                    }
                }
                $("#" + key + "DebtCalculateButton").click();
                $('#' + key + 'debtSaveNeeded').val(false);
            },
            toggleDebt: function(event){
                event.preventDefault();

                var name = event.target.name;
                var key = name.substring(0, name.indexOf("DebtChoice"));
                var debtDebt = $('input[name=' + key + 'DebtChoice]:checked').val();
                var totalAmt=0;
                if(debtDebt == 1)
                {
                    $("#" + key + "DebtDebtList").show();
                    $("#" + key + "DebtDebt").show();
                    $("#" + key + "TotalDebt").hide();
                    $("input[name=" + key + "DebtCheck]").each(function () {
                        if($(this).is(':checked')){
                            var idAmt = $(this).val().toString().split('#');
                            var amt = idAmt[1].replace(/,/g,'');
                            totalAmt=parseFloat(totalAmt)+parseFloat(amt);
                        }
                    });
                    totalAmt = commaSeparateNumber(totalAmt, 0);
                    $("#" + key + "DebtDebt").html("($" + totalAmt + ")");
                }
                else
                {
                    $("#" + key + "DebtDebtList").hide();
                    $("#" + key + "DebtDebt").hide();
                    $("#" + key + "TotalDebt").show();
                    $("input[name=" + key + "DebtCheck]").each(function () {
                        var idAmt = $(this).val().toString().split('#');
                        var amt = idAmt[1].replace(/,/g,'');
                        totalAmt=parseFloat(totalAmt)+parseFloat(amt);
                    });
                    totalAmt = commaSeparateNumber(totalAmt, 0);
                    $("#" + key + "DebtDebt").html("($" + totalAmt + ")");
                }
                $("#" + key + "DebtCalculateButton").click();
            },
            debtDebt: function(event){
                event.preventDefault();

                var name = event.target.name;
                var key = name.substring(0, name.indexOf("DebtCheck"));
                $('#' + key + 'debtSaveNeeded').val(true);
                //recalculate all the values
                var totalAmt=0;
                var index = 0;
                $("input[name=" + key + "DebtCheck]").each(function () {
                    if($(this).is(':checked')){
                        var idAmt = $(this).val().toString().split('#');
                        var amt = idAmt[1].replace(/,/g,'');
                        totalAmt=parseFloat(totalAmt)+parseFloat(amt);
                        index++;
                    }
                });
                if(index == 0)
                {
                    $(event.target).attr("checked", true);
                    $("input[name=" + key + "DebtCheck]").each(function () {
                        if($(this).is(':checked')){
                            var idAmt = $(this).val().toString().split('#');
                            var amt = idAmt[1].replace(/,/g,'');
                            totalAmt=parseFloat(totalAmt)+parseFloat(amt);
                        }
                    });
                }
                totalAmt = commaSeparateNumber(totalAmt, 0);
                $("#" + key + "DebtDebt").html("($" + totalAmt + ")");
                $("#" + key + "DebtCalculateButton").click();
            },
            toggleDebtAccomplishGoal: function(event){
                event.preventDefault();
                var name = event.target.name;
                var key = name.substring(0, name.indexOf("DebtAchieve"));

                if($('#' + key + 'DebtDateGoal').is(":checked"))
                {
                    $('#' + key + 'DebtContributionDiv').hide();
                    $('#' + key + 'DebtDateDiv').show();
                }
                else
                {
                    $('#' + key + 'DebtDateDiv').hide();
                    $('#' + key + 'DebtContributionDiv').show();
                }
                $("#" + key + "DebtCalculateButton").click();
            },
            calculateDebt: function(event) {
                event.preventDefault();

                var name = event.target.id;
                var key = name.substring(0, name.indexOf("Debt"));

                var debtChoice = $('input[name=' + key + 'DebtChoice]:checked').val();
                $('#' + key + 'debtSaveNeeded').val(true);

                var debtArray = [];
                var payoffdebt = "";
                var totalDebt = 0;
                var debtminimumpayments = $('#' + key + 'DebtMinimumPayments').val().replace(/,/g, '');                
                $("input[name=" + key + "DebtCheck]").each(function () {
                    var idAmt = $(this).val().toString().split('#');
                    if (debtChoice == 1){
                        if($(this).is(':checked')){
                            var id = idAmt[0];
                            totalDebt += parseFloat(idAmt[1].replace(/,/g, ''));
                            payoffdebt = payoffdebt+""+id+",";
                            var i=0;
                            for(i=0;i<financialData.debts.length;i++)
                            {
                                if(financialData.debts[i].id == id && financialData.debts[i].status == 0 && financialData.debts[i].monthly_payoff_balances == 0)
                                {
                                    var currentDebt = [];
                                    currentDebt.balance = financialData.debts[i]["amount"].replace(/,/g, '') * 1;
                                    currentDebt.type = financialData.debts[i]["accttype"];
                                    currentDebt.payment = financialData.debts[i]["amtpermonth"].replace(/,/g, '') * 1;
                                    if(currentDebt.payment == "") {
                                        currentDebt.payment = currentDebt.balance * (debtminimumpayments / 100);
                                    }
                                    currentDebt.minimum = (currentDebt.type == 'CC') ? (currentDebt.balance * (debtminimumpayments / 100)) : currentDebt.payment;
                                    currentDebt.rate = financialData.debts[i]["apr"].replace(/,/g, '') / 100;
                                    if(currentDebt.rate == "") {
                                        currentDebt.rate = 0.1;
                                    }
                                    debtArray[debtArray.length] = currentDebt;
                                }
                            }
                        }
                    }
                    else
                    {
                        var id = idAmt[0];
                        totalDebt += parseFloat(idAmt[1].replace(/,/g, ''));
                        var i=0;
                        for(i=0;i<financialData.debts.length;i++)
                        {
                            if(financialData.debts[i].id == id && financialData.debts[i].status == 0 && financialData.debts[i].monthly_payoff_balances == 0)
                            {
                                var currentDebt = [];
                                currentDebt.balance = financialData.debts[i]["amount"].replace(/,/g, '') * 1;
                                currentDebt.type = financialData.debts[i]["accttype"];
                                currentDebt.payment = financialData.debts[i]["amtpermonth"].replace(/,/g, '') * 1;
                                if(currentDebt.payment == "") {
                                    currentDebt.payment = currentDebt.balance * (debtminimumpayments / 100);
                                }
                                currentDebt.minimum = (currentDebt.type == 'CC') ? (currentDebt.balance * (debtminimumpayments / 100)) : currentDebt.payment;
                                currentDebt.rate = financialData.debts[i]["apr"].replace(/,/g, '') / 100;
                                if(currentDebt.rate == "") {
                                    currentDebt.rate = 0.1;
                                }
                                debtArray[debtArray.length] = currentDebt;
                            }
                        }
                    }
                });
                
                // Sort based on selection
                var debtorderofpay = $('#' + key + 'DebtPayoffType option:selected').val();
				if($('#' + key + 'DebtPayoffType').is('input')) {
					debtorderofpay = $('#' + key + 'DebtPayoffType').val();
				}
                switch(debtorderofpay) {
                    case "74":
                        debtArray.sort(function(a, b) {
                            if (a.rate == b.rate) {
                                if (a.balance == b.balance) {
                                    return 0;
                                } else {
                                    return (a.balance < b.balance) ? -1 : 1;
                                }
                            } else {
                                return (a.rate < b.rate) ? -1 : 1;
                            }
                        });
                        break;
                    case "73":
                        debtArray.sort(function(a, b) {
                            if (a.rate == b.rate) {
                                if (a.balance == b.balance) {
                                    return 0;
                                } else {
                                    return (a.balance < b.balance) ? -1 : 1;
                                }
                            } else {
                                return (a.rate > b.rate) ? -1 : 1;
                            }
                        });
                        break;
                    case "72":
                        debtArray.sort(function(a, b) {
                            if (a.balance == b.balance) {
                                if (a.rate == b.rate) {
                                    return 0;
                                } else {
                                    return (a.rate > b.rate) ? -1 : 1;
                                }
                            } else {
                                return (a.balance > b.balance) ? -1 : 1;
                            }
                        });
                        break;
                    case "71":
                    default:
                        debtArray.sort(function(a, b) {
                            if (a.balance == b.balance) {
                                if (a.rate == b.rate) {
                                    return 0;
                                } else {
                                    return (a.rate > b.rate) ? -1 : 1;
                                }
                            } else {
                                return (a.balance < b.balance) ? -1 : 1;
                            }
                        });
                }
                
                var debtMonth = $('#' + key + 'DebtMonth option:selected').val();
                var debtDay = $('#' + key + 'DebtDay option:selected').val();
                var debtYear = $('#' + key + 'DebtYear option:selected').val();
				if($('#' + key + 'DebtMonth').is('input')) {
					debtMonth = $('#' + key + 'DebtMonth').val();
	                debtDay = $('#' + key + 'DebtDay').val();
					debtYear = $('#' + key + 'DebtYear').val();
				}
                var debtdate = debtYear + '-' + debtMonth + '-' + debtDay;

                var debtAchieve = $('input[name=' + key + 'DebtAchieve]:checked').val();
                var debtcontribution = "";
                if(debtAchieve == "0")
                    debtcontribution = $('#' + key + 'DebtContribution').val().replace(/,/g,'');


                if(debtAchieve == 1) {
                    var endDate = new Date(parseInt(debtYear), parseInt(debtMonth) - 1, parseInt(debtDay));
                    var currentDate = new Date();
                    if(endDate < currentDate)
                    {
                        endDate = currentDate;
                    }
                    var diff = flexDateDiff(currentDate, endDate);
                    var months = diff.years * 12 + diff.months;
                    months += ((diff.days > 0) ? 1 : 0);
                    months = (months > 0) ? months : 0;

                    var monthlyPayments = 0;
                    var interest = 0;
                    if(months == 0) {
                        monthlyPayments = totalDebt;
                    }
                    else
                    {
                        var payments = 10000;
                        var oldpayments = 0;
                        var increasePayment = 10000;
                        var resultArray = { payment: 0, interest: 0 };
                        var sign = '+';
                        while(Math.abs(payments - oldpayments) >=1)
                        {
                            oldpayments = payments;
                            resultArray = restructDebt(debtArray, payments, months, increasePayment, sign);
                            payments = resultArray.payments;
                            increasePayment = resultArray.increasePayment;
                            sign = resultArray.sign;
                        }
                        monthlyPayments = payments;
                        interest = resultArray.interest;
                        months = resultArray.months;
                    }

                    $("#" + key + "DebtMonthlyNeeds").html(commaSeparateNumber(monthlyPayments, 0));
                    $("#" + key + "DebtMonthlyNeedsNever").hide();
                    $("#" + key + "DebtMonthlyNeedsDiv").show();
                    $("#" + key + "DebtCompletionInterest").html(commaSeparateNumber(interest, 0));
                    $("#" + key + "DebtCompletionInterestDiv").show();
                    $("#" + key + "DebtCompletionInterestNever").hide();
                    $("#" + key + "DebtCompletion").show();
                    var years = months / 12;
                    months = months % 12;
                    $("#" + key + "DebtCompletionYears").html(Math.floor(years));
                    $("#" + key + "DebtCompletionMonths").html(months);
                }
                else
                {
                    var payments = 0;
                    if(debtcontribution != "") {
                        payments = parseFloat(debtcontribution);
                    }
                    var resultArray = restructDebt(debtArray, payments);

                    if(resultArray.months == 1001) {
                        $("#" + key + "DebtCompletion").hide();
                        $("#" + key + "DebtCompletionNever").show();
                        $("#" + key + "DebtCompletionInterestNever").show();
                        $("#" + key + "DebtCompletionInterestDiv").hide();
                        $("#" + key + "DebtMonthlyNeedsNever").show();
                        $("#" + key + "DebtMonthlyNeedsDiv").hide();
                    }
                    else
                    {
                        var years = resultArray.months / 12;
                        var months = resultArray.months % 12;
                        var interest = resultArray.interest;
                        $("#" + key + "DebtCompletionYears").html(Math.floor(years));
                        $("#" + key + "DebtCompletionMonths").html(months);
                        $("#" + key + "DebtCompletionInterest").html(commaSeparateNumber(interest, 0));
                        $("#" + key + "DebtCompletion").show();
                        $("#" + key + "DebtCompletionNever").hide();
                        $("#" + key + "DebtCompletionInterestNever").hide();
                        $("#" + key + "DebtCompletionInterestDiv").show();
                        $("#" + key + "DebtMonthlyNeeds").html(commaSeparateNumber(resultArray.payments, 0));
                        $("#" + key + "DebtMonthlyNeedsNever").hide();
                        $("#" + key + "DebtMonthlyNeedsDiv").show();
                    }
                }
            },
            getKey: function() {
                return "debt";
            }

        });
        return new debtView;
    });