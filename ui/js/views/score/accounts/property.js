// Filename: views/score/accounts/property
define([
    'handlebars',
    'text!../../../../html/score/accounts/property.html',
], function(Handlebars, propertyTemplate) {
    var propertyView = Backbone.View.extend({
        el: $("#body"),
        render: function(element, obj) {
            var source = $(propertyTemplate).html();
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
            "click .createPropertyButton": "createProperty",
            "click .updatePropertyButton": "updateProperty",
            "click .cancelPropertyButton": "resetProperty",
        },
        createProperty: function(event) {
            event.preventDefault();
            var propname = $('#PropertyInputName').val().trim();
            var estvalue = $('#PropertyInputBalance').val().replace(/,/g, '');
            var netincome = $('#PropertyInputIncome').val().replace(/,/g, '');
            var proploan = $('input:radio[name=PropertyLoan]:checked').val();
            if (typeof(proploan) == 'undefined') {
                proploan = '';
            }
            var proplivehere = $('input:radio[name=PropertyHome]:checked').val();
            if (typeof(proplivehere) == 'undefined') {
                proplivehere = '';
            }
            var propadd = $('#PropertyInputAddress1').val();
            var propadd2 = $('#PropertyInputAddress2').val();
            var propcity = $('#PropertyInputCity').val();
            var propstate = $('#PropertyInputState').val();
            var zipcode = $('#PropertyInputZip').val();

            if($("#propertyCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#propertyFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#propertyLoading").show();

            var formValues = {
                amount: estvalue,
                name: propname,
                netincome: netincome,
                loan: proploan,
                propadd: propadd,
                propadd2: propadd2,
                propcity: propcity,
                propstate: propstate,
                zipcode: zipcode,
                livehere: proplivehere,
                action: 'ADD',
                accttype: 'PROP'
            };


            $.ajax({
                url: userAssetAddUpdateURL,
                dataType: "json",
                data: formValues,
                type: 'POST',
                success: function(jsonData) {
					timeoutPeriod = defaultTimeoutPeriod;
                    require(
                            ['views/score/accounts/property'],
                            function(addAccountV) {
                                $("#propertyLoading").hide();
                                $("#newAccounts").html('');
                                var nameSummary = "Property";
                                if (jsonData.asset.name != "")
                                    nameSummary = jsonData.asset.name;

                                var obj = {accttype: jsonData.asset.accttype,
                                    id: jsonData.asset.id,
                                    index: accountIndex,
                                    priority: jsonData.asset.priority,
                                    name: jsonData.asset.name,
                                    nameSummary: nameSummary,
                                    refId: jsonData.asset.refid,
                                    status: jsonData.asset.status,
                                    ticker: jsonData.asset.ticker,
                                    amount: commaSeparateNumber(jsonData.asset.amount),
                                    amountSummary: commaSeparateNumber(jsonData.asset.amount, 0),
                                    netincome: commaSeparateNumber(jsonData.asset.netincome),
                                    loan: jsonData.asset.loan,
                                    livehere: jsonData.asset.livehere,
                                    propadd: jsonData.asset.propadd,
                                    propadd2: jsonData.asset.propadd2,
                                    propcity: jsonData.asset.propcity,
                                    propstate: jsonData.asset.propstate,
                                    zipcode: jsonData.asset.zipcode,
                                };
                                addAccountV.render('#existingAccounts', obj);
                                init();
                                accountIndex++;
                                $("#existingHeader").show();
                                $('#existingAccounts').show();
                                $('#totalAccounts').show();

                                financialData.other[financialData.other.length] = obj;
                                RetoggleOnOff("assets");
                                $("#propertyAddAccount").removeClass("active");
                                $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
                            }
                    );
                }
            });

        },
        updateProperty: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("UpdateProperty"));
            var propname = $('#' + key + 'PropertyInputName').val().trim();
            var estvalue = $('#' + key + 'PropertyInputBalance').val().replace(/,/g, '');
            var netincome = $('#' + key + 'PropertyInputIncome').val().replace(/,/g, '');
            var proploan = $('input:radio[name=' + key + 'PropertyLoan]:checked').val();
            if (typeof(proploan) == 'undefined') {
                proploan = '';
            }
            var proplivehere = $('input:radio[name=' + key + 'PropertyHome]:checked').val();
            if (typeof(proplivehere) == 'undefined') {
                proplivehere = '';
            }
            var propadd = $('#' + key + 'PropertyInputAddress1').val();
            var propadd2 = $('#' + key + 'PropertyInputAddress2').val();
            var propcity = $('#' + key + 'PropertyInputCity').val();
            var propstate = $('#' + key + 'PropertyInputState').val();
            var zipcode = $('#' + key + 'PropertyInputZip').val();

            if($("#" + key + "propertyCollapseBox").height() > 0 && needsToClose) {
                updateCollapse = false;
                $("#" + key + "propertyFAQArrow").click();
                updateCollapse = true;                        
            }
            $("#" + key + "propertyLoading").show();

            var formValues = {
                id: key,
                amount: estvalue,
                name: propname,
                netincome: netincome,
                loan: proploan,
                propadd: propadd,
                propadd2: propadd2,
                propcity: propcity,
                propstate: propstate,
                zipcode: zipcode,
                livehere: proplivehere,
                action: 'UPDATE',
                accttype: 'PROP'
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
                    
            $("#" + key + "propertyLoading").hide();
            var nameSummary = "Property";
            if (propname != "")
                nameSummary = propname;
            var i = 0;
            for (i = 0; i < financialData.other.length; i++)
            {
                if (financialData.other[i].id == key)
                {
                    financialData.other[i].name = propname;
                    financialData.other[i].nameSummary = nameSummary;
                    financialData.other[i].amount = commaSeparateNumber(estvalue);
                    financialData.other[i].amountSummary = commaSeparateNumber(estvalue, 0);
                    financialData.other[i].netincome = commaSeparateNumber(netincome);
                    financialData.other[i].loan = proploan;
                    financialData.other[i].propadd = propadd;
                    financialData.other[i].propadd2 = propadd2;
                    financialData.other[i].propcity = propcity;
                    financialData.other[i].propstate = propstate;
                    financialData.other[i].livehere = proplivehere;
                    financialData.other[i].zipcode = zipcode;
                }
            }
            $('#' + key + 'PropertyInputName').val(propname);
            $("#" + key + 'PropertyNameSummary').html(nameSummary);
            //Fix Negative Dollar Amounts - only for showing purpose //
            if(estvalue < 0 ){
                var estAmountForShow = '-$' + (commaSeparateNumber(estvalue, 0).replace("-", ""));
                $("#" + key + 'propertyAmountSummary').html(estAmountForShow);
            }else{
                $("#" + key + 'propertyAmountSummary').html('$' + commaSeparateNumber(estvalue, 0));
            }
            $("#" + key + 'PropertyInputBalance').val(commaSeparateNumber(estvalue));
            $("#totalcalculatedvalue").html(fnGetFinancialTotal($("#fiType").val()));
            RetoggleOnOff("assets");
        },
        resetProperty: function(event) {
            event.preventDefault();
            var name = event.target.id;
            var key = name.substring(0, name.indexOf("CancelPropertyButton"));
            var i = 0;
            for (i = 0; i < financialData.other.length; i++)
            {
                if (financialData.other[i].id == key)
                {
                    $('#' + key + 'PropertyInputName').val(financialData.other[i].name);
                    $('#' + key + 'PropertyInputBalance').val(financialData.other[i].amount);
                    $('#' + key + 'PropertyInputIncome').val(financialData.other[i].netincome);
                    $('#' + key + 'PropertyInputAddress1').val(financialData.other[i].propadd);
                    $('#' + key + 'PropertyInputAddress2').val(financialData.other[i].propadd2);
                    $('#' + key + 'PropertyInputCity').val(financialData.other[i].propcity);
                    $('#' + key + 'PropertyInputState').val(financialData.other[i].propstate);
                    $('#' + key + 'PropertyInputZip').val(financialData.other[i].zipcode);
                    $("input:radio[name=" + key + "PropertyHome]")[0].checked = (financialData.other[i].livehere == 1);
                    $("input:radio[name=" + key + "PropertyHome]")[1].checked = (financialData.other[i].livehere === "0");
                    $("input:radio[name=" + key + "PropertyLoan]")[0].checked = (financialData.other[i].loan == 1);
                    $("input:radio[name=" + key + "PropertyLoan]")[1].checked = (financialData.other[i].loan === "0");
                    if($("#" + key + "propertyCollapseBox").height() > 0 && needsToClose) {
                        updateCollapse = false;
                        $("#" + key + "propertyFAQArrow").click();
                        updateCollapse = true;                        
                    }
                }
            }
            RetoggleOnOff("assets");
        },
        getKey: function() {
            return "property";
        }

    });
    return new propertyView;
});