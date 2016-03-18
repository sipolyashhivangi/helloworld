<?php

/* * ********************************************************************
 * Filename: EmailSubscriptionController.php
 * Folder: controllers
 * Description: Manage Email Subscriptions
 * @author Daphne Dorman
 * @copyright (c) 2012 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class EmailSubscriptionController extends SController
{
    public function accessRules ()
    {
        return array_merge(
                array(array('allow', 'users' => array('?'))),
                // Include parent access rules
                parent::accessRules()
        );
    }
    function actionList ()
    {
            $output = array(
                "status" => "SUCCESS",
                "message" => "Device saved.",
                "subscription" => []
            );
            $this->sendResponse(
                200,
                CJSON::encode($output)
            );
    }
}

?>
