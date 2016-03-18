<?php

/* * **********************************************************************************
 * Filename: AssetController.php
 * Folder: controllers
 * Description: Getting input from the HTML
 * @author Thayub J(For TruGlobal Inc)
 * @copyright (c) 2013 - 2015
 * Change History:
 * Version         Author               Change Description
 * All the changed were made to optimize the controller - Ganesh Manoharan 10/29/2013
 * *********************************************************************************** */

class AssetController extends Scontroller {

    public $iraAmount = 5000;

    // Define access control
    public function accessRules() {
        return array_merge(
                array(array('allow', 'users' => array('?'))),
                // Include parent access rules
                parent::accessRules()
        );
    }

    function actionAssetcrud() {
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
                $this->createupdateAsset();
                $actionSet = true;
                break;
            CASE "READ":
                $this->Readasset();
                $actionSet = true;
                break;
            CASE "UPDATE":
                $this->createupdateAsset();
                $actionSet = true;
                break;
            CASE "DELETE":
                $this->Deleteasset();
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

    function createupdateAsset() {
        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $action = $_POST["action"];
        $id = isset($_POST["id"]) ? $_POST["id"] : 0;

        if (!isset($_POST["accttype"]) && $action == 'ADD') {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'accttype' was not set.")));
        }

        //Initializing Objects
        $assetObj = new Assets();
        $utilityObj = new Utility();

        $needsUpdate = false;
        $asset = new Assets();
        if ($action == 'UPDATE' && $id > 0) {
            $asset = $assetObj->findByPk($id);
            if (!$asset) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'id' was not set to a valid value.")));
            } else if ($asset->user_id != $user_id) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "This user does not have permissions to update this asset.")));
            }
        } else if ($action == 'ADD') {
            $ctype = strtoupper($_POST["accttype"]);
            if ($ctype != 'BANK' && $ctype != 'BROK' && $ctype != 'EDUC' && $ctype != 'IRA' &&
                    $ctype != 'CR' && $ctype != 'BUSI' && $ctype != 'PROP' && $ctype != 'VEHI' &&
                    $ctype != 'SS' && $ctype != 'PENS' && $ctype != 'OTHE') {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'accttype' should be set to either BANK, BROK, EDUC, IRA, CR, BUSI, PROP, VEHI, SS, PENS, or OTHE.")));
            }
            $totalassets = Assets::model()->count("user_id=:user_id", array("user_id" => $user_id));
            $asset->priority = $totalassets + 1;
            $asset->context = 'MANUAL';
            $asset->refid = '';
            $asset->status = 0;
            $asset->type = $ctype;
            $asset->user_id = $user_id;
            $asset->modifiedtimestamp = date("Y-m-d H:i:s");
            $needsUpdate = true;
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "We could not add/update the asset at this time.")));
        }

        if (isset($_POST["name"])) {
            $name = $_POST["name"];
            $asset->name = $name;
            $needsUpdate = true;
        }

        if (isset($_POST["growthrate"])) {
            $growthrate = $utilityObj->amountToDB($_POST["growthrate"]);
            $asset->growthrate = $growthrate;
            $needsUpdate = true;
        }

        if (isset($_POST["amount"])) {
            $balance = $utilityObj->amountToDB($_POST["amount"]);
            $asset->balance = $balance;
            $needsUpdate = true;
        }

        if (isset($_POST["withdrawal"])) {
            $withdrawal = $utilityObj->amountToDB($_POST["withdrawal"]);
            $asset->withdrawal = $withdrawal;
            $needsUpdate = true;
        }

        if (isset($_POST["contribution"])) {
            $contribution = $utilityObj->amountToDB($_POST["contribution"]);
            $asset->contribution = $contribution;
            $needsUpdate = true;
        }

        if (isset($_POST["empcontribution"])) {
            $empcontribution = $utilityObj->amountToDB($_POST["empcontribution"]);
            $asset->empcontribution = $empcontribution;
            $needsUpdate = true;
        }

        if (isset($_POST["netincome"])) {
            $netincome = $utilityObj->amountToDB($_POST["netincome"]);
            $asset->netincome = $netincome;
            $needsUpdate = true;
        }

        if (isset($_POST["assettype"])) {
            $assettype = $_POST["assettype"];
            $asset->assettype = $assettype;
            $needsUpdate = true;
        }

        if (isset($_POST["beneficiary"])) {
            $beneficiary = $_POST["beneficiary"];
            $asset->beneficiary = $beneficiary;
            $needsUpdate = true;
        }

        if (isset($_POST["loan"])) {
            $loan = $_POST["loan"];
            $asset->loan = $loan;
            $needsUpdate = true;
        }

        if (isset($_POST["livehere"])) {
            $livehere = $_POST["livehere"];
            $asset->livehere = $livehere;
            $needsUpdate = true;
        }

        if (isset($_POST["zipcode"])) {
            $zipcode = $_POST["zipcode"];
            $asset->zipcode = $zipcode;
            $needsUpdate = true;
        }

        if (isset($_POST["agepayout"])) {
            $agepayout = $_POST["agepayout"];
            $asset->agepayout = $agepayout;
            $needsUpdate = true;
        }

        if (isset($_POST["propadd"]) || isset($_POST["propadd2"]) || isset($_POST["propcity"]) || isset($_POST["propstate"])) {
            $propadd = (isset($_POST["propadd"])) ? $_POST["propadd"] : "";
            $propadd2 = (isset($_POST["propadd2"])) ? $_POST["propadd2"] : "";
            $propcity = (isset($_POST["propcity"])) ? $_POST["propcity"] : "";
            $propstate = (isset($_POST["propstate"])) ? $_POST["propstate"] : "";
            $asset->address = $propadd . '+' . $propadd2 . '+' . $propcity . '+' . $propstate;
            $needsUpdate = true;
        }

        if (isset($_POST["invpos"])) {
            $invpos = $_POST["invpos"];
            $invPostionArr = array();

            if ($invpos != "" && preg_match('/[0-9]/', $invpos)) {
                $inPosArr = explode(',', $invpos);
                for ($i = 0; $i < count($inPosArr) - 1; $i+=2) {
                    if ($inPosArr[$i] != "") {
                        $eachTicker = array(
                            'ticker' => $inPosArr[$i],
                            'amount' => $inPosArr[$i + 1]
                        );
                        $invPostionArr[] = $eachTicker;
                    }
                }
            }
            $asset->invpos = json_encode($invPostionArr);
            $needsUpdate = true;
        }

        $tok = strtok($asset->address, '+');
        $propadd = '';
        $propadd2 = '';
        $propcity = '';
        $propstate = '';
        if ($tok != false) {
            $propadd = $tok;
            $propadd2 = strtok('+');
            $propcity = strtok('+');
            $propstate = strtok('+');
        }
        $assetObj = array(
            'id' => $asset->id,
            'user_id' => $asset->user_id,
            'context' => $asset->context,
            'type' => $asset->type,
            'subtype' => $asset->subtype,
            'name' => $asset->name,
            'amount' => $asset->balance,
            'actid' => $asset->actid,
            'accttype' => $asset->type,
            'refId' => $asset->refid,
            'beneficiary' => $asset->beneficiary,
            'assettype' => $asset->assettype,
            'contribution' => $asset->contribution,
            'growthrate' => $asset->growthrate,
            'empcontribution' => $asset->empcontribution,
            'withdrawal' => $asset->withdrawal,
            'netincome' => $asset->netincome,
            'loan' => $asset->loan,
            'propadd' => $propadd,
            'propadd2' => $propadd2,
            'propcity' => $propcity,
            'propstate' => $propstate,
            'livehere' => $asset->livehere,
            'zipcode' => $asset->zipcode,
            'agepayout' => $asset->agepayout,
            'status' => $asset->status,
            'ticker' => $asset->ticker,
            'invpos' => json_decode($asset->invpos),
            'priority' => $asset->priority,
            'lstype' => $asset->lstype
        );

        if ($needsUpdate) {
            if ($asset->save()) {
                $assetObj["id"] = $asset->id;
                $this->actionreCalculateScoreAssets($asset, "ADD", $user_id, 1); // Enabled for single call
                $this->sendResponse(200, CJSON::encode(array("status" => 'OK', 'totalscore' => $this->totalScore, "message" => 'Asset has been updated successfully.', "asset" => $assetObj)));
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Asset has not been updated.", "asset" => $assetObj)));
            }
        }
        //Destroying Object
        unset($assetObj);
        unset($utilityObj);
        parent::setEngine();
        $totalScore = $sengineObj->updateScore();
        $this->sendResponse(200, CJSON::encode(array("status" => 'OK', 'totalscore' => $totalScore, "message" => 'Asset has been updated successfully.', "asset" => $assetObj)));
    }

    public function actionGetAssets() {
        $retAge = 0;
        //if there is no session
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $singleORMAssetObj = Assets::model()->findAll("user_id=:user_id and status!=1", array("user_id" => $user_id));
        $lsAccAssets = array();

        foreach ($singleORMAssetObj as $acc) {
            $tok = strtok($acc->address, '+');
            $propadd = '';
            $propadd2 = '';
            $propcity = '';
            $propstate = '';
            if ($tok != false) {
                $propadd = $tok;
                $propadd2 = strtok('+');
                $propcity = strtok('+');
                $propstate = strtok('+');
            }
            $lsEachAcc = array(
                'id' => $acc->id,
                'user_id' => $acc->user_id,
                'context' => $acc->context,
                'type' => $acc->type,
                'subtype' => $acc->subtype,
                'name' => $acc->name,
                'amount' => $acc->balance,
                'actid' => $acc->actid,
                'accttype' => $acc->type,
                'refId' => $acc->refid,
                'beneficiary' => $acc->beneficiary,
                'assettype' => $acc->assettype,
                'contribution' => $acc->contribution,
                'growthrate' => $acc->growthrate,
                'empcontribution' => $acc->empcontribution,
                'withdrawal' => $acc->withdrawal,
                'netincome' => $acc->netincome,
                'loan' => $acc->loan,
                'propadd' => $propadd,
                'propadd2' => $propadd2,
                'propcity' => $propcity,
                'propstate' => $propstate,
                'livehere' => $acc->livehere,
                'zipcode' => $acc->zipcode,
                'agepayout' => $acc->agepayout,
                'status' => $acc->status,
                'ticker' => $acc->ticker,
                'invpos' => json_decode($acc->invpos),
                'priority' => $acc->priority,
                'lstype' => $acc->lstype
            );
            $lsAccAssets[] = $lsEachAcc;
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "assets" => $lsAccAssets)));
    }

    function readAsset() {
        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $id = isset($_POST["id"]) ? $_POST["id"] : 0;
        if ($id != 0) {
            $asset = Assets::model()->findByPk($id);
            $tok = strtok($asset->address, '+');
            $propadd = '';
            $propadd2 = '';
            $propcity = '';
            $propstate = '';
            if ($tok != false) {
                $propadd = $tok;
                $propadd2 = strtok('+');
                $propcity = strtok('+');
                $propstate = strtok('+');
            }
            $assetObj = array(
                'id' => $asset->id,
                'user_id' => $asset->user_id,
                'context' => $asset->context,
                'type' => $asset->type,
                'subtype' => $assets->subtype,
                'name' => $asset->name,
                'amount' => $asset->balance,
                'actid' => $asset->actid,
                'accttype' => $asset->type,
                'refId' => $asset->refid,
                'beneficiary' => $asset->beneficiary,
                'assettype' => $asset->assettype,
                'contribution' => $asset->contribution,
                'growthrate' => $asset->growthrate,
                'empcontribution' => $asset->empcontribution,
                'withdrawal' => $asset->withdrawal,
                'netincome' => $asset->netincome,
                'loan' => $asset->loan,
                'propadd' => $propadd,
                'propadd2' => $propadd2,
                'propcity' => $propcity,
                'propstate' => $propstate,
                'livehere' => $asset->livehere,
                'zipcode' => $asset->zipcode,
                'agepayout' => $asset->agepayout,
                'status' => $asset->status,
                'ticker' => $asset->ticker,
                'invpos' => json_decode($asset->invpos),
                'lstype' => $asset->lstype
            );
            if ($asset && $user_id == $asset->user_id) {
                $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => "Asset successfully read.", 'asset' => $assetObj)));
            }
        }
        $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => "Unable to read asset.")));
    }

    /**
     *
     */
    function deleteAsset() {

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $id = isset($_POST["id"]) ? $_POST["id"] : 0;
        if ($id != 0) {
            $assets = Assets::model()->findByPk($id);
            if ($assets && $user_id == $assets->user_id) {
                if ($assets->status != 1) {
                    $type = $assets->context;
                    $assets->status = 1;
                    $assets->save();
                    $this->actionreCalculateScoreAssets($assets, "DELETE", $user_id, 1); // Enabled for single call
                    // Delete from CE
                    if ($type == 'AUTO') {
                        $obj = new CashedgeController(1); // preparing object
                        $obj->actionDeletefiAcctid($assets->actid, $assets->user_id, 'ASSE'); // For single call
                    }
                    $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'totalscore' => $this->totalScore, 'message' => "Asset has been deleted.")));
                } else {
                    parent::setEngine();
                    $totalScore = $sengineObj->updateScore();
                    $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'totalscore' => $totalScore, 'message' => "Asset has been deleted.")));
                }
            }
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "This user does not have permissions to delete this asset.")));
    }

    function ToggleStatus() {

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $action = $_POST["action"];
        $id = isset($_POST["id"]) ? $_POST["id"] : 0;
        if ($id != 0) {
            $asset = Assets::model()->findByPk($id);
            if ($asset && $asset->user_id == $user_id) {
                if ($asset->status != 1) {
                    $needsUpdate = false;
                    if ($asset->status == 0 && $action == 'HIDE') {
                        $asset->status = 2;
                        $needsUpdate = true;
                    } else if ($asset->status == 2 && $action == 'UNHIDE') {
                        $asset->status = 0;
                        $needsUpdate = true;
                    }
                    if ($needsUpdate) {
                        if ($asset->save()) {
                            $this->actionreCalculateScoreAssets($asset, "DELETE", $user_id);
                            $this->sendResponse(200, CJSON::encode(array("status" => 'OK', "totalscore" => $this->totalScore, "message" => 'Asset status has been updated.')));
                        } else {
                            $this->sendResponse(200, CJSON::encode(array("status" => 'ERROR', "message" => 'Asset status was not updated.')));
                        }
                    } else {
                        parent::setEngine();
                        $totalScore = $sengineObj->updateScore();
                        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'totalscore' => $totalScore, 'message' => "Asset status has been updated.")));
                    }
                } else {
                    $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => "Asset has already been deleted.")));
                }
            }
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "This user does not have permissions to update this asset.")));
    }

    /**
     * Used to recalculate score
     *
     */
    function actionreCalculateScoreAssets($ceAssetObj = NULL, $ceAction = NULL, $ceUid = NULL, $ceFlag = NULL) {
        $asset = isset($_GET["asset"]) ? (object) $_GET["asset"] : $ceAssetObj;
        //$action = isset($_GET["action"]) ? $_GET["action"] : $ceAction;
        $user_id = isset($_GET["uid"]) ? $_GET["uid"] : isset($ceUid) ? $ceUid : 0;

        if ($user_id == 0 && $ceFlag != 1) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Insufficent Data')));
            // Error Logging needs to be done here.
        }

        $userPerDetails = Userpersonalinfo::model()->find("user_id=:user_id", array("user_id" => $user_id));
        parent::setEngine($user_id);
        $retirementAmount = $this->sengine->retirementAmountDesired;
        parent::saveEngine();

        //Object Initilization
        $assetObj = new Assets();
        $debtObj = new Debts();
        $insuranceObj = new Insurance();
        $pertrackObj = new Pertrack();
        $utilityObj = new Utility();

        # POINT 1:
        $userDebtAcc = $debtObj->count("user_id=:user_id and status=0 and context='AUTO'", array("user_id" => $user_id));
        $userAssetAcc = $assetObj->count("user_id=:user_id and status=0 and context='AUTO'", array("user_id" => $user_id));
        $userInsuranceAcc = $insuranceObj->count("user_id=:user_id and status=0 and context='AUTO'", array("user_id" => $user_id));
        parent::setEngine();
        if ($userDebtAcc > 0 || $userAssetAcc > 0 || $userInsuranceAcc > 0 || $userPerDetails->connectAccountPreference == '1') {
            $this->sengine->isUserDownloadAccount = true;
            $this->sengine->userProfilePoints_connectAccount = 1;
        } else {
            $this->sengine->isUserDownloadAccount = false;
            $this->sengine->userProfilePoints_connectAccount = 0;
        }
        parent::saveEngine();
        unset($userAssetAcc, $userDebtAcc, $userInsuranceAcc);

        $userDebtAcc = $debtObj->count("user_id=:user_id and status=0", array("user_id" => $user_id));
        $userAssetAcc = $assetObj->count("user_id=:user_id and status=0", array("user_id" => $user_id));
        $userInsuranceAcc = $insuranceObj->count("user_id=:user_id and status=0", array("user_id" => $user_id));
        parent::setEngine();
        if ($userDebtAcc > 0 || $userAssetAcc > 0 || $userInsuranceAcc > 0) {
            $this->sengine->isUserEnteredAccount = true;
        } else {
            $this->sengine->isUserEnteredAccount = false;
        }
        parent::saveEngine();
        unset($userAssetAcc, $userDebtAcc, $userInsuranceAcc);

        switch ($asset->type) {
            case "BANK":
                $totalAssets = $assetObj->findBySql("select sum(balance) as total from assets where type not in ('SS','PENS','EDUC','VEHI') and status=0 and user_id=:user_id and livehere <> 1", array("user_id" => $user_id));
                $goalSettingAssets = $assetObj->findBySql("select sum(balance) as total from assets where type in ('BANK','BROK') and status=0 and user_id=:user_id", array("user_id" => $user_id));
                $totalCashValue = $insuranceObj->findBySql("select sum(cashvalue) as total_cashvalue from insurance where type in ('LIFE') and status = 0 and user_id=:user_id and lifeinstype <> 64", array("user_id" => $user_id));
                $diffAssets = $assetObj->findBySql("select sum(balance) as diff from assets where type in ('BUSI','PROP','OTHE') and status=0 and user_id=:user_id and livehere <> 1", array("user_id" => $user_id));

                $assetData = parent::getAssetData($user_id);
                $assets = $assetData[0];
                $insurance = $assetData[1];
                $pertrack = $assetData[2];
                $tickers = $assetData[3];

                parent::setEngine();
                parent::CalculatePoint10($assets, $insurance, $pertrack);
                parent::saveEngine();

                parent::setEngine();
                // Total Assets (Minus EDUC, PENS, SS, VEHI) for Point 10, 11, 22
                if ($totalAssets) {
                    $this->sengine->userSumOfAssets = $totalAssets->total;
                } else {
                    $this->sengine->userSumOfAssets = 0;
                }
                // Point #3
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
                // Point #14 and #3
                if ($totalCashValue) {
                    $this->sengine->userSumOfAssets += $totalCashValue->total_cashvalue;
                    $this->sengine->userSumOfGoalSettingAssets += $totalCashValue->total_cashvalue;
                }
                if ($diffAssets) {
                    $this->sengine->numeratorP14 = $this->sengine->userSumOfAssets - $diffAssets->diff;
                } else {
                    $this->sengine->numeratorP14 = $this->sengine->userSumOfAssets;
                }
                parent::saveEngine();
                unset($totalAssets, $goalSettingAssets, $totalCashValue, $diffAssets);

                # Point 16, 26
                $contributionCount = $assetObj->count("type in ('BANK','BROK','EDUC','IRA','CR') and contribution > 0 and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($contributionCount > 0) {
                    $this->sengine->investmentFactor = 1;
                    $this->sengine->point26cond = 1;
                } else {

                    $this->sengine->investmentFactor = 0;
                    $this->sengine->point26cond = 0;
                }
                parent::saveEngine();
                unset($contributionCount);

                $contribution = $assetObj->findBySql("select sum(contribution) as total from assets where type in ('BANK','BROK') and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($contribution && $contribution->total > 0) {
                    $this->sengine->taxableAnnualSavings = 12 * $contribution->total;
                } else {
                    $this->sengine->taxableAnnualSavings = 0;
                }
                parent::saveEngine();
                unset($contribution);

                # Point 20
                $withdrawal = $assetObj->findBySql("select sum(withdrawal) as total from assets where type in ('BANK','BROK','EDUC','IRA','CR') and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($withdrawal && $withdrawal->total > 0) {
                    $this->sengine->permonthWithdrawal = $withdrawal->total;
                } else {

                    $this->sengine->permonthWithdrawal = 0;
                }
                parent::saveEngine();
                unset($withdrawal);

                $allAssets = $assetObj->findAllBySql("select balance,invpos from assets where type in ('BANK','BROK','CR','IRA') and user_id=:user_id and status=0", array("user_id" => $user_id));
                $tickerRiskVal = 0;
                $extraCashValue = 0;
                parent::setEngine();
                $allTickAmount = array();
                if ($allAssets) {
                    // Calculating the Total Ticker Value
                    $this->calculateAndUpdateTickerValue($allAssets, $utilityObj, $tickers);
                } else {
                    $this->sengine->tickerRiskValue = 0;
                }
                unset($tickers);
                $this->sengine->extraCashValue = $extraCashValue;
                parent::saveEngine();
                unset($allAssets, $allTickAmount);

                break;

            case "IRA":
                $totalAssets = $assetObj->findBySql("select sum(balance) as total from assets where type not in ('SS','PENS','EDUC','VEHI') and status=0 and user_id=:user_id and livehere <> 1", array("user_id" => $user_id));
                $totalCashValue = $insuranceObj->findBySql("select sum(cashvalue) as total_cashvalue from insurance where type in ('LIFE') and status = 0 and user_id=:user_id and lifeinstype <> 64", array("user_id" => $user_id));
                $diffAssets = $assetObj->findBySql("select sum(balance) as diff from assets where type in ('BUSI','PROP','OTHE') and status=0 and user_id=:user_id and livehere <> 1", array("user_id" => $user_id));

                $assetData = parent::getAssetData($user_id);
                $assets = $assetData[0];
                $insurance = $assetData[1];
                $pertrack = $assetData[2];
                $tickers = $assetData[3];

                parent::setEngine();
                parent::CalculatePoint10($assets, $insurance, $pertrack);
                parent::saveEngine();

                parent::setEngine();
                // Total Assets (Minus EDUC, PENS, SS, VEHI) for Point 11, 22
                if ($totalAssets) {
                    $this->sengine->userSumOfAssets = $totalAssets->total;
                } else {
                    $this->sengine->userSumOfAssets = 0;
                }
                // Point # 14
                if ($totalCashValue) {
                    $this->sengine->userSumOfAssets += $totalCashValue->total_cashvalue;
                }
                if ($diffAssets) {
                    $this->sengine->numeratorP14 = $this->sengine->userSumOfAssets - $diffAssets->diff;
                } else {
                    $this->sengine->numeratorP14 = $this->sengine->userSumOfAssets;
                }
                parent::saveEngine();
                unset($totalAssets, $totalCashValue, $diffAssets);

                $beneficiary = $assetObj->count("type in ('EDUC','IRA','CR','PENS') and beneficiary=1 and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                //POINT 15:
                if ($beneficiary > 0) {
                    $this->sengine->beneAssigned = true;
                } else {
                    $this->sengine->beneAssigned = false;
                }
                parent::saveEngine();
                unset($beneficiary);

                # Point 16, 26
                $contributionCount = $assetObj->count("type in ('BANK','BROK','EDUC','IRA','CR') and contribution > 0 and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($contributionCount > 0) {
                    $this->sengine->investmentFactor = 1;
                    $this->sengine->point26cond = 1;
                } else {

                    $this->sengine->investmentFactor = 0;
                    $this->sengine->point26cond = 0;
                }
                parent::saveEngine();
                unset($contributionCount);

                #Point 17
                $retirementCount = $assetObj->count("type in ('IRA','CR') and contribution > 0 and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($retirementCount > 0) {
                    $this->sengine->retirementMonthlyContribution = true;
                } else {
                    $this->sengine->retirementMonthlyContribution = false;
                }
                parent::saveEngine();
                unset($retirementCount);

                # Point 20
                $withdrawal = $assetObj->findBySql("select sum(withdrawal) as total from assets where type in ('BANK','BROK','EDUC','IRA','CR') and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($withdrawal && $withdrawal->total > 0) {
                    $this->sengine->permonthWithdrawal = $withdrawal->total;
                } else {

                    $this->sengine->permonthWithdrawal = 0;
                }
                parent::saveEngine();
                unset($withdrawal);

                // type not equal to 51 => Regular => tax deferred
                $contribution = $assetObj->findBySql("select sum(balance) as total, sum(empcontribution) as retSum, sum(contribution) as retireContriSum from assets where type in ('CR','IRA') and status=0 and user_id=:user_id and assettype <> 51", array("user_id" => $user_id));
                parent::setEngine();
                if ($contribution) {
                    $empsavings = ($contribution->retSum / 100) * $this->sengine->grossIncome;
                    $savings = $contribution->retireContriSum;
                    $balance = $contribution->total;
                    $this->sengine->taxDeferredAnnualSavings = 12 * ($savings + $empsavings);
                    $this->sengine->startingTaxDeferredBalance = $balance;
                } else {
                    $this->sengine->taxDeferredAnnualSavings = 0;
                    $this->sengine->startingTaxDeferredBalance = 0;
                }
                parent::saveEngine();
                unset($contribution);

                // type equal to 51 => ROTH => tax free
                $contribution = $assetObj->findBySql("select sum(balance) as total, sum(empcontribution) as retSum, sum(contribution) as retireContriSum from assets where type in ('CR','IRA') and assettype=51 and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($contribution) {
                    $empsavings = ($contribution->retSum / 100) * $this->sengine->grossIncome;
                    $savings = $contribution->retireContriSum;
                    $balance = $contribution->total;
                    $this->sengine->taxFreeAnnualSavings = 12 * ($savings + $empsavings);
                    $this->sengine->startingTaxFreeBalance = $balance;
                } else {
                    $this->sengine->taxFreeAnnualSavings = 0;
                    $this->sengine->startingTaxFreeBalance = 0;
                }
                parent::saveEngine();
                unset($contribution);

                //point 23, 25
                $stocksMFETFs = false;
                $nonCoreelatedTicker = false;
                if ($tickers && $tickers != "") {
                    // Point 23
                    $pertrackRows = $pertrackObj->findBySql("SELECT GROUP_CONCAT(distinct(itemtype)) as itemType, GROUP_CONCAT(distinct(ticker)) as tickers FROM `pertrac` WHERE ticker in ({$tickers}) and status=1");
                    if ($pertrackRows) {
                        $allInvs = explode(',', $pertrackRows->itemType);
                        $tickersFound = explode(',', $pertrackRows->tickers);
                        $tickerValues = explode(',', $tickers);
                        if (in_array("STOCK", $allInvs) && in_array("MF", $allInvs) && in_array("ETF", $allInvs)) {
                            $stocksMFETFs = true;
                        } else if (in_array("MF", $allInvs) && in_array("ETF", $allInvs) && count($tickerValues) > count($tickersFound)) {
                            // Any tickers not found in pertrac DB are assumed to be Stocks
                            $stocksMFETFs = true;
                        }
                    }
                    unset($pertrackRows);

                    // Point 25
                    $pertrackC = $pertrackObj->count("category = 'NONCOR' and ticker in ({$tickers}) and status=1");
                    if ($pertrackC > 0) {
                        $nonCoreelatedTicker = true;
                    }
                    unset($pertrackC);
                }
                parent::setEngine();
                $this->sengine->stocksMFETFs = $stocksMFETFs;
                $this->sengine->nonCoreelatedTicker = $nonCoreelatedTicker;
                parent::saveEngine();

                $allAssets = $assetObj->findAllBySql("select balance,invpos from assets where type in ('BANK','BROK','CR','IRA') and user_id=:user_id and status=0", array("user_id" => $user_id));
                $tickerRiskVal = 0;
                $extraCashValue = 0;
                parent::setEngine();
                $allTickAmount = array();
                if ($allAssets) {
                    // Calculating the Total Ticker Value
                    $this->calculateAndUpdateTickerValue($allAssets, $utilityObj, $tickers);
                } else {
                    $this->sengine->tickerRiskValue = 0;
                }
                unset($tickers);
                $this->sengine->extraCashValue = $extraCashValue;
                parent::saveEngine();
                unset($allAssets, $allTickAmount);
                break;

            case "CR":
                $totalAssets = $assetObj->findBySql("select sum(balance) as total from assets where type not in ('SS','PENS','EDUC','VEHI') and status=0 and user_id=:user_id and livehere <> 1", array("user_id" => $user_id));
                $totalCashValue = $insuranceObj->findBySql("select sum(cashvalue) as total_cashvalue from insurance where type in ('LIFE') and status = 0 and user_id=:user_id and lifeinstype <> 64", array("user_id" => $user_id));
                $diffAssets = $assetObj->findBySql("select sum(balance) as diff from assets where type in ('BUSI','PROP','OTHE') and status=0 and user_id=:user_id and livehere <> 1", array("user_id" => $user_id));

                $assetData = parent::getAssetData($user_id);
                $assets = $assetData[0];
                $insurance = $assetData[1];
                $pertrack = $assetData[2];
                $tickers = $assetData[3];

                parent::setEngine();
                parent::CalculatePoint10($assets, $insurance, $pertrack);
                parent::saveEngine();

                parent::setEngine();
                // Total Assets (Minus EDUC, PENS, SS, VEHI) for Point 11, 22
                if ($totalAssets) {
                    $this->sengine->userSumOfAssets = $totalAssets->total;
                } else {
                    $this->sengine->userSumOfAssets = 0;
                }
                // Point # 14
                if ($totalCashValue) {
                    $this->sengine->userSumOfAssets += $totalCashValue->total_cashvalue;
                }
                if ($diffAssets) {
                    $this->sengine->numeratorP14 = $this->sengine->userSumOfAssets - $diffAssets->diff;
                } else {
                    $this->sengine->numeratorP14 = $this->sengine->userSumOfAssets;
                }
                parent::saveEngine();
                unset($totalAssets, $totalCashValue, $diffAssets);

                $beneficiary = $assetObj->count("type in ('EDUC','IRA','CR','PENS') and beneficiary=1 and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                //POINT 15:
                if ($beneficiary > 0) {
                    $this->sengine->beneAssigned = true;
                } else {
                    $this->sengine->beneAssigned = false;
                }
                parent::saveEngine();
                unset($beneficiary);

                # Point 16, 26
                $contributionCount = $assetObj->count("type in ('BANK','BROK','EDUC','IRA','CR') and contribution > 0 and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($contributionCount > 0) {
                    $this->sengine->investmentFactor = 1;
                    $this->sengine->point26cond = 1;
                } else {

                    $this->sengine->investmentFactor = 0;
                    $this->sengine->point26cond = 0;
                }
                parent::saveEngine();
                unset($contributionCount);

                #Point 17
                $retirementCount = $assetObj->count("type in ('IRA','CR') and contribution > 0 and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($retirementCount > 0) {
                    $this->sengine->retirementMonthlyContribution = true;
                } else {
                    $this->sengine->retirementMonthlyContribution = false;
                }
                parent::saveEngine();
                unset($retirementCount);

                # Point 19
                $empcontributionCount = $assetObj->count("type=:type and empcontribution > 0 and status=0 and user_id=:user_id", array("type" => $asset->type, "user_id" => $user_id));
                parent::setEngine();
                if ($empcontributionCount > 0) {
                    $this->sengine->userRetirementAmountContributionByEmployer = 1;
                } else {

                    $this->sengine->userRetirementAmountContributionByEmployer = 0;
                }
                parent::saveEngine();
                unset($empcontributionCount);

                # Point 20
                $withdrawal = $assetObj->findBySql("select sum(withdrawal) as total from assets where type in ('BANK','BROK','EDUC','IRA','CR') and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($withdrawal && $withdrawal->total > 0) {
                    $this->sengine->permonthWithdrawal = $withdrawal->total;
                } else {

                    $this->sengine->permonthWithdrawal = 0;
                }
                parent::saveEngine();
                unset($withdrawal);

                // type not equal to 51 => Regular
                $contribution = $assetObj->findBySql("select sum(balance) as total, sum(empcontribution) as retSum, sum(contribution) as retireContriSum from assets where type in ('CR','IRA') and status=0 and user_id=:user_id and assettype <> 51", array("user_id" => $user_id));
                parent::setEngine();
                if ($contribution) {
                    $empsavings = ($contribution->retSum / 100) * $this->sengine->grossIncome;
                    $savings = $contribution->retireContriSum;
                    $balance = $contribution->total;
                    $this->sengine->taxDeferredAnnualSavings = 12 * ($savings + $empsavings);
                    $this->sengine->startingTaxDeferredBalance = $balance;
                } else {
                    $this->sengine->taxDeferredAnnualSavings = 0;
                    $this->sengine->startingTaxDeferredBalance = 0;
                }
                parent::saveEngine();
                unset($contribution);

                // type equal to 51 => ROTH
                $contribution = $assetObj->findBySql("select sum(balance) as total, sum(empcontribution) as retSum, sum(contribution) as retireContriSum from assets where type in ('CR','IRA') and assettype=51 and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($contribution) {
                    $empsavings = ($contribution->retSum / 100) * $this->sengine->grossIncome;
                    $savings = $contribution->retireContriSum;
                    $balance = $contribution->total;
                    $this->sengine->taxFreeAnnualSavings = 12 * ($savings + $empsavings);
                    $this->sengine->startingTaxFreeBalance = $balance;
                } else {
                    $this->sengine->taxFreeAnnualSavings = 0;
                    $this->sengine->startingTaxFreeBalance = $balance;
                }
                parent::saveEngine();
                unset($contribution);

                //point 23, 25
                $stocksMFETFs = false;
                $nonCoreelatedTicker = false;
                if ($tickers && $tickers != "") {
                    // Point 23
                    $pertrackRows = $pertrackObj->findBySql("SELECT GROUP_CONCAT(distinct(itemtype)) as itemType, GROUP_CONCAT(distinct(ticker)) as tickers FROM `pertrac` WHERE ticker in ({$tickers}) and status=1");
                    if ($pertrackRows) {
                        $allInvs = explode(',', $pertrackRows->itemType);
                        $tickersFound = explode(',', $pertrackRows->tickers);
                        $tickerValues = explode(',', $tickers);
                        if (in_array("STOCK", $allInvs) && in_array("MF", $allInvs) && in_array("ETF", $allInvs)) {
                            $stocksMFETFs = true;
                        } else if (in_array("MF", $allInvs) && in_array("ETF", $allInvs) && count($tickerValues) > count($tickersFound)) {
                            // Any tickers not found in pertrac DB are assumed to be Stocks
                            $stocksMFETFs = true;
                        }
                    }
                    unset($pertrackRows);
                    // Point 25
                    $pertrackC = $pertrackObj->count("category = 'NONCOR' and ticker in ({$tickers}) and status=1");
                    if ($pertrackC > 0) {
                        $nonCoreelatedTicker = true;
                    }
                    unset($pertrackC);
                }
                parent::setEngine();
                $this->sengine->stocksMFETFs = $stocksMFETFs;
                $this->sengine->nonCoreelatedTicker = $nonCoreelatedTicker;
                parent::saveEngine();

                $allAssets = $assetObj->findAllBySql("select balance,invpos from assets where type in ('BANK','BROK','CR','IRA') and user_id=:user_id and status=0", array("user_id" => $user_id));
                $tickerRiskVal = 0;
                $extraCashValue = 0;
                parent::setEngine();
                $allTickAmount = array();
                if ($allAssets) {
                    // Calculating the Total Ticker Value
                    $this->calculateAndUpdateTickerValue($allAssets, $utilityObj, $tickers);
                } else {
                    $this->sengine->tickerRiskValue = 0;
                }
                unset($tickers);
                $this->sengine->extraCashValue = $extraCashValue;
                parent::saveEngine();
                unset($allAssets, $allTickAmount);
                break;

            case "EDUC":
                # Point 11
                $otherAssets = $assetObj->findBySql("select sum(balance) as total from assets where (type in ('EDUC','VEHI') OR (type='PROP' and livehere=1)) and status=0 and user_id=:user_id", array("user_id" => $user_id));

                $assetData = parent::getAssetData($user_id);
                $assets = $assetData[0];
                $insurance = $assetData[1];
                $pertrack = $assetData[2];
                $tickers = $assetData[3];

                parent::setEngine();
                parent::CalculatePoint10($assets, $insurance, $pertrack);
                parent::saveEngine();

                parent::setEngine();
                if ($otherAssets) {
                    $this->sengine->userSumOfOtherAssets = $otherAssets->total;
                } else {
                    $this->sengine->userSumOfOtherAssets = 0;
                }
                parent::saveEngine();
                unset($otherAssets);

                $beneficiary = $assetObj->count("type in ('EDUC','IRA','CR','PENS') and beneficiary=1 and status=0 and user_id=:user_id", array("user_id" => $user_id));
                //POINT 15:
                parent::setEngine();
                if ($beneficiary > 0) {
                    $this->sengine->beneAssigned = true;
                } else {
                    $this->sengine->beneAssigned = false;
                }
                parent::saveEngine();
                unset($beneficiary);

                # Point 16, 26
                $contributionCount = $assetObj->count("type in ('BANK','BROK','EDUC','IRA','CR') and contribution > 0 and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($contributionCount > 0) {
                    $this->sengine->investmentFactor = 1;
                    $this->sengine->point26cond = 1;
                } else {

                    $this->sengine->investmentFactor = 0;
                    $this->sengine->point26cond = 0;
                }
                parent::saveEngine();
                unset($contributionCount);

                # Point 20
                $withdrawal = $assetObj->findBySql("select sum(withdrawal) as total from assets where type in ('BANK','BROK','EDUC','IRA','CR') and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($withdrawal && $withdrawal->total > 0) {
                    $this->sengine->permonthWithdrawal = $withdrawal->total;
                } else {

                    $this->sengine->permonthWithdrawal = 0;
                }
                parent::saveEngine();
                unset($withdrawal);

                //point 23, 25
                $stocksMFETFs = false;
                $nonCoreelatedTicker = false;
                if ($tickers && $tickers != "") {
                    // Point 23
                    $pertrackRows = $pertrackObj->findBySql("SELECT GROUP_CONCAT(distinct(itemtype)) as itemType, GROUP_CONCAT(distinct(ticker)) as tickers FROM `pertrac` WHERE ticker in ({$tickers}) and status=1");
                    if ($pertrackRows) {
                        $allInvs = explode(',', $pertrackRows->itemType);
                        $tickersFound = explode(',', $pertrackRows->tickers);
                        $tickerValues = explode(',', $tickers);
                        if (in_array("STOCK", $allInvs) && in_array("MF", $allInvs) && in_array("ETF", $allInvs)) {
                            $stocksMFETFs = true;
                        } else if (in_array("MF", $allInvs) && in_array("ETF", $allInvs) && count($tickerValues) > count($tickersFound)) {
                            // Any tickers not found in pertrac DB are assumed to be Stocks
                            $stocksMFETFs = true;
                        }
                    }
                    unset($pertrackRows);
                    // Point 25
                    $pertrackC = $pertrackObj->count("category = 'NONCOR' and ticker in ({$tickers}) and status=1");
                    if ($pertrackC > 0) {
                        $nonCoreelatedTicker = true;
                    }
                    unset($pertrackC);
                }
                unset($tickers);
                parent::setEngine();
                $this->sengine->stocksMFETFs = $stocksMFETFs;
                $this->sengine->nonCoreelatedTicker = $nonCoreelatedTicker;
                parent::saveEngine();
                break;

            case "BUSI":
                $totalAssets = $assetObj->findBySql("select sum(balance) as total from assets where type not in ('SS','PENS','EDUC','VEHI') and status=0 and user_id=:user_id and livehere <> 1", array("user_id" => $user_id));
                $totalCashValue = $insuranceObj->findBySql("select sum(cashvalue) as total_cashvalue from insurance where type in ('LIFE') and status = 0 and user_id=:user_id and lifeinstype <> 64", array("user_id" => $user_id));
                $diffAssets = $assetObj->findBySql("select sum(balance) as diff from assets where type in ('BUSI','PROP','OTHE') and status=0 and user_id=:user_id and livehere <> 1", array("user_id" => $user_id));

                $assetData = parent::getAssetData($user_id);
                $assets = $assetData[0];
                $insurance = $assetData[1];
                $pertrack = $assetData[2];
                $tickers = $assetData[3];

                parent::setEngine();
                parent::CalculatePoint10($assets, $insurance, $pertrack);
                parent::saveEngine();

                parent::setEngine();
                // Total Assets (Minus EDUC, PENS, SS, VEHI) for Point 11, 22
                if ($totalAssets) {
                    $this->sengine->userSumOfAssets = $totalAssets->total;
                } else {
                    $this->sengine->userSumOfAssets = 0;
                }
                // Point # 14
                if ($totalCashValue) {
                    $this->sengine->userSumOfAssets += $totalCashValue->total_cashvalue;
                }
                if ($diffAssets) {
                    $this->sengine->numeratorP14 = $this->sengine->userSumOfAssets - $diffAssets->diff;
                } else {
                    $this->sengine->numeratorP14 = $this->sengine->userSumOfAssets;
                }
                parent::saveEngine();
                unset($totalAssets, $totalCashValue, $diffAssets);
                break;

            case "PROP":
                $totalAssets = $assetObj->findBySql("select sum(balance) as total from assets where type not in ('SS','PENS','EDUC','VEHI') and status=0 and user_id=:user_id and livehere <> 1", array("user_id" => $user_id));
                $totalCashValue = $insuranceObj->findBySql("select sum(cashvalue) as total_cashvalue from insurance where type in ('LIFE') and status = 0 and user_id=:user_id and lifeinstype <> 64", array("user_id" => $user_id));
                $diffAssets = $assetObj->findBySql("select sum(balance) as diff from assets where type in ('BUSI','PROP','OTHE') and status=0 and user_id=:user_id and livehere <> 1", array("user_id" => $user_id));
                $otherAssets = $assetObj->findBySql("select sum(balance) as total from assets where (type in ('EDUC','VEHI') OR (type='PROP' and livehere=1)) and status=0 and user_id=:user_id", array("user_id" => $user_id));

                $assetData = parent::getAssetData($user_id);
                $assets = $assetData[0];
                $insurance = $assetData[1];
                $pertrack = $assetData[2];
                $tickers = $assetData[3];

                parent::setEngine();
                parent::CalculatePoint10($assets, $insurance, $pertrack);
                parent::saveEngine();

                parent::setEngine();
                if ($otherAssets) {
                    $this->sengine->userSumOfOtherAssets = $otherAssets->total;
                } else {
                    $this->sengine->userSumOfOtherAssets = 0;
                }

                // Total Assets (Minus EDUC, PENS, SS, VEHI) for Point 11, 22
                if ($totalAssets) {
                    $this->sengine->userSumOfAssets = $totalAssets->total;
                } else {
                    $this->sengine->userSumOfAssets = 0;
                }
                // Point # 14
                if ($totalCashValue) {
                    $this->sengine->userSumOfAssets += $totalCashValue->total_cashvalue;
                }
                if ($diffAssets) {
                    $this->sengine->numeratorP14 = $this->sengine->userSumOfAssets - $diffAssets->diff;
                } else {
                    $this->sengine->numeratorP14 = $this->sengine->userSumOfAssets;
                }
                parent::saveEngine();
                unset($totalAssets, $totalCashValue, $diffAssets, $otherAssets);
                break;

            case "VEHI":
                # Point 11
                $otherAssets = $assetObj->findBySql("select sum(balance) as total from assets where (type in ('EDUC','VEHI') OR (type='PROP' and livehere=1)) and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($otherAssets) {
                    $this->sengine->userSumOfOtherAssets = $otherAssets->total;
                } else {
                    $this->sengine->userSumOfOtherAssets = 0;
                }
                parent::saveEngine();
                unset($otherAssets);
                break;

            case"PENS":
                # POINT 27:
                $pensAssets = $assetObj->findBySql("select sum(balance) as total from assets where type in ('PENS') and status=0 and user_id=:user_id", array("user_id" => $user_id));
                $ssAssets = $assetObj->findBySql("select sum(balance) as total from assets where type in ('SS') and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($pensAssets && $pensAssets->total > 0) {
                    $this->sengine->userPensionDetails = 1;
                    $this->sengine->annualRetirementIncome = isset($pensAssets->total) ? $pensAssets->total * 12 : 0;
                } else {
                    $this->sengine->userPensionDetails = 0;
                    $this->sengine->annualRetirementIncome = 0;
                }
                if ($ssAssets && $ssAssets->total > 0) {
                    $this->sengine->userSocialSecurityDetails = 1;
                    $this->sengine->annualRetirementIncome += isset($ssAssets->total) ? $ssAssets->total * 12 : 0;
                } else {
                    $this->sengine->userSocialSecurityDetails = 0;
                }
                parent::saveEngine();
                unset($pensAssets, $ssAssets);

                $beneficiary = $assetObj->count("type in ('EDUC','IRA','CR','PENS') and beneficiary=1 and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                //POINT 15:
                if ($beneficiary > 0) {
                    $this->sengine->beneAssigned = true;
                } else {
                    $this->sengine->beneAssigned = false;
                }
                parent::saveEngine();
                unset($beneficiary);

                parent::setupDefaultRetirementGoal();
                break;

            case"BROK":
                $totalAssets = $assetObj->findBySql("select sum(balance) as total from assets where type not in ('SS','PENS','EDUC','VEHI') and status=0 and user_id=:user_id and livehere <> 1", array("user_id" => $user_id));
                $goalSettingAssets = $assetObj->findBySql("select sum(balance) as total from assets where type in ('BANK','BROK') and status=0 and user_id=:user_id", array("user_id" => $user_id));
                $totalCashValue = $insuranceObj->findBySql("select sum(cashvalue) as total_cashvalue from insurance where type in ('LIFE') and status = 0 and user_id=:user_id and lifeinstype <> 64", array("user_id" => $user_id));
                $diffAssets = $assetObj->findBySql("select sum(balance) as diff from assets where type in ('BUSI','PROP','OTHE') and status=0 and user_id=:user_id and livehere <> 1", array("user_id" => $user_id));

                $assetData = parent::getAssetData($user_id);
                $assets = $assetData[0];
                $insurance = $assetData[1];
                $pertrack = $assetData[2];
                $tickers = $assetData[3];

                parent::setEngine();
                parent::CalculatePoint10($assets, $insurance, $pertrack);
                parent::saveEngine();

                parent::setEngine();
                // Total Assets (Minus EDUC, PENS, SS, VEHI) for Point 11, 22
                if ($totalAssets) {
                    $this->sengine->userSumOfAssets = $totalAssets->total;
                } else {
                    $this->sengine->userSumOfAssets = 0;
                }
                // Point #3
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
                // Point #14 and #3
                if ($totalCashValue) {
                    $this->sengine->userSumOfAssets += $totalCashValue->total_cashvalue;
                    $this->sengine->userSumOfGoalSettingAssets += $totalCashValue->total_cashvalue;
                }
                if ($diffAssets) {
                    $this->sengine->numeratorP14 = $this->sengine->userSumOfAssets - $diffAssets->diff;
                } else {
                    $this->sengine->numeratorP14 = $this->sengine->userSumOfAssets;
                }
                parent::saveEngine();
                unset($totalAssets, $totalCashValue, $diffAssets, $goalSettingAssets);

                # Point 16, 26
                $contributionCount = $assetObj->count("type in ('BANK','BROK','EDUC','IRA','CR') and contribution > 0 and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($contributionCount > 0) {
                    $this->sengine->investmentFactor = 1;
                    $this->sengine->point26cond = 1;
                } else {

                    $this->sengine->investmentFactor = 0;
                    $this->sengine->point26cond = 0;
                }
                parent::saveEngine();
                unset($contributionCount);

                # Point 20
                $withdrawal = $assetObj->findBySql("select sum(withdrawal) as total from assets where type in ('BANK','BROK','EDUC','IRA','CR') and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($withdrawal && $withdrawal->total > 0) {
                    $this->sengine->permonthWithdrawal = $withdrawal->total;
                } else {

                    $this->sengine->permonthWithdrawal = 0;
                }
                parent::saveEngine();
                unset($withdrawal);

                $contribution = $assetObj->findBySql("select sum(contribution) as total from assets where type in ('BANK','BROK') and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($contribution && $contribution->total > 0) {
                    $this->sengine->taxableAnnualSavings = 12 * $contribution->total;
                } else {
                    $this->sengine->taxableAnnualSavings = 0;
                }
                parent::saveEngine();
                unset($contribution);

                //point 23, 25
                $stocksMFETFs = false;
                $nonCoreelatedTicker = false;
                if ($tickers && $tickers != "") {
                    // Point 23
                    $pertrackRows = $pertrackObj->findBySql("SELECT GROUP_CONCAT(distinct(itemtype)) as itemType, GROUP_CONCAT(distinct(ticker)) as tickers FROM `pertrac` WHERE ticker in ({$tickers}) and status=1");
                    if ($pertrackRows) {
                        $allInvs = explode(',', $pertrackRows->itemType);
                        $tickersFound = explode(',', $pertrackRows->tickers);
                        $tickerValues = explode(',', $tickers);
                        if (in_array("STOCK", $allInvs) && in_array("MF", $allInvs) && in_array("ETF", $allInvs)) {
                            $stocksMFETFs = true;
                        } else if (in_array("MF", $allInvs) && in_array("ETF", $allInvs) && count($tickerValues) > count($tickersFound)) {
                            // Any tickers not found in pertrac DB are assumed to be Stocks
                            $stocksMFETFs = true;
                        }
                    }
                    unset($pertrackRows);
                    // Point 25
                    $pertrackC = $pertrackObj->count("category = 'NONCOR' and ticker in ({$tickers}) and status=1");
                    if ($pertrackC > 0) {
                        $nonCoreelatedTicker = true;
                    }
                    unset($pertrackC);
                }
                parent::setEngine();
                $this->sengine->stocksMFETFs = $stocksMFETFs;
                $this->sengine->nonCoreelatedTicker = $nonCoreelatedTicker;
                parent::saveEngine();

                $allAssets = $assetObj->findAllBySql("select balance,invpos from assets where type in ('BANK','BROK','CR','IRA') and user_id=:user_id and status=0", array("user_id" => $user_id));
                $tickerRiskVal = 0;
                $extraCashValue = 0;
                $allTickAmount = array();
                parent::setEngine();
                if ($allAssets) {
                    // Calculating the Total Ticker Value
                    $this->calculateAndUpdateTickerValue($allAssets, $utilityObj, $tickers);
                } else {
                    $this->sengine->tickerRiskValue = 0;
                }
                unset($tickers);
                $this->sengine->extraCashValue = $extraCashValue;
                parent::saveEngine();
                unset($allAssets, $allTickAmount);
                break;

            case "SS":
                # POINT 27:
                $pensAssets = $assetObj->findBySql("select sum(balance) as total from assets where type in ('PENS') and status=0 and user_id=:user_id", array("user_id" => $user_id));
                $ssAssets = $assetObj->findBySql("select sum(balance) as total from assets where type in ('SS') and status=0 and user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($pensAssets && $pensAssets->total > 0) {
                    $this->sengine->userPensionDetails = 1;
                    $this->sengine->annualRetirementIncome = isset($pensAssets->total) ? $pensAssets->total * 12 : 0;
                } else {
                    $this->sengine->userPensionDetails = 0;
                    $this->sengine->annualRetirementIncome = 0;
                }
                if ($ssAssets && $ssAssets->total > 0) {
                    $this->sengine->userSocialSecurityDetails = 1;
                    $this->sengine->annualRetirementIncome += isset($ssAssets->total) ? $ssAssets->total * 12 : 0;
                } else {
                    $this->sengine->userSocialSecurityDetails = 0;
                }
                parent::saveEngine();
                unset($ssAssets, $pensAssets);
                parent::setupDefaultRetirementGoal();
                break;

            case"OTHE":
                $totalAssets = $assetObj->findBySql("select sum(balance) as total from assets where type not in ('SS','PENS','EDUC','VEHI') and status=0 and user_id=:user_id and livehere <> 1", array("user_id" => $user_id));
                $totalCashValue = $insuranceObj->findBySql("select sum(cashvalue) as total_cashvalue from insurance where type in ('LIFE') and status = 0 and user_id=:user_id and lifeinstype <> 64", array("user_id" => $user_id));
                $diffAssets = $assetObj->findBySql("select sum(balance) as diff from assets where type in ('BUSI','PROP','OTHE') and status=0 and user_id=:user_id and livehere <> 1", array("user_id" => $user_id));

                $assetData = parent::getAssetData($user_id);
                $assets = $assetData[0];
                $insurance = $assetData[1];
                $pertrack = $assetData[2];
                $tickers = $assetData[3];

                parent::setEngine();
                parent::CalculatePoint10($assets, $insurance, $pertrack);
                parent::saveEngine();

                parent::setEngine();
                // Total Assets (Minus EDUC, PENS, SS, VEHI) for Point 11, 22
                if ($totalAssets) {
                    $this->sengine->userSumOfAssets = $totalAssets->total;
                } else {
                    $this->sengine->userSumOfAssets = 0;
                }
                // Point # 14
                if ($totalCashValue) {
                    $this->sengine->userSumOfAssets += $totalCashValue->total_cashvalue;
                }
                if ($diffAssets) {
                    $this->sengine->numeratorP14 = $this->sengine->userSumOfAssets - $diffAssets->diff;
                } else {
                    $this->sengine->numeratorP14 = $this->sengine->userSumOfAssets;
                }
                parent::saveEngine();
                unset($totalAssets, $totalCashValue, $diffAssets);
                break;
        }


        $assetCount = $assetObj->findBySql("SELECT count(distinct(type)) as count_assets FROM assets WHERE status=0 and user_id=:user_id", array("user_id" => $user_id));
        $acount = ($assetCount) ? $assetCount->count_assets : 0;
        unset($assetCount);

        $loanCount = $debtObj->findBySql("SELECT count(distinct(mortgagetype)) as count_debts FROM debts WHERE status=0 and user_id=:user_id and type in ('LOAN')", array("user_id" => $user_id));
        $lcount = ($loanCount) ? $loanCount->count_debts : 0;
        unset($loanCount);

        $debtCount = $debtObj->findBySql("SELECT count(distinct(type)) as count_debts FROM debts WHERE status=0 and user_id=:user_id and type not in ('LOAN')", array("user_id" => $user_id));
        $dcount = ($debtCount) ? $debtCount->count_debts : 0;
        unset($debtCount);

        $insuranceCount = $insuranceObj->findBySql("SELECT count(distinct(type)) as count_insurance FROM insurance WHERE status=0 and user_id=:user_id", array("user_id" => $user_id));
        $icount = ($insuranceCount) ? $insuranceCount->count_insurance : 0;
        unset($insuranceCount);

        $value = ($acount + $icount + $lcount + $dcount) / (11 + 7 + 6);
        $value = ($value > 0) ? $value : 0;
        $value = ($value < 1) ? $value : 1;
        parent::setEngine();
        $this->sengine->userProfilePoints_others = round(4 * $value);
        parent::saveEngine();

        $userassets = 0;
        $estimation = Estimation::model()->find("user_id=:user_id", array("user_id" => $user_id));
        $sumOfAssets = $this->sengine->userSumOfAssets + $this->sengine->userSumOfOtherAssets;
        parent::setEngine();
        if ($acount > 0) {
            $this->sengine->userProfilePoints_assets = 10;
        } else {
            $this->sengine->userProfilePoints_assets = 0;
        }

        parent::saveEngine();
        unset($estimation);

        //CALLING MONTE CARLO:
        $sectionNames = "ASSET";
        if ($retirementAmount != $this->sengine->retirementAmountDesired) {
            $sectionNames .= "|GOAL";
        }
        parent::calculateScore($sectionNames, $user_id);
    }

    // Calculating the Total Ticker Value
    private function calculateAndUpdateTickerValue($allAssets, $utilityObj, $tickers) {
        $total = 0;
        foreach ($allAssets as $asset) {
            $invArr = json_decode($asset->invpos);
            $investmentTotal = 0;
            if ($invArr) {
                // Calculating the total ticker value
                foreach ($invArr as $inv) {
                    if (isset($inv) && isset($inv->amount) && isset($inv->ticker)) {
                        $total += $utilityObj->tickerAmountToDB($inv->amount);
                        $investmentTotal += $utilityObj->tickerAmountToDB($inv->amount);
                    }
                }
            }
            if ($asset->balance - $investmentTotal > 0) {
                $total += $asset->balance - $investmentTotal;
            }
        }


        if ($total <= 0) {
            parent::setEngine();
            $this->sengine->tickerRiskValue = 0;
            parent::saveEngine();
            return;
        }


        $pertrackObj = new Pertrack();
        $pertrackS = array();
        if ($tickers && $tickers != "") {
            $pertrackS = $pertrackObj->findAll("ticker in ({$tickers})");
        }
        if (!$pertrackS) {
            $pertrackS = array();
        }


        $total_std_deviation = 0;
        foreach ($allAssets as $asset) {
            $invArr = json_decode($asset->invpos);
            if ($invArr) {
                foreach ($invArr as $inv) {
                    if (isset($inv) && isset($inv->amount) && isset($inv->ticker)) {
                        $ticker_percentage = ($inv->amount / $total);
                        $ticker_stddev = 0.1;
                        foreach ($pertrackS as $pertrac) {
                            if (strtoupper(trim($pertrac->ticker)) == strtoupper(trim($inv->ticker))) {
                                $ticker_stddev = $pertrac->std_deviation;
                                break;
                            }
                        }
                        $total_std_deviation += $ticker_percentage * $ticker_stddev;
                    }
                }
            }
        }

        // Updating sengine with ticker value.
        parent::setEngine();
        $this->sengine->tickerRiskValue = $total_std_deviation * 100;
        parent::saveEngine();
    }

    function actionReprioritizeAssets() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        if (!isset($_POST["assets"])) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'assets' was not set.")));
        }
        $assetArray = $_POST["assets"];

        $assetIds = array();
        $assetHash = array();
        foreach ($assetArray as $assetValue) {
            $values = explode("|", $assetValue);
            $assetIds[] = $values[0];
            $assetHash[$values[0]] = $values[1];
        }
        $assetObj = new Assets();
        $criteria = new CDbCriteria();
        $criteria->condition = "user_id = :user_id AND status <> 1";
        $criteria->select = 'id,priority';
        $criteria->params = array('user_id' => $user_id);
        $criteria->addInCondition("id", $assetIds);
        $assets = $assetObj->findAll($criteria);

        if(isset($assets) && !empty($assets)) {
            foreach ($assets as $asset) {
                if (array_key_exists($asset->id, $assetHash)) {
                    $asset->priority = $assetHash[$asset->id];
                    $asset->save();
                }
            }

            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "Assets have been reprioritized successfully.")));
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "The user does not have permission to reprioritize these assets.")));
    }

}

?>
