<?php

/* * **********************************************************************
 * Filename: cas-batch.php
 * Folder: scripts/batchfiles
 * Description:  process nightly Cashedge batchfiles
 * @copyright (c) 2013 - 2014
 * Change History:
 * Co-authored by: Ganesh Manoharan
 * Validated by : Ganesh Manoharan
 * Modified by : Dan Tormey
 * Version         Author               Change Description
 * ********************************************************************** */
try {
    require_once(realpath(dirname(__FILE__) . '/../../service/helpers/Messages.php'));


    $ini_array = parse_ini_file("../config/values.ini");
    $dbhost = $ini_array["dbhost"];
    $dbname1 = $ini_array["dbname1"];
    $dbname2 = $ini_array["dbname2"];
    $dbuser = $ini_array["dbuser"];
    $dbpassword = $ini_array["dbpassword"];
    $batchFilePath = $ini_array["batchpath"];

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


    $notificationHash = array();
    $batchfileErrorMessages = Messages::getBatchfileErrorMessages();


    $to = "melroy@flexscore.com, ganesh.m@truglobal.com, dan@flexscore.com";
    $subject = "Batch File Processing - Error";
    $message = "";
    $newErrorCode = "";
    $from = "noreply@flexscore.com";
    $headers = "From:" . $from;


    $link = mysql_connect($dbhost, $dbuser, $dbpassword);

    if (!$link) {
        die('Could not connect: ' . mysql_error());
    }
    mysql_select_db($dbname1);



//CAS-BANK.csv
//
//0 = HOMEID,
//1 = ACCTID,
//2 = LASTUPDATESTATUSCODE,
//3 = HARVESTID,
//4 = ERRORCODE,
//5 = LASTUPDATEATTEMPT,
//6 = LASTSUCCESSFULUPDATE,
//7 = RECLASSIFICATIONREQUIRED,
//8 = CURRENTBALANCE,
//9 = AVAILABLEBALANCE,
//10 = ANNUALPERCENTAGEYIELD,
//11 = INTERESTYTD,
//12 = INTERESTRATE
    $timenow = "\"" . date("Y-m-d H:i:s") . "\"";
    $actlogfile = $batchFilePath . "/account.log";
    $logfd = fopen($actlogfile, "a+");
    fwrite($logfd, "Logs for the accounts that are updated $timenow - BEGIN \n");

    if (($handle = fopen($bankFile, "r+")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {

            if (count($data) == 0) {
                break;
            }

            $acctid = trim($data[1]);
            $errorCode = $data[4];
            $getAssetSql = "SELECT user_id, FILoginAcctId, balance, growthrate, modifiedtimestamp FROM assets WHERE context='AUTO' and actid = " . $acctid;
            $queryRes1 = mysql_query($getAssetSql);
            $row1 = mysql_fetch_array($queryRes1);


            if ($row1) {
                $modifiedTimestamp = (string) $row1["modifiedtimestamp"];
                $dbModifiedTimestamp = date_create($modifiedTimestamp);
                if ($data[2] != "F") {
                    if (isset($data[6]) && $data[6] != "") {
                        $ceLastSuccessfulUpdate = date_create(substr_replace(substr_replace($data[6], "-", 6, 0), "-", 4, 0));

                        if ($ceLastSuccessfulUpdate > $dbModifiedTimestamp) {

                            $currentBal = (isset($data[8]) && $data[8] != "") ? $data[8] : $row1["balance"];
                            $interestRate = (isset($data[12]) && $data[12] != "") ? $data[12] : $row1["growthrate"];

                            if (isset($currentBal)) {
                                $updateSql1 = "update assets set balance = " . $currentBal . ", growthrate = " . $interestRate . ", modifiedtimestamp = '" . date_format($ceLastSuccessfulUpdate, 'Y-m-d H:i:s') . "' where context='AUTO' and actid=" . $acctid;
                                mysql_query($updateSql1);
                                fwrite($logfd, "Success:Updated Bank Account ID: $acctid \n");
                            }
                        }
                    }
                } else {  // it is an error
                    $message .= "Bank Account ID: " . $acctid . " with Error Code: " . $errorCode . "\n";

                    if (isset($row1['user_id']) && isset($row1['FILoginAcctId'])) {
                        $notificationHash[$row1['user_id'] . "|" . $row1['FILoginAcctId']] = $errorCode;
                    }
                }
            }
            $message .= "Bank Account ID: " . $acctid . " Not Found \n";
        }
    }
    fclose($handle);
//for debug
//echo "CAS-BANK.csv notifications<br>";
//print_r($notificationHash)."<br><br>";
// CAS-CREDITCARD.csv
//
//    [0] => HOMEID
//    [1] => ACCTID
//    [2] => LASTUPDATESTATUSCODE
//    [3] => HARVESTID
//    [4] => ERRORCODE
//    [5] => LASTUPDATEATTEMPT
//    [6] => LASTSUCCESSFULUPDATE
//    [7] => RECLASSIFICATIONREQUIRED
//    [8] => PAYMENTDUEDATE
//    [9] => CREDITLIMIT
//    [10] => AVAILCREDIT
//    [11] => OUTSTANDING
//    [12] => CASHADVANCELIMIT
//    [13] => AVAILABLECASHLIMIT
//    [14] => CURRENTREWARDSBALANCE
//    [15] => POINTSACCRUED
//    [16] => POINTSREDEEMED
//    [17] => STATEMENTBALANCE
//    [18] => MINIMUMPAYMENTDUE
//    [19] => FINANCECHARGES
//    [20] => PURCHASEAPR
//    [21] => ADVANCEAPR
    if (($handle = fopen($ccFile, "r+")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {

            if (count($data) == 0) {
                break;
            }

            $acctid = trim($data[1]);
            $errorCode = $data[4];

            $getDebtSql = "SELECT user_id, FILoginAcctId, balowed, apr, modifiedtimestamp FROM debts WHERE context='AUTO' and actid = " . $acctid;
            $queryRes1 = mysql_query($getDebtSql);
            $row1 = mysql_fetch_array($queryRes1);

            if ($row1) {
                $modifiedTimestamp = (string) $row1["modifiedtimestamp"];
                $dbModifiedTimestamp = date_create($modifiedTimestamp);
                if ($data[2] != "F") {
                    if (isset($data[6]) && $data[6] != "") {
                        $ceLastSuccessfulUpdate = date_create(substr_replace(substr_replace($data[6], "-", 6, 0), "-", 4, 0));

                        if ($ceLastSuccessfulUpdate > $dbModifiedTimestamp) {
                            $outstandingBal = (isset($data[11]) && $data[11] != "") ? $data[11] : $row1["balowed"];
                            $apr = (isset($data[20]) && $data[20] != "") ? $data[20] : $row1["apr"];

                            if (isset($outstandingBal)) {
                                $updateSqlCC = "update debts set balowed = " . $outstandingBal . ", apr=" . $apr . ", modifiedtimestamp = '" . date_format($ceLastSuccessfulUpdate, 'Y-m-d H:i:s') . "' where context='AUTO' and actid=" . $acctid;
                                mysql_query($updateSqlCC);
                                fwrite($logfd, "Success:Updated Credit Card Account ID: $acctid \n");
                            }
                        }
                    }
                } else {  // it is an error
                    $message .= "Credit Card Account ID: $acctid with Error Code: $errorCode\n";
                    if (isset($row1['user_id']) && isset($row1['FILoginAcctId'])) {
                        $notificationHash[$row1['user_id'] . "|" . $row1['FILoginAcctId']] = $errorCode;
                    }
                }
            }
            $message .= "Credit Card Account ID: " . $acctid . " Not Found \n";
        }
    }
    fclose($handle);

//for debug
//echo "CAS-INVESTMENT notifications (includes CAS-BANK.csv)<br>";
//print_r($notificationHash)."<br><br>";
// Read the file CAS-INVESTMENT.csv
//   [0] => HOMEID
//   [1] => ACCTID
//   [2] => LASTUPDATESTATUSCODE
//   [3] => HARVESTID
//   [4] => ERRORCODE
//   [5] => LASTUPDATEATTEMPT
//   [6] => LASTSUCCESSFULUPDATE
//   [7] => RECLASSIFICATIONREQUIRED
//   [8] => CASHBALANCE
//   [9] => MONEYMARKETBALANCE
//   [10] => SECURITIESBALANCE
//   [11] => TOTALBROKERAGEACCOUNTVALUE
//   [12] => DAILYCHANGE
//   [13] => PERCENTAGECHANGE
//   [14] => CURRENTVESTEDBALANCE
//   [15] => TOTALEMPLOYERCONTRIBUTION
//   [16] => TOTALOTHERCONTRIBUTION
//   [17] => DEFERRALPERCENTAGE
//   [18] => EMPLOYERPROFITSHARING
//   [19] => LOANAMOUNT
//   [20] => DEATHBENEFIT
//   [21] => CASHSURRENDERVALUE
//   [22] => INSURANCEPREMIUM
//   [23] => PAYMENTDUEDATE
//   [24] => INTERESTRATE
    if (($handle = fopen($investmentFile, "r+")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {

            if (count($data) == 0) {
                break;
            }

            $acctid = trim($data[1]);
            $errorCode = $data[4];

            $getInvSql = "SELECT user_id, FILoginAcctId, balance, contribution, empcontribution, modifiedtimestamp FROM assets WHERE context='AUTO' and actid = " . $acctid;
            $queryRes1 = mysql_query($getInvSql);
            $row1 = mysql_fetch_array($queryRes1);

            if ($row1) {
                $modifiedTimestamp = (string) $row1["modifiedtimestamp"];
                $dbModifiedTimestamp = date_create($modifiedTimestamp);
                if ($data[2] != "F") {
                    if (isset($data[6]) && $data[6] != "") {
                        $ceLastSuccessfulUpdate = date_create(substr_replace(substr_replace($data[6], "-", 6, 0), "-", 4, 0));

                        if ($ceLastSuccessfulUpdate > $dbModifiedTimestamp) {
                            $brokVal = (isset($data[11]) && $data[11] != "") ? $data[11] : $row1["balance"];
                            $employerContribution = (isset($data[15]) && $data[15] != "") ? $data[15] : $row1["empcontribution"];
                            $otherContribution = (isset($data[16]) && $data[16] != "") ? $data[16] : $row1["contribution"];

                            if (isset($brokVal)) {
                                $updateInvSql = "update assets set balance = " . $brokVal . ", contribution=" . $otherContribution . ",empcontribution=" . $employerContribution . ", modifiedtimestamp = '" . date_format($ceLastSuccessfulUpdate, 'Y-m-d H:i:s') . "' where context='AUTO' and actid=" . $acctid;
                                mysql_query($updateInvSql);
                                fwrite($logfd, "Success:Updated Investment Account ID: $acctid \n");
                            }
                        }
                    }
                } else {  // it is an error
                    $message .= "Investment Account ID: $acctid with Error Code: $errorCode \n";

                    if (isset($row1['user_id']) && isset($row1['FILoginAcctId'])) {
                        $notificationHash[$row1['user_id'] . "|" . $row1['FILoginAcctId']] = $errorCode;
                    }
                }
            }
            $message .= "Investment Account ID: " . $acctid . " Not Found \n";
        }
    }

    fclose($handle);


//for debug
//echo "<br>".print_r($notificationHash)."<br><br>";
// Read the file CAS-OTHER.csv
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
// The other file needs to go through the debts, assets, and possibly insurance tables.
    if (($handle = fopen($othersFile, "r+")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {

            if (count($data) == 0) {
                break;
            }

            $otherFound = false;
            $acctid = trim($data[1]);
            $errorCode = $data[4];

            $getDebtSql = "SELECT user_id, FILoginAcctId, balowed, apr, modifiedtimestamp FROM debts WHERE context='AUTO' and actid = " . $acctid;
            $queryRes1 = mysql_query($getDebtSql);
            $row1 = mysql_fetch_array($queryRes1);

            if ($row1) {
                $otherFound = true;
                $modifiedTimestamp = (string) $row1["modifiedtimestamp"];
                $dbModifiedTimestamp = date_create($modifiedTimestamp);
                if ($data[2] != "F") {
                    if (isset($data[6]) && $data[6] != "") {
                        $ceLastSuccessfulUpdate = date_create(substr_replace(substr_replace($data[6], "-", 6, 0), "-", 4, 0));

                        if ($ceLastSuccessfulUpdate > $dbModifiedTimestamp) {
                            $outstandingBal = (isset($data[8]) && $data[8] != "") ? $data[8] : $row1["balowed"];
                            $apr = (isset($data[13]) && $data[13] != "") ? $data[13] : $row1["apr"];

                            if (isset($outstandingBal)) {
                                $updateSqlCC = "update debts set balowed = " . $outstandingBal . ", apr=" . $apr . ", modifiedtimestamp = '" . date_format($ceLastSuccessfulUpdate, 'Y-m-d H:i:s') . "' where context='AUTO' and actid=" . $acctid;
                                mysql_query($updateSqlCC);
                                fwrite($logfd, "Success:Updated Other Debt Account ID: $acctid \n");
                            }
                        }
                    }
                } else {  // it is an error
                    $message .= "Other Debt Account ID: $acctid with Error Code: $errorCode \n";

                    if (isset($row1['user_id']) && isset($row1['FILoginAcctId'])) {
                        $notificationHash[$row1['user_id'] . "|" . $row1['FILoginAcctId']] = $errorCode;
                    }
                }
            }
            $message .= "Other Debt Account ID: " . $acctid . " Not Found \n";
            /*  Not sure yet if insurance is needed.  There are no insurance records in the latest csv file.
              if ( $otherFound == false) {
              $getInsuranceSql = "SELECT FILoginAcctId FROM insurance WHERE actid = " . $acctid;
              $queryRes2 = mysql_query($getInsuranceSql);
              $row1 = mysql_fetch_array($queryRes1);
              if ( $queryRes2 ) {
              $otherFound = true;
              $modifiedTimestamp = (string) $row1["modifiedtimestamp"];
              $dbModifiedTimestamp =  date_create($modifiedTimestamp);
              if ($data[2] != "F") {
              if (isset($data[6]) && $data[6] != "") {
              $ceLastSuccessfulUpdate = date_create(substr_replace(substr_replace($data[6], "-", 6, 0), "-", 4, 0));

              if ( $ceLastSuccessfulUpdate > $dbModifiedTimestamp ) {
              $outstandingBal = (isset($data[8]) && $data[8] != "") ? $data[8] : $row1["balowed"];
              $apr = (isset($data[13]) && $data[13] != "") ? $data[13] : $row1["apr"];

              if (isset($outstandingBal)) {
              $updateSqlCC = "update debts set balowed = " . $outstandingBal . ", apr=" . $apr . ", modifiedtimestamp = '".date_format($ceLastSuccessfulUpdate, 'Y-m-d H:i:s')."' where context='AUTO' and actid=" . $acctid;
              $updateSqlInsurance = "update insurance set balowed = " . $outstandingBal . ", apr=" . $apr . " where context='AUTO' and actid=" . $acctid;
              mysql_query($updateSqlInsurance);
              }
              }
              }
              }
              else {  // it is an error
              $message .= "Other Insurance Account ID: $data[1] with Error Code: $data[4]\n";

              $row1 = mysql_fetch_array($queryRes2);
              if (isset($row1['user_id']) && isset($row1['FILoginAcctId']) ) {
              $notificationHash[$row1['user_id'] . "|" . $row1['FILoginAcctId']] = $errorCode;
              }
              }
              }
              } */
            if ($otherFound == false) {

                $getAssetSql = "SELECT user_id, FILoginAcctId, balance, growthrate, modifiedtimestamp FROM assets WHERE context='AUTO' and actid = " . $acctid;
                $queryRes1 = mysql_query($getAssetSql);
                $row1 = mysql_fetch_array($queryRes1);

                if ($row1) {
                    $modifiedTimestamp = (string) $row1["modifiedtimestamp"];
                    $dbModifiedTimestamp = date_create($modifiedTimestamp);
                    if ($data[2] != "F") {
                        if (isset($data[6]) && $data[6] != "") {
                            $ceLastSuccessfulUpdate = date_create(substr_replace(substr_replace($data[6], "-", 6, 0), "-", 4, 0));

                            if ($ceLastSuccessfulUpdate > $dbModifiedTimestamp) {

                                $currentBal = (isset($data[8]) && $data[8] != "") ? $data[8] : $row1["balance"];
                                $interestRate = (isset($data[12]) && $data[12] != "") ? $data[12] : $row1["growthrate"];

                                if (isset($currentBal)) {
                                    $updateSql1 = "update assets set balance = " . $currentBal . ", growthrate = " . $interestRate . ", modifiedtimestamp = '" . date_format($ceLastSuccessfulUpdate, 'Y-m-d H:i:s') . "' where context='AUTO' and actid=" . $acctid;
                                    mysql_query($updateSql1);
                                    fwrite($logfd, "Success:Updated Other Asset Account ID: $acctid \n");
                                }
                            }
                        }
                    } else {  // it is an error
                        $message .= "Other Asset Account ID: " . $acctid . " with Error Code: " . $errorCode . "\n";

                        if (isset($row1['user_id']) && isset($row1['FILoginAcctId'])) {
                            $notificationHash[$row1['user_id'] . "|" . $row1['FILoginAcctId']] = $errorCode;
                        }
                    }
                }
                $message .= "Other Asset Account ID: " . $acctid . " Not Found \n";
            }
        }
    }
    fclose($handle);



//for debug
//echo "<br>".print_r($notificationHash)."<br><br>";
// Create the notifications:
// Read all of the user_id|FILoginAcctId combinations in the notificationHash
// and insert them into the notifications table.

    foreach ($notificationHash as $notificationkey => $errorcodeval) {
        if (isset($batchfileErrorMessages[$errorcodeval])) {
            $userFiLogin = explode("|", $notificationkey);
            $user_id = $userFiLogin[0];
            $FILoginAcctId = $userFiLogin[1];
            $msg = $batchfileErrorMessages[$errorcodeval]["msg"];
            $getCashedgeitemSql = "SELECT id, name FROM cashedgeitem WHERE FILoginAcctId = " . $FILoginAcctId . " AND user_id = " . $user_id;
            $queryRes = mysql_query($getCashedgeitemSql);
            while ($row = mysql_fetch_assoc($queryRes)) {
                $context = "CONNECT";
                $type = "ERROR";
                $msgArray = array(
                    'id' => $row["id"],
                    'name' => $row["name"],
                    'filoginacctid' => $FILoginAcctId,
                    'msg' => $msg,
                    'template' => "info"
                );
                $msgJson = json_encode($msgArray);
                $stat = 1;
                $flag = 1;
                $insertSql = "INSERT INTO notification(user_id, msg, context, type, stat, batchflag) VALUES (" . $user_id . ", '" . $msgJson . "', '" . $context . "', '" . $type . "'," . $stat . "," . $flag . ")";
                mysql_query($insertSql);

                echo "FILoginAcctId: " . $FILoginAcctId . "<br> Error Code: " . $errorcodeval . "<br> Message: " . $msg . "<br><br><br> \n\n";
            }
        }
        $newErrorCode .= "New Error Code Val=> $errorcodeval \n";
    }



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
                $eachTicker = array(
                    'ticker' => $data[5],
                    'secdesc' => $data[6],
                    'unitprice' => $data[8],
                    'unit' => $data[7],
                    'marketvalue' => $data[9],
                    'amount' => $data[9]
                );
                $securitiesHash[$data[1]] = $eachTicker;
                continue;
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
        $tickervals = filter_var($tickervals, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        $invpos = json_encode($tickervals);

        if (isset($invpos)) {
            $updateSql1 = "update assets set invpos = '" . $invpos . "' where actid=" . $acctid;
            mysql_query($updateSql1);
        }
        //for debug
        //echo $rowSecurities++.".<br>";
        //echo $acctid."<br>".$invpos."<br><br><br>";
    }


    if (isset($message)) {
        fwrite($logfd, $message);
        fwrite($logfd, $newErrorCode);
        fwrite($logfd, "Logs for the accounts that are updated $timenow - END \n");
        $message = "Please check the below Account ID and Error CODESET\n\n" . $message;
        $message .= "Please check New Error Code \n\n" . $newErrorCode;
        //mail($to, $subject, $message, $headers);
    }


    mysql_close($link);
    fclose($logfd);
} catch (Exception $E) {
    echo $E;
}
?>