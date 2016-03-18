// Filename: views/score/accounts/healthinsurance
define([
    'handlebars',
    'text!../../../../html/score/accounts/healthinsurance.html',
], function(Handlebars, healthinsuranceTemplate) {
    var healthinsuranceView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(healthinsuranceTemplate).html();
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
            "click .createHealthInsuranceButton": "createHealthInsurance",
            "click .updateHealthInsuranceButton": "updateHealthInsurance",
            "click .cancelHealthInsuranceButton": "resetHealthInsurance",
        },
        // ADDING THE HEALTH INSURANCE:
        // ----------------------------------

        createHealthInsurance: function(event) {
            event.preventDefault();
            var name = $('#HealthInsuranceName').val().trim();
            var coverageamt = 0;
            var grouppolicy = $('input:radio[name=HealthInsurancePolicyMethod]:checked').val();
            if (typeof(grouppolicy) == 'undefined') {
                grouppolicy = '';
            }
            var insurancefor = $('#HealthInsurancePolicyType').val();
            var annualpremium = $('#HealthInsuranceInputPremiumPayment').val().replace(/,/g, '');
            var deductibleamt = $('#HealthInsuranceInputDeductible').val().replace(/,/g, '');
            var cashvalue = 0;
            var lastreviewyear = $('#HealthInsuranceReviewYear').val();

            if($("#healthinsuranceCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#healthinsuranceFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#healthinsuranceLoading").show();

            var formValues = {
                name: name,
                coverageamt: coverageamt,
                annualpremium: annualpremium,
                deductible: deductibleamt,
                amount: cashvalue,
                reviewyear: lastreviewyear,
                insurancefor: insurancefor,
                grouppolicy: grouppolicy,
                accttype: 'HEAL',
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
                            ['views/score/accounts/healthinsurance'],
                            function(addAccountV) {
                                $("#healthinsuranceLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Health Insurance";
                                if (jsonData.insurance.name != "")
                                    nameSummary = jsonData.insurance.name;
                                //Needed to construct the DOM
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
                                    insurancefor: jsonData.insurance.insurancefor,
                                    grouppolicy: jsonData.insurance.grouppolicy,
                                    coverageamt: commaSeparateNumber(jsonData.insurance.coverageamt),
                                    deductible: commaSeparateNumber(jsonData.insurance.deductible),
                                    annualpremium: commaSeparateNumber(jsonData.insurance.annualpremium),
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
                                $("#healthinsuranceAddAccount").removeClass("active");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                                userPreferences.insuranceAdded = '1';
                                userPreferences.insuranceData = '1';
                            }
                    );
                }
            });
        },
        // UPDATING THE HEALTH INSURANCE:
        // ----------------------------------

        updateHealthInsurance: function(event) {
            event.preventDefault();
            var eventname = event.target.id;
            var key = eventname.substring(0, eventname.indexOf("UpdateHealthInsurance"));
            var name = $('#' + key + 'HealthInsuranceName').val().trim();
            var insurancefor = $('#' + key + 'HealthInsurancePolicyType').val();
            var grouppolicy = $('input:radio[name=' + key + 'HealthInsurancePolicyMethod]:checked').val();
            if (typeof(grouppolicy) == 'undefined') {
                grouppolicy = '';
            }
            var coverageamt = 0;
            var annualpremium = $('#' + key + 'HealthInsuranceInputPremiumPayment').val().replace(/,/g, '');
            var deductibleamt = $('#' + key + 'HealthInsuranceInputDeductible').val().replace(/,/g, '');
            var cashvalue = 0;
            var lastreviewyear = $('#' + key + 'HealthInsuranceReviewYear').val();

            if($("#" + key + "healthinsuranceCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "healthinsuranceFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#" + key + "healthinsuranceLoading").show();

            var formvalues = {
                id: key,
                name: name,
                coverageamt: coverageamt,
                insurancefor: insurancefor,
                grouppolicy: grouppolicy,
                annualpremium: annualpremium,
                deductible: deductibleamt,
                amount: cashvalue,
                reviewyear: lastreviewyear,
                accttype: 'HEAL',
                action: 'UPDATE'
            };

            $.ajax({
                url: userAddUpdateInsuranceURL,
                dataType: "json",
                data: formvalues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                }
            });
            $("#" + key + "healthinsuranceLoading").hide();
            var nameSummary = "Health Insurance";
            if (name != "")
                nameSummary = name;
            var i = 0;
            for (i = 0; i < financialData.insurance.length; i++)
            {
                if (financialData.insurance[i].id == key)
                {
                    financialData.insurance[i].name = name;
                    financialData.insurance[i].nameSummary = nameSummary;
                    financialData.insurance[i].grouppolicy = grouppolicy;
                    financialData.insurance[i].amount = commaSeparateNumber(cashvalue);
                    financialData.insurance[i].reviewyear = lastreviewyear;
                    financialData.insurance[i].insurancefor = insurancefor;
                    financialData.insurance[i].annualpremium = commaSeparateNumber(annualpremium);
                    financialData.insurance[i].deductible = commaSeparateNumber(deductibleamt);
                    financialData.insurance[i].coverageamt = commaSeparateNumber(coverageamt);
                }
            }
            $('#' + key + 'HealthInsuranceName').val(name);
            $('#' + key + 'HealthInsuranceNameSummary').html(nameSummary);
            $("#" + key + 'HealthInsuranceInputPremiumPayment').val(commaSeparateNumber(annualpremium));
            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
            RetoggleOnOff("insurance");
        },
        // DELETING THE HEALTH INSURANCE:
        // ----------------------------------

        resetHealthInsurance: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelHealthInsurance"));

            var i = 0;
            for (i = 0; i < financialData.insurance.length; i++)
            {
                if (financialData.insurance[i].id == key)
                {
                    $("input:radio[name=" + key + "HealthInsurancePolicyMethod]")[0].checked = (financialData.insurance[i].grouppolicy == 69);
                    $("input:radio[name=" + key + "HealthInsurancePolicyMethod]")[1].checked = (financialData.insurance[i].grouppolicy == 70);
                    $('#' + key + 'HealthInsuranceName').val(financialData.insurance[i].name);
                    $('#' + key + 'HealthInsurancePolicyType').select(financialData.insurance[i].insurancefor);
                    $('#' + key + 'HealthInsuranceInputPremiumPayment').val(financialData.insurance[i].annualpremium);
                    $('#' + key + 'HealthInsuranceInputDeductible').val(financialData.insurance[i].deductible);
                    $("#" + key + 'HealthInsuranceReviewYear').val(financialData.insurance[i].reviewyear);
                    if($("#" + key + "healthinsuranceCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "healthinsuranceFAQArrow").click();
                        updateCollapse = true;                        
                    }
                }
            }
            RetoggleOnOff("insurance");
        },
        getKey: function() {
            return "healthinsurance";
        }
    });
    return new healthinsuranceView;
});