<?php

/* * ********************************************************************
 * Filename: MonteCarlo.php
 * Folder: components
 * Description: Interaction with the Monte Carlo service
 * @author Dan Tormey
 * @copyright (c) 2012 - 2014
 * ******************************************************************** */

//include nu soap client
require_once(realpath(dirname(__FILE__) . '/../lib/nusoap/nusoap.php'));
require_once(realpath(dirname(__FILE__) . '/../helpers/montecarlo/YearlyInputs.php'));
require_once(realpath(dirname(__FILE__) . '/../helpers/montecarlo/YearlyOutputs.php'));
require_once(realpath(dirname(__FILE__) . '/../helpers/montecarlo/WithdrawalDetails.php'));
require_once(realpath(dirname(__FILE__) . '/../helpers/montecarlo/ComputeFunding.php'));
require_once(realpath(dirname(__FILE__) . '/../helpers/montecarlo/Statistical.php'));
require_once(realpath(dirname(__FILE__) . '/../helpers/montecarlo/mersenne_twister/mersenne_twister.php'));

use mersenne_twister\twister;

class MonteCarlo extends CApplicationComponent {

    public $yearsInPlan;
    public $simIterations;
    public $currentAge;
    public $retirementStartYear;
    public $iraWithdrawalStartYear;
    public $earlyIraWithdrawalPenalty;
    public $spendingPolicy;
    public $spendingPolicySensitivity;
    public $spendingPercentFloor;
    public $spendingPercentCeiling;
    public $gsMode;
    public $gsTargetProbability;
    public $btwTriggerPercent;
    public $btwDuration;
    public $btwMaxYear;
    public $RMDStartYear;
    private $rmdPayoutTable = array(27.4, 26.5, 25.6, 24.7, 23.8, 22.9, 22, 21.2, 20.3,
    19.5, 18.7, 17.9, 17.1, 16.3, 15.5, 14.8, 14.1, 13.4, 12.7, 12, 11.4, 10.8, 10.2,
    9.6, 9.1, 8.6, 8.1, 7.6, 7.1, 6.7, 6.3, 5.9, 5.5, 5.2, 4.9, 4.5, 4.2, 3.9, 3.7, 3.4,
    3.1, 2.9, 2.6, 2.4, 2.1, 1.9);
    public $swapWithdrawalOrder = true;
    public $startingTaxableBalance;
    public $startingTaxDeferredBalance;
    public $startingTaxFreeBalance;
    public $baseAnnualWithdrawal;
    public $yearlyInputsArr = array();
    public $probabilityOfSuccess;
    public $probabilityOfBackToWork;
    public $lowestMedianPPMaintained;
    public $highestMedianPPMaintained;
    public $averageShortfallPercent;
    public $iraPenaltyPaid;
    public $yearlyOutputsArr = array();
    public $taxableBalance;
    public $taxDeferredBalance;
    public $taxFreeBalance;
    public $withdrawalDetailsObj = array();
    public $extraTaxFreeIncome;
    public $extraTaxFreeIncomePension = array();
    public $extraTaxFreeIncomeSocialSecurity;
    public $extraTaxableIncome;
    // Monte Carlo Rates
    public $riskReturn;
    public $riskStdDev;
    public $yearlyReturn;
    public $inflationRate;
    public $inflationStdDev;
    public $investmentTaxRate;
    public $incomeTaxRate;
    public $portfolioBalanceAtRetirement;
    public $pensionPaymentAtRetirement = 0;
    public $socialSecurityPaymentAtRetirement = 0;
    public $returnType = "";


    /**
     *
     */
    function computeCompoundRate($rate, $years) {
        $product = $rate;
        for ($i = 2; $i <= $years; $i++) {
            $product += pow($rate, $i);
        }
        return $product;
    }


