if (typeof (serverSess) == 'undefined') {
    serverSess = 'sess';
}

var is_chrome = navigator.userAgent.indexOf('Chrome') > -1;
var is_explorer = navigator.userAgent.indexOf('MSIE') > -1;
var is_firefox = navigator.userAgent.indexOf('Firefox') > -1;
var is_safari = navigator.userAgent.indexOf("Safari") > -1;
if ((is_chrome) && (is_safari)) {
    is_safari = false;
}
if (is_safari) {
    $("h3").css('font-size', "1.15em");
}
var financialData = {
    "cash": "",
    "debts": "",
    "insurance": "",
    "investments": "",
    "goals": "",
    "accountsdownloading": true
};
var goalSnapshot = null;
var forceUserNotifications = false;
var globalCanvas = null;
var previousSearchFI = '';
var reportData = '';
var videokey = '';
var calculateGoals = true;
var currentMyScoreTab = 'tab-1';
var needsToClose = true;
var riskCurrentIndex = 0;
var riskCurrentLength = 0;
var riskCurrentIntervalId = '';
var riskAjaxInProcess = false;
var riskCurrentVariables = {};
var accountCurrentIndex = 0;
var accountCurrentLength = 0;
var accountCurrentIntervalId = '';
var accountAjaxInProcess = false;
var accountCurrentVariables = {};
var accountIndex = 1;
var goalIndex = 1;
var updateCollapse = true;
var tempid = 1139101821;  // Used in accountsignin when the cancel button is clicked to hide the current search box and load a fresh one.
var giveawayId = ''; // Used to load the giveaway message on logged in pages, shown once per browser session
/* Used in storing current breakdown values */
var breakage = 0;
var breakgoal = 0;
var breaksavings = 0;
var breakassets = 0;
var breakdebts = 0;
var breakliving = 0;
var breakscore = 0;
var breakdownSimulationOn = false;
var mimic = true; // for break down tab
/**/
/* Used to keep track of the current action step, and if an asset/debt/insurance is loaded via an action step */
var currentactionstepid = 0;
var throughActionStep = false;
var currentActionEvent = '';
var loadfakeactionstep = false;
var currentState = '';
var currentOpenField = '';
var currentOpenType = '';
/**/
var signupmode = 'token';  // Legacy Request Token Flow. Do Not Remove.
var currentNotificationKey = ''; // Used to open up pending accounts array from notification section
var date = new Date(); // Current Date
var defaultAge = 18; // Used for Logged In user => min age is 18 for spouse or user signed in.
var oldestAge = 85; // Used for DOB => max is 85 years from now
var furthestAge = 60; // Used for goals => max is 60 years from now
var lcSummary = ""; // Stores the current learning center / blog object on summary page, for faster page loads.
var currentErrorMsg = "";  // Stores the error message when transferring from homepage to login page
var currentErrorType = ""; // Stores the error type when transferring from homepage to login page
var goals = []; // Current goals
var sliderDefaultValue = 5; // Risk default value
var currentSearchLeft = []; // Search results for cashedge Left Side
var currentSearchRight = []; // Search results for cashedge Right Side
var allowToggle = true; // Allow toggling of users between on/off states on assets/debts/insurance
var currentScore = null; // storing the current score
/* Timeout Dialog properties */
var defaultTimeoutPeriod = 900; //Set this to 30 seconds for testing
var timeoutPeriod = defaultTimeoutPeriod;
var classifyData = [];
var timeoutDialogShown = false;
/**/
/* user's profile data */
var userData = null;
var profileUserData = {
    "firstname": "",
    "lastname": "",
    "zip": "",
    "needsUpdate": true
}
var socket = null;
var riskdata = null; // Storing Risk data
var userPreferences = {
    "user_id":"",
    "connectAccountPreference":"",
    "debtsPreference":"",
    "insurancePreference":"",
    "debtData":"",
    "insuranceData":"",
    "debtAdded":"",
    "insuranceAdded":""
}


// Pop up the action step
function popUpActionStep(div, content, height) {
    "use strict";
    $.scrollTo($('#body'), 200);
    $('#comparisonBox').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#comparisonBox').css("height", 'auto');
}

// Close all dialogs
function removeLayover() {
    "use strict";
    $('#profileContents').html('');
    $('#profileBox').hide();
    $('#notificationContents').html('');
    $('#createnewclientContents').html('');
    $('#createnewASDescContents').html('');
    $('#createnewASProductBoxContents').html('');
    $('#manageCredentialsContents').hide();
    $('#deleteclientContents').html('');
    $('#notificationBox').hide();
    $('#createnewclientBox').hide();
    $('#createnewASDescBox').hide();
    $('#createnewASProductBox').hide();
    $('#uploadnewclientlistBox').hide();
    $('#clientfinancialsummaryBox').hide();
    $('#deleteclientBox').hide();
    $('#comparisonBox').html('');
    $('#comparisonBox').hide();
    $('#myAdvisorContents').html('');
    $('#myAdvisorBox').hide();
    $('#darkBackground').hide();
    $('#consumeremailverify').hide();
    $('#verificationContent').show();
    $('#verificationResults').hide();
    $('#sendEmailVerification').show();
    $('#iAmDone').hide();
    
    if (!breakdownSimulationOn) {
        $(".breakToggleOnLabel").click();
    }
}

// Used to place the error message above the input element
function PositionErrorMessage(element, error)
{
    var elementwidth = $(element).width();
    var errorwidth = $(error).width();
    var elementheight = $(element).height();
    var errorheight = $(error).height();
    var elementoffset = $(element).offset();
    var erroroffset = $(error).offset();
    var left = 10;
    var top = 0;
    if (error == "#termscheckbubble")
    {
        left = 14;
        top = 5;
    }
    if (typeof (elementoffset) != "undefined") {
        $(error).offset({
            top: elementoffset.top - errorheight - 18 - top,
            left: (elementwidth - errorwidth) / 2 + elementoffset.left - left
        });
    }
}

// Based on Type of Asset/Debt/Insurance, we return the user friendly default name
var GetDefaultNameForType = function(a, t) {
    var key = 'Other';
    switch (a)
    {
        case 'LIFE':
            key = 'Life Insurance';
            break;
        case 'LONG':
            key = 'Long Term Care Insurance';
            break;
        case 'HOME':
            key = "Home Owner's / Renter Insurance";
            break;
        case 'VEHI':
            if (t == 'insurance')
                key = 'Vehicle Insurance';
            else
                key = 'Vehicle';
            break;
        case 'DISA':
            key = 'Disability Insurance';
            break;
        case 'UMBR':
            key = 'Umbrella Insurance';
            break;
        case 'HEAL':
            key = 'Health Insurance';
            break;
        case 'CC':
            key = 'Credit Card';
            break;
        case 'MORT':
            key = 'Mortgage';
            break;
        case 'LOAN':
            key = 'Loan';
            break;
        case 'ALOAN':
            key = 'Auto Loan';
            break;
        case 'BLOAN':
            key = 'Business Loan';
            break;
        case 'SLOAN':
            key = 'Student Loan';
            break;
        case 'BANK':
            key = 'Bank Account';
            break;
        case 'IRA':
            key = 'IRA';
            break;
        case 'CR':
            key = 'Company Retirement Plan';
            break;
        case 'BROK':
            key = 'Brokerage';
            break;
        case 'EDUC':
            key = 'Educational Account';
            break;
        case 'PROP':
            key = 'Property';
            break;
        case 'PENS':
            key = 'Pension';
            break;
        case 'SS':
            key = 'Social Security';
            break;
        case 'BUSI':
            key = 'Business';
            break;
        case 'OTHE':
            if (t == 'insurance')
                key = 'Umbrella Insurance';
            else if (t == 'debts')
                key = 'Loan';
            else
                key = 'Other';
            break;
        default:
            key = 'Other';
    }
    return key;
}

// Based on Type of Asset/Debt/Insurance, we return the user friendly default name
var GetDefaultNameForGoalType = function(a, t) {
    var key = 'Other';
    switch (a)
    {
        case 'RETIREMENT':
            key = 'Retirement Goal';
            break;
        case 'COLLEGE':
            key = 'Save For College';
            break;
        case 'CUSTOM':
            key = 'Custom';
            break;
        case 'DEBT':
            key = 'Pay Off Debt';
            break;
        case 'HOUSE':
            key = 'Buy a House';
            break;
        default:
            key = 'Custom';
    }
    return key;
}

// Based on Type of Goals, we return the key used in the UI found under views/score/goals/
var calculateGoalKey = function(a, t) {
    var key = 'custom';
    switch (a)
    {
        case 'RETIREMENT':
            key = 'retirement';
            break;
        case 'HOUSE':
            key = 'house';
            break;
        case 'COLLEGE':
            key = 'college';
            break;
        case 'DEBTS':
            key = 'debts';
            break;
        case 'DEBT':
            key = 'debt';
            break;
        case 'CUSTOM':
            key = 'custom';
            break;
        default:
            key = 'custom';
    }
    return key;
}

// Based on Type of Asset/Debt/Insurance, we return the key used in the UI found under views/score/accounts/
var calculateKey = function(a, t) {
    var key = 'other';
    switch (a)
    {
        case 'LIFE':
            key = 'lifeinsurance';
            break;
        case 'LONG':
            key = 'longtermcareinsurance';
            break;
        case 'HOME':
            key = 'homeinsurance';
            break;
        case 'VEHI':
            if (t == 'insurance')
                key = 'vehicleinsurance';
            else
                key = 'vehicle';
            break;
        case 'DISA':
            key = 'disabilityinsurance';
            break;
        case 'UMBR':
            key = 'umbrellainsurance';
            break;
        case 'HEAL':
            key = 'healthinsurance';
            break;
        case 'CC':
            key = 'creditcard';
            break;
        case 'MORT':
            key = 'mortgage';
            break;
        case 'LOAN':
            key = 'loan';
            break;
        case 'ALOAN':
            key = 'loan';
            break;
        case 'BLOAN':
            key = 'loan';
            break;
        case 'SLOAN':
            key = 'loan';
            break;
        case 'BANK':
            key = 'bank';
            break;
        case 'IRA':
            key = 'ira';
            break;
        case 'CR':
            key = 'companyretirementplan';
            break;
        case 'BROK':
            key = 'brokerage';
            break;
        case 'EDUC':
            key = 'educationalaccount';
            break;
        case 'PROP':
            key = 'property';
            break;
        case 'PENS':
            key = 'pension';
            break;
        case 'SS':
            key = 'socialsecurity';
            break;
        case 'BUSI':
            key = 'business';
            break;
        case 'OTHE':
            if (t == 'insurance')
                key = 'umbrellainsurance';
            else if (t == 'debts')
                key = 'loan';
            else
                key = 'other';
            break;
        default:
            key = 'other';
    }
    return key;
}

var calculateTitleKey = function(a, t) {
    var key = 'other';
    switch (a)
    {
        case 'LIFE':
            key = 'LifeInsurance';
            break;
        case 'LONG':
            key = 'LongTermCareInsurance';
            break;
        case 'HOME':
            key = 'HomeInsurance';
            break;
        case 'VEHI':
            if (t == 'insurance')
                key = 'VehicleInsurance';
            else
                key = 'Vehicle';
            break;
        case 'DISA':
            key = 'DisabilityInsurance';
            break;
        case 'UMBR':
            key = 'UmbrellaInsurance';
            break;
        case 'HEAL':
            key = 'HealthInsurance';
            break;
        case 'CC':
            key = 'CreditCard';
            break;
        case 'MORT':
            key = 'Mortgage';
            break;
        case 'LOAN':
            key = 'Loan';
            break;
        case 'ALOAN':
            key = 'Loan';
            break;
        case 'BLOAN':
            key = 'Loan';
            break;
        case 'SLOAN':
            key = 'Loan';
            break;
        case 'BANK':
            key = 'Bank';
            break;
        case 'IRA':
            key = 'IRA';
            break;
        case 'CR':
            key = 'CompanyRetirementPlan';
            break;
        case 'BROK':
            key = 'Brokerage';
            break;
        case 'EDUC':
            key = 'Educational';
            break;
        case 'PROP':
            key = 'Property';
            break;
        case 'PENS':
            key = 'Pension';
            break;
        case 'SS':
            key = 'SocialSecurity';
            break;
        case 'BUSI':
            key = 'Business';
            break;
        case 'OTHE':
            if (t == 'insurance')
                key = 'UmbrellaInsurance';
            else if (t == 'debts')
                key = 'Loan';
            else
                key = 'Other';
            break;
        default:
            key = 'Other';
    }
    return key;
}

// Check the key
var isValidKey = function(a) {
    var key = false;
    switch (a)
    {
        case 'lifeinsurance':
            key = true;
            break;
        case 'longtermcareinsurance':
            key = true;
            break;
        case 'homeinsurance':
            key = true;
            break;
        case 'vehicleinsurance':
            key = true;
            break;
        case 'vehicle':
            key = true;
            break;
        case 'disabilityinsurance':
            key = true;
            break;
        case 'umbrellainsurance':
            key = true;
            break;
        case 'healthinsurance':
            key = true;
            break;
        case 'creditcard':
            key = true;
            break;
        case 'mortgage':
            key = true;
            break;
        case 'loan':
            key = true;
            break;
        case 'bank':
            key = true;
            break;
        case 'ira':
            key = true;
            break;
        case 'companyretirementplan':
            key = true;
            break;
        case 'brokerage':
            key = true;
            break;
        case 'educationalaccount':
            key = true;
            break;
        case 'property':
            key = true;
            break;
        case 'pension':
            key = true;
            break;
        case 'socialsecurity':
            key = true;
            break;
        case 'business':
            key = true;
            break;
        case 'umbrellainsurance':
            key = true;
            break;
        case 'other':
            key = true;
            break;
    }
    return key;
}

