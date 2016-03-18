<?php

/* * **********************************************************************
 * Filename: unsubscribe_mailchimp_users.php
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
    $updatedUserCount = 0;

    $mcemailHandle = fopen("unsubscribe_mailchimp_data.csv","r");
    $mcemailData = fgetcsv($mcemailHandle,1000,",");

    $mcmailArray = array();
    $emailIdsToUpdate = array();

    /* Retrieve all the emails from the csv file and add them to an array*/
    do {
        if (trim($mcemailData[0]) != "") {
            $mcmailArray[strtolower(trim($mcemailData[0]))] = true;
            $totalCSVFileUserCount++;
        }
    } while ($mcemailData = fgetcsv($mcemailHandle,1000,","));

    /*  For each user in the user table not currently verified,
     *  if the user's email is in the mailchimp email array, add them
     *  to a new array of users to be updated.
     */
    $allUsersSql = 'select id, LOWER(email) as email, verified from user where mailchimpstatus == 0';
    $allUsersResults = mysql_query($allUsersSql);
    if(!$allUsersResults === FALSE) {

        while ($row = mysql_fetch_array($allUsersResults)) {
            if (isset($mcmailArray[$row["email"]])) {
                $emailIdsToUpdate[] = $row["id"];
                $updatedUserCount++;
            }
            $totalDatabaseUsersCount++;
        }
    } else {
        die(mysql_error());
    }

    /*  Set the "verified" field to 1 for all users to be updated */
    if ($emailIdsToUpdate) {
       $emailIdsToUpdateSql = "UPDATE user SET mailchimpstatus = 1 WHERE id IN (".implode(',',$emailIdsToUpdate).")";
        mysql_query($emailIdsToUpdateSql) or die(mysql_error());
        echo "Users updated!\n\n";
    }

} catch (Exception $E) {
    echo $E;
}

echo "Total Users From Database: ". $totalDatabaseUsersCount. "\n";
echo "Total Users From CSV File: ". $totalCSVFileUserCount. "\n";
echo "Total Users Updated: ". $updatedUserCount. "\n\n";


$date = new DateTime();
echo "Script End Time: " . date_format($date, 'Y-m-d H:i:s'). "\n\n";

?>