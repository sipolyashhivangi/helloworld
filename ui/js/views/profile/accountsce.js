define([
    'handlebars',
    'backbone',
    'text!../../../html/profile/accountsce.html',
], function(Handlebars, Backbone, accountsceTemplate) {

    var accountsceView = Backbone.View.extend({
        el: $("#body"),
        render: function(item) {
            var source = $(accountsceTemplate).html();
            var template = Handlebars.compile(source);
            if (typeof(item.id) == 'undefined')
                item.id = 0;
            for (var i = 0; i < item.pending.length; i++)
            {
                item.pending[i].id = item.id;
                for (var j = 0; j < item.pending[i].accountsSupported.length; j++)
                {
                    item.pending[i].accountsSupported[j].id = item.id;
                }
            }
            $("#" + item.id + "connectDesc").html(template(item));

        },
        events: {
            "click .btnAddItemToLS": "fnAddAccountsToLS",
            "click .deleteClassification": "deleteClassification"
        },
        fnAddAccountsToLS: function(event) {
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("btnAddItemToLS"));
            var total = $('#' + key + 'total').val();
            var cid = $('#' + key + 'cid').val();
            var accounts = new Array();
            var invests = new Array();
            for (i = 1; i <= total; i++) {
                var accountMeta = $("#" + key + "accountMeta" + i).val();
                var acctType = $('#' + key + 'addAccountVal' + i).val();
                var flag = $('#' + key + 'accountFlag' + i).val();

                if (flag == '0') {
                    accounts[i] = accountMeta + "|" + acctType;
                }
                if (flag == '1') {
                    invests[i] = accountMeta + "|" + acctType;
                }
            }

            var title = $('#' + key + 'connectTitle').html();
            require(
                ['views/profile/accountstatus'],
                function(accountstatusV) {
                   // Added to hide the existing div and show a message div
                    accountstatusV.render({"id": cid, "status": 'We are currently classifying your accounts for accuracy. Please wait a few minutes.', "title": title, "oldaccount":true });
               }
            );

            var formValues = {
                accounts: accounts,
                invests: invests,
                cid: cid
            };
            financialData.accountsdownloading = true;
            $.ajax({
                url: accountAddLSURL,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
					timeoutPeriod = defaultTimeoutPeriod;
                    if (data.status == "ERROR") {
                        require(
                                ['views/profile/accountstatus'],
                                function(accountstatusView) {
                                    accountstatusView.render({
                                        "id": key,
                                        "status": data.message,
                                        "title": $('#' + key + 'connectTitle').html(),
                                        "oldaccount":true
                                    });
                                }
                        );
                    } else {
                        if (data.connected)
                        {
                            for (var i = 0; i < data.connected.length; i++)
                            {
                                var header = "#connectedDebtsHeader";
                                var content = "#connectedDebts";
                                var div = "addDebts";
                                if (data.connected[i].accounttype == "Assets")
                                {
                                    header = "#connectedAssetsHeader";
                                    content = "#connectedAssets";
                                    var div = "addAssets";
                                }
                                else if (data.connected[i].accounttype == "Insurance")
                                {
                                    header = "#connectedInsuranceHeader";
                                    content = "#connectedInsurance";
                                    var div = "addInsurance";
                                }
                                $(header).show();
                                $(content).append('<div style="width:90%;padding-top:5px"><a id="' + data.connected[i].id + div + '" class="' + div + '" href="#">' + data.connected[i].name + '</a></div>');
                            }
                            $("#" + key + "profileAssetsStatus").hide();
                        } else {
                            $("#" + key + "connectDesc").html('Classification completed.');
                        }
                    }
                }
            });
        },
        deleteClassification: function(event) {
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("DeleteClassification"));
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
    return new accountsceView;
});