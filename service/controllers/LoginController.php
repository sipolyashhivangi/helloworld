<?php

/* * ********************************************************************
 * Filename: LoginController.php
 * Folder: controllers
 * Description: login to score engine
 * @author Subramanya H S (For TruGlobal Inc)
 * @copyright (c) 2013 - 2015
 * Change History:
 * Updated on: 06-02-2014 By Rajeev Ranjan (For TruGlobal Inc)
 * Version         Author               Change Description
 * ******************************************************************** */

class LoginController extends Controller {

    public $peerdefaultAge = 30;

    // Define access control
    public function accessRules() {
        return array_merge(
                array(array('allow', 'users' => array('?'))),
                // Include parent access rules
                parent::accessRules()
        );
    }

    /**
     * get score
     *
     */
    public function actionGetscore() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) && !isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        if (isset(Yii::app()->getSession()->get('wsadvisor')->id) && isset($_GET['user_id'])) {
            $user_id = $_GET['user_id']; // if request comes from advisor
        } else {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
        }
        //get the user  last user score from the table and store in
        // session
        $userScoreObj = new UserScore();
        $scoreRecord = $userScoreObj->find("user_id=:user_id", array("user_id" => $user_id));

        // create object of scorechange model //
        $scoreChangeObj = new ScoreChange();
        $scoreChangeRecord = $scoreChangeObj->find("user_id=:user_id", array("user_id" => $user_id));

        if (isset($scoreChangeRecord) && $scoreChangeRecord["scorechange"]!="") {
            $sparsedScoreObjArray = json_decode($scoreChangeRecord["scorechange"], true);
            $first = strtotime(date(key($sparsedScoreObjArray)));
            $current = strtotime(date("Y-m-d"));

            $resultScoreArray = array();
            while ($first <= $current) {
                $dateKey = date("Y-m-d", $first);
                if (isset($sparsedScoreObjArray["$dateKey"])) {
                    $resultScoreArray["$dateKey"] = $sparsedScoreObjArray["$dateKey"];
                } else {
                    $resultScoreValues = array_values($resultScoreArray);
                    $lastScoreDetails = end($resultScoreValues);
                    $resultScoreArray["$dateKey"] = array('Total' => $lastScoreDetails['Total'], 'Change' => 0, 'Reason' => '');
                }
                $first = strtotime('+1 day', $first);
            }
        }
        //

        $scoreChangeVal = new stdClass();
        $scoreChangeVal->sevenDC = "NA";
        $scoreChangeVal->fifteenDC = "NA";
        $scoreChangeVal->oneMC = "NA";

        $graphDataArray = array();

        if (isset($scoreRecord) && !empty($scoreRecord)) {

            $totalScore = $scoreRecord["totalscore"];
            $scorechange = $scoreChangeRecord["scorechange"];

            $sengineObj = unserialize($scoreRecord["scoredetails"]);

            $montecarloprobability = Yii::app()->cache->get('montecarloprobability' . $user_id);
            if ($montecarloprobability === false) {
                $montecarloprobability = $sengineObj->monteCarlo;
                $monteCarloUser =  MonteCarloUser::model()->find("user_id=:user_id", array("user_id" => $user_id));
                if($monteCarloUser) {
                    $montecarloprobability = $monteCarloUser->montecarloprobability;            
                }
                Yii::app()->cache->set('montecarloprobability' . $user_id, $montecarloprobability);
            }

            if($montecarloprobability != $sengineObj->monteCarlo) {
                $sengineObj->monteCarlo = $montecarloprobability;
                $sengineObj->setMonteCarlo();
                $totalScore = $sengineObj->updateScore();
            }


            //store the object in session
            //
            if (!isset(Yii::app()->session["sengine"])) {            
                Yii::app()->session["sengine"] = serialize($sengineObj);
            }

            if (isset($scorechange) && $scorechange != "") { // It should be available as the cronjob is set.If empty, check cronlog
                $cnt = count($resultScoreArray);

                //get the 7 day change
                if ($cnt >= 7) {
                    $min = date('Y-m-d', strtotime("-6 days"));
                    $scoreChangeVal->sevenDC = $totalScore - $resultScoreArray[$min]["Total"];
                    // add notation and pts string
                    if ($scoreChangeVal->sevenDC > 0) {
                        $scoreChangeVal->sevenDC = "+" . $scoreChangeVal->sevenDC . " pts";
                    }
                    if ($scoreChangeVal->sevenDC < 0) {
                        $scoreChangeVal->sevenDC = $scoreChangeVal->sevenDC . " pts";
                    }
                }

                // get the fifteen day changes //
                if ($cnt >= 15) {
                    $min = date('Y-m-d', strtotime("-14 days"));
                    $scoreChangeVal->fifteenDC = $totalScore - $resultScoreArray[$min]["Total"];
                    // add notation and pts string
                    if ($scoreChangeVal->fifteenDC > 0) {
                        $scoreChangeVal->fifteenDC = "+" . $scoreChangeVal->fifteenDC . " pts";
                    }
                    if ($scoreChangeVal->fifteenDC < 0) {
                        $scoreChangeVal->fifteenDC = $scoreChangeVal->fifteenDC . " pts";
                    }
                }

                // get the thirty day changes //

                if ($cnt >= 30) {
                    $min = date('Y-m-d', strtotime("-29 days"));
                    $scoreChangeVal->oneMC = $totalScore - $resultScoreArray[$min]["Total"];
                    if ($scoreChangeVal->oneMC > 0) {
                        $scoreChangeVal->oneMC = "+" . $scoreChangeVal->oneMC . " pts";
                    }
                    if ($scoreChangeVal->oneMC < 0) {
                        $scoreChangeVal->oneMC = $scoreChangeVal->oneMC . " pts";
                    }
                }

                if (isset($_GET["range"]) && $_GET["range"] != "undefined") {
                    $range = $_GET["range"];
                } else {
                    $range = "01";
                }

                // Milestone graph data calculation start//
                //for seven days data //
//                if ($cnt > 6 && $cnt < 30 || $range == "01") {
                if ($range == "01" && $cnt > 6) {

                    $index1 = 1;
                    for ($i = 6; $i > 0; $i--) {
                        $day = $i;
                        $graphDataArray[$index1]['FullDate'] = date('D M d Y H:i:s', strtotime("-$day days")) . " UTC";
                        $milestoneArraydate = date('Y-m-d', strtotime("-$day days"));

                        if (array_key_exists($milestoneArraydate, $resultScoreArray)) {
                            $graphDataArray[$index1]['TotalScore'] = $resultScoreArray[$milestoneArraydate]["Total"];
                            $previousValue = $resultScoreArray[$milestoneArraydate]["Total"];
                        } else {
                            $graphDataArray[$index1]['TotalScore'] = $previousValue;
                        }
                        $graphDataArray[$index1]['DataDate'] = $milestoneArraydate;
                        $index1++;
                    }
                    $graphDataArray[7]['FullDate'] = date('D M d Y H:i:s') . " UTC";
                    $graphDataArray[7]['TotalScore'] = $totalScore;
                    $graphDataArray[7]['DataDate'] = date('D M d Y H:i:s') . " UTC";
                }

                //print_r($graphDataArray);die;
                //for one month data . We might need to handle leap year//
//                if ($cnt >= 30 && $cnt < 90 && $sevenDaysFlag!=1) {
                if ($range == "02" && $cnt >= 30) {

                    $index1 = 1;
                    for ($i = 3; $i > 0; $i--) {
                        $day = $i * 7;

                        $graphDataArray[$index1]['FullDate'] = date('D M d Y H:i:s', strtotime("-$day days")) . " UTC";
                        $milestoneArraydate = date('Y-m-d', strtotime("-$day days"));
                        if($i==3){
                            $day = 29; //we are taking value of 30th day from current technically for 1st data point
                            $milestoneArraydate = date('Y-m-d', strtotime("-$day days"));
                            $graphDataArray[$index1]['TotalScore'] = $resultScoreArray[$milestoneArraydate]["Total"];
                        }else if($i==1){
                            $day = 6; //we are taking value of 30th day from current technically for 1st data point
                            $milestoneArraydate = date('Y-m-d', strtotime("-$day days"));
                            $graphDataArray[$index1]['TotalScore'] = $resultScoreArray[$milestoneArraydate]["Total"];
                        }else{
                            $milestoneArraydate = date('Y-m-d', strtotime("-$day days"));
                            $graphDataArray[$index1]['TotalScore'] = $resultScoreArray[$milestoneArraydate]["Total"];
                        }
                        $graphDataArray[$index1]['DataDate'] = $milestoneArraydate;
                        $index1++;
                    }
                    $graphDataArray[4]['FullDate'] = date('D M d Y H:i:s') . " UTC";
                    $graphDataArray[4]['TotalScore'] = $totalScore;
                    $graphDataArray[4]['DataDate'] = date('D M d Y H:i:s') . " UTC";
                }


                //for three months data //
//                if ($cnt >= 90 && $cnt < 180) {
                if ($range == "03" && $cnt >= 90) {

                    $index1 = 1;
                    for ($i = 5; $i > 0; $i--) {
                        $day = ($i * 15)-1;
                        $graphDataArray[$index1]['FullDate'] = date('D M d Y H:i:s', strtotime("-$day days")) . " UTC";
                        $milestoneArraydate = date('Y-m-d', strtotime("-$day days"));
                        if($i==5){
                            $day = 89; //we are taking value of 30th day from current technically for 1st data point
                            $milestoneArraydate = date('Y-m-d', strtotime("-$day days"));
                            $graphDataArray[$index1]['TotalScore'] = $resultScoreArray[$milestoneArraydate]["Total"];
                        }else{
                            $milestoneArraydate = date('Y-m-d', strtotime("-$day days"));
                            $graphDataArray[$index1]['TotalScore'] = $resultScoreArray[$milestoneArraydate]["Total"];
                        }
                        $graphDataArray[$index1]['DataDate'] = $milestoneArraydate;
                        $index1++;
                    }
                    $graphDataArray[6]['FullDate'] = date('D M d Y H:i:s') . " UTC";
                    $graphDataArray[6]['TotalScore'] = $totalScore;
                    $graphDataArray[6]['DataDate'] = date('D M d Y H:i:s') . " UTC";

                    //echo "<pre>";
                    //print_r($graphDataArray);
                }

                // for six months data//
//                if ($cnt > 179) {
                // for six months data//
                if ($range == "04" && $cnt > 179) {

                    $index1 = 1;
                    for ($i = 5; $i > 0; $i--) {
                        $day = $i * 30;

                        $graphDataArray[$index1]['FullDate'] = date('D M d Y H:i:s', strtotime("-$i month")) . " UTC";
                        $milestoneArraydate = date('Y-m-d', strtotime("-$i month"));
                        if($i==1){
                            $day = 29; //we are taking value of 30th day from current technically for 1st data point
                            $milestoneArraydate = date('Y-m-d', strtotime("-$day days"));
                            $graphDataArray[$index1]['TotalScore'] = $resultScoreArray[$milestoneArraydate]["Total"];
                        }else if($i==3){
                            $day = 89; //we are taking value of 30th day from current technically for 1st data point
                            $milestoneArraydate = date('Y-m-d', strtotime("-$day days"));
                            $graphDataArray[$index1]['TotalScore'] = $resultScoreArray[$milestoneArraydate]["Total"];
                        }else{
                            $milestoneArraydate = date('Y-m-d', strtotime("-$day days"));
                            $graphDataArray[$index1]['TotalScore'] = $resultScoreArray[$milestoneArraydate]["Total"];
                        }
                        $graphDataArray[$index1]['DataDate'] = $milestoneArraydate;
                        $index1++;
                    }
                    $graphDataArray[6]['FullDate'] = date('D M d Y H:i:s') . " UTC";
                    $graphDataArray[6]['TotalScore'] = $totalScore;
                    $graphDataArray[6]['DataDate'] = date('D M d Y H:i:s') . " UTC";
                }
            }
        } else {
            //need to create score engine component and update the score and store the score inside the table
            $sengineObj = new Sengine();

            // Fetch Score for National
            $age = $this->peerdefaultAge;
            $peerscoreData = Peerranking::model()->find(array('condition' => "baseage = :baseage", 'params' => array("baseage" => $age), 'select' => 'weightage1'));
            if (isset($peerscoreData->weightage1)) {
                $nationalpeerrank = $peerscoreData->weightage1;
                $localpeerrank = $peerscoreData->weightage1;
            }

            $sengineObj->localPeerRank = $localpeerrank;
            $sengineObj->nationalPeerRank = $nationalpeerrank;

            //default total score is 1
            for ($i = 1; $i < 62; $i++) {
                if ($i < 51 || $i == 58 || $i == 59) {
                    $callMethod = "setWfPoint" . $i;
                    $sengineObj->$callMethod();
                }
            }
            $totalScore = $sengineObj->updateScore();
            $totalScore = ($totalScore < 0) ? 0 : $totalScore;
            $totalScore = ($totalScore > 1000) ? 1000 : $totalScore;
            $userScoreObj->user_id = $user_id;
            $userScoreObj->scoredetails = serialize($sengineObj);
            $userScoreObj->totalscore = $totalScore;
            $userScoreObj->save();
            Yii::app()->session["sengine"] = $userScoreObj->scoredetails;
        }

        $totalScore = ($totalScore < 0) ? 0 : $totalScore;
        $totalScore = ($totalScore > 1000) ? 1000 : $totalScore;
        $imageNo = round(($totalScore * 20) / 1000);
        $imageRender = "MyScoreHorseShoe" . $imageNo;

        $userPerDetails = Userpersonalinfo::model()->find("user_id = :user_id", array(':user_id' => $user_id));
        $retage = 65;
        if ($userPerDetails && isset($userPerDetails->retirementage) && $userPerDetails->retirementage > 0) {
            $retage = $userPerDetails->retirementage;
        }

        //create array of points to send to UI
        $pointsAndTotalArr = array(
            "totalscore" => $totalScore,
            "image" => $imageRender,
            "sevenDC" => $scoreChangeVal->sevenDC,
            "fifteenDC" => $scoreChangeVal->fifteenDC,
            "oneMC" => $scoreChangeVal->oneMC,
            "point1" => $sengineObj->wfPoint1,
            "point2" => $sengineObj->wfPoint2,
            "point3" => $sengineObj->wfPoint3,
            "point4" => $sengineObj->wfPoint4,
            "point5" => $sengineObj->wfPoint5,
            "point6" => $sengineObj->wfPoint6,
            "point7" => $sengineObj->wfPoint7,
            "point8" => $sengineObj->wfPoint8,
            "point9" => $sengineObj->wfPoint9,
            "point10" => $sengineObj->wfPoint10,
            "point11" => $sengineObj->wfPoint11,
            "point12" => $sengineObj->wfPoint12,
            "point13" => $sengineObj->wfPoint13,
            "point14" => $sengineObj->wfPoint14,
            "point15" => $sengineObj->wfPoint15,
            "point16" => $sengineObj->wfPoint16,
            "point17" => $sengineObj->wfPoint17,
            "point18" => $sengineObj->wfPoint18,
            "point19" => $sengineObj->wfPoint19,
            'point20' => $sengineObj->wfPoint20,
            'point21' => $sengineObj->wfPoint21,
            'point22' => $sengineObj->wfPoint22,
            'point23' => $sengineObj->wfPoint23,
            'point24' => $sengineObj->wfPoint24,
            'point25' => $sengineObj->wfPoint25,
            'point26' => $sengineObj->wfPoint26,
            'point27' => $sengineObj->wfPoint27,
            'point28' => $sengineObj->wfPoint28,
            'point29' => $sengineObj->wfPoint29,
            'point30' => $sengineObj->wfPoint30,
            'point31' => $sengineObj->wfPoint31,
            'point32' => $sengineObj->wfPoint32,
            'point33' => $sengineObj->wfPoint33,
            'point34' => $sengineObj->wfPoint34,
            'point35' => $sengineObj->wfPoint35,
            'point36' => $sengineObj->wfPoint36,
            'point37' => $sengineObj->wfPoint37,
            'point38' => $sengineObj->wfPoint38,
            'point39' => $sengineObj->wfPoint39,
            'point40' => $sengineObj->wfPoint40,
            'point41' => $sengineObj->wfPoint41,
            'point42' => $sengineObj->wfPoint42,
            'point43' => $sengineObj->wfPoint43,
            'point44' => $sengineObj->wfPoint44,
            'point45' => $sengineObj->wfPoint45,
            'point46' => $sengineObj->wfPoint46,
            'point47' => $sengineObj->wfPoint47,
            'point48' => $sengineObj->wfPoint48,
            'point49' => $sengineObj->wfPoint49,
            'point50' => $sengineObj->wfPoint50,
            'point58' => $sengineObj->wfPoint58,
            'point59' => $sengineObj->wfPoint59,
            'montecarlo' => $sengineObj->monteCarlo,
            'recomendedrisk' => $sengineObj->recomendedRisk,
            'currentrisk' => $sengineObj->currentRisk,
            'currentage' => $sengineObj->userCurrentAge,
            'ActionstepInterest needed' => $sengineObj->insuranceNeededActionStep,
            'localpeerrank' => $sengineObj->localPeerRank,
            'nationalpeerrank' => $sengineObj->nationalPeerRank,
            'income' => $sengineObj->userIncomePerMonth,
            'livingCosts' => $sengineObj->userExpensePerMonth,
            'assetsTotal' => $sengineObj->userSumOfAssets + $sengineObj->userSumOfOtherAssets,
            'debtsTotal' => $sengineObj->userSumOfDebts,
            'savingsTotal' => ($sengineObj->taxableAnnualSavings + $sengineObj->taxDeferredAnnualSavings + $sengineObj->taxFreeAnnualSavings) / 12,
            'age' => $sengineObj->userCurrentAge,
            'retage' => $retage,
        );

        $userPerObj = Userpersonalinfo::model()->findByPk($user_id);
        if ($userPerObj) {
            $pointsAndTotalArr['retirementstatus'] = $userPerObj->retirementstatus;
            $pointsAndTotalArr['risk'] = $userPerObj->risk;
        }
        if (!isset($pointsAndTotalArr['retirementstatus'])) {
            $pointsAndTotalArr['retirementstatus'] = 0;
        }
        if (!isset($pointsAndTotalArr['risk'])) {
            $pointsAndTotalArr['risk'] = 5;
        }
        $assetCount = Assets::model()->count(array('condition' => 'user_id = :user_id AND context = "AUTO" and status = 0',
            'params' => array('user_id' => $user_id)));
        $debtCount = Debts::model()->count(array('condition' => 'user_id = :user_id AND context = "AUTO" and status = 0',
            'params' => array('user_id' => $user_id)));
        $pointsAndTotalArr['linkedcount'] = $assetCount + $debtCount;

        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "score" => $pointsAndTotalArr, "scoreChangeVal" => $scoreChangeVal, "mileStoneGraphData" => $graphDataArray)));
    }

    /**
     * get networth score
     *
     */
    public function actionNetworthscore() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        try {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
        } catch (Exception $e) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Could not read networth a this time.")));
            exit;
        }

        // create object of scorechange model //
        $netWorthObj = new Networth();
        $netWorthRecord = $netWorthObj->find("user_id=:user_id", array("user_id" => $user_id));
        //
        $SControllerObj = new Scontroller(1);
        $SControllerObj->setEngine();
        $sengineObj = $SControllerObj->sengine;
        $sumOfAssets = $sengineObj->userSumOfAssets + $sengineObj->userSumOfOtherAssets;
        $currentNetWorth = ($sumOfAssets - $sengineObj->userSumOfDebts);

        $graphDataArray = array();
        $previousValue = 0;
        $netWorthScore = $netWorthRecord["val"];

        if (isset($netWorthScore) && $netWorthScore != "") { // It should be available as the cronjob is set.If empty, check cronlog
            $netWorthScore = json_decode($netWorthScore);
            $cnt = count(get_object_vars($netWorthScore));
            $search_array = get_object_vars($netWorthScore);

            if (isset($_GET["range"]) && $_GET["range"] != "undefined") {
                $range = $_GET["range"];
            } else {
                $range = "01";
            }

            // Networth graph data calculation start//
            //for seven days data //

            if (($range == "01" || $range == "all") && $cnt > 6) {
                $graphDataArray = array();
                $previousValue = 0;

                $index1 = 1;
                for ($i = 6; $i > 0; $i--) {
                    $day = $i;
                    $graphDataArray[$index1]['FullDate'] = date('D M d Y H:i:s', strtotime("-$day days")) . " UTC";
                    $netWorthArraydate = date('Y-m-d', strtotime("-$day days"));
                    if (array_key_exists($netWorthArraydate, $search_array)) {
                        $graphDataArray[$index1]['TotalScore'] = $netWorthScore->$netWorthArraydate;
                        $previousValue = $netWorthScore->$netWorthArraydate;
                    } else {
                        $graphDataArray[$index1]['TotalScore'] = $previousValue;
                    }
                    $index1++;
                }
                $graphDataArray[7]['FullDate'] = date('D M d Y H:i:s') . " UTC";
                $graphDataArray[7]['TotalScore'] = $currentNetWorth;
            }

            //for one month data . We might need to handle leap year//

            if (($range == "02" || $range == "all") && $cnt >= 30) {
                $graphDataArray = array();
                $previousValue = 0;

                $index1 = 1;
                for ($i = 3; $i > 0; $i--) {
                    $day = $i * 7;
                    $graphDataArray[$index1]['FullDate'] = date('D M d Y H:i:s', strtotime("-$day days")) . " UTC";
                    $netWorthArraydate = date('Y-m-d', strtotime("-$day days"));
                    if (array_key_exists($netWorthArraydate, $search_array)) {
                        $graphDataArray[$index1]['TotalScore'] = $netWorthScore->$netWorthArraydate;
                        $previousValue = $netWorthScore->$netWorthArraydate;
                    } else {
                        $graphDataArray[$index1]['TotalScore'] = $previousValue;
                    }
                    $index1++;
                }
                $graphDataArray[4]['FullDate'] = date('D M d Y H:i:s') . " UTC";
                $graphDataArray[4]['TotalScore'] = $currentNetWorth;
            }


            //for three months data //
            if (($range == "03" || $range == "all") && $cnt >= 90) {
                $graphDataArray = array();
                $previousValue = 0;

                $index1 = 1;
                for ($i = 5; $i > 0; $i--) {
                    $day = ($i * 15)+1;
                    $graphDataArray[$index1]['FullDate'] = date('D M d Y H:i:s', strtotime("-$day days")) . " UTC";
                    $netWorthArraydate = date('Y-m-d', strtotime("-$day days"));
                    if (array_key_exists($netWorthArraydate, $search_array)) {
                        $graphDataArray[$index1]['TotalScore'] = $netWorthScore->$netWorthArraydate;
                        $previousValue = $netWorthScore->$netWorthArraydate;
                    } else {
                        $graphDataArray[$index1]['TotalScore'] = $previousValue;
                    }
                    $index1++;
                }
                $graphDataArray[6]['FullDate'] = date('D M d Y H:i:s') . " UTC";
                $graphDataArray[6]['TotalScore'] = $currentNetWorth;

                //echo "<pre>";
                //print_r($graphDataArray);
            }


            // for six months data//
            if (($range == "04" || $range == "all") && $cnt > 179) {
                $graphDataArray = array();
                $previousValue = 0;

                $index1 = 1;
                for ($i = 5; $i > 0; $i--) {
                    $day = $i * 30;

                    $graphDataArray[$index1]['FullDate'] = date('D M d Y H:i:s', strtotime("-$day days")) . " UTC";
                    $netWorthArraydate = date('Y-m-d', strtotime("-$day days"));
                    if (array_key_exists($netWorthArraydate, $search_array)) {
                        $graphDataArray[$index1]['TotalScore'] = $netWorthScore->$netWorthArraydate;
                        $previousValue = $netWorthScore->$netWorthArraydate;
                    } else {
                        $graphDataArray[$index1]['TotalScore'] = $previousValue;
                    }
                    $index1++;
                }
                $graphDataArray[6]['FullDate'] = date('D M d Y H:i:s') . " UTC";
                $graphDataArray[6]['TotalScore'] = $currentNetWorth;
            }

            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "networthGraphData" => $graphDataArray)));
        }
    }

}

?>