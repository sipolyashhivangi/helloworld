// Filename: views/score/accounts/vehicleinsurance
define([
    'handlebars',
    'text!../../../../html/score/accounts/vehicleinsurance.html',
], function(Handlebars, vehicleinsuranceTemplate) {
    var vehicleinsuranceView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(vehicleinsuranceTemplate).html();
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
            "click .createVehicleInsuranceButton": "createVehicleInsurance",
            "click .updateVehicleInsuranceButton": "updateVehicleInsurance",
            "click .cancelVehicleInsuranceButton": "resetVehicleInsurance",
        },
        // ADDING THE VEHICLE INSURANCE:
        // ----------------------------------

        createVehicleInsurance: function(event) {
            event.preventDefault();
            var vehiclename = $('#VehicleInsuranceInputName').val().trim();
            var annualpremium = $('#VehicleInsuranceInputPremiumPayment').val().replace(/,/g, '');
            var deductibleamt = $('#VehicleInsuranceInputDeductible').val().replace(/,/g, '');
            var coverageamt = 0;
            var cashvalue = 0;
            var lastreviewyear = $('#VehicleInsuranceReviewYear').val();

            if($("#vehicleinsuranceCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#vehicleinsuranceFAQArrow").click();
                updateCollapse = true;
            }
            $("#vehicleinsuranceLoading").show();

            var formValues = {
                name: vehiclename,
                annualpremium: annualpremium,
                deductible: deductibleamt,
                coverageamt: coverageamt,
                amount: cashvalue,
                reviewyear: lastreviewyear,
                accttype: 'VEHI',
                action: 'ADD'
            };

            $.ajax({
                url: userAddUpdateInsuranceURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/vehicleinsurance'],
                            function(addAccountV) {
                                $("#vehicleinsuranceLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Vehicle Insurance";
                                if (jsonData.insurance.name != "")
                                    nameSummary = jsonData.insurance.name;

                                var obj = {
                                    accttype: jsonData.insurance.accttype,
                                    amount: commaSeparateNumber(jsonData.insurance.amount),
                                    id: jsonData.insurance.id,
                                    index: accountIndex,
                                    name: jsonData.insurance.name,
                                    nameSummary: nameSummary,
                                    refId: jsonData.insurance.refid,
                                    priority: jsonData.insurance.priority,
                                    status: jsonData.insurance.status,
                                    annualpremium: commaSeparateNumber(jsonData.insurance.annualpremium),
                                    coverageamt: commaSeparateNumber(jsonData.insurance.coverageamt),
                                    deductible: commaSeparateNumber(jsonData.insurance.deductible),
                                    reviewyear: jsonData.insurance.reviewyear

                                };

                                addAccountV.render('#existingAccounts', obj);
                                init();
                                accountIndex++;
                                financialData.insurance[financialData.insurance.length] = obj;
                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();
                                RetoggleOnOff("insurance");
                                $("#vehicleinsuranceAddAccount").removeClass("active");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                                userPreferences.insuranceAdded = '1';
                                userPreferences.insuranceData = '1';
                            }
                    );
                }
            });
        },
        // UPDATING THE VEHICLE INSURANCE:
        // ----------------------------------

        updateVehicleInsurance: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateVehicleInsurance"));
            var vehiclename = $('#' + key + 'VehicleInsuranceInputName').val().trim();
            var annualpremium = $('#' + key + 'VehicleInsuranceInputPremiumPayment').val().replace(/,/g, '');
            var deductibleamt = $('#' + key + 'VehicleInsuranceInputDeductible').val().replace(/,/g, '');
            var coverageamt = 0;
            var cashvalue = 0;
            var lastreviewyear = $('#' + key + 'VehicleInsuranceReviewYear').val();
            if($("#" + key + "vehicleinsuranceCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "vehicleinsuranceFAQArrow").click();
                updateCollapse = true;
            }
            $("#" + key + "vehicleinsuranceLoading").show();


            var formValues = {
                id: key,
                name: vehiclename,
                annualpremium: annualpremium,
                deductible: deductibleamt,
                coverageamt: coverageamt,
                amount: cashvalue,
                reviewyear: lastreviewyear,
                accttype: 'VEHI',
                action: 'UPDATE'
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
                    
            $("#" + key + "vehicleinsuranceLoading").hide();
            var nameSummary = "Vehicle Insurance";
            if (vehiclename != "")
            {
                nameSummary = vehiclename;
            }
            var i = 0;
            for (i = 0; i < financialData.insurance.length; i++)
            {
                if (financialData.insurance[i].id == key)
                {
                    financialData.insurance[i].amount = commaSeparateNumber(cashvalue);
                    financialData.insurance[i].name = vehiclename;
                    financialData.insurance[i].nameSummary = nameSummary;
                    financialData.insurance[i].annualpremium = commaSeparateNumber(annualpremium);
                    financialData.insurance[i].reviewyear = lastreviewyear;
                    financialData.insurance[i].coverageamt = commaSeparateNumber(coverageamt);
                    financialData.insurance[i].deductible = commaSeparateNumber(deductibleamt);
                }
            }
            $('#' + key + 'VehicleInsuranceInputName').val(vehiclename);
            $("#" + key + 'VehicleInsuranceNameSummary').html(nameSummary);
            $("#" + key + 'VehicleInsuranceInputPremiumPayment').val(commaSeparateNumber(annualpremium));
            $("#" + key + 'VehicleInsuranceInputDeductible').val(commaSeparateNumber(deductibleamt));
            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
    
            RetoggleOnOff("insurance");            
        },
        // Resetting THE VEHICLE INSURANCE:
        // ----------------------------------

        resetVehicleInsurance: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelVehicleInsurance"));
            var i = 0;
            for (i = 0; i < financialData.insurance.length; i++)
            {
                if (financialData.insurance[i].id == key)
                {
                    $('#' + key + 'VehicleInsuranceInputName').val(financialData.insurance[i].name);
                    $('#' + key + 'VehicleInsuranceInputPremiumPayment').val(financialData.insurance[i].annualpremium);
                    $('#' + key + 'VehicleInsuranceInputDeductible').val(financialData.insurance[i].deductible);
                    $('#' + key + 'VehicleInsuranceReviewYear').val(financialData.insurance[i].reviewyear);
                    if($("#" + key + "vehicleinsuranceCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "vehicleinsuranceFAQArrow").click();
                        updateCollapse = true;
                    }
                }
            }
            RetoggleOnOff("insurance");
        },
        getKey: function() {
            return "vehicleinsurance";
        }

    });
    return new vehicleinsuranceView;
});