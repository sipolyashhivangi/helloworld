// Filename: views/score/debts/creditcard
define([
    'handlebars',
    'text!../../../../html/score/accounts/creditcard.html',
], function(Handlebars, creditcardTemplate) {
    var creditcardView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(creditcardTemplate).html();
            var template = Handlebars.compile(source);
            if (typeof (userData.advisor) != 'undefined') {
                userData.user.impersonationMode = true;
                if (userData.permission == 'RO') {// if advisor has RO permission during impersonation.
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
            "click .createCreditCardButton": "createCreditCard",
            "click .updateCreditCardButton": "updateCreditCard",
            "click .cancelCreditCardButton": "resetCreditCard",
        },
        createCreditCard: function(event) {
            event.preventDefault();
            var name = $('#CreditCardInputName').val().trim();
            var interest = $('#CreditCardInputInterestYield').val();
            var balanceowed = $('#CreditCardInputBalance').val().replace(/,/g, '');
            var amount = $('#CreditCardInputPayment').val().replace(/,/g, '');
            var interestded = '0';
            var payOffMonthlyBalances = 0;

            // If pay Off monthly balances is checked then
            // set the form value to true.
            if ($('#payoff_monthly_balances').is(':checked')) {
                payOffMonthlyBalances = 1;
            }

            if($("#creditcardCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#creditcardFAQArrow").click();
                updateCollapse = true;
            }
            $("#creditcardLoading").show();

            var formValues = {
                name: name,
                apr: interest,
                amount: balanceowed,
                amtpermonth: amount,
                intdeductible: interestded,
                action: 'ADD',
                accttype: 'CC',
                monthly_payoff_balances: payOffMonthlyBalances
            };

            $.ajax({
                url: userDebtsAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/creditcard'],
                            function(addAccountV) {
                                $("#creditcardLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Credit Card";
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
                                $("#creditcardAddAccount").removeClass("active");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                                userPreferences.debtAdded = '1';
                                userPreferences.debtData = '1';
                            }
                    );
                }
            });
        },
        updateCreditCard: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateCreditCard"));
            var name = $('#' + key + 'CreditCardInputName').val().trim();
            var interest = $('#' + key + 'CreditCardInputInterestYield').val();
            var balanceowed = $('#' + key + 'CreditCardInputBalance').val().replace(/,/g, '');
            var amount = $('#' + key + 'CreditCardInputPayment').val().replace(/,/g, '');
            var payOffMonthlyBalances = 0;

            // If pay Off monthly balances is checked then
            // set the form value to true.
            if ($('#' + key + 'payoff_monthly_balances').is(':checked')) {
                payOffMonthlyBalances = 1;
            }

            if($("#" + key + "creditcardCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "creditcardFAQArrow").click();
                updateCollapse = true;
            }
            $("#" + key + "creditcardLoading").show();

            var formValues = {
                id: key,
                name: name,
                apr: interest,
                amount: balanceowed,
                amtpermonth: amount,
                action: 'UPDATE',
                accttype: 'CC',
                monthly_payoff_balances: payOffMonthlyBalances
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

            $("#" + key + "creditcardLoading").hide();
            var nameSummary = "Credit Card";
            if (name != "")
                nameSummary = name;
            var i = 0;
            for (i = 0; i < financialData.debts.length; i++)
            {
                if (financialData.debts[i].id == key)
                {
                    financialData.debts[i].amount = commaSeparateNumber(balanceowed);
                    financialData.debts[i].amountSummary = commaSeparateNumber(balanceowed, 0);
                    financialData.debts[i].name = name;
                    financialData.debts[i].nameSummary = nameSummary;
                    financialData.debts[i].apr = interest;
                    financialData.debts[i].amtpermonth = commaSeparateNumber(amount);
                    financialData.debts[i].monthly_payoff_balances = payOffMonthlyBalances;
                }
            }
            $("#" + key + 'CreditCardNameSummary').html(nameSummary);
            //Fix Negative Dollar Amounts - only for showing purpose //
            if (balanceowed < 0) {
                var creditcardAmountForShow = '-$' + (commaSeparateNumber(balanceowed, 0).replace("-", ""));
                $("#" + key + 'creditcardAmountSummary').html(creditcardAmountForShow);
            } else {
                $("#" + key + 'creditcardAmountSummary').html('$' + commaSeparateNumber(balanceowed, 0));
            }
            $("#" + key + 'CreditCardInputBalance').val(commaSeparateNumber(balanceowed));
            $("#" + key + 'CreditCardInputPayment').val(commaSeparateNumber(amount));
            $('#' + key + 'CreditCardInputName').val(name);
            // If pay Off monthly balances is checked then
            // set the form value to true.
            if (payOffMonthlyBalances == 1) {
                $("#" + key + "payoff_monthly_balances").prop('checked', true);
            }

            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
            RetoggleOnOff("debts");
        },
        resetCreditCard: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelCreditCard"));
            var payOffMonthlyBalances = 0;

            var i = 0;
            for (i = 0; i < financialData.debts.length; i++)
            {
                if (financialData.debts[i].id == key)
                {
                    $('#' + key + 'CreditCardInputName').val(financialData.debts[i].name);
                    $('#' + key + 'CreditCardInputBalance').val(financialData.debts[i].amount);
                    $('#' + key + 'CreditCardInputInterestYield').val(financialData.debts[i].apr);
                    $('#' + key + 'CreditCardInputPayment').val(financialData.debts[i].amtpermonth);
                    payOffMonthlyBalances = financialData.debts[i].monthly_payoff_balances;
                    // If pay Off monthly balances is checked then
                    // set the form value to true.
                    if (payOffMonthlyBalances == 1) {
                        $("#" + key + 'payoff_monthly_balances').prop('checked', true);
                    }
                    if($("#" + key + "creditcardCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "creditcardFAQArrow").click();
                        updateCollapse = true;
                    }
                }
            }
            RetoggleOnOff("debts");
        },
        getKey: function() {
            return "creditcard";
        }


    });
    return new creditcardView;
});
