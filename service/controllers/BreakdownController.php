<?php

/* * ********************************************************************
 * Filename: BreakdownController.php
 * Folder: controllers
 * Description: Getting input from the HTML
 * @author Vinoth Arunagiri(For TruGlobal Inc)
 * @copyright (c) 2013 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class BreakdownController extends Bcontroller {

    // Define access control
    public function accessRules() {
        return array_merge(
                array(array('allow', 'users' => array('?'))),
                // Include parent access rules
                parent::accessRules()
        );
    }

    public function actionBreakdowntabs() {
        try {
            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $userid = Yii::app()->getSession()->get('wsuser')->id;

                if (isset($_POST['reset'])) {
                    parent::resetBreakdownEngine($userid);
                }

                if (isset($_POST['reset']) || !isset(Yii::app()->session["benginepoint12"]) || !isset(Yii::app()->session["benginemc"])) {
                    $balance = $this->bengine->userSumOfAssets;
                    $amountNeeded = $this->bengine->retirementAmountDesired;
                    $contributions = $this->bengine->taxableAnnualSavings + $this->bengine->taxDeferredAnnualSavings + $this->bengine->taxFreeAnnualSavings;
                    $years = $this->bengine->yearToRetire;
                    $rate = $this->bengine->userGrowthRate / 100;

                    for ($i = 1; $i <= $years; $i++) {
                        $balance = $balance * (1 + $rate) + $contributions;
                    }

                    $inflation = 0.034;
                    for ($i = 1; $i <= $years; $i++) {
                        $balance = $balance / (1 + $inflation);
                    }
                    $max = 250;

                    $MC = $this->bengine->wfPoint12;
                    $wfPoint12 = $MC;
                    if ($amountNeeded && $amountNeeded > 0) {
                        $wfPoint12 = $max * ($balance / $amountNeeded);
                    } else {
                        $wfPoint12 = $max;
                    }
                    Yii::app()->session["benginepoint12"] = $wfPoint12;
                    Yii::app()->session["benginemc"] = $MC;
                }

                parent::setBreakdownEngine($userid);
                $score = $this->bengine->updateScore();
                $recalcParam = '';

                if (isset($_POST['age'])) {
                    $age = $_POST['age'];

                    if ($this->bengine->userCurrentAge < $age) {
                        $this->bengine->userRetirementAge = $age;
                    } else {
                        $this->bengine->userRetirementAge = $this->bengine->userCurrentAge;
                    }

                    $this->bengine->yearToRetire = $this->bengine->userRetirementAge - $this->bengine->userCurrentAge;

                    if ($this->bengine->spouseAge > 0) {
                        $this->bengine->spouseRetAge = $this->bengine->yearToRetire + $this->bengine->spouseAge;
                    } else {
                        $this->bengine->spouseRetAge = 0;
                    }

                    $ratesDependency = new CDbCacheDependency('SELECT * FROM sustainablerates');
                    $rates = Sustainablerates::model()->cache(QUERY_CACHE_TIMEOUT, $ratesDependency)->find('age = ' . $age);

                    $sustainableRate = isset($rates->sustainablewithdrawalpercent) ? $rates->sustainablewithdrawalpercent : 4;
                    $this->bengine->sustainablewithdrawalpercent = $sustainableRate;

                    $recalcParam = "BDAGE";
                }


                if (isset($_POST['goal'])) {
                    $goal = $_POST['goal'];
                    $userIncome = Income::model()->find("user_id=:user_id", array("user_id" => $userid));

                    $socialIncome = 0;
                    $assetSocial = Assets::model()->findBySql("select sum(balance) as socialsecurityTotal from assets where user_id=:user_id and type='SS' and status=0", array("user_id" => $userid));
                    if ($assetSocial) {
                        $socialIncome = $assetSocial->socialsecurityTotal;
                    } else if ($userIncome) {
                        $socialIncome = $userIncome->social_security;
                    }

                    $pensionIncome = 0;
                    $assetPension = Assets::model()->findBySql("select sum(balance) as pensionTotal from assets where user_id=:user_id and type='PENS' and status=0", array("user_id" => $userid));
                    if ($assetPension) {
                        $pensionIncome = $assetPension->pensionTotal;
                    } else if ($userIncome) {
                        $pensionIncome = $userIncome->pension_income;
                    }

                    $totalUserIncome = ($goal * 12);
                    $desiredIncome = ($totalUserIncome - $pensionIncome * 12 - $socialIncome * 12) / ($this->bengine->sustainablewithdrawalpercent / 100);
                    $this->bengine->retirementAmountDesired = Utility::amountToDB($desiredIncome);

                    $recalcParam = "BDGOALS";
                }

                // Savings = amount of money user is saving monthly,
                // Savings Points are WfPoint2,WfPoint12,WfPoint16,WfPoint17,WfPoint18
                if (isset($_POST['savings'])) {
                    $savings = $_POST['savings'];
                    ## point WfPoint18 as per GoalController.php

                    $this->bengine->taxableAnnualSavings = 12 * $savings;
                    $this->bengine->taxDeferredAnnualSavings = 0;
                    $this->bengine->taxFreeAnnualSavings = 0;
                    if ($savings > 0) {
                        $this->bengine->investmentFactor = 1;
                        if (!$this->bengine->retirementMonthlyContribution) {
                            parent::setEngine($userid);
                            $this->bengine->retirementMonthlyContribution = $this->sengine->retirementMonthlyContribution;
                        }
                    } else {
                        $this->bengine->investmentFactor = 0;
                        $this->bengine->retirementMonthlyContribution = false;
                    }
                    $recalcParam = "BDSAVINGS";
                }
                // Points 11, 12, 16, 17, 29, 30
                if (isset($_POST['assets'])) {
                    $assets = $_POST['assets'];

                    $assetData = parent::getAssetData($userid);
                    $assetsObj = $assetData[0];
                    $insuranceObj = $assetData[1];
                    $pertrack = $assetData[2];
                    $tickers = $assetData[3];

                    parent::setEngine($userid);
                    if ($assets <= $this->sengine->userSumOfOtherAssets) {
                        $assetsObj = 0;
                        $insuranceObj = 0;
                        $this->bengine->userSumOfAssets = 0;
                        $this->bengine->insuranceCashValue = 0;
                        $this->bengine->userSumOfGoalSettingAssets = 0;
                        $this->bengine->numeratorP14 = 0;
                        $this->bengine->userSumOfOtherAssets = $assets;
                    } else if ($this->sengine->insuranceCashValue > 0 && $assets <= $this->sengine->userSumOfOtherAssets + $this->sengine->insuranceCashValue) {
                        $assetsObj = 0;
                        $consumerAssets = ($assets - $this->sengine->userSumOfOtherAssets);

                        $this->bengine->userSumOfAssets = $consumerAssets;
                        $this->bengine->userSumOfGoalSettingAssets = $consumerAssets;
                        $this->bengine->numeratorP14 = $consumerAssets;
                        $this->bengine->insuranceCashValue = $consumerAssets;

                        $this->bengine->userSumOfOtherAssets = $this->sengine->userSumOfOtherAssets;

                        $percentChange = ($consumerAssets / $this->sengine->insuranceCashValue);
                        foreach ($insuranceObj as $each) {
                            $each->cashvalue = $percentChange * $each->cashvalue;
                        }
                    } else {
                        $consumerAssets = ($assets - $this->sengine->userSumOfOtherAssets);

                        if ($this->sengine->userSumOfAssets == 0 || ($this->sengine->userSumOfAssets - $this->sengine->insuranceCashValue == 0)) {
                            $this->bengine->userSumOfGoalSettingAssets = $consumerAssets;
                            $this->bengine->numeratorP14 = $consumerAssets;
                            $percentChange = 1;
                        } else {
                            $percentChange = $consumerAssets / $this->sengine->userSumOfAssets;
                            $this->bengine->userSumOfGoalSettingAssets = $percentChange * $this->sengine->userSumOfGoalSettingAssets;
                            $this->bengine->numeratorP14 = $percentChange * $this->sengine->numeratorP14;
                            $percentChange = ($consumerAssets  - $this->sengine->insuranceCashValue) / ($this->sengine->userSumOfAssets - $this->sengine->insuranceCashValue);
                        }

                        if ($assetsObj == 0) {
                            $assetsObj = array();
                            $valueObj = new stdClass();
                            $valueObj->balance = $consumerAssets - $this->sengine->insuranceCashValue;
                            $valueObj->invpos = '';
                            $valueObj->type = "BANK";
                            $assetsObj[0] = $valueObj;
                            $percentChange = 1;
                        }

                        foreach ($assetsObj as $each) {
                            $each->balance = $percentChange * $each->balance;
                            if ($each->invpos) {
                                $invPosArray = json_decode($each->invpos);
                                if ($invPosArray && !empty($invPosArray)) {
                                    foreach ($invPosArray as $invPos) {
                                        $invPos->amount = $percentChange * $invPos->amount;
                                    }
                                }
                                $each->invpos = json_encode($invPosArray);
                            }
                        }

                        $this->bengine->userSumOfAssets = $consumerAssets;
                        $this->bengine->userSumOfOtherAssets = $this->sengine->userSumOfOtherAssets;
                        $this->bengine->insuranceCashValue = $this->sengine->insuranceCashValue;
                    }

                    if ($assets == 0) {
                        if ($this->bengine->userSumOfDebts == 0 && $this->bengine->healthInsuranceType == '' && $this->bengine->LifeInsurance == 0 && $this->bengine->incomeCoverage == 0 && $this->bengine->dailyLongTermAmount == 0 && !$this->bengine->hasHomeInsurance && !$this->bengine->hasVehicleInsurance && !$this->bengine->hasVehicleInsurance) {
                            $this->bengine->isUserEnteredAccount = false;
                        } else {
                            $this->bengine->isUserEnteredAccount = true;
                        }
                        $this->bengine->userProfilePoints_assets = 0;
                    } else {
                        $this->bengine->isUserEnteredAccount = true;
                        $value = 0;
                        if ($this->sengine->userSumOfAssets + $this->sengine->userSumOfOtherAssets > 0) {
                            $value = $this->sengine->userProfilePoints_assets * ($assets / ($this->sengine->userSumOfAssets + $this->sengine->userSumOfOtherAssets));
                        }
                        $value = ($value > 0) ? $value : 0;
                        $value = ($value < 10) ? $value : 10;
                        $this->bengine->userProfilePoints_assets = $value;
                    }

                    parent::CalculateBreakdownPoint10($assetsObj, $insuranceObj, $pertrack);
                    $recalcParam = "BDASSET";
                }

                if (isset($_POST['debts'])) {
                    $debts = $_POST['debts'];
                    $this->bengine->userSumOfDebts = $debts;
                    $debtData = parent::getDebtData($userid);
                    $restructuringDebtsArr = $debtData[0];
                    $personalDebtLoanArr = $debtData[1];

                    parent::setEngine($userid);
                    if ($debts < $this->sengine->mortgageBalance) {
                        foreach ($personalDebtLoanArr as $each) {
                            $each->balance = 0;
                            $each->minimumPayment = 0;
                            $each->actualPayment = 0;
                        }

                        foreach ($restructuringDebtsArr as $each) {
                            $each->balance = 0;
                            $each->minimum = 0;
                            $each->payment = 0;
                        }
                        $this->bengine->emiLoanCC = 0;
                        $this->bengine->otherDebts = 0;
                        $this->bengine->rentMortgage = $debts * ($this->sengine->rentMortgage / $this->sengine->mortgageBalance);
                        $this->bengine->mortgageBalance = $debts;
                    } else {
                        $consumerDebt = ($debts - $this->sengine->mortgageBalance);

                        if ($this->sengine->otherDebts == 0) {
                            $restructuringDebtsArr = array();
                            $personalDebtLoanArr = array();

                            $valueObj = new stdClass();
                            $valueObj->creditor = "Temp Debt";
                            $valueObj->balance = $consumerDebt;
                            $valueObj->minimumPayment = 0.02 * $consumerDebt;
                            $valueObj->actualPayment = 0.02 * $consumerDebt;
                            $valueObj->rate = 0.01;
                            $valueObj->type = "LOAN";

                            $personalDebtLoanArr[0] = $valueObj;

                            $valueObj1 = new stdClass();
                            $valueObj1->debtName = "Temp Debt";
                            $valueObj1->balance = $consumerDebt;
                            $valueObj1->minimum = 0.02 * $consumerDebt;
                            $valueObj1->payment = 0.02 * $consumerDebt;
                            $valueObj1->rate = 0.01;
                            $restructuringDebtsArr[0] = $valueObj1;

                            $percentChange = 1;
                        } else {
                            $percentChange = ($consumerDebt / $this->sengine->otherDebts);
                        }

                        foreach ($personalDebtLoanArr as $each) {
                            $each->balance = $percentChange * $each->balance;
                            $each->minimumPayment = $percentChange * $each->minimumPayment;
                            $each->actualPayment = $percentChange * $each->actualPayment;
                        }

                        foreach ($restructuringDebtsArr as $each) {
                            $each->balance = $percentChange * $each->balance;
                            $each->minimum = $percentChange * $each->minimum;
                            $each->payment = $percentChange * $each->payment;
                        }

                        if ($this->sengine->otherDebts > 0) {
                            $percentPayments = ($this->sengine->emiLoanCC / $this->sengine->otherDebts);
                            $baseEmiLoan = 0;
                        } else {
                            $percentPayments = 0.02;
                            $baseEmiLoan = $this->sengine->emiLoanCC;
                        }
                        $this->bengine->emiLoanCC = $baseEmiLoan + $consumerDebt * $percentPayments;
                        $this->bengine->otherDebts = $consumerDebt;
                        $this->bengine->rentMortgage = $this->sengine->rentMortgage;
                        $this->bengine->mortgageBalance = $this->sengine->mortgageBalance;
                    }

                    if ($debts == 0 && $this->sengine->userSumOfDebts != 0) {
                        if ($this->bengine->userSumOfAssets == 0 && $this->bengine->healthInsuranceType == '' && $this->bengine->LifeInsurance == 0 && $this->bengine->incomeCoverage == 0 && $this->bengine->dailyLongTermAmount == 0 && !$this->bengine->hasHomeInsurance && !$this->bengine->hasVehicleInsurance && !$this->bengine->hasVehicleInsurance) {
                            $this->bengine->isUserEnteredAccount = false;
                        } else {
                            $this->bengine->isUserEnteredAccount = true;
                        }
                        $this->bengine->userProfilePoints_debts = 0;
                        $this->bengine->creditCardFlag = 1;
                        $this->bengine->mortgageInfo = false;
                        parent::CalculateBreakdownPoint5($restructuringDebtsArr, $personalDebtLoanArr);
                    } else if ($debts == 0 && $this->sengine->userSumOfDebts == 0) {
                        $this->bengine->isUserEnteredAccount = $this->sengine->isUserEnteredAccount;
                        $this->bengine->emiLoanCC = $this->sengine->emiLoanCC;
                        $this->bengine->rentMortgage = $this->sengine->rentMortgage;
                        $this->bengine->userProfilePoints_debts = $this->sengine->userProfilePoints_debts;
                        $this->bengine->creditCardFlag = $this->sengine->creditCardFlag;
                        $this->bengine->mortgageInfo = $this->sengine->mortgageInfo;
                        $this->bengine->wfPoint5 = $this->sengine->wfPoint5;
                    } else {
                        $this->bengine->isUserEnteredAccount = true;
                        $value = 0;
                        if ($this->sengine->userSumOfDebts != 0) {
                            $value = $this->sengine->userProfilePoints_debts * ($debts / $this->sengine->userSumOfDebts);
                        }
                        $value = ($value > 0) ? $value : 0;
                        $value = ($value < 10) ? $value : 10;
                        $this->bengine->userProfilePoints_debts = $value;
                        $this->bengine->creditCardFlag = $this->sengine->creditCardFlag;
                        $this->bengine->mortgageInfo = $this->sengine->mortgageInfo;
                        parent::CalculateBreakdownPoint5($restructuringDebtsArr, $personalDebtLoanArr);
                    }

                    $recalcParam = "BDDEBTS";
                }
                if (isset($_POST['living'])) {
                    $living = $_POST['living'];
                    $this->bengine->userExpensePerMonth = $living;

                    if ($living > 0) {
                        $this->bengine->userProfilePoints_expense = 1;
                    } else {
                        $this->bengine->userProfilePoints_expense = 0;
                    }

                    $recalcParam = "BDLIVINGS";
                }

                if (isset($_POST['reset']) && $_POST['reset'] == 'all') {
                    $recalcParam = "BDALL";
                }

                $balance = $this->bengine->userSumOfAssets;
                $amountNeeded = $this->bengine->retirementAmountDesired;
                $contributions = $this->bengine->taxableAnnualSavings + $this->bengine->taxDeferredAnnualSavings + $this->bengine->taxFreeAnnualSavings;
                $years = $this->bengine->yearToRetire;
                $rate = $this->bengine->userGrowthRate / 100;

                for ($i = 1; $i <= $years; $i++) {
                    $balance = $balance * (1 + $rate) + $contributions;
                }

                $inflation = 0.034;
                for ($i = 1; $i <= $years; $i++) {
                    $balance = $balance / (1 + $inflation);
                }
                $max = 250;

                $wfPoint12 = Yii::app()->session["benginepoint12"];
                if ($amountNeeded && $amountNeeded > 0) {
                    $wfPoint12 = $max * ($balance / $amountNeeded);
                } else {
                    $wfPoint12 = $max;
                }

                $this->bengine->wfPoint12 = ($wfPoint12 - Yii::app()->session["benginepoint12"]) + Yii::app()->session["benginemc"];
                $this->bengine->wfPoint12 = ($this->bengine->wfPoint12 > 0) ? $this->bengine->wfPoint12 : 0;
                $this->bengine->wfPoint12 = ($this->bengine->wfPoint12 < $max) ? $this->bengine->wfPoint12 : $max;

                $score = parent::breakdownCalculateScore($recalcParam);
                $returnArrayJson = array('status' => "OK", 'result' => $score); //, 'points' => $pointsAndTotalArr);
                $this->sendResponse("200", CJSON::encode($returnArrayJson));
            }
        } catch (Exception $E) {
            echo $E;
        }
    }

    public function actionGetBreakdown() {

        //if there is no session
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        parent::setEngine();

        $goal = Goal::model()->find("user_id=:user_id and goalstatus=1 and goaltype = 'RETIREMENT'", array("user_id" => $user_id));
        if (!$goal) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "The breakdown section is only for non-retired people.")));
        }

        $userPerDetails = Userpersonalinfo::model()->find("user_id = :user_id", array(':user_id' => $user_id));
        $retage = 65;
        if ($userPerDetails && isset($userPerDetails->retirementage) && $userPerDetails->retirementage > 0) {
            $retage = $userPerDetails->retirementage;
        }

        $totalScore = $this->sengine->updateScore();
        $totalScore = ($totalScore < 0) ? 0 : $totalScore;
        $totalScore = ($totalScore > 1000) ? 1000 : $totalScore;

        $wsEachUserItemBankAccount = array(
            'retage' => $retage,
            'retamount' => $goal->monthlyincome,
            'savings' => ($this->sengine->taxableAnnualSavings + $this->sengine->taxDeferredAnnualSavings + $this->sengine->taxFreeAnnualSavings) / 12,
            'assets' => $this->sengine->userSumOfAssets + $this->sengine->userSumOfOtherAssets,
            'debts' => $this->sengine->userSumOfDebts,
            'livingCosts' => $this->sengine->userExpensePerMonth,
            'totalscore' => $totalScore,
        );
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "lsacc" => $wsEachUserItemBankAccount)));
    }

    function actionSaveBreakdowntabs() {

        //if there is no session
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        if (isset($_POST)) {
            $userBreakDownDetail = new BreakdownChange();
            $userBreakDownDetail->user_id = Yii::app()->getSession()->get('wsuser')->id;
            $userBreakDownDetail->name = $_POST["name"];
            $userBreakDownDetail->age = $_POST["age"];
            $userBreakDownDetail->goal = $_POST["goal"];
            $userBreakDownDetail->savings = $_POST["savings"];
            $userBreakDownDetail->assets = $_POST["assets"];
            $userBreakDownDetail->debts = $_POST["debts"];
            $userBreakDownDetail->living = $_POST["living"];
            $userBreakDownDetail->timestamp = date("Y-m-d H:i:s");
            $userBreakDownDetail->save();

            $userBreakDownDetails = BreakdownChange::model()->findAll("user_id = :user_id", array('user_id' => Yii::app()->getSession()->get('wsuser')->id));
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "breakdownData" => $userBreakDownDetails)));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameters are not found.")));
        }
    }

    function actionUpdateBreakdowntabs() {

        //if there is no session
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        if (isset($_POST) && isset($_POST["id"])) {
            $userBreakDownDetail = BreakdownChange::model()->find("user_id = :user_id and id = :id", array('user_id' => Yii::app()->getSession()->get('wsuser')->id, 'id' => $_POST["id"]));
            if ($userBreakDownDetail) {
                $userBreakDownDetail->age = $_POST["age"];
                $userBreakDownDetail->goal = $_POST["goal"];
                $userBreakDownDetail->savings = $_POST["savings"];
                $userBreakDownDetail->assets = $_POST["assets"];
                $userBreakDownDetail->debts = $_POST["debts"];
                $userBreakDownDetail->living = $_POST["living"];
                $userBreakDownDetail->timestamp = date("Y-m-d H:i:s");
                $userBreakDownDetail->save();

                $userBreakDownDetails = BreakdownChange::model()->findAll("user_id = :user_id", array('user_id' => Yii::app()->getSession()->get('wsuser')->id));
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "breakdownData" => $userBreakDownDetails)));
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "You do not have permissions to update this scenario.")));
            }
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameters are not found.")));
        }
    }

    function actionDeleteBreakdowntabs() {

        //if there is no session
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        if (isset($_POST) && isset($_POST["id"])) {
            $id = $_POST["id"];
            $userBreakDownDetails = BreakdownChange::model()->find("user_id = :user_id and id = :id", array('user_id' => Yii::app()->getSession()->get('wsuser')->id, 'id' => $id));
            if ($userBreakDownDetails) {
                BreakdownChange::model()->deleteAll("id = " . $id);

                $userBreakDownDetails = BreakdownChange::model()->findAll("user_id = :user_id", array('user_id' => Yii::app()->getSession()->get('wsuser')->id));
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "breakdownData" => $userBreakDownDetails)));
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "You do not have permissions to delete this scenario.")));
            }
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameters are not found.")));
        }
    }

}

?>