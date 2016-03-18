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

class ExpenseController extends Scontroller {

    public function accessRules() {
        return array_merge(
                        array(array('allow', 'users' => array('?'))),
                        // Include parent access rules
                        parent::accessRules()
        );
    }

    function actionExpenseCrud() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $action = $_POST["action"];

        switch ($action) {
            case "ADD":
                $this->createupdateExpense();
                break;

            case "READ":
                $this->readExpense();
                break;

            case "UPDATE":
                $this->createupdateExpense();
                break;

            case "DELETE":
                break;
        }
    }

    function CreateUpdateExpense() {

			$wsUserObject = Yii::app()->getSession()->get('wsuser');
			$user_id = Yii::app()->getSession()->get('wsuser')->id;


        $expense = Expense::model()->findByPk($user_id);

        if (!$expense) {
            $expense = new Expense();
        }

        $expense->user_id = $user_id;
        $expense->rentmortgage = $_POST["rent"];
        $expense->utilities = $_POST["utilities"];
        $expense->groceries = $_POST["groceries"];
        $expense->gastransportation = $_POST["gas"];
        $expense->entertainment = $_POST["entertainment"];
        $expense->household = $_POST["household"];
        $expense->health = $_POST["health"];
        $expense->cardloadpmnts = $_POST["cc"];
        $expense->taxes = $_POST["taxes"];
        $expense->travel = $_POST["travel"];
        $expense->other = $_POST["other"];
        $expense->actualexpense = $_POST["actualexpense"];

        if ($expense->save()) {

            //get the score engine component
            // Wfpoint 2 & 3 :
            parent::setEngine();
            if ($expense && $expense->actualexpense > 0) {
                $this->sengine->userExpensePerMonth = $expense->actualexpense;
                $this->sengine->userProfilePoints_expense = 1;
            } else {
                //take from expense
                $this->sengine->userProfilePoints_expense = 0;
                parent::saveEngine();

                $estimation = Estimation::model()->find("user_id=:user_id", array("user_id" => $user_id));
                parent::setEngine();
                if ($estimation && $estimation->houseexpense > 0) {
                    $this->sengine->userExpensePerMonth = $estimation->houseexpense;
                } else {
                    //default
                    $this->sengine->userExpensePerMonth = 0;
                }
            }
            parent::saveEngine();

            parent::calculateScore("PROFILE|EXPENSE", $user_id);
            unset($expense);
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'totalscore' => $this->totalScore, 'message' => 'Expense updated.')));
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Could not update expenses at this time.', 'expense' => array())));
        }
    }

    function readExpense() {

		$swsUserObject = Yii::app()->getSession()->get('wsuser');
		$id = Yii::app()->getSession()->get('wsuser')->id;

        $expense = Expense::model()->findBySql("select * from expense where user_id= :user_id", array("user_id" => $id));
		//$userAdvisorObject = AdvisorClientRelated::model()->findBySql("SELECT permission FROM consumervsadvisor WHERE user_id=:user_id AND advisor_id =:advisor_id", array("user_id" => $id, "advisor_id" => $advisor_id));
        if ($expense) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => 'Expenses successfully read.', 'expense' => $expense)));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => 'ERROR', 'message' => 'Could not update expenses at this time.', 'expense' => array())));
        }
    }

}

?>
