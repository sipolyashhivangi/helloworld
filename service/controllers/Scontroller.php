<?php

/* * ********************************************************************
 * Filename: Scontroller.php
 * Folder: controllers
 * Description: Calls score engine calculations for specified sections
 *              For more info refer the doc Points By Wireframe V 2.doc
 *              of leapscore
 * @author Subramanya HS (For TruGlobal Inc)
 * @editor Thayub J (For Myself)
 * @copyright (c) 2012 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class Scontroller extends Controller {

    /**
     *
     *
     */
    const CONNECT_ACCOUNT = "wfPoint1";
    const INCOME = "wfPoint2,wfPoint7,wfPoint9,wfPoint29,wfPoint30,wfPoint38";
    const EXPENSE = "wfPoint3,wfPoint1,wfPoint2,wfPoint4,wfPoint28,wfPoint30,wfPoint38,wfPoint58";
    const DEBTS = "wfPoint1,wfPoint4,wfPoint5,wfPoint6,wfPoint7,wfPoint8,wfPoint9,wfPoint11,wfPoint29,wfPoint38";
    const ASSET = "wfPoint3,wfPoint13,wfPoint1,wfPoint4,wfPoint10,wfPoint11,wfPoint12,wfPoint14,wfPoint15,wfPoint16,wfPoint17,wfPoint18,wfPoint19,wfPoint20,wfPoint21,wfPoint22,wfPoint23,wfPoint25,wfPoint26,wfPoint27,wfPoint28,wfPoint29,wfPoint30,wfPoint36,wfPoint38,wfPoint44,wfPoint45,wfPoint47,wfPoint58,wfPoint59";
    const RISK = "wfPoint13,wfPoint12,wfPoint16,wfPoint18,wfPoint23,wfPoint25,wfPoint26,wfPoint28,wfPoint29,wfPoint36,wfPoint38,wfPoint44,wfPoint45,wfPoint47,wfPoint59";
    const INSURANCE = "wfPoint3,wfPoint1,wfPoint4,wfPoint10,wfPoint11,wfPoint14,wfPoint28,wfPoint29,wfPoint30,wfPoint31,wfPoint32,wfPoint33,wfPoint34,wfPoint38,wfPoint24,wfPoint58";
    const MISCTAX = "wfPoint35,wfPoint36,wfPoint37,wfPoint38";
    const MISCESTATE = "wfPoint38,wfPoint39,wfPoint40,wfPoint41,wfPoint42,wfPoint43";
    const MISCMORE = "wfPoint38,wfPoint44,wfPoint45,wfPoint46,wfPoint47,wfPoint48,wfPoint49";
    const LEARNING = "wfPoint50";
    const GOALPRIORITY = "wfPoint38,wfPoint58,wfPoint59";
    const GOAL = "wfPoint13,wfPoint11,wfPoint12,wfPoint16,wfPoint18,wfPoint22,wfPoint23,wfPoint25,wfPoint26,wfPoint29,wfPoint36,wfPoint38,wfPoint44,wfPoint45,wfPoint47,wfPoint58,wfPoint59";
    const PROFILE = "wfPoint13,wfPoint16,wfPoint19,wfPoint20,wfPoint21,wfPoint22,wfPoint23,wfPoint25,wfPoint26,wfPoint27,wfPoint29,wfPoint30,wfPoint31,wfPoint36,wfPoint38,wfPoint44,wfPoint45,wfPoint47,wfPoint59";
    const ACTION = "actionStep1,actionStep2,actionStep3,actionStep4,actionStep5,actionStep6,actionStep7,actionStep8,actionStep9,actionStep10,actionStep11,actionStep13,actionStep12,actionStep14,actionStep15";
    const CALCXML = "wfPoint5,wfPoint29";
    const MONTECARLO = "MonteCarlo";
    const SPECIAL = "wfPoint10,wfPoint12,wfPoint17,wfPoint18,wfPoint20,wfPoint21,wfPoint28";
    // for mobile version//
    const SAVINGS = "WfPoint12,WfPoint16,WfPoint17,WfPoint18";
    const DOB = "WfPoint12,WfPoint18,WfPoint22,WfPoint29,WfPoint31";

    /**
     *  For Realistic Action Points
     *
     */
    const CONNECTACCOUNT = "WfPoint1,WfPoint4,WfPoint38";
    const INCREASELIFEINSURANCE = "WfPoint29";
    const GETLIFEINSURANCE = "WfPoint29";
    const COMPLETERISKTOLERANCE = "WfPoint28";
    const REVIEWBENEFICIARY = "WfPoint15";
    const ADDIRAROTHORTRADITIONAL = "WfPoint12";
    const UPDATEWILLANDESTATEPLANNING = "WfPoint39,WfPoint40";
    const ADDGOAL = "WfPoint58,WfPoint59";
    const SETGOAL = "WfPoint58,WfPoint59";
    const MORTGAGEDEBT = "WfPoint8";
    const CREATEEMERGENCYFUND = "WfPoint12";
    const REVIEWCREDITSCORE = "WfPoint49";
    const MOREASSET = "WfPoint12";
    const MOREWILLANDTRUST = "WfPoint39";
    const COMPLETENESS = "WfPoint38";
    const INCREASEDISABILITYINSURANCE = "WfPoint30";
    const GETDISABILITYINSURANCE = "WfPoint30";
    const IMPROVECREDITSCORE = "WfPoint49";
    const REFINANCE = "WfPoint5";
    const INVESTMENTMULTIPLIER = "WfPoint16,WfPoint23,WfPoint25,WfPoint26,WfPoint36,WfPoint44,WfPoint45,WfPoint47,WfPoint59";
    const RA_ASSET = "WfPoint3,WfPoint13,WfPoint1,WfPoint4,WfPoint10,WfPoint11,WfPoint12,WfPoint14,WfPoint15,WfPoint16,WfPoint17,WfPoint18,WfPoint19,WfPoint20,WfPoint21,WfPoint22,WfPoint23,WfPoint25,WfPoint26,WfPoint27,WfPoint28,WfPoint29,WfPoint30,WfPoint36,WfPoint38,WfPoint44,WfPoint45,WfPoint47,WfPoint58,WfPoint59";
    const MOREDEBT = "WfPoint8";
    const FILLMISCTAX = "WfPoint35,WfPoint36,WfPoint37";
    const FILLMISCESTATEPLANNING = "WfPoint39,WfPoint40,WfPoint41,WfPoint42,WfPoint43";
    const MOREINSURANCE = "WfPoint29";
    const REVIEWRISKTOLERANCE = "WfPoint13";
    const ADDAUTOREBALANCE = "WfPoint45";
    const ESTATEPLANNING = "WfPoint39,WfPoint40";
    const EVALUATECONSUMERDEBTCOSTS = "WfPoint8";
    const EVALUATEHOUSINGCOSTS = "WfPoint9";
    const SETUPGOAL = "WfPoint58,WfPoint59";
    const HEALTHINSURANCE = "WfPoint24";
    const REVIEWHEALTHINSURANCE = "WfPoint24";
    const REVIEWLIFEINSURANCE = "WfPoint29";
    const REVIEWDISABILITYINSURANCE = "WfPoint30";
    const INCREASESAVINGS = "WfPoint58,WfPoint59";
    const RETIREMENTFUND = "WfPoint58,WfPoint59";
    const SAVINGSACCOUNT = "WfPoint12";
    const CHARITY = "WfPoint48";
    const NONCOR = "WfPoint25";
    const CREATEINFORMATIONLIST = "WfPoint41";
    const DEGRADABLEPOINTS = "WfPoint24,WfPoint30,WfPoint31,WfPoint32,WfPoint33,WfPoint39,WfPoint40";

    public $sengine = null;
    public $simulationengine = null;
    public $totalScore = 0;
    public $simulatedPoint = 0;
    public $user_id = 0;
    public $peerminAge = 21;
    public $peermaxAge = 75;
    public $peerdefaultAge = 30;

    /**
     * get initial score
     */
    function init() {
        //if accessed from backend process
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->user_id = Yii::app()->getSession()->get('wsuser')->id;

            //if user is not logged in
            if ($this->user_id == 0) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR")));
            } else {
                // for update user last access date & time
                $umodel = new User;
                $file = $umodel->find("id=:user_id", array("user_id" => Yii::app()->getSession()->get('wsuser')->id));
                $file->lastaccesstimestamp = new CDbExpression('NOW()');
                $file->saveAttributes(array('lastaccesstimestamp'));
            }
        }
    }

    /**
     * get the engine present in the
     * cache / session if present
     */
    function setEngine($id = 0) {
        //get the score from cache
        // or table
        $user_id = $id;
        if (isset(Yii::app()->session['wsuser'])) {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
        }
        
        if (isset(Yii::app()->session["sengine"])) {
            $this->sengine = unserialize(Yii::app()->session["sengine"]);
        } else {

            //get the score from table
            $userScoreObj = UserScore::model()->find("user_id=:user_id", array("user_id" => $user_id));
            //default total score is 1
            if (!$userScoreObj  || $user_id == 0) {
                $sengineObj = new Sengine();
                //default total score is 1
                for ($i = 1; $i < 62; $i++) {
                    if ($i < 51 || $i == 58 || $i == 59) {
                        $callMethod = "setWfPoint" . $i;
                        $sengineObj->$callMethod();
                    }
                }
                $totalScore = $sengineObj->updateScore();
                $userScoreObj = new UserScore();
                $userScoreObj->user_id = $user_id;
                $userScoreObj->scoredetails = serialize($sengineObj);
                $userScoreObj->totalscore = $totalScore;
                if ($user_id > 0) {
                    $userScoreObj->save();
                }
            }
            $this->sengine = unserialize($userScoreObj->scoredetails);
            Yii::app()->session["sengine"] = $userScoreObj->scoredetails;
        }
        
        if($user_id > 0) {
            $montecarloprobability = Yii::app()->cache->get('montecarloprobability' . $user_id);
            if ($montecarloprobability === false) {
                $montecarloprobability = $this->sengine->monteCarlo;
                $monteCarloUser =  MonteCarloUser::model()->find("user_id=:user_id", array("user_id" => $user_id));
                if($monteCarloUser) {
                    $montecarloprobability = $monteCarloUser->montecarloprobability;            
                }
                Yii::app()->cache->set('montecarloprobability' . $user_id, $montecarloprobability);
            }
            if($montecarloprobability != $this->sengine->monteCarlo) {
                $this->sengine->monteCarlo = $montecarloprobability;
                $this->sengine->setMonteCarlo();
                Yii::app()->session["sengine"] = serialize($this->sengine);
            }
        }
    }

    function unsetEngine() {
        unset(Yii::app()->session["sengine"]);
    }

    /**
     * get the engine present in the
     * cache / session if present
     */
    function setSimulationEngine($user_id = 0, $overrideSession) {
        //get the score from cache
        // or table
        if (!$overrideSession && isset(Yii::app()->session["simulationengine"])) {
            $this->sengine = unserialize(Yii::app()->session["simulationengine"]);
        } else {
            $uscore = new UserScore();
            $checkqry = $uscore->findBySql("SELECT scoredetails FROM userscore WHERE user_id = :user_id", array("user_id" => $user_id));
            if ($checkqry) {
                $this->sengine = unserialize($checkqry->scoredetails);
                Yii::app()->session["simulationengine"] = $checkqry->scoredetails;
            } else {
                // Technically it would never reach this state because when a new user is created, a row in score engine is also generated via updatecomponents api
                $this->sengine = new Sengine();
                Yii::app()->session["simulationengine"] = serialize($this->sengine);
            }
        }
        
        if($user_id > 0) {
            $montecarloprobability = Yii::app()->cache->get('montecarloprobability' . $user_id);
            if ($montecarloprobability === false) {
                $montecarloprobability = $this->sengine->monteCarlo;
                $monteCarloUser =  MonteCarloUser::model()->find("user_id=:user_id", array("user_id" => $user_id));
                if($monteCarloUser) {
                    $montecarloprobability = $monteCarloUser->montecarloprobability;            
                }
                Yii::app()->cache->set('montecarloprobability' . $user_id, $montecarloprobability);
            }
            if($montecarloprobability != $this->sengine->monteCarlo) {
                $this->sengine->monteCarlo = $montecarloprobability;
                $this->sengine->setMonteCarlo();
                Yii::app()->session["simulationengine"] = serialize($this->sengine);
            }
        }
    }

    function saveEngine() {
        Yii::app()->session["sengine"] = serialize($this->sengine);
    }

    /**
     * The below function used to simulate the Score without being storing the data.
     *
     * @param type $section  can be one or more like ASSETS,DEBTS
     */
    function simulateCalculateScore($section, $user_id) {

        $varNames = explode(",", constant("self::" . $section));

        foreach ($varNames as $varName) {
            if (strtolower($varName) == "wfpoint29") {
                $this->CalculatePoint29();
            } else {
                $callMethod = "set" . $varName;
                $this->sengine->$callMethod();
            }
        }

        $totalScore = $this->sengine->updateScore();
        $totalScore = ($totalScore < 0) ? 0 : $totalScore;
        $totalScore = ($totalScore > 1000) ? 1000 : $totalScore;
        $this->simulatedPoint = $totalScore;
        return $this->simulatedPoint;
    }

    /**
     * if section changes it calls the associated function from the
     * score component to call the score engine
     *
     * @param type $section
     */
    function calculateScore($section, $user_id = 0) {

        if (isset(Yii::app()->session['wsuser'])) {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
        } else {
            $user_id = $user_id;
        }
        $sectionNames = explode("|", $section);
        $montecarlo = false;
        $combinedVarnames = "";
        $varNames = array();
        foreach ($sectionNames as $sectionName) {
            $combinedVarnames .= constant("self::" . $sectionName) . ",";
        }
        $combinedVarnames = trim($combinedVarnames, ",");
        $varNames = array_unique(explode(",", $combinedVarnames));
        //print_r($varNames);
        foreach ($varNames as $varName) {
            //call the set method for
            $this->sengine = unserialize(Yii::app()->session["sengine"]);
            if (strtolower($varName) == "wfpoint29") {
                $this->CalculatePoint29();
            } else {
                $callMethod = "set" . $varName;
                $this->sengine->$callMethod();
            }
            if (strtolower($varName) == "wfpoint12") {
                $montecarlo = true;
            }
            Yii::app()->session["sengine"] = serialize($this->sengine);
        }

        $totalScore = $this->sengine->updateScore();
        $totalScore = ($totalScore < 0) ? 0 : $totalScore;
        $totalScore = ($totalScore > 1000) ? 1000 : $totalScore;
        $this->totalScore = $totalScore;

        // Update the score in the table
        // $scoreScoreObj1 = new UserScore();
        $scoreScoreObj = UserScore::model()->find("user_id = :user_id", array("user_id" => $user_id));
        if (!$scoreScoreObj) {
            $scoreScoreObj = new UserScore();
        }
        $sengineSer = serialize($this->sengine);
        $scoreScoreObj->totalscore = round($this->totalScore);
        $scoreScoreObj->montecarloprobability = $this->sengine->monteCarlo;
        $scoreScoreObj->scoredetails = $sengineSer;
        $scoreScoreObj->timestamp = date("Y-m-d H:i:s");
        $scoreScoreObj->save();
        if ($montecarlo) {
            $monteCarloUser = MonteCarloUser::model()->find("user_id = :user_id", array("user_id" => $user_id));
            if (!$monteCarloUser) {
                $monteCarloUser = new MonteCarloUser();
                $monteCarloUser->user_id = $user_id;
            }
            $monteCarloUser->modifiedtimestamp = date("Y-m-d H:i:s");
            $monteCarloUser->save();
        }
        //FlexScore team code
        Yii::app()->cache->set('score' . $user_id, $scoreScoreObj->timestamp);
        unset($scoreScoreObj);
        Yii::app()->session["sengine"] = $sengineSer;
    }

    /**
     * setup default retirement goal if not added by user
     *
     */
    function setupDefaultRetirementGoal($user_id = 0) {
        if (isset(Yii::app()->session['wsuser'])) {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
        } else {
            $user_id = $user_id;
        }
        $userDetails = Userpersonalinfo::model()->find("user_id = :user_id", array("user_id" => $user_id));
        $retirementage = 65;
        $currentAge = 30;
        $retired = 0;
        $age = (date("Y") - 30) . "-01-01";

        if (isset($userDetails)) {
            if (isset($userDetails->age)) {
                $age = $userDetails->age;
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
            $age = implode('-', $ageArray);

            if(isset($userDetails->retirementage) && intval($userDetails->retirementage) > 0) {
                $retirementage = intval($userDetails->retirementage);
            }
            $userDoB = new DateTime($age);
            $currentD = new DateTime();
            $interval = $userDoB->diff($currentD);
            $currentAge = $interval->y;

            $retired = $userDetails->retirementstatus;
        }

        if ($retired == 0) {
            $goalsetting = Goal::model()->find("goaltype=:goaltype and user_id=:user_id", array("goaltype" => "RETIREMENT", "user_id" => $user_id));

            if (!$goalsetting) {
                $goalsetting = new Goal();
                $goalsetting->goalpriority = 0;
            }

            if ($goalsetting->goalpriority == 0) {
                $goalName = "Retirement Goal";
                $goalsetting->goalname = $goalName;
                //need for retirement
                $userIncome = Income::model()->find("user_id=:user_id", array("user_id" => $user_id));
                $rates = Sustainablerates::model()->findbySql("SELECT sustainablewithdrawalpercent FROM sustainablerates WHERE age=:age", array("age" => $retirementage));
                $sustainableRate = isset($rates->sustainablewithdrawalpercent) ? $rates->sustainablewithdrawalpercent : 4;

                $socialIncome = 0;
                $pensionIncome = 0;
                //check pension income in asset
                $assetSocial = Assets::model()->findBySql("select sum(balance) as socialsecurityTotal from assets where user_id=:user_id and type='SS' and status=0", array("user_id" => $user_id));
                $assetPension = Assets::model()->findBySql("select sum(balance) as pensionTotal from assets where user_id=:user_id and type='PENS' and status=0", array("user_id" => $user_id));
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

                if ($userIncome && $userIncome->totaluserincome > 0) {
                    $monthlyIncome = $userIncome->totaluserincome * 0.8;
                } else {
                    $userEstIncome = Estimation::model()->find("user_id=:user_id", array("user_id" => $user_id));
                    if ($userEstIncome && $userEstIncome->houseincome > 0) {
                        $monthlyIncome = $userEstIncome->houseincome * 0.8;
                    } else {
                        $monthlyIncome = 5000 * 0.8;
                    }
                }

                $totalUserIncome = $monthlyIncome * 12;
                $desiredIncome = ($totalUserIncome - $pensionIncome * 12 - $socialIncome * 12) / ($sustainableRate / 100);
                $goalenddate = new DateTime($age);
                $goalenddate->add(new DateInterval('P' . $retirementage . 'Y'));

                $goalEnd = "" . $goalenddate->format('Y-m-d H:i:s');


                $goalsetting->goaltype = "RETIREMENT";
                $goalsetting->goalpriority = 0;
                $goalsetting->goalamount = Utility::amountToDB($desiredIncome);
                if ($goalsetting->goalamount < 0) {
                    $goalsetting->goalamount = 0;
                }
                $goalsetting->goalstatus = 1;
                $goalsetting->goalenddate = $goalEnd;
                $goalsetting->user_id = $user_id;
                $goalsetting->monthlyincome = $monthlyIncome;
                $goalsetting->goalassumptions_1 = "3.4";

                if ($user_id > 0) {
                    $goalsetting->save();
                }
                $this->sengine = unserialize(Yii::app()->session["sengine"]);
                $this->sengine->retirementAmountDesired = $goalsetting->goalamount;
                Yii::app()->session["sengine"] = serialize($this->sengine);
            } else if ($goalsetting->goalpriority > 0) {

                // Age/SS/Pension => affects all retirement goals (default or user entered)
                //if user entered goal
                //need for retirement
                $userIncome = Income::model()->find("user_id=:user_id", array("user_id" => $user_id));
                $rates = Sustainablerates::model()->findbySql("SELECT sustainablewithdrawalpercent FROM sustainablerates WHERE age=:age", array("age" => $retirementage));
                $sustainableRate = isset($rates->sustainablewithdrawalpercent) ? $rates->sustainablewithdrawalpercent : 4;

                $socialIncome = 0;
                $pensionIncome = 0;
                //check pension income in asset
                $assetSocial = Assets::model()->findBySql("select sum(balance) as socialsecurityTotal from assets where user_id=:user_id and type='SS' and status=0", array("user_id" => $user_id));
                $assetPension = Assets::model()->findBySql("select sum(balance) as pensionTotal from assets where user_id=:user_id and type='PENS' and status=0", array("user_id" => $user_id));
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

                $totalUserIncome = $goalsetting->monthlyincome * 12;
                $desiredIncome = ($totalUserIncome - $pensionIncome * 12 - $socialIncome * 12) / ($sustainableRate / 100);
                $goalenddate = new DateTime($age);
                $goalenddate->add(new DateInterval('P' . $retirementage . 'Y'));

                $goalEnd = "" . $goalenddate->format('Y-m-d H:i:s');


                $goalsetting->goaltype = "RETIREMENT";
                $goalsetting->goalamount = Utility::amountToDB($desiredIncome);
                if ($goalsetting->goalamount < 0) {
                    $goalsetting->goalamount = 0;
                }
                $goalsetting->goalenddate = $goalEnd;
                $goalsetting->goalstatus = 1;
                if ($user_id > 0) {
                    $goalsetting->save();
                }
                $this->sengine = unserialize(Yii::app()->session["sengine"]);
                $this->sengine->retirementAmountDesired = $goalsetting->goalamount;
                Yii::app()->session["sengine"] = serialize($this->sengine);
            }
        } else {
            //disable the retirement goal if user is retired
            $goalsetting = Goal::model()->find("goaltype=:goaltype and user_id=:user_id", array("goaltype" => "RETIREMENT", "user_id" => $user_id));

            if ($goalsetting && $user_id > 0) {
                $goalsetting->goalstatus = 0;
                $goalsetting->save();
            }
        }
    }

    /**
     * Get the user tickers in comma seperated value to the UI
     *
     */
    function getUserAssetTickers($user_id = 0) {
        if (isset(Yii::app()->session['wsuser'])) {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
        } else {
            $user_id = $user_id;
        }
        $tickers = "";
        $assets = Assets::model()->findAll("user_id=:user_id and invpos!='' and status=0", array("user_id" => $user_id));
        if ($assets) {
            foreach ($assets as $asse) {
                //check if ticker has no coreleated alternatives
                if ($asse->invpos) {
                    $invPosArray = json_decode($asse->invpos);
                    if ($invPosArray && !empty($invPosArray)) {
                        foreach ($invPosArray as $invPos) {
                            if (isset($invPos->ticker) && $invPos->ticker != "") {
                                $invPos->ticker = str_replace("\\", "", $invPos->ticker);
                                //$tickers = $tickers . "'" . strtoupper($invPos->ticker) . "',";
                                $tickers = $tickers . "'" . strtoupper(addslashes($invPos->ticker)) . "',";
                            }
                        }
                    }
                }
            }
        }
        return rtrim($tickers, ',');
    }

    function getAssetData($user_id = 0) {
        $assetObj = new Assets();
        $insuranceObj = new Insurance();
        $pertrackObj = new Pertrack();
        $granularAssets = $assetObj->findAllBySql("select id,name,balance,invpos,type from assets where type not in ('SS','PENS','EDUC','VEHI') and user_id=:user_id and status=0 and livehere <> 1", array("user_id" => $user_id));
        $granularInsurance = $insuranceObj->findAllBySql("select id,name,cashvalue from insurance where type in ('LIFE') and user_id=:user_id and status=0 and lifeinstype <> 64", array("user_id" => $user_id));

        // Point 10
        $assets = 0;
        if ($granularAssets) {
            $assets = $granularAssets;
        }

        $insurance = 0;
        if ($granularInsurance) {
            $insurance = $granularInsurance;
        }

        $pertrackResults = null;
        $tickers = $this->getUserAssetTickers($user_id);
        $pertrack = 0;
        if ($tickers && $tickers != "") {
            $pertrackResults = $pertrackObj->findAllBySql("select itemtype, ticker from pertrac where ticker in ({$tickers}) and status=1");
            if ($pertrackResults) {
                $pertrack = $pertrackResults;
            }
        }
        unset($pertrackResults);

        $result = array();
        $result[0] = $assets;
        $result[1] = $insurance;
        $result[2] = $pertrack;
        $result[3] = $tickers;
        return $result;
    }

    function getDebtData($user_id = 0) {
        $debtObj = new Debts();
        $allDebts = $debtObj->findAll("user_id=:user_id AND type NOT IN ('MORT') AND status=0 AND monthly_payoff_balances=0", array("user_id" => $user_id));
        $restructuringDebtsArr = array();
        $personalDebtLoanArr = array();
        if ($allDebts) {
            $valueArr1 = array();
            $valueArr = array();
            foreach ($allDebts as $debts) {
                $valueObj = new stdClass();
                $valueObj->creditor = $debts["name"];
                $valueObj->balance = $debts["balowed"];
                $valueObj->minimumPayment = ($debts["type"] == 'CC') ? ($debts["balowed"] * 0.02) : $debts["amtpermonth"];
                $valueObj->actualPayment = $debts["amtpermonth"];

                $valueObj->rate = $debts["apr"] / 100;
                $valueObj->type = $debts["type"];
                $valueObj->subtype = $debts["mortgagetype"];
                $valueArr[] = $valueObj;

                $valueObj1 = new stdClass();
                $valueObj1->debtName = $debts["name"];
                $valueObj1->balance = $debts["balowed"];
                $valueObj1->minimum = ($debts["type"] == 'CC') ? ($debts["balowed"] * 0.02) : $debts["amtpermonth"];
                $valueObj1->payment = $debts["amtpermonth"];
                $valueObj1->rate = $debts["apr"] / 100;
                $valueObj1->type = $debts["type"];
                $valueObj1->subtype = $debts["mortgagetype"];
                $valueArr1[] = $valueObj1;
            }
            $restructuringDebtsArr = $valueArr1;
            $personalDebtLoanArr = $valueArr;
        }
        $result = array();
        $result[0] = $restructuringDebtsArr;
        $result[1] = $personalDebtLoanArr;
        return $result;
    }

    function UpdateLearningActionStep($user_id, $mediaCount) {
        // Create Action step : Read Learning Center articles at 5 points each (#90)
        $obj = new ActionstepController(1); // preparing object
        $actionsteps = Actionstep::model()->findAll("user_id=:user_id AND actionstatus IN ('0','2','3') order by points desc", array("user_id" => $user_id));

        //get all the articles read in the last 90 days
        $todayDate = new DateTime();
        $lapseDate = $todayDate->sub(new DateInterval('P90D'));
        $articlesRead = UserMedia::model()->findAllBySql("select media_id from usermedia
                where modified > :lapseDate and user_id=:user_id", array("lapseDate" => $lapseDate->format('Y-m-d g:i:s'), "user_id" => $user_id));

        $articleCount = 10;
        if(isset($articlesRead) && !empty($articlesRead)) {
            $articleCount = 10 - count($articlesRead);
        }
        $currentArticleCount = 0;
        $articleNames = array();
        $articleIds = array();

        // loop through the actionsteps taking one article from each actionstep until number of articles needed is reached.
        $actionStepKey = 0;
        $articleNum = 1;
        if ($actionsteps) {
            foreach ($actionsteps as $actionVals) {
                foreach ($actionsteps as $vals) {
                    $getval = $obj->getActionstep($vals->actionid);
                    if ($getval->articles <> '' && $getval->link <> 'learnmore' && $getval->linktype <> 'video') {
                        $narray = explode('|', $getval->articles);
                        foreach ($narray as $k => $nval) {
                            $artdiv = explode('#', $nval);
                            $hasArticle = false;
                            foreach ($articlesRead as $artKey => $article) {
                                if ($artdiv[2] == $article->media_id) {
                                    $hasArticle = true;
                                }
                            }
                            if (!$hasArticle && $actionStepKey == $k && !isset($articleIds[$artdiv[2]])) {
                                $articleIds[$artdiv[2]] = true;
                                $articleNames[] = '<a href="' . $artdiv[1] . '" id="' . $artdiv[2] . '" name="' . $this->learningArticles .
                                        '"target="_blank" class="articlelink">' . $articleNum . '. ' . $artdiv[0] . '</a>';
                                $currentArticleCount++;
                                $articleNum++;
                            }
                        }
                    }
                    if ($articleCount <= $currentArticleCount) {
                        break;
                    }
                }
                if ($articleCount <= $currentArticleCount) {
                    break;
                }
                $actionStepKey++;
            }

            if ($articleNames) {
                $artnames = implode(' <br>', $articleNames);
                $obj->SaveASteps($user_id, $this->learningArticles, '', '<br>' . $artnames . '<br>');
            }
        }
    }

    function CalculatePoint5($restructuringDebtsArr, $personalDebtLoanArr) {
        $calcXMLObj = Yii::app()->calcxml;
        # CALC XML - Restructuring debts
        $calculatePoint1 = true;
        foreach ($restructuringDebtsArr as $each) {
            if (!isset($each->rate) || $each->rate <= 0) {
                $calculatePoint1 = false;
                break;
            }
        }

        if ($calculatePoint1) {
            $currentPayoffMonths = $calcXMLObj->restructuringDebtsAcceleratedPayoffHelper($restructuringDebtsArr);
            $goalEndD = new DateTime();
            $years = floor($currentPayoffMonths / 12);
            $months = $currentPayoffMonths % 12;
            $goalEndDN = $goalEndD->add(new DateInterval('P' . $years . 'Y' . $months . 'M'));

            if (strpos($this->sengine->maxGoalEndYear, "-") === false) {
                $goalEndU = new DateTime();
                $goalEndU = $goalEndU->add(new DateInterval('P' . $this->sengine->maxGoalEndYear . 'Y'));
            } else {
                $goalEndU = new DateTime($this->sengine->maxGoalEndYear);
            }

            if ($goalEndU < $goalEndDN) {
                $point1 = 0;
            } else {
                $point1 = 12;
            }
        } else {
            $point1 = 0;
        }

        # Calc XML - Should I consolidate my personal debt into a new loan?
        $calculatePoint2 = true;
        foreach ($personalDebtLoanArr as $each) {
            if (!isset($each->rate) || $each->rate <= 0) {
                $calculatePoint2 = false;
                break;
            }
        }

        if ($calculatePoint2) {
            $recommendTerm = $calcXMLObj->personalDebtLoanHelper($personalDebtLoanArr);

            if ($recommendTerm) {
                //not consolidate
                $point2 = 13;
            } else {
                //consolidate
                $point2 = 0;
            }
        } else {
            $point2 = 0;
        }
        unset($calcXMLObj);
        $this->setEngine();        
        $this->sengine->wfPoint5 = $point1 + $point2;
        $this->saveEngine();
    }

    /**
     * (10) + 100 (Funding the Nest)
     * Denominator = Sum up all Assets = Overall Wealth
     * Numerator = Sum up any asset, at the granular level of each stock,
     *      or each mutual fund, etc. that is < 10% of overall wealth
     * Include Insurance Cash Value in this calculation.
     * Do not include educational, vehicle, pension, and social security amounts
     *      in the denominator or numerator.
     * Final Score: 100 * (Numerator / Denominator)
     */
    function CalculatePoint10($assetsObj, $insuranceObj, $pertrackObj) {
        $denominator = 0;
        // Calculate denominator
        if ($assetsObj) {
            foreach ($assetsObj as $each) {
                if ($each->invpos) {
                    $invPosArray = json_decode($each->invpos);
                    if ($invPosArray && !empty($invPosArray)) {
                        $total = 0;
                        foreach ($invPosArray as $invPos) {
                            $total += ($invPos->amount) ? $invPos->amount : 0;
                        }
                        $denominator += ($each->balance && $each->balance > $total) ? $each->balance : $total;
                    } else if ($each->balance) {
                        $denominator += $each->balance;
                    }
                } else if ($each->balance) {
                    $denominator += $each->balance;
                }
            }
        }
        if ($insuranceObj) {
            foreach ($insuranceObj as $each) {
                if ($each->cashvalue) {
                    $denominator += $each->cashvalue;
                }
            }
        }

        $percentAssets = round($denominator / 10);
        $granularLevel = 0;
        $excessAssets = array();
        // Calculate numerator if denominator > 0
        if ($denominator > 0 && $assetsObj) {
            foreach ($assetsObj as $each) {
                if ($each->invpos) {
                    $invPosArray = json_decode($each->invpos);
                    if ($invPosArray && !empty($invPosArray)) {
                        $total = 0;
                        foreach ($invPosArray as $invPos) {
                            if ($invPos->amount && $invPos->amount < $percentAssets) {
                                $granularLevel += $invPos->amount;
                            } else if ($pertrackObj) {
                                $found = false;
                                foreach ($pertrackObj as $pertrack) {
                                    if ($pertrack->ticker == strtoupper($invPos->ticker)) {
                                        $found = true;
                                        if ($pertrack->itemtype == 'MF' || $pertrack->itemtype == 'ETF') {
                                            $granularLevel += $invPos->amount;
                                        }
                                        else
                                        {
                                            $excessAsset = array();
                                            $excessAsset["id"] = $each->id;
                                            $excessAsset["ticker"] = strtoupper($invPos->ticker);
                                            $excessAssets[] = $excessAsset;
                                        }
                                        break;
                                    }
                                }
                                if(!$found) {
                                    $excessAsset = array();
                                    $excessAsset["id"] = $each->id;
                                    $excessAsset["ticker"] = strtoupper($invPos->ticker);
                                    $excessAssets[] = $excessAsset;
                                }
                            }
                            else
                            {
                                $excessAsset = array();
                                $excessAsset["id"] = $each->id;
                                $excessAsset["ticker"] = strtoupper($invPos->ticker);
                                $excessAssets[] = $excessAsset;
                            }
                            $total += ($invPos->amount) ? $invPos->amount : 0;
                        }
                        $cashvalue = ($each->balance && $each->balance > $total) ? ($each->balance - $total) : 0;
                        $granularLevel += ($cashvalue < $percentAssets) ? $cashvalue : 0;
                    } else if ($each->balance && ($each->balance < $percentAssets || $each->type == 'BANK')) {
                        $granularLevel += $each->balance;
                    }
                    else
                    {
                        $excessAsset = array();
                        $excessAsset["id"] = $each->id;
                        $excessAssets[] = $excessAsset;
                    }
                } else if ($each->balance && ($each->balance < $percentAssets || $each->type == 'BANK')) {
                    $granularLevel += $each->balance;
                }
                else
                {
                    $excessAsset = array();
                    $excessAsset["id"] = $each->id;
                    $excessAssets[] = $excessAsset;
                }
            }
        }

        if ($denominator > 0 && $insuranceObj) {
            foreach ($insuranceObj as $each) {
                if ($each->cashvalue && $each->cashvalue < $percentAssets) {
                    $granularLevel += $each->cashvalue;
                }
                else
                {
                    $excessAsset = array();
                    $excessAsset["id"] = $each->id;
                    $excessAssets[] = $excessAsset;
                }
            }
        }

        if(isset($this->sengine)) {
            $this->setEngine();        
            $this->sengine->wfPoint10 = ($denominator > 0) ? (50 * ($granularLevel / $denominator)) : 0;
            $this->saveEngine();
        }
        return $excessAssets;
    }

    function CalculatePoint29() {
        $calcXMLObj = Yii::app()->calcxml;
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

        $outputCalc = $calcXMLObj->lifeInsuranceINeedHelper($valueObj);

        $this->setEngine();        
        if ($outputCalc != -1) {
            $this->sengine->insuranceNeededActionStep = $outputCalc;
            $denominator = $this->sengine->LifeInsurance + $outputCalc;
            $this->sengine->wfPoint29 = ($denominator > 0) ? (24 * ($this->sengine->LifeInsurance / $denominator)) : 24;
        }

        // unset($outputCalc);
        if ($this->sengine->wfPoint29 > 24) {
            $this->sengine->wfPoint29 = 24;
        } elseif ($this->sengine->wfPoint29 <= 0) {
            $this->sengine->wfPoint29 = 0;
        }

        // Degrade points
        if ($this->sengine->insuranceReviewYear29 > 0) {
            $currentYear = date('Y');
            $yearDifference = $currentYear - $this->sengine->insuranceReviewYear29;

            if ($yearDifference > 4 && $this->sengine->wfPoint29 > -24) {
                $this->sengine->wfPoint29 = -24;
            } elseif ($yearDifference > 3 && $yearDifference <= 4 && $this->sengine->wfPoint29 > -12) {
                $this->sengine->wfPoint29 = -12;
            } elseif ($yearDifference > 2 && $yearDifference <= 3 && $this->sengine->wfPoint29 > 0) {
                $this->sengine->wfPoint29 = 0;
            } elseif ($yearDifference > 1 && $yearDifference <= 2 && $this->sengine->wfPoint29 > 12) {
                $this->sengine->wfPoint29 = 12;
            } elseif ($yearDifference <= 1 && $this->sengine->wfPoint29 > 24) {
                $this->sengine->wfPoint29 = 24;
            }
        }
        $this->saveEngine();
        unset($calcXMLObj);
    }

}
