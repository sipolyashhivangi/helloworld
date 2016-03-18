define([
    'jquery',
    'handlebars',
    'backbone',
    'text!../../../html/profile/goals.html',
    ], function($, Handlebars, Backbone, goalsTemplate){
    
        var goalsView = Backbone.View.extend({
            el: $("#body"),
            render: function(obj){
                var source = $(goalsTemplate).html();
                var template = Handlebars.compile(source);
                if(typeof(userData.advisor) != 'undefined') {
                    userData.user.impersonationMode = true;
                    if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                        financialData.permission = true;
                    }
                }
                $(obj).html(template(financialData));
                var currentView = this;
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
                                currentView.loadData();
                                $("#ProfileTracker").val('goals');
                                $(".nextProfilePopupBox").hide();
                            }
                        }
                   });
                }
                else
                {
                    currentView.loadData();
                    $("#ProfileTracker").val('goals');
                    $(".nextProfilePopupBox").hide();
                }                
                $('#existingGoals').html('');
                $('#newGoals').html('');
            },
            loadData: function(){
                // Load the array with data
                var outerLoopCount = 0;
                var innerLoopCount = 0;
                var loadGoals = []
                RecalculateGoalAmounts(true);
                
                var goals = [];
                for(var attrname in financialData) 
                { 
                    if(attrname == "goals")
                        goals = goals.concat(financialData[attrname]); 
                }
                    
                goals.sort(function (a, b) {
                    return a.goalpriority - b.goalpriority;
                });

                // Load each view
                goalIndex = 1;
                for(var attrname in goals)
                { 
                    if(goals[attrname]["goalstatus"] == 1)
                    {
                        outerLoopCount++;
                        goals[attrname]["uiloadstatus"] = 0;
                        goals[attrname]["index"] = goalIndex;
                        goalIndex++;
                        var keyName = calculateGoalKey(goals[attrname]["goaltype"]);
                        require(
                            [ 'views/score/goals/' + keyName],
                            function( existingGoalV){
                                var goalKey = existingGoalV.getKey();
                                
                                // Views come in, in reverse order so need to reorder them
                                for(var attr in goals)
                                {
                                    var keyName = calculateGoalKey(goals[attr]["goaltype"]);                            
                                    
                                    if(goals[attr]["goalstatus"] == 1 && keyName == goalKey && goals[attr]["uiloadstatus"] == 0)
                                    {
                                        loadGoals[attr] = existingGoalV; 
                                        innerLoopCount++;
                                        goals[attr]["uiloadstatus"] = 1;
                                        break;
                                    }
                                }
                            
                                // When the above storage is done, we now start loading data, and adjusting all the fields
                                if(innerLoopCount>=outerLoopCount)
                                {                                	
                                    $("#debtAddGoal").show();
                                    calculateGoals = false;
                                    
                                    for(var i in goals)
                                    {
                                        if(goals[i]["goalstatus"] == 1)
                                        {                                
                                            var goal = goals[i];
                                            if(loadGoals[i].getKey() == "debt")
                                            {
                                                $("#debtAddGoal").hide();
                                                goal["debts"] = financialData["debts"];
                                                var customTotal = 0;
                                                var debtTotal = 0;
                                                for(var j = 0; j<goal.debts.length; j++)
                                                {
                                                    if(goal.debts[j].status == 0 && goal.debts[j].monthly_payoff_balances == 0) {
                                                        debtTotal += parseFloat(goal.debts[j].amount.replace(/,/g,''));
                                                    }
                                                    if(typeof(goal.payoffdebts) == 'undefined')
                                                    {
                                                        goal.payoffdebts = "";
                                                    }
                                                    var payoffdebts = goal.payoffdebts.split(',');
                                                    if(goal.debts[j].status == 0 && goal.debts[j].monthly_payoff_balances == 0 && (payoffdebts.indexOf(goal.debts[j].id) > -1 || goal.payoffdebts == ""))
                                                    {
                                                        goal.debts[j].payoffdebts = true;
                                                        customTotal += parseFloat(goal.debts[j].amount.replace(/,/g,''));
                                                    }
                                                    else
                                                    {
                                                        goal.debts[j].payoffdebts = false;
                                                    }
                                                    goal.debts[j].goalid = goal.id;
                                                }  
                                                goal["debtTotal"] = commaSeparateNumber(debtTotal, 0);
                                                goal["customTotal"] = commaSeparateNumber(customTotal, 0);
                                            }
                                            loadGoals[i].render("#existingGoals", goal);
                                        }
                                    }
                                    for(var i in goals)
                                    {                                       
                                        if (currentOpenField == goals[i]["id"])
                                        {
                                            var keyName = calculateGoalKey(goals[i]["goaltype"], "goals");
                                            $("#" + goals[i]["id"] + keyName + "FAQArrow").click();
                                            $.scrollTo($("#" + goals[i]["id"] + keyName + "ProfileDataBox"), 0);
                                            if (throughActionStep) {
                                                var formFields = {
                                                    event: currentActionEvent,
                                                    id: currentactionstepid
                                                }
                                                $.ajax({
                                                    url: addTrackuserURL,
                                                    type: 'POST',
                                                    dataType: "json",
                                                    data: formFields
                                                });
                                                throughActionStep = false;
                                            }
                                        }
                                    }                                                                
                                    currentOpenField = '';
                                    currentOpenType = '';
                                    init(); 
                                    $("#existingHeader").show();
                                    calculateGoals = true;
                    
                                }
                            }
                            );
                    }
                }
                if(outerLoopCount == 0) {
                    $("#debtAddGoal").show();
                }
            },
            events: {
                "click .addGoalButton": "addGoal",
                "click .deleteGoalButton": "deleteGoal",
                "click .removeNewGoalButton": "removeNewGoal"
            },
            addGoal: function(event){
                event.preventDefault(); 
                var name = event.target.id;
                var btnKey = "AddGoal";
                var key = name.substring(0, name.indexOf(btnKey));
                require(
                    [ 'views/score/goals/' + key],
                    function( addGoalV ){
                        $("#newGoals").html('');
                        var goal = {};
                        if(key == "debt")
                        {
                            goal["debts"] = financialData["debts"];
                            var customTotal = 0;
                            var debtTotal = 0;
                            for(var j = 0; j<goal.debts.length; j++)
                            {
                                goal.payoffdebts = "";
                                if(goal.debts[j].status == 0 &&  goal.debts[j].monthly_payoff_balances == 0) {
                                    goal.debts[j].payoffdebts = true;
                                    debtTotal += parseFloat(goal.debts[j].amount.replace(/,/g,''));
                                    customTotal += parseFloat(goal.debts[j].amount.replace(/,/g,''));
                                }
                                else
                                {
                                    goal.debts[j].payoffdebts = false;
                                }
                                goal.debts[j].goalid = "";
                            }  
                            goal["debtTotal"] = commaSeparateNumber(debtTotal, 0);
                            goal["customTotal"] = commaSeparateNumber(customTotal, 0);
                        }
                        else
                        {
                            var foundNewGoal = false;
                        	for(var i=0;i<goalSnapshot.length; i++) {
								if(goalSnapshot[i].id == "") {
									goalSnapshot[i].id = "";
									goalSnapshot[i].goalpriority = "1000000";
									goalSnapshot[i].goalendMonth = "1";
									goalSnapshot[i].goalendYear = "2015";
									goalSnapshot[i].goalendDay = "1";
									goalSnapshot[i].goalamount = "0";
									goalSnapshot[i].permonth = "0";
									goalSnapshot[i].goalstatus = "1";
									goalSnapshot[i].goaltype = key.toUpperCase();
									goalSnapshot[i].saved = "0";
									goalSnapshot[i].contributions = "0";
									foundNewGoal = true;
								}				    
							}
							if(!foundNewGoal) {
							    var length = goalSnapshot.length;
								goalSnapshot[length] = {};
								goalSnapshot[length].id = "";
								goalSnapshot[length].goalpriority = "1000000";
								goalSnapshot[length].goalendMonth = "1";
								goalSnapshot[length].goalendYear = "2015";
								goalSnapshot[length].goalendDay = "1";
								goalSnapshot[length].goalamount = "0";
								goalSnapshot[length].permonth = "0";
								goalSnapshot[length].goalstatus = "1";
								goalSnapshot[length].goaltype = key.toUpperCase();
								goalSnapshot[length].saved = "0";
								goalSnapshot[length].contributions = "0";
							}
                        }
                        addGoalV.render('#newGoals',goal);
                        init();
                        updateCollapse = false;
                        $("#" + key + "FAQArrow").click();
                        updateCollapse = true;
                    }
                    );
                $("#existingHeader").show();  
            },
            deleteGoal: function(event){
                event.preventDefault();
               
                var name = event.target.id;
                var index = name.indexOf("DeleteGoalButton");
                var idVal = name.substring(0, index);
                var key = name.substring(index + 16,name.length); 
                $("#" + key + "AddGoal").removeClass("active");
                updateCollapse = false;
                $("#" + idVal + key + "FAQArrow").click();
                updateCollapse = true;
                $("#" + idVal + key + "Loading").show();
                $("#" + idVal + key + "ProfileDataBox").removeClass("profileDatabox");

                var formValues = {
                    id : idVal,
                    action:'DELETE'
                };
                $.ajax({
                    url:userGoalAddUpdateURL,
                    dataType: "json",
                    data: formValues,
                    type:'POST',
                    success:function (jsonData) {
                        timeoutPeriod = defaultTimeoutPeriod;
                    } 
                });
                var i = 0;
                var j = 0;
                for(i=0;i<financialData.goals.length;i++)
                {
                     if(financialData.goals[i]["id"] == idVal)
                     {
                        if(financialData.goals[i]["goaltype"] == 'DEBT') {
                            $("#debtAddGoal").show();
                        }
                        financialData.goals[i]["goalstatus"] = 0;
                        $("#" + idVal + key + "ProfileDataBox").hide();
                        $("#" + idVal + key + "ProfileVSpace").hide();
                    }                    
                } 
                for(var i=0;i<goalSnapshot.length; i++) {
					if(goalSnapshot[i].id == idVal) {
						goalSnapshot[i].goalstatus = 0;
					}				    
				}       
	            RecalculateGoalAmounts(false);                
	            SetCalculatedGoalAmounts();				         
            },
            removeNewGoal: function(event){
                event.preventDefault(); 
                $("#newGoals").html('');
                var type = event.target.id.substring(0, event.target.id.indexOf('RemoveNewGoalButton'));
                $("#" + type + "AddGoal").removeClass("active");

                for(var i=0;i<goalSnapshot.length; i++) {
					if(goalSnapshot[i].id == "") {
						goalSnapshot[i].goalstatus = 0;
					}				    
				}       
	            RecalculateGoalAmounts(false);
	            SetCalculatedGoalAmounts();				         
            }
        });
        return new goalsView;
    });
