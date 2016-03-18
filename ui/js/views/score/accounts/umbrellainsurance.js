// Filename: views/score/accounts/umbrellainsurance
define([
    'handlebars',
    'text!../../../../html/score/accounts/umbrellainsurance.html',
], function(Handlebars, umbrellainsuranceTemplate) {
    var umbrellainsuranceView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(umbrellainsuranceTemplate).html();
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
            "click .createUmbrellaInsuranceButton": "createUmbrellaInsurance",
            "click .updateUmbrellaInsuranceButton": "updateUmbrellaInsurance",
            "click .cancelUmbrellaInsuranceButton": "resetUmbrellaInsurance",
        },
        // ADDING THE UMBRELLA INSURANCE:
        // ----------------------------------

        createUmbrellaInsurance: function(event) {
            event.preventDefault();
            var name = $('#UmbrellaInsuranceName').val().trim();
            var coverageamt = $('#UmbrellaInsuranceInputAmount').val().replace(/,/g, '');
            var annualpremium = $('#UmbrellaInsuranceInputPremiumPayment').val().replace(/,/g, '');
            var cashvalue = 0;
            var lastreviewyear = $('#UmbrellaInsuranceReviewYear').val();

            if($("#umbrellainsuranceCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#umbrellainsuranceFAQArrow").click();
                updateCollapse = true;
            }
            $("#umbrellainsuranceLoading").show();

            var formValues = {
                name: name,
                coverageamt: coverageamt,
                annualpremium: annualpremium,
                amount: cashvalue,
                reviewyear: lastreviewyear,
                accttype: 'UMBR',
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
                            ['views/score/accounts/umbrellainsurance'],
                            function(addAccountV) {
                                $("#umbrellainsuranceLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Umbrella Insurance";
                                if (jsonData.insurance.name != "")
                                    nameSummary = jsonData.insurance.name;

                                var obj = {accttype: jsonData.insurance.accttype,
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
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                                $("#umbrellainsuranceAddAccount").removeClass("active");
                                userPreferences.insuranceAdded = '1';
                                userPreferences.insuranceData = '1';
                            }
                    );
                }
            });
        },
        // UPDATING THE UMBRELLA INSURANCE:
        // ----------------------------------

        updateUmbrellaInsurance: function(event) {
            event.preventDefault();
            var ename = event.target.id;
            var key = ename.substring(0, ename.indexOf("UpdateUmbrellaInsurance"));
            var name = $('#' + key + 'UmbrellaInsuranceName').val().trim();

            var coverageamt = $('#' + key + 'UmbrellaInsuranceInputAmount').val().replace(/,/g, '');
            var annualpremium = $('#' + key + 'UmbrellaInsuranceInputPremiumPayment').val().replace(/,/g, '');
            var cashvalue = 0;
            var lastreviewyear = $('#' + key + 'UmbrellaInsuranceReviewYear').val();

            if($("#" + key + "umbrellainsuranceCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "umbrellainsuranceFAQArrow").click();
                updateCollapse = true;
            }
            $("#" + key + "umbrellainsuranceLoading").show();

            var formValues = {
                id: key,
                name: name,
                coverageamt: coverageamt,
                annualpremium: annualpremium,
                amount: cashvalue,
                reviewyear: lastreviewyear,
                accttype: 'UMBR',
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
            $("#" + key + "umbrellainsuranceLoading").hide();
        	var nameSummary = "Umbrella Insurance";
            if (name != "")
                nameSummary = name;

            var i = 0;
            for (i = 0; i < financialData.insurance.length; i++)
            {
                if (financialData.insurance[i].id == key)
                {
                    financialData.insurance[i].name = name;
                    financialData.insurance[i].nameSummary = nameSummary;
                    financialData.insurance[i].annualpremium = commaSeparateNumber(annualpremium);
                    financialData.insurance[i].amount = commaSeparateNumber(cashvalue);
                    financialData.insurance[i].reviewyear = lastreviewyear;
                    financialData.insurance[i].coverageamt = commaSeparateNumber(coverageamt);
                }
            }
            $('#' + key + 'UmbrellaInsuranceName').val(name);
            $('#' + key + 'UmbrellaInsuranceNameSummary').html(nameSummary);
            $("#" + key + 'UmbrellaInsuranceInputPremiumPayment').val(commaSeparateNumber(annualpremium));
            $("#" + key + 'UmbrellaInsuranceInputCoverage').val(commaSeparateNumber(coverageamt));
            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
            RetoggleOnOff("insurance");
        },
        // DELETING THE UMBRELLA INSURANCE:
        // ----------------------------------

        resetUmbrellaInsurance: function(event) {
            event.preventDefault();
            var ename = event.target.id;
            var key = ename.substring(0, ename.indexOf("CancelUmbrellaInsurance"));
            var i = 0;
            for (i = 0; i < financialData.insurance.length; i++)
            {
                if (financialData.insurance[i].id == key)
                {
                    $('#' + key + 'UmbrellaInsuranceName').val(financialData.insurance[i].name);
                    $('#' + key + 'UmbrellaInsuranceInputPremiumPayment').val(financialData.insurance[i].annualpremium);
                    $('#' + key + 'UmbrellaInsuranceInputAmount').val(financialData.insurance[i].coverageamt);
                    $('#' + key + 'UmbrellaInsuranceReviewYear').val(financialData.insurance[i].reviewyear);
                    if($("#" + key + "umbrellainsuranceCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "umbrellainsuranceFAQArrow").click();
                        updateCollapse = true;
                    }
                }
            }
            RetoggleOnOff("insurance");
        },
        getKey: function() {
            return "umbrellainsurance";
        }


    });
    return new umbrellainsuranceView;
});