// Return the type of account
var returnInternalKey = function(a) {
    var key = 'other';
    switch (a)
    {
        case 'LIFE':
        case 'LONG':
        case 'HOME':
        case 'DISA':
        case 'UMBR':
        case 'HEAL':
            key = 'Insurance';
            break;
        case 'VEHI':
            key = 'Insurance-Assets';
            break;
        case 'MORT':
        case 'CC':
        case 'LOAN':
        case 'ALOAN':
        case 'BLOAN':
        case 'SLOAN':
            key = 'Debts';
            break;
        case 'BANK':
        case 'IRA':
        case 'CR':
        case 'BROK':
        case 'EDUC':
        case 'PROP':
        case 'PENS':
        case 'SS':
        case 'BUSI':
            key = 'Assets';
            break;
        case 'OTHE':
        default:
            key = 'Debts-Insurance-Assets';
            break;
    }
    return key;
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


/* on slider */
function toggleSimOn(node) {
    "use strict";
    var sliderTop = $(node);
    sliderTop.find('.simToggleSlider').attr("style", "width: 50px;");
    sliderTop.find('.ui-slider-horizontal .ui-slider-range-min').css('border-style', 'solid');
    sliderTop.find('.ui-slider-horizontal .ui-slider-range-min').css('width', '55px');
    sliderTop.find('.toggleOnLabel').removeClass('hdn');
    sliderTop.find('.toggleOffLabel').addClass('hdn');
    $('.simOffWrapper').hide();
    $('.tabSliderWrapper').show();
    var name = sliderTop.find('.toggleOnLabel')[0].id;
    var index = name.indexOf('toggleOnLabel');
    var idKey = name.substring(0, index);
    var typeKey = name.substring(index + 13, name.length);
    if (allowToggle) {
        $("#" + idKey + "PermissionToggleButton" + typeKey).click();
    }
}

/* off slider */
function toggleSimOff(node) {
    "use strict";
    var sliderTop = $(node);
    sliderTop.find('.simToggleSlider').attr("style", "width: 68px; left: 110px");
    sliderTop.find('.ui-slider-horizontal .ui-slider-range-min').css('border-style', 'none');
    sliderTop.find('.ui-slider-horizontal .ui-slider-range-min').css('width', '0px');
    sliderTop.find('.toggleOnLabel').addClass('hdn');
    sliderTop.find('.toggleOffLabel').removeClass('hdn');
    $('.simOffWrapper').show();
    $('.tabSliderWrapper').hide();
    resetCompareSliders();
    var name = sliderTop.find('.toggleOffLabel')[0].id;
    var index = name.indexOf('toggleOffLabel');
    var idKey = name.substring(0, index);
    var typeKey = name.substring(index + 14, name.length);
    if (allowToggle)
        $("#" + idKey + "PermissionToggleButton" + typeKey).click();
}

// Placeholder fix for IE
$.fn.placeholder = function() {
    if (typeof document.createElement("input").placeholder == 'undefined') {
        $('.specialplaceholder').focus(function() {
            var input = $(this);
            if (input.val() == input.attr('placeholder') && input.hasClass('specialplaceholder')) {
                input.val('');
                input.removeClass('placeholder');
                if (input.data('type') == 'password') {
                    input.get(0).type = 'password';
                }
            }
        }).blur(function() {
            var input = $(this);
            if ((input.val() == '' || input.val() == input.attr('placeholder')) && input.hasClass('specialplaceholder')) {
                if (input.attr('type') == 'password') {
                    input.data('type', 'password').get(0).type = 'text';
                }
                input.addClass('placeholder');
                input.val(input.attr('placeholder'));
            }
        }).blur().parents('form').submit(function() {
            $(this).find('[placeholder]').each(function() {
                var input = $(this);
                if (input.val() == input.attr('placeholder') && input.hasClass('specialplaceholder')) {
                    input.val('');
                }
            })
        });
    }
};



//initialize function
function init(sliders) {

    if (is_safari) {
        $("h3").css('font-size', "1.15em");
    }

    $(".accomplishGoal").unbind().change(function() {
        var name = this.id;
        name = name.substr(0, name.indexOf('Goal'));
        $(".accomplishGoalDiv").hide();
        $("#" + name + "Div").show();
    });

    $(".whichyouhave").unbind().click(function() {
        $(this).toggleClass("active");
    });

    $(".maritalStatus").unbind().click(function() {
        $(".maritalStatus").removeClass("active");
        $(this).addClass("active");
        if ($(this).hasClass("spouse"))
        {
            var maritalStatus = $('button.maritalStatus.active').text();
            if (maritalStatus == "Married")
                $("#spousespan").html("Spouse");
            else
                $("#spousespan").html("Partner");
            $("#spouseInfo").removeClass("hdn");
        }
        else
        {
            $("#spouseInfo").addClass("hdn");
        }
    });

    $("#NumChildren").unbind().change(function() {
        var num = parseInt($(this).val());
        for (var i = 1; i <= num; i++)
        {
            $("#child" + i + "AgeQuestion").removeClass("hdn");
        }
        for (var i = num + 1; i <= 8; i++)
        {
            $("#child" + i + "AgeQuestion").addClass("hdn");
        }
    });
    // Allow only certain values
    $(".dollaramount").unbind().keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        keycode = parseInt(keycode);
        if (keycode == 13) {
            event.preventDefault();
            $(this).blur();
            $(this).parents(".accordion-body").find(".lsPillBtnGreen").click();
        }
        else if ((keycode < 48 || keycode > 57) && event.keyCode != 37 && (keycode != 45 || (keycode == 45 && this.selectionStart > 0) || (keycode == 45 && this.selectionStart == 0 && typeof (this.value) != 'undefined' && this.value[0] == '-')) && (keycode != 39 || event.which == 39) && keycode != 44 && keycode != 46 && keycode != 8 && keycode != 9)
        {
            event.preventDefault();
        }
    });
    // Format to 2 decimal places
    $(".dollaramount").blur(function(event) {
        var amount = $(this).val();
        if (amount != "")
        {
            amount = parseFloat(amount.replace(/,/g, '')).toFixed(2);
            if (amount > 999999999999.99)
                amount = "999999999999.99";
            $(this).val(commaSeparateNumber(amount));
        }
        $(this).parents(".accordion-body").find(".calculateGoalButton").click();
        return false;
    });
    // Allow only certain values
    $(".numberamount").unbind().keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        keycode = parseInt(keycode);
        if (keycode == 13) {
            event.preventDefault();
            $(this).blur();
            $(this).parents(".accordion-body").find(".lsPillBtnGreen").click();
        }
        else if ((keycode < 48 || keycode > 57) && event.keyCode != 37 && (keycode != 39 || event.which == 39) && keycode != 44 && keycode != 8 && keycode != 9)
        {
            event.preventDefault();
        }
    });
    // Format to 0 decimal placers
    $(".numberamount").blur(function(event) {
        var amount = $(this).val();
        if (amount != "")
        {
            amount = parseInt(amount.replace(/,/g, ''));
            if (amount > 999999999999)
                amount = "999999999999";
            $(this).val(commaSeparateNumber(amount, 0));
        }
        else if ($(event.target).hasClass('assumptionsNumber')) {
            $(this).val("0");
        }
        $(this).parents(".accordion-body").find(".calculateGoalButton").click();
        return false;
    });
    // Allow only certain values
    $(".percent").unbind().keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        keycode = parseInt(keycode);
        if (keycode == 13) {
            event.preventDefault();
            $(this).blur();
            $(this).parents(".accordion-body").find(".lsPillBtnGreen").click();
        }
        else if ((keycode < 48 || keycode > 57) && event.keyCode != 37 && (keycode != 39 || event.which == 39) && keycode != 46 && keycode != 8 && keycode != 9)
        {
            event.preventDefault();
        }
    });
    // Max of 100%
    $(".percent").blur(function(event) {
        var amount = $(this).val();
        if (amount != "")
        {
            amount = parseFloat(amount.replace(/,/g, '')).toFixed(2);
            if (amount > 100)
                amount = "100.00";
            $(this).val(amount);
        }
        $(this).parents(".accordion-body").find(".calculateGoalButton").click();
        return false;
    });
    // Allow only certain values
    $(".profileAccPercent").unbind().keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        keycode = parseInt(keycode);
        if (keycode == 13) {
            event.preventDefault();
            $(this).blur();
            $(this).parents(".accordion-body").find(".lsPillBtnGreen").click();
        }
        else if ((keycode < 48 || keycode > 57) && event.keyCode != 37 && (keycode != 39 || event.which == 39) && keycode != 46 && keycode != 8 && keycode != 9)
        {
            event.preventDefault();
        }
    });
    // Max of 100%
    $(".profileAccPercent").blur(function(event) {
        var amount = $(this).val();
        if (amount != "")
        {
            amount = parseFloat(amount.replace(/,/g, '')).toFixed(1);
            if (amount > 100)
                amount = "100.0";
            $(this).val(amount);
        }
        else if ($(event.target).hasClass('assumptionsPercent')) {
            $(this).val("0.0");
        }

        $(this).parents(".accordion-body").find(".calculateGoalButton").click();
        return false;
    });
    // Click on the submit button if enter key is clicked
    $(".profileAccName").unbind().keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        keycode = parseInt(keycode);
        if (keycode == 13) {
            event.preventDefault();
            $(this).blur();
            $(this).parents(".accordion-body").find(".lsPillBtnGreen").click();
        }
    });
    $(".goalName").blur(function(event) {
        $(this).parents(".accordion-body").find(".saveNeeded").val(true);
        return false;
    });
    $(".hoverGoals").unbind().hover(function(event) {
        $(".hoverGoalsBubble").css('display', 'block');
    }, function() {
        $(".hoverGoalsBubble").css('display', 'none');
    });
    // Click on the submit button if enter key is clicked
    $(".homeInput").keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        keycode = parseInt(keycode);
        if (keycode == 13) {
            event.preventDefault();
            $(this).blur();
            $("#bottomSignup").click();
        }
    });

    // Hide Popup
    $(".cancelPopup").click(function() {
        $('#comparisonBoxHome, #darkBackground').hide();
        $("#signIn").toggleClass("hdn");
        return false;
    });

    // Load Signup Dialog
    $(".signupPopup").click(function() {
        if ((this.id == 'signupPopupButton' || this.id == 'joinPopupButton') && $("body").width() < 500)
        {
            var params = "";
            if (currentErrorType != "")
            {
                params = "?error=" + currentErrorType + "&msg=" + currentErrorMsg;
            }
            window.location = baseUrl + "/signup" + encodeURI(params);
            return false;
        }
        else if ($("body").width() < 500)
        {
            window.location = baseUrl + "/login";
            return false;
        }
        $("#noAccessToken").click();
        var offset = $("#globalNavLoggedOut").offset();
        $('#comparisonBoxHome').attr('style', 'top: ' + (offset.top + 50) + 'px');
        $('#comparisonBoxHome, #darkBackground').show();
        $('#comparisonBoxHome').css("height", "auto");
        $('#darkBackground').fadeTo("fast", 0.6);
        $(".dialogContent").addClass("hdn");
        $("#signIn").removeClass("hdn");

        if (this.id == 'signupPopupButton' || this.id == 'joinPopupButton')
        {
            $("#signupLink").addClass("active");
            $("#signinLink").removeClass("active");
            $("#signupUser").addClass("active in");
            $("#loginUser").removeClass("active in");
            $("#email").val("");
            $("#email2").val("");
            $("#password1").val("");
            $("#password2").val("");
            $("#advsignemail").val("");
            $("#advsignemail2").val("");
            $("#advsignpassword1").val("");
            $("#advsignpassword2").val("");
            $("#advusername").val("");
            $("#advpassword").val("");
            $("#token").val("");
            $("#termscheck").prop('checked', false);
            $("#termscheckadv").prop('checked', false);

            $("#email").blur();
            $("#email2").blur();
            $("#password1").blur();
            $("#password2").blur();
            $(".controls").removeClass('error');
        }
        else
        {
            $("#signinLink").addClass("active");
            $("#signupLink").removeClass("active");
            $("#loginUser").addClass("active in");
            $("#signupUser").removeClass("active in");
            $("#username").val("");
            $("#password").val("");
            $("#advsignemail").val("");
            $("#advsignemail2").val("");
            $("#advsignpassword1").val("");
            $("#advsignpassword2").val("");
            $("#advusername").val("");
            $("#advpassword").val("");
            $(".controls").removeClass('error');
            $("#username").blur();
            $("#password").blur();

        }
        return false;
    });

    $(".openDialogContent").click(function() {
        var offset = $("#globalNavLoggedOut").offset();
        $('#comparisonBoxHome').attr('style', 'display: block; height: auto; top: ' + (offset.top + 50) + 'px');
        $('#comparisonBoxHome, #darkBackground').show();
        $('#darkBackground').fadeTo("fast", 0.6);
        $('#comparisonBoxHome').css("height", "auto");
        $(".dialogContent").addClass("hdn");
        var name = this.id;
        name = name.substring(0, name.indexOf("Link"));
        $("#" + name).removeClass("hdn");
        return false;
    });

    $("#username").focus(function() {
        $('#usernamebubble').addClass("hdn");
        $("#usernamediv").removeClass('error');
    });

    $("#email, #email2").focus(function() {
        $('#emailbubble').addClass("hdn");
        $("#emaildiv").removeClass('error');
        $('#email2bubble').addClass("hdn");
        $("#email2div").removeClass('error');
        $('#password1bubble').addClass("hdn");
        $("#password1div").removeClass('error');
        $('#termscheckbubble').addClass("hdn");
        $("#termscheckdiv").removeClass('error');
        $('#tokenbubble').addClass("hdn");
        $("#tokendiv").removeClass('error');
    });

    $("#oldpassword").focus(function() {
        $('#password1bubble').addClass("hdn");
        $('#password2bubble').addClass("hdn");
        $('#oldpasswordbubble').addClass("hdn");
        $("#password1div").removeClass('error');
        $("#password2div").removeClass('error');
        $("#oldpassworddiv").removeClass('error');
    });

    $("#password1").focus(function() {
        $('#emailbubble').addClass("hdn");
        $("#emaildiv").removeClass('error');
        $('#email2bubble').addClass("hdn");
        $("#email2div").removeClass('error');
        $('#password1bubble').addClass("hdn");
        $('#password2bubble').addClass("hdn");
        $('#oldpasswordbubble').addClass("hdn");
        $("#oldpassworddiv").removeClass('error');
        $("#password1div").removeClass('error');
        $("#password2div").removeClass('error');
        $('#termscheckbubble').addClass("hdn");
        $("#termscheckdiv").removeClass('error');
        $('#tokenbubble').addClass("hdn");
        $("#tokendiv").removeClass('error');
    });

    $("#termscheck").click(function() {
        $('#emailbubble').addClass("hdn");
        $("#emaildiv").removeClass('error');
        $('#email2bubble').addClass("hdn");
        $("#email2div").removeClass('error');
        $('#password1bubble').addClass("hdn");
        $("#password1div").removeClass('error');
        $('#termscheckbubble').addClass("hdn");
        $("#termscheckdiv").removeClass('error');
        $('#tokenbubble').addClass("hdn");
        $("#tokendiv").removeClass('error');
    });

    $("#password2").focus(function() {
        $('#emailbubble').addClass("hdn");
        $("#emaildiv").removeClass('error');
        $('#email2bubble').addClass("hdn");
        $("#email2div").removeClass('error');
        $('#password1bubble').addClass("hdn");
        $('#password2bubble').addClass("hdn");
        $('#oldpasswordbubble').addClass("hdn");
        $("#oldpassworddiv").removeClass('error');
        $("#password1div").removeClass('error');
        $("#password2div").removeClass('error');
        $('#termscheckbubble').addClass("hdn");
        $("#termscheckdiv").removeClass('error');
        $('#tokenbubble').addClass("hdn");
        $("#tokendiv").removeClass('error');
    });

    $("#token").focus(function() {
        $('#emailbubble').addClass("hdn");
        $("#emaildiv").removeClass('error');
        $('#email2bubble').addClass("hdn");
        $("#email2div").removeClass('error');
        $('#password1bubble').addClass("hdn");
        $("#password1div").removeClass('error');
        $('#termscheckbubble').addClass("hdn");
        $("#termscheckdiv").removeClass('error');
        $('#tokenbubble').addClass("hdn");
        $("#tokendiv").removeClass('error');
    });

    $("#password").focus(function() {
        $('#usernamebubble').addClass("hdn");
        $("#usernamediv").removeClass('error');
    });

    $("#showSignUp").click(function() {
        $("#firstNameDiv").hide();
        $("#password1Div").show();
        $("#password2Div").show();
        $("#accessTokenDiv").show();
        $("#termscheckdiv").show();
        $("#signup").show();
        $("#submitEmail").hide();
        $("#accessDiv").hide();
        $("#noaccessDiv").show();
        return false;
    });

    $("#showPurpleSignUp").click(function() {
        $("#bottomFirstName").hide();
        $("#bottomPassword1").show();
        $("#bottomPassword2").show();
        $("#bottomToken").show();
        $("#bottomTermsCheckDiv").show();
        $("#bottomSignup").show();
        $("#submitBottomEmail").hide();
        $("#bottomAccessDiv").hide();
        $("#bottomNoaccessDiv").show();
        $("#bottomSignupTitle").html("Create Your Free Account");
        return false;
    });

    $("#termscond").click(function() {
        $('#termBubbless').addClass("hdn");
        $("#termtextdivs").removeClass('error');
    });

    //create an advisor account link
    $('.accountLink').click(function(event) {
        event.preventDefault();
        $("#hide_advisor").fadeOut(10);
        //$("#createadvisor").fadeOut(10);
        $("#newAdvisor").fadeIn(500);
        $("#step1body").fadeIn(500);
        $("#step1Submit").fadeIn(500);
        $('#step1header').fadeIn(500);
    });

    $("#designationOption").change(function() { //show the other textbox on click of other option.
        $("select option:selected").each(function() {
            if ($(this).val() == "Other") {
                $("#extra").show();
            } else if ($(this).val() != "Other") {
                $("#extra").hide();
            }
        });
    });

    $("#productAndServiceOption").change(function() { //show the other textbox on click of other option.
        $("select option:selected").each(function() {
            if ($(this).val() == "Other") {
                $("#extraprod").show();
            } else if ($(this).val() != "Other") {
                $("#extraprod").hide();
            }
        });
    });

    $('#advusername').focus(function() {
        $("#advusernamediv").removeClass("error");
    });
    //advisor sign up form
    $('#advsignemail, #advsignemail2, #advsignpassword1, #advsignpassword2, #termscheckadv').focus(function() {
        $("#emailadvdiv").removeClass("error");
        $("#email2advdiv").removeClass("error");
        $("#passwordadvidiv").removeClass("error");
        $("#termscheckadvdiv").removeClass("error");
    });
    //advisor firstname
    $('#firstname').focus(function() {
        $("#firstnamediv").removeClass("error");
    });
    //lastname
    $('#lastname').focus(function() {
        $("#lastnamediv").removeClass("error");
    });
    //states
    $('.ddPlainBtn').focus(function() {
        $("#statesdiv").removeClass("error");
    });
    //advisor type.
    $('.advType').focus(function() {
        $("#advTypediv").removeClass("error");

    });
    //firmname
    $('#firmname').focus(function() {
        $("#firmNamediv").removeClass("error");

    });
    //upload image
    $('#filename').focus(function() {
        $("#filenamediv").removeClass("error");

    });
    //Tell me about yourself
    $('#description').focus(function() {
        $("#descriptiontextdiv").removeClass("error");
    });
    //maxlength limit on textarea.
    maxLength = $("textarea#description").attr("maxlength");
    $("#txt-counter").html(maxLength + " Remaining");

    $("textarea#description").bind("keyup change", function() {
        checkMaxLength(this.id, maxLength);
    });

    function checkMaxLength(textareaID, maxLength) {
        currentLengthInTextarea = $("#" + textareaID).val().length;
        $('#txt-counter').text(parseInt(maxLength) - parseInt(currentLengthInTextarea) + ' Remaining');
        if (currentLengthInTextarea > (maxLength)) {
            //Trim the field current length over the maxlength.
            $("textarea#description").val($("textarea#description").val().slice(0, maxLength));
            $('#txt-counter').text(0);
        }
    }
    //designations
    $('.ddPlainBtn').focus(function() {
        $("#designationdiv").removeClass("error");
    });
    //product service
    $('.ddPlainBtn').focus(function() {
        $("#productdiv").removeClass("error");
    });
    //average balance
    $('#avg_bal').focus(function() {
        $("#avgAccountdiv").removeClass("error");
    });

    //minimum assest
    $('#min_assist').focus(function() {
        $("#minimumdiv").removeClass("error");
    });
    //how do you charge.
    $('.hourly').focus(function() {
        $("#howdodiv").removeClass("error");
    });
    //individual crd
    $('#crd').focus(function() {
        $("#individualCrddiv").removeClass("error");
    });
    //cancel button on advisor sign in
    $(".cancelAdvisorPopup").click(function() {
        removeLayover();
        $('#comparisonBoxHome, #darkBackground').hide();
        $('#signupAdvisorLink').removeClass("active");
        $('#advisorSignup').removeClass('in active');
        $("#advsignemail").val("");
        $("#advsignemail2").val("");
        $("#advsignpassword1").val("");
        $("#advsignpassword2").val("");
        $("#advusername").val("");
        $("#advpassword").val("");
        $("#termscheckadv").prop('checked', false);
        return false;
    });
    $("#cancelContinue").click(function() {
        $("#advsignemail").val("");
        $("#advsignemail2").val("");
        $("#advsignpassword1").val("");
        $("#advsignpassword2").val("");
        $("#advusername").val("");
        $("#advpassword").val("");
        $("#termscheckadv").prop('checked', false);
        return false;
    });

    //cancel button on advisor step 1 and 2
    $('.cancel').click(function(event) {
        event.preventDefault();
        $('#newAdvisor').fadeOut(10);
        $('#step2body').fadeOut(10);
        $('#step2header').fadeOut(10);
        $('#hide_advisor').fadeIn(500);
    });

    $('#myCarousel').on('slid', function(event) {
        $('.caro-info').hide();
        $('#caro-info-' + $('#myCarousel .active').index()).show();
    });
    /* tab selection - modal and regular */
    $('div.tabBox ul.tabs li, div.mtabBox ul.tabs li').live("click", function(event) {
        var currentId = this.id;
        selectTab(currentId);
        if((currentId == "tab-1" || currentId == "tab-3" || currentId == "tab-4") && currentMyScoreTab != currentId) {
            event.preventDefault();
            $("#DownloadinPopup").mouseenter();
            currentMyScoreTab = currentId;
        }
    });
    $.event.special.swipe.scrollSupressionThreshold = 1;
    $("#myCarousel").swiperight(function() {
        $("#myCarousel").carousel('prev');
    });
    $("#myCarousel").swipeleft(function() {
        $("#myCarousel").carousel('next');
    });

    // faq accordion
    $('.accordion-toggle').click(function() {

        var thisClass = $(this);
        if ($('.accordion-body').hasClass('in')) {
            thisClass.removeClass('collapsed');
            $('.accordion-toggle').addClass('collapsed');
        }

    });

    $('#abtCarosel').bxSlider({
        onSlideAfter: function(slideElement, oldIndex, newIndex) {
            // do mind-blowing JS stuff here
            $('.caro-info').hide();
            $('#caro-info-' + newIndex).show();
        }
    });
    var nav = $('#navWrap'),
            body = $('#body'),
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
    $('nav.white li').hover(function() {
        if ($(this).hasClass('gnavButton')) {
            $(this).addClass('hover');
            $(this).addClass('reverseShadowBoxLight');
        }
    }, function() {
        if ($(this).hasClass('gnavButton')) {
            $(this).removeClass('hover');
            $(this).removeClass('reverseShadowBoxLight');
        }
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
    ;
    /* tab selection - modal and regular */
    $('div.tabBox ul.tabs li, div.mtabBox ul.tabs li').live("click", function() {
        selectTab(this.id);
    });
    if (typeof (sliders) == 'undefined' || sliders == true) {
        initSliders();
        initSliders2();
    }
    initArrows();
    initButtons();
    resetCompareSliders();

    /* page load behaviors */
    if (location.hash === '#breakdown') {
        $('#tab-4').click();
    } else if (location.hash === '#projection') {
        $('#tab-2').click();
    } else if (location.hash === '#comparison') {
        $('#tab-3').click();
    } else if (location.hash === '#actionOverlay') {
        popUpActionStep('actionOverlay1.html', 490);
    } else if (location.hash === '#about') {
        $.scrollTo($('#aboutFlexScore'), 800);
    } else if (location.hash === '#how') {
        $.scrollTo($('#howItWorks_land'), 800);
    } else if (location.hash === '#security') {
        $.scrollTo($('#security_land'), 800);
    } else if (location.hash === '#step2') {
        popUpActionStep('step2.html', 935);
    } else if (location.hash === '#estimatedScore') {
        popUpActionStep('estimatedScore.html', 660);
    }

    if (typeof (sliders) == 'undefined' || sliders == true) {
        $('.simulationToggle .ui-slider-horizontal .ui-slider-range-min').css('width', '55px');
    }
    /* open-close arrows for advisor permissions box */
    $('#arw1').live("click", function() {
        $('#arw1').toggleClass("openArrow").toggleClass("closeArrow");
        $('#arw2').removeClass("openArrow");
        $('#arw2').addClass("closeArrow");
        $('#arw3').removeClass("openArrow");
        $('#arw3').addClass("closeArrow");
    });
    $('#arw2').live("click", function() {
        $('#arw2').toggleClass("openArrow").toggleClass("closeArrow");
        $('#arw1').removeClass("openArrow");
        $('#arw1').addClass("closeArrow");
        $('#arw3').removeClass("openArrow");
        $('#arw3').addClass("closeArrow");
    });
    $('#arw3').live("click", function() {
        $('#arw3').toggleClass("openArrow").toggleClass("closeArrow");
        $('#arw1').removeClass("openArrow");
        $('#arw1').addClass("closeArrow");
        $('#arw2').removeClass("openArrow");
        $('#arw2').addClass("closeArrow");
    });
    $('.nav_22 li').hover(function() {
        if ($(this).hasClass('gnavButton')) {
            $(this).addClass('hover');
            $(this).addClass('reverseShadowBoxLight');
        }
    }, function() {
        if ($(this).hasClass('gnavButton')) {
            $(this).removeClass('hover');
            $(this).removeClass('reverseShadowBoxLight');
        }
    });
    $("#loadingIndicator").ajaxStart(function() {
        $(this).show();
    }).ajaxStop(function() {
        $(this).hide();
    });

    $('.goalDrag').unbind().mousedown(function(e) {
        var goals = financialData.goals;
        for (var i = 0; i < goals.length; i++)
        {
            if (goals[i].goalstatus == 1)
            {
                var key = financialData.goals[i].id;
                if ($("#" + key + calculateGoalKey(financialData.goals[i].goaltype) + "CollapseBox").height() > 0) {
                    $("#" + key + calculateGoalKey(financialData.goals[i].goaltype) + "FAQArrow").click();
                }
            }
        }
    });

    $('.accountDrag').unbind().mousedown(function(e) {
        var key = $("#fiType").val();
        var obj = [];
        for (var attrname in financialData)
        {
            if (key == "assets" && (attrname == "cash" || attrname == "investment" || attrname == "silent" || attrname == "other"))
                obj = obj.concat(financialData[attrname]);
            else if (key == attrname)
                obj = obj.concat(financialData[attrname]);
        }
        for (var i = 0; i < obj.length; i++)
        {
            if (obj[i].status != 1)
            {
                if ($("#" + obj[i].id + calculateKey(obj[i].accttype, key) + "CollapseBox").height() > 0) {
                    $("#" + obj[i].id + calculateKey(obj[i].accttype, key) + "FAQArrow").click();
                }
            }
        }
    });

    /* draggable/droppable baseballcards */
    if (userData != null && userData.user != undefined && userData.user.permission != true) {
        $(".goalDraggable").draggable({
            opacity: 0.9,
            helper: "clone",
            containment: '#profileDetails',
            handle: ".goalDrag",
            start: function(event, ui) {
                $(ui.helper.context).fadeTo("slow", 0.5);
            },
            drag: function(event, ui) {
                var i = 1;
                var dragOffset = $('.ui-draggable-dragging').offset();
                $(".goalDroppableDiv").addClass("hdn");
                var dragBoxFound = false;
                var currentDragObj = $(ui.helper.context).parents(".priorityIndex")[0];
                var currentDragIndex = parseInt(currentDragObj.id.substring(0, currentDragObj.id.indexOf('PriorityDiv')));

                while ($("#" + i + "PriorityDiv").length > 0) {
                    var dropOffset = $("#" + i + "PriorityDiv").offset();
                    if (dragOffset.top < dropOffset.top) {
                        if (i < currentDragIndex || i > currentDragIndex + 1) {
                            $("#" + i + "PriorityDiv").children(".goalDroppableDiv").removeClass("hdn");
                        }
                        dragBoxFound = true;
                        break;
                    }
                    i++;
                }
                if (!dragBoxFound && $("#" + (currentDragIndex + 1) + "PriorityDiv").length > 0) {
                    var dropOffset = $("#EndPriorityDiv").offset();
                    $("#EndPriorityDiv").children(".goalDroppableDiv").removeClass("hdn");
                }
            },
            stop: function(event, ui) {
                $(".profileDatabox").fadeTo("slow", 1);
            }
        });

        $(".accountDraggable").draggable({
            opacity: 0.9,
            helper: "clone",
            containment: '#profileDetails',
            handle: ".accountDrag",
            start: function(event, ui) {
                $(ui.helper.context).fadeTo("slow", 0.5);
            },
            drag: function(event, ui) {
                var i = 1;
                var dragOffset = $('.ui-draggable-dragging').offset();
                $(".accountDroppableDiv").addClass("hdn");
                var dragBoxFound = false;
                var currentDragObj = $(ui.helper.context).parents(".priorityIndex")[0];
                var currentDragIndex = parseInt(currentDragObj.id.substring(0, currentDragObj.id.indexOf('PriorityDiv')));

                while ($("#" + i + "PriorityDiv").length > 0) {
                    var dropOffset = $("#" + i + "PriorityDiv").offset();
                    if (dragOffset.top < dropOffset.top) {
                        if (i < currentDragIndex || i > currentDragIndex + 1) {
                            $("#" + i + "PriorityDiv").children(".accountDroppableDiv").removeClass("hdn");
                        }
                        dragBoxFound = true;
                        break;
                    }
                    i++;
                }
                if (!dragBoxFound && $("#" + (currentDragIndex + 1) + "PriorityDiv").length > 0) {
                    var dropOffset = $("#EndPriorityDiv").offset();
                    $("#EndPriorityDiv").children(".accountDroppableDiv").removeClass("hdn");
                }
            },
            stop: function(event, ui) {
                $(".profileDatabox").fadeTo("slow", 1);
            }
        });
    }

    $("#goalProfileDetails").droppable({
        accept: ".goalDraggable",
        activeClass: "ui-state-hover",
        hoverClass: "ui-state-active",
        drop: function(event, ui) {
            var i = 0;
            $('.ui-draggable-dragging').hide();
            $('.ui-draggable-dragging').removeClass('row-fluid allRound accordion-group smallGlow lightGray profileDatabox draggable goalDraggable ui-draggable');
            $('.ui-draggable-dragging').html('');
            $('.ui-draggable-dragging').removeClass('ui-draggable-dragging');
            if ($(".goalDroppableDiv:visible").length > 0) {
                var toObj = $(".goalDroppableDiv:visible").parents('.priorityIndex')[0];
                var fromObj = $(ui.draggable[0]).parents('.priorityIndex')[0];
                LookUpGoalDragObject(fromObj, toObj);
            }
            $(".goalDroppableDiv").addClass("hdn");
            $(".profileDatabox").fadeTo("slow", 1);
        }
    });

    $("#accountProfileDetails").droppable({
        accept: ".accountDraggable",
        activeClass: "ui-state-hover",
        hoverClass: "ui-state-active",
        drop: function(event, ui) {
            var i = 0;
            $('.ui-draggable-dragging').hide();
            $('.ui-draggable-dragging').removeClass('row-fluid allRound accordion-group smallGlow lightGray profileDatabox draggable accountDraggable ui-draggable');
            $('.ui-draggable-dragging').html('');
            $('.ui-draggable-dragging').removeClass('ui-draggable-dragging');
            if ($(".accountDroppableDiv:visible").length > 0) {
                var toObj = $(".accountDroppableDiv:visible").parents('.priorityIndex')[0];
                var fromObj = $(ui.draggable[0]).parents('.priorityIndex')[0];
                LookUpAccountDragObject(fromObj, toObj);
            }
            $(".accountDroppableDiv").addClass("hdn");
            $(".profileDatabox").fadeTo("slow", 1);
        }
    });


    var gnavWrapper = $('#navWrap');

    $(".specialCollapseBox").unbind().click(function(event) {
        if (updateCollapse && $(this).parents(".accordion-group").find(".accordion-body").height() > 0) {
            var name = event.target.id;
            var index = name.indexOf("AccordionHeader");
            if(index == -1) { index = name.indexOf("FAQArrow"); }
            if(index == -1) { index = name.indexOf("NameSummary"); }
            if(index == -1) { index = name.indexOf("Manual"); }
            var key = name.substring(0, index).toLowerCase();
            if ($('#' + key + 'SaveNeeded').val() === "true") {
                needsToClose = false;
                $(this).parents(".accordion-group").find(".lsPillBtnGreen").click();
                needsToClose = true;
            }
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
        $('.baseballCardWrapper').removeClass('on', 500)
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
        $('.asLine2').removeClass('hdn');
        $('#moreActionSteps').hide();
        $('#fewerActionSteps').show();
    });

    //actionsteps hide/show
    $('#fewerActionSteps').click(function() {
        $('.asLine2').addClass('hdn');
        $('#moreActionSteps').show();
        $('#fewerActionSteps').hide();
    });

    // account created security questions dropdown
    $('.openCloseArrow').live("click", function(e) {
        e.preventDefault();
        $(this).toggleClass("openArrow");
        $(this).toggleClass("closeArrow");
        $(this).parent().next('.collapse').toggleClass('in', 500);
    });

    // info bubbles toggle
    $('.infoTip').unbind().hover(function() {
        $(this).toggleClass('on');
    }, function() {
        $('.infoTip').removeClass('on');
    });

    if (!breakdownSimulationOn) {
        $(".breakToggleOnLabel").click();
    }
}
//for white background
function initializeAfter() {
    "use strict";
    $('#body').attr('class', '');
}
function fnLearningCenterLoad() {
    /*  CarouFredSel: a circular, responsive jQuery carousel.
     Configuration created by the "Configuration Robot"
     at caroufredsel.dev7studios.com
     */
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

    $('.topics.list_carousel li').hover(function(thing) {
        $($(this).children('div')[0]).hide();
        $($(this).children('div')[1]).show();
    }, function(thing) {
        $($(this).children('div')[0]).show();
        $($(this).children('div')[1]).hide();
    });

}

function bsGnavFunction(element, srollToId) {
    "use strict";
    $('.nav_22 li').each(function() {
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

//for score backbone POC
function fnShowMoreResult() {
    $('#score_details').toggle();
    if ($('#showmoredetails').html() == "Collapse") {
        $('#showmoredetails').html("Details");
    } else {
        $('#showmoredetails').html("Collapse");
    }
}

function fnCollapseShow(div) {
    $('#' + div + '_table').toggle();
    if ($('#' + div + '_label').html() == "Collapse") {
        $('#' + div + '_label').html("Show");
    } else {
        $('#' + div + '_label').html("Collapse");
    }
}

// wait for the DOM to be loaded
$(document).ready(function() {
    var options = {
        target: '#score_result', // target element(s) to be updated with server response
        beforeSubmit: showRequest, // pre-submit callback
        success: showResponse  // post-submit callback
    };


});
function calculateTotal(obj) {
    if (typeof (obj) == 'undefined')
        return 0;
    var i = 0;
    var total = 0;
    for (i = 0; i < obj.length; i++)
    {
        if (obj[i].status == 0 && (typeof (obj[i].monthly_payoff_balances) == 'undefined' || obj[i].monthly_payoff_balances == 0) && typeof (obj[i].amount) != 'undefined' && obj[i].amount != "" && obj[i].amount != null)
        {
            total = total + parseFloat(obj[i].amount.toString().replace(/,/g, ''));
        }
    }
    return total.toFixed(2);
}
// Used for Financial Snapshot page
function checkSize(total, obj, addId, closeId, divId, spanId) {
    var isActive = false;
    if (total == "0.00" || total == "0")
    {
        for (var attrname in obj)
        {
            if (obj[attrname].status == '0')
            {
                isActive = true;
                break;
            }
        }
    }
    else
        isActive = true;
    if (isActive)
    {
        $(addId).addClass("hdn");
        $(divId).addClass("fsMSheader")
    }
    else
    {
        $(spanId).addClass("hdn");
        $(closeId).attr("style", "display:none");
    }
    return isActive;

}
// Add Commas
function commaSeparateNumber(val, fixed)
{
    if (typeof (fixed) == 'undefined')
        fixed = 2;
    if (val == null || val === '')
        return val;

    val = val.toString().replace(/,/g, '');
    val = parseFloat(val).toFixed(fixed);
    while (/(\d+)(\d{3})/.test(val.toString())) {
        val = val.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
    }
    return val;
}
// Format Financial Data object
function formatFinancialAmounts(arr, type)
{
    for (var i = 0; i < arr.length; i++)
    {

        //Fix Negative Dollar Amounts - only for showing purpose //
        if (arr[i].amount < 0) {
            arr[i].amountSummaryForShow = "-$" + commaSeparateNumber(arr[i].amount, 0).replace("-", "");
        } else {
            arr[i].amountSummaryForShow = "$" + commaSeparateNumber(arr[i].amount, 0);
        }
        arr[i].amount = commaSeparateNumber(arr[i].amount);
        arr[i].amountSummary = commaSeparateNumber(arr[i].amount, 0);
        arr[i].contribution = commaSeparateNumber(arr[i].contribution);
        arr[i].empcontribution = commaSeparateNumber(arr[i].empcontribution, 1);
        arr[i].withdrawal = commaSeparateNumber(arr[i].withdrawal);
        arr[i].netincome = commaSeparateNumber(arr[i].netincome);
        arr[i].amtpermonth = commaSeparateNumber(arr[i].amtpermonth);
        arr[i].apr = commaSeparateNumber(arr[i].apr);
        arr[i].growthrate = commaSeparateNumber(arr[i].growthrate, 1);
        arr[i].amtupondeath = commaSeparateNumber(arr[i].amtupondeath);
        arr[i].deductible = commaSeparateNumber(arr[i].deductible);
        arr[i].annualpremium = commaSeparateNumber(arr[i].annualpremium);
        if (arr[i].accttype == 'DISA') {
            arr[i].coverageamt = commaSeparateNumber(arr[i].coverageamt, 1);
        }
        else
        {
            arr[i].coverageamt = commaSeparateNumber(arr[i].coverageamt);
        }
        arr[i].dailybenfitamt = commaSeparateNumber(arr[i].dailybenfitamt);
        if (arr[i].name == null || typeof (arr[i].name) == 'undefined' || arr[i].name == "")
        {
            arr[i].nameSummary = GetDefaultNameForType(arr[i].accttype, type);
        }
        else
        {
            arr[i].nameSummary = arr[i].name;
        }

        if (typeof (arr[i].invpos) == 'undefined' || arr[i].invpos == null || arr[i].invpos.length == 0)
        {
            arr[i].invpos = [];
            arr[i].invpos[0] = {
                'index': 0,
                'id': arr[i].id
            };
            arr[i].invpos[1] = {
                'index': 1,
                'id': arr[i].id
            };
            arr[i].invpos[2] = {
                'index': 2,
                'id': arr[i].id
            };
            arr[i].invpos[3] = {
                'index': 3,
                'id': arr[i].id
            };
            arr[i].invpos[4] = {
                'index': 4,
                'id': arr[i].id
            };
            arr[i].tickercount = 5;
        }
        else
        {
            for (var j = 0; j < arr[i].invpos.length; j++)
            {
                arr[i].invpos[j].amount = commaSeparateNumber(arr[i].invpos[j].amount);
                arr[i].invpos[j].index = j;
                arr[i].invpos[j].id = arr[i].id;
            }
            arr[i].tickercount = arr[i].invpos.length;
        }
    }
    return arr;
}
// Format Goals Amount
function formatGoalAmounts(arr)
{
    for (var i = 0; i < arr.length; i++)
    {
        arr[i].permonth = commaSeparateNumber(arr[i].permonth);
        arr[i].saved = commaSeparateNumber(arr[i].saved);
        arr[i].goalamount = commaSeparateNumber(arr[i].goalamount);
        arr[i].monthlyincome = commaSeparateNumber(arr[i].monthlyincome);
        arr[i].goalpriority = parseInt(arr[i].goalpriority);
        arr[i].downpayment = commaSeparateNumber(arr[i].downpayment, 0);
        if (arr[i].goalname == null || typeof (arr[i].goalname) == 'undefined' || arr[i].goalname == "")
        {
            arr[i].goalnamesummary = GetDefaultNameForGoalType(arr[i].goaltype);
        }
        else
        {
            arr[i].goalnamesummary = arr[i].goalname;
        }
    }
    return arr;
}
function showRequest(formData, jqForm, options) {
    //$('#resultModal').modal('show');
    $('#score_result').html("Your Score is : ...");
    $('#score_details').html("");
    $('#score_breakup').html("");
    $('#user_id').val($('#user_id').val() + 1);
}
function showResponse(responseText, statusText, xhr, $form) {
    var resultArr = responseText.split('#')
    $('#score_result').html("<strong>Your score is : " + resultArr[0] + "</strong>");
    $('#score_details').html(resultArr[1]);
    $('#score_breakup').html(resultArr[2]);
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
// Load Profile Dialog
function popUpProfile(url, height) {
    "use strict";
    if (!$("#profileBox").is(":visible"))
    {
        $.scrollTo($('#body'), 200);
    }
    $('#profileBox').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#profileBox').css("height", "auto");
}
//used for account notification popup
function popUpNotification() {
    "use strict";
    if (!$("#notificationBox").is(":visible"))
    {
        $.scrollTo($('#body'), 200);
    }
    $('#notificationBox').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#notificationBox').css("height", "auto");
}

// Update Sliderss
//used for create new client popup
function popUpCreatenewclient() {

    "use strict";
    if (!$("#createnewclientBox").is(":visible"))
    {
        $.scrollTo($('#body'), 200);
    }
    $('#createnewclientBox').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#createnewclientBox').css("height", "auto");
}

function popUpCreateASDescPopup() {

    "use strict";
    if (!$("#createnewASDescBox").is(":visible"))
    {
        $.scrollTo($('#body'), 200);
    }
    $('#createnewASDescBox').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#createnewASDescBox').css("height", "auto");
}

function popUpCreatenewASProduct() {

    "use strict";
    if (!$("#createnewASProductBox").is(":visible"))
    {
        $.scrollTo($('#body'), 200);
    }
    $('#createnewASProductBox').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#createnewASProductBox').css("height", "auto");
}

function popUpUploadnewclientList() {

    "use strict";
    if (!$("#uploadnewclientlistBox").is(":visible"))
    {
        $.scrollTo($('#body'), 200);
    }
    $('#uploadnewclientlistBox').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#uploadnewclientlistBox').css("height", "auto");
}

function popUpclientFinancialSummary() {

    "use strict";
    if (!$("#clientfinancialsummaryBox").is(":visible"))
    {
        $.scrollTo($('#body'), 200);
    }
    $('#clientfinancialsummaryBox').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#clientfinancialsummaryBox').css("height", "auto");
}

//used for delete client popup
function popUpDeleteclient() {
    "use strict";
    if (!$("#deleteclientBox").is(":visible"))
    {
        $.scrollTo($('#body'), 200);
    }
    $('#deleteclientBox').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#deleteclientBox').css("height", "auto");
}

//used for delete client popup
function popUpMyadvisor() {
    "use strict";
    if (!$("#myAdvisorBox").is(":visible"))
    {
        $.scrollTo($('#body'), 200);
    }
    $('#myAdvisorBox').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#myAdvisorBox').css("height", "auto");
}
//from onload.js
function updateSliderValues(event, ui) {
    "use strict";
    try {
        //var i = event.target.parentElement.id;
        var i = event.target.id;
        var valueElement = $('#' + i + 'Value');
        valueElement.text(ui.value.formatMoney(0, '.', ','));

    } catch (err) {
    }
}
// Update Sliderss
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
    $('#breakdownHorseshoe').attr("src", "./ui/images/horseshoes/variations/myscore/MyScoreHorseShoe" + imageNum + ".png");
}
//from onload.js
function resetCompareSliders() {
//$('.slider').slider("value", 100);
//$('.sliderAge').slider("value", 21);
//updateSliderValuesAndGraph();
}
//onload.js
/* global function for initializing sliders */
function initSliders() {
    //onload.js
    // Tooltip for slider
    var tooltip = $('.tooltip');

    $(".riskSlider").slider({
        range: "min",
        value: (typeof (profileUserData.risk) != 'undefined' && profileUserData.risk != null) ? parseInt(profileUserData.risk) : sliderDefaultValue,
        min: 1,
        max: 10,
        step: 1,
        slide: riskSliderUpdate,
        change: riskSliderUpdate
    });

    $(".riskSliderValue").keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        keycode = parseInt(keycode);
        if (keycode == 13) {
            event.preventDefault();
            $(this).blur();
        }
        else if ((keycode < 48 || keycode > 57) && event.keyCode != 37 && (keycode != 39 || event.which == 39) && keycode != 8 && keycode != 9)
        {
            event.preventDefault();
        }
    });

    $(".simToggleSlider").slider({// commented out from this common area, used it in break.js
        value: 20,
        min: 10,
        max: 20,
        step: 10,
        range: "min",
        slide: function(event, ui) {
            if (ui.value === 20) {
                toggleSimOn(ui.handle.parentNode.parentNode);
                if (!$("#profileBox").is(":visible"))
                {
                    resetBreakSliders();
                    breakdownSimulationOn = true;
                    $('.resetBreakButton').show();
                }
            } else {
                toggleSimOff(ui.handle.parentNode.parentNode);
                if (!$("#profileBox").is(":visible"))
                {
                    var simScore = parseInt(financialData.totalscore);
                    breakscore = simScore;
                    var imageId = Math.round((simScore * 20) / 1000);
                    imageId = (imageId > 0) ? imageId : 0;
                    imageId = (imageId < 20) ? imageId : 20;
                    alignScore('breakdownScore', 'breakdownHorseshoe', simScore, imageId);
                    breakdownSimulationOn = false;
                    $('.resetBreakButton').hide();
                }
            }
        },
        stop: function(event, ui) {
            if (ui.value === 20) {
                $(ui.handle.parentNode.parentNode).find('.ui-slider-horizontal .ui-slider-range-min').css('width', '55px');
            }
        }
    });

    $('.toggleOnLabel').unbind().click(function(event) {
        event.stopPropagation()
        $(this).siblings(".simToggleSlider").slider("option", "value", 10);
        toggleSimOff(this.parentNode);
        if (!$("#profileBox").is(":visible"))
        {
            var simScore = parseInt(financialData.totalscore);
            breakscore = simScore;
            var imageId = Math.round((simScore * 20) / 1000);
            imageId = (imageId > 0) ? imageId : 0;
            imageId = (imageId < 20) ? imageId : 20;
            alignScore('breakdownScore', 'breakdownHorseshoe', simScore, imageId);
            breakdownSimulationOn = false;
            $('.resetBreakButton').hide();
        }
    });

    $('.toggleOffLabel').unbind().click(function(event) {
        event.stopPropagation()
        $(this).siblings(".simToggleSlider").slider("option", "value", 20);
        toggleSimOn(this.parentNode);
        if (!$("#profileBox").is(":visible"))
        {
            resetBreakSliders();
            breakdownSimulationOn = true;
            $('.resetBreakButton').show();
        }
    });

    $('.ui-slider-handle').hover(function() {
        $(this).css("outline-style", "none");
    }, function() {
        $(this).css("outline-style", "none");
    });

    $('.simulationToggle .ui-slider-horizontal .ui-slider-range-min').css('width', '55px');
}
//onload.js
//update step 2 slider values
function step2SliderUpdate(event, ui) {
    "use strict";
    var sliderId = event.target.id;
    $('#' + sliderId + "Value").val(ui.value.formatMoney(0, '.', ','));
}

function riskSliderUpdate(event, ui) {
    "use strict";
    // Tooltip for slider
    var tooltip = $('.tooltip');
    var tooltip_pointer = $('.pbPointerTop');
    var slider = $('#risk_slider_pointer');
    var slide_value = "";
    var text_output = "";

    var sliderId = event.target.id;
    $('#' + sliderId + "Value").val(ui.value).change();
    slide_value = slider.css('left');

    var obj = riskdata[ui.value - 1];
    text_output = "<p bgcolor='#ffff00' style='font-style:bold;padding-left:20px'>68% of the time, 1 year returns could range from " +
            obj.low_range_of_returns + "% (low) to " +
            obj.high_range_of_returns + "% (high)<p>";
    text_output = text_output + "<p bgcolor='#ffc0cb' style='font-style:bold;padding-left:20px'>2.5% of the time, 1 year returns could fall below " +
            obj.modeled_loss_expectation + "%</p>";
    tooltip.html(text_output);

    // Adjusting the tooltip accordingly
    tooltip.css('left', "-17px");
    tooltip_pointer.css('left', parseInt(slide_value) - 15);
}


//Rounding of number to decimal
function floorFigure(figure, decimals) {
    //if (!decimals) decimals = 2;
    var d = Math.pow(10, decimals);
    return (parseInt(figure * d) / d).toFixed(decimals);
}
;

//onload.js
/* global function for initializing sliders */
function initSliders2() {
    "use strict";
    $(".sliderStep2").slider({
        range: "min",
        value: 0,
        min: 0,
        max: 250000,
        step: 100,
        slide: step2SliderUpdate,
        change: step2SliderUpdate
    });

    $('.sliderStep2Value').change(function() {
        var thisId = this.id;
        var value = this.value.replace(/,/g, '');
        $('#' + thisId.substring(0, 7)).slider("value", value);
    });

    $(".sliderStep3").slider({
        range: "min",
        value: 0,
        min: 0,
        max: 2000000,
        step: 100,
        slide: step2SliderUpdate,
        change: step2SliderUpdate
    });

    $('.sliderStep3Value').change(function() {
        var thisId = this.id;
        var value = this.value.replace(/,/g, '');
        $('#' + thisId.substring(0, 7)).slider("value", value);
    });

    $(".sliderStep4").slider({
        range: "min",
        value: 0,
        min: 0,
        max: 100000,
        step: 100,
        slide: step2SliderUpdate,
        change: step2SliderUpdate
    });

    $('.sliderStep4Value').change(function() {
        var thisId = this.id;
        var value = this.value.replace(/,/g, '');
        $('#' + thisId.substring(0, 7)).slider("value", value);
    });
}
//onload.js
function initArrows() {
    "use strict";
    /* open-close arrows for advisor permissions box */
    $('.arw-wrapper').click(function(elem) {
        $(this).siblings('.accSec').toggleClass('hdn');
        $(this).find('.arw').toggleClass("openArrow").toggleClass("closeArrow");
    });
}

//from buttons.js
var imgNumber = 4;
var selectionClicked = false;

function changeImage() {
    imgNumber++;
    if (imgNumber > 4) {
        imgNumber = 1;
    }
    //var randomImage = Math.round(Math.random()*3) + 1
    $('#imgChange').attr("src", "./ui/images/horseshoeDemo/myscore_horseshoe" + imgNumber + ".png");
    /*swap();*/
}

function swap() {
    var randomImageNumber = Math.round(Math.random() * 3) + 1
    var newimg = "./ui/images/horseshoeDemo/myscore_horseshoe" + randomImageNumber + ".png";
    newimg = "./ui/images/horseshoeDemo/myscore_horseshoe4.png";
    $('#nextimg').attr('src', newimg);
    /*$('#currentimg').fadeOut('slow',
     function(){
     $(this).attr('src',$('#nextimg').attr('src')).fadeIn();
     }
     );*/
    $('#currentimg').fadeOut(500);
}

function calcResize() {
    if ($(".mobileLogo:visible").length > 0 || $(this).scrollTop() <= $(".articleNavls").offset().top + $(".articleNavls").height() - 50) {
        $("#signupbox").css('position', 'relative');
        $("#signupbox").css('top', '0px');
    }
    else if ($(this).scrollTop() > $(".articleNavls").offset().top + $(".articleNavls").height() - 50 && $(".contactSupport").offset().top > $(this).scrollTop() + 470)
    {
        $("#signupbox").css('position', 'fixed');
        $("#signupbox").css('top', '100px');
    }
    else
    {
        $("#signupbox").css('position', 'relative');
        $("#signupbox").css('top', ($(".contactSupport").offset().top - 540 - $(".articleNavls").height()) + 'px');
    }
}
function initSignupButton() {
    $(window).scroll(function(event) {
        calcResize();
    });
    $(window).resize(function(event) {
        calcResize();
    });
}

function initButtons() {
    /* normal buttons */
    $('.recButton').hover(
            function() {
                $(this).removeClass('recButton recButtonMouseDown');
                $(this).addClass('recButtonHover');
            },
            function() {
                $(this).removeClass('recButtonHover recButtonMouseDown');
                $(this).addClass('recButton');
            });

    $('.msdwn').mousedown(function() {
        $(this).removeClass('recButtonHover');
        $(this).addClass('recButtonMouseDown');
    });

    $('.msdwn').mouseup(function() {
        $(this).removeClass('recButtonMouseDown');
        $(this).addClass('recButtonHover');
    });


    /* large buttons */
    $('.recButtonDarkLarge').hover(
            function() {
                if (!$(this).hasClass('btnclicked')) {
                    $(this).removeClass('recButtonDarkLarge recButtonDarkLargeMouseDown');
                    $(this).addClass('recButtonDarkLargeHover');
                }
            },
            function() {
                if (!$(this).hasClass('btnclicked')) {
                    $(this).removeClass('recButtonDarkLargeHover recButtonDarkLargeMouseDown');
                    $(this).addClass('recButtonDarkLarge');
                }

            });

    $('.recButtonDarkLarge2Line').hover(
            function() {
                if (!$(this).hasClass('btnclicked')) {
                    $(this).removeClass('recButtonDarkLarge2Line recButtonDarkLargeMouseDown');
                    $(this).addClass('recButtonDarkLargeHover2Line');
                }
            },
            function() {
                if (!$(this).hasClass('btnclicked')) {
                    $(this).removeClass('recButtonDarkLargeHover2Line recButtonDarkLargeMouseDown');
                    $(this).addClass('recButtonDarkLarge2Line');
                }
            });

    $('.msdwnLarge').mousedown(function() {
        // 'click' this button, set all others to normal state
        $('.recButtonDarkLargeMouseDown').each(function() {
            //if ( ! $(this).hasClass( 'btnclicked' ) ) {
            $(this).removeClass('recButtonDarkLargeMouseDown');
            $(this).addClass('recButtonDarkLarge');
            //}
        });
        $('.recButtonDarkLargeMouseDown2Line').each(function() {
            //if ( ! $(this).hasClass( 'btnclicked' ) ) {
            $(this).removeClass('recButtonDarkLargeMouseDown2Line');
            $(this).addClass('recButtonDarkLarge2Line');
            //}
        });

        $(this).removeClass('recButtonDarkLargeHover');
        $(this).addClass('recButtonDarkLargeMouseDown');
        $(this).addClass('btnclicked');
        selectionClicked = true;
    })

    $('.msdwnLarge').mouseup(function() {
        if (!selectionClicked) {
            $(this).removeClass('recButtonDarkLargeMouseDown');
            $(this).addClass('recButtonDarkLargeHover');
        }
    })

    $('.msdwnLarge2Line').mousedown(function() {
        // 'click' this button, set all others to normal state
        $('.recButtonDarkLargeMouseDown').each(function() {
            //if ( ! $(this).hasClass( 'btnclicked' ) ) {
            $(this).removeClass('recButtonDarkLargeMouseDown');
            $(this).addClass('recButtonDarkLarge');
            //}
        });
        $('.recButtonDarkLargeMouseDown2Line').each(function() {
            //if ( ! $(this).hasClass( 'btnclicked' ) ) {
            $(this).removeClass('recButtonDarkLargeMouseDown2Line');
            $(this).addClass('recButtonDarkLarge2Line');
            //}
        });

        $(this).removeClass('recButtonDarkLargeHover2Line');
        $(this).addClass('recButtonDarkLargeMouseDown2Line');
        $(this).addClass('btnclicked');
        selectionClicked = true;
    })

    $('.msdwnLarge2Line').mouseup(function() {
        if (!selectionClicked) {
            $(this).removeClass('recButtonDarkLargeMouseDown2Line');
            $(this).addClass('recButtonDarkLargeHover2Line');
        }
    })
}
// Update Financial Data
function fnUpdateAllData(data) {
    data.lsacc.cash = formatFinancialAmounts(data.lsacc.cash, "assets");
    data.lsacc.debts = formatFinancialAmounts(data.lsacc.debts, "debts");
    data.lsacc.insurance = formatFinancialAmounts(data.lsacc.insurance, "insurance");
    data.lsacc.investment = formatFinancialAmounts(data.lsacc.investment, "assets");
    data.lsacc.other = formatFinancialAmounts(data.lsacc.other, "assets");
    data.lsacc.silent = formatFinancialAmounts(data.lsacc.silent, "assets");
    data.lsacc.goals = formatGoalAmounts(data.lsacc.goals);
    financialData = data.lsacc;
    financialData.accflg = 0;
}

// Update Financial Data
function fnUpdateFinancialData() {
    financialData.cashTotal = calculateTotal(financialData.cash);
    financialData.debtTotal = calculateTotal(financialData.debts);
    financialData.insuranceTotal = calculateTotal(financialData.insurance);
    financialData.investmentTotal = calculateTotal(financialData.investment);
    financialData.otherTotal = calculateTotal(financialData.other);
    financialData.silentTotal = calculateTotal(financialData.silent);
    financialData.networth = (parseFloat(financialData.cashTotal) + parseFloat(financialData.insuranceTotal) + parseFloat(financialData.investmentTotal) + parseFloat(financialData.otherTotal) - parseFloat(financialData.debtTotal)).toFixed(2);

    //Fix Negative Dollar Amounts - only for showing purpose //
    if (financialData.cashTotal < 0) {
        financialData.cashTotalForShow = '-$' + (commaSeparateNumber(financialData.cashTotal, 0).replace("-", ""));
    } else {
        financialData.cashTotalForShow = '$' + commaSeparateNumber(financialData.cashTotal, 0);
    }

    if (financialData.debtTotal < 0) {
        financialData.debtTotalForShow = '-$' + (commaSeparateNumber(financialData.debtTotal, 0).replace("-", ""));
    } else {
        financialData.debtTotalForShow = '$' + commaSeparateNumber(financialData.debtTotal, 0);
    }

    if (financialData.alldebtTotal < 0) {
        financialData.alldebtTotalForShow = '-$' + (commaSeparateNumber(financialData.alldebtTotal, 0).replace("-", ""));
    } else {
        financialData.alldebtTotalForShow = '$' + commaSeparateNumber(financialData.alldebtTotal, 0);
    }

    if (financialData.insuranceTotal < 0) {
        financialData.insuranceTotalForShow = '-$' + (commaSeparateNumber(financialData.insuranceTotal, 0).replace("-", ""));
    } else {
        financialData.insuranceTotalForShow = '$' + commaSeparateNumber(financialData.insuranceTotal, 0);
    }

    if (financialData.investmentTotal < 0) {
        financialData.investmentTotalForShow = '-$' + (commaSeparateNumber(financialData.investmentTotal, 0).replace("-", ""));
    } else {
        financialData.investmentTotalForShow = '$' + commaSeparateNumber(financialData.investmentTotal, 0);
    }

    if (financialData.otherTotal < 0) {
        financialData.otherTotalForShow = '-$' + (commaSeparateNumber(financialData.otherTotal, 0).replace("-", ""));
    } else {
        financialData.otherTotalForShow = '$' + commaSeparateNumber(financialData.otherTotal, 0);
    }

    if (financialData.silentTotal < 0) {
        financialData.silentTotalForShow = '-$' + (commaSeparateNumber(financialData.silentTotal, 0).replace("-", ""));
    } else {
        financialData.silentTotalForShow = '$' + commaSeparateNumber(financialData.silentTotal, 0);
    }

    if (financialData.networth < 0) {
        financialData.networthForShow = '-$' + (commaSeparateNumber(financialData.networth, 0).replace("-", ""));
    } else {
        financialData.networthForShow = '$' + commaSeparateNumber(financialData.networth, 0);
    }

    /*end*/
    financialData.cashTotal = commaSeparateNumber(financialData.cashTotal, 0);
    financialData.debtTotal = commaSeparateNumber(financialData.debtTotal, 0);
    financialData.alldebtTotal = commaSeparateNumber(financialData.alldebtTotal, 0);
    financialData.insuranceTotal = commaSeparateNumber(financialData.insuranceTotal, 0);
    financialData.investmentTotal = commaSeparateNumber(financialData.investmentTotal, 0);
    financialData.otherTotal = commaSeparateNumber(financialData.otherTotal, 0);
    financialData.silentTotal = commaSeparateNumber(financialData.silentTotal, 0);
    financialData.networth = commaSeparateNumber(financialData.networth, 0);



    if (financialData.debtsTotal != null) {
        financialData.debtsTotal = commaSeparateNumber(financialData.debtsTotal.toString().replace(/,/g, ''), 0);
    }
    else
    {
        financialData.debtsTotal = "0";
    }

    if (financialData.assetsTotal != null) {
        financialData.assetsTotal = commaSeparateNumber(financialData.assetsTotal.toString().replace(/,/g, ''), 0);
    }
    else
    {
        financialData.assetsTotal = "0";
    }
    if (financialData.savingsTotal != null) {
        financialData.savingsTotal = commaSeparateNumber(financialData.savingsTotal.toString().replace(/,/g, ''), 0);
    }
    else
    {
        financialData.savingsTotal = "0";
    }
    if (financialData.livingCosts != null) {
        financialData.livingCosts = commaSeparateNumber(financialData.livingCosts.toString().replace(/,/g, ''), 0);
    }
    else
    {
        financialData.livingCosts = "0";
    }
}

function fnGetFinancialTotal(key) {
    var total = 0;
    if (key == "debts")
    {
        total = calculateTotal(financialData.debts);
    }
    else if (key == "insurance")
    {
        total = calculateTotal(financialData.insurance);
    }
    else
    {
        total = (parseFloat(calculateTotal(financialData.cash)) + parseFloat(calculateTotal(financialData.investment)) + parseFloat(calculateTotal(financialData.other))).toFixed(2);
    }
    if (total < 0) {
        return "-$" + commaSeparateNumber(total, 0).replace("-", "");
    } else {
        return "$" + commaSeparateNumber(total, 0);
    }
}

function fnCleanUpFinancialData() {
    var hasNetWorth = false;
    if (checkSize(financialData.cashTotal, financialData.cash, "#addCash", "#closeCash", "#cashHeader", "#cashSummarySpan"))
        hasNetWorth = true;
    if (checkSize(financialData.debtTotal, financialData.debts, "#addDebts", "#closeDebts", "#debtHeader", "#debtSummarySpan"))
        hasNetWorth = true;
    if (checkSize(financialData.insuranceTotal, financialData.insurance, "#addInsurance", "#closeInsurance", "#insureHeader", "#insuranceSummarySpan"))
        hasNetWorth = true;
    if (checkSize(financialData.investmentTotal, financialData.investment, "#addInvestments", "#closeInvestments", "#investHeader", "#investmentsSummarySpan"))
        hasNetWorth = true;
    if (checkSize(financialData.otherTotal, financialData.other, "#addOther", "#closeOther", "#otherHeader", "#otherSummarySpan"))
        hasNetWorth = true;

    if (hasNetWorth)
    {
        $("#addNetWorth").addClass("hdn");
        $("#netWorthDiv").toggleClass("darkGray purpleBG");
        $("#netWorthDescSpan").attr("style", "color: white; font-size: 20px");
        $("#netWorthNumberSpan").attr("style", "color: white; font-size: 20px");
    }
    else
    {
        $("#netWorthSummarySpan").addClass("hdn");
    }
}
// Update User Data
function fnUpdateUserData(data) {
    var days = [];
    try {
        for (var i = 1; i <= 31; i++)
        {
            days[i - 1] = [];
            if (i < 10) {
                days[i - 1]['day'] = "0" + i;
            }
            else {
                days[i - 1]['day'] = "" + i;
            }
        }
        var years = [];
        for (var i = date.getFullYear() - defaultAge; i >= date.getFullYear() - oldestAge; i--)
        {
            years[date.getFullYear() - defaultAge - i] = [];
            years[date.getFullYear() - defaultAge - i]['year'] = i;
        }
        var childyears = [];
        for (var i = date.getFullYear(); i >= date.getFullYear() - oldestAge; i--)
        {
            childyears[date.getFullYear() - i] = [];
            childyears[date.getFullYear() - i]['year'] = i;
        }
        var months = [{
                month: 'January',
                value: '01'
            }, {
                month: 'February',
                value: '02'
            }, {
                month: 'March',
                value: '03'
            }, {
                month: 'April',
                value: '04'
            }, {
                month: 'May',
                value: '05'
            }, {
                month: 'June',
                value: '06'
            }, {
                month: 'July',
                value: '07'
            }, {
                month: 'August',
                value: '08'
            }, {
                month: 'September',
                value: '09'
            }, {
                month: 'October',
                value: '10'
            }, {
                month: 'November',
                value: '11'
            }, {
                month: 'December',
                value: '12'
            }];

        if (typeof (data.userdata.age) != 'undefined' && data.userdata.age != null)
        {
            var values = data.userdata.age.split('-');
            if (values[0] != '' && values[0] != '0000')
                years[date.getFullYear() - defaultAge - values[0]].dobyear = values[0];
            if (values[1] != '' && values[1] != '00')
                months[values[1] - 1].dobmonth = values[1];
            if (values[2] != '' && values[2] != '00')
                days[values[2] - 1].dobday = values[2];
        }

        if (typeof (data.userdata.spouseage) != 'undefined' && data.userdata.spouseage != null)
        {
            var values = data.userdata.spouseage.split('-');
            if (values[0] != '' && values[0] != '0000')
                years[date.getFullYear() - defaultAge - values[0]].spousedobyear = values[0];
            if (values[1] != '' && values[1] != '00')
                months[values[1] - 1].spousedobmonth = values[1];
            if (values[2] != '' && values[2] != '00')
                days[values[2] - 1].spousedobday = values[2];
        }
        if (typeof (data.userdata.childrensage) != 'undefined' && data.userdata.childrensage != null)
        {
            var children = data.userdata.childrensage.split(',');
            for (var i = 1; i <= children.length; i++)
            {
                var values = children[i - 1].split('-');
                if (typeof (values) != 'undefined' && values != "") {
                    if (values[0] != '' && values[0] != '0000')
                        childyears[date.getFullYear() - values[0]]["child" + i + "dobyear"] = values[0];
                    if (values[1] != '' && values[1] != '00')
                        months[values[1] - 1]["child" + i + "dobmonth"] = values[1];
                    if (values[2] != '' && values[2] != '00')
                        days[values[2] - 1]["child" + i + "dobday"] = values[2];
                }
            }
        }
    } catch (e) {
    }

    profileUserData = data.userdata;
    profileUserData.months = months;
    profileUserData.days = days;
    profileUserData.years = years;
    profileUserData.childyears = childyears;
    profileUserData.needsUpdate = false;
}


function fnUpdateUserPreferences(data) {
    userPreferences.user_id = data.user_id;
    userPreferences.connectAccountPreference = data.connectAccountPreference;
    userPreferences.debtsPreference = data.debtsPreference;
    userPreferences.insurancePreference = data.insurancePreference;
    userPreferences.debtData = data.debtData;
    userPreferences.insuranceData = data.insuranceData;
    userPreferences.debtAdded = data.debtAdded;
    userPreferences.insuranceAdded = data.insuranceAdded;
}


var milestoneGraphData = [];
var milestoneRange = 0;

function fnUpdateMilestoneGraphData(data, range) {
    milestoneRange = range;
    milestoneGraphData = data;
}




// Load Header Information
function CalculateHeaderText()
{
    if (typeof (profileUserData.firstname) != ' undefined' && profileUserData.firstname != null && profileUserData.firstname != ""
            && typeof (profileUserData.lastname) != ' undefined' && profileUserData.lastname != null && profileUserData.lastname != "")
    {
        if (profileUserData.firstname.length > 14)
            return profileUserData.firstname.substr(0, 15);
        else if (profileUserData.firstname.length > 0 && profileUserData.firstname.length + profileUserData.lastname.length > 14)
            return profileUserData.firstname + " " + profileUserData.lastname[0];
        else if (profileUserData.lastname.length > 14)
            return profileUserData.lastname.substr(0, 15);
        else
            return profileUserData.firstname + " " + profileUserData.lastname;
    }
    else if (typeof (profileUserData.firstname) != 'undefined' && profileUserData.firstname != null && profileUserData.firstname != "")
    {
        if (profileUserData.firstname.length > 14)
            return profileUserData.firstname.substr(0, 15);
        else
            return profileUserData.firstname;
    }
    else if (typeof (profileUserData.lastname) != 'undefined' && profileUserData.lastname != null && profileUserData.lastname != "")
    {
        if (profileUserData.lastname.length > 14)
            return profileUserData.lastname.substr(0, 15);
        else
            return profileUserData.lastname;
    }
    else
    {
        if (profileUserData.email.length > 14)
            return profileUserData.email.substr(0, 15);
        else
            return profileUserData.email;
    }
}

function SetupGoalDate(type, goal)
{
    var days = [];
    for (var i = 1; i <= 31; i++)
    {
        days[i - 1] = [];
        if (i < 10) {
            days[i - 1]['day'] = "0" + i;
        }
        else {
            days[i - 1]['day'] = "" + i;
        }
    }
    var years = [];
    var currentFurthestAge = furthestAge;
    if (type == "debt") {
        currentFurthestAge = 19;
    }
    for (var i = date.getFullYear(); i <= date.getFullYear() + currentFurthestAge; i++)
    {
        years[i - date.getFullYear()] = [];
        years[i - date.getFullYear()]['year'] = i;
    }
    var months = [{
            month: 'January',
            value: '01'
        }, {
            month: 'February',
            value: '02'
        }, {
            month: 'March',
            value: '03'
        }, {
            month: 'April',
            value: '04'
        }, {
            month: 'May',
            value: '05'
        }, {
            month: 'June',
            value: '06'
        }, {
            month: 'July',
            value: '07'
        }, {
            month: 'August',
            value: '08'
        }, {
            month: 'September',
            value: '09'
        }, {
            month: 'October',
            value: '10'
        }, {
            month: 'November',
            value: '11'
        }, {
            month: 'December',
            value: '12'
        }];

    if (typeof (goal) == 'undefined')
        goal = {};
    var defaultyears = 10;
    if (typeof (goal.goalendYear) != 'undefined' && goal.goalendYear > 0 && goal.goalendYear != '' && goal.goalendYear != '0000')
        years[goal.goalendYear - date.getFullYear()][type + "year"] = goal.goalendYear;
    else
        years[defaultyears][type + "year"] = date.getFullYear() + defaultyears;
    if (typeof (goal.goalendMonth) != 'undefined' && goal.goalendMonth != '' && goal.goalendMonth != '00')
        months[goal.goalendMonth - 1][type + "month"] = goal.goalendMonth;
    else
        months[date.getMonth()][type + "month"] = date.getMonth() + 1;
    if (typeof (goal.goalendDay) != 'undefined' && goal.goalendDay != '' && goal.goalendDay != '00')
        days[goal.goalendDay - 1][type + "day"] = goal.goalendDay;
    else
        days[date.getDate() - 1][type + "day"] = date.getDate();

    goal.months = months;
    goal.days = days;
    goal.years = years;
    return goal;
}

// Load Popular Accounts for CashEdge
function GetPopularAccounts()
{
    var popularAccounts = new Object();
    popularAccounts.popularAccountsLeft = [];
    popularAccounts.popularAccountsLeft[0] = {
        'name': 'Bank of America'
    };
    popularAccounts.popularAccountsLeft[1] = {
        'name': 'Charles Schwab'
    };
    popularAccounts.popularAccountsLeft[2] = {
        'name': 'Citibank'
    };
    popularAccounts.popularAccountsLeft[3] = {
        'name': 'Fidelity'
    };
    popularAccounts.popularAccountsLeft[4] = {
        'name': 'JP Morgan Chase'
    };
    popularAccounts.popularAccountsRight = [];
    popularAccounts.popularAccountsRight[0] = {
        'name': 'Merrill Lynch'
    };
    popularAccounts.popularAccountsRight[1] = {
        'name': 'Morgan Stanley'
    };
    popularAccounts.popularAccountsRight[2] = {
        'name': 'US Bank'
    };
    popularAccounts.popularAccountsRight[3] = {
        'name': 'Vanguard'
    };
    popularAccounts.popularAccountsRight[4] = {
        'name': 'Wells Fargo'
    };
    for (var attrname in financialData) {
        popularAccounts[attrname] = financialData[attrname];
    }
    return popularAccounts;
}
// Check Email
function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
// Check Query String
function getQueryVariable(variable) {
    var query = window.location.search.substring(1);
    var vars = query.split('&');
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split('=');
        if (decodeURIComponent(pair[0]) == variable) {
            return decodeURIComponent(pair[1]);
        }
    }
    return "";
}

function LoadScoreDialog() {
    "use strict";
    $('#testScoreBox').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#testScoreBox').css("height", "auto");
    $('#testScoreContents').html('<br><br>');
    if (currentScore != null)
    {
        $('#testScoreContents').append('<div style="width:300px;margin:0 auto;text-align:center"><div class="floatL profileEmph" style="width:150px">Point #</div><div class="floatL profileEmph" style="width:150px">Score</div><div class="clearOnly"></div></div>');
        for (var attr in currentScore)
        {
            if (attr.indexOf('point') != -1)
                $('#testScoreContents').append('<div style="width:300px;margin:0 auto;text-align:center"><div class="floatL" style="width:150px">' + attr.substr(5) + '</div><div class="floatL" style="width:150px">' + currentScore[attr] + '</div><div class="clearOnly"></div></div><hr>');
        }
    }
}

function LoadPCDialog() {
    "use strict";
    if (!$("#testScoreBox").is(":visible"))
    {
        $.scrollTo($('#body'), 200);
    }
    $('#testScoreBox').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#testScoreBox').css("height", "auto");
    $('#testScoreBox').css("width", "550px");
    $('#testScoreContents').html('');
    if (currentScore != null)
    {
        $.getJSON(getuserprofiledata, function(data) {
            var user_info_data = $.map(data.userprofiledata, function(value, index) {
                return [value];
            });
            // Global Variable
            var html = '<div>';
            html += '<div class="lightGray roundTop bevelBottom sectionHeaderDouble line2">';
            html += '<div class="floatL"><span class="accTitle">Profile Completeness</span></div>';
            html += '<div class="clearOnly"></div>';
            html += '</div>';
            html += '<ul id="accOverlayTabs"><li class="accOverlayTabOn" style="height:35px;line-height:35px;"><a href="#" class="tabAbout">&nbsp;About You</a></li></ul>';
            html += '<div style="color:#666">';
            html += '<table width="95%" cellpadding="3" align="center">';
            //fullname validation
            if (user_info_data[0] != "" && user_info_data[1] != "") {
                html += '<tr><td style="font-size:0.815em;width:70%">Full Name</td><td><button class="aboutHeader lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em;width:70%">Full Name</td><td><button class="aboutHeader lsPillBtnGray btn btn-success" id="aboutyou" style="width:120px">Complete Now</button></td></tr>';
            }
            //dob validation
            var matches = /^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/.exec(user_info_data[2]);
            if (matches != null) {
                html += '<tr><td style="font-size:0.815em">DOB</td><td><button class="aboutHeader lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">DOB</td><td><button class="aboutHeader lsPillBtnGray btn btn-success" id="aboutyou">Complete Now</button></td></tr>';
            }
            //zipcode validation
            if (user_info_data[3] != "") {
                html += '<tr><td style="font-size:0.815em">Zip Code</td><td><button class="aboutHeader lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Zip Code</td><td><button class="aboutHeader lsPillBtnGray btn btn-success" id="aboutyou">Complete Now</button></td></tr>';
            }
            //marital status
            if (user_info_data[4] != "") {
                html += '<tr><td style="font-size:0.815em">Marital Status</td><td><button class="aboutHeader lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Marital Status</td><td><button class="aboutHeader lsPillBtnGray btn btn-success" id="aboutyou" style="width:120px">Complete Now</button></td></tr>';
            }
            //noofchildren
            if (user_info_data[5] >= "0") {
                html += '<tr><td style="font-size:0.815em">Number of Children</td><td><button class="aboutHeader lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Number of Children</td><td><button class="aboutHeader lsPillBtnGray btn btn-success" id="aboutyou">Complete Now</button></td></tr>';
            }
            //childrensage
            if (user_info_data[5] > "0") {
                var cdob_matches = /^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/.exec(user_info_data[6].split(",")[0]);
                if (cdob_matches != null) {
                    html += '<tr><td style="font-size:0.815em">Children DOBs</td><td><button class="aboutHeader lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
                } else {
                    html += '<tr><td style="font-size:0.815em">Children DOBs</td><td><button class="aboutHeader lsPillBtnGray btn btn-success" id="aboutyou" style="width:120px">Complete Now</button></td></tr>';
                }
            } else {
                html += '<tr><td style="font-size:0.815em">Children DOBs</td><td><button class="aboutHeader lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';

            }
            //retirementstatus
            if (user_info_data[7] != "") {
                html += '<tr><td style="font-size:0.815em">Retired?</td><td><button class="aboutHeader lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Retired?</td><td><button class="aboutHeader lsPillBtnGray btn btn-success" id="aboutyou">Complete Now</button></td></tr>';
            }
            html += '</table>';
            html += '<ul id="accOverlayTabs"><li class="accOverlayTabOn" style="height:35px;line-height:35px;"><a href="#" class="tabFinancial">&nbsp;Financial Accounts & Details</a></li></ul>';
            html += '<table width="95%" cellpadding="3" align="center">';
            //Connecting Accounts
            if (user_info_data[35] > 0) {
                html += '<tr><td style="font-size:0.815em;width:70%">Connecting Accounts</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em;width:70%">Connecting Accounts</td><td><button class="lsPillBtnGray btn btn-success financialDetails">Complete Now</button></td></tr>';
            }
            //income
            if (user_info_data[9] > 0) {
                html += '<tr><td style="font-size:0.815em">Income</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Income</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addincome" style="width:120px">Complete Now</button></td></tr>';
            }
            //expenses
            if (user_info_data[10] > 0) {
                html += '<tr><td style="font-size:0.815em">Expenses</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Expenses</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addexpense" style="width:120px">Complete Now</button></td></tr>';
            }
            //debts
            if (user_info_data[11] > 0) {
                html += '<tr><td style="font-size:0.815em">Debts</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Debts</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="adddebt">Complete Now</button></td></tr>';
            }
            //assets
            if (user_info_data[12] > 0) {
                html += '<tr><td style="font-size:0.815em">Assets</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Assets</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addasset">Complete Now</button></td></tr>';
            }
            //insurance
            if (user_info_data[13] > 0) {
                html += '<tr><td style="font-size:0.815em">Insurance</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Insurance</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addinsurance">Complete Now</button></td></tr>';
            }
            //risk
            if (user_info_data[8] > 0) {
                html += '<tr><td style="font-size:0.815em">Risk Tolerance</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Risk Tolerance</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addrisk" style="width:120px">Complete Now</button></td></tr>';
            }
            html += '</table>';
            html += '<ul id="accOverlayTabs"><li class="accOverlayTabOn" style="height:35px;line-height:35px;"><a href="#" class="tabFinancial">&nbsp;Miscellaneous - Taxes</a></li></ul>';
            html += '<table width="95%" cellpadding="3" align="center">';
            if (user_info_data[14] != null && user_info_data[14] != "") {
                html += '<tr><td style="font-size:0.815em;width:70%">After completing your taxes…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em;width:70%">After completing your taxes…</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addtax">Complete Now</button></td></tr>';
            }
            if (user_info_data[16] != null && user_info_data[16] != "" && user_info_data[16] != 3) {
                html += '<tr><td style="font-size:0.815em">How much?</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">How much?</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addtax">Complete Now</button></td></tr>';
            }
            if (user_info_data[15] != null && user_info_data[15] != "" && user_info_data[15] != 4) {
                html += '<tr><td style="font-size:0.815em">Combining federal and state…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Combining federal and state…</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addtax">Complete Now</button></td></tr>';
            }
            if (user_info_data[17] != null && user_info_data[17] != "") {
                html += '<tr><td style="font-size:0.815em">Are your retirement contributions…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Are your retirement contributions…</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addtax">Complete Now</button></td></tr>';
            }
            if (user_info_data[18] != null && user_info_data[18] != "") {
                html += '<tr><td style="font-size:0.815em">When filing your annual tax return…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">When filing your annual tax return…</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addtax">Complete Now</button></td></tr>';
            }
            html += '</table>';
            html += '<ul id="accOverlayTabs"><li class="accOverlayTabOn" style="height:35px;line-height:35px;"><a href="#" class="tabFinancial">&nbsp;Miscellaneous - Estate Planning</a></li></ul>';
            html += '<table width="95%" cellpadding="3" align="center">';
            if (user_info_data[19] != null && user_info_data[19] != "") {
                html += '<tr><td style="font-size:0.815em;width:70%">Do you have a will or trust…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em;width:70%">Do you have a will or trust…</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addestate">Complete Now</button></td></tr>';
            }
            if (user_info_data[20] != null && user_info_data[21] != null && user_info_data[20] != "" && user_info_data[21] != "") {
                html += '<tr><td style="font-size:0.815em">When was it last reviewed?</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">When was it last reviewed?</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addestate">Complete Now</button></td></tr>';
            }
            if (user_info_data[22] != null && user_info_data[22] != "") {
                html += '<tr><td style="font-size:0.815em">Do you have an information list…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Do you have an information list…</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addestate">Complete Now</button></td></tr>';
            }
            if (user_info_data[23] != null && user_info_data[22] == 1) {
                if (user_info_data[23] != null && user_info_data[23] != "") {
                    html += '<tr><td style="font-size:0.815em">Have you told the right person…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
                } else {
                    html += '<tr><td style="font-size:0.815em">Have you told the right person…</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addestate">Complete Now</button></td></tr>';
                }
            }
            if (user_info_data[23] != null && user_info_data[22] == 0) {
                html += '<tr><td style="font-size:0.815em">Have you told the right person…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Not Required</button></td></tr>';
            }
            if (user_info_data[24] != null && user_info_data[24] != "") {
                html += '<tr><td style="font-size:0.815em">Do you own anything that needs to be…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Do you own anything that needs to be…</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addestate">Complete Now</button></td></tr>';
            }
            if (user_info_data[25] != null && user_info_data[25] != "") {
                html += '<tr><td style="font-size:0.815em">Should you or your spouse…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Should you or your spouse…</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addestate">Complete Now</button></td></tr>';
            }
            html += '</table>';
            html += '<ul id="accOverlayTabs"><li class="accOverlayTabOn" style="height:35px;line-height:35px;"><a href="#" class="tabFinancial">&nbsp;Miscellaneous - More</a></li></ul>';
            html += '<table width="95%" cellpadding="3" align="center">';
            if (user_info_data[26] != null && user_info_data[26] != "") {
                html += '<tr><td style="font-size:0.815em;width:70%">Do you manually move money…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em;width:70%">Do you manually move money…</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addmore">Complete Now</button></td></tr>';
            }
            if (user_info_data[27] != null && user_info_data[27] != "") {
                html += '<tr><td style="font-size:0.815em">Do you have investments set to…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Do you have investments set to…</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addmore">Complete Now</button></td></tr>';
            }
            if (user_info_data[28] != null && user_info_data[28] != "") {
                html += '<tr><td style="font-size:0.815em">Once money is contributed to…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px"  disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Once money is contributed to…</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addmore">Complete Now</button></td></tr>';
            }
            if (user_info_data[29] != null && user_info_data[29] != "") {
                html += '<tr><td style="font-size:0.815em">Have you considered how liquid…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px"  disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Have you considered how liquid…</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addmore">Complete Now</button></td></tr>';
            }
            if (user_info_data[30] != null && user_info_data[30] != "") {
                html += '<tr><td style="font-size:0.815em">Do you currently give to charity…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Do you currently give to charity…</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addmore">Complete Now</button></td></tr>';
            }
            if (user_info_data[31] != null && user_info_data[31] != "") {
                html += '<tr><td style="font-size:0.815em">Enter your approximate credit score…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">Enter your approximate credit score…</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addmore">Complete Now</button></td></tr>';
            }
            if (user_info_data[32] != null && user_info_data[33] != null && user_info_data[32] != "" && user_info_data[33] != "") {
                html += '<tr><td style="font-size:0.815em">When was it last reviewed…</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em">When was it last reviewed…</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addmore">Complete Now</button></td></tr>';
            }
            html += '</table>';
            html += '<ul id="accOverlayTabs"><li class="accOverlayTabOn" style="height:35px;line-height:35px;"><a href="#" class="tabFinancial">&nbsp;Goal</a></li></ul>';
            html += '<table width="95%" cellpadding="3" align="center">';
            if (user_info_data[34] > 0) {
                html += '<tr><td style="font-size:0.815em;width:70%">Add a Goal</td><td><button class="lsPillBtnGreen btn btn-success" style="width:120px" disabled>Completed</button></td></tr>';
            } else {
                html += '<tr><td style="font-size:0.815em;width:70%">Add a Goal</td><td><button class="lsPillBtnGray btn btn-success popLayerButton" id="addgoal">Complete Now</button></td></tr>';
            }
            html += '</table>';
            html += '</div>';
            html += '<div class="profileBottomRow round">&nbsp;</div>';
            $('#testScoreContents').append(html);

        });

    }
}

function RemoveScoreDialog() {
    "use strict";
    $('#testScoreContents').html('');
    $('#testScoreBox').hide();
    $('#darkBackground').hide();
}

if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function(needle) {
        for (var i = 0; i < this.length; i++) {
            if (this[i] === needle) {
                return i;
            }
        }
        return -1;
    };
}

// Load Investment Position for UI
function createInvestmentPositions(text, key, start, end, obj) {
    var html = '';
    for (var i = start; i < end; i++)
    {
        var ticker = (typeof (obj) != 'undefined') ? obj[i].ticker : "";
        var amount = (typeof (obj) != 'undefined') ? obj[i].amount : "";
        html += '<div class="floatL">';
        html += '<span class="profileEmph" style="font-size:0.9em">Symbol/Ticker';
        html += '</span>';
        html += '<br>';
        html += '<input type="text" placeholder="Enter ticker" class="accTicker" value="' + ticker + '" id="' + key + text + 'Ticker' + i + '">';
        html += '</div>';
        html += '<div class="floatL" style="padding-left:25px">';
        html += '<span class="profileEmph" style="font-size:0.9em">Amount';
        html += '</span>';
        html += '<br>';
        html += '<span class="profileEmphDollar" style="font-size:1.2em">$';
        html += '</span>';
        html += '<input type="text" placeholder="Enter amount" class="accTicker dollaramount" value="' + amount + '" id="' + key + text + 'TickerPrice' + i + '">';
        html += '</div>';
        html += '<div class="clearOnly"></div>';
    }
    return html;
}

function RetoggleOnOff(key) {
    var obj = [];
    for (var attrname in financialData)
    {
        if (key == "assets" && (attrname == "cash" || attrname == "investment" || attrname == "silent" || attrname == "other"))
            obj = obj.concat(financialData[attrname]);
        else if (key == attrname)
            obj = obj.concat(financialData[attrname]);
    }

    allowToggle = false;
    updateCollapse = false;

    for (var i in obj)
    {
        if (obj[i]["status"] != 1)
        {
            var keyName = calculateKey(obj[i]["accttype"], key);
            $("#" + obj[i]["id"] + keyName + "ConnectAccount").hide();
            $("#" + obj[i]["id"] + "PermissionToggle" + keyName).show();
            if (currentOpenField == obj[i]["id"])
            {
                $("#" + obj[i]["id"] + keyName + "FAQArrow").click();
                $.scrollTo($("#" + obj[i]["id"] + keyName + "ProfileDataBox"), 0);
                if (throughActionStep) {
                    var formFields = {
                        event: currentActionEvent,
                        id: currentactionstepid
                    }

                    $.ajax({
                        url: addTrackuserURL,
                        type: 'POST',
                        dataType: "json",
                        data: formFields
                    });
                    throughActionStep = false;
                }
            }

            if (obj[i]["status"] == 2)
            {
                $("#" + obj[i]["id"] + "toggleOnLabel" + keyName).click();
                $('#' + obj[i]["id"] + keyName + "AmountSummary").hide();
            }
        }
    }
    if (currentOpenField == '' && currentOpenType != '') {
        $("#" + currentOpenType + "AddAccount").click();
        if (throughActionStep) {
            var formFields = {
                event: currentActionEvent,
                id: currentactionstepid
            }

            $.ajax({
                url: addTrackuserURL,
                type: 'POST',
                dataType: "json",
                data: formFields
            });
            throughActionStep = false;
        }
    }
    currentOpenField = '';
    currentOpenType = '';
    allowToggle = true;
    updateCollapse = true;
}

function CheckCashedgeResponse(data, key) {
    //var dataInfo = data.info;
    // var data = jQuery.parseJSON( '{"status":"OK","reenter":"1","fiid":"77","msg":"We are not able to update this account at this time as we are currently upgrading our data collection process for this financial institution. Please try again later","info":1}' );


    if (data == null) {
        // THAYUB
    }
    else if (typeof (data.code) != 'undefined' && data.code == '304') {
        // THAYUB
    }
    else if (data.ismfa) {
        require(
                ['views/profile/mfaquestion'],
                function(mfaquestionView) {
                    data.id = key;
                    mfaquestionView.render(data);
                }
        );
    } else if (data.reenter == 1) {
        $.ajax({
            url: getfiConnectUrl,
            type: 'GET',
            dataType: "json",
            data: "fiid=" + data.fiid,
            success: function(data1) {
                timeoutPeriod = defaultTimeoutPeriod;
                if (data1.status == "OK") {
                    require(
                            ['views/profile/accountsignin'],
                            function(accountsigninView) {
                                data1.items[0].id = key;
                                accountsigninView.render(data1.items[0]);
                                if (typeof (data.info) != 'undefined' && data.info == '1') {
                                    $('#' + key + 'downloadMessage').html(data1.message);
                                } else {
                                    $('#' + key + 'downloadMessage').html('The account credentials you provided did not work. Please re-enter your credentials below. <br>If you have entered correct credentials, please enable access to financial management tools on your financial institution\'s website.');
                                }
                                $('#' + key + 'btnCancelItem').hide();
                                $('#' + key + 'btnBackItem').hide();
                            }
                    );
                }
            }
        });
    } else if (data.status == "OK") {

        $("#" + key + "connectDesc").children('.profileEmph').html(data.message);
        if (typeof (data.nonew) != 'undefined' && data.nonew == 1) {
            $("#" + key + "profileAssetsStatus").hide();
        }
        if (typeof (data.connected) != 'undefined' && data.connected.length > 0) {
            for (var i = 0; i < data.connected.length; i++) {
                var header = "#connectedDebtsHeader";
                var content = "#connectedDebts";
                var div = "addDebts";
                if (data.connected[i].accounttype == "Assets") {
                    var header = "#connectedAssetsHeader";
                    var content = "#connectedAssets";
                    var div = "addAssets";
                } else if (data.connected[i].accounttype == "Insurance") {
                    var header = "#connectedInsuranceHeader";
                    var content = "#connectedInsurance";
                    var div = "addInsurance";
                }
                $(header).show();
                $(content).append('<div style="width:90%;padding-top:5px"><a id="' + data.connected[i].id + div + '" class="' + div + '" href="#">' + data.connected[i].name + '</a></div>');
            }
        }
        if (typeof (data.pending) != 'undefined' && data.pending.length > 0) {
            require(
                    ['views/profile/accountsce'],
                    function(accountsceView) {
                        data.id = key;
                        accountsceView.render(data);
                        init();
                    }
            );
        }

        userData.notification = parseInt(userData.notification) + 1;
        $("#existingConnectCount").val(parseInt($("#existingConnectCount").val()) - 1);
        if ($("#existingConnectCount").val() == "0") {
            $("#existingHeader").hide();
        }
    } else if (data.status == "ERROR") {

        $("#" + key + "connectDesc").children('.profileEmph').html(data.message);
        $("#" + key + "RetryHarvesting").attr("disabled", true);
        $('#' + name).removeAttr("disabled");
        if (data.info == 2) {
            $("#" + key + "connectDesc").html(data.message);
        }
    }
}

function alignScore(fieldScore, fieldHorseshoe, simScore, imageNum)
{
    if (simScore < 10) {
        $('#' + fieldScore).css("left", "135px");
        $('#' + fieldScore).css("letter-spacing", "0em");
    } else if (simScore < 100) {
        $('#' + fieldScore).css("left", "120px");
        $('#' + fieldScore).css("letter-spacing", "0em");
    } else if (simScore === 1000) {
        $('#' + fieldScore).css("left", "85px");
        $('#' + fieldScore).css("letter-spacing", "-0.05em");
    } else {
        $('#' + fieldScore).css("left", "100px");
        $('#' + fieldScore).css("letter-spacing", "0em");
    }
    $('#' + fieldScore).text(simScore);
    $('#' + fieldHorseshoe).attr("src", "./ui/images/horseshoes/variations/myscore/MyScoreHorseShoe" + imageNum + ".png");
}

function alignCongratsScore(fieldScore, fieldHorseshoe, simScore, imageNum)
{
    if (simScore === 1000) {
        $('#' + fieldScore).css("left", "90px");
        $('#' + fieldScore).css("letter-spacing", "-0.05em");
        $('#' + fieldScore).css("font-size", "50px");
    }
    else {
        $('#' + fieldScore).css("left", "100px");
        $('#' + fieldScore).css("font-size", "56px");
        $('#' + fieldScore).css("letter-spacing", "0em");
    }
    $('#' + fieldScore).text(simScore);
    $('#' + fieldHorseshoe).attr("src", "./ui/images/horseshoes/variations/myscore/MyScoreHorseShoe" + imageNum + ".png");
}

function HandleNodeResponse(data, congratsTemplate, Handlebars) {
    if (data.type == 'notification') {
        $.ajax({
            url: getNotificationDataURL + "?forceUser=" + forceUserNotifications,
            type: 'GET',
            dataType: "json",
            success: function(data) {
                $('#headNotifyTags').html(data.total);
                $('#menuNotifyTags').html(data.total);
            }
        });
        if ($("#notificationBox").is(":visible")) {
            $('#notifications').click();
        }
    }
    if (data.type == 'score') {
        if (!$("#profileBox").is(":visible")) {
            $('.myscoreHorseshoe').trigger('change');
        }
    }
    if (data.type == 'actionstep') {
        if (!$("#profileBox").is(":visible")) {
            $('#ActionStepContent').trigger('change');
        }
        if ($("#comparisonBox").is(":visible") && currentactionstepid > 0 && $("#isActionStep").val() == "true")
        {
            $('#fakeActionStep').val(currentactionstepid);
            $("#fakeActionStep").click();
        } else if (!$("#darkBackground").is(":visible")) {
            $.ajax({
                url: finalscoreURL,
                type: 'POST',
                dataType: "json",
                success: function(getAll) {
                    if (getAll.status == "OK") {
                        $.ajax({
                            url: userGetScoreURL,
                            type: 'GET',
                            dataType: "json",
                            success: function(scoreData) {
                                if (scoreData.status == "OK") {
                                    window.parent.removeLayover();
                                    var source = $(congratsTemplate).html();
                                    var template = Handlebars.compile(source);
                                    $.scrollTo($('#body'), 200);
                                    $('#comparisonBox').show();
                                    $('#darkBackground').show();
                                    $('#darkBackground').fadeTo("fast", 0.6);
                                    $('#comparisonBox').css("height", 'auto');
                                    $('#comparisonBox').html(template(getAll));
                                    var simScore = parseInt(scoreData.score.totalscore);
                                    var imageId = Math.round((simScore * 20) / 1000);
                                    imageId = (imageId > 0) ? imageId : 0;
                                    imageId = (imageId < 20) ? imageId : 20;
                                    alignCongratsScore('reportScore', 'reportHorseshoe', simScore, imageId);
                                }
                            }
                        });
                    }
                }
            });
        }
    }
    if (data.type == 'login') {
        if ($(".keepAliveTimeout:visible").length > 0) {
            window.location.reload();
        }
    }
    if (data.type == 'logout') {
        window.location.reload();
    }

}
//used to manage advisor credentials
function popUpManageCredentials() {
    "use strict";
    if (!$("#profileBox").is(":visible"))
        $.scrollTo($('#body'), 200);

    $('#profileBox').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#profileBox').css("height", "auto");
}

function flexDateDiff(dateFrom, dateTo) {
    var from = {
        d: dateFrom.getDate(),
        m: dateFrom.getMonth() + 1,
        y: dateFrom.getFullYear()
    };

    var to = {
        d: dateTo.getDate(),
        m: dateTo.getMonth() + 1,
        y: dateTo.getFullYear()
    };

    var daysFebruary = to.y % 4 != 0 || (to.y % 100 == 0 && to.y % 400 != 0) ? 28 : 29;
    var daysInMonths = [0, 31, daysFebruary, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    if (to.d < from.d) {
        to.d += daysInMonths[parseInt(to.m)];
        from.m += 1;
    }
    if (to.m < from.m) {
        to.m += 12;
        from.y += 1;
    }

    return {
        days: to.d - from.d,
        months: to.m - from.m,
        years: to.y - from.y
    };
}

function restructDebt(debtArray, payments, maxmonths, oldIncrease, sign) {
    var maxPayment = 0;
    var minPayment = 0;
    var totalDebt = 0;
    var i = 0;
    for (i = 0; i < debtArray.length; i++) {
        var debt = debtArray[i];
        var minimumPayment = debt.minimum;
        var monthlyPayment = debt.payment;
        totalDebt += debt.balance;

        if (minimumPayment > monthlyPayment) {
            maxPayment += minimumPayment;
        } else {
            maxPayment += monthlyPayment;
        }
        minPayment += minimumPayment;
    }

    if (typeof (payments) != 'undefined') {
        if (maxPayment < minPayment) {
            maxPayment = minPayment;
        }
        if (payments < maxPayment) {
            payments = maxPayment;
        }
    }

    var resultDebtArray = [];
    var debtPaidOff = false;
    var totalInterest = 0;
    var months = 0;
    var extraRollover = 0;
    while (!debtPaidOff) {
        var currentDebtPaidOff = true;
        var index = 0;
        var rolloverPayment = payments - minPayment + extraRollover;
        var rolloverAdded = false;

        var i = 0;
        for (i = 0; i < debtArray.length; i++) {
            var debt = debtArray[i];
            var monthlyRate = debt.rate / 12;
            var amount = debt.balance;
            var monthlyPayment = debt.minimum;

            if (typeof (resultDebtArray[index]) == 'undefined') {
                // Setting Default Value
                resultDebtArray[index] = [];
                resultDebtArray[index]['amount'] = amount;
            } else if (resultDebtArray[index]['amount'] > 0) {
                if (!rolloverAdded) {
                    monthlyPayment += rolloverPayment;
                }
                // Calculating new amount and interest
                var previousAmount = resultDebtArray[index]['amount'];
                var previousInterest = resultDebtArray[index]['interest'];
                resultDebtArray[index]['amount'] = (previousAmount + previousInterest - monthlyPayment);
                if (resultDebtArray[index]['amount'] < 0) {
                    rolloverPayment = Math.abs(resultDebtArray[index]['amount']);
                    extraRollover += debt.minimum;
                } else {
                    rolloverAdded = true;
                }
            }
            resultDebtArray[index]['interest'] = resultDebtArray[index]['amount'] * monthlyRate;
            if (resultDebtArray[index]['interest'] > 0) {
                totalInterest += resultDebtArray[index]['interest'];
                currentDebtPaidOff = false;
            }
            index++;
        }

        debtPaidOff = currentDebtPaidOff;
        if (months > 1001 || (typeof (maxmonths) != 'undefined' && maxmonths < months)) {
            break;
        }
        months++;
    }
    months--;
    if (months < 0) {
        months = 0;
    }

    if (typeof (maxmonths) == 'undefined') {
        return {months: months, interest: totalInterest, payments: payments};
    }
    else
    {
        if (debtArray.length == 0 || totalDebt <= 0) {
            return {payments: 0, interest: 0, increasePayment: 0, sign: sign, months: 0};
        }
        var i = 0;
        var newtotalDebt = 0;
        for (i = 0; i < resultDebtArray.length; i++) {
            var debt = resultDebtArray[i];
            newtotalDebt += debt["amount"];
        }
        var increasePayment = oldIncrease;
        if (newtotalDebt < 0) {
            if (sign == "+") {
                sign = "-";
                increasePayment = 0 - increasePayment / 10;
            }
        }
        else
        {
            if (sign == "-") {
                sign = "+";
                increasePayment = 0 - increasePayment / 10;
            }
        }

        var newPayment = payments + increasePayment;
        if (newPayment < maxPayment) {
            newPayment = maxPayment;
        }
        return {payments: newPayment, interest: totalInterest, increasePayment: increasePayment, sign: sign, months: months};
    }
}

function LookUpGoalDragObject(fromObj, toObj) {
    var goalAjax = [];
    var toIndex = parseInt(toObj.id.substring(0, toObj.id.indexOf('PriorityDiv')));
    var fromIndex = parseInt(fromObj.id.substring(0, fromObj.id.indexOf('PriorityDiv')));
    var currentIndex = fromIndex;
    var finalHtml = $(fromObj).html();
    var finalGoalObj = $(fromObj).find(".goalDroppableDiv")[0];
    var finalGoalId = parseInt(finalGoalObj.id.substring(0, finalGoalObj.id.indexOf('DroppableDiv')));

    var oldPriority = -1;
    var oldIndex = -1;
    for (var i = 0; i < financialData.goals.length; i++) {
        if (finalGoalId == financialData.goals[i].id) {
            oldPriority = financialData.goals[i].goalpriority;
            oldIndex = i;
            break;
        }
    }

    if (toIndex > fromIndex + 1 || isNaN(toIndex)) {
        while (currentIndex < toIndex - 1 || isNaN(toIndex)) {
            if ($("#" + (currentIndex + 1) + "PriorityDiv").length > 0) {
                var nextHtml = $("#" + (currentIndex + 1) + "PriorityDiv").html();
                var prevGoalObj = $("#" + (currentIndex + 1) + "PriorityDiv").find(".goalDroppableDiv")[0];
                var prevGoalId = parseInt(prevGoalObj.id.substring(0, prevGoalObj.id.indexOf('DroppableDiv')));
                var currentGoalObj = $("#" + currentIndex + "PriorityDiv").find(".goalDroppableDiv")[0];
                var currentGoalId = parseInt(currentGoalObj.id.substring(0, currentGoalObj.id.indexOf('DroppableDiv')));
                for (var i = 0; i < financialData.goals.length; i++) {
                    if (prevGoalId == financialData.goals[i].id) {
                        currentPriority = financialData.goals[i].goalpriority;
                        financialData.goals[i].goalpriority = oldPriority;
                        for (var j = 0; j < goalSnapshot.length; j++) {
                            if (financialData.goals[i].id == goalSnapshot[j].id) {
                                goalSnapshot[j].goalpriority = oldPriority;
                            }
                        }
                        if (financialData.goals[i].goalpriority == 0) {
                            financialData.goals[i].goalpriority = 1;
                            for (var j = 0; j < goalSnapshot.length; j++) {
                                if (financialData.goals[i].id == goalSnapshot[j].id) {
                                    goalSnapshot[j].goalpriority = 1;
                                }
                            }
                        }
                        goalAjax[goalAjax.length] = financialData.goals[i].id + "|" + financialData.goals[i].goalpriority;
                        oldPriority = currentPriority;
                        break;
                    }
                }
                $("#" + currentIndex + "PriorityDiv").html(nextHtml);
                currentIndex++;
            }
            else
            {
                break;
            }
        }
        if (oldPriority != -1 && oldIndex != -1) {
            financialData.goals[oldIndex].goalpriority = oldPriority;
            for (var j = 0; j < goalSnapshot.length; j++) {
                if (financialData.goals[oldIndex].id == goalSnapshot[j].id) {
                    goalSnapshot[j].goalpriority = oldPriority;
                }
            }
            if (financialData.goals[oldIndex].goalpriority == 0) {
                financialData.goals[oldIndex].goalpriority = 1;
                for (var j = 0; j < goalSnapshot.length; j++) {
                    if (financialData.goals[oldIndex].id == goalSnapshot[j].id) {
                        goalSnapshot[j].goalpriority = 1;
                    }
                }
            }
            goalAjax[goalAjax.length] = financialData.goals[oldIndex].id + "|" + financialData.goals[oldIndex].goalpriority;
        }
        $("#" + currentIndex + "PriorityDiv").html(finalHtml);
    }
    else if (toIndex < fromIndex)
    {
        while (currentIndex > toIndex) {
            var nextHtml = $("#" + (currentIndex - 1) + "PriorityDiv").html();
            var prevGoalObj = $("#" + (currentIndex - 1) + "PriorityDiv").find(".goalDroppableDiv")[0];
            var prevGoalId = parseInt(prevGoalObj.id.substring(0, prevGoalObj.id.indexOf('DroppableDiv')));
            var currentGoalObj = $("#" + currentIndex + "PriorityDiv").find(".goalDroppableDiv")[0];
            var currentGoalId = parseInt(currentGoalObj.id.substring(0, currentGoalObj.id.indexOf('DroppableDiv')));
            for (var i = 0; i < financialData.goals.length; i++) {
                if (prevGoalId == financialData.goals[i].id) {
                    currentPriority = financialData.goals[i].goalpriority;
                    financialData.goals[i].goalpriority = oldPriority;
                    for (var j = 0; j < goalSnapshot.length; j++) {
                        if (financialData.goals[i].id == goalSnapshot[j].id) {
                            goalSnapshot[j].goalpriority = oldPriority;
                        }
                    }
                    if (financialData.goals[i].goalpriority == 0) {
                        financialData.goals[i].goalpriority = 1;
                        for (var j = 0; j < goalSnapshot.length; j++) {
                            if (financialData.goals[i].id == goalSnapshot[j].id) {
                                goalSnapshot[j].goalpriority = 1;
                            }
                        }
                    }
                    goalAjax[goalAjax.length] = financialData.goals[i].id + "|" + financialData.goals[i].goalpriority;
                    oldPriority = currentPriority;
                    break;
                }
            }
            $("#" + currentIndex + "PriorityDiv").html(nextHtml);
            currentIndex--;
        }
        if (oldPriority != -1 && oldIndex != -1) {
            financialData.goals[oldIndex].goalpriority = oldPriority;
            for (var j = 0; j < goalSnapshot.length; j++) {
                if (financialData.goals[oldIndex].id == goalSnapshot[j].id) {
                    goalSnapshot[j].goalpriority = oldPriority;
                }
            }
            if (financialData.goals[oldIndex].goalpriority == 0) {
                financialData.goals[oldIndex].goalpriority = 1;
                for (var j = 0; j < goalSnapshot.length; j++) {
                    if (financialData.goals[oldIndex].id == goalSnapshot[j].id) {
                        goalSnapshot[j].goalpriority = 1;
                    }
                }
            }
            goalAjax[goalAjax.length] = financialData.goals[oldIndex].id + "|" + financialData.goals[oldIndex].goalpriority;
        }
        $("#" + currentIndex + "PriorityDiv").html(finalHtml);
    }
    init();
    calculateGoals = false;
    ResetGoalAmounts();
    calculateGoals = true;
    RecalculateGoalAmounts(true);
    SetCalculatedGoalAmounts();
    if (goalAjax.length > 0) {
        var formValues = {
            goals: goalAjax,
        };

        $.ajax({
            url: reprioritizeGoalsURL,
            type: 'POST',
            dataType: "json",
            data: formValues,
            success: function(data) {
            }
        });
    }
}

function LookUpAccountDragObject(fromObj, toObj) {
    var accountAjax = [];
    var toIndex = parseInt(toObj.id.substring(0, toObj.id.indexOf('PriorityDiv')));
    var fromIndex = parseInt(fromObj.id.substring(0, fromObj.id.indexOf('PriorityDiv')));
    var currentIndex = fromIndex;
    var finalHtml = $(fromObj).html();
    var finalAccountObj = $(fromObj).find(".accountDroppableDiv")[0];
    var finalAccountId = parseInt(finalAccountObj.id.substring(0, finalAccountObj.id.indexOf('DroppableDiv')));

    var oldPriority = -1;
    var oldIndex = -1;

    var key = $("#fiType").val();
    var obj = [];
    for (var attrname in financialData)
    {
        if (key == "assets" && (attrname == "cash" || attrname == "investment" || attrname == "silent" || attrname == "other"))
            obj = obj.concat(financialData[attrname]);
        else if (key == attrname)
            obj = obj.concat(financialData[attrname]);
    }

    obj.sort(function (a, b) {
        return a.priority - b.priority;
    });

    for (var i = 0; i < obj.length; i++) {
        if (finalAccountId == obj[i].id) {
            oldPriority = obj[i].priority;
            oldIndex = i;
            break;
        }
    }

    if (toIndex > fromIndex + 1 || isNaN(toIndex)) {
        while (currentIndex < toIndex - 1 || isNaN(toIndex)) {
            if ($("#" + (currentIndex + 1) + "PriorityDiv").length > 0) {
                var nextHtml = $("#" + (currentIndex + 1) + "PriorityDiv").html();
                var prevAccountObj = $("#" + (currentIndex + 1) + "PriorityDiv").find(".accountDroppableDiv")[0];
                var prevAccountId = parseInt(prevAccountObj.id.substring(0, prevAccountObj.id.indexOf('DroppableDiv')));
                var currentAccountObj = $("#" + currentIndex + "PriorityDiv").find(".accountDroppableDiv")[0];
                var currentAccountId = parseInt(currentAccountObj.id.substring(0, currentAccountObj.id.indexOf('DroppableDiv')));
                for (var i = 0; i < obj.length; i++) {
                    if (prevAccountId == obj[i].id) {
                        currentPriority = obj[i].priority;
                        obj[i].priority = oldPriority;
                        accountAjax[accountAjax.length] = obj[i].id + "|" + obj[i].priority;
                        oldPriority = currentPriority;
                        break;
                    }
                }
                $("#" + currentIndex + "PriorityDiv").html(nextHtml);
                currentIndex++;
            }
            else
            {
                break;
            }
        }
        if (oldPriority != -1 && oldIndex != -1) {
            obj[oldIndex].priority = oldPriority;
            accountAjax[accountAjax.length] = obj[oldIndex].id + "|" + obj[oldIndex].priority;
        }
        $("#" + currentIndex + "PriorityDiv").html(finalHtml);
    }
    else if (toIndex < fromIndex)
    {
        while (currentIndex > toIndex) {
            var nextHtml = $("#" + (currentIndex - 1) + "PriorityDiv").html();
            var prevAccountObj = $("#" + (currentIndex - 1) + "PriorityDiv").find(".accountDroppableDiv")[0];
            var prevAccountId = parseInt(prevAccountObj.id.substring(0, prevAccountObj.id.indexOf('DroppableDiv')));
            var currentAccountObj = $("#" + currentIndex + "PriorityDiv").find(".accountDroppableDiv")[0];
            var currentAccountId = parseInt(currentAccountObj.id.substring(0, currentAccountObj.id.indexOf('DroppableDiv')));
            for (var i = 0; i < obj.length; i++) {
                if (prevAccountId == obj[i].id) {
                    currentPriority = obj[i].priority;
                    obj[i].priority = oldPriority;
                    accountAjax[accountAjax.length] = obj[i].id + "|" + obj[i].priority;
                    oldPriority = currentPriority;
                    break;
                }
            }
            $("#" + currentIndex + "PriorityDiv").html(nextHtml);
            currentIndex--;
        }
        if (oldPriority != -1 && oldIndex != -1) {
            obj[oldIndex].priority = oldPriority;
            accountAjax[accountAjax.length] = obj[oldIndex].id + "|" + obj[oldIndex].priority;
        }
        $("#" + currentIndex + "PriorityDiv").html(finalHtml);
    }
    init();
    ResetAccountAmounts();
    RetoggleOnOff(key);
    if (accountAjax.length > 0) {
        var formValues = {};
        formValues[key] = accountAjax;
        var url = reprioritizeAssetsURL;
        if(key == "insurance") { url = reprioritizeInsuranceURL; }
        else if(key == "debts") { url = reprioritizeDebtsURL; }

        $.ajax({
            url: url,
            type: 'POST',
            dataType: "json",
            data: formValues,
            success: function(data) {
            }
        });
    }
}

//preserving the bookmark after redirecting from other pages//
// start //
(function($) {

    var jump = function(e)
    {
        if (e) {
            e.preventDefault();
            var target = $(this).attr("href");
        } else {
            var target = location.hash;
        }
        //alert(target);

        $('html,body').animate(
                {
                    //scrollTop: $(target).offset().top
                }, 1000, function()
        {
            location.hash = target;
        });

    }

    $('html, body').hide()

    $(document).ready(function()
    {
        if (location.hash) {
            setTimeout(function() {
                $('html, body').scrollTop(0).show()
                jump()
            }, 0);
        } else {
            $('html, body').show()
        }
    });

})(jQuery);

// end //

function advisorHelp(val1, val2, val3) {
    var uid = val1;
    var actionid = val2;
    var id = val3;
    var formValues = {
        uid: val1,
        actionid: val2,
        id: val3,
        advisorhelpstatus: '1'
    };
    $.ajax({
        url: getadvisorhelp,
        type: 'POST',
        dataType: "json",
        data: formValues,
        success: function(data) {
            $("#advhelp" + id).html('<button class="lsPillBtnGreen btn btn-success" disabled>Advisor Notified</button>');
        }
    });
}


//used for account notification popup
function openSubscriptionDialog() {
    require(['views/account/subscription'],
            function(subscriptionV) {
                subscriptionV.render();
                popUpActionStep();
            }
    );
}


//used to open the credit card update form for renewing subscriptions
function openCreditCardDialog() {
    require(
        ['views/account/account','views/account/subscriptiondetails','views/account/creditcard'],
        function(account,subscriptiondetails,creditcard) {
            account.render(userData);
            subscriptiondetails.render();
            $("#tabBillingSummary").removeClass('selected');
            $("#tabCreditCard").addClass('selected');
            creditcard.render(true);
            init();
        }
    );
}


function CalculateAssetContributions(income, assets) {
    // Calculate Contributions Amounts
    var iraMax = 5500;
    var over50IraMax = 6500;
    var crMax = 17500;
    var over50CrMax = 23000;
    var crContribution = 0;
    var empCrContribution = 0;
    var iraContribution = 0;
    var educContribution = 0;
    var brokContribution = 0;
    var bankContribution = 0;

    for (var i = 0; i < assets.length; i++)
    {
        if (assets[i].status == "0") {
            switch (assets[i].accttype) {
                case "BANK":
                    bankContribution = bankContribution + assets[i].contribution.replace(/,/g, '') * 1;
                    break;
                case "CR":
                    crContribution = crContribution + assets[i].contribution.replace(/,/g, '') * 1;
                    empCrContribution = empCrContribution + (assets[i].empcontribution.replace(/,/g, '') / 100) * income;
                    break;
                case "IRA":
                    iraContribution = iraContribution + assets[i].contribution.replace(/,/g, '') * 1;
                    break;
                case "EDUC":
                    educContribution = educContribution + assets[i].contribution.replace(/,/g, '') * 1;
                    break;
                case "BROK":
                    brokContribution = brokContribution + assets[i].contribution.replace(/,/g, '') * 1;
                    break;
            }
        }
    }

    crContribution = Math.round(crContribution);
    empCrContribution = Math.round(empCrContribution);
    iraContribution = Math.round(iraContribution);
    educContribution = Math.round(educContribution);
    brokContribution = Math.round(brokContribution);
    bankContribution = Math.round(bankContribution);
    var extraCrContribution = 0;
    var extraIraContribution = 0;

    var currentAge = financialData.age;
    if (currentAge <= 49) {
        if (crContribution * 12 > crMax) {
            crContribution = Math.round(crMax / 12);
        }
        if (iraContribution * 12 > iraMax) {
            iraContribution = Math.round(iraMax / 12);
        }
        extraCrContribution = Math.round(crMax / 12) - crContribution;
        extraIraContribution = Math.round(iraMax / 12) - iraContribution;
    }
    else if (currentAge >= 50) {
        if (crContribution * 12 > over50CrMax) {
            crContribution = Math.round(over50CrMax / 12);
        }
        if (iraContribution * 12 > over50IraMax) {
            iraContribution = Math.round(over50IraMax / 12);
        }
        extraCrContribution = Math.round(over50CrMax / 12) - crContribution;
        extraIraContribution = Math.round(over50IraMax / 12) - iraContribution;
    }

    return {"bank": bankContribution, "ira": iraContribution, "cr": crContribution, "educ": educContribution, "brok": brokContribution, "crEmp": empCrContribution, "extraCr": extraCrContribution, "extraIra": extraIraContribution};
}

function CalculateAssetBalances(assets) {
    // Calculate Balances Amounts
    var crBalance = 0;
    var bankBalance = 0;
    var iraBalance = 0;
    var educBalance = 0;
    var brokBalance = 0;

    for (var i = 0; i < assets.length; i++)
    {
        if (assets[i].status == "0") {
            switch (assets[i].accttype) {
                case "BANK":
                    bankBalance = bankBalance + assets[i].amount.replace(/,/g, '') * 1;
                    break;
                case "CR":
                    crBalance = crBalance + assets[i].amount.replace(/,/g, '') * 1;
                    break;
                case "IRA":
                    iraBalance = iraBalance + assets[i].amount.replace(/,/g, '') * 1;
                    break;
                case "EDUC":
                    educBalance = educBalance + assets[i].amount.replace(/,/g, '') * 1;
                    break;
                case "BROK":
                    brokBalance = brokBalance + assets[i].amount.replace(/,/g, '') * 1;
                    break;
            }
        }
    }

    crBalance = Math.round(crBalance);
    bankBalance = Math.round(bankBalance);
    iraBalance = Math.round(iraBalance);
    educBalance = Math.round(educBalance);
    brokBalance = Math.round(brokBalance);

    return {"ira": iraBalance, "cr": crBalance, "educ": educBalance, "brok": brokBalance, "bank": bankBalance};
}

function CalculateGoalContributions() {
    var goalContributions = 0;
    for (var i = 0; i < goals.length; i++)
    {
        if (goals[i].goalstatus == 1 && goals[i].goaltype != 'DEBT')
        {
            goalContributions = goalContributions + goals[i].permonth.replace(/,/g, '') * 1;
        }
    }
    return Math.round(goalContributions);
}

function RecalculateGoalAmounts(reset) {
    var income = financialData.income;
    var goals = financialData.goals;
    if (!reset) {
        goals = goalSnapshot;
    }
    var growthrate = parseFloat(financialData.growthrate);
    var costIncrease = {"RETIREMENT": "3.4", "COLLEGE": "5.8", "CUSTOM": "3.4", "HOUSE": "3.4"};
    goals.sort(function(a, b) {
        return a.goalpriority - b.goalpriority;
    });

    var assets = financialData.investment;
    assets = assets.concat(financialData.other);
    assets = assets.concat(financialData.cash);

    // Contributions per asset type
    var assetContributions = CalculateAssetContributions(income, assets);
    // Balances per asset type
    var assetBalances = CalculateAssetBalances(assets);
    // Contributions set in goals
    var goalContributions = CalculateGoalContributions();

    // Calculate Minimum Saved Amount needed based on End Date and Contribution of 0
    for (var i = 0; i < goals.length; i++)
    {
        if (goals[i].goalstatus == 1 && goals[i].goaltype != 'DEBT')
        {
            var amount = parseFloat(goals[i].goalamount.replace(/,/g, ''));
            // Calculate Months to End Date
            var months = 120;
            if (goals[i].permonth.replace(/,/g, '') <= 0 || goals[i].goaltype == 'RETIREMENT' || goals[i].goaltype == 'COLLEGE') {
                var currentDate = new Date();
                var endDate = new Date(parseInt(goals[i].goalendYear), parseInt(goals[i].goalendMonth) - 1, parseInt(goals[i].goalendDay));
                if (endDate < currentDate)
                {
                    endDate = currentDate;
                }
                var diff = flexDateDiff(currentDate, endDate);
                var months = diff.years * 12 + diff.months;
                months += ((diff.days > 0) ? 1 : 0);
                months = (months > 0) ? months : 0;
            }

            var cost = costIncrease[goals[i].goaltype];
            if (goals[i].goalassumptions_1 != null && goals[i].goalassumptions_1 != "") {
                cost = goals[i].goalassumptions_1;
            }
            // Goal Amount in Future Dollars, accounting for cost increase / inflation
            var totalFuture = amount * Math.pow(1 + cost / 1200, months);
            // Goal Amount in Current Dollars, accounting for growth rate
            var savedDollars = totalFuture / Math.pow(1 + growthrate / 1200, months);
            if (savedDollars < 0) {
                savedDollars = 0;
            }
            goals[i].saved = 0;
            // Add Retirement Amounts
            if (goals[i].goaltype == 'RETIREMENT') {
                goals[i].saved += assetBalances["cr"] + assetBalances["ira"];
                savedDollars = savedDollars - assetBalances["cr"] - assetBalances["ira"];
                assetBalances["cr"] = 0;
                assetBalances["ira"] = 0;
            }
            // Add Educational Account Amounts
            if (goals[i].goaltype == 'COLLEGE') {
                goals[i].saved += assetBalances["educ"];
                savedDollars = savedDollars - assetBalances["educ"];
                if (savedDollars < 0) {
                    goals[i].saved += savedDollars;
                    assetBalances["educ"] = Math.abs(savedDollars);
                }
                else
                {
                    assetBalances["educ"] = 0;
                }
            }
            // Add Bank Amount
            if (savedDollars > 0) {
                goals[i].saved += assetBalances["bank"];
                savedDollars = savedDollars - assetBalances["bank"];
                if (savedDollars < 0) {
                    goals[i].saved += savedDollars;
                    assetBalances["bank"] = Math.abs(savedDollars);
                }
                else
                {
                    assetBalances["bank"] = 0;
                }
            }
            // Add Brokerage Amount
            if (savedDollars > 0) {
                goals[i].saved += assetBalances["brok"];
                savedDollars = savedDollars - assetBalances["brok"];
                if (savedDollars < 0) {
                    goals[i].saved += savedDollars;
                    assetBalances["brok"] = Math.abs(savedDollars);
                }
                else
                {
                    assetBalances["brok"] = 0;
                }
            }
            goals[i].saved = commaSeparateNumber(goals[i].saved, 0);
        }
    }

    // Add remaining saved amount to each goal
    for (var i = 0; i < goals.length; i++)
    {
        if (goals[i].goalstatus == 1 && goals[i].goaltype != 'DEBT')
        {
            var amount = parseFloat(goals[i].goalamount.replace(/,/g, ''));
            goals[i].saved = parseFloat(goals[i].saved.replace(/,/g, ''));
            if (goals[i].saved >= amount) {
                goals[i].saved = commaSeparateNumber(goals[i].saved, 0);
                continue;
            }
            if (goals[i].goaltype == 'COLLEGE') {
                goals[i].saved += assetBalances["educ"];
                if (goals[i].saved > amount) {
                    var savedAmount = (amount - goals[i].saved);
                    goals[i].saved += savedAmount;
                    assetBalances["educ"] = Math.abs(savedAmount);
                }
                else
                {
                    assetBalances["educ"] = 0;
                }
            }
            // Add Bank Amount
            if (goals[i].saved < amount) {
                goals[i].saved += assetBalances["bank"];
                if (goals[i].saved > amount) {
                    var savedAmount = (amount - goals[i].saved);
                    goals[i].saved += savedAmount;
                    assetBalances["bank"] = Math.abs(savedAmount);
                }
                else
                {
                    assetBalances["bank"] = 0;
                }
            }
            // Add Brokerage Amount
            if (goals[i].saved < amount) {
                goals[i].saved += assetBalances["brok"];
                if (goals[i].saved > amount) {
                    var savedAmount = (amount - goals[i].saved);
                    goals[i].saved += savedAmount;
                    assetBalances["brok"] = Math.abs(savedAmount);
                }
                else
                {
                    assetBalances["brok"] = 0;
                }
            }
            goals[i].saved = commaSeparateNumber(goals[i].saved, 0);
        }
    }

    // Calculate contribution needed per saved amount / end date of goals
    // Calculate status
    for (var i = 0; i < goals.length; i++)
    {
        if (goals[i].goalstatus == 1 && goals[i].goaltype != 'DEBT')
        {
            var amount = parseFloat(goals[i].goalamount.replace(/,/g, ''));
            var saved = parseFloat(goals[i].saved.replace(/,/g, ''));
            // Calculate Months to End Date
            var months = 120;
            var monthlyContributions = goals[i].permonth.replace(/,/g, '');
            var minimumContributions = monthlyContributions;
            if (goals[i].permonth.replace(/,/g, '') <= 0 || goals[i].goaltype == 'RETIREMENT' || goals[i].goaltype == 'COLLEGE') {
                var currentDate = new Date();
                var endDate = new Date(parseInt(goals[i].goalendYear), parseInt(goals[i].goalendMonth) - 1, parseInt(goals[i].goalendDay));
                if (endDate < currentDate)
                {
                    endDate = currentDate;
                }
                var diff = flexDateDiff(currentDate, endDate);
                var months = diff.years * 12 + diff.months;
                months += ((diff.days > 0) ? 1 : 0);
                months = (months > 0) ? months : 0;
                goals[i].months = months;

                var cost = costIncrease[goals[i].goaltype];
                if (goals[i].goalassumptions_1 != null && goals[i].goalassumptions_1 != "") {
                    cost = goals[i].goalassumptions_1;
                }
                // Goal Amount in Future Dollars, accounting for cost increase / inflation
                var totalFuture = amount * Math.pow(1 + cost / 1200, months);
                var savedDollars = saved;
                var amountNeeded = totalFuture - savedDollars;
                var checkAmount = ((amountNeeded / 1000000) > 1) ? (amountNeeded / 1000000) : 1;

                monthlyContributions = 0;
                if (months == 0) {
                    monthlyContributions = amountNeeded;
                }

                var increment = 1;
                while (amountNeeded > increment * 100) {
                    increment *= 10;
                }
                sign = '+';
                while (Math.abs(amountNeeded) >= checkAmount && totalFuture > savedDollars && months > 0) {
                    for (var j = 1; j <= months; j++) {
                        savedDollars = savedDollars * (1 + growthrate / 1200) + monthlyContributions;
                    }
                    amountNeeded = totalFuture - savedDollars;
                    if (Math.abs(amountNeeded) >= checkAmount) {
                        if (amountNeeded > 0 && sign == '-') {
                            sign = '+';
                            increment = 0 - increment / 10;
                        }
                        if (amountNeeded < 0 && sign == '+') {
                            sign = '-';
                            increment = 0 - increment / 10;
                        }
                        monthlyContributions += increment;
                    }
                    savedDollars = saved;
                }

                if (monthlyContributions < 0) {
                    monthlyContributions = 0;
                }

                goals[i].minimumContributions = monthlyContributions;
                goals[i].contributions = 0;

                // Add Retirement Contributions
                if (goals[i].goaltype == 'RETIREMENT') {
                    goals[i].contributions += assetContributions["cr"] + assetContributions["ira"] + assetContributions["crEmp"];
                    monthlyContributions = monthlyContributions - assetContributions["cr"] - assetContributions["ira"] - assetContributions["crEmp"];

                    var crFound = false;
                    for (var j = 0; j < assets.length; j++) {
                        if (assets[j].accttype == 'CR' && assets[j].status == "0")
                        {
                            monthlyContributions = monthlyContributions - assetContributions["extraCr"];
                            assetContributions["extraCr"] = 0;
                            crFound = true;
                            break;
                        }
                    }
                    for (var j = 0; j < assets.length; j++) {
                        if ((assets[j].accttype == 'IRA' && assets[j].status == "0" && assets[j].assettype != 51 && !crFound && income * 12 >= 160000) || (assets[j].accttype == 'IRA' && assets[j].status == "0" && assets[j].assettype == 51 && income * 12 < 160000)) {
                            monthlyContributions = monthlyContributions - assetContributions["extraIra"];
                            assetContributions["extraIra"] = 0;
                            break;
                        }
                    }
                    assetContributions["cr"] = 0;
                    assetContributions["crEmp"] = 0;
                    assetContributions["ira"] = 0;
                }
                // Add Educational Account Contributions
                if (goals[i].goaltype == 'COLLEGE') {
                    goals[i].contributions += assetContributions["educ"];
                    monthlyContributions = monthlyContributions - assetContributions["educ"];
                    if (monthlyContributions < 0) {
                        goals[i].contributions += monthlyContributions;
                        assetContributions["educ"] = Math.abs(monthlyContributions);
                    }
                    else
                    {
                        assetContributions["educ"] = 0;
                    }
                }
                // Add Bank Contributions
                if (monthlyContributions > 0) {
                    goals[i].contributions += assetContributions["bank"];
                    monthlyContributions = monthlyContributions - assetContributions["bank"];
                    if (monthlyContributions < 0) {
                        goals[i].contributions += monthlyContributions;
                        assetContributions["bank"] = Math.abs(monthlyContributions);
                    }
                    else
                    {
                        assetContributions["bank"] = 0;
                    }
                }
                // Add Brokerage Contributions
                if (monthlyContributions > 0) {
                    goals[i].contributions += assetContributions["brok"];
                    monthlyContributions = monthlyContributions - assetContributions["brok"];
                    if (monthlyContributions < 0) {
                        goals[i].contributions += monthlyContributions;
                        assetContributions["brok"] = Math.abs(monthlyContributions);
                    }
                    else
                    {
                        assetContributions["brok"] = 0;
                    }
                }
            }
            else
            {
                goals[i].minimumContributions = monthlyContributions * 1;
                var cost = costIncrease[goals[i].goaltype];
                if (goals[i].goalassumptions_1 != null && goals[i].goalassumptions_1 != "") {
                    cost = goals[i].goalassumptions_1;
                }
                var months = 0;
                var totalFuture = amount;
                var savedDollars = saved;
                while (totalFuture > savedDollars && months <= 1000) {
                    savedDollars = savedDollars * (1 + growthrate / 1200) + monthlyContributions * 1;
                    totalFuture = totalFuture * (1 + cost / 1200);
                    months++;
                }
                goals[i].months = months;

                goals[i].contributions = 0;
                if (monthlyContributions > 0) {
                    goals[i].contributions += assetContributions["bank"];
                    monthlyContributions = monthlyContributions - assetContributions["bank"];
                    if (monthlyContributions < 0) {
                        goals[i].contributions += monthlyContributions;
                        assetContributions["bank"] = Math.abs(monthlyContributions);
                    }
                    else
                    {
                        assetContributions["bank"] = 0;
                    }
                }
                if (monthlyContributions > 0) {
                    goals[i].contributions += assetContributions["brok"];
                    monthlyContributions = monthlyContributions - assetContributions["brok"];
                    if (monthlyContributions < 0) {
                        goals[i].contributions += monthlyContributions;
                        assetContributions["brok"] = Math.abs(monthlyContributions);
                    }
                    else
                    {
                        assetContributions["brok"] = 0;
                    }
                }
            }
            goals[i].contributions = commaSeparateNumber(goals[i].contributions, 0);
            goals[i].minimumContributions = commaSeparateNumber(goals[i].minimumContributions, 0);
        }
    }

    // Add remaining contribution amount to each goal
    for (var i = 0; i < goals.length; i++)
    {
        if (goals[i].goalstatus == 1 && goals[i].goaltype != 'DEBT')
        {
            var minimumContributions = parseFloat(goals[i].minimumContributions.replace(/,/g, ''));
            goals[i].contributions = parseFloat(goals[i].contributions.replace(/,/g, ''));
            if (goals[i].contributions >= minimumContributions) {
                goals[i].contributions = commaSeparateNumber(goals[i].contributions, 0);
                continue;
            }

            if (goals[i].goaltype == 'COLLEGE') {
                goals[i].contributions += assetContributions["educ"];
                if (goals[i].contributions > minimumContributions) {
                    var excessContributions = (minimumContributions - goals[i].contributions);
                    goals[i].contributions += excessContributions;
                    assetContributions["educ"] = Math.abs(excessContributions);
                }
                else
                {
                    assetContributions["educ"] = 0;
                }
            }
            // Add Bank Contributions
            if (goals[i].contributions < minimumContributions) {
                goals[i].contributions += assetContributions["bank"];
                if (goals[i].contributions > minimumContributions) {
                    var excessContributions = (minimumContributions - goals[i].contributions);
                    goals[i].contributions += excessContributions;
                    assetContributions["bank"] = Math.abs(excessContributions);
                }
                else
                {
                    assetContributions["bank"] = 0;
                }
            }
            // Add Brokerage Contributions
            if (goals[i].contributions < minimumContributions) {
                goals[i].contributions += assetContributions["brok"];
                if (goals[i].contributions > minimumContributions) {
                    var excessContributions = (minimumContributions - goals[i].contributions);
                    goals[i].contributions += excessContributions;
                    assetContributions["brok"] = Math.abs(excessContributions);
                }
                else
                {
                    assetContributions["brok"] = 0;
                }
            }
            goals[i].contributions = commaSeparateNumber(goals[i].contributions, 0);
        }
    }

    // Add remaining contribution amount to each goal
    for (var i = 0; i < goals.length; i++)
    {
        if (goals[i].goalstatus == 1 && goals[i].goaltype != 'DEBT')
        {
            var minimumContributions = parseFloat(goals[i].minimumContributions.replace(/,/g, ''));
            goals[i].contributions = parseFloat(goals[i].contributions.replace(/,/g, ''));

            if (goals[i].goaltype == 'COLLEGE') {
                goals[i].contributions += assetContributions["educ"];
                assetContributions["educ"] = 0;
            }
            // Add Bank Contributions
            goals[i].contributions += assetContributions["bank"];
            assetContributions["bank"] = 0;

            // Add Brokerage Contributions
            goals[i].contributions += assetContributions["brok"];
            assetContributions["brok"] = 0;

            if (Math.round(minimumContributions) > Math.round(goals[i].contributions)) {
                goals[i].status = "Needs Attention";
            }
            else
            {
                goals[i].status = "On Track";
            }
            goals[i].contributions = commaSeparateNumber(goals[i].contributions, 0);
        }
    }

    if (reset) {
        goalSnapshot = clone(financialData.goals);
    }
}

function ResetGoalAmounts() {
    var goals = financialData.goals;
    for (var i = 0; i < goals.length; i++)
    {
        if (goals[i].goalstatus == 1)
        {
            var key = financialData.goals[i].id;
            $("#" + key + "Cancel" + toTitleCase(calculateGoalKey(financialData.goals[i].goaltype)) + "Button").click();
        }
    }
}

function ResetAccountAmounts() {
    var tempObj = [];
    var key = $("#fiType").val();
    for (var attrname in financialData)
    {
        if (key == "assets" && (attrname == "cash" || attrname == "investment" || attrname == "silent" || attrname == "other"))
            tempObj = tempObj.concat(financialData[attrname]);
        else if (key == attrname)
            tempObj = tempObj.concat(financialData[attrname]);
    }
    for (var i = 0; i < tempObj.length; i++)
    {
        if (tempObj[i].status != 1)
        {
            $("#" + tempObj[i].id + "Cancel" + calculateTitleKey(tempObj[i].accttype, key) + "Button").click();
        }
    }
}

function SetCalculatedGoalAmounts() {
    var goals = goalSnapshot;
    for (var i = 0; i < goals.length; i++)
    {
        if (goals[i].goalstatus == 1 && goals[i].goaltype != 'DEBT')
        {
            var key = goals[i].id;
            $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "Saved").val(goals[i].saved);
            $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "SavedSpan").html(goals[i].saved);
            if (goals[i].goaltype != 'CUSTOM' && goals[i].goaltype != 'HOUSE') {
                $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "Contribution").val(goals[i].contributions);
                $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "ContributionSpan").html(goals[i].contributions);

                var total = (goals[i].minimumContributions.replace(/,/g, '') - goals[i].contributions.replace(/,/g, '') > 0) ? goals[i].minimumContributions.replace(/,/g, '') - goals[i].contributions.replace(/,/g, '') : 0;

                if (total > 0) {
                    $("#" + key + "colorChange").css("color", "#ff0000");
                } else {
                    $("#" + key + "colorChange").css("color", "#33CC00");
                }

                $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "MonthlyTotal").html(commaSeparateNumber(goals[i].minimumContributions, 0));
                $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "MonthlyCurrent").html(commaSeparateNumber(goals[i].contributions, 0));
                $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "Saving").html(commaSeparateNumber(goals[i].contributions, 0));
                $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "MonthlyNeeds").html(commaSeparateNumber(total, 0));
                $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "MonthlyNeedsDiv").show();
                $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "CompletionDiv").hide();
            }
            else
            {
                $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "Contri").val(goals[i].contributions);
                $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "ContriSpan").html(goals[i].contributions);

                var achieve = $('input[name=' + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + 'Achieve]:checked').val();
                if (achieve == 1) {
                    $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "CompletionDiv").hide();
                }
                else {
                    if (goals[i].months == 1001) {
                        $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "Completion").hide();
                        $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "CompletionNever").show();
                    }
                    else
                    {
                        var years = goals[i].months / 12;
                        var months = goals[i].months % 12;
                        $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "CompletionYears").html(Math.floor(years));
                        $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "CompletionMonths").html(months);
                        $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "Completion").show();
                        $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "CompletionNever").hide();
                    }
                    $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "CompletionDiv").show();
                }
                var total = (goals[i].minimumContributions.replace(/,/g, '') - goals[i].contributions.replace(/,/g, '') > 0) ? goals[i].minimumContributions.replace(/,/g, '') - goals[i].contributions.replace(/,/g, '') : 0;
                if (total > 0) {
                    $("#" + key + "colorChange").css("color", "#ff0000");
                } else {
                    $("#" + key + "colorChange").css("color", "#33CC00");
                }
                $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "MonthlyNeeds").html(commaSeparateNumber(goals[i].minimumContributions, 0));
                $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "MonthlyCurrent").html(commaSeparateNumber(goals[i].contributions, 0));
                $("#" + key + toTitleCase(calculateGoalKey(goals[i].goaltype)) + "MonthlyLeft").html(commaSeparateNumber(total, 0));
            }
        }
    }

}

