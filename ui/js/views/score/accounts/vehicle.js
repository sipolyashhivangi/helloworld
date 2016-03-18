// Filename: views/score/accounts/vehicle
define([
    'handlebars',
    'text!../../../../html/score/accounts/vehicle.html',
], function(Handlebars, vehicleTemplate) {
    var vehicleView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(vehicleTemplate).html();
            var template = Handlebars.compile(source);
             if(typeof(userData.advisor) != 'undefined') {
                userData.user.impersonationMode = true;
                if(userData.permission == 'RO'){// if advisor has RO permission during impersonation.
                    obj.permission = true;
                }
            }
            //Fix Negative Dollar Amounts - only for showing purpose //
            if (typeof (obj.amountSummary) != 'undefined') {
                if (parseFloat(obj.amountSummary.replace(/,/g, '')) < 0) {
                    obj.amountSummaryForShow = '-$' + (commaSeparateNumber(obj.amountSummary, 0).replace("-", ""));
                } else {
                    obj.amountSummaryForShow = '$' + commaSeparateNumber(obj.amountSummary, 0);
                }
            }
            $(element).append(template(obj));
        },
        events: {
            "click .createVehicleButton": "createVehicle",
            "click .updateVehicleButton": "updateVehicle",
            "click .cancelVehicleButton": "resetVehicle",
        },
        createVehicle: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CreateVehicle"));
            var vehiclename = $('#VehicleInputName').val().trim();
            var vehicleestvalue = $('#VehicleInputBalance').val().replace(/,/g, '');
            var vehicleapprrate = '0';
            var vehicleloan = $('input:radio[name=VehicleLoan]:checked').val();
            if (typeof(vehicleloan) == 'undefined') {
                vehicleloan = '';
            }

            if($("#vehicleCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#vehicleFAQArrow").click();
                updateCollapse = true;
            }
            $("#vehicleLoading").show();

            var formValues = {
                name: vehiclename,
                amount: vehicleestvalue,
                growthrate: vehicleapprrate,
                loan: vehicleloan,
                action: 'ADD',
                accttype: 'VEHI'
            };

            $.ajax({
                url: userAssetAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/vehicle'],
                            function(addAccountV) {
                                $("#vehicleLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Vehicle";
                                if (jsonData.asset.name != "")
                                    nameSummary = jsonData.asset.name;

                                var obj = {accttype: jsonData.asset.accttype,
                                    amount: commaSeparateNumber(jsonData.asset.amount),
                                    amountSummary: commaSeparateNumber(jsonData.asset.amount, 0),
                                    id: jsonData.asset.id,
                                    index: accountIndex,
                                    loan: jsonData.asset.loan,
                                    name: jsonData.asset.name,
                                    nameSummary: nameSummary,
                                    priority: jsonData.asset.priority,
                                    refId: jsonData.asset.refid,
                                    status: jsonData.asset.status,
                                    ticker: jsonData.asset.ticker,
                                };
                                addAccountV.render('#existingAccounts', obj);
                                init();
                                accountIndex++;
                                financialData.other[financialData.other.length] = obj;

                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();
                                RetoggleOnOff("assets");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                                $("#vehicleAddAccount").removeClass("active");
                            }
                    );
                }
            });

        },
        updateVehicle: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateVehicle"));
            var vehiclename = $('#' + key + 'VehicleInputName').val().trim();
            var vehicleestvalue = $('#' + key + 'VehicleInputBalance').val().replace(/,/g, '');

            var vehicleloan = $('input:radio[name=' + key + 'VehicleLoan]:checked').val();
            if (typeof(vehicleloan) == 'undefined') {
                vehicleloan = '';
            }
            var vehicleapprrate = '0';

            if($("#" + key + "vehicleCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "vehicleFAQArrow").click();
                updateCollapse = true;
            }
            $("#" + key + "vehicleLoading").show();

            var formValues = {
                id: key,
                name: vehiclename,
                amount: vehicleestvalue,
                growthrate: vehicleapprrate,
                loan: vehicleloan,
                action: 'UPDATE',
                accttype: 'VEHI'
            };

            $.ajax({
                url: userAssetAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
                    timeoutPeriod = defaultTimeoutPeriod;
                }
            });
                    
            $("#" + key + "vehicleLoading").hide();
            var nameSummary = "Vehicle";
            if (vehiclename != "")
            {
                nameSummary = vehiclename;
            }        
            var i = 0;
            for (i = 0; i < financialData.other.length; i++)
            {
                if (financialData.other[i].id == key)
                {
                    financialData.other[i].amount = commaSeparateNumber(vehicleestvalue);
                    financialData.other[i].amountSummary = commaSeparateNumber(vehicleestvalue, 0);
                    financialData.other[i].name = vehiclename;
                    financialData.other[i].nameSummary = nameSummary;
                    financialData.other[i].loan = vehicleloan;
                }
            }
            $('#' + key + 'VehicleInputName').val(vehiclename);
            $("#" + key + 'VehicleNameSummary').html(nameSummary);
            //Fix Negative Dollar Amounts - only for showing purpose //
            if(vehicleestvalue < 0 ){
                var vehAmountForShow = '-$' + (commaSeparateNumber(vehicleestvalue, 0).replace("-", ""));
                $("#" + key + 'vehicleAmountSummary').html(vehAmountForShow);
            }else{
                $("#" + key + 'vehicleAmountSummary').html('$' + commaSeparateNumber(vehicleestvalue, 0));
            }
            $("#" + key + 'VehicleInputBalance').val(commaSeparateNumber(vehicleestvalue));
            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
            RetoggleOnOff("assets");
            
        },
        resetVehicle: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelVehicleButton"));
            var i = 0;
            for (i = 0; i < financialData.other.length; i++)
            {
                if (financialData.other[i].id == key)
                {
                    $('#' + key + 'VehicleInputName').val(financialData.other[i].name);
                    $('#' + key + 'VehicleInputBalance').val(financialData.other[i].amount);
                    $("input:radio[name=" + key + "VehicleLoan]")[0].checked = (financialData.other[i].loan == 1);
                    $("input:radio[name=" + key + "VehicleLoan]")[1].checked = (financialData.other[i].loan === "0");
                    if($("#" + key + "vehicleCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "vehicleFAQArrow").click();
                        updateCollapse = true;
                    }
                }
            }
            RetoggleOnOff("assets");

        },
        getKey: function() {
            return "vehicle";
        }
    });
    return new vehicleView;
});