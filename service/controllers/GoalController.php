<?php

/* * ********************************************************************
 * Filename: GoalController.php
 * Folder: controllers
 * Description: Getting input from the HTML
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2013 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class GoalController extends Scontroller {

    // Define access control
    public function accessRules() {
        return array_merge(
                array(array('allow', 'users' => array('?'))),
                // Include parent access rules
                parent::accessRules()
        );
    }

    function actionGetGoals() {
        $retAge = 0;
        //if there is no session
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        parent::setEngine();

        $debtsORMObj = Debts::model()->findAll("user_id=:user_id and status=0 and monthly_payoff_balances = 0", array("user_id" => $user_id));
        $debtTotal = 0;
        foreach ($debtsORMObj as $acc) {
            $debtTotal = $debtTotal + $acc->balowed;
        }

        $goalincome = 0;
        $goals = Goal::model()->findAll("user_id=:user_id and goalstatus=1", array("user_id" => $user_id));
        $lsUserGoals = array();
        if ($goals) {
            //get user income
            foreach ($goals as $goal) {
                //check if the goal is retirement to get age
                $retirementage = 65;
                if ($goal->goaltype == "RETIREMENT") {
                    $profile = Userpersonalinfo::model()->findBySql("SELECT age, retirementage FROM userpersonalinfo WHERE user_id=:user_id", array("user_id" => $user_id));
                    if (isset($profile) && isset($profile->retirementage) && intval($profile->retirementage) > 0) {
                        $retirementage = intval($profile->retirementage);
                    }

                    $age = '0000-00-00';
                    if (isset($profile) && isset($profile->age)) {
                        $age = $profile->age;
                    }

                    $ageArray = explode('-', $age);
                    if ($ageArray[0] == "0000") {
                        $ageArray[0] = date('Y') - 30;
                    }
                    if ($ageArray[1] == "00") {
                        $ageArray[1] = "01";
                    }
                    if ($ageArray[2] == "00") {
                        $ageArray[2] = "01";
                    }
                    $ageArray[0] = intval($ageArray[0]) + $retirementage;
                    $goal->goalenddate = implode('-', $ageArray);
                } else if ($goal->goaltype == "DEBT") {
                    //calculate the amount from payoffdebts
                    $payoffdebts = $goal->payoffdebts;
                    $debtIds = explode(',', $payoffdebts);
                    $gAmount = 0;
                    if ($debtIds) {
                        foreach ($debtIds as $debtValue) {
                            //
                            if ($debtValue != "") {
                                foreach ($debtsORMObj as $acc) {
                                    if ($acc->id == $debtValue) {
                                        $gAmount = $gAmount + $acc->balowed;
                                    }
                                }
                            }
                        }
                    } else {
                        $gAmount = $debtTotal;
                    }
                    $goal->goalamount = $gAmount;
                }
                else if($goal->goaltype == "HOUSE") {
                    if ($goal->downpayment > 0)
                        $goalincome = $goal->goalamount / ($goal->downpayment / 100);
                    else
                        $goalincome = $goal->goalamount;
                }
                else if($goal->goaltype == "COLLEGE") {
                    if ($goal->collegeyears > 0)
                        $goalincome = $goal->goalamount / $goal->collegeyears;
                    else
                        $goalincome = $goal->goalamount;
                }


                $goalE = new DateTime($goal->goalenddate);
                $goalendYear = $goalE->format("Y");
                $goalendMonth = $goalE->format("m");
                $goalendDay = $goalE->format("d");

                $goalS = new DateTime($goal->goalstartdate);
                $goalstartYear = $goalS->format("Y");
                $goalstartMonth = $goalS->format("m");
                $goalstartDay = $goalS->format("d");

                $lsUserGoal = array(
                    'id' => $goal->id,
                    'goalname' => $goal->goalname,
                    'goaltype' => $goal->goaltype,
                    'retage' => $retirementage,
                    'goalincome' => $goalincome,
                    'goalpriority' => $goal->goalpriority,
                    'goalamount' => $goal->goalamount,
                    'permonth' => $goal->permonth,
                    'saved' => $goal->saved,
                    'downpayment' => $goal->downpayment,
                    'collegeyears' => $goal->collegeyears,
                    'goalstartdate' => $goal->goalstartdate,
                    'goalenddate' => $goal->goalenddate,
                    'goalstatus' => $goal->goalstatus,
                    'goalstartYear' => $goalstartYear,
                    'goalstartMonth' => $goalstartMonth,
                    'goalstartDay' => $goalstartDay,
                    'goalendYear' => $goalendYear,
                    'goalendMonth' => $goalendMonth,
                    'goalendDay' => $goalendDay,
                    'monthlyincome' => $goal->monthlyincome,
                    'payoffdebts' => $goal->payoffdebts,
                    'lifeEC' => $this->sengine->lifeEC,
                    'goalassumptions_1' => $goal->goalassumptions_1,
                    'goalassumptions_2' => $goal->goalassumptions_2,
                    'status' => $goal->status,
                    'modifiedtimestamp' => $goal->modifiedtimestamp
                );
                $lsUserGoals[] = $lsUserGoal;
            }
        }

        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "goals" => $lsUserGoals)));
    }

    /**
     *
     */
    function actionAddupdategoal() {
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
                $this->createupdateGoal();
                $actionSet = true;
                break;
            CASE "READ":
                $this->readGoal();
                $actionSet = true;
                break;
            CASE "UPDATE":
                $this->createupdateGoal();
                $actionSet = true;
                break;
            CASE "DELETE";
                $this->deleteGoal();
                $actionSet = true;
                break;
        }
        if (!$actionSet) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'action' must be set to either 'ADD', 'READ', 'UPDATE', or 'DELETE'.")));
        }

    }

    function actionReprioritizeGoals() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        if (!isset($_POST["goals"])) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'goals' was not set.")));
        }
        $goalArray = $_POST["goals"];

        $goalIds = array();
        $goalHash = array();
        foreach ($goalArray as $uiGoals) {
            $values = explode("|", $uiGoals);
            $goalIds[] = $values[0];
            $goalHash[$values[0]] = $values[1];
        }
        $goalObj = new Goal();
        $criteria = new CDbCriteria();
        $criteria->condition = "user_id = :user_id AND goalstatus=1";
        $criteria->select = 'id,goalpriority';
        $criteria->params = array('user_id' => $user_id);
        $criteria->addInCondition("id", $goalIds);
        $goals = $goalObj->findAll($criteria);

        $needsUpdate = false;
        if(isset($goals) && !empty($goals)) {
            foreach ($goals as $goal) {
                if (array_key_exists($goal->id, $goalHash)) {
                    $goal->goalpriority = $goalHash[$goal->id];
                    $goal->save();
                    $needsUpdate = true;
                }
            }

            if($needsUpdate) {
                parent::setEngine();
                $this->sengine->firstGoalEntryCheck = true;
                $this->sengine->userProfilePoints_goals = 1;
                parent::saveEngine();
                parent::calculateScore("GOALPRIORITY", $user_id);
            }

            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "Goals have been reprioritized successfully.")));
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "The user does not have permission to reprioritize these goals")));
    }

    /**
     *
     */
    function createupdateGoal() {
        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $action = $_POST["action"];
        $id = isset($_POST["id"]) ? $_POST["id"] : 0;

        if (!isset($_POST["goaltype"]) && $action == 'ADD') {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'goaltype' was not set.")));
        }

        //get all the values
        $goalObj = new Goal();
        $susObj = new Sustainablerates();
        $incObj = new Income();
        $assetsObj = new Assets();
        $debtsObj = new Debts();
        $infoObj = new Userpersonalinfo();

        $needsUpdate = false;
        $goalsetting = new Goal();
        if ($action == 'UPDATE' && $id > 0) {
            $goalsetting = $goalObj->findByPk($id);
            if (!$goalsetting) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'id' was not set to a valid value.")));
            }
            else if($goalsetting->user_id != $user_id) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "This user does not have permissions to update this goal.")));
            }
        } else  if($action == 'ADD') {
            $ctype = strtoupper($_POST["goaltype"]);
            if($ctype != 'RETIREMENT' && $ctype != 'DEBT' && $ctype != 'CUSTOM' &&
               $ctype != 'HOUSE' && $ctype != 'COLLEGE') {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'goaltype' should be set to either RETIREMENT, CUSTOM, COLLEGE, DEBT, or HOUSE.")));
            }
            if($ctype == 'RETIREMENT') {
                $totalgoals = $goalObj->count("user_id=:user_id and goaltype = 'RETIREMENT'", array("user_id" => $user_id));
                if($totalgoals > 0) {
                    $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Retirement Goal already exists.")));
                }
            }
            if($ctype == 'DEBT') {
                $totalgoals = $goalObj->count("user_id=:user_id and goaltype = 'DEBT' and goalstatus=1", array("user_id" => $user_id));
                if($totalgoals > 0) {
                    $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Debt Goal already exists.")));
                }
            }
            $totalgoals = $goalObj->count("user_id=:user_id", array("user_id" => $user_id));
            $goalsetting->goalpriority = $totalgoals + 1;
            $goalsetting->goaltype = $ctype;
            $goalsetting->goalstatus = 1;
            $goalsetting->user_id = $user_id;
            $needsUpdate = true;
        }
        else
        {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "We could not add/update the goal at this time.")));
        }

        if(isset($_POST["goalname"])) {
            $goalname = $_POST["goalname"];
            $goalsetting->goalname = $goalname;
            $needsUpdate = true;
        }

        if(isset($_POST["monthlyincome"])) {
            $monthlyincome = $_POST["monthlyincome"];
            $goalsetting->monthlyincome = Utility::amountToDB($monthlyincome);
            $needsUpdate = true;
        }

        if(isset($_POST["goalassumptions_1"])) {
            $goalassumptions_1 = $_POST["goalassumptions_1"];
            $goalsetting->goalassumptions_1 = $goalassumptions_1;
            $needsUpdate = true;
        }

        if(isset($_POST["goalassumptions_2"])) {
            $goalassumptions_2 = $_POST["goalassumptions_2"];
            $goalsetting->goalassumptions_2 = $goalassumptions_2;
            $needsUpdate = true;
        }

        $goalincome = 0;
        $profile = $infoObj->find("user_id=:user_id", array("user_id" => $user_id));
        if (!$profile) {
            $profile = new Userpersonalinfo();
            $profile->user_id = $user_id;
            $profile->retirementage = 65;
        } else if(!isset($profile->retirementage) || intval($profile->retirementage) <= 0) {
            $profile->retirementage = 65;
        }
        $retirementage = intval($profile->retirementage);

        //insert depending on goal type
        switch ($goalsetting->goaltype) {
            case "RETIREMENT":
                if(isset($_POST["retirementage"]) && intval($_POST["retirementage"]) > 0) {
                    $retirementage = intval($_POST["retirementage"]);
                    $profile->retirementage = $retirementage;
                    $profile->save();
                    $needsUpdate = true;
                }

                $age = '0000-00-00';
                if (isset($profile) && isset($profile->age)) {
                    $age = $profile->age;
                }

                $ageArray = explode('-', $age);
                if ($ageArray[0] == "0000") {
                    $ageArray[0] = date('Y') - 30;
                }
                if ($ageArray[1] == "00") {
                    $ageArray[1] = "01";
                }
                if ($ageArray[2] == "00") {
                    $ageArray[2] = "01";
                }
                $profile->age = implode('-', $ageArray);

                $goalenddate = new DateTime($profile->age);
                $goalenddate->add(new DateInterval('P' . $profile->retirementage . 'Y'));
                $goalEnd = $goalenddate->format('Y-m-d');

                if ($goalsetting->goalpriority == 0)
                {
                    $goalsetting->goalpriority = 1;
                    $needsUpdate = true;
                }

                //need for retirement
                $userIncome = $incObj->find("user_id=:user_id", array("user_id" => $user_id));
                $rates = $susObj->findbySql("SELECT sustainablewithdrawalpercent FROM sustainablerates WHERE age=:age", array("age" => $profile->retirementage));
                $sustainableRate = isset($rates->sustainablewithdrawalpercent) ? $rates->sustainablewithdrawalpercent : 4;

                $socialIncome = 0;
                $pensionIncome = 0;
                //check pension income in asset
                $assetSocial = $assetsObj->findBySql("select sum(balance) as socialsecurityTotal from assets where user_id=:user_id and type='SS' and status=0", array("user_id" => $user_id));
                $assetPension = $assetsObj->findBySql("select sum(balance) as pensionTotal from assets where user_id=:user_id and type='PENS' and status=0", array("user_id" => $user_id));
                if ($assetSocial) {
                    $socialIncome = $assetSocial->socialsecurityTotal;
                } else if ($userIncome) {
                    $socialIncome = $userIncome->social_security;
                }
                if ($assetPension) {
                    $pensionIncome = $assetPension->pensionTotal;
                } else if ($userIncome) {
                    $pensionIncome = $userIncome->pension_income;
                }
                $totalUserIncome = ($goalsetting->monthlyincome * 12);
                $desiredIncome = ($totalUserIncome - $pensionIncome * 12 - $socialIncome * 12) / ($sustainableRate / 100);

                if($goalsetting->goalamount != Utility::amountToDB($desiredIncome)) {
                    $goalsetting->goalamount = Utility::amountToDB($desiredIncome);
                    $needsUpdate = true;
                }
                if($goalsetting->goalenddate != $goalEnd) {
                    $goalsetting->goalenddate = $goalEnd;
                    $needsUpdate = true;
                }
                break;
            case "HOUSE":
                if(isset($_POST["goalenddate"])) {
                    $goalenddate = $_POST["goalenddate"];
                    $goalsetting->goalenddate = $goalenddate;
                    $needsUpdate = true;
                }

                if(isset($_POST["permonth"])) {
                    $permonth = $_POST["permonth"];
                    $goalsetting->permonth = Utility::amountToDB($permonth);
                    $needsUpdate = true;
                }

                if(isset($_POST["downpayment"])) {
                    $downpayment = $_POST["downpayment"];
                    $goalsetting->downpayment = Utility::amountToDB($downpayment);
                    $needsUpdate = true;
                }

                //need for house
                if(isset($_POST["goalincome"])) {
                    $goalincome = $_POST["goalincome"];
                    if ($goalsetting->downpayment > 0)
                        $goalsetting->goalamount = Utility::amountToDB($goalincome * $goalsetting->downpayment / 100);
                    else
                        $goalsetting->goalamount = Utility::amountToDB($goalincome);
                    $needsUpdate = true;
                }

                break;
            case "COLLEGE":
                if(isset($_POST["goalenddate"])) {
                    $goalenddate = $_POST["goalenddate"];
                    $goalsetting->goalenddate = $goalenddate;
                    $needsUpdate = true;
                }

                if(isset($_POST["collegeyears"])) {
                    $collegeyears = $_POST["collegeyears"];
                    $goalsetting->collegeyears = $collegeyears;
                    $needsUpdate = true;
                }

                if(isset($_POST["goalincome"])) {
                    $goalincome = $_POST["goalincome"];
                    if ($goalsetting->collegeyears > 0)
                        $goalsetting->goalamount = Utility::amountToDB($goalincome * $goalsetting->collegeyears);
                    else
                        $goalsetting->goalamount = Utility::amountToDB($goalincome);
                    $needsUpdate = true;
                }
                break;
            case "DEBT":
                if(isset($_POST["goalenddate"])) {
                    $goalenddate = $_POST["goalenddate"];
                    $goalsetting->goalenddate = $goalenddate;
                    $needsUpdate = true;
                }

                if(isset($_POST["permonth"])) {
                    $permonth = $_POST["permonth"];
                    $goalsetting->permonth = Utility::amountToDB($permonth);
                    $needsUpdate = true;
                }

                if(isset($_POST["payoffdebts"])) {
                    $payoffdebts = $_POST["payoffdebts"];
                    $goalsetting->payoffdebts = $payoffdebts;
                    $needsUpdate = true;
                }

                 if ($goalsetting->goalenddate == "" || $goalsetting->goalenddate == "--") {
                    $calcXMLObj = Yii::app()->calcxml;
                    $debts = $debtsObj->findAll("user_id=:user_id and status = 0", array("user_id" => $user_id));
                    if ($debts) {
                        $valueArr = array();
                        foreach ($debts as $debt) {

                            $valueObj = new stdClass();
                            $valueObj->debtName = $debt->name;
                            $valueObj->balance = $debt->balowed;
                            $valueObj->minimum = $debt->balowed * 0.2; //2%
                            $valueObj->payment = $debt->amtpermonth;
                            $valueObj->rate = $debt->apr;

                            $valueArr[] = $valueObj;
                        }
                        $calcOutput = $calcXMLObj->restructuringDebtsAcceleratedPayoff($valueArr);

                        //calculate end date if not given

                        $goalEndD = new DateTime();
                        $calcOutput = round($calcOutput);
                        $goalEndD->add(new DateInterval('P' . $calcOutput . 'Y'));
                        $goalsetting->goalenddate = $goalEndD->format("Y-m-d");
                        $needsUpdate = true;
                    }
                }
                break;

            case "CUSTOM":
                if(isset($_POST["goalenddate"])) {
                    $goalenddate = $_POST["goalenddate"];
                    $goalsetting->goalenddate = $goalenddate;
                    $needsUpdate = true;
                }

                if(isset($_POST["permonth"])) {
                    $permonth = $_POST["permonth"];
                    $goalsetting->permonth = Utility::amountToDB($permonth);
                    $needsUpdate = true;
                }

                if(isset($_POST["goalamount"])) {
                    $goalamount = $_POST["goalamount"];
                    $goalsetting->goalamount = Utility::amountToDB($goalamount);
                    $needsUpdate = true;
                }
                break;
        }

        if($needsUpdate) {
            $goalsetting->modifiedtimestamp = date("Y-m-d H:i:s");
            $goalsetting->save();
            $this->reCalculateScore($goalsetting, "ADD", $user_id);
        }
        if ($goalsetting->goalenddate != '--' || $goalsetting->goalenddate != '') {
            $goalE = new DateTime($goalsetting->goalenddate);
            $goalendYear = $goalE->format("Y");
            $goalendMonth = $goalE->format("m");
            $goalendDay = $goalE->format("d");
        }
        $goalS = new DateTime();
        $goalstartYear = $goalS->format("Y");
        $goalstartMonth = $goalS->format("m");
        $goalstartDay = $goalS->format("d");

        $lsUserGoal = array(
            'id' => $goalsetting->id,
            'goalname' => $goalsetting->goalname,
            'goaltype' => $goalsetting->goaltype,
            'retirementage' => $retirementage,
            'goalincome' => $goalincome,
            'goalpriority' => $goalsetting->goalpriority,
            'goalamount' => $goalsetting->goalamount,
            'permonth' => $goalsetting->permonth,
            'saved' => $goalsetting->saved,
            'downpayment' => $goalsetting->downpayment,
            'collegeyears' => $goalsetting->collegeyears,
            'goalstartdate' => $goalsetting->goalstartdate,
            'goalenddate' => $goalsetting->goalenddate,
            'goalstatus' => $goalsetting->goalstatus,
            'goalstartYear' => $goalstartYear,
            'goalstartMonth' => $goalstartMonth,
            'goalstartDay' => $goalstartDay,
            'goalendYear' => $goalendYear,
            'goalendMonth' => $goalendMonth,
            'goalendDay' => $goalendDay,
            'payoffdebts' => $goalsetting->payoffdebts,
            'monthlyincome' => $goalsetting->monthlyincome,
            'goalassumptions_1' => $goalsetting->goalassumptions_1,
            'goalassumptions_2' => $goalsetting->goalassumptions_2,
            'status' => $goalsetting->status,
            'modifiedtimestamp' => $goalsetting->modifiedtimestamp
        );

        $this->sendResponse(200, CJSON::encode(array("status" => 'OK', "message" => 'Goal has been updated successfully.', "goal" => $lsUserGoal)));

    }

    /**
     *
     */
    function deleteGoal() {

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $id = isset($_POST["id"]) ? $_POST["id"] : 0;
        if ($id != 0) {
            $goalObj = new Goal();
            $goalsetting = $goalObj->findByPk($id);
            if ($goalsetting) {
                if ($user_id == $goalsetting->user_id) {
                    if($goalsetting->goalstatus != 0) {
                        $goalsetting->goalstatus = 0;
                        if ($goalsetting->save()) {
                            $this->reCalculateScore($goalsetting, "DELETE", $user_id);
                            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => "Goal deleted successfully.")));
                        } else {
                            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => "Unable to delete goal.")));
                        }
                    }
                    else
                    {
                        $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => "Goal has already been deleted.")));
                    }
                }
            }
            else
            {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "This user does not have permissions to delete this goal.")));
            }
        }
        $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => "Unable to delete goal.")));
    }

    /**
     *
     */
    function readGoal() {

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $id = isset($_POST["id"]) ? $_POST["id"] : 0;
        if ($id != 0) {
            $goalObj = new Goal();
            $goalsetting = $goalObj->findByPk($id);
            if ($goalsetting && $user_id == $goalsetting->user_id) {
                $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'goal' => $goalsetting)));
            }
        }

        $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => "Unable to read goal.")));
    }

    function reCalculateScore($goalsetting, $action, $user_id) {

        $goalObj = new Goal();
        $susObj = new Sustainablerates();
        $infoObj = new Userpersonalinfo();

        if ($goalsetting->goaltype == 'RETIREMENT') {
            $profile = $infoObj->find("user_id=:user_id", array("user_id" => $user_id));
            if (!$profile) {
                $profile = new Userpersonalinfo();
                $profile->retirementage = 65;
            } else if(!isset($profile->retirementage) || intval($profile->retirementage) <= 0) {
                $profile->retirementage = 65;
            }
            $retirementage = intval($profile->retirementage);
            unset($profile);

            $ratesDependency = new CDbCacheDependency('SELECT * FROM sustainablerates');
            $rates = $susObj->cache(QUERY_CACHE_TIMEOUT, $ratesDependency)->find('age = ' . $retirementage);

            // echo $rates->sustainablewithdrawalpercent;
            $sustainablewithdrawalpercent = isset($rates->sustainablewithdrawalpercent) ? $rates->sustainablewithdrawalpercent : 4;

            parent::setEngine();
            $this->sengine->sustainablewithdrawalpercent = $sustainablewithdrawalpercent;
            $this->sengine->lifeEC = $goalsetting->goalassumptions_2;
            $this->sengine->setLifeEC = true;
            parent::saveEngine();

            #POINT 18:
            $goalData = $goalObj->find("user_id=:user_id and goaltype IN ('RETIREMENT')", array("user_id" => $user_id));
            parent::setEngine();
            $this->sengine->retirementAmountDesired = $goalData->goalamount;
            $this->sengine->monthlyIncome = $goalData->monthlyincome;

            #POINT 31:
            if ($this->sengine->userCurrentAge < $retirementage) {
                $this->sengine->userRetirementAge = $retirementage;
            } else {
                $this->sengine->userRetirementAge = $this->sengine->userCurrentAge;
            }
            $this->sengine->yearToRetire = $this->sengine->userRetirementAge - $this->sengine->userCurrentAge;

            if ($this->sengine->spouseAge > 0) {
                $this->sengine->spouseRetAge = $this->sengine->yearToRetire + $this->sengine->spouseAge;
            } else {
                $this->sengine->spouseRetAge = 0;
            }
            parent::saveEngine();
        } else if ($goalsetting->goaltype == 'DEBT') {
            $goaldata = $goalObj->findBySql("select max(goalenddate) as goalendYear from goal where user_id=:user_id and goalstatus=1 and goaltype='DEBT'", array("user_id" => $user_id));
            parent::setEngine();
            if (isset($goaldata) && isset($goaldata->goalendYear) && $goaldata->goalendYear > 0 && $goaldata->goalendYear != '0000-00-00') {
                $this->sengine->maxGoalEndYear = $goaldata->goalendYear;
            } else {
                $this->sengine->maxGoalEndYear = 7;
            }
            parent::saveEngine();
        } else if ($goalsetting->goaltype == 'COLLEGE') {
            $college = $goalObj->findBySql("select sum(goalamount) as goalamount_sum from goal where goaltype in ('COLLEGE') and user_id=:user_id and goalstatus=1", array("user_id" => $user_id));
            parent::setEngine();
            if ($college) {
                $collegeAmount = $college->goalamount_sum;
            } else {
                $collegeAmount = 0;
            }
            $this->sengine->collegeAmount = $collegeAmount;
            parent::saveEngine();
        }

        $totalgoals = $goalObj->count("user_id=:user_id and goalstatus=1 and goalname!='Retirement Goal'", array("user_id" => $user_id));
        parent::setEngine();
        if ($totalgoals > 0) {
            $this->sengine->firstGoalEntryCheck = true;
            $this->sengine->userProfilePoints_goals = 1;
        } else {
            $this->sengine->firstGoalEntryCheck = false;
            $this->sengine->userProfilePoints_goals = 0;
        }
        parent::saveEngine();
        parent::calculateScore("GOAL|ASSET|INSURANCE|PROFILE", $user_id);
    }

}

?>
