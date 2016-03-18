function toRadians(angle) {
    "use strict";
    return angle * (Math.PI / 180);
}

function drawNetWorthToolTip(points, info, dateStr, colorClass) {
    "use strict";
    //TODO: make sure to adjust fontsize with number of digits
    return '<div class="pointerBubble ' + colorClass + '" style="margin-top:-25px; margin-left: 22px;">' +
            ' <span class="pbNumber"  style="font-size:1.2em">' + commaSeparateNumber(points) + '</span><br/>' +
            ' <span class="pbInfo">' + info + '</span><br/>' +
            ' <span class="pbDate">' + dateStr + '</span>' +
            ' <div class="pbPointerBottom">&nbsp;</div>' +
            '</div>';
}


function drawAllCharts() {
    var hasData = false;
    if (financialData.cashTotal != "0" || financialData.investmentTotal != "0" || financialData.otherTotal != "0" || financialData.insuranceTotal != "0")
    {
        drawAssetsChart();
        hasData = true;
    }
    else
    {
        drawEmptyAssetChart();
    }

    if (financialData.debtTotal != "0")
    {
        drawDebtsChart();
        hasData = true;
        drawDebtsBarChart();
    }
    else
    {
        drawEmptyDebtsChart();
        drawEmptyBarChart('debtsBarChart');
    }
    if (financialData.advisor_id == "") {
        drawNetWorthBarChart();
    }

    if (hasData || financialData.silentTotal != "0")
    {
        hasData = true;
        //        $("#manageProfile").show();
    }
    else
    {
        //        $("#manageProfile").hide();
    }
}

// TODO: Fix for skipping of tooltip
function drawEmptyAssetChart() {
    if (!$("#assetsPieInfo").hasClass('hdn')) {
        $("#assetsPieInfo").show();
        $("#assetsChartTotal").hide();
    }
    else
    {
        $("#assetsChartTotal").show();
        $("#cumulativeAssetsTotal").html("$0");
    }
    $("#editAssets").hide();
    var data = google.visualization.arrayToDataTable([
        [' ', 'Hours per Day'],
        [' ', 10],
        [' ', 5],
        [' ', 2.5],
        [' ', 2.5]
    ]);
    var options = {
        chartArea: {
            width: "80%",
            height: "80%"
        },
        colors: ['#eeeeee', '#dddddd', '#cccccc', '#bbbbbb'],
        hAxis: {
            viewWindowMode: 'maximized'
        },
        tooltip: {
            isHtml: true
        },
        pointSize: 8,
        lineWidth: 3,
        legend: {
            position: 'none'
        },
        height: 290,
        width: 300,
        vAxis: {
            minValue: 0,
            maxValue: 50,
            textStyle: {
                color: '#898989'
            }
        },
        hAxis: {
            textStyle: {
                color: '#898989'
            }
        },
        pieSliceText: 'none',
        backgroundColor: '#F5F4F2',
        tooltip: {
            trigger: 'none'
        }
    };
    var chart = new google.visualization.PieChart(document.getElementById('assetsPieChart'));
    chart.draw(data, options);
}

