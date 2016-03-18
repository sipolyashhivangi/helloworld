// Filename: views/login/login
define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/connectresult.html',
    ], function(Handlebars, Backbone,searchresultsTemplate) {
    
        var searchResultsView = Backbone.View.extend({
            el: $("#body"),
            render: function(formValues){
                $.ajax({
                    url:userSearchFiURL,
                    type:'GET',
                    dataType:"json",
                    data: formValues,
                    cache: false,
                    beforeSend: function(request) {
                        request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                    },
                    success:function (jsonResponse) {
						timeoutPeriod = defaultTimeoutPeriod;
                        var itemsLeft = [];
                        var itemsRight = [];
                        var i=0;
                        var j=0;
                        var middle = Math.round(jsonResponse.totalRecords / 2);
                        for (var attrname in jsonResponse.items) 
                        { 
                            if(i < middle)
                            {
                                itemsLeft[i] = jsonResponse.items[attrname]; 
                            }
                            else
                            {
                                itemsRight[j] = jsonResponse.items[attrname]; 
                                j++;
                            }
                            i++;
                            if(i>=50)
                            {
                                break;
                            }
                        }
                        var responseObj = new Object();
                        responseObj.itemsLeft = itemsLeft;
                        responseObj.itemsRight = itemsRight;
                        responseObj.totalRecords = itemsLeft.length + itemsRight.length;

                        var source   = $(searchresultsTemplate).html();
                        var template = Handlebars.compile(source);
                        $("#searchAccounts").html(template(responseObj));
                        //$('#searchButton').removeAttr("disabled");
                        
                        currentSearchLeft = itemsLeft;
                        currentSearchRight = itemsRight;
                    } 
                });                
            },
            events: {
                "click .addBtn":"fnShowParam"
            },
            fnShowParam: function (event){
                event.preventDefault();
                var serviceId = event.target.id;
                var requiredItem = 0;
                require(
                    [ 'views/profile/accountsignin'],
                    function( accountsigninV ){
                        for (var attrname in currentSearchLeft) 
                        {
                            if(currentSearchLeft[attrname]['serviceId'] == serviceId)
                            {
                                requiredItem = currentSearchLeft[attrname];
                                break;
                            }
                        }
                        for (var attrname in currentSearchRight) 
                        {
                            if(currentSearchRight[attrname]['serviceId'] == serviceId)
                            {
                                requiredItem = currentSearchRight[attrname];
                                break;
                            }
                        }
                        accountsigninV.render(requiredItem);
                    }
                );

                $('#showfrm'+serviceId).show();
                $('.hideItem').hide();

				var formValues = { fiid: serviceId };
                $.ajax({
                    url:updateCashEdgeFIPriorityURL,
                    type:'GET',
                    dataType:"json",
                    data: formValues,
                    cache: false,
                    beforeSend: function(request) {
                        request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                    },
                    success:function (jsonResponse) {}
                });
            }
        });
        return new searchResultsView;
    });