// Filename: views/score/accounts/longtermcareinsurance
define([
    'handlebars',
    'text!../../../../html/score/accounts/longtermcareinsurance.html',
], function(Handlebars, longtermcareinsuranceTemplate) {
    var longtermcareinsuranceView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(longtermcareinsuranceTemplate).html();
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
            "click .createLongTermCareInsuranceButton": "createLongTermCareInsurance",
            "click .updateLongTermCareInsuranceButton": "updateLongTermCareInsurance",
            "click .cancelLongTermCareInsuranceButton": "resetLongTermCareInsurance",
        },
        createLongTermCareInsurance: function(event) {
            event.preventDefault();
            var dailybenefitamt = $('#LongTermCareInsuranceInputIncome').val().replace(/,/g, '');
            var annualpremium = $('#LongTermCareInsuranceInputPremiumPayment').val().replace(/,/g, '');
            var dailyamtindexed = $('input:radio[name=LongTermCareInsuranceInflation]:checked').val();
            if (typeof(dailyamtindexed) == 'undefined') {
                dailyamtindexed = '';
            }
            var cashvalue = 0;
            var lastreviewyear = $('#LongTermCareInsuranceReviewYear').val();

            if($("#longtermcareinsuranceCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#longtermcareinsuranceFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#longtermcareinsuranceLoading").show();

            var formValues = {
                dailybenfitamt: dailybenefitamt,
                annualpremium: annualpremium,
                dailyamtindexed: dailyamtindexed,
                amount: cashvalue,
                reviewyear: lastreviewyear,
                action: 'ADD',
                accttype: 'LONG'
            };
            $.ajax({
                url: userAddUpdateInsuranceURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/longtermcareinsurance'],
                            function(addAccountV) {
                                $("#longtermcareinsuranceLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Long Term Care Insurance";

                                var obj = {
                                    accttype: jsonData.insurance.accttype,
                                    id: jsonData.insurance.id,
                                    index: accountIndex,
                                    priority: jsonData.insurance.priority,
                                    name: jsonData.insurance.name,
                                    nameSummary: nameSummary,
                                    refId: jsonData.insurance.refid,
                                    status: jsonData.insurance.status,
                                    amount:commaSeparateNumber(jsonData.insurance.amount),
                                    annualpremium: commaSeparateNumber(jsonData.insurance.annualpremium),
                                    dailybenfitamt: commaSeparateNumber(jsonData.insurance.dailybenfitamt),
                                    reviewyear: jsonData.insurance.reviewyear,
                                    dailyamtindexed: jsonData.insurance.dailyamtindexed,
                                };

                                addAccountV.render('#existingAccounts', obj);
                                init();
                                accountIndex++;
                                financialData.insurance[financialData.insurance.length] = obj;
                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();
                                RetoggleOnOff("insurance");
                                $("#longtermcareinsuranceAddAccount").removeClass("active");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                                userPreferences.insuranceAdded = '1';
                                userPreferences.insuranceData = '1';
                            }
                    );
                }
            });

        },
        // Updating the LONG TERM INSURANCE
        // ---------------------------------

        updateLongTermCareInsurance: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateLongTermCareInsurance"));
            var dailybenefitamt = $('#' + key + 'LongTermCareInsuranceInputIncome').val().replace(/,/g, '');
            var annualpremium = $('#' + key + 'LongTermCareInsuranceInputPremiumPayment').val().replace(/,/g, '');
            var dailyamtindexed = $('input:radio[name=' + key + 'LongTermCareInsuranceInflation]:checked').val();
            if (typeof(dailyamtindexed) == 'undefined') {
                dailyamtindexed = '';
            }
            var cashvalue = 0;
            var lastreviewyear = $('#' + key + 'LongTermCareInsuranceReviewYear').val();

            if($("#" + key + "longtermcareinsuranceCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "longtermcareinsuranceFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#" + key + "longtermcareinsuranceLoading").show();

            var formValues = {
                id: key,
                dailybenfitamt: dailybenefitamt,
                annualpremium: annualpremium,
                dailyamtindexed: dailyamtindexed,
                amount: cashvalue,
                reviewyear: lastreviewyear,
                action: 'UPDATE',
                accttype: 'LONG'
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
            
            $("#" + key + "longtermcareinsuranceLoading").hide();
            var nameSummary = "Long Term Care Insurance";

        	var i = 0;
            for (i = 0; i < financialData.insurance.length; i++)
            {
                if (financialData.insurance[i].id == key)
                {
                    financialData.insurance[i].name = nameSummary;
                    financialData.insurance[i].nameSummary = nameSummary;
                    financialData.insurance[i].amount = commaSeparateNumber(cashvalue);
                    financialData.insurance[i].dailyamtindexed = dailyamtindexed;
                    financialData.insurance[i].annualpremium = commaSeparateNumber(annualpremium);
                    financialData.insurance[i].dailybenfitamt = commaSeparateNumber(dailybenefitamt);
                    financialData.insurance[i].reviewyear = lastreviewyear;
                }
            }
            $("#" + key + 'LongTermCareInsuranceInputPremiumPayment').val(commaSeparateNumber(annualpremium));
            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
            RetoggleOnOff("insurance");
        },
        // Deleting the LONG TERM INSURANCE
        // ---------------------------------


        resetLongTermCareInsurance: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelLongTermCareInsurance"));

            var i = 0;
            for (i = 0; i < financialData.insurance.length; i++)
            {
                if (financialData.insurance[i].id == key)
                {
                    $('#' + key + 'LongTermCareInsuranceInputPremiumPayment').val(financialData.insurance[i].annualpremium);
                    $('#' + key + 'LongTermCareInsuranceInputIncome').val(financialData.insurance[i].dailybenfitamt);
                    $('#' + key + 'LongTermCareInsuranceReviewYear').val(financialData.insurance[i].reviewyear);
                    $("input:radio[name=" + key + "LongTermCareInsuranceInflation]")[0].checked = (financialData.insurance[i].dailyamtindexed == 1);
                    $("input:radio[name=" + key + "LongTermCareInsuranceInflation]")[1].checked = (financialData.insurance[i].dailyamtindexed === "0");
                    if($("#" + key + "longtermcareinsuranceCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "longtermcareinsuranceFAQArrow").click();
                        updateCollapse = true;                        
                    }
                }
            }
            RetoggleOnOff("insurance");
        },
        getKey: function() {
            return "longtermcareinsurance";
        }


    });
    return new longtermcareinsuranceView;
});