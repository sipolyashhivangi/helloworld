<?php

/* * ********************************************************************
 * Filename: ExpenseController.php
 * Folder: controllers
 * Description: expense controller
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class LearningController extends Scontroller {

    public $learningArticles = 90;
    public $evaluateHousingCosts = 55;
    public $improveCreditScore = 57;
    public $healthMedicalInsuranceArticle = 64;
    public $actionCompleted = '1';
    public $actionStarted = '3';
    public $actionHistory = '4';  //When any action done through Action Step
    public $actionDeleted = '5';

    public function accessRules() {
        return array_merge(
                array(array('allow', 'users' => array('?'))),
                // Include parent access rules
                parent::accessRules()
        );
    }

    /**
     * This function updates the usermedia table
     */
    function actionLearningUpdate() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $articleId = 0;
        $mediaType = '';

        // if the user is clicking on a video
        if (isset($_POST["name"])) {
            $video = $_POST["name"];
            $mediaType = "video";

            $metaObj = Actionstepmeta::model()->find("link=:link", array("link" => $video));
            if($metaObj) {
                $actionObj = Actionstep::model()->find("user_id = :user_id and actionid = :actionid and actionstatus in ('0','2','3')", array("user_id" => $user_id, "actionid" => $metaObj->actionid));
                if($actionObj && $actionObj->actionstatus == '0') {
                    $actionObj->actionstatus = '5';
                    $actionObj->save();
                }
                else if($actionObj)
                {
                    $actionObj->actionstatus = '4';
                    $actionObj->save();
                }
            }

            if ($metaObj && isset($metaObj->articles)) {
                $narray = explode('|', $metaObj->articles);
                foreach ($narray as $k => $nval) {
                    $viddiv = explode('#', $nval);
                    if ($viddiv[2]) {
                        $articleId = $viddiv[2];
                    }
                }
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "No articles were found for this video.")));
                return;
            }
        }
        // else the user is clicking on an article
        else {
            $articleId = $_POST["id"];
            $mediaType = "article";
        }
        if ($articleId == 0) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => 'Article/Video does not exist.')));
            return;
        }

        $mediaObj = UserMedia::model()->find("user_id=:user_id and media_id=:media_id", array("media_id" => $articleId, "user_id" => $user_id));
        $increaseMediaCount = true;
        if (!$mediaObj) {
            $mediaObj = new UserMedia();
            $mediaObj->created = new CDbExpression('NOW()');
        } else {
            $modified = new DateTime($mediaObj->modified);
            $interval = $modified->diff(new DateTime());
            if ($interval->days <= 90) {
                $increaseMediaCount = false;
            }
        }

        $mediaObj->modified = new CDbExpression('NOW()');
        $mediaObj->user_id = $user_id;
        $mediaObj->media_id = $articleId;
        $mediaObj->media_type = $mediaType;
        $mediaObj->save();

        if ($increaseMediaCount) {
            parent::setEngine();
            $this->sengine->mediaCount++;
            if ($this->sengine->oldestMediaDate == null) {
                $todayDate = new DateTime();
                $this->sengine->oldestMediaDate = $todayDate->format('Y-m-d H:i:s');
            }
            parent::saveEngine();
            if ($this->sengine->mediaCount >= 10) {
                $asObj = new Actionstep();
                $learningActionObj = Actionstep::model()->find("user_id = :user_id and actionid = :actionid and actionstatus in ('0','2','3')", array("user_id" => $user_id, "actionid" => $this->learningArticles));
                if(isset($learningActionObj) && $learningActionObj->actionstatus == $this->actionStarted) {
                    $asObj->updateActionStepStatus($learningActionObj->id, $this->actionCompleted);
                }
                else if(isset($learningActionObj)) {
                    $asObj->updateActionStepStatus($learningActionObj->id, $this->actionDeleted);
                }
            } else {
                parent::UpdateLearningActionStep($user_id, $this->sengine->mediaCount);
            }
        }
        parent::calculateScore("LEARNING", $user_id);

        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'totalscore' => $this->totalScore, 'score' => 'Updated')));
    }

}

?>