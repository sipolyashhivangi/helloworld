define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/accountstatus.html',
], function(Handlebars, Backbone, accountstatusTemplate) {

    var accountstatusView = Backbone.View.extend({
        el: $("#body"),
        render: function(obj) {
            if (typeof(obj) == 'undefined')
                obj = {
                    "id": "",
                    "status": "We are in process of downloading your account details, please continue using the site in the meantime."
                };
            var source = $(accountstatusTemplate).html();
            var template = Handlebars.compile(source);
            if(typeof(obj.oldaccount) == 'undefined')
            {
	        $("#existingConnectDiv").prepend(template(obj));
    	        $("#existingConnectCount").val(parseInt($("#existingConnectCount").val()) + 1);
	        $('#connectDesc').hide();
            }
            else
            {
                $('#' + obj.id + 'connectDesc').html(template(obj));
            }
            $('#connectAccountPreferenceDiv').hide();
            $("#existingHeader").show();
            $("#existingConnectAccounts").show();
            financialData.accountsdownloading = true;
        },
        events: {
            "click .checkStatus": "fnRetryAccount",
            "click .deleteStatus": "fnDeleteAccount",
            "click .checkClose": "fnCheckClose"
        },
        fnCheckClose: function(event) {
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("ClosePending"));
            $("#" + key + "profileAssetsStatus").hide();
        },
        fnRetryAccount: function(event) {

            var name = event.target.id;
            var key = name.substring(0, name.indexOf("RetryHarvesting"));
            // $("#" + name).attr("disabled", true);
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
        },
        fnDeleteAccount: function(event) {
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("DeleteHarvesting"));
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