<?php

/* * ********************************************************************
 * Filename: Scoreengineconstants.php
 * Folder: models
 * Description:  This is the model for the constants that are created in Score Engine DB - (db3)
 * @author Thayub Hashim Munnavver (For TruGlobal Inc)
 * @copyright (c) 2013 - 2014
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class Scoreengineconstants extends SActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

// Creating the table ORM :

    public function tableName() {
        return 'profile';
    }

    //public var $breakup;
// TABLE NAME : Goal Setting Multiplier
//
//
//
// -------------------------------------

    public function constant1($user_id) {


        //Bank account and Insurance:

        $userAssSql = "SELECT * FROM assets WHERE user_id=:user_id ";
        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userAssSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();

        $assets_1 = $row["bank_account"];
        $assets_2 = $row["insurance"];

        $sum = ($assets_1 + $assets_2);

        // Total Expenses :
        $userExpSql = "SELECT * FROM expenses WHERE user_id=:user_id";

        $command = $connection->createCommand($userExpSql)->bindValue('user_id', $user_id);
        $row1 = $command->queryRow();


        // Calculation of expenses, soon to be moved to the expenses table itself.

        $rent_mortgage = $row1["rent_mortgage"];
        $utilities = $row1["utilities"];
        $groceries = $row1["groceries"];
        $gas = $row1["gas"];
        $entertainment = $row1["entertainment"];
        $household = $row1["household"];
        $health = $row1["health"];
        $travel = $row1["travel"];
        $loans_emi = $row1["loans_emi"];
        $credit_card = $row1["credit_card"];
        $other = $row1["other"];

        $total_expenses = ($rent_mortgage + $utilities + $groceries + $gas + $entertainment + $household + $health + $travel + $loans_emi + $credit_card + $other);

        if ($sum > ($total_expenses * 3)) {

            $constant1 = "1";
        } elseif ($sum > ($total_expenses * 2)) {

            $constant1 = ".75";
        } elseif ($sum > ($total_expenses * 1)) {

            $constant1 = ".5";
        } else {
            $constant1 = ".5";
        }



        return $constant1;
    }

// TABLE NAME : Monthly cost/monthly income
//
//
//
// -------------------------------------

    public function constant2($user_id) {

        $userIncSql = "SELECT * FROM income WHERE user_id=:user_id ";
        $userExpSql = "SELECT * FROM expenses WHERE user_id=:user_id";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userIncSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();

        $command = $connection->createCommand($userExpSql)->bindValue('user_id', $user_id);
        $row1 = $command->queryRow();

        //print_r ($row);print_r($row1);
        //	Calculation of income, soon to be moved to the income table itself.

        $gross_income = $row["gross_income"];
        $investment_income = $row["investment_income"];
        $spouse_income = $row["spouse_income"];
        $retirement_plan = $row["retirement_plan"];
        $pension_income = $row["pension_income"];
        $social_security = $row["social_security"];
        $disability_benefit = $row["disability_benefit"];
        $veteran_income = $row["veteran_income"];

        $total_income = ($gross_income + $investment_income + $spouse_income + $retirement_plan + $pension_income + $social_security + $disability_benefit + $veteran_income);

        // Calculation of expenses, soon to be moved to the expenses table itself.

        $rent_mortgage = $row1["rent_mortgage"];
        $utilities = $row1["utilities"];
        $groceries = $row1["groceries"];
        $gas = $row1["gas"];
        $entertainment = $row1["entertainment"];
        $household = $row1["household"];
        $health = $row1["health"];
        $travel = $row1["travel"];
        $loans_emi = $row1["loans_emi"];
        $credit_card = $row1["credit_card"];
        $other = $row1["other"];

        $total_expenses = ($rent_mortgage + $utilities + $groceries + $gas + $entertainment + $household + $health + $travel + $loans_emi + $credit_card + $other);

        //print_r	($total_expenses);print_r ($total_income);

        $constant2 = (($total_expenses / $total_income) * 100);

        //print_r ($constant2);
        return $constant2;
    }

// TABLE NAME : Debt Optimisation
//
//
//
// -------------------------------------

    public function constant3($user_id) {

        //$user_id = '29845';
        $connection = Yii::app()->db3;
        $userCheckSql = "SELECT count(user_id) FROM credit_cards_score WHERE user_id=:user_id";
        $command = $connection->createCommand($userCheckSql)->bindvalue('user_id', $user_id);
        $row = $command->queryAll();

        //print_r(($row));die;
        $condition = $row[0]['count(user_id)'];

        if ($condition == '0') {
            $result = '5';
        } elseif ($condition == '1') {
            $result = '0';
        } elseif ($condition > '1') {
            //Total Credit cards:
            $userCred1Sql = "SELECT roi FROM credit_cards_score WHERE amount IN
                ( SELECT MAX(amount)FROM credit_cards_score where user_id=:user_id) AND user_id=:user_id";

            $userCred2Sql = "SELECT MIN(roi) FROM credit_cards_score where user_id=:user_id";

            $command1 = $connection->createCommand($userCred1Sql)->bindvalue('user_id', $user_id);
            $command2 = $connection->createCommand($userCred2Sql)->bindvalue('user_id', $user_id);
            $row1 = $command1->queryRow();
            $row2 = $command2->queryRow();
            //print_r($row1);
            //print_r($row2);die;
            if ($row1['roi'] <= $row2['MIN(roi)']) {
                $result = '5';
            } else {
                $result = '0';
            }
        }
        //echo $result;die;
        return $result;
    }

// TABLE NAME : Credit Card - Funding Nest (7)
//
//	50 * [ 1 - ( (loan_emi+cc_emi)/total_income) / .2)  ]
//
// -------------------------------------

    public function constant4($user_id) {

        //Total Income:
        $userIncSql = "SELECT * FROM income WHERE user_id=:user_id";

        //Loan Emi & CC Emi:
        $userExpSql = "SELECT * FROM expenses WHERE user_id=:user_id";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userIncSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();

        $command1 = $connection->createCommand($userExpSql)->bindValue('user_id', $user_id);
        $row1 = $command1->queryRow();

        $gross_income = $row["gross_income"];
        $investment_income = $row["investment_income"];
        $spouse_income = $row["spouse_income"];
        $retirement_plan = $row["retirement_plan"];
        $pension_income = $row["pension_income"];
        $social_security = $row["social_security"];
        $disability_benefit = $row["disability_benefit"];
        $veteran_income = $row["veteran_income"];

        $total_income = ($gross_income + $investment_income + $spouse_income + $retirement_plan + $pension_income + $social_security + $disability_benefit + $veteran_income);

        // Adding the Loan EMI and CC EMI :

        $loans_emi = $row1["loans_emi"];
        $credit_card = $row1["credit_card"];

        $emi = ($credit_card);
//	echo "emi";		echo "$emi";	echo "total_income";		echo "$total_income";
        $constant_4 = 50 * ( 1 - ($loans_emi / $total_income / .2) );

        return $constant_4;
    }

// TABLE NAME : Mortgage - Funding Nest (9)
//
//
//
// -------------------------------------

    public function constant5($user_id) {

        //Total Income:
        $userIncSql = "SELECT * FROM income WHERE user_id=:user_id ";
        // Rent / mortgage:
        $userExpSql = "SELECT * FROM expenses WHERE user_id=:user_id";


        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userIncSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();

        $command = $connection->createCommand($userExpSql)->bindValue('user_id', $user_id);
        $row1 = $command->queryRow();

        $gross_income = $row["gross_income"];
        $investment_income = $row["investment_income"];
        $spouse_income = $row["spouse_income"];
        $retirement_plan = $row["retirement_plan"];
        $pension_income = $row["pension_income"];
        $social_security = $row["social_security"];
        $disability_benefit = $row["disability_benefit"];
        $veteran_income = $row["veteran_income"];

        $total_income = ($gross_income + $investment_income + $spouse_income + $retirement_plan + $pension_income + $social_security + $disability_benefit + $veteran_income);

        // Adding the rent /mortgage :

        $rent = $row1["rent_mortgage"];
//	echo $rent ;die;

        $constant5 = 50 * ( 1 - ( ($rent / $total_income) / .28) );
//	echo $constant5;die;
        return $constant5;
    }

// TABLE NAME : Assets - (Funding the Nest) (10)
//
//
//
// -------------------------------------

    public function constant6($user_id) {

        //Total Assets:
        $userAssSql = "SELECT * FROM assets WHERE user_id=:user_id ";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userAssSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();

        $assets[0] = $row["bank_account"];
        $assets[1] = $row["insurance"];
        $assets[2] = $row["ira"];
        $assets[3] = $row["comp_retirement_plan"];
        $assets[4] = $row["brokerage"];
        $assets[5] = $row["property"];
        $assets[6] = $row["pension"];
        $assets[7] = $row["social_security"];
        $assets[8] = $row["business"];
        $assets[9] = $row["others"];

        // Sum of all the Assets:
        $sum = $assets[0] + $assets[2] + $assets[3] + $assets[4] + $assets[5] + $assets[8] + $assets[9];



        // 10% of the sum :
        $percent_assets = round($sum / 10);


        // Finding the Granular level :

        $granular_level = "0";

        foreach ($assets as $each) {

            if ($each < $percent_assets) {

                $granular_level = ($granular_level + $each);
            }
        }
        //echo $granular_level;echo $sum;die;
        //Actual Formula :

        $constant6 = (($granular_level / $sum) * 100);
        return $constant6;
    }

// TABLE NAME : Assets - (Funding the Nest) (11)   - it is suppose to be (10)
//
// -------------------------------------

    public function constant7($user_id, $risk_tolerance, $retirementGoalValue) {

        // Age calculation :
        $userAgeSql = "SELECT age,retirement_age FROM profile WHERE user_id=:user_id ";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userAgeSql)->bindValue('user_id', $user_id);

        $row1 = $command->queryRow();
        $current_age = $row1["age"];
        $retirement_age = $row1["retirement_age"];

        $constant12 = $retirement_age - $current_age;

        // Risk Tolerance Factor :
        $risk = $risk_tolerance;

        //Inflation
        $inflation = ".034";

        // Total Assets :
        $userAssSql = "SELECT * FROM assets WHERE user_id=:user_id ";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userAssSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();

        $assets[0] = $row["bank_account"];
        $assets[1] = $row["insurance"];
        $assets[2] = $row["ira"];
        $assets[3] = $row["comp_retirement_plan"];
        $assets[4] = $row["brokerage"];
        $assets[5] = $row["property"];
        $assets[6] = $row["pension"];
        $assets[7] = $row["social_security"];
        $assets[8] = $row["business"];
        $assets[9] = $row["others"];

        // Sum of all the Assets:
        $sum = "0";

        foreach ($assets as $each) {
            $sum = $sum + $each;
        }

        $sum = $sum - ($assets[6] + $assets[7]);


        // Getting the goal amount :
        //$cond = "20";
        //$userGoalSql = "SELECT * FROM goals WHERE goal_tenure=:cond";
        $userGoalSql = "SELECT sum(amount) as totalGoal FROM goals_score where user_id=:cond";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userGoalSql)->bindParam("cond", $user_id);
        $row3 = $command->queryRow();

        // Ganesh:

        $goal_amount = $row3["totalGoal"] ? $row3["totalGoal"] : $retirementGoalValue;

        //echo "Goal amount=$goal_amount";
        //$goal_amount=500000;
        //echo "($sum * (pow( (1 +($risk / 10)),$constant12)) + $assets[6] + $assets[7])";
        //echo "<BR>";
        //echo "($goal_amount * (pow((1 + $inflation),$constant12)))";die;
        // Nr and Dr :
        $nr = ($sum * (pow((1 + ($risk / 10)), $constant12)) + $assets[6] + $assets[7]);
        $dr = ($goal_amount * (pow((1 + $inflation), $constant12)));

        //$nr = ($sum * (pow( (1 +($risk / 10)),$constant12)) + $assets[6] + $assets[7]);
        //$dr = ($goal_amount * (pow((1 + $inflation),$constant12)));
        //  echo "($sum * (pow(1 +($risk / 10),$constant12)) + $assets[6] + $assets[7])";
        //  echo 'foo';
        //  echo "($goal_amount * (pow((1 + $inflation),$constant12)))";die;
        //echo "$goal_amount";
        //echo "tooth";die;
        //Formula:
        if (!$goal_amount) {

            $constant7 = "0";
        } else {
            $constant7 = ( 100 * ($nr / $dr ));
        }
        //echo "$constant7 <BR>";
        return $constant7;
    }

// TABLE NAME : Bank Accounts - funding the Nest (14)
//
//
//
// -------------------------------------

    public function constant8($user_id) {

        //Total Assets :
        $userAssSql = "SELECT * FROM assets WHERE user_id=:user_id ";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userAssSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();


        $bank_account = $row["bank_account"];
        $insurance = $row["insurance"];
        $ira = $row["ira"];
        $comp_retirement_plan = $row["comp_retirement_plan"];
        $brokerage = $row["brokerage"];
        $pension = $row["pension"];
        $social_security = $row["social_security"];
        $business = $row["business"];
        $others = $row["others"];
        $k401 = $row["k401"];
        $property = $row["property"];
        $vehicle = $row["vehicle"];
        $edu_account = $row["edu_account"];

//	echo "($bank_account + $insurance + $ira + $brokerage + $others + $k401)";

        $nr = ($bank_account + $insurance + $ira + $brokerage + $others + $comp_retirement_plan);

        $dr = ($bank_account + $insurance + $ira + $comp_retirement_plan + $brokerage + $business + $others + $k401 + $property);

//	echo "$nr";
//	echo "$dr";

        $constant8 = 50 * ($nr / $dr);

        //echo "$constant8";die;
        return $constant8;
    }

// TABLE NAME : Investment Multiplier
//
//Investment Multiplier
//
// -------------------------------------

    public function constant9($user_id, $risk_tolerance) {

        $reco_risk = '0.5';
        $curr_risk = $risk_tolerance;

        $constant9 = 1 - (abs($reco_risk - $curr_risk) / $reco_risk);

        //$constant9 = ".8";
        if ($constant9 < 0) {
            $constant9 = 0;
        }
        return $constant9;
    }

// TABLE NAME : Inflation %
//
//Investment Multiplier
//
// -------------------------------------

    public function constant10($user_id) {

        $constant10 = "0.034";
        return $constant10;
    }

// TABLE NAME : Risk tolerance factor
//
//Investment Multiplier
//
// -------------------------------------

    public function constant11($user_id) {

        $constant11 = "0.6";
        return $constant11;
    }

// TABLE NAME : Years to retire
//
//Investment Multiplier
//
// -------------------------------------

    public function constant12($user_id) {

        // Age calculation :
        $userAgeSql = "SELECT age,retirement_age FROM profile WHERE user_id=:user_id ";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userAgeSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();

        $current_age = $row["age"];
        $retirement_age = $row["retirement_age"];

        $constant12 = $retirement_age - $current_age;

        return $constant12;
    }

// TABLE NAME : Contribution % (user provided)
//
//Investment Multiplier
//
// -------------------------------------

    public function constant13($user_id) {

        // Amount given for retirement annually :
        $userInsSql = "SELECT retirement FROM insurance WHERE user_id=:user_id";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userInsSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();

        // Calculation of Total Income:

        $userIncSql = "SELECT * FROM income WHERE user_id=:user_id ";

        $command1 = $connection->createCommand($userIncSql)->bindValue('user_id', $user_id);
        $row1 = $command1->queryRow();

        //print_r ($row);print_r($row1);
        //	Calculation of income, soon to be moved to the income table itself.

        $gross_income = $row1["gross_income"];
        $investment_income = $row1["investment_income"];
        $spouse_income = $row1["spouse_income"];
        $retirement_plan = $row1["retirement_plan"];
        $pension_income = $row1["pension_income"];
        $social_security = $row1["social_security"];
        $disability_benefit = $row1["disability_benefit"];
        $veteran_income = $row1["veteran_income"];

        $total_income = ($gross_income + $investment_income + $spouse_income + $retirement_plan + $pension_income + $social_security + $disability_benefit + $veteran_income);


        $dr = ($total_income * 12);

        $nr = $row["retirement"];

        $constant13 = (100 * ($nr / $dr));

        return $constant13;
    }

// TABLE NAME : Recommended % (calculated from calc xml)
//
//Investment Multiplier
//
// -------------------------------------

    public function constant14($user_id, $yearsToRet, $retGoalAmountNeeded) {

        // Calculation of Total Income:

        $userIncSql = "SELECT * FROM income WHERE user_id=:user_id ";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userIncSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();

        //Calculation of income, soon to be moved to the income table itself.
        //sav06: How much should I save to reach my goal?
        $gross_income = $row["gross_income"];
        $investment_income = $row["investment_income"];
        $spouse_income = $row["spouse_income"];
        $retirement_plan = $row["retirement_plan"];
        $pension_income = $row["pension_income"];
        $social_security = $row["social_security"];
        $disability_benefit = $row["disability_benefit"];
        $veteran_income = $row["veteran_income"];

        $total_income = ($gross_income + $investment_income + $spouse_income + $retirement_plan + $pension_income + $social_security + $disability_benefit + $veteran_income);


        // Constant from CalcXML:
        $currentSavingsBalance = $total_income;
        $futureAmountDesired = $retGoalAmountNeeded;
        $noOfYearNeeded = $yearsToRet;
        $annualIncreaseOnsaving = 0.04; //4% return on saving
        $beforeTaxReturn = 0.10; //risk tolerance number
        $taxBracket = 0.15;

        $calcXMLObj = Yii::app()->calcxml;
        $valueObj = new stdClass();
        $valueObj->initialBalance = $currentSavingsBalance;
        $valueObj->amountNeeded = $futureAmountDesired;
        $valueObj->numYears = $noOfYearNeeded;
        $valueObj->annualIncrease = $annualIncreaseOnsaving;
        $valueObj->beforeTaxReturn = $beforeTaxReturn;
        $valueObj->taxBracket = $taxBracket;

        $outputCalc = $calcXMLObj->saveToReachGoal($valueObj);

        // Formula :
        $dr = ($total_income * 12);


        $constant14 = (($outputCalc / $dr) * 100);

        return $constant14 < 0 ? 0 : $constant14;
    }

// TABLE NAME : Disability Insurance
//
//Investment Multiplier
//
// -------------------------------------

    public function constant15($user_id) {

        // Amount given for retirement annually :
        $userInsSql = "SELECT disability FROM insurance WHERE user_id=:user_id";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userInsSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();

        // To get the INCOMES :
        $userIncSql = "SELECT spouse_income,investment_income FROM income WHERE user_id=:user_id";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userIncSql)->bindValue('user_id', $user_id);
        $row1 = $command->queryRow();


        // To get the total EXPENSES :
        $userExpSql = "SELECT * FROM expenses WHERE user_id=:user_id";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userExpSql)->bindValue('user_id', $user_id);
        $row2 = $command->queryRow();


        // Calculation of the spouse income and the investment income:

        $investment_income = $row1["investment_income"];
        $spouse_income = $row1["spouse_income"];



        // Calculation of expenses, soon to be moved to the expenses table itself.

        $rent_mortgage = $row2["rent_mortgage"];
        $utilities = $row2["utilities"];
        $groceries = $row2["groceries"];
        $gas = $row2["gas"];
        $entertainment = $row2["entertainment"];
        $household = $row2["household"];
        $health = $row2["health"];
        $travel = $row2["travel"];
        $loans_emi = $row2["loans_emi"];
        $credit_card = $row2["credit_card"];
        $other = $row2["other"];

        $total_expenses = ($rent_mortgage + $utilities + $groceries + $gas + $entertainment + $household + $health + $travel + $loans_emi + $credit_card + $other);

        // Calculating the Nr and Dr :

        $nr = ($row["disability"] / 12);
        $dr = ($total_expenses - ($spouse_income * $investment_income));

        // Formula :
        $constant15 = (18 * ($nr / $dr));
        return $constant15;
    }

// TABLE NAME : Long Term Insurance
//
//Investment Multiplier
//
// -------------------------------------

    public function constant16($user_id) {

        // Amount your insurance will pay per day - Daily benefit amount

        $userInsSql = "SELECT daily_benefit_amount FROM insurance WHERE user_id=:user_id";
        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userInsSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();

        //print_r($row);die;


        $daily_amount = $row["daily_benefit_amount"];


        // Age calculation :
        $userAgeSql = "SELECT age,retirement_age FROM profile WHERE user_id=:user_id ";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userAgeSql)->bindValue('user_id', $user_id);

        $row1 = $command->queryRow();
        $current_age = $row1["age"];
        $retirement_age = $row1["retirement_age"];

        $constant12 = $retirement_age - $current_age; // Years to retire

        // Nr and Dr :
        // ins06 What are my long-term care insurance needs?
        $calcXMLObj = Yii::app()->calcxml;
        $valueObj = new stdClass();
        $valueObj->clientAge = $current_age;
        $valueObj->ltcAge = $retirement_age ;
        $valueObj->ltcCost = 50000;
        $valueObj->ltcInflation = 0.034;
        $valueObj->ltcYears = $retirement_age - $current_age;
        $valueObj->ltcAssets =0;
        $valueObj->beforeTaxReturn = 0.04;
        $valueObj->taxBracket = 0.15 ;
        $valueObj->ltcIncreases = 0;

        $neededAcmount = $calcXMLObj->longTermCareInsurance($valueObj);
//print _r($neededAcmount);
        //echo "Needed amount ".$neededAcmount;

        $neededAmount = $calcXMLObj->longTermCareInsurance($valueObj);


        $nr = ( $daily_amount * 365 );
        $dr = ( (375121 / 3) / pow(1.034, $constant12));

        $constant16 = 16 * ($nr / $dr);


        return $constant16;
        }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////			SCORE ENGINE CALCULATION
////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ACCOUNTS
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function score_1($user_id, $constant_1) {

        $max = "10";
        // Condition for calculation:

        if ($constant_1) {
            $result = ($max * $constant_1);
        } else {
            $result = $max;
        }

        Yii::app()->params['ACCOUNTS'] = $result;

        return $result;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// INCOME
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function score_2($user_id, $constant_2) {

        $max = "35";

        // Getting the values :

        $userIncSql = "SELECT * FROM income WHERE user_id=:user_id ";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userIncSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();

        //	Calculation of income, soon to be moved to the income table itself
        $gross_income = $row["gross_income"];
        $investment_income = $row["investment_income"];
        $spouse_income = $row["spouse_income"];
        $retirement_plan = $row["retirement_plan"];
        $pension_income = $row["pension_income"];
        $social_security = $row["social_security"];
        $disability_benefit = $row["disability_benefit"];
        $veteran_income = $row["veteran_income"];

        $total_income = ($gross_income + $investment_income + $spouse_income + $retirement_plan + $pension_income + $social_security + $disability_benefit + $veteran_income);


        // Condition for calculation:

        if ($constant_2 <= (.95 * $total_income)) {
            $result = "35";
        } elseif (($constant_2 >= (.95 * $total_income)) && ($constant_2 <= ( 105 * $total_income))) {
            $result = "20";
        } else {
            $result = "0";
        }

        Yii::app()->params['INCOME'] = $result;
        return $result;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// EXPENSES
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function score_3($user_id, $constant_2) {

        $max = "35";
        // Conditions
        if ($constant_2 <= 95) {

            $result = "35";
        } elseif (($constant_2 >= 95) && ($constant_2 < 105 )) {

            $result = "20";
        } else {
            $result = "0";
        }


        Yii::app()->params['EXPENSES'] = $result;

        return $result;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// DEBTS
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function score_4($user_id, $req_3, $yearToretire) {

        // Result 1:
        // Checking if the data is there. !
        //get all debts by user
        $userDebtsSql = "SELECT * FROM account WHERE user_id=:user_id and accttype in ('CreditCard','Mortgage','DebtOther','Loan','Autoloan')";
        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userDebtsSql)->bindValue('user_id', $user_id);

        $allDebts = $command->queryAll();

        $flag = 0;
        $valueArr = array();
        $counter = 1;
        foreach ($allDebts as $debts) {
            $valueObj = new stdClass();
            $valueObj->creditor = $debts["name"];
            $valueObj->balance = $debts["balanceowed"];
            $valueObj->minimumPayment = $debts["paypermonth"];
            $valueObj->actualPayment = $debts["amount"];
            $valueObj->rate = $debts["interestrate"] / 100;
            $valueObj->type = $debts["accttype"];
            $counter += $counter;
            $flag += $debts["balanceowed"];
            $valueArr[] = $valueObj;
        }

        //BUG FIXED:Point //4 = 5 not 0. As long as they download/manual entry any asset/debt/insurance they have,
        //they get these points.
        //check for all accounts in assets/debt/insurance
        //TODO Remove
        $accountCheckFor4 = "SELECT count(a.user_id) as accentry FROM assets a where a.user_id=:user_id";
        $cmdAccountCheckFor4 = $connection->createCommand($accountCheckFor4)->bindValue('user_id', $user_id);
        $rowAccountCheckFor4 = $cmdAccountCheckFor4->queryRow();
        $result1 = 0;
        if ($rowAccountCheckFor4["accentry"] > 0) {
            //TODO check for all tables
            $result1 = 5 * $req_3['constant_1'];
        }

        // Result 2
        //det07 - Restructuring debts   (12) points

        $calcXMLObj = Yii::app()->calcxml;
        $debtTerm = $calcXMLObj->personalDebtLoan($valueArr);
        //TODO calculate debt term based on the goal year
        if ($debtTerm < $yearToretire) {
            $result2 = 12;
        } else {
            $result2 = 0;
        }
        // Result 3
        //det06: Should I consolidate my personal debt into a new loan?
        $result3 = "0";
        //TODO
        //$debtTerm = $calcXMLObj->restructuringDebtsAcceleratedPayoff($valueArr);
        // Result 4
        $result4 = $req_3['constant_3'];

        // Result 5
        $result5 = $req_3['constant_4'];

        if ($result5 > '50') {

            $result5 = "50";
        } elseif ($result5 < '0') {
            $result5 = "0";
        }


        // Result 6
        // Total Expenses :
        $userDebtsSql = "SELECT * FROM debts WHERE user_id=:user_id";
        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userDebtsSql)->bindValue('user_id', $user_id);
        $row1 = $command->queryRow();
        // Calculation of rent_mortgage:

        $rent_mortgage = $row1["mortgage"];
        if ($rent_mortgage > 0) {

            $result6 = "5";
        } else {

            $result6 = "0";
        }


        // Result 7

        $mortgageFundingNest = $req_3['constant_5'];

        if ($mortgageFundingNest > '50') {
            $mortgageFundingNest = "50";
        } elseif ($mortgageFundingNest < '0') {
            $mortgageFundingNest = "0";
        }
        // echo "$result7.'hoo'.$result6";die;
        Yii::app()->params['DEBTS'] = "Bkgnd- Goal Setting: $result1
                        Bkgnd-Cash Flow :          $result2  + $result3
                        Cred cards-Bkgnd- Debt Optimize:$result4
                        Cred cards-Funding the nest:    $result5
                        Mortgage    -Debt opt.:  $result6
                        Mortgage    -Funding the nest:  $mortgageFundingNest";


        $result = ($result1 + $result2 + $result3 + $result4 + $result5 + $result6 + $mortgageFundingNest);
        return $result;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ASSETS
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function score_5($user_id, $req_4, $retired_status) {

        //print_r ($req_4);die;
// Result 1		:	Background calculations
//-----------------------------------------
//	a. background calculations		-	NOT CALCULATED
//	b. Funding the nest				-	YES
//	c. Funding the nest				- 	YES
// 	d. Fundind the nest - Monte carlo-	PENDING
        // a.
        ////////////////////////////////////////////////////////////////////////
        // b.
        $result1_2 = $req_4["constant_6"];

// c. Funding the NEST - 11
// New Formula
//Net-worth = Assets � Debts
//When Net-worth is POSITIVE:   (1 - Debt/Net-worth) * 100
//When Net-worth is NEGATIVE: (Assets/Debt� 1) * 100


        $userAssetsSql = "SELECT * FROM assets WHERE user_id=:user_id ";

        $connection = Yii::app()->db3;
        $command_assets = $connection->createCommand($userAssetsSql)->bindValue('user_id', $user_id);
        $assets = $command_assets->queryRow();



        $total_assets = $assets['bank_account'] + $assets['ira'] + $assets['comp_retirement_plan'] + $assets['brokerage'] +
                $assets['property'] + $assets['business'] + $assets['others'];


        $userDebtsSql = "SELECT * FROM debts WHERE user_id-:user_id";
        $command_debts = $connection->createCommand($userDebtsSql)->bindValue('user_id', $user_id);
        $debts = $command_debts->queryRow();

        $debts_1 = $debts['mortgage'] + $debts['loan'] + $debts['autoloan'] + $debts['others'];

        $userCreditSql = "SELECT SUM(amount) FROM credit_cards_score WHERE user_id =:user_id";
        $command_credit = $connection->createCommand($userCreditSql)->bindValue('user_id', $user_id);
        $debts_credit = $command_credit->queryRow();

        $debts_2 = $debts_credit['SUM(amount)'];

        $debts = ($debts_1 + $debts_2);

        $net = ($total_assets - $debts);

        if ($net > '0') {

            $net_result = (1 - ($debts / $net)) * 100;
        } else {

            $net_result = ($total_assets / $net) * 100;
        }


        if ($net_result > '100') {

            $net_result = "100";
        } elseif ($net_result < '-100') {

            $net_result = "-100";
        }


        $result1_3 = $net_result;

        // d.
        // Pending - Assumed values:

        $result1_4 = 150 * (.67);   // .67 is assumed
        // $RESULT1:
        //echo "($result1_2 + $result1_3 + $result1_4)";
        $result1 = ($result1_2 + $result1_3 + $result1_4);

// Result 2		:	BANK ACCOUNTS
//-----------------------------------------
//	a. background calculations

        $result2 = $req_4["constant_8"];
        if ($result2 > 50) {
            $result2 = "50";
        }


// RESULT 3		:	IRA
//-----------------------
//	a. Estate planning
//	b. Investment - NOT RETIRED
//	c. Retirement planning
//	d. Retirement planning - CalcXML
//	e. Retirement planning - NOT RETIRED
//	f. Retirement planning - RETIRED
// 	g. Static points - (Life Expectancy Considerations)
//	BACKGROUND CALCULATIONS:
//	h. If Retired only
//	i. Investment - I
//	j. Investment - II
//	k. Investment - III
//  l. Funding the Nest
//	a. Estate planning:
        // Getting the values :

        $userConSql = "SELECT * FROM conditions_score WHERE user_id=:user_id ";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userConSql)->bindValue('user_id', $user_id);
        $row = $command->queryAll();
//	echo '<PRE>';
//	print_r ($row);
        foreach ($row as $each) {
            if ($each['condition_id'] == '1') {
                $result3_1_cond = $each['value'];
                break;
            }
        }
        if ($result3_1_cond == 'yes') {

            $result3_1 = '12';
        } else {
            $result3_1 = '0';
        }


//	b. Investment - Not retired

        foreach ($row as $each) {
            if ($each['condition_id'] == '2') {
                $result3_2_cond = $each['value'];
                break;
            }
        }
        if ($result3_2_cond > '0') {

            $result3_2 = '0'; //(5 * $req_4["constant_9"]); pending
        } else {

            $result3_2 = '0';
        }




// 	c. Retirement Planning:

        if ($result3_2_cond > '0') {

            $result3_3 = '0';   // pending
        } else {

            $result3_3 = '0';
        }

//	d. 	Retirement planning - CalcXML
//		print_r($req_4);die;
        if ($req_4['constant_13'] == '0') {
            $result3_4 = '0';
        } elseif ($req_4['constant_13'] > '0') {
            $result3_4 = (30 * ($req_4['constant_12'] / $req_4['constant_13']));
        } else {

        }

        if ($result3_4 > '30') {
            $result3_4 = '30';
        }


//	e.	Retirement planning - NOT RETIRED

        if ($retired_status == '1') {
            $result3_5 = '0';
        } else {

            foreach ($row as $each) {
                if ($each['condition_id'] == '26') {
                    $result3_5_cond1 = $each['value'];
                }
            }

            if ($result3_5_cond1 > '0') {

                $result3_5_1 = '8';
            } else {
                $result3_5_1 = '0';
            }


            $result3_5 = ($result3_5_1 );
        }

//	f. 	Retirement planning - RETIRED

        if ($retired_status == '0') {
            $result3_6 = '0';
        } else {
            foreach ($row as $each) {
                if ($each['condition_id'] == '16') {
                    $result3_6 = $each['value'];
                    break;
                }
            }

            if ($result3_6 > '0') {
                $result3_6 = '20';
            } else {
                $result3_6 = '0';
            }
        }


//	g.	Static points - (Life Expectancy Considerations)

        if ($retired_status == '1') {
            $result3_7 = '10';
        } else {

            $result3_7 = '0';
        }

//	h.	BACKGROUND CALCULATIONS  -- If Retired only
//              Formula:
//              (Amount withdrawn / year) / Total Assets  >= 	Sustainable Rate
        $result3_8 = "0";

        if ($retired_status == '1') {

            $userAmtSql = "SELECT * from expenses WHERE user_id=:user_id";
            $userAssSql = "SELECT * FROM assets WHERE user_id=:user_id";
            $command1 = $connection->createCommand($userAmtSql)->bindValue('user_id', $user_id);
            $command2 = $connection->createCommand($userAssSql)->bindValue('user_id', $user_id);
            $row1 = $command1->queryRow();
            $row2 = $command2->queryRow();
            //print_r($row1);die;
            $expenses_month = ($row1["rent_mortgage"] + $row1["utilities"] + $row1["groceries"] + $row1["gas"] + $row1["other"] +
                    $row1["entertainment"] + $row1["household"] + $row1["health"] + $row1["travel"] + $row1["loans_emi"] + $row1["credit_card"]);

            $expenses_year = ($expenses_month * 12);

            $total_assets = ($row2["bank_account"] + $row2["ira"] + $row2["comp_retirement_plan"] + $row2["brokerage"]
                    + $row2["property"] + $row2["business"] + $row2["others"]);

            $userAgeSql = "SELECT age FROM profile where user_id=:user_id";
            $command3 = $connection->createCommand($userAgeSql)->bindValue('user_id', $user_id);
            $row3 = $command3->queryRow();


            $percent = ($expenses_year / $total_assets) * 100;
            $age = $row3["age"];

            if ($age > '100') {
                $age = '100';
            }
            $userSustRate = "SELECT sustainablewithdrawalpercent FROM sustainable_rates WHERE age=:age";
            $command4 = $connection->createCommand($userSustRate)->bindValue('age', $age);
            $row4 = $command4->queryRow();

            $rate = $row4["sustainablewithdrawalpercent"];

            if ($percent >= $rate) {
                $result3_8 = '20';
            }

            //echo $percent;die;
        }



// 	i.	Investment - I

        $result3_9 = '0'; //(5 * $req_4['constant_9']);  pending
//	j.	Investment - II

        $result3_10 = '0'; // (10 * $req_4['constant_9']);   pending
//	k.	Investment - III

        $result_one = '0';
        $result_two = '0';
        $result_three = '0';
        $result_final = '0';

        foreach ($row as $each) {
            if ($each['condition_id'] == '24') {
                $result_one = $each['value'];
                break;
            }
            if ($each['condition_id'] == '31') {
                $result_two = $each['value'];
                break;
            }
            if ($each['condition_id'] == '29') {
                $result_three = $each['value'];
                break;
            }
        }

        if (($result_one > '0') || ($result_two > '0') || ($result_three > '0')) {

            $result_final = '10';
        }

        $result3_11 = $result_final;

//	l.	Funding the Nest

        $result3_12 = $req_4['constant_8'];

//	echo "($result3_1 + $result3_2 + $result3_3 + $result3_4 + $result3_5 + $result3_6 + $result3_7 + $result3_9 + $result3_10 + $result3_11 + $result3_12)";

        $result3 = ($result3_1 + $result3_2 + $result3_3 + $result3_4 + $result3_5 + $result3_6 + $result3_7 + $result3_8 + $result3_9 + $result3_10 + $result3_11 + $result3_12);


// Result 4		:	Company Retirement Plan
// Result 5		:	Brokerages
// Result 6		:	Educational Account
// Result 7		: 	Pension
// Getting the values :
        $userConSql = "SELECT * FROM assets WHERE user_id=:user_id";

        $connection = Yii::app()->db3;
        $command1 = $connection->createCommand($userConSql)->bindValue('user_id', $user_id);
        $row1 = $command1->queryRow();

        $result7_1_cond = $row1['pension'];

        if ($retired_status == 0) {

            if ($result7_1_cond > '0') {

                $result7_1 = '3';
                $result7_2 = '4';
                $result7 = ($result7_1 + $result7_2);
            } else {

                $result7 = '0';
            }
        } else {

            $result7 = '0';
        }




// Result 8		:	Others
//		echo "($result1+$result2+$result3+$result7)";die;

        Yii::app()->params['ASSETS'] = "        Bkg Calculations(MC):   $result1 : ($result1_2 + $result1_3 + $result1_4)
                        Bank Accounts:  $result2
                        IRA - Estate planning   :   $result3_1
                        IRA - Investment        :   $result3_2
                        IRA - Retirement plan I :   $result3_3
                        IRA - Retire - CalcXML  :   $result3_4
                        IRA - Retirement plan II:   $result3_5
                        IRA - Retire.Plan(RETIRED): $result3_6
                        IRA - Retire.Plan(RETIRED): $result3_7
                        IRA - Backgnd Calculations: ($result3_8 + $result3_9 + $result3_10 + $result3_11 + $result3_12)
                        Pension:        $result7";
        // echo "($result1 + $result2 + $result3 + $result7)";die;
        $result = ($result1 + $result2 + $result3 + $result7);
        return $result;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// RISK TOLERANCE  #28
// As long as they enter a risk #, they get these points.
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function score_6($user_id, $constant_1) {

        $max = "15";

        $result = ($max * $constant_1);

        if ($result >= $max) {
            $result = "15";
        }

        Yii::app()->params['RISK'] = $result;

        return $result;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	INSURANCE
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function score_7($user_id, $req, $income, $yearToretire) {


        // Result 1
        // Back ground Calculations I
        $result1 = (5 * $req['constant_1']);

        if ($result1 >= '5') {
            $result1 = "5";
        } else {
            $result1 = "0";
        }

        // Result 2
        // Back ground Calculations II

        $result2 = (50 * $req['constant_8']);

        if ($result2 >= '50') {
            $result2 = "50";
        } else {
            $result2 = "0";
        }

        // Result 3
        // Life Insurance:
        $userInsSql = "SELECT * FROM insurance WHERE user_id=:user_id";
        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userInsSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();
        $insurance_life = $row['life'];

        //CALC XML Integration
        //ins01 - How much life insurance do I need?
        $calcXMLObj = Yii::app()->calcxml;
        $valueObj = new stdClass();
        $valueObj->clientIncome = $income->inc_gross + $income->inc_investment + $income->inc_retirement + $income->inc_pension + $income->inc_social_security + $income->inc_disability + $income->inc_veteran;
        $valueObj->spouseIncome = 0;
        $valueObj->spouseAge = 0;
        $valueObj->spouseRetAge = 0;
        $valueObj->beforeTaxReturn = 0.04;
        $valueObj->inflation = 0.034;
        $valueObj->funeral = 0;
        $valueObj->finalExpenses = 0;
        $valueObj->mortgageBalance = 0;
        $valueObj->otherDebts = 0;
        $valueObj->desiredIncome = $valueObj->clientIncome * 0.80; // 80 % of total income
        $valueObj->term = $yearToretire;
        $valueObj->collegeNeeds = 0;
        $valueObj->investmentAssets = 0;
        $valueObj->lifeInsurance = 0;
        $valueObj->includeSocsec = "N";
        $valueObj->child1Age = 0;
        $valueObj->child2Age = 0;
        $valueObj->child3Age = 0;
        $valueObj->child4Age = 0;
        $outputCalc = $calcXMLObj->lifeInsuranceINeed($valueObj);

        $result3 = $outputCalc?(24 * ($insurance_life / $outputCalc) ):0;  //Division by zero- needs to be modified

        /*
        if ($result3 >= '24') {
            $result3 = "24";
        } else {
            $result3 = "0";
        } */
        $lifeInsuranceyours=$row["life"];
        $result3=0;

        if ( $lifeInsuranceyours){
        $yearReviewSql = "SELECT year_id,year_value FROM review_year_score WHERE user_id=:user_id AND year_id=9 ORDER BY NO DESC LIMIT 1";
        $command1 = $connection->createCommand($yearReviewSql)->bindValue('user_id', $user_id);
        $row3 = $command1->queryRow();
        $reviewDate = $row3["year_value"];
        $yearGapfromReview = date("Y") - $reviewDate;
        $baseValue = 24;
        $division = 12;
        if ($reviewDate){
            $result3 = ($yearGapfromReview <= 0) ? $baseValue : ($baseValue - ($yearGapfromReview * $division));
            $result3 = ($result3 <= (-$baseValue)) ? -$baseValue : $result3;
        }
        }

        // Points degeneration : (Will be made into a single query to get all the data, once it is finalised)
        //$yearReviewSql = "SELECT * FROM review_year_score WHERE user_id=:user_id AND year_id=1";
        //$command1 = $connection->createCommand($yearReviewSql)->bindValue('user_id', $user_id);
        //$row3 = $command1->queryRow();

       /* if ($row3["year_value"] != 0) {
            $result3_year_review = self::pointsDegenaration($row3, $result3);
            $result3 = ($result3_year_review);
        }
        */

        //**********************
        // Result 4
        // Disablity Insurance:
        //*********************

        $disabilityInsurance = $req['constant_15'];
        $result4 = 0;
        //Ganesh - Code has to be generic - Point Degradation
        if($disabilityInsurance) {

        $yearReviewSql = "SELECT year_id,year_value FROM review_year_score WHERE user_id=:user_id AND year_id=2 ORDER BY NO DESC LIMIT 1";
        $command1 = $connection->createCommand($yearReviewSql)->bindValue('user_id', $user_id);
        $row4 = $command1->queryRow();
        $reviewDate = $row4["year_value"];
        $yearGapfromReview = date("Y") - $reviewDate;

        $baseValue = 18;
        $division = -12;
        if ($reviewDate){
            $result4 = ($yearGapfromReview <= 0) ? $baseValue : ($baseValue + ($yearGapfromReview * $division));
            $result4 = ($result4 <= (-$baseValue)) ? -$baseValue : $result4;
        }
        }

        // Points degeneration :
        /*
        $yearReviewSql = "SELECT * FROM review_year_score WHERE user_id=:user_id AND year_id=2";
        $command1 = $connection->createCommand($yearReviewSql)->bindValue('user_id', $user_id);
        $row4 = $command1->queryRow();

        $result4_year_review = self::pointsDegenaration($row4, $result4);

        $result4 = ($result4_year_review);
*/


        //**************************
        // Result 5
        // Long term Insurance
        //////////////////////
        //**************************
        $longTermInsurance = $req['constant_16'];
        $result5 = 0;
        //Ganesh - Code has to be generic - Point Degradation
        if($longTermInsurance) {

        $yearReviewSql = "SELECT year_id,year_value FROM review_year_score WHERE user_id=:user_id AND year_id=8 ORDER BY NO DESC LIMIT 1";
        $command1 = $connection->createCommand($yearReviewSql)->bindValue('user_id', $user_id);
        $row5 = $command1->queryRow();
        $reviewDate = $row5["year_value"];
        $yearGapfromReview = date("Y") - $reviewDate;

        $baseValue = 16;
        $division = $baseValue / 2;
        if ($reviewDate){
            $result5 = ($yearGapfromReview <= 0) ? $baseValue : ($baseValue - ($yearGapfromReview * $division));
            $result5 = ($result5 <= (-$baseValue)) ? -$baseValue : $result5;
        }
        }
       //echo "$yearGapfromReview <br>";
        /*if ($result5 >= '16') {
            $result5 = "16";
        } else {
            $result5 = "0";
        }
        // Points degeneration :
        $yearReviewSql = "SELECT * FROM review_year_score WHERE user_id=:user_id AND year_id=5";
        $command1 = $connection->createCommand($yearReviewSql)->bindValue('user_id', $user_id);
        $row3 = $command1->queryRow();

        $result5_year_review = self::pointsDegenaration($row3, $result5);
        $result5 = ($result5_year_review);
        */


        //***************************************
        // Result 6
        // Home / Renters insurance
        //***************************************

        $insurance_home = $row['home'];
        $result6=0;
        //Ganesh - Code has to be generic - Point Degradation
        if ($insurance_home) {
        $yearReviewSql = "SELECT year_id,year_value FROM review_year_score WHERE user_id=:user_id AND year_id=6 ORDER BY NO DESC LIMIT 1";
        $command1 = $connection->createCommand($yearReviewSql)->bindValue('user_id', $user_id);
        $row6 = $command1->queryRow();

        $baseValue = 8;
        $reviewDate = $row6["year_value"];
        $yearGapfromReview = date("Y") - $reviewDate;
        //$division = ($baseValue * 2) / 15;

        if($yearGapfromReview>=0){
        switch($yearGapfromReview)
        {
            case "0":$result6=$baseValue;break;
            case "1":$result6=$baseValue-$baseValue;break;
            default:$result6=-$baseValue;break;
        }
        }else{
            $result6=$baseValue;
        }
        }

        // Points degeneration :

        //$yearReviewSql = "SELECT * FROM review_year_score WHERE user_id=:user_id AND year_id=6";
        //$command1 = $connection->createCommand($yearReviewSql)->bindValue('user_id', $user_id);
        //$row4 = $command1->queryRow();

        //$result6_year_review = self::pointsDegenaration($row4, $result6);
        //$result6 = $result6_year_review;

        //*************************************************
        // Result 7
        // Vehicle insurance
        //*************************************************
        $insurance_vehicle = $row['vehicle'];
        $result7 = "0";

        if ($insurance_vehicle){
        //Ganesh - Code has to be generic - Point Degradation
        $yearReviewSql = "SELECT year_id,year_value FROM review_year_score WHERE user_id=:user_id AND year_id=3 ORDER BY NO DESC LIMIT 1";
        $command1 = $connection->createCommand($yearReviewSql)->bindValue('user_id', $user_id);
        $row7 = $command1->queryRow();
        $baseValue = 8;
        $reviewDate = $row7["year_value"];
        $yearGapfromReview = date("Y") - $reviewDate;
        //$division = ($baseValue * 2) / 15;

        if($yearGapfromReview>=0){
        switch($yearGapfromReview)
        {
            case "0":$result7=$baseValue;break;
            case "1":$result7=$baseValue-$baseValue;break;
            default:$result7=-$baseValue;break;
        }
        }else{
            $result7=$baseValue;
        }
        }
        // Points degeneration :
        /*
        $yearReviewSql = "SELECT * FROM review_year_score WHERE user_id=:user_id AND year_id=3";
        $command1 = $connection->createCommand($yearReviewSql)->bindValue('user_id', $user_id);
        $row5 = $command1->queryRow();
        */
        //$result7_year_review = self::pointsDegenaration($row5, $result7);
        //$result7 = $result7_year_review;


        //***************************
        // Result 8
        // Health insurance
        //***************************
        $insurance_health = $row['health'];
        $result8 = 0;

        if ($insurance_health) {


        // Points degeneration :
        //Ganesh - Code has to be generic - Point Degradation
        $yearReviewSql = "SELECT year_id,year_value FROM review_year_score WHERE user_id=:user_id AND year_id=4 ORDER BY NO DESC LIMIT 1";
        $command1 = $connection->createCommand($yearReviewSql)->bindValue('user_id', $user_id);
        $row8 = $command1->queryRow();
        $baseValue = 15;
        $reviewDate = $row8["year_value"];
        $yearGapfromReview = date("Y") - $reviewDate;
        //$division = ($baseValue * 2) / 15;

        if($yearGapfromReview>=0){
        switch($yearGapfromReview)
        {
            case "0":$result8=$baseValue;break;
            case "1":$result8=$baseValue-$baseValue;break;
            //case "2":$result8=-$baseValue;break;
            default:$result8=-$baseValue;break;
        }
        }else{
            $result8=$baseValue;
        }
        }
        //$result8_year_review = self::pointsDegenaration($row6, $result8);
        //$result8 = $result8_year_review;



        Yii::app()->params['INSURANCE'] = "Life Insurance:  $result3
                        Disablity Insurance:    $result4
                        Long term Insurance:    $result5
                        Home/RenterInsurance:   $result6
                        Vehicle Insurance :     $result7
                        Health Insurance :      $result8";

        $result = ($result3 + $result4 + $result5 + $result6 + $result7 + $result8); // Result 1 and Result 2 are removed as per excel sheet
        //print_r($result3 ."+". $result4 ."+". $result5 ."+". $result6 ."+". $result7 ."+". $result8);die;
        return $result;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // MISC
    // TAXES
    // ESTATE  PLANNING
    // MORE
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function score_8($user_id, $req_1) {



        $userConSql = "SELECT * FROM conditions_score WHERE user_id=:user_id ORDER BY condition_id ASC";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userConSql)->bindValue("user_id", $user_id);
        $row = $command->queryAll();

        //echo '<PRE>';print_r ($row);
        // Result 1   Money back or Pay more

        $result1_1 = $row[20];
        $result1_cond = $result1_1['value'];
        //echo "$result1_cond <BR>";
        if (($result1_cond == 'val_1') || ($result1_cond == 'val_2') || ($result1_cond == 'val_3')) {
            $result1 = "4";
        } elseif ($result1_cond == 'val_0') {
            $result1 = "0";
        } else {
            $result1 = "0";
        }

        // Result 2
        // Ganesh - Combining Federal and state tax, what bracket do you fall in ?
        // Should consider row[21]
        $result2_cond = $row[21]['value'];
        // echo  "$result2_cond ";

        if ($result2_cond == 'val_0') {
            $result2 = "0";
        } else {
            $result2 = "5";
        }
        // Ganesh - Commented
        //$result2 = (5 * ($req_1['constant_9']) );
        //echo $result2;
        // Result 3

        $result3_1 = $row[3];
        $result3_cond = $result3_1['value'];
        $result3 = 0;
        if ($result3_cond == 'yes') {
            $result3 = "5";
        } elseif ($result3_cond == 'no') {
            $result3 = "0";
        }


        // Result 4 & 5
        //Ganesh - Code has to be generic - Point Degradation
        $yearReviewSql = "SELECT year_id,year_value FROM review_year_score WHERE user_id=:user_id AND year_id=7 ORDER BY NO DESC LIMIT 1";
        $command1 = $connection->createCommand($yearReviewSql)->bindValue('user_id', $user_id);
        $row1 = $command1->queryRow();
        $reviewDate = $row1["year_value"];
        $yearGapfromReview = date("Y") - $reviewDate;
        //$result4_1 = $row[4];
        $result4_cond = $row[4]['value'];
        $result4 = 0;
        $baseValue = 20;
        $division = ($baseValue * 2) / 5;

        if ($result4_cond == 'yes' && !$reviewDate) {
        $result4 = "20";
        }
        elseif ($result4_cond == 'yes' && $reviewDate){
            $result4 = ($yearGapfromReview <= 0) ? $baseValue : ($baseValue - ($yearGapfromReview * $division));
            $result4 = ($result4 <= (-$baseValue)) ? -$baseValue : $result4;
        } elseif ($result4_cond == 'no') {
            $result4 = "0";
        }
