// Filename: views/score/accounts/pension
define([
    'handlebars',
    'text!../../../../html/score/accounts/pension.html',
], function(Handlebars, pensionTemplate) {
    var pensionView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(pensionTemplate).html();
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
            "click .createPensionButton": "createPension",
            "click .updatePensionButton": "updatePension",
            "click .cancelPensionButton": "resetPension",
        },
        createPension: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, "createPension");
            var pensionname = $('#PensionInputName').val().trim();
            var pensionpayout = $('#PensionInputPayout').val().replace(/,/g, '');
            var pensionage = $('#PensionInputPayoutYear').val();
            var pensionbeneaccu = $('input:radio[name=PensionBeneficiaries]:checked').val();
            if (typeof(pensionbeneaccu) == 'undefined') {
                pensionbeneaccu = '';
            }

            if($("#pensionCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#pensionFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#pensionLoading").show();
            var formValues = {
                name: pensionname,
                amount: pensionpayout,
                agepayout: pensionage,
                beneficiary: pensionbeneaccu,
                action: 'ADD',
                accttype: 'PENS'
            };

            $.ajax({
                url: userAssetAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/pension'],
                            function(addAccountV) {
                                $("#pensionLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Pension";
                                if (jsonData.asset.name != "")
                                    nameSummary = jsonData.asset.name;

                                var obj = {accttype: jsonData.asset.accttype,
                                    amount: commaSeparateNumber(jsonData.asset.amount),
                                    id: jsonData.asset.id,
                                    index: accountIndex,
                                    priority: jsonData.asset.priority,
                                    name: jsonData.asset.name,
                                    nameSummary: nameSummary,
                                    refId: jsonData.asset.refid,
                                    status: jsonData.asset.status,
                                    ticker: jsonData.asset.ticker,
                                    growthrate: jsonData.asset.growthrate,
                                    agepayout: jsonData.asset.agepayout,
                                    beneficiary: jsonData.asset.beneficiary

                                };
                                addAccountV.render('#existingAccounts', obj);
                                init();
                                accountIndex++;

                                financialData.silent[financialData.silent.length] = obj;
                                RetoggleOnOff("assets");
                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();
                                $("#pensionAddAccount").removeClass("active");
                            }
                    );
                }
            });

        },
        updatePension: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdatePensionButton"));
            var pensionname = $('#' + key + 'PensionInputName').val().trim();
            var pensionpayout = $('#' + key + 'PensionInputPayout').val().replace(/,/g, '');
            var pensionage = $('#' + key + 'PensionInputPayoutYear').val();
            //var pensionbeneaccu = $('#' + key + 'PensionBeneficiaries').val();
            var pensionbeneaccu = $('input:radio[name='+key+'PensionBeneficiaries]:checked').val();

            if($("#" + key + "pensionCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "pensionFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#" + key + "pensionLoading").show();
            var formValues = {
                id: key,
                name: pensionname,
                amount: pensionpayout,
                agepayout: pensionage,
                beneficiary: pensionbeneaccu,
                action: 'UPDATE',
                accttype: 'PENS'
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
                
            $("#" + key + "pensionLoading").hide();
            var nameSummary = "Pension";
            if (pensionname != "")
            	nameSummary = pensionname;
            var i = 0;
            for (i = 0; i < financialData.silent.length; i++)
            {
                if (financialData.silent[i].id == key)
                {
                    financialData.silent[i].amount = commaSeparateNumber(pensionpayout);
                    financialData.silent[i].name = pensionname;
                    financialData.silent[i].nameSummary = nameSummary;
                    financialData.silent[i].beneficiary = pensionbeneaccu;
                    financialData.silent[i].agepayout = pensionage;
                }
            }
            $('#' + key + 'PensionInputName').val(pensionname);
            $("#" + key + 'PensionNameSummary').html(nameSummary);
            $("#" + key + 'PensionInputPayout').val(commaSeparateNumber(pensionpayout));
            RetoggleOnOff("assets");
            
        },
        resetPension: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelPensionButton"));
            var i = 0;
            for (i = 0; i < financialData.silent.length; i++)
            {
                if (financialData.silent[i].id == key)
                {
                    $('#' + key + 'PensionInputName').val(financialData.silent[i].name);
                    $('#' + key + 'PensionInputPayout').val(financialData.silent[i].amount);
                    $('#' + key + 'PensionInputPayoutYear').val(financialData.silent[i].agepayout);
                    $("input:radio[name=" + key + "PensionBeneficiaries]")[0].checked = (financialData.silent[i].beneficiary == 1);
                    $("input:radio[name=" + key + "PensionBeneficiaries]")[1].checked = (financialData.silent[i].beneficiary === "0");
                    $("input:radio[name=" + key + "PensionBeneficiaries]")[2].checked = (financialData.silent[i].beneficiary == 2);
                    if($("#" + key + "pensionCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "pensionFAQArrow").click();
                        updateCollapse = true;                        
                    }
                }
            }
            RetoggleOnOff("assets");
        },
        getKey: function() {
            return "pension";
        }
    });
    return new pensionView;
});