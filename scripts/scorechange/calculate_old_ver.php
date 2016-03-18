<?php

// Initialize //
//$ini_array = parse_ini_file("/var/www/dev/scripts/config/values.ini"); // server path - enable on server 
$ini_array = parse_ini_file("../config/values.ini");
$dbhost = $ini_array["dbhost"];
$dbname1 = $ini_array["dbname1"];
$dbname2 = $ini_array["dbname2"];
$dbuser = $ini_array["dbuser"];
$dbpassword = $ini_array["dbpassword"];
// Connect DB
$link = mysql_connect($dbhost, $dbuser, $dbpassword);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db($dbname1);
// Fetch Users
$option = getopt("u:");
if (isset($option["u"])) {
    $userSql = "SELECT uid, id, user_id, totalscore, scorechange FROM user u INNER JOIN userscore s ON u.uid=s.user_id WHERE isactive = '1' AND uid = " . $option["u"];
} else {
    $userSql = "SELECT uid, id, user_id, totalscore, scorechange FROM user u INNER JOIN userscore s ON u.uid=s.user_id WHERE isactive = '1'";
}
// Main loop
$queryRes = mysql_query($userSql);
while ($row = mysql_fetch_array($queryRes)) {
   // $networthSql = "SELECT id, user_id, totalscore, scorechange FROM userscore WHERE user_id = " . $row["uid"];
   // $queryRes1 = mysql_query($networthSql);
   // $row1 = mysql_fetch_array($queryRes1);
    $today = date("Y-m-d");
    //if ($row1) {
        $id = $row["id"];
        $uid = $row["user_id"];
        $totalscore = $row["totalscore"];
        $scorechange = $row["scorechange"];
        calculcateTodayScore($totalscore, $scorechange, $uid,$id);
       
   // }
}

mysql_free_result($queryRes);
mysql_close($link);

/**
 * Calculating user Score for Today. 
 * Parameters $totalscore, $scorechange
 */
function calculcateTodayScore($totalscore, $scorechange, $uid,$id) {
    
    //calculate score
    if (isset($scorechange) && $scorechange != "") {
        $cS1 = json_decode($scorechange);
        $totalC = count(get_object_vars($cS1));
        if ($totalC > 180) {
            //push the 1st day info and create
            @array_shift($cS1);
        }
        // Need to calculate 90 days scorechange here - Added by Alex on 12-11-2013
        $arraytest = (array) $cS1;
        if ($totalC <= 90 && current(array_keys($arraytest)) <= 90) {
            $count = 0;
            $scorechange = 0;
            foreach ($arraytest as $k => $value) {
                $scorechange = $scorechange + $value->Total;
                $count++;
            }
            if ($scorechange > 0) {
                $avgTot = $scorechange / $count;
            } else {
                $avgTot = 0;
            }
            $updateSql2 = "UPDATE userscore SET nintydays = '" . $avgTot . "' WHERE user_id = " . $uid;
            mysql_query($updateSql2);
        }
        // End code - Added by Alex on 12-11-2013
        // Calculate todays scorechange here
        if(isset($cS1->$totalC)){
        $scoreC = $cS1->$totalC->Total - $totalscore;
        $dayScore = array("Total" => $totalscore, "Change" => $scoreC, "Reason" => "");
        $changeS = array($totalC + 1 => $dayScore);
        $changeS1 = (array) $cS1;
        $changeS1[$totalC + 1] = $dayScore;
        $cS = json_encode($changeS1);
        }
    } else {
        $dayScore = array("Total" => $totalscore, "Change" => "0", "Reason" => "");
        $changeS = array("1" => $dayScore);
        $cS = json_encode($changeS);
    }
    
    //return $cS;
    
     // Update userscore table
        $updateSql1 = "UPDATE userscore SET scorechange = '" . $cS . "' WHERE id=" . $id;
        mysql_query($updateSql1);
}

?>