//echo "$yearGapfromReview $result4<br>";

        // Result 5 :
        // //If they have a will ask this question,
        //and if it was updated within the last 5 years, give them the points
        $result5 = ( $yearGapfromReview <= 4) && ($result4_cond == 'yes') ? "5" : "0";



        // year with the depreciation factor to be added---?
        // Result 6
        // #41 Do you have an information list of where you keep your hidden assets, passwords, keys?
        //echo '<PRE>';print_r($row);
        $result6_1 = $row[5]; // changed from $row[4]
        $result6_1_cond = $result6_1['value'];
        $result6_2 = $row[19];
        $result6_2_cond = $result6_2['value'];


        //echo "$result6_1_cond + $result6_2_cond";
        $result6 = "0";
        if (($result6_1_cond == 'yes') && ($result6_2_cond == 'yes')) {
            $result6 = "4";
        }


        // Result 7
        //#42 Do you own anything that needs to liquidated upon your death

        $result7_1 = $row[7];
        $result7_cond = $result7_1['value'];
        $result7 = "0";
        if ($result7_cond) {
            $result7 = "3";
        }

        // Result 8

        $result8_1 = $row[6];
        $result8_cond = $result8_1['value'];
        $result8 = "0";
        if ($result8_cond == 'yes') {
            $result8 = "3";
        } elseif ($result8_cond == 'no') {
            $result8 = "0";
        }
