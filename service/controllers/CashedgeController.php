<?php

/* * ********************************************************************
 * Filename: CashedgeController.php
 * Folder: controllers
 * Description:  Controller cashedge interaction
 * @author THayub Hashim (For TruGlobal Inc)
 * @copyright (c) 2012 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */
require_once(realpath(dirname(__FILE__) . '/../helpers/AccountMapping.php'));
require_once(realpath(dirname(__FILE__) . '/../helpers/Messages.php'));
#error_reporting(E_ALL);
#ini_set("display_errors", 1);

class CashedgeController extends Scontroller {

    // Define access control
    public function accessRules() {
        return array_merge(
                array(array('allow', 'users' => array('?'))),
                // Include parent access rules
                parent::accessRules()
        );
    }

    /* This is to be used to pupulate the notification table.Ideally it should be in Meta table */

    public $mess1           =   "Please retry your login credentials for this financial institution.";
    public $mess2           =   "No new accounts were found for this user at the specified financial institution.";
    public $mess3           =   "Please wait a few minutes for the download to complete.";
    public $mess4           =   "Accounts have been added successfully.";
    public $mess5           =   "Accounts have been refreshed successfully.";
    public $mess6           =   "We are unable to add your account at this time. Please try again later.";
    public $mess7           =   "We are unable to update your account at this time. Please try again later.";
    public $mess8           =   "The accounts have been classified & added successfully!";
    public $mess9           =   "Please wait a few minutes for the download to complete before trying to refresh.";
    public $mess10          =   "Please answer the security question to refresh your accounts.";
    public $mess11          =   "Almost done! Please tell us what type of accounts these are.";
    public $mess12          =   "Almost done! Please answer the security question(s) for this financial institution.";


    public $temp1        =   'connect_mfa';
    public $temp2        =   'connect_error';
    public $temp3        =   'refresh';
    public $temp4        =   'info';
    public $temp5        =   'connect_retry';


    /*                          */
    public function actionAddupdatecashedgeuser() {
        try {
            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            $user_id = Yii::app()->getSession()->get('wsuser')->id;

            $cashedge = CashedgeAccount::model()->find("user_id = :user_id", array("user_id" => $user_id));

            if (!$cashedge) {
                $cashedge = new CashedgeAccount();

                $cashedgeComp = Yii::app()->cashedge;
                $userObj = new SUser();
                $userFound = $userObj->findByPk($user_id);

                $userObject = new stdClass();
                $userObject->username = $userFound->name;
                $userObject->password = $cashedgeComp->generatePassword();
                $userObject->lastname = $userFound->name;
                $userObject->firstname = $userFound->name;
                $userObject->middlename = $userFound->name;
                $userObject->email = $userFound->email;

                $CEUserID = $cashedgeComp->createUser($userObject);
                $cashedge->user_id = $user_id;
                $cashedge->username = $userObject->username;
                $cashedge->cpassword = $userObject->password;
                $cashedge->ceuserid = $CEUserID;
                $cashedge->cutype = "Customer";
                $cashedge->save();
            } else {
                $CEUserID = $cashedge->ceuserid;
            }

            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "CEUserID" => $CEUserID)));
        } catch (Exception $E) {
            //general error message send to UI and log the error to logs
        }
    }
####################################################################################################################################
    /*                          */
    public function actionUpdatecashedgefipriority() {
        if(!empty($_GET["fiid"])) {

            $keyword = $_GET["fiid"];

            $cashedgefiDetailsObj = new CashedgefiDetails();
            $q = new CDbCriteria();
            $q->condition = "FIId = " . $keyword;
            $accountDetails = $cashedgefiDetailsObj->find($q);
            if($accountDetails) {
                $accountDetails->priority += 1;
                $accountDetails->save();
                $this->sendResponse(200, CJSON::encode(array("status" => 'OK')));
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => 'ERROR', "message" => "Could not update priority.")));
            }
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => 'ERROR', "message" => "Could not update priority.")));
        }
    }
####################################################################################################################################
    /*                          */
    public function actionSearchfidetails() {
        try {
            if (!empty($_GET["finame"])) {

                $keyword = trim($_GET["finame"]);
                $totalResultsCount = 0;
                $totalRecords = array();
                $totalRecordsIdHash = array();
                $firesults = array();

                $searchtermObj = new CashedgeSearchTerm();
                $runCashEdgeSearch = false;
                $searchterm = $searchtermObj->find(array('condition' => "searchterm = :term", 'params' => array('term' => $keyword)));
                if (!$searchterm) {
                    $searchterm = new CashedgeSearchTerm();
                    $searchterm->searchterm = $keyword;
                    $searchterm->modified = new CDbExpression('NOW()');
                    $runCashEdgeSearch = true;
                } else {
                    $modified = new DateTime($searchterm->modified);
                    $interval = $modified->diff(new DateTime());
                    if ($interval->days > 30) {
                        $searchterm->modified = new CDbExpression('NOW()');
                        $runCashEdgeSearch = true;
                    }

                    if($searchterm->firesults) {
                        $cashedgefiDetailsObj = new CashedgefiDetails();
                        $q = new CDbCriteria();
                        $q->condition = "FIId in (" . $searchterm->firesults . ") order by priority desc, finame asc";

                        $accountDetails = $cashedgefiDetailsObj->findAll($q);
                        if (isset($accountDetails) && !empty($accountDetails)) {
                            foreach ($accountDetails as $row) {
                                if(!isset($totalRecordsIdHash[$row->FIId])) {
                                    $firesults[] = $row->FIId;
                                    $totalRecordsIdHash[$row->FIId] = true;
                                    if($totalResultsCount < 10) {
                                        $eachRecord = array(
                                            "serviceId" => $row->FIId,
                                            "displayName" => $row->FIName,
                                            "country" => $row->Country,
                                            "URL" => json_decode($row->URL),
                                            "accountSupported" => json_decode($row->AccountSupported),
                                            "loginParams" => json_decode($row->FILoginParametersInfo)
                                        );
                                        $totalRecords[] = $eachRecord;
                                        $totalResultsCount++;
                                    }
                                }
                            }
                        }
                    }
                }

                $totalResultsCount = count($totalRecords);
                if($runCashEdgeSearch) {
                    //call the cashedge and update to local database if found
                    $cashedgeObject = Yii::app()->cashedge;
                    $fiList = $cashedgeObject->searchFIs(str_replace("&", "&amp;", $keyword));
                    if (isset($fiList) && !empty($fiList)) {
                        //create database object
                        foreach ($fiList as $fi) {
                            try {
                                if(!isset($totalRecordsIdHash[$fi["fiid"]])) {
                                    $firesults[] = $fi["fiid"];
                                    $totalRecordsIdHash[$fi["fiid"]] = true;
                                    if($totalResultsCount < 10) {
                                        $eachRecord = array(
                                            "serviceId" => $fi["fiid"],
                                            "displayName" => $fi["fiName"],
                                            "country" => $fi["fiCountry"],
                                            "URL" => $fi["fiURL"],
                                            "accountSupported" => $fi["fiACC"],
                                            "loginParams" => $fi["fiLoginParams"]
                                        );
                                        $totalRecords[] = $eachRecord;
                                        $totalResultsCount++;
                                    }
                                }

                                $cashedgefiDetailsObj = new CashedgefiDetails();
                                $newCashedgefiDetailsObj = $cashedgefiDetailsObj->find("FIId = :fiid", array("fiid" => $fi["fiid"]));
                                if(!$newCashedgefiDetailsObj) {
                                    $newCashedgefiDetailsObj = new CashedgefiDetails();
                                    $newCashedgefiDetailsObj->isNewRecord = true;
                                }
                                $newCashedgefiDetailsObj->FIName = $fi["fiName"];
                                $newCashedgefiDetailsObj->FIId = $fi["fiid"];
                                $newCashedgefiDetailsObj->Country = $fi["fiCountry"];
                                $newCashedgefiDetailsObj->URL = json_encode($fi["fiURL"]);
                                $newCashedgefiDetailsObj->AccountSupported = json_encode($fi["fiACC"]);
                                $newCashedgefiDetailsObj->FILoginParametersInfo = json_encode($fi["fiLoginParams"]);
                                $newCashedgefiDetailsObj->save();
                            } catch (Exception $e) {
                            }
                        }
                    }
                    $firesults = implode(",", $firesults);
                    $searchterm->firesults = $firesults;
                    $searchterm->save();
                }
                $totalRecords = array_splice($totalRecords, 0, 10);
                $totalResultsCount = count($totalRecords);
                $this->sendResponse(200, CJSON::encode(array("status" => 'OK', "totalRecords" => $totalResultsCount, "items" => $totalRecords)));
            }
            $this->sendResponse(200, CJSON::encode(array("status" => 'ERROR', "message" => "Could not complete search at this time.", "totalRecords" => false, "items" => array())));
        } catch (Exception $E) {
            //general error message send to UI and log the error to logs
        }
    }
####################################################################################################################################
    /*                          */
    public function actionStorefidetails() {

        try {
            //call the cashedge and update to local database if found
            $cashedgeObject = Yii::app()->cashedge;
            $endDate = date('Y-m-d');
            $startDate = date("Y-m-d", strtotime("yesterday"));

            $fiList = $cashedgeObject->getFIByDate($startDate, $endDate);

            $totalResultsCount = 0;
            if (isset($fiList) && !empty($fiList)) {
                $totalRecords = null;
                $totalResultsCount = count($fiList);
                //create database object
                foreach ($fiList as $fi) {
                    $newCashedgefiDetailsObj = new CashedgefiDetails();
                    $newCashedgefiDetailsObj->isNewRecord = true;
                    $newCashedgefiDetailsObj->FIName = $fi["fiName"];
                    $newCashedgefiDetailsObj->FIId = $fi["fiid"];
                    $newCashedgefiDetailsObj->Country = $fi["fiCountry"];
                    $newCashedgefiDetailsObj->URL = json_encode($fi["fiURL"]);
                    $newCashedgefiDetailsObj->AccountSupported = json_encode($fi["fiACC"]);
                    $newCashedgefiDetailsObj->FILoginParametersInfo = json_encode($fi["fiLoginParams"]);
                    $newCashedgefiDetailsObj->save();

                    $eachRecord = array(
                        "serviceId" => $fi["fiid"],
                        "displayName" => $fi["fiName"],
                        "country" => $fi["fiCountry"],
                        "URL" => $fi["fiURL"],
                        "accountSupported" => $fi["fiACC"],
                        "loginParams" => $fi["fiLoginParams"]
                    );
                    $totalRecords[] = $eachRecord;
                }
                //$this->sendResponse(200, CJSON::encode(array("totalRecords" => $totalResultsCount, "items" => $totalRecords)));
            }
            //$this->sendResponse(200, CJSON::encode(array("totalRecords" => false, "items" => array())));
        } catch (Exception $E) {
            //general error message send to UI and log the error to logs
        }
    }
####################################################################################################################################
    /*                          */
    public function actionAddfiitem(){
        try{

            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            $user_id = Yii::app()->getSession()->get('wsuser')->id;

            /* get the content service id from the UI */
            $contentServiceId = $_POST["serviceid"];
            $fiName = $_POST["finame"];

            $deletedItems = array();
            $cashedgeItem = new CashedgeItem();
            $existsAcc = $cashedgeItem->findAll("filoginacctid = 0 AND fiid = :fiid AND user_id=:userid",array("fiid" => $contentServiceId, "userid"=>$user_id));
            if (isset($existsAcc) && !empty($existsAcc)){
                foreach($existsAcc as $account) {
                    $deletedItems[] = $account->id;
                    $account->status = 1;
                    $account->save();
                    // TODO: Switch this to soft deletes so we have historical data.
                }
            }

            /* Creating a new row in CashEdgeItem tables                ----     New Workflow change    */
            $newRow = new CashedgeItem();

            /*  Creating basic details in the new row  */
            $newRow->user_id = $user_id;
            $newRow->name = $fiName;
            $newRow->Fiid = $contentServiceId;
            $newRow->lsstatus = 0;
            $newRow->lsupdate = 0;
            $newRow->save();

            $msg = array('status' => 'OK', 'loginacctid' => $newRow->id, 'deletedItems' => $deletedItems, 'flag' => 0,
                        'message' => 'It will only take about a minute to download your account details for the first time.
                         Please continue to connect other accounts and complete your profile. You will receive a notification when the process is done.');

            /*  Sending the RowID to the user after the row creation    ----     New Workflow change        */
            $this->sendResponse(200, CJSON::encode($msg));

        }catch (Exception $E) {
            //general error message send to UI and log the error to logs
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => $E->getMessage())));
        }
    }
