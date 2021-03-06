<?php
/* * ********************************************************************
 * Filename: update_changevalue.php   
 * Folder: scripts/scorechange
 * Description: update the incorrect change value if - then + and if + then - in scorechange json data
 * @author Rajeev Ranjan (For TruGlobal Inc)
 * @copyright (c) 2014 - 2015
 * Created Date: 05-Feb-2014
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

// Initialize //
//$ini_array = parse_ini_file("C:/wamp/www/master/scripts/config/values.ini");
$ini_array = parse_ini_file("../config/values.ini"); // server path - enable on server 
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

$sql_userscore = "SELECT user_id, scorechange FROM userscore WHERE scorechange IS NOT NULL";
$res_userscore = mysql_query($sql_userscore);
$i=0;
while($row_userscore = mysql_fetch_array($res_userscore)){

	$scorechange = $row_userscore["scorechange"];
	$scorechangeObj = json_decode($scorechange,true);
    //$cnt = count(@get_object_vars($scorechangeObj));
    $cnt = count($scorechangeObj);

	
	
	// retain only last 180 elements //
	$new = array_slice($scorechangeObj, -180);

	// start array with key 1//
	$result = array();
	foreach ( $new as $key => $val ){

		if($val["Change"]!=0){
			$val["Change"] = - $val["Change"];
		}

		$result[ $key+1 ] = $val;

	}

	//echo "<pre>";

	//print_r($result);

	// change array into object //

	$object = (object) $result;

	// convery array object into JSON //
	$cS = json_encode($object,true);

	//echo $row_userscore['user_id']."=><br/>". $cS;

	//echo $row_userscore['user_id']."=>".$cnt."<hr><br/>";

	$sql_query = "UPDATE userscore SET scorechange='".$cS."' WHERE user_id='".$row_userscore['user_id']."'";
	mysql_query($sql_query);
	
	

}

//echo "<br/>Total ".$i. " records updated into userscore table";
mysql_close($link);
?>