define([
    'handlebars',
    'text!../../../html/user/break.html',
], function(Handlebars, breakTemplate) {
    var breakdata = "";
    var breakname = "";
    var currentmode = "";
    var breakView = Backbone.View.extend({
        el: $("#body"),
        render: function(obj, leaveOn) {
            //get the details from the getuseritem

            var source = $(breakTemplate).html();
            var template = Handlebars.compile(source);
            var goalcount = 0;
            for (var i = 0; i < obj.goals.length; i++)
            {
                if (obj.goals[i].goalstatus == 1)
                {
                    goalcount++;
                    obj.goals[i].goalcount = goalcount;
                }
                else
                {
                    obj.goals[i].goalcount = 0;
                }
            }
            //$('#simulateDataDropDown').val('');
            obj.goalscount = goalcount;
            breakdata = obj.breakdowndata;
            breakname = obj.breakname;
            
            //Fix Negative Dollar Amounts - only for showing purpose //
            if (typeof (obj.assetsTotal) != 'undefined') {
                if (parseFloat(obj.assetsTotal.replace(/,/g, '')) < 0) {
                    obj.assetsTotalForShow = '-$' + (commaSeparateNumber(obj.assetsTotal, 0).replace("-", ""));
                } else {
                    obj.assetsTotalForShow = '$' + commaSeparateNumber(obj.assetsTotal, 0);
                }
            }

            if (typeof (obj.savingsTotal) != 'undefined') {
                if (parseFloat(obj.savingsTotal.replace(/,/g, '')) < 0) {
                    obj.savingsTotalForShow = '-$' + (commaSeparateNumber(obj.savingsTotal, 0).replace("-", ""));
                } else {
                    obj.savingsTotalForShow = '$' + commaSeparateNumber(obj.savingsTotal, 0);
                }
            }

            if (typeof (obj.livingCosts) != 'undefined') {
                if (parseFloat(obj.livingCosts.replace(/,/g, '')) < 0) {
                    obj.livingCostsForShow = '-$' + (commaSeparateNumber(obj.livingCosts, 0).replace("-", ""));
                } else {
                    obj.livingCostsForShow = '$' + commaSeparateNumber(obj.livingCosts, 0);
                }
            }

            if (typeof (obj.debtsTotal) != 'undefined') {
                if (parseFloat(obj.debtsTotal.replace(/,/g, '')) < 0) {
                    obj.debtsTotalForShow = '-$' + (commaSeparateNumber(obj.debtsTotal, 0).replace("-", ""));
                } else {
                    obj.debtsTotalForShow = '$' + commaSeparateNumber(obj.debtsTotal, 0);
                }
            }

            if(typeof(userData.advisor) != 'undefined') {
                userData.user.impersonationMode = true;
                if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                    obj.permission = true;
                }
            }

            $('.BreakdownContent').html(template(obj));
            init();
            // Initializing and reseting break down tab
            initBreakSliders();
            if (typeof(leaveOn) != 'undefined' && leaveOn)
            {
                resetAndUpdateSliders();
            }
            else{
                $(".breakToggleOnLabel").click();
                var simScore = parseInt(obj.totalscore);
                breakscore = simScore;
                var imageId = Math.round((simScore * 20) / 1000);
                imageId = (imageId > 0) ? imageId : 0;
                imageId = (imageId < 20) ? imageId : 20;
                alignScore(simScore, imageId);
            }

            if (typeof (breakname) != 'undefined') {
                $("#simulateDataDropDown option").each(function() {
                    if ($(this).html() == breakname) {
                        $(this).attr("selected", "selected");
                        return;
                    }
                });
            }
 
            if(typeof(breakdata) != 'undefined' && breakdata != null && breakdata.length > 0) {
                $(".loadTextBox").show();
                $(".deleteTextBox").show();
                $(".updateTextBox").show();
           }       
 
         }, events: {
            "click .openTextBox": "fnopenBreakNameTextBox",
            "click .loadTextBox": "fnloadBreakNameTextBox",
            "click .updateTextBox": "fnupdateBreakNameTextBox",
            "click .deleteTextBox": "fndeleteBreakNameTextBox",
            "click .saveBreakButton": "fnsaveBreakButton",
            "click .cancelSave": "fncancelBreakSaveButton",
            "change #simulateDataDropDown": "fnloadBreakData",
        },
        fnloadBreakData: function(event) {
            var id = $("#simulateDataDropDown").val();
            if(currentmode == "load") {
                if (id != "") {
                    $.each(breakdata, function(key, value) {
                        if (value.id == id) {
                            userBreakSliders(value);
                        }
                        // here `value` refers to the objects 
                    });
                } else {
                    resetBreakSliders();
                }
            }
            else if(currentmode == "update") {
                updateBreakdownSliders();
            }
            else if(currentmode == "delete") {
                deleteBreakdownSliders();
            }
            $(".openTextBox").show();
            $("#resetBreakBtn").show();
            $(".loadTextBox").show();
            $(".updateTextBox").show();
            $(".deleteTextBox").show();
            $(".saveBreakButton").hide();
            $(".cancelSave").hide();
            $("#breakName").hide();
            $("#simDD").hide();
        },
        fncancelBreakSaveButton: function(event) {
            $(".openTextBox").show();
            $("#resetBreakBtn").show();
            $(".saveBreakButton").hide();
            $(".cancelSave").hide();
            $("#breakName").hide();
            $("#simDD").hide();
            $("#clientdiv").removeClass('error');
            if(typeof(breakdata) != 'undefined' && breakdata != null && breakdata.length > 0) {
                $(".loadTextBox").show();
                $(".deleteTextBox").show();
                $(".updateTextBox").show();
            }            
        },
        fnopenBreakNameTextBox: function(event) {
            $(".openTextBox").hide();
            $(".loadTextBox").hide();
            $(".updateTextBox").hide();
            $(".deleteTextBox").hide();
            $("#resetBreakBtn").hide();
            $(".saveBreakButton").show();
            $(".cancelSave").show();
            $("#breakName").show();
        },
        fnloadBreakNameTextBox: function(event) {
            $(".openTextBox").hide();
            $(".loadTextBox").hide();
            $(".updateTextBox").hide();
            $(".deleteTextBox").hide();
            $("#defaultSim").show();
            currentmode = "load";
            $("#simulateDataDropDown").prop("selectedIndex", 0)
            $("#resetBreakBtn").hide();
            $(".cancelSave").show();
            $("#simDD").show();
        },
        fndeleteBreakNameTextBox: function(event) {
            $(".openTextBox").hide();
            $(".loadTextBox").hide();
            $(".updateTextBox").hide();
            $(".deleteTextBox").hide();
            $("#defaultSim").hide();
            currentmode = "delete";
            $("#simulateDataDropDown").prop("selectedIndex", 0)
            $("#resetBreakBtn").hide();
            $(".cancelSave").show();
            $("#simDD").show();
        },
        fnupdateBreakNameTextBox: function(event) {
            $(".openTextBox").hide();
            $(".loadTextBox").hide();
            $(".updateTextBox").hide();
            $(".deleteTextBox").hide();
            currentmode = "update";
            $("#resetBreakBtn").hide();
            $("#defaultSim").hide();
            $("#simulateDataDropDown").prop("selectedIndex", 0)
            $(".cancelSave").show();
            $("#simDD").show();
        },
        fnsaveBreakButton: function(event) {
            var breakname = $('#breakName').val();
            if (breakname == "")
            {
                $('#clienterror').html('Enter name to save scores.');
                $('#clientbubble').removeClass("hdn");
                $("#clientdiv").addClass('error');
                PositionErrorMessage("#breakName", "#clientbubble");
                return false;
            } else {
                $("#clientdiv").removeClass('error');
            }
            saveBreakdownSliders();
        }
    });
    return new breakView;
});