    /**
     * Simulation rates.
     */
    function addPlanRateInfo($rateType, $startYear, $endYear, $rate) {
        if ($startYear < 1) {
            $startYear = 1;
        }
        if ($endYear > 100) {
            $endYear = 100;
        }

        switch ($rateType) {
            case 1: // income tax rate, based on selected bracket on Miscellaneous form.
                for ($year = $startYear; $year <= $endYear; $year++) {
                    $yearlyInputs = isset($this->yearlyInputsArr[$year]) ? $this->yearlyInputsArr[$year] : new YearlyInputs();

                    $yearlyInputs->incomeTaxRate = $rate;
                    $yearlyInputs->compoundedIncomeTaxRate = self::computeCompoundRate($rate, $endYear - $startYear);
                    $yearlyInputs->compoundedTaxAndIraPenaltyRate = self::computeCompoundRate($rate + $this->earlyIraWithdrawalPenalty, $endYear - $startYear);
                    $this->yearlyInputsArr[$year] = $yearlyInputs;
                }
                break;
            case 2: // investment tax rate, 15% constant
                for ($year = $startYear; $year <= $endYear; $year++) {
                    $yearlyInputs = isset($this->yearlyInputsArr[$year]) ? $this->yearlyInputsArr[$year] : new YearlyInputs();
                    $yearlyInputs->investmentTaxRate = $rate;
                    $this->yearlyInputsArr[$year] = $yearlyInputs;
                }
                break;
            case 3: // inflation rate, 3.4% constant
                for ($year = $startYear; $year <= $endYear; $year++) {
                    $yearlyInputs = isset($this->yearlyInputsArr[$year]) ? $this->yearlyInputsArr[$year] : new YearlyInputs();
                    $yearlyInputs->inflationAvg = $rate;
                    $this->yearlyInputsArr[$year] = $yearlyInputs;
                }
                break;
            case 4: // inflation standard deviation, not considered in flexscore system so set to 0.
                for ($year = $startYear; $year <= $endYear; $year++) {
                    $yearlyInputs = isset($this->yearlyInputsArr[$year]) ? $this->yearlyInputsArr[$year] : new YearlyInputs();
                    $yearlyInputs->inflationStdev = $rate;
                    $this->yearlyInputsArr[$year] = $yearlyInputs;
                }
                break;
            case 5: // initial return rate to be randomized each year.
                for ($year = $startYear; $year <= $endYear; $year++) {
                    $yearlyInputs = isset($this->yearlyInputsArr[$year]) ? $this->yearlyInputsArr[$year] : new YearlyInputs();
                    if (isset($rate)) {
                        $yearlyInputs->returnAvg = $rate;
                    } else {
                        $yearlyInputs->returnAvg = .07;
                    }
                    $this->yearlyInputsArr[$year] = $yearlyInputs;
                }
                break;
            case 6: // initial return standard deviation to be used in return rate randomization.
                for ($year = $startYear; $year <= $endYear; $year++) {
                    $yearlyInputs = $this->yearlyInputsArr[$year];
                }
                if (empty($yearlyInputs)) {
                    $yearlyInputs = new YearlyInputs();
                }
                $yearlyInputs->returnStdev = $rate;
                $this->yearlyInputsArr[$year] = $yearlyInputs;
        }
    }


    /**
     * Add Plan Cashflow Data
     */
    function addPlanCashflow($cashflowType, $startYear, $endYear, $amount) {
        if ($startYear < 1) {
            $startYear = 1;
        }
        if ($endYear > 100) {
            $endYear = 100;
        }
        switch ($cashflowType) {
            case 1:  // Taxable Annual Savings
                for ($year = $startYear; $year < $endYear; $year++) {
                    $yearlyInputs = isset($this->yearlyInputsArr[$year]) ? $this->yearlyInputsArr[$year] : new YearlyInputs();
                    $yearlyInputs->newTaxableInvestment += $amount;
                    $this->yearlyInputsArr[$year] = $yearlyInputs;
                }
                break;
            case 2: // Tax Deferred Annual Savings
                for ($year = $startYear; $year < $endYear; $year++) {
                    $yearlyInputs = isset($this->yearlyInputsArr[$year]) ? $this->yearlyInputsArr[$year] : new YearlyInputs();
                    $yearlyInputs->newTaxDeferredInvestment += $amount;
                    $this->yearlyInputsArr[$year] = $yearlyInputs;
                }
                break;
            case 3: // Tax Free Annual Savings
                for ($year = $startYear; $year < $endYear; $year++) {
                    $yearlyInputs = isset($this->yearlyInputsArr[$year]) ? $this->yearlyInputsArr[$year] : new YearlyInputs();
                    $yearlyInputs->newTaxFreeInvestment += $amount;
                    $this->yearlyInputsArr[$year] = $yearlyInputs;
                }
                break;
            case 4: // Annual Retirement Income
                for ($year = $startYear; $year <= $endYear; $year++) {
                    $yearlyInputs = isset($this->yearlyInputsArr[$year]) ? $this->yearlyInputsArr[$year] : new YearlyInputs();
                    $yearlyInputs->extraTaxableIncome += $amount;
                    $this->yearlyInputsArr[$year] = $yearlyInputs;
                }
                break;
            case 5:
                for ($year = $startYear; $year <= $endYear; $year++) {
                    $yearlyInputs = isset($this->yearlyInputsArr[$year]) ? $this->yearlyInputsArr[$year] : new YearlyInputs();
                    $yearlyInputs->extraTaxFreeIncome += $amount;
                    $this->yearlyInputsArr[$year] = $yearlyInputs;
                }
                break;
            case 6:
                $reducedAmount = $amount;
                for ($year = $startYear; $year <= $endYear; $year++) {
                    $yearlyInputs = isset($this->yearlyInputsArr[$year]) ? $this->yearlyInputsArr[$year] : new YearlyInputs();
                    if ($year >= $startYear) {
                        $yearlyInputs->extraTaxableIncome += $reducedAmount;
                        $this->yearlyInputsArr[$year] = $yearlyInputs;
                    }

                    $reducedAmount *= (1.0 - $yearlyInputs->inflationAvg);
                }
                break;
            case 7:
                $reducedAmount = $amount;
                for ($year = $startYear; $year <= $endYear; $year++) {
                    $yearlyInputs = isset($this->yearlyInputsArr[$year]) ? $this->yearlyInputsArr[$year] : new YearlyInputs();
                    if ($year >= $startYear) {
                        $yearlyInputs->extraTaxFreeIncome += $reducedAmount;
                        $this->yearlyInputsArr[$year] = $yearlyInputs;
                    }
                    $reducedAmount *= (1.0 - $yearlyInputs->inflationAvg);
                }
                break;
            case 8:
                for ($year = $startYear; $year <= $endYear; $year++) {
                    $yearlyInputs = isset($this->yearlyInputsArr[$year]) ? $this->yearlyInputsArr[$year] : new YearlyInputs();
                    $yearlyInputs->spendingRequested += $amount;
                    $this->yearlyInputsArr[$year] = $yearlyInputs;
                }
                break;
            case 9:
            /*     $reducedAmount = $amount;
              for ($year = $startYear; $year <= $endYear; $year++) {

              $yearlyInputs = isset($this->yearlyInputsArr[$year]) ? $this->yearlyInputsArr[$year] : new YearlyInputs();
              if ($year >= $startYear) {
              $yearlyInputs->spendingRequested += $reducedAmount;
              $this->yearlyInputsArr[$year] = $yearlyInputs;
              }
              $reducedAmount *= (1.0 - $yearlyInputs->inflationAvg);
              } */
        }
    }