####################################################################################################################################
    public function actionRetryaccount() {
        try {
            /*  Parameters required     */
            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            $user_id = Yii::app()->getSession()->get('wsuser')->id;

            /*  RowID of the CashEdge Item table */
            $rowID = isset($_POST["cid"]) ? $_POST["cid"] : $cid;

            /* Error Handdling for $rowID */
            if (!($rowID) || ($rowID == 0)){
                $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'There is no ID')));
            }

            /*  Fetching the row details    */
            $ceRow = CashedgeItem::model()->find("id = :rid",array("rid" => $rowID));

            $data = $_POST;

            /* send only required params to cashedge & formulating the FIELDS TO CASHEDGE */
            $notRequiredParams = array("cid","flag","serviceid", "finame", "finame" . $data['serviceid']);
            $wsFieldsToCashedge = array();
            foreach ($data as $key => $value) {
                if (!in_array($key, $notRequiredParams)) {
                    $wsFieldsToCashedge[$key] = $value;
                }
            }

            /*  Checking if this user already has a login in CashEdge, if not creating a new one for this asami  */
            $cashedgeObject = Yii::app()->cashedge;
            $cashedgeUserDetails = CashedgeAccount::model()->find("user_id = :user_id", array("user_id" => $user_id));

            /* If user is not present add the current user to cashedge */
            if (!$cashedgeUserDetails) {

                $cashedgeUserDetails = new CashedgeAccount();
                $userFound = User::model()->find("id = :user_id",array('user_id' => $user_id));

                /*  Creating the object to be sent to CE for user creation  */
                $userObject = new stdClass();
                $cePrefix = Yii::app()->params->cePrefix;
                $userObject->username = $cePrefix . str_replace('+', 'fsplus', $userFound->email);

                $userObject->password = $cashedgeObject->generatePassword();
                $userObject->lastname = $userFound->email;
                $userObject->firstname = $userFound->email;
                $userObject->middlename = $userFound->email;
                $userObject->email = $userFound->email;

                /* Creating the user in CashEdge system */
                $CEUserID = $cashedgeObject->createUser($userObject);

                if (!is_array($CEUserID)) {
                    $cashedgeUserDetails->user_id = $user_id;
                    $cashedgeUserDetails->username = $userObject->username;
                    $cashedgeUserDetails->cpassword = $userObject->password;
                    $cashedgeUserDetails->ceuserid = $CEUserID;
                    $cashedgeUserDetails->cutype = "Customer";
                    $cashedgeUserDetails->save();
                } else {
                    $this->sendResponse(200, CJSON::encode($CEUserID));
                }
            }

            /* get the cashedge session ,login and store the ce sess info from cashedge ,for futher use */
            $details = $cashedgeObject->getSignonRq($cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);

            /* update the database with the session for the user  */
            $cashedgeUserDetails->sesinfo = $details["session"];
            $cashedgeUserDetails->save();

            /* Conditions to be checked for the previous row to be over written or a new one to be added  or delete the old row     */

            // Incorrect username and password : 301 error
            // ---------------------------------------------
            $userFailedAttempt = CashedgeItem::model()->find("user_id = :user_id AND Fiid = :fid AND lsstatus = 100 and status = 0",array("user_id" => $user_id , "fid" => $data['serviceid']));
            if($userFailedAttempt){
                /* API to update new credentials to CE with FILoginAcctId */
                $updateInfo = $cashedgeObject->updateNewFILoginInfo($cashedgeUserDetails->username, $cashedgeUserDetails->cpassword,$userFailedAttempt->FILoginAcctId,$wsFieldsToCashedge);
                $updateStatus = $updateInfo["msg"];

                if ($updateStatus == 'Success'){
                /* HarvestAddRq + AddMoreAccts */

                /*  Update the new row with the values and Delete the old row and continue the call*/
                $newAddAccts = $cashedgeObject->addMFAToAccountsLater($userFailedAttempt->FILoginAcctId,$cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
                $ceRow->HarvestAddID = $newAddAccts["harvestAddId"];
                $ceRow->FILoginAcctId = $userFailedAttempt->FILoginAcctId;
                $ceRow->Fiid = $userFailedAttempt->Fiid;
                $ceRow->RunId = $newAddAccts["harvestId"];
                $ceRow->AcctHarvestStatus = $userFailedAttempt->AcctHarvestStatus;
                $ceRow->ClassifiedStatus = $userFailedAttempt->ClassifiedStatus;
                $ceRow->accountdetails = '';
                $ceRow->lsstatus = 3;
                $ceRow->save();

                $userFailedAttempt->status = 1;
                $userFailedAttempt->save();

                /* Updating the notification rowID*/

                self::notificationsFn($ceRow->FILoginAcctId, null, null, null, null, $ceRow->id, $user_id);

                /* Update the DB with the new fresh values */
                    /*$newAddAccts = $cashedgeObject->addMFAToAccountsLater($userFailedAttempt->FILoginAcctId,$cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
                    $userFailedAttempt->HarvestAddID = $newAddAccts["harvestAddId"];
                    $userFailedAttempt->RunId = $newAddAccts["harvestId"];
                    $userFailedAttempt->accountdetails = '';
                    $userFailedAttempt->lsstatus = 3;
                    $userFailedAttempt->save(); */

                    /* Calling actionRetryaccountce  */
                    self::actionRetryaccountce($ceRow->id,0);
                }else{
                    // Send a generic error message asking them to try later, and infrom CE response team
                    // ce logging also has to happen
                    $msg = array('status' => 'ERROR' ,'loginacctid' => $userFailedAttempt->FILoginAcctId,'flag' => 0, 'message' => 'We could not add your account at this time. Please try again later.');
                }
            }
            /* Garbage values : 4460 error */
            $invalidCred = CashedgeItem::model()->find("user_id = :user_id AND Fiid = :fid AND lsstatus = 110",array("user_id" => $user_id , "fid" => $data['serviceid']));
            if ($invalidCred){
                $lsOutputRes = $cashedgeObject->addAccountToUser($data['serviceid'], $wsFieldsToCashedge, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
            }else if (!$invalidCred) {
                /*  NORMAL FLOW     */
                $lsOutputRes = $cashedgeObject->addAccountToUser($data['serviceid'], $wsFieldsToCashedge, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
            }
            if (isset($lsOutputRes) && $lsOutputRes["status"] == 1 ) {

                    $lsAddedResults = $lsOutputRes["details"];
                    $ceRow->RunId = $lsAddedResults->RunId;
                    $ceRow->user_id = $user_id;
                    $ceRow->name = $data['finame'];
                    $ceRow->Fiid = $data['serviceid'];
                    $ceRow->HarvestAddID = $lsAddedResults->HarvestAddID;
                    $ceRow->FILoginAcctId = $lsAddedResults->FILoginAcctId;
                    $ceRow->AcctHarvestStatus = $lsAddedResults->AcctHarvestStatus;
                    $ceRow->ClassifiedStatus = $lsAddedResults->ClassifiedStatus;
                    $ceRow->accountdetails = '';
                    $ceRow->lsstatus = 3;
                    $ceRow->save();
                    #$invalidCred->status = 1;
                    #$invalidCred->save();

                    /*  Updating the FILoginAcctID to the notification table*/
                       /*  Creating a new row in notifications */

                    $info = array(
                            'fid' => $data['serviceid'],
                            'finame' => $data['finame']
                            );

                    self::notificationsFn($ceRow->FILoginAcctId, null, null, $this->temp4, 1, $rowID, $user_id, json_encode($info));

                    /*  Call actionretryaccountce fn */
                    self::actionRetryaccountce($ceRow->id,0);
            }
            elseif (isset($lsOutputRes['code']) && ($lsOutputRes['code'] == 4300)) {
                if (isset($lsOutputRes['id']) && $lsOutputRes['id'] != 4300){

                        $exists_Cond = CashedgeItem::model()->find("FILoginAcctId=:FILoginAcctId",array('FILoginAcctId'=>$lsOutputRes['id']));

                        if (isset($exists_Cond)){

                            /* Update the username and password with the recent one the user has provided */
                            $updateInfo = $cashedgeObject->updateNewFILoginInfo($cashedgeUserDetails->username, $cashedgeUserDetails->cpassword,$exists_Cond->FILoginAcctId,$wsFieldsToCashedge);
                            /* Continue the flow */
                            $ceRow->Fiid = $exists_Cond->Fiid;
                            $ceRow->FILoginAcctId = $exists_Cond->FILoginAcctId;
                            $ceRow->AcctHarvestStatus = $exists_Cond->AcctHarvestStatus;
                            $ceRow->ClassifiedStatus = $exists_Cond->ClassifiedStatus;
                            $ceRow->save();

                            self::actionRetryaccountce($ceRow->id,1);
                        }else{
                            /*  Continuing the flow */
                            /*  So do nothing */
                        }
                }
            }
            /* Garbage values */
            elseif (isset($lsOutputRes['code']) && ($lsOutputRes['code'] == 4460)) {

                    /* DB entry in Cashedgeitem needs to be done */
                        $ceRow->user_id = $user_id;
                        $ceRow->name = $data['finame'];
                        $ceRow->Fiid = $data['serviceid'];
                        $ceRow->lsstatus = 110;
                        $cerow->accountdetails = serialize($lsOutputRes);
                        $ceRow->save();

                     $msg = array('status' => 'OK', 'loginacctid' => $ceRow->id, 'flag' => 0,
                        'message' => "The credentials you have entered exceeds your financial institution's max length. Please try again.");
                    $this->sendResponse(200, CJSON::encode($msg));
            }
            $this->sendResponse(200, CJSON::encode($lsOutputRes));
        }catch (Exception $E) {
        /* general error message send to UI and log the error to logs */
        $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => $E->getMessage())));
    }
}
####################################################################################################################################
    /*                          */
    public function actionRetryaccountce($cid = 0, $flag = 0) {
        try {
            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
            $fid = $cid;

            if ($fid == "") {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Unique ID missing.")));
            }

            $cashedgeItemDetails = CashedgeItem::model()->find("id=:id", array("id" => $fid));

            if (!isset($cashedgeItemDetails)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Invalid request.")));
            }
            /* Get all the details from the UI */
            $cashedgeObject = Yii::app()->cashedge;
            $cashedgeUserDetails = CashedgeAccount::model()->find("user_id = :user_id", array("user_id" => $user_id));

            if ((isset($flag)) && $flag == 1) {
                /* Getting the new harvest ID and Run ID for the existing user with same FILoginAcctID */

                $initiateDetailsDeleted = $cashedgeObject->addMFAToAccountsLater($cashedgeItemDetails->FILoginAcctId, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
                $cashedgeItemDetails->HarvestAddID = $initiateDetailsDeleted["harvestAddId"];
                $cashedgeItemDetails->RunId = $initiateDetailsDeleted["harvestId"];
                $cashedgeItemDetails->save();
            }

            /* getAddAccountStatus API is called */
            do {
                $initiateDetails = $cashedgeObject->checkAccountStatus($cashedgeItemDetails->RunId, $cashedgeItemDetails->HarvestAddID, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
            } while ($initiateDetails["status"] == "InProgress");

            Yii::trace("Time:" . date('r'));
            Yii::trace(":API call:" . "RESPONSE OF getAddAccountStatus API CALL in CONTROLLER", "cashedgedata");
            Yii::trace(":Type:" . gettype($initiateDetails), "cashedgedata");
            Yii::trace(":Data:" . CVarDumper::dumpAsString($initiateDetails), "cashedgedata");
            Yii::trace("END ==============================", "cashedgedata");

            /* Login Credentials do not match and CE returns error: */
            /* THAYUB: have to capture general errors also in this */
            if ($initiateDetails["status"] == 'ERROR'){
                $cashedgeItemDetails->lsstatus = 200;
                $cashedgeItemDetails->accountdetails = serialize($initiateDetails);
                $cashedgeItemDetails->save();

                $info_msg = (($initiateDetails["msg"] != '') ? $initiateDetails["msg"] : "We could not add your account at this time. Please try again later.");
                /*  Wrong username / password combo notification*/
                self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'LOGIN', $this->mess1, $this->temp5, 0, $cashedgeItemDetails->id, $user_id);

                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "reenter" => "1", "fiid" => $cashedgeItemDetails->Fiid, "message" => $info_msg, "info" => 1 )));
            }
            if ($initiateDetails["status"] == 'LOGIN') {
                $cashedgeItemDetails->lsstatus = 100;
                $cashedgeItemDetails->accountdetails = serialize($initiateDetails);
                $cashedgeItemDetails->save();

                /*  Wrong username / password combo notification*/
                self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'LOGIN', $this->mess1, $this->temp5, 0, $cashedgeItemDetails->id, $user_id);

                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "reenter" => "1", "fiid" => $cashedgeItemDetails->Fiid, "message" => "We could not add your account at this time. Please try again later." )));
            }
            /* The GETADDACCOUNTSTATUS call is complete */
            if ($initiateDetails["status"] == 'OK') {
                $cashedgeItemDetails->lsstatus = 4;
                $cashedgeItemDetails->save();

                /* getNewAccounts API is called */
                $newAccountDetails = $cashedgeObject->getNewAccounts($cashedgeItemDetails->RunId, $cashedgeItemDetails->HarvestAddID, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);

                if (isset($newAccountDetails["createdAccounts"]) || isset($newAccountDetails["pendingAccounts"])) {
                    $cashedgeItemDetails->lsstatus = 5;
                    $cashedgeItemDetails->accountpending = isset($newAccountDetails["pendingAccounts"]) ? $newAccountDetails["pendingAccounts"] : 0;
                    $cashedgeItemDetails->save();
                }
                /* No new accounts available for FLAG == 1 */
                /* User tries this flow for existing accounts */
                if (isset($newAccountDetails["code"]) && $newAccountDetails["code"] == '4450') {
                    $cashedgeItemDetails->lsstatus = 8;
                    $cashedgeItemDetails->save();

                    /*  If there are no new accounts notification*/
                    self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'LOGIN', $this->mess2, $this->temp4, 0, $cashedgeItemDetails->id, $user_id);
                    $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => "No new accounts were found for this user at the specified financial institution.", "loginacctid" => $fid, "nonew" => '1')));

                    /* THAYUB - Need to add CE LOG HERE. */
                }
                if ($flag == 1) {
                    /* The account already exists and we need to check for the accounts that are not in our DB */
                    $accountNumberArray = array();

                    /* Pending Accounts */
                    if (isset($newAccountDetails["pendingAccounts"]) && !empty($newAccountDetails["pendingAccounts"])) {
                        $xml = new DOMDocument();
                        $xml->loadXML("<PendingAcc>" . $newAccountDetails["pendingAccounts"] . "</PendingAcc>");
                        $accountsNode = $xml->getElementsByTagName("HarvestAddFetchAcct");
                        if ($accountsNode->length > 0) {
                            foreach ($accountsNode as $account) {
                                $eachPendingAccount["AcctNumber"] = $account->getElementsByTagName("AcctNumber")->item(0)->nodeValue;
                                $accountNumberArray[] = $eachPendingAccount["AcctNumber"];
                            }
                        }
                    }

                    /* Created Accounts */
                    if (isset($newAccountDetails["createdAccounts"]) && !empty($newAccountDetails["createdAccounts"])) {
                        $xml1 = new DOMDocument();
                        $xml1->loadXML("<CreatedAcc>" . $newAccountDetails["createdAccounts"] . "</CreatedAcc>");
                        $accountsNode1 = $xml1->getElementsByTagName("HarvestAddFetchAcct");
                        if ($accountsNode1->length > 0) {
                            foreach ($accountsNode1 as $account) {
                                $eachCreatedAccount["AcctNumber"] = $account->getElementsByTagName("AcctNumber")->item(0)->nodeValue;
                                $accountNumberArray[] = $eachCreatedAccount["AcctNumber"];
                            }
                        }
                    }
                    Yii::trace(CVarDumper::dumpAsString($accountNumberArray) . "FLAG 1 : Account Numbers", "cashedgedata");
                }
                Yii::trace(CVarDumper::dumpAsString($newAccountDetails) . "-->5 getNewAccounts response", "cashedgedata");
                /* SEPERATE WORK FLOW FOR PENDING ACCOUNTS AS THEY NEED TO BE CLASSFIFIED */
                $pendingAccountsArr = array();
                if (isset($newAccountDetails["pendingAccounts"]) && !empty($newAccountDetails["pendingAccounts"])) {
                    $xml = new DOMDocument();
                    $xml->loadXML("<PendingAcc>" . $newAccountDetails["pendingAccounts"] . "</PendingAcc>");
                    $accountsNode = $xml->getElementsByTagName("HarvestAddFetchAcct");

                    if ($accountsNode->length > 0) {
                        /* Iterate through accounts */
                        $counter = 1;
                        foreach ($accountsNode as $account) {

                            $eachAccount = array();

                            $eachAccount["counter"] = $counter;
                            $eachAccount["Fiid"] = $account->getElementsByTagName("FIId")->item(0)->nodeValue;
                            $eachAccount["flag"] = 0;
                            /* Get all the accounts supported */
                            $accountsSupported = CashedgefiDetails::model()->find("FIId=:fiid", array("fiid" => $eachAccount["Fiid"]));

                            /* account supported */
                            if ($accountsSupported) {
                                $eachAccount["accountsSupported"] = json_decode($accountsSupported->AccountSupported);
                            }

                            $eachAccount["AcctNumber"] = $account->getElementsByTagName("AcctNumber")->item(0)->nodeValue;
                            if ($account->getElementsByTagName("FIAcctName")->length > 0) {
                                $fiiAcc = $account->getElementsByTagName("FIAcctName")->item(0);
                                $eachAccount["FIAcctNameParamName"] = $fiiAcc->getElementsByTagName("ParamName")->item(0)->nodeValue;
                                $eachAccount["FIAcctNameParamVal"] = $fiiAcc->getElementsByTagName("ParamVal")->item(0)->nodeValue;
                            }
                            $fiiAccBal = $account->getElementsByTagName("AcctBal");
                            if ($fiiAccBal->length > 0) {
                                foreach ($fiiAccBal as $accBal) {

                                    $accountBal = array();

                                    $accountBal["AccBalType"] = $accBal->getElementsByTagName("BalType")->item(0)->nodeValue;
                                    $curAmtNode = $accBal->getElementsByTagName("CurAmt")->item(0);
                                    $accountBal["CurAmt"] = $curAmtNode->getElementsByTagName("Amt")->item(0)->nodeValue;
                                    $accountBal["CurCode"] = $curAmtNode->getElementsByTagName("CurCode")->item(0)->nodeValue;

                                    $eachAccount["AccBal"] = $accountBal;
                                }
                            } else {
                                $accountBal = array();
                                $accountBal["CurAmt"] = 0;
                                $eachAccount["AccBal"] = $accountBal;
                            }
                            $pendingAccountsArr[] = $eachAccount;
                            $counter++;
                        }
                    }
                }
                /* Work flow for created accounts if they are set, the API calls continue */
                if (isset($newAccountDetails["createdAccounts"]) && !empty($newAccountDetails["createdAccounts"])) {
                    /* This is the XML list of all the accounts returned */
                    $allAccountsList = $newAccountDetails["createdAccounts"];
                    Yii::trace(CVarDumper::dumpAsString($newAccountDetails) . "-->TESTTEST", "cashedgedata");

                    /* createAccounts API is called */
                    $createAccountAPI = $cashedgeObject->createAccounts($cashedgeItemDetails->RunId, $cashedgeItemDetails->HarvestAddID, $allAccountsList, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
                    $details["accounts"] = isset($createAccountAPI["accounts"]) ? $createAccountAPI["accounts"] : 0;
                    $details["status"] = "OK";

                    /* Created accounts check */
                    Yii::trace(":Data: AFTER CREATE ACCOUNTS IN CONTROLLER" . CVarDumper::dumpAsString($createAccountAPI), "cashedgedata");
                    Yii::trace("-->-6 BEFORE additemtodb ", "cashedgedata");

                    if ($details["accounts"] != 0) {
                        $connectedAccounts = self::addItemToDB($details, $cashedgeItemDetails->id, $user_id);
                    }

                    Yii::trace(CVarDumper::dumpAsString($details) . "-->6 createAccounts response", "cashedgedata");

                    if ($details["status"] == "OK") {
                        $cashedgeItemDetails->lsstatus = 6;
                        $cashedgeItemDetails->save();
                    }

                    /* updateAccounts API is called: */

                    $updateAccountAPI = $cashedgeObject->updateAccounts($cashedgeItemDetails->FILoginAcctId, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
                    Yii::trace(CVarDumper::dumpAsString($updateAccountAPI) . "-->7 updateAccounts response", "cashedgedata");
                    $updateAccountHarvestId = $updateAccountAPI["newharvestid"];
                    $updateAccountRunId = $updateAccountAPI["newrunid"];


                    if (isset($updateAccountRunId) && isset($updateAccountHarvestId)) {
                        $cashedgeItemDetails->lsstatus = 7;
                        $cashedgeItemDetails->save();
                    }

                    /* getHarvestStatus API is called: */
                    $accHarvestAPI = $cashedgeObject->getHarvestStatus($cashedgeUserDetails->username, $cashedgeUserDetails->cpassword, $updateAccountHarvestId, $updateAccountRunId);

                    Yii::trace(CVarDumper::dumpAsString($accHarvestAPI) . "-->8 getHarvestStatus response", "cashedgedata");

                    if ($accHarvestAPI["status"] == "OK") {
                        $cashedgeItemDetails->lsstatus = 8;
                        $cashedgeItemDetails->msg = "Your accounts have been added successfully.";
                        # Some Logging has to happen here.
                        #THAYUB
                        $cashedgeItemDetails->RunId = $updateAccountRunId;
                        $cashedgeItemDetails->HarvestAddID = $updateAccountHarvestId;
                        $cashedgeItemDetails->save();
                        Yii::trace($accHarvestAPI["status"] . "-->8 getHarvestStatus response", "cashedgedata");

                        /* Getting the INV unique Ids from the Financial Informtaion: */
                        $AcctIDs = CashedgefiDetails::model()->find("FIId=:fid", array("fid" => $cashedgeItemDetails->Fiid));
                        $accountsSupportedbyFI = isset($AcctIDs->AccountSupported) ? json_decode($AcctIDs->AccountSupported) : 0;

                        if ($accountsSupportedbyFI != 0) {
                            foreach ($accountsSupportedbyFI as $eachAcctInfo) {
                                if ($eachAcctInfo->AcctType == "INV") {
                                    $invAcctInfo = $eachAcctInfo;
                                }
                            }
                        }

                        if (isset($invAcctInfo)) {

                            $invClass = Assets::model()->findAllBySql("SELECT * FROM assets WHERE user_id=:user_id AND FILoginAcctId=:FILoginAcctId AND accttype LIKE '%INV%' AND classified = 0 ", array('user_id' => $user_id, 'FILoginAcctId' => $fid));

                            if (isset($invClass)) {

                                /* Check for setting the counter value */
                                $counterNow = isset($pendingAccountsArr) ? (count($pendingAccountsArr) + 1) : 1;

                                foreach ($invClass as $eachINV) {
                                    /* Creating the array object like pending accounts for classification */
                                    $invArray = array();
                                    $otlt = Otlt::model()->findAllBySql("SELECT name FROM otlt WHERE description IN ('INV')");
                                    $AccountsSupportedArray = array();
                                    /* Making it the same as pending accounts array */
                                    foreach ($otlt as $eachOtltVal) {

                                        $eachOtlt = explode('&', $eachOtltVal->name);
                                        $eachINVInfo = new StdClass();
                                        $eachINVInfo->AcctName = $eachOtlt[1];
                                        $eachINVInfo->AcctTypeId = $eachOtlt[0];
                                        $eachINVInfo->AcctGroup = $eachOtlt[2];
                                        $eachINVInfo->AcctType = 'INV';
                                        $eachINVInfo->ExtAcctType = 'INV';
                                        $AccountsSupportedArray[] = $eachINVInfo;
                                    }

                                    $actIDINV = new stdClass();
                                    $actIDINV->CurCode = $eachINV->actid;
                                    $invArray["counter"] = $counterNow++;
                                    $invArray["Fiid"] = $cashedgeItemDetails->Fiid;
                                    $invArray["AcctNumber"] = $invAcctInfo->AcctTypeId;
                                    $invArray["FIAcctNameParamName"] = '';
                                    $invArray["FIAcctNameParamVal"] = $eachINV->name;
                                    $invArray["AccBal"] = $actIDINV;
                                    $invArray["accountsSupported"] = $AccountsSupportedArray;
                                    $invArray["flag"] = 1;
                                    $pendingAccountsArr[] = $invArray;
                                }
                            }
                        }
                    } else {
                        /*
                        self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'HARVEST', $this->mess3, $this->temp4, 0);
                        */
                    }
                }

                /* Saving the pending accounts + Inv classification in the DB for further use by API */
                if (empty($pendingAccountsArr)) {
                    $cashedgeItemDetails->accountpending = 0;

                        /* Successful Notification if there are no pending accounts: */
                        self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'CONNECT', $this->mess4, $this->temp4, 0, $cashedgeItemDetails->id, $user_id);

                        /*  Checking if there is another row with the same FiLoginAcctID and row ID greater than the current row */
                        /*  New model agreed by Melroy and Jeff */

/**** Melroy - Question: When did i agree to this? We agreed to anything with a lower id gets deleted, not the higher ones. Since the higher ones are the newer ones.
               Question: Where is the initialization of this notification object? Also why would it replace the above notification?
*****/
                        $existsAcc = CashedgeItem::model()->find("id > :id AND FILoginAcctId = :FILoginAcctId",array("id"=>$cashedgeItemDetails->id , "FILoginAcctId" => $cashedgeItemDetails->FILoginAcctId));
                        if (isset($existsAcc)){
                            $existsAcc->status = 1;
                            $existsAcc->save();
                            self::notificationsFn($cashedgeItemDetails->FILoginAcctId, null, null, null, 1, $cashedgeItemDetails->id, $user_id);
                        }


                    } else {
                        $cashedgeItemDetails->accountpending = serialize($pendingAccountsArr);
                        $cashedgeItemDetails->lsstatus = 9;
                        $cashedgeItemDetails->save();

                        # Adding a notification for the same:
                        Yii::trace(CVarDumper::dumpAsString($cashedgeItemDetails->accountpending) . "-->1234567", "cashedgedata");


                        /*  There are pending accounts to be classified */
                        self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'PENDING', $this->mess11, $this->temp1, 0, $cashedgeItemDetails->id, $user_id);
                    }

                // This is for ticker values to be updated:
                self::actionUpdateaccountsInternal($cashedgeItemDetails->FILoginAcctId);

                $connectedAccountDetails = isset($connectedAccounts) ? $connectedAccounts : '';
                $pendingAccountDetails = isset($pendingAccountsArr) ? $pendingAccountsArr : '';

                /*  Checking if there is another row with the same FiLoginAcctID and row ID greater than the current row */
                /*  New model agreed by Melroy and Jeff */
