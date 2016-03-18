/*jslint vars: true, white: false */
/*global google: false, $: false, window: false, document: false, initButtons: false, drawProjectionChart: false, location: false */
$(function () {
    "use strict";
    $("#foo5").carouFredSel({
        width: "100%",
        circular: false,
        infinite: false,
        items: {
            visible: {
                min: 1,
                max: 4
            }
        },
        auto: false,
        pagination: "#pager_container",
        swipe: true
    });

    $("#foo6").carouFredSel({
        width: "100%",
        circular: false,
        infinite: false,
        items: {
            visible: {
                min: 1,
                max: 4
            }
        },
        auto: false,
        pagination: "#pager_container2",
        swipe: true
    });

    $('.topics.list_carousel li').hover(function (thing) {
        $($(this).children('div')[0]).hide();
        $($(this).children('div')[1]).show();
    }, function (thing) {
        $($(this).children('div')[0]).show();
        $($(this).children('div')[1]).hide();
    });

    // faq accordion
    $('.accordion-toggle').click(function () {
        
        var thisClass = $(this);
        if ($('.accordion-body').hasClass('in')) {
            thisClass.removeClass('collapsed');
            $('.accordion-toggle').addClass('collapsed');
        }
    });
});

// header sign up button

function popUpActionStep(url, height) {
    "use strict";
    //$.scrollTo($('#homeBg'), 200);
    $('#comparisonBoxHome, #darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#comparisonBoxHome').css("height", height);
    $.get(url, function (data) {
        $('#comparisonBoxHome').css('height', height);
        $('#comparisonBoxHome').html(data);
    });
};

function removeLayover() {
    "use strict";
    $('#comparisonBoxHome').html('');
    $('#comparisonBoxHome').hide();
    $('#darkBackground').hide();
}

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

// signin advisor dropdown
$(".ddPlain *").on("click", function(e){
    e.stopPropagation();  
});
function calculate() {
    // put the values into an array
    var arr = $.map($('input:checkbox:checked'), function(e, i) {
        return e.value;
    });
    // check to see if any are checked
    if ($('input[name=location]').is(':checked')) {
      $('.btnText').text(arr.join(', '));
      $('.ddUncheckAll').show();
    } else {
      $('.btnText').text('Please Select...');
      $('.ddUncheckAll').hide();
    }
    // as the dropdown gets bigger or smaller adjust
    var ddWid = $('.ddPlainBtn').width();
    $('.ddPlain').css('width', ddWid + 10);
}
calculate();
$('.ddPlain li').delegate('input:checkbox', 'change', calculate); 
$('.ddUncheckAll').click(function(){
  $('input[name=location]').removeAttr('checked');
  $('.btnText').text('Please Select...');
  $(this).hide();
  // as the dropdown gets bigger or smaller adjust
    var ddWid = $('.ddPlainBtn').width();
    $('.ddPlain').css('width', ddWid + 10);
});