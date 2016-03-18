define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/financialdetails.html',
], function(Handlebars, Backbone, financialDetailsTemplate) {

    var financialDetailsView = Backbone.View.extend({
        el: $("#body"),
        render: function() {
            var source = $(financialDetailsTemplate).html();
            var template = Handlebars.compile(source);
            RemoveScoreDialog();
            if (cef) {
                var data = {"hdn": ""};
            } else {
                var data = {"hdn": "hdn"};
            }
            if(typeof(userData.advisor) != 'undefined') {
                userData.user.impersonationMode = true;
                if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                    data.permission = true;
                }
            }
            $("#profileDetails").html(template(data));
            $(".financialIconOff").addClass("financialIconOn");
            $(".accOverlayTabOn").removeClass('accOverlayTabOn');
            $(".financialIconOn").removeClass("financialIconOff");
            $(".financialIconOn").parents("li").addClass('accOverlayTabOn');
        },
        events: {
            "click .profileNavInactive": "inactiveNavClick"
        },
        inactiveNavClick: function(event) {
            event.preventDefault();
            var name = event.target.id;

            var key = name.substring(0, name.indexOf('Link'));
            $("#" + key + "Selected").removeClass("hdn");
            $("#" + key + "Unselected").addClass("hdn");
            //TODO 

            var file = key;
            // For track, which page is currently render.
            $("#ProfileTracker").val(key);
            $(".nextProfilePopupBox").show();

            if (key != "connect")
                file = "accounts";
            require(
                    ['views/profile/' + file],
                    function(associatedV) {
                        if(currentNotificationKey == '' && $("#" + key + "Selected").hasClass("hdn")) {
                        }
                        else if (financialData.accountsdownloading && key != "miscellaneous" && key != "income" && key != "expenses" && key != "risk")
                        {
                            $.ajax({
                                url: getAllItem,
                                type: 'GET',
                                dataType: "json",
                                cache: false,
                                beforeSend: function(request) {
                                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                                },
                                success: function(data) {
									timeoutPeriod = defaultTimeoutPeriod;
                                    if (data.status == "OK") {                                    	
                                        fnUpdateAllData(data);
                                        fnUpdateFinancialData();
                                        if(!$("#" + key + "Selected").hasClass("hdn")) {
                                            associatedV.render(key);
                                        }
                                        popUpProfile();
                                        init();
                                    }
                                }
                            });
                        }
                        else
                        {
                            if (key != "miscellaneous" && key != "income" && key != "expenses" && key != "risk")
                            {
                                fnUpdateFinancialData();
                            }
                            associatedV.render(key);
                            popUpProfile();
                            init();
                            if (key == "miscellaneous" && currentState == 'addestate')
                            {
                                $("#estateplanningAddAccount").click();
                                currentState = '';
                            }
                            else if (key == "miscellaneous" && currentState == 'addmore')
                            {
                                $("#moreAddAccount").click();
                                currentState = '';
                            }
                            else if (key == "miscellaneous" && currentState == 'addtax')
                            {
                                $("#taxesAddAccount").click();
                                currentState = '';
                            }
                            else if (key == "miscellaneous") {
                                $("#taxesAddAccount").click();
                            }
                        }
                    }
            );

            if (typeof($(".profileNavOn")[0]) != 'undefined')
            {
                name = $(".profileNavOn")[0].id;
                var oldkey = name.substring(0, name.indexOf('Section'));
                $("#" + oldkey + "Selected").addClass("hdn");
                $("#" + oldkey + "Unselected").removeClass("hdn");
                $("#" + oldkey + "Section").removeClass("posRel profileNavOn");
            }
            $("#" + key + "Section").addClass("posRel profileNavOn");
        }
    });
    return new financialDetailsView;
});
