/*jslint vars: true, white: false */
/*global $: false, window: false, document: false, initButtons: false, drawProjectionChart: false, location: false, formatMoneyFunc: false */
/* pop/destroy light box functions */
function popUpActionStep(url, height) {
    "use strict";
    $.scrollTo($('#body'), 200);
    $('#comparisonBox').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#comparisonBox').css("height", 'auto');
    //$('#comparisonBox').html('<iframe class="layoverIframe" height="' + height + '" src="' + url + '"></iframe>');
    $.get(url, function(data) {
        $('#comparisonBox').css('height', 'auto');
        $('#comparisonBox').html(data);
        //alert('Load was performed.');
    });
}

function replaceComparisonContents(url, height) {
    "use strict";
    $('#comparisonBox').html('');
    $('#comparisonBox').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#comparisonBox').css("height", height);
    $.get(url, function(data) {
        $('#comparisonBox').css('height', height);
        $('#comparisonBox').html(data);
    });
}

var formatMoneyFunc = function(c, d, t) {
    "use strict";
    var n = this,
            c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d == undefined ? "," : d,
            t = t == undefined ? "." : t,
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

Number.prototype.formatMoney = formatMoneyFunc;

function removeLayover() {
    "use strict";
    $('#profileBox').html('');
    $('#profileBox').hide();
    $('#comparisonBox').html('');
    $('#comparisonBox').hide();
    $('#darkBackground').hide();
    //$('#darkBackground').hide();
}


function popUpProfile(url, height) {
    "use strict";
    $.scrollTo($('#body'), 200);
    $('#profileBox').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#profileBox').css("height", "auto");
    $.get(url, function(data) {
        //$('#profileBox').css( 'height', height );
        $('#profileBox').html(data);
    });
}



/* simulation slider on breakdown tab */
function toggleSimOn(node) {
    "use strict";
    var sliderTop = $(node);
    sliderTop.find('.simToggleSlider').attr("style", "width: 50px;");
    sliderTop.find('.ui-slider-horizontal .ui-slider-range-min').css('border-style', 'solid');
    sliderTop.find('.ui-slider-horizontal .ui-slider-range-min').css('width', '55px');
    sliderTop.find('.toggleOnLabel').toggleClass('hdn');
    sliderTop.find('.toggleOffLabel').toggleClass('hdn');
    $('.simOffWrapper').hide();
    $('.tabSliderWrapper').show();
}

function updateSliderValues(event, ui) {
    "use strict";
    //var i = event.target.parentElement.id;
    var i = event.target.id;
    var valueElement = $('#' + i + 'Value');
    valueElement.text(ui.value.formatMoney(0, '.', ','));
}

function updateSliderValuesAndGraph(event, ui) {
    "use strict";
    if (event) {
        updateSliderValues(event, ui);
    }
    var totalValues = 0;
    $('.slider').each(function(index, element) {
        totalValues = totalValues + $(this).slider("option", "value");
    });

    $('.sliderAge').each(function(index, element) {
        totalValues = totalValues + ($(this).slider("option", "value") * 10000);
    });
    
    //alert( 'Total: ' + totalValues );
    var imageNum = ((totalValues / 6000000) * 20).toFixed(0);
    var simScore = ((totalValues / 6000000) * 1000).toFixed(0);
    if (simScore < 10) {
        $('#breakdownScore').css("left", "135px");
        $('#breakdownScore').css("letter-spacing", "0em");
    } else if (simScore < 100) {
        $('#breakdownScore').css("left", "120px");
        $('#breakdownScore').css("letter-spacing", "0em");
    } else if (simScore === 1000) {
        $('#breakdownScore').css("left", "85px");
        $('#breakdownScore').css("letter-spacing", "-0.05em");
    } else {
        $('#breakdownScore').css("left", "100px");
        $('#breakdownScore').css("letter-spacing", "0em");
    }
    $('#breakdownScore').text(simScore);
    $('#breakdownHorseshoe').attr("src", "images/horseshoes/variations/myscore/MyScoreHorseShoe" + imageNum + ".png");
    //alert( "imageNum: " + imageNum ); 
}

function resetCompareSliders() {
    "use strict";
    //$('.slider').slider("value", 100);
    //$('.sliderAge').slider("value", 22);
    //updateSliderValuesAndGraph();
}

function toggleSimOff(node) {
    "use strict";
    var sliderTop = $(node);
    sliderTop.find('.simToggleSlider').attr("style", "width: 68px; left: 110px");
    sliderTop.find('.ui-slider-horizontal .ui-slider-range-min').css('border-style', 'none');
    sliderTop.find('.ui-slider-horizontal .ui-slider-range-min').css('width', '0px');
    sliderTop.find('.toggleOnLabel').toggleClass('hdn');
    sliderTop.find('.toggleOffLabel').toggleClass('hdn');
    $('.simOffWrapper').show();
    $('.tabSliderWrapper').hide();
    resetCompareSliders();
}

/* global function for initializing sliders */
function initSliders() {
    "use strict";
    /*$(".slider").slider({ // commented out from this common area, used it in break.js
     range: "min",
     value: 650000,
     min: 0,
     max: 1000000,
     step: 1000,
     slide: updateSliderValues,
     change: updateSliderValuesAndGraph
     });
     $(".sliderAge").slider({
     range: "min",
     value: 65,
     min: 21,
     max: 70,
     step: 1,
     slide: updateSliderValues,
     change: updateSliderValuesAndGraph
     });
     
     $(".simToggleSlider").slider({
     value: 20,
     min: 10,
     max: 20,
     step: 10,
     range: "min",
     slide: function(event, ui) {
     if (ui.value === 20) {
     toggleSimOn(ui.handle.parentNode.parentNode);
     } else {
     toggleSimOff(ui.handle.parentNode.parentNode);
     }
     },
     stop: function(event, ui) {
     if (ui.value === 20) {
     $(ui.handle.parentNode.parentNode).find('.ui-slider-horizontal .ui-slider-range-min').css('width', '55px');
     }
     }
     });
     
     $('.toggleOnLabel').click(function(event) {
     event.stopPropagation();
     $(this).siblings(".simToggleSlider").slider("option", "value", 10);
     toggleSimOff(this.parentNode);
     });
     
     $('.toggleOffLabel').click(function(event) {
     event.stopPropagation();
     $(this).siblings(".simToggleSlider").slider("option", "value", 20);
     toggleSimOn(this.parentNode);
     });
     
     $('.ui-slider-handle').hover(function() {
     $(this).css("outline-style", "none");
     }, function() {
     $(this).css("outline-style", "none");
     });
     
     $('.simulationToggle .ui-slider-horizontal .ui-slider-range-min').css('width', '55px');
     */
}

//update step 2 slider values

function step2SliderUpdate(event, ui) {
    "use strict";
    var sliderId = event.target.id;
    $('#' + sliderId + "Value").val(ui.value.formatMoney(0, '.', ','));
}

/* global function for initializing sliders */
function initSliders2() {
    "use strict";
    $(".sliderStep2").slider({
        range: "min",
        value: 250000,
        min: 0,
        max: 1000000,
        step: 1000,
        slide: step2SliderUpdate,
        change: step2SliderUpdate
    });
    $('.sliderStep2Value').change(function() {
        var thisId = this.id;
        var value = this.value;
        $('#' + thisId.substring(0, 7)).slider("value", value);
    });
}

function initArrows() {
    "use strict";
    /* open-close arrows for advisor permissions box */
    $('.arw-wrapper').click(function(elem) {
        
        $(this).siblings('.accSec').toggleClass('hdn');
        $(this).find('.arw').toggleClass("openArrow").toggleClass("closeArrow");
    });
}


/* on doc load */

$(function() {
    "use strict";
    $(window).resize(function() {

    });
    // Stick the #nav to the top of the window
    var nav = $('#navWrap'),
            body = $('#mainBody'),
            $w = $(window);
    $w.scroll(function() {
        var doc = document.documentElement,
                body = document.body,
                left = ((doc && doc.scrollLeft) || (body && body.scrollLeft) || 0),
                top = ((doc && doc.scrollTop) || (body && body.scrollTop) || 0),
                Xoffset = -1 * left;
        nav.css({
            left: Xoffset
        });
    });
    /* gnav rollovers */

    $('nav li').hover(function() {
        if ($(this).hasClass('gnavButton')) {
            $(this).addClass('hover');
            $(this).addClass('reverseShadowBox');
        }
    }, function() {
        if ($(this).hasClass('gnavButton')) {
            $(this).removeClass('hover');
            $(this).removeClass('reverseShadowBox');
        }
    });
    $('nav.white li').hover(function() {
        if ($(this).hasClass('gnavButton')) {
            $(this).addClass('hover');
            $(this).addClass('reverseShadowBoxLight');
        }
    }, function() {
        $(this).removeClass('hover');
        $(this).removeClass('reverseShadowBoxLight');
    });
    /* deprecated? */
    $('.simpleButton').hover(function() {
        $(this).toggleClass('simpleButtonGradient simpleButtonGradientReverse');
    }, function() {
        $(this).toggleClass('simpleButtonGradient simpleButtonGradientReverse');
    });
    function selectTab(tabId) {
        $('.selected').removeClass('selected');
        $('#' + tabId).addClass('selected');
        //var tabId = tab.id,
        var tabNum = tabId.substring(4);
        if ($('#' + tabId).is('div.tabBox ul.tabs li')) {
            $('.tab-content').addClass("hdn");
            $('#tab-content-' + tabNum).removeClass("hdn");
        } else if ($('#' + tabId).is('div.mtabBox ul.tabs li')) {
            $('.mtab-content').addClass("hdn");
            $('#m-tab-content' + tabNum).removeClass("hdn");
        }
    }

    /* tab selection - modal and regular */
    $('div.tabBox ul.tabs li, div.mtabBox ul.tabs li').live("click", function() {
        selectTab(this.id);
        /** see funciton above **/

        /*$('.selected').removeClass('selected');
         $(this).addClass('selected');
         
         var tabId = this.id,
         tabNum = tabId.substring(4);
         
         if ($(this).is('div.tabBox ul.tabs li')){
         $('.tab-content').addClass("hdn");
         $('#tab-content-' + tabNum).removeClass("hdn");
         } else if ($(this).is('div.mtabBox ul.tabs li')){
         $('.mtab-content').addClass("hdn");
         $('#m-tab-content-' + tabNum).removeClass("hdn");
         }*/
    });
    initSliders();
    initArrows();
    initButtons();
    resetCompareSliders();
    /* page load behaviors */
    if (location.hash === '#created') {
        //popup lightbox with iframe for account created
        popUpActionStep('accountCreated.html', 'auto');
    } else if (location.hash === '#breakdown') {
        $('#tab-4').click();
    } else if (location.hash === '#projection') {
        $('#tab-2').click();
    } else if (location.hash === '#comparison') {
        $('#tab-3').click();
    } else if (location.hash === '#actionOverlay') {
        popUpActionStep('actionOverlay1.html', 'auto');
    } else if (location.hash === '#actionOverlay2') {
        popUpActionStep('actionOverlay2.html', 'auto');
    } else if (location.hash === '#about') {
        $.scrollTo($('#aboutFlexScore'), 800);
    } else if (location.hash === '#how') {
        $.scrollTo($('#howItWorks_land'), 800);
    } else if (location.hash === '#security') {
        $.scrollTo($('#security_land'), 800);
    } else if (location.hash === '#step2') {
        popUpActionStep('step2.html', 'auto');
    } else if (location.hash === '#estimatedScore') {
        popUpActionStep('estimatedScore.html', 'auto');
    } else if (location.hash === '#account') {
        popUpActionStep('account_notifications.html', 660);
    } else if (location.hash === '#advisors') {
        popUpActionStep('my_advisors.html', 'auto');
    } else if (location.hash === '#profilePhoto') {
        popUpActionStep('profile_photo.html', 520);
    } else if (location.hash === '#additionalQuestions') {
        popUpProfile('assets_account_additional_questions.html', 900);
    }



    /* draggable/droppable baseballcards */
    $(".draggable").draggable({
        opacity: 0.9,
        helper: "clone"
    });
    $(".droppable").droppable({
        accept: ".draggable",
        activeClass: "ui-state-hover",
        hoverClass: "ui-state-active",
        drop: function(event, ui) {
            $('.dropHint').hide();
            drawProjectionChart(true);
        }
    });
    $(".notReallyDroppable").droppable({
        accept: ".draggable",
        activeClass: "ui-state-hover",
        hoverClass: "ui-state-active",
        drop: function(event, ui) {
            selectTab('tab-2');
            $('.dropHint').hide();
            drawProjectionChart(true);
        }
    });
    /*var body = $('#body');*/
    var gnavWrapper = $('#navWrap');
    $(window).scroll(function(e) {
        if (gnavWrapper.offset().top !== 0) {
            if (!gnavWrapper.hasClass('gnavShadow')) {
                gnavWrapper.addClass('gnavShadow');
            }
        } else {
            gnavWrapper.removeClass('gnavShadow');
        }
    });
    /* faq page accordion */
    $('.faqArrow').each(function(i) {
        if ($(this).hasClass('on')) {
            $(this).parent('.sectionHeader').removeClass('allRound');
        } else {
            $(this).parent('.sectionHeader').addClass('allRound');
        }
    });
    // upon click
    $('.faqText, .faqArrow').on("click", function() {
        // initial when clicked
        $('.baseballCardWrapper').removeClass('on', 500);
        $('.faqArrow').removeClass('on', 50);
        $('.sectionHeader').addClass('allRound');
        // arrow movement
        if ($(this).is('.faqArrow')) {
            $(this).toggleClass('on', 50);
        } else {
            $(this).prev('.faqArrow').toggleClass('on', 50);
        }
        // open/close
        $(this).parent('.sectionHeader').removeClass('allRound').next('.baseballCardWrapper').toggleClass('on', 500);
    });
    //actionsteps hide/show

    $('#moreActionSteps').click(function() {
        $('#asLine2').show();
        $('#moreActionSteps').hide();
        $('#fewerActionSteps').show();
    });
    //actionsteps hide/show
    $('#fewerActionSteps').click(function() {
        $('#asLine2').hide();
        $('#moreActionSteps').show();
        $('#fewerActionSteps').hide();
    });
    $('#fewerActionSteps').hide();
    // account created security questions dropdown 
    $('.openCloseArrow').live("click", function(e) {
        e.preventDefault();
        $(this).toggleClass("openArrow");
        $(this).toggleClass("closeArrow");
        $(this).parent().next('.collapse').toggleClass('in', 500);
    });
    // info bubbles toggle
    $('.infoTip').live("click", function() {
        $('.infoTip').removeClass('on');
        $(this).toggleClass('on');
    });
    $('#body').click(function() {
        $('.infoTip').removeClass('on');
    });
});