/**** Melroy - Question: When did i agree to this? We agreed to anything with a lower id gets deleted, not the higher ones. Since the higher ones are the newer ones.
               Question: Where is the initialization of this notification object? Also why would it replace the above notification?
*****/
                $existsAcc = CashedgeItem::model()->find("id > :id AND FILoginAcctId = :FILoginAcctId",array("id"=>$cashedgeItemDetails->id , "FILoginAcctId" => $cashedgeItemDetails->FILoginAcctId));
                if (isset($existsAcc)){
                    $existsAcc->status = 1;
                    $existsAcc->save();
                    self::notificationsFn($cashedgeItemDetails->FILoginAcctId, null, null, null, 1, $cashedgeItemDetails->id, $user_id);
                }else{
                    $this->sendResponse(200, CJSON::encode(array("status" => "OK", "ismfa" => 0, "connected" => $connectedAccountDetails, "pending" => $pendingAccountDetails, "ptotal" => count($pendingAccountDetails), "cid" => $cashedgeItemDetails->id , "nonew" => (count($pendingAccountDetails) == '0' ? 1 : 0))));
                }
            }
            # If this is a MFA accounts, the flow comes here after getAddAccountStatus API call
            elseif ($initiateDetails["status"] == 'MFA') {

                $cashedgeItemDetails->HarvestID = $initiateDetails["harvestid"];
                $cashedgeItemDetails->msg = "Almost done! Please answer security question(s) for this financial institution.";
                $cashedgeItemDetails->mfa = 1;
                $cashedgeItemDetails->lsstatus = 1;
                $cashedgeItemDetails->accountdetails = serialize($initiateDetails);
                $cashedgeItemDetails->cecheck = 1;
                $cashedgeItemDetails->save();

                self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'MFA', $this->mess12, $this->temp1, 0, $cashedgeItemDetails->id, $user_id);

                /*  Checking if there is another row with the same FiLoginAcctID and row ID greater than the current row */
                /*  New model agreed by Melroy and Jeff */
/**** Melroy - Question: When did i agree to this? We agreed to anything with a lower id gets deleted, not the higher ones. Since the higher ones are the newer ones.
               Question: Where is the initialization of this notification object? Also why would it replace the above notification?
*****/
                $existsAcc = CashedgeItem::model()->find("id > :id AND FILoginAcctId = :FILoginAcctId",array("id"=>$cashedgeItemDetails->id , "FILoginAcctId" => $cashedgeItemDetails->FILoginAcctId));
                if (isset($existsAcc)){
                    $existsAcc->status = 1;
                    $existsAcc->save();
                    self::notificationsFn($cashedgeItemDetails->FILoginAcctId, null, null, null, 1, $cashedgeItemDetails->id, $user_id);
                }else{
                    $this->sendResponse(200, CJSON::encode(array("status" => "OK", "ismfa" => 1, "cid" => $cashedgeItemDetails->id, "flag" => $flag, "mfadetails" => $initiateDetails["mfaStore"], "parameters" => $initiateDetails["parameters"])));
                }
            }
        } catch (Exception $E) {
            //general error message send to UI and log the error to logs
            //return false;
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => $E->getMessage())));
        }
    }
