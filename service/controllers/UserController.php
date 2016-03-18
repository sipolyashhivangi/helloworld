<?php

/* * ********************************************************************
 * Filename: UserController.php
 * Folder: controllers
 * Description: User controller to carry out user related operation like
 *              adding info , personal details etc
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

// Yii::import('application.extensions.html2pdf'); # disabled since we are using it
require(realpath(dirname(__FILE__) . '/../components/MailChimp.php'));

class UserController extends Scontroller {

    public $activities = '';
    public $peerminAge = 21;
    public $peermaxAge = 75;
    public $peerdefaultAge = 30;
    public $scoreChangeDays = 90;
    public $percentage = 100;
    public $numberFormat = 2;

    public function __construct($id, $module = null) {
        parent::__construct($id, $module);

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        if (isset($wsUserObject)) {

            $sql = "select activitykey, activityvalue from roleactivities";
            $getUserMetaInfo = Roleactivities::model()->findAllBySql($sql, array("roleid" => $wsUserObject->roleid));
            if (count($getUserMetaInfo) > 0) {
                foreach ($getUserMetaInfo as $metaName) {
                    $resultArray[$metaName->activitykey] = $metaName->activityvalue;
                }
                $this->activities = $resultArray;
            } else {
                $this->activities = '0';
            }
        }
    }


    // Define access control
    public function accessRules() {
        return array_merge(
        array(array('allow', 'users' => array('?'))),
        // Include parent access rules
        parent::accessRules()
        );
    }


    /**
     *
     */
    public function actionAdduserinfoone() {

        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = $wsUserObject->id;
        $userDetails = User::model()->findByPk($user_id);

        $needsUpdate = false;
        $updateScoreEngine = false;

        if (isset($_POST["firstname"])) {
            $firstname = $_POST["firstname"];
            $userDetails->firstname = $firstname;
            $wsUserObject->firstname = $userDetails->firstname;
            $needsUpdate = true;
        }

        if (isset($_POST["lastname"])) {
            $lastname = $_POST["lastname"];
            $userDetails->lastname = $lastname;
            $wsUserObject->lastname = $userDetails->lastname;
            $needsUpdate = true;
        }

        if (isset($_POST["zip"])) {
            $zipcode = $_POST["zip"];
            $userDetails->zip = $zipcode;
            $needsUpdate = true;
        }

        if ($needsUpdate) {
            $userDetails->save();
            $updateScoreEngine = true;
        }

        $userSecurityQuestion = SecurityQuestion::model()->findAll(
        array('select' => array('id', 'question'))
        );

        $userSecurityResponse = UserSecurityAnswer::model()->findAll(
        "user_id = :user_id", array(':user_id' => $user_id)
        );
        $count = count($userSecurityResponse);
        while ($count++ < 3) {
            $userSecurityResponse[] = new UserSecurityAnswer();
        }

        if (isset($_POST["question_id"]) && isset($_POST["answer"])) {
            $i = 0;
            foreach ($userSecurityResponse as $model) {
                $model->user_id = $user_id;
                $model->question_id = $_POST['question_id'][$i];
                if ($model->answer != $_POST['answer'][$i] && $_POST['answer'][$i] != "") {
                    $hasher = PasswordHasher::factory();
                    $model->answer = $hasher->HashPassword($_POST['answer'][$i]);
                    $model->modifiedtimestamp = new CDbExpression('NOW()');
                } else if ($_POST['answer'][$i] == "") {
                    $model->answer = "";
                }
                $model->save();
                $i++;
            }
        }

        $userSecurityResponse = UserSecurityAnswer::model()->findAll(
        "user_id = :user_id", array(':user_id' => $user_id)
        );

        $needsUpdate = false;
        $userPerDetails = Userpersonalinfo::model()->find("user_id = :user_id", array(':user_id' => $user_id));
        if (!$userPerDetails) {
            $userPerDetails = new Userpersonalinfo();
            $userPerDetails->user_id = $user_id;
            $needsUpdate = true;
        }
        //update userprofile table
        if (isset($_POST["retirementstatus"])) {
            $retired = $_POST["retirementstatus"];
            $userPerDetails->retirementstatus = $retired;
            $needsUpdate = true;
        }

        if (isset($_POST["age"])) {
            $age = $_POST["age"];
            $userPerDetails->age = $age;
            $needsUpdate = true;
        }

        if (isset($_POST["noofchildren"])) {
            $noofchild = $_POST["noofchildren"];
            $userPerDetails->noofchildren = $noofchild;
            $needsUpdate = true;
        }

        if (isset($_POST["childrensage"])) {
            $childDOBs = $_POST["childrensage"];
            $userPerDetails->childrensage = $childDOBs;
            $needsUpdate = true;
        }

        if (isset($_POST["spouseage"])) {
            $spouseage = $_POST["spouseage"];
            $userPerDetails->spouseage = $spouseage;
            $needsUpdate = true;
        }

        if (isset($_POST["maritalstatus"])) {
            $maritalstatus = $_POST["maritalstatus"];
            $userPerDetails->maritalstatus = $maritalstatus;
            $needsUpdate = true;
        }

        if ($needsUpdate) {
            $userPerDetails->save();
            $updateScoreEngine = true;
        }

        $rates = Sustainablerates::model()->findAll(array('select' => array('age', 'sustainablewithdrawalpercent')));
        $rateArray = array();
        foreach ($rates as $rate) {
            $r = array();
            $r['age'] = $rate->age;
            $r['withdrawal'] = $rate->sustainablewithdrawalpercent;
            $rateArray[] = $r;
        }

        $userData = array(
        'firstname' => $userDetails->firstname,
        'lastname' => $userDetails->lastname,
        'email' => $userDetails->email,
        'zipcode' => $userDetails->zip,
        'rates' => $rateArray,
        'age' => $userPerDetails->age,
        'maritalstatus' => $userPerDetails->maritalstatus,
        'spouseage' => $userPerDetails->spouseage,
        'noofchildren' => $userPerDetails->noofchildren,
        'retirementstatus' => $userPerDetails->retirementstatus,
        'childrensage' => $userPerDetails->childrensage,
        'retirementage' => $userPerDetails->retirementage,
        'risk' => $userPerDetails->risk,
        'securityquestion' => $userSecurityQuestion,
        'permission' => ''
        );

        $userData['securityresponse'] = array();
        if ($userSecurityResponse) {
            foreach ($userSecurityResponse as $answer) {
                $userData['securityresponse'][] = array(
                'id' => $answer->id,
                'question_id' => $answer->question_id,
                'response_text' => $answer->answer
                );
            }
        }

        if ($updateScoreEngine) {

            parent::setEngine();

            // wfPoint38:
            // later we need to think of adding Picture for calculation
            /* if ($userPerDetails->userpic) {
              $this->sengine->userProfilePoints_pict = 1;
              } else {
              $this->sengine->userProfilePoints_pict = 0;
              } */
            //about you wfpoint38 calculation
            $count = 0;
            $count1 = 0;
            if ($userDetails->firstname != "" && $userDetails->lastname != "") {
                $count++;
            }
            $date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
            if (preg_match($date_regex, $userPerDetails->age)) {
                $count1++;
            }
            if ($userDetails->zip > 0) {
                $count++;
            }
            if ($userPerDetails->maritalstatus != "") {
                $count++;
            }
            if ($userPerDetails->noofchildren >= 0) {
                $count++;
            }
            if ($userPerDetails->noofchildren > 0) {
                $childrensAge = explode(",", $userPerDetails->childrensage);
                if (preg_match($date_regex, $childrensAge[0])) {
                    $count++;
                }
            } else {
                $count++;
            }
            if ($userPerDetails->retirementstatus != "") {
                $count++;
            }
            $this->sengine->userProfilePoints_aboutyou = ($count * 1.25 + $count1 * 2.50);

            parent::saveEngine();

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


            // Peer Ranking Calculation and Updation Starts
            $localpeerrank = 0;
            $nationalpeerrank = 0;
            $userage = $ageInYears;
            if (isset($userage)) {
                if ($userage <= $this->peerminAge) {
                    $userage = $this->peerminAge;
                }
                if ($userage >= $this->peermaxAge) {
                    $userage = $this->peermaxAge;
                }
            } else {
                $userage = $this->peerdefaultAge;
            }
            // Fetch Score for National
            $peerscoreData = Peerranking::model()->find(array('condition' => "baseage = :baseage", 'params' => array("baseage" => $userage), 'select' => 'weightage1'));

            if (isset($peerscoreData->weightage1)) {
                $nationalpeerrank = $peerscoreData->weightage1;
                $localpeerrank = $peerscoreData->weightage1;
            }

            // Get the user's zip code and compare it to the zips in the regiondetails table.
            $userZip = 0;
            $userZipData = User::model()->find(array('condition' => "id=:user_id", 'params' => array("user_id" => $user_id), 'select' => 'zip'));
            if (strlen($userZipData->zip) >= 5) {
                $userZip = intval(substr($userZipData->zip, 0, 3));
            }

            $regionId = 0;
            $regionalDependency = new CDbCacheDependency('SELECT * FROM regiondetails');
            $regionObjs = Regiondetails::model()->cache(QUERY_CACHE_TIMEOUT, $regionalDependency)->findAll();

            if ($regionObjs) {
                foreach ($regionObjs as $regionObj) {
                    if ($regionObj->zipcoderangeprefix) {
                        $stateZips = explode("|", $regionObj->zipcoderangeprefix);
                        foreach ($stateZips as $stateZip) {
                            $rangeZips = explode("-", $stateZip);
                            if (count($rangeZips) > 1) {
                                if ($userZip >= intval($rangeZips[0]) && $userZip <= intval($rangeZips[1])) {
                                    $regionId = $regionObj->region;
                                }
                            } else if ($userZip == intval($rangeZips[0])) {
                                $regionId = $regionObj->region;
                            }
                        }
                    }
                }
            }

            // get the row in the peerranking table based on the user's region and age.
            $peerscoreData = Peerranking::model()->findBySql('SELECT ROUND(score) AS total
            FROM peerranking AS P
            WHERE P.region = :rid AND P.baseage = :bage', array("bage" => $userage, "rid" => $regionId));
            if ($peerscoreData && $peerscoreData->total) {
                $localpeerrank = $peerscoreData->total;
            }

            $monteCarlo = false;
            parent::setEngine();
            if ($this->sengine->userCurrentAge != $ageInYears) {
                $monteCarlo = true;
                $this->sengine->userCurrentAge = $ageInYears;
            }
            $this->sengine->localPeerRank = $localpeerrank;
            $this->sengine->nationalPeerRank = $nationalpeerrank;

            if ($this->sengine->userCurrentAge < $userPerDetails->retirementage) {
                $this->sengine->userRetirementAge = $userPerDetails->retirementage;
            } else {
                $this->sengine->userRetirementAge = $this->sengine->userCurrentAge;
            }
            $this->sengine->yearToRetire = $this->sengine->userRetirementAge - $this->sengine->userCurrentAge;
            parent::saveEngine();

            $ageInYears = 0;
            $spouseRetAge = 0;
            $ageSplit = explode("-", $userPerDetails->spouseage);

            // X--, -X-, --X, X-X, -XX, XX-, --- <= Partial age dates get stored as 0000-00-00 in DB
            if (($userPerDetails->maritalstatus == 'Married' || $userPerDetails->maritalstatus == 'Domestic Union') && $ageSplit[0] != "0000") {
                $ageInYears = date('Y') - $ageSplit[0];
                if ($ageSplit[1] > date('m')) // Birth Month is later in the year
                    $ageInYears--;
                else if ($ageSplit[1] == date('m') && $ageSplit[2] > date('d')) // Birth day is later in the month
                    $ageInYears--;
                $spouseRetAge = $this->sengine->yearToRetire + $ageInYears;

                // If user gives us an age, then we look for life expectancy
                // Life expectancy values are based on users' ages in 2010
                $lifeexpAge = 2010 - $ageSplit[0];
                if ($lifeexpAge < 0)
                    $lifeexpAge = 0;

                $spouseLifeExpdependency = new CDbCacheDependency('SELECT FLifeExpectancy, baseyearage FROM lifeexpectancy');
                $spouselifeexp = Lifeexpectancy::model()->cache(QUERY_CACHE_TIMEOUT, $spouseLifeExpdependency)->find('baseyearage = ' . $lifeexpAge);
            }
            else if ($userPerDetails->maritalstatus == 'Married' || $userPerDetails->maritalstatus == 'Domestic Union') {
                $ageInYears = $this->sengine->userCurrentAge;
                $spouseRetAge = $this->sengine->userRetirementAge;
                if (isset($lifeexp)) {
                    $spouselifeexp = $lifeexp;
                }
            }

            parent::setEngine();
            $this->sengine->spouseAge = $ageInYears;
            $this->sengine->spouseRetAge = $spouseRetAge;
            $this->sengine->child1Age = 0;
            $this->sengine->child2Age = 0;
            $this->sengine->child3Age = 0;
            $this->sengine->child4Age = 0;
            parent::saveEngine();

            $ageArray = explode(",", $userPerDetails->childrensage);
            sort($ageArray);
            $max = 4;
            $index = 1;
            for ($i = 0; $i < $userPerDetails->noofchildren; $i++) {
                $ageSplit = explode("-", $ageArray[$i]);
                if ($ageSplit[0] != "0000") {
                    $ageFinal = date('Y') - $ageSplit[0];
                    if ($ageSplit[1] > date('m')) // Birth Month is later in the year
                        $ageFinal--;
                    else if ($ageSplit[1] == date('m') && $ageSplit[2] > date('d')) // Birth day is later in the month
                        $ageFinal--;

                    $value = 'child' . $index . 'Age';
                    if ($ageFinal < 18) {
                        parent::setEngine();
                        $this->sengine->$value = $ageFinal;
                        parent::saveEngine();
                        $index++;
                    }
                    if ($index > $max) {
                        break;
                    }
                }
            }

            parent::setEngine();
            if ($userPerDetails->retirementstatus == 1) {
                $this->sengine->retired = true;
                parent::saveEngine();
                $rates = Sustainablerates::model()->findbySql("SELECT * FROM sustainablerates WHERE age=:age", array("age" => $this->sengine->userCurrentAge));
                parent::setEngine();
                $sustainablewithdrawalpercent = isset($rates->sustainablewithdrawalpercent) ? $rates->sustainablewithdrawalpercent : 4;
                $this->sengine->sustainablewithdrawalpercent = $sustainablewithdrawalpercent;
            } else {
                $this->sengine->retired = false;
                parent::saveEngine();
                $rates = Sustainablerates::model()->findbySql("SELECT * FROM sustainablerates WHERE age=:age", array("age" => $this->sengine->userRetirementAge));
                parent::setEngine();
                $sustainablewithdrawalpercent = isset($rates->sustainablewithdrawalpercent) ? $rates->sustainablewithdrawalpercent : 4;
                $this->sengine->sustainablewithdrawalpercent = $sustainablewithdrawalpercent;
            }

            // Life Expectancy check
            if (!$this->sengine->setLifeEC) {
                if (isset($lifeexp)) {
                    $this->sengine->lifeEC = $lifeexp->FLifeExpectancy;
                    $this->sengine->enteredAge = true;
                } else {
                    $this->sengine->lifeEC = 82;
                }
            }

            // Spouse Life Expectancy check
            if (isset($spouselifeexp)) {
                $this->sengine->spouseLifeEC = $spouselifeexp->FLifeExpectancy;
            } else {
                $this->sengine->spouseLifeEC = 82;
            }
            parent::saveEngine();

            $retirementAmount = $this->sengine->retirementAmountDesired;
            parent::setupDefaultRetirementGoal();
            $calcs = "";
            if ($retirementAmount != $this->sengine->retirementAmountDesired) {
                $calcs = "GOAL|";
            }
            /* Renable when needed */
            $calcs .= "PROFILE";
            if ($monteCarlo) {
                $calcs .= "|DOB";
            }
            parent::calculateScore($calcs, $user_id);
        }

        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "userdata" => $userData, "message" => "User details updated successfully")));
    }


    /**
     *  Get all the user added account to user for financial snapshot page
     *
     */
    public function actionGetallitem() {
        $retAge = 0;
        //if there is no session for either client or advisor
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) && !isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $wsUserObject = Yii::app()->getSession()->get('wsuser');

        if (isset(Yii::app()->getSession()->get('wsadvisor')->id) && isset($_GET['user_id'])) {
            $user_id = $_GET['user_id']; // if request comes from advisor
            $advisor_id = Yii::app()->getSession()->get('wsadvisor')->id;
        } else {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
            $advisor_id = "";
        }

        parent::setEngine();
        //get any connected accounts in assets,debts and insurance table
        $sql = "select *,if(type='BANK','CASH',if(type in ('IRA','CR','BROK'),'INVESTMENT',if(type in ('SS','PENS'),'SILENT','OTHER'))) as lstype from assets where user_id=:user_id and status!=1 ORDER BY priority ASC";

        $singleORMAssetObj = Assets::model()->findAllBySql($sql, array("user_id" => $user_id));
        $debtsORMObj = Debts::model()->findAll("user_id=:user_id and status!=1 ORDER BY priority ASC", array("user_id" => $user_id));

        $insuranceORMObj = Insurance::model()->findAll("user_id=:user_id and status!=1 ORDER BY priority ASC", array("user_id" => $user_id));

        // lsstatus is modified to 1,2 to fill the harvesting[] in getallitems with pending and error accounts:
        #$cashedgeitemORMObj = CashedgeItem::model()->findAllBySql("select * from cashedgeitem where lsupdate in (1,2) and user_id=:user_id",array("user_id"=>$user_id));
        #$cashedgeitemORMObj = CashedgeItem::model()->findAllBySql("SELECT * from cashedgeitem where user_id=:user_id and (lsupdate in (1,2) or lsstatus in (100))", array("user_id" => $user_id));
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
        $netWorth = 0;

        $cashTotal = 0;
        $investmentTotal = 0;
        $silentTotal = 0;
        $otherTotal = 0;

        //$lsAccCASH = array();
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
            $arrayName = "lsAcc" . $acc->lstype;
            ${$arrayName}[] = $lsEachAcc;
            $secTotal = strtolower($acc->lstype) . "Total";
            ${$secTotal} = ${$secTotal} + $acc->balance;
        }
        $netWorth = $netWorth + $cashTotal;

        $debtTotal = 0;
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
            if ($acc->monthly_payoff_balances == 0) {
                $debtTotal = $debtTotal + $acc->balowed;
            }
        }
        $netWorth = $netWorth - $debtTotal;

        $insuranceTotal = 0;
        $lsAccINSURANCE = array();
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
            $lsAccINSURANCE[] = $lsEachAcc;
            $insuranceTotal = $insuranceTotal + $acc->cashvalue;
        }
        $netWorth = $netWorth + $insuranceTotal;

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
            #$data = unserialize($cashedgeItem->accountdetails);
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

        $estimation = Estimation::model()->find("user_id=:user_id", array("user_id" => $user_id));
        if ($estimation) {
            //get user estimates
            $lsUserEstimates = array(
            'income' => $estimation->houseincome,
            'expense' => $estimation->houseexpense,
            'assets' => $estimation->houseassets,
            'debts' => $estimation->housedebts,
            'savings' => $estimation->housesavings,
            'whichyouhave' => $estimation->whichyouhave
            );
        } else {
            $lsUserEstimates = array();
        }

        $income = Income::model()->find("user_id=:user_id", array("user_id" => $user_id));
        if ($income) {
            //get user income
            $lsUserIncome = array(
            'gross_income' => $income->gross_income,
            'investment_income' => $income->investment_income,
            'spouse_income' => $income->spouse_income,
            'retirement_plan' => $income->retirement_plan,
            'pension_income' => $income->pension_income,
            'social_security' => $income->social_security,
            'disability_benefit' => $income->disability_benefit,
            'veteran_income' => $income->veteran_income,
            'totaluserincome' => $income->totaluserincome
            );
        } else {
            $lsUserIncome = array();
        }
        // Expense
        $expense = Expense::model()->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $user_id), 'select' => 'actualexpense'));
        if ($expense) {
            $expenseTotal = $expense->actualexpense;
        } else {
            $expenseTotal = 0;
        }

        $profile = Userpersonalinfo::model()->findBySql("SELECT age,retirementage FROM userpersonalinfo WHERE user_id=:user_id", array("user_id" => $user_id));
        $retirementage = 65;
        if (isset($profile) && isset($profile->retirementage) && intval($profile->retirementage) > 0) {
            $retirementage = intval($profile->retirementage);
        }

        $goals = Goal::model()->findAll("user_id=:user_id and goalstatus=1", array("user_id" => $user_id));
        $lsUserGoals = array();
        if ($goals) {
            //get user income
            foreach ($goals as $goal) {
                //check if the goal is retirement to get age
                if ($goal->goaltype == "RETIREMENT") {
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
                'modifiedtimestamp' => $goal->modifiedtimestamp
                );
                $lsUserGoals[] = $lsUserGoal;
            }
        }

        $totalScore = $this->sengine->updateScore();
        $totalScore = ($totalScore < 0) ? 0 : $totalScore;
        $totalScore = ($totalScore > 1000) ? 1000 : $totalScore;

        $userDetails = User::model()->findByPk($user_id);
        $userPerDetails = Userpersonalinfo::model()->find("user_id=:user_id", array("user_id" => $user_id));
        $firstname = $userDetails->firstname;
        $lastname = $userDetails->lastname;
        $email = $userDetails->email;
        $phone = $userDetails->phone;
        $userPic = "ui/images/genericAvatar.png";
        if ($userPerDetails) {
            $userPic = $userPerDetails->userpic;
        }
        $wfPoint38 = $this->sengine->wfPoint38;

        $userBreakDownDetails = BreakdownChange::model()->findAll(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $user_id)));

        $wsEachUserItemBankAccount = array(
        'harvesting' => $finalResult, #$lsCashedgeItemArr, // Fix for duplicate rows in harvesting array
        'cash' => isset($lsAccCASH) ? $lsAccCASH : array(),
        'investment' => isset($lsAccINVESTMENT) ? $lsAccINVESTMENT : array(),
        'debts' => $lsAccDEBT,
        'insurance' => $lsAccINSURANCE,
        'other' => isset($lsAccOTHER) ? $lsAccOTHER : array(),
        'silent' => isset($lsAccSILENT) ? $lsAccSILENT : array(),
        'cashTotal' => $cashTotal,
        'debtTotal' => $debtTotal,
        'expenseTotal' => $expenseTotal,
        'insuranceTotal' => $insuranceTotal,
        'investmentTotal' => $investmentTotal,
        'otherTotal' => $otherTotal,
        'networth' => $netWorth,
        'estimation' => $lsUserEstimates,
        'income' => $lsUserIncome,
        'goals' => $lsUserGoals,
        'accountsdownloading' => $downloadingAcc,
        'income' => $this->sengine->userIncomePerMonth,
        'livingCosts' => $this->sengine->userExpensePerMonth,
        'assetsTotal' => $this->sengine->userSumOfAssets + $this->sengine->userSumOfOtherAssets,
        'debtsTotal' => $this->sengine->userSumOfDebts,
        'savingsTotal' => ($this->sengine->taxableAnnualSavings + $this->sengine->taxDeferredAnnualSavings + $this->sengine->taxFreeAnnualSavings) / 12,
        'age' => $this->sengine->userCurrentAge,
        'retage' => $retirementage,
        'growthrate' => $this->sengine->userGrowthRate,
        'totalscore' => $totalScore,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'phone' => $phone,
        'userpic' => $userPic,
        'advisor_id' => $advisor_id,
        'wfPoint38' => $wfPoint38,
        'breakdownData' => $userBreakDownDetails
        );
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "lsacc" => $wsEachUserItemBankAccount)));
    }


    public function actionGetuserdetails() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $userObj = User::model()->findByPk($user_id);
        $userPerObj = Userpersonalinfo::model()->findByPk($user_id);
        $userSecurityResponse = UserSecurityAnswer::model()->findAll("user_id = :user_id", array(':user_id' => $user_id));
        $userSecurityQuestion = SecurityQuestion::model()->findAll(array('select' => array('id', 'question')));
        $rates = Sustainablerates::model()->findAll(array('select' => array('age', 'sustainablewithdrawalpercent')));
        $rateArray = array();
        foreach ($rates as $rate) {
            $r = array();
            $r['age'] = $rate->age;
            $r['withdrawal'] = $rate->sustainablewithdrawalpercent;
            $rateArray[] = $r;
        }

        $userData = array(
        'firstname' => $userObj->firstname,
        'lastname' => $userObj->lastname,
        'email' => $userObj->email,
        'zipcode' => $userObj->zip,
        'rates' => $rateArray,
        'securityquestion' => $userSecurityQuestion,
        'permission' => ''
        );
        if ($userPerObj) {
            $userData['userpic'] = $userPerObj->userpic;
            $userData['age'] = $userPerObj->age;
            $userData['maritalstatus'] = $userPerObj->maritalstatus;
            $userData['spouseage'] = $userPerObj->spouseage;
            $userData['noofchildren'] = $userPerObj->noofchildren;
            $userData['retirementstatus'] = $userPerObj->retirementstatus;
            $userData['retirementage'] = $userPerObj->retirementage;
            $userData['childrensage'] = $userPerObj->childrensage;
            $userData['risk'] = $userPerObj->risk;
        }
        $assetCount = Assets::model()->count(array('condition' => 'user_id = :user_id AND context = "AUTO" and status = 0',
        'params' => array('user_id' => $user_id)));
        $debtCount = Debts::model()->count(array('condition' => 'user_id = :user_id AND context = "AUTO" and status = 0',
        'params' => array('user_id' => $user_id)));
        $userData['linkedcount'] = $assetCount + $debtCount;

        $userData['securityresponse'] = array();
        if ($userSecurityResponse) {
            foreach ($userSecurityResponse as $answer) {
                $userData['securityresponse'][] = array(
                'id' => $answer->id,
                'question_id' => $answer->question_id,
                'response_text' => $answer->answer
                );
            }
        }
        $count = count($userData['securityresponse']);
        while ($count++ < 3):
            $userData['securityresponse'][] = array(
            'id' => '',
            'question_id' => $count,
            'response_text' => ''
            );
        endwhile;

        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "userdata" => $userData)));
    }


    /**
     * get notification data
     */
    public function actionGetnotificationdata() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) && !isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $forceUserNotifications = false;
        if (isset($_GET['forceUser'])) {
            $forceUserNotifications = ($_GET['forceUser'] === 'true');
        }

        if (isset(Yii::app()->getSession()->get('wsadvisor')->id) && !$forceUserNotifications) {
            $user_id = Yii::app()->getSession()->get('wsadvisor')->id;
            $userPerObj = Advisorpersonalinfo::model()->findByPk($user_id);

            $notificationORM = AdvisorNotification::model()->findAll("advisor_id=:advisor_id and status=0", array("advisor_id" => $user_id));
            $totalItem = 0;
            $lsNotificationArr = array();

            if (count($notificationORM) > 0) {
                // create the hash for advisorid //
                $hasher = PasswordHasher::factory();
                $advisorHashObj = Advisor::model()->find(array('condition' => "id = :advisor_id", 'params' => array("advisor_id" => $user_id), 'select' => 'advidhashvalue'));
                if ($advisorHashObj) {
                    if ($advisorHashObj->advidhashvalue == "") {
                        $advisorHashObj->advidhashvalue = str_replace("/", "", $hasher->HashPassword($user_id));
                        Advisor::model()->updateByPk($user_id, array('advidhashvalue' => $advisorHashObj->advidhashvalue));
                        $advidhashvalue = $advisorHashObj->advidhashvalue;
                    } else {
                        $advidhashvalue = $advisorHashObj->advidhashvalue;
                    }
                }
                unset($hasher);
                $folderPath = realpath(dirname(__FILE__) . '/../..');
                if (is_dir($folderPath . '/ui/usercontent/advisor/' . $user_id . '/')) {
                    rename($folderPath . '/ui/usercontent/advisor/' . $user_id, $folderPath . '/ui/usercontent/advisor/' . $advidhashvalue);
                } else if (!is_dir($folderPath . '/ui/usercontent/advisor/' . $user_id . '/') && !is_dir($folderPath . '/ui/usercontent/advisor/' . $advidhashvalue . '/')) {
                    mkdir($folderPath . '/ui/usercontent/advisor/' . $advidhashvalue . '/');
                }
            }

            foreach ($notificationORM as $notification) {
                $createdDate = date('D M d Y H:i:s', strtotime($notification->lastmodified)) . " UTC";
                $filelink = './ui/usercontent/advisor/' . $advidhashvalue . '/' . $notification->file;
                $lsNotificationItem = array(
                'id' => $notification->id,
                'message' => $notification->message,
                'info' => json_decode($notification->info),
                'rowid' => $notification->rowid,
                'context' => $notification->context,
                'created' => $createdDate,
                'template' => $notification->template,
                'file' => $filelink,
                'advisor_id' => $notification->advisor_id
                );
                $totalItem = $totalItem + 1;
                $lsNotificationArr[] = $lsNotificationItem;
            }
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "total" => $totalItem, "notification" => $lsNotificationArr)));
        } elseif (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
            $notificationORM = Notification::model()->findAll("user_id=:user_id and status=0", array("user_id" => $user_id));
            $totalItem = 0;
            $lsNotificationArr = array();
            foreach ($notificationORM as $notification) {
                $createdDate = date('D M d Y H:i:s', strtotime($notification->lastmodified)) . " UTC";
                $lsNotificationItem = array(
                'id' => $notification->id,
                'message' => $notification->message,
                'info' => json_decode($notification->info),
                'rowid' => $notification->rowid,
                'context' => $notification->context,
                'created' => $createdDate,
                'template' => $notification->template
                );
                $totalItem = $totalItem + 1;
                $lsNotificationArr[] = $lsNotificationItem;
            }
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "total" => $totalItem, "notification" => $lsNotificationArr)));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => 'Session timeout')));
        }
    }


    /**
     * updatenotification
     */
    public function actionUpdatenotification() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) && !isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $forceUserNotifications = false;
        if (isset($_GET['forceUser'])) {
            $forceUserNotifications = ($_GET['forceUser'] === 'true');
        }
        if (Yii::app()->getSession()->get('wsadvisor') && !$forceUserNotifications) {
            $user_id = Yii::app()->getSession()->get('wsadvisor')->id;
            $id = $_GET["id"];
            $notificationORM = AdvisorNotification::model()->findByPk($id);
            if ($notificationORM->advisor_id == $user_id) {
                $notificationORM->status = 1;
                $notificationORM->lastmodified = date("Y-m-d H:i:s");
                $notificationORM->save();
            }
            $notiCount = AdvisorNotification::model()->count("advisor_id=:advisor_id and status=0", array("advisor_id" => $user_id));
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "notification" => $notiCount)));
        } else if (Yii::app()->getSession()->get('wsuser')) {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
            $id = $_GET["id"];
            $notificationORM = Notification::model()->findByPk($id);
            if ($notificationORM->user_id == $user_id) {
                $notificationORM->status = 1;
                $notificationORM->lastmodified = date("Y-m-d H:i:s");
                $notificationORM->save();
                Yii::app()->cache->set('notification' . $user_id, $notificationORM->lastmodified);
            }
            $notiCount = Notification::model()->count("user_id=:user_id and status=0", array("user_id" => $user_id));
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "notification" => $notiCount)));
        }
    }


    /**
     * Rendering Action Steps in myscore Page
     */
    public function actionGetactionstep($type = '') {
        // checking user session
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) && !isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        if (isset(Yii::app()->getSession()->get('wsadvisor')->id) && isset($_GET['user_id'])) {
            $user_id = $_GET['user_id']; // if request comes from advisor
        } else {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
        }
        $stepcount = 0;
        if (isset($_GET["stepscount"])) {
            $stepcount = $_GET["stepscount"];
        }
        $status1 = '0';
        $status2 = '2';
        $status3 = '3';
        if (isset($_GET["type"]) && $_GET["type"] == 'completed') {
            $status1 = '1';
            $status2 = '4';
            $status3 = '5';
        }

        // initializing
        $asArr = array();
        $asfew = array();
        $asmore = array();
        $notassigned = array();
        $divarr = array();
        $freecells = 0;
        $rownum = 3;
        $rowcount = 0;
        $firstHalf = ceil($stepcount / 2);
        $secondHalf = floor($stepcount / 2);
        if (!$type)
            $type = 'custom';
        $getData = Actionstep::model()->findAll(array('condition' => "user_id=:user_id AND actionstatus IN ('" . $status1 . "', '" . $status2 . "','" . $status3 . "') order by points desc", 'params' => array("user_id" => $user_id)));
        $ascategories = array();

        //query to fetch all categories of action steps /
        if ($getData) {
            $actionIds = array();
            foreach ($getData as $row) {
                $actionIds[] = $row->actionid;
            }
            $actionIds = implode(',', $actionIds);
            $actionStepsCategories = Actionstepmeta::model()->findAll(array('condition' => "actionid IN (" . $actionIds . ") GROUP BY category ORDER BY category ASC", 'params' => array("user_id" => $user_id), 'select' => 'category'));
            $i = 0;
            foreach ($actionStepsCategories as $keyASC => $valueASCategories) {
                $ascategories[$i]['categoryname'] = $valueASCategories['category'];
                if (isset($_GET["catName"]) && $valueASCategories['category'] == $_GET["catName"]) {
                    $ascategories[$i]['catselected'] = "selected='selected'";
                } else {
                    $ascategories[$i]['catselected'] = "";
                }
                $i++;
            }
        }


        // Sort by Action Steps, get category
        if (isset($_GET["catName"])) {
            $catname = $_GET["catName"];
            $actMetaObj = new Actionstepmeta();
            //$actionStepMeta = $actMetaObj->findAllBySql(array('condition' => "category=:category", 'params' => array("category" => $_GET["catName"]), 'select' => array('actionid')));
            $actionStepsByCat = Actionstepmeta::model()->findAll(array('condition' => "category=:categoryname", 'params' => array("categoryname" => $_GET["catName"])));
            $actionStepsIds = array();
            $actionStepsIdsString = "";
            foreach ($actionStepsByCat as $keyAS => $valueAS) {
                $actionStepsIds[] = $valueAS['actionid'];
            }
            $actionStepsIdsString = implode(",", $actionStepsIds);
            $getData = Actionstep::model()->findAll(array('condition' => "user_id=:user_id AND actionstatus IN ('" . $status1 . "', '" . $status2 . "','" . $status3 . "') AND actionid IN (" . $actionStepsIdsString . ") order by points desc", 'params' => array("user_id" => $user_id)));
        } else {
            $catname = '';
            $getData = Actionstep::model()->findAll(array('condition' => "user_id=:user_id AND actionstatus IN ('" . $status1 . "', '" . $status2 . "','" . $status3 . "') order by points desc", 'params' => array("user_id" => $user_id)));
        }


        if (!empty($getData)) {
            $icount = 0;    // To count Instant type
            $ikey = 0;      // Index starting position for Instant Type
            $skey = 2;      // Index starting position for Short Type
            $mkey = 3;      // Index starting position for Medium Type
            // fetching array values in a loop
            foreach ($getData as $asrow) {
                $cat = Actionstepmeta::model()->findByPk($asrow->actionid);

                // points from master table. We need to get points from SE Simulation. Currently its not done.
                $point = $asrow->points;
                // changing actionstep key positions
                // commented for finnovate demo 10/09/2013
                switch ($asrow->type) {
                    case 'instant':
                        $key = $ikey;
                        if ($icount % 2) {
                            $ikey = $ikey + 3;
                        } else {
                            $ikey = $ikey + 1;
                        }
                        $icount++;
                        break;
                    case 'short':
                        $key = $skey;
                        $skey = $skey + 4;
                        break;
                    case 'mid':
                        $key = $mkey;
                        $mkey = $mkey + 4;
                        break;
                } //*/
                //$key = $mkey; // need to delete after demo 10/09/2013
                //$mkey = $mkey + 1; // need to delete after demo 10/09/2013
                //
                // $key will determine the Index position of the array
                $asArr[$key]['id'] = $asrow->id;
                $asArr[$key]['actionname'] = $asrow->actionsteps;
                $asArr[$key]['points'] = $point;
                $asArr[$key]['type'] = $cat->type;
                $asArr[$key]['category'] = $cat->category;
                $asArr[$key]['link'] = $cat->link;
                $asArr[$key]['linktype'] = $cat->linktype;
                if ($cat->linktype == 'action') {
                    if ($asrow->actionstatus == 0 || $asrow->actionstatus == 2) {
                        $asArr[$key]['pagelink'] = $cat->buttonstep1;
                    } else {
                        $asArr[$key]['pagelink'] = $cat->buttonstep2;
                    }
                } else {
                    $asArr[$key]['pagelink'] = $cat->buttonstep1;
                }
                $asArr[$key]['actionsteps'] = $asrow->actionsteps;
                $asArr[$key]['actionid'] = $asrow->actionid;
                $asArr[$key]['user_id'] = $asrow->user_id;
                $rowcount++;
            }
            // For creating blank div for UI display
            if ($rowcount > $stepcount && $stepcount > 0) {
                $rowcount = $stepcount;
            }
            if ($firstHalf == 0 && $secondHalf == 0) {
                $firstHalf = ceil($rowcount / 2);
                $secondHalf = floor($rowcount / 2);
            }
            $remain = $rownum - ($rowcount % $rownum);
            $remain = $remain % $rownum;
            for ($k = 1; $k <= $remain; $k++) {
                $divarr[] = $k;
            }
            // Sorting array & slice into 2 arrays. Display as 2 blocks in UI.
            ksort($asArr);
            if (count($asArr) > $firstHalf) {
                $asmore = array_slice($asArr, $firstHalf, $secondHalf);
            }
            if (count($asArr) >= 1) {
                $asfew = array_slice($asArr, 0, $firstHalf);
            }
        }

        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "ascategories" => $ascategories, "actstepfew" => $asfew, "actstepmore" => $asmore, "type" => $type, "count" => $rowcount, 'darr' => $divarr, 'catName' => $catname)));
    }


    public function actionGetactionstepdetail() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        // values get from url is $id and $type.
        $id = $_GET["id"];
        if (isset($_GET['type'])) {
            $ctype = isset($_GET['type']);
        } else {
            $ctype = '';
        }
        // checking user is logged in.
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
            $uemail = Yii::app()->getSession()->get('wsuser')->email;
            //Checking secure hosting
            $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
            $sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
            $protocol = substr($sp, 0, strpos($sp, "/")) . $s;

            // initializing
            $actionstepdetail = array();
            $category = array();
            ///////Fetching Action step
            $qc = new CDbCriteria();
            $qc->condition = "id = :id";
            $qc->params = array('id' => $id);
            $stepval = Actionstep::model()->find($qc);

            if (isset($stepval) && !empty($stepval)) {
                ///// Fetching Actionstep Meta
                $cat = Actionstepmeta::model()->findByPk($stepval->actionid);
                //  Fetching SE details
                $uscore = new UserScore();
                $checkqry = $uscore->findBySql("SELECT scoredetails FROM userscore WHERE user_id = :user_id", array("user_id" => $user_id));
                if ($checkqry) {
                    $details = $checkqry->scoredetails;
                    $sengineObj = unserialize($details);
                    $acusrinsurcoverage = '';
                    if ($stepval->actionid == 2 || $stepval->actionid == 3) {
                        $acusrinsurcoverage = $stepval->flexi5;
                    } else if ($stepval->actionid == 35 || $stepval->actionid == 36 || $stepval->actionid == 43 || $stepval->actionid == 49 || $stepval->actionid == 12 || $stepval->actionid == 20) {
                        $acusrinsurcoverage = $stepval->flexi5;
                    }
                }
                $point = $stepval->points;
                $astitle = $stepval->flexi1;
                $aspercent = $stepval->flexi2;

                $acarray['category'] = $cat->category;
                $acarray['info'] = $cat->info;
                if (isset($cat->image)) {
                    $acarray['image'] = $cat->image;
                }
                // Refine Title
                $actiontitle = $cat->actionname;
                $actiontitle = str_replace('{{amt}}', $acusrinsurcoverage, $actiontitle);
                $actiontitle = str_replace('{{title}}', $astitle, $actiontitle);
                $actiontitle = str_replace('{{percent}}', $aspercent . '%', $actiontitle);
                $acarray['actionname'] = $actiontitle;

                /// Refine details
                $finaldetails = $cat->description;
                $externallinkdetails = $cat->externallink;
                //$advisorhelp = $cat->advisorhelpbutton;
                //advisor recommendation query //
                $sql = "SELECT ar.advisor_id, ar.actionid, ar.product_name, REPLACE(ar.description, '\\\', '') as recommendation, ar.product_image, ar.product_link, ap.firstname, ap.lastname, ap.profilepic, ca.status
                FROM advisorasrecommendation ar
                LEFT JOIN advisorpersonalinfo ap ON ar.advisor_id = ap.advisor_id
                LEFT JOIN consumervsadvisor ca ON ar.advisor_id = ca.advisor_id
                WHERE ar.actionid = $cat->actionid AND ca.user_id = $user_id AND ca.status = 1
                ORDER BY ca.dateconnect ASC";

                $advasdesc = Yii::app()->db->createCommand($sql)->queryAll();

                if ($advasdesc) {

                    foreach ($advasdesc as $key => $value) {
                        $content_array = preg_split('/\s/', $value["recommendation"]);
                        $output = '';
                        foreach ($content_array as $content) {
                            //starts with http:// or https://
                            if (substr($content, 0, 7) == "http://" || substr($content, 0, 8) == "https://")
                                $content = '<a href="' . $content . '" id="' . $id . '" class="advisorPopLinkCongrats masterTooltip" title="By providing links to other sites, FlexScore does not necessarily guarantee, approve, or endorse the information or products available on these sites. We do our best to provide convenient ways to fulfill action steps through outside providers. But, these are all separate entities from FlexScore and, therefore, are outside of our control.">' . $content . '</a>';

                            //starts with www.
                            if (substr($content, 0, 4) == "www.")
                                $content = '<a href="http://' . $content . '" id="' . $id . '" class="advisorPopLinkCongrats masterTooltip" title="By providing links to other sites, FlexScore does not necessarily guarantee, approve, or endorse the information or products available on these sites. We do our best to provide convenient ways to fulfill action steps through outside providers. But, these are all separate entities from FlexScore and, therefore, are outside of our control.">' . $content . '</a>';

                            $output .= " " . $content;
                        }

                        $output = trim($output);
                        $advasdesc[$key]["recommendation"] = $output;
                    }
                    $acarray['advisorrecommendations'] = $advasdesc;
                    //$advassociated = "1";
                } else {
                    $acarray['advisorrecommendations'] = "";
                    //$advassociated = "0";
                }


                //start admin product//
                $adminRSql = "SELECT *, REPLACE(product_description, '\\\', '') as description FROM adminasrecommendation WHERE user_email='" . $uemail . "'";
                $adminasdesc = Yii::app()->db->createCommand($adminRSql)->queryAll();
                $admin_actionid = array();
                if ($adminasdesc) {
                    $adminresult = array();
                    foreach ($adminasdesc as $adminkey => $adminvalue) {
                        $admin_actionid = explode(",", $adminvalue['action_id']);

                        if (in_array($cat->actionid, $admin_actionid)) {

                            $pd_array = explode(" ", $adminvalue["product_description"]);
                            $pd_output = '';
                            foreach ($pd_array as $pd) {
                                //starts with http:// or https://
                                if (substr($pd, 0, 7) == "http://" || substr($pd, 0, 8) == "https://")
                                    $pd = '<a href="' . $pd . '" id="' . $id . '" class="advisorPopLinkCongrats masterTooltip" title="By providing links to other sites, FlexScore does not necessarily guarantee, approve, or endorse the information or products available on these sites. We do our best to provide convenient ways to fulfill action steps through outside providers. But, these are all separate entities from FlexScore and, therefore, are outside of our control.">' . $pd . '</a>';

                                //starts with www.
                                if (substr($pd, 0, 4) == "www.")
                                    $pd = '<a href="http://' . $pd . '" id="' . $id . '" class="advisorPopLinkCongrats masterTooltip" title="By providing links to other sites, FlexScore does not necessarily guarantee, approve, or endorse the information or products available on these sites. We do our best to provide convenient ways to fulfill action steps through outside providers. But, these are all separate entities from FlexScore and, therefore, are outside of our control.">' . $pd . '</a>';

                                $pd_output .= " " . $pd;
                            }

                            $pd_output = trim($pd_output);
                            $adminasdesc[$adminkey]["html_description"] = $pd_output;
                            $adminresult[] = $adminasdesc[$adminkey];
                        }
                    }
                    $acarray['admincommendations'] = $adminresult;
                    //$advassociated = "1";
                } else {
                    $acarray['admincommendations'] = "";
                    //$advassociated = "0";
                }
                //end admin product //

                if ($externallinkdetails != "") {
                    $advassociated = "1";
                } else {
                    $advassociated = "0";
                }
                if (preg_match("/{{pdflink}}/i", $finaldetails)) {
                    $cat->externallink = "ui/content/ConfidentialDocsandCredentialLocator.pdf";
                    $finaldetails = str_replace('{{pdflink}}', '<a href="' . $cat->externallink . '" id="' . $id . '" class="popLinkCongrats" title="">Click Here</a>', $finaldetails);
                }
                if (preg_match("/{{lnk}}/i", $finaldetails)) {
                    if ($advassociated == "1") {
                        $lines = explode(".", $finaldetails);
                        $exclude = array();
                        foreach ($lines as $line) {
                            if (strpos($line, '{{lnk}}') !== FALSE) {
                                if (strpos($line, '{{title}}') !== FALSE) {
                                    $exclude[] = '<br>{{title}}';
                                }
                                continue;
                            }
                            $exclude[] = $line;
                        }
                        $finaldetails = implode(".", $exclude);
                    } else {
                        $finaldetails = str_replace('{{lnk}}', '<a href="' . $cat->externallink . '" id="' . $id . '" class="popLinkCongrats masterTooltip" title="By providing links to other sites, FlexScore does not necessarily guarantee, approve, or endorse the information or products available on these sites. We do our best to provide convenient ways to fulfill action steps through outside providers. But, these are all separate entities from FlexScore and, therefore, are outside of our control.">Click Here</a>', $finaldetails);
                    }
                }

                $finaldetails = str_replace('{{amt}}', $acusrinsurcoverage, $finaldetails);
                $finaldetails = str_replace('{{title}}', $astitle, $finaldetails);
                $finaldetails = str_replace('{{percent}}', $aspercent . '%', $finaldetails);

                $simpledetails = $finaldetails;
                if ($cat->simpledescription != '') {
                    $simpledetails = $cat->simpledescription;
                    $simpledetails = str_replace('{{amt}}', $acusrinsurcoverage, $simpledetails);
                }

                $acarray['id'] = $id;
                $acarray['description'] = $finaldetails;
                $acarray['simpledescription'] = $simpledetails;

                $acarray['vtitle'] = $cat->vtitle;
                $acarray['points'] = $point;
                $acarray['type'] = $cat->type;
                $acarray['link'] = $cat->link;

                $objectids = array();
                if ($cat->link == "learnmore" && $stepval->flexi1 != "") {
                    $artdiv = explode('/learningcenter/', $stepval->flexi1);
                    for ($i = 1; $i < count($artdiv); $i++) {
                        $objectArray = explode('"', $artdiv[$i]);
                        $objectid = $objectArray[0];
                        $objectids[] = $objectid;
                    }
                }
                if (($cat->link == "addasset" || $cat->link == "editasset") && $stepval->flexi1 != "" && $stepval->actionid != 94) {
                    $artdiv = explode('addAssets', $stepval->flexi1);
                    for ($i = 0; $i < count($artdiv) - 1; $i+=2) {
                        $objectArray = explode(' id="', $artdiv[$i]);
                        $objectid = $objectArray[1];
                        $objectids[] = $objectid;
                    }
                }
                if (($cat->link == "adddebt" || $cat->link == "editdebt") && $stepval->flexi1 != "" && $stepval->actionid != 93) {
                    $artdiv = explode('addDebts', $stepval->flexi1);
                    for ($i = 0; $i < count($artdiv) - 1; $i+=2) {
                        $objectArray = explode(' id="', $artdiv[$i]);
                        $objectid = $objectArray[1];
                        $objectids[] = $objectid;
                    }
                }
                $acarray['objectids'] = $objectids;

                $acarray['protocol'] = $protocol;
                $acarray['linktype'] = $cat->linktype;
                $acarray['actionid'] = $cat->actionid;
                $actionid = $cat->actionid;
                $points = $point;
                $lnk = $cat->externallink;
                $lnktyp = $cat->linktype;

                //added for advisor help button//
                $acarray['advisorhelpstatus'] = $stepval->advisorhelpstatus;
                $acarray['externallink'] = $externallinkdetails;
                $acarray['externallinkUrl'] = $cat->externallink;
                $acarray['external_institution_logo'] = $cat->external_institution_logo;
                $acarray['externallinkdescription'] = $cat->externallinkdescription;
                $acarray['externallinkname'] = $cat->externallinkname;
                $acarray['user_id'] = $user_id;

                if (isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
                    $advisor_id = Yii::app()->getSession()->get('wsadvisor')->id;
                } else {
                    $advisor_id = "";
                }
                $acarray['advisor_id'] = $advisor_id;
                $acarray['advassociated'] = $advassociated;
                if ($cat->linktype != 'video') {
                    $videokey = 'ok';
                } else {
                    $videokey = $cat->link;
                }
                if ($cat->linktype == 'action') { // $stepval->actionstatus == 2 &&
                    $acarray['buttonlink'] = $cat->linkstep2;
                } else {
                    $acarray['buttonlink'] = $cat->linkstep1;
                }
                $acarray['actionstatus'] = $stepval->actionstatus;
                $acarray['id'] = $stepval->id;
                $acarray['flexi1'] = $stepval->flexi1;
                $acarray['flexi2'] = $stepval->flexi2;
                $acarray['flexi3'] = $stepval->flexi3;
                $actionstepdetail[] = $acarray;
            }
            $steps = Actionstep::model()->find("id=:id", array(":id" => $id));
            if ($steps) {
                $chkcat = Actionstepmeta::model()->findByPk($steps->actionid);
                if ($steps->actionstatus == '0') {
                    $steps->actionstatus = '2';
                    $steps->save();
                } else {
                    if ($ctype == 'over' && $cat->info == "info" && $cat->linktype != 'action') {
                        $steps->actionstatus = '4';
                        $steps->save();
                    }
                    if ($ctype == 'over' && $cat->info == "info" && $cat->linktype == 'action') {
                        $steps->actionstatus = '1';
                        $steps->save();
                    }
                }
            }


            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "actionstepdetail" => $actionstepdetail,
            "videokey" => $videokey, "user_id" => $user_id, "actionid" => $actionid, "points" => $points,
            "lnk" => $lnk, "lnktyp" => $lnktyp)));
        }
    }


    function actionUsersByStateReport() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) || Yii::app()->getSession()->get('wsuser')->roleid != User::USER_IS_ADMIN) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        // Fetching data from DB
        $peerscoreData = Analytics::model()->find(array('select' => 'details,datetime', 'order' => 'id DESC'));
        if (isset($peerscoreData->details)) {
            $scorechange = unserialize($peerscoreData->details);
        } else {
            $scorechange = array();
        }

        if (isset($scorechange['usersbystate'])) {
            $userbystate = $scorechange['usersbystate'];
        } else {
            $userbystate = array();
        }

        $result[] = array('usersByState' => $userbystate, 'todate' => date('d F, Y H:i A'));

        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "result" => $result)));
    }


    function actionReports() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) || Yii::app()->getSession()->get('wsuser')->roleid != User::USER_IS_ADMIN) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        // if($this->activities["reports"] == 'yes') {
        //die;
        $result = array();
        $popularActions = array();
        $externalActions = array();
        $astObj = new Assets();
        $debtObj = new Debts();
        $insObj = new Insurance();
        $miscObj = new Miscellaneous();
        $incObj = new Income();
        $expObj = new Expense();
        $estObj = new Estimation();
        $infoObj = new Userpersonalinfo();
        $usrObj = new User();
        $acsObj = new Actionstep();
        $asmetaObj = new Actionstepmeta();
        $uscoreObj = new UserScore();
        // Average


        $assets = $astObj->find(array('select' => array('COUNT(user_id) AS acount', 'AVG(balance) AS aavg')));
        $debts = $debtObj->find(array('select' => array('COUNT(user_id) AS dcount', 'AVG(balowed) AS davg'), 'condition' => "monthly_payoff_balances=0"));
        $insurance = $insObj->find(array('select' => array('COUNT(user_id) AS icount', 'AVG(cashvalue) AS iavg')));
        // Percentage
        $misc = $miscObj->find(array('select' => array('COUNT(user_id) AS mcount')));
        $income = $incObj->find(array('select' => array('COUNT(user_id) AS icount')));
        $expense = $expObj->find(array('select' => array('COUNT(user_id) AS ecount')));
        $esti = $estObj->find(array('select' => array('COUNT(user_id) AS ecount')));
        $info = $infoObj->find(array('select' => array('COUNT(user_id) AS icount'), 'condition' => "age IS NOT NULL"));
        // Users count
        $usr = $usrObj->find(array('select' => array('COUNT(id) AS totcount')));
        $activeusr = $usrObj->find(array('select' => array('COUNT(id) AS totcount'), 'condition' => "isactive = '1' AND lastaccesstimestamp > NOW() - INTERVAL 7 DAY"));
        $requested = $usrObj->find(array('select' => array('COUNT(id) AS totcount'), 'condition' => "isactive = '0'"));
        $loggedin = $usrObj->find(array('select' => array('COUNT(id) AS totcount'), 'condition' => "isactive = '1'"));
        $deleted = $usrObj->find(array('select' => array('COUNT(id) AS totcount'), 'condition' => "isactive = '2'"));