function drawAssetsChart() {
    $("#assetTooltip").show();
    $("#debtTooltip").hide();
    $("#editAssets").show();
    $("#assetsChartTotal").show();
    $("#assetsPieInfo").hide();
    "use strict";
    var lastAddedElement = null,
            i = 0,
            cumulativeTotals = [],
            totalValue = 0,
            dataTable = new google.visualization.DataTable();

    var dataArray = financialData.cash.concat(financialData.investment, financialData.other, financialData.insurance);
    var cssClasses = ['pbYellow', 'pbOrange', 'pbBlue', 'pbTurquoise', 'pbPink', 'pbRainforest', 'pbPurple', 'pbRed'];
    var rows = [];
    var tempTotal = 0;
    var totalAmt = 0;
    var k = 0;
    for (i = 0; i < dataArray.length; i++)
    {
        if (dataArray[i].status == 0)
        {
            if (dataArray[i].amount == "" || typeof (dataArray[i].amount) == 'undefined')
            {
                rows[k] = {
                    'name': dataArray[i].nameSummary,
                    'value': 0,
                    'sign': ''
                };
            }
            else
            {
                rows[k] = {
                    'name': dataArray[i].nameSummary,
                    'value': Math.abs(parseFloat(dataArray[i].amount.replace(/,/g, ''))),
                    'sign': (parseInt(dataArray[i].amount) < 0) ? "-" : ""
                };
            }
            k++;
            totalAmt = parseFloat(totalAmt) + parseFloat(dataArray[i].amount.replace(/,/g, ''));
        }

    }

    rows.sort(function(a, b) {
        return (b.value - a.value);
    });
    for (var i = 0; i < rows.length; i++)
    {
        rows[i]['cssClass'] = cssClasses[i % 8];
    }

    dataTable.addColumn('string', 'AssetName');
    dataTable.addColumn('number', 'AssetPercentage');

    for (i = 0; i < rows.length; i++) {
        totalValue = (totalValue + rows[i].value);
        dataTable.addRow([rows[i].name + ': $' + rows[i].value, rows[i].value]);
        cumulativeTotals.push(totalValue);
    }
    var sign = (totalAmt < 0) ? "-" : "";
    $("#cumulativeAssetsTotal").html(sign + "$" + commaSeparateNumber(Math.abs(totalAmt), 0));
    var options = {
        chartArea: {
            width: "80%",
            height: "80%"
        },
        colors: ['#ffc324', 'f36639', '#00669a', '#00b1b8', '#ff8da6', '#00605e', '6c68aa', '9a180d'],
        tooltip: {
            trigger: 'none'
        },
        pointSize: 8,
        lineWidth: 3,
        legend: {
            position: 'none'
        },
        height: 290,
        width: 300,
        vAxis: {
            minValue: 0,
            maxValue: 50,
            textStyle: {
                color: '#898989'
            }
        },
        hAxis: {
            textStyle: {
                color: '#898989'
            },
            viewWindowMode: 'maximized'
        },
        pieSliceText: 'none',
        backgroundColor: '#F5F4F2'
    };
    var chart = new google.visualization.PieChart(document.getElementById('assetsPieChart'));
    var mousehandler = function(event, x, y) {
        var row = rows[event.row];
        var halfValue = Math.round(row.value / 2);
        var cumTotal = cumulativeTotals[event.row] - halfValue;
        var theta = 450 - ((cumTotal / totalValue) * 360);
        if (theta > 360) {
            theta = -1 * (360 - theta);
        }
        if (theta === 360) {
            theta = 0;
        }
        var hoverX = Math.round((60) * Math.cos(toRadians(theta)));
        var hoverY = Math.round((60) * Math.sin(toRadians(theta)));
        //alert( "cumTotal: " + cumTotal + "\ntheta: " + theta + "\nx: " + x );
        var contents = 'cumTotal: ' + cumTotal;
        contents += "<br>row: " + event.row;
        contents += "<br>theta: " + theta;
        contents += "<br>cos(theta): " + Math.cos(toRadians(theta));
        contents += "<br>sin(theta): " + Math.sin(toRadians(theta));
        contents += "<br>x: " + hoverX;
        contents += "<br>y: " + hoverY;
        var style = "border: red solid 1px; background-color: white; color: gray; font-size: 10px; position: absolute; top: " + (70 - hoverY) + "px; left: " + (90 + hoverX) + "px";

        if (lastAddedElement) {
            lastAddedElement.remove();
            lastAddedElement = null;
        }

        var colorClass = 'pbPurple';
        var htmlStr = '<div id="assetTooltip"><div class="pointerBubble pbShort ' + row.cssClass + '" style="position: absolute; top: ' + (70 - hoverY) + 'px; left: ' + (70 + hoverX) + 'px' + '">' +
                ' <span class="pbPlus"></span><span class="pbNumber">' + (Math.round(row.value / totalValue * 100)) + '</span><span class="pbPercentage">%</span><br/>' +
                ' <span style="color: black; font-size: .8em; font-family: arial"><b>' + row.name + '</b>&nbsp;&nbsp;' + row.sign + '$' + row.value.formatMoney(0, '.', ',') + '</span><br/>' + ' <div class="pbPointerBottom">&nbsp;</div>' +
                '</div></div>';

        lastAddedElement = $(htmlStr);
        lastAddedElement.appendTo('#assetsChartWrapper');

        var styleReal = "position: absolute; top: " + (70 - $(".pointerBubble")[0].clientHeight + 89 - hoverY) + "px; left: " + (70 + hoverX) + "px";
        $(".pointerBubble").attr('style', styleReal);
        var styleReal = "position: absolute; top: " + ($(".pointerBubble")[0].clientHeight + $(".pointerBubble")[0].clientHeight % 2) + "px;";
        $(".pbPointerBottom").attr('style', styleReal);
    };
    var mousehandlerout = function(event, x, y) {
        if (lastAddedElement) {
            lastAddedElement.remove();
            lastAddedElement = null;
        }
    };
    var myReadyHandler = function() {
        google.visualization.events.addListener(chart, 'onmouseover', mousehandler);
        google.visualization.events.addListener(chart, 'onmouseout', mousehandlerout);
    };
    google.visualization.events.addListener(chart, 'ready', myReadyHandler);
    chart.draw(dataTable, options);
    
    $( "#assetsChartWrapper" ).mouseout(function() {
        $( "#assetTooltip" ).hide();
    });
    $( "#debtsChartWrapper" ).mouseout(function() {
        $( "#debtTooltip" ).hide();
    });
    
    $( "#assetsPieChart" ).mouseover(function() {
        $( "#debtTooltip" ).hide();
        $( "#assetTooltip" ).show();
        
    });
    $( "#debtsPieChart" ).mouseover(function() {
        $( "#assetTooltip" ).hide();
        $( "#debtTooltip" ).show();
    });
};


