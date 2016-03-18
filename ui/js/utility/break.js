/* 
 * Used for live calculations in break down tabs.
 * Break down tabs showing in My score page.
 */
var allowToggle = true;
var mimic = false; // for break down tab
var currentVariables = {};
var currentIndex = 0;
var currentLength = 0;
var currentIntervalId = '';
var ajaxInProcess = false;

function runBreakdownCalculations() {
    if (!ajaxInProcess && currentIndex < currentLength) {
        ajaxInProcess = true;
        formValues = currentVariables[currentIndex];
        currentIndex++;

        $.ajax({
            url: breakdownURL,
            type: 'POST',
            dataType: "json",
            data: formValues,
            success: function(scoreData) {
                ajaxInProcess = false;
                timeoutPeriod = defaultTimeoutPeriod;
                var simScore = parseInt(scoreData.result);
                breakscore = simScore;
                var imageId = Math.round((simScore * 20) / 1000);
                imageId = (imageId > 0) ? imageId : 0;
                imageId = (imageId < 20) ? imageId : 20;
                alignScore('breakdownScore', 'breakdownHorseshoe', simScore, imageId);
            }
        });
    }
    else if (!ajaxInProcess)
    {
        if (currentIntervalId != '') {
            clearInterval(currentIntervalId);
            currentIntervalId = '';
        }
    }
}

function updateAgeBreakSliderValuesAndGraph(event, ui) {
    "use strict";
    if (event.originalEvent) {
        updateBreakSliderValues(event, ui);
        var value = $(this).slider("option", "value");
        var age = $("#retAgeSliderValue").html().replace(/,/g, '');
        breakage = age;

        var formValues = {
            age: age,
            simulation: 'on'
        }
        currentVariables[currentLength] = formValues;
        currentLength++;
        if (!ajaxInProcess && currentIntervalId == '') {
            currentIntervalId = setInterval(runBreakdownCalculations, 500);
        }
    }
}


function updateGoalBreakSliderValuesAndGraph(event, ui) {
    "use strict";
    if (event.originalEvent) {
        updateBreakSliderValues(event, ui);
        var value = $(this).slider("option", "value");
        var goal = $("#retGoalSliderValue").html().replace(/,/g, '');
        breakgoal = goal;

        var formValues = {
            goal: goal,
            simulation: 'on'
        }
        currentVariables[currentLength] = formValues;
        currentLength++;
        if (!ajaxInProcess && currentIntervalId == '') {
            currentIntervalId = setInterval(runBreakdownCalculations, 500);
        }
    }
}

function updateSavingsBreakSliderValuesAndGraph(event, ui) {
    "use strict";
    if (event.originalEvent) {
        updateBreakSliderValues(event, ui);
        var value = $(this).slider("option", "value");
        var savings = $("#savingsSliderValue").html().replace(/,/g, '');
        breaksavings = savings;

        var formValues = {
            savings: savings,
            simulation: 'on'
        }
        currentVariables[currentLength] = formValues;
        currentLength++;
        if (!ajaxInProcess && currentIntervalId == '') {
            currentIntervalId = setInterval(runBreakdownCalculations, 500);
        }
    }
}

function updateAssetsBreakSliderValuesAndGraph(event, ui) {
    "use strict";
    if (event.originalEvent) {
        updateBreakSliderValues(event, ui);
        var value = $(this).slider("option", "value");
        var assets = $("#assetsSliderValue").html().replace(/,/g, '');
        breakassets = assets;

        var formValues = {
            assets: assets,
            simulation: 'on'
        }
        currentVariables[currentLength] = formValues;
        currentLength++;
        if (!ajaxInProcess && currentIntervalId == '') {
            currentIntervalId = setInterval(runBreakdownCalculations, 500);
        }
    }
}

