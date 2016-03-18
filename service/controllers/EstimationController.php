<?php

/* * ********************************************************************
 * Filename: ExpenseController.php
 * Folder: controllers
 * Description: expense controller
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class EstimationController extends Scontroller {

    public function accessRules() {
        return array_merge(
                array(array('allow', 'users' => array('?'))),
                // Include parent access rules
                parent::accessRules()
        );
    }

    function actionSetUserEstimates() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $needsUpdate = false;

        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $estimation = Estimation::model()->find("user_id = :user_id", array(':user_id' => $user_id));
        if (!$estimation) {
            $estimation = new Estimation();
            $estimation->user_id = $user_id;
            $needsUpdate = true;
        }

        $hIfreq = 1;
        if(isset($_POST["hIfreq"]) && $_POST["hIfreq"] > 0) {
            $hIfreq = $_POST["hIfreq"];
        }

        $hEfreq = 1;
        if(isset($_POST["hEfreq"]) && $_POST["hEfreq"] > 0) {
            $hEfreq = $_POST["hEfreq"];
        }

        $hSfreq = 1;
        if(isset($_POST["hSfreq"]) && $_POST["hSfreq"] > 0) {
            $hSfreq = $_POST["hSfreq"];
        }

        if(isset($_POST["houseincome"])) {
            $houseincome = $_POST["houseincome"];
            $estimation->houseincome = $houseincome / $hIfreq;
            $needsUpdate = true;
        }

        if(isset($_POST["houseexpense"])) {
            $houseexpense = $_POST["houseexpense"];
            $estimation->houseexpense = $houseexpense / $hEfreq;
            $needsUpdate = true;
        }

        if(isset($_POST["housesavings"])) {
            $housesavings = $_POST["housesavings"];
            $estimation->housesavings = $housesavings / $hSfreq;
            $needsUpdate = true;
        }

        if(isset($_POST["houseassets"])) {
            $houseassets = $_POST["houseassets"];
            $estimation->houseassets = $houseassets;
            $needsUpdate = true;
        }

        if(isset($_POST["housedebts"])) {
            $housedebts = $_POST["housedebts"];
            $estimation->housedebts = $housedebts;
            $needsUpdate = true;
        }

        if(isset($_POST["whichyouhave"])) {
            $whichyouhave = $_POST["whichyouhave"];
            $estimation->whichyouhave = $whichyouhave;
            $needsUpdate = true;
        }

        if($needsUpdate) {
            $estimation->save();

            $income = Income::model()->findBySql("SELECT totaluserincome FROM income WHERE user_id=:user_id", array("user_id" => $user_id));

            parent::setEngine();

            if ($income && $income->totaluserincome > 0) {
                $this->sengine->userIncomePerMonth = $income->totaluserincome;
            } else {
                if ($estimation->houseincome > 0) {
                    $this->sengine->userIncomePerMonth = $estimation->houseincome;
                } else { //default
                    $this->sengine->userIncomePerMonth = 5000;
                }
            }

            $this->sengine->grossIncome = ($income && $income->gross_income > 0) ? $income->gross_income : $this->sengine->userIncomePerMonth;

            parent::saveEngine();

            $expense = Expense::model()->findBySql("SELECT actualexpense FROM expense WHERE user_id=:user_id", array("user_id" => $user_id));
            parent::setEngine();
            if ($expense && $expense->actualexpense > 0) {
                $this->sengine->userExpensePerMonth = $expense->actualexpense;
            } else {  //take from estimates
                if ($estimation->houseexpense > 0) {
                    $this->sengine->userExpensePerMonth = $estimation->houseexpense;
                } else { //default
                    $this->sengine->userExpensePerMonth = 0;
                }
            }
            parent::saveEngine();

            $retirementAmount = $this->sengine->retirementAmountDesired;
            $sectionNames = "";
            parent::setupDefaultRetirementGoal();
            if ($retirementAmount != $this->sengine->retirementAmountDesired) {
                $sectionNames = "GOAL|";
            }
            $sectionNames .= "EXPENSE|INCOME";
            parent::calculateScore($sectionNames, $user_id);

            unset($income);
            unset($expense);
        }
        unset($estimation);

        parent::setEngine();
        $totalScore = $this->sengine->updateScore();
        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'totalscore' => $totalScore, 'message' => 'Financial Highlights details added')));
    }

    function actionSetEstimates() {
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "This API can not be called with a valid session.")));
        }

        $estimation = new Estimation();

        $hIfreq = 1;
        if(isset($_POST["hIfreq"]) && $_POST["hIfreq"] > 0) {
            $hIfreq = $_POST["hIfreq"];
        }

        $hEfreq = 1;
        if(isset($_POST["hEfreq"]) && $_POST["hEfreq"] > 0) {
            $hEfreq = $_POST["hEfreq"];
        }

        $hSfreq = 1;
        if(isset($_POST["hSfreq"]) && $_POST["hSfreq"] > 0) {
            $hSfreq = $_POST["hSfreq"];
        }

        if(isset($_POST["houseincome"])) {
            $houseincome = $_POST["houseincome"];
            $estimation->houseincome = $houseincome / $hIfreq;
        }

        if(isset($_POST["houseexpense"])) {
            $houseexpense = $_POST["houseexpense"];
            $estimation->houseexpense = $houseexpense / $hEfreq;
        }

        if(isset($_POST["housesavings"])) {
            $housesavings = $_POST["housesavings"];
            $estimation->housesavings = $housesavings / $hSfreq;
        }

        if(isset($_POST["houseassets"])) {
            $houseassets = $_POST["houseassets"];
            $estimation->houseassets = $houseassets;
        }

        if(isset($_POST["housedebts"])) {
            $housedebts = $_POST["housedebts"];
            $estimation->housedebts = $housedebts;
        }

        if(isset($_POST["whichyouhave"])) {
            $whichyouhave = $_POST["whichyouhave"];
            $estimation->whichyouhave = $whichyouhave;
        }

        $user = new User();
        $user->zip = "";
        if(isset($_POST["zip"])) {
            $zip = $_POST["zip"];
            $user->zip = $zip;
        }

        $userPerDetails = new Userpersonalinfo();
        $userPerDetails->age = (date("Y") - 30) . "-" . date("m") . "-" . date("d");
        $userPerDetails->retirementage = 65;
        if(isset($_POST["age"])) {
            $age = $_POST["age"];
            $userPerDetails->age = $age;
        }

        parent::setEngine();

        $count = 0;
        $count1 = 0;

        $date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
        if (preg_match($date_regex, $userPerDetails->age)) {
            $count1++;
        }

        if ($user->zip > 0) {
            $count++;
        }
        $this->sengine->userProfilePoints_aboutyou = ($count * 1.25 + $count1 * 2.50);

        // SETTING AGE VALUES
        $ageInYears = 30;
        $ageSplit = explode("-", $userPerDetails->age);

        // X--, -X-, --X, X-X, -XX, XX-, --- <= Partial age dates get stored as 0000-00-00 in DB
        if ($ageSplit[0] != "0000") {
            $ageInYears = date('Y') - $ageSplit[0];
            if ($ageSplit[1] > date('m')) // Birth Month is later in the year
                $ageInYears--;
            else if ($ageSplit[1] == date('m') && $ageSplit[2] > date('d')) // Birth day is later in the month
                $ageInYears--;

            // If user gives us an age, then we look for life expectancy
            // Life expectancy values are based on users' ages in 2010
            $lifeexpAge = 2010 - $ageSplit[0];
            if ($lifeexpAge < 0)
                $lifeexpAge = 0;

            $lifeExpdependency = new CDbCacheDependency('SELECT FLifeExpectancy, baseyearage FROM lifeexpectancy');
            $lifeexp = Lifeexpectancy::model()->cache(QUERY_CACHE_TIMEOUT, $lifeExpdependency)->find('baseyearage = ' . $lifeexpAge);
        }
        $this->sengine->userCurrentAge = $ageInYears;
        if ($this->sengine->userCurrentAge < $userPerDetails->retirementage) {
            $this->sengine->userRetirementAge = $userPerDetails->retirementage;
        } else {
            $this->sengine->userRetirementAge = $this->sengine->userCurrentAge;
        }
        $this->sengine->yearToRetire = $this->sengine->userRetirementAge - $this->sengine->userCurrentAge;

        $rates = Sustainablerates::model()->findbySql("SELECT * FROM sustainablerates WHERE age=:age", array("age" => $this->sengine->userRetirementAge));
        $sustainablewithdrawalpercent = isset($rates->sustainablewithdrawalpercent) ? $rates->sustainablewithdrawalpercent : 4;
        $this->sengine->sustainablewithdrawalpercent = $sustainablewithdrawalpercent;

        if (isset($lifeexp)) {
            $this->sengine->lifeEC = $lifeexp->FLifeExpectancy;
            $this->sengine->enteredAge = true;
        } else {
            $this->sengine->lifeEC = 82;
        }

        // Set Income Values
        if ($estimation->houseincome > 0) {
            $this->sengine->userIncomePerMonth = $estimation->houseincome;
        } else { //default
            $this->sengine->userIncomePerMonth = 5000;
        }
        $this->sengine->grossIncome = $this->sengine->userIncomePerMonth;

        // Set Expense Values
        if ($estimation->houseexpense > 0) {
            $this->sengine->userExpensePerMonth = $estimation->houseexpense;
        } else { //default
            $this->sengine->userExpensePerMonth = 0;
        }

        $this->sengine->userSumOfDebts = $estimation->housedebts;
        if($estimation->housedebts > 0) {
            $this->sengine->wfPoint5 = 0;
        }
        else {
            $this->sengine->wfPoint5 = 25;
        }

        $this->sengine->userSumOfAssets = $estimation->houseassets;
        $this->sengine->userSumOfGoalSettingAssets = $estimation->houseassets;
        $this->sengine->numeratorP14 = $estimation->houseassets;
        $this->sengine->taxableAnnualSavings = $estimation->housesavings * 12;

        $assets = array();
        $valueObj = new stdClass();
        $valueObj->balance = $estimation->houseassets;
        $valueObj->invpos = '';
        $valueObj->type = "BANK";
        $valueObj->name = "Bank Account";
        $assets[0] = $valueObj;
        parent::CalculatePoint10($assets, null, null);

        // Set Goal Values
        $retirementAmount = $this->sengine->retirementAmountDesired;
        $sectionNames = "";
        $desiredIncome = ($this->sengine->userIncomePerMonth * 0.8 * 12) / ($this->sengine->sustainablewithdrawalpercent / 100);

        if ($retirementAmount != $desiredIncome) {
            $this->sengine->retirementAmountDesired = $desiredIncome;
            parent::simulateCalculateScore("GOAL", 0);
        }
        parent::simulateCalculateScore("PROFILE", 0);
        parent::simulateCalculateScore("ASSET", 0);
        parent::simulateCalculateScore("DEBTS", 0);
        parent::simulateCalculateScore("INCOME", 0);
        parent::simulateCalculateScore("EXPENSE", 0);

        $balance = $this->sengine->userSumOfAssets;
        $amountNeeded = $this->sengine->retirementAmountDesired;
        $contributions = $this->sengine->taxableAnnualSavings + $this->sengine->taxDeferredAnnualSavings + $this->sengine->taxFreeAnnualSavings;
        $years = $this->sengine->yearToRetire;
        $rate = $this->sengine->userGrowthRate / 100;

        for ($i = 1; $i <= $years; $i++) {
          $balance = $balance * (1 + $rate) + $contributions;
        }

        $inflation = 0.034;
        for ($i = 1; $i <= $years; $i++) {
          $balance = $balance / (1 + $inflation);
        }
        $max = 250;
        if ($amountNeeded && $amountNeeded > 0) {
          $this->sengine->wfPoint12 = $max * ($balance / $amountNeeded);
          $this->sengine->wfPoint12 = ($this->sengine->wfPoint12 > 0) ? $this->sengine->wfPoint12 : 0;
          $this->sengine->wfPoint12 = ($this->sengine->wfPoint12 < $max) ? $this->sengine->wfPoint12 : $max;
        } else {
          $this->sengine->wfPoint12 = $max;
        }

        $totalScore = $this->sengine->updateScore();

        parent::unsetEngine();
        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'totalscore' => $totalScore, 'message' => 'Financial Highlights details added')));
    }

    function actionGetUserEstimates() {

        $user_id = 0;
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
        }

        $estimation = Estimation::model()->findByPk($user_id);
        if (!$estimation) {
            $estimation = new Estimation();
        }
        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'estimates' => $estimation)));
    }
}

?>