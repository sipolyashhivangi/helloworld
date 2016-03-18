// Filename: views/score/accounts/expenses
define([
    'handlebars',
    'text!../../../../html/score/accounts/expenses.html',
], function(Handlebars, expensesTemplate) {
    var expensesView = Backbone.View.extend({
        el: $("#body"),
        render: function(obj) {
            var action = 'READ';

            var formValues = {
                action: action,
            };

            $.ajax({
                url: userExpenseAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    var source = $(expensesTemplate).html();
                    var template = Handlebars.compile(source);
                    if (typeof financialData.estimation != 'undefined') {
                        jsonData.expense.estexpense = financialData.estimation.expense;
                    } else {
                        jsonData.expense.estexpense = 0;
                    }
                    for (var i in jsonData.expense)
                    {
                        jsonData.expense[i] = commaSeparateNumber(jsonData.expense[i]);
                    }
                    jsonData.expense["actualexpensesummary"] = commaSeparateNumber(jsonData.expense.actualexpense, 0);

                    //Fix Negative Dollar Amounts - only for showing purpose //
                    if (typeof (jsonData.expense.actualexpense) != 'undefined') {
                        if (parseFloat(jsonData.expense.actualexpense.replace(/,/g, '')) < 0 ) {
                            jsonData.expense["actualexpensesummaryforShow"] = '-$' + (commaSeparateNumber(jsonData.expense.actualexpense, 0).replace("-", ""));
                        } else {
                            jsonData.expense["actualexpensesummaryforShow"] = '$' + commaSeparateNumber(jsonData.expense.actualexpense, 0);
                        }
                    }
                    if (typeof (userData.advisor) != 'undefined') {
                        userData.user.impersonationMode = true;
                        if (userData.permission == 'RO') {// if advisor has RO permission during impersonation.
                            jsonData.expense.permission = true;
                        }
                    }
                    $("#newAccounts").append(template(jsonData.expense));
                    init();
                }
            });
        },
        events: {
            "change .expenses": "fnExpenseChange",
        },
        fnExpenseChange: function(event) {

            var thisId = event.target.id;
            var value = $("#" + thisId).val();

            value = value || 0;

            var rent = parseFloat($('#rentExpenses').val().replace(/,/g, '')) || 0;
            var groceries = parseFloat($('#groceryExpenses').val().replace(/,/g, '')) || 0;
            var utilities = parseFloat($('#utilityExpenses').val().replace(/,/g, '')) || 0;
            var gas = parseFloat($('#gasExpenses').val().replace(/,/g, '')) || 0;
            var entertainment = parseFloat($('#entertainmentExpenses').val().replace(/,/g, '')) || 0;
            var household = parseFloat($('#householdExpenses').val().replace(/,/g, '')) || 0;
            var health = parseFloat($('#healthExpenses').val().replace(/,/g, '')) || 0;
            var cc = parseFloat($('#loanExpenses').val().replace(/,/g, '')) || 0;
            var taxes = parseFloat($('#taxExpenses').val().replace(/,/g, '')) || 0;
            var travel = parseFloat($('#travelExpenses').val().replace(/,/g, '')) || 0;
            var other = parseFloat($('#otherExpenses').val().replace(/,/g, '')) || 0;
            var actualexpense = (rent + groceries + utilities + gas + entertainment + household + health + cc + taxes + travel + other);

            $('#householdExpensestotal').val(commaSeparateNumber(actualexpense));
            if (actualexpense < 0) {
                var totalExpensesForShow = '-$' + (commaSeparateNumber(actualexpense, 0).replace("-", ""));
                $('#householdExpensestotalSpan').html(totalExpensesForShow);
                $('#totalexpensescalculated').text(totalExpensesForShow);

            } else {
                $('#householdExpensestotalSpan').html("$" + commaSeparateNumber(actualexpense, 0));
                $('#totalexpensescalculated').text("$" + commaSeparateNumber(actualexpense, 0));
            }

            var formValues = {
                rent: rent,
                groceries: groceries,
                utilities: utilities,
                gas: gas,
                entertainment: entertainment,
                household: household,
                health: health,
                cc: cc,
                taxes: taxes,
                travel: travel,
                other: other,
                action: 'ADD',
                actualexpense: actualexpense
            };
            $.ajax({
                url: userExpenseAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                }
            });
        },
        getKey: function() {
            return "expenses";
        }
    });
    return new expensesView;
});