    function processPortfolioGrowth($year, $portfolioReturn, $inflation) {
        $yearlyInputs = $this->yearlyInputsArr[$year];
        $taxRate = $yearlyInputs->investmentTaxRate;

        if ($this->taxableBalance >= 0.0) {
            $afterTaxGain = 0;
            if ($portfolioReturn > 0.0) {
                $afterTaxGain = $this->taxableBalance * $portfolioReturn * (1.0 - $taxRate);
            } else {
                $afterTaxGain = $this->taxableBalance * $portfolioReturn * (1.0 - $taxRate);
            }
            $inflationAdjustment = $this->taxableBalance * $inflation;
            $this->taxableBalance = ($this->taxableBalance + $yearlyInputs->newTaxableInvestment + $afterTaxGain - $inflationAdjustment);
        }
        if ($this->taxableBalance < 0.0) {
            $this->taxableBalance = 0.0;
        }

        if ($this->taxFreeBalance >= 0.0) {
            $afterTaxGain = $this->taxFreeBalance * $portfolioReturn;
            $inflationAdjustment = $this->taxFreeBalance * $inflation;
            $this->taxFreeBalance = ($this->taxFreeBalance +
            $yearlyInputs->newTaxFreeInvestment + $afterTaxGain - $inflationAdjustment);
        }
        if ($this->taxFreeBalance < 0.0) {
            $this->taxFreeBalance = 0.0;
        }

        if ($this->taxDeferredBalance >= 0.0) {
            $afterTaxGain = $this->taxDeferredBalance * $portfolioReturn;
            $inflationAdjustment = $this->taxDeferredBalance * $inflation;
            $this->taxDeferredBalance = ($this->taxDeferredBalance + $yearlyInputs->newTaxDeferredInvestment + $afterTaxGain - $inflationAdjustment);
        }
        if ($this->taxDeferredBalance < 0.0) {
            $this->taxDeferredBalance = 0.0;
        }
    }


    /**
     *
     */
    function getNetWithdrawalAmount($percentOfExpensesFunded, $inData) {
        return $percentOfExpensesFunded * $inData->spendingRequested - $inData->extraTaxFreeIncome - ($inData->extraTaxableIncome * (1.0 - $inData->incomeTaxRate));
    }


    /**
     *
     */
    function processAnnualWithdrawal($netWithdrawalNeeded, $year) {

        $inData = $this->yearlyInputsArr[$year];
        $results = new WithdrawalDetails();
        if ($year >= $this->RMDStartYear) {
            $yearsOver70 = $this->currentAge + $year - 1 - 70;

            if ($yearsOver70 >= 0) {
                if ($yearsOver70 < count($this->rmdPayoutTable)) {
                    $actuarialLifeExpectancy = $this->rmdPayoutTable[$yearsOver70];
                } else {
                    $actuarialLifeExpectancy = $this->rmdPayoutTable[count($this->rmdPayoutTable) - 1];
                }
                $results->grossRMD = ($this->taxDeferredBalance / $actuarialLifeExpectancy);
                $this->taxDeferredBalance -= $results->grossRMD;
                $results->RMDTaxes = ($results->grossRMD * $inData->incomeTaxRate);
                $results->netRMD = ($results->grossRMD - $results->RMDTaxes);
                if ($results->netRMD <= $netWithdrawalNeeded) {
                    $results->RMDWithdrawalUsedForExpenses = $results->netRMD;
                    $netWithdrawalNeeded -= $results->netRMD;
                } else {
                    $results->RMDWithdrawalUsedForExpenses = $netWithdrawalNeeded;
                }
            }
        }

        if ($netWithdrawalNeeded <= 0.0) {
            $this->taxableBalance -= $netWithdrawalNeeded;
            $results->grossWithdrawalAmount = 0.0;
        } else {
            $results->netWithdrawalNeeded = $netWithdrawalNeeded;

            $this->taxableBalance -= $netWithdrawalNeeded;
            if ($this->taxableBalance < 0.0) {
                if (!$this->swapWithdrawalOrder) {
                    $results = self::handleNormalDeferredWithdrawals($netWithdrawalNeeded, $year, $inData, $results);
                } else {
                    $results = self::handleSwappedDeferredWithdrawals($netWithdrawalNeeded, $year, $inData, $results);
                }
            } else {
                $results->grossWithdrawalAmount = $netWithdrawalNeeded;
            }
        }
        $results->grossWithdrawalAmount = $results->RMDWithdrawalUsedForExpenses;
        return $results;
    }