function updateDebtsBreakSliderValuesAndGraph(event, ui) {
    "use strict";
    if (event.originalEvent) {
        updateBreakSliderValues(event, ui);
        var value = $(this).slider("option", "value");
        var debts = $("#debtsSliderValue").html().replace(/,/g, '');
        breakdebts = debts;

        var formValues = {
            debts: debts,
            simulation: 'on'
        }
        currentVariables[currentLength] = formValues;
        currentLength++;
        if (!ajaxInProcess && currentIntervalId == '') {
            currentIntervalId = setInterval(runBreakdownCalculations, 500);
        }
    }
}

function updateLivingBreakSliderValuesAndGraph(event, ui) {
    "use strict";
    if (event.originalEvent) {
        updateBreakSliderValues(event, ui);
        var value = $(this).slider("option", "value");
        var living = $("#colSliderValue").html().replace(/,/g, '');
        breakliving = living;

        var formValues = {
            living: living,
            simulation: 'on'
        }
        currentVariables[currentLength] = formValues;
        currentLength++;
        if (!ajaxInProcess && currentIntervalId == '') {
            currentIntervalId = setInterval(runBreakdownCalculations, 500);
        }
    }
}
function resetAndUpdateSliders() {
    "use strict";

    $('.sliderAge').slider("option", "value", breakage);
    $('.sliderGoal').slider("option", "value", breakgoal);
    $('.sliderSavings').slider("option", "value", breaksavings);
    $('.sliderAssets').slider("option", "value", breakassets);
    $('.sliderDebts').slider("option", "value", breakdebts);
    $('.sliderCliving').slider("option", "value", breakliving);

    $("#retAgeSliderValue").html(commaSeparateNumber(breakage, 0));
    $("#retGoalSliderValue").html(commaSeparateNumber(breakgoal, 0));
    $("#savingsSliderValue").html(commaSeparateNumber(breaksavings, 0));
    $("#assetsSliderValue").html(commaSeparateNumber(breakassets, 0));
    $("#debtsSliderValue").html(commaSeparateNumber(breakdebts, 0));
    $("#colSliderValue").html(commaSeparateNumber(breakliving, 0));

    var formValues = {
        age: breakage,
        goal: breakgoal,
        savings: breaksavings,
        assets: breakassets,
        debts: breakdebts,
        living: breakliving,
        reset: 'all',
        simulation: 'on'
    }
    var simScore = breakscore;
    var imageId = Math.round((simScore * 20) / 1000);
    imageId = (imageId > 0) ? imageId : 0;
    imageId = (imageId < 20) ? imageId : 20;
    alignScore('breakdownScore', 'breakdownHorseshoe', simScore, imageId);
    currentVariables[currentLength] = formValues;
    currentLength++;
    if (!ajaxInProcess && currentIntervalId == '') {
        currentIntervalId = setInterval(runBreakdownCalculations, 500);
    }
}
function updateBreakSliderValues(event, ui) {
    "use strict";
    try {
        //var i = event.target.parentElement.id;
        var i = event.target.id;
        var valueElement = $('#' + i + 'Value');
        valueElement.text(ui.value.formatMoney(0, '.', ','));
    } catch (err) {
    }
}


