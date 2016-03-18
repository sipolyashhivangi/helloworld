// Filename: views/score/accounts/ira
define([
    'handlebars',
    'text!../../../../html/score/accounts/ira.html',
], function(Handlebars, iraTemplate) {
    var iraView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(iraTemplate).html();
            var template = Handlebars.compile(source);
            if(typeof(userData.advisor) != 'undefined') {
                userData.user.impersonationMode = true;
                if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                    obj.permission = true;
                    obj.invpos.permission = true;
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
            "click .createIRAButton": "createIRA",
            "click .addMoreIRATickers": "addMoreIRATickers",
            "click .updateIRAButton": "updateIRA",
            "click .cancelIRAButton": "resetIRA",
        },
        createIRA: function(event) {
            event.preventDefault();
            var iraname = $('#IRAInputName').val().trim();
            var irabalance = $('#IRAInputBalance').val().replace(/,/g, '');
            var iratype = $('input:radio[name=IRARothOrRegular]:checked').val();
            if (typeof(iratype) == 'undefined') {
                iratype = '';
            }
            var irabeneaccurate = $("input:radio[name=IRABeneficiaries]:checked").val();
            if (typeof(irabeneaccurate) == 'undefined') {
                irabeneaccurate = '';
            }
            var irahowmuch = $('#IRAInputContribution').val().replace(/,/g, '');
            var withdrawal = $('#IRAInputWithdrawal').val().replace(/,/g, '');
            var irahowmuchemployer = '0';

            var invPosStr = "";
            var tickercount = $('#IRATickerCount').val();
            for (var i = 0; i < tickercount; i++)
            {
                var ticker = $('#IRATicker' + i).val();
                var amount = $('#IRATickerPrice' + i).val().replace(/,/g, '');
                invPosStr = (invPosStr.length > 0) ? invPosStr + "," : "";
                invPosStr = invPosStr + ticker + "," + amount;
            }

            if($("#iraCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#iraFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#iraLoading").show();
            var formValues = {
                name: iraname,
                amount: irabalance,
                assettype: iratype,
                beneficiary: irabeneaccurate,
                contribution: irahowmuch,
                withdrawal: withdrawal,
                empcontribution: irahowmuchemployer,
                invpos: invPosStr,
                accttype: 'IRA',
                action: 'ADD'
            };

            $.ajax({
                url: userAssetAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/ira'],
                            function(addAccountV) {
                                $("#iraLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "IRA";
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
                                    withdrawal: commaSeparateNumber(jsonData.asset.withdrawal),
                                    status: jsonData.asset.status,
                                    ticker: jsonData.asset.ticker,
                                    contribution: commaSeparateNumber(jsonData.asset.contribution),
                                    assettype: jsonData.asset.assettype,
                                    beneficiary: jsonData.asset.beneficiary,
                                    invpos: jsonData.asset.invpos
                                };
                                if (typeof(obj.invpos) == 'undefined' || obj.invpos == null || obj.invpos.length == 0)
                                {
                                    obj.invpos = [];
                                    obj.invpos[0] = {'index': 0, 'id': obj.id};
                                    obj.invpos[1] = {'index': 1, 'id': obj.id};
                                    obj.invpos[2] = {'index': 2, 'id': obj.id};
                                    obj.invpos[3] = {'index': 3, 'id': obj.id};
                                    obj.invpos[4] = {'index': 4, 'id': obj.id};
                                    obj.tickercount = 5;
                                }
                                else
                                {
                                    for (var j = 0; j < obj.invpos.length; j++)
                                    {
                                        obj.invpos[j].index = j;
                                        obj.invpos[j].id = obj.id;
                                        obj.invpos[j].amount = commaSeparateNumber(obj.invpos[j].amount);
                                    }
                                    obj.tickercount = obj.invpos.length;
                                }
                                addAccountV.render('#existingAccounts', obj);
                                init();
                                accountIndex++;

                                financialData.investment[financialData.investment.length] = obj;
                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();
                                RetoggleOnOff("assets");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                                $("#iraAddAccount").removeClass("active");
                            }
                    );
                }
            });
        },
        addMoreIRATickers: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("IRATickerAddMore"));
            var index = parseInt($("#" + key + "IRATickerCount").val());
            var html = createInvestmentPositions("IRA", key, index, index + 5);
            $("#" + key + "IRATickerList").append(html);
            $("#" + key + "IRATickerCount").val(index + 5);
            init();
            RetoggleOnOff("assets");
        },
        updateIRA: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateIRAButton"));
            var iraname = $('#' + key + 'IRAInputName').val().trim();
            var irabalance = $('#' + key + 'IRAInputBalance').val().replace(/,/g, '');
            var iratype = $('input:radio[name=' + key + 'IRARothOrRegular]:checked').val();
            if (typeof(iratype) == 'undefined') {
                iratype = '';
            }
            var irabeneaccurate = $("input:radio[name=" + key + "IRABeneficiaries]:checked").val();
            if (typeof(irabeneaccurate) == 'undefined') {
                irabeneaccurate = '';
            }
            var irahowmuch = $('#' + key + 'IRAInputContribution').val().replace(/,/g, '');
            var withdrawal = $('#' + key + 'IRAInputWithdrawal').val().replace(/,/g, '');

            var invPosStr = "";
            var tickercount = $('#' + key + 'IRATickerCount').val();
            var invPos = new Array();
            for (var i = 0; i < tickercount; i++)
            {
                var ticker = $('#' + key + 'IRATicker' + i).val();
                var amount = $('#' + key + 'IRATickerPrice' + i).val().replace(/,/g, '');
                invPosStr = (invPosStr.length > 0) ? invPosStr + "," : "";
                invPosStr = invPosStr + ticker + "," + amount;
                invPos[i] = new Array();
                invPos[i]['ticker'] = ticker;
                invPos[i]['amount'] = amount;                
            }

            if($("#" + key + "iraCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "iraFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#" + key + "iraLoading").show();

            var formValues = {
                id: key,
                name: iraname,
                amount: irabalance,
                assettype: iratype,
                beneficiary: irabeneaccurate,
                contribution: irahowmuch,
                withdrawal: withdrawal,
                invpos: invPosStr,
                accttype: 'IRA',
                action: 'UPDATE'
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
			$("#" + key + "iraLoading").hide();
			var nameSummary = "IRA";
			if (iraname != "")
				nameSummary = iraname;
			var i = 0;
			for (i = 0; i < financialData.investment.length; i++)
			{
				if (financialData.investment[i].id == key)
				{
					financialData.investment[i].amount = commaSeparateNumber(irabalance);
					financialData.investment[i].amountSummary = commaSeparateNumber(irabalance, 0);
					financialData.investment[i].name = iraname;
					financialData.investment[i].nameSummary = nameSummary;
					financialData.investment[i].contribution = commaSeparateNumber(irahowmuch);
					financialData.investment[i].withdrawal = commaSeparateNumber(withdrawal);
					financialData.investment[i].beneficiary = irabeneaccurate;
					financialData.investment[i].assettype = iratype;
					financialData.investment[i].invpos = invPos;
					if (typeof(financialData.investment[i].invpos) == 'undefined' || financialData.investment[i].invpos == null || financialData.investment[i].invpos.length == 0)
					{
						financialData.investment[i].invpos = [];
						financialData.investment[i].invpos[0] = {'index': 0, 'id': financialData.investment[i].id, 'ticker': '', 'amount': ''};
						financialData.investment[i].invpos[1] = {'index': 1, 'id': financialData.investment[i].id, 'ticker': '', 'amount': ''};
						financialData.investment[i].invpos[2] = {'index': 2, 'id': financialData.investment[i].id, 'ticker': '', 'amount': ''};
						financialData.investment[i].invpos[3] = {'index': 3, 'id': financialData.investment[i].id, 'ticker': '', 'amount': ''};
						financialData.investment[i].invpos[4] = {'index': 4, 'id': financialData.investment[i].id, 'ticker': '', 'amount': ''};
						financialData.investment[i].tickercount = 5;
					}
					else
					{
						for (var j = 0; j < financialData.investment[i].invpos.length; j++)
						{
							financialData.investment[i].invpos[j].index = j;
							financialData.investment[i].invpos[j].amount = commaSeparateNumber(financialData.investment[i].invpos[j].amount);
							financialData.investment[i].invpos[j].id = financialData.investment[i].id;
						}
						financialData.investment[i].tickercount = financialData.investment[i].invpos.length;
					}

					var html = createInvestmentPositions("IRA", key, 0, financialData.investment[i].tickercount, financialData.investment[i].invpos);
					$("#" + key + "IRATickerList").html(html);
					$("#" + key + "IRATickerCount").val(financialData.investment[i].tickercount);
					init();
				}
			}
			$('#' + key + 'IRAInputName').val(iraname);
			$("#" + key + 'IRANameSummary').html(nameSummary);
                        //Fix Negative Dollar Amounts - only for showing purpose //
                        if(irabalance < 0 ){
                            var iraAmountForShow = '-$' + (commaSeparateNumber(irabalance, 0).replace("-", ""));
                            $("#" + key + 'iraAmountSummary').html(iraAmountForShow);
                        }else{
                            $("#" + key + 'iraAmountSummary').html('$' + commaSeparateNumber(irabalance, 0));
                        }
			$("#" + key + 'IRAInputBalance').val(commaSeparateNumber(irabalance));
			$("#" + key + 'IRAInputContribution').val(commaSeparateNumber(irahowmuch));
			$("#" + key + 'IRAInputWithdrawal').val(commaSeparateNumber(withdrawal));
			$("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
			RetoggleOnOff("assets");

        },
        resetIRA: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelIRAButton"));
            var i = 0;
            for (i = 0; i < financialData.investment.length; i++)
            {
                if (financialData.investment[i].id == key)
                {
                    $('#' + key + 'IRAInputName').val(financialData.investment[i].name);
                    $('#' + key + 'IRAInputBalance').val(financialData.investment[i].amount);
                    $('#' + key + 'IRAInputContribution').val(financialData.investment[i].contribution);
                    $('#' + key + 'IRAInputWithdrawal').val(financialData.investment[i].withdrawal);
                    $("input:radio[name=" + key + "IRABeneficiaries]")[0].checked = (financialData.investment[i].beneficiary == 1);
                    $("input:radio[name=" + key + "IRABeneficiaries]")[1].checked = (financialData.investment[i].beneficiary === "0");
                    $("input:radio[name=" + key + "IRABeneficiaries]")[2].checked = (financialData.investment[i].beneficiary == 2);
                    $("input:radio[name=" + key + "IRARothOrRegular]")[0].checked = (financialData.investment[i].assettype == 51);
                    $("input:radio[name=" + key + "IRARothOrRegular]")[1].checked = (financialData.investment[i].assettype == 52);
                    var html = createInvestmentPositions("IRA", key, 0, financialData.investment[i].tickercount, financialData.investment[i].invpos);
                    $("#" + key + "IRATickerList").html(html);
                    $("#" + key + "IRATickerCount").val(financialData.investment[i].tickercount);
                    init();
                    if($("#" + key + "iraCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "iraFAQArrow").click();
                        updateCollapse = true;                        
                    }

                }
            }
            RetoggleOnOff("assets");
        },
        getKey: function() {
            return "ira";
        }
    });
    return new iraView;
});