    /**
     *
     */
    function handleNormalDeferredWithdrawals($netWithdrawalNeeded, $year, $inData, $results) {
        $shortfall = 0.0 - $this->taxableBalance;
        $this->taxableBalance = 0.0;

        $results->grossWithdrawalAmount = $netWithdrawalNeeded;
        if ($year >= $this->iraWithdrawalStartYear) {
            $this->taxFreeBalance -= $shortfall;
        } else {
            $taxesDue = $shortfall * $inData->compoundedTaxAndIraPenaltyRate;
            $this->taxFreeBalance -= $shortfall + $taxesDue;
            $results->taxesOnGrossWithdrawal += $taxesDue;
            $results->grossWithdrawalAmount += $taxesDue;
            $this->iraPenaltyPaid = 1;
        }

        if ($this->taxFreeBalance < 0.0) {
            $shortfall = 0.0 - $this->taxFreeBalance;
            $this->taxFreeBalance = 0.0;

            if ($year >= $this->iraWithdrawalStartYear) {
                $taxesDue = $shortfall * $inData->compoundedIncomeTaxRate;
                $this->taxDeferredBalance -= $shortfall + $taxesDue;
                $results->taxesOnGrossWithdrawal += $taxesDue;
                $results->grossWithdrawalAmount += $taxesDue;
            } else {
                $this->taxDeferredBalance -= $shortfall;
                $this->iraPenaltyPaid = 1;
            }
            if ($this->taxDeferredBalance < 0.0) {
                $this->taxDeferredBalance = 0.0;
                $results->grossWithdrawalAmount = 0.0;
                $results->taxesOnGrossWithdrawal = 0.0;
            }
        }

        return $results;
    }


    /**
     *
     */
    function handleSwappedDeferredWithdrawals($netWithdrawalNeeded, $year, $inData, $results) {
        $taxesDue = 0.0;

        $shortfall = 0.0 - $this->taxableBalance;
        $this->taxableBalance = 0.0;

        $results->grossWithdrawalAmount = $netWithdrawalNeeded;

        if ($year >= $this->iraWithdrawalStartYear) {
            $taxesDue = $shortfall * $inData->compoundedIncomeTaxRate;
            $this->taxDeferredBalance -= $shortfall + $taxesDue;
            $results->taxesOnGrossWithdrawal = $taxesDue;
            $results->grossWithdrawalAmount += $taxesDue;
        } else {
            $taxesDue = $shortfall * $inData->compoundedTaxAndIraPenaltyRate;
            $this->taxDeferredBalance -= $shortfall + $taxesDue;
            $results->grossWithdrawalAmount += $taxesDue;
            $results->taxesOnGrossWithdrawal = $taxesDue;
            $this->iraPenaltyPaid = 1;
        }

        if ($this->taxDeferredBalance <= 0.0) {
            $shortfall = 0.0 - $this->taxDeferredBalance;
            $this->taxDeferredBalance = 0.0;
            if ($year >= $this->iraWithdrawalStartYear) {
                $shortfall -= $taxesDue;
                $results->grossWithdrawalAmount -= $taxesDue;
                $results->taxesOnGrossWithdrawal -= $taxesDue;
                $this->taxFreeBalance -= $shortfall;
            } else {
                $this->taxFreeBalance -= $shortfall;
                $this->iraPenaltyPaid = 1;
            }
            if ($this->taxFreeBalance < 0.0) {
                $this->taxFreeBalance = 0.0;
                $results->grossWithdrawalAmount = 0.0;
                $results->taxesOnGrossWithdrawal = 0.0;
            }
        }
        return $results;
    }


