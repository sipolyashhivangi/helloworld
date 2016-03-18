// Filename: views/score/accounts/bank
define([
    'jquery',
    'handlebars',
    'backbone',
    'text!../../../../html/score/accounts/bank.html',
], function($, Handlebars, Backbone, bankTemplate) {
    var bankView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(bankTemplate).html();
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
            "click .createBankButton": "createBank",
            "click .updateBankButton": "updateBank",
            "click .cancelBankButton": "resetBank"

        },
        // Creating the Bank Account:
        //------------------------------

        createBank: function(event) {
            event.preventDefault();
            var bankname = $('#BankInputName').val().trim();
            var bankbal = $('#BankInputBalance').val().replace(/,/g, '');
            var bankint = $('#BankInputInterestYield').val();
            var contribution = $('#BankInputContribution').val().replace(/,/g, '');
            var withdrawal = $('#BankInputWithdrawal').val().replace(/,/g, '');
            var currentView = this;

            var formValues = {
                name: bankname,
                amount: bankbal,
                growthrate: bankint,
                contribution: contribution,
                withdrawal: withdrawal,
                action: 'ADD',
                accttype: 'BANK'
            };

            if($("#bankCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#bankFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#bankLoading").show();

            $.ajax({
                url: userAssetAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/bank'],
                            function(addAccountV) {
                                $("#bankLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Bank Account";
                                if (jsonData.asset.name != "")
                                    nameSummary = jsonData.asset.name;

                                var obj = {accttype: jsonData.asset.accttype,
                                    amount: commaSeparateNumber(jsonData.asset.amount),
                                    amountSummary: commaSeparateNumber(jsonData.asset.amount, 0),
                                    id: jsonData.asset.id,
                                    index: accountIndex,
                                    priority: jsonData.asset.priority,
                                    name: jsonData.asset.name,
                                    nameSummary: nameSummary,
                                    refId: jsonData.asset.refid,
                                    status: jsonData.asset.status,
                                    ticker: jsonData.asset.ticker,
                                    growthrate: jsonData.asset.growthrate,
                                    contribution: commaSeparateNumber(jsonData.asset.contribution),
                                    withdrawal: commaSeparateNumber(jsonData.asset.withdrawal)
                                };
                                addAccountV.render('#existingAccounts', obj);
                                init();
                                accountIndex++;
                                financialData.cash[financialData.cash.length] = obj;
                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();
                                RetoggleOnOff("assets");
                                $("#bankAddAccount").removeClass("active");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                            }
                    );
                }

            });

        },
        // Updating the Bank Account:
        //------------------------------

        updateBank: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateBankButton"));
            var bankname = $('#' + key + 'BankInputName').val().trim();
            var bankbal = $('#' + key + 'BankInputBalance').val().replace(/,/g, '');
            var bankint = $('#' + key + 'BankInputInterestYield').val();
            var contribution = $('#' + key + 'BankInputContribution').val().replace(/,/g, '');
            var withdrawal = $('#' + key + 'BankInputWithdrawal').val().replace(/,/g, '');

            if($("#" + key + "bankCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "bankFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#" + key + "bankLoading").show();

            var formValues = {
                id: key,
                name: bankname,
                amount: bankbal,
                growthrate: bankint,
                contribution: contribution,
                withdrawal: withdrawal,
                action: 'UPDATE',
                accttype: 'BANK'
            };
            $.ajax({
                url: userAssetAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                }
            });
        
            $("#" + key + "bankLoading").hide();
            var nameSummary = "Bank Account";
            if (bankname != "")
                nameSummary = bankname;
            var i = 0;
            for (i = 0; i < financialData.cash.length; i++)
            {
                if (financialData.cash[i].id == key)
                {
                    financialData.cash[i].amount = commaSeparateNumber(bankbal);
                    financialData.cash[i].amountSummary = commaSeparateNumber(bankbal, 0);
                    financialData.cash[i].name = bankname;
                    financialData.cash[i].nameSummary = nameSummary;
                    financialData.cash[i].growthrate = bankint;
					financialData.cash[i].contribution = commaSeparateNumber(contribution);
					financialData.cash[i].withdrawal = commaSeparateNumber(withdrawal);
                }
            }
            $('#' + key + 'BankInputName').val(bankname);
            $("#" + key + 'BankNameSummary').html(nameSummary);
            if(bankbal < 0 ){
                var bankAmountForShow = '-$' + (commaSeparateNumber(bankbal, 0).replace("-", ""));
                $("#" + key + 'bankAmountSummary').html(bankAmountForShow);
            }else{
                $("#" + key + 'bankAmountSummary').html('$' + commaSeparateNumber(bankbal, 0));
            }
            $("#" + key + 'BankInputBalance').val(commaSeparateNumber(bankbal));
            $("#" + key + 'BankInputContribution').val(commaSeparateNumber(contribution));
            $("#" + key + 'BankInputWithdrawal').val(commaSeparateNumber(withdrawal));
            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
            RetoggleOnOff("assets");
        },
        resetBank: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelBankButton"));
            var i = 0;
            for (i = 0; i < financialData.cash.length; i++)
            {
                if (financialData.cash[i].id == key)
                {
                    $('#' + key + 'BankInputName').val(financialData.cash[i].name);
                    $('#' + key + 'BankInputBalance').val(financialData.cash[i].amount);
                    $('#' + key + 'BankInputInterestYield').val(financialData.cash[i].growthrate);
                    $('#' + key + 'BankInputContribution').val(financialData.cash[i].contribution);
                    $('#' + key + 'BankInputWithdrawal').val(financialData.cash[i].withdrawal);
                    if($("#" + key + "bankCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "bankFAQArrow").click();
                        updateCollapse = true;                        
                    }
                }
            }
            RetoggleOnOff("assets");
        },
        getKey: function() {
            return "bank";
        }

    });
    return new bankView;
});