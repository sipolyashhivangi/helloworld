// Filename: views/score/assets/educationalaccount
define([
    'handlebars',
    'text!../../../../html/score/accounts/educationalaccount.html',
], function(Handlebars, educationalaccountTemplate) {
    var educationalaccountView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(educationalaccountTemplate).html();
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
            "click .createEducationalButton": "createEducational",
            "click .updateEducationalAccountButton": "updateEducational",
            "click .cancelEducationalAccountButton": "resetEducational",
        },
        createEducational: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CreateEducational"));
            var eduname = $('#EducationalInputName').val().trim();
            var eduacctype = $('#EducationalAccType').val();
            var eduaccbal = $('#EducationalInputBalance').val().replace(/,/g, '');
            var edubeneaccu = $('input:radio[name=EducationalAccountBeneficiaries]:checked').val();
            if (typeof(edubeneaccu) == 'undefined') {
                edubeneaccu = '';
            }
            var educontri = $('#EducationalInputContribution').val().replace(/,/g, '');
            var withdrawal = $('#EducationalInputWithdrawal').val().replace(/,/g, '');

            $("#educationalaccountLoading").show();
            if($("#educationalaccountCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#educationalaccountFAQArrow").click();
                updateCollapse = true;                        
            }
            var formValues = {
                name: eduname,
                assettype: eduacctype,
                amount: eduaccbal,
                beneficiary: edubeneaccu,
                contribution: educontri,
                withdrawal: withdrawal,
                action: 'ADD',
                accttype: 'EDUC'
            };

            $.ajax({
                url: userAssetAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/educationalaccount'],
                            function(addAccountV) {
                                $("#educationalaccountLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Educational Account";
                                if (jsonData.asset.name != "")
                                    nameSummary = jsonData.asset.name;

                                var obj = {accttype: jsonData.asset.accttype,
                                    amount: commaSeparateNumber(jsonData.asset.amount),
                                    amountSummary: commaSeparateNumber(jsonData.asset.amount, 0),
                                    id: jsonData.asset.id,
                                    index: accountIndex,
                                    priority: jsonData.asset.priority,
                                    name: jsonData.asset.name,
                                    nameSummary: nameSummary,
                                    refId: jsonData.asset.refid,
                                    retired: profileUserData.retirementstatus,
                                    status: jsonData.asset.status,
                                    withdrawal: commaSeparateNumber(jsonData.asset.withdrawal),
                                    ticker: jsonData.asset.ticker,
                                    contribution: commaSeparateNumber(jsonData.asset.contribution),
                                    beneficiary: jsonData.asset.beneficiary,
                                    assettype: jsonData.asset.assettype
                                };

                                addAccountV.render('#existingAccounts', obj);
                                init();
                                accountIndex++;
                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();
                                financialData.other[financialData.other.length] = obj;
                                RetoggleOnOff("assets");
                                $("#educationalaccountAddAccount").removeClass("active");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                            }
                    );
                }
            });
        },
        updateEducational: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateEducational"));
            var eduname = $('#' + key + 'EducationalInputName').val().trim();
            var eduacctype = $('#' + key + 'EducationalAccType').val();
            var eduaccbal = $('#' + key + 'EducationalInputBalance').val().replace(/,/g, '');
            var edubeneaccu = $('input:radio[name=' + key + 'EducationalAccountBeneficiaries]:checked').val();
            if (typeof(edubeneaccu) == 'undefined') {
                edubeneaccu = '';
            }
            var educontri = $('#' + key + 'EducationalInputContribution').val().replace(/,/g, '');
            var withdrawal = $('#' + key + 'EducationalInputWithdrawal').val().replace(/,/g, '');

            if($("#" + key + "educationalaccountCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "educationalaccountFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#" + key + "educationalaccountLoading").show();

            var formValues = {
                id: key,
                name: eduname,
                assettype: eduacctype,
                amount: eduaccbal,
                beneficiary: edubeneaccu,
                contribution: educontri,
                withdrawal: withdrawal,
                action: 'UPDATE',
                accttype: 'EDUC'
            };

            $.ajax({
                url: userAssetAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                }
            });
                    
            $("#" + key + "educationalaccountLoading").hide();
            var nameSummary = "Educational Account";
            if (eduname != "")
                nameSummary = eduname;
            var i = 0;
            for (i = 0; i < financialData.other.length; i++)
            {
                if (financialData.other[i].id == key)
                {
                    financialData.other[i].amount = commaSeparateNumber(eduaccbal);
                    financialData.other[i].amountSummary = commaSeparateNumber(eduaccbal, 0);
                    financialData.other[i].name = eduname;
                    financialData.other[i].nameSummary = nameSummary;
                    financialData.other[i].contribution = commaSeparateNumber(educontri);
                    financialData.other[i].withdrawal = commaSeparateNumber(withdrawal);
                    financialData.other[i].beneficiary = edubeneaccu;
                    financialData.other[i].assettype = eduacctype;
                }
            }
            $('#' + key + 'EducationalInputName').val(eduname);
            $("#" + key + 'EducationalAccountNameSummary').html(nameSummary);
            //Fix Negative Dollar Amounts - only for showing purpose //
            if(eduaccbal < 0 ){
                var eduaccAmountForShow = '-$' + (commaSeparateNumber(eduaccbal, 0).replace("-", ""));
                $("#" + key + 'educationalaccountAmountSummary').html(eduaccAmountForShow);
            }else{
                $("#" + key + 'educationalaccountAmountSummary').html('$' + commaSeparateNumber(eduaccbal, 0));
            }
            $("#" + key + 'EducationalInputBalance').val(commaSeparateNumber(eduaccbal));
            $("#" + key + 'EducationalInputContribution').val(commaSeparateNumber(educontri));
            $("#" + key + 'EducationalInputWithdrawal').val(commaSeparateNumber(withdrawal));
            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
            RetoggleOnOff("assets");
        },
        resetEducational: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelEducationalButton"));
            var i = 0;
            for (i = 0; i < financialData.other.length; i++)
            {
                if (financialData.other[i].id == key)
                {
                    $('#' + key + 'EducationalInputName').val(financialData.other[i].name);
                    $('#' + key + 'EducationalInputBalance').val(financialData.other[i].amount);
                    $('#' + key + 'EducationalAccType').val(financialData.other[i].assettype);
                    $('#' + key + 'EducationalInputContribution').val(financialData.other[i].contribution);
                    $('#' + key + 'EducationalInputWithdrawal').val(financialData.other[i].withdrawal);
                    $("input:radio[name=" + key + "EducationalAccountBeneficiaries]")[0].checked = (financialData.other[i].beneficiary == 1);
                    $("input:radio[name=" + key + "EducationalAccountBeneficiaries]")[1].checked = (financialData.other[i].beneficiary === "0");
                    $("input:radio[name=" + key + "EducationalAccountBeneficiaries]")[2].checked = (financialData.other[i].beneficiary == 2);
                    if($("#" + key + "educationalaccountCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "educationalaccountFAQArrow").click();
                        updateCollapse = true;                        
                    }
                }
            }
            RetoggleOnOff("assets");
        },
        getKey: function() {
            return "educationalaccount";
        }
    });
    return new educationalaccountView;
});