<?php

/* * **********************************************************************
 * Filename: verify_mailchimp_users.php
 * Modified by : Dan Tormey
 * ********************************************************************** */


$date = new DateTime();
echo "\nScript Start Time: " . date_format($date, 'Y-m-d H:i:s'). "\n\n";

try {

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

    $totalDatabaseUsersCount = 0;
    $totalCSVFileUserCount = 0;
    $verifiedIs0Count = 0;
    $verifiedIs1Count = 0;
    $mcEmailsNotInDbCount = 0;


    $mcemailHandle = fopen("verify_mailchimp_data.csv","r");
    $mcemailData = fgetcsv($mcemailHandle,1000,",");

    $mcEmailArray = array();
    $emailIdsToUpdate = array();
    $mcEmailsVerifiedIs0 = array();
    $mcEmailsVerifiedIs1 = array();
    $mcEmailsNotInDb = array();


    /* Retrieve all the emails from the csv file and add them to an array*/
    do {
        if (trim($mcemailData[0]) != "") {
            $mcEmailArray[strtolower(trim($mcemailData[0]))] = true;
            $totalCSVFileUserCount++;
        }
    } while ($mcemailData = fgetcsv($mcemailHandle,1000,","));

    /*  For each user in the user table not currently verified,
     *  if the user's email is in the mailchimp email array, add them
     *  to a new array of users to be updated.
     */
    $allDbUsersSql = 'select id, LOWER(email) as email, verified from user';
    $allDbUsersResults = mysql_query($allDbUsersSql);

    echo "Total Users in Database: ".mysql_num_rows($allDbUsersResults)."\n";

    if(!$allDbUsersResults === FALSE) {

        while ($row = mysql_fetch_array($allDbUsersResults)) {
            if (isset($mcEmailArray[$row["email"]]) && $row["verified"] == 0) {
                $emailIdsToUpdate[] = $row["id"];
                $mcEmailsVerifiedIs0[$row["email"]] = true;
                $verifiedIs0Count++;
            }
            else if (isset($mcEmailArray[$row["email"]]) && $row["verified"] == 1) {
                $mcEmailsVerifiedIs1[$row["email"]] = true;
                $verifiedIs1Count++;
            }
        }
    } else {
        die(mysql_error());
    }

    /*  Set the "verified" field to 1 for all users to be updated */
    if ($emailIdsToUpdate) {
       $emailIdsToUpdateSql = "UPDATE user SET verified = 1 WHERE id IN (".implode(',',$emailIdsToUpdate).")";
        mysql_query($emailIdsToUpdateSql) or die(mysql_error());
    }

    echo "Total Users From Mailchimp CSV File: ". $totalCSVFileUserCount. "\n";
    echo "Total Database Users Newly Verified: ". $verifiedIs0Count. "\n";
    echo "Total Database Users Already Verified: ". $verifiedIs1Count. "\n";

    foreach ($mcEmailArray as $key => $value) {
        if ( !isset($mcEmailsVerifiedIs0[$key]) && !isset($mcEmailsVerifiedIs1[$key]) ) {
            $mcEmailsNotInDb[$key] = true;
            $mcEmailsNotInDbCount++;
        }
    }
    echo "Total Mailchimp Users Not in Database: ". $mcEmailsNotInDbCount. "\n\n";

    $date = new DateTime();
    $displayDate = date_format($date, 'Y-m-d_H-i-s');

    $emailFile = fopen('./mailchimp_email_analysis_'.$displayDate.'.txt', 'w');

    fwrite($emailFile, "Mailchimp Users Updated to Verified in Database, Count: ".$verifiedIs0Count.", Runtime: ".$displayDate."\n");
    fwrite($emailFile, "===========================================================\n");
    foreach ($mcEmailArray as $mcEmail => $value) {
        if (isset($mcEmailsVerifiedIs0[$mcEmail])) {
            fwrite($emailFile, $mcEmail.",1\n");
        }
    }
    fwrite($emailFile, "\n\n\nMailchimp Users Already Verified in Database, Count: ".$verifiedIs1Count.", Runtime: ".$displayDate."\n");
    fwrite($emailFile, "===========================================================\n");
    foreach ($mcEmailArray as $mcEmail => $value) {
        if (isset($mcEmailsVerifiedIs1[$mcEmail])) {
            fwrite($emailFile, $mcEmail.",2\n");
        }
    }
    fwrite($emailFile, "\n\n\nMailchimp Users Not In Database, Count: ".$mcEmailsNotInDbCount.", Runtime: ".$displayDate."\n");
    fwrite($emailFile, "===========================================================\n");
    foreach ($mcEmailArray as $mcEmail => $value) {
        if (isset($mcEmailsNotInDb[$mcEmail])) {
            fwrite($emailFile, $mcEmail.",3\n");
        }
    }
    fclose($emailFile);
    mysql_close($link);

} catch (Exception $E) {
    echo $E;
}


$date = new DateTime();
echo "Script End Time: " . date_format($date, 'Y-m-d H:i:s'). "\n\n";

?>