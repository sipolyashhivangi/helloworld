define([
    'jquery',
    'handlebars',
    'backbone',
    'text!../../../html/profile/accounts.html',
], function($, Handlebars, Backbone, accountsTemplate) {

    var accountsView = Backbone.View.extend({
        el: $("#body"),
        render: function(key) {
            var source = $(accountsTemplate).html();
            var template = Handlebars.compile(source);
            keyObj = {};
            keyObj[key] = key;
            if (typeof (userData.advisor) != 'undefined') {
                userData.user.impersonationMode = true;
                if (userData.permission == 'RO') {// if advisor has RO permission during impersonation.
                    keyObj.permission = true;
                }
            }
            $('#profileCol2').html(template(keyObj));

            $('#existingAccounts').html('');
            $('#newAccounts').html('');
            $('#riskHtml').html('');

            if (key == "debts" || key == "insurance") {
                var currentView = this;
                if (typeof (userPreferences.debtsPreference) == 'undefined' || userPreferences.debtsPreference == '' ||
                    typeof (userPreferences.insurancePreference) == 'undefined' || userPreferences.insurancePreference == '') {
                    $.getJSON(getUserCheckboxPreferences, function(data) {
                        user_info_data = $.map(data.userdata, function(value, index) {
                            return [value];
                        });
                        fnUpdateUserPreferences(data.userdata);
                        currentView.fnShowDebtsInsuranceForm(user_info_data, key);
                    });
                }
                else {
                    user_info_data = $.map(userPreferences, function(value, index) {
                        return [value];
                    });
                    currentView.fnShowDebtsInsuranceForm(user_info_data, key);
                }
            }

            if (key != 'assets' && key != 'debts' && key != 'insurance' && key != 'miscellaneous')
            {
                    require(
                            ['views/score/accounts/' + key],
                            function(existingAccountV) {
                                existingAccountV.render({});
                                init();
                            }
                    );
            }
            else if (key != 'miscellaneous')
            {
                // Load the array with data
                var obj = [];
                for (var attrname in financialData)
                {
                    if (key == "assets" && (attrname == "cash" || attrname == "investment" || attrname == "silent" || attrname == "other"))
                        obj = obj.concat(financialData[attrname]);
                    else if (key == attrname)
                        obj = obj.concat(financialData[attrname]);
                }

                // TODO Sort by date
                $("#existingHeader").hide();
                $(".msHeadingDivLine1").hide();
                var outerLoopCount = 0;
                var innerLoopCount = 0;
                var loadAccounts = []

                obj.sort(function (a, b) {
                    return a.priority - b.priority;
                });

                accountIndex = 1;

                if (obj.length > 0) {
                    // Load each view
                    for (var attrname in obj)
                    {
                        if (obj[attrname]["status"] == 0 || (obj[attrname]["status"] == 2 && userData.permission != 'RO'))
                        {
                            outerLoopCount++;
                            obj[attrname]["uiloadstatus"] = 0;
                            obj[attrname]["index"] = accountIndex;
                            accountIndex++;
                            var keyName = calculateKey(obj[attrname]["accttype"], key);

                            require(
                                    ['views/score/accounts/' + keyName],
                                    function(existingAccountV) {
                                        var accountKey = existingAccountV.getKey();

                                        // Views come in, in reverse order so need to reorder them
                                        for (var attr in obj)
                                        {
                                            var keyName = calculateKey(obj[attr]["accttype"], key);
                                            if ((obj[attr]["status"] == 0 || (obj[attr]["status"] == 2 && userData.permission != 'RO')) && keyName == accountKey && obj[attr]["uiloadstatus"] == 0)
                                            {
                                                loadAccounts[attr] = existingAccountV;
                                                innerLoopCount++;
                                                obj[attr]["uiloadstatus"] = 1;
                                                break;
                                            }
                                        }

                                        // When the above storage is done, we now start loading data, and adjusting all the fields
                                        if (innerLoopCount >= outerLoopCount)
                                        {
                                            for (var i in obj)
                                            {
                                                if (obj[i]["status"] == 0 || (obj[i]["status"] == 2 && userData.permission != 'RO'))
                                                {
                                                    obj[i].retired = profileUserData.retirementstatus;
                                                    loadAccounts[i].render("#existingAccounts", obj[i]);
                                                }
                                            }
                                            init();
                                            $("#existingHeader").show();
                                            $(".msHeadingDivLine1").show();
                                            $("#existingAccounts").show();
                                            $("#totalAccounts").show();
                                            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));

                                            RetoggleOnOff(key);
                                        }
                                    }
                            );
                        }
                    }
                }
                else
                {
                    var keyName = calculateKey('LIFE', key);
                    require(
                            ['views/score/accounts/' + keyName],
                            function(existingAccountV) {
                                RetoggleOnOff(key);
                            }
                    );
                }
            }
        },
        fnShowDebtsInsuranceForm: function(user_info_data, key) {
            var user_data = user_info_data;

            // for debts
            if (key == "debts") {
                if (user_data[6] != '1') {
                    if (user_data[2] == '1') {
                        $('#debtsPreference').prop('checked', true);
                        $('#debtBox').hide();
                    } else {
                        $('#debtsPreference').prop('checked', false);
                        $('#debtBox').show();
                    }
                } else {
                    $('#debtBox').show();
                }

                if (user_data[6] == '1') {
                    $('#accountCheckbox').hide();
                    if (user_data[2] == '1') {
                        var formValues = {
                            checkboxStatus: '0',
                            checkboxName: 'debtsPreference'
                        };

                        $.ajax({
                            url: updateUserCheckboxPreferences,
                            type: 'POST',
                            dataType: "json",
                            data: formValues,
                            success:function (data) {
                                timeoutPeriod = defaultTimeoutPeriod;
                                userPreferences.debtsPreference = data.debtsPreference;
                                if(userPreferences.debtsPreference == '1' || userPreferences.debtAdded == '1') {
                                     userPreferences.debtData = '1';
                                }
                                else
                                {
                                     userPreferences.debtData = '0';
                                }
                            }
                        });
                    }
                } else {
                    $('#accountCheckbox').show();
                }

            }
            // for insurance
            if (key == "insurance") {

                if (user_data[7] != '1') {
                    if (user_data[3] == '1') {
                        $('#insurancePreference').prop('checked', true);
                        $('#insuranceBox').hide();
                    } else {
                        $('#insurancePreference').prop('checked', false);
                        $('#insuranceBox').show();
                    }
                } else {
                    $('#insuranceBox').show();
                }

                if (user_data[7] == '1') {
                    $('#accountCheckbox').hide();
                    if (user_data[3] == '1') {
                        var formValues = {
                            checkboxStatus: '0',
                            checkboxName: 'insurancePreference'
                        };
                        var currentView = this;
                        $.ajax({
                            url: updateUserCheckboxPreferences,
                            type: 'POST',
                            dataType: "json",
                            data: formValues,
                            success:function (data) {
                                timeoutPeriod = defaultTimeoutPeriod;
                                userPreferences.insurancePreference = data.insurancePreference;
                                if(userPreferences.insurancePreference == '1' || userPreferences.insuranceAdded == '1') {
                                     userPreferences.insuranceData = '1';
                                }
                                else
                                {
                                     userPreferences.insuranceData = '0';
                                }

                            }
                        });
                    }
                } else {
                    $('#accountCheckbox').show();
                }
            }
        },
        events: {
            "click .addAccountButton": "addAccount",
            "click .deleteAccountButton": "deleteAccount",
            "click .permissionToggleButton": "toggleAccount",
            "click .removeNewAccountButton": "removeNewAccount",
            "click #debtsPreference": "fndebtsPreference",
            "click #insurancePreference": "fninsurancePreference"

        },
        fndebtsPreference: function(event) {

            if ($('#debtsPreference').is(':checked')) {
                var formValues = {
                    checkboxStatus: '1',
                    checkboxName: 'debtsPreference'
                };
                //$('#debtBox').hide();
            } else {
                var formValues = {
                    checkboxStatus: '0',
                    checkboxName: 'debtsPreference'
                };
                // $('#debtBox').show();
            }
            $.ajax({
                url: updateUserCheckboxPreferences,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    var source = $(accountsTemplate).html();
                    var template = Handlebars.compile(source);
                    keyObj.debtstatus = data.debtsPreference;
                    if (keyObj.debtstatus == 0) {
                        $('#profileCol2').html(template(keyObj));
                        $('#existingHeader').hide();
                        $('.msHeadingDivLine1').hide();
                    } else {
                        $('#debtBox').hide();
                    }
                    userPreferences.debtsPreference = data.debtsPreference;
                    if(userPreferences.debtsPreference == '1' || userPreferences.debtAdded == '1') {
                         userPreferences.debtData = '1';
                    }
                    else
                    {
                         userPreferences.debtData = '0';
                    }

                }
            });
        },
        fninsurancePreference: function(event) {
            if ($('#insurancePreference').is(':checked')) {
                var formValues = {
                    checkboxStatus: '1',
                    checkboxName: 'insurancePreference'
                };
                // $('#insuranceBox').hide();
            } else {
                var formValues = {
                    checkboxStatus: '0',
                    checkboxName: 'insurancePreference'
                };
                // $('#insuranceBox').show();
            }
            $.ajax({
                url: updateUserCheckboxPreferences,
                type: 'POST',
                dataType: "json",
                data: formValues,
                success: function(data) {
                    var source = $(accountsTemplate).html();
                    var template = Handlebars.compile(source);
                    keyObj.insurancestatus = data.insurancePreference;
                    if (keyObj.insurancestatus == 0) {
                        $('#profileCol2').html(template(keyObj));
                        $('#existingHeader').hide();
                        $('.msHeadingDivLine1').hide();
                    } else {
                        $('#insuranceBox').hide();
                    }
                    userPreferences.insurancePreference = data.insurancePreference;
                    if(userPreferences.insurancePreference == '1' || userPreferences.insuranceAdded == '1') {
                         userPreferences.insuranceData = '1';
                    }
                    else
                    {
                         userPreferences.insuranceData = '0';
                    }

                }
            });


        },
        addAccount: function(event) {
            event.preventDefault();
            $("#debtCheckTitle").hide();
            $("#insCheckTitle").hide();
            var name = event.target.id;
            var btnKey = "AddAccount";
            var key = name.substring(0, name.indexOf(btnKey));
            require(
                    ['views/score/accounts/' + key],
                    function(addAccountV) {
                        $("#newAccounts").html('');
                        var obj = {"retired": profileUserData.retirementstatus};
                        if (key == "loan")
                        {
                            var extraSpace = 0;
                            if (name.indexOf(btnKey + "Div") != -1) {
                                extraSpace = 4;
                            }
                            obj.mortgagetype = name.substring(name.indexOf(btnKey) + btnKey.length + extraSpace, name.length);
                        }
                        if (key == "brokerage" || key == "ira" || key == "companyretirementplan")
                        {
                            obj.invpos = [];
                            obj.invpos[0] = {'index': 0};
                            obj.invpos[1] = {'index': 1};
                            obj.invpos[2] = {'index': 2};
                            obj.invpos[3] = {'index': 3};
                            obj.invpos[4] = {'index': 4};
                            obj.tickercount = 5;
                        }
                        addAccountV.render('#newAccounts', obj);
                        init();
                        $("#" + key + "FAQArrow").click();
                        if ($("#" + key + "ProfileDataBox").length > 0) {
                            $.scrollTo($("#" + key + "ProfileDataBox"), {duration: 0, offsetTop: '100'});
                        }
                        RetoggleOnOff($("#fiType").val());

                    }
            );
            $("#existingHeader").show();
            $(".msHeadingDivLine1").show();
            if ($("#fiType").val() != 'miscellaneous')
                $("#totalAccounts").show();
        },
        deleteAccount: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var index = name.indexOf("DeleteAccountButton");
            var idVal = name.substring(0, index);
            var key = name.substring(index + 19, name.length);
            $("#" + key + "AddAccount").removeClass("active");
            var urlUsed = userAssetAddUpdateURL;
            if (key == 'loan' || key == 'mortgage' || key == 'creditcard')
                urlUsed = userDebtsAddUpdateURL;
            else if (key == 'umbrellainsurance' || key == 'homeinsurance' || key == 'lifeinsurance' || key == 'healthinsurance' || key == 'disabilityinsurance' || key == 'longtermcareinsurance' || key == 'vehicleinsurance')
                urlUsed = userAddUpdateInsuranceURL;

            $("#" + idVal + key + "Loading").show();
            $("#" + idVal + key + "FAQArrow").click();
            $("#" + idVal + key + "ProfileDataBox").removeClass("profileDatabox");

            var formValues = {
                id: idVal,
                action: 'DELETE'
            };
            $.ajax({
                url: urlUsed,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    if (jsonData.debtcount == '0') {
                        $('#debtCheckTitle').show();
                        $('#accountCheckbox').show();
                        userPreferences.debtAdded = '0';
                        if(userPreferences.debtsPreference == '1') {
                             userPreferences.debtData = '1';
                        }
                        else
                        {
                             userPreferences.debtData = '0';
                        }
                    }
                    if (jsonData.inscount == '0') {
                        $('#insCheckTitle').show();
                        $('#accountCheckbox').show();
                        userPreferences.insuranceAdded = '0';
                        if(userPreferences.insurancePreference == '1') {
                             userPreferences.insuranceData = '1';
                        }
                        else
                        {
                             userPreferences.insuranceData = '0';
                        }
                    }
                }
            });

            var i = 0;
            var j = 0;
            switch (urlUsed)
            {
                case userAssetAddUpdateURL:
                    for (i = 0; i < financialData.cash.length; i++)
                    {
                        if (financialData.cash[i]["id"] == idVal && calculateKey(financialData.cash[i]["accttype"], "assets") == key)
                        {
                            financialData.cash[i]["status"] = 1;
                            $("#" + idVal + key + "ProfileDataBox").hide();
                            $("#" + idVal + key + "ProfileVSpace").hide();
                        }
                        if (financialData.cash[i]["status"] == 0 || (financialData.cash[i]["status"] == 2 && userData.permission != 'RO'))
                        {
                            j++;
                        }
                    }
                    for (i = 0; i < financialData.investment.length; i++)
                    {
                        if (financialData.investment[i]["id"] == idVal && calculateKey(financialData.investment[i]["accttype"], "assets") == key)
                        {
                            financialData.investment[i]["status"] = 1;
                            $("#" + idVal + key + "ProfileDataBox").hide();
                            $("#" + idVal + key + "ProfileVSpace").hide();
                        }
                        if (financialData.investment[i]["status"] == 0 || (financialData.investment[i]["status"] == 2 && userData.permission != 'RO'))
                        {
                            j++;
                        }
                    }
                    for (i = 0; i < financialData.other.length; i++)
                    {
                        if (financialData.other[i]["id"] == idVal && calculateKey(financialData.other[i]["accttype"], "assets") == key)
                        {
                            financialData.other[i]["status"] = 1;
                            $("#" + idVal + key + "ProfileDataBox").hide();
                            $("#" + idVal + key + "ProfileVSpace").hide();
                        }
                        if (financialData.other[i]["status"] == 0 || (financialData.other[i]["status"] == 2 && userData.permission != 'RO'))
                        {
                            j++;
                        }
                    }
                    for (i = 0; i < financialData.silent.length; i++)
                    {
                        if (financialData.silent[i]["id"] == idVal && calculateKey(financialData.silent[i]["accttype"], "assets") == key)
                        {
                            $("#" + idVal + key + "ProfileDataBox").hide();
                            $("#" + idVal + key + "ProfileVSpace").hide();
                            financialData.silent[i]["status"] = 1;
                        }
                        if (financialData.silent[i]["status"] == 0 || (financialData.silent[i]["status"] == 2 && userData.permission != 'RO'))
                        {
                            j++;
                        }
                    }
                    break;
                case userDebtsAddUpdateURL:
                    for (i = 0; i < financialData.debts.length; i++)
                    {
                        if (financialData.debts[i]["id"] == idVal && calculateKey(financialData.debts[i]["accttype"], "debts") == key)
                        {
                            financialData.debts[i]["status"] = 1;
                            $("#" + idVal + key + "ProfileDataBox").hide();
                            $("#" + idVal + key + "ProfileVSpace").hide();
                        }
                        if (financialData.debts[i]["status"] == 0 || (financialData.debts[i]["status"] == 2 && userData.permission != 'RO'))
                        {
                            j++;
                        }
                    }
                    break;
                case userAddUpdateInsuranceURL:
                    for (i = 0; i < financialData.insurance.length; i++)
                    {
                        if (financialData.insurance[i]["id"] == idVal && calculateKey(financialData.insurance[i]["accttype"], "insurance") == key)
                        {
                            $("#" + idVal + key + "ProfileDataBox").hide();
                            $("#" + idVal + key + "ProfileVSpace").hide();
                            financialData.insurance[i]["status"] = 1;
                        }
                        if (financialData.insurance[i]["status"] == 0 || (financialData.insurance[i]["status"] == 2 && userData.permission != 'RO'))
                        {
                            j++;
                        }
                    }
                    break;
                default:
            }
            if (j == 0)
            {
                $("#existingAccounts").hide();
                if ($("#newAccounts").html() == '')
                {
                    $("#existingHeader").hide();
                    $(".msHeadingDivLine1").hide();
                    $("#totalAccounts").hide();
                }
            }
            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
        },
        toggleAccount: function(event) {
            event.preventDefault();
            if (allowToggle)
            {
                var name = event.target.id;
                var index = name.indexOf("PermissionToggleButton");
                var idVal = name.substring(0, index);
                var key = name.substring(index + 22, name.length);
                var urlUsed = userAssetAddUpdateURL;
                if (key == 'loan' || key == 'mortgage' || key == 'creditcard')
                    urlUsed = userDebtsAddUpdateURL;
                else if (key == 'umbrellainsurance' || key == 'homeinsurance' || key == 'lifeinsurance' || key == 'healthinsurance' || key == 'disabilityinsurance' || key == 'longtermcareinsurance' || key == 'vehicleinsurance')
                    urlUsed = userAddUpdateInsuranceURL;
                var currAction = 'HIDE';
                if ($("#" + idVal + "toggleOffLabel" + key).hasClass("hdn"))
                    currAction = 'UNHIDE';

                var formValues = {
                    id: idVal,
                    action: currAction,
                    url: urlUsed
                };

                // ADD TO QUEUE
                accountCurrentVariables[accountCurrentLength] = formValues;
                accountCurrentLength++;
                if (!accountAjaxInProcess && accountCurrentIntervalId == '') {
                    accountCurrentIntervalId = setInterval(runAccountCalculations, 500);
                }

                // Update UI
                if (currAction == 'HIDE')
                {
                    $('#' + idVal + key + "AmountSummary").hide();
                }
                else
                {
                    $('#' + idVal + key + "AmountSummary").show();
                }
                switch (urlUsed)
                {
                    case userAssetAddUpdateURL:
                        var i = 0;
                        for (i = 0; i < financialData.cash.length; i++)
                        {
                            if (financialData.cash[i]["id"] == idVal && calculateKey(financialData.cash[i]["accttype"], "assets") == key)
                            {
                                if (currAction == 'HIDE')
                                {
                                    financialData.cash[i]["status"] = 2;
                                }
                                else
                                {
                                    financialData.cash[i]["status"] = 0;
                                }
                            }
                        }
                        for (i = 0; i < financialData.investment.length; i++)
                        {
                            if (financialData.investment[i]["id"] == idVal && calculateKey(financialData.investment[i]["accttype"], "assets") == key)
                            {
                                if (currAction == 'HIDE')
                                {
                                    financialData.investment[i]["status"] = 2;
                                }
                                else
                                {
                                    financialData.investment[i]["status"] = 0;
                                }
                            }
                        }
                        for (i = 0; i < financialData.other.length; i++)
                        {
                            if (financialData.other[i]["id"] == idVal && calculateKey(financialData.other[i]["accttype"], "assets") == key)
                            {
                                if (currAction == 'HIDE')
                                {
                                    financialData.other[i]["status"] = 2;
                                }
                                else
                                {
                                    financialData.other[i]["status"] = 0;
                                }
                            }
                        }
                        for (i = 0; i < financialData.silent.length; i++)
                        {
                            if (financialData.silent[i]["id"] == idVal && calculateKey(financialData.silent[i]["accttype"], "assets") == key)
                            {
                                if (currAction == 'HIDE')
                                {
                                    financialData.silent[i]["status"] = 2;
                                }
                                else
                                {
                                    financialData.silent[i]["status"] = 0;
                                }
                            }
                        }
                        break;
                    case userDebtsAddUpdateURL:
                        for (i = 0; i < financialData.debts.length; i++)
                        {
                            if (financialData.debts[i]["id"] == idVal && calculateKey(financialData.debts[i]["accttype"], "debts") == key)
                            {
                                if (currAction == 'HIDE')
                                {
                                    financialData.debts[i]["status"] = 2;
                                }
                                else
                                {
                                    financialData.debts[i]["status"] = 0;
                                }
                            }
                        }
                        break;
                    case userAddUpdateInsuranceURL:
                        for (i = 0; i < financialData.insurance.length; i++)
                        {
                            if (financialData.insurance[i]["id"] == idVal && calculateKey(financialData.insurance[i]["accttype"], "insurance") == key)
                            {
                                if (currAction == 'HIDE')
                                {
                                    financialData.insurance[i]["status"] = 2;
                                }
                                else
                                {
                                    financialData.insurance[i]["status"] = 0;
                                }
                            }
                        }
                        break;
                    default:
                }
                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
            }
        },
        removeNewAccount: function(event) {
            event.preventDefault();
            $("#newAccounts").html('');
            var type = event.target.id.substring(0, event.target.id.indexOf('RemoveNewAccountButton'));
            $("#" + type + "AddAccount").removeClass("active");
            var j = 0;
            var key = $("#fiType").val();
            for (var attrname in financialData)
            {
                var i = 0;
                if (key == "assets" && (attrname == "cash" || attrname == "investment" || attrname == "silent" || attrname == "other"))
                {
                    for (i = 0; i < financialData[attrname].length; i++)
                    {
                        if (financialData[attrname]["status"] == 0 || (financialData[attrname]["status"] == 2 && userData.permission != 'RO'))
                            j++;
                    }
                }
                else if (key == attrname)
                {
                    for (i = 0; i < financialData[attrname].length; i++)
                    {
                        if (financialData[attrname]["status"] == 0 || (financialData[attrname]["status"] == 2 && userData.permission != 'RO'))
                            j++;
                    }
                }
            }
            if (j == 0)
            {
                $("#existingHeader").hide();
                $(".msHeadingDivLine1").hide();
                $("#totalAccounts").hide();
            }
        }
    });
    return new accountsView;
});
