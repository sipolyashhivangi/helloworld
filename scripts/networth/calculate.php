<?php

/**
 * Usage: php calculate.php
 */
//$ini_array = parse_ini_file("/var/www/dev/scripts/config/values.ini"); // enable on server - server path
$ini_array = parse_ini_file("../config/values.ini");
$dbhost = $ini_array["dbhost"];
$dbname1 = $ini_array["dbname1"];
$dbname2 = $ini_array["dbname2"];
$dbuser = $ini_array["dbuser"];
$dbpassword = $ini_array["dbpassword"];

$link = mysql_connect($dbhost, $dbuser, $dbpassword);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db($dbname1);


$option = getopt("u:");

if (isset($option["u"])) {
    $userSql = "SELECT id FROM user WHERE isactive = '1' AND id = " . $option["u"];
} else {
    $userSql = "SELECT id FROM user WHERE isactive = '1'";
}

$queryRes = mysql_query($userSql);
while ($row = mysql_fetch_array($queryRes)) {

    calculcateTodayNetworth($row['id']);

}

mysql_free_result($queryRes);
mysql_close($link);
/**
 *
 */
function calculcateTodayNetworth($user_id) {



    $userSumOfAssets = "select sum(balance) as total from assets where type not in ('SS','PENS','EDUC','VEHI') and livehere <> 1 and status=0 and user_id=" . $user_id;
    $totalCashValue = "select sum(cashvalue) as total_cashvalue from insurance where type in ('LIFE') and lifeinstype <> 64 and status = 0 and user_id=" . $user_id;
    $userSumOfOtherAssets = "select sum(balance) as total from assets where (type in ('EDUC','VEHI') OR (type='PROP' and livehere=1)) and status=0 and user_id=" . $user_id;
    $totalDebts = "select sum(balowed) as totalDebts from debts where status = 0 and monthly_payoff_balances = 0 and user_id=" . $user_id." having totalDebts  is not null";

    $userAssetQ1 = mysql_query($userSumOfAssets);
    $userAssetQ2 = mysql_query($totalCashValue);
    $userAssetQ3 = mysql_query($userSumOfOtherAssets);
    $userDebtQ = mysql_query($totalDebts);

    if ($userDebtQ) {
        $userDebtR = mysql_fetch_array($userDebtQ);
    } else {
        $userDebtR["totalDebts"] = 0;
    }


    if ($userAssetQ1) {
        $userAssetR1 = mysql_fetch_array($userAssetQ1);
    } else {
        $userAssetR1["total"] = 0;
    }

    if ($userAssetQ2) {
        $userAssetR2 = mysql_fetch_array($userAssetQ2);
    } else {
        $userAssetR2["total_cashvalue"] = 0;
    }

    if ($userAssetQ3) {
        $userAssetR3 = mysql_fetch_array($userAssetQ3);
    } else {
        $userAssetR3["total"] = 0;
    }

    //Need to find a Better way
    $userAssetR1["total"] = ($userAssetR1["total"]>0)?$userAssetR1["total"]:0;
    $userDebtR["totalDebts"] = ($userDebtR["totalDebts"]>0)?$userDebtR["totalDebts"]:0;
    $userAssetR3["total"] = ($userAssetR3["total"]>0)?$userAssetR3["total"]:0;

    $userSumOfAssets = $userAssetR1["total"] + $userAssetR2["total_cashvalue"] + $userAssetR3["total"];
    $userSumOfDebts = $userDebtR["totalDebts"];
    $networth = $userSumOfAssets - $userSumOfDebts;
    //return $netWorth;

    //saving into the table //


    $networthSql = "SELECT id, val FROM networth WHERE user_id = " . $user_id;
    $networthRes = mysql_query($networthSql);
    $networthRow = mysql_fetch_array($networthRes);
    $today = date("Y-m-d");
    if ($networthRow) {
        $id = $networthRow["id"];
        $val = $networthRow["val"];
        $valDecode = json_decode($val, true);
        $valDecode[$today] = $networth;
        $val1 = json_encode($valDecode);

        $updateSql1 = "UPDATE networth SET val = '" . $val1 . "' WHERE user_id=" . $user_id;
        mysql_query($updateSql1);
        //update to the table
    } else {
        //insert
        //insert to database

        $val = json_encode(array($today => $networth));
        $insertSql = "INSERT INTO networth(user_id, val) VALUES (" . $user_id . ", '" . $val . "')";
        mysql_query($insertSql);
    }
}
?>