####################################################################################################################################
    /*                          */
    public function actionAddmfals() {
        try {

            # Getting the details from ther request:
            #---------------------------------------
            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
            $mfares = $_POST["json"];
            $flag = $_POST["flag"];
            $cid = $_POST["cid"];

            #self::checkSession($user_id);

            $cashedgeObject = Yii::app()->cashedge;
            $cashedgeUserDetails = CashedgeAccount::model()->find("user_id = :user_id", array("user_id" => $user_id));
            $cashedgeItemDetails = CashedgeItem::model()->findByPk($cid);
            $cashedgeItemDetails->cecheck = 3;
            $cashedgeItemDetails->save();

            self::notificationsFn($cashedgeItemDetails->FILoginAcctId, null, null, null, 1, $cashedgeItemDetails->id, $user_id);

            /* initiateAddAccounts API call with the answer and modified REQUEST */

            $initiateAPI = $cashedgeObject->addMFAToAccounts($cashedgeItemDetails->FILoginAcctId, $cashedgeItemDetails->RunId, $cashedgeItemDetails->HarvestAddID, $mfares, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword, $cashedgeItemDetails->HarvestAddID);
            $harvestIdMFA = isset($initiateAPI["harvestId"]) ? $initiateAPI["harvestId"] : 0;

            /* This condition works in case the session has expired */
            if ((isset($initiateAPI["code"]) && ($initiateAPI["code"] == '-1')) || $harvestIdMFA == 0) {
                Yii::trace("Time:" . date('r'));
                Yii::trace(":API call:" . "MFA - AFTER addMFAToAccounts 2", "cashedgedata");
                Yii::trace(":Type:" . gettype($initiateAPI), "cashedgedata");
                Yii::trace(":Data:" . CVarDumper::dumpAsString($initiateAPI), "cashedgedata");
                Yii::trace("END ==============================", "cashedgedata");

                #print_r($initiateAPI);die;
                //CE session has expired, and hence we need to get a new session ID:
                $retryMFA = $cashedgeObject->addMFAToAccountsLater($cashedgeItemDetails->FILoginAcctId, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
                $newRunId = isset($retryMFA["harvestId"]) ? $retryMFA["harvestId"] : 0;
                $newHarvestId = isset($retryMFA["harvestAddId"]) ? $retryMFA["harvestAddId"] : 0;
                if ($newRunId == 0) {
                    $cashedgeItemDetails->cecheck = 1;
                    $cashedgeItemDetails->save();
                    $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "The security question was answered incorrectly. Please try again." , "info" => 2, "cid" => $cashedgeItemDetails->id)));
                }

                /* get the harvestIdMFA HarvestAddID && call the getaddaccountstatus */
                do {
                    $initiateDetails = $cashedgeObject->checkAccountStatus($newRunId, $newHarvestId, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
                } while ($initiateDetails["status"] == "InProgress");

                $cashedgeItemDetails->HarvestAddID = $newHarvestId;
                $cashedgeItemDetails->RunId = $newRunId;

                $data = unserialize($cashedgeItemDetails->accountdetails);

                if($data["mfaStore"] != $initiateDetails["mfaStore"]) {
                    $cashedgeItemDetails->msg = "Almost done! Please answer security question(s) for this financial institution.";
                    $cashedgeItemDetails->mfa = 1;
                    $cashedgeItemDetails->lsstatus = 1;
                    $cashedgeItemDetails->accountdetails = serialize($initiateDetails);
                    $cashedgeItemDetails->cecheck = 1;
                    $cashedgeItemDetails->save();
                    self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'MFA', $this->mess12, $this->temp1, 0, $cashedgeItemDetails->id, $user_id);

                    /* get the question and send it */
                    $this->sendResponse(200, CJSON::encode(array("status" => "OK", "ismfa" => 1, "cid" => $cashedgeItemDetails->id, "mfadetails" => $initiateDetails["mfaStore"], "parameters" => $initiateDetails["parameters"], "message" => "The security question was answered incorrectly. Please try again.", "loginacctid" => $cashedgeItemDetails->FILoginAcctId)));
                    return;
                }
                else {
                    $cashedgeItemDetails->save();
                    // Making the same call with different session ID
                    unset($initiateAPI);
                    $initiateAPI = $cashedgeObject->addMFAToAccounts($cashedgeItemDetails->FILoginAcctId, $cashedgeItemDetails->RunId, $cashedgeItemDetails->HarvestAddID, $mfares, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword, $cashedgeItemDetails->HarvestAddID);
                }
            }
            Yii::trace("Time:" . date('r'));
            Yii::trace(":API call:" . "MFA - AFTER addMFAToAccounts 1", "cashedgedata");
            Yii::trace(":Type:" . gettype($initiateAPI), "cashedgedata");
            Yii::trace(":Data:" . CVarDumper::dumpAsString($initiateAPI), "cashedgedata");
            Yii::trace("END ==============================", "cashedgedata");

            $harvestIdMFA = isset($initiateAPI["harvestId"]) ? $initiateAPI["harvestId"] : 0;
            if ($harvestIdMFA == 0) {
                $cashedgeItemDetails->cecheck = 1;
                $cashedgeItemDetails->save();
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "The security question was answered incorrectly. Please try again." , "info" => 2, "cid" => $cashedgeItemDetails->id)));
            }

            # getAddAccountStatus API is called:
            #-----------------------------------
            do {
                $initiateDetails = $cashedgeObject->checkAccountStatus($harvestIdMFA, $cashedgeItemDetails->HarvestAddID, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
            } while ($initiateDetails["status"] == "InProgress");

            Yii::trace("Time:" . date('r'));
            Yii::trace(":API call:" . "MFA - AFTER ANSWERING QUESTION ADDMFALS", "cashedgedata");
            Yii::trace(":Type:" . gettype($initiateDetails), "cashedgedata");
            Yii::trace(":Data:" . CVarDumper::dumpAsString($initiateDetails), "cashedgedata");
            Yii::trace("END ==============================", "cashedgedata");


            /* This is specific use case, if the FI needs / sends an authentication code or process */
            if ($initiateDetails["status"] == 'LOGIN') {
                $cashedgeItemDetails->RunId = $harvestIdMFA;
                $cashedgeItemDetails->msg = "";
                $cashedgeItemDetails->mfa = 0;
                $cashedgeItemDetails->lsstatus = 100;
                $cashedgeItemDetails->accountdetails = serialize($initiateDetails);
                $cashedgeItemDetails->cecheck = 0;
                $cashedgeItemDetails->save();

                /*  Wrong username / password combo notification*/
                self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'LOGIN', $this->mess1, $this->temp5, 0, $cashedgeItemDetails->id, $user_id);

                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "reenter" => "1", "fiid" => $cashedgeItemDetails->Fiid, "message" => "We could not add your account at this time. Please try again later.", "info" => 2, "cid" => $cashedgeItemDetails->id)));
            }
            if ($initiateDetails["status"] == 'ERROR' && $initiateDetails["code"] != 304 ){
                $cashedgeItemDetails->RunId = $harvestIdMFA;
                $cashedgeItemDetails->msg = "";
                $cashedgeItemDetails->mfa = 0;
                $cashedgeItemDetails->lsstatus = 200;
                $cashedgeItemDetails->accountdetails = serialize($initiateDetails);
                $cashedgeItemDetails->cecheck = 0;
                $cashedgeItemDetails->save();

                $info_msg = (($initiateDetails["msg"] != '') ? $initiateDetails["msg"] : "We could not add your account at this time. Please try again later.");
                // Needs work - T
                self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'LOGIN', $this->mess1, $this->temp5, 0, $cashedgeItemDetails->id, $user_id);
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "reenter" => "1", "fiid" => $cashedgeItemDetails->Fiid, "message" => $info_msg, "info" => 2, "cid" => $cashedgeItemDetails->id)));
            }
            if (isset($initiateDetails["status"]) && ($initiateDetails["status"] == 'MFA')){
                $cashedgeItemDetails->RunId = $harvestIdMFA;
                $cashedgeItemDetails->msg = "Almost done! Please answer security question(s) for this financial institution.";
                $cashedgeItemDetails->mfa = 1;
                $cashedgeItemDetails->lsstatus = 1;
                $cashedgeItemDetails->accountdetails = serialize($initiateDetails);
                $cashedgeItemDetails->cecheck = 1;
                $cashedgeItemDetails->save();

                self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'MFA', $this->mess12, $this->temp1, 0, $cashedgeItemDetails->id, $user_id);

                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "ismfa" => 1, "cid" => $cashedgeItemDetails->id, "mfadetails" => $initiateDetails["mfaStore"], "parameters" => $initiateDetails["parameters"] ,"loginacctid" => $cashedgeItemDetails->FILoginAcctId)));
            }
            if (isset($initiateDetails["code"]) && ($initiateDetails["code"] == '304')) {


                /* call the addMFAToAccountsLater here */
                $retryMFA = $cashedgeObject->addMFAToAccountsLater($cashedgeItemDetails->FILoginAcctId, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);

                $newRunId = isset($retryMFA["harvestId"]) ? $retryMFA["harvestId"] : 0;
                $newHarvestId = isset($retryMFA["harvestAddId"]) ? $retryMFA["harvestAddId"] : 0;
                if ($newRunId == 0) {
                    $cashedgeItemDetails->cecheck = 1;
                    $cashedgeItemDetails->save();
                    $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "The security question was answered incorrectly. Please try again." , "info" => 2, "cid" => $cashedgeItemDetails->id)));
                }
                /* get the harvestIdMFA HarvestAddID && call the getaddaccountstatus */
                do {
                    $initiateDetails = $cashedgeObject->checkAccountStatus($newRunId, $newHarvestId, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
                } while ($initiateDetails["status"] == "InProgress");

                $cashedgeItemDetails->HarvestAddID = $newHarvestId;
                $cashedgeItemDetails->RunId = $newRunId;
                $cashedgeItemDetails->msg = "Almost done! Please answer security question(s) for this financial institution.";
                $cashedgeItemDetails->mfa = 1;
                $cashedgeItemDetails->lsstatus = 1;
                $cashedgeItemDetails->accountdetails = serialize($initiateDetails);
                $cashedgeItemDetails->cecheck = 1;
                $cashedgeItemDetails->save();

                self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'MFA', $this->mess12, $this->temp1, 0, $cashedgeItemDetails->id, $user_id);

                /* get the question and send it */
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "ismfa" => 1, "cid" => $cashedgeItemDetails->id, "mfadetails" => $initiateDetails["mfaStore"], "parameters" => $initiateDetails["parameters"], "message" => "The security question was answered incorrectly. Please try again.", "loginacctid" => $cashedgeItemDetails->FILoginAcctId)));
            }
            if ($initiateDetails["status"] == 'OK') {

                $cashedgeItemDetails->lsstatus = 4;
                $cashedgeItemDetails->mfa = 0;
                $cashedgeItemDetails->cecheck = 0;
                $cashedgeItemDetails->save();

                /* getNewAccounts API is called: */

                $newAccountDetails = $cashedgeObject->getNewAccounts($cashedgeItemDetails->RunId, $cashedgeItemDetails->HarvestAddID, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);

                if (isset($newAccountDetails["createdAccounts"]) || isset($newAccountDetails["pendingAccounts"])) {
                    $cashedgeItemDetails->lsstatus = 5;
                    if (isset($newAccountDetails["pendingAccounts"])) {
                        $cashedgeItemDetails->msg = "Almost done! Please tell us what type of accounts these are.";
                        // This is for getalitems call
                        $cashedgeItemDetails->lsupdate = 1;
                        $cashedgeItemDetails->save();
                    }
                    $cashedgeItemDetails->save();
                }
                if (isset($newAccountDetails["code"]) && $newAccountDetails["code"] == '4450') {
                    $cashedgeItemDetails->lsstatus = 8;
                    $cashedgeItemDetails->save();
                    self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'LOGIN', $this->mess2, $this->temp4, 0, $cashedgeItemDetails->id, $user_id);

                    /* THAYUB - Need to add CE LOG HERE */
                }

                if (isset($flag) && ($flag == 1)) {
                    /* The account already exists and we need to check for the accounts that are not in our DB */
                    $accountNumberArray = array();
                    /* Pending Accounts */
                    if (isset($newAccountDetails["pendingAccounts"]) && !empty($newAccountDetails["pendingAccounts"])) {
                        $xml = new DOMDocument();
                        $xml->loadXML("<PendingAcc>" . $newAccountDetails["pendingAccounts"] . "</PendingAcc>");
                        $accountsNode = $xml->getElementsByTagName("HarvestAddFetchAcct");
                        if ($accountsNode->length > 0) {
                            foreach ($accountsNode as $account) {
                                $eachPendingAccount["AcctNumber"] = $account->getElementsByTagName("AcctNumber")->item(0)->nodeValue;
                                $accountNumberArray[] = $eachPendingAccount["AcctNumber"];
                            }
                        }
                    }


                    /* Created Accounts */

                    if (isset($newAccountDetails["createdAccounts"]) && !empty($newAccountDetails["createdAccounts"])) {
                        $xml1 = new DOMDocument();
                        $xml1->loadXML("<CreatedAcc>" . $newAccountDetails["createdAccounts"] . "</CreatedAcc>");
                        $accountsNode1 = $xml->getElementsByTagName("HarvestAddFetchAcct");
                        if ($accountsNode1->length > 0) {
                            foreach ($accountsNode1 as $account) {
                                $eachCreatedAccount["AcctNumber"] = $account->getElementsByTagName("AcctNumber")->item(0)->nodeValue;
                                $accountNumberArray[] = $eachCreatedAccount["AcctNumber"];
                            }
                        }
                    }

                    Yii::trace(CVarDumper::dumpAsString($accountNumberArray) . "FLAG 1 : Account Numbers ADDMFALS", "cashedgedata");
                }

                Yii::trace(CVarDumper::dumpAsString($newAccountDetails) . "-->5 getNewAccounts response ADDMFALS", "cashedgedata");
                /* SEPERATE WORK FLOW FOR PENDING ACCOUNTS AS THEY NEED TO BE CLASSFIFIED: */
                $pendingAccountsArr = array();
                if (isset($newAccountDetails["pendingAccounts"]) && !empty($newAccountDetails["pendingAccounts"])) {

                    $xml = new DOMDocument();
                    $xml->loadXML("<PendingAcc>" . $newAccountDetails["pendingAccounts"] . "</PendingAcc>");
                    $accountsNode = $xml->getElementsByTagName("HarvestAddFetchAcct");

                    if ($accountsNode->length > 0) {
                        /* iterate through accounts */
                        $counter = 1;
                        foreach ($accountsNode as $account) {

                            $eachAccount = array();
                            $eachAccount["counter"] = $counter;
                            $eachAccount["Fiid"] = $account->getElementsByTagName("FIId")->item(0)->nodeValue;
                            $eachAccount["flag"] = 0;
                            /* get all the accounts supported */
                            $accountsSupported = CashedgefiDetails::model()->find("FIId=:fiid", array("fiid" => $eachAccount["Fiid"]));
                            /* account supported */
                            if ($accountsSupported) {
                                $eachAccount["accountsSupported"] = json_decode($accountsSupported->AccountSupported);
                            }

                            $eachAccount["AcctNumber"] = $account->getElementsByTagName("AcctNumber")->item(0)->nodeValue;
                            if ($account->getElementsByTagName("FIAcctName")->length > 0) {
                                $fiiAcc = $account->getElementsByTagName("FIAcctName")->item(0);
                                $eachAccount["FIAcctNameParamName"] = $fiiAcc->getElementsByTagName("ParamName")->item(0)->nodeValue;
                                $eachAccount["FIAcctNameParamVal"] = $fiiAcc->getElementsByTagName("ParamVal")->item(0)->nodeValue;
                            }
                            $fiiAccBal = $account->getElementsByTagName("AcctBal");
                            if ($fiiAccBal->length > 0) {
                                foreach ($fiiAccBal as $accBal) {

                                    $accountBal = array();

                                    $accountBal["AccBalType"] = $accBal->getElementsByTagName("BalType")->item(0)->nodeValue;
                                    $curAmtNode = $accBal->getElementsByTagName("CurAmt")->item(0);
                                    $accountBal["CurAmt"] = $curAmtNode->getElementsByTagName("Amt")->item(0)->nodeValue;
                                    $accountBal["CurCode"] = $curAmtNode->getElementsByTagName("CurCode")->item(0)->nodeValue;

                                    $eachAccount["AccBal"] = $accountBal;
                                }
                            } else {
                                $accountBal = array();
                                $accountBal["CurAmt"] = 0;
                                $eachAccount["AccBal"] = $accountBal;
                            }
                            $pendingAccountsArr[] = $eachAccount;
                            $counter++;
                        }
                    }
                    /* Storing them into DB for later use: */
                    $cashedgeItemDetails->accountpending = serialize($pendingAccountsArr);
                    $cashedgeItemDetails->save();
                }

                /* This workflow is for created accounts, i.e when the accounts are classified */
                if (isset($newAccountDetails["createdAccounts"]) || !empty($newAccountDetails["createdAccounts"])) {

                    # This is the XML list of all the accounts returned.
                    $allAccountsList = $newAccountDetails["createdAccounts"];
                    Yii::trace(CVarDumper::dumpAsString($newAccountDetails) . "-->TESTTEST ADDMFALS", "cashedgedata");
                    # createAccounts API is called:
                    #------------------------------
                    $createAccountAPI = $cashedgeObject->createAccounts($cashedgeItemDetails->RunId, $cashedgeItemDetails->HarvestAddID, $allAccountsList, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
                    $details["accounts"] = isset($createAccountAPI["accounts"]) ? $createAccountAPI["accounts"] : 0;
                    $details["status"] = "OK";
                    # Created accounts check:
                    Yii::trace("-->-6 BEFORE additemtodb ", "cashedgedata");
                    if ($details["accounts"] != 0) {
                        $connectedAccounts = self::addItemToDB($details, $cashedgeItemDetails->id, $user_id);
                    }

                    Yii::trace(CVarDumper::dumpAsString($details) . "-->6 createAccounts response ADDMFALS", "cashedgedata");

                    if ($details["status"] == "OK") {
                        $cashedgeItemDetails->lsstatus = 6;
                        $cashedgeItemDetails->save();
                    }

                    # updateAccounts API is called:
                    #------------------------------
                    $updateAccountAPI = $cashedgeObject->updateAccounts($cashedgeItemDetails->FILoginAcctId, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
                    $updateAccountHarvestId = $updateAccountAPI["newharvestid"];
                    $updateAccountRunId = $updateAccountAPI["newrunid"];
                    Yii::trace(CVarDumper::dumpAsString($updateAccountAPI) . "-->7 updateAccounts response ADDMFALS", "cashedgedata");

                    if (isset($updateAccountRunId) && isset($updateAccountHarvestId)) {
                        $cashedgeItemDetails->lsstatus = 7;
                        $cashedgeItemDetails->save();
                    }

                    # getHarvestStatus API is called:
                    #------------------------------
                    $accHarvestAPI = $cashedgeObject->getHarvestStatus($cashedgeUserDetails->username, $cashedgeUserDetails->cpassword, $updateAccountHarvestId, $updateAccountRunId);

                    Yii::trace(CVarDumper::dumpAsString($accHarvestAPI) . "-->8 getHarvestStatus response ADDMFALS", "cashedgedata");

                    if ($accHarvestAPI["status"] == "OK") {
                        $cashedgeItemDetails->lsstatus = 8;
                        if (!(isset($cashedgeItemDetails->msg))) {
                            $cashedgeItemDetails->msg = "Your accounts have been added successfully.";
                        }
                        # Some Logging has to happen here.
                        #THAYUB
                        $cashedgeItemDetails->RunId = $updateAccountRunId;
                        $cashedgeItemDetails->HarvestAddID = $updateAccountHarvestId;
                        $cashedgeItemDetails->save();
                        Yii::trace($accHarvestAPI["status"] . "-->8 getHarvestStatus response ADDMFALS", "cashedgedata");

                        $pendingAccountDetails = isset($pendingAccountsArr) ? $pendingAccountsArr : '';
                        $connectedAccountDetails = isset($connectedAccounts) ? $connectedAccounts : '';

                        self::actionUpdateaccountsInternal($cashedgeItemDetails->FILoginAcctId);
                        $AcctIDs = CashedgefiDetails::model()->find("FIId=:fid", array("fid" => $cashedgeItemDetails->Fiid));
                        $accountsSupportedbyFI = isset($AcctIDs->AccountSupported) ? json_decode($AcctIDs->AccountSupported) : 0;

                        Yii::trace(CVarDumper::dumpAsString($accountsSupportedbyFI) . "-->accountsSupportedbyFI ADDMFALS ", "cashedgedata");

                        if ($accountsSupportedbyFI != 0) {
                            foreach ($accountsSupportedbyFI as $eachAcctInfo) {
                                if ($eachAcctInfo->AcctType == "INV") {
                                    $invAcctInfo = $eachAcctInfo;
                                }
                            }
                        }

                        if (isset($invAcctInfo)) {
                            $invClass = Assets::model()->findAllBySql("SELECT * FROM assets WHERE user_id=:user_id AND FILoginAcctId=:FILoginAcctId AND accttype LIKE '%INV%' AND classified = 0 ", array('user_id' => $user_id, 'FILoginAcctId' => $cid));

                            if (isset($invClass)) {

                                // Check for setting the counter value:
                                $counterNow = isset($pendingAccountsArr) ? (count($pendingAccountsArr) + 1) : 1;

                                foreach ($invClass as $eachINV) {
                                    // Creating the array object like pending accounts for classification:
                                    $invArray = array();
                                    #Yii::trace(CVarDumper::dumpAsString($eachINV) . "-->eachINV ", "cashedgedata");

                                    $otlt = Otlt::model()->findAllBySql("SELECT name FROM otlt WHERE description IN ('INV')");

                                    $AccountsSupportedArray = array();
                                    // Making it the same as pending accounts array:
                                    foreach ($otlt as $eachOtlt) {
                                        $eachINVInfo = new StdClass();
                                        $eachINVInfo->AcctName = $eachOtlt->name;
                                        $eachINVInfo->AcctTypeId = $eachOtlt->name;
                                        $eachINVInfo->AcctGroup = '';
                                        $eachINVInfo->AcctType = 'INV';
                                        $eachINVInfo->ExtAcctType = 'INV';
                                        $AccountsSupportedArray[] = $eachINVInfo;
                                    }

                                    $actIDINV = new stdClass();
                                    $actIDINV->CurCode = $eachINV->actid;

                                    $invArray["counter"] = $counterNow++;
                                    $invArray["Fiid"] = $cashedgeItemDetails->Fiid;
                                    $invArray["AcctNumber"] = $invAcctInfo->AcctTypeId;
                                    $invArray["FIAcctNameParamName"] = '';
                                    $invArray["FIAcctNameParamVal"] = $eachINV->name;
                                    $invArray["AccBal"] = $actIDINV;
                                    $invArray["accountsSupported"] = $AccountsSupportedArray;
                                    $invArray["flag"] = 1;

                                    #Yii::trace(CVarDumper::dumpAsString($invArray) . "-->invArray ", "cashedgedata");
                                    // This is for getalitems call
                                    $cashedgeItemDetails->lsupdate = 1;
                                    $cashedgeItemDetails->save();
                                    $pendingAccountsArr[] = $invArray;
                                }
                            }
                        }
                    } else {
                        /*
                        self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'HARVEST', $this->mess2, $this->temp4, 0);
                        */
                    }
                }
                /* Saving the pending accounts + Inv classification in the DB for further use by API */
                if (empty($pendingAccountsArr)) {
                    $cashedgeItemDetails->accountpending = 0;
                    }
                    else{
                        $cashedgeItemDetails->accountpending = serialize($pendingAccountsArr);
                        $cashedgeItemDetails->lsstatus = 9;
                        $cashedgeItemDetails->save();

                        # Adding a notification for the same:
                        self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'PENDING', $this->mess11, $this->temp1, 0, $cashedgeItemDetails->id, $user_id);
                    }

                $connectedAccountDetails = isset($connectedAccounts) ? $connectedAccounts : 0;
                $pendingAccountDetails = isset($pendingAccountsArr) ? $pendingAccountsArr : 0;

                $cashedgeItemDetails = CashedgeItem::model()->findByPk($cid);
                $cashedgeItemDetails->cecheck = 2;
                $cashedgeItemDetails->save();

                /* Successful Notification if there are no pending accounts */

                if (count($pendingAccountDetails) == 0 && count($connectedAccountDetails) > 0){

                    self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'CONNECT', $this->mess4, $this->temp4, 0, $cashedgeItemDetails->id, $user_id);

                }else{

                self::notificationsFn($cashedgeItemDetails->FILoginAcctId, null, null, null, 0, $cashedgeItemDetails->id, $user_id);

                }
                Yii::trace(CVarDumper::dumpAsString($connectedAccountDetails) . "-->connectedAccountDetails ADDMFALS ", "cashedgedata");
                Yii::trace(CVarDumper::dumpAsString($pendingAccountDetails) . "-->pendingAccountDetails ADDMFALS ", "cashedgedata");

                /*  Checking if there is another row with the same FiLoginAcctID and row ID greater than the current row */
                /*  New model agreed by Melroy and Jeff */
                $existsAcc = CashedgeItem::model()->find("id > :id AND FILoginAcctId = :FILoginAcctId",array("id"=>$cashedgeItemDetails->id , "FILoginAcctId" => $cashedgeItemDetails->FILoginAcctId));
                if (isset($existsAcc)){
                    $existsAcc->status = 1;
                    $existsAcc->save();
                }else{
                    $this->sendResponse(200, CJSON::encode(array("status" => "OK", "ismfa" => 0, "connected" => $connectedAccountDetails, "pending" => $pendingAccountDetails, "ptotal" => count($pendingAccountDetails), "cid" => $cashedgeItemDetails->id, "loginacctid" => $cashedgeItemDetails->id , "nonew" => (count($pendingAccountDetails) == '0' ? 1 : 0))));
                }
            }
        } catch (Exception $E) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => $E->getMessage())));
        }
    }
