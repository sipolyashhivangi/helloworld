// Filename: views/score/accounts/mortgage
define([
    'handlebars',
    'text!../../../../html/score/accounts/mortgage.html',
], function(Handlebars, mortgageTemplate) {
    var mortgageView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(mortgageTemplate).html();
            var template = Handlebars.compile(source);
            if(typeof(userData.advisor) != 'undefined') {
                userData.user.impersonationMode = true;
                if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                    obj.permission = true;
                }
            }
            $(element).append(template(obj));
        },
        events: {
            "click .createMortgageButton": "createMortgage",
            "click .updateMortgageButton": "updateMortgage",
            "click .cancelMortgageButton": "resetMortgage",
        },
        createMortgage: function(event) {
            event.preventDefault();
            var name = $('#MortgageInputName').val().trim();
            var balanceowed = $('#MortgageInputBalance').val().replace(/,/g, '');
            var yearremainingmort = $('#MortgageInputYears').val();
            var interest = $('#MortgageInputAPR').val();
            var morttype = $('#MortgageAccType').val();
            var mortamtmonth = $('#MortgageInputPayment').val();
            var mortlivehere = $('input:radio[name=MortgageHome]:checked').val();
            if (typeof(mortlivehere) == 'undefined') {
                mortlivehere = '';
            }
            var intdeductible = $('input:radio[name=MortgageDeductible]:checked').val();
            if (typeof(intdeductible) == 'undefined') {
                intdeductible = '';
            }

            if($("#mortgageCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#mortgageFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#mortgageLoading").show();

            var formValues = {
                name: name,
                amount: balanceowed,
                yearsremaining: yearremainingmort,
                apr: interest,
                mortgagetype: morttype,
                amtpermonth: mortamtmonth,
                livehere: mortlivehere,
                intdeductible: intdeductible,
                action: 'ADD',
                accttype: 'MORT'
            };

            $.ajax({
                url: userDebtsAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/mortgage'],
                            function(addAccountV) {
                                $("#mortgageLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Mortgage";
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
                                    monthly_payoff_balances: "0"
                                };
                                addAccountV.render('#existingAccounts', obj);
                                init();
                                accountIndex++;

                                financialData.debts[financialData.debts.length] = obj;
                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();
                                RetoggleOnOff("debts");
                                $("#mortgageAddAccount").removeClass("active");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                                userPreferences.debtAdded = '1';
                                userPreferences.debtData = '1';
                            }
                    );
                }
            });



        },
        updateMortgage: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateMortgage"));
            var name = $('#' + key + 'MortgageInputName').val().trim();
            var balanceowed = $('#' + key + 'MortgageInputBalance').val().replace(/,/g, '');
            var yearremainingmort = $('#' + key + 'MortgageInputYears').val();
            var interest = $('#' + key + 'MortgageInputAPR').val();
            var morttype = $('#' + key + 'MortgageAccType').val();
            var mortamtmonth = $('#' + key + 'MortgageInputPayment').val();
            var mortlivehere = $('input:radio[name=' + key + 'MortgageHome]:checked').val();
            if (typeof(mortlivehere) == 'undefined') {
                mortlivehere = '';
            }
            var intdeductible = $('input:radio[name=' + key + 'MortgageDeductible]:checked').val();
            if (typeof(intdeductible) == 'undefined') {
                intdeductible = '';
            }

            if($("#" + key + "mortgageCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "mortgageFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#" + key + "mortgageLoading").show();

            var formValues = {
                id: key,
                name: name,
                amount: balanceowed,
                yearsremaining: yearremainingmort,
                apr: interest,
                mortgagetype: morttype,
                amtpermonth: mortamtmonth,
                livehere: mortlivehere,
                intdeductible: intdeductible,
                action: 'UPDATE',
                accttype: 'MORT'
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
            $("#" + key + "mortgageLoading").hide();
            var nameSummary = "Mortgage";
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
                    financialData.debts[i].intdeductible = intdeductible;
                    financialData.debts[i].yearsremaining = yearremainingmort;
                    financialData.debts[i].mortgagetype = morttype;
                    financialData.debts[i].livehere = mortlivehere;
                    financialData.debts[i].amtpermonth = commaSeparateNumber(mortamtmonth);
                }
            }
            $("#" + key + 'MortgageNameSummary').html(nameSummary);
            $("#" + key + 'mortgageAmountSummary').html('$' + commaSeparateNumber(balanceowed, 0));
            $("#" + key + 'MortgageInputBalance').val(commaSeparateNumber(balanceowed));
            $("#" + key + 'MortgageInputPayment').val(commaSeparateNumber(mortamtmonth));
            $('#' + key + 'MortgageInputName').val(name);
            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
            RetoggleOnOff("debts");

        },
        resetMortgage: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelMortgageButton"));
            var i = 0;
            for (i = 0; i < financialData.debts.length; i++)
            {
                if (financialData.debts[i].id == key)
                {
                    $('#' + key + 'MortgageInputName').val(financialData.debts[i].name);
                    $('#' + key + 'MortgageInputBalance').val(financialData.debts[i].amount);
                    $('#' + key + 'MortgageInputAPR').val(financialData.debts[i].apr);
                    $('#' + key + 'MortgageInputYears').val(financialData.debts[i].yearsremaining);
                    $('#' + key + 'MortgageType').val(financialData.debts[i].mortgagetype);
                    $('#' + key + 'MortgageInputPayment').val(financialData.debts[i].amtpermonth);
                    $("input:radio[name=" + key + "MortgageHome]")[0].checked = (financialData.debts[i].livehere == 1);
                    $("input:radio[name=" + key + "MortgageHome]")[1].checked = (financialData.debts[i].livehere === "0");
                    $("input:radio[name=" + key + "MortgageDeductible]")[0].checked = (financialData.debts[i].intdeductible == 1);
                    $("input:radio[name=" + key + "MortgageDeductible]")[1].checked = (financialData.debts[i].intdeductible === "0");
                    if($("#" + key + "mortgageCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "mortgageFAQArrow").click();
                        updateCollapse = true;                        
                    }
                }
            }
            RetoggleOnOff("debts");
        },
        getKey: function() {
            return "mortgage";
        }


    });
    return new mortgageView;
});