function drawEmptyDebtsChart() {
    if (!$("#debtsPieInfo").hasClass('hdn')) {
        $("#debtsPieInfo").show();
        $("#debtsChartTotal").hide();
    }
    else
    {
        $("#debtsChartTotal").show();
        $("#cumulativeDebtsTotal").html("$0");
    }
    $("#editDebts").hide();

    var data = google.visualization.arrayToDataTable([
        ['', 'Hours per Day'],
        [' ', 22],
        ['', 7],
        ['', 4],
        ['', 4],
        ['', 2],
        ['', 1.5]
    ]);

    var options = {
        chartArea: {
            width: "80%",
            height: "80%"
        },
        colors: ['#ededed', '#dddddd', '#cccccc', '#bbbbbb', '#ababab', '#aaaaaa'],
        hAxis: {
            viewWindowMode: 'maximized'
        },
        tooltip: {
            isHtml: true
        },
        pointSize: 8,
        lineWidth: 3,
        legend: {
            position: 'none'
        },
        height: 290,
        width: 300,
        vAxis: {
            minValue: 0,
            maxValue: 50,
            textStyle: {
                color: '#898989'
            }
        },
        hAxis: {
            textStyle: {
                color: '#898989'
            }
        },
        pieSliceText: 'none',
        backgroundColor: '#F5F4F2',
        tooltip: {
            trigger: 'none'
        }
    };

    var chart = new google.visualization.PieChart(document.getElementById('debtsPieChart'));
    chart.draw(data, options);
}

