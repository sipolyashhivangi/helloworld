// Filename: views/login/login
define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/mfaquestion.html',
], function(Handlebars, Backbone, mfaQuestionTemplate) {

    var accountstatusView = Backbone.View.extend({
        el: $("#body"),
        render: function(obj) {
            var source = $(mfaQuestionTemplate).html();
            var template = Handlebars.compile(source);
            if (typeof(obj.id) == 'undefined')
                obj.id = "";
            var i = 0;
            for (i = 0; i < obj.mfadetails.length; i++)
            {
                obj.mfadetails[i].id = obj.id;
            }
            $("#" + obj.id + "connectDesc").html(template(obj));
        },
        events: {
            "click .btnAddItemToMFA": "fnSubmitMFA",
            "click .btnRefreshMFA": "fnRefreshMFA",
            "click .deleteMFA": "fnDeleteMFA",
            "keypress .mfafields": "fnCheckMFAFields",
        },
        fnCheckMFAFields: function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                event.preventDefault();
                $("#" + event.target.id).parents("form").find(".lsPillBtnGreen").click();
            }
        },
        fnRefreshMFA: function(event) {
            //send MFA
            var name = event.target.id;
            var id = name.substr(0, name.indexOf('btnRefreshMFA'));
            $('#' + name).attr("disabled", "true");

            var parameters = $('#' + id + 'parameters').val();
            var flag = 303;
            var paramArr = parameters.split("#");
            var length = paramArr.length;
            var jsonObj = []; //declare object

            for (var i = 0; i < length; i++) {
                param = paramArr[i];
                if (param != "") {
                    //get the values from the field
                    var ans = $('#' + id + param.replace(/[.]/g,'\\.').replace(/[$]/g,'\\$')).val().replace(/&/g,'&amp;');
                    var cryptType = $('#' + id + 'cryptType' + param.replace(/[.]/g,'\\.').replace(/[$]/g,'\\$')).val();
                    //send to server
                    jsonObj.push({
                        ParamName: param,
                        CryptType: cryptType,
                        CryptVal: ans
                    });
                }
            }
            cid = $('#' + id + 'cid').val();
            var title = $('#' + id + 'connectTitle').html();
            
            require(
                ['views/profile/accountstatus'],
                function(accountstatusV) {
                   // Added to hide the existing div and show a message div
                    accountstatusV.render({"id": cid, "status": 'We are checking the answer to your security question(s). Please wait.', "title": title, "oldaccount":true });
               }
            );
            //var key = id;
            var formValues = {
                json: jsonObj,
                cid: cid,
                flag: flag
            };
            financialData.accountsdownloading = true;
            $.ajax({
                url: refreshAllUrl,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
                    if (typeof(data.rerender) != 'undefined' && data.rerender == 1) {
                        var key = data.loginacctid;
                        var formValues = {
                            cid: key
                        };
                        $.ajax({
                            url: retryAccountUrl,
                            type: 'GET',
                            dataType: "json",
                            data: formValues,
                            cache: false,
                            beforeSend: function(request) {
                                request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                            },
                            success: function(data) {
                                timeoutPeriod = defaultTimeoutPeriod;
                                CheckCashedgeResponse(data, key);
                            }
                        });
                    } else {
                        timeoutPeriod = defaultTimeoutPeriod;
                        var key = data.loginacctid;
                        CheckCashedgeResponse(data, key);
                    }
                },
                error: function(data) {
                    $('#' + name).removeAttr("disabled");
                }
            });
        },
        fnSubmitMFA: function(event) {
            //send MFA
            var name = event.target.id;
            var id = name.substr(0, name.indexOf('btnAddItemToMFA'));
            $('#' + name).attr("disabled", "true");

            var parameters = $('#' + id + 'parameters').val();
            var flag = $('#' + id + 'flag').val();
            var paramArr = parameters.split("#");
            var length = paramArr.length;
            var jsonObj = []; //declare object

            for (var i = 0; i < length; i++) {
                param = paramArr[i];
                if (param != "") {
                    //get the values from the field
                    var ans = $('#' + id + param.replace(/[.]/g,'\\.').replace(/[$]/g,'\\$')).val().replace(/&/g,'&amp;');
                    var cryptType = $('#' + id + 'cryptType' + param.replace(/[.]/g,'\\.').replace(/[$]/g,'\\$')).val();
                    //send to server
                    jsonObj.push({
                        ParamName: param,
                        CryptType: cryptType,
                        CryptVal: ans
                    });
                }
            }
            cid = $('#' + id + 'cid').val();
            var title = $('#' + id + 'connectTitle').html();
            
            require(
                ['views/profile/accountstatus'],
                function(accountstatusV) {
                   // Added to hide the existing div and show a message div
                    accountstatusV.render({"id": cid, "status": 'We are checking the answer to your security question(s). Please wait.', "title": title, "oldaccount":true });
               }
            );
            
            //var key = id;
            var formValues = {
                json: jsonObj,
                cid: cid,
                flag: flag
            };
            financialData.accountsdownloading = true;
            $.ajax({
                url: mfaSendLSURL,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
                    if (typeof(data.rerender) != 'undefined' && data.rerender == 1) {
                        var key = data.loginacctid;
                        var formValues = {
                            cid: key
                        };
                        $.ajax({
                            url: retryAccountUrl,
                            type: 'GET',
                            dataType: "json",
                            data: formValues,
                            cache: false,
                            beforeSend: function(request) {
                                request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                            },
                            success: function(data) {
                                timeoutPeriod = defaultTimeoutPeriod;
                                CheckCashedgeResponse(data, key);
                            }
                        });
                    } else if((typeof(data.ismfa) != 'undefined' && data.ismfa == 1)){
                        key = data.cid;
                        CheckCashedgeResponse(data, key);
                    } else if(data.info == 2) {
                        var key = data.cid;
                        CheckCashedgeResponse(data, key);
                    } else {
                        timeoutPeriod = defaultTimeoutPeriod;
                        var key = data.loginacctid;
                        CheckCashedgeResponse(data, key);
                    }
                },
                error: function(data) {
                    $('#' + name).removeAttr("disabled");
                }
            });
        },
        fnDeleteMFA: function(event) {
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("DeleteMFA"));
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
    return new accountstatusView;
});
