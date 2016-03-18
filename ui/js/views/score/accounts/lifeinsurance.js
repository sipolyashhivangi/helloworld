// Filename: views/score/accounts/lifeinsurance
define([
    'handlebars',
    'text!../../../../html/score/accounts/lifeinsurance.html',
], function(Handlebars, lifeinsuranceTemplate) {
    var lifeinsuranceView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(lifeinsuranceTemplate).html();
            var template = Handlebars.compile(source);
            if(typeof(userData.advisor) != 'undefined') {
                userData.user.impersonationMode = true;
                if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                    obj.permission = true;
                }
            }
            //Fix Negative Dollar Amounts - only for showing purpose //
            if (typeof (obj.amountSummary) != 'undefined') {
                if (parseFloat(obj.amountSummary.replace(/,/g, '')) < 0) {
                    obj.amountSummaryForShow = '-$' + (commaSeparateNumber(obj.amountSummary, 0).replace("-", ""));
                } else {
                    obj.amountSummaryForShow = '$' + commaSeparateNumber(obj.amountSummary, 0);
                }
            }
            $(element).append(template(obj));
        },
        events: {
            "click .createLifeInsuranceButton": "createLifeInsurance",
            "click .updateLifeInsuranceButton": "updateLifeInsurance",
            "click .cancelLifeInsuranceButton": "resetLifeInsurance",
            "change .profileAccType": "fnchkCashValue",
        },
        fnchkCashValue: function(event) {
            var eventname = event.target.id;
            var key = eventname.substring(0, eventname.indexOf("LifeInsurancePolicyType"));

            if (event.target.value == 64) {
                $("#" + key + "LifeInsuranceCashValueDiv").hide();
                $("#" + key + "LifeInsurancePolicyYearDiv").show();
            } else {
                $("#" + key + "LifeInsuranceCashValueDiv").show();
                $("#" + key + "LifeInsurancePolicyYearDiv").hide();
            }
        },
        // ADDING LIFE INSURANCE :
        // -------------------------

        createLifeInsurance: function(event) {
            event.preventDefault();
            var name = $('#LifeInsuranceName').val().trim();
            var insurancefor = $('#LifeInsurancePolicyFor').val();
            var beneficiary = $('#LifeInsurancePolicyBeneficiary').val();
            var grouppolicy = $('input:radio[name=LifeInsurancePolicyMethod]:checked').val();
            if (typeof(grouppolicy) == 'undefined') {
                grouppolicy = '';
            }

            var insurancetype = $('#LifeInsurancePolicyType').val();
            var insurancecashvalue = '';
            var insurancepolicyyearend = '';
            if(insurancetype == 64) {
            	insurancepolicyyearend = $('#LifeInsuranceInputPolicyYear').val().replace(/,/g, '');
            }
            else
            {
            	insurancecashvalue = $('#LifeInsuranceInputCashValue').val().replace(/,/g, '');
            }

            var insurancemoneyondeath = $('#LifeInsuranceInputIncome').val().replace(/,/g, '');
            var insuranceannualpremium = $('#LifeInsuranceInputPremiumPayment').val().replace(/,/g, '');
            var insurancereviewyear = $('#LifeInsuranceReviewYear').val();


            if($("#lifeinsuranceCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#lifeinsuranceFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#lifeinsuranceLoading").show();

            var formValues = {
                name: name,
                insurancefor: insurancefor,
                amount: insurancecashvalue,
                lifeinstype: insurancetype,
                amtupondeath: insurancemoneyondeath,
                policyendyear: insurancepolicyyearend,
                annualpremium: insuranceannualpremium,
                reviewyear: insurancereviewyear,
                beneficiary: beneficiary,
                grouppolicy: grouppolicy,
                accttype: 'LIFE',
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
                            ['views/score/accounts/lifeinsurance'],
                            function(addAccountV) {
                                $("#lifeinsuranceLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Life Insurance";
                                if (jsonData.insurance.name != "")
                                    nameSummary = jsonData.insurance.name;

                                var obj = {
                                    accttype: jsonData.insurance.accttype,
                                    amount: commaSeparateNumber(jsonData.insurance.amount),
                                    amountSummary: commaSeparateNumber(jsonData.insurance.amount, 0),
                                    id: jsonData.insurance.id,
                                    index: accountIndex,
                                    priority: jsonData.insurance.priority,
                                    name: jsonData.insurance.name,
                                    nameSummary: nameSummary,
                                    insurancefor: jsonData.insurance.insurancefor,
                                    refId: jsonData.insurance.refid,
                                    status: jsonData.insurance.status,
                                    annualpremium: commaSeparateNumber(jsonData.insurance.annualpremium),
                                    amtupondeath: commaSeparateNumber(jsonData.insurance.amtupondeath),
                                    policyendyear: jsonData.insurance.policyendyear,
                                    beneficiary: jsonData.insurance.beneficiary,
                                    grouppolicy: jsonData.insurance.grouppolicy,
                                    reviewyear: jsonData.insurance.reviewyear,
                                    lifeinstype: jsonData.insurance.lifeinstype
                                };

                                addAccountV.render('#existingAccounts', obj);
                                init();
                                accountIndex++;
                                financialData.insurance[financialData.insurance.length] = obj;
                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();

                                RetoggleOnOff("insurance");
                                $("#lifeinsuranceAddAccount").removeClass("active");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                                userPreferences.insuranceAdded = '1';
                                userPreferences.insuranceData = '1';
                            }
                    );
                }
            });
        },
        // UPDATING LIFE INSURANCE :
        // -------------------------

        updateLifeInsurance: function(event) {
            event.preventDefault();
            var eventname = event.target.id;
            var key = eventname.substring(0, eventname.indexOf("UpdateLifeInsurance"));
            var name = $('#' + key + 'LifeInsuranceName').val().trim();
            var insurancefor = $('#' + key + 'LifeInsurancePolicyFor').val();
            var beneficiary = $('#' + key + 'LifeInsurancePolicyBeneficiary').val();
            var grouppolicy = $('input:radio[name=' + key + 'LifeInsurancePolicyMethod]:checked').val();
            if (typeof(grouppolicy) == 'undefined') {
                grouppolicy = '';
            }

            var insurancetype = $('#' + key + 'LifeInsurancePolicyType').val();
            var insurancecashvalue = '';
            var insurancepolicyyearend = '';
            if(insurancetype == 64) {
            	insurancepolicyyearend = $('#' + key + 'LifeInsuranceInputPolicyYear').val().replace(/,/g, '');
            }
            else
            {
            	insurancecashvalue = $('#' + key + 'LifeInsuranceInputCashValue').val().replace(/,/g, '');
            }

            var insurancemoneyondeath = $('#' + key + 'LifeInsuranceInputIncome').val().replace(/,/g, '');
            var insuranceannualpremium = $('#' + key + 'LifeInsuranceInputPremiumPayment').val().replace(/,/g, '');
            var insurancereviewyear = $('#' + key + 'LifeInsuranceReviewYear').val();

            if($("#" + key + "lifeinsuranceCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "lifeinsuranceFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#" + key + "lifeinsuranceLoading").show();

            var formValues = {
                id: key,
                name: name,
                insurancefor: insurancefor,
                amount: insurancecashvalue,
                lifeinstype: insurancetype,
                amtupondeath: insurancemoneyondeath,
                policyendyear: insurancepolicyyearend,
                annualpremium: insuranceannualpremium,
                reviewyear: insurancereviewyear,
                beneficiary: beneficiary,
                grouppolicy: grouppolicy,
                accttype: 'LIFE',
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

            $("#" + key + "lifeinsuranceLoading").hide();
            var nameSummary = "Life Insurance";
            if (name != "")
                nameSummary = name;

            var i = 0;
            for (i = 0; i < financialData.insurance.length; i++)
            {
                if (financialData.insurance[i].id == key)
                {
                    financialData.insurance[i].name = name;
                    financialData.insurance[i].nameSummary = nameSummary;
                    financialData.insurance[i].insurancefor = insurancefor;
                    financialData.insurance[i].amount = commaSeparateNumber(insurancecashvalue);
                    financialData.insurance[i].amountSummary = commaSeparateNumber(insurancecashvalue, 0);
                    financialData.insurance[i].annualpremium = commaSeparateNumber(insuranceannualpremium);
                    financialData.insurance[i].amtupondeath = commaSeparateNumber(insurancemoneyondeath);
                    financialData.insurance[i].lifeinstype = insurancetype;
                    financialData.insurance[i].policyendyear = insurancepolicyyearend;
                    financialData.insurance[i].reviewyear = insurancereviewyear;
                    financialData.insurance[i].beneficiary = beneficiary;
                    financialData.insurance[i].grouppolicy = grouppolicy;
                }
            }
            $("#" + key + 'LifeInsuranceName').val(name);
            $("#" + key + 'LifeInsuranceNameSummary').html(nameSummary);
            //Fix Negative Dollar Amounts - only for showing purpose //
            if(insurancecashvalue < 0 ){
                var insAmountForShow = '-$' + (commaSeparateNumber(insurancecashvalue, 0).replace("-", ""));
                $("#" + key + 'lifeinsuranceAmountSummary').html(insAmountForShow);
            }else{
                $("#" + key + 'lifeinsuranceAmountSummary').html('$' + commaSeparateNumber(insurancecashvalue, 0));
            }
            $("#" + key + 'LifeInsuranceInputCashValue').val(commaSeparateNumber(insurancecashvalue));
            $("#" + key + 'LifeInsuranceInputPremiumPayment').val(commaSeparateNumber(insuranceannualpremium));
            $("#" + key + 'LifeInsuranceInputIncome').val(commaSeparateNumber(insurancemoneyondeath));
            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
            RetoggleOnOff("insurance");
        },
        // Reseting LIFE INSURANCE :
        // -------------------------


        resetLifeInsurance: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelLifeInsurance"));

            var i = 0;
            for (i = 0; i < financialData.insurance.length; i++)
            {
                if (financialData.insurance[i].id == key)
                {
                    $("#" + key + 'LifeInsuranceName').val(financialData.insurance[i].name);
                    $('#' + key + 'LifeInsuranceInputPremiumPayment').val(financialData.insurance[i].annualpremium);
                    $('#' + key + 'LifeInsuranceInputCashValue').val(financialData.insurance[i].amount);
                    $('#' + key + 'LifeInsuranceInputIncome').val(financialData.insurance[i].amtupondeath);
                    $('#' + key + 'LifeInsuranceReviewYear').val(financialData.insurance[i].reviewyear);
                    $('#' + key + 'LifeInsurancePolicyType').val(financialData.insurance[i].lifeinstype);
                    $('#' + key + 'LifeInsuranceInputPolicyYear').val(financialData.insurance[i].policyendyear);
                    $('#' + key + 'LifeInsurancePolicyFor').val(financialData.insurance[i].insurancefor);
                    $('#' + key + 'LifeInsurancePolicyBeneficiary').val(financialData.insurance[i].beneficiary);
                    $("input:radio[name=" + key + "LifeInsurancePolicyMethod]")[0].checked = (financialData.insurance[i].grouppolicy == 69);
                    $("input:radio[name=" + key + "LifeInsurancePolicyMethod]")[1].checked = (financialData.insurance[i].grouppolicy == 70);
                    if($("#" + key + "lifeinsuranceCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "lifeinsuranceFAQArrow").click();
                        updateCollapse = true;                        
                    }
                }
            }
            RetoggleOnOff("insurance");
        },
        getKey: function() {
            return "lifeinsurance";
        }

    });
    return new lifeinsuranceView;
});