function resetBreakSliders() {
    $('#simulateDataDropDown').val('');
    breakgoal = 4000;
    if (financialData.goals.length > 0) {
        for (var i = 0; i < financialData.goals.length; i++) {
            if (financialData.goals[i].goaltype == 'RETIREMENT') {
                breakgoal = financialData.goals[i].monthlyincome.replace(/,/g, '');
                break;
            }
        }
    }
    breakage = financialData.retage;
    breaksavings = financialData.savingsTotal.replace(/,/g, '');
    breakassets = financialData.assetsTotal.replace(/,/g, '');
    breakdebts = financialData.debtsTotal.replace(/,/g, '');
    breakliving = financialData.livingCosts.replace(/,/g, '');

    $('.sliderAge').slider("option", "value", breakage);
    $('.sliderGoal').slider("option", "value", breakgoal);
    $('.sliderSavings').slider("option", "value", breaksavings);
    $('.sliderAssets').slider("option", "value", breakassets);
    $('.sliderDebts').slider("option", "value", breakdebts);
    $('.sliderCliving').slider("option", "value", breakliving);

    $("#retAgeSliderValue").html(commaSeparateNumber(breakage, 0));
    $("#retGoalSliderValue").html(commaSeparateNumber(breakgoal, 0));
    $("#savingsSliderValue").html(commaSeparateNumber(breaksavings, 0));
    $("#assetsSliderValue").html(commaSeparateNumber(breakassets, 0));
    $("#debtsSliderValue").html(commaSeparateNumber(breakdebts, 0));
    $("#colSliderValue").html(commaSeparateNumber(breakliving, 0));

    $(".openTextBox").show();
    $("#resetBreakBtn").show();
    $(".saveBreakButton").hide();
    $(".cancelSave").hide();
    $("#breakName").hide();
    $("#simDD").hide();
    $("#clientdiv").removeClass('error');
    if(typeof(financialData.breakdowndata) != 'undefined' && financialData.breakdowndata != null && financialData.breakdowndata.length > 0) {
        $(".loadTextBox").show();
        $(".deleteTextBox").show();
        $(".updateTextBox").show();
    }            

    var formValues = {
        reset: 'true',
        simulation: 'on'
    }
    currentVariables[currentLength] = formValues;
    currentLength++;
    if (!ajaxInProcess && currentIntervalId == '') {
        currentIntervalId = setInterval(runBreakdownCalculations, 500);
    }

    return true;
}

function initBreakSliders() {
    $(".sliderAge").slider({
        range: "min",
        min: parseInt(financialData.age),
        max: (parseInt(financialData.age) > 65) ? parseInt(financialData.age) + 10 : 75,
        step: 1,
        slide: updateBreakSliderValues,
        change: updateAgeBreakSliderValuesAndGraph
    });
    var goalamount = 4000;
    if (financialData.goals.length > 0) {
        for(var i = 0; i < financialData.goals.length; i++) {
            if(financialData.goals[i].goaltype == 'RETIREMENT') {
                goalamount = Math.round(financialData.goals[i].monthlyincome.replace(/,/g, ''));
            }
        }
    }
    $(".sliderGoal").slider({
        range: "min",
        min: (goalamount < Math.round(financialData.income * 0.5)) ? goalamount : Math.round(financialData.income * 0.5),
        max: (goalamount > Math.round(financialData.income)) ? goalamount * 1.5 : Math.round(financialData.income * 1.5),
        step: 500,
        slide: updateBreakSliderValues,
        change: updateGoalBreakSliderValuesAndGraph
    });
    $(".sliderSavings").slider({
        range: "min",
        min: 0,
        max: Math.round(financialData.income * 1),
        step: 100,
        slide: updateBreakSliderValues,
        change: updateSavingsBreakSliderValuesAndGraph
    });
    var assetsTotal = Math.round(financialData.assetsTotal.replace(/,/g, ''));
    $(".sliderAssets").slider({
        range: "min",
        min: 0,
        max: (assetsTotal > 500000) ? assetsTotal * 2 : 1000000,
        step: 500,
        slide: updateBreakSliderValues,
        change: updateAssetsBreakSliderValuesAndGraph
    });
    var debtsTotal = Math.round(financialData.debtsTotal.replace(/,/g, ''));
    $(".sliderDebts").slider({
        range: "min",
        min: 0,
        max: (debtsTotal > 500000) ? debtsTotal * 2 : 1000000,
        step: 500,
        slide: updateBreakSliderValues,
        change: updateDebtsBreakSliderValuesAndGraph
    });

    $(".sliderCliving").slider({
        range: "min",
        min: 0,
        max: (Math.round(financialData.livingCosts.replace(/,/g, '') * 10) > Math.round(financialData.income * 1)) ? Math.round(financialData.livingCosts.replace(/,/g, '') * 10) : Math.round(financialData.income * 1),
        step: 500,
        slide: updateBreakSliderValues,
        change: updateLivingBreakSliderValuesAndGraph
    });
}

