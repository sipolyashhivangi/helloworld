<?php

/* * ********************************************************************
 * Filename: BatchfileeController.php
 * Folder: controllers
 * Description:  Controller to process cashedge batchfiles
 * @copyright (c) 2013 - 2015
 * Change History:
 * Co-authored by: Ganesh Manoharan
 * Validated by : Ganesh Manoharan
 * Modified by : Dan Tormey
 * Version         Author               Change Description
 * ********************************************************************** */

require_once(realpath(dirname(__FILE__) . '/../helpers/Messages.php'));

class BatchfileController extends Scontroller {

    public function accessRules() {
        return array_merge(
                        array(array('allow', 'users' => array('?'))),
                        // Include parent access rules
                        parent::accessRules()
        );
    }

    public function actionProcessBatchfiles() {
        $md5hashval = "b28159c334ecd24e2f8d17ad64407362";
        if(!isset($_GET['accTok']) || $_GET['accTok'] == '' || md5($_GET['accTok']) != $md5hashval)
        {
            header('HTTP/1.1 403 Unauthorized');exit;
        }
        $action = $_GET["action"];
        try {

            $paramsLocal = file_exists('./config/params-local.php') ? require('./config/params-local.php') : array();
            $batchFilePath = $paramsLocal['batchpath'];

            $batchfileNotifications = Messages::getBatchfileNotifications();

            //Consolidated Batch Files
            $bankFile = $batchFilePath . "/CAS-BANK.csv";
            $ccFile = $batchFilePath . "/CAS-CREDITCARD.csv";
            $investmentFile = $batchFilePath . "/CAS-INVESTMENT.csv";
            $othersFile = $batchFilePath . "/CAS-OTHER.csv";
            $securityFile = $batchFilePath . "/CAT-SECURITIES.csv";

            //Unused Batch Files
            $bankFileTransaction = $batchFilePath . "/CAT-BANK.csv";
            $ccFileTransaction = $batchFilePath . "/CAT-CREDITCARD.csv";
            $investmentFileTransaction = $batchFilePath . "/CAT-INVESTMENT.csv";
            $othersFileTransaction = $batchFilePath . "/CAT-OTHER.csv";

            //Other Unused Batch Files
            $deletedTransaction = $batchFilePath . "/CAT-DELETED.csv";
            $financeInstDeleted = $batchFilePath . "/CFI-DELETED.csv";
            $financeInstProfile = $batchFilePath . "/CFI-PROFILE.csv";
            $userProfileFile = $batchFilePath . "/USR-PROFILE.csv";

            // Store notifications for all files
            $notificationHash = array();

            // Store email information for system admins
            $to = "ganesh.m@truglobal.com, dan@flexscore.com, melroy@flexscore.com";
            $subject = "Batch File Processing - Error";
            $message = "";
            $newErrorCode = "";
            $from = "noreply@flexscore.com";
            $headers = "From:" . $from;

            // Log accounts from files that are not found in the database
            $timenow = "\"" . date("Y-m-d H:i:s") . "\"";
            $actlogfile = $batchFilePath . "/account.log";
            $logfd = fopen($actlogfile, "a+");
            fwrite($logfd, "Logs for the accounts that are updated $timenow - BEGIN \n");

            switch ($action) {

                CASE "bank":
                    //  CAS-BANK.csv
                    //
            //  [0] => HOMEID,
                    //  [1] => ACCTID,
                    //  [2] => LASTUPDATESTATUSCODE,
                    //  [3] => HARVESTID,
                    //  [4] => ERRORCODE,
                    //  [5] => LASTUPDATEATTEMPT,
                    //  [6] => LASTSUCCESSFULUPDATE,
                    //  [7] => RECLASSIFICATIONREQUIRED,
                    //  [8] => CURRENTBALANCE,
                    //  [9] => AVAILABLEBALANCE,
                    //  [10] => ANNUALPERCENTAGEYIELD,
                    //  [11] => INTERESTYTD,
                    //  [12] => INTERESTRATE
                    $count = 0;
                    if (($handle = fopen($bankFile, "r+")) !== FALSE) {
                        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {

                            if (count($data) == 0) {
                                break;
                            }

                            $acctid = trim($data[1]);
                            $errorCode = $data[4];

                            $currentBankAsset = Assets::model()->find("context='AUTO' and actid = :acctid", array('acctid' => $acctid));

                            if ($currentBankAsset) {
                                $dbModifiedTimestamp = new DateTime($currentBankAsset->modifiedtimestamp);

                                if ($data[2] != "F") {
                                    if (isset($data[6]) && $data[6] != "") {
                                        $ceLastSuccessfulUpdate = new DateTime(substr_replace(substr_replace($data[6], "-", 6, 0), "-", 4, 0));

                                        if ($ceLastSuccessfulUpdate > $dbModifiedTimestamp) {
                                            $currentBal = (isset($data[8]) && $data[8] != "") ? $data[8] : $currentBankAsset->balance;
                                            $interestRate = (isset($data[12]) && $data[12] != "") ? $data[12] : $currentBankAsset->growthrate;

                                            $currentBankAsset->balance = $currentBal;
                                            $currentBankAsset->growthrate = $interestRate;
                                            $currentBankAsset->modifiedtimestamp = $ceLastSuccessfulUpdate->format('Y-m-d H:i:s');
                                            $currentBankAsset->save();

                                            parent::setEngine($currentBankAsset->user_id);
                                            $assetController = new AssetController(1);
                                            $assetController->actionreCalculateScoreAssets($currentBankAsset, 'UPDATE', $currentBankAsset->user_id, 1);

                                            parent::unsetEngine();
                                            unset($assetController, $currentBankAsset, $currentBal, $interestRate);


                                            $count++;
                                            fwrite($logfd, "Success! Updated Bank Account ID: $acctid \n");
                                        }
                                    }
                                } else {  // it is an error
                                    $message .= "Bank Account ID: " . $acctid . " with Error Code: " . $errorCode . "\n";
                                    if (isset($currentBankAsset->user_id) && isset($currentBankAsset->FILoginAcctId)) {
                                        $notificationHash[$currentBankAsset->user_id . "|" . $currentBankAsset->FILoginAcctId] = $errorCode;
                                    }
                                }
                            } else {
                                $message .= "Bank Account ID: " . $acctid . " Not Found \n";
                            }
                        }
                    }
                    fclose($handle);

                    break;



                CASE "credit":
                    //  CAS-CREDITCARD.csv
                    //
                    //  [0] => HOMEID
                    //  [1] => ACCTID
                    //  [2] => LASTUPDATESTATUSCODE
                    //  [3] => HARVESTID
                    //  [4] => ERRORCODE
                    //  [5] => LASTUPDATEATTEMPT
                    //  [6] => LASTSUCCESSFULUPDATE
                    //  [7] => RECLASSIFICATIONREQUIRED
                    //  [8] => PAYMENTDUEDATE
                    //  [9] => CREDITLIMIT
                    //  [10] => AVAILCREDIT
                    //  [11] => OUTSTANDING
                    //  [12] => CASHADVANCELIMIT
                    //  [13] => AVAILABLECASHLIMIT
                    //  [14] => CURRENTREWARDSBALANCE
                    //  [15] => POINTSACCRUED
                    //  [16] => POINTSREDEEMED
                    //  [17] => STATEMENTBALANCE
                    //  [18] => MINIMUMPAYMENTDUE
                    //  [19] => FINANCECHARGES
                    //  [20] => PURCHASEAPR
                    //  [21] => ADVANCEAPR
                    if (($handle = fopen($ccFile, "r+")) !== FALSE) {
                        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {

                            if (count($data) == 0) {
                                break;
                            }

                            $acctid = trim($data[1]);
                            $errorCode = $data[4];

                            $currentCCDebt = Debts::model()->find("context='AUTO' and actid = :acctid", array('acctid' => $acctid));

                            if ($currentCCDebt) {
                                $dbModifiedTimestamp = new DateTime($currentCCDebt->modifiedtimestamp);
                                if ($data[2] != "F") {
                                    if (isset($data[6]) && $data[6] != "") {
                                        $ceLastSuccessfulUpdate = new DateTime(substr_replace(substr_replace($data[6], "-", 6, 0), "-", 4, 0));

                                        if ($ceLastSuccessfulUpdate > $dbModifiedTimestamp) {
                                            $outstandingBal = (isset($data[11]) && $data[11] != "") ? $data[11] : $currentCCDebt->balowed;
                                            $apr = (isset($data[20]) && $data[20] != "") ? $data[20] : $currentCCDebt->apr;

                                            $currentCCDebt->balowed = $outstandingBal;
                                            $currentCCDebt->apr = $apr;
                                            $currentCCDebt->modifiedtimestamp = $ceLastSuccessfulUpdate->format('Y-m-d H:i:s');
                                            $currentCCDebt->save();

                                            parent::setEngine($currentCCDebt->user_id);
                                            $debtController = new DebtController(1);
                                            $debtController->actionreCalculateScoreDebts($currentCCDebt, 'UPDATE', $currentCCDebt->user_id, 1);
                                            parent::unsetEngine();
                                            unset($currentCCDebt, $outstandingBal, $apr, $debtController);

                                            fwrite($logfd, "Success:Updated Credit Card Account ID: $acctid \n");
                                        }
                                    }
                                } else {  // it is an error
                                    $message .= "Credit Card Account ID: $acctid with Error Code: $errorCode\n";
                                    if (isset($currentCCDebt->user_id) && isset($currentCCDebt->FILoginAcctId)) {
                                        $notificationHash[$currentCCDebt->user_id . "|" . $currentCCDebt->FILoginAcctId] = $errorCode;
                                    }
                                }
                            } else {
                                $message .= "Credit Card Account ID: " . $acctid . " Not Found \n";
                            }
                        }
                    }
                    fclose($handle);

                    break;

                CASE "inv":

                    //  CAS-INVESTMENT.csv
                    //
                    //  [0] => HOMEID
                    //  [1] => ACCTID
                    //  [2] => LASTUPDATESTATUSCODE
                    //  [3] => HARVESTID
                    //  [4] => ERRORCODE
                    //  [5] => LASTUPDATEATTEMPT
                    //  [6] => LASTSUCCESSFULUPDATE
                    //  [7] => RECLASSIFICATIONREQUIRED
                    //  [8] => CASHBALANCE
                    //  [9] => MONEYMARKETBALANCE
                    //  [10] => SECURITIESBALANCE
                    //  [11] => TOTALBROKERAGEACCOUNTVALUE
                    //  [12] => DAILYCHANGE
                    //  [13] => PERCENTAGECHANGE
                    //  [14] => CURRENTVESTEDBALANCE
                    //  [15] => TOTALEMPLOYERCONTRIBUTION
                    //  [16] => TOTALOTHERCONTRIBUTION
                    //  [17] => DEFERRALPERCENTAGE
                    //  [18] => EMPLOYERPROFITSHARING
                    //  [19] => LOANAMOUNT
                    //  [20] => DEATHBENEFIT
                    //  [21] => CASHSURRENDERVALUE
                    //  [22] => INSURANCEPREMIUM
                    //  [23] => PAYMENTDUEDATE
                    //  [24] => INTERESTRATE


                    if (($handle = fopen($investmentFile, "r+")) !== FALSE) {
                        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {

                            if (count($data) == 0) {
                                break;
                            }

                            $investmentFound = false;
                            $acctid = trim($data[1]);
                            $errorCode = $data[4];

                            $currentInvAsset = Assets::model()->find("context='AUTO' and actid = :acctid", array('acctid' => $acctid));

                            if ($currentInvAsset) {
                                $investmentFound = true;
                                $dbModifiedTimestamp = new DateTime($currentInvAsset->modifiedtimestamp);
                                if ($data[2] != "F") {
                                    if (isset($data[6]) && $data[6] != "") {
                                        $ceLastSuccessfulUpdate = new DateTime(substr_replace(substr_replace($data[6], "-", 6, 0), "-", 4, 0));

                                        if ($ceLastSuccessfulUpdate > $dbModifiedTimestamp) {
                                            $brokVal = (isset($data[11]) && $data[11] != "") ? $data[11] : $currentInvAsset->balance;
                                            $employerContribution = $currentInvAsset->empcontribution; //(isset($data[15]) && $data[15] != "") ? $data[15] : $currentInvAsset->empcontribution;
                                            $otherContribution = $currentInvAsset->contribution; //(isset($data[16]) && $data[16] != "") ? $data[16] : $currentInvAsset->contribution;

                                            $currentInvAsset->balance = $brokVal;
                                            $currentInvAsset->empcontribution = $employerContribution;
                                            $currentInvAsset->contribution = $otherContribution;
                                            $currentInvAsset->modifiedtimestamp = $ceLastSuccessfulUpdate->format('Y-m-d H:i:s');
                                            $currentInvAsset->save();

                                            parent::setEngine($currentInvAsset->user_id);
                                            $assetController = new AssetController(1);
                                            $assetController->actionreCalculateScoreAssets($currentInvAsset, 'UPDATE', $currentInvAsset->user_id, 1);
                                            parent::unsetEngine();
                                            unset($assetController, $brokVal, $employerContribution, $otherContribution);

                                            fwrite($logfd, "Success:Updated Investment Account ID: $acctid \n");
                                        }
                                    }
                                } else {  // it is an error
                                    $message .= "Investment Account ID: $acctid with Error Code: $errorCode \n";
                                    if (isset($currentInvAsset->user_id) && isset($currentInvAsset->FILoginAcctId)) {
                                        $notificationHash[$currentInvAsset->user_id . "|" . $currentInvAsset->FILoginAcctId] = $errorCode;
                                    }
                                }
                            }
                            // If the current CAS-INVESTMENT.csv record is not in the assets table, then check if it is in the insurance table.
                            if ($investmentFound == false) {

                                $currentInvInsurance = Insurance::model()->find("context='AUTO' and actid = :acctid", array('acctid' => $acctid));

                                if ($currentInvInsurance) {
                                    $investmentFound = true;
                                    $dbModifiedTimestamp = new DateTime($currentInvInsurance->modifiedtimestamp);

                                    if ($data[2] != "F") {
                                        if (isset($data[6]) && $data[6] != "") {
                                            $ceLastSuccessfulUpdate = new DateTime(substr_replace(substr_replace($data[6], "-", 6, 0), "-", 4, 0));

                                            if ($ceLastSuccessfulUpdate > $dbModifiedTimestamp) {
                                                $deathbenefit = (isset($data[20]) && $data[20] != "") ? $data[20] : $currentInvInsurance->amtupondeath;
                                                $cashvalue = (isset($data[21]) && $data[21] != "") ? $data[21] : $currentInvInsurance->cashvalue;
                                                $annualpremium = (isset($data[22]) && $data[22] != "") ? $data[22] : $currentInvInsurance->annualpremium;

                                                $currentInvInsurance->amtupondeath = $deathbenefit;
                                                $currentInvInsurance->cashvalue = $cashvalue;
                                                $currentInvInsurance->annualpremium = $annualpremium;
                                                $currentInvInsurance->modifiedtimestamp = $ceLastSuccessfulUpdate->format('Y-m-d H:i:s');
                                                $currentInvInsurance->save();

                                                parent::setEngine($currentInvInsurance->user_id);
                                                $insuranceController = new InsuranceController(1);
                                                $insuranceController->actionreCalculateScoreInsurance($currentInvInsurance, 'UPDATE', $currentInvInsurance->user_id, 1);
                                                parent::unsetEngine();
                                                unset($currentInvInsurance, $insuranceController, $deathbenefit, $cashvalue, $annualpremium);

                                                fwrite($logfd, "Success:Updated Insurance Account ID: $acctid \n");
                                            }
                                        }
                                    } else {  // it is an error
                                        $message .= "Insurance Investment Account ID: " . $acctid . " with Error Code: " . $errorCode . "\n";
                                        if (isset($currentInvInsurance->user_id) && isset($currentInvInsurance->FILoginAcctId)) {
                                            $notificationHash[$currentInvInsurance->user_id . "|" . $currentInvInsurance->FILoginAcctId] = $errorCode;
                                        }
                                    }
                                }
                            }
                            // If the current CAS-OTHER.csv record is not in the debts or assets table, then add it to the messages.
                            // These should be checked checked if they are in the insurance table, if so then another block of code
                            // could be added to process insurance records.
                            if ($investmentFound == false) {
                                $message .= "Investment Account ID: " . $acctid . " Not Found \n";
                            }
                        }
                    }
                    fclose($handle);

                    break;

                CASE "other":
                    //   CAS-OTHER.csv
                    //
                    //   The CAS-OTHER.csv file needs to go through the debts, assets, and possibly insurance tables.
                    //   [0] => HOMEID
                    //   [1] => ACCTID
                    //   [2] => LASTUPDATESTATUSCODE
                    //   [3] => HARVESTID
                    //   [4] => ERRORCODE
                    //   [5] => LASTUPDATEATTEMPT
                    //   [6] => LASTSUCCESSFULUPDATE
                    //   [7] => RECLASSIFICATIONREQUIRED
                    //   [8] => CURRENTBALANCE
                    //   [9] => AVAILABLEBALANCE
                    //   [10] => ANNUALPERCENTAGEYIELD
                    //   [11] => INTERESTYTD
                    //   [12] => TERM
                    //   [13] => INTERESTRATE
                    //   [14] => INTERESTRATETYPE
                    //   [15] => PAYOFFAMOUNT
                    //   [16] => INSTALLMENTAMOUNT
                    //   [17] => AMOUNTDUE
                    //   [18] => PAYMENTDUEDATE
                    //   [19] => MATURITYDATE
                    //
                    if (($handle = fopen($othersFile, "r+")) !== FALSE) {
                        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {

                            if (count($data) == 0) {
                                break;
                            }

                            $otherFound = false;
                            $acctid = trim($data[1]);
                            $errorCode = $data[4];

                            $currentOtherDebt = Debts::model()->find("context='AUTO' and actid = :acctid", array('acctid' => $acctid));

                            // First check if the current CAS-OTHER.csv record is in the debt table.
                            if ($currentOtherDebt) {

                                $otherFound = true;
                                $dbModifiedTimestamp = new DateTime($currentOtherDebt->modifiedtimestamp);


                                if ($data[2] != "F") {
                                    if (isset($data[6]) && $data[6] != "") {
                                        $ceLastSuccessfulUpdate = new DateTime(substr_replace(substr_replace($data[6], "-", 6, 0), "-", 4, 0));

                                        if ($ceLastSuccessfulUpdate > $dbModifiedTimestamp) {
                                            $outstandingBal = (isset($data[8]) && $data[8] != "") ? $data[8] : $currentOtherDebt->balowed;
                                            $apr = (isset($data[13]) && $data[13] != "") ? $data[13] : $currentOtherDebt->apr;

                                            $currentOtherDebt->balowed = $outstandingBal;
                                            $currentOtherDebt->apr = $apr;
                                            $currentOtherDebt->modifiedtimestamp = $ceLastSuccessfulUpdate->format('Y-m-d H:i:s');
                                            $currentOtherDebt->save();

                                            parent::setEngine($currentOtherDebt->user_id);
                                            $debtController = new DebtController(1);
                                            $debtController->actionreCalculateScoreDebts($currentOtherDebt, 'UPDATE', $currentOtherDebt->user_id, 1);
                                            parent::unsetEngine();
                                            unset($currentOtherDebt, $outstandingBal, $apr, $debtController);

                                            fwrite($logfd, "Success: Updated Other Debt Account ID: $acctid \n");
                                        }
                                    }
                                } else {  // it is an error
                                    $message .= "Other Debt Account ID: $acctid with Error Code: $errorCode\n";
                                    if (isset($currentOtherDebt->user_id) && isset($currentOtherDebt->FILoginAcctId)) {
                                        $notificationHash[$currentOtherDebt->user_id . "|" . $currentOtherDebt->FILoginAcctId] = $errorCode;
                                    }
                                }
                            }


                            // If the current CAS-OTHER.csv record is not in the debts table, then check if it is in the assets table.
                            if ($otherFound == false) {

                                $currentOtherAsset = Assets::model()->find("context='AUTO' and actid = :acctid", array('acctid' => $acctid));

                                if ($currentOtherAsset) {
                                    $otherFound = true;
                                    $dbModifiedTimestamp = new DateTime($currentOtherAsset->modifiedtimestamp);

                                    if ($data[2] != "F") {
                                        if (isset($data[6]) && $data[6] != "") {
                                            $ceLastSuccessfulUpdate = new DateTime(substr_replace(substr_replace($data[6], "-", 6, 0), "-", 4, 0));

                                            if ($ceLastSuccessfulUpdate > $dbModifiedTimestamp) {
                                                $currentBal = (isset($data[8]) && $data[8] != "") ? $data[8] : $currentOtherAsset->balance;
                                                $interestRate = (isset($data[13]) && $data[13] != "") ? $data[13] : $currentOtherAsset->growthrate;

                                                $currentOtherAsset->balance = $currentBal;
                                                $currentOtherAsset->growthrate = $interestRate;
                                                $currentOtherAsset->modifiedtimestamp = $ceLastSuccessfulUpdate->format('Y-m-d H:i:s');
                                                $currentOtherAsset->save();

                                                parent::setEngine($currentOtherAsset->user_id);
                                                $assetController = new AssetController(1);
                                                $assetController->actionreCalculateScoreAssets($currentOtherAsset, 'UPDATE', $currentOtherAsset->user_id, 1);
                                                parent::unsetEngine();
                                                unset($currentOtherAsset, $assetController, $currentBal, $interestRate);

                                                fwrite($logfd, "Success:Updated Bank Account ID: $acctid \n");
                                            }
                                        }
                                    } else {  // it is an error
                                        $message .= "Other Asset Account ID: " . $acctid . " with Error Code: " . $errorCode . "\n";
                                        if (isset($currentOtherAsset->user_id) && isset($currentOtherAsset->FILoginAcctId)) {
                                            $notificationHash[$currentOtherAsset->user_id . "|" . $currentOtherAsset->FILoginAcctId] = $errorCode;
                                        }
                                    }
                                }
                            }
                            // If the current CAS-OTHER.csv record is not in the debts or assets table, then add it to the messages.
                            // These should be checked checked if they are in the insurance table, if so then another block of code
                            // could be added to process insurance records.
                            if ($otherFound == false) {
                                $message .= "Other Account ID: " . $acctid . " Not Found \n";
                            }
                        }
                    }
                    fclose($handle);

                    break;

                CASE "sec":

                    // Read the file CAT-SECURITIES.csv
                    // The Securities file contains all cashedge records for each acctid.
                    // The acctid hashmap updates the invpos field in the asset table with an array of
                    // tickers and related data.
                    //   [0] => HOMEID
                    //   [1] => ACCTID
                    //   [2] => RETRIEVEDATE
                    //   [3] => CEPOSITIONID
                    //   [4] => POSITIONID
                    //   [5] => SYMBOL (TICKER)
                    //   [6] => DESCRIPTION
                    //   [7] => QUANTITY
                    //   [8] => PRICE
                    //   [9] => MARKETVALUE
                    //   [10] => ASSETID
                    //   [11] => CESID
                    //   [12] => COSTBASIS
                    //   [13] => MARGINABLE
                    //   [14] => SECURITYTYPE
                    //   [15] => CURRENCYCODE
                    $rowSecurities = 0;
                    $securitiesHash = array();
                    $invPosArr = array();
                    if (($handle = fopen($securityFile, "r+")) !== FALSE) {
                        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {

                            if (!isset($securitiesHash[$data[1]])) {
                                $securitiesHash[$data[1]] = array();
                            }
                            $invPosArr = $securitiesHash[$data[1]];
                            $count = count($securitiesHash[$data[1]]);

                            $eachTicker = array(
                                'ticker' => $data[5],
                                'secdesc' => $data[6],
                                'unitprice' => $data[8],
                                'unit' => $data[7],
                                'marketvalue' => $data[9],
                                'amount' => $data[9]
                            );
                            $invPosArr[$count] = $eachTicker;
                            $securitiesHash[$data[1]] = $invPosArr;
                        }
                    }
                    fclose($handle);




                    foreach ($securitiesHash as $acctid => $tickervals) {
                        $invpos = json_encode($tickervals);

                        $currentSecuritiesAsset = Assets::model()->find("context='AUTO' and actid = :acctid", array('acctid' => $acctid));

                        if ($currentSecuritiesAsset) {
                            $currentSecuritiesAsset->invpos = $invpos;
                            $currentSecuritiesAsset->save();

                            parent::setEngine($currentSecuritiesAsset->user_id);
                            $assetController = new AssetController(1);
                            $assetController->actionreCalculateScoreAssets($currentSecuritiesAsset, 'UPDATE', $currentSecuritiesAsset->user_id, 1);
                            unset($assetController, $invpos, $currentSecuritiesAsset);
                            parent::unsetEngine();

                            fwrite($logfd, "Success:Updated Securities Account ID: $acctid \n");
                        }
                    }
                    break;
                CASE "deleted":

                    //  CFI-DELETED.csv
                    //
            //  [0] => PERSONID,
                    //  [1] => HOMEID,
                    //  [2] => ACCTID,
                    //  [3] => FIID,
                    //  [4] => ACCTTYPE,
                    //  [5] => EXTACCTTYPE,
                    //  [6] => CURCODE,
                    //  [7] => DELETED DATE,
                    $count = 0;
                    if (($handle = fopen($financeInstDeleted, "r+")) !== FALSE) {
                        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {

                            if (count($data) == 0) {
                                break;
                            }

                            $acctid = trim($data[2]);

                            $deletedAccount = Assets::model()->find("context='AUTO' and actid = :acctid", array('acctid' => $acctid));
                            if (!$deletedAccount) {
                                $deletedAccount = Debts::model()->find("context='AUTO' and actid = :acctid", array('acctid' => $acctid));
                            }
                            if (!$deletedAccount) {
                                $deletedAccount = Insurance::model()->find("context='AUTO' and actid = :acctid", array('acctid' => $acctid));
                            }

                            if ($deletedAccount) {
                                if ($deletedAccount->status != 1) {
                                    $deletedAccount->status = 1;
                                    $deletedAccount->modifiedtimestamp = date("Y-m-d H:i:s");
                                    $deletedAccount->save();

                                    $controller = new Scontroller(1);
                                    parent::setEngine($deletedAccount->user_id);
                                    if ($deletedAccount->tableName() == "assets") {
                                        $controller = new AssetController(1);
                                        $controller->actionreCalculateScoreAssets($deletedAccount, 'DELETE', $deletedAccount->user_id, 1);
                                    }
                                    if ($deletedAccount->tableName() == "debts") {
                                        $controller = new DebtController(1);
                                        $controller->actionreCalculateScoreDebts($deletedAccount, 'DELETE', $deletedAccount->user_id, 1);
                                    }
                                    if ($deletedAccount->tableName() == "insurance") {
                                        $controller = new InsuranceController(1);
                                        $controller->actionreCalculateScoreInsurance($deletedAccount, 'DELETE', $deletedAccount->user_id, 1);
                                    }
                                    parent::unsetEngine();
                                    unset($controller);

                                    fwrite($logfd, "Success:Deleted Account ID: $acctid \n");
                                }
                            } else {
                                $message .= "Deleted Account ID: " . $acctid . " Not Found \n";
                            }
                        }
                    }
                    fclose($handle);

                    break;
                    default:
                        fwrite($logfd, "Error - Invalid Parameter (\"$action\") - No Batchfile Processed - $timenow \n");
            }

            // Create the notifications as follows:
            // Read all of the user_id|FILoginAcctId combinations in the notificationHash
            // and insert them into the notifications table.

            foreach ($notificationHash as $notificationkey => $errorcodeval) {
                if (isset($batchfileNotifications[$errorcodeval])) {
                    /*
                      $userFiLogin = explode("|", $notificationkey);
                      $user_id = $userFiLogin[0];
                      $FILoginAcctId = $userFiLogin[1];
                      $type = $batchfileNotifications[$errorcodeval]["type"];
                      $context = $batchfileNotifications[$errorcodeval]["context"];
                      $template = $batchfileNotifications[$errorcodeval]["template"];
                      $message = $batchfileNotifications[$errorcodeval]["msg"];

                      $cashedgeItem = CashedgeItem::model()->findBySql("SELECT id, name FROM cashedgeitem WHERE user_id = :user_id AND FILoginAcctId = :FILoginAcctId order by modified desc", array('FILoginAcctId' => $FILoginAcctId, 'user_id' => $user_id));
                      $notification = Notification::model()->find("refid=:refid AND user_id=:user_id",array("user_id" => $user_id , "refid" => $FILoginAcctId));
                     */
                }
                $newErrorCode .= "New Error Code Val=> $errorcodeval \n";
            }


            if (isset($message) && $message <> "") {
                fwrite($logfd, $message);
                fwrite($logfd, $newErrorCode);
                fwrite($logfd, "Logs for the accounts that are updated for $action - $timenow - END \n");
                $message = "Please check the below Account ID and Error CODESET\n\n" . $message;
                $message .= "Please check New Error Code \n\n" . $newErrorCode;
                //mail($to, $subject, $message, $headers);
            }

            $this->sendResponse(200, CJSON::encode(array("batchfile status: " => "OK", "notificationHash: " => $notificationHash)));
            fclose($logfd);
        } catch (Exception $E) {
            echo $E;
        }
    }

}

?>