function drawDebtsChart() {
    $("#assetTooltip").hide();
    $("#debtTooltip").show();
    $("#debtsPieInfo").hide();
    $("#editDebts").show();
    $("#debtsChartTotal").show();

    "use strict";
    var lastAddedElement = null;
    var i = 0;
    var dataTable = new google.visualization.DataTable();
    var totalAmt = 0;
    var dataArray = financialData.debts;
    var cssClasses = ['pbYellow', 'pbOrange', 'pbBlue', 'pbTurquoise', 'pbPink', 'pbRainforest', 'pbPurple', 'pbRed'];
    var rows = [];
    var k = 0;
    for (i = 0; i < dataArray.length; i++)
    {
        if (dataArray[i].status == 0 && dataArray[i].monthly_payoff_balances == 0)
        {
            if (dataArray[i].amount == "" || typeof (dataArray[i].amount) == 'undefined')
            {
                rows[k] = {
                    'name': dataArray[i].nameSummary,
                    'value': 0,
                    'sign': ''
                };
            }
            else
            {
                rows[k] = {
                    'name': dataArray[i].nameSummary,
                    'value': Math.abs(parseFloat(dataArray[i].amount.replace(/,/g, ''))),
                    'sign': (parseInt(dataArray[i].amount) < 0) ? "-" : ""
                };
            }
            k++;
            totalAmt = parseFloat(totalAmt) + parseFloat(dataArray[i].amount.replace(/,/g, ''));
        }
    }

    rows.sort(function(a, b) {
        return (b.value - a.value);
    });
    for (var i = 0; i < rows.length; i++)
    {
        rows[i]['cssClass'] = cssClasses[i % 8];
    }

    dataTable.addColumn('string', 'AssetName');
    dataTable.addColumn('number', 'AssetPercentage');

    var totalValue = 0;
    var cumulativeTotals = [];
    for (i = 0; i < rows.length; i++) {
        totalValue = (totalValue + rows[i].value);
        dataTable.addRow([rows[i].name + ': $' + rows[i].value, rows[i].value]);
        cumulativeTotals.push(totalValue);
    }
    var sign = (totalAmt < 0) ? "-" : "";
    $("#cumulativeDebtsTotal").html(sign + "$" + commaSeparateNumber(Math.abs(totalAmt), 0));

    var options = {
        chartArea: {
            width: "80%",
            height: "80%"
        },
        colors: ['#ffc324', 'f36639', '#00669a', '#00b1b8', '#ff8da6', '#00605e', '6c68aa', '9a180d'],
        tooltip: {
            trigger: 'none'
        },
        pointSize: 8,
        lineWidth: 3,
        legend: {
            position: 'none'
        },
        height: 290,
        width: 300,
        vAxis: {
            minValue: 0,
            maxValue: 50,
            textStyle: {
                color: '#898989'
            }
        },
        hAxis: {
            textStyle: {
                color: '#898989'
            },
            viewWindowMode: 'maximized'
        },
        pieSliceText: 'none',
        backgroundColor: '#F5F4F2'
    };
    var chart = new google.visualization.PieChart(document.getElementById('debtsPieChart'));
    var mousehandler = function(event, x, y) {
        var row = rows[event.row];
        var halfValue = Math.round(row.value / 2);
        var cumTotal = cumulativeTotals[event.row] - halfValue;
        var theta = 450 - ((cumTotal / totalValue) * 360);
        if (theta > 360) {
            theta = -1 * (360 - theta);
        }
        if (theta === 360) {
            theta = 0;
        }
        var hoverX = Math.round((60) * Math.cos(toRadians(theta)));
        var hoverY = Math.round((60) * Math.sin(toRadians(theta)));
        //alert( "cumTotal: " + cumTotal + "\ntheta: " + theta + "\nx: " + x );
        var contents = 'cumTotal: ' + cumTotal;
        contents += "<br>row: " + event.row;
        contents += "<br>theta: " + theta;
        contents += "<br>cos(theta): " + Math.cos(toRadians(theta));
        contents += "<br>sin(theta): " + Math.sin(toRadians(theta));
        contents += "<br>x: " + hoverX;
        contents += "<br>y: " + hoverY;
        var style = "border: red solid 1px; background-color: white; color: gray; font-size: 10px; position: absolute; top: " + (70 - hoverY) + "px; left: " + (90 + hoverX) + "px";

        if (lastAddedElement) {
            lastAddedElement.remove();
            lastAddedElement = null;
        }

        var dstyleReal = "position: absolute; top: " + (70 - hoverY) + "px; left: " + (70 + hoverX) + "px";
        var colorClass = 'pbPurple';
        var htmlStr = '<div id="debtTooltip"><div class="dpointerBubble pointerBubble pbShort ' + row.cssClass + '" style="' + dstyleReal + '">' +
                ' <span class="pbPlus"></span><span class="pbNumber">' + (Math.round(row.value / totalValue * 100)) + '</span><span class="pbPercentage">%</span><br/>' +
                ' <span style="color: black; font-size: .8em; font-family: arial"><b>' + row.name + '</b>&nbsp;&nbsp;' + row.sign + '$' + row.value.formatMoney(0, '.', ',') + '</span><br/>' +
                ' <div class="pbPointerBottom dpbPointerBottom">&nbsp;</div>' +
                '</div></div>';
        /*lastAddedElement = $('<div style="' + style + '">' + contents + '</div>');*/
        lastAddedElement = $(htmlStr);
        lastAddedElement.appendTo('#debtsChartWrapper');

        var dstyleReal = "position: absolute; top: " + (70 - $(".dpointerBubble")[0].clientHeight + 89 - hoverY) + "px; left: " + (70 + hoverX) + "px";
        $(".dpointerBubble").attr('style', dstyleReal);
        var dstyleReal = "position: absolute; top: " + ($(".dpointerBubble")[0].clientHeight + $(".dpointerBubble")[0].clientHeight % 2) + "px;";
        $(".dpbPointerBottom").attr('style', dstyleReal);
    };
    var mousehandlerout = function(event, x, y) {
        if (lastAddedElement) {
            lastAddedElement.remove();
            lastAddedElement = null;
        }
    };
    var myReadyHandler = function() {
        google.visualization.events.addListener(chart, 'onmouseover', mousehandler);
        google.visualization.events.addListener(chart, 'onmouseout', mousehandlerout);
    };
    google.visualization.events.addListener(chart, 'ready', myReadyHandler);
    chart.draw(dataTable, options);
    
    $( "#assetsChartWrapper" ).mouseout(function() {
        $( "#assetTooltip" ).hide();
    });
    $( "#debtsChartWrapper" ).mouseout(function() {
        $( "#debtTooltip" ).hide();
    });
    
    $( "#assetsPieChart" ).mouseover(function() {
        $( "#debtTooltip" ).hide();
        $( "#assetTooltip" ).show();
        
    });
    $( "#debtsPieChart" ).mouseover(function() {
        $( "#assetTooltip" ).hide();
        $( "#debtTooltip" ).show();
    });
};