// Least & most used Action steps
        $actionMax = $acsObj->findBySql("SELECT COUNT(*) AS acscount, actionsteps FROM actionstep WHERE actionstatus = '4' GROUP BY actionid ORDER BY acscount DESC LIMIT 1");
        $actionLess = $acsObj->findBySql("SELECT COUNT(*) AS acscount, actionsteps FROM actionstep WHERE actionstatus = '0' GROUP BY actionid ORDER BY acscount DESC LIMIT 1");
        // Popular Action steps
        $actionPopular = $acsObj->findAllBySql("SELECT COUNT(*) AS acscount, actionsteps, type, points FROM actionstep WHERE actionstatus = '4' GROUP BY actionid ORDER BY acscount DESC LIMIT 5");
        foreach ($actionPopular as $k => $popularVal) {
            $popularActions[$k] = array('name' => $popularVal->actionsteps, 'type' => strtoupper($popularVal->type), 'points' => $popularVal->points);
        }
        // 90 days Points
        $pointsDet = $uscoreObj->findAll(array('select' => 'nintydays', 'condition' => "nintydays IS NOT NULL AND nintydays <> '0' AND nintydays <> ''"));
        // External Link
        /*    $metaactions = $asmetaObj->findAllBySql("SELECT actionname, actionid, externallink FROM actionstepmeta WHERE externallink <> '' AND status = '0'");
          foreach ($metaactions as $k => $metaVal) {
          $exCtr = $acsObj->findBySql("SELECT COUNT(*) AS acscount FROM actionstep WHERE actionstatus IN ('3','2','1','4') AND actionid = :actionid", array('actionid' => $metaVal->actionid));
          $exCom = $acsObj->findBySql("SELECT COUNT(*) AS acscount FROM actionstep WHERE actionstatus IN ('1','4') AND actionid = :actionid", array('actionid' => $metaVal->actionid));

          $externalActions[$k] = array('name' => $metaVal->actionname, 'link' => $metaVal->externallink, 'title' => substr($metaVal->externallink, 0, 26), 'ctr' => $exCtr->acscount, 'completed' => $exCom->acscount);
          } */

        // Calculate Percentage
        $miscper = ($misc->mcount / $usr->totcount) * $this->percentage;
        $incper = ($income->icount / $usr->totcount) * $this->percentage;
        $expper = ($expense->ecount / $usr->totcount) * $this->percentage;
        $estper = ($esti->ecount / $usr->totcount) * $this->percentage;
        $infoper = ($info->icount / $usr->totcount) * $this->percentage;

        // 90 days Array
        $p = 0;
        $aPoint = 0;
        if ($pointsDet) {
            foreach ($pointsDet as $pVal) {
                $aPoint = $aPoint + $pVal->nintydays;
                $p++;
            }
        }
        if ($aPoint > 0) {
            $avgTot = $aPoint / $p;
        } else {
            $avgTot = 0;
        }

        // Fetching data from DB
        $peerscoreData = Analytics::model()->find(array('select' => 'details,datetime', 'order' => 'id DESC'));
        if (isset($peerscoreData->details)) {
            $scorechange = unserialize($peerscoreData->details);
        } else {
            $scorechange = array();
        }

        if (isset($scorechange['usersbystate'])) {
            $userbystate = $scorechange['usersbystate'];
        } else {
            $userbystate = array();
        }



        $qryZip = Yii::app()->db->createCommand("SELECT u.zip, u.id, YEAR(CURRENT_TIMESTAMP) - YEAR(t.age) - (RIGHT(CURRENT_TIMESTAMP, 5) < RIGHT(t.age, 5)) AS age, ROUND(us.totalscore + 250 * (IF(mcu.montecarloprobability is NULL, us.montecarloprobability, mcu.montecarloprobability) - us.montecarloprobability)) as totalscore
                    FROM user u
                    INNER JOIN userscore us ON u.id=us.user_id
                    INNER JOIN userpersonalinfo t ON t.user_id = u.id
                    LEFT JOIN montecarlouser mcu ON u.id=mcu.user_id
                    WHERE u.isactive = '1'")->queryAll();

        $regionalDependency = new CDbCacheDependency('SELECT * FROM regiondetails');
        $regionObjs = Regiondetails::model()->cache(QUERY_CACHE_TIMEOUT, $regionalDependency)->findAll();

        $regionIdsHash = array();
        if ($regionObjs) {
            foreach ($regionObjs as $regionObj) {
                if ($regionObj->zipcoderangeprefix) {
                    $stateZips = explode("|", $regionObj->zipcoderangeprefix);
                    foreach ($stateZips as $stateZip) {
                        $rangeZips = explode("-", $stateZip);
                        $zip = intval($rangeZips[0]);
                        $finalZip = $zip;
                        if (isset($rangeZips[1])) {
                            $finalZip = intval($rangeZips[1]);
                        }
                        while ($zip <= $finalZip) {
                            $regionIdsHash[$zip] = $regionObj->region;
                            $zip++;
                        }
                    }
                }
            }
        }

        $scoreResult = array();
        $ageResult = array();
        $izips = array();

        if (isset($qryZip)) {
            foreach ($qryZip as $uVal) {
                $hasZip = false;
                if ($uVal['zip'] <> '' && $uVal['zip'] <> '0') {
                    $hasZip = true;
                    $usrzip = substr($uVal['zip'], 0, 3);
                    if (strlen($usrzip) == 3) {

                        // cache regionalDetails table
                        $regionId = 0;
                        if (isset($regionIdsHash[$usrzip])) {
                            $regionId = $regionIdsHash[$usrzip];
                        }

                        if (isset($regionId) && $regionId != 0) {
                            $region = $regionId;
                            $avgTot = 0;
                            $ageTot = 0;
                            $avgTot = $uVal['totalscore'];
                            $ageTot = $uVal['age'];

                            if (isset($scoreResult['region'][$region]['sum'])) {
                                $scoreResult['region'][$region]['sum'] += number_format($avgTot, $this->numberFormat);
                                $scoreResult['region'][$region]['total'] = $scoreResult['region'][$region]['total'] + 1;
                                $scoreResult['region'][$region]['name'] = $region;
                                $scoreResult['region'][$region]['average'] = number_format(($scoreResult['region'][$region]['sum'] / $scoreResult['region'][$region]['total']), $this->numberFormat);
                            } else {
                                $scoreResult['region'][$region]['sum'] = number_format($avgTot, $this->numberFormat);
                                $scoreResult['region'][$region]['total'] = 1;
                                $scoreResult['region'][$region]['name'] = $region;
                                $scoreResult['region'][$region]['average'] = number_format($avgTot, $this->numberFormat);
                            }
                            if (isset($ageResult['region'][$region]['agesum'])) {
                                $ageResult['region'][$region]['agesum'] += number_format($ageTot, $this->numberFormat);
                                $ageResult['region'][$region]['total'] = $ageResult['region'][$region]['total'] + 1;
                                $ageResult['region'][$region]['name'] = $region;
                                $ageResult['region'][$region]['averageAge'] = number_format(($ageResult['region'][$region]['agesum'] / $ageResult['region'][$region]['total']), $this->numberFormat);
                            } else {
                                $ageResult['region'][$region]['agesum'] = number_format($ageTot, $this->numberFormat);
                                $ageResult['region'][$region]['total'] = 1;
                                $ageResult['region'][$region]['name'] = $region;
                                $ageResult['region'][$region]['averageAge'] = number_format($ageTot, $this->numberFormat);
                            }
                        } else {
                            $izips[] = $usrzip;
                        }
                    }
                }
                if ($hasZip == false) {
                    $region = 5;
                    $avgTot = 0;
                    $ageTot = 0;
                    $avgTot = $uVal['totalscore'];
                    $ageTot = $uVal['age'];
                    if (isset($scoreResult['region'][$region]['sum'])) {
                        $scoreResult['region'][$region]['sum'] += number_format($avgTot, $this->numberFormat);
                        $scoreResult['region'][$region]['total'] = $scoreResult['region'][$region]['total'] + 1;
                        $scoreResult['region'][$region]['name'] = $region;
                        $scoreResult['region'][$region]['average'] = number_format(($scoreResult['region'][$region]['sum'] / $scoreResult['region'][$region]['total']), $this->numberFormat);
                    } else {
                        $scoreResult['region'][$region]['sum'] = number_format($avgTot, $this->numberFormat);
                        $scoreResult['region'][$region]['total'] = 1;
                        $scoreResult['region'][$region]['name'] = $region;
                        $scoreResult['region'][$region]['average'] = number_format($avgTot, $this->numberFormat);
                    }
                    if (isset($ageResult['region'][$region]['agesum'])) {
                        $ageResult['region'][$region]['agesum'] += number_format($ageTot, $this->numberFormat);
                        $ageResult['region'][$region]['total'] = $ageResult['region'][$region]['total'] + 1;
                        $ageResult['region'][$region]['name'] = $region;
                        $ageResult['region'][$region]['averageAge'] = number_format(($ageResult['region'][$region]['agesum'] / $ageResult['region'][$region]['total']), $this->numberFormat);
                    } else {
                        $ageResult['region'][$region]['agesum'] = number_format($ageTot, $this->numberFormat);
                        $ageResult['region'][$region]['total'] = 1;
                        $ageResult['region'][$region]['name'] = $region;
                        $ageResult['region'][$region]['averageAge'] = number_format($ageTot, $this->numberFormat);
                    }
                }
            }
            // Creating zip array.
            ksort($scoreResult['region']);
            $scoreResult['region'] = array_slice($scoreResult['region'], 0, count($scoreResult['region']));
//            $scoreResult['invalidZips'] = $izips;
            ksort($ageResult['region']);
            $ageResult['region'] = array_slice($ageResult['region'], 0, count($ageResult['region']));
        }

        // Creating result array
        $result[] = array('AssetsCount' => $assets->acount, 'AssetsAverage' => number_format($assets->aavg, $this->numberFormat),
        'DebtsCount' => $debts->dcount, 'DebtsAverage' => number_format($debts->davg, $this->numberFormat),
        'InsuranceCount' => $insurance->icount, 'InsuranceAverage' => number_format($insurance->iavg, $this->numberFormat),
        'MiscellaneousCount' => $misc->mcount,
        'MiscellaneousPercent' => round($miscper) . '%',
        'IncomeCount' => $income->icount,
        'IncomePercent' => round($incper) . '%',
        'ExpenseCount' => $expense->ecount,
        'ExpensePercent' => round($expper) . '%',
        'EstimationCount' => $esti->ecount,
        'EstimationPercent' => round($estper) . '%',
        'AboutCount' => $info->icount,
        'AboutPercent' => round($infoper) . '%',
        'TotalUser' => $usr->totcount,
        'TotalActiveUser' => $activeusr->totcount,
        'RequestedUsers' => $requested->totcount,
        'LoggedInUsers' => $loggedin->totcount,
        'DeletedUsers' => $deleted->totcount,
        'todate' => date('d F, Y H:i A'),
        'maxAcs' => isset($actionMax->actionsteps) ? $actionMax->actionsteps : 'None',
        'maxAcsCount' => isset($actionMax->acscount) ? $actionMax->acscount : 0,
        'lessAcs' => isset($actionLess->actionsteps) ? $actionLess->actionsteps : 'None',
        'lessAcsCount' => isset($actionLess->acscount) ? $actionLess->acscount : 0,
        'popularActions' => $popularActions,
        'nintyDays' => number_format($avgTot, $this->numberFormat),
//            'nintyRes' => $scorechange,
        'scoreResult' => $scoreResult,
        'ageResult' => $ageResult,
        'usersByState' => $userbystate
        //   'externalLinks' => $externalActions
        );

        # HTML2PDF has very similar syntax
        //$html2pdf = Yii::app()->ePdf->HTML2PDF();
        //$html2pdf->WriteHTML($this->renderPartial('index', array(), true));
        //$html2pdf->Output();
        // send Response
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "result" => $result)));
    }


    function actionScorechanges() {
        $md5hashval = "b28159c334ecd24e2f8d17ad64407362";
        if (!isset($_GET['accTok']) || $_GET['accTok'] == '' || md5($_GET['accTok']) != $md5hashval) {
            header('HTTP/1.1 403 Unauthorized');
            exit;
        }
        $result = array();
        $izips = array();

        //$usrObj = new User();
        //$uscoreObj = new UserScore();
        //$infoObj = new Userpersonalinfo();
        //$regObj = new Regiondetails();
        // cache regionalDetails table
        $regionalDependency = new CDbCacheDependency('SELECT * FROM regiondetails');
        $regionObjs = Regiondetails::model()->cache(QUERY_CACHE_TIMEOUT, $regionalDependency)->findAll();

        // Fetching users having age and nintydays from userpersonalinfo and userscore table
        $usrAge = Yii::app()->db->createCommand('SELECT u.age, u.user_id, s.nintydays FROM userpersonalinfo u INNER JOIN userscore s ON u.user_id=s.user_id INNER JOIN scorechange sc ON sc.user_id=s.user_id WHERE u.age IS NOT NULL AND sc.scorechange IS NOT NULL')->queryAll();
        if (isset($usrAge)) {
            foreach ($usrAge as $infoVal) {

                if ($infoVal['age'] <> '' && $infoVal['age'] <> '0000-00-00') {
                    $ageSplit = explode("-", $infoVal['age']);
                    $ageInYears = date('Y') - $ageSplit[0];

                    $avgTot = 0;
                    $avgTot = $infoVal['nintydays'];

                    if ($ageInYears <= $this->peerminAge) {
                        $ageInYears = $this->peerminAge;
                    }
                    if ($ageInYears >= $this->peermaxAge) {
                        $ageInYears = $this->peermaxAge;
                    }
                    if (isset($result['age'][$ageInYears]['sum'])) {
                        $result['age'][$ageInYears]['sum'] = number_format(($result['age'][$ageInYears]['sum'] + $avgTot), $this->numberFormat);
                        $result['age'][$ageInYears]['total'] = $result['age'][$ageInYears]['total'] + 1;
                        $result['age'][$ageInYears]['name'] = $ageInYears;
                        $result['age'][$ageInYears]['average'] = number_format(($result['age'][$ageInYears]['sum'] / $result['age'][$ageInYears]['total']), $this->numberFormat);
                    } else {
                        $result['age'][$ageInYears]['sum'] = number_format($avgTot, $this->numberFormat);
                        $result['age'][$ageInYears]['total'] = 1;
                        $result['age'][$ageInYears]['name'] = $ageInYears;
                        $result['age'][$ageInYears]['average'] = number_format($avgTot, $this->numberFormat);
                    }
                }
            }
            // Creating age array
            ksort($result['age']);
            $result['age'] = array_slice($result['age'], 0, count($result['age']));
        }


        // Fetching users having zip code and nintydays from user and userscore table
        $qryZip = Yii::app()->db->createCommand('SELECT u.zip, u.id, s.nintydays FROM user u INNER JOIN userscore s ON u.id=s.user_id INNER JOIN scorechange sc ON sc.user_id=s.user_id WHERE u.zip IS NOT NULL AND u.zip != 0 AND sc.scorechange IS NOT NULL')->queryAll();

        if (isset($qryZip)) {
            foreach ($qryZip as $uVal) {
                if ($uVal['zip'] <> '' && $uVal['zip'] <> '0') {
                    $usrzip = substr($uVal['zip'], 0, 3);
                    if (strlen($usrzip) == 3) {

                        // cache regionalDetails table
                        $regionId = 0;
                        if ($regionObjs) {
                            foreach ($regionObjs as $regionObj) {
                                if ($regionObj->zipcoderangeprefix) {
                                    $stateZips = explode("|", $regionObj->zipcoderangeprefix);
                                    foreach ($stateZips as $stateZip) {
                                        $rangeZips = explode("-", $stateZip);
                                        if (count($rangeZips) > 1) {
                                            if ($usrzip >= intval($rangeZips[0]) && $usrzip <= intval($rangeZips[1])) {
                                                $regionId = $regionObj->region;
                                            }
                                        } else if ($usrzip == intval($rangeZips[0])) {
                                            $regionId = $regionObj->region;
                                        }
                                    }
                                }
                            }
                        }

                        if (isset($regionId) && $regionId != 0) {
                            $region = $regionId;
                            $avgTot = 0;
                            $avgTot = $uVal['nintydays'];

                            if (isset($result['region'][$region]['sum'])) {
                                $result['region'][$region]['sum'] = number_format(($result['region'][$region]['sum'] + $avgTot), $this->numberFormat);
                                $result['region'][$region]['total'] = $result['region'][$region]['total'] + 1;
                                $result['region'][$region]['name'] = $region;
                                $result['region'][$region]['average'] = number_format(($result['region'][$region]['sum'] / $result['region'][$region]['total']), $this->numberFormat);
                            } else {
                                $result['region'][$region]['sum'] = number_format($avgTot, $this->numberFormat);
                                $result['region'][$region]['total'] = 1;
                                $result['region'][$region]['name'] = $region;
                                $result['region'][$region]['average'] = number_format($avgTot, $this->numberFormat);
                            }
                        } else {
                            $izips[] = $usrzip;
                        }
                    }
                }
            }
            // Creating zip array.
            ksort($result['region']);
            $result['region'] = array_slice($result['region'], 0, count($result['region']));
            $result['invalidZips'] = $izips;
        }

        //append users by state data in json and store in db //

        $result['usersbystate'] = $this->actionUsersByState();


        // Saving score change results
        $repObj = new Analytics();
        $repObj->details = serialize($result);
        $repObj->datetime = date('Y-m-d h:i:s');
        $repObj->save();

        $this->sendResponse(200, CJSON::encode(array("status" => "OK")));
    }


    function actionCeUserReport() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) || Yii::app()->getSession()->get('wsuser')->roleid != User::USER_IS_ADMIN) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $user = User::model()->find("id = :user_id", array("user_id" => $user_id));
        if ($user && $user->roleid != 777) {
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "reportType" => "user", "ceUserId" => $ceUserId, "ceEmail" => $ceEmail, "items" => $totalRecords)));
        }

        if (isset($_POST["reset"])) {
            $records = array();
            $otlt = Otlt::model()->findAll();
            $constantArray = array();
            if ($otlt) {
                foreach ($otlt as $constant) {
                    $constantArray[$constant->id] = $constant->name;
                }
            }
            $assets = Assets::model()->findAll(array('condition' => "context='AUTO' and status=0 ", 'select' => array('distinct(user_id) as user_id')));
            $debts = Debts::model()->findAll(array('condition' => "context='AUTO' and status=0", 'select' => array('distinct(user_id) as user_id')));
            $insurances = Insurance::model()->findAll(array('condition' => "context='AUTO' and status=0", 'select' => array('distinct(user_id) as user_id')));
            $count = 0;
            $countAssets = 0;
            $countDebts = 0;
            $countInsurance = 0;
            $hashId = array();
            if ($assets) {
                $countAssets = count($assets);
                foreach ($assets as $asset) {
                    if (!isset($hashId[$asset->user_id])) {
                        $hashId[$asset->user_id] = true;
                        $count++;
                    }
                }
            }
            if ($debts) {
                $countDebts = count($debts);
                foreach ($debts as $debt) {
                    if (!isset($hashId[$debt->user_id])) {
                        $hashId[$debt->user_id] = true;
                        $count++;
                    }
                }
            }
            if ($insurances) {
                $countInsurance = count($insurances);
                foreach ($insurances as $insurance) {
                    if (!isset($hashId[$insurance->user_id])) {
                        $hashId[$insurance->user_id] = true;
                        $count++;
                    }
                }
            }
            $record = array();
            $record["usercount"] = number_format($count);
            $record["assetcount"] = number_format($countAssets);
            $record["debtcount"] = number_format($countDebts);
            $record["insurancecount"] = number_format($countInsurance);
            $records[] = $record;

            $assets = Assets::model()->findAll(array('condition' => "context='AUTO' and status=0 group by type, assettype order by sum(balance) desc", 'select' => array('sum(balance) as total', 'avg(balance) as balance', 'count(*) as count_assets', 'type', 'assettype')));
            if ($assets) {
                foreach ($assets as $asset) {
                    $record = array();
                    $record["average"] = "$" . number_format(round($asset->balance));
                    $record["sum"] = "$" . number_format(round($asset->total));
                    $record["count"] = number_format($asset->count_assets);
                    $record["type"] = $asset->getDefaultName($asset->type);
                    if (isset($constantArray[$asset->assettype])) {
                        $record["accounttype"] = $constantArray[$asset->assettype];
                    }
                    $records[] = $record;
                }
            }
            unset($assets);

            $debts = Debts::model()->findAll(array('condition' => "context='AUTO' and status=0 group by type, mortgagetype order by sum(balowed) desc", 'select' => array('sum(balowed) as totalDebts', 'avg(balowed) as balowed', 'count(*) as count_debts', 'type', 'mortgagetype')));
            if ($debts) {
                foreach ($debts as $debt) {
                    $record = array();
                    $record["average"] = "$" . number_format(round($debt->balowed));
                    $record["sum"] = "$" . number_format(round($debt->totalDebts));
                    $record["count"] = number_format($debt->count_debts);
                    $record["type"] = $debt->getDefaultName($debt->type);
                    if (isset($constantArray[$debt->mortgagetype])) {
                        $record["accounttype"] = $constantArray[$debt->mortgagetype];
                    }
                    $records[] = $record;
                }
            }
            unset($debts);

            $insurances = Insurance::model()->findAll(array('condition' => "context='AUTO' and status=0 group by type, lifeinstype order by sum(cashvalue) desc", 'select' => array('sum(cashvalue) as total_cashvalue', 'avg(cashvalue) as cashvalue', 'count(*) as count_insurance', 'type', 'lifeinstype')));
            if ($insurances) {
                foreach ($insurances as $insurance) {
                    $record = array();
                    $record["average"] = "$" . number_format(round($insurance->cashvalue));
                    $record["sum"] = "$" . number_format(round($insurance->total_cashvalue));
                    $record["count"] = number_format($insurance->count_insurance);
                    $record["type"] = $insurance->getDefaultName($insurance->type);
                    if (isset($constantArray[$insurance->lifeinstype])) {
                        $record["accounttype"] = $constantArray[$insurance->lifeinstype];
                    }
                    $records[] = $record;
                }
            }
            unset($insurances);
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "reportType" => "user", "items" => $records)));
        }

        $statusValue = array("Pending" => "0,3,4,5,6,7,10", "MFA Needed" => "1,2", "Success" => "8,11", "Needs Classification" => "9", "Bad Credentials" => "100", "Error" => "110,127,200");
        $email = trim($_POST["email"]);
        $status = trim($_POST["status"]);
        $statusQuery = "";
        if (isset($status)) {
            $statusArray = explode(",", $status);
            foreach ($statusArray as $stat) {
                if (isset($statusValue[$stat])) {
                    if ($statusQuery != "") {
                        $statusQuery .= ",";
                    }
                    $statusQuery .= $statusValue[$stat];
                }
            }
        }
        if ($statusQuery != "") {
            $statusQuery = "and lsstatus in (" . $statusQuery . ")";
        }
        function timesort($a, $b) {
            if ($a["sortfield"] > $b["sortfield"]) {
                return -1;
            }
            if ($a["sortfield"] < $b["sortfield"]) {
                return 1;
            }
            return 0;
        }


        $userRecords = array();
        $ceUserId = "None";
        $ceEmail = "None";

        $ce_user = null;
        if ($email == "") {
            $ce_user = User::model()->findAll('lastaccesstimestamp > NOW() - INTERVAL 7 DAY');
        } else {
            $ce_user = User::model()->findAll("lastaccesstimestamp > NOW() - INTERVAL 7 DAY and email like :email", array("email" => '%' . $email . '%'));
        }
        $ceUserReport = CashedgeItem::model()->findAll("modified > NOW() - INTERVAL 7 DAY " . $statusQuery);
        $ceCustomer = CashedgeAccount::model()->findAll();

        if (isset($ce_user) && !empty($ce_user)) {
            foreach ($ce_user as $currentUser) {
                $totalRecords = array();
                $totalStatusCount = array();
                $ceUserId = "None";
                $ceEmail = "None";

                if (isset($ceCustomer) && !empty($ceCustomer)) {
                    foreach ($ceCustomer as $customer) {
                        if ($customer->user_id == $currentUser->id) {
                            $ceEmail = $customer->username;
                            $ceUserId = $customer->ceuserid;
                            break;
                        }
                    }
                }

                $ceStatus = "";
                $modifiedLatest = '0000-00-00 00:00:0000';
                $sortModifiedLatest = '0000-00-00 00:00:0000';
                $totalStatusCount["Total"] = array("type" => "Total", "count" => 0);
                if (isset($ceUserReport) && !empty($ceUserReport)) {
                    foreach ($ceUserReport as $urow) {
                        if ($urow->user_id == $currentUser->id) {
                            switch ($urow->lsstatus) {
                                case 0;
                                case 3;
                                case 4;
                                case 5;
                                case 6;
                                case 7;
                                case 10;
                                    $ceStatus = "Pending";
                                    break;
                                case 1;
                                case 2;
                                    $ceStatus = "MFA Needed";
                                    break;
                                case 8;
                                case 11;
                                    $ceStatus = "Success";
                                    break;
                                case 9;
                                    $ceStatus = "Needs Classification";
                                    break;
                                case 100;
                                    $ceStatus = "Bad Credentials";
                                    break;
                                case 110;
                                case 127;
                                case 200;
                                    $ceStatus = "Error";
                                    break;
                                default;
                                    $ceStatus = "Unknown status";
                                    break;
                            }
                            if (!isset($totalStatusCount[$ceStatus])) {
                                $totalStatusCount[$ceStatus] = array("type" => $ceStatus, "count" => 1);
                            } else {
                                $totalStatusCount[$ceStatus]["count"] = $totalStatusCount[$ceStatus]["count"] + 1;
                            }
                            if (!isset($totalStatusCount["Total"])) {
                                $totalStatusCount["Total"] = array("type" => "Total", "count" => 1);
                            } else {
                                $totalStatusCount["Total"]["count"] = $totalStatusCount["Total"]["count"] + 1;
                            }
                            $lastModified = date("Y-M-d H:i:s", strtotime("$urow->modified - 8 hours"));
                            if ($sortModifiedLatest < $urow->modified) {
                                $modifiedLatest = $lastModified;
                                $sortModifiedLatest = $urow->modified;
                            }
                            $eachRecord = array(
                            "fiid" => $urow->Fiid,
                            "fiName" => $urow->name,
                            "fiLoginAcctId" => $urow->FILoginAcctId,
                            "status" => $ceStatus,
                            "accountDetails" => unserialize($urow->accountdetails),
                            "modified" => $lastModified,
                            "sortfield" => $urow->modified,
                            );
                            $totalRecords[] = $eachRecord;
                        }
                    }
                }

                $statusCount = array();
                $totalCount = $totalStatusCount["Total"]["count"];
                if ($totalCount > 0) {
                    foreach ($totalStatusCount as $key => $val) {
                        $statusCount[] = $val;
                    }
                    usort($totalRecords, 'timesort');
                    $eachRecord = array(
                    "flexEmail" => $currentUser->email,
                    "id" => $currentUser->id,
                    "ceUserId" => $ceUserId,
                    "ceEmail" => $ceEmail,
                    "statusCount" => $statusCount,
                    "totalCount" => $totalCount,
                    "modified" => $modifiedLatest,
                    "sortfield" => $sortModifiedLatest,
                    "records" => $totalRecords
                    );
                    $userRecords[] = $eachRecord;
                }
            }
        }

        usort($userRecords, 'timesort');
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "reportType" => "user", "items" => $userRecords)));
    }


    function actionLifeInsuranceParams() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) || Yii::app()->getSession()->get('wsuser')->roleid != User::USER_IS_ADMIN) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $user = User::model()->find("id = :user_id", array("user_id" => $user_id));
        if ($user && $user->roleid != 777) {
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "reportType" => "user", "ceUserId" => $ceUserId, "ceEmail" => $ceEmail, "items" => $totalRecords)));
        }

        $calcXMLObj = Yii::app()->calcxml;

        parent::setEngine();
        $valueObj = new stdClass();
        $valueObj->clientIncome = $this->sengine->grossIncome * 12;
        $valueObj->spouseIncome = $this->sengine->spouseIncome * 12;
        $valueObj->spouseAge = $this->sengine->spouseAge;
        $valueObj->spouseRetAge = $this->sengine->spouseRetAge;
        $valueObj->beforeTaxReturn = $this->sengine->userGrowthRate / 100;
        $valueObj->inflation = 0.034;
        $valueObj->funeral = 0;
        $valueObj->finalExpenses = 6560;
        $valueObj->mortgageBalance = $this->sengine->mortgageBalance;

        $valueObj->otherDebts = $this->sengine->otherDebts;
        if ($this->sengine->spouseAge == 0 && $this->sengine->child1Age == 0 && $this->sengine->child2Age == 0 && $this->sengine->child3Age == 0 && $this->sengine->child4Age == 0) {
            $valueObj->desiredIncome = 0;
        } else {
            $valueObj->desiredIncome = $this->sengine->userIncomePerMonth * 12 * 0.80; // 80 % of total income
        }
        if ($this->sengine->spouseAge > 0) {
            $valueObj->term = $this->sengine->spouseLifeEC - $this->sengine->spouseAge;
        } else {
            $valueObj->term = $this->sengine->lifeEC - $this->sengine->userCurrentAge;
        }

        $valueObj->collegeNeeds = $this->sengine->collegeAmount;
        $valueObj->investmentAssets = ($this->sengine->numeratorP14 - $this->sengine->insuranceCashValue);
        $valueObj->lifeInsurance = $this->sengine->LifeInsurance;
        $valueObj->includeSocsec = "Y";

        $valueObj->child1Age = $this->sengine->child1Age;
        $valueObj->child2Age = $this->sengine->child2Age;
        $valueObj->child3Age = $this->sengine->child3Age;
        $valueObj->child4Age = $this->sengine->child4Age;

        $returnArray = array('status' => 'ok');

        $outputCalc = $calcXMLObj->lifeInsuranceINeedHelper($valueObj, $returnArray);
        $lifeInsuranceINeed = $outputCalc["totalLifeInsuranceNeeded"];
        $reportRows = $outputCalc["reportrows"];


        $this->sendResponse("200", CJSON::encode(array("status" => "OK", "totalLifeInsuranceNeeded" => $lifeInsuranceINeed,
        "summary" => $reportRows["summary"], "payments" => $reportRows["payments"])));
    }


    /*
     * Function MonteCarloParams creates a detailed report that contains both summary
     * param values and yearly median datails.
     */
    function actionMonteCarloParams() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) || Yii::app()->getSession()->get('wsuser')->roleid != User::USER_IS_ADMIN) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $user = User::model()->find("id = :user_id", array("user_id" => $user_id));
        if ($user && $user->roleid != 777) {
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "reportType" => "user", "mcUserId" => $user_id)));
        }

        $name = $user->email;
        if (isset($user->firstname) && $user->firstname != "" && isset($user->lastname) && $user->lastname != "") {
            $name = $user->firstname . " " . $user->lastname;
        }

        // Run the simulations and return median data for the reports
        $monteCarloUser = new MonteCarloUser();
        $mcData = $monteCarloUser->runMonteCarlo($user_id, "medianData");

        if ($mcData) {
            $this->sendResponse("200", CJSON::encode(array(
            'status' => 'OK',
            'probability' => round($mcData['probability'] * 100, 0),
            'numiterations' => $mcData['numIterations'],
            'portfolioBalanceAtRetirement' => $mcData['portfolioBalanceAtRetirement'],
            'sustainablewithdrawalpercent' => $mcData['sustainablewithdrawalpercent'],
            'monthlyPensionAtRetirement' => $mcData['monthlyPensionAtRetirement'],
            'monthlySocialSecurityAtRetirement' => $mcData['monthlySocialSecurityAtRetirement'],
            'futureIncome' => $mcData['futureIncome'],
            'mcparams' => $mcData['mcParams'],
            'medianData' => $mcData['medianData'],
            'name' => $name
            )));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR",
            "message" => "monte carlo did not run.")));
        }
    }


    /*
     *  actionRunMonteCarlo is run on certain changes made by the user through the use of
     *  a queueing table montecarlouser.
     */
    function actionRunMonteCarlo() {
        $md5hashval = "b28159c334ecd24e2f8d17ad64407362";
        if (!isset($_GET['accTok']) || $_GET['accTok'] == '' || md5($_GET['accTok']) != $md5hashval) {
            header('HTTP/1.1 403 Unauthorized');
            exit;
        }
        $usersRun = array();
        try {

            $monteCarloUser = new MonteCarloUser();
            $userNeedingMonteCarlo =  MonteCarloUser::model()->find(array('condition' => 'modifiedtimestamp > lastruntimestamp and failedruns < 5', 'order' => 'failedruns, modifiedtimestamp, lastruntimestamp'));

            if ($userNeedingMonteCarlo) {
                $timestamp = date("Y-m-d H:i:s");
                $userNeedingMonteCarlo->failedruns = $userNeedingMonteCarlo->failedruns + 1;
                $userNeedingMonteCarlo->lastfailedtimestamp = $timestamp;
                $userNeedingMonteCarlo->save();
                $user_id = $userNeedingMonteCarlo->user_id;

                $mcData = $monteCarloUser->runMonteCarlo($user_id);

                if ($mcData) {
                    $userNeedingMonteCarlo->montecarloprobability = $mcData['probability'];
                    $userNeedingMonteCarlo->futureincome = $mcData['futureIncome'];
                    $userNeedingMonteCarlo->lastruntimestamp = $timestamp;
                    $userNeedingMonteCarlo->failedruns = 0;
                    $userNeedingMonteCarlo->lastfailedtimestamp = "0000-00-00 00:00:00";

                    $userNeedingMonteCarlo->save();
                    Yii::app()->cache->set('score' . $user_id, date("Y-m-d H:i:s"));
                    Yii::app()->cache->set('montecarloprobability' . $user_id, $mcData['probability']);

                    $this->sendResponse(200, CJSON::encode(array(
                    'user' => $user_id,
                    'probability' => $mcData['probability'],
                    'numiterations' => $mcData['numIterations'],
                    'portfolioBalanceAtRetirement' => $mcData['portfolioBalanceAtRetirement'],
                    'sustainablewithdrawalpercent' => $mcData['sustainablewithdrawalpercent'],
                    'monthlyPensionAtRetirement' => $mcData['monthlyPensionAtRetirement'],
                    'monthlySocialSecurityAtRetirement' => $mcData['monthlySocialSecurityAtRetirement'],
                    'futureIncome' => $mcData['futureIncome']
                    )));
                } else {
                    $this->sendResponse(200, CJSON::encode(array("status" => "ERROR",
                    "message" => "monte carlo did not run.")));
                }
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "OK",
                "message" => "monte carlo did not run, no users to be run.")));
            }
        } catch (Exception $E) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR",
            "message" => "monte carlo did not run, attempt number " . $userNeedingMonteCarlo->failedruns)));
        }
    }

    /*
     * Called by a nightly batch file script, users stuck with 5 failed runs
     * are reset to 0 failed runs after 3 days.
     */
    function actionResetMonteCarloFailedRuns() {
        $md5hashval = "b28159c334ecd24e2f8d17ad64407362";
        if (!isset($_GET['accTok']) || $_GET['accTok'] == '' || md5($_GET['accTok']) != $md5hashval) {
            header('HTTP/1.1 403 Unauthorized');
            exit;
        }

        $todayDate = new DateTime();
        $threeDaysAgo = $todayDate->sub(new DateInterval('P3D'));
        $updateFailedUsers = MonteCarloUser::model()->updateAll(array('failedruns' => 0,
        'lastfailedtimestamp' => "0000-00-00 00:00:00"), 'failedruns = 5 AND lastfailedtimestamp <> "0000-00-00 00:00:00" ' .
        'AND lastfailedtimestamp < "' . $threeDaysAgo->format('Y-m-d H:i:s') . '"');

        if ($updateFailedUsers) {
            $this->sendResponse(200, CJSON::encode(array("status" => "OK",
            "message" => $updateFailedUsers . " user(s) reset to 0 failed runs.")));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "OK",
            "message" => "There were no monte carlo failed users to reset.")));
        }
    }


    public function actionUpdateEchoUserAgreement() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        if (!isset($_POST["action"]) && $_POST["action"] != 'decline' && $_POST["action"] != 'accept') {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required parameter 'action' must be set to either 'accept' or 'decline'.")));
        }

        $permission = '1';
        if ($_POST["action"] == 'decline') {
            $permission = '2';
        }
        $email = Yii::app()->getSession()->get('wsuser')->email;

        $echoUser = EchoUser::model()->find("email=:email and permission='0'", array("email" => $email));
        if (!$echoUser) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "This user is not a part of EchoUser study.")));
        }
        $echoUser->permission = $permission;
        $echoUser->save();
    }


    /**
     * Update Connecting Account, Debt and Insurance checkbox status
     */
    public function actionUpdatePreferences() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $checkboxStatus = $_POST["checkboxStatus"];
        $checkboxName = $_POST["checkboxName"];
        $userPerDetails = Userpersonalinfo::model()->find("user_id=:user_id", array("user_id" => $user_id));

        $userPerDetails->$checkboxName = $checkboxStatus;

        $userPerDetails->save();

        $aaDet = Assets::model()->findAll(array('condition' => "user_id = :user_id AND context = 'AUTO' AND status=0", 'params' => array("user_id" => $user_id), 'select' => 'balance'));
        $adDet = Debts::model()->findAll(array('condition' => "user_id = :user_id AND context = 'AUTO' AND status=0", 'params' => array("user_id" => $user_id), 'select' => 'balowed'));
        $aiDet = Insurance::model()->findAll(array('condition' => "user_id = :user_id AND context = 'AUTO' AND status=0", 'params' => array("user_id" => $user_id), 'select' => 'annualpremium'));

        parent::setEngine($user_id);
        // wfPoint38:
        //Connecting Account Preference checked
        if ((!empty($adDet) || !empty($aaDet) || !empty($aiDet)) || $userPerDetails->connectAccountPreference == '1') {
            $this->sengine->userProfilePoints_connectAccount = 1;
        } else {
            $this->sengine->userProfilePoints_connectAccount = 0;
        }

        //Debts Preference checked
        $debts = Debts::model()->findAll("user_id=:user_id and status=0", array("user_id" => $user_id));
        if ($debts || $userPerDetails->debtsPreference == '1') {
            $this->sengine->userProfilePoints_debts = 1;
        } else {
            $this->sengine->userProfilePoints_debts = 0;
        }
        //Insurance Preference checked
        $insurance = Insurance::model()->findAll("user_id=:user_id and status=0", array("user_id" => $user_id));
        if ($insurance || $userPerDetails->insurancePreference == '1') {
            $this->sengine->userProfilePoints_insurance = 1;
        } else {
            $this->sengine->userProfilePoints_insurance = 0;
        }
        parent::saveEngine();
        parent::calculateScore("COMPLETENESS", $user_id);
        $this->sendResponse(200, CJSON::encode(array($checkboxName => $checkboxStatus, "message" => 'Updated successfully')));
    }


    public function actiongetUserPreferences() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $array = array();
        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $userPerDetails = Userpersonalinfo::model()->find("user_id=:user_id", array("user_id" => $user_id));

        $debts = Debts::model()->findAll("user_id=:user_id and status!=1", array("user_id" => $user_id));
        if ($debts || $userPerDetails["debtsPreference"] == "1") {
            $debtData = 1;
        } else {
            $debtData = 0;
        }

        if ($debts) {
            $debtAdded = 1;
        } else {
            $debtAdded = 0;
        }

        $insurance = Insurance::model()->findAll("user_id=:user_id and status!=1", array("user_id" => $user_id));
        if ($insurance || $userPerDetails["insurancePreference"] == "1") {
            $insuranceData = 1;
        } else {
            $insuranceData = 0;
        }
        if ($insurance) {
            $insuranceAdded = 1;
        } else {
            $insuranceAdded = 0;
        }

        $debtsAuto = Debts::model()->findAll("user_id=:user_id and status!=1 and context=:contxt", array("user_id" => $user_id, "contxt" => 'AUTO'));
        $assetsAuto = Assets::model()->findAll("user_id=:user_id and status!=1 and context=:contxt", array("user_id" => $user_id, "contxt" => 'AUTO'));
        $insuranceAuto = Insurance::model()->findAll("user_id=:user_id and status!=1 and context=:contxt", array("user_id" => $user_id, "contxt" => 'AUTO'));
        $userHasConnectedAccounts = ($debtsAuto || $assetsAuto || $insuranceAuto) ? 1 : 0;

        $array["user_id"] = $userPerDetails["user_id"];
        $array["connectAccountPreference"] = $userPerDetails["connectAccountPreference"];
        $array["debtsPreference"] = $userPerDetails["debtsPreference"];
        $array["insurancePreference"] = $userPerDetails["insurancePreference"];
        $array["debtData"] = $debtData;
        $array["insuranceData"] = $insuranceData;
        $array["debtAdded"] = $debtAdded;
        $array["insuranceAdded"] = $insuranceAdded;

        $this->sendResponse(200, CJSON::encode(array("userdata" => $array, "status" => 'Ok', 'userHasConnectedAccounts' => $userHasConnectedAccounts)));
    }


    public function actionGetUserProfileData() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $userDetails = User::model()->findByPk($user_id);
        $userPerDetails = Userpersonalinfo::model()->find("user_id=:user_id", array("user_id" => $user_id));

        $userData = array(
        'firstname' => $userDetails->firstname,
        'lastname' => $userDetails->lastname,
        'dob' => $userPerDetails->age,
        'zipcode' => $userDetails->zip,
        'maritalstatus' => $userPerDetails->maritalstatus,
        'noofchildren' => $userPerDetails->noofchildren,
        'childrensage' => $userPerDetails->childrensage,
        'retirementstatus' => $userPerDetails->retirementstatus,
        'risk' => $userPerDetails->risk
        );

        $income = Income::model()->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $user_id), 'select' => 'totaluserincome'));

        if ($income && $income->totaluserincome > 0) {
            $userData['incomeData'] = 1;
        } else {
            $userData['incomeData'] = 0;
        }

        $expense = Expense::model()->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $user_id), 'select' => 'actualexpense'));
        if ($expense && $expense->actualexpense > 0) {
            $userData['expenseData'] = 1;
        } else {
            $userData['expenseData'] = 0;
        }

        $debts = Debts::model()->findAll("user_id=:user_id and status=0", array("user_id" => $user_id));
        if ($debts || $userPerDetails["debtsPreference"] == "1") {
            $userData['debtData'] = 1;
        } else {
            $userData['debtData'] = 0;
        }

        $assets = Assets::model()->findAll("user_id=:user_id and status=0", array("user_id" => $user_id));
        if ($assets) {
            $userData['assetData'] = 1;
        } else {
            $userData['assetData'] = 0;
        }

        $insurance = Insurance::model()->findAll("user_id=:user_id and status=0", array("user_id" => $user_id));
        if ($insurance || $userPerDetails["insurancePreference"] == "1") {
            $userData['insuranceData'] = 1;
        } else {
            $userData['insuranceData'] = 0;
        }

        $misc_prof = Miscellaneous::model()->find("user_id=:user_id", array("user_id" => $user_id));
        if (!$misc_prof) {
            $misc_prof = new Misc();
            $misc_prof->user_id = $user_id;
            $misc_prof->save();
        }
        //misc_taxes//
        $userData['taxpay'] = $misc_prof->taxpay;
        $userData['taxbracket'] = $misc_prof->taxbracket;
        $userData['taxvalue'] = $misc_prof->taxvalue;
        $userData['taxcontri'] = $misc_prof->taxcontri;
        $userData['taxStdOrItemDed'] = $misc_prof->taxStdOrItemDed;
        //misc_estate_planning//
        $userData['misctrust'] = $misc_prof->misctrust;
        $userData['miscreviewmonth'] = $misc_prof->miscreviewmonth;
        $userData['miscreviewyear'] = $misc_prof->miscreviewyear;
        $userData['mischiddenasset'] = $misc_prof->mischiddenasset;
        $userData['miscrightperson'] = $misc_prof->miscrightperson;
        $userData['miscliquid'] = $misc_prof->miscliquid;
        $userData['miscspouse'] = $misc_prof->miscspouse;
        //misc_more//
        $userData['moremoney'] = $misc_prof->moremoney;
        $userData['moreinvrebal'] = $misc_prof->moreinvrebal;
        $userData['moreautoinvest'] = $misc_prof->moreautoinvest;
        $userData['moreliquidasset'] = $misc_prof->moreliquidasset;
        $userData['morecharity'] = $misc_prof->morecharity;
        $userData['morecreditscore'] = $misc_prof->morecreditscore;
        $userData['morereviewmonth'] = $misc_prof->morereviewmonth;
        $userData['morescorereviewyear'] = $misc_prof->morescorereviewyear;

        $goals = Goal::model()->findAll("user_id=:user_id and goalstatus=1 AND goalname!='Retirement Goal'", array("user_id" => $user_id));
        if ($goals) {
            $userData['goalData'] = 1;
        } else {
            $userData['goalData'] = 0;
        }

        $aaDet = Assets::model()->findAll(array('condition' => "user_id = :user_id AND context = 'AUTO' AND status=0", 'params' => array("user_id" => $user_id), 'select' => 'balance'));
        $adDet = Debts::model()->findAll(array('condition' => "user_id = :user_id AND context = 'AUTO' AND status=0", 'params' => array("user_id" => $user_id), 'select' => 'balowed'));
        $aiDet = Insurance::model()->findAll(array('condition' => "user_id = :user_id AND context = 'AUTO' AND status=0", 'params' => array("user_id" => $user_id), 'select' => 'annualpremium'));

        if ((!empty($adDet) || !empty($aaDet) || !empty($aiDet)) || $userPerDetails["connectAccountPreference"]) {
            $userData['connectAccount'] = 1;
        } else {
            $userData['connectAccount'] = 0;
        }

        $this->sendResponse(200, CJSON::encode(array("userprofiledata" => $userData, "status" => 'Ok')));
    }


    public function actionUsersByState() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) || Yii::app()->getSession()->get('wsuser')->roleid != User::USER_IS_ADMIN) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $sql = 'SELECT * FROM regiondetails GROUP BY statecode ORDER by statecode ASC';
        $regionObjs = Regiondetails::model()->findAllBySql($sql);
        $finalresult = array();
        $result = array();
        $sum = 0;
        if ($regionObjs) {
            foreach ($regionObjs as $key => $regionObj) {
                $result[$key]["statecode"] = $regionObj["statecode"];
                $result[$key]["state"] = $regionObj["state"];
                if ($regionObj["region"] == 1) {
                    $region = "Northeast";
                }if ($regionObj["region"] == 2) {
                    $region = "Midwest";
                }if ($regionObj["region"] == 3) {
                    $region = "South";
                }if ($regionObj["region"] == 4) {
                    $region = "West";
                }
                //$result[$key]["region"] = $regionObj["region"];
                $result[$key]["regionname"] = $region;
                //$result[$key]["division"] = $regionObj["division"];
                //$result[$key]["zipcoderangeprefix"] = $regionObj["zipcoderangeprefix"];

                $stateZips = explode("|", $regionObj['zipcoderangeprefix']);
                $totalCount = 0;
                $totalCount1 = 0;
                $totalCount2 = 0;
                for ($j = 0; $j < count($stateZips); $j++) {
                    $rangeZips = explode("-", $stateZips[$j]);

                    if (count($rangeZips) > 1) {
                        $userSql = "SELECT * FROM user WHERE length(zip) >= 3 AND zip REGEXP '^[0-9]+$' AND zip != 0 AND (left(zip, 3)) BETWEEN '$rangeZips[0]' AND '$rangeZips[1]'";
                        $resSql1 = Yii::app()->db->createCommand($userSql)->queryAll();
                        if ($resSql1) {
                            $totalCount1 = count($resSql1);
                            $totalCount = $totalCount + $totalCount1;
                        }
                    } else {
                        $userSql = "SELECT * FROM user WHERE length(zip) >= 3 AND zip REGEXP '^[0-9]+$' AND zip != 0 AND (left(zip, 3)) = '$rangeZips[0]'";
                        $resSql1 = Yii::app()->db->createCommand($userSql)->queryAll();
                        if ($resSql1) {
                            $totalCount2 = count($resSql1);
                            $totalCount = $totalCount + $totalCount2;
                        }
                    }
                }
                $result[$key]["user_count"] = $totalCount;
                $sum = $sum + $result[$key]["user_count"];
            }
            //calculating user percentage per state and stroing in array//
            foreach ($result as $key => $value) {
                $result[$key]["user_per"] = number_format(($result[$key]["user_count"] / $sum * $this->percentage), $this->numberFormat);
            }
            $result = $this->array_sort($result, 'user_count', SORT_DESC); // Sort by max user_count first

            $finalresult["totalUsers"] = $sum;
            $finalresult["dataByState"] = $result;

            return $finalresult;
        }
    }


    function array_sort($array, $on, $order = SORT_ASC) {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }
            $i = 0;
            foreach ($sortable_array as $k => $v) {
                $new_array[$i] = $array[$k];
                $i++;
            }
        }

        return $new_array;
    }


    /*
     * debts + assets + Insurance in one API, - 1
     * goals API  - 2
     * estimation in another API, -3
     * linked assets + pending accounts in a third API,
     * breakdown properties into fourth API
     *
     */
    function actionGetFinancialDetails() {
        //if there is no session
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $singleORMAssetObj = Assets::model()->findAll("user_id=:user_id and status!=1", array("user_id" => $user_id));
        $debtsORMObj = Debts::model()->findAll("user_id=:user_id and status!=1", array("user_id" => $user_id));
        $insuranceORMObj = Insurance::model()->findAll("user_id=:user_id and status!=1", array("user_id" => $user_id));

        $netWorth = 0;
        $assetTotal = 0;
        $assets = array();
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
            'lstype' => $acc->lstype
            );
            $assets[] = $lsEachAcc;
            if ($acc->status == 0 && $acc->type != 'PENS' && $acc->type != 'SS') {
                $assetTotal = $assetTotal + $acc->balance;
            }
        }
        $netWorth = $netWorth + $assetTotal;

        $debtTotal = 0;
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
            'lstype' => "DEBT"
            );
            $lsAccDEBT[] = $lsEachAcc;
            if ($acc->status == 0 && $acc->monthly_payoff_balances == 0) {
                $debtTotal = $debtTotal + $acc->balowed;
            }
        }
        $netWorth = $netWorth - $debtTotal;

        $insuranceTotal = 0;
        $lsAccINSURANCE = array();
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
            'lstype' => "INSURANCE"
            );
            $lsAccINSURANCE[] = $lsEachAcc;
            if ($acc->status == 0 && $acc->type == 'LIFE') {
                $insuranceTotal = $insuranceTotal + $acc->cashvalue;
            }
        }
        $netWorth = $netWorth + $insuranceTotal;

        $wsEachUserItemBankAccount = array(
        'assets' => $assets,
        'assetTotal' => $assetTotal,
        'debts' => $lsAccDEBT,
        'debtTotal' => $debtTotal,
        'insurance' => $lsAccINSURANCE,
        'insuranceTotal' => $insuranceTotal,
        'networth' => $netWorth,
        );
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "accounts" => $wsEachUserItemBankAccount)));
    }


    public function actionGetSubscriptionStatus() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) && !isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        require(realpath(dirname(__FILE__) . '/../config/config.mailchimp.php'));
        $api = new MCAPI($mcapikey);

        if (isset(Yii::app()->getSession()->get('wsadvisor')->email)) {
            $my_email = Yii::app()->getSession()->get('wsadvisor')->email;
            $listId = $advisorListId;
        } else if (isset(Yii::app()->getSession()->get('wsuser')->email)) {
            $my_email = Yii::app()->getSession()->get('wsuser')->email;
            $listId = $loggedinListId;
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $retval = $api->listMemberInfo($listId, $my_email);
        $status = 'unsubscribed';
        if (isset($retval['data'][0]['id'])) {
            $status = $retval['data'][0]['status'];
        }
        $this->sendResponse(200, CJSON::encode(array(
        "status" => "OK",
        "message" => $status,
        "email_address" => $my_email
        )));
    }


    public function actionSetSubscriptionStatus() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) && !isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        if (!isset($_POST["action"])) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'action' was not set.")));
        }
        $action = $_POST["action"];
        $retval = $this->setSubscriptionStatus($action);
        if (!isset($retval)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Required Parameter 'action' must be set to either 'Unsubscribe', or 'Subscribe'.")));
        }

        $status = 'unsubscribed';
        if (isset($retval['data'][0]['id'])) {
            $status = $retval['data'][0]['status'];
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => $status)));
    }


    function setSubscriptionStatus($action, $forceUser = false) {
        require(realpath(dirname(__FILE__) . '/../config/config.mailchimp.php'));

        $retval = null;
        $api = new MCAPI($mcapikey);

        if (!$forceUser && isset(Yii::app()->getSession()->get('wsadvisor')->email)) {
            $my_email = Yii::app()->getSession()->get('wsadvisor')->email;
            $listId = $advisorListId;
        } else if (isset(Yii::app()->getSession()->get('wsuser')->email)) {
            $my_email = Yii::app()->getSession()->get('wsuser')->email;
            $listId = $loggedinListId;
        } else {
            return $retval;
        }

        if ($action == "Subscribe") {
            $merge_vars = array('FNAME' => "", 'LNAME' => "");
            $api->listSubscribe($listId, $my_email, $merge_vars);
            $retval = $api->listMemberInfo($listId, $my_email);
        } else if ($action == "Unsubscribe") {
            $api->listUnsubscribe($listId, $my_email);
            $retval = $api->listMemberInfo($listId, $my_email);
        }

        return $retval;
    }

    function actionUnsubscribeEmail() {
        require(realpath(dirname(__FILE__) . '/../config/config.mailchimp.php'));

        $code = "";
        $type = "";
        if (isset($_POST["code"]) && isset($_POST["type"]) ) {
             $code = $_POST["code"];
             $type = $_POST["type"];
        }

        $retval = null;
        $api = new MCAPI($mcapikey);

        $advisorToUnsubscribe = "";

        if ($type == "us") {
            $userToUnsubscribe = User::model()->find("unsubscribecode = :code", array("code" => $code));
            if ($userToUnsubscribe && $userToUnsubscribe->email) {
                $api->listUnsubscribe($loggedinListId, $userToUnsubscribe->email);
            //    $retval = $api->listMemberInfo($loggedinListId, $userToUnsubscribe->email);
            }
        }
        else if ($type == "ad") {
            $advisorToUnsubscribe = Advisor::model()->find("unsubscribecode = :code", array("code" => $code));
            if ($advisorToUnsubscribe && $advisorToUnsubscribe->email) {
                $api->listUnsubscribe($advisorListId, $advisorToUnsubscribe->email);
            //    $retval = $api->listMemberInfo($advisorListId, $advisorToUnsubscribe->email);
            }
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "You have been successfully unsubscribed.")));
    }

    function actionMoveUserAssetData() {
        if (!isset($_GET['code'])) {        
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Bad Code')));
        }
        $code = md5($_GET['code']);
        if($code == "724f4dcba70ea2642f1d9ae3c4f29e40") {
            $debtObject = new Debts();
            $debt = $debtObject->find("id = 28875 and user_id = 45924");            
            if($debt) {
                $asset = new Assets();
                $asset->refid = $debt->refid;
                $asset->context = $debt->context;
                $asset->status = $debt->status;
                $asset->type = 'BANK';
                $asset->user_id = $debt->user_id;
                $asset->FILoginAcctId = $debt->FILoginAcctId;
                $asset->accttype = 'SDA&SDA';
                $asset->subtype = 'saving';
                $asset->actid = $debt->actid;
                $asset->name = $debt->name;
                $asset->balance = $debt->balowed;
                $asset->growthrate = $debt->apr;
                $asset->modifiedtimestamp = date("Y-m-d H:i:s");
                $asset->save();
                
                parent::setEngine($asset->user_id);
                $assetController = new AssetController(1);
                $assetController->actionreCalculateScoreAssets($asset, 'ADD', $asset->user_id, 1);                                    
                unset($assetController, $asset);

                $debt->status = 1;
                $debt->modifiedtimestamp = date("Y-m-d H:i:s");
                $debt->save();
                
                parent::setEngine($debt->user_id);
                $debtController = new DebtController(1);
                $debtController->actionreCalculateScoreDebts($debt, 'DELETE', $debt->user_id, 1);                                    
                parent::unsetEngine();
                unset($debtController, $debt);
            }
            unset($assets);         
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => 'Success')));
        }
        else
        {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Bad Code')));
        }
    }



}

?>
