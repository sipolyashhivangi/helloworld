<?php

/* * ********************************************************************
 * Filename: IncomeController.php
 * Folder: controllers
 * Description: Calls score engine income section
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class IncomeController extends Scontroller {

    public function accessRules() {
        return array_merge(
                        array(array('allow', 'users' => array('?'))),
                        // Include parent access rules
                        parent::accessRules()
        );
    }

    function actionIncomeCrud() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $action = $_POST["action"];

        switch ($action) {
            case "ADD":
                $this->createupdateincome();
                break;

            case "READ":
                $this->readincome();
                break;

            case "UPDATE":
                $this->createupdateincome();
                break;

            case "DELETE":
                break;
        }
    }

    //  Function for Creating / Updating the income table:
    //
    function createupdateincome() {

		$wsUserObject = Yii::app()->getSession()->get('wsuser');
		$user_id = Yii::app()->getSession()->get('wsuser')->id;
		$income = Income::model()->findBySql("select * from income where user_id= :user_id", array("user_id" => $user_id));

        if (!$income) {
            $income = new Income();
            $income->user_id = $user_id;
        }

		$income->gross_income = $_POST["grossincome"];
        $income->investment_income = $_POST["investincome"];
        $income->spouse_income = $_POST["spouseincome"];
        $income->retirement_plan = $_POST["retireincome"];
        $income->pension_income = $_POST["pensionincome"];
        $income->social_security = $_POST["socialincome"];
        $income->disability_benefit = $_POST["disaincome"];
        $income->veteran_income = $_POST["veteincome"];
        $income->totaluserincome = $_POST["totaluserincome"];
        if ($income->save()) {
            $this->reCalculateScore($income, "ADD", $user_id);
            unset($income);
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'totalscore' => $this->totalScore, 'message' => 'Income successfully updated.', 'Sengine' => 'Income Updated')));
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Could not update income at this time.', 'income' => array())));
        }
    }

    //  Function for reading the income table:
    function readincome() {
		$wsUserObject = Yii::app()->getSession()->get('wsuser');
		$id = Yii::app()->getSession()->get('wsuser')->id;

        $income = Income::model()->findBySql("select * from income where user_id= :user_id", array("user_id" => $id));

        if ($income) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => 'Income successfully read', 'income' => $income )));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => 'ERROR', 'message' => 'Could not read income at this time.', 'income' => array())));
        }
    }

    /**
     *
     * @param type $income
     * @param type $action
     * @param type $user_id
     */
    function reCalculateScore($income, $action, $user_id) {

        $spouseIncome = $income->spouse_income + $income->retirement_plan + $income->pension_income + $income->social_security
                + $income->disability_benefit + $income->veteran_income;

        parent::setEngine();
        $this->sengine->spouseIncome = ($spouseIncome > 0) ? $spouseIncome : 0;
        $this->sengine->investmentIncome = ($income->investment_income > 0) ? $income->investment_income : 0;

        if ($income && $income->totaluserincome > 0) {
            $this->sengine->userIncomePerMonth = $income->totaluserincome;
            $this->sengine->userProfilePoints_income = 1;
        } else {
            $this->sengine->userProfilePoints_income = 0;
            parent::saveEngine();

            $estimation = Estimation::model()->find("user_id=:user_id", array("user_id" => $user_id));
            parent::setEngine();
            if ($estimation && $estimation->houseincome > 0) {
                $this->sengine->userIncomePerMonth = $estimation->houseincome;
            } else {
                //default
                $this->sengine->userIncomePerMonth = 5000;
            }
        }

        $this->sengine->grossIncome = ($income->gross_income > 0) ? $income->gross_income : $this->sengine->userIncomePerMonth;
        parent::saveEngine();

        $assetObj = new Assets();
        $contribution = $assetObj->findBySql("select sum(empcontribution) as retSum, sum(contribution) as retireContriSum from assets where type in ('CR','IRA') and status=0 and user_id=:user_id and assettype <> 51", array("user_id" => $user_id));
        parent::setEngine();
        if ($contribution) {
            $empsavings = ($contribution->retSum / 100) * $this->sengine->grossIncome;
            $savings = $contribution->retireContriSum;
            $this->sengine->taxDeferredAnnualSavings = 12 * ($savings + $empsavings);
        } else {
            $this->sengine->taxDeferredAnnualSavings = 0;
        }
        parent::saveEngine();
        unset($contribution);

        // type equal to 51 => ROTH => tax free
        $contribution = $assetObj->findBySql("select sum(empcontribution) as retSum, sum(contribution) as retireContriSum from assets where type in ('CR','IRA') and assettype=51 and status=0 and user_id=:user_id", array("user_id" => $user_id));
        parent::setEngine();
        if ($contribution) {
            $empsavings = ($contribution->retSum / 100) * $this->sengine->grossIncome;
            $savings = $contribution->retireContriSum;
            $this->sengine->taxFreeAnnualSavings = 12 * ($savings + $empsavings);
        } else {
            $this->sengine->taxFreeAnnualSavings = 0;
        }
        parent::saveEngine();
        unset($contribution);

        $retirementAmount = $this->sengine->retirementAmountDesired;
        $sectionNames = "";
        parent::setupDefaultRetirementGoal();
        if ($retirementAmount != $this->sengine->retirementAmountDesired) {
            $sectionNames = "GOAL|";
        }
        $sectionNames .= "PROFILE|INCOME";
        parent::calculateScore($sectionNames, $user_id);
    }

}

?>
