<?php

/* * ********************************************************************
 * Filename: DebtController.php
 * Folder: controllers
 * Description: Getting input from the HTML
 * @author Thayub J (For TruGlobal Inc)
 * @copyright (c) 2013 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class DebtController extends Scontroller {

    // Define access control
    public function accessRules() {
        return array_merge(
                array(array('allow', 'users' => array('?'))),
                // Include parent access rules
                parent::accessRules()
        );
    }

    function actionDebtcrud() {
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
                $this->createupdateDebt();
                $actionSet = true;
                break;
            CASE "READ":
                $this->ReadDebt();
                $actionSet = true;
                break;
            CASE "UPDATE":
                $this->createupdateDebt();
                $actionSet = true;
                break;
            CASE "DELETE";
                $this->DeleteDebt();
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

    function createupdateDebt() {
        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $action = $_POST["action"];
        $id = isset($_POST["id"]) ? $_POST["id"] : 0;

        //get all the values
        if (!isset($_POST["accttype"]) && $action == 'ADD') {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'accttype' was not set.")));
        }

        $needsUpdate = false;
        $debt = new Debts();
        if ($action == 'UPDATE' && $id > 0) {
            $debt = Debts::model()->findByPk($id);
            if (!$debt) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'id' was not set to a valid value.")));
            } else if ($debt->user_id != $user_id) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "This user does not have permissions to update this debt.")));
            }
        } else if ($action == 'ADD') {
            $ctype = strtoupper($_POST["accttype"]);
            if ($ctype != 'CC' && $ctype != 'LOAN' && $ctype != 'MORT') {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'accttype' should be set to either CC, LOAN, or MORT.")));
            }
            $totaldebts = Debts::model()->count("user_id=:user_id", array("user_id" => $user_id));
            $debt->priority = $totaldebts + 1;
            $debt->refid = '';
            $debt->context = 'MANUAL';
            $debt->refid = '';
            $debt->status = 0;
            $debt->type = $ctype;
            $debt->user_id = $user_id;
            $debt->modifiedtimestamp = date("Y-m-d H:i:s");
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "We could not add/update the debt at this time.")));
        }

        if (isset($_POST["name"])) {
            $name = $_POST["name"];
            $debt->name = $name;
            $needsUpdate = true;
        }

        if (isset($_POST["apr"])) {
            $apr = $_POST["apr"];
            $debt->apr = $apr;
            $needsUpdate = true;
        }

        if (isset($_POST["monthly_payoff_balances"])) {
            $monthly_payoff_balances = $_POST["monthly_payoff_balances"];
            $debt->monthly_payoff_balances = $monthly_payoff_balances;
            $needsUpdate = true;
        }

        if (isset($_POST["amount"])) {
            $amount = Utility::amountToDB($_POST["amount"]);
            $debt->balowed = $amount;
            $needsUpdate = true;
        }

        if (isset($_POST["amtpermonth"])) {
            $amtpermonth = Utility::amountToDB($_POST["amtpermonth"]);
            $debt->amtpermonth = $amtpermonth;
            $needsUpdate = true;
        }

        if (isset($_POST["intdeductible"])) {
            $intdeductible = $_POST["intdeductible"];
            $debt->intdeductible = $intdeductible;
            $needsUpdate = true;
        }

        if (isset($_POST["livehere"])) {
            $livehere = $_POST["livehere"];
            $debt->livehere = $livehere;
            $needsUpdate = true;
        }

        if (isset($_POST["mortgagetype"])) {
            $mortgagetype = $_POST["mortgagetype"];
            $debt->mortgagetype = $mortgagetype;
            $needsUpdate = true;
        }

        if (isset($_POST["yearsremaining"])) {
            $yearsremaining = $_POST["yearsremaining"];
            $debt->yearsremaining = $yearsremaining;
            $needsUpdate = true;
        }

        $debtObj = array(
            'id' => $debt->id,
            'user_id' => $debt->user_id,
            'context' => $debt->context,
            'name' => $debt->name,
            'amount' => $debt->balowed,
            'actid' => $debt->actid,
            'amtpermonth' => $debt->amtpermonth,
            'apr' => $debt->apr,
            'yearsremaining' => $debt->yearsremaining,
            'intdeductible' => $debt->intdeductible,
            'mortgagetype' => $debt->mortgagetype,
            'livehere' => $debt->livehere,
            'accttype' => $debt->type,
            'subtype' => $debt->subtype,
            'refId' => $debt->refid,
            'status' => $debt->status,
            'priority' => $debt->priority,
            'monthly_payoff_balances' => $debt->monthly_payoff_balances,
            'lstype' => "DEBT"
        );
        if ($needsUpdate) {
            if ($debt->save()) {
                $debtObj["id"] = $debt->id;
                $this->actionreCalculateScoreDebts($debt, "ADD", $user_id, 1); // Enabled for single call
                $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'totalscore' => $this->totalScore, 'message' => 'Debt has been updated successfully.', "score" => "updated", "debt" => $debtObj)));
            } else {
                $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Debt has not been updated.', "debt" => $debtObj)));
            }
        }
        parent::setEngine();
        $totalScore = $sengineObj->updateScore();
        $this->sendResponse(200, CJSON::encode(array("status" => 'OK', "totalscore" => $totalScore, "message" => 'Debt has been updated successfully.', "debt" => $debtObj)));
    }

    function actionGetDebts() {
        $retAge = 0;
        //if there is no session
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $debtsORMObj = Debts::model()->findAll("user_id=:user_id and status!=1", array("user_id" => $user_id));

        $lsAccDEBT = array();
        foreach ($debtsORMObj as $acc) {
            $lsEachAcc = array(
                'id' => $acc->id,
                'user_id' => $acc->user_id,
                'context' => $acc->context,
                'name' => $acc->name,
                'amount' => $acc->balowed,
                'actid' => $acc->actid,
                'amtpermonth' => $acc->amtpermonth,
                'apr' => $acc->apr,
                'yearsremaining' => $acc->yearsremaining,
                'intdeductible' => $acc->intdeductible,
                'mortgagetype' => $acc->mortgagetype,
                'livehere' => $acc->livehere,
                'accttype' => $acc->type,
                'subtype' => $acc->subtype,
                'refId' => $acc->refid,
                'status' => $acc->status,
                'monthly_payoff_balances' => $acc->monthly_payoff_balances,
                'priority' => $acc->priority,
                'lstype' => "DEBT"
            );
            $lsAccDEBT[] = $lsEachAcc;
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "debts" => $lsAccDEBT)));
    }

    function ReadDebt() {
        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $id = isset($_POST["id"]) ? $_POST["id"] : 0;
        if ($id != 0) {
            $debt = Debts::model()->getUserDebts($user_id);
            $debtObj = array(
                'id' => $debt->id,
                'user_id' => $debt->user_id,
                'context' => $debt->context,
                'name' => $debt->name,
                'amount' => $debt->balowed,
                'actid' => $debt->actid,
                'amtpermonth' => $debt->amtpermonth,
                'apr' => $debt->apr,
                'yearsremaining' => $debt->yearsremaining,
                'intdeductible' => $debt->intdeductible,
                'mortgagetype' => $debt->mortgagetype,
                'livehere' => $debt->livehere,
                'accttype' => $debt->type,
                'subtype' => $debt->subtype,
                'refId' => $debt->refid,
                'status' => $debt->status,
                'monthly_payoff_balances' => $debt->monthly_payoff_balances,
                'priority' => $debt->priority,
                'lstype' => "DEBT"
            );
            if ($debt && $user_id == $debt->user_id) {
                $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => "Debt successfully read.", 'debt' => $debtObj)));
            }
        }
        $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => "Unable to read debt.")));
    }

    function DeleteDebt() {

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $id = isset($_POST["id"]) ? $_POST["id"] : 0;
        if ($id != 0) {
            $debts = Debts::model()->findByPk($id);
            if ($debts && $user_id == $debts->user_id) {
                if ($debts->status != 1) {
                    $type = $debts->context;
                    $debts->status = 1;
                    $debts->save();
                    $this->actionreCalculateScoreDebts($debts, "DELETE", $user_id, 1); // Enabled for single call
                    // Delete from CE
                    if ($type == 'AUTO') {
                        $obj = new CashedgeController(1); // preparing object
                        $obj->actionDeletefiAcctid($debts->actid, $debts->user_id, 'DEBT'); // For single call
                    }
                    $otherdebts = Debts::model()->findAll("user_id=:user_id and status!=1", array("user_id" => $user_id));
                    if ($otherdebts)
                        $otherdebtscnt = 1;
                    else
                        $otherdebtscnt = 0;
                    $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'totalscore' => $this->totalScore, 'message' => "Debt has been deleted.", 'debtcount' => $otherdebtscnt)));
                }
                else {
                    parent::setEngine();
                    $totalScore = $sengineObj->updateScore();
                    $otherdebts = Debts::model()->findAll("user_id=:user_id and status!=1", array("user_id" => $user_id));
                    if ($otherdebts)
                        $otherdebtscnt = 1;
                    else
                        $otherdebtscnt = 0;
                    $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'totalscore' => $totalScore, 'message' => "Debt has been deleted.", 'debtcount' => $otherdebtscnt)));
                }
            }
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "This user does not have permissions to delete this debt.")));
    }

    function ToggleStatus() {

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $action = $_POST["action"];
        $id = isset($_POST["id"]) ? $_POST["id"] : 0;
        if ($id != 0) {
            $debt = Debts::model()->findByPk($id);
            if ($debt && $debt->user_id == $user_id) {
                if ($debt->status != 1) {
                    $needsUpdate = false;
                    if ($debt->status == 0 && $action == 'HIDE') {
                        $debt->status = 2;
                        $needsUpdate = true;
                    } else if ($debt->status == 2 && $action == 'UNHIDE') {
                        $debt->status = 0;
                        $needsUpdate = true;
                    }
                    if ($needsUpdate) {
                        if ($debt->save()) {
                            $this->actionreCalculateScoreDebts($debt, "DELETE", $user_id);
                            $this->sendResponse(200, CJSON::encode(array("status" => 'OK', 'totalscore' => $this->totalScore, "message" => 'Debt status has been updated.')));
                        } else {
                            $this->sendResponse(200, CJSON::encode(array("status" => 'ERROR', "message" => 'Debt status was not updated.')));
                        }
                    } else {
                        parent::setEngine();
                        $totalScore = $sengineObj->updateScore();
                        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'totalscore' => $totalScore, 'message' => "Debt status has been updated.")));
                    }
                } else {
                    $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => "Debt has already been deleted.")));
                }
            }
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "This user does not have permissions to update this debt.")));
    }

    /**
     * Used to recalculate score
     */
    function actionreCalculateScoreDebts($ceDebts, $ceAction, $ceUid, $ceFlag = NULL) {

        $debts = isset($_GET["debt"]) ? (object) $_GET["debt"] : $ceDebts;
        // $action = isset($_GET["action"]) ? $_GET["action"] : $ceAction; - not using
        $user_id = isset($_GET["uid"]) ? $_GET["uid"] : isset($ceUid) ? $ceUid : 0;

        if ($user_id == 0 && $ceFlag != 1) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Insufficent Data')));
            // Error Logging needs to be done here.
        }

        $userPerDetails = Userpersonalinfo::model()->find("user_id=:user_id", array("user_id" => $user_id));

        //Object Initilization
        $assetObj = new Assets();
        $debtObj = new Debts();
        $insuranceObj = new Insurance();
        $expenseObj = new Expense();
        $estimationObj = new Estimation();

        # POINT 1:
        $userDebtAcc = $debtObj->count("user_id=:user_id AND status=0 AND context='AUTO'", array("user_id" => $user_id));
        $userAssetAcc = $assetObj->count("user_id=:user_id AND status=0 AND context='AUTO'", array("user_id" => $user_id));
        $userInsuranceAcc = $insuranceObj->count("user_id=:user_id AND status=0 AND context='AUTO'", array("user_id" => $user_id));
        parent::setEngine($user_id);
        if ($userDebtAcc > 0 || $userAssetAcc > 0 || $userInsuranceAcc > 0 || $userPerDetails->connectAccountPreference == '1') {
            $this->sengine->isUserDownloadAccount = true;
            $this->sengine->userProfilePoints_connectAccount = 1;
        } else {
            $this->sengine->isUserDownloadAccount = false;
            $this->sengine->userProfilePoints_connectAccount = 0;
        }
        parent::saveEngine();
        unset($userDebtAcc, $userAssetAcc, $userInsuranceAcc);

        $userDebtAcc = $debtObj->count("user_id=:user_id AND status=0", array("user_id" => $user_id));
        $userAssetAcc = $assetObj->count("user_id=:user_id AND status=0", array("user_id" => $user_id));
        $userInsuranceAcc = $insuranceObj->count("user_id=:user_id AND status=0", array("user_id" => $user_id));
        parent::setEngine();
        if ($userDebtAcc > 0 || $userAssetAcc > 0 || $userInsuranceAcc > 0) {
            $this->sengine->isUserEnteredAccount = true;
        } else {
            $this->sengine->isUserEnteredAccount = false;
        }
        parent::saveEngine();
        unset($userDebtAcc, $userAssetAcc, $userInsuranceAcc);
        # POINT 6:
        if ($debts->type == 'CC') {
            $debtsCreditCard = $debtObj->findAllBySql("SELECT apr FROM debts WHERE user_id=:user_id AND status=0 AND monthly_payoff_balances=0 AND type IN ('CC') ORDER BY balowed DESC", array('user_id' => $user_id));
            $creditFlag = 1;
            $prevCardAPR = 0;
            if (isset($debtsCreditCard)) {
                foreach ($debtsCreditCard as $each) {
                    if (!isset($each->apr) || $prevCardAPR > $each->apr) {
                        $creditFlag = 0;
                        break;
                    }
                    $prevCardAPR = $each->apr;
                }
            }
            parent::setEngine();
            $this->sengine->creditCardFlag = $creditFlag;
            parent::saveEngine();
            unset($debtsCreditCard);
        }

        # POINT 7:
        if ($debts->type != 'MORT') {
            $emiloan = $debtObj->findBySql("SELECT SUM(amtpermonth) AS emiloansql FROM debts WHERE type NOT IN ('MORT') AND user_id=:user_id AND status = 0 AND monthly_payoff_balances=0", array("user_id" => $user_id));
            $otherDebtsInfo = $debtObj->findBySql("SELECT SUM(balowed) AS otherDebtsSum FROM debts WHERE type NOT IN ('MORT') AND user_id=:user_id AND status = 0 AND monthly_payoff_balances=0", array("user_id" => $user_id));

            parent::setEngine();
            $this->sengine->otherDebts = isset($otherDebtsInfo->otherDebtsSum) ? ($otherDebtsInfo->otherDebtsSum) : 0;
            $this->sengine->emiLoanCC = isset($emiloan->emiloansql) ? $emiloan->emiloansql : 0;
            parent::saveEngine();
            unset($emiloan, $otherDebtsInfo);

            if ($this->sengine->emiLoanCC == 0) {
                $debtExpense = $expenseObj->findBySql("SELECT cardloadpmnts FROM expense WHERE user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                $this->sengine->emiLoanCC = isset($debtExpense->cardloadpmnts) ? $debtExpense->cardloadpmnts : 0;
                parent::saveEngine();
                unset($debtExpense);
            }

            $debtData = parent::getDebtData($user_id);
            $restructuringDebtsArr = $debtData[0];
            $personalDebtLoanArr = $debtData[1];
            parent::setEngine();
            parent::CalculatePoint5($restructuringDebtsArr, $personalDebtLoanArr);
            parent::saveEngine();
            unset($allDebts);
        }

        # CONDITIONS FOR MORTGAGE
        # POINT 8:
        if ($debts->type == 'MORT') {
            $mortInfo = $debtObj->findBySql("SELECT SUM(balowed) AS otherDebtsSum FROM debts WHERE type IN ('MORT') AND user_id=:user_id AND status = 0", array("user_id" => $user_id));
            parent::setEngine();
            $this->sengine->mortgageBalance = isset($mortInfo->otherDebtsSum) ? $mortInfo->otherDebtsSum : 0;
            parent::saveEngine();

            $debtCount = $debtObj->count("type=:type AND status=0 AND user_id=:user_id", array("type" => $debts->type, "user_id" => $user_id));
            parent::setEngine();
            if ($debtCount > 0) {
                $this->sengine->mortgageInfo = true;
                parent::saveEngine();
                $rentMortgage = $debtObj->findBySql("SELECT SUM(amtpermonth) AS maxamtpermonth FROM debts WHERE type IN ('MORT') AND user_id=:user_id AND status = 0", array("user_id" => $user_id));
                parent::setEngine();
                $this->sengine->rentMortgage = isset($rentMortgage->maxamtpermonth) ? $rentMortgage->maxamtpermonth : 0;
            } else {
                $this->sengine->mortgageInfo = false;
                $this->sengine->rentMortgage = 0;
            }
            parent::saveEngine();
            unset($mortInfo, $debtCount, $rentMortgage);
        }
        # total assets and total debts of the user for point no 11
        $totalDebts = $debtObj->findBySql("SELECT SUM(balowed) AS totalDebts FROM debts WHERE status = 0 AND monthly_payoff_balances=0 AND user_id=:user_id", array("user_id" => $user_id));
        if ($totalDebts) {
            parent::setEngine();
            $this->sengine->userSumOfDebts = $totalDebts->totalDebts;
            parent::saveEngine();
            unset($totalDebts);
        }

        $assetCount = $assetObj->findBySql("SELECT COUNT(distinct(type)) AS count_assets FROM assets WHERE status=0 AND user_id=:user_id", array("user_id" => $user_id));
        $acount = ($assetCount) ? $assetCount->count_assets : 0;
        $loanCount = $debtObj->findBySql("SELECT COUNT(distinct(mortgagetype)) AS count_debts FROM debts WHERE status=0 AND user_id=:user_id AND type IN ('LOAN')", array("user_id" => $user_id));
        $lcount = ($loanCount) ? $loanCount->count_debts : 0;
        $debtCount = $debtObj->findBySql("SELECT COUNT(distinct(type)) AS count_debts FROM debts WHERE status=0 AND user_id=:user_id AND type NOT IN ('LOAN')", array("user_id" => $user_id));
        $dcount = ($debtCount) ? $debtCount->count_debts : 0;
        $insuranceCount = $insuranceObj->findBySql("SELECT COUNT(distinct(type)) AS count_insurance FROM insurance WHERE status=0 AND user_id=:user_id", array("user_id" => $user_id));
        $icount = ($insuranceCount) ? $insuranceCount->count_insurance : 0;

        $value = ($acount + $icount + $lcount + $dcount) / (11 + 7 + 6);
        $value = ($value > 0) ? $value : 0;
        $value = ($value < 1) ? $value : 1;
        parent::setEngine();
        $this->sengine->userProfilePoints_others = round(4 * $value);
        parent::saveEngine();
        unset($assetCount, $loanCount, $debtCount, $insuranceCount);

        $estimation = $estimationObj->find("user_id=:user_id", array("user_id" => $user_id));
        parent::setEngine();
        if ($dcount > 0) {
            $this->sengine->userProfilePoints_debts = 10;
        } else {
            $this->sengine->userProfilePoints_debts = 0;
        }
        parent::saveEngine();
        unset($estimation);

        parent::calculateScore("DEBTS", $user_id);

        unset($assetObj);
        unset($debtObj);
        unset($insuranceObj);
        unset($expenseObj);
        unset($estimationObj);
    }

    function actionReprioritizeDebts() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        if (!isset($_POST["debts"])) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'debts' was not set.")));
        }
        $debtArray = $_POST["debts"];

        $debtIds = array();
        $debtHash = array();
        foreach ($debtArray as $debtValue) {
            $values = explode("|", $debtValue);
            $debtIds[] = $values[0];
            $debtHash[$values[0]] = $values[1];
        }
        $debtObj = new Debts();
        $criteria = new CDbCriteria();
        $criteria->condition = "user_id = :user_id AND status <> 1";
        $criteria->select = 'id,priority';
        $criteria->params = array('user_id' => $user_id);
        $criteria->addInCondition("id", $debtIds);
        $debts = $debtObj->findAll($criteria);

        if(isset($debts) && !empty($debts)) {
            foreach ($debts as $debt) {
                if (array_key_exists($debt->id, $debtHash)) {
                    $debt->priority = $debtHash[$debt->id];
                    $debt->save();
                }
            }

            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "Debts have been reprioritized successfully.")));
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "The user does not have permission to reprioritize these debts.")));
    }

}

?>
