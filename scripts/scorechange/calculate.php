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
if (!$link) {
    die('Could not connect: ' . mysqli_error());
}

mysqli_select_db($link, $dbname1);
// Fetch Users
$option = getopt("u:");
if (isset($option["u"])) {
    $userSql = "SELECT u.id as user_id, s.id as userscore_id, s.user_id, ROUND(s.totalscore + 250 * (IF(mcu.montecarloprobability is NULL, s.montecarloprobability, mcu.montecarloprobability) - s.montecarloprobability)) as totalscore FROM user u INNER JOIN userscore s ON u.id=s.user_id LEFT JOIN montecarlouser mcu ON u.id=mcu.user_id WHERE u.isactive = '1' AND u.id = " . $option["u"];
} else {
    $userSql = "SELECT u.id as user_id, s.id as userscore_id, s.user_id, ROUND(s.totalscore + 250 * (IF(mcu.montecarloprobability is NULL, s.montecarloprobability, mcu.montecarloprobability) - s.montecarloprobability)) as totalscore FROM user u INNER JOIN userscore s ON u.id=s.user_id LEFT JOIN montecarlouser mcu ON u.id=mcu.user_id WHERE u.isactive = '1'";
}

// Main loop
$queryRes = mysqli_query($link, $userSql);
while ($row = mysqli_fetch_array($queryRes, MYSQLI_ASSOC)) {
    $scorechangeSql = "SELECT scorechange FROM scorechange WHERE user_id = " . $row["user_id"];
    $queryRes1 = mysqli_query($link, $scorechangeSql);
    $row1 = mysqli_fetch_array($queryRes1, MYSQLI_ASSOC);

        $id = $row["id"];
        $user_id = $row["user_id"];
        $totalscore = $row["totalscore"];
        $scorechange = $row1["scorechange"];

        $val1 = calculateTodayScore($totalscore, $scorechange, $user_id, $link);
        if ($row1) {
            $updateqry = "UPDATE scorechange SET scorechange = '".$val1."' WHERE user_id='".$user_id."'";
            mysqli_query($link, $updateqry);
        } else {
            $insertqry = "INSERT INTO scorechange SET scorechange = '" . $val1 . "', user_id='".$user_id."'";
            mysqli_query($link, $insertqry);
        }
}

/**
 * Calculating user Score for Today.
 * Parameters $totalscore, $scorechange, $user_id->User Id
 */
function calculateTodayScore($totalscore, $scorechange, $user_id, $link) {

    $today = date("Y-m-d");
    $cS = '';

    // IF when scorechange entries already exists for user append current scorechange - Changed by Rajeev on 12th Aug 2014
    if ($scorechange != "") {

        // get the scorechange json
        $cS1 = json_decode($scorechange);
        $cS1Array = (array) $cS1;

        // Start code - need to calculate 90 days scorechange average here - Added by Alex on 12-11-2013 - Changed by Rajeev on 12th Aug 2014
        $firstDay = strtotime('-90 days', strtotime(date('Y-m-d')));
        $lastDay = strtotime(date("Y-m-d"));

        if(count($cS1Array) > 0) {
            $count = 0;
            $score = 0;

            $firstScore = null;
            $dateKey = date("Y-m-d", $firstDay);
            if(!isset($cS1Array["$dateKey"])) {
                $firstKey = current(array_keys($cS1Array));
                $earliestDay = strtotime($firstKey);
                while($earliestDay < $firstDay) {
                    $dateKey = date("Y-m-d", $earliestDay);
                    if (isset($cS1Array["$dateKey"])) {
                        $firstScore = $cS1Array["$dateKey"]->Total;
                    }
                    $earliestDay = strtotime('+1 day', $earliestDay);
                }
            }
            $currentScore = $firstScore;

            while ($firstDay < $lastDay) {
                $dateKey = date("Y-m-d", $firstDay);
                if (isset($cS1Array["$dateKey"])) {
                    $currentScore = $cS1Array["$dateKey"]->Total;
                }
                if(isset($currentScore)) {
                    $score = $score + $currentScore;
                    $count++;
                }
                $firstDay = strtotime('+1 day', $firstDay);
            }
            $avgTot = 0;
            if ($count > 0) {
                $avgTot = round($score / $count);
            }

            $updateSql2 = "UPDATE userscore SET nintydays = '" . $avgTot . "' WHERE user_id = " . $user_id;
            mysqli_query($link, $updateSql2);
        }
        // End code - Added by Alex on 12-11-2013 - Changed by Rajeev on 12th Aug 2014

        // Start code - Populate current date scorechange - Changed by Rajeev on 12th Aug 2014
        if(count($cS1Array) > 0) {
            // populate new array element when change is positive value
            if(!isset($cS1Array["$today"])) {
                $lastArrElement = end($cS1Array);
                $scoreC = $totalscore - $lastArrElement->Total;
                if($scoreC != 0) {
                    $changeS1 = (array) $cS1;
                    $dayScore = array("Total" => $totalscore, "Change" => $scoreC, "Reason" => "");
                    $changeS1[$today] = $dayScore;
                    $cS = json_encode($changeS1);
                } else {
                    $cS = $scorechange;
                }
            } elseif(isset($cS1Array["$today"])) { // if the script runs more than once in a day
                if(count($cS1Array) > 1) {
                    $lastArrElement = array_slice($cS1Array, -2, 1);
                    $secondLastElement = array_pop($lastArrElement);
                    $scoreC = $totalscore - $secondLastElement->Total;
                    if($scoreC != 0) {
                        $changeS1 = (array) $cS1;
                        $dayScore = array("Total" => $totalscore, "Change" => $scoreC, "Reason" => "");
                        $changeS1[$today] = $dayScore;
                        $cS = json_encode($changeS1);
                    } else {
                        $changeS1 = (array) $cS1;
                        unset($changeS1[$today]);
                        $cS = json_encode($changeS1);
                    }
                } else {
                    $cS = $scorechange;
                }
            } else {
                $cS = $scorechange;
            }
        }
        // End code - Populate current date scorechange - Changed by Rajeev on 12th Aug 2014

    } else { // ELSE when scorechange entry does not exists populate first entry - Changed by Rajeev on 12th Aug 2014
        $dayScore = array("Total" => $totalscore, "Change" => "0", "Reason" => "");
        $cS = json_encode(array($today => $dayScore));
    }

//  TODO: need a loop that now goes through and makes sure that the first element is <= 185 days in the past
//  TODO: First: Ignore anything before 185 days
//  TODO: Second: Add first day (185 days behind). If it doesnt exist then find the previous score and it
//  TODO: Third: Add remaining days <= 185 days behind
    return $cS;

}

?>