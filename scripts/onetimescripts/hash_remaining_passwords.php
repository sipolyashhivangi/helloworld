<?php

/* * **********************************************************************
 * Filename: hash_remaining_passwords.php
 * Modified by : Dan Tormey
 * ********************************************************************** */
require_once(realpath(dirname(__FILE__) . '/../../service/helpers/PWGen.php'));
require_once(realpath(dirname(__FILE__) . '/../../service/helpers/PasswordHash.php'));


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

    $advisorPasswordCount = 0;
    $userPasswordCount = 0;
    $personalInfoCount = 0;
    $blankPasswordCount = 0;


    /* Update user passwords that are more than three months old */
    $oldPasswordsSql = 'select id, email, lastaccesstimestamp, password from user where length(password) < 40';
    $queryResults = mysql_query($oldPasswordsSql);

    echo "Passwords to update: ".mysql_num_rows($queryResults)."\n";

    if (!$queryResults === FALSE) {
        while ($row = mysql_fetch_array($queryResults)) {
//            echo "user: ". $row['email'];
            $passwordGenerator = new PWGen(25, true, true, true, false, false, false);
            $password = $passwordGenerator->generate();
//            echo " | new password: ".$password;
            $hasher = new PasswordHash(8, FALSE);
            $hashedPassword = $hasher->HashPassword($password);

//            echo " | new hashedpassword: ".$hashedPassword."\n";
            $resetPasswordSql = 'UPDATE user set password = "'.$hashedPassword.'", passwordupdated = 1 where id = '. $row['id'];
            mysql_query($resetPasswordSql) or die(mysql_error());
            $userPasswordCount++;
        }
    } else {
        die(mysql_error());
    }
    echo "Passwords updated: ".$userPasswordCount."\n";


    /* Update lastaccesstimestamp = "0000-00-00 00:00:00"*/
    $blankLastAccessSql = 'select id, email, lastaccesstimestamp, password from user where lastaccesstimestamp = "0000-00-00 00:00:00"';
    $queryResults = mysql_query($blankLastAccessSql);
    if (!$queryResults === FALSE) {
        while ($row = mysql_fetch_array($queryResults)) {
//            echo "blank timestamp: ".$row['email']." | timestamp: ".$row['lastaccesstimestamp']."\n";
            $resetBlankTimestampsSql = 'UPDATE user set lastaccesstimestamp = NOW() where id = '. $row['id'];
            mysql_query($resetBlankTimestampsSql) or die(mysql_error());
            $blankPasswordCount++;
        }
    } else {
        die(mysql_error());
    }

    echo "0000-00-00 00:00:00 lastaccesstimestamps updated: ".$blankPasswordCount."\n\n";

} catch (Exception $E) {
    echo $E;
}

$date = new DateTime();
echo "Script End Time: " . date_format($date, 'Y-m-d H:i:s'). "\n\n";


?>