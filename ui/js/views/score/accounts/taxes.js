// Filename: views/score/accounts/taxes
define([
    'handlebars',
    'text!../../../../html/score/accounts/taxes.html',
], function(Handlebars, taxesTemplate) {
    var taxesView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var action = 'READ';

            var formValues = {
                action: action
            };

            $.ajax({
                url: userMiscAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
				
					timeoutPeriod = defaultTimeoutPeriod;
                    var source = $(taxesTemplate).html();
                    var template = Handlebars.compile(source);
                    if(typeof(userData.advisor) != 'undefined') {
                        userData.user.impersonationMode = true;
                        if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                            jsonData.permission = true;
                        }
                    }
                    $(element).html(template(jsonData));
                    $("#existingAccounts").hide();                                
                    $("#totalAccounts").hide();                                
                 	$("#ProfileTracker").val('tax');
                }
            });
        },
        events: {
            "change .taxes": "fnTaxesChange",
            "click #footest": "footest"
        },
        // Adding the functions :
        // ----------------------

        fnTaxesChange: function(event) {
            event.preventDefault();
            var taxpay = ($('input:radio[name=TaxesMoney]:checked').val()) ? ($('input:radio[name=TaxesMoney]:checked').val()) : "";
            var taxvalue = ($('#TaxAmount').val()) ? ($('#TaxAmount').val()) : "";
            var taxbracket = ($('#TaxBracket').val()) ? ($('#TaxBracket').val()) : "";
            var taxcontri = ($('input:radio[id=TaxesDeductible]:checked').val()) ? ($('input:radio[id=TaxesDeductible]:checked').val()) : "";
            var taxStdOrItemDed = ($('input:radio[id=TaxesDeduction]:checked').val()) ? ($('input:radio[id=TaxesDeduction]:checked').val()) : "";
            var action = 'ADD';


            var formValues = {
                taxpay: taxpay,
                taxvalue: taxvalue,
                taxbracket: taxbracket,
                taxcontri: taxcontri,
                taxStdOrItemDed: taxStdOrItemDed,
                action: action,
                ctype: 'TAX'
            };
            $.ajax({
                url: userMiscAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {               
					timeoutPeriod = defaultTimeoutPeriod;
                }

            });

        },
        footest: function(event) {

            var formValues = {
                //taxpay:taxpay,
                action: 'DELETE'
            };

            $.ajax({
                url: userMiscAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                }
            });
        },
        getKey: function() {
            return "taxes";
        },
    });
    return new taxesView;
});