    /**
     *
     */
    function checkIfBtwNeeded($year, $totalBalance, $BalanceAtRetirementStart) {
        $backToWorkEndYear = 0;

        if (($year < $this->btwMaxYear) && ($totalBalance / $BalanceAtRetirementStart < $this->btwTriggerPercent)) {
            $backToWorkEndYear = $year + $this->btwDuration;

            if ($backToWorkEndYear > $this->btwMaxYear)
                $backToWorkEndYear = 0;
        }
        return $backToWorkEndYear;
    }


    /**
     *
     * Main class
     */
    function execute($returnType = null) {
        try {
            $successCount = 0;
            $backToWorkCount = 0;
            $yearlyTaxableBalance = array();
            $yearlyTaxDeferredBalance = array();
            $yearlyTaxFreeBalance = array();
            $yearlyTotalBalance = array();
            $yearlyPPMaintained = array();
            $yearlySpendingRequested = array();
            $yearlyExpensesToFund = array();
            $yearlyNetWithdrawalNeeded = array();
            $yearlyWithdrawalAmount = array();
            $yearlyWithdrawalTaxes = array();
            $yearlyWithdrawalWithTaxes = array();
            $yearlyRMD = array();
            $detailYears = array();

            $twister = new twister($this->make_seed());
            $statObj = new PHPExcel_Calculation_Statistical();

            $detailData = array();
            $detailCurrentAge = $this->currentAge;
            $detailCurrentYear = date("Y");

            $balanceAtRetirementStart = 0.0;
            $yearlyReturn = array();
            $yearlyPortfolioBalanceAtRetirement = array();

            for ($iteration = 1; $iteration <= $this->simIterations; $iteration++) {
                $yearPlanFailed = 0;
                $backToWorkEndYear = 0;

                $this->taxableBalance = $this->startingTaxableBalance;

                $this->taxDeferredBalance = $this->startingTaxDeferredBalance;
                $this->taxFreeBalance = $this->startingTaxFreeBalance;
                $totalBalance = $this->taxableBalance + $this->taxDeferredBalance + $this->taxFreeBalance;
                $previousBalance = $totalBalance;

                $previousPercentOfExpensesFunded = 1.0;
                $percentOfExpensesFunded = 1.0;

                for ($year = 1; $year <= $this->yearsInPlan; $year++) {
                    $inData = $this->yearlyInputsArr[$year];

                    if ($year == $this->retirementStartYear) {
                        $balanceAtRetirementStart = $totalBalance;
                    }
                    $netWithdrawalAmount = 0;
                    if ($year < $backToWorkEndYear) {
                        $netWithdrawalAmount = 0.0;
                    } else {
                        $netWithdrawalAmount = self::getNetWithdrawalAmount($percentOfExpensesFunded, $inData);
                    }

                    $withdrawalDetails = self::processAnnualWithdrawal($netWithdrawalAmount, $year);

                    if ($inData->inflationStdev != 0.0) {
                        $inflation = $rng * $inData->inflationStdev + $inData->inflationAvg;
                    } else {
                        $inflation = $inData->inflationAvg;
                    }

                    $randomNum = $twister->real_open();
                    $portfolioReturn = $statObj->NORMINV($randomNum, $this->riskReturn, $this->riskStdDev);

                    self::processPortfolioGrowth($year, $portfolioReturn, $inflation);

                    $totalBalance = $this->taxableBalance + $this->taxFreeBalance + $this->taxDeferredBalance;

                    if (($year >= $this->retirementStartYear) && ($totalBalance >= 0.0)) {
                        if (($backToWorkEndYear == 0) && ($this->btwTriggerPercent != 0.0)) {
                            $backToWorkEndYear = self::checkIfBtwNeeded($year, $totalBalance, $balanceAtRetirementStart);
                        }
                        $computeFunding = new ComputeFunding();
                        if ($this->spendingPolicy == 1) {
                            $percentOfExpensesFunded = $computeFunding->computeFundingUsingConservativePolicy($totalBalance, $previousBalance, $balanceAtRetirementStart, $previousPercentOfExpensesFunded, $inflation *
                            $this->spendingPolicySensitivity, $this->spendingPercentFloor, $this->spendingPercentCeiling);
                        } else if ($this->spendingPolicy == 2) {
                            $percentOfExpensesFunded = $computeFunding->computeFundingUsingFlexiblePolicy($totalBalance, $previousBalance, $balanceAtRetirementStart, $previousPercentOfExpensesFunded, $inflation *
                            $this->spendingPolicySensitivity, $this->spendingPercentFloor, $this->spendingPercentCeiling);
                        }
                    }

                    if (($totalBalance <= 0.0) && ($yearPlanFailed == 0)) {
                        $yearPlanFailed = $year;
                    }
                    $yearlyOutputs = new YearlyOutputs();

                    $yearlyOutputs->avgReturn = $portfolioReturn;
                    $yearlyOutputs->avgInflation = $inflation;
                    $yearlyOutputs->avgIraPenaltyPaid = $this->iraPenaltyPaid;

                    $yearlyReturn[$year][] = $portfolioReturn;

                    $this->yearlyOutputsArr[$year] = $yearlyOutputs;

                    $yearlyTaxableBalance[$year][$iteration] = $this->taxableBalance;
                    $yearlyTaxDeferredBalance[$year][$iteration] = $this->taxDeferredBalance;
                    $yearlyTaxFreeBalance[$year][$iteration] = $this->taxFreeBalance;
                    $yearlyTotalBalance[$year][$iteration] = $totalBalance;
                    $yearlySpendingRequested[$year][$iteration] = ($inData->spendingRequested);

                    if ($year == $this->retirementStartYear) {
                        $yearlyPortfolioBalanceAtRetirement[$year][$iteration] = $totalBalance + $withdrawalDetails->netWithdrawalNeeded;
                        $this->pensionPaymentAtRetirement = $inData->extraTaxFreeIncome;
                        if (($this->currentAge + $this->retirementStartYear) >= 62) {
                            $this->socialSecurityPaymentAtRetirement = $this->extraTaxFreeIncomeSocialSecurity;
                        }
                    }

                    if ($totalBalance > 0.0) {
                        $yearlyPPMaintained[$year][$iteration] = 1; //$previousPercentOfExpensesFunded;
                        $yearlyWithdrawalAmount[$year][$iteration] = ($withdrawalDetails->grossWithdrawalAmount);
                        $yearlyNetWithdrawalNeeded[$year][$iteration] = ($withdrawalDetails->netWithdrawalNeeded);
                        $yearlyWithdrawalTaxes[$year][$iteration] = ($withdrawalDetails->taxesOnGrossWithdrawal);
                        $yearlyWithdrawalWithTaxes[$year][$iteration] = ($withdrawalDetails->netWithdrawalNeeded + $withdrawalDetails->taxesOnGrossWithdrawal);
                        $yearlyRMD[$year][$iteration] = $withdrawalDetails->grossRMD;
                        $yearlyRMDTaxes[$year][$iteration] = $withdrawalDetails->RMDTaxes;
                        $yearlyRMDWithdrawalUsedForExpenses[$year][$iteration] = $withdrawalDetails->RMDWithdrawalUsedForExpenses;
                        $yearlyTaxFreeIncome[$year][$iteration] = $inData->extraTaxFreeIncome;
                        $yearlyExpensesToFund[$year][$iteration] = ($inData->spendingRequested);
                    } else {
                        $yearlyPPMaintained[$year][$iteration] = 0;
                        $yearlyWithdrawalAmount[$year][$iteration] = 0;
                        $yearlyNetWithdrawalNeeded[$year][$iteration] = 0;
                        $yearlyWithdrawalTaxes[$year][$iteration] = 0;
                        $yearlyWithdrawalWithTaxes[$year][$iteration] = 0;
                        $yearlyRMD[$year][$iteration] = 0;
                        $yearlyRMDTaxes[$year][$iteration] = 0;
                        $yearlyRMDWithdrawalUsedForExpenses[$year][$iteration] = 0;
                        $yearlyTaxFreeIncome[$year][$iteration] = 0;
                        $yearlyExpensesToFund[$year][$iteration] = 0;
                    }
                    $previousPercentOfExpensesFunded = $percentOfExpensesFunded;
                    $previousBalance = $totalBalance;
                }
                if (($totalBalance > 0.0) && ($yearPlanFailed == 0)) {
                    $successCount++;
                } else {
                    $yearlyOutputs = $this->yearlyOutputsArr[$yearPlanFailed];
                    $yearlyOutputs->numberOfFailures += 1.0;
                }

                if ($backToWorkEndYear > 0) {
                    $backToWorkCount++;
                }
            }

            $this->lowestMedianPPMaintained = 1000.0;
            $this->highestMedianPPMaintained = 0.0;
            $cumulativeFailureCount = 0;
            for ($year = 1; $year <= $this->yearsInPlan; $year++) {
                $outData = $this->yearlyOutputsArr[$year];

                $outData->medianTaxableBalance = self::getMedian($yearlyTaxableBalance[$year], $this->simIterations);
                $outData->medianTaxDeferredBalance = self::getMedian($yearlyTaxDeferredBalance[$year], $this->simIterations);
                $outData->medianTaxFreeBalance = self::getMedian($yearlyTaxFreeBalance[$year], $this->simIterations);

                $outData->medianTotalBalance = self::getMedian($yearlyTotalBalance[$year], $this->simIterations);
                $outData->top10PercentBalance = self::getTopNPercent($yearlyTotalBalance[$year], $this->simIterations, 10);
                $outData->bottom10PercentBalance = self::getBottomNPercent($yearlyTotalBalance[$year], $this->simIterations, 10);

                $outData->medianSpendingRequested = self::getMedian($yearlySpendingRequested[$year], $this->simIterations);
                $outData->medianExpensesToFund = self::getMedian($yearlyExpensesToFund[$year], $this->simIterations);
                $outData->medianNetWithdrawalNeeded = self::getMedian($yearlyNetWithdrawalNeeded[$year], $this->simIterations);
                $outData->medianWithdrawal = self::getMedian($yearlyWithdrawalAmount[$year], $this->simIterations);
                $outData->medianWithdrawalTaxes = self::getMedian($yearlyWithdrawalTaxes[$year], $this->simIterations);
                $outData->medianWithdrawalWithTaxes = self::getMedian($yearlyWithdrawalWithTaxes[$year], $this->simIterations);
                $outData->medianTaxFreeIncome = self::getMedian($yearlyTaxFreeIncome[$year], $this->simIterations);

                $outData->medianPercentPPMaintained = self::getMedian($yearlyPPMaintained[$year], $this->simIterations);
                if ($year >= $this->RMDStartYear) {
                    $outData->medianRMD = self::getMedian($yearlyRMD[$year], $this->simIterations);
                    $yearlyInput = $this->yearlyInputsArr[$year];
                    $outData->medianRMDTaxes = self::getMedian($yearlyRMDTaxes[$year], $this->simIterations);
                    $outData->medianRMDUsedForExpenses = self::getMedian($yearlyRMDWithdrawalUsedForExpenses[$year], $this->simIterations);
                } else {
                    $outData->medianRMD = 0.0;
                    $outData->medianRMDTaxes = 0.0;
                    $outData->medianRMDUsedForExpenses = 0.0;
                }

                if ($year >= $this->retirementStartYear) {
                    if ($outData->medianPercentPPMaintained < $this->lowestMedianPPMaintained) {
                        $this->lowestMedianPPMaintained = $outData->medianPercentPPMaintained;
                    }
                    if ($outData->medianPercentPPMaintained > $this->highestMedianPPMaintained) {
                        $this->highestMedianPPMaintained = $outData->medianPercentPPMaintained;
                    }
                }

                $cumulativeFailureCount = $cumulativeFailureCount + $outData->numberOfFailures;
                $outData->probabilityOfSuccess = (($this->simIterations - $cumulativeFailureCount) / $this->simIterations);

                $outData->avgReturn = (array_sum($yearlyReturn[$year]) / $this->simIterations) * 100;
                $outData->avgInflation /= $this->simIterations;
                $outData->avgIraPenaltyPaid /= $this->simIterations;
                if ($year == $this->retirementStartYear) {
                    $this->portfolioBalanceAtRetirement = self::getMedian($yearlyPortfolioBalanceAtRetirement[$year], $this->simIterations);
                }
            }
            $this->averageShortfallPercent = self::computeAverageShortfallPercent($cumulativeFailureCount);

            $this->probabilityOfBackToWork = ($backToWorkCount / $this->simIterations);
            $this->probabilityOfSuccess = ($successCount / $this->simIterations);

            if ($this->probabilityOfSuccess > 0.999) {
                $this->probabilityOfSuccess = 0.999;
            }
            if ($returnType == "medianData") {
                $medianData = array();
                $currentAge = $this->currentAge;
                $currentYear = date("Y");
                foreach ($this->yearlyOutputsArr as $key => $simYear) {
                    $inData = $this->yearlyInputsArr[$key];
                    $yearData = array();
                    $yearData["year"] = $currentYear;
                    if ($key != $this->retirementStartYear) {
                        $yearData["age"] = $currentAge;
                    } else {
                        $yearData["age"] = $currentAge . "-R";
                    }
                    $yearData["medianTotalBalance"] = number_format($simYear->medianTotalBalance);
                    $yearData["medianTaxableBalance"] = number_format($simYear->medianTaxableBalance);
                    $yearData["medianTaxDeferredBalance"] = number_format($simYear->medianTaxDeferredBalance);
                    $yearData["medianTaxFreeBalance"] = number_format($simYear->medianTaxFreeBalance);

                    $yearData["newTotalInvestment"] = number_format($inData->newTaxableInvestment + $inData->newTaxFreeInvestment + $inData->newTaxDeferredInvestment);
                    $yearData["newTaxableInvestment"] = number_format($inData->newTaxableInvestment);
                    $yearData["newTaxDeferredInvestment"] = number_format($inData->newTaxDeferredInvestment);
                    $yearData["newTaxFreeInvestment"] = number_format($inData->newTaxFreeInvestment);

                    $yearData["medianRMD"] = number_format($simYear->medianRMD);
                    $yearData["medianRMDTaxes"] = number_format($simYear->medianRMDTaxes);
                    $yearData["medianRMDAvailableforExpenses"] = number_format($simYear->medianRMDUsedForExpenses);

                    $yearData["medianSpendingRequested"] = number_format($simYear->medianSpendingRequested);
                    $yearData["medianExpensesToFund"] = number_format($simYear->medianExpensesToFund);
                    $yearData["medianNetWithdrawalNeeded"] = number_format($simYear->medianNetWithdrawalNeeded);
                    $yearData["medianRMDUsedForExpenses"] = number_format($simYear->medianRMDUsedForExpenses);
                    $yearData["medianTaxFreeIncome"] = number_format($simYear->medianTaxFreeIncome);

                    $yearData["medianWithdrawalTaxes"] = number_format($simYear->medianWithdrawalTaxes);
                    $yearData["medianPercentPPMaintained"] = $simYear->medianPercentPPMaintained * 100;

                    $yearData["medianWithdrawalWithTaxes"] = number_format($simYear->medianWithdrawalWithTaxes);

                    $yearData["avgReturn"] = number_format($simYear->avgReturn, 2);

                    //$yearData["avgInflation"] = number_format($simYear->avgInflation, 2, '.' , ',');
                    $medianData[] = array("data" => $yearData);
                    $currentAge++;
                    $currentYear++;
                }
            }

            unset($yearData);
            unset($outData);
            unset($detailYears);
            unset($yearlyInputs);
            unset($yearlyOutputs);
            unset($successCount);
            unset($backToWorkCount);
            unset($yearlyTaxableBalance);
            unset($yearlyTaxDeferredBalance);
            unset($yearlyTaxFreeBalance);
            unset($yearlyTotalBalance);
            unset($yearlyPPMaintained);
            unset($yearlySpendingRequested);
            unset($yearlyExpensesToFund);
            unset($yearlyNetWithdrawalNeeded);
            unset($yearlyWithdrawalAmount);
            unset($yearlyWithdrawalTaxes);
            unset($yearlyWithdrawalWithTaxes);
            unset($yearlyRMD);
            unset($detailYears);

            unset($twister);
            unset($statObj);

            unset($detailData);
            unset($detailCurrentAge);
            unset($detailCurrentYear);

            unset($balanceAtRetirementStart);
            unset($yearlyReturn);

            $response = array(
            'status' => 'OK',
            'probability' => $this->probabilityOfSuccess,
            'numIterations' => $this->simIterations,
            'annualSocialSecurityAtRetirement' => $this->socialSecurityPaymentAtRetirement,
            'annualPensionAtRetirement' => $this->pensionPaymentAtRetirement,
            'portfolioBalanceAtRetirement' => $this->portfolioBalanceAtRetirement,
            );

            if ($returnType == "medianData") {
                $response["medianData"] = $medianData;
            }
            unset($medianData);
            unset($pensionPaymentAtRetirement);
            unset($portfolioBalanceAtRetirement);

            return $response;
        } catch (Exception $e) {
            throw $e;
        }
    }


