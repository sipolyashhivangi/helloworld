<?php

/* * ********************************************************************
 * Filename: ReportController.php
 * Folder: controllers
 * Description: Report controller to create Admin reports
 * @author Dan Tormey
 * @copyright (c) 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

require_once(realpath(dirname(__FILE__) . '/../../scripts/reports/ZipCodes.php'));

class ReportController extends Scontroller {

    public function actionUserFinancesReport() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) || Yii::app()->getSession()->get('wsuser')->roleid != User::USER_IS_ADMIN) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $all_users = 0;
        $sengineObj = array();
        $consumerDebtToIncome = array();

        $userScores = array();
        $ZipCodes = new ZipCodes();

        $users = User::model()->findAll(array('condition' => 'zip <> "" AND zip <> 0 and isactive="1"', 'select' => 'id, zip'));

        foreach($users as $user) {
            $userzip = substr($user->zip, 0, 5);
            if (strlen($userzip) == 5) {
                if (array_key_exists($userzip, $ZipCodes->allUsZips)) {
                    $userScoreObjs = UserScore::model()->find(array('condition' => 'user_id = ' . $user->id));
                    if (isset($userScoreObjs->scoredetails)) {
                        $all_users++;
                        $sengineObj = unserialize($userScoreObjs->scoredetails);
                        if (!isset($consumerDebtToIncome[$userzip])) {
                            $consumerDebtToIncome[$userzip] = array();
                            $consumerDebtToIncome[$userzip]['consumer'] = 0;
                            $consumerDebtToIncome[$userzip]['mortgage'] = 0;
                            $consumerDebtToIncome[$userzip]['totalDebt'] = 0;
                            $consumerDebtToIncome[$userzip]['risk'] = 0;
                            $consumerDebtToIncome[$userzip]['zipcount'] = 0;
                        }
                        $consumerDebtToIncome[$userzip]['consumer'] += number_format($sengineObj->wfPoint7, 2);
                        $consumerDebtToIncome[$userzip]['mortgage'] += number_format($sengineObj->wfPoint9, 2);
                        $consumerDebtToIncome[$userzip]['totalDebt'] += number_format($sengineObj->wfPoint7 + $sengineObj->wfPoint9, 2);
                        $risk = 5;
                        if($sengineObj->userRiskValue > 0) {
                            $risk = $sengineObj->userRiskValue;
                        }
                        $consumerDebtToIncome[$userzip]['risk'] += number_format($risk, 2);
                        $consumerDebtToIncome[$userzip]['zipcount'] ++;

                        unset($sengineObj);
                    }
                    unset($userScoreObjs);
                }
            }
        }
        unset($ZipCodes);

        $lowConsumerDebtToIncomeSliced = array();
        $lowMortgageDebtToIncomeSliced = array();
        $lowTotalDebtToIncomeSliced = array();
        $lowRiskSliced = array();
        $highConsumerDebtToIncomeSliced = array();
        $highMortgageDebtToIncomeSliced = array();
        $highTotalDebtToIncomeSliced = array();
        $highRiskSliced = array();
        if ($consumerDebtToIncome) {
            $consumerDebtToIncomeZips = array();
            $mortgageDebtToIncomeZips = array();
            $totalDebtToIncomeZips = array();
            $riskZips = array();
            foreach ($consumerDebtToIncome as $zip => $data) {
                if ($data['zipcount'] > 10) {
                    $consumerDebtToIncomeZips[] = array('zip' => $zip, 'zipaverage' => number_format(($data['consumer'] / $data['zipcount']), 2));
                    $mortgageDebtToIncomeZips[] = array('zip' => $zip, 'zipaverage' => number_format(($data['mortgage'] / $data['zipcount']), 2));
                    $totalDebtToIncomeZips[] = array('zip' => $zip, 'zipaverage' => number_format(($data['totalDebt'] / $data['zipcount']), 2));
                    $riskZips[] = array('zip' => $zip, 'zipaverage' => number_format(($data['risk'] / $data['zipcount']), 2));
                }
            }
            $this->array_sort_by_column($consumerDebtToIncomeZips, 'zipaverage', SORT_ASC);
            $lowConsumerDebtToIncomeSliced = array_slice($consumerDebtToIncomeZips, 0, 10, true);
            $this->array_sort_by_column($consumerDebtToIncomeZips, 'zipaverage', SORT_DESC);
            $highConsumerDebtToIncomeSliced = array_slice($consumerDebtToIncomeZips, 0, 10, true);
            $this->array_sort_by_column($mortgageDebtToIncomeZips, 'zipaverage', SORT_ASC);
            $lowMortgageDebtToIncomeSliced = array_slice($mortgageDebtToIncomeZips, 0, 10, true);
            $this->array_sort_by_column($mortgageDebtToIncomeZips, 'zipaverage', SORT_DESC);
            $highMortgageDebtToIncomeSliced = array_slice($mortgageDebtToIncomeZips, 0, 10, true);
            $this->array_sort_by_column($totalDebtToIncomeZips, 'zipaverage', SORT_ASC);
            $lowTotalDebtToIncomeSliced = array_slice($totalDebtToIncomeZips, 0, 10, true);
            $this->array_sort_by_column($totalDebtToIncomeZips, 'zipaverage', SORT_DESC);
            $highTotalDebtToIncomeSliced = array_slice($totalDebtToIncomeZips, 0, 10, true);
            $this->array_sort_by_column($riskZips, 'zipaverage', SORT_ASC);
            $lowRiskSliced = array_slice($riskZips, 0, 10, true);
            $this->array_sort_by_column($riskZips, 'zipaverage', SORT_DESC);
            $highRiskSliced = array_slice($riskZips, 0, 10, true);
        }

        $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'numusers' => $all_users,
                    'lowConsumer' => $lowConsumerDebtToIncomeSliced, 'highConsumer' => $highConsumerDebtToIncomeSliced,
                    'lowMortgage' => $lowMortgageDebtToIncomeSliced, 'highMortgage' => $highMortgageDebtToIncomeSliced,
                    'lowTotal' => $lowTotalDebtToIncomeSliced, 'highTotal' => $highTotalDebtToIncomeSliced,
                    'lowRisk' => $lowRiskSliced, 'highRisk' => $highRiskSliced)));
    }

    private function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
        $sort_col = array();
        foreach ($arr as $key => $row) {
            $sort_col[$key] = $row[$col];
        }
        array_multisort($sort_col, $dir, $arr);
    }

}

?>
