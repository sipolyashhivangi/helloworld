<?php

/* * ********************************************************************
 * Filename: Sengine.php
 * Folder: components
 * Description: Component for score engine as per points by wireframe
 * @author  Thayub Hashim (For myself)
 * @copyright (c) 2012 - 2013
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class Sengine extends CApplicationComponent {

    public $wfPoint1 = 0;
    public $wfPoint2 = 0;
    public $wfPoint3 = 1;
    public $wfPoint4 = 0;
    public $wfPoint5 = 25;
    public $wfPoint6 = 5;
    public $wfPoint7 = 50;
    public $wfPoint8 = 0;
    public $wfPoint9 = 50;
    public $wfPoint10 = 0;
    public $wfPoint11 = 0;
    public $wfPoint12 = 0;
    public $wfPoint13 = 1;
    public $wfPoint14 = 0;
    public $wfPoint15 = 0;
    public $wfPoint16 = 0;
    public $wfPoint17 = 0;
    public $wfPoint18 = 15;
    public $wfPoint19 = 0;
    public $wfPoint20 = 0;
    public $wfPoint21 = 0;
    public $wfPoint22 = 0;
    public $wfPoint23 = 0;
    public $wfPoint24 = 0;
    public $wfPoint25 = 0;
    public $wfPoint26 = 0;
    public $wfPoint27 = 0;
    public $wfPoint28 = 0;
    public $wfPoint29 = 0;
    public $wfPoint30 = 0;
    public $wfPoint31 = 0;
    public $wfPoint32 = 0;
    public $wfPoint33 = 0;
    public $wfPoint34 = 0;
    public $wfPoint35 = 0;
    public $wfPoint36 = 0;
    public $wfPoint37 = 0;
    public $wfPoint38 = 0;
    public $wfPoint39 = 0;
    public $wfPoint40 = 0;
    public $wfPoint41 = 0;
    public $wfPoint42 = 0;
    public $wfPoint43 = 0;
    public $wfPoint44 = 0;
    public $wfPoint45 = 0;
    public $wfPoint46 = 0;
    public $wfPoint47 = 0;
    public $wfPoint48 = 0;
    public $wfPoint49 = 0;
    public $wfPoint50 = 0;
    public $wfPoint58 = 0;
    public $wfPoint59 = 0;
    public $monteCarlo = 0;
    public $localPeerRank = 383;
    public $nationalPeerRank = 383;
    public $zipCode = 0;
    public $batchFileUpdate = false;
    public $assetsObj = null;
    public $insuranceObj = null;
    public $pertrackObj = null;
    public $restructuringDebtsArr = null;
    public $personalDebtLoanArr = null;
    public $currentYear = 2013;
    // Parameters required for the Score Engine to work :

    public $retired = false;
    public $permonthWithdrawal = 0;
    public $userExpensePerMonth = 0;
    public $userIncomePerMonth = 5000;
    //bank, brokerage, others and life insurance cash value
    public $userSumOfAssets = 0;
    public $userSumOfGoalSettingAssets = 0;
    public $userSumOfOtherAssets = 0;
    public $extraCashValue = 0;
    //Some constants and assumption used in score engine
    public $annualIncrease = 0.04; //4% return on saving
    public $taxBracket = 0.15; //standard 15%
    public $userCurrentAge = 30;
    public $yearToRetire = 35;
    public $userRetirementAge = 65;
    public $lifeEC = 82;
    public $setLifeEC = false; // Tracks if user set Life Expectation in Retirement Goal or is it by default
    public $currentInflation = 0.034; //3.4
    public $recomendedRisk = 0;
    public $lastConstantUpdate = '2013-01-01'; // Set a new variable in score engine
    // to default to "2013-01-01".

    /**
     * (1) + 10 (Goal Setting) (on First Download) As long as the user
     *      downloads at least one account, we give them the points.
     */
    public $isUserDownloadAccount = false;

    function setWfPoint1() {

        if ($this->isUserDownloadAccount) {
            $this->wfPoint1 = 10 * $this->wfPoint3;
        } else {
            $this->wfPoint1 = 0;
        }
    }

    /**
     * (2) + 35 (Cash Flow)
     * Monthly Expenses less than 95% of Monthly Income => 35 points. Monthly Expenses between 95-105 % of Monthly Income => 20 points.
     * Monthly Expenses is more than 105 % of Monthly Income => 0 points.
     */
    function setWfPoint2() {

        if ($this->userExpensePerMonth < (.95 * $this->userIncomePerMonth)) {

            $this->wfPoint2 = 35;
        } elseif (($this->userExpensePerMonth >= (.95 * $this->userIncomePerMonth)) && ($this->userExpensePerMonth <= (1.05 * $this->userIncomePerMonth))) {

            $this->wfPoint2 = 20;
        } elseif ($this->userExpensePerMonth > (1.05 * $this->userIncomePerMonth)) {

            $this->wfPoint2 = 0;
        } else {

            $this->wfPoint2 = 0;
        }
    }  

    /**
     * (3) (% Goal Setting Multiplier)
     *     Add the following:
     *     •    Bank Accounts
     *     •    Brokerage – Money Market Fund/Stable Value Fund/Cash
     *     •    Life Insurance Cash Value.
     *     If they cover at least 3 months of monthly expenses give 100%.
     *     If they cover at least 2 months of monthly expenses give them 75%.
     *     If they cover less than 2 months of monthly expenses give them 50%.
     */
    function setWfPoint3() {

        if ($this->userSumOfGoalSettingAssets > 0 && $this->userSumOfGoalSettingAssets >= ($this->userExpensePerMonth * 3)) {
            $this->wfPoint3 = 1;
        } elseif ($this->userSumOfGoalSettingAssets > 0 && $this->userSumOfGoalSettingAssets >= ($this->userExpensePerMonth * 2)) {
            $this->wfPoint3 = .75;
        } elseif ($this->userSumOfGoalSettingAssets > 0 && $this->userSumOfGoalSettingAssets < ($this->userExpensePerMonth * 2)) {
            $this->wfPoint3 = .5;
        } else {
            $this->wfPoint3 = .5;
        }
    }

    /**
     * (4) + 5 (Goal Setting)
     *  As long as they download/manual entry any asset/debt/insurance they have, they get these points.
     */
    public $isUserEnteredAccount = false;

    function setWfPoint4() {

        if ($this->isUserEnteredAccount) {
            $this->wfPoint4 = 5 * $this->wfPoint3;
        } else {
            $this->wfPoint4 = 0;
        }
    }

    /**
     * (5) + 25 (Cash Flow) (Two Calc XML Algorithms)
     * CALC XML - Restructuring debts
     *  Inputs:
     *  Balance: Question asked in Credit/Loan/Mortgage section
     *  Actual Payment: Question asked in Credit/Loan/Mortgage section
     *  Interest Rate: Question asked in Credit/Loan/Mortgage section
     *  Minimum Payment: Try to get from bank data, or use monthly cost of 2% of balance
     *  Extra Payments: 0
     *  One Time Payments: 0
     *  Debt Ordering: Lowest to Highest Balance
     *  Savings Interest: Risk Tolerance Number
     *
     * Result: # of Years to get out of debt.
     * Give them 12 points if they are going to be out of debt before their final goal.
     *
     * Calc XML - Should I consolidate my personal debt into a new loan?
     * Inputs:
     * Annual Interest Rate: User’s Average %
     * Number of Years: To Pay of Debt, either they inputted it, or we use 7 years as default
     * Credit Card Details: We asked this in Credit Section
     *
     * Result: Consolidate or not. If they do not need to, then they get 13 points.
     */
    //default set 30
    public $maxGoalEndYear = 7;

    function setWfPoint5() {
        // Moved to Scontroller
        $this->restructuringDebtsArr = null;
        $this->personalDebtLoanArr = null;
    }

    /**
     * (6) + 5 (Debt Optimization) If you have 2 or more credit cards,
     *  where the largest debt is not with the smallest rate, then give 0 points. Default is 5.
     */
    public $creditCardFlag = 1;

    function setWfPoint6() {

        if ($this->creditCardFlag == 0) {
            $this->wfPoint6 = 0;
        } elseif ($this->creditCardFlag == 1) {
            $this->wfPoint6 = 5;
        } else {
            $this->wfPoint6 = 5;
        }
    }

    /**
     * (7) + 50 (Funding the Nest) (Depends on Credit Cards and Loans)
     *  Monthly Debt = Loans + Credit Cards + Auto Loans + Other
     * Calculation: 50 * (1 - ((monthly debt costs / monthly gross income) / 0.2)
     * If we don’t have debts information, then we fall back on values found in expenses section.
     *
     */
    public $emiLoanCC = 0;

    function setWfPoint7() {
        // Income always defaults to 5000 and is never 0. If its 0, then theres a bug in the setters for userIncomePerMonth
        //calculation and formauls
        $calVal = 50 * ( 1 - (($this->emiLoanCC / $this->userIncomePerMonth ) / 0.2) );
        $calVal = ($calVal > 50) ? 50 : $calVal;
        $calVal = ($calVal < -50) ? -50 : $calVal;
        $this->wfPoint7 = $calVal;
    }

    /**
     * (8) +5 (Debt Optimization) You get it once you download/manual entry any mortgage information.
     */
    public $mortgageInfo = false;

    function setWfPoint8() {

        if ($this->mortgageInfo) {
            $this->wfPoint8 = 5;
        } else {
            $this->wfPoint8 = 0;
        }
    }

    /**
     *
     * (9) + 50 (Funding the Nest) Depends on Mortgages or Rent
     * Calculation: 50 * (1 - ((monthly housing costs / monthly gross income) / 0.28)
     * We look at mortgage information in debts section. If not there we look at rent information found at expenses section.
     */
    public $rentMortgage = 0;

    function setWfPoint9() {
        $calVal = 50 * ( 1 - (($this->rentMortgage / $this->userIncomePerMonth ) / 0.28) );
        $calVal = ($calVal > 50) ? 50 : $calVal;
        $calVal = ($calVal < -50) ? -50 : $calVal;
        $this->wfPoint9 = $calVal;
    }

    /**
     * (10) + 50 (Funding the Nest)
     * Denominator = Sum up all Assets = Overall Wealth
     * Numerator = Sum up any asset, at the granular level of each stock,
     *      or each mutual fund, etc. that is < 10% of overall wealth
     * Include Insurance Cash Value in this calculation.
     * Do not include educational, vehicle, pension, and social security amounts
     *      in the denominator or numerator.
     * Final Score: 100 * (Numerator / Denominator)
     */
    function setWfPoint10() {
        // Calculated in Scontroller.php
        $this->assetsObj = null;
        $this->insuranceObj = null;
        $this->pertrackObj = null;
    }

    /**
     * (11) + 100 (Funding the Nest)
     * Include Insurance Cash Value in this calculation.
     * Do not include educational, vehicle, pension, and social security amounts in the numerator
     * Net-Worth = Assets – Debts
     * Final Score:
     * When Net-worth is POSITIVE:   (1 - Debt/Assets) * 100
     * When Net-worth is NEGATIVE: (Assets/Debt– 1) * 100
     * This score varies from -100 to 100.
     */
    public $userSumOfDebts = 0;

    function setWfPoint11() {
        $sumOfAssets = $this->userSumOfAssets + $this->userSumOfOtherAssets;
        $netWorth = ($sumOfAssets - $this->userSumOfDebts);

        if ($netWorth > 0 && $sumOfAssets > 0) {
            $this->wfPoint11 = (1 - ($this->userSumOfDebts / $sumOfAssets)) * (100);
        } elseif ($netWorth < 0 && $this->userSumOfDebts > 0) {
            $this->wfPoint11 = (($sumOfAssets / $this->userSumOfDebts) - 1 ) * (100);
        } else {
            $this->wfPoint11 = 0;
        }

        if ($this->wfPoint11 > 100) {
            $this->wfPoint11 = 100;
        } elseif ($this->wfPoint11 < -100) {
            $this->wfPoint11 = -100;
        }
    }

    /**
     * (12) + 250 (Funding the Nest) (Monte Carlo)
     * The Monte Carlo algorithm is run 100 times, using the standard deviation on the risk tolerance factor.
     * If the amount it reaches at the final goal year, covers all must have goals, then they get 1%. Since the
     * algorithm is run 100 times, you can get up to 100 %.
     *
     * Final Score: 250 * percentage of time a simulation result achieved the financial goals in an indicated portion
     * of the simulations
     */
    public $taxableAnnualSavings = 0;
    public $taxDeferredAnnualSavings = 0;
    public $taxFreeAnnualSavings = 0;
    public $startingTaxDeferredBalance = 0;
    public $startingTaxFreeBalance = 0;
    public $riskStdDev = 8.7;
    public $riskMetric = 0.43;
    public $startingTaxFreeBalValue = 0;
    public $annualRetirementIncome = 0;
    public $baseAnnualWithdrawalValue = 0;
    public $tickerRiskValue = 0;

    function setWfPoint12() {
/*          $balance = $this->userSumOfAssets;
          $amountNeeded = $this->retirementAmountDesired;
          $contributions = $this->taxableAnnualSavings + $this->taxDeferredAnnualSavings + $this->taxFreeAnnualSavings;
          $years = $this->yearToRetire;
          $rate = $this->userGrowthRate / 100;

          for ($i = 1; $i <= $years; $i++) {
              $balance = $balance * (1 + $rate) + $contributions;
          }

          $inflation = 0.034;
          for ($i = 1; $i <= $years; $i++) {
              $balance = $balance / (1 + $inflation);
          }
          $max = 250;
          if ($amountNeeded && $amountNeeded > 0) {
              $this->wfPoint12 = $max * ($balance / $amountNeeded);
              $this->wfPoint12 = ($this->wfPoint12 > 0) ? $this->wfPoint12 : 0;
              $this->wfPoint12 = ($this->wfPoint12 < $max) ? $this->wfPoint12 : $max;
          } else {
              $this->wfPoint12 = $max;
          }  */
    }

    function setMonteCarlo() {
        //currently MC is running on the fly
        $this->wfPoint12 = 250 * $this->monteCarlo;
    }

    /**
     * (13) (% Investment Multiplier) (Monte Carlo)
     * (Pertrack)  Using Pertrack Database, calculate the current risk for the user.
     * Run the Monte Carlo algorithm for each risk # between 1 and 10, that is, run it 10 times.
     * See which risk # gives the highest score, this will be the recommended risk.
     * Calculation: % = 1 – |recommended risk - current risk| / recommended risk
     */
    public $currentRisk = 0;

    function setWfPoint13() {
        $currentStdDev = $this->tickerRiskValue;
        $userStdDev = $this->riskStdDev;
        $denominator = ($currentStdDev > $userStdDev) ? $currentStdDev : $userStdDev;

        if (0.9 * $currentStdDev <= $userStdDev && 1.1 * $currentStdDev >= $userStdDev) {
            $this->wfPoint13 = 1;
        } else {
            $val1 = (1 - abs($userStdDev - 0.9 * $currentStdDev) / $denominator );
            $val2 = (1 - abs($userStdDev - 1.1 * $currentStdDev) / $denominator );

            $this->wfPoint13 = ($val1 > $val2) ? $val1 : $val2;
            $this->wfPoint13 = ($this->wfPoint13 > 0) ? $this->wfPoint13 : 0;
            $this->wfPoint13 = ($this->wfPoint13 < 1) ? $this->wfPoint13 : 1;
        }
    }

    /**
     * (14) + 50 (Funding the Nest)
     * Liquid assets are:
     * •    Insurance Cash Value
     * •    Brokerage
     * •    IRA,
     * •    Company Sponsored Plans
     * •    Bank accounts
     * Do not include educational, vehicle, pension, social security, and property you live in amounts
     * Calculation: 50 * (assets that are liquid / total assets)
     */
    public $numeratorP14;

    function setWfPoint14() {


        $nrSum = $this->numeratorP14;
        $drSum = $this->userSumOfAssets;

        #print_r($nrSum);echo 'foo';
        #print_r($drSum);die;

        if ($drSum > 0) {
            $this->wfPoint14 = 50 * ($nrSum / $drSum);
            if ($this->wfPoint14 > 50) {
                $this->wfPoint14 = 50;
            }
        } else {
            $this->wfPoint14 = 0;
        }
    }

    /**
     * Are beneﬁciaries designated on this account and are they accurate?
     * (15) + 12 (Estate Planning)
     * As long as users have any account with beneficiaries set up they get these points.
     */
    public $beneAssigned = false;

    function setWfPoint15() {

        if ($this->beneAssigned) {
            $this->wfPoint15 = 12;
        } else {
            $this->wfPoint15 = 0;
        }
    }

    /**
     * If Not Retired:  How much do you contribute?
     * (16) + 5 (Investment) If any of the user’s retirement, education,
     * or investment account has contributions set up and greater than 0,
     * then they get this point.
     */
    public $investmentFactor = 0;

    function setWfPoint16() {
        if (!$this->retired && $this->investmentFactor > 0) {
            $this->wfPoint16 = 5 * $this->wfPoint13;
        } else {
            $this->wfPoint16 = 0;
        }
    }

    /**
     * (17) + 0 (Retirement Planning)
     * If any retirement accounts have monthly contributions, then they get these points.
     */
    public $retirementMonthlyContribution = false;

    function setWfPoint17() {
        if ($this->retirementMonthlyContribution) {
            $this->wfPoint17 = 0;
        } else {
            $this->wfPoint17 = 0;
        }
    }

    /**
     * (18) + 0 (Retirement Planning) (CalcXML Algorithm) (depends on all retirement accounts)
     */
    public $retirementPlanAmt = 0;
    public $investmentContriPerMonth = 0;
    public $userInitialBalance = 0;
    public $retirementAmountDesired = 0;
    public $monthlyIncome = 0;

    function setWfPoint18() {

        # Call the required calc xml calculator

        $this->wfPoint18 = 0;
    }

    /**
     * If not Retired: Amount your employer contributes on your behalf?
     * (19) + 8 (Retirement Planning)
     *   If they have a value > 0, then they get these points
     */
    public $userRetirementAmountContributionByEmployer = 0;

    function setWfPoint19() {
        if (!$this->retired && ($this->userRetirementAmountContributionByEmployer > 0 || $this->userPensionDetails > 0)) {
            $this->wfPoint19 = 8;
        } else {
            $this->wfPoint19 = 0;
        }
    }

    /**
     * If Retired: How much do you take out per month?
     * (20) + 0 (Retirement Planning)
     * They get these points for answering the question. Monthly cost of retirement,
     * which implies are they living within their means.
     *
     */
    function setWfPoint20() {
        if ($this->retired && $this->permonthWithdrawal > 0) {
            $this->wfPoint20 = 0;
        } else {
            $this->wfPoint20 = 0;
        }
    }

    /**
     * If Retired: What age do you think you will live to?
     * (21) + 0 (Retirement Planning) They get these points for life expectancy considerations.
     * Life Expectancy Stats: http://www.ssa.gov/oact/STATS/table4c6.html
     * This can be found to edit under the assumptions section.
     *
     */
    public $enteredAge = false;

    function setWfPoint21() {
        if ($this->retired && $this->enteredAge) {
            $this->wfPoint21 = 0;
        } else {
            $this->wfPoint21 = 0;
        }
    }

    /**
     * If Retired: (22) + 20 (Retirement Planning) Based on monthly withdrawal,
     * compare with total assets to get a percentage. If they haven’t given us
     * withdrawal amounts, use the expenses amounts.
     *  Compare this with Sustainable Withdrawal Rates. If they are doing well,
     * then give them 20 points.
     * Calculation: Sustainable Withdrawal Rate >= Actual Withdrawal Rates
     * Added by Thayub:
     * ----------------
     * (Amount withdrawn/yr) / Total Assets <= Sustainable rates
     *
     */
    public $sustainablewithdrawalpercent = 4.5;

    function setWfPoint22() {
        if ($this->retired && $this->userSumOfAssets > 0) {
            $amtSpent = $this->permonthWithdrawal * 12;
            $rate = 100 * ($amtSpent / $this->userSumOfAssets);

            if ($rate <= $this->sustainablewithdrawalpercent) {
                $this->wfPoint22 = 20;
            } else {
                $this->wfPoint22 = 0;
            }
        } else {
            $this->wfPoint22 = 0;
        }
    }

    /**
     * (23) + 5 (Investment)
     * If any of the user’s retirement, education,
     * or investment account has at least three different types
     * of funds (stocks vs mutual funds vs ETFs, etc) set up, then they get this point.
     * UPDATE: August 27, 2013: #23 and #26 are removed.
     */
    public $stocksMFETFs = false;

    function setWfPoint23() {
        if ($this->stocksMFETFs) {
            $this->wfPoint23 = 0 * $this->wfPoint13;
        } else {
            $this->wfPoint23 = 0;
        }
    }

    /**
     * HEALTH Insurance - BKG calculations
     * (24) + 30 (Protection Planning)
     * 1/2 of Points decay each year for 2 years => 30, 0, -30.  After user signifies
     * that they've reviewed health insurance, they get full points
     * again and decaying process starts over.
     * at least one comprehensive => 30, other 15 for having insurance. 0 for no insurance
     */
    public $insuranceReviewYear24 = 0;
    public $healthInsuranceType = '';

    function setWfPoint24() {

        // Check type
        if ($this->healthInsuranceType == 'Comprehensive') {
            $this->wfPoint24 = 30;
        } elseif ($this->healthInsuranceType == 'Limited') {
            $this->wfPoint24 = 15;
        } else {
            $this->wfPoint24 = 0;
        }
        // Degrade points
        if ($this->insuranceReviewYear24 > 0) {
            $currentYear = date('Y');
            $yearDifference = $currentYear - $this->insuranceReviewYear24;

            if ($yearDifference > 2 && $this->wfPoint24 > -30) {
                $this->wfPoint24 = -30;
            } elseif ($yearDifference > 1 && $yearDifference <= 2 && $this->wfPoint24 > 0) {
                $this->wfPoint24 = 0;
            } elseif ($yearDifference <= 1 && $this->wfPoint24 > 30) {
                $this->wfPoint24 = 30;
            }
        }
    }

    /**
     *  (25) + 10 (Investment)
     *  If any of the user’s retirement, education, or investment account has
     * non-correlated/alternatives as a portion of their overall portfolio, then they get this point.
     * Should be using PERTRACK
     */
    //pass the ticker
    public $nonCoreelatedTicker = false;

    function setWfPoint25() {
        if ($this->nonCoreelatedTicker) {
            $this->wfPoint25 = 10 * $this->wfPoint13;
        } else {
            $this->wfPoint25 = 0;
        }
    }

    /**
     * (26) + 10 (Investment)
     * If any of the user’s retirement, education, or investment account has
     * contributed in the last 90 days (using Transaction Lists), then they get this point.
     *      If we don't get transaction lists,
     * then if they answer the question: "How much do you contribute?" and it's
     * greater than 0, then they get these points.
     * UPDATE: August 27, 2013: #23 and #26 are removed.
     */
    public $point26cond = 0;

    function setWfPoint26() {

        if ($this->point26cond > 0) {
            $this->wfPoint26 = 0 * $this->wfPoint13;
        } else {
            $this->wfPoint26 = 0;
        }
    }

    /**
     * If Not Retired: (27) + 7 (Retirement Planning)
     * They get 7 points for entering their pension details.
     */
    public $userPensionDetails = 0;
    public $userSocialSecurityDetails = 0;

    function setWfPoint27() {
        if (!$this->retired && ($this->userPensionDetails > 0 || $this->userSocialSecurityDetails > 0)) {

            $this->wfPoint27 = 7;
        } else {
            $this->wfPoint27 = 0;
        }
    }

    /**
     * (28) + 16 (Goal Setting)
     * As long as they enter a risk #, they get these points.
     */
    // Compare to Risk table to get growth rate%, 7.0 default
    public $userRiskValue = 0;
    public $userGrowthRate = 7.0;

    function setWfPoint28() {

        if ($this->userRiskValue > 0) {

            $this->wfPoint28 = 16 * $this->wfPoint3;
        } else {

            $this->wfPoint28 = 0;
        }
    }

    /**
     * CALC XML Integration
     * ins01 - How much life insurance do I need?
     * (29) + 24 (Protection Planning) (CalcXML)
     * Current annual income ($)
     * Spouse's annual income (if applicable) ($)
     * Spouse's current age (if applicable)
     * Spouse's desired retirement age (if applicable)
     *
     * Investment return: (%) RISK
     * Anticipated inflation rate: (%) 3.4%
     * Funeral expenses ($): $6,560
     * Final expenses ($): 0
     * Mortgage balance ($) (from Profile Section)
     * Other debts ($) (from Profile Section)
     * Desired annual income needs
     *          (typically 70-80% of current combined income) ($): 80% of
     *          current combined income
     * Number of years income is needed = Default is average life expectancy unless they edit it in assumptions.
     * Life Expectancy Stats: http://www.ssa.gov/oact/STATS/table4c6.html
     * College needs ($) (from Goals Section and CalcXML Results of college saving calc)
     * Investment assets ($)(from Profile Section)
     * Existing life insurance ($)(from Profile Section)
     * Include social security benefits? YES
     * Age of oldest child under 18 (from Profile Section)
     * Age of second child under 18 (from Profile Section)
     * Age of third child under 18 (from Profile Section)
     * Age of fourth child under 18 (from Profile Section)
     * Final Score: 24 * (amount of current life insurance coverage / coverage need to meet financial goals)
     * 1/4 of Points Decay Each Year, for 4 years => 24, 12, 0, -12, -24.  After user signifies
     * that they've reviewed life insurance plan, they get increased points again and decaying process starts over.
     */
    public $LifeInsurance = 0;
    public $spouseAge = 0;
    public $spouseLifeEC = 82;
    public $spouseRetAge = 0;
    public $mortgageBalance = 0;
    public $otherDebts = 0;
    public $collegeAmount = 0;
    public $child1Age = 0;
    public $child2Age = 0;
    public $child3Age = 0;
    public $child4Age = 0;
    public $insuranceCashValue = 0;
    public $insuranceReviewYear29 = 0;
    public $insuranceNeededActionStep = 0;
    public $maxPoint29 = 24;

    function setWfPoint29() {
        // Moved to Scontroller
    }

    /**
     * (30) + 18 (Protection Planning)
     * Spouse's Monthly after-tax income ($): Personal Section
     * Monthly Investment income ($): 4% of total investments not allocated to another goal) / 12
     * Monthly Expenses: Personal section
     * Monthly Coverage needed: Monthly expenses - spouses income - investment income
     * Monthly Current Disability Coverage: % of Annual Income Coverage / 12        (75% is max)
     * Final Score: 18 * (amount of current disability insurance coverage / coverage needed to meet financial goals)
     * 1/3 of Points Decay Each Year, for 3 years =>18, 6, -6, -18.
     * After user signifies that they've reviewed beneficiaries, they get full points again and decaying process starts over.
     *
     */
    public $spouseIncome = 0;
    public $investmentIncome = 0;
    public $grossIncome = 5000;
    public $incomeCoverage = 0;
    public $insuranceReviewYear30 = 0;
    public $disainsuranceNeededActionStep = 0;
    public $maxPoint30 = 18;

    function setWfPoint30() {
        $investmentIncome = (($this->userSumOfAssets * 0.04) / 12) + $this->investmentIncome;
        $monthlyCoverage = ($this->userExpensePerMonth) - ($this->spouseIncome + $investmentIncome);
        $incomeCoverage = ($this->incomeCoverage > 0.75) ? 0.75 : $this->incomeCoverage;

        if ($monthlyCoverage > 0.75 * $this->grossIncome) {
            $this->disainsuranceNeededActionStep = (0.75 * $this->grossIncome) - ($incomeCoverage * $this->grossIncome);
        } else {
            $this->disainsuranceNeededActionStep = $monthlyCoverage - ($incomeCoverage * $this->grossIncome);
        }
        if ($this->disainsuranceNeededActionStep < 0) {
            $this->disainsuranceNeededActionStep = 0;
        }

        if ($monthlyCoverage > 0) {
            $this->wfPoint30 = 18 * (($incomeCoverage * $this->grossIncome) / $monthlyCoverage);
            $this->wfPoint30 = ($this->wfPoint30 > 18) ? 18 : $this->wfPoint30;
        } else {
            // monthly coverage needed is less than or equal to 0. Means they get full points
            $this->wfPoint30 = 18;
        }

        if ($this->insuranceReviewYear30 > 0) {
            $currentYear = date('Y');
            $yearDifference = $currentYear - $this->insuranceReviewYear30;

            if ($yearDifference > 4 && $this->wfPoint30 > -18) {
                $this->wfPoint30 = -18;
            } elseif ($yearDifference > 3 && $yearDifference <= 4 && $this->wfPoint30 > -9) {
                $this->wfPoint30 = -9;
            } elseif ($yearDifference > 2 && $yearDifference <= 3 && $this->wfPoint30 > 0) {
                $this->wfPoint30 = 0;
            } elseif ($yearDifference > 1 && $yearDifference <= 2 && $this->wfPoint30 > 9) {
                $this->wfPoint30 = 9;
            } elseif ($yearDifference <= 1 && $this->wfPoint30 > 18) {
                $this->wfPoint30 = 18;
            }
        }
    }

    /**
     * (31) + 16 (Protection Planning) (CalcXML)
     */
    public $dailyLongTermAmount = 0;
    public $insuranceReviewYear31 = 0;
    public $maxPoint31 = 16;

    function setWfPoint31() {
        if ($this->userCurrentAge < 50) {
            $this->wfPoint31 = 16;
        } else {
            $this->wfPoint31 = 16 * ($this->dailyLongTermAmount / 205);
            $this->wfPoint31 = ($this->wfPoint31 > 16) ? 16 : $this->wfPoint31;
        }

        if ($this->insuranceReviewYear31 > 0) {
            $currentYear = date('Y');
            $yearDifference = $currentYear - $this->insuranceReviewYear31;

            if ($yearDifference > 4 && $this->wfPoint31 > -16) {
                $this->wfPoint31 = -16;
            } elseif ($yearDifference > 3 && $yearDifference <= 4 && $this->wfPoint31 > -8) {
                $this->wfPoint31 = -8;
            } elseif ($yearDifference > 2 && $yearDifference <= 3 && $this->wfPoint31 > 0) {
                $this->wfPoint31 = 0;
            } elseif ($yearDifference > 1 && $yearDifference <= 2 && $this->wfPoint31 > 8) {
                $this->wfPoint31 = 8;
            } elseif ($yearDifference <= 1 && $this->wfPoint31 > 16) {
                $this->wfPoint31 = 16;
            }
        }
    }

    /**
     * (32) + 8 (Protection Planning)
     * 1/2 of Points decay each year for 2 years => 8, 0, -8.  After user signifies
     * that they've reviewed home owner’s/renter’s insurance, they get full points again and decaying process starts over
     */
    public $insuranceReviewYear32 = 0;
    public $hasHomeInsurance = false;

    function setWfPoint32() {

        if ($this->hasHomeInsurance) {
            $this->wfPoint32 = 8;
        } else {
            $this->wfPoint32 = 0;
        }

        $currentYear = date('Y');
        if ($this->insuranceReviewYear32 > 0) {
            $yearDifference = $currentYear - $this->insuranceReviewYear32;

            if ($yearDifference > 2 && $this->wfPoint32 > -8) {
                $this->wfPoint32 = -8;
            } elseif ($yearDifference > 1 && $yearDifference <= 2 && $this->wfPoint32 > 0) {
                $this->wfPoint32 = 0;
            } elseif ($yearDifference <= 1 && $this->wfPoint32 > 8) {
                $this->wfPoint32 = 8;
            }
        }
    }

    /**
     * (33) + 8 (Protection Planning)
     * 1/2 of Points decay each year for 2 years => 8, 0, -8.
     * After user signifies that they've reviewed vehicle insurance,
     * they get full points again and decaying process starts over.
     */
    public $insuranceReviewYear33 = 0;
    public $hasVehicleInsurance = false;

    function setWfPoint33() {

        if ($this->hasVehicleInsurance) {
            $this->wfPoint33 = 8;
        } else {
            $this->wfPoint33 = 0;
        }
        $currentYear = date('Y');

        if ($this->insuranceReviewYear33 > 0) {
            $yearDifference = $currentYear - $this->insuranceReviewYear33;

            if ($yearDifference > 2 && $this->wfPoint33 > -8) {
                $this->wfPoint33 = -8;
            } elseif ($yearDifference > 1 && $yearDifference <= 2 && $this->wfPoint33 > 0) {
                $this->wfPoint33 = 0;
            } elseif ($yearDifference <= 1 && $this->wfPoint33 > 8) {
                $this->wfPoint33 = 8;
            }
        }
    }

    /**
     * Knowledge and optimization of umbrella insurance
     * (34) + 6 (Protection Planning)
     * They get these points if they have umbrella insurance. 
     */
    public $hasUmbrellaInsurance = false;

    function setWfPoint34() {

        if ($this->hasUmbrellaInsurance) {
            $this->wfPoint34 = 6;
        } else {
            $this->wfPoint34 = 0;
        }
    }

    /**
     * Do you get money back or do you have to pay more?
     * (35) + 4 (Tax) They get the points when they answer the question.
     */
    public $doGetMoneyBackPayMore = false;

    function setWfPoint35() {

        if ($this->doGetMoneyBackPayMore) {
            $this->wfPoint35 = 4;
        } else {
            $this->wfPoint35 = 0;
        }
    }

    /**
     * What tax bracket are you in?
     * (36)+ 5 (Investment) When we get this tax bracket range, we adjust it for the calc xml algorithms that need this field
     * If no answer, we can assume 15% (http://www.bankrate.com/finance/taxes/2013-tax-bracket-rates.aspx)
     */
    public $taxBracketUser = 0;

    function setWfPoint36() {

        if ($this->taxBracketUser > 0) {
            $this->wfPoint36 = 5 * $this->wfPoint13;
        } else {
            $this->wfPoint36 = 0;
        }
    }

    /**
     * Are your �retirement plan�contributions deductible?
     * (37) + 5 (Tax)
     * They get the points when they answer the question.
     */
    public $userRetirementContributionDeductible = false;

    function setWfPoint37() {
        if ($this->userRetirementContributionDeductible) {
            $this->wfPoint37 = 5;
        } else {
            $this->wfPoint37 = 0;
        }
    }

    /**
     * (38)  +50 (Profile Points)
     * About you                : 10
     * Connecting Accounts      : 10
     * Income                   : 5
     * Expense                  : 5
     * Debts                    : 10
     * Assets                   : 10
     * Insurance                : 7.5
     * Risk Tole                : 7.5
     * Miscellaneous - Taxes    : 7.5
     * Miscellaneous - EP       : 7.5
     * Miscellaneous - More     : 7.5
     * Goals                    : 12.5
     * Total = Sum of these / 2 = out of 50
     */
    public $userProfilePoints_aboutyou = 5;
    public $userProfilePoints_connectAccount = 0;
    public $userProfilePoints_income = 0;
    public $userProfilePoints_expense = 0;
    public $userProfilePoints_debts = 0;
    public $userProfilePoints_assets = 0;
    public $userProfilePoints_insurance = 0;
    public $userProfilePoints_userRisk = 0;
    public $userProfilePoints_misc = 0;
    public $userProfilePoints_goals = 0;
    public $userProfilePoints_others = 0;
    public $wfPoint38_1 = 0;
    public $wfPoint38_2 = 0;
    public $wfPoint38_3 = 0;
    public $wfPoint38_4 = 0;
    public $wfPoint38_5 = 0;
    public $wfPoint38_6 = 0;
    public $wfPoint38_7 = 0;
    public $wfPoint38_8 = 0;
    public $wfPoint38_9 = 0;
    public $wfPoint38_10 = 0;

    function setWfPoint38() {
        //about you
        $this->wfPoint38_1 = $this->userProfilePoints_aboutyou;
        //Connecting Account Point

        if ($this->userProfilePoints_connectAccount > 0) {
            $this->wfPoint38_2 = 10;
        } else {
            $this->wfPoint38_2 = 0;
        }
        //income
        if ($this->userProfilePoints_income > 0) {
            $this->wfPoint38_3 = 5;
        } else {
            $this->wfPoint38_3 = 0;
        }
        //expense
        if ($this->userProfilePoints_expense > 0) {
            $this->wfPoint38_4 = 5;
        } else {
            $this->wfPoint38_4 = 0;
        }
        //Debts
        if ($this->userProfilePoints_debts > 0) {
            $this->wfPoint38_5 = 10;
        } else {
            $this->wfPoint38_5 = 0;
        }
        //assets
        $this->wfPoint38_6 = $this->userProfilePoints_assets;
        //Insurance
        if ($this->userProfilePoints_insurance > 0) {
            $this->wfPoint38_7 = 7.5;
        } else {
            $this->wfPoint38_7 = 0;
        }
        //risk
        if ($this->userProfilePoints_userRisk > 0) {
            $this->wfPoint38_8 = 7.5;
        } else {
            $this->wfPoint38_8 = 0;
        }
        //misc
        $this->wfPoint38_9 = $this->userProfilePoints_misc;
        //goal
        if ($this->userProfilePoints_goals > 0) {
            $this->wfPoint38_10 = 12.5;
        } else {
            $this->wfPoint38_10 = 0;
        }

        //final calculation
        $this->wfPoint38 = ($this->wfPoint38_1 + $this->wfPoint38_2 + $this->wfPoint38_3 + $this->wfPoint38_4 + $this->wfPoint38_5 + $this->wfPoint38_6 + $this->wfPoint38_7 + $this->wfPoint38_8 + $this->wfPoint38_9 + $this->wfPoint38_10) / 2;
    }

    /**
     * Do you have a will or trust ?. handled upon your death?
     * (39) + 20 (Estate Planning)
     * 1/4 of Points Each Year, for 4 years => 20, 10, 0, -10, -20.  After user signifies that they've reviewed estate planning documents, they get full points again and decaying process starts over.
     * If Yes, ask: What year was it last reviewed and updated?
     */
    public $willOrTrust = false;
    public $willTrustReviwed = 0;

    function setWfPoint39() {
        if ($this->willOrTrust) {
            //apply degradation point
            //$willTrustReviwed
            $this->wfPoint39 = 20;
            //connected to next point
            if ($this->willTrustReviwed > 0) {
                #self::setWfPoint40($this->willTrustReviwed);
                $currentYear = date('Y');
                $difference = ($currentYear - $this->reviewYearP40);
                if ($difference > 0)
                    $difference--; // this year and last year are considered no degradation
                $max = 20;
                $diff = 10;
                for ($i = 0; $i < 4; $i++) {
                    if ($difference == $i) {
                        break;
                    }
                    $max = $max - $diff;
                }
                $this->wfPoint39 = $max;
            }
        } else {
            $this->wfPoint39 = 0;
        }
    }

    /**
     * CONNECTED TO ABOVE POINT if year is less thatn 5 years give full point
     * Do you have a will or trust �. handled upon your death?
     * (39) + 20 (Estate Planning)
     * 1/5 of Points Each Year, for 5 years => 20, 12, 4, -4, -12, -20.
     * After user signifies that they've reviewed estate planning documents,
     * they get full points again and decaying process starts over.
     * If Yes, ask: What year was it last reviewed and updated?
     * (40) + 5 (Estate Planning)
     * If they have a will ask this question, and if it was updated within the last 5 years, give them the points.
     */
    public $reviewYearP40 = 0;

    function setWfPoint40() {

        $currentYear = date('Y');
        $difference = ($currentYear - $this->reviewYearP40);

        if ($this->willTrustReviwed > 0 && $difference <= 5) {
            $this->wfPoint40 = 5;
        } else {
            $this->wfPoint40 = 0;
        }
    }

    /**
     * Do you have an information list of where you keep your hidden assets, passwords, keys?
     * If yes, ask: Have you told the right person the location of this so it can
     * be used after your death? (41) + 4 (Estate Planning) They get the points when they answer yes to both questions.
     */
    public $informationListOfHiddenAsset = false;

    function setWfPoint41() {

        if ($this->informationListOfHiddenAsset) {
            $this->wfPoint41 = 4;
        } else {
            $this->wfPoint41 = 0;
        }
    }

    /**
     * Do you own anything that needs to liquidated upon your death (i.e. stock on your business,
     *  investment real estate, etc)?
     * (42) + 3 (Estate Planning)  They get the points when they answer the question.
     */
    public $liquidedOnDeath = false;

    function setWfPoint42() {

        if ($this->liquidedOnDeath) {
            $this->wfPoint42 = 3;
        } else {
            $this->wfPoint42 = 0;
        }
    }

    /**
     * Have you planned for your inability to work from disability or injuries
     *  during a time frame where others may need your income?
     * (43) + 3 (Estate Planning) They get the points when they answer yes to the question.
     */
    public $plannedForInability = false;

    function setWfPoint43() {
        //
        if ($this->plannedForInability) {
            $this->wfPoint43 = 3;
        } else {
            $this->wfPoint43 = 0;
        }
    }

    /**
     * Do you manually move money into savings accounts or are funds transferred automatically?
     * (44) + 5 (Investment) They get the points if they answer the question as automatically.
     *
     */
    public $manualOrAutomatic = false;

    function setWfPoint44() {
        if ($this->manualOrAutomatic) {
            $this->wfPoint44 = 5 * $this->wfPoint13;
        } else {
            $this->wfPoint44 = 0;
        }
    }

    /**
     * Do you have your investments set to automatically re-balance?
     * (45) + 5 (Investment)
     * They get the points of they have set it to automatically.
     */
    public $investmentAutomatically = false;

    function setWfPoint45() {

        if ($this->investmentAutomatically) {
            $this->wfPoint45 = 5 * $this->wfPoint13;
        } else {
            $this->wfPoint45 = 0;
        }
    }

    /**
     * Are you automatically investing into a retirement or brokerage account?
     * (46) + 5 (Cash Flow)
     * They get the points if they have answered automatically.
     */
    public $investAutoRetBrokerage = false;

    function setWfPoint46() {
        if ($this->investAutoRetBrokerage) {
            $this->wfPoint46 = 5;
        } else {
            $this->wfPoint46 = 0;
        }
    }

    /**
     * Have you considered how liquid (i.e. easy access to full value) your assets are?
     * (47) + 5 (Investment) If they answer yes, give them points.
     */
    public $liquidAssets = false;

    function setWfPoint47() {
        if ($this->liquidAssets) {
            $this->wfPoint47 = 5 * $this->wfPoint13;
        } else {
            $this->wfPoint47 = 0;
        }
    }

    /**
     * Do you currently give to charity or do you plan to upon your death?
     * (48) + 3 (Estate Planning) They get the points if they answer the question as YES.
     */
    public $charityPlanDeath = false;

    function setWfPoint48() {
        if ($this->charityPlanDeath) {
            $this->wfPoint48 = 3;
        } else {
            $this->wfPoint48 = 0;
        }
    }

    /**
     * Enter your approximate credit score, if you know it.
     * (49) + 7 (Debt Optimization) They get the points if they answer the question and score > 0.
     */
    public $creditScoreApprox = 0;

    function setWfPoint49() {

        if ($this->creditScoreApprox > 0) {
            $this->wfPoint49 = 7;
        } else {
            $this->wfPoint49 = 0;
        }
    }

    /**
     * (50) + 50 (Learning Center) They get the points if they watch/read at least 10 videos/articles in last three months.
     *
     */
    public $mediaCount = 0;
    public $oldestMediaDate = null;

    function setWfPoint50() {
        $this->mediaCount = ($this->mediaCount > 10) ? 10 : $this->mediaCount;
        $this->mediaCount = ($this->mediaCount < 0) ? 0 : $this->mediaCount;
        $this->wfPoint50 = 5 * $this->mediaCount;
    }

    /**
     * (58) + 10 (Goal Setting) (on First Goal Entry)
     * As long as they have at least one goal entry entered, they will get these points.
     *
     */
    public $firstGoalEntryCheck = false;

    function setWfPoint58() {

        if ($this->firstGoalEntryCheck) {
            $this->wfPoint58 = 10 * $this->wfPoint3;
        } else {
            $this->wfPoint58 = 0;
        }
    }

    /**
     * (59) + 8 (Investment) (on First Goal Entry)
     * As long as they have at least one goal entry entered, they will get these points.
     *
     */
    function setWfPoint59() {

        if ($this->firstGoalEntryCheck) {

            $this->wfPoint59 = 8 * $this->wfPoint13;
        } else {

            $this->wfPoint59 = 0;
        }
    }

    /**
     * fincally calculate the total score
     *
     * @return type
     */
    function updateScore() {
        //adding up all the score
        // 3 and 13 are multipliers and should not be in calculation
        // 51-57, 60, 61 was deleted => points merged with 50
        $score = round($this->wfPoint1 + $this->wfPoint2 + $this->wfPoint4 +
                $this->wfPoint5 + $this->wfPoint6 + $this->wfPoint7 + $this->wfPoint8 +
                $this->wfPoint9 + $this->wfPoint10 + $this->wfPoint11 + $this->wfPoint12 +
                $this->wfPoint14 + $this->wfPoint15 + $this->wfPoint16 +
                $this->wfPoint17 + $this->wfPoint18 + $this->wfPoint19 + $this->wfPoint20 +
                $this->wfPoint21 + $this->wfPoint22 + $this->wfPoint23 + $this->wfPoint24 +
                $this->wfPoint25 + $this->wfPoint26 + $this->wfPoint27 + $this->wfPoint28 +
                $this->wfPoint29 + $this->wfPoint30 + $this->wfPoint31 + $this->wfPoint32 +
                $this->wfPoint33 + $this->wfPoint34 + $this->wfPoint35 + $this->wfPoint36 +
                $this->wfPoint37 + $this->wfPoint38 + $this->wfPoint39 + $this->wfPoint40 +
                $this->wfPoint41 + $this->wfPoint42 + $this->wfPoint43 + $this->wfPoint44 +
                $this->wfPoint45 + $this->wfPoint46 + $this->wfPoint47 + $this->wfPoint48 +
                $this->wfPoint49 + $this->wfPoint50 + $this->wfPoint58 + $this->wfPoint59);
        if ($score < 0) {
            $score = 0;
        }
        return $score;
    }

    function updateScoreforAS($points) {
        $setPoints = "set" . $points;
        $caseSenstivePoints = preg_replace('/W/', 'w', $points); // direct using of Points doesn't works due to objects case sensitive
        $this->$setPoints();
    }

}

?>