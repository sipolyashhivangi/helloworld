    define([
        'handlebars',
        'backbone',
        'text!../../../html/profile/accountsignin.html',
        'views/profile/mfaquestion',
        'views/profile/accountstatus',
        'views/profile/connectsearch'
    ], function(Handlebars, Backbone, accountsigninTemplate, mfaquestionView, accountstatusV, connectsearchView) {
        var accountsigninView = Backbone.View.extend({
            el: $("#body"),
            render: function(item) {
                var source = $(accountsigninTemplate).html();
                var template = Handlebars.compile(source);
                if (typeof(item.id) == 'undefined')
                    item.id = "";
                if(typeof(item.URL.ParamVal) != 'undefined') {
                    $("#" + item.id + "currentFIURL").html("<a href='" + item.URL.ParamVal + "' target='_blank'>" + item.URL.ParamVal + "</a>");
                    $("#" + item.id + "currentFIURL").show();
                }

                if(typeof(item.loginParams) != 'undefined') {
    	            for(var i = 0; i < item.loginParams.length; i++) {
    	            	item.loginParams[i].id = item.id;
    	            }
    	        }

                $("#" + item.id + "connectDesc").html(template(item));
                $("#" + item.id + "connectTitle").html('Connect to ' + item.displayName);
            },
            events: {
                "click .additemBtn": "performAddItem",
                "click .deleteItemBtn": "deleteAddItem",
                //  "click .addBtn":"fnShowParam",
                "click .btnCancelItem": "fnHideParam",
                "click .btnBackItem": "fnReloadResults",
                "keypress .fields": "fnCheckFields",
            },
            fnCheckFields: function(event) {
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if (keycode == '13') {
                    event.preventDefault();
                    $("#" + event.target.id).parents("form").find("button").click();
                }
            },
            fnHideParam: function(event) { 
                event.preventDefault();
                id = tempid;
                $("#connectTitle").parents('.profileDataEntry').attr('id', id + "profileAssetsStatus");
                $("#connectTitle").attr('id', id + "connectTitle");
                $('#downloadMessage').attr('id', id + "downloadMessage");
                $('#pContent').attr('id', id + "pContent");
                $('#btnCancelItem').attr('id', id + "btnCancelItem");
                $('#additemBtn').attr('id', id + "additemBtn");
                $('#' + id + 'profileAssetsStatus').hide();
                tempid++;
                connectsearchView.render(GetPopularAccounts());
                $("#connectSearchDiv").show();
                init();
            },
            fnReloadResults: function(event) { 
                event.preventDefault();
                id = tempid;
                $("#connectTitle").parents('.profileDataEntry').attr('id', id + "profileAssetsStatus");
                $("#connectTitle").attr('id', id + "connectTitle");
                $('#downloadMessage').attr('id', id + "downloadMessage");
                $('#pContent').attr('id', id + "pContent");
                $('#btnCancelItem').attr('id', id + "btnCancelItem");
                $('#additemBtn').attr('id', id + "additemBtn");
                $('#' + id + 'profileAssetsStatus').hide();
                tempid++;
                connectsearchView.render(GetPopularAccounts());
                $("#connectSearchDiv").show();
                init();
                var formValues = {
                    timezone: "US",
                    finame: previousSearchFI
                };
            
                require(
                    ['views/profile/connectresult'],
                    function(connectresultV) {
                        connectresultV.render(formValues);
                        $("#popularAccounts").hide();
                        $("#searchAccounts").show();
                    }
                );
            },
            /*
             fnShowParam: function (event){
             var serviceId = event.target.id;
             var id = name.substr(0,name.indexOf('addBtn'));
             $('#' + id + 'showfrm'+serviceId).show();
             $('.hideItem').hide();
             },
             */
            performAddItem: function(event) {
                var serviceId = event.target.value;
                var name = event.target.id;
                var id = name.substr(0, name.indexOf('additemBtn'));
                $('#' + name).hide();
                $('#' + id + 'addhrsignin').hide();
                event.preventDefault();
                //get the values for this form
                var finame = $('#' + id + 'finame' + serviceId).val();
                var values = $('#' + id + 'item-form' + serviceId).serialize();
                values = values.replace(/%26/g,'%26amp;');
                values += "&serviceid=" + serviceId;
                values += "&finame=" + encodeURIComponent(finame);

                // Added to hide the existing div and show a message div
                var title = "Connecting to " + finame;
                $('#' + id + 'connectTitle').html(title);
                $('#' + id + 'downloadMessage').html('We are trying to set up a connection with the Financial Institution. If other security questions need answering, we’ll let you know.');
                $('#' + id + 'pContent').hide();
                if (id == '')
                {
                    id = tempid;
                    $("#connectTitle").parents('.profileDataEntry').attr('id', id + "profileAssetsStatus");
                    $("#" + id + "profileAssetsStatus").html('');
                    tempid++;
                    connectsearchView.render(GetPopularAccounts());
                    $("#connectSearchDiv").show();
                    init();
                }

                financialData.accountsdownloading = true;
                $.ajax({
                    url: userAddFiURL,
                    type: 'POST',
                    dataType: "json",
                    data: values,
                    success: function(jsonResponse) {
                        timeoutPeriod = defaultTimeoutPeriod;
                        if (jsonResponse.status == "OK") {
                            if(typeof(jsonResponse.deletedItems) != 'undefined') {
                                for(var i=0; i<jsonResponse.deletedItems.length; i++) {
                                    $("#" + jsonResponse.deletedItems[i] + 'profileAssetsStatus').hide();
                                    var j = 0;
                                    for (j = 0; j < financialData.harvesting.length; j++)
                                    {
                                       if(financialData.harvesting[j].id == key) {
                                            financialData.harvesting[j].status = 1;
                                        }
                                    }                    
                                }
                            }
                            // Added to hide the existing div and show a message div
                            var str = "Connecting To ";
                            $("#" + id + "currentFIURL").hide();
                            accountstatusV.render({"id": jsonResponse.loginacctid, "status": jsonResponse.message, "title": title.substr(str.length)});
                            if (id == "") {
                                $("#connectSearchDiv").show();
                            } else {
                                $("#" + id + "profileAssetsStatus").hide();
                                $("#connectDesc").show();
                            }

                            // Getting the FILoginAcctID
                            // var value2 = "&cid=" + jsonResponse.loginacctid;
                            var fid = jsonResponse.loginacctid;
                            var key = jsonResponse.loginacctid;
                            var flag = jsonResponse.flag;
                            values += "&cid=" + fid;
                            values += "&flag=" + flag;
                            // AJAX CALL1 - START
                            $.ajax({
                                url: accountAddContinueURL,
                                type: 'POST',
                                dataType: "json",
                                data: values,
                                success: function(addresponse) {
                                    timeoutPeriod = defaultTimeoutPeriod;
                                    if (typeof(addresponse) == 'undefined') {
                                        $("#" + key + "connectDesc").children('.profileEmph').html('Please contact FlexScore support team.');
                                    } else {
                                        CheckCashedgeResponse(addresponse, key);
                                    }

                                }
                            });
                            // AJAX CALL1 - END

                            // AJAX CALL2 - START
                            $.ajax({
                                url: getNotificationDataURL + "?forceUser=" + forceUserNotifications,
                                type: 'GET',
                                dataType: "json",
                                cache: false,
                                beforeSend: function(request) {
                                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                                },
                                success: function(data) {
                                    timeoutPeriod = defaultTimeoutPeriod;
                                    if (data.total != 0) {
                                        $('#headNotifyTags').html(data.total);
                                        $('.gnavNotifications').html(data.total);
                                    }
                                }
                            });
							 
                            // AJAX CALL2 - END
                        }
                    }
                });
            },
            deleteAddItem: function(event) {
                var name = event.target.id;
                var key = name.substring(0, name.indexOf("DeleteItemBtn"));
                var formValues = {
                    cid: key
                };

                $.ajax({
                    url: deleteAccountUrl,
                    type: 'GET',
                    dataType: "json",
                    data: formValues,
                    cache: false,
                    beforeSend: function(request) {
                        request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                    },
                    success: function(data) {
                        timeoutPeriod = defaultTimeoutPeriod;
                    }
                });
                var i = 0;
                for (i = 0; i < financialData.harvesting.length; i++)
                {
                    if(financialData.harvesting[i].id == key) {
                        financialData.harvesting[i].status = 1;
                    }
                }    
                $("#" + key + 'profileAssetsStatus').hide();                
            }            
        });
        return new accountsigninView;
    });