############################################################################################################################################################

    public function actionDeleteaccount($cid = 0) {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $fid = isset($_GET["cid"]) ? $_GET["cid"] : $cid;
        $cashedgeItemDetails = CashedgeItem::model()->find("id=:id and user_id = :user_id", array("id" => $fid, "user_id" => $user_id));
        if($cashedgeItemDetails) {
            $itemDetails = CashedgeItem::model()->findAll("filoginacctid=:id  AND fiid = :fiid AND user_id = :user_id", array("id" => $cashedgeItemDetails->FILoginAcctId, "fiid" => $cashedgeItemDetails->Fiid, "user_id" => $user_id));
            if($itemDetails)
            {
                foreach($itemDetails as $item) {
                    $item->status = 1;
                    $item->save();
                }
            }

            $row = Notification::model()->find("rowid = :rowid and user_id = :user_id and status = 0",array("rowid" => $cashedgeItemDetails->id, "user_id" => $user_id));
            if($row) {
                $row->status = 1;
                $row->lastmodified = date("Y-m-d H:i:s");
                $row->save();
                Yii::app()->cache->set('notification' . $user_id, $row->lastmodified);
            }

        }
        $this->sendResponse(200, CJSON::encode(array("status" => "OK")));
    }

    /*                          */
    public function actionCheckStatus($cid = 0) {
        try {
            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            $user_id = Yii::app()->getSession()->get('wsuser')->id;

            $fid = isset($_GET["cid"]) ? $_GET["cid"] : $cid;

            if ($fid == "") {
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => 'Please wait,
                the system will notify you of the status soon.', "flag" => '1')));
            }

            $cashedgeItemDetails = CashedgeItem::model()->find("id=:id", array("id" => $fid));

            if (!$cashedgeItemDetails){
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "Generic error message" ,"nonew" => '1')));
            }

            if ($cashedgeItemDetails->cecheck == 303){
                /*This should be called in case the session expires for MFA answering - typically more than 4 mins*/
                $timeStamp = strtotime($cashedgeItemDetails->modified);
                $diff = (time() - $timeStamp);

                $cashedgeItemDetails->msg = "We are retrieving the security question(s) from your financial institution. Please try again later.";
                $cashedgeItemDetails->save();

                /*If the time diff is more than 4 mins , we call a new question*/
                if ($diff/60 >= 4){
                    self::actionUpdateaccounts($fid);
                    #$this->sendResponse(200, CJSON::encode(array('status' => $timeStamp , 'timediff' => $diff/60 )));
                }

                // This is the response for MFA question with notification for refresh.
                $data = unserialize($cashedgeItemDetails->accountdetails);
                $this->sendResponse(200, CJSON::encode(array('status' => "OK", "ismfa" => 1, "cid" => $cashedgeItemDetails->id,
                    "filoginacctid" => $fid, "flag" => 0, "mfadetails" => $data["mfaStore"], "parameters" => $data["parameters"], "refresh" => 1 )));
            }

            if ($cashedgeItemDetails->mfa == 1 && $cashedgeItemDetails->cecheck == 1) {

                $data = unserialize($cashedgeItemDetails->accountdetails);
                $this->sendResponse(200, CJSON::encode(array('status' => "OK", "ismfa" => 1, "cid" => $cashedgeItemDetails->id, "filoginacctid" => $fid, "flag" => 0, "mfadetails" => $data["mfaStore"], "parameters" => $data["parameters"])));
            }
            if($cashedgeItemDetails->mfa == 1 && $cashedgeItemDetails->cecheck == 3) {

                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "We are checking the answer to your security question(s). Please wait.", "fiid" => $cashedgeItemDetails->Fiid)));
            }


            if ($cashedgeItemDetails->lsstatus == 9) {
                // This account is for pending accounts response:
                $dataPending = unserialize($cashedgeItemDetails->accountpending);
                $this->sendResponse(200, CJSON::encode(array('status' => "OK", "cid" => $cashedgeItemDetails->id, "filoginacctid" => $fid, "flag" => 0, "pending" => $dataPending, "ptotal" => count($dataPending))));
            }

            $status = $cashedgeItemDetails->lsstatus;

            if ($status == 3) {
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "Your accounts are being initialized for the download process. Please wait a few minutes.", "fiid" => $cashedgeItemDetails->Fiid)));
            }if (($status > 3 && $status <= 7) || $status == 10) {
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "Your account details are being downloaded. Please wait a few minutes.", "fiid" => $cashedgeItemDetails->Fiid)));
            }if ($status == 8 || $status == 11) {
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "Your accounts have been added successfully.", "type" => $status, "fiid" => $cashedgeItemDetails->Fiid)));
            }
            if ($status >= 0 && $status <= 3){
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "Your accounts are being initialized for the download process. Please wait a few minutes.", "type" => $status, "fiid" => $cashedgeItemDetails->Fiid)));
            }
            if ($status == 100 || $status == 110) {
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "reenter" => "1", "message" => "The credentials you entered were invalid. Please try again.", "fiid" => $cashedgeItemDetails->Fiid)));
            }if ($status == 200 || $status == 127){
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "reenter" => "1", "message" => "We could not add your account at this time. Please try again later.", "fiid" => $cashedgeItemDetails->Fiid)));
            }
        } catch (Exception $E) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => $E->getMessage())));
        }
    }
####################################################################################################################################
    /*                          */
    public function actionUpdateaccounts($fid=0) {
        try {

            /* Flag from the UI call*/
            $flag = isset($_POST["flag"]) ? $_POST["flag"] : 0;

            $cid = isset($_POST["cid"]) ? $_POST["cid"] : 0;

            /* Getting the user id from the session */
            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            $user_id = Yii::app()->getSession()->get('wsuser')->id;

            /* Cashedge component initialized and CE user details */
            $cashedgeComp = Yii::app()->cashedge;
            $cashedgeUserDetails = CashedgeAccount::model()->find("user_id = :user_id", array("user_id" => $user_id));
            $cerowDetails = CashedgeItem::model()->find("FILoginAcctId = :fid",array("fid" => $fid));

            /* This condition is executed when this function is called after 4 mins of security question rendered initally*/
            /*-------------------------------------------------------------------------------------------------------------*/
            if (isset($fid) && ($fid != 0)){
                $cerowDetails = CashedgeItem::model()->find("id = :fid",array("fid" => $fid));

                /* Calling the UpdateAccounts API to get the updated the latest info from FI */
                $updateDetails = $cashedgeComp->updateAccounts($cerowDetails->FILoginAcctId,$cashedgeUserDetails->username,$cashedgeUserDetails->cpassword,'');
                /*TRACE*/
                Yii::trace(":REFRESH UPDATE $fid exists:" . CVarDumper::dumpAsString($updateDetails), "cashedgedata");
                    /* When the API comes with an error code */
                    if (isset($updateDetails['errorcode'])){

                        $codeError = $updateDetails['errorcode'];
                        /* Switch case for the error code, as of not only 4470 is caught*/
                        switch($codeError){
                            case "4470":
                            $this->sendResponse(200, CJSON::encode(array("status" => "OK" , "message" => $updateDetails["msg"] , "code" => $updateDetails["errorcode"] , "flag" => 100)));
                            break;

                            case "100":
                            $this->sendResponse(200, CJSON::encode(array("status" => "OK" , "message" => $updateDetails["msg"] , "code" => $updateDetails["errorcode"] , "flag" => 100)));

                            default:
                            $this->sendResponse(200, CJSON::encode(array("status" => "OK" , "message" => "There is an error in accessing your account(s) right now. Please try again in a few minutes." , "flag" => 100)));
                            break;
                        }
                    }
                    if (isset($updateDetails["newharvestid"])){
                        $updateAccountHarvestId = $updateDetails["newharvestid"];
                        $updateAccountRunId = $updateDetails["newrunid"];

                        /* Updating the new session token and Run ID */
                        $cerowDetails->RunId = $updateAccountRunId;
                        $cerowDetails->HarvestAddID = $updateAccountHarvestId;
                        $cerowDetails->save();
                    }
                    /* Calling the Harvesting API */
                    $harvestDetails = $cashedgeComp->getHarvestStatus($cashedgeUserDetails->username,$cashedgeUserDetails->cpassword,$updateAccountHarvestId, $updateAccountRunId);
                    /*TRACE*/
                    Yii::trace(":REFRESH HARVEST" . CVarDumper::dumpAsString($harvestDetails), "cashedgedata");

                    /* Now to get the get account details to the get the updated Accounts INFO from CE DB */
                    if ($harvestDetails["status"] == "OK") {
                        if (isset($harvestDetails["code"]) && $harvestDetails["code"] == 303){

                            /* Notification already exists for this FiLoginAcctID for this user */
                            /* Storing the question in the DB & updating status */
                            $cerowDetails->accountdetails = serialize($harvestDetails);
                            $cerowDetails->msg = "We are retrieving the security question from your financial institution. Please try again in a few minutes.";
                            $cerowDetails->save();
                        }else{
                            $details = $cashedgeComp->getAccountsSummarytest($cashedgeUserDetails->username, $cashedgeUserDetails->cpassword, $cerowDetails->FILoginAcctId);
                            /* update the database with the session for the user */
                            $cashedgeUserDetails->sesinfo = $details["session"];
                            $cashedgeUserDetails->save();
                            $refreshStatus = self::addUpdateToDB($details, $user_id);
                        }
                    }
            }
            /* This condition is executed normally, and when the flag is 0 */
            /*-------------------------------------------------------------*/
            if ((!isset($flag) || $flag == 0) && ($fid == 0)){

                $cashedgeUserDetails = CashedgeAccount::model()->find("user_id = :user_id", array("user_id" => $user_id));

                // It has to be 11 and as well as 8(for accounts withtout pending/classifications)
                $FILoginAcctIDS = CashedgeItem::model()->findAllBySql("SELECT * FROM cashedgeitem WHERE user_id=:user_id AND lsstatus IN ('11','8')", array("user_id" => $user_id));
                $refreshStatusVal = 0;
                    if (isset($FILoginAcctIDS) && isset($cashedgeUserDetails)) {
                        foreach ($FILoginAcctIDS as $eachFILoginAcctId) {

                            $FILoginAcctId = $eachFILoginAcctId->FILoginAcctId;
                            $currentLSstatus = $eachFILoginAcctId->lsstatus;
                            if ($currentLSstatus == '11' || $currentLSstatus == '8') {
                                // Calling the UpdateAccounts API to get the updated the latest info from FI.
                                $updateDetails = $cashedgeComp->updateAccounts($FILoginAcctId,$cashedgeUserDetails->username,$cashedgeUserDetails->cpassword,'');
                                Yii::trace(":REFRESH UPDATE:" . CVarDumper::dumpAsString($updateDetails), "cashedgedata");

                                if (isset($updateDetails['errorcode'])){

                                    $codeError = $updateDetails['errorcode'];
                                    switch($codeError){
                                        case "4470":
                                        $this->sendResponse(200, CJSON::encode(array("status" => "OK" , "message" => $updateDetails["msg"] , "code" => $updateDetails["errorcode"] , "flag" => 100)));
                                        break;

                                        default:
                                        $this->sendResponse(200, CJSON::encode(array("status" => "OK" , "message" => "There is an error in accessing your account(s) right now. Please try again later." , "flag" => 100)));
                                        break;

                                    }
                                }
                                if (isset($updateDetails["newharvestid"])){
                                    $updateAccountHarvestId = $updateDetails["newharvestid"];
                                    $updateAccountRunId = $updateDetails["newrunid"];

                                    // Updating the new session token and Run ID
                                    $eachFILoginAcctId->RunId = $updateAccountRunId;
                                    $eachFILoginAcctId->HarvestAddID = $updateAccountHarvestId;
                                    $eachFILoginAcctId->save();

                                }

                                // Calling the Harvesting API
                                $harvestDetails = $cashedgeComp->getHarvestStatus($cashedgeUserDetails->username,$cashedgeUserDetails->cpassword,$updateAccountHarvestId, $updateAccountRunId);
                                //Yii::trace(":REFRESH HARVEST" . CVarDumper::dumpAsString($harvestDetails), "cashedgedata");
                                Yii::trace(":REFRESH HARVEST" . CVarDumper::dumpAsString($harvestDetails), "cashedgedata");

                                // Now to get the get account details to the get the updated Accounts INFO from CE DB:
                                if ($harvestDetails["status"] == "OK") {

                                    if (isset($harvestDetails["code"]) && $harvestDetails["code"] == 303){

                                        // Creating notification:
                                        self::notificationsFn($eachFILoginAcctId->FILoginAcctId, 'REFRESH', $this->mess10, $this->temp3, 0, $eachFILoginAcctId->id, $user_id);

                                        /* Storing the question in the DB & updating status: */
                                        $eachFILoginAcctId->accountdetails = serialize($harvestDetails);
                                        $eachFILoginAcctId->cecheck = 303;
                                        $eachFILoginAcctId->save();
                                        $this->sendResponse(200, CJSON::encode(array("status" => "OK" , "flag" => 303 , "message" => "Please answer the security question again.")));

                                    }

                                    $details = $cashedgeComp->getAccountsSummarytest($cashedgeUserDetails->username, $cashedgeUserDetails->cpassword, $FILoginAcctId);
                                    /* update the database with the session for the user */
                                    $cashedgeUserDetails->sesinfo = $details["session"];
                                    $cashedgeUserDetails->save();
                                    $refreshStatus = self::addUpdateToDB($details, $user_id);
                                    if ($refreshStatus) {
                                        $refreshStatusVal = 1;
                                    }
                                }
                            } elseif (($currentLSstatus != 11 || $currentLSstatus != 8)&& isset($cashedgeItemDetails)) {

                                /* Notification : */
                                self::notificationsFn($eachFILoginAcctId->FILoginAcctId, 'REFRESH', $this->mess9, $this->temp4, 0, $eachFILoginAcctId->id, $user_id);

                                /* Logging in ce log:*/
                                $log = new Celog();
                                $log->action = 'Update Accounts';
                                $log->flloginacctid = $details["id"];
                                $log->acctid = '';
                                $log->user_id = $user_id;
                                $log->status = '0';
                                $log->save();
                            }

                            if ($refreshStatusVal == 1) {
                                # Notification :
                                self::notificationsFn($eachFILoginAcctId->FILoginAcctId, 'REFRESH', $this->mess5, $this->temp4, 0, $eachFILoginAcctId->id, $user_id);
                            }
                            /* Logging in ce log: */
                            $log = new Celog();
                            $log->action = 'Update Accounts';
                            $log->flloginacctid = $user_id;
                            $log->acctid = '';
                            $log->user_id = $user_id;
                            $log->status = '1';
                            $log->save();
                        }
                    }
                    $this->sendResponse(200, CJSON::encode(array("status" => "OK" )));
            }
            /* This condition is when you have to answer the security question - We called the modified getHarvestStatus API with the answer */
            /*-------------------------------------------------------------------------------------------------------------------------------*/
            if ($flag == 303){

                /* Calling the modified response with answer */
                $CErow = CashedgeItem::model()->find("id = :fid",array("fid" => $cid));
                $FILoginAcctId = isset($CErow->FILoginAcctId) ? $CErow->FILoginAcctId : 0;

                if ($FILoginAcctId == 0){
                    $this->sendResponse(200, CJSON::encode(array("status" => "ERROR" , "message" => 'Please try again later.')));
                }

                self::notificationsFn($FILoginAcctId, null, null, null, 1, $CErow->id, $user_id);

                $cashEdgeItemInfo = CashedgeItem::model()->find("user_id=:user_id AND FILoginAcctId=:loginacctid",array("user_id"=>$user_id, "loginacctid" => $FILoginAcctId));
                $mfares = $_POST["json"];

                $updateAnswer = $cashedgeComp->getHarvestStatusMFA($FILoginAcctId ,$cashEdgeItemInfo->HarvestAddID,$mfares,$cashEdgeItemInfo->RunId);
                $harvestAnswer = $cashedgeComp->getHarvestStatus($cashedgeUserDetails->username,$cashedgeUserDetails->cpassword,$cashEdgeItemInfo->HarvestAddID,$cashEdgeItemInfo->RunId);
                $details = $cashedgeComp->getAccountsSummarytest($cashedgeUserDetails->username, $cashedgeUserDetails->cpassword, $FILoginAcctId);

                if (isset($details["accounts"]) && (isset($details["errorCode"]) && $details["errorCode"] == 0 )) {
                    /*  Remove 303 from DB row */
                    /*  This removes it form the get all items call*/
                    $cerowDetails->cecheck = 0 ;
                    $cerowDetails->save();


                    /* Updating the details in DB */
                    self::addUpdateToDB($details, $user_id);

                    Yii::trace(":REFRESH HARVEST MFA" . CVarDumper::dumpAsString($harvestAnswer), "cashedgedata");
                    Yii::trace(":REFRESH GET ACCOUNT DETAILS " . CVarDumper::dumpAsString($details), "cashedgedata");

                    /*  Accounts are refreshed successfully notification*/
                    self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'REFRESH', $this->mess4, $this->temp4, 0, $cashedgeItemDetails->id, $user_id);

                    #$this->notificationsFn('DISA',$FILoginAcctId,'REFRESH');
                    $this->sendResponse(200, CJSON::encode(array("status" => "OK" , "message" => 'works' , "flag" => 303 , "nonew" => '1' , "loginacctid" =>$CErow->id)));

                }

                if (isset($details["errorCode"]) && ($details["errorCode"] != 0)) {
                    /* Answer is wrong*/

                    $codeError = $details['errorcode'];
                    /* Switch case for the error code, as of not only 4470 is caught*/
                    switch($codeError){
                        case "200":
                        $this->sendResponse(200, CJSON::encode(array("status" => "OK" , "message" => "There is an error in accessing your account(s) right now. Please try again later." , "flag" => 100)));
                        break;

                        case "303":
                        $this->sendResponse(200, CJSON::encode(array("status" => "OK" , "ismfa" => 1 , "rerender" => 1,"loginacctid" =>$CErow->id)));

                        default:
                        $this->sendResponse(200, CJSON::encode(array("status" => "OK" , "message" => "There is an error in accessing your account(s) right now. Please try again later." , "flag" => 100)));




                    /* New workflow*/

                }
            }
          }
        } catch (Exception $e) {
            /**/
        }
    }
