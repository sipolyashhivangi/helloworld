/*jslint vars: true, white: false */
/*global google: false, $: false, window: false, document: false, initButtons: false, drawProjectionChart: false, location: false, formatMoneyFunc: false */
/* myScore COMPARE tab charts - GVT-based horseshoes */

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
//// 	drawProjectionChart();
}


/* MILESTONES CHART */
function drawMilestonesChart() {
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

    var data;
//    var todayScore = currentScore["totalscore"];
    
/*
        dataTable.addRows([
            ['Dec 15', 499, true, drawToolTip(499, '', 'December 15, 2012', 'pbPurple', 0, 0), null, false, null],
            ['Jan 15', 485, true, drawToolTip(485, '', 'January 15, 2013', 'pbPurple', 0, 0), null, false, null],
            ['Feb 15', 501, true, drawToolTip(501, '', 'February 15, 2013', 'pbPurple', 0, 0), null, false, null],
            ['Mar 15', 520, true, drawToolTip(520, '', 'March 15, 2013', 'pbPurple', 0, 0), null, false, null],
            ['Apr 15', 514, true, drawToolTip(514, '', 'April 15, 2013', 'pbPurple', 0, 0), null, false, null],
            ['Today', 535, true, drawToolTip(535, '', 'Today', 'pbPurple', 0, 0), null, false, null],

        ]);
*/
        dataTable.addRows([
            ['Dec 15', 220, true, drawToolTip(220, '', 'December 15, 2012', 'pbPurple', 0, 0), null, false, null],
            ['Jan 15', 425, true, drawToolTip(425, '', 'January 15, 2013', 'pbPurple', 0, 0), null, false, null],
            ['Feb 15', 383, true, drawToolTip(383, '', 'February 15, 2013', 'pbPurple', 0, 0), null, false, null],
            ['Mar 15', 407, true, drawToolTip(407, '', 'March 15, 2013', 'pbPurple', 0, 0), null, false, null],
            ['Apr 15', 442, true, drawToolTip(442, '', 'April 15, 2013', 'pbPurple', 0, 0), null, false, null],
            ['May 15', 415, true, drawToolTip(415, '', 'May 15, 2013', 'pbPurple', 0, 0), null, false, null],
            ['Jun 15', 470, true, drawToolTip(470, '', 'June 15, 2013', 'pbPurple', 0, 0), null, false, null],
            ['July 15', 505, true, drawToolTip(505, '', 'July 15, 2013', 'pbPurple', 0, 0), null, false, null],
            ['Aug 15', 535, true, drawToolTip(535, '', 'August 15, 2013', 'pbPurple', 0, 0), null, false, null],

        ]);

    var options = {
        chartArea: {
            left: 30,
            top: 40,
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
        height: 300,
        width: 595,
        vAxis: {
            minValue: 400,
            maxValue: 600,
            baselineColor: '#898989',
            textStyle: {
                color: '#898989'
            }
        },
        hAxis: {
            textStyle: {
                color: '#898989'
            },
            baselineColor: '#898989',
            viewWindowMode: 'maximized'
        }
    };

    var chart = new google.visualization.AreaChart(document.getElementById('tab-content-1-chart'));

    /* handlers for hover/clicks on individual datum */
    var mousehandler = function (event, x, y) {
        $('.google-visualization-tooltip').addClass('google-visualization-tooltip-override');
        $('.google-visualization-tooltip').removeClass('google-visualization-tooltip');
    };

    var clickhandler = function (event, x, y) {
        $('.google-visualization-tooltip').addClass('google-visualization-tooltip-override');
        $('.google-visualization-tooltip').removeClass('google-visualization-tooltip');
    };

    var myReadyHandler = function () {
        google.visualization.events.addListener(chart, 'onmouseover', mousehandler);
        google.visualization.events.addListener(chart, 'select', clickhandler);
    };

    //var chart = new google.visualization.AreaChart(document.getElementById('tab-content-2-chart'));
    google.visualization.events.addListener(chart, 'ready', myReadyHandler);
    chart.draw(dataTable, options);

}
