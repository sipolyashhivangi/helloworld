/*jslint vars: true, white: false */
/*global google: false, $: false, window: false, document: false, initButtons: false, drawProjectionChart: false, location: false, formatMoneyFunc: false */
/* myScore COMPARE tab charts - GVT-based horseshoes */
function toRadians(angle) {
    "use strict";
    return angle * (Math.PI / 180);
}
function drawToolTip(points, info, dateStr, colorClass, topMargin, leftMargin) {
    "use strict";
    return '<div class="pointerBubble ' + colorClass + '" style="margin-top: ' + topMargin + '; margin-left: ' + leftMargin + ';">' +
    ' <span class="pbPlus"></span><span class="pbNumber">' + points + '</span><span class="pbPts">pts</span><br/>' +
    ' <span class="pbDate">' + dateStr + '</span>' +
    ' <div class="pbPointerBottom">&nbsp;</div>' +
    '</div>';
}

function drawNothing() {
    "use strict";
    return '<div>&nbsp;</div>';
}

function drawBothCharts() {
    drawMilestonesChart();
////    drawProjectionChart();
}


/* MILESTONES CHART */
function drawMilestonesChart(range) {
    "use strict";
    var dataTable = new google.visualization.DataTable();
    dataTable.addColumn('string', 'Date');
    dataTable.addColumn('number', 'FlexScore');
    dataTable.addColumn({
        type: 'boolean',
        role: 'certainty'
    }); // certainty col.
    dataTable.addColumn({
        'type': 'string',
        'role': 'tooltip',
        'p': {
            'html': true
        }
    });
    dataTable.addColumn('number', 'ProjectedFlexScore');
    dataTable.addColumn({
        type: 'boolean',
        role: 'certainty'
    }); // certainty col.
    dataTable.addColumn({
        'type': 'string',
        'role': 'tooltip',
        'p': {
            'html': true
        }
    });

    if ((typeof(milestoneRange) == 'undefined' || milestoneRange == 0) && range == undefined) {
        range = '01';
    } else if (milestoneRange > 0 && range == undefined) {
        range = milestoneRange;
    }
    if (range != undefined && range == milestoneRange) {
        if (range == "02") {
            $("#milesButtonSpan").html("1 Month");
        } else if (range == "03") {
            $("#milesButtonSpan").html("3 Months");
        } else if (range == "04") {
            $("#milesButtonSpan").html("6 Months");
        } else {
            $("#milesButtonSpan").html("7 Days");
        }
    }

    var milestone_data = [];
    if (typeof (milestoneGraphData) == 'undefined' || milestoneGraphData == '' ||  milestoneRange != range || milestoneRange == 0) {
        $.getJSON(baseUrl + "/service/api/getscore?range=" + range, function(data) {
            milestone_data = $.map(data.mileStoneGraphData, function(value, index) {
                return [value];
            });
            fnUpdateMilestoneGraphData(data.mileStoneGraphData, range);
            fnShowMilestones(dataTable, milestone_data, range);
        });
    } else {
        milestone_data = $.map(milestoneGraphData, function(value, index) {
            return [value];
        });
        fnShowMilestones(dataTable, milestone_data, range);
    }
}

