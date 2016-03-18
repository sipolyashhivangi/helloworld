// Filename: views/score/accounts/homeinsurance
define([
    'handlebars',
    'text!../../../../html/score/accounts/homeinsurance.html',
], function(Handlebars, homeinsuranceTemplate) {
    var homeinsuranceView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(homeinsuranceTemplate).html();
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
            "click .createHomeInsuranceButton": "createHomeInsurance",
            "click .updateHomeInsuranceButton": "updateHomeInsurance",
            "click .cancelHomeInsuranceButton": "resetHomeInsurance",
        },
        // ADDING THE HOME / RENTER'S INSURANCE:
        // ----------------------------------


        createHomeInsurance: function(event) {
            event.preventDefault();
            var propertyname = $('#HomeInsuranceInputName').val().trim();
            var annualpremium = $('#HomeInsuranceInputPremiumPayment').val().replace(/,/g, '');
            var deductibleamt = $('#HomeInsuranceInputDeductible').val().replace(/,/g, '');
            var cashvalue = 0;
            var lastreviewyear = $('#HomeInsuranceReviewYear').val();

            var formValues = {
                name: propertyname,
                annualpremium: annualpremium,
                deductible: deductibleamt,
                amount: cashvalue,
                reviewyear: lastreviewyear,
                action: 'ADD',
                accttype: 'HOME'
            };

            if($("#homeinsuranceCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#homeinsuranceFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#homeinsuranceLoading").show();

            $.ajax({
                url: userAddUpdateInsuranceURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/homeinsurance'],
                            function(addAccountV) {
                                $("#homeinsuranceLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Home Owner's / Renter Insurance";
                                if (jsonData.insurance.name != "")
                                    nameSummary = jsonData.insurance.name;

                                var obj = {
                                    accttype: jsonData.insurance.accttype,
                                    amount: commaSeparateNumber(jsonData.insurance.amount),
                                    id: jsonData.insurance.id,
                                    index: accountIndex,
                                    priority: jsonData.insurance.priority,
                                    name: jsonData.insurance.name,
                                    nameSummary: nameSummary,
                                    refId: jsonData.insurance.refid,
                                    status: jsonData.insurance.status,
                                    reviewyear: jsonData.insurance.reviewyear,
                                    annualpremium: commaSeparateNumber(jsonData.insurance.annualpremium),
                                    deductible: commaSeparateNumber(jsonData.insurance.deductible)
                                };

                                addAccountV.render('#existingAccounts', obj);
                                init();
                                accountIndex++;

                                financialData.insurance[financialData.insurance.length] = obj;
                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();
                                RetoggleOnOff("insurance");
                                $("#homeinsuranceAddAccount").removeClass("active");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                                userPreferences.insuranceAdded = '1';
                                userPreferences.insuranceData = '1';
                            }
                    );
                }
            });
        },
        // UPDATING THE HOME / RENTER'S INSURANCE:
        // ----------------------------------


        updateHomeInsurance: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateHomeInsurance"));
            var propertyname = $('#' + key + 'HomeInsuranceInputName').val().trim();
            var annualpremium = $('#' + key + 'HomeInsuranceInputPremiumPayment').val().replace(/,/g, '');
            var deductibleamt = $('#' + key + 'HomeInsuranceInputDeductible').val().replace(/,/g, '');
            var cashvalue = 0;
            var lastreviewyear = $('#' + key + 'HomeInsuranceReviewYear').val();

            if($("#" + key + "homeinsuranceCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "homeinsuranceFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#" + key + "homeinsuranceLoading").show();

            var formValues = {
                id: key,
                name: propertyname,
                annualpremium: annualpremium,
                deductible: deductibleamt,
                amount: cashvalue,
                reviewyear: lastreviewyear,
                action: 'UPDATE',
                accttype: 'HOME'
            };
            $.ajax({
                url: userAddUpdateInsuranceURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                }
            });
                    
            $("#" + key + "homeinsuranceLoading").hide();
            var nameSummary = "Home Owner's / Renter Insurance";
            if (propertyname != "")
                nameSummary = propertyname;
            var i = 0;
            for (i = 0; i < financialData.insurance.length; i++)
        	{
                if (financialData.insurance[i].id == key)
                {
                    financialData.insurance[i].name = propertyname;
                    financialData.insurance[i].nameSummary = nameSummary;
                    financialData.insurance[i].amount = commaSeparateNumber(cashvalue);
                    financialData.insurance[i].deductible = commaSeparateNumber(deductibleamt);
                	financialData.insurance[i].annualpremium = commaSeparateNumber(annualpremium);
                    financialData.insurance[i].reviewyear = lastreviewyear;
                }
            }
            $('#' + key + 'HomeInsuranceInputName').val(propertyname);
            $("#" + key + 'HomeInsuranceNameSummary').html(nameSummary);
            $("#" + key + 'HomeInsuranceInputPremiumPayment').val(commaSeparateNumber(annualpremium));
        	$("#" + key + 'HomeInsuranceInputDeductible').val(commaSeparateNumber(deductibleamt));
            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
            RetoggleOnOff("insurance");
        },
        // DELETING THE HOME / RENTER'S INSURANCE:
        // ----------------------------------

        resetHomeInsurance: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelHomeInsurance"));

            var i = 0;
            for (i = 0; i < financialData.insurance.length; i++)
            {
                if (financialData.insurance[i].id == key)
                {
                    $('#' + key + 'HomeInsuranceInputPremiumPayment').val(financialData.insurance[i].annualpremium);
                    $('#' + key + 'HomeInsuranceInputDeductible').val(financialData.insurance[i].deductible);
                    $('#' + key + 'HomeInsuranceInputName').val(financialData.insurance[i].name);
                    $('#' + key + 'HomeInsuranceReviewYear').val(financialData.insurance[i].reviewyear);
                    if($("#" + key + "homeinsuranceCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "homeinsuranceFAQArrow").click();
                        updateCollapse = true;                        
                    }
                }
            }
            RetoggleOnOff("insurance");
        },
        getKey: function() {
            return "homeinsurance";
        }


    });
    return new homeinsuranceView;
});