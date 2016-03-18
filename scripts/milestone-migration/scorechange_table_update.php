<?php
/**
 * Usage: php scorechange_table_update.php
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

$sql_userscore = "SELECT user_id, scorechange FROM userscore";
$res_userscore = mysql_query($sql_userscore);
$i=0;
while($row_userscore = mysql_fetch_array($res_userscore)){

    $sql_scorechange = "INSERT INTO scorechange SET user_id = '".$row_userscore['user_id']."', scorechange = '".$row_userscore['scorechange']."'";
    mysql_query($sql_scorechange);
  $i++;
}

echo "Total ".$i. "records inserted into scorechange table";
mysql_close($link);
?>