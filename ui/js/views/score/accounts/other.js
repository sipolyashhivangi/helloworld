// Filename: views/score/accounts/other
define([
    'handlebars',
    'text!../../../../html/score/accounts/other.html',
], function(Handlebars, otherTemplate) {
    var otherView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(otherTemplate).html();
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
            "click .createOtherButton": "createOther",
            "click .updateOtherButton": "updateOther",
            "click .cancelOtherButton": "resetOther",
        },
        createOther: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CreateOther"));
            var othersname = $('#OtherInputName').val().trim();
            var othersworth = $('#OtherInputWorth').val();
            var othersgrowth = $('#OtherInputGrowthRate').val();

            if($("#otherCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#otherFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#otherLoading").show();

            var formValues = {
                name: othersname,
                growthrate: othersgrowth,
                amount: othersworth,
                action: 'ADD',
                accttype: 'OTHE'
            };

            $.ajax({
                url: userAssetAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/other'],
                            function(addAccountV) {
                                $("#otherLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Other";
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
                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();
                                financialData.other[financialData.other.length] = obj;
                                RetoggleOnOff("assets");
                                $("#otherAddAccount").removeClass("active");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                            }
                    );
                }
            });
        },
        updateOther: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateOther"));
            var othersname = $('#' + key + 'OtherInputName').val().trim();
            var othersworth = $('#' + key + 'OtherInputWorth').val().replace(/,/g, '');
            var othersgrowth = $('#' + key + 'OtherInputGrowthRate').val();

            var formValues = {
                id: key,
                name: othersname,
                growthrate: othersgrowth,
                amount: othersworth,
                action: 'UPDATE',
                accttype: 'OTHE'
            };

            if($("#" + key + "otherCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "otherFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#" + key + "otherLoading").show();

            $.ajax({
                url: userAssetAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                }
            });
            
            $("#" + key + "otherLoading").hide();
            var nameSummary = "Other";
            if (othersname != "")
                nameSummary = othersname;
        	var i = 0;
            for (i = 0; i < financialData.other.length; i++)
            {
                if (financialData.other[i].id == key)
                {
                    financialData.other[i].amount = commaSeparateNumber(othersworth);
                    financialData.other[i].amountSummary = commaSeparateNumber(othersworth, 0);
                    financialData.other[i].name = othersname;
                    financialData.other[i].nameSummary = nameSummary;
                    financialData.other[i].growthrate = othersgrowth;
                }
            }
            $('#' + key + 'OtherInputName').val(othersname);
            $("#" + key + 'OtherNameSummary').html(nameSummary);
            //Fix Negative Dollar Amounts - only for showing purpose //
            if(othersworth < 0 ){
                var othersAmountForShow = '-$' + (commaSeparateNumber(othersworth, 0).replace("-", ""));
                $("#" + key + 'otherAmountSummary').html(othersAmountForShow);
            }else{
                $("#" + key + 'otherAmountSummary').html('$' + commaSeparateNumber(othersworth, 0));
            }
            $("#" + key + 'OtherInputWorth').val(commaSeparateNumber(othersworth));
            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
            RetoggleOnOff("assets");
        },
        resetOther: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelOtherButton"));
            var i = 0;
            for (i = 0; i < financialData.other.length; i++)
            {
                if (financialData.other[i].id == key)
                {
                    $('#' + key + 'OtherInputName').val(financialData.other[i].name);
                    $('#' + key + 'OtherInputWorth').val(financialData.other[i].amount);
                    $('#' + key + 'OtherInputGrowthRate').val(financialData.other[i].growthrate);
                    if($("#" + key + "otherCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "otherFAQArrow").click();
                        updateCollapse = true;                        
                    }
                }
            }
            RetoggleOnOff("assets");
        },
        getKey: function() {
            return "other";
        }
    });
    return new otherView;
});