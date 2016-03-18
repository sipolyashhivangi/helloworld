// Filename: views/score/accounts/disabilityinsurance
define([
    'handlebars',
    'text!../../../../html/score/accounts/disabilityinsurance.html',
], function(Handlebars, disabilityinsuranceTemplate) {
    var disabilityinsuranceView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(disabilityinsuranceTemplate).html();
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
            "click .createDisabilityInsuranceButton": "createDisabilityInsurance",
            "click .updateDisabilityInsuranceButton": "updateDisabilityInsurance",
            "click .cancelDisabilityInsuranceButton": "resetDisabilityInsurance",
        },
        createDisabilityInsurance: function(event) {
            event.preventDefault();
            var name = $('#DisabilityInsuranceName').val().trim();
            var annualincomecovered = $('#DisabilityInsuranceInputIncome').val().replace(/,/g, '');
            var annualpremiumpayment = $('#DisabilityInsuranceInputPremiumPayment').val().replace(/,/g, '');
            var grouppolicy = $('input:radio[name=DisabilityInsurancePolicyType]:checked').val();
            if (typeof(grouppolicy) == 'undefined') {
                grouppolicy = '';
            }
            var lastreviewyear = $('#DisabilityInsuranceReviewYear').val();
            var cashvalue = 0;

            if($("#disabilityinsuranceCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#disabilityinsuranceFAQArrow").click();
                updateCollapse = true;
            }
            $("#disabilityinsuranceLoading").show();

            var formValues = {
                name: name,
                coverageamt: annualincomecovered,
                annualpremium: annualpremiumpayment,
                grouppolicy: grouppolicy,
                reviewyear: lastreviewyear,
                amount: cashvalue,
                action: 'ADD',
                accttype: 'DISA'
            };

            $.ajax({
                url: userAddUpdateInsuranceURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/disabilityinsurance'],
                            function(addAccountV) {
                                $("#disabilityinsuranceLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Disability Insurance";
                                if (jsonData.insurance.name != "")
                                    nameSummary = jsonData.insurance.name;

                                var obj = {accttype: jsonData.insurance.accttype,
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
                                    coverageamt: commaSeparateNumber(jsonData.insurance.coverageamt, 1),
                                    grouppolicy: jsonData.insurance.grouppolicy
                                };

                                addAccountV.render('#existingAccounts', obj);
                                init();
                                accountIndex++;
                                financialData.insurance[financialData.insurance.length] = obj;
                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();
                                RetoggleOnOff("insurance");
                                $("#disabilityinsuranceAddAccount").removeClass("active");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                                userPreferences.insuranceAdded = '1';
                                userPreferences.insuranceData = '1';
                            }
                    );
                }
            });
        },
        // UPDATING THE DISABILITY INSURANCE:
        // ----------------------------------

        updateDisabilityInsurance: function(event) {
            event.preventDefault();
            var eventname = event.target.id;
            var key = eventname.substring(0, eventname.indexOf("UpdateDisabilityInsurance"));
            var name = $('#' + key + 'DisabilityInsuranceName').val().trim();
            var annualincomecovered = $('#' + key + 'DisabilityInsuranceInputIncome').val().replace(/,/g, '');
            var annualpremiumpayment = $('#' + key + 'DisabilityInsuranceInputPremiumPayment').val().replace(/,/g, '');
            var grouppolicy = $('input:radio[name=' + key + 'DisabilityInsurancePolicyType]:checked').val();
            if (typeof(grouppolicy) == 'undefined') {
                grouppolicy = '';
            }
            var lastreviewyear = $('#' + key + 'DisabilityInsuranceReviewYear').val();
            var cashvalue = 0;

            if($("#" + key + "disabilityinsuranceCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "disabilityinsuranceFAQArrow").click();
                updateCollapse = true;
            }
            $("#" + key + "disabilityinsuranceLoading").show();

            var formValues = {
                id: key,
                name: name,
                coverageamt: annualincomecovered,
                annualpremiumt: annualpremiumpayment,
                grouppolicy: grouppolicy,
                reviewyear: lastreviewyear,
                amount: cashvalue,
                action: 'UPDATE',
                accttype: 'DISA'
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
                    
            $("#" + key + "disabilityinsuranceLoading").hide();
            var nameSummary = "Disability Insurance";
            if (name != "")
                nameSummary = name;
            var i = 0;
            for (i = 0; i < financialData.insurance.length; i++)
            {
                if (financialData.insurance[i].id == key)
                {
                    financialData.insurance[i].name = name;
                    financialData.insurance[i].nameSummary = nameSummary;
                    financialData.insurance[i].amount = commaSeparateNumber(cashvalue);
                    financialData.insurance[i].annualpremium = commaSeparateNumber(annualpremiumpayment);
                    financialData.insurance[i].grouppolicy = grouppolicy;
                    financialData.insurance[i].coverageamt = commaSeparateNumber(annualincomecovered,1);
                    financialData.insurance[i].reviewyear = lastreviewyear;
                }
            }
            $('#' + key + 'DisabilityInsuranceName').val(name);
            $('#' + key + 'DisabilityInsuranceNameSummary').html(nameSummary);
            $("#" + key + 'DisabilityInsuranceInputPremiumPayment').val(commaSeparateNumber(annualpremiumpayment));
            $("#" + key + 'DisabilityInsuranceInputIncome').val(commaSeparateNumber(annualincomecovered,1));
            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
            RetoggleOnOff("insurance");
        },
        // UPDATING THE DISABILITY INSURANCE:
        // ----------------------------------


        resetDisabilityInsurance: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelDisabilityInsurance"));

            var i = 0;
            for (i = 0; i < financialData.insurance.length; i++)
            {
                if (financialData.insurance[i].id == key)
                {
                    $('#' + key + 'DisabilityInsuranceName').val(financialData.insurance[i].name);
                    $('#' + key + 'DisabilityInsuranceInputPremiumPayment').val(financialData.insurance[i].annualpremium);
                    $("#" + key + 'DisabilityInsuranceInputIncome').val(commaSeparateNumber(financialData.insurance[i].coverageamt, 1));
                    $("#" + key + 'DisabilityInsuranceReviewYear').val(financialData.insurance[i].reviewyear);
                    $("input:radio[name=" + key + "DisabilityInsurancePolicyType]")[0].checked = (financialData.insurance[i].grouppolicy == 69);
                    $("input:radio[name=" + key + "DisabilityInsurancePolicyType]")[1].checked = (financialData.insurance[i].grouppolicy == 70);
                    if($("#" + key + "disabilityinsuranceCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "disabilityinsuranceFAQArrow").click();
                        updateCollapse = true;
                    }
                }
            }
            RetoggleOnOff("insurance");
        },
        getKey: function() {
            return "disabilityinsurance";
        }


    });
    return new disabilityinsuranceView;
});