// Filename: views/score/accounts/loan
define([
    'handlebars',
    'text!../../../../html/score/accounts/loan.html',
], function(Handlebars, loanTemplate) {
    var loanView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(loanTemplate).html();
            var template = Handlebars.compile(source);
             if(typeof(userData.advisor) != 'undefined') {
                userData.user.impersonationMode = true;
                if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                    obj.permission = true;
                }
            }
            //Fix Negative Dollar Amounts - only for showing purpose //
            if (typeof (obj.amountSummary) != 'undefined') {
                if (parseFloat(obj.amountSummary.replace(/,/g, '')) < 0) {
                    obj.amountSummaryForShow = '-$' + (commaSeparateNumber(obj.amountSummary, 0).replace("-", ""));
                } else {
                    obj.amountSummaryForShow = '$' + commaSeparateNumber(obj.amountSummary, 0);
                }
            }
            $(element).append(template(obj));
        },
        events: {
            "click .createLoanButton": "createLoan",
            "click .updateLoanButton": "updateLoan",
            "click .cancelLoanButton": "resetLoan",
        },
        createLoan: function(event) {
            event.preventDefault();
            //get all the values
            var LoanInputName = $('#LoanInputName').val().trim();
            var LoanInputBalance = $('#LoanInputBalance').val().replace(/,/g, '');
            var LoanInputAPR = $('#LoanInputAPR').val();
            var LoanInputYears = $('#LoanInputYears').val();
            var LoanType = $('#LoanType').val();
            var LoanPerMonth = $('#LoanInputPayment').val().replace(/,/g, '');
            var LoanDeduct = $('input:radio[name=LoanDeductible]:checked').val();
            if (typeof(LoanDeduct) == 'undefined') {
                LoanDeduct = '';
            }

            var payOffMonthlyBalances = 0;

            // If pay Off monthly balances is checked then
            // set the form value to true.
            if ($('#payoff_monthly_balances').is(':checked')) {
                payOffMonthlyBalances = 1;
            }

            if($("#loanCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#loanFAQArrow").click();
                updateCollapse = true;
            }
            $("#loanLoading").show();

            var formValues = {
                name: LoanInputName,
                amount: LoanInputBalance,
                yearsremaining: LoanInputYears,
                apr: LoanInputAPR,
                mortgagetype: LoanType,
                intdeductible: LoanDeduct,
                amtpermonth: LoanPerMonth,
                monthly_payoff_balances: payOffMonthlyBalances,
                action: 'ADD',
                accttype: 'LOAN'
            };

            $.ajax({
                url: userDebtsAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/loan'],
                            function(addAccountV) {
                                $("#loanLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Loan";
                                if (jsonData.debt.name != "")
                                    nameSummary = jsonData.debt.name;

                                var obj = {accttype: jsonData.debt.accttype,
                                    amount: commaSeparateNumber(jsonData.debt.amount),
                                    amountSummary: commaSeparateNumber(jsonData.debt.amount, 0),
                                    id: jsonData.debt.id,
                                    index: accountIndex,
                                    priority: jsonData.debt.priority,
                                    name: jsonData.debt.name,
                                    nameSummary: nameSummary,
                                    refId: jsonData.debt.refid,
                                    status: jsonData.debt.status,
                                    ticker: jsonData.debt.ticker,
                                    apr: jsonData.debt.apr,
                                    intdeductible: jsonData.debt.intdeductible,
                                    yearsremaining: jsonData.debt.yearsremaining,
                                    mortgagetype: jsonData.debt.mortgagetype,
                                    livehere: jsonData.debt.livehere,
                                    amtpermonth: commaSeparateNumber(jsonData.debt.amtpermonth),
                                    monthly_payoff_balances: jsonData.debt.monthly_payoff_balances
                                };
                                addAccountV.render('#existingAccounts', obj);
                                init();
                                accountIndex++;

                                financialData.debts[financialData.debts.length] = obj;
                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();
                                RetoggleOnOff("debts");
                                $("#loanAddAccount" + LoanType).removeClass("active");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                                userPreferences.debtAdded = '1';
                                userPreferences.debtData = '1';
                            }
                    );
                }
            });

        },
        updateLoan: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateLoanButton"));
            //get all the values
            var LoanInputName = $('#' + key + 'LoanInputName').val().trim();
            var LoanInputBalance = $('#' + key + 'LoanInputBalance').val().replace(/,/g, '');
            var LoanInputAPR = $('#' + key + 'LoanInputAPR').val();
            var LoanInputYears = $('#' + key + 'LoanInputYears').val();
            var LoanType = $('#' + key + 'LoanType').val();
            var LoanPerMonth = $('#' + key + 'LoanInputPayment').val().replace(/,/g, '');
            var LoanDeduct = $('input:radio[name=' + key + 'LoanDeductible]:checked').val();
            if (typeof(LoanDeduct) == 'undefined') {
                LoanDeduct = '';
            }
            var payOffMonthlyBalances = 0;

            // If pay Off monthly balances is checked then
            // set the form value to true.
            if ($('#' + key + 'payoff_monthly_balances').is(':checked')) {
                payOffMonthlyBalances = 1;
            }

            if($("#" + key + "loanCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "loanFAQArrow").click();
                updateCollapse = true;
            }
            $("#" + key + "loanLoading").show();

            var formValues = {
                id: key,
                name: LoanInputName,
                amount: LoanInputBalance,
                yearsremaining: LoanInputYears,
                apr: LoanInputAPR,
                mortgagetype: LoanType,
                amtpermonth: LoanPerMonth,
                intdeductible: LoanDeduct,
                monthly_payoff_balances: payOffMonthlyBalances,
                action: 'UPDATE',
                accttype: 'LOAN'
            };

            $.ajax({
                url: userDebtsAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                }
            });

            $("#" + key + "loanLoading").hide();
            var nameSummary = "Loan";
            if (LoanInputName != "")
                nameSummary = LoanInputName;
            var i = 0;
            for (i = 0; i < financialData.debts.length; i++) {
                if (financialData.debts[i].id == key)
                {
                    financialData.debts[i].amount = commaSeparateNumber(LoanInputBalance);
                    financialData.debts[i].amountSummary = commaSeparateNumber(LoanInputBalance, 0);
                    financialData.debts[i].name = LoanInputName;
                    financialData.debts[i].nameSummary = nameSummary;
                    financialData.debts[i].apr = LoanInputAPR;
                    financialData.debts[i].intdeductible = LoanDeduct;
                    financialData.debts[i].yearsremaining = LoanInputYears;
                    financialData.debts[i].mortgagetype = LoanType;
                    financialData.debts[i].amtpermonth = commaSeparateNumber(LoanPerMonth);
                    financialData.debts[i].monthly_payoff_balances = payOffMonthlyBalances;
                }
            }
            $("#" + key + 'LoanNameSummary').html(nameSummary);
            if (LoanInputBalance < 0) {
                var loanAmountForShow = '-$' + (commaSeparateNumber(LoanInputBalance, 0).replace("-", ""));
                $("#" + key + 'loanAmountSummary').html(loanAmountForShow);
            } else {
                $("#" + key + 'loanAmountSummary').html('$' + commaSeparateNumber(LoanInputBalance, 0));
            }
            $("#" + key + 'LoanInputOtherBalance').val(commaSeparateNumber(LoanInputBalance));
            $("#" + key + 'LoanInputPayment').val(commaSeparateNumber(LoanPerMonth));
            $('#' + key + 'LoanInputName').val(LoanInputName);
            if (payOffMonthlyBalances == 1) {
                $("#" + key + "payoff_monthly_balances").prop('checked', true);
            }
            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
            RetoggleOnOff("debts");

        },
        resetLoan: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelLoanButton"));
            var i = 0;
            var payOffMonthlyBalances = 0;
            for (i = 0; i < financialData.debts.length; i++)
            {
                if (financialData.debts[i].id == key)
                {
                    $('#' + key + 'LoanInputName').val(financialData.debts[i].name);
                    $('#' + key + 'LoanInputBalance').val(financialData.debts[i].amount);
                    $('#' + key + 'LoanInputAPR').val(financialData.debts[i].apr);
                    $('#' + key + 'LoanInputYears').val(financialData.debts[i].yearsremaining);
                    $('#' + key + 'LoanType').val(financialData.debts[i].mortgagetype);
                    $('#' + key + 'LoanInputPayment').val(financialData.debts[i].amtpermonth);
                    $("input:radio[name=" + key + "LoanDeductible]")[0].checked = (financialData.debts[i].intdeductible == 1);
                    $("input:radio[name=" + key + "LoanDeductible]")[1].checked = (financialData.debts[i].intdeductible === "0");
                    payOffMonthlyBalances = financialData.debts[i].monthly_payoff_balances;
                    // If pay Off monthly balances is checked then
                    // set the form value to true.
                    if (payOffMonthlyBalances == 1) {
                        $("#" + key + 'payoff_monthly_balances').prop('checked', true);
                    }
                    if($("#" + key + "loanCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "loanFAQArrow").click();
                        updateCollapse = true;
                    }
                }
            }
            RetoggleOnOff("debts");
        },
        getKey: function() {
            return "loan";
        }


    });
    return new loanView;
});
