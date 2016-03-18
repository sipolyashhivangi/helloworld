<?php

/* * ********************************************************************
 * Filename: RiskController.php
 * Folder: controllers
 * Description: Getting input from the HTML
 * @author Thayub J (For TruGlobal Inc)
 * @copyright (c) 2013 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class RiskController extends Scontroller {

    // Define access control
    public function accessRules() {
        return array_merge(
                        array(array('allow', 'users' => array('?'))),
                        // Include parent access rules
                        parent::accessRules()
        );
    }

    function actionRiskcrud() {

		if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
			$this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
		}
        $action = $_POST["action"];

        switch ($action) {
            case 'ADD':
                $this->createUpdateRisk();
                break;

            case 'UPDATE':
                $this->createUpdateRisk();
                break;

            case 'READ':
                $this->readRisk();
                break;
        }
    }

    function createUpdateRisk() {

        $wsUserObject = Yii::app()->getSession()->get('wsuser');
        $user_id = Yii::app()->getSession()->get('wsuser')->id;

        $profile = Userpersonalinfo::model()->find("user_id = :user_id", array(':user_id' => $user_id));

        if (!$profile) {
            $profile = new Userpersonalinfo();
            $profile->user_id = $user_id;
        }

        $profile->risk = $_POST["risk"];

        if ($profile->save()) {

            parent::setEngine();
            if ($profile->risk > 0) {
		        $riskObj = Risk::model()->find("risk = :risk", array(':risk' => $profile->risk));

                parent::setEngine();

                $this->sengine->userProfilePoints_userRisk = 1;
		        if($riskObj)
		        {
	                $this->sengine->userRiskValue = $profile->risk;
	                $this->sengine->userGrowthRate = $riskObj->returnrate;
                    $this->sengine->riskStdDev = $riskObj->stddev;
	                $this->sengine->riskMetric = $riskObj->metric;
		        }
		        else
		        {
    	            $this->sengine->userRiskValue = 0;
	                $this->sengine->userGrowthRate = 7.0;
	                $this->sengine->riskStdDev = 8.7;
	                $this->sengine->riskMetric = 0.43;
		        }
            }
            else {
                $this->sengine->userRiskValue = 0;
                $this->sengine->userGrowthRate = 7.0;
                $this->sengine->riskStdDev = 8.7;
                $this->sengine->riskMetric = 0.43;
                $this->sengine->userProfilePoints_userRisk = 0;
		    }
            parent::saveEngine();
            parent::calculateScore("RISK", $user_id);
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "growthrate" => $this->sengine->userGrowthRate, "message" => "success", "userdata" => $profile)));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "failure", "userdata" => array())));
        }
    }

    /*
     * To get Risk data from database and return the JSON array.
     */
    function actionRiskGetdata(){
        // Fetching the Risk information from the database
        $riskData = Risk::model()->findAll();
        if (isset($riskData)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "riskdata" => $riskData, "message" => "success")));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "failure")));
        }
    }

    /*
     * To get the Risk factors information as a JSON array for
     * an input risk value
     */
    function actionRiskFactorsGetdata(){
    	$risk_value = $_GET['risk_value'];
        // Fetching the Risk information from the database
        $riskData = Riskfactors::model()->findBySql("SELECT risk,domestic_equity, international_equity, " .
        		" 	altr_non_corelated_assets, income_bonds, market_cash" .
        		"   FROM riskfactors WHERE risk=:risk_value", array("risk_value" => $risk_value));
        if (isset($riskData)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "riskdata" => $riskData, "message" => "success")));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "failure")));
        }
    }
}

?>
