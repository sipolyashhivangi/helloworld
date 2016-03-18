define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/clientfinancialsummary.html',
    'text!../../../html/user/goals.html',
], function(Handlebars, Backbone, clientfinancialsummaryTemplate,goalTemplate) {

    var createnewView = Backbone.View.extend({
        el: $("#body"),
        render: function(obj,user_id) {
            var source = $(clientfinancialsummaryTemplate).html();
            var template = Handlebars.compile(source);
            obj.consumer_id = user_id;
            $("#clientfinancialsummaryContents").html(template(obj));

            try {
                google.load('visualization', '1', {
                    'callback': drawAllCharts,
                    'packages': ['corechart']
                });
            } catch (err) {
                alert(err);
            }
            initFinancial();
            $("#debtsPieInfo").addClass('hdn');
            $("#assetsPieInfo").addClass('hdn');
            var goalsource = $(goalTemplate).html();
            var goaltemplate = Handlebars.compile(goalsource);
            
            var obj = {};
            obj.goals = [];
            var colors = ["progressBarOrange", "progressBarTurquoise", "progressBarYellow", "progressBarBlue", "progressBarPink", "progressBarRainforest", "progressBarRed", "progressBarPurple"];
            var goalcount = 0;
                    
            RecalculateGoalAmounts(true);
            var goals = financialData.goals;

            for(var i = 0; i < goals.length; i++)
            {
                if(goals[i].goalstatus == 1 && goals[i].goaltype != 'DEBT')
                {
                    obj.goals[goalcount] = {};
                    obj.goals[goalcount].goalid = goals[i].id;
                    if(typeof(goals[i].goalname) == 'undefined' || goals[i].goalname == "") {
                        obj.goals[goalcount].goalname = GetDefaultNameForGoalType(goals[i].goaltype);
                    }
                    else
                    {
                        obj.goals[goalcount].goalname = goals[i].goalname;
                    }
                    if(goals[i].saved.replace(/,/g, '') < 0 && goals[i].saved.replace(/,/g, '') < goals[i].goalamount.replace(/,/g, '')) {
						obj.goals[goalcount].percentage = 0;
                    }
                    else if(goals[i].goalamount.replace(/,/g, '') > 0) {
                        obj.goals[goalcount].percentage = Math.round(goals[i].saved.replace(/,/g, '') *100/ goals[i].goalamount.replace(/,/g, ''));
                    } else {
                        obj.goals[goalcount].percentage = 100;
                    }
                    
                    if( obj.goals[goalcount].percentage > 100) {
                        obj.goals[goalcount].percentage = 100;                  
                    }
                    obj.goals[goalcount].classes = colors[goalcount%8];                 
                    obj.goals[goalcount].status = goals[i].status; 
                    obj.goals[goalcount].hideLinks = true; 
                    goalcount++;
                }
            }

            $('#goalsBox').html(goaltemplate(obj));
            
            $(".header2").css('font-size','1em');
            $('.goalsHeader').hide();
            $(".regBold3 a").attr("href", "javascript:void(0);");
            $(".regBold3 a").attr("style", "text-decoration:none");
            $(".regBold3 a").attr("class", "");
            init();
            
            
            $.getJSON(baseUrl + "/service/api/getscore?user_id="+user_id+"refresh=" + new Date().valueOf(), function(scoreData) {
                if (scoreData.status == "OK") {
                    $(".totalScoreHorseshoe").attr("src", "./ui/images/horseshoes/variations/myscore/" + scoreData.score.image + ".png", "style", "margin-left: 33px; margin-top: 3px");
                    $(".floatedScore").html(scoreData.score.totalscore);
                    var value = scoreData.score.point38;
                    value = Math.round(value * 2);
                    var imageId = Math.round(value / 5);
                    imageId = (imageId > 0) ? imageId : 0;
                    imageId = (imageId < 20) ? imageId : 20;
                    $(".floatedProfileComplete").html(value + '<span style="font-size: .6em;display:inline;">%');
                    $(".floatedProfileCompleteImage").attr("src", "./ui/images/horseshoes/variations/profile/ProfileHorseShoe" + imageId + ".png", "style", "margin-left: 25px; margin-top: 8px");
                }
            });

        },
        events: {
            "click .clientMyscore": "fnviewFinances", // To view client finances
        },
        fnviewFinances: function(event) {//Client View Finances
            event.preventDefault();
            var email = event.target.attributes.getNamedItem('clientemail').nodeValue;
            var formValues = {
                email: email
            };

            $.ajax({
                url: getviewFinances,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    window.location.replace(baseUrl + "/myscore");
                }
            });
        }

    });
    return new createnewView;
});