####################################################################################################################################
    /* Common function to add / disable notifications */
    public function notificationsFn($id, $context, $message, $template, $status, $rowid, $userid, $info = null){

        $row = Notification::model()->find("refid = :refid and user_id = :user_id",array("refid" => $id, "user_id" => $userid));
        $needsUpdate = false;

        if (!isset($row)){
            $row = new Notification();
            $row->refid = $id;
            $row->user_id = $userid;
            $needsUpdate = true;
        }
        if($rowid !== null && $row->rowid != $rowid) {
            $row->rowid = $rowid;
            $needsUpdate = true;
        }
        if($context !== null && $row->context != $context) {
            $row->context = $context;
            $needsUpdate = true;
        }
        if($message !== null && $row->message != $message) {
            $row->message = $message;
            $needsUpdate = true;
        }
        if($template !== null && $row->template != $template) {
            $row->template = $template;
            $needsUpdate = true;
        }
        if($status !== null && $row->status != $status) {
            $row->status = $status;
            $needsUpdate = true;
        }
        if($info !== null && $row->info != $info) {
            $row->info = $info;
            $needsUpdate = true;
        }

        if($needsUpdate) {
            $row->lastmodified = date("Y-m-d H:i:s");
            $row->save();
            Yii::app()->cache->set('notification' . $userid, $row->lastmodified);
        }
}
####################################################################################################################################
    /*                          */
    public function actionAdditemls() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $accounts = isset($_POST["accounts"]) ? $_POST["accounts"] : 0;
        $invests = isset($_POST["invests"]) ? $_POST["invests"] : 0;
        $cid = $_POST["cid"];

        if (!(isset($cid)) && $cid == 0) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "There is no cid")));
        }

        $cashedgeItemDetails = CashedgeItem::model()->findByPk($cid);

        self::notificationsFn($cashedgeItemDetails->FILoginAcctId, null, null, null, 1, $cashedgeItemDetails->id, $user_id);

        $accountXML = "";

        /*  IF only accounts are present: */
        if (isset($accounts) && ($accounts != 0)) {
            foreach ($accounts as $account) {
                if (isset($account) && !empty($account)) {
                    $accountArr = explode("|", $account);
                    if(count($accountArr) < 4) {
                        $accountArr = explode("#", $account);
                    }
                    $paramName = str_replace('/','',$accountArr[3]);
                    $curCode = "USD";
                    if($accountArr[6] != '') {
                        $curCode = $accountArr[6];
                    }
                    $acctBal = "";
                    if($accountArr[4] != '') {
                        $acctBal = "<AcctBal>
                                        <BalType>{$accountArr[4]}</BalType>
                                        <CurAmt>
                                            <Amt>{$accountArr[5]}</Amt>
                                            <CurCode>{$curCode}</CurCode>
                                        </CurAmt>
                                   </AcctBal>";
                    }
                    $accountXML .= <<<END
                <HarvestAddFetchAcct>
                    <FIId>
                        {$accountArr[0]}
                    </FIId>
                    <AcctNumber>
                        {$accountArr[1]}
                    </AcctNumber>
                    <FIAcctName>
                        <ParamName>{$accountArr[2]}</ParamName>
                        <ParamVal>{$paramName}</ParamVal>
                    </FIAcctName>
                    {$acctBal}
                    <AcctTypeId>{$accountArr[7]}</AcctTypeId>
                    <Misc/>
                    <CurCode>USD</CurCode>
                </HarvestAddFetchAcct>
END;
                }
            }
            $cashedgeObject = Yii::app()->cashedge;
            $cashedgeUserDetails = CashedgeAccount::model()->find("user_id = :user_id", array("user_id" => $user_id));
            $details = $cashedgeObject->createAccounts($cashedgeItemDetails->RunId, $cashedgeItemDetails->HarvestAddID, $accountXML, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);

            if (isset($details["code"]) && $details["code"] == 4090) {
                $updateAccountAPI = $cashedgeObject->updateAccounts($cashedgeItemDetails->FILoginAcctId, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
                $updateAccountHarvestId = $updateAccountAPI["newharvestid"];
                $updateAccountRunId = $updateAccountAPI["newrunid"];

                $details = $cashedgeObject->createAccounts($updateAccountRunId, $updateAccountHarvestId, $accountXML, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
                unset($updateAccountAPI);
            }
            $accdetails = self::addItemToDB($details, $cid, $user_id);
            Yii::trace(CVarDumper::dumpAsString($accdetails) . " --> PENDING ACCOUNTS ADDED WFLOW ", "cashedgedata");

            /* updateAccounts API is called: */
            $updateAccountAPI = $cashedgeObject->updateAccounts($cashedgeItemDetails->FILoginAcctId, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
            $updateAccountHarvestId = $updateAccountAPI["newharvestid"];
            $updateAccountRunId = $updateAccountAPI["newrunid"];
            Yii::trace(CVarDumper::dumpAsString($updateAccountAPI) . "-->7 PENDING ACCOUNT WFLOW updateAccounts response", "cashedgedata");

            if (isset($updateAccountRunId) && isset($updateAccountHarvestId)) {
                $cashedgeItemDetails->lsstatus = 10;
                $cashedgeItemDetails->save();
            }

            /* getHarvestStatus API is called: */
            $accHarvestAPI = $cashedgeObject->getHarvestStatus($cashedgeUserDetails->username, $cashedgeUserDetails->cpassword, $updateAccountHarvestId, $updateAccountRunId);

            Yii::trace(CVarDumper::dumpAsString($accHarvestAPI) . "-->8  PENDING ACCOUNT WFLOW getHarvestStatus response", "cashedgedata");

            if ($accHarvestAPI["status"] == "OK") {
                $cashedgeItemDetails->lsstatus = 11;
                # Some Logging has to happen here.
                #THAYUB
                $cashedgeItemDetails->RunId = $updateAccountRunId;
                $cashedgeItemDetails->HarvestAddID = $updateAccountHarvestId;
                $cashedgeItemDetails->msg = "Your accounts have been classified and downloaded successfully.";
                $cashedgeItemDetails->save();
                Yii::trace($accHarvestAPI["status"] . "-->8 getHarvestStatus response YOYO", "cashedgedata");
            }
        }


        if (isset($invests) && ($invests != 0)) {
            $counter = 0;
            foreach ($invests as $invest) {
                if (isset($invest) && !empty($invest)) {


                    $investArr = explode("|", $invest);
                    if(count($investArr) < 4) {
                        $investArr = explode("#", $invest);
                    }
                    $cashedgeObject = Yii::app()->cashedge;
                    $cashedgeUserDetails = CashedgeAccount::model()->find("user_id = :user_id", array("user_id" => $user_id));
                    $FIId = isset($investArr[0]) ? $investArr[0] : 0;
                    $AcctId = isset($investArr[6]) ? $investArr[6] : 0;
                    $AcctTypeId = isset($investArr[1]) ? $investArr[1] : 0;
                    $Instrument = isset($investArr[8]) ? $investArr[8] : 0;
                    $RetirementStatus = isset($investArr[7]) ? $investArr[7] : 0;
                    $AccountOwnerShip = isset($investArr[9]) ? $investArr[9] : 0;

                    Yii::trace(CVarDumper::dumpAsString($invest) . "-->ARRAY", "cashedgedata");

                    $assetClas = Assets::model()->find("actid =:actid",array("actid"=>$AcctId));
                    $assetClas->classified = 1;
                    $assetClas->save();

                    $maintainAcc = $cashedgeObject->maintainAccount($cashedgeUserDetails->username, $cashedgeUserDetails->cpassword, $FIId, $AcctId, 'INV', 'INV', $AcctTypeId, $Instrument, $RetirementStatus, $AccountOwnerShip);
                    Yii::trace(CVarDumper::dumpAsString($maintainAcc) . "-->INV RECLASSIFICATION", "cashedgedata");
                    // Error logging should happen here
                    $counter++;
                }
            }
            // This is for fix it:
            $cashedgeItemDetails->lsstatus = 11;
            $cashedgeItemDetails->save();
        }
        $accPendingDone = isset($accdetails) ? $accdetails : 0;
        $invUpdated = isset($counter) ? $counter : 0;



        self::actionUpdateaccountsInternal($cashedgeItemDetails->FILoginAcctId);

        self::notificationsFn($cashedgeItemDetails->FILoginAcctId, 'CLASSIFY', $this->mess8, $this->temp4, 0, $cashedgeItemDetails->id, $user_id);

        /*  Checking if there is another row with the same FiLoginAcctID and row ID greater than the current row */
        /*  New model agreed by Melroy and Jeff */
/**** Melroy - Question: When did i agree to this? We agreed to anything with a lower id gets deleted, not the higher ones. Since the higher ones are the newer ones.
               Question: Where is the initialization of this notification object? Also why would it replace the above notification?
*****/
        $existsAcc = CashedgeItem::model()->find("id > :id AND FILoginAcctId = :FILoginAcctId",array("id"=>$cashedgeItemDetails->id , "FILoginAcctId" => $cashedgeItemDetails->FILoginAcctId));
        if (isset($existsAcc)){
            $existsAcc->status = 1;
            $existsAcc->save();
            self::notificationsFn($cashedgeItemDetails->FILoginAcctId, null, null, null, 1, $cashedgeItemDetails->id, $user_id);
        }else{
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "connected" => $accPendingDone, "updatedCount" => $invUpdated , "nonew" => '1')));
        }


    }
####################################################################################################################################
    /* FUNCTION CASCADING DELETE:
       BROKEN BROKEN!!!
     */
    public function actionDeleteCascade($user_id) {

        // get each user FIloginAcctid
        //$userAll = CashedgeItem::model()->findAllBySql("SELECT user_id,FILoginAcctId from cashedgeitem where deleted=0");
        if(!empty($user_id)){
         $userAll = CashedgeItem::model()->findAllBySql("SELECT user_id,FILoginAcctId from cashedgeitem where user_id='".$user_id."'");
        }else{
        // get each user FIloginAcctid
        $userAll = CashedgeItem::model()->findAllBySql("SELECT user_id,FILoginAcctId from cashedgeitem where deleted=2");
        }
        // Get all the items from Insurance, Debts and Assets that are deleted & not deleted.
        foreach ($userAll as $each) {

            $id = $each->FILoginAcctId;

            $assetsObj1 = Assets::model()->count("status in ('0','1') and FILoginAcctId=:id", array("id" => $id));
            $debtsObj1 = Debts::model()->count("status in ('0','1')  and FILoginAcctId=:id", array("id" => $id));
            $insuranceObj1 = Insurance::model()->count("status in ('0','1') and FILoginAcctId=:id", array("id" => $id));

            $userAnyAcct = ($assetsObj1 + $debtsObj1 + $insuranceObj1);

            if ($userAnyAcct == 0) {
                // Removing the FILoginAcctID
                self::actionDeletefiloginacctid($each->user_id, $fId);

                $user_id = $each->user_id;
                // Checking for other accouts for that user
                $assetsObj2 = Assets::model()->count("status in ('0','1') and user_id=:user_id", array("user_id" => $user_id));
                $debtsObj2 = Debts::model()->count("status in ('0','1')  and user_id=:user_id", array("user_id" => $user_id));
                $insuranceObj2 = Insurance::model()->count("status in ('0','1') and user_id=:user_id", array("user_id" => $user_id));

                $userAcctExists = ($assetsObj2 + $debtsObj2 + $insuranceObj2);

                if ($userAcctExists == 0) {
                    // Removing the user for CE:
                    self::CEdeleteUser($user_id);
                }
            }
        }
    }
