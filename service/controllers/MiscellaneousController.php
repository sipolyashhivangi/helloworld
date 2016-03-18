<?php

/* * ********************************************************************
 * Filename: MiscellaneousController.php
 * Folder: controllers
 * Description: Miscellaneous controller (taxes, estate planning, more)
 * @author Thayub J (For Myself)
 * @copyright (c) 2012 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */
#require_once(realpath(dirname(__FILE__) . '/../extensions/runactions/components/ERunActions.php'));
#Yii::import('ext.runactions.*');

class MiscellaneousController extends Scontroller {

    public $amt;

    public function accessRules() {
        return array_merge(
                        array(array('allow', 'users' => array('?'))),
                        // Include parent access rules
                        parent::accessRules()
        );
    }

    function actionMisccrud() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $action = $_POST["action"];

        switch ($action) {

            CASE "ADD":
                $this->createupdateMisc();
                break;
            CASE "READ":
                $this->readMisc();
                break;
            CASE "UPDATE":
                $this->createupdateMisc();
                break;
            CASE "DELETE";
                break;
        }
    }

    /**
     *
     */
    function createupdateMisc() {

			$wsUserObject = Yii::app()->getSession()->get('wsuser');
			$user_id = Yii::app()->getSession()->get('wsuser')->id;

        $ctype = $_POST["ctype"];

        $misc = Miscellaneous::model()->find("user_id= :user_id", array("user_id" => $user_id));

        if (!$misc) {
            $misc = new Misc();
            $misc->user_id = $user_id;
        }
        #$jsonValues = $_POST["json"];
        #$misc->values = json_encode($jsonValues);
        #$misc->subcategory = $ctype;
        #$misc->save();
        //get the score engine component

        switch ($ctype) {
            case "TAX":
                //update the required files
                $misc->taxpay = isset($_POST["taxpay"]) ? $_POST["taxpay"] : '';
                $misc->taxbracket = isset($_POST["taxbracket"]) ? $_POST["taxbracket"] : '';
                $misc->taxvalue = isset($_POST["taxvalue"]) ? $_POST["taxvalue"] : '';
                $misc->taxcontri = isset($_POST["taxcontri"]) ? $_POST["taxcontri"] : '';
                $misc->taxStdOrItemDed = isset($_POST["taxStdOrItemDed"]) ? $_POST["taxStdOrItemDed"] : '';
                if ($misc->save()) {
                    $this->reCalculateScore($misc, "ADD", 'TAX', $user_id);
                }
                break;

            case "ESTATE":
                $misc->misctrust = isset($_POST["misctrust"]) ? $_POST["misctrust"] : '';
                $misc->miscreviewmonth = isset($_POST["miscreviewmonth"]) ? $_POST["miscreviewmonth"] : '';
                $misc->miscreviewyear = isset($_POST["miscreviewyear"]) ? $_POST["miscreviewyear"] : '';
                $misc->mischiddenasset = isset($_POST["mischiddenasset"]) ? $_POST["mischiddenasset"] : '';
                $misc->miscrightperson = isset($_POST["miscrightperson"]) ? $_POST["miscrightperson"] : '';
                $misc->miscliquid = isset($_POST["miscliquid"]) ? $_POST["miscliquid"] : '';
                $misc->miscspouse = isset($_POST["miscspouse"]) ? $_POST["miscspouse"] : '';
                if ($misc->save()) {
                    $this->reCalculateScore($misc, "ADD", 'ESTATE', $user_id);
                }
                break;

            case "MORE":
                $misc->moremoney = isset($_POST["moremoney"]) ? $_POST["moremoney"] : '';
                $misc->moreinvrebal = isset($_POST["moreinvrebal"]) ? $_POST["moreinvrebal"] : '';
                $misc->moreautoinvest = isset($_POST["moreautoinvest"]) ? $_POST["moreautoinvest"] : '';
                $misc->moreliquidasset = isset($_POST["moreliquidasset"]) ? $_POST["moreliquidasset"] : '';
                $misc->morecharity = isset($_POST["morecharity"]) ? $_POST["morecharity"] : '';
                $misc->morecreditscore = isset($_POST["morecreditscore"]) ? $_POST["morecreditscore"] : '';
                $misc->morereviewmonth = isset($_POST["morereviewmonth"]) ? $_POST["morereviewmonth"] : '';
                $misc->morescorereviewyear = isset($_POST["morescorereviewyear"]) ? $_POST["morescorereviewyear"] : '';
                if ($misc->save()) {
                    $this->reCalculateScore($misc, "ADD", 'MORE', $user_id);
                }
                break;
        }

        unset($misc);
        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => 'Miscellaneous added / updated')));
    }

    function ReadMisc($id = 0) {

			$wsUserObject = Yii::app()->getSession()->get('wsuser');
			$id = Yii::app()->getSession()->get('wsuser')->id;


        $misc = Miscellaneous::model()->findBySql("select * from miscellaneous where user_id= :user_id", array("user_id" => $id));


        if ($misc) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => 'Miscellaneous successfully read.', 'misc' => $misc)));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => 'ERROR', 'message' => 'Could not read miscellaneous at this time.', 'misc' => '')));
        }
    }

    /**
     *
     * @param type $misc
     * @param type $action
     * @param type $ctype
     * @param type $user_id
     */
    function reCalculateScore($misc, $action, $ctype, $user_id) {
        parent::setEngine();

        switch ($ctype) {
            case "TAX":
                $taxBracketValue = 0.15;
                $taxBracketUser = 0;
                if (isset($misc->taxbracket)) {
                    switch ($misc->taxbracket) {
                        case 0:
                            $taxBracketValue = 0.15;
                            $taxBracketUser = 1;
                            break;
                        case 1:
                            $taxBracketValue = 0.25;
                            $taxBracketUser = 1;
                            break;
                        case 2:
                            $taxBracketValue = 0.35;
                            $taxBracketUser = 1;
                            break;
                        case 3:
                        case 4:
                        default:
                            $taxBracketValue = 0.15;
                            break;
                    }
                }
                $this->sengine->taxBracket = $taxBracketValue;
                # POINT 36:
                $this->sengine->taxBracketUser = $taxBracketUser;

                # POINT 35:
                if ($misc->taxpay != NULL && $misc->taxpay != "" && $misc->taxvalue != "" && $misc->taxvalue != "3") {
                    $this->sengine->doGetMoneyBackPayMore = true;
                } else {
                    $this->sengine->doGetMoneyBackPayMore = false;
                }

                # POINT 37:
                if ($misc->taxcontri != NULL && $misc->taxcontri != "") {
                    $this->sengine->userRetirementContributionDeductible = true;
                } else {
                    $this->sengine->userRetirementContributionDeductible = false;
                }
                break;

            case "ESTATE":

                # POINT 39:
                if ($misc->misctrust == 1) {
                    $this->sengine->willOrTrust = true;
                } else {
                    $this->sengine->willOrTrust = false;
                }

                # POINT 40:
                if ($misc->misctrust == 1 && $misc->miscreviewyear && $misc->miscreviewyear != 'Year' && $misc->miscreviewyear != NULL) {
                    $this->sengine->willTrustReviwed = 1;
                    $this->sengine->reviewYearP40 = $misc->miscreviewyear;
                } else {
                    $this->sengine->willTrustReviwed = 0;
                    $this->sengine->reviewYearP40 = date('Y');
                }

                # POINT 41:
                if ($misc->mischiddenasset == 1 && $misc->miscrightperson == 1) {
                    $this->sengine->informationListOfHiddenAsset = true;
                } else {
                    $this->sengine->informationListOfHiddenAsset = false;
                }

                # POINT 42:
                if ($misc->miscliquid != NULL && $misc->miscliquid != "") {
                    $this->sengine->liquidedOnDeath = true;
                } else {
                    $this->sengine->liquidedOnDeath = false;
                }

                # POINT 43:
                if ($misc->miscspouse != NULL && $misc->miscspouse != "") {
                    $this->sengine->plannedForInability = true;
                } else {
                    $this->sengine->plannedForInability = false;
                }
                break;

            case "MORE":

                # POINT 44:
                if ($misc->moremoney == 1) {
                    $this->sengine->manualOrAutomatic = true;
                } else {
                    $this->sengine->manualOrAutomatic = false;
                }
                # Point 45
                if ($misc->moreinvrebal == 1) {
                    $this->sengine->investmentAutomatically = true;
                } else {
                    $this->sengine->investmentAutomatically = false;
                }
                # Point 46
                if ($misc->moreautoinvest == 1) {
                    $this->sengine->investAutoRetBrokerage = true;
                } else {
                    $this->sengine->investAutoRetBrokerage = false;
                }
                # Point 47
                if ($misc->moreliquidasset == 1) {
                    $this->sengine->liquidAssets = true;
                } else {
                    $this->sengine->liquidAssets = false;
                }
                # Point 48
                if ($misc->morecharity == 1) {
                    $this->sengine->charityPlanDeath = true;
                } else {
                    $this->sengine->charityPlanDeath = false;
                }
                # Point 49
                if ($misc->morecreditscore > 0) {
                    $this->sengine->creditScoreApprox = 1;
                } else {
                    $this->sengine->creditScoreApprox = 0;
                }

                break;
        }
        parent::saveEngine();

        $misc_prof = Miscellaneous::model()->find("user_id=:user_id", array("user_id" => $user_id));
        parent::setEngine();
        // Ganesh :  I am deleteing the use of $count - not in use
        //$count = 0;
        //$total = 20;
        //for profile completeness points//
        $cnt1 = 0; // for taxes 1.5
        $cnt2 = 0; // for estate planning 1.25
        $cnt3 = 0; // for more 1.5
        $cnt4 = 0; // for more 1.0
        //taxes//
        if ($misc_prof->taxpay != "") {
            $cnt1++;
        }
        if ($misc_prof->taxbracket != "" && $misc_prof->taxbracket != "4") {
            $cnt1++;
        }
        if ($misc_prof->taxvalue != "" && $misc_prof->taxvalue != "3") {
            $cnt1++;
        }
        if ($misc_prof->taxcontri != "") {
            $cnt1++;
        }
        if ($misc_prof->taxStdOrItemDed != "") {
            $cnt1++;
        }
        //estate planning
        if ($misc_prof->misctrust != "") {
            $cnt2++;
        }
        if ($misc_prof->miscreviewmonth != "" && $misc_prof->miscreviewyear != "Year") {
            $cnt2++;
        }
        if ($misc_prof->mischiddenasset != "") {
            $cnt2 = ($misc_prof->mischiddenasset == '0') ? $cnt2+2 : $cnt2+1;
        }
        if ($misc_prof->miscrightperson != "") {
            $cnt2++;
        }
        if ($misc_prof->miscliquid != "") {
            $cnt2++;
        }
        if ($misc_prof->miscspouse != "") {
            $cnt2++;
        }

        //more//
        if ($misc_prof->moremoney != "") {
            $cnt3++;
        }
        if ($misc_prof->moreinvrebal != "") {
            $cnt4++;
        }
        if ($misc_prof->moreautoinvest != "") {
            $cnt4++;
        }
        if ($misc_prof->moreliquidasset != "") {
            $cnt4++;
        }
        if ($misc_prof->morecharity != "") {
            $cnt4++;
        }
        if ($misc_prof->morecreditscore != "") {
            $cnt4++;
        }
        if ($misc_prof->morereviewmonth != "" && $misc_prof->morescorereviewyear != 'Year') {
            $cnt4++;
        }

        $this->sengine->userProfilePoints_misc = ($cnt1 * 1.5 + $cnt2 * 1.25 + $cnt3 * 1.5 + $cnt4 * 1);
        parent::saveEngine();
        parent::calculateScore("MISCTAX|MISCMORE|MISCESTATE", $user_id);
    }
}

?>
