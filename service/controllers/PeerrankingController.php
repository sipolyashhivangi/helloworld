<?php

/* * ********************************************************************
 * Filename: PeerrankingController.php
 * Folder: controllers
 * Description: Peerranking controller (peer ranking)
 * @author Alex Thomas (For Truglobal Inc.)
 * @copyright (c) 2012 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class PeerrankingController extends Scontroller {

    public $sengine = null;
    public $peerminAge = 21;
    public $peermaxAge = 75;
    public $peerdefaultAge = 30;

    public function accessRules() {
        return array_merge(
                array(array('allow', 'users' => array('?'))),
                // Include parent access rules
                parent::accessRules()
        );
    }

    function actionGetpeerrank() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $localpeer = 0;
        $nationalpeer = 0;
        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        if (isset($user_id)) {
            parent::setEngine();
            $localpeer = $this->sengine->localPeerRank;
            $nationalpeer = $this->sengine->nationalPeerRank;
            $totalscore = $this->sengine->updateScore();

            $locimageNo = round(($localpeer * 20) / 1000);
            $locimageRender = "CompareHorseShoe" . $locimageNo;

            $natimageNo = round(($nationalpeer * 20) / 1000);
            $natimageRender = "CompareHorseShoe" . $natimageNo;

            $userData = User::model()->find(array('condition' => "id=:user_id", 'params' => array("user_id" => $user_id), 'select' => 'zip'));
            if (strlen($userData->zip) >= 5) {
                $szipCode = $userData->zip;
            } else {
                $szipCode = 0;
            }

            $peerArr = array('localpeer' => $localpeer, 'nationalpeer' => $nationalpeer, 'localimage' => $locimageRender, 'nationalimage' => $natimageRender, 'totalscore' => $totalscore, 'zip' => $szipCode);
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "peerval" => $peerArr)));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR")));
        }
    }

    function actionUpdatepeerrank() {
        $usrArr = array();
        $userObj = new User();
        $qu = new CDbCriteria();
        $qu->condition = "isactive = :isactive";
        $qu->params = array('isactive' => 1);
        $userDetails = $userObj->findAll($qu);
        if (isset($userDetails) && !empty($userDetails)) {
            foreach ($userDetails as $urow) {
                $uscore = new UserScore();
                $checkqry = $uscore->findBySql("SELECT scoredetails FROM userscore WHERE user_id = :user_id", array("user_id" => $urow->id));
                if ($checkqry) {
                    $details = $checkqry->scoredetails;
                    $sengineObj = unserialize($details);
                    $this->sengine = $sengineObj;
                    $age = $sengineObj->userCurrentAge;
                    $localpeerrank = $sengineObj->localPeerRank;
                    $nationalpeerrank = $sengineObj->nationalPeerRank;
                    if ($localpeerrank == 0 || $nationalpeerrank == 0) {
                        $usrArr[] = array('age' => $age, 'localpeer' => $localpeerrank, 'nationalpeer' => $nationalpeerrank);
                        if (isset($age)) {
                            if ($age <= $this->peerminAge) {
                                $age = $this->peerminAge;
                            }
                            if ($age >= $this->peermaxAge) {
                                $age = $this->peermaxAge;
                            }
                        } else {
                            $age = $this->peerdefaultAge;
                        }
                        // Fetch Score for National
                        $peerscoreData = Peerranking::model()->find(array('condition' => "baseage = :baseage", 'params' => array("baseage" => $age), 'select' => 'weightage1'));
                        if (isset($peerscoreData->weightage1)) {
                            $nationalpeerrank = $peerscoreData->weightage1;
                            $localpeerrank = $peerscoreData->weightage1;
                        }
                        // Fetch Zip code
                        $userData = User::model()->find(array('condition' => "id=:user_id", 'params' => array("user_id" => $urow->id), 'select' => 'zip'));
                        if (strlen($userData->zip) >= 5) {
                            $usrzip = substr($userData->zip, 0, 3);
                            if (strlen($usrzip) == 3) {
                                $peerscoreData = Peerranking::model()->findBySql('SELECT ROUND(score) AS total
                FROM peerranking AS P
                JOIN regiondetails AS R ON P.region = R.region
                WHERE (:uzip BETWEEN SUBSTRING( R.zipcoderangeprefix, 1, 3 )
                AND SUBSTRING( R.zipcoderangeprefix, 5, 3 )
                OR :uzip BETWEEN SUBSTRING( R.zipcoderangeprefix, 9, 3 )
                AND SUBSTRING( R.zipcoderangeprefix, 13, 3 ))
                AND P.baseage = :bage', array("bage" => $age, "uzip" => $usrzip));
                                if ($peerscoreData->total) {
                                    $localpeerrank = $peerscoreData->total;
                                }
                            }
                        }

                        $usrArr[] = array('age' => $age, 'localpeer' => $localpeerrank, 'nationalpeer' => $nationalpeerrank);
                        $this->sengine->localPeerRank = $localpeerrank;
                        $this->sengine->nationalPeerRank = $nationalpeerrank;
                        $scoreScoreObj = UserScore::model()->find("user_id = :user_id", array("user_id" => $urow->id));
                        $seSerial = serialize($this->sengine);
                        $scoreScoreObj->scoredetails = $seSerial;
                        $scoreScoreObj->timestamp = date("Y-m-d H:i:s");
                        $scoreScoreObj->save();

                    }
                }
            }
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "peerrank" => $usrArr)));
    }

}

?>