    /**
     *
     */
    function computeAverageShortfallPercent($cumulativeFailureCount) {
        $cumulativeShortfallAmount = 0.0;
        for ($year = 1; $year <= $this->yearsInPlan; $year++) {

            $yearlyOutputs = $this->yearlyOutputsArr[$year];
            if ($yearlyOutputs->numberOfFailures > 0.0) {
                $totalShortfall = 0.0;
                for ($i = $year; $i <= $this->yearsInPlan; $i++) {
                    $totalShortfall += self::getNetWithdrawalAmount(1.0, $this->yearlyInputsArr[$i]);
                }
                $yearlyOutputs = $this->yearlyOutputsArr[$year];
                $totalShortfall *= $yearlyOutputs->numberOfFailures;
                $cumulativeShortfallAmount += $totalShortfall;
            }
        }

        $totalWithdrawalsNeeded = 0.0;
        for ($year = 1; $year <= $this->yearsInPlan; $year++) {
            $totalWithdrawalsNeeded += self::getNetWithdrawalAmount(1.0, $this->yearlyInputsArr[$year]);
        }
        $averageShortfallPercent = 0.0;
        if (($cumulativeFailureCount > 0) && ($totalWithdrawalsNeeded > 0.0)) {
            $averageShortfallPercent = $cumulativeShortfallAmount / ($totalWithdrawalsNeeded * $cumulativeFailureCount);
        }
        return $averageShortfallPercent;
    }


    /**
     *
     */
    function getTopNPercent($arrayData, $numberOfElements, $percent) {
        if ($numberOfElements > 1) {
            sort($arrayData);
            $idx = $numberOfElements / (100 / $percent);
            return $arrayData[($numberOfElements - $idx)];
        }
        return $arrayData[1];
    }


    /**
     *
     */
    function getBottomNPercent($arrayData, $numberOfElements, $percent) {
        if ($numberOfElements > 1) {
            sort($arrayData);
            $idx = $numberOfElements / (100 / $percent);
            return $arrayData[$idx];
        }

        return $arrayData[1];
    }


    /**
     *
     */
    function getMedian($arrayData, $numberOfElements) {

        if ($numberOfElements > 1) {
            sort($arrayData);
            $mid = $numberOfElements / 2;
            return $arrayData[$mid];
        }
        return $arrayData[1];
    }


    function random_float($min, $max) {

        return ($min + lcg_value() * (abs($max - $min)));
    }


    function make_seed() {
        list($usec, $sec) = explode(' ', microtime());
        return (float) $sec + ((float) $usec * 100000);
    }


}

?>