function fnShowMilestones(data_table, milestone_data, range) {

    var range = range;
    var array = milestone_data;
    var dataTable = data_table;
    var Month=new Array("Jan", "Feb", "Mar","Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

    if (array.length > 0) {
        for (var i = 0; i < array.length; i++) {
            var obj = array[i];

            var serverTime = new Date(obj.FullDate);
            var shortMonth = serverTime.getMonth();

            var browserShortDate = Month[shortMonth]+" "+ serverTime.getDate();
            var browserFullDate = Month[shortMonth]+" "+ serverTime.getDate()+" "+ serverTime.getFullYear();

            if (i == array.length - 1) {
                dataTable.addRows([
                    ["Today", parseInt(obj.TotalScore), true, drawToolTip(parseInt(obj.TotalScore), '', browserFullDate, 'pbPurple', "-25px", "22px"), null, false, null],
                    ]);
            }
            else {
                dataTable.addRows([
                    [browserShortDate, parseInt(obj.TotalScore), true, drawToolTip(parseInt(obj.TotalScore), '', browserFullDate, 'pbPurple', "-25px", "22px"), null, false, null],
                    ]);
            }
        }
        document.getElementById("popupArea").style.display = "none";


        var options = {
            chartArea: {
                left: 40,
                top: 30,
                width: "85%",
                height: "80%"
            },
            colors: ['#6863a6', '#f36639'],
            tooltip: {
                isHtml: true
            },
            pointSize: 8,
            lineWidth: 3,
            legend: {
                position: 'none'
            },
            height: 290,
            width: 595,
            vAxis: {
                minValue: 0,
                maxValue: 1000,
                baselineColor: '#898989',
                textStyle: {
                    color: '#898989'
                }
            },
            hAxis: {
                titleTextStyle: {
                    color: '#FF0000'
                },
                textStyle: {
                    color: '#898989'
                },
                baselineColor: '#898989',
                viewWindowMode: 'maximized'
            }
        };

        var chart = new google.visualization.AreaChart(document.getElementById('tab-content-1-chart'));

        /* handlers for hover/clicks on individual datum */
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

        //var chart = new google.visualization.AreaChart(document.getElementById('tab-content-2-chart'));
        google.visualization.events.addListener(chart, 'ready', myReadyHandler);
        chart.draw(dataTable, options);

    } else {

        switch(range)
        {
            case "01":
            default:
                document.getElementById("popupArea").innerHTML = "It requires minimum seven days data to plot the graph.";
                break;
            case "02":
                document.getElementById("popupArea").innerHTML = "It requires minimum 30 days data to plot the graph.";
                break;
            case "03":
                document.getElementById("popupArea").innerHTML = "It requires minimum 90 days data to plot the graph.";
                break;
            case "04":
                document.getElementById("popupArea").innerHTML = "It requires minimum 180 days data to plot the graph.";
                break;
        }

        document.getElementById("popupArea").style.display = "";

        dataTable.addRows([
            ['Dec 15', 220, true, drawToolTip(220, '', 'December 15, 2012', 'pbPurple', 0, 0), null, false, null],
            ['Jan 15', 425, true, drawToolTip(425, '', 'January 15, 2013', 'pbPurple', 0, 0), null, false, null],
            ['Feb 15', 383, true, drawToolTip(383, '', 'February 15, 2013', 'pbPurple', 0, 0), null, false, null],
            ['Mar 15', 407, true, drawToolTip(407, '', 'March 15, 2013', 'pbPurple', 0, 0), null, false, null],
            ['Apr 15', 442, true, drawToolTip(442, '', 'April 15, 2013', 'pbPurple', 0, 0), null, false, null],
            ['May 15', 415, true, drawToolTip(415, '', 'May 15, 2013', 'pbPurple', 0, 0), null, false, null],
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
                viewWindowMode: 'maximized',
                textStyle: {
                    color: '#898989'
                }
            },
            tooltip: {
                isHtml: true,
                trigger: 'none'
            },
            pointSize: 8,
            lineWidth: 3,
            legend: {
                position: 'none'
            },
            height: 295,
            width: 595,
            vAxis: {
                minValue: 0,
                maxValue: 50,
                textStyle: {
                    color: '#898989'
                }
            }
        };

        var chart = new google.visualization.AreaChart(document.getElementById("tab-content-1-chart"));
        chart.draw(dataTable, options);

    }
}


var output_array = "";
function drawRiskChart(riskValue) {
    $.ajax({
        url: riskFactorsGetDataURL,
        type: 'GET',
        dataType: "json",
        data: "risk_value=" + riskValue,
        success: function(rskdata) {
            timeoutPeriod = defaultTimeoutPeriod;
            if (rskdata.status == "OK") {
                output_array = rskdata.riskdata;
                if(typeof(google.visualization) != 'undefined') {
                    fnDrawRiskChart();
                }
                else
                {
                     try {
                       google.load('visualization', '1', {
                            'callback': fnDrawRiskChart,
                            'packages': ['corechart']
                        });
                    } catch (err) {
                    //alert(err);
                    }
                }
            }
        }
    });
}

function fnDrawRiskChart() {
    "use strict";
    var lastAddedElement = null,
            i = 0,
            cumulativeTotals = [],
            totalValue = 0,
            dataTable = new google.visualization.DataTable();
    var dataArray = [];
    var deq_obj = {
        'name': '',
        'value': ''
    };
    var ieq_obj = {
        'name': '',
        'value': ''
    };
    var alncassets_obj = {
        'name': '',
        'value': ''
    };
    var income_bonds_obj = {
        'name': '',
        'value': ''
    };
    var marketorcash_obj = {
        'name': '',
        'value': ''
    };

    if (output_array != "") {
        deq_obj.name = 'US Stocks';
        deq_obj.value = output_array.domestic_equity;
        dataArray[0] = deq_obj;
        ieq_obj.name = 'Foreign Stocks';
        ieq_obj.value = output_array.international_equity;
        dataArray[1] = ieq_obj;
        alncassets_obj.name = 'Non-Correlated / Alternative Assets';
        alncassets_obj.value = output_array.altr_non_corelated_assets;
        dataArray[2] = alncassets_obj;
        income_bonds_obj.name = 'Bonds';
        income_bonds_obj.value = output_array.income_bonds;
        dataArray[3] = income_bonds_obj;
        marketorcash_obj.name = 'Cash';
        marketorcash_obj.value = output_array.market_cash;
        dataArray[4] = marketorcash_obj;
    }
    //var dataArray = financialData.cash.concat(financialData.investment, financialData.other);

    var cssClasses = ['pbYellow', 'pbOrange', 'pbBlue', 'pbTurquoise', 'pbPink', 'pbRainforest', 'pbPurple', 'pbRed'];
    var rows = [];
    var tempTotal = 0;
    var k = 0;
    for (i = 0; i < dataArray.length; i++)
    {
        rows[k] = {
            'name': dataArray[i].name,
            'value': parseFloat(dataArray[i].value)
        };
        k++;
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
        dataTable.addRow([rows[i].name, rows[i].value]);
        $("#pbRow" + (i + 1)).html(rows[i].name);
        cumulativeTotals.push(totalValue);
    }

    var options = {

        colors: ['#ffc324', 'f36639', '#00669a', '#00b1b8', '#ff8da6', '#00605e', '6c68aa', '9a180d'],
        tooltip: {
            trigger: 'none'
        },
        pointSize: 8,
        lineWidth: 3,
        legend: {
            position: 'none'
        },
        height: 350,
        width: 320,
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
    var chart = new google.visualization.PieChart(document.getElementById('riskPieChart'));
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
        var style = "border: red solid 1px; background-color: white; color: gray; font-size: 10px; position: absolute; top: " + (90 - hoverY) + "px; left: " + (80 + hoverX) + "px";

        if (lastAddedElement) {
            lastAddedElement.remove();
            lastAddedElement = null;
        }

        var colorClass = 'pbPurple';
        var htmlStr = '<div class="pointerBubble pbShort ' + row.cssClass + '" style="position: absolute; top: ' + (90 - hoverY) + 'px; left: ' + (70 + hoverX) + 'px' + '">' +
                ' <span class="pbPlus" style="font-size: .8em; "></span><span class="pbNumber" style="font-size: 2em;">' + (Math.round(row.value / totalValue * 100)) + '</span><span class="pbPercentage">%</span> ' +
                ' <span style="color: black; font-size: .8em; font-family: arial"><b>' + row.name + '</b></span><br/>' + ' <div class="pbPointerBottom">&nbsp;</div>' +
                '</div>';

        lastAddedElement = $(htmlStr);
        lastAddedElement.appendTo('#riskChartWrapper');

        var styleReal = "position: absolute; top: " + (80 - $(".pointerBubble")[0].clientHeight + 60 - hoverY) + "px; left: " + (80 + hoverX) + "px";
        $(".pointerBubble").attr('style', styleReal);
        var styleReal = "position: absolute; top: " + $(".pointerBubble")[0].clientHeight  + "px;";
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
};