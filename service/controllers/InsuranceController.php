<?php

/* * ********************************************************************
 * Filename: SIncomeController.php
 * Folder: controllers
 * Description: Calls score engine income section
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class InsuranceController extends Scontroller {

    public function accessRules() {
        return array_merge(
                array(array('allow', 'users' => array('?'))),
                // Include parent access rules
                parent::accessRules()
        );
    }

    function actionGetInsurance() {
        //if there is no session
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $insuranceORMObj = Insurance::model()->findAll("user_id=:user_id and status!=1", array("user_id" => $user_id));
        $insurance = array();
        foreach ($insuranceORMObj as $acc) {
            $lsEachAcc = array(
                'id' => $acc->id,
                'name' => $acc->name,
                'insurancefor' => $acc->insurancefor,
                'user_id' => $acc->user_id,
                'context' => $acc->context,
                'amount' => $acc->cashvalue,
                'annualpremium' => $acc->annualpremium,
                'reviewyear' => $acc->reviewyear,
                'actid' => $acc->actid,
                'dailybenfitamt' => $acc->dailybenfitamt,
                'policyendyear' => $acc->policyendyear,
                'coverageamt' => $acc->coverageamt,
                'lifeinstype' => $acc->lifeinstype,
                'amtupondeath' => $acc->amtupondeath,
                'deductible' => $acc->deductible,
                'grouppolicy' => $acc->grouppolicy,
                'beneficiary' => $acc->beneficiary,
                'dailyamtindexed' => $acc->dailyamtindexed,
                'accttype' => $acc->type,
                'subtype' => $acc->subtype,
                'refId' => $acc->refid,
                'status' => $acc->status,
                'priority' => $acc->priority,
                'lstype' => "INSURANCE"
            );
            $insurance[] = $lsEachAcc;
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "insurances" => $insurance)));
    }

    function actionInsurancecrud() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        if (!isset($_POST["action"])) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'action' was not set.")));
        }
        $action = $_POST["action"];
        if ($action != "ADD" && !isset($_POST["id"])) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'id' was not set.")));
        }

        $actionSet = false;
        switch ($action) {
            CASE "ADD":
                $this->createupdateInsurance();
                $actionSet = true;
                break;
            CASE "READ":
                $this->ReadInsurance();
                $actionSet = true;
                break;
            CASE "UPDATE":
                $this->createupdateInsurance();
                $actionSet = true;
                break;
            CASE "DELETE":
                $this->DeleteInsurance();
                $actionSet = true;
                break;
            CASE "HIDE":
                $this->ToggleStatus();
                $actionSet = true;
                break;
            CASE "UNHIDE":
                $this->ToggleStatus();
                $actionSet = true;
                break;
        }
        if (!$actionSet) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'action' must be set to either 'ADD', 'READ', 'UPDATE', 'HIDE', 'UNHIDE', or 'DELETE'.")));
        }
    }

    /**
     *
     */
    function createupdateInsurance() {
        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $action = $_POST["action"];
        $id = isset($_POST["id"]) ? $_POST["id"] : 0;

        if (!isset($_POST["accttype"]) && $action == 'ADD') {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'accttype' was not set.")));
        }

        $needsUpdate = false;
        $insurance = new Insurance();
        if ($action == 'ADD') {
            $ctype = strtoupper($_POST["accttype"]);
            if($ctype != 'LIFE' && $ctype != 'VEHI' && $ctype != 'DISA' && $ctype != 'LONG' &&
               $ctype != 'HOME' && $ctype != 'HEAL' && $ctype != 'UMBR') {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'accttype' should be set to either LIFE, DISA, LONG, VEHI, HOME, HEAL, or UMBR.")));
            }
            $totalinsurance = Insurance::model()->count("user_id=:user_id", array("user_id" => $user_id));
            $insurance->priority = $totalinsurance + 1;
            $insurance->context = 'MANUAL';
            $insurance->refid = '';
            $insurance->status = 0;
            $insurance->type = $ctype;
            $insurance->user_id = $user_id;
            $insurance->modifiedtimestamp = date("Y-m-d H:i:s");
            $needsUpdate = true;
        } else if($action == 'UPDATE' && $id > 0) {
            $insurance = Insurance::model()->findByPk($id);
            if (!$insurance) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'id' was not set to a valid value.")));
            }
            else if($insurance->user_id != $user_id) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "This user does not have permissions to update this insurance.")));
            }
        }
        else
        {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "We could not add/update the insurance at this time.")));
        }

        if(isset($_POST["name"])) {
            $name = $_POST["name"];
            $insurance->name = $name;
            $needsUpdate = true;
        }

        if(isset($_POST["insurancefor"])) {
            $insurancefor = $_POST["insurancefor"];
            $insurance->insurancefor = $insurancefor;
            $needsUpdate = true;
        }

        if(isset($_POST["amount"])) {
            $cashvalue = Utility::amountToDB($_POST["amount"]);
            $insurance->cashvalue = $cashvalue;
            $needsUpdate = true;
        }

        if(isset($_POST["lifeinstype"])) {
            $lifeinstype = $_POST["lifeinstype"];
            $insurance->lifeinstype = $lifeinstype;
            $needsUpdate = true;
        }

        if(isset($_POST["amtupondeath"])) {
            $amtupondeath = Utility::amountToDB($_POST["amtupondeath"]);
            $insurance->amtupondeath = $amtupondeath;
            $needsUpdate = true;
        }

        if(isset($_POST["coverageamt"])) {
            $coverageamt = Utility::amountToDB($_POST["coverageamt"]);
            $insurance->coverageamt = $coverageamt;
            $needsUpdate = true;
        }

        if(isset($_POST["annualpremium"])) {
            $annualpremium = Utility::amountToDB($_POST["annualpremium"]);
            $insurance->annualpremium = $annualpremium;
            $needsUpdate = true;
        }

        if(isset($_POST["dailybenfitamt"])) {
            $dailybenfitamt = Utility::amountToDB($_POST["dailybenfitamt"]);
            $insurance->dailybenfitamt = $dailybenfitamt;
            $needsUpdate = true;
        }

        if(isset($_POST["deductible"])) {
            $deductible = Utility::amountToDB($_POST["deductible"]);
            $insurance->deductible = $deductible;
            $needsUpdate = true;
        }

        if(isset($_POST["dailyamtindexed"])) {
            $dailyamtindexed = $_POST["dailyamtindexed"];
            $insurance->dailyamtindexed = $dailyamtindexed;
            $needsUpdate = true;
        }

        if(isset($_POST["reviewyear"])) {
            $reviewyear = $_POST["reviewyear"];
            $insurance->reviewyear = $reviewyear;
            $needsUpdate = true;
        }

        if(isset($_POST["grouppolicy"])) {
            $grouppolicy = $_POST["grouppolicy"];
            $insurance->grouppolicy = $grouppolicy;
            $needsUpdate = true;
        }

        if(isset($_POST["dailyamtindexed"])) {
            $dailyamtindexed = $_POST["dailyamtindexed"];
            $insurance->dailyamtindexed = $dailyamtindexed;
            $needsUpdate = true;
        }

        if(isset($_POST["beneficiary"])) {
            $beneficiary = $_POST["beneficiary"];
            $insurance->beneficiary = $beneficiary;
            $needsUpdate = true;
        }

        if(isset($_POST["policyendyear"])) {
            $policyendyear = $_POST["policyendyear"];
            $insurance->policyendyear = $policyendyear;
            $needsUpdate = true;
        }

        $insuranceObj = array(
                'id' => $insurance->id,
                'name' => $insurance->name,
                'insurancefor' => $insurance->insurancefor,
                'user_id' => $insurance->user_id,
                'context' => $insurance->context,
                'amount' => $insurance->cashvalue,
                'annualpremium' => $insurance->annualpremium,
                'reviewyear' => $insurance->reviewyear,
                'actid' => $insurance->actid,
                'dailybenfitamt' => $insurance->dailybenfitamt,
                'policyendyear' => $insurance->policyendyear,
                'coverageamt' => $insurance->coverageamt,
                'lifeinstype' => $insurance->lifeinstype,
                'amtupondeath' => $insurance->amtupondeath,
                'deductible' => $insurance->deductible,
                'grouppolicy' => $insurance->grouppolicy,
                'beneficiary' => $insurance->beneficiary,
                'dailyamtindexed' => $insurance->dailyamtindexed,
                'accttype' => $insurance->type,
                'subtype' => $insurance->subtype,
                'refId' => $insurance->refid,
                'status' => $insurance->status,
                'priority' => $insurance->priority,
                'lstype' => "INSURANCE"
            );

        //save to table and call the score engine
        if($needsUpdate) {
            if ($insurance->save()) {
                $insuranceObj["id"] = $insurance->id;
                $this->actionreCalculateScoreInsurance($insurance, "ADD", $user_id, 1); // Enabled for single call
                $this->sendResponse(200, CJSON::encode(array("status" => 'OK', 'totalscore' => $this->totalScore, "message" => 'Insurance has been updated successfully.', "insurance" => $insuranceObj)));
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => 'ERROR', "message" => 'Insurance has not been updated.', "insurance" => $insuranceObj)));
            }
        }
        parent::setEngine();
        $totalScore = $sengineObj->updateScore();
        $this->sendResponse(200, CJSON::encode(array("status" => 'OK', 'totalscore' => $totalScore, "message" => 'Insurance has been updated successfully.', "insurance" => $insuranceObj)));
    }

    function ReadInsurance() {
        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $id = isset($_POST["id"]) ? $_POST["id"] : 0;
        if ($id != 0) {
            $insurance = Insurance::model()->findByPk($id);
            $insuranceObj = array(
                'id' => $insurance->id,
                'name' => $insurance->name,
                'insurancefor' => $insurance->insurancefor,
                'user_id' => $insurance->user_id,
                'context' => $insurance->context,
                'amount' => $insurance->cashvalue,
                'annualpremium' => $insurance->annualpremium,
                'reviewyear' => $insurance->reviewyear,
                'actid' => $insurance->actid,
                'dailybenfitamt' => $insurance->dailybenfitamt,
                'policyendyear' => $insurance->policyendyear,
                'coverageamt' => $insurance->coverageamt,
                'lifeinstype' => $insurance->lifeinstype,
                'amtupondeath' => $insurance->amtupondeath,
                'deductible' => $insurance->deductible,
                'grouppolicy' => $insurance->grouppolicy,
                'beneficiary' => $insurance->beneficiary,
                'dailyamtindexed' => $insurance->dailyamtindexed,
                'accttype' => $insurance->type,
                'subtype' => $insurance->subtype,
                'refId' => $insurance->refid,
                'status' => $insurance->status,
                'priority' => $insurance->priority,
                'lstype' => "INSURANCE"
            );
            if ($insurance && $user_id == $insurance->user_id) {
                $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => "Insurance successfully read.", 'insurance' => $insuranceObj)));
            }
        }
        $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => "Unable to read insurance.")));
    }

    function DeleteInsurance() {

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $id = isset($_POST["id"]) ? $_POST["id"] : 0;
        if($id != 0) {
            $insurance = Insurance::model()->findByPk($id);
            if($insurance && $insurance->user_id == $user_id) {
                if($insurance->status != 1) {
                    $type = $insurance->context;
                    $insurance->status = 1;
                    if ($insurance->save()) {
                        $this->actionreCalculateScoreInsurance($insurance, "DELETE", $user_id, 1); // Enabled for single call
                        // Delete from CE
                        if ($type == 'AUTO') {
                            $obj = new CashedgeController(1); // preparing object
                            $obj->actionDeletefiAcctid($insurance->actid, $insurance->user_id, 'INSU'); // For single call
                        }
                        $otherinsurance = Insurance::model()->findAll("user_id=:user_id and status!=1", array("user_id" => $user_id));
                        if($otherinsurance) $otherinsurancecnt = 1;
                        else $otherinsurancecnt = 0;
                        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'totalscore' => $this->totalScore, 'message' => "Insurance has been deleted.", 'inscount' => $otherinsurancecnt)));
                    } else {
                        $this->sendResponse(200, CJSON::encode(array("status" => 'ERROR', "message" => 'Insurance was not deleted.')));
                    }
                }
                else
                {
                    parent::setEngine();
                    $totalScore = $sengineObj->updateScore();
                    $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'totalscore' => $totalScore, 'message' => "Insurance has been deleted.")));
                }
            }
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "This user does not have permissions to delete this insurance.")));
    }

    function ToggleStatus() {

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $action = $_POST["action"];
        $id = isset($_POST["id"]) ? $_POST["id"] : 0;
        if($id != 0) {
            $insurance = Insurance::model()->findByPk($id);
            if($insurance && $insurance->user_id == $user_id) {
                if($insurance->status != 1) {
                    $needsUpdate = false;
                    if ($insurance->status == 0 && $action == 'HIDE') {
                        $insurance->status = 2;
                        $needsUpdate = true;
                    } else if ($insurance->status == 2 && $action == 'UNHIDE') {
                        $insurance->status = 0;
                        $needsUpdate = true;
                    }
                    if($needsUpdate) {
                        if ($insurance->save()) {
                            $this->actionreCalculateScoreInsurance($insurance, "DELETE", $user_id);
                            $this->sendResponse(200, CJSON::encode(array("status" => 'OK', 'totalscore' => $this->totalScore, "message" => 'Insurance status has been updated.')));
                        } else {
                            $this->sendResponse(200, CJSON::encode(array("status" => 'ERROR', "message" => 'Insurance status was not updated.')));
                        }
                    }
                    else
                    {
                        parent::setEngine();
                        $totalScore = $sengineObj->updateScore();
                        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'totalscore' => $totalScore, 'message' => "Insurance status has been updated.")));
                    }
                }
                else
                {
                    $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => "Insurance has already been deleted.")));
                }
            }
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "This user does not have permissions to update this insurance.")));

    }

    /**
     * Used to recalculate score
     *
     */
    function actionreCalculateScoreInsurance($ceInsurance, $ceAction, $ceUid, $ceFlag = NULL) {

        $insurance = isset($_GET["insurance"]) ? (object) $_GET["insurance"] : $ceInsurance;
        $action = isset($_GET["action"]) ? $_GET["action"] : $ceAction;
        $user_id = isset($_GET["uid"]) ? $_GET["uid"] : isset($ceUid) ? $ceUid : 0;


        if ($user_id == 0 && $ceFlag != 1) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Insufficent Data')));
            // Error Logging needs to be done here.
        }
        $userPerDetails = Userpersonalinfo::model()->find("user_id=:user_id", array("user_id" => $user_id));
        # POINT 1:
        $userDebtAcc = Debts::model()->count("user_id=:user_id and status=0 and context='AUTO'", array("user_id" => $user_id));
        $userAssetAcc = Assets::model()->count("user_id=:user_id and status=0 and context='AUTO'", array("user_id" => $user_id));
        $userInsuranceAcc = Insurance::model()->count("user_id=:user_id and status=0 and context='AUTO'", array("user_id" => $user_id));
        parent::setEngine();
        if ($userDebtAcc > 0 || $userAssetAcc > 0 || $userInsuranceAcc > 0 || $userPerDetails->connectAccountPreference == '1') {
            $this->sengine->isUserDownloadAccount = true;
            $this->sengine->userProfilePoints_connectAccount = 1;
        } else {
            $this->sengine->isUserDownloadAccount = false;
            $this->sengine->userProfilePoints_connectAccount = 0;
        }
        parent::saveEngine();

        # Point 4
        $userDebtAcc = Debts::model()->count("user_id=:user_id and status=0", array("user_id" => $user_id));
        $userAssetAcc = Assets::model()->count("user_id=:user_id and status=0", array("user_id" => $user_id));
        $userInsuranceAcc = Insurance::model()->count("user_id=:user_id and status=0", array("user_id" => $user_id));
        parent::setEngine();
        if ($userDebtAcc > 0 || $userAssetAcc > 0 || $userInsuranceAcc > 0) {
            $this->sengine->isUserEnteredAccount = true;
        } else {
            $this->sengine->isUserEnteredAccount = false;
        }
        parent::saveEngine();

        switch ($insurance->type) {

            case "LIFE":
                # POINT 14:
                $totalAssets = Assets::model()->findBySql("select sum(balance) as total from assets where type not in ('SS','PENS','EDUC','VEHI') and status=0 and user_id=:user_id and livehere <> 1", array("user_id" => $user_id));
                $totalCashValue = Insurance::model()->findBySql("select sum(cashvalue) as total_cashvalue from insurance where type in ('LIFE') and status = 0 and user_id=:user_id and lifeinstype <> 64", array("user_id" => $user_id));
                $diffAssets = Assets::model()->findBySql("select sum(balance) as diff from assets where type in ('BUSI','PROP','OTHE') and status=0 and user_id=:user_id and livehere <> 1", array("user_id" => $user_id));
                $goalSettingAssets = Assets::model()->findBySql("select sum(balance) as total from assets where type in ('BANK','BROK') and status=0 and user_id=:user_id", array("user_id" => $user_id));

                $assetData = parent::getAssetData($user_id);
                $assets = $assetData[0];
                $insurance = $assetData[1];
                $pertrack = $assetData[2];
                $tickers = $assetData[3];

                parent::setEngine();
                parent::CalculatePoint10($assets, $insurance, $pertrack);
                parent::saveEngine();

                parent::setEngine();
                if ($totalAssets) {
                    $this->sengine->userSumOfAssets = $totalAssets->total;
                } else {
                    $this->sengine->userSumOfAssets = 0;
                }
                // Point 3
                if ($goalSettingAssets) {
                    $this->sengine->userSumOfGoalSettingAssets = $goalSettingAssets->total;
                } else {
                    $this->sengine->userSumOfGoalSettingAssets = 0;
                }
                $total = 0;
                if ($assets) {
                    foreach ($assets as $each) {
                        if ($each->invpos && $each->type == 'BROK') {
                            $invPosArray = json_decode($each->invpos);
                            if ($invPosArray && !empty($invPosArray)) {
                                foreach ($invPosArray as $invPos) {
                                    $total += ($invPos->amount) ? abs($invPos->amount) : 0;
                                }
                            }
                        }
                    }
                    $this->sengine->userSumOfGoalSettingAssets -= $total;
                }
                // Point 3,14
                if ($totalCashValue) {
                    $this->sengine->userSumOfAssets += $totalCashValue->total_cashvalue;
                    $this->sengine->userSumOfGoalSettingAssets += $totalCashValue->total_cashvalue;
                    $this->sengine->insuranceCashValue = $totalCashValue->total_cashvalue;
                } else {
                    $this->sengine->insuranceCashValue = 0;
                }
                if ($diffAssets) {
                    $this->sengine->numeratorP14 = $this->sengine->userSumOfAssets - $diffAssets->diff;
                } else {
                    $this->sengine->numeratorP14 = $this->sengine->userSumOfAssets;
                }
                parent::saveEngine();

                $insuranceInfo = Insurance::model()->findBySql("select sum(amtupondeath) as life_insu_sum from insurance where type IN ('LIFE') and user_id=:user_id and status=0 and insurancefor='80' and beneficiary in ('81','82','83')", array("user_id" => $user_id));
// spouse /partner / children/ revocable trust
                parent::setEngine();
                $this->sengine->LifeInsurance = isset($insuranceInfo->life_insu_sum) ? ($insuranceInfo->life_insu_sum) : 0;
                parent::saveEngine();

                $granularIns = Insurance::model()->findAllBySql("select reviewyear from insurance where type in ('LIFE') and user_id=:user_id and status=0", array("user_id" => $user_id));
                parent::setEngine();
                $year = date('Y');
                if ($granularIns) {
                    foreach ($granularIns as $insurance) {
                        if ($insurance && isset($insurance->reviewyear) && $insurance->reviewyear != 'Year' && $insurance->reviewyear < $year) {
                            $year = $insurance->reviewyear;
                        }
                    }
                }
                $this->sengine->insuranceReviewYear29 = $year;
                parent::saveEngine();
                break;

            case "DISA":
                $coverageAmount = 0;
                $granular = Insurance::model()->findAllBySql("select reviewyear, coverageamt from insurance where type in ('DISA') and user_id=:user_id and status=0", array("user_id" => $user_id));
                parent::setEngine();
                $year = date('Y');
                if ($granular) {
                    foreach ($granular as $insurance) {
                        if ($insurance && isset($insurance->reviewyear) && $insurance->reviewyear != 'Year' && $insurance->reviewyear < $year) {
                            $year = $insurance->reviewyear;
                        }
                        if ($insurance && isset($insurance->coverageamt) && $insurance->coverageamt > $coverageAmount) {
                            $coverageAmount = $insurance->coverageamt;
                        }
                    }
                }
                $this->sengine->insuranceReviewYear30 = $year;
                $this->sengine->incomeCoverage = $coverageAmount / 100;
                parent::saveEngine();
                break;

            case "LONG":
                $dailyLongTermAmount = 0;
                $granular = Insurance::model()->findAllBySql("select reviewyear, dailybenfitamt from insurance where type in ('LONG') and user_id=:user_id and status=0", array("user_id" => $user_id));
                parent::setEngine();
                $year = date('Y');
                if ($granular) {
                    foreach ($granular as $insurance) {
                        if ($insurance && isset($insurance->reviewyear) && $insurance->reviewyear != 'Year' && $insurance->reviewyear < $year) {
                            $year = $insurance->reviewyear;
                        }
                        if ($insurance && isset($insurance->dailybenfitamt) && $insurance->dailybenfitamt > $dailyLongTermAmount) {
                            $dailyLongTermAmount = $insurance->dailybenfitamt;
                        }
                    }
                }
                $this->sengine->insuranceReviewYear31 = $year;
                $this->sengine->dailyLongTermAmount = $dailyLongTermAmount;
                parent::saveEngine();
                break;

            case "HOME":
                $hasHomeInsurance = false;
                $granular = Insurance::model()->findAllBySql("select reviewyear from insurance where type in ('HOME') and user_id=:user_id and status=0", array("user_id" => $user_id));
                parent::setEngine();
                $year = date('Y');
                if ($granular) {
                    $hasHomeInsurance = true;
                    foreach ($granular as $insurance) {
                        if ($insurance && isset($insurance->reviewyear) && $insurance->reviewyear != 'Year' && $insurance->reviewyear < $year) {
                            $year = $insurance->reviewyear;
                        }
                    }
                }
                $this->sengine->insuranceReviewYear32 = $year;
                $this->sengine->hasHomeInsurance = $hasHomeInsurance;
                parent::saveEngine();
                break;

            case "VEHI":
                $hasVehicleInsurance = false;
                $granular = Insurance::model()->findAllBySql("select reviewyear from insurance where type in ('VEHI') and user_id=:user_id and status=0", array("user_id" => $user_id));
                parent::setEngine();
                $year = date('Y');
                if ($granular) {
                    $hasVehicleInsurance = true;
                    foreach ($granular as $insurance) {
                        if ($insurance && isset($insurance->reviewyear) && $insurance->reviewyear != 'Year' && $insurance->reviewyear < $year) {
                            $year = $insurance->reviewyear;
                        }
                    }
                }
                $this->sengine->insuranceReviewYear33 = $year;
                $this->sengine->hasVehicleInsurance = $hasVehicleInsurance;
                parent::saveEngine();
                break;

            case "UMBR":
                $hasUmbrellaInsurance = false;
                $granular = Insurance::model()->count("type in ('UMBR') and user_id=:user_id and status=0", array("user_id" => $user_id));
                parent::setEngine();
                if ($granular > 0) {
                    $hasUmbrellaInsurance = true;
                }
                $this->sengine->hasUmbrellaInsurance = $hasUmbrellaInsurance;
                parent::saveEngine();
                break;

            case "HEAL":
                $healthInsuranceType = '';
                $granular = Insurance::model()->findAllBySql("select reviewyear, insurancefor from insurance where type in ('HEAL') and user_id=:user_id and status=0", array("user_id" => $user_id));
                parent::setEngine();
                $year = date('Y');
                if ($granular) {
                    $healthInsuranceType = 'Limited';
                    foreach ($granular as $insurance) {
                        if ($insurance && isset($insurance->reviewyear) && $insurance->reviewyear != 'Year' && $insurance->reviewyear < $year) {
                            $year = $insurance->reviewyear;
                        }
                        if ($insurance && isset($insurance->insurancefor) && $insurance->insurancefor == '88') {
                            $healthInsuranceType = 'Comprehensive';
                        }
                    }
                }
                $this->sengine->insuranceReviewYear24 = $year;
                $this->sengine->healthInsuranceType = $healthInsuranceType;
                parent::saveEngine();
                break;

            case "OTHE":

                break;
        }
        //GENERAL RECALCUALTION  -- Needs to be reviewed with Melroy 04102014

        $assetCount = Assets::model()->findBySql("SELECT count(distinct(type)) as count_assets FROM assets WHERE status=0 and user_id=:user_id", array("user_id" => $user_id));
        $acount = ($assetCount) ? $assetCount->count_assets : 0;
        $loanCount = Debts::model()->findBySql("SELECT count(distinct(mortgagetype)) as count_debts FROM debts WHERE status=0 and user_id=:user_id and type in ('LOAN')", array("user_id" => $user_id));
        $lcount = ($loanCount) ? $loanCount->count_debts : 0;
        $debtCount = Debts::model()->findBySql("SELECT count(distinct(type)) as count_debts FROM debts WHERE status=0 and user_id=:user_id and type not in ('LOAN')", array("user_id" => $user_id));
        $dcount = ($debtCount) ? $debtCount->count_debts : 0;
        $insuranceCount = Insurance::model()->findBySql("SELECT count(distinct(type)) as count_insurance FROM insurance WHERE status=0 and user_id=:user_id", array("user_id" => $user_id));
        $icount = ($insuranceCount) ? $insuranceCount->count_insurance : 0;

        $value = ($acount + $icount + $lcount + $dcount) / (11 + 7 + 6);
        $value = ($value > 0) ? $value : 0;
        $value = ($value < 1) ? $value : 1;
        parent::setEngine();
        $this->sengine->userProfilePoints_others = round(4 * $value);
        if ($icount > 0) {
            $this->sengine->userProfilePoints_insurance = 1;
        } else {
            $this->sengine->userProfilePoints_insurance = 0;
        }

        parent::saveEngine();

        # POINT 30 -------------------------
        parent::calculateScore("INSURANCE", $user_id);
    }

    function actionReprioritizeInsurance() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        if (!isset($_POST["insurance"])) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'insurance' was not set.")));
        }
        $insuranceArray = $_POST["insurance"];

        $insuranceIds = array();
        $insuranceHash = array();
        foreach ($insuranceArray as $insuranceValue) {
            $values = explode("|", $insuranceValue);
            $insuranceIds[] = $values[0];
            $insuranceHash[$values[0]] = $values[1];
        }
        $insuranceObj = new Insurance();
        $criteria = new CDbCriteria();
        $criteria->condition = "user_id = :user_id AND status <> 1";
        $criteria->select = 'id,priority';
        $criteria->params = array('user_id' => $user_id);
        $criteria->addInCondition("id", $insuranceIds);
        $insurances = $insuranceObj->findAll($criteria);

        if(isset($insurances) && !empty($insurances)) {
            foreach ($insurances as $insurance) {
                if (array_key_exists($insurance->id, $insuranceHash)) {
                    $insurance->priority = $insuranceHash[$insurance->id];
                    $insurance->save();
                }
            }

            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "Insurance have been reprioritized successfully.")));
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "The user does not have permission to reprioritize these insurance.")));
    }

}

?>