function clone(obj) {
    // Handle the 3 simple types, and null or undefined
    if (null == obj || "object" != typeof obj)
        return obj;

    // Handle Date
    if (obj instanceof Date) {
        var copy = new Date();
        copy.setTime(obj.getTime());
        return copy;
    }

    // Handle Array
    if (obj instanceof Array) {
        var copy = [];
        for (var i = 0, len = obj.length; i < len; i++) {
            copy[i] = clone(obj[i]);
        }
        return copy;
    }

    // Handle Object
    if (obj instanceof Object) {
        var copy = {};
        for (var attr in obj) {
            if (obj.hasOwnProperty(attr))
                copy[attr] = clone(obj[attr]);
        }
        return copy;
    }

    throw new Error("Unable to copy obj! Its type isn't supported.");
}

function toTitleCase(str) {
    return str.replace(/(?:^|\s)\w/g, function(match) {
        return match.toUpperCase();
    });
}

function popUpConsumerEmailverify() {
    "use strict";
    if (!$("#consumeremailverify").is(":visible"))
    {
        $.scrollTo($('#body'), 200);
    }
    $('#consumeremailverify').show();
    $('#darkBackground').show();
    $('#darkBackground').fadeTo("fast", 0.6);
    $('#consumeremailverify').css("height", "auto");
}


