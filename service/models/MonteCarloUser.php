<?php

/**
 * This is the model class for table "montecarlouser".
 */
class MonteCarloUser extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return expense the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }


    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'montecarlouser';
    }


    public function runMonteCarlo($user_id, $returnType = null) {

        try {
            $sController = new Scontroller(1);
            $sController->unsetEngine();
            $sController->setEngine($user_id);
            $retirementMC = new MonteCarlo();


            $retirementMC->simIterations = 1000;
            $retirementMC->currentAge = $sController->sengine->userCurrentAge;
            $userRisk = ($sController->sengine->userRiskValue > 0) ? $sController->sengine->userRiskValue / 10 : 5;

            $retirementMC->retirementStartYear = $sController->sengine->yearToRetire + 1;
            $retirementMC->yearsInPlan = $sController->sengine->lifeEC - $sController->sengine->userCurrentAge + 1;

            if ($retirementMC->yearsInPlan > 0) {
                $retirementMC->simIterations = round(50000 / $retirementMC->yearsInPlan);
            } else {
                $retirementMC->simIterations = 1000;
            }
            if ($retirementMC->simIterations > 1000) {
                $retirementMC->simIterations = 1000;
            }

            // Min IRA/401k Withdrawal Age = 60
            $iraWithdrawalStartYear = 60 - $sController->sengine->userCurrentAge + 1;
            $retirementMC->iraWithdrawalStartYear = ($iraWithdrawalStartYear > 0) ? $iraWithdrawalStartYear : 1;

            //Retirement Income Start Age = 62 if not set by user
            $retirementIncomeAge = $sController->sengine->userRetirementAge;

            $retirementIncomeStartYear = $retirementIncomeAge - $sController->sengine->userCurrentAge + 1;
            $retirementMC->retirementStartYear = $retirementIncomeStartYear;

            //Early Withdrawal Penalty = 10.0%
            $retirementMC->earlyIraWithdrawalPenalty = .10;

            $socialSecurityStartYear = $sController->sengine->userRetirementAge - $sController->sengine->userCurrentAge + 1;
            if ($sController->sengine->userRetirementAge <= 62) {
                $socialSecurityStartYear = 62 - $sController->sengine->userCurrentAge + 1;
            }

            $pensionTaxFreeIncomeFlows = Assets::model()->findAll(array('condition' => 'user_id= :user_id AND type = "PENS" AND status ="0"',
            'params' => array('user_id' => $user_id), 'select' => 'agepayout, sum(balance) as balance',
            'group' => 'agepayout'));

            if ($pensionTaxFreeIncomeFlows) {
                foreach ($pensionTaxFreeIncomeFlows as $pensionTaxFreeIncomeFlow) {
                    $pensionStartYear = $pensionTaxFreeIncomeFlow->agepayout - $sController->sengine->userCurrentAge + 1;
                    $retirementMC->extraTaxFreeIncomePension[$pensionStartYear] = $pensionTaxFreeIncomeFlow->balance * 12;
                }
            }
            $socialSecurityTaxFreeIncome = Assets::model()->find(array('condition' => 'user_id= :user_id AND type ="SS" AND status ="0"',
            'params' => array('user_id' => $user_id), 'select' => 'sum(balance) as balance'));
            if ($socialSecurityTaxFreeIncome) {
                $retirementMC->extraTaxFreeIncomeSocialSecurity = $socialSecurityTaxFreeIncome->balance * 12;
            }

            // For monte carlo version 1 spending is stable only
            $mcSpendingPolicy = "Stable";
            $retirementMC->spendingPolicy = 1;

            // Left code below in case spending policy is implemented later
            // spending policy Flexible - 2, Conservative - 1, other - 3
            // take the user risk  (// How to get this value from user?  Can we use the Risk tolerance Factor)
            /*  $userRiskPer = $userRisk * 100;
              if ($userRiskPer > 0 && $userRiskPer <= 30) {
              $retirementMC->spendingPolicy = 1;
              $mcSpendingPolicy = "1 - Conservative";
              } else if ($userRiskPer > 30 && $userRiskPer <= 60) {
              $retirementMC->spendingPolicy = 3;
              $mcSpendingPolicy = "3 - Other";
              } else {
              $retirementMC->spendingPolicy = 2;
              $mcSpendingPolicy = "2 - Flexible";
              } */

            //Minimum percent of expenses to fund 75%
            $retirementMC->spendingPercentFloor = 0.75;
            //Maximum percent of expenses to fund 500 %
            $retirementMC->spendingPercentCeiling = 5;
            $retirementMC->gsMode = 3;
            //Portfolio Value Trigger - 70%
            //   $retirementMC->btwTriggerPercent = 0.7;
            //Return to work duration (yrs) - 5
            //    $retirementMC->btwDuration = 5;
            //Max age to work - 70
            //    $btwMaxYear = 70 - $sController->sengine->userCurrentAge + 1;
            //    $retirementMC->btwMaxYear = ($btwMaxYear > 0) ? $btwMaxYear : 1;


            $retirementMC->swapWithdrawalOrder = true;
            $retirementMC->RMDStartYear = ($retirementMC->btwMaxYear - $retirementMC->currentAge + 1 > 0) ? $retirementMC->btwMaxYear - $retirementMC->currentAge + 1 : 1;

            $retirementMC->startingTaxableBalance = $sController->sengine->userSumOfAssets - $sController->sengine->startingTaxFreeBalance - $sController->sengine->startingTaxDeferredBalance;
            $retirementMC->startingTaxDeferredBalance = $sController->sengine->startingTaxDeferredBalance;
            $retirementMC->startingTaxFreeBalance = $sController->sengine->startingTaxFreeBalance;

            $goalObj = Goal::model()->find("user_id = :user_id and monthlyincome > 0", array("user_id" => $user_id));
            if (isset($goalObj) && !empty($goalObj)) {
                $retirementMC->baseAnnualWithdrawal = $goalObj->monthlyincome * 12;
            } else {
                $retirementMC->baseAnnualWithdrawal = $sController->sengine->userIncomePerMonth * 12 * 0.80; // 80 % of total income
            }

            // Set the plan rates
            $retirementMC->inflationRate = $sController->sengine->currentInflation;
            $retirementMC->inflationStdDev = 0.0;
            $retirementMC->riskReturn = $sController->sengine->userGrowthRate / 100;
            $retirementMC->riskStdDev = $sController->sengine->riskStdDev / 100;
            $retirementMC->investmentTaxRate = .15;
            $retirementMC->incomeTaxRate = $sController->sengine->taxBracket;

            // 1. Income Tax Rate - tax bracket set in miscellaneous form
            $retirementMC->addPlanRateInfo(1, 1, $retirementMC->yearsInPlan, $retirementMC->incomeTaxRate);
            // 2. Investment Tax Rate - 15%
            $retirementMC->addPlanRateInfo(2, 1, $retirementMC->yearsInPlan, $retirementMC->investmentTaxRate);
            // 3. Inflation - constant set at 3.4%
            $retirementMC->addPlanRateInfo(3, 1, $retirementMC->yearsInPlan, $retirementMC->inflationRate); // $sController->sengine->currentInflation -> 0.034
            // 4. Inflation - the flexscore monte carlo system does not vary inflation, so standard deviation is set to 0%
            $retirementMC->addPlanRateInfo(4, 1, $retirementMC->yearsInPlan, $retirementMC->inflationStdDev);
            // 5. Return - The user's base return rate is initialized with the user's risk level and randomized in the
            //    monte carlo component using a normal distribution and the standard deviation.
            $retirementMC->addPlanRateInfo(5, 1, $retirementMC->yearsInPlan, $sController->sengine->userGrowthRate / 100);
            // 6. Return standard deviation, also from the risk table and randomized in the monte carlo component.
            $retirementMC->addPlanRateInfo(6, 1, $retirementMC->yearsInPlan, $sController->sengine->riskStdDev / 100);


            // Set the annual cash flows
            // 1. Taxable Annual Savings - 5000;
            $retirementMC->addPlanCashflow(1, 1, $retirementMC->retirementStartYear, $sController->sengine->taxableAnnualSavings);  // Default Value = 5000
            // 2. Tax Deferred Annual Savings - 500;
            #Combined amount user is contributing in IRA , and Company Retirement Plan : Taken from MC
            $retirementMC->addPlanCashflow(2, 1, $retirementMC->retirementStartYear, $sController->sengine->taxDeferredAnnualSavings);
            // 3. Tax Free Annual Savings
            $retirementMC->addPlanCashflow(3, 1, $retirementMC->retirementStartYear, $sController->sengine->taxFreeAnnualSavings);
            // 4. Annual Taxable Retirement Income (to be added later, includes revenue such as annuities and real estate rental income).
            // $retirementMC->addPlanCashflow(4, $retirementMC->retirementStartYear, $retirementMC->yearsInPlan, $retirementMC->extraTaxFreeIncome);
            // 5. Annual Tax Free Retirement Income (includes pensions and social security)
            // Social security starts at age 62 in the FlexScore system.
            if (isset($retirementMC->extraTaxFreeIncomeSocialSecurity)) {
                $retirementMC->addPlanCashflow(5, $socialSecurityStartYear, $retirementMC->yearsInPlan, $retirementMC->extraTaxFreeIncomeSocialSecurity);
            }
            if (isset($retirementMC->extraTaxFreeIncomePension)) {
                foreach ($retirementMC->extraTaxFreeIncomePension as $pensionIncomeStartYear => $pensionTaxFreeCashFlow) {
                    $retirementMC->addPlanCashflow(5, $pensionIncomeStartYear, $retirementMC->yearsInPlan, $pensionTaxFreeCashFlow);
                }
            }
            // 8. Spending Reqested
            $retirementMC->addPlanCashflow(8, $retirementMC->retirementStartYear, $retirementMC->yearsInPlan, $retirementMC->baseAnnualWithdrawal);
            // 9. Spending Reqested
            $retirementMC->addPlanCashflow(9, $retirementMC->retirementStartYear, $retirementMC->yearsInPlan, $retirementMC->baseAnnualWithdrawal);

            //check for default goal if no goal is present
            //Spending Adjustment Multiplier from 1 to 99.99 (risk tolerance)
            $retirementMC->spendingPolicySensitivity = $userRisk;

            $mcParams = array();
            if ($returnType == "medianData") {
                $mcParams["userRisk"] = number_format($userRisk * 10);
                $mcParams["riskReturn"] = number_format($retirementMC->riskReturn * 100, 2);
                $mcParams["riskStdDev"] = number_format($retirementMC->riskStdDev * 100, 2);
                $mcParams["yearsInPlan"] = $retirementMC->yearsInPlan;

                $mcParams["currentAge"] = $retirementMC->currentAge;
                $mcParams["retirementAge"] = $retirementIncomeAge;
                $mcParams["lifeExpectancy"] = $sController->sengine->lifeEC;

                $mcParams["inflation"] = number_format($retirementMC->inflationRate * 100, 2);
                $mcParams["taxBracket"] = number_format($retirementMC->incomeTaxRate * 100, 2);
                $mcParams["investmentTaxRate"] = number_format($retirementMC->investmentTaxRate * 100, 2);

                $mcParams["startingTaxableBalance"] = number_format($retirementMC->startingTaxableBalance);
                $mcParams["startingTaxDeferredBalance"] = number_format($retirementMC->startingTaxDeferredBalance);
                $mcParams["startingTaxFreeBalance"] = number_format($retirementMC->startingTaxFreeBalance);
                $mcParams["minIraWithdrawalAge"] = 60;
                $mcParams["taxableAnnualSavings"] = number_format($sController->sengine->taxableAnnualSavings);
                $mcParams["taxDeferredAnnualSavings"] = number_format($sController->sengine->taxDeferredAnnualSavings);
                $mcParams["taxFreeAnnualSavings"] = number_format($sController->sengine->taxFreeAnnualSavings);

                $mcParams["retirementStartYear"] = $retirementMC->retirementStartYear;
                $mcParams["iraWithdrawalStartYear"] = $retirementMC->iraWithdrawalStartYear;
                $mcParams["RMDStartYear"] = $retirementMC->RMDStartYear;

                $mcParams["baseAnnualWithdrawal"] = number_format($retirementMC->baseAnnualWithdrawal);
                $mcParams["retirementIncomeStartYear"] = $retirementMC->currentAge + $retirementIncomeStartYear;
                $mcParams["spendingPolicy"] = $mcSpendingPolicy;
                $mcParams["socialSecurityStartYear"] = $socialSecurityStartYear + $sController->sengine->userCurrentAge - 1;;
                $mcParams["extraTaxFreeIncomeSocialSecurity"] = number_format($retirementMC->extraTaxFreeIncomeSocialSecurity);
                if (isset($retirementMC->extraTaxFreeIncomePension)) {
                    foreach ($retirementMC->extraTaxFreeIncomePension as $pensionIncomeStartYear => $pensionTaxFreeCashFlow) {
                        //     $retirementMC->addPlanCashflow(5, $pensionIncomeStartYear, $retirementMC->yearsInPlan, $pensionTaxFreeCashFlow);
                        $mcParams["extraTaxFreeIncomePension"][] = array('startYear' => $pensionIncomeStartYear + $retirementMC->currentAge - 1,
                        'amountPerYear' => number_format($pensionTaxFreeCashFlow));
                    }
                }
            }

            if ($returnType == "medianData") {
                $mcData = $retirementMC->execute("medianData");
            } else {
                $mcData = $retirementMC->execute();
            }

            $returnData = array();
            if ($mcData) {
                $sController->sengine->monteCarlo = $mcData["probability"];
                $monthlyPensionAtRetirement = $mcData['annualPensionAtRetirement'] / 12;
                $monthlySocialSecurityAtRetirement = $mcData['annualSocialSecurityAtRetirement'] / 12;
                $sustainablewithdrawalpercent = isset($sController->sengine->sustainablewithdrawalpercent) ?
                $sController->sengine->sustainablewithdrawalpercent / 100 : .04;

                $futureIncome = $monthlyPensionAtRetirement + $monthlySocialSecurityAtRetirement +
                (($mcData['portfolioBalanceAtRetirement'] * $sustainablewithdrawalpercent) / 12);

                $returnData = array(
                'user' => $user_id,
                'probability' => $mcData['probability'],
                'numIterations' => $retirementMC->simIterations,
                'portfolioBalanceAtRetirement' => $mcData['portfolioBalanceAtRetirement'],
                'sustainablewithdrawalpercent' => $sustainablewithdrawalpercent,
                'monthlyPensionAtRetirement' => $monthlyPensionAtRetirement,
                'monthlySocialSecurityAtRetirement' => $monthlySocialSecurityAtRetirement,
                'futureIncome' => $futureIncome,
                );
                if ($returnType == "medianData") {
                    $returnData['mcParams'] = $mcParams;
                    $returnData['medianData'] = $mcData["medianData"];
                }
            }
            $sController->unsetEngine();
            unset($retirementMC);
            unset($mcData);

            return $returnData;
        } catch (Exception $e) {
            throw $e;
        }
    }


}

?>