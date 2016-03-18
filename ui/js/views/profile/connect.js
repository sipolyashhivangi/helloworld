define([
    'jquery',
    'handlebars',
    'backbone',
    'text!../../../html/profile/connect.html',
    'views/profile/connectsearch',
    'views/profile/accountstatus',
], function($, Handlebars, Backbone, searchitemTemplate, connectView, accountstatusView) {

    var searchItemView = Backbone.View.extend({
        el: $("#body"),

        render: function() {
            var source = $(searchitemTemplate).html();
            var template = Handlebars.compile(source);

            var currentView = this;
            if (typeof (userPreferences.connectAccountPreference) == 'undefined' || userPreferences.connectAccountPreference == '') {
                $.getJSON(getUserCheckboxPreferences, function(data) {
                    user_info_data = $.map(data.userdata, function(value, index) {
                        return [value];
                    });
                    fnUpdateUserPreferences(data.userdata);
                    currentView.fnShowConnectForm(user_info_data, template);
                });
            }
            else {
                user_info_data = $.map(userPreferences, function(value, index) {
                    return [value];
                });
                currentView.fnShowConnectForm(user_info_data, template);
            }
        },
        fnShowConnectForm: function(user_info_data, template) {
            var user_data = user_info_data;

            var popularAccounts = GetPopularAccounts();
            $('#profileCol2').html(template(popularAccounts));
            connectView.render(popularAccounts);

            if (user_data[1] == '1') {
                $('#connectAccountPreference').prop('checked', true);
                $('#connectAccBox').hide();
            } else {
                $('#connectAccountPreference').prop('checked', false);
                $('#connectAccBox').show();
            }

            if(userPreferences.userHasConnectedAccounts == '1' || financialData.harvesting.length > 0) {
                $('#connectAccountPreferenceDiv').hide();
                $('#connectAccBox').show();
            } else {
                $('#connectAccountPreferenceDiv').show();
            }

            var i = 0;
            for (i = 0; i < financialData.harvesting.length; i++)
            {
                if (typeof (financialData.harvesting[i].status) == 'undefined') {
                    var obj = financialData.harvesting[i];
                    var msg = "Please click one of the actions available below.";
                    if (obj.message != "")
                        msg = obj.message;
                    accountstatusView.render({"id": obj.id, "status": msg, "title": obj.name, "dontshowconnect": true});
                }
            }

            $("#connectDesc").show();

            if (currentNotificationKey != '')
            {
                $('#' + currentNotificationKey + 'profileAssetsStatus').scrollTo({top: 150, left: 250});
                if ($("#" + currentNotificationKey + "RefreshAccounts").length > 0) {
                    $("#" + currentNotificationKey + "RefreshAccounts").click();
                } else {
                    $("#" + currentNotificationKey + "RetryHarvesting").click();
                }
                $("#" + currentNotificationKey + "pendingFAQArrow").click();
                currentNotificationKey = '';
            }

            if ($("#connectedAssets").length > 0 && $("#connectedAssets").html().trim() == "")
                $("#connectedAssetsHeader").hide();
            if ($("#connectedDebts").length > 0 && $("#connectedDebts").html().trim() == "")
                $("#connectedDebtsHeader").hide();
            if ($("#connectedInsurance").length > 0 && $("#connectedInsurance").html().trim() == "")
                $("#connectedInsuranceHeader").hide();
        },
        events: {
            "click #searchButton": "performSearch",
            "click .searchPopular": "performPopularSearch",
            "click #connectAccountPreference": "fnconnectAccPref"
                    //"keyup #search":"invokefetch" //for auto fill
        },
        fnconnectAccPref: function(event) {

            if ($('#connectAccountPreference').is(':checked')) {

                var formValues = {
                    checkboxStatus: '1',
                    checkboxName: 'connectAccountPreference'
                };
                $('#connectAccBox').hide();



            } else {

                var formValues = {
                    checkboxStatus: '0',
                    checkboxName: 'connectAccountPreference'
                };
                $('#connectAccBox').show();

            }


            $.ajax({
                url: updateUserCheckboxPreferences,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
                    userPreferences.connectAccountPreference = data.connectAccountPreference;
                }
            });


        },
        performSearch: function(event) {
            event.preventDefault();
            $('#searchButton').attr("disabled", "true");
            previousSearchFI = $('#search').val();

            var formValues = {
                timezone: "US",
                finame: $('#search').val()
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
        performPopularSearch: function(event) {
            event.preventDefault();
            $('#searchButton').attr("disabled", "true");
            previousSearchFI = event.target.innerHTML;
            var formValues = {
                timezone: "US",
                finame: event.target.innerHTML
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
        invokefetch: function() {
            var formValues = {
                timezone: "US",
                keyword: $('#search').val()
            };
            $("#search").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: searchAccUrl,
                        dataType: "json",
                        data: formValues,
                        cache: false,
                        beforeSend: function(request) {
                            request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                        },
                        success: function(jsondata) {
                            timeoutPeriod = defaultTimeoutPeriod;
                            response($.map(jsondata.items, function(item) {
                                return {
                                    label: item.displayName,
                                    value: item.displayName
                                }
                            }));
                        }
                    });
                },
                minLength: 3
            });
        },
        fnRetryHarvesting: function(event) {
            require(
                    ['views/profile/accountsignin'],
                    function(addAccountV) {
                        addAccountV.fnRetryHarvesting(event);
                    }
            )
        },
        accountRemove: function(event) {
            var itemIdCid = event.target.id;
            var key = itemIdCid.substring(0, itemIdCid.indexOf("DelHarvesting"));

            var splitVal = key.split("#");
            var itemId = splitVal[0];
            var cid = splitVal[1];
            var formValues = {
                fiacctid: itemId
            };

            $.ajax({
                url: accountRemoveUrl,
                type: 'GET',
                dataType: "json",
                data: formValues,
                cache: false,
                beforeSend: function(request) {
                    request.setRequestHeader("'Cache-Control", "no-cache, no-store, max-age=0, must-revalidate");
                },
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    $("#" + cid + "Harvest").hide();
                }
            });
        }
    });
    return new searchItemView;
});