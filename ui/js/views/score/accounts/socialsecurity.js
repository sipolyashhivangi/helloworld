// Filename: views/score/assets/socialsecurity
define([
    'handlebars',
    'text!../../../../html/score/accounts/socialsecurity.html',
], function(Handlebars, socialsecurityTemplate) {
    var socialsecurityView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(socialsecurityTemplate).html();
            var template = Handlebars.compile(source);
            if(typeof(userData.advisor) != 'undefined') {
                userData.user.impersonationMode = true;
                if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                    obj.permission = true;
                }
            }
            obj.socialSecurityStartAge = 65;
            if (financialData.goals.length > 0) {
                for (var i = 0; i < financialData.goals.length; i++) {
                    if (financialData.goals[i].goaltype == 'RETIREMENT') {
                        if(financialData.goals[i].retage < 62) { 
                            obj.socialSecurityStartAge = 62; 
                        }
                        else {
                            obj.socialSecurityStartAge = financialData.goals[i].retage;
                        }
                        break;
                    }
                }
            }
  
            $(element).append(template(obj));
        },
        events: {
            "click .createSocialSecurityButton": "createSocialSecurity",
            "click .updateSocialSecurityButton": "updateSocialSecurity",
            "click .cancelSocialSecurityButton": "resetSocialSecurity",
        },
        createSocialSecurity: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, "createSocialSecurity");
            var ssage62 = $('#SocialSecurityInputContribution').val().replace(/,/g, '');

            if($("#socialsecurityCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#socialsecurityFAQArrow").click();
                updateCollapse = true;
            }
            $("#socialsecurityLoading").show();

            var formValues = {
                amount: ssage62,
                action: 'ADD',
                accttype: 'SS'
            };

            $.ajax({
                url: userAssetAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/socialsecurity'],
                            function(addAccountV) {
                                $("#socialsecurityLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Social Security";

                                var obj = {accttype: jsonData.asset.accttype,
                                    amount: commaSeparateNumber(jsonData.asset.amount),
                                    id: jsonData.asset.id,
                                    index: accountIndex,
                                    name: jsonData.asset.name,
                                    nameSummary: nameSummary,
                                    priority: jsonData.asset.priority,
                                    refId: jsonData.asset.refid,
                                    status: jsonData.asset.status,
                                    ticker: jsonData.asset.ticker,
                                };
                                addAccountV.render('#existingAccounts', obj);
                                init();
                                accountIndex++;

                                financialData.silent[financialData.silent.length] = obj;
                                RetoggleOnOff("assets");
                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();
                                $("#socialsecurityAddAccount").removeClass("active");
                            }
                    );
                }
            });

        },
        updateSocialSecurity: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateSocialSecurity"));

            var ssage62 = $('#' + key + 'SocialSecurityInputContribution').val().replace(/,/g, '');

            if($("#" + key + "socialsecurityCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "socialsecurityFAQArrow").click();
                updateCollapse = true;
            }
            $("#" + key + "socialsecurityLoading").show();
            var formValues = {
                id: key,
                amount: ssage62,
                action: 'UPDATE',
                accttype: 'SS'
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

            $("#" + key + "socialsecurityLoading").hide();
            var nameSummary = "Social Security";
            var i = 0;
            for (i = 0; i < financialData.silent.length; i++)
            {
                if (financialData.silent[i].id == key)
                {
                    financialData.silent[i].amount = commaSeparateNumber(ssage62);
                    financialData.silent[i].nameSummary = nameSummary;
                }
            }
            $("#" + key + 'SocialSecurityInputContribution').val(commaSeparateNumber(ssage62));
            RetoggleOnOff("assets");

        },
        resetSocialSecurity: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelSocialSecurityButton"));
            var i = 0;
            for (i = 0; i < financialData.silent.length; i++)
            {
                if (financialData.silent[i].id == key)
                {
                    $('#' + key + 'SocialSecurityInputContribution').val(financialData.silent[i].amount);
                    if($("#" + key + "socialsecurityCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "socialsecurityFAQArrow").click();
                        updateCollapse = true;
                    }
                }
            }
            RetoggleOnOff("assets");
        },
        getKey: function() {
            return "socialsecurity";
        }
    });
    return new socialsecurityView;
});