function drawEmptyBarChart(divId) {
    if (divId == 'netWorthBarChart')
        $("#netWorthBarInfo").show();
    else
        $("#debtsBarInfo").show();

    var data = google.visualization.arrayToDataTable([
        ['Date', 'FlexScore'],
        ['', 10],
        ['', 19],
        [' ', 24],
        [' ', 23],
        ['', 20],
        ['', 21]
    ]);

    var options = {
        chartArea: {
            left: 35,
            top: 40,
            width: "90%",
            height: "75%"
        },
        colors: ['#bbbbbb'],
        hAxis: {
            viewWindowMode: 'maximized'
        },
        tooltip: {
            isHtml: true
        },
        pointSize: 8,
        lineWidth: 3,
        legend: {
            position: 'none'
        },
        height: 330,
        width: 620,
        vAxis: {
            minValue: 0,
            maxValue: 50,
            textStyle: {
                color: '#898989'
            }
        },
        hAxis: {
            minValue: 0,
            textStyle: {
                color: '#898989'
            }
        },
        backgroundColor: '#F5F4F2',
        tooltip: {
            trigger: 'none'
        }
    };

    var chart = new google.visualization.AreaChart(document.getElementById(divId));
    chart.draw(data, options);

}

function drawEmptyNetworthBarChart() {

    var data = google.visualization.arrayToDataTable([
        ['Date', 'FlexScore'],
        ['', 10],
        ['', 19],
        [' ', 24],
        [' ', 23],
        ['', 20],
        ['', 21]
    ]);

    var options = {
        chartArea: {
            left: 35,
            top: 40,
            width: "90%",
            height: "75%"
        },
        colors: ['#bbbbbb'],
        hAxis: {
            viewWindowMode: 'maximized'
        },
        tooltip: {
            isHtml: true
        },
        pointSize: 8,
        lineWidth: 3,
        legend: {
            position: 'none'
        },
        height: 330,
        width: 620,
        vAxis: {
            minValue: 0,
            maxValue: 50,
            textStyle: {
                color: '#898989'
            }
        },
        hAxis: {
            textStyle: {
                color: '#898989'
            }
        },
        backgroundColor: '#F5F4F2',
        tooltip: {
            trigger: 'none'
        }
    };

    var chart = new google.visualization.AreaChart(document.getElementById("netWorthBarChart"));
    chart.draw(data, options);

}

