// Filename: views/score/accounts/risk
define([
   'handlebars',
   'text!../../../../html/score/accounts/risk.html',
    ], function(Handlebars,riskTemplate){
       var riskView = Backbone.View.extend({
           el: $("#body"),
           render: function(obj) {      
                var source = $(riskTemplate).html();  
                var template = Handlebars.compile(source); 
                $.getJSON(userRiskGetDataURL, function(data){
                    var risk_local_data = $.map(data.riskdata, function(value, index) {
                    return [value];
                    });
                    
                    // Global Variable
                    riskdata = risk_local_data;
                });
                
                if(profileUserData.needsUpdate)
                {
                    $.ajax({
                        url:getUserDetails,
                        type:'GET',
                        dataType:"json",
                        cache: false,
                        beforeSend: function(request) {
                            request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                        },
                        success:function (data) {
                            timeoutPeriod = defaultTimeoutPeriod;
                            if (data.status == "OK"){
                                fnUpdateUserData(data);
                                if(typeof(userData.advisor) != 'undefined') {
                                    userData.user.impersonationMode = true;
                                    if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                                        profileUserData.permission = true;
                                        profileUserData.risk = parseInt(profileUserData.risk);
                                    }
                                }
                                $("#riskHtml").html(template(profileUserData));
                                var value = 5;
                                if(typeof(profileUserData.risk) != 'undefined' && profileUserData.risk)
                                {
                                    value = profileUserData.risk;
                                }
                                else {
                                    var formValues = {
                                        risk:value,
                                        action:'UPDATE'
                                    };
                                    riskCurrentVariables[riskCurrentLength] = formValues;
                                    riskCurrentLength++;
                                    if (!riskAjaxInProcess && riskCurrentIntervalId == '') {
                                        riskCurrentIntervalId = setInterval(runRiskCalculations, 500);
                                    }
                                } 
                                
                                sliderDefaultValue = value;
                                
                                // Setting the tooltip value
                                init();            
                                
                                // Tooltip for slider
                                var tooltip = $('.tooltip');   
                                var tooltip_pointer = $('.pbPointerTop');                                   
                                var text_output = "";
                                var slide_value = 0;

                                var obj = riskdata[parseInt(value - 1)];
                                text_output = "<p bgcolor='#ffff00' style='font-style:bold;padding-left:20px'>68% of the time, 1 year returns could range from " + 
                                    obj.low_range_of_returns +"% (low) to " + 
                                    obj.high_range_of_returns  + "% (high)<p>";
                                text_output = text_output + "<p bgcolor='#ffc0cb' style='font-style:bold;padding-left:20px'>2.5% of the time, 1 year returns could fall below " + 
                                    obj.modeled_loss_expectation + "%</p>";
                                tooltip.html(text_output);
                                
                                var slider = $('#risk_slider_pointer');
                                slide_value = slider.css('left');   

                                // Adjusting the tooltip accordingly
                                tooltip.css('left', "-17px");
                                tooltip_pointer.css('left', parseInt(slide_value) - 15);

                            }
                        }
                    });
                }
                else
                {
                    if(typeof(userData.advisor) != 'undefined') {
                            userData.user.impersonationMode = true;
                        if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                            profileUserData.permission = true;
                            profileUserData.risk = parseInt(profileUserData.risk);
                        }
                    }
                    $("#riskHtml").html(template(profileUserData));
                    var value = 5;
                    if(typeof(profileUserData.risk) != 'undefined' && profileUserData.risk)
                    {
                        value = profileUserData.risk;
                    }           
                    else
                    {
                        var formValues = {
                            risk:value,
                            action:'UPDATE'
                        };
                        riskCurrentVariables[riskCurrentLength] = formValues;
                        riskCurrentLength++;
                        if (!riskAjaxInProcess && riskCurrentIntervalId == '') {
                            riskCurrentIntervalId = setInterval(runRiskCalculations, 500);
                        }
                    }
                    sliderDefaultValue = value;
                    init();            
                }
            },
            events: {
                "change #riskSliderValue": "fnRiskChange"
            },
            fnRiskChange: function(event) {
                event.preventDefault();
                var value = $("#riskSliderValue").val();
                if($('.riskSlider').slider( "value") != value)
                    $('.riskSlider').slider( "value", value );
                if(parseFloat(value) != parseFloat(profileUserData.risk) && (profileUserData.risk || value !=5))
                {    
                    var formValues = {
                        risk:value,
                        action:'UPDATE'
                    };
                    riskCurrentVariables[riskCurrentLength] = formValues;
                    riskCurrentLength++;
                    if (!riskAjaxInProcess && riskCurrentIntervalId == '') {
                        riskCurrentIntervalId = setInterval(runRiskCalculations, 500);
                    }
                }
            }, 
            getKey: function() {
                return "risk";
            }
       });
       return new riskView;
});
