/*jslint vars: true, white: false */
/*global google: false, $: false, window: false, document: false, initButtons: false, drawProjectionChart: false, location: false */
function bsGnavFunction(element, srollToId) {
    "use strict";
    $('.nav_22 li').each(function () {
        if ($(this).hasClass('gnavButton')) {
            $(this).removeClass('hover');
            $(this).removeClass('reverseShadowBoxLight');
        }
    });

    $(element).addClass('hover');
    $(element).addClass('reverseShadowBoxLight');

    $('.btn-navbar').click();
    $.scrollTo($('#' + srollToId), 800, {
        'axis': 'y'
    });
}

$('.gnavButton, .gnavButtonMobile').click(function () {
    "use strict";
    $('.gnavButton, .gnavButtonMobile').removeClass('on');
    $(this).addClass('on');
});

// set the header 'on' state based on hashtag
var hashName = window.location.hash;
if (hashName === '#securityLand') {
    $('#gnav_security').addClass('on');
} else if (hashName === '#howItWorksLand') {
    $('#gnav_howItWorks').addClass('on');
} else if (hashName === '#aboutFlexScoreLand') {
    $('#gnav_about').addClass('on');
}

$(function () {
    /* gnav rollovers */
    "use strict";
    $('.nav_22 li').hover(function () {
        if ($(this).hasClass('gnavButton')) {
            $(this).addClass('hover');
            $(this).addClass('reverseShadowBoxLight');
        }
    }, function () {
        if ($(this).hasClass('gnavButton')) {
            $(this).removeClass('hover');
            $(this).removeClass('reverseShadowBoxLight');
        }
    });
});