<?php

/* * **********************************************************************
 * Filename: check_montecarlo_discrepancies.php
 * Modified by : Dan Tormey
 * ********************************************************************** */


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


    $resultScoreArray = array();
    /* Update the userscore table montecarloprobability field for any user that has
     * a monteCarlo value > 0 in the scoredetails field.  */
    $scoreChangeSql = 'select * from scorechange';
    $scoreChangeRecord = mysql_query($scoreChangeSql);

    if(!$scoreChangeRecord === FALSE) {
        while ($row = mysql_fetch_array($scoreChangeRecord)) {

            $totalUserCount++;

            $userscoreObj = array();
            $userscoreObj = json_decode($row["scorechange"], true);

            if ($row["scorechange"]!="") {
                $sparsedScoreObjArray = json_decode($row["scorechange"], true);
                $first = strtotime(date(key($sparsedScoreObjArray)));
                $current = strtotime(date("Y-m-d"));


                while ($first <= $current) {
                    $dateKey = date("Y-m-d", $first);
                    if (isset($sparsedScoreObjArray["$dateKey"]) && $dateKey > "2015-03-01") {
                        if (abs($sparsedScoreObjArray["$dateKey"]["Change"]) > 100) {

                            $scoreChangeValuesSql = 'select a.montecarloprobability as montecarloprobability_original, a.lastruntimestamp as lastruntimestamp_original,
                                b.montecarloprobability as montecarloprobability_new, b.lastruntimestamp as lastruntimestamp_new,
                                (a.montecarloprobability - b.montecarloprobability) as difference_change
                                from montecarlouser_original a
                                join montecarlouser b
                                where a.user_id = b.user_id and a.user_id = '.$row["user_id"];
                            $scoreChangeValues = mysql_query($scoreChangeValuesSql);
                            $diffRow = mysql_fetch_array($scoreChangeValues);

                            if ($diffRow) {
                                $tempResult = array("date" => $dateKey,
                                    "total" =>  $sparsedScoreObjArray["$dateKey"]["Total"],
                                    "change" => $sparsedScoreObjArray["$dateKey"]["Change"],
                                    "montecarloprobability_original" => $diffRow["montecarloprobability_original"],
                                    "lastruntimestamp_original" => $diffRow["lastruntimestamp_original"],
                                    "montecarloprobability_new" => $diffRow["montecarloprobability_new"],
                                    "lastruntimestamp_new" => $diffRow["lastruntimestamp_new"],
                                    "difference_change" => $diffRow["difference_change"]);
                            }

                            $resultScoreArray[$row["user_id"]] = $tempResult;
                        }
                    }
                    $first = strtotime('+1 day', $first);
                }
            }
        }

        printf("%-8s %-14s %-8s %-8s %-10s %-17s %-15s %-15s %-15s \n",  "User Id", "Date", "Total", "Change", "MC_Orig", "LastRun_Orig", "MC_New", "LastRun_New", "Difference");
        printf("----------------------------------------------------------------------------------------------------------------------\n");

        foreach ($resultScoreArray as $key => $resultScore) {
            printf("%-8s %-14s %-8s %-8s %-10s %-17s %-15s %-15s %-15s\n",  $key, $resultScore["date"], $resultScore["total"], $resultScore["change"],
                    $resultScore["montecarloprobability_original"], substr($resultScore["lastruntimestamp_original"],0,10),
                    $resultScore["montecarloprobability_new"], substr($resultScore["lastruntimestamp_new"],0,10),
                    $resultScore["difference_change"]);
        }

        echo "\n\nChanged user count: ".count($resultScoreArray)."\n";

    } else {
        die(mysql_error());
    }
} catch (Exception $E) {
    echo $E;
}

echo "totalUserCount: ". $totalUserCount. "\n\n";


$date = new DateTime();
echo "End Time: " . date_format($date, 'Y-m-d H:i:s'). "\n\n";

$fp = fopen('data.txt', 'w');



fclose($fp);


?>