function saveBreakdownSliders() {
    "use strict";
    var breakname = $("#breakName").val();
    var formValues = {
        name: breakname,
        age: breakage,
        goal: breakgoal,
        savings: breaksavings,
        assets: breakassets,
        debts: breakdebts,
        living: breakliving
    }

    $.ajax({
        url: savebreakdownURL,
        type: 'POST',
        dataType: "json",
        data: formValues,
        success: function(scoreData) {
            if (scoreData.status == "OK") {
                financialData.breakdowndata = scoreData.breakdownData;
                financialData.breakname = $("#breakName").val();
                require(
                        ['views/user/break'],
                        function(breakV) {
                            breakV.render(financialData, true);
                        }
                );               
            }
        }
    });
}

function updateBreakdownSliders() {
    "use strict";
    var id = $("#simulateDataDropDown").val();
    var formValues = {
        id: id,
        age: breakage,
        goal: breakgoal,
        savings: breaksavings,
        assets: breakassets,
        debts: breakdebts,
        living: breakliving
    }

    $.ajax({
        url: updatebreakdownURL,
        type: 'POST',
        dataType: "json",
        data: formValues,
        success: function(scoreData) {
            if (scoreData.status == "OK") {
                financialData.breakdowndata = scoreData.breakdownData;
                financialData.breakname = $("#breakName").val();
                require(
                        ['views/user/break'],
                        function(breakV) {
                            breakV.render(financialData, true);
                        }
                );               
            }
        }
    });
}

function deleteBreakdownSliders() {
    "use strict";
    var id = $("#simulateDataDropDown").val();
    var formValues = {
        id: id
    }

    $.ajax({
        url: deletebreakdownURL,
        type: 'POST',
        dataType: "json",
        data: formValues,
        success: function(scoreData) {
            if (scoreData.status == "OK") {
                financialData.breakdowndata = scoreData.breakdownData;
                financialData.breakname = $("#breakName").val();
                require(
                        ['views/user/break'],
                        function(breakV) {
                            breakV.render(financialData, true);
                        }
                );               
            }
        }
    });
}

function userBreakSliders(obj) {
    if(obj.goal == null){
        breakgoal = 4000;  
    }else{
        breakgoal = obj.goal;
    }
    if(obj.age == null){
        breakage = 65;  
    }else{
        breakage = obj.age;
    }
    if(obj.savings == null){
        breaksavings = null;  
    }else{
        breaksavings = obj.savings.replace(/,/g, '');
    }
    if(obj.assets == null){
        breakassets = null;  
    }else{
        breakassets = obj.assets.replace(/,/g, '');
    }
    if(obj.debts == null){
        breakdebts = null;  
    }else{
        breakdebts = obj.debts.replace(/,/g, '');

    }
    if(obj.living == null){
        breakliving = null;  
    }else{
        breakliving = obj.living.replace(/,/g, '');
    }
    
    var formValues = {
        age: breakage,
        goal: breakgoal,
        savings: breaksavings,
        assets: breakassets,
        debts: breakdebts,
        living: breakliving,
        reset: 'all',
        simulation: 'on'
    }
    currentVariables[currentLength] = formValues;
    currentLength++;
    if (!ajaxInProcess && currentIntervalId == '') {
        currentIntervalId = setInterval(runBreakdownCalculations, 500);
    }

    $('.sliderAge').slider("option", "value", breakage);
    $('.sliderGoal').slider("option", "value", breakgoal);
    $('.sliderSavings').slider("option", "value", breaksavings);
    $('.sliderAssets').slider("option", "value", breakassets);
    $('.sliderDebts').slider("option", "value", breakdebts);
    $('.sliderCliving').slider("option", "value", breakliving);

    $("#retAgeSliderValue").html(commaSeparateNumber(breakage, 0));
    $("#retGoalSliderValue").html(commaSeparateNumber(breakgoal, 0));
    $("#savingsSliderValue").html(commaSeparateNumber(breaksavings, 0));
    $("#assetsSliderValue").html(commaSeparateNumber(breakassets, 0));
    $("#debtsSliderValue").html(commaSeparateNumber(breakdebts, 0));
    $("#colSliderValue").html(commaSeparateNumber(breakliving, 0));
}