####################################################################################################################################
    /*                          */
    public function actionDeletefiAcctid($argAcctId = 0, $arguser_id = 0, $argvalue = 0) {
        try {
            $FIAcctId = isset($_GET["AcctId"]) ? $_GET["AcctId"] : $argAcctId;
            $user_id = isset($_GET["uid"]) ? $_GET["uid"] : $arguser_id;
            $value = isset($_GET["value"]) ? $_GET["value"] : $argvalue;

            if ($FIAcctId == 0) {
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "All values are required")));
            }

            switch ($value) {
                case "ASSE":
                    $result = Assets::model()->findBySql("SELECT accttype,FILoginAcctId FROM assets WHERE actid=:id", array("id" => $FIAcctId));
                    break;
                case "DEBT":
                    $result = Debts::model()->findBySql("SELECT accttype,FILoginAcctId FROM debts WHERE actid=:id", array("id" => $FIAcctId));
                    break;
                case "INSU":
                    $result = Insurance::model()->findBySql("SELECT accttype,FILoginAcctId FROM insurance WHERE actid=:id", array("id" => $FIAcctId));
                    break;
            }

            $AcctValue = explode('&', $result->accttype);
            $FILoginAcctId = $result->FILoginAcctId;

            $cashedgeObject = Yii::app()->cashedge;
            $cashedgeUserDetails = CashedgeAccount::model()->find("user_id = :user_id", array("user_id" => $user_id));
            $status = $cashedgeObject->deleteAccountByAccountId($FIAcctId, $AcctValue[1], $AcctValue[0], $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
            //
            if ($status) {

                /*Checking if the user has any more accounts in that FiLoginAcctID*/
                $result_1 = Assets::model()->findBySql("SELECT id FROM assets WHERE FILoginAcctId =:id AND status= 0", array("id" => $FILoginAcctId));
                $result_2 = Debts::model()->findBySql("SELECT id FROM debts WHERE FILoginAcctId =:id AND status= 0", array("id" => $FILoginAcctId));
                $result_3 = Insurance::model()->findBySql("SELECT id FROM insurance WHERE FILoginAcctId =:id AND status= 0", array("id" => $FILoginAcctId));

                $countResult = count($result_1) + count($result_2) + count($result_3);

                if ($countResult == 0){
                    $removeUserFromCe = $cashedgeObject->deleteAccountByFiLoginAcctId($FILoginAcctId,$cashedgeUserDetails->username, $cashedgeUserDetails->cpassword,'');
                }

                //delete the entry in the database
                $celogging = new Celog();
                $celogging->action = 'Delete';
                $celogging->flloginacctid = $result->FILoginAcctId;
                $celogging->acctid = $FIAcctId;
                $celogging->status = '1';
                $celogging->save();
                return;
            } else {
                //
                $celogging = new Celog();
                $celogging->action = 'Delete';
                $celogging->flloginacctid = $result->FILoginAcctId;
                $celogging->acctid = $FIAcctId;
                $celogging->status = '0';
                $celogging->save();
                return;
            }
        } catch (Exception $e) {

        }
    }
####################################################################################################################################
    /*                          */
    public function actionDeletefiloginacctid($user_id=0,$FILoginAcctId=0) {
        try {

            $cashedgeObject = Yii::app()->cashedge;
            $cashedgeUserDetails = CashedgeAccount::model()->find("user_id = :user_id", array("user_id" => $user_id));
            $status = $cashedgeObject->deleteAccountByFiLoginAcctId($FILoginAcctId, $cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);
            //

            if ($status) {
                //delete the entry in the database
                $cashedgeItemObj = CashedgeItem::model()->find("user_id=:user_id and FILoginAcctId = :FILoginAcctId", array("user_id" => $user_id, "FILoginAcctId" => $FILoginAcctId));
                $cashedgeItemObj->deleted = 1;
                $cashedgeItemObj->save();

                $log = new Celog();
                $log->action = 'Delete FIloginAcctid';
                $log->flloginacctid = $FILoginAcctId;
                $log->acctid = '';
                $log->status = '1';
                $log->save();

                $notify = Notification::model()->find("user_id=:user_id AND context IN ('LOGIN') AND message LIKE '%:fid%'",array("user_id" => $user_id , "fid" => $FILoginAcctId));
                $notify->status = 1;
                $notify->save();

                return;
            } else {
                $log = new Celog();
                $log->action = 'Delete FIloginAcctid';
                $log->flloginacctid = $FILoginAcctId;
                $log->acctid = '';
                $log->status = '0';
                $log->save();
            }
        } catch (Exception $E) {

        }
    }
####################################################################################################################################
    /*                          */
    public function actionUpdateaccountsInternal($fid) {
        try {
            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
            if ($user_id == 0) {

                // Error Logging:
                $log = new Celog();
                $log->action = 'Update Accounts for TICKER';
                $log->flloginacctid = $fid;
                $log->acctid = '';
                $log->user_id = 0;
                $log->status = '0';
                $log->save();

                return;
            }

            $cashedgeComp = Yii::app()->cashedge;
            $cashedgeUserDetails = CashedgeAccount::model()->find("user_id = :user_id", array("user_id" => $user_id));

            $details = $cashedgeComp->getAccountsSummarytest($cashedgeUserDetails->username, $cashedgeUserDetails->cpassword, $fid);

            //update the database with the session for the user
            $cashedgeUserDetails->sesinfo = $details["session"];
            $cashedgeUserDetails->save();
            self::addUpdateToDB($details, $user_id);

            if (isset($details["id"])) {

                $cashedgeItemDetails = CashedgeItem::model()->find("FILoginAcctId =:id", array("id" => $details["id"]));

                $log = new Celog();
                $log->action = 'Update Accounts for TICKER';
                $log->flloginacctid = $details["id"];
                $log->acctid = '';
                $log->user_id = $user_id;
                $log->status = '1';
                $log->save();
            }

            return true;
        } catch (Exception $e) {
            //
        }
    }
####################################################################################################################################
   /**
     *
     */
    function addItemToDB($details, $cid, $user_id) {

        $cashedgeItemDetails = CashedgeItem::model()->findByPk($cid);
        $accountsCreatedArr = array();
        $FILoginAcctId = $cashedgeItemDetails->FILoginAcctId;

        Yii::trace(":API call:" . "JEFF CHECK", "cashedgedata");
        Yii::trace(":Type:" . gettype($details), "cashedgedata");
        Yii::trace("US BANK OUTSIDE" . CVarDumper::dumpAsString($details), "cashedgedata");

        if (!empty($details) && $details["status"] != "ERROR") {

            if (isset($details["accounts"])) {
                /* Map and check category types */
                $accounts = $details["accounts"];
                $accountMapping = new AccountMapping();
                $totalassets = Assets::model()->count("user_id=:user_id", array("user_id" => $user_id));
                $totalinsurance = Insurance::model()->count("user_id=:user_id", array("user_id" => $user_id));
                $totaldebts = Debts::model()->count("user_id=:user_id", array("user_id" => $user_id));

                foreach ($accounts as $acc) {
                    $accountsCreated = array();
                    $accAccName = isset($acc["FIAcctNameParamVal"]) ? $acc["FIAcctNameParamVal"] : $acc["AcctNumber"];
                    if (isset($acc["ExtAcctType"])) {
                        $accounttype = $accountMapping->getAccountMapping($acc["ExtAcctType"], "ExtAcctType");
                    } else if (isset($acc["AcctType"])) {
                        $accounttype = $accountMapping->getAccountMapping($acc["AcctType"], "AcctType");
                    }
                    if (isset($accounttype["table"])) {
                        $tableORM = $accounttype["table"];
                        /* Creating table object */
                        $ORMObj = new $tableORM();

                        $ORMObj->user_id = $user_id;
                        $ORMObj->refid = $cid;
                        $ORMObj->context = "AUTO";
                        $ORMObj->type = $accounttype["type"];
                        if($ORMObj->type == 'LIFE') {
                            $ORMObj->insurancefor = 80;
                            $ORMObj->beneficiary = 81;
                        }
                        $ORMObj->subtype = $accounttype["subtype"];
                        $ORMObj->name = $accAccName;
                        $ORMObj->FILoginAcctId = $FILoginAcctId;
                        $ORMObj->accttype = ($acc["ExtAcctType"] . '&' . $acc["AcctType"]);
                        $ORMObj->actid = $acc["AcctId"];

                        if ($accounttype["table"] == "Assets"){
                            $ORMObj->classified = 0;
                        }
                        if ($accounttype["table"] == "Assets") {
                            $totalassets++;
                            $ORMObj->priority = $totalassets;

                            /* For Assets: AccBal shows up as the following via CreateAccounts API
                                   'AccBal' => array
                                        (
                                            0 => array
                                            (
                                                'AccBalType' => 'Current'
                                                'CurAmt' => '3897.52'
                                                'CurCode' => 'USD'
                                            )
                                            1 => array
                                            (
                                                'AccBalType' => 'Avail'
                                                'CurAmt' => '3897.52'
                                                'CurCode' => 'USD'
                                            )
                                        )
                            */
                            foreach ($acc["AccBal"] as $eachAccType => $value) {
                                if (isset($value['AccBalType']) && (($value['AccBalType']) == 'Current')) {
                                    $ORMObj->balance = $value['CurAmt'];
                                }
                            }

                        } else if ($accounttype["table"] == "Debts") {
                            $totaldebts++;
                            $ORMObj->priority = $totaldebts;

                            /* For Debts: AccBal shows up as the following via CreateAccounts API
                                    'AccBal' => array
                                    (
                                        0 => array
                                        (
                                            'AccBalType' => 'Current'
                                            'CurAmt' => '3015.1'
                                            'CurCode' => 'USD'
                                        )
                                        1 => array
                                        (
                                            'AccBalType' => 'Avail'
                                            'CurAmt' => '2000.0'
                                            'CurCode' => 'USD'
                                        )
                                    )
                            */
                            foreach ($acc["AccBal"] as $eachAccType => $value) {
                                if (isset($value['AccBalType']) && (($value['AccBalType']) == 'Current')) {
                                    $ORMObj->balowed = $value['CurAmt'];
                                }
                            }
                        } else if ($accounttype["table"] == "Insurance") {
                            $totalinsurance++;
                            $ORMObj->priority = $totalinsurance;

                            foreach ($acc["AccBal"] as $eachAccType => $value) {
                                /* This is for INSURANCE: */
                                if ($accounttype["type"] == 'LIFE') {
                                    if (isset($value['AccBalType']) && (($value['AccBalType']) == 'TotalBrokerageAccountValue')) {
                                        //$ORMObj->balowed =  $value['CurAmt'];
                                    }
                                    if (isset($value['AccBalType']) && (($value['AccBalType']) == 'Securities')) {
                                        //$ORMObj->amtpermonth =  $value['CurAmt'];
                                    }
                                    if (isset($value['AccBalType']) && (($value['AccBalType']) == 'CashSurrenderValue')) {
                                        $ORMObj->cashvalue = $value['CurAmt'];
                                    }
                                    if (isset($value['AccBalType']) && (($value['AccBalType']) == 'DeathBenefit')) {
                                        $ORMObj->amtupondeath = $value['CurAmt'];
                                    }
                                    if (isset($value['AccBalType']) && (($value['AccBalType']) == 'InsurancePremium')) {
                                        $ORMObj->annualpremium = $value['CurAmt'];
                                    }
                                    if (isset($value['AccBalType']) && (($value['AccBalType']) == 'LoanAmount')) {
                                        //$ORMObj->amtpermonth =  $value['CurAmt'];
                                    }
                                }
                            }
                        }
                        /* Saving the data to the table */
                        if ($ORMObj->save()) {
                            if ($accounttype["table"] == "Assets") {
                                Yii::import('application.controllers.AssetController');
                                $obj = new AssetController(1); // preparing object

                                $obj->actionreCalculateScoreAssets($ORMObj, "ADD", $user_id, 1);
                            } else if ($accounttype["table"] == "Debts") {
                                Yii::import('application.controllers.DebtController');
                                $obj = new DebtController(1); // preparing object

                                $obj->actionreCalculateScoreDebts($ORMObj, "ADD", $user_id, 1);
                            } else if ($accounttype["table"] == "Insurance") {
                                Yii::import('application.controllers.InsuranceController');
                                $obj = new InsuranceController(1); // preparing object

                                $obj->actionreCalculateScoreInsurance($ORMObj, "ADD", $user_id, 1);
                            }
                        }

                        $accountsCreated = array(
                            "id" => $ORMObj->id,
                            "name" => self::mask_number($ORMObj->name),
                            "actid" => $ORMObj->actid,
                            "accounttype" => $accounttype["table"],
                            "mapping" => $accounttype
                        );
                    }
                    $accountsCreatedArr[] = $accountsCreated;
                }
                /*update lsstatus */
                //$cashedgeItemDetails->lsstatus = 1;
                $cashedgeItemDetails->msg = "Your accounts have been added successfully.";
                $cashedgeItemDetails->accountdetails = serialize($details);
                $cashedgeItemDetails->save();

                $cashedgeObject = Yii::app()->cashedge;
                $cashedgeUserDetails = CashedgeAccount::model()->find("user_id = :user_id", array("user_id" => $user_id));
            }
        } else if (!empty($details) && $details["status"] == "ERROR") {
        } else {
        }
        Yii::trace("Time:" . date('r'));
        Yii::trace(":API call:" . "Add ITEM TO DB", "cashedgedata");
        Yii::trace(":Type:" . gettype($accountsCreatedArr), "cashedgedata");
        Yii::trace(":Data:" . CVarDumper::dumpAsString($accountsCreatedArr), "cashedgedata");
        Yii::trace("END ==============================", "cashedgedata");

        return $accountsCreatedArr;
    }