//		echo $result8;die;
        // Result 9

        $result9_1 = $row[9];
        $result9_cond = $result9_1['value'];
        $result9 = "0";
        if ($result9_cond == 'man') {
            $result9 = "0";
        } elseif ($result9_cond == 'aut') {
            $result9 = ( 5 * ($req_1['constant_9']) );
        }


        // Result 10

        $result10_1 = $row[10];
        $result10_cond = $result10_1['value'];
        $result10 = "0";
        if ($result10_cond == 'yes') {
            $result10 = "5";
        } elseif ($result10_cond == 'no') {
            $result10 = "0";   // Doubt //( 5 * ($req_1['constant_9']) );
        }


        // Result 11

        $result11_1 = $row[11];
        $result11_cond = $result11_1['value'];
        $result11 = "0";
        if ($result11_cond == 'man') {
            $result11 = "0";
        } elseif ($result11_cond == 'aut') {
            $result11 = ( 5 * ($req_1['constant_9']) );
        }

        // Result 12

        $result12_1 = $row[16];
        $result12_cond = $result12_1['value'];

        if ($result12_cond == 'yes') {
            $result12 = ( 5 * ($req_1['constant_9']) );
        } else {
            $result12 = "0";
        }

        // Result 13

        $result13_1 = $row[17];
        $result13_cond = $result13_1['value'];

        if ($result13_cond == 'yes') {
            $result13 = "3";
        } else {
            $result13 = "0";
        }

        // Result 14

        $result14_1 = $row[8];
        $result14_cond = $result14_1['value'];

        if ($result14_cond > "0") {
            $result14 = "7";
        } else {
            $result14 = "0";
        }


        Yii::app()->params['MISC'] = "Taxes - Tax   :$result1
                        Taxes - Investment  :$result2
                        Taxes - Tax II      :$result3
                        Estate Planning 1   :$result4
                        Estate Planning 2   :$result5
                        Estate Planning 3   :$result6
                        Estate Planning 4   :$result7
                        Estate Planning 5   :$result8
                        Others-Investment 1 :$result9
                        Others-Investment 2 :$result10
                        Others-Cash Flow    :$result11
                        Others-Investment 4 :$result12
                        Others-EstatePlanni :$result13
                        Others-Debt optimi. :$result14";


        $result = ($result1 + $result2 + $result3 + $result4 + $result5 + $result6 + $result7 + $result8 + $result9 + $result10 + $result11 + $result12 + $result13 + $result14);
        return $result;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// LEARNING VIDEOS
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function score_9($user_id) {


        $userProSql = "SELECT * FROM profile WHERE user_id=:user_id";
        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userProSql)->bindvalue("user_id", $user_id);
        $row1 = $command->queryAll();

        //print_r($row1[0]["retired"]);
        $retired_status = $row1[0]["retired"];

        $userConSql = "SELECT * FROM learning_videos_score WHERE user_id=:user_id";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userConSql)->bindValue("user_id", $user_id);
        $row = $command->queryAll();

        //print_r($row);
        // Result 1
        //--------

        $result1_1 = $row[0];
        $result1_cond = $result1_1['score'];

        if ($result1_cond == 'yes') {
            $result1 = "5";
        } elseif ($result1_cond == 'no') {
            $result1 = "0";
        }

        // Result 2
        //--------

        $result2_1 = $row[1];
        $result2_cond = $result2_1['score'];

        if ($result2_cond == 'yes') {
            $result2 = "5";
        } elseif ($result2_cond == 'no') {
            $result2 = "0";
        }

        // Result 3
        //--------

        $result3_1 = $row[2];
        $result3_cond = $result3_1['score'];

        if (($retired_status == 0) && ($result3_cond == 'yes')) {
            $result3 = "6";
        } elseif ($result3_cond == 'no') {
            $result3 = "0";
        } else {
            $result3 = "0";
        }

        // Result 4
        //--------

        $result4_1 = $row[3];
        $result4_cond = $result4_1['score'];

        if ($result4_cond == 'yes') {
            $result4 = "5";
        } elseif ($result4_cond == 'no') {
            $result4 = "0";
        }

        // Result 5
        //--------

        $result5_1 = $row[4];
        $result5_cond = $result5_1['score'];
        $result5 = "0";

        if (($retired_status == 1) && ($result5_cond == 'yes')) {
            $result5 = "8";
        } elseif ($result5_cond == 'no') {
            $result5 = "0";
        } else {
            $result5 = "0";
        }

        // Result 6
        //--------
        $result6_1 = $row[5];
        $result6_cond = $result6_1['score'];
        $result6 = "0";

        if (($retired_status == 1) && ($result3_cond == 'yes')) {
            $result6 = "5";
        } elseif ($result6_cond == 'no') {
            $result6 = "0";
        } else {
            $result6 = "0";
        }

        // Result 7
        //--------
        $result7_1 = $row[6];
        $result7_cond = $result7_1['score'];

        if ($result7_cond == 'yes') {
            $result7 = "10";
        } elseif ($result7_cond == 'no') {
            $result7 = "0";
        }
        // Result 8

        $result8_1 = $row[7];
        $result8_cond = $result8_1['score'];

        if ($result8_cond == 'yes') {
            $result8 = "20";
        } elseif ($result8_cond == 'no') {
            $result8 = "0";
        }

        // Result 9

        $result9_1 = $row[8];
        $result9_cond = $result9_1['score'];

        if ($result9_cond == 'yes') {
            $result9 = "5";
        } elseif ($result9_cond == 'no') {
            $result9 = "0";
        }

        // Result 10

        $result10_1 = $row[9];
        $result10_cond = $result10_1['score'];

        if ($result10_cond == 'yes') {
            $result10 = "5";
        } elseif ($result10_cond == 'no') {
            $result10 = "0";
        }

        // Result 11
        $result11_1 = $row[10];
        $result11_cond = $result11_1['score'];

        if ($result11_cond == 'yes') {
            $result11 = "6";
        } elseif ($result11_cond == 'no') {
            $result11 = "0";
        }

        //echo "foo";
        //echo "($result1+$result2+$result3+$result4+$result5+$result6+$result7+$result8+$result9+$result10+$result11)";

        Yii::app()->params['LEARNING_VIDEOS'] = "Knowledge of Liabilities    :$result1
                Knowledge to imp Debt situtaion :$result2
                Social Security                 :$result3
                Knowledge of health insurance   :$result4
                Total return Vs Straight income :$result5
                Inflation considerations        :$result6
                Investment Diversification      :$result7
                Budgeting                       :$result8
                Know & opt of vehicle/auto/rv insurance :$result9
                Know & opt of owners/renters insurance  :$result10
                Know and opt. of umbrella insurance     :$result11";

        $result = ($result1 + $result2 + $result3 + $result4 + $result5 + $result6 + $result7 + $result8 + $result9 + $result10 + $result11);
        return $result;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// GOALS
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function score_10($user_id, $req_2) {


        $userGoaSql = "SELECT * FROM goals_score WHERE user_id=:user_id";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userGoaSql)->bindValue("user_id", $user_id);
        $row = $command->queryAll();

        //	print_R ($row);die;

        if (!empty($row)) {

            $status = "1";
        } else {
            $status = "0";
        }

        // Result 1 : Assuming the goal condition:

        if ($status == 1) {

            $result1 = (10 * $req_2['constant_1']);

            if ($result1 > 10) {
                $result1 = '10';
            } else {

            }
        } else {

            $result1 = "0";
        }

        // Result 2	:	Assuming the previous condition is true


        if ($status == "1") {

            $result2 = (8 * $req_2['constant_9']);
            if ($result2 > 8) {
                $result2 = '8';
            } else {

            }
        } else {
            $result2 = "0";
        }

        // Result 3 :

        if ($req_2['constant_14'] > 0) {
            $result3 = (30 * ( $req_2['constant_13'] / $req_2['constant_14']));
        } else {
            $result3 = '0';
        }

        if ($result3 > '6') {
            $result3 = '6';
        } else {
            $result3 = '0';
        }

        // Result 4

        $result4 = $req_2['constant_7'];

        if ($result4 > '100') {
            $result4 = '100';
        } else {
            $result4 = '0';
        }

        // Result 5	: SIMULATION RESULT
        //////////////// PENDING ////////////////////////

        Yii::app()->params['GOALS'] = "Goal Setting :   $result1
                        Investment on goal  :   $result2";


        $result = ($result1 + $result2); // Result 3 and 4 are removed.
        return $result;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// PROFILE
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function score_11($user_id) {


        $userProSql = "SELECT * FROM profile WHERE user_id=:user_id";
        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userProSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();

        $prof_about = $row["about"];
        $prof_assets = $row["assets"];
        $prof_debts = $row["debts"];
        $prof_income = $row["income"];
        $prof_expenses = $row["expenses"];
        $prof_misc = $row["misc"];
        $prof_goals = $row["goals"];
        $prof_other = $row["other"];
        $prof_profile_pic = $row["profile_pic"];


        // ABOUT

        if ($prof_about) {
            $result1 = "5";   ////////////////////////////
        } else {
            $result1 = "0";   ////////////////////////////
        }

        // Assets
        //Total Assets:
        $userAssSql = "SELECT * FROM assets WHERE user_id=:user_id ";

        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userAssSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();

        $assets[0] = $row["bank_account"];
        $assets[1] = $row["insurance"];
        $assets[2] = $row["ira"];
        $assets[3] = $row["comp_retirement_plan"];
        $assets[4] = $row["brokerage"];
        $assets[5] = $row["property"];
        $assets[6] = $row["pension"];
        $assets[7] = $row["social_security"];
        $assets[8] = $row["business"];
        $assets[9] = $row["others"];

        // Sum of all the Assets:
        $sum = "0";

        foreach ($assets as $each) {
            $sum = $sum + $each;
        }

        $result2 = (($sum / $prof_assets ) * 10 ); ////////////////////////////////////

        if ($result2 > 10) {
            $result2 = "10";      ////////////////////////////////////
        }



        //DEBTS:
        //Total Credit cards:
        $userCredSql = "SELECT * FROM credit_cards";
        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userCredSql);
        $row = $command->queryAll();

        //print_r($row);
        $count = count($row);
        $slab1 = array();
        $slab2 = array();

        for ($i = '0'; $i < $count; $i++) {

            if (($row["$i"]['rate_of_interest'] >= '10' ) && ($row["$i"]['rate_of_interest'] < '15')) {
                array_push($slab1, $row["$i"]);
            }
            if (($row["$i"]['rate_of_interest']) >= '15' && ($row["$i"]['rate_of_interest'] <= '20')) {
                array_push($slab2, $row["$i"]);
            }
        }

        // Getting the amount in the slabs :

        $slab1_amount = "0";
        $slab2_amount = "0";

        foreach ($slab1 as $each_1) {
            $card_id = $each_1['no'];

            $userAmoSql = "SELECT * FROM credit_cards_score WHERE user_id=:user_id AND cc_no=:cc_no";

            $connection = Yii::app()->db3;
            $command1 = $connection->createCommand($userAmoSql);
            $command1->bindValue('user_id', $user_id);
            $command1->bindValue('cc_no', $card_id);
            $row1 = $command1->queryRow();

            $slab1_amount += $row1['amount'];
        }

        foreach ($slab2 as $each_2) {

            $card_id = $each_2['no'];
            $userAmoSql = "SELECT * FROM credit_cards_score WHERE user_id=:user_id AND cc_no=:cc_no";
            $connection = Yii::app()->db3;
            $command2 = $connection->createCommand($userAmoSql);
            $command2->bindValue('user_id', $user_id);
            $command2->bindValue('cc_no', $card_id);
            $row2 = $command2->queryRow();

            $slab2_amount += $row2['amount'];
        }


        $result3 = (10) * (($slab2_amount + $slab1_amount) / $prof_debts);

        if ($result3 > 10) {
            $result3 = "10";      ////////////////////////////////////
        }


        // INCOME

        $userIncSql = "SELECT * FROM income WHERE user_id=:user_id ";
        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userIncSql)->bindValue('user_id', $user_id);
        $row = $command->queryRow();
        //	Calculation of income, soon to be moved to the income table itself.
        $gross_income = $row["gross_income"];
        $investment_income = $row["investment_income"];
        $spouse_income = $row["spouse_income"];
        $retirement_plan = $row["retirement_plan"];
        $pension_income = $row["pension_income"];
        $social_security = $row["social_security"];
        $disability_benefit = $row["disability_benefit"];
        $veteran_income = $row["veteran_income"];

        $total_income = ($gross_income + $investment_income + $spouse_income + $retirement_plan + $pension_income + $social_security + $disability_benefit + $veteran_income);


        $result4 = (($total_income / $prof_income) * 5);

        if ($result4 > 5) {
            $result4 = "5";      ////////////////////////////////////
        }



        // EXPENSES :

        $userExpSql = "SELECT * FROM expenses WHERE user_id=:user_id";

        $command = $connection->createCommand($userExpSql)->bindValue('user_id', $user_id);
        $row1 = $command->queryRow();


        // Calculation of expenses, soon to be moved to the expenses table itself.

        $rent_mortgage = $row1["rent_mortgage"];
        $utilities = $row1["utilities"];
        $groceries = $row1["groceries"];
        $gas = $row1["gas"];
        $entertainment = $row1["entertainment"];
        $household = $row1["household"];
        $health = $row1["health"];
        $travel = $row1["travel"];
        $loans_emi = $row1["loans_emi"];
        $credit_card = $row1["credit_card"];
        $other = $row1["other"];

        $total_expenses = ($rent_mortgage + $utilities + $groceries + $gas + $entertainment + $household + $health + $travel + $loans_emi + $credit_card + $other);

        $result5 = (($total_expenses / $prof_expenses ) * 5);

        if ($result5 > 5) {
            $result5 = "5";      ////////////////////////////////////
        }

        // MISC

        if ($prof_misc) {

            $result6 = "5";
        } else {

            $result6 = "0";
        }


        // GOALS

        if ($prof_goals) {

            $result7 = "5";
        } else {

            $result7 = "5"; // doubt
        }


        // OTHERS

        if ($prof_other) {

            $result8 = "4";
        } else {

            $result8 = "0";
        }

        // PROFILE PIC
        if ($prof_profile_pic) {

            $result9 = "1";
        } else {

            $result9 = "0";
        }

        //echo "($result1+$result2+$result3+$result4+$result5+$result6+$result7+$result8+$result9)";

        Yii::app()->params['PROFILE'] = "About You + Estimates  :$result1
                        Assets  :$result2
                        Debts   :$result3
                        Income  :$result4
                        Expense :$result5
                        Misc    :$result6
                        Goals   :$result7
                        Other   :$result8
                        Prof Pic:$result9";

        $result = ($result1 + $result2 + $result3 + $result4 + $result5 + $result6 + $result7 + $result8 + $result9);

        return $result;
    }

    // Function to calculate degenerative points :
    //--------------------------------------------

    public function pointsDegenaration($data, $result) {

        $maxima = $result;
        $year_value = $data['year_value'];
        $year_id = $data['year_id'];

        $yearValueSql = "SELECT * FROM review_year WHERE no=:year_id ";
        $connection = Yii::app()->db3;
        $command = $connection->createCommand($yearValueSql)->bindValue('year_id', $year_id);
        $row = $command->queryRow();

        //$maxima         =   $row['maxima'];
        $year_constant = $data['year_value'] == 0 ? date("Y") : $data['year_value'];
        $year_current = date("Y");
        //

        $threshold = ($year_current - $year_constant);
//echo "THRESH $year_id $year_value $year_constant $threshold $maxima <br>";
        if ($threshold > 1) {

            $range_full = 2 * $maxima;
            $points_per_year = ($range_full / $year_constant);
            $year_diff = ($year_current - $year_value);
            $result = $maxima - ($year_diff * $points_per_year); //echo "$maxima - ($year_diff * $points_per_year)";
        } else {
            $result = ($maxima);
        }

        return $result;
    }

}

?>