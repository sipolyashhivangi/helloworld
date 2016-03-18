define([
    'handlebars',
    'text!../../../html/user/goals.html',
], function(Handlebars, goalTemplate) {
    var goalView = Backbone.View.extend({
        el: $("#body"),
        render: function() {
            //get the details from the getuseritem
            
            var source = $(goalTemplate).html();
            var template = Handlebars.compile(source);

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
                    goalcount++;
                }
            }
            for(var i = 0; i < obj.goals.length; i++)
            {
                obj.goals[i].goalcount = goalcount;
            }
            obj.goalcount = goalcount;

            $('#goalsBox').html(template(obj));
            init();
        }
    });
    return new goalView;
});