####################################################################################################################################
    /**
     *
     */
    function addUpdateToDB($details, $user_id) {
        Yii::trace(":UPDATEDB:" . CVarDumper::dumpAsString($details), "cashedgedata");
        $accounts = $details["accounts"];
        $accountMapping = new AccountMapping();
        foreach ($accounts as $acc) {
            if (isset($acc["ExtAcctType"])) {
                $accounttype = $accountMapping->getAccountMapping($acc["ExtAcctType"], "ExtAcctType");
            } else if (isset($acc["AcctType"])) {
                $accounttype = $accountMapping->getAccountMapping($acc["AcctType"], "AcctType");
            }
            if (isset($accounttype["table"])) {
                $tableORM = $accounttype["table"];
                $ORMObj = new $tableORM();
                $rowDetails = $ORMObj->find("user_id = :user_id and context='AUTO' and actid = :actid", array("user_id" => $user_id, "actid" => $acc["AcctId"]));

                if ($accounttype["table"] == "Assets") {
                    foreach ($acc["AccBal"] as $eachAccType => $value) {
                        /* For Bank: AccBal,Property shows up as the following via UpdateAccounts API
                        Bank:
                            'AccBal' => array
                            (
                                0 => array
                                (
                                    'AccBalType' => 'Current'
                                    'CurAmt' => '893.21'
                                    'CurCode' => 'USD'
                                )
                                1 => array
                                (
                                    'AccBalType' => 'Avail'
                                    'CurAmt' => '893.21'
                                    'CurCode' => 'USD'
                                )
                            )
                            'Property' => array
                            (
                                0 => array
                                (
                                    'Name' => 'RetirementStatus'
                                    'Val' => 'Not Selected'
                                )
                                1 => array
                                (
                                    'Name' => 'Instrument'
                                    'Val' => 'N/A'
                                )
                                2 => array
                                (
                                    'Name' => 'AccountOwnership'
                                    'Val' => 'N/A'
                                )
                            )
                        */
                        if ($accounttype["type"] == 'BANK') {
                            if (isset($value['AccBalType']) && (($value['AccBalType']) == 'Current')) {
                                $rowDetails->balance = $value['CurAmt'];
                            }
                        }

                        /* For Investment: AccBal,Property shows up as the following via UpdateAccounts API
                            'AccBal' => array
                            (
                                0 => array
                                (
                                    'AccBalType' => 'Cash'
                                    'CurAmt' => '4222.758067399313'
                                    'CurCode' => 'USD'
                                )
                                1 => array
                                (
                                    'AccBalType' => 'MMF'
                                    'CurAmt' => '5624.705300259751'
                                    'CurCode' => 'USD'
                                )
                                2 => array
                                (
                                    'AccBalType' => 'TotalBrokerageAccountValue'
                                    'CurAmt' => '343124.2943963405'
                                    'CurCode' => 'USD'
                                )
                                3 => array
                                (
                                    'AccBalType' => 'Securities'
                                    'CurAmt' => '333276.83102868136'
                                    'CurCode' => 'USD'
                                )
                                4 => array
                                (
                                    'AccBalType' => 'CurrentVestedBalance'
                                    'CurAmt' => '333085.2360496474'
                                    'CurCode' => 'USD'
                                )
                            )
                            'Property' => array
                            (
                                0 => array
                                (
                                    'Name' => 'RetirementStatus'
                                    'Val' => 'Non Retirement'
                                )
                                1 => array
                                (
                                    'Name' => 'Instrument'
                                    'Val' => 'Brokerage'
                                )
                                2 => array
                                (
                                    'Name' => 'AccountOwnership'
                                    'Val' => 'Individual'
                                )
                            )
                        */
                        if ($accounttype["type"] == 'BROK') {
                            if (isset($value['AccBalType']) && (($value['AccBalType']) == 'TotalBrokerageAccountValue')) {
                                $rowDetails->balance = $value['CurAmt'];
                            }
                        }
                    }


                    /* THis is for the classification mapping: */
                    foreach ($acc["Property"] as $eachAccType => $value) {
                        if (isset($value["Name"]) && ($value["Name"]) == 'Instrument') {

                            if (isset($value["Val"]) && ($value["Val"] == '529 / Education Savings' )) {
                                $rowDetails->type = 'EDUC';
                            }
                            if (isset($value["Val"]) && ($value["Val"] == 'Brokerage' )) {
                                $rowDetails->type = 'BROK';
                            }
                            if (isset($value["Val"]) && ($value["Val"] == '401 (k)' )) {
                                $rowDetails->type = 'CR';
                            }
                            if (isset($value["Val"]) && ($value["Val"] == '403 (k)' )) {
                                $rowDetails->type = 'CR';
                            }
                            if (isset($value["Val"]) && ($value["Val"] == '457' )) {
                                $rowDetails->type = 'CR';
                            }
                            if (isset($value["Val"]) && ($value["Val"] == 'Deferred Comp' )) {
                                $rowDetails->type = 'CR';
                            }
                            if (isset($value["Val"]) && ($value["Val"] == 'KEOGH' )) {
                                $rowDetails->type = 'CR';
                            }
                            if (isset($value["Val"]) && ($value["Val"] == 'Pension' )) {
                                $rowDetails->type = 'PENS';
                            }
                            if (isset($value["Val"]) && ($value["Val"] == 'Profit Sharing Plan' )) {
                                $rowDetails->type = 'CR';
                            }
                            if (isset($value["Val"]) && ($value["Val"] == 'IRA' )) {
                                $rowDetails->type = 'IRA';
                                $rowDetails->assettype = 52;
                            }
                            if (isset($value["Val"]) && ($value["Val"] == 'Rollover IRA' )) {
                                $rowDetails->type = 'IRA';
                                $rowDetails->assettype = 52;
                            }
                            if (isset($value["Val"]) && ($value["Val"] == 'Roth IRA' )) {
                                $rowDetails->type = 'IRA';
                                $rowDetails->assettype = 51;
                            }
                            if (isset($value["Val"]) && ($value["Val"] == 'SEP IRA' )) {
                                $rowDetails->type = 'IRA';
                                $rowDetails->assettype = 52;
                            }
                            if (isset($value["Val"]) && ($value["Val"] == 'Simple IRA' )) {
                                $rowDetails->type = 'IRA';
                                $rowDetails->assettype = 52;
                            }
                            if (isset($value["Val"]) && ($value["Val"] == 'Employer Stock Account' )) {
                                $rowDetails->type = 'CR';
                            }
                        }
                    }

                    /* Specific to assets */
                    $rowDetails->ticker = isset($acc["Ticker"]) ? $acc["Ticker"] : "";
                } else if ($accounttype["table"] == "Debts") {

                    foreach ($acc["AccBal"] as $eachAccType => $value) {

                        /* This is for LOAN  & MORT : */
                        if ($accounttype["type"] == 'LOAN' || $accounttype["type"] == 'MORT') {

                            if ($accounttype["subtype"] == 'Bill') {
                                if (isset($value['AccBalType']) && (($value['AccBalType']) == 'AmountDue')) {
                                    $rowDetails->amtpermonth = $value['CurAmt'];
                                    $rowDetails->balowed = $value['CurAmt'];
                                }
                            } else {
                                if (isset($value['AccBalType']) && (($value['AccBalType']) == 'AmountDue')) {
                                    $rowDetails->amtpermonth = $value['CurAmt'];
                                }
                                if (isset($value['AccBalType']) && (($value['AccBalType']) == 'Current')) {
                                    $rowDetails->balowed = $value['CurAmt'];
                                }
                            }
                        }


                        // This is for CREDIT CARD:
                        if ($accounttype["type"] == 'CC') {
                            if (isset($value['AccBalType']) && (($value['AccBalType']) == 'Outstanding')) {
                                $rowDetails->balowed = $value['CurAmt'];
                            }
                            if (isset($value['AccBalType']) && (($value['AccBalType']) == 'MinimumPaymentDue')) {
                                $rowDetails->amtpermonth = $value['CurAmt'];
                            }
                            if (isset($value['AccBalType']) && (($value['AccBalType']) == 'Current')) {
                                $rowDetails->balowed = $value['CurAmt'];
                            }
                            if (isset($value['AccBalType']) && (($value['AccBalType']) == 'PurchasesApr')) {
                                $rowDetails->apr = $value['CurAmt'];
                            }
                        }
                    }
                } else if ($accounttype["table"] == "Insurance") {
                    foreach ($acc["AccBal"] as $eachAccType => $value) {
                        /* This is for INSURANCE: */
                        if ($accounttype["type"] == 'LIFE') {
                            if (isset($value['AccBalType']) && (($value['AccBalType']) == 'TotalBrokerageAccountValue')) {
                                //$rowDetails->balowed =  $value['CurAmt'];
                            }
                            if (isset($value['AccBalType']) && (($value['AccBalType']) == 'Securities')) {
                                //$rowDetails->amtpermonth =  $value['CurAmt'];
                            }
                            if (isset($value['AccBalType']) && (($value['AccBalType']) == 'CashSurrenderValue')) {
                                $rowDetails->cashvalue = $value['CurAmt'];
                            }
                            if (isset($value['AccBalType']) && (($value['AccBalType']) == 'DeathBenefit')) {
                                $rowDetails->amtupondeath = $value['CurAmt'];
                            }
                            if (isset($value['AccBalType']) && (($value['AccBalType']) == 'InsurancePremium')) {
                                $rowDetails->annualpremium = $value['CurAmt'];
                            }
                            if (isset($value['AccBalType']) && (($value['AccBalType']) == 'LoanAmount')) {
                                //$rowDetails->amtpermonth =  $value['CurAmt'];
                            }
                        }
                    }
                }
                if ($rowDetails->save()) {
                    if ($accounttype["table"] == "Assets") {
                        Yii::import('application.controllers.AssetController');
                        $obj = new AssetController(1); /* Preparing object */

                        $obj->actionreCalculateScoreAssets($rowDetails, "UPDATE", $user_id, 1);
                    } else if ($accounttype["table"] == "Debts") {
                        Yii::import('application.controllers.DebtController');
                        $obj = new DebtController(1); /* Preparing object */

                        $obj->actionreCalculateScoreDebts($rowDetails, "UPDATE", $user_id, 1);
                    } else if ($accounttype["table"] == "Insurance") {
                        Yii::import('application.controllers.InsuranceController');
                        $obj = new InsuranceController(1); /* Preparing object */

                        $obj->actionreCalculateScoreInsurance($rowDetails, "UPDATE", $user_id, 1);
                    }
                }
            }
        }
        $tickers = $details["tickers"];

        foreach ($tickers as $tick) {
            $assetsObj = Assets::model()->find("actid=:actid", array("actid" => $tick["acctid"]));
            if ($assetsObj) {
                $assetsObj->invpos = json_encode($tick["invpos"]);
                $assetsObj->save();

                $obj = new AssetController(1); /* Preparing object */
                $obj->actionreCalculateScoreAssets($assetsObj, "UPDATE", $user_id, 1);
            } else {
                $insuranceObj = Insurance::model()->find("actid=:actid", array("actid" => $tick["acctid"]));
                if ($insuranceObj) {
                    $insuranceObj->invpos = json_encode($tick["invpos"]);
                    $insuranceObj->save();
                }
            }
        }
        return true;
    }
####################################################################################################################################
    /**
     *
     * @param type $number
     * @param type $count
     * @param type $seperators
     * @return string
     */
    function mask_number($number, $count = 4, $seperators = '-') {
        $masked = preg_replace('/\d/', 'x', $number);
        $last = preg_match(sprintf('/([%s]?\d){%d}$/', preg_quote($seperators), $count), $number, $matches);
        if ($last) {
            list($clean) = $matches;
            $masked = substr($masked, 0, -strlen($clean)) . $clean;
        }
        return $masked;
    }
####################################################################################################################################
    /* get fi connect details */
    public function actionFiconnect() {
        try {

            if (!empty($_GET["fiid"])) {

                $fiid = $_GET["fiid"];

                $accountDetails = CashedgefiDetails::model()->find("FIId = :fiid", array("fiid" => $fiid));

                if (isset($accountDetails) && !empty($accountDetails)) {
                    $totalRecords = null;
                    $eachRecord = array(
                        "serviceId" => $accountDetails->FIId,
                        "displayName" => $accountDetails->FIName,
                        "country" => $accountDetails->Country,
                        "URL" => json_decode($accountDetails->URL),
                        "accountSupported" => json_decode($accountDetails->AccountSupported),
                        "loginParams" => json_decode($accountDetails->FILoginParametersInfo)
                    );
                    $totalRecords[] = $eachRecord;
                    $this->sendResponse(200, CJSON::encode(array("status" => "OK", "items" => $totalRecords)));
                }
            }
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Could not get FI Details.", "totalRecords" => false, "items" => array())));
        } catch (Exception $E) {
            /* General error message send to UI and log the error to logs */
        }
    }
####################################################################################################################################
    /* get session details */
    public function actionGetaccountdetails() {
        try {
            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            $user_id = Yii::app()->getSession()->get('wsuser')->id;

            $cashedgeComp = Yii::app()->cashedge;
            $cashedgeUserDetails = CashedgeAccount::model()->find("user_id = :user_id", array("user_id" => $user_id));

            $details = $cashedgeComp->getAccountDetails($cashedgeUserDetails->username, $cashedgeUserDetails->cpassword);

            //update the database with the session for the user
            $cashedgeUserDetails->sesinfo = $details["session"];
            $cashedgeUserDetails->save();
            self::addUpdateToDB($details, $user_id);

            $this->sendResponse(200, CJSON::encode($details));
        } catch (Exception $e) {
            /* */
        }
    }
####################################################################################################################################

    public function actionCEdeleteUser($userid) {

        $cashedgeObject = Yii::app()->cashedge;

        if ($userid != 0) {

            $cashedgeAcc = CashedgeAccount::model()->find("user_id=:user_id", array("user_id" => $userid));
            if($cashedgeAcc) {
                $result = $cashedgeObject->deleteUser($cashedgeAcc->username);

                if ($result['status'] == 'OK') {
                    $log = new Celog();
                    $log->action = 'Delete User';
                    $log->flloginacctid = '';
                    $log->acctid = '';
                    $log->user_id = $userid;
                    $log->status = '1';
                    $log->save();
                    return;
                }
            }
        }

        $log = new Celog();
        $log->action = 'Delete User';
        $log->flloginacctid = '';
        $log->acctid = '';
        $log->user_id = $userid;
        $log->status = '0';
        $log->save();
        return;
    }
####################################################################################################################################

    public function actionGetPendingLinks() {
        //if there is no session
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $cashedgeitemORMObj = CashedgeItem::model()->findAllBySql("SELECT * from cashedgeitem where user_id=:user_id and lsstatus NOT IN (8,11) and status = 0", array("user_id" => $user_id));
        // For MFA
        $cashedgeitemORMObj1 = CashedgeItem::model()->findAllBySql("SELECT * FROM  `cashedgeitem` WHERE  `user_id` =:user_id AND  `mfa` =1 AND  `cecheck` = 1 and status = 0", array("user_id" => $user_id));

        // For refreshing related issues:
        $cashedgeitemORMObj2 = CashedgeItem::model()->findAllBySql("SELECT * from cashedgeitem where user_id=:user_id and cecheck = 303 and status = 0", array("user_id" => $user_id));


        $downloadingAcc = false;
        if ((isset($cashedgeitemORMObj) && !empty($cashedgeitemORMObj))) {
            foreach ($cashedgeitemORMObj as $itemObj) {
                if ($itemObj->lsstatus == 0 || $itemObj->lsstatus == 3 || $itemObj->lsstatus == 4 || $itemObj->lsstatus == 5 || $itemObj->lsstatus == 6 || $itemObj->lsstatus == 7 || $itemObj->lsstatus == 10) {
                    $downloadingAcc = true;
                    break;
                }
            }
        }

        $lsCashedgeItemArr = array();

        foreach ($cashedgeitemORMObj as $cashedgeItem) {
            $lsCashedgeItem = array(
                'id' => $cashedgeItem->id,
                'name' => $cashedgeItem->name,
                'filoginacctid' => $cashedgeItem->FILoginAcctId,
                'message' => $cashedgeItem->msg
            );
            $lsCashedgeItemArr[] = $lsCashedgeItem;
        }
        foreach ($cashedgeitemORMObj1 as $cashedgeItem) {
            $lsCashedgeItem = array(
                'id' => $cashedgeItem->id,
                'name' => $cashedgeItem->name,
                'filoginacctid' => $cashedgeItem->FILoginAcctId,
                'message' => 'Please click notifications for further information'
            );
            $lsCashedgeItemArr[] = $lsCashedgeItem;
        }
        foreach ($cashedgeitemORMObj2 as $cashedgeItem) {
            $lsCashedgeItem = array(
                'id' => $cashedgeItem->id,
                'name' => $cashedgeItem->name,
                'filoginacctid' => $cashedgeItem->FILoginAcctId,
                'message' => $cashedgeItem->msg
            );
            $lsCashedgeItemArr[] = $lsCashedgeItem;
        }

        $allIds = array();

        foreach ($lsCashedgeItemArr as $eachArrEl) {
            if ($eachArrEl['filoginacctid'] > 0) {
                array_push($allIds, $eachArrEl['filoginacctid']);
            } else {
                array_push($allIds, $eachArrEl['id']);
            }
        }

        $uniqueIds = array_unique($allIds);
        $finalResult = array();
        $checkArray = array();

        foreach ($uniqueIds as $key => $uniqueId) {
            foreach ($lsCashedgeItemArr as $eachItem) {

                if ($uniqueId == $eachItem['filoginacctid']) {
                    if (!(in_array(($eachItem['filoginacctid']), $checkArray))) {
                        array_push($finalResult, $eachItem);
                        array_push($checkArray, $eachItem['filoginacctid']);
                    }
                } else if ($uniqueId == $eachItem['id']) {
                    if (!(in_array(($eachItem['id']), $checkArray))) {
                        array_push($finalResult, $eachItem);
                        array_push($checkArray, $eachItem['id']);
                    }
                }
            }
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "attempts" => $finalResult, 'accountsdownloading' => $downloadingAcc)));
    }
}
?>
