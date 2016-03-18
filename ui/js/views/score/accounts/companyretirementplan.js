// Filename: views/score/assets/companyretirementplan
define([
    'handlebars',
    'text!../../../../html/score/accounts/companyretirementplan.html',
], function(Handlebars, companyretirementplanTemplate) {
    var companyretirementplanView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(companyretirementplanTemplate).html();
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
            "click .createCompanyRetirementPlanButton": "createCompanyPlan",
            "click .addMoreCompanyRetirementPlanTickers": "addMoreCompanyPlanTickers",
            "click .updateCompanyRetirementPlanButton": "updateCompanyPlan",
            "click .cancelCompanyRetirementPlanButton": "resetCompanyPlan",
        },
        createCompanyPlan: function(event) {
            var crname = $('#CompanyRetirementPlanInputName').val().trim();
            var crbal = $('#CompanyRetirementPlanInputBalance').val().replace(/,/g, '');
            var crbeneacc = $('input:radio[name=CompanyRetirementPlanBeneficiaries]:checked').val();
            if (typeof(crbeneacc) == 'undefined') {
                crbeneacc = '';
            }
            var crhowmuch = $('#CompanyRetirementPlanInputContribution').val().replace(/,/g, '');
            var crhowmuchemp = $('#CompanyRetirementPlanInputEmployerContribution').val();
            var withdrawal = $('#CompanyRetirementPlanInputWithdrawal').val().replace(/,/g, '');

            var invPosStr = "";
            var tickercount = $('#CompanyRetirementPlanTickerCount').val();
            for (var i = 0; i < tickercount; i++)
            {
                var ticker = $('#CompanyRetirementPlanTicker' + i).val();
                var amount = $('#CompanyRetirementPlanTickerPrice' + i).val().replace(/,/g, '');
                invPosStr = (invPosStr.length > 0) ? invPosStr + "," : "";
                invPosStr = invPosStr + ticker + "," + amount;
            }

            if($("#companyretirementplanCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#companyretirementplanFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#companyretirementplanLoading").show();
            var formValues = {
                name: crname,
                amount: crbal,
                beneficiary: crbeneacc,
                contribution: crhowmuch,
                empcontribution: crhowmuchemp,
                withdrawal: withdrawal,
                invpos: invPosStr,
                accttype: 'CR',
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
                            ['views/score/accounts/companyretirementplan'],
                            function(addAccountV) {
                                $("#companyretirementplanLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Company Retirement Plan";
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
                                    retired: profileUserData.retirementstatus,
                                    withdrawal: commaSeparateNumber(jsonData.asset.withdrawal),
                                    ticker: jsonData.asset.ticker,
                                    beneficiary: jsonData.asset.beneficiary,
                                    contribution: commaSeparateNumber(jsonData.asset.contribution),
                                    empcontribution: jsonData.asset.empcontribution,
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
                                $("#companyretirementplanAddAccount").removeClass("active");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                            }
                    );
                }
            });

        },
        addMoreCompanyPlanTickers: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CompanyRetirementPlanTickerAddMore"));
            var index = parseInt($("#" + key + "CompanyRetirementPlanTickerCount").val());
            var html = createInvestmentPositions("CompanyRetirementPlan", key, index, index + 5);
            $("#" + key + "CompanyRetirementPlanTickerList").append(html);
            $("#" + key + "CompanyRetirementPlanTickerCount").val(index + 5);
            init();
            RetoggleOnOff("assets");
        },
        updateCompanyPlan: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateCompany"));
            var crname = $('#' + key + 'CompanyRetirementPlanInputName').val().trim();
            var crbal = $('#' + key + 'CompanyRetirementPlanInputBalance').val().replace(/,/g, '');
            var crbeneacc = $('input:radio[name=' + key + 'CompanyRetirementPlanBeneficiaries]:checked').val();
            if (typeof(crbeneacc) == 'undefined') {
                crbeneacc = '';
            }
            var crhowmuch = $('#' + key + 'CompanyRetirementPlanInputContribution').val().replace(/,/g, '');
            var crhowmuchemp = $('#' + key + 'CompanyRetirementPlanInputEmployerContribution').val();
            var withdrawal = $('#' + key + 'CompanyRetirementPlanInputWithdrawal').val().replace(/,/g, '');

            var invPosStr = "";
            var tickercount = $('#' + key + 'CompanyRetirementPlanTickerCount').val();
			var invPos = new Array();
            for (var i = 0; i < tickercount; i++)
            {
                var ticker = $('#' + key + 'CompanyRetirementPlanTicker' + i).val();
                var amount = $('#' + key + 'CompanyRetirementPlanTickerPrice' + i).val().replace(/,/g, '');
                invPosStr = (invPosStr.length > 0) ? invPosStr + "," : "";
                invPosStr = invPosStr + ticker + "," + amount;
                invPos[i] = new Array();
                invPos[i]['ticker'] = ticker;
                invPos[i]['amount'] = amount;                
            }

            if($("#" + key + "companyretirementplanCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "companyretirementplanFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#" + key + "companyretirementplanLoading").show();

            var formValues = {
                id: key,
                name: crname,
                amount: crbal,
                beneficiary: crbeneacc,
                contribution: crhowmuch,
                empcontribution: crhowmuchemp,
                withdrawal: withdrawal,
                invpos: invPosStr,
                accttype: 'CR',
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

			$("#" + key + "companyretirementplanLoading").hide();
			var nameSummary = "Company Retirement Plan";
			if (crname != "")
				nameSummary = crname;
			var i = 0;
			for (i = 0; i < financialData.investment.length; i++)
			{
				if (financialData.investment[i].id == key)
				{
					financialData.investment[i].amount = commaSeparateNumber(crbal);
					financialData.investment[i].amountSummary = commaSeparateNumber(crbal, 0);
					financialData.investment[i].name = crname;
					financialData.investment[i].nameSummary = nameSummary;
					financialData.investment[i].contribution = commaSeparateNumber(crhowmuch);
					financialData.investment[i].withdrawal = commaSeparateNumber(withdrawal);
					financialData.investment[i].beneficiary = crbeneacc;
					financialData.investment[i].empcontribution = crhowmuchemp;
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

					var html = createInvestmentPositions("CompanyRetirementPlan", key, 0, financialData.investment[i].tickercount, financialData.investment[i].invpos);
					$("#" + key + "CompanyRetirementPlanTickerList").html(html);
					$("#" + key + "CompanyRetirementPlanTickerCount").val(financialData.investment[i].tickercount);
					init();
				}
			}
			$("#" + key + 'CompanyRetirementPlanNameSummary').html(nameSummary);
			$('#' + key + 'CompanyRetirementPlanInputName').val(crname);
                        //Fix Negative Dollar Amounts - only for showing purpose //
                        if(crbal < 0 ){
                            var crAmountForShow = '-$' + (commaSeparateNumber(crbal, 0).replace("-", ""));
                            $("#" + key + 'companyretirementplanAmountSummary').html(crAmountForShow);
                        }else{
                            $("#" + key + 'companyretirementplanAmountSummary').html('$' + commaSeparateNumber(crbal, 0));
                        }
			$("#" + key + 'CompanyRetirementPlanInputBalance').val(commaSeparateNumber(crbal));
			$("#" + key + 'CompanyRetirementPlanInputContribution').val(commaSeparateNumber(crhowmuch));
			$("#" + key + 'CompanyRetirementPlanInputWithdrawal').val(commaSeparateNumber(withdrawal));
			$("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
			RetoggleOnOff("assets");

        },
        resetCompanyPlan: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelCompanyRetirementPlanButton"));
            var i = 0;
            for (i = 0; i < financialData.investment.length; i++)
            {
                if (financialData.investment[i].id == key)
                {
                    $('#' + key + 'CompanyRetirementPlanInputName').val(financialData.investment[i].name);
                    $('#' + key + 'CompanyRetirementPlanInputBalance').val(financialData.investment[i].amount);
                    $('#' + key + 'CompanyRetirementPlanInputContribution').val(financialData.investment[i].contribution);
                    $('#' + key + 'CompanyRetirementPlanInputEmployerContribution').val(financialData.investment[i].empcontribution);
                    $('#' + key + 'CompanyRetirementPlanInputWithdrawal').val(financialData.investment[i].withdrawal);
                    $("input:radio[name=" + key + "CompanyRetirementPlanBeneficiaries]")[0].checked = (financialData.investment[i].beneficiary == 1);
                    $("input:radio[name=" + key + "CompanyRetirementPlanBeneficiaries]")[1].checked = (financialData.investment[i].beneficiary === "0");
                    $("input:radio[name=" + key + "CompanyRetirementPlanBeneficiaries]")[2].checked = (financialData.investment[i].beneficiary == 2);
                    var html = createInvestmentPositions("CompanyRetirementPlan", key, 0, financialData.investment[i].tickercount, financialData.investment[i].invpos);
                    $("#" + key + "CompanyRetirementPlanTickerList").html(html);
                    $("#" + key + "CompanyRetirementPlanTickerCount").val(financialData.investment[i].tickercount);
                    init();
                    if($("#" + key + "companyretirementplanCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "companyretirementplanFAQArrow").click();
                        updateCollapse = true;                        
                    }
                }
            }
            RetoggleOnOff("assets");
        },
        getKey: function() {
            return "companyretirementplan";
        }
    });
    return new companyretirementplanView;
});