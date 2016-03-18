/*jslint vars: true, white: false */
/*global google: false, $: false, window: false, document: false, initButtons: false, drawProjectionChart: false, location: false */

function popUpActionStep(url, height) {
    "use strict";
    $.scrollTo($('#homeBg'), 200);
    $('#comparisonBoxHome, #darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#comparisonBoxHome').css("height", height);
    $.get(url, function (data) {
        $('#comparisonBoxHome').css('height', height);
        $('#comparisonBoxHome').html(data);
    });
}

function removeLayover() {
    "use strict";
    $('#comparisonBoxHome').html('');
    $('#comparisonBoxHome').hide();
    $('#darkBackground').hide();
}

/* for tabbed signup overlay */
function selectTab(tabId) {
    "use strict";
    $('.selected').removeClass('selected');
    $('#' + tabId).addClass('selected');

    //var tabId = tab.id,
    var tabNum = tabId.substring(4);

    if ($('#' + tabId).is('div.tabBox ul.tabs li')) {
        $('.tab-content').addClass("hdn");
        $('#tab-content-' + tabNum).removeClass("hdn");
    } else if ($('#' + tabId).is('div.mtabBox ul.tabs li')) {
        $('.mtab-content').addClass("hdn");
        $('#m-tab-content-' + tabNum).removeClass("hdn");
    }
}

$(function () {
    "use strict";
    $('#myCarousel').on('slid', function (event) {
        $('.caro-info').hide();
        $('#caro-info-' + $('#myCarousel .active').index('#myCarousel .item')).show();
    });

    /* tab selection - modal and regular */
    $('div.tabBox ul.tabs li, div.mtabBox ul.tabs li').live("click", function () {
        selectTab(this.id);
    });

    $.event.special.swipe.scrollSupressionThreshold = 1;

    $("#myCarousel").swiperight(function () {
        $("#myCarousel").carousel('prev');
    });
    $("#myCarousel").swipeleft(function () {
        $("#myCarousel").carousel('next');
    });
});