function drawNetWorthBarChart(range) {
    "use strict";
    $("#netWorthBarInfo").hide();

    var dataTable = new google.visualization.DataTable();
    dataTable.addColumn('string', 'Date');
    dataTable.addColumn('number', 'NetWorth');
    dataTable.addColumn({
        'type': 'string',
        'role': 'tooltip',
        'p': {
            'html': true
        }
    });
    
    if(range == undefined){
        range = '01';
    }

    $.getJSON(baseUrl + "/service/api/getnetworthscore?range=" + range, function(data) {
        if (data != null)
        {
            var array = $.map(data.networthGraphData, function(value, index) {
                return [value];
            });
        }

        var Month = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
        var cssClasses = ['pbYellow', 'pbOrange', 'pbBlue', 'pbTurquoise', 'pbPink', 'pbRainforest', 'pbPurple', 'pbRed'];
        //TODO: connect this with networth api

        var minV = 0;
        var maxV = 50;
        var firstData = 0;
        if (array && array.length > 0) {
            for (var i = 0; i < array.length; i++) {
                var obj = array[i];
                firstData = parseInt(array[0].TotalScore);

                var current = parseInt(array[i].TotalScore);
                if (current > maxV) {
                    maxV = current;
                }
                if (current < minV) {
                    minV = current;
                }

                var cnt2 = array.length - 1;
                var lastData = parseInt(array[cnt2].TotalScore);

                var growthRate = 0;
                if (firstData == 0) {
                    growthRate = 100;
                }
                else {
                    growthRate = (lastData - firstData) / firstData;
                    growthRate = Math.round(growthRate * 100, 2);
                }

                if (lastData > firstData) {
                    document.getElementById("growth").innerHTML = "+" + Math.abs(growthRate) + "%";
                }
                else if (lastData < firstData) {
                    document.getElementById("growth").innerHTML = "-" + Math.abs(growthRate) + "%";
                }
                else {
                    document.getElementById("growth").innerHTML = "0%";
                }

                var serverTime = new Date(obj.FullDate);
                var shortMonth = serverTime.getMonth();

                var browserShortDate = Month[shortMonth] + " " + serverTime.getDate();
                var browserFullDate = Month[shortMonth] + " " + serverTime.getDate() + " " + serverTime.getFullYear();

                if (i == array.length - 1) {
                    dataTable.addRows([
                        ["Today", parseInt(obj.TotalScore), drawNetWorthToolTip(parseInt(obj.TotalScore), 'Net Worth', browserFullDate, cssClasses[i])],
                    ]);
                }
                else {
                    dataTable.addRows([
                        [browserShortDate, parseInt(obj.TotalScore), drawNetWorthToolTip(parseInt(obj.TotalScore), 'Net Worth', browserFullDate, cssClasses[i])],
                    ]);
                }
            }
            document.getElementById("netWorthBarInfo").style.display = "none";

            if (Math.abs(minV - firstData) > Math.abs(maxV - firstData)) {
                maxV = firstData + Math.abs(minV - firstData);
            }
            else if (Math.abs(minV - firstData) < Math.abs(maxV - firstData)) {
                minV = firstData - Math.abs(maxV - firstData);
            }
            // TODO: add 8 left pixels per extra digit. 1 digit = 25 default starting point.
            var options = {
                chartArea: {
                    left: 73,
                    top: 40,
                    width: "85%",
                    height: "75%"
                },
                colors: ['#6863a6'],
                tooltip: {
                    isHtml: true
                },
                pointSize: 8,
                lineWidth: 3,
                legend: {
                    position: 'none'
                },
                height: 330,
                width: 620,
                vAxis: {
                    minValue: minV,
                    maxValue: maxV,
                },
                hAxis: {
                    textStyle: {
                        color: '#898989'
                    },
                    viewWindowMode: 'maximized'
                },
                backgroundColor: '#F5F4F2'
            };
            var chart = new google.visualization.AreaChart(document.getElementById('netWorthBarChart'));
            var mousehandler = function(event, x, y) {
                $('.google-visualization-tooltip').addClass('google-visualization-tooltip-override');
                $('.google-visualization-tooltip').removeClass('google-visualization-tooltip');
            };
            var clickhandler = function(event, x, y) {
                $('.google-visualization-tooltip').addClass('google-visualization-tooltip-override');
                $('.google-visualization-tooltip').removeClass('google-visualization-tooltip');
            };
            var myReadyHandler = function() {
                google.visualization.events.addListener(chart, 'onmouseover', mousehandler);
                google.visualization.events.addListener(chart, 'select', clickhandler);
            };
            google.visualization.events.addListener(chart, 'ready', myReadyHandler);
            chart.draw(dataTable, options);

        } else {
            switch (range)
            {
                case "01":
                default:
                    document.getElementById("netWorthBarInfo").innerHTML = "It requires minimum seven days data to plot the graph.";
                    break;
                case "02":
                    document.getElementById("netWorthBarInfo").innerHTML = "It requires minimum 30 days data to plot the graph.";
                    break;
                case "03":
                    document.getElementById("netWorthBarInfo").innerHTML = "It requires minimum 90 days data to plot the graph.";
                    break;
                case "04":
                    document.getElementById("netWorthBarInfo").innerHTML = "It requires minimum 180 days data to plot the graph.";
                    break;
            }
            document.getElementById("netWorthBarInfo").style.display = "";
            drawEmptyNetworthBarChart();
        }
    });
}

