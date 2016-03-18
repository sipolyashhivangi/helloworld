<?php

/* * ********************************************************************
 * Filename: Scoreengine.php
 * Folder: models
 * Description:  This is the model for the Score Engine DB - (db3)
 * @author Thayub Hashim Munnavver (For TruGlobal Inc)
 * @copyright (c) 2013 - 2014
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class Scoreengine extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    // Creating the table ORM :

    public function tableName() {
        return 'goals';
    }

// TABLE NAME : INCOME 
// ------------------

    public function insertIncomeInfo($user_id, $income) {

//		$user_id = '1';

        $userIncomeInfo = "INSERT into income VALUES ('','$user_id',$income->inc_gross,$income->inc_investment,$income->inc_spouse,$income->inc_retirement,$income->inc_pension,$income->inc_social_security,$income->inc_disability,$income->inc_veteran,'','')";

        //print_r($income->{'inc_gross'});die;

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userIncomeInfo)->execute();

        return 1;
    }

// TABLE NAME : EXPENSES
// ---------------------


    public function insertExpensesInfo($user_id, $expenses) {

//		$user_id = '1';
        $userExpensesInfoSql = "INSERT into expenses VALUES ('',$user_id,$expenses->exp_rent,$expenses->exp_utilities,$expenses->exp_groceries,$expenses->exp_gas,$expenses->exp_entertainment,$expenses->exp_household,$expenses->exp_health,$expenses->exp_travel,$expenses->exp_emi,$expenses->exp_cc_month,$expenses->exp_others,'','')";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userExpensesInfoSql)->execute();

        return 1;
    }

// TABLE NAME : GOALS 
// ------------------

    public function insertDebtsInfo($user_id, $ccname, $roi, $cc_amount) {

//		$user_id = 1;
        $roi = round($roi, 2);
        if ($user_id && $ccname && $roi && $cc_amount) {
            $roi = round($roi, 2);
            $userDebtsInfoSql = "INSERT into credit_cards_score VALUES ('','$user_id','','$ccname','$roi','$cc_amount')";
            $connection = Yii::app()->db3;
            $command = $connection->createCommand($userDebtsInfoSql)->execute();
            return 1;
        } else {
            return 0;
        }
    }

    public function insertDebts($user_id, $debts) {

        $userDebtsSql = "INSERT into debts VALUES ('',$user_id,$debts->mortgage,$debts->loan,$debts->autoloan,'')";
        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userDebtsSql)->execute();
    }

// TABLE NAME : GOALS 
// ------------------

    public function insertGoalsInfo($user_id, $goal_id, $goal_amount, $goal_year) {

//		$user_id = 1;
        $userGoalsInfoSql = "INSERT into goals_score VALUES ('','$user_id','$goal_id','$goal_amount','$goal_year','')";

        //	$userGoalsInfoSql = "INSERT into goals_score VALUES ('',$user_id,$goals->goal_id,$goals->goal_score)";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userGoalsInfoSql)->execute();

        return 1;
    }

// TABLE NAME : ASSETS 
// --------------------

    public function insertAssetsInfo($user_id, $assets) {
//		$user_id = '1';
        $userAssetsInfoSql = "INSERT into assets VALUES ('',$user_id,$assets->as_accounts,$assets->as_insurance,$assets->as_ira,$assets->as_company_retirement,$assets->as_brokerage,$assets->as_educational,$assets->as_property,$assets->as_vehicle,$assets->as_pension,$assets->as_social_security,$assets->as_business,'',$assets->as_others,'','')";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userAssetsInfoSql)->execute();
        return 1;
    }

// TABLE NAME : INSURANCE 
// -----------------------

    public function insertInsuranceInfo($user_id, $insurance) {

//		$user_id = '1';	
        $userInsuranceInfoSql = "INSERT into insurance VALUES ('',$user_id,$insurance->ins_daily_benefit_amount,$insurance->ins_life,$insurance->ins_disability,$insurance->ins_vehicle,$insurance->ins_health,$insurance->ins_retirement,$insurance->ins_home)";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userInsuranceInfoSql)->execute();

        return 1;
    }

// TABLE NAME : PROFILE
// --------------------

    public function insertProfileInfo($user_id, $profile) {
        //print_r($profile);die;
//		$user_id = '1';
        $userProfileInfoSql = "INSERT into profile VALUES ('',$user_id,$profile->prof_about,$profile->prof_assets,$profile->prof_debts,$profile->prof_income,$profile->prof_expenses,'','','',$profile->prof_pic,$profile->prof_age,$profile->prof_retirement_age,$profile->prof_retired,'','')";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userProfileInfoSql)->execute();

        return 1;
    }

// TABLE NAME : CONDITIONS 
// -----------------------

    public function insertConditionsInfo($user_id, $conditions) {

//		$user_id = '1';
//		Converting the object into array
        $arr = (array) $conditions;

//		Getting the condition names 
        $names = (array_keys($arr));
        $loop_count = count($names);

//      INSERTING INTO THE TABLES WITH THE CONDITION NUMBER , HENCE THE LOGIC AS BELOW :

        $connection = Yii::app()->db3;
        foreach ($names as $name) {

            $parts = explode("_", $name);
            $condition_id = $parts[1];
            //echo $condition_id;echo "  $arr[$name]";echo '<br>';
            $insertConditionsInfoSql = "INSERT into conditions_score VALUES ('',$user_id,$condition_id,'$arr[$name]','')";
            $command = $connection->createCommand($insertConditionsInfoSql)->execute();
            //echo "Result=".$insertConditionsInfoSql."<BR>";
        }
        return 1;
    }

    public function insertVideosInfo($user_id, $videos) {

//		$user_id = '1';

        $arr = (array) $videos;

        $names = (array_keys($arr));
        $loop_count = count($names);

        foreach ($names as $name) {

            $parts = explode("_", $name);
            $video_id = $parts[1];
//			echo $video_id;echo $arr[$name];echo '<br>';die;
            $insertVideosInfoSql = "INSERT into learning_videos_score VALUES ('',$user_id,$video_id,'$arr[$name]')";

            $connection = Yii::app()->db3;
            $command = $connection->createCommand($insertVideosInfoSql)->execute();
        }
        return 1;
    }

// TABLE NAME : Review Years 
// --------------------------

    public function insertYears($user_id, $years) {

//Creating the link between the years and the year Id's

        $values[1] = $years->life;
        $values[2] = $years->disability;
        $values[3] = $years->vehicle;
        $values[4] = $years->health;
        $values[5] = $years->umbrella;
        $values[6] = $years->home;
        $values[7] = $years->will;
        $values[8] = $years->longterm;
        $values[9] = $years->yourlife;
        $values[10] = $years->yourspouse;
//   INSERTING INTO THE TABLES WITH THE CONDITION NUMBER , HENCE THE LOGIC AS BELOW :
     //   $connection = Yii::app()->db3;
     // print_r($values);die;
        
//Ganesh - Build the insert values instead of inserting in loop - below is not a good idea - ORM
        for ($i = 1; $i < 11; $i++) {
         //   The below object should go out - Ganesh
            $userObject1 = new SreviewYearScore();
            $userObject1->user_id = $user_id;
            $userObject1->year_id = $i;
            $userObject1->year_value = $values[$i];
            $userObject1->save();  
            //$insertYearsSql = "INSERT INTO review_year_score (user_id,year_id,year_value) VALUES ('$user_id','$i','$values[$i]')";
            //$insertYearsSql = "INSERT into review_year_score VALUES ('',$user_id,$i,'$values[$i]')";
            //$command = $connection->createCommand($insertYearsSql)->execute();
        }
        
            return 1;
    }

    /**
     * 
     * @param type $user_id
     */
    function clearData($user_id) {
        $connection = Yii::app()->db3;
        $deleteQuery = "delete from income where user_id = $user_id";
        $connection->createCommand($deleteQuery)->execute();

        $deleteQuery = "delete from assets where user_id = $user_id";
        $connection->createCommand($deleteQuery)->execute();
        $deleteQuery = "delete from expenses where user_id = $user_id";
        $connection->createCommand($deleteQuery)->execute();
        $deleteQuery = "delete from debts where user_id = $user_id";
        $connection->createCommand($deleteQuery)->execute();
        $deleteQuery = "delete from  credit_cards_score where user_id = $user_id";
        $connection->createCommand($deleteQuery)->execute();
        $deleteQuery = "delete from  insurance where user_id = $user_id";
        $connection->createCommand($deleteQuery)->execute();
        $deleteQuery = "delete from  profile where user_id = $user_id";
        $connection->createCommand($deleteQuery)->execute();
        $deleteQuery = "delete from  learning_videos_score where user_id = $user_id";
        $connection->createCommand($deleteQuery)->execute();
        $deleteQuery = "delete from  conditions_score where user_id = $user_id";
        $connection->createCommand($deleteQuery)->execute();
    //    $deleteQuery = "delete from  goals_score where user_id = $user_id";
    //    $connection->createCommand($deleteQuery)->execute();
      //  $deleteQuery = "delete from  review_year_score where user_id = $user_id";
      //  $connection->createCommand($deleteQuery)->execute();
    }

}

?>