// Filename: views/score/accounts/estateplanning
define([
    'handlebars',
    'text!../../../../html/score/accounts/estateplanning.html',
], function(Handlebars, estateplanningTemplate) {
    var estateplanningView = Backbone.View.extend({
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
                    var source = $(estateplanningTemplate).html();
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
                 	$("#ProfileTracker").val('estateplanning');
                }
            });
        },
        events: {
            "change .estate": "fnEstateChange",
        },
        fnEstateChange: function(event) {
            event.preventDefault();
            var misctrust = $('input:radio[name=EstatePlanningWillTrust]:checked').val();
            if (typeof(misctrust) == 'undefined') {
                misctrust = '';
            }
            if (misctrust == 1)
                $("#EPReviewDate").removeClass("hdn");
            else
            {
                $("#EPReviewDate").addClass("hdn");
                $('#EstatePlanningWillTrustReviewMonth').val('');
                $('#EstatePlanningWillTrustReviewYear').val('');
            }
            var miscreviewmonth = $('#EstatePlanningWillTrustReviewMonth').val();
            var miscreviewyear = $('#EstatePlanningWillTrustReviewYear').val();
            var mischiddenasset = $('input:radio[name=EstatePlanningHiddenAssets]:checked').val();
            if (typeof(mischiddenasset) == 'undefined') {
                mischiddenasset = '';
            }
            if (mischiddenasset == 1)
                $("#EPRightPerson").removeClass("hdn");
            else
            {
                $("#EPRightPerson").addClass("hdn");
                $('input:radio[name=EstatePlanningHiddenAssetsLocation]').prop('checked', false);
            }
            var miscrightperson = $('input:radio[name=EstatePlanningHiddenAssetsLocation]:checked').val();
            ;
            if (typeof(miscrightperson) == 'undefined') {
                miscrightperson = '';
            }
            var miscliquid = $('input:radio[name=EstatePlanningLiquidated]:checked').val();
            if (typeof(miscliquid) == 'undefined') {
                miscliquid = '';
            }
            var miscspouse = $('input:radio[name=EstatePlanningIncapacitated]:checked').val();
            if (typeof(miscspouse) == 'undefined') {
                miscspouse = '';
            }
            var action = 'ADD';

            var formValues = {
                misctrust: misctrust,
                miscreviewyear: miscreviewyear,
                mischiddenasset: mischiddenasset,
                miscrightperson: miscrightperson,
                miscliquid: miscliquid,
                miscreviewmonth: miscreviewmonth,
                miscspouse: miscspouse,
                action: action,
                ctype: 'ESTATE'
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
            return "estateplanning";
        }
    });
    return new estateplanningView;
});
