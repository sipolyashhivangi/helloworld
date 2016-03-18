<?php

/**
 * 
 */
class ComputeFunding {

    function computeFundingUsingConservativePolicy($currentBalance, $previousBalance, $startBalance, $previousFundingPercent, $adjustmentRate, $spendingPercentFloor, $spendingPercentCeiling) {
     /*   $percentOfExpensesToFund = 0;

        if (($currentBalance < $previousBalance) && ($currentBalance < $startBalance)) {
            if ($adjustmentRate > 1.0) {
                $adjustmentRate = 0.99;
            }

            $percentOfExpensesToFund = $previousFundingPercent * (1.0 - $adjustmentRate);
        } else if (($currentBalance > $startBalance) && ($previousFundingPercent < 1.0)) {

            if ($currentBalance > 2.0 * $startBalance) {
                $percentOfExpensesToFund = $previousFundingPercent * (1.0 + $adjustmentRate);
            } else {
                $percentOfExpensesToFund = $previousFundingPercent * (1.0 + $adjustmentRate / 4.0);
            }
            if ($percentOfExpensesToFund > 1.0) {
                $percentOfExpensesToFund = 1.0;
            }
        } else {
            $percentOfExpensesToFund = $previousFundingPercent;
        }
        if ($percentOfExpensesToFund > $spendingPercentCeiling) {
            $percentOfExpensesToFund = $spendingPercentCeiling;
        } else if ($percentOfExpensesToFund < $spendingPercentFloor) {
            $percentOfExpensesToFund = $spendingPercentFloor;
        }  */
        $percentOfExpensesToFund = 1;
        return $percentOfExpensesToFund;
    }

    function computeFundingUsingFlexiblePolicy($currentBalance, $previousBalance, $startBalance, $previousFundingPercent, $adjustmentRate, $spendingPercentFloor, $spendingPercentCeiling) {
    /*    $percentOfExpensesToFund = 0;
        if (($currentBalance < $previousBalance) && ($currentBalance < $startBalance)) {
            if ($adjustmentRate > 1.0) {
                $adjustmentRate = 0.99;
            }
            $percentOfExpensesToFund = $previousFundingPercent * (1.0 - $adjustmentRate);
        } else {
            if ($currentBalance > $startBalance) {
                $percentOfExpensesToFund;
                if ($currentBalance > 2.0 * $startBalance) {
                    $percentOfExpensesToFund = $previousFundingPercent * (1.0 + $adjustmentRate);
                } else {
                    $percentOfExpensesToFund = $previousFundingPercent * (
                            1.0 + $adjustmentRate / 4.0);
                }
            } else {
                $percentOfExpensesToFund = $previousFundingPercent;
            }
        }
        if ($percentOfExpensesToFund > $spendingPercentCeiling)
            $percentOfExpensesToFund = $spendingPercentCeiling;
        else if ($percentOfExpensesToFund < $spendingPercentFloor)
            $percentOfExpensesToFund = $spendingPercentFloor;
       */
        $percentOfExpensesToFund = 1;
        return $percentOfExpensesToFund;
    }

}

?>