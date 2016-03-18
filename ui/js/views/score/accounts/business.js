// Filename: views/score/accounts/business
define([
    'handlebars',
    'text!../../../../html/score/accounts/business.html',
], function(Handlebars, businessTemplate) {
    var businessView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(businessTemplate).html();
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
            "click .createBusinessButton": "createBusiness",
            "click .updateBusinessButton": "updateBusiness",
            "click .cancelBusinessButton": "resetBusiness",
        },
        createBusiness: function(event) {
            event.preventDefault();
            var businame = $('#BusinessInputName').val().trim();
            var busiworth = $('#BusinessInputWorth').val().replace(/,/g, '');
            var busigrowth = $('#BusinessInputGrowthRate').val();

            if($("#businessCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#businessFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#businessLoading").show();

            var formValues = {
                name: businame,
                amount: busiworth,
                growthrate: busigrowth,
                action: 'ADD',
                accttype: 'BUSI'
            };

            $.ajax({
                url: userAssetAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/business'],
                            function(addAccountV) {
                                $("#businessLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Business";
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
                                    status: jsonData.asset.status,
                                    ticker: jsonData.asset.ticker,
                                    growthrate: jsonData.asset.growthrate
                                };
                                addAccountV.render('#existingAccounts', obj);
                                init();
                                accountIndex++;

                                financialData.other[financialData.other.length] = obj;
                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();
                                RetoggleOnOff("assets");
                                $("#businessAddAccount").removeClass("active");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                            }
                    );
                }
            });
        },
        updateBusiness: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateBusinessButton"));
            var businame = $('#' + key + 'BusinessInputName').val().trim();
            var busiworth = $('#' + key + 'BusinessInputWorth').val().replace(/,/g, '');
            var busigrowth = $('#' + key + 'BusinessInputGrowthRate').val();

            if($("#" + key + "businessCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "businessFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#" + key + "businessLoading").show();
            var formValues = {
                id: key,
                name: businame,
                amount: busiworth,
                growthrate: busigrowth,
                action: 'UPDATE',
                accttype: 'BUSI'
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
                    
            $("#" + key + "businessLoading").hide();
            var nameSummary = "Business";
            if (businame != "")
                nameSummary = businame;
            var i = 0;
            for (i = 0; i < financialData.other.length; i++)
            {
                if (financialData.other[i].id == key)
                {
                    financialData.other[i].amount = commaSeparateNumber(busiworth);
                    financialData.other[i].amountSummary = commaSeparateNumber(busiworth, 0);
                    financialData.other[i].name = businame;
                    financialData.other[i].nameSummary = nameSummary;
                    financialData.other[i].growthrate = busigrowth;
                }
            }
            $('#' + key + 'BusinessInputName').val(businame);
            $("#" + key + 'BusinessNameSummary').html(nameSummary);
            //Fix Negative Dollar Amounts - only for showing purpose //
            if(busiworth < 0 ){
                var businessAmountForShow = '-$' + (commaSeparateNumber(busiworth, 0).replace("-", ""));
                $("#" + key + 'businessAmountSummary').html(businessAmountForShow);
            }else{
                $("#" + key + 'businessAmountSummary').html('$' + commaSeparateNumber(busiworth, 0));
            }
            $("#" + key + 'BusinessInputWorth').val(commaSeparateNumber(busiworth));
            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
            RetoggleOnOff("assets");
        },
        resetBusiness: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelBusinessButton"));
            var i = 0;
            for (i = 0; i < financialData.other.length; i++)
            {
                if (financialData.other[i].id == key)
                {
                    $('#' + key + 'BusinessInputName').val(financialData.other[i].name);
                    $('#' + key + 'BusinessInputWorth').val(financialData.other[i].amount);
                    $('#' + key + 'BusinessInputGrowthRate').val(financialData.other[i].growthrate);
                    if($("#" + key + "businessCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "businessFAQArrow").click();
                        updateCollapse = true;                        
                    }
                }
            }
            RetoggleOnOff("assets");
        },
        getKey: function() {
            return "business";
        }
    });
    return new businessView;
});