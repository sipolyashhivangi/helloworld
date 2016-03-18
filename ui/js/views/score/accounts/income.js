// Filename: views/score/accounts/income
define([
    'handlebars',
    'text!../../../../html/score/accounts/income.html',
], function(Handlebars, incomeTemplate) {
    var incomeView = Backbone.View.extend({
        el: $("#body"),
        render: function(obj) {
            var action = 'READ';

            var formValues = {
                action: action
            };

            $.ajax({
                url: userIncomeAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    var source = $(incomeTemplate).html();
                    var template = Handlebars.compile(source);


                    if (typeof financialData.estimation != 'undefined') {
                        jsonData.income.estincome = financialData.estimation.income;
                    } else {
                        jsonData.income.estincome = 0;
                    }

                    for (var i in jsonData.income) {
                        jsonData.income[i] = commaSeparateNumber(jsonData.income[i]);
                    }

                    jsonData.income["totaluserincomesummary"] = commaSeparateNumber(jsonData.income.totaluserincome, 0);

                    //Fix Negative Dollar Amounts - only for showing purpose //
                    if (typeof (jsonData.income.totaluserincome) != 'undefined') {
                        if (parseFloat(jsonData.income.totaluserincome.replace(/,/g, '')) < 0) {
                            jsonData.income["totaluserincomesummaryforShow"] = '-$' + (commaSeparateNumber(jsonData.income.totaluserincome, 0).replace("-", ""));
                        } else {
                            jsonData.income["totaluserincomesummaryforShow"] = '$' + commaSeparateNumber(jsonData.income.totaluserincome, 0);
                        }
                    }
                    
                    if (typeof (userData.advisor) != 'undefined') {
                        userData.user.impersonationMode = true;
                        if (userData.permission == 'RO') {// if advisor has RO permission during impersonation.
                            jsonData.income.permission = true;
                        }
                    }
                    $("#newAccounts").append(template(jsonData.income));
                    init();

                }
            });
        },
        events: {
            "change .income": "fnIncomeChange",
        },
        fnIncomeChange: function(event) {

            var thisId = event.target.id;
            var value = $("#" + thisId).val();
            value = value || 0;
            var grossincome = parseFloat($('#grossIncome').val().replace(/,/g, '')) || 0;
            var spouseincome = parseFloat($('#spouseIncome').val().replace(/,/g, '')) || 0;
            var investincome = parseFloat($('#investmentIncome').val().replace(/,/g, '')) || 0;
            var retireincome = parseFloat($('#retirementIncome').val().replace(/,/g, '')) || 0;
            var pensionincome = parseFloat($('#pensionIncome').val().replace(/,/g, '')) || 0;
            var socialincome = parseFloat($('#ssIncome').val().replace(/,/g, '')) || 0;
            var disaincome = parseFloat($('#benefitIncome').val().replace(/,/g, '')) || 0;
            var veteincome = parseFloat($('#veteranIncome').val().replace(/,/g, '')) || 0;
            var totaluserincome = (grossincome + spouseincome + investincome + retireincome + pensionincome + socialincome + disaincome + veteincome);

            $('#householdIncome').val(commaSeparateNumber(totaluserincome));
            //Fix Negative Dollar Amounts - only for showing purpose //
            if (totaluserincome < 0) {
                var totalIncomeForShow = '-$' + (commaSeparateNumber(totaluserincome, 0).replace("-", ""));
                $('#householdIncomeSpan').html(totalIncomeForShow);
                $('#totalcalculatedincome').text(totalIncomeForShow);

            } else {
                $('#householdIncomeSpan').html("$" + commaSeparateNumber(totaluserincome, 0));
                $('#totalcalculatedincome').text("$" + commaSeparateNumber(totaluserincome, 0));
            }
            var action = 'UPDATE';

            var formValues = {
                grossincome: grossincome,
                spouseincome: spouseincome,
                investincome: investincome,
                retireincome: retireincome,
                pensionincome: pensionincome,
                socialincome: socialincome,
                disaincome: disaincome,
                veteincome: veteincome,
                totaluserincome: totaluserincome,
                action: action
            };
            financialData.accountsdownloading = true;

            $.ajax({
                url: userIncomeAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                }
            });
        },
        getKey: function() {
            return "income";
        }
    });
    return new incomeView;
});