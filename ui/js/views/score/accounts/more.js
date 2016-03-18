// Filename: views/score/accounts/more
define([
    'handlebars',
    'text!../../../../html/score/accounts/more.html',
], function(Handlebars, moreTemplate) {
    var moreView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var action = 'READ';

            var formValues = {
                action: action
            };

            $.ajax({
                url: userMiscAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                    var source = $(moreTemplate).html();
                    var template = Handlebars.compile(source);
                      if(typeof(userData.advisor) != 'undefined') {
                        userData.user.impersonationMode = true;
                        if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                            jsonData.permission = true;
                        }
                    }
                    $(element).html(template(jsonData));
                    $("#existingAccounts").hide();                                
                    $("#totalAccounts").hide();                                
                    init();
                 	$("#ProfileTracker").val('more');
                }
            });
        },
        events: {
            "change .miscmore": "fnMoreChange",
        },
        fnMoreChange: function(event) {
            event.preventDefault();
            var moremoney = ($('input:radio[name=MoreMoneyTransfer]:checked').val()) ? $('input:radio[name=MoreMoneyTransfer]:checked').val() : "";
            var moreinvrebal = ($('input:radio[name=MoreInvestmentRebalance]:checked').val()) ? $('input:radio[name=MoreInvestmentRebalance]:checked').val() : "";
            var moreautoinvest = ($('input:radio[name=MoreContributionInvested]:checked').val()) ? $('input:radio[name=MoreContributionInvested]:checked').val() : "";
            var moreliquidasset = ($('input:radio[name=MoreLiquidAssets]:checked').val()) ? $('input:radio[name=MoreLiquidAssets]:checked').val() : "";
            var morecharity = ($('input:radio[name=MoreCharity]:checked').val()) ? $('input:radio[name=MoreCharity]:checked').val() : "";
            var morecreditscore = ($('#MoreCreditScore').val()) ? $('#MoreCreditScore').val() : "";
            var morereviewmonth = ($('#MoreCreditScoreReviewMonth').val()) ? $('#MoreCreditScoreReviewMonth').val() : "";
            var morescorereviewyear = ($('#MoreCreditScoreReviewYear').val()) ? $('#MoreCreditScoreReviewYear').val() : "";
            var action = 'ADD';


            var formValues = {
                moremoney: moremoney,
                moreinvrebal: moreinvrebal,
                moreautoinvest: moreautoinvest,
                moreliquidasset: moreliquidasset,
                morecharity: morecharity,
                morecreditscore: morecreditscore,
                morereviewmonth: morereviewmonth,
                morescorereviewyear: morescorereviewyear,
                action: action,
                ctype: 'MORE',
            };
            $.ajax({
                url: userMiscAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                }
            });
        },
        getKey: function() {
            return "more";
        }
    });
    return new moreView;
});