// TODO: Sorting all assets and debts charts by highest to lowest
function drawDebtsBarChart() {
    "use strict";

    $("#debtsBarInfo").hide();

    var i = 0;
    var dataArray = financialData.debts;
    var cssClasses = ['#f36639', '#00b1b8', '#898989', '#ff8da6', '#00605e', '#ffc324', '#00669a', '#6c68aa','#f36639','#00b1b8','#898989','#ff8da6','#00605e','#ffc324','#00669a','#6c68aa','#f36639','#00b1b8','#898989','#ff8da6','#00605e','#ffc324','#00669a','#6c68aa'];

    var temprows = [];
    var k = 0;
    for (i = 0; i < dataArray.length; i++)
    {
        if (dataArray[i].status == 0 && dataArray[i].monthly_payoff_balances == 0)
        {

            if (dataArray[i].amount == "" || typeof (dataArray[i].amount) == 'undefined')
                temprows[k] = {
                    'name': dataArray[i].nameSummary,
                    'amount': 0,
                    'sign': "",
                    'tooltip_amount':""

                };

            else
                temprows[k] = {
                    'name': dataArray[i].nameSummary,
                    //'amount': Math.abs(parseFloat(dataArray[i].amount.replace(/,/g, ''))),
                    'amount': parseFloat(dataArray[i].amount.replace(/,/g, '')),
                    'sign': (parseInt(dataArray[i].amount) < 0) ? "-" : "",
                    'tooltip_amount': dataArray[i].amount
                };
            k++;
        }
    }
    temprows.sort(function(a, b) {
        return (b.amount - a.amount);
    });

    var data = new google.visualization.DataTable();

    data.addColumn('string', 'debtname');
    data.addColumn('number', 'amount');
    data.addColumn({ type: 'string', role: 'style'});
    data.addColumn({ type: 'string', role: 'tooltip', 'p': { 'html': true} });
    var amtShow = "";
     for (var i = 0; i < temprows.length; i++) {
         if(temprows[i].amount < 0){
             amtShow = '-$'+temprows[i].tooltip_amount.replace("-","");
         }else{
             amtShow = '$'+temprows[i].tooltip_amount;
         }
      data.addRows([
        [temprows[i].name, temprows[i].amount, cssClasses[i], customTooltip('<span style="font-family:Arial;font-weight:bold;color:#333333">'+temprows[i].name+'</span><br/>Amount: <span style="font-family:Arial;font-weight:bold;color:#333333">'+amtShow +'</span>')],]);
     }
    var options = {
        chartArea: {
            width: "70%",
            height: "80%"
        },
        tooltip: {
            isHtml: true
        },
        pointSize: 8,
        lineWidth: 3,
        legend: {
            position: 'none'
        },
        height: 330,
        width: 620,
        vAxis: {
            textStyle: {
                color: '#898989'
            }
        },
        hAxis: {
            textStyle: {
                color: '#898989'
            },
            viewWindowMode: 'maximized',
            format: '###,###',
         },
         backgroundColor: '#F5F4F2',
         isStacked: true
    };
    var chart = new google.visualization.BarChart(document.getElementById('debtsBarChart'));
    chart.draw(data, options);
}

function customTooltip(text) {
    return '<div style="padding:10px;">' +
    '<table id="medals_layout">' + '<tr>' +
    '<td><b>' + text + '</b></td>' + '</tr>' + '</table>' + '</div>';

}

function initFinancial()
{
    $('.fsMSheader').click(function(event) {
        $(this).find('.fsMSTitle a').toggleClass('openArrow');
        $(this).find('.fsMSTitle a').toggleClass('closeArrow');
        $('.' + this.id + 'Details').toggleClass('hdn');
    });

}
