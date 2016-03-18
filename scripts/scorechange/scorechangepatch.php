<?php

// Initialize //
//$ini_array = parse_ini_file("C:/wamp/www/master/scripts/config/values.ini");
$ini_array = parse_ini_file("../config/values.ini"); // server path - enable on server
$dbhost = $ini_array["dbhost"];
$dbname1 = $ini_array["dbname1"];
$dbname2 = $ini_array["dbname2"];
$dbuser = $ini_array["dbuser"];
$dbpassword = $ini_array["dbpassword"];
// Connect DB
$link = mysqli_connect($dbhost, $dbuser, $dbpassword);
if (mysqli_connect_errno()) {
    die('Could not connect: ' . mysqli_connect_error());
}

mysqli_select_db($link,$dbname1);
// Fetch Users
$option = getopt("u:");
$scorechangeSql = "SELECT user_id,scorechange FROM scorechange WHERE patchstatus = 0";


// Main loop
$queryRes = mysqli_query($link, $scorechangeSql);
while ($row = mysqli_fetch_array($queryRes)) {
        $user_id = $row["user_id"];
        echo $user_id . "\n";
        $scorechange = $row["scorechange"];

        // add one more key 'Date' to the existing object array.
        $newScoreChangeObj = changePreviousKeys($scorechange);
        $newScoreChangeObj = (object) $newScoreChangeObj;
        $updateqry = "UPDATE scorechange SET scorechange = '".json_encode($newScoreChangeObj)."', patchstatus='1' WHERE user_id='".$user_id."'";

        mysqli_query($link, $updateqry);
}

/* Add 'Date' to the exisiting scoreChange object array
 * Parameter: $scoreChange - Score Change Object Array
*/

function changePreviousKeys($scoreChange) {

        $scoreChange = json_decode($scoreChange);

        $scoreChangeObjectToArray = (array) $scoreChange;
        $scoreChangeObjectToArray = array_reverse($scoreChangeObjectToArray);

        $currentDate = date('Y-m-d', strtotime(' +1 day'));
        $currentTotal = 0;
        $nextTotal = 0;
        $resultScoreArray = array();
        $i=1;
        foreach ($scoreChangeObjectToArray as $key => $value) {

            $currentTotal = $value->Total;
            $previousDate = date('Y-m-d', strtotime($currentDate .' -1 day'));
            // set current date to previous date
            $currentDate = $previousDate;
            $value->Date = $previousDate;

            if($value->Change == 0 && $key==1) {
                $resultScoreArray[$value->Date]['Total'] = $currentTotal;
                $resultScoreArray[$value->Date]['Change'] = $value->Change;
                $resultScoreArray[$value->Date]['Reason'] = $value->Reason;
            }
            if($value->Change != 0) {

                $nextTotal = $currentTotal;
                //$resultScoreArray[$i]['Date'] = $value->Date;
                $resultScoreArray[$value->Date]['Total'] = $currentTotal;
                $resultScoreArray[$value->Date]['Change'] = $value->Change;
                $resultScoreArray[$value->Date]['Reason'] = $value->Reason;
                $i++;
               // } //end: if
            }// end: if
        } //end: for each
                ksort($resultScoreArray);
                return ($resultScoreArray);

    }//end: function changePreviousKeys


?>


