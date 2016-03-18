<?php

/* * **********************************************************************
 * Filename: hash_old_passwords.php
 * Modified by : Dan Tormey
 * ********************************************************************** */
require_once(realpath(dirname(__FILE__) . '/../../service/helpers/PWGen.php'));
require_once(realpath(dirname(__FILE__) . '/../../service/helpers/PasswordHash.php'));

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

    $advisorUnsubscribecodeCount = 0;
    $userUnsubscribecodeCount = 0;

    /* Update advisor passwords that are more than three months old */
    $oldAdvisorPasswordsSql = 'select id, email, lastaccesstimestamp, unsubscribecode from advisor';
    $queryAdvisorResults = mysql_query($oldAdvisorPasswordsSql);
    if(!$queryAdvisorResults === FALSE) {
        while ($row = mysql_fetch_array($queryAdvisorResults)) {
//            echo "advisor: ". $row['email'];
            $passwordGenerator = new PWGen(25, true, true, true, false, false, false);
            $password = $passwordGenerator->generate();
//            echo " | new password: ".$password;
            $hasher = new PasswordHash(8, FALSE);
            $hashedPassword = $hasher->HashPassword($password);

//            echo " | new hashedpassword: ".$hashedPassword."\n";
            $resetPasswordSql = 'UPDATE advisor set unsubscribecode = "'.$hashedPassword.'" where id = '. $row['id'];
            mysql_query($resetPasswordSql) or die(mysql_error());
            $advisorUnsubscribecodeCount++;
        }
    } else {
        die(mysql_error());
    }
echo "advisorPasswordCount: ".$advisorUnsubscribecodeCount."\n";


    /* Update user passwords that are more than three months old */
    $oldPasswordsSql = 'select id, email, lastaccesstimestamp, unsubscribecode from user';
    $queryResults = mysql_query($oldPasswordsSql);
    if (!$queryResults === FALSE) {
        while ($row = mysql_fetch_array($queryResults)) {
//            echo "user: ". $row['email'];
            $passwordGenerator = new PWGen(25, true, true, true, false, false, false);
            $password = $passwordGenerator->generate();
//            echo " | new password: ".$password;
            $hasher = new PasswordHash(8, FALSE);
            $hashedPassword = $hasher->HashPassword($password);

//            echo " | new hashedpassword: ".$hashedPassword."\n";
            $resetPasswordSql = 'UPDATE user set unsubscribecode = "'.$hashedPassword.'" where id = '. $row['id'];
            mysql_query($resetPasswordSql) or die(mysql_error());
            $userUnsubscribecodeCount++;
        }
    } else {
        die(mysql_error());
    }
echo "userPasswordCount: ".$userUnsubscribecodeCount."\n";



} catch (Exception $E) {
    echo $E;
}

?>