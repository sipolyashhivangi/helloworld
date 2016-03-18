<?php

/* * **********************************************************************
 * Filename: set_montecarlo_probabilities.php
 * Modified by : Dan Tormey
 * ********************************************************************** */


// class Sengine is necessary for unserialize.
class Sengine{

}

$date = new DateTime();
echo "Start Time: " . date_format($date, 'Y-m-d H:i:s'). "\n\n";

try {

    $ini_array = parse_ini_file("values.ini");
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

    $totalUserCount = 0;
    $updatedUserCount = 0;


    /* Update the userscore table montecarloprobability field for any user that has
     * a monteCarlo value > 0 in the scoredetails field.  */
    $userScoreSql = 'select id, scoredetails from userscore';
    $userScoreResults = mysql_query($userScoreSql);
    if(!$userScoreResults === FALSE) {
        while ($row = mysql_fetch_array($userScoreResults)) {

            $totalUserCount++;
            $userscoreObj = array();
            $userscoreObj = unserialize($row["scoredetails"]);
            if ($userscoreObj && $userscoreObj->monteCarlo) {
                $userscoreUpdateSql = 'UPDATE userscore set montecarloprobability = "'.$userscoreObj->monteCarlo.'" where id = '. $row['id'];
                mysql_query($userscoreUpdateSql) or die(mysql_error());
                $updatedUserCount++;
            }
        }
    } else {
        die(mysql_error());
    }
} catch (Exception $E) {
    echo $E;
}

echo "totalUserCount: ". $totalUserCount. "\n\n";
echo "updatedUserCount: ". $updatedUserCount. "\n\n";


$date = new DateTime();
echo "End Time: " . date_format($date, 'Y-m-d H:i:s'). "\n\n";

?>