//print and pdf functions//

function downloadElement(outtype) {
    $('#manageProfile').hide();
    $('#moreActionSteps').hide();
    $('#PrintinPopup').hide();
    $('#DownloadinPopup').hide();
    $('.goalsHeader').hide();
    $('.lsPillBtnGreen').hide();
    $('.caret').hide();
    //$('.myScoreTopicFull').hide();
    //$('#mainBody').downloadElement(options);

    var doc = new jsPDF("p", "pt", "letter");
    doc.setFontSize(40);
    doc.setDrawColor(0);
    doc.setFillColor(238, 238, 238);

    if (globalCanvas != null) {
        var imageData = globalCanvas.toDataURL("image/jpeg");
        $('.caret').show();
        $('#PrintinPopup').show();
        Modernizr.addTest("blobconstructor", function() {
            try {
                return!!(new Blob)
            } catch (a) {
                return!1
            }
        });
        if (Modernizr.blobconstructor) {
            $("#DownloadinPopup").show();
        }
        $('.lsPillBtnGreen').show();
        $('.goalsHeader').show();
        $('#moreActionSteps').show();
        $('#manageProfile').show();
        if (outtype == 'pdf') {
            doc.addImage(imageData, 'jpeg', 5, 15, 600, 0);
            doc.save("MyFlexScore.pdf");
            return false;

        } else {
            $("#dialog").html('<img src="' + imageData + '">');
            $('#dialog').show().printElement();
            $("#dialog").html('');
            $("#dialog").hide();
        }
    }
    else
    {
        $('.caret').show();
        $('#PrintinPopup').show();
        Modernizr.addTest("blobconstructor", function() {
            try {
                return!!(new Blob)
            } catch (a) {
                return!1
            }
        });
        if (Modernizr.blobconstructor) {
            $("#DownloadinPopup").show();
        }
        $('.lsPillBtnGreen').show();
        $('.goalsHeader').show();
        $('#moreActionSteps').show();
        $('#manageProfile').show();
    }
}

function getImgData(chartContainer)
{

    var chartArea = chartContainer.getElementsByTagName('svg')[0].parentNode;
    var svg = chartArea.innerHTML.trim();
    svg = svg.substring(0, svg.indexOf('</svg>') + 6);
    var doc = chartContainer.ownerDocument;
    var canvas = doc.createElement('canvas');
    canvas.setAttribute('width', chartArea.offsetWidth);
    canvas.setAttribute('height', chartArea.offsetHeight);


    canvas.setAttribute(
            'style',
            'position: absolute; ' +
            'top: ' + (-chartArea.offsetHeight * 2) + 'px;' +
            'left: ' + (-chartArea.offsetWidth * 2) + 'px;');
    doc.body.appendChild(canvas);
    canvg(canvas, svg);

    var imgData = canvas.toDataURL("image/JPEG");
    var data = canvas.toDataURL('image/JPEG').slice('data:image/JPEG;base64,'.length);

// Convert the data to binary form
    data = atob(data)

    canvas.parentNode.removeChild(canvas);

    return imgData;
}

function getParameter(paramName) {
    var searchString = window.location.search.substring(1),
            i, val, params = searchString.split("&");

    for (i = 0; i < params.length; i++) {
        val = params[i].split("=");
        if (val[0] == paramName) {
            return val[1];
        }
    }
    return null;
}

function runRiskCalculations() {
    if (!riskAjaxInProcess && riskCurrentIndex < riskCurrentLength) {
        riskAjaxInProcess = true;
        var formValues = riskCurrentVariables[riskCurrentIndex];
        riskCurrentIndex++;

        if(profileUserData.risk != formValues["risk"]) {
            profileUserData.risk = formValues["risk"];
            $.ajax({
                url:userRiskAddUpdateURL,
                dataType:"json",
                data:formValues,
                type:'POST',
                success:function (jsonData){
                    timeoutPeriod = defaultTimeoutPeriod;
                    riskAjaxInProcess = false;
                    financialData.growthrate = jsonData.growthrate;
                }
            });
        }
        else
        {
            riskAjaxInProcess = false;
        }
    }
    else if (!riskAjaxInProcess)
    {
        if (riskCurrentIntervalId != '') {
            clearInterval(riskCurrentIntervalId);
            riskCurrentIntervalId = '';
        }
    }
}

function runAccountCalculations() {
    if (!accountAjaxInProcess && accountCurrentIndex < accountCurrentLength) {
        accountAjaxInProcess = true;
        var formValues = accountCurrentVariables[accountCurrentIndex];
        accountCurrentIndex++;
        var idVal = formValues["id"];
        var currAction = formValues["action"];
        var urlUsed = formValues["url"];

        $.ajax({
            url: urlUsed,
            dataType: "json",
            data: formValues,
            type: 'POST',
            success: function(jsonData) {
                accountAjaxInProcess = false;
                timeoutPeriod = defaultTimeoutPeriod;
            }
        });
    }
    else if (!accountAjaxInProcess)
    {
        if (accountCurrentIntervalId != '') {
            clearInterval(accountCurrentIntervalId);
            accountCurrentIntervalId = '';
        }
    }
}