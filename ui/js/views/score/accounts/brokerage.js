// Filename: views/score/accounts/brokerage
define([
    'handlebars',
    'backbone',
    'text!../../../../html/score/accounts/brokerage.html',
], function(Handlebars, Backbone, brokerageTemplate) {
    var brokerageView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(brokerageTemplate).html();
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
            "click .createBrokerageButton": "createBrokerage",
            "click .addMoreBrokerageTickers": "addMoreBrokerageTickers",
            "click .updateBrokerageButton": "updateBrokerage",
            "click .cancelBrokerageButton": "resetBrokerage"
        },
        // Creating the Brokerage Account:
        //-------

        createBrokerage: function(event) {
            event.preventDefault();
            var brokname = $('#BrokerageInputName').val().trim();
            var brokcont = $('#BrokerageInputContribution').val().replace(/,/g, '');
            var brokaccbal = $('#BrokerageInputBalance').val().replace(/,/g, '');
            var withdrawal = $('#BrokerageInputWithdrawal').val().replace(/,/g, '');

            var invPosStr = "";
            var tickercount = $('#BrokerageTickerCount').val();
            for (var i = 0; i < tickercount; i++)
            {
                var ticker = $('#BrokerageTicker' + i).val();
                var amount = $('#BrokerageTickerPrice' + i).val().replace(/,/g, '');
                invPosStr = (invPosStr.length > 0) ? invPosStr + "," : "";
                invPosStr = invPosStr + ticker + "," + amount;
            }

            if($("#brokerageCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#brokerageFAQArrow").click();
                updateCollapse = true;
            }
            $("#brokerageLoading").show();
            var formValues = {
                name: brokname,
                amount: brokaccbal,
                contribution: brokcont,
                withdrawal: withdrawal,
                invpos: invPosStr,
                action: 'ADD',
                accttype: 'BROK'
            };

            $.ajax({
                url: userAssetAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/brokerage'],
                            function(addAccountV) {
                                $("#brokerageLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Brokerage";
                                if (jsonData.asset.name != "")
                                    nameSummary = jsonData.asset.name;

                                var obj = {
                                    accttype: jsonData.asset.accttype,
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
                                    contribution: commaSeparateNumber(jsonData.asset.contribution),
                                    withdrawal: commaSeparateNumber(jsonData.asset.withdrawal),
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
                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();
                                financialData.investment[financialData.investment.length] = obj;
                                RetoggleOnOff("assets");
                                $("#brokerageAddAccount").removeClass("active");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                            }
                    );
                }
            });
        },
        addMoreBrokerageTickers: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("BrokerageTickerAddMore"));
            var index = parseInt($("#" + key + "BrokerageTickerCount").val());
            var html = createInvestmentPositions("Brokerage", key, index, index + 5);
            $("#" + key + "BrokerageTickerList").append(html);
            $("#" + key + "BrokerageTickerCount").val(index + 5);
            init();
            RetoggleOnOff("assets");
        },
        // Updating the Brokerage Account:
        //------------------------------

        updateBrokerage: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateBrokerageButton"));
            var brokname = $('#' + key + 'BrokerageInputName').val().trim();
            var brokcont = $('#' + key + 'BrokerageInputContribution').val().replace(/,/g, '');
            var brokaccbal = $('#' + key + 'BrokerageInputBalance').val().replace(/,/g, '');
            var withdrawal = $('#' + key + 'BrokerageInputWithdrawal').val().replace(/,/g, '');

            var invPosStr = "";
            var tickercount = $('#' + key + 'BrokerageTickerCount').val();
            var invPos = new Array();
            for (var i = 0; i < tickercount; i++)
            {
                var ticker = $('#' + key + 'BrokerageTicker' + i).val();
                var amount = $('#' + key + 'BrokerageTickerPrice' + i).val().replace(/,/g, '');
                invPosStr = (invPosStr.length > 0) ? invPosStr + "," : "";
                invPosStr = invPosStr + ticker + "," + amount;
                invPos[i] = new Array();
                invPos[i]['ticker'] = ticker;
                invPos[i]['amount'] = amount;                
            }

            if($("#" + key + "brokerageCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "brokerageFAQArrow").click();
                updateCollapse = true;
            }
            $("#" + key + "brokerageLoading").show();
            var formValues = {
                id: key,
                name: brokname,
                amount: brokaccbal,
                contribution: brokcont,
                withdrawal: withdrawal,
                invpos: invPosStr,
                action: 'UPDATE',
                accttype: 'BROK'

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
			$("#" + key + "brokerageLoading").hide();
			var nameSummary = "Brokerage";
			if (brokname != "")
				nameSummary = brokname;
			var i = 0;
			for (i = 0; i < financialData.investment.length; i++)
			{
				if (financialData.investment[i].id == key)
				{
					financialData.investment[i].amount = commaSeparateNumber(brokaccbal);
					financialData.investment[i].amountSummary = commaSeparateNumber(brokaccbal, 0);
					financialData.investment[i].name = brokname;
					financialData.investment[i].nameSummary = nameSummary;
					financialData.investment[i].contribution = commaSeparateNumber(brokcont);
					financialData.investment[i].withdrawal = commaSeparateNumber(withdrawal);
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

					var html = createInvestmentPositions("Brokerage", key, 0, financialData.investment[i].tickercount, financialData.investment[i].invpos);
					$("#" + key + "BrokerageTickerList").html(html);
					$("#" + key + "BrokerageTickerCount").val(financialData.investment[i].tickercount);
					init();
				}
			}
			$('#' + key + 'BrokerageInputName').val(brokname);
			$("#" + key + 'BrokerageNameSummary').html(nameSummary);
                        //Fix Negative Dollar Amounts - only for showing purpose //
                        if(brokaccbal < 0 ){
                            var brokAmountForShow = '-$' + (commaSeparateNumber(brokaccbal, 0).replace("-", ""));
                            $("#" + key + 'brokerageAmountSummary').html(brokAmountForShow);
                        }else{
                            $("#" + key + 'brokerageAmountSummary').html('$' + commaSeparateNumber(brokaccbal, 0));
                        }
			$("#" + key + 'BrokerageInputBalance').val(commaSeparateNumber(brokaccbal));
			$("#" + key + 'BrokerageInputContribution').val(commaSeparateNumber(brokcont));
			$("#" + key + 'BrokerageInputWithdrawal').val(commaSeparateNumber(withdrawal));
			$("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
			RetoggleOnOff("assets");
        },
        resetBrokerage: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelBrokerageButton"));
            var i = 0;
            for (i = 0; i < financialData.investment.length; i++)
            {
                if (financialData.investment[i].id == key)
                {
                    $('#' + key + 'BrokerageInputName').val(financialData.investment[i].name);
                    $('#' + key + 'BrokerageInputBalance').val(financialData.investment[i].amount);
                    $('#' + key + 'BrokerageInputContribution').val(financialData.investment[i].contribution);
                    $('#' + key + 'BrokerageInputWithdrawal').val(financialData.investment[i].withdrawal);
                    var html = createInvestmentPositions("Brokerage", key, 0, financialData.investment[i].tickercount, financialData.investment[i].invpos);
                    $("#" + key + "BrokerageTickerList").html(html);
                    $("#" + key + "BrokerageTickerCount").val(financialData.investment[i].tickercount);
                    init();
                    if($("#" + key + "brokerageCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "brokerageFAQArrow").click();
                        updateCollapse = true;
                    }
                }
            }
            RetoggleOnOff("assets");
        },
        getKey: function() {
            return "brokerage";
        }
    });
    return new brokerageView;
});