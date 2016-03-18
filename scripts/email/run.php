<?php

//  Run every Tuesday morning at 6 AM:
//      00 06 * * 2 /usr/bin/env php /path/to/scripts/email/run.php
//
//  Or run manually:
//      php run.php --task="notifications" OBSOLETE TILL WE FIX CASHEDGE
//      php run.php --task="scorechange"
//      php run.php --task="learningcenter"
//      php run.php --task="advisordashboard"
//      php run.php --task="reminduser"
//
//  @TODO
//  We need to handle year stitching to prevent emails
//  from being sent on back-to_back weeks.

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

$DS = DIRECTORY_SEPARATOR;
$service_dir    = realpath(dirname(dirname(__DIR__)))
                . "{$DS}service";
$config_dir     = "{$service_dir}{$DS}config";
$config         = require "{$config_dir}{$DS}params-local.php";

$log = basename(__FILE__, ".php").'.log';
$log_message = [];

$log_message[] = '['.microtime(true).'] Started: ' . __FILE__;

function autoloader ($class)
{
    $class = strtolower($class);
    $files = glob(__DIR__."/{bin,lib}/{$class}.php", GLOB_BRACE);
    foreach ($files as $file):
        require $file;
    endforeach;
}
spl_autoload_register('autoloader');
$email = dirname(dirname(__DIR__)).sprintf(
    '%1$sservice%1$shelpers%1$semail%1$sEmail.php',
    DIRECTORY_SEPARATOR
);
require $email;
$options = getopt(false,
[
    "task::",
]);
$tasks = array();

if (!isset($options['task'])) {
    $i = date("W") - 1;
    if ($i % 8 === 0) {
// OBSOLETE TILL WE FIX CASHEDGE        $tasks[] = 'notifications';
    }
    if ($i % 8 === 2) {
        $tasks[] = 'scorechange';
        $tasks[] = 'advisordashboard';
    }
    if ($i % 8 === 4) {
        $tasks[] = 'learningcenter';
    }
    if ($i % 8 === 6) {
        $tasks[] = 'scorechange';
        $tasks[] = 'advisordashboard';
    }
    //$tasks[] = 'reminduser';
    $log_message[] = '['.microtime(true)."] Automated execution: " . implode("|",$tasks) . "...";
} else {
    $tasks[] = $options['task'];
    $log_message[] = '['.microtime(true)."] Manual execution: " . implode("|",$tasks) . "...";
}

$log_message[] = '['.microtime(true)."] Running: " . implode("|",$tasks) . "...";

foreach($tasks as $task) {
    $t = $task;
    $task .= "_controller";
    if (!class_exists($task)):
        $log_message[] = '['.microtime(true)."] Failed: {$t} not found...";
        die("ERROR: {$t} not found." . PHP_EOL);
    endif;
    $task = new $task();
    $task->run($config);

    $log_message[] = '['.microtime(true)."] Complete: {$t}.";
}

$eml_message = "<ul style='text-align:left;'>\n<li>".join("</li>\n<li>", $log_message)."</li>\n</ul>";
$log_message = join(PHP_EOL, $log_message)."<hr/>";

file_put_contents($log, $log_message, FILE_APPEND);


$email = new Email();
$email->recipients = [
    [
        'name'  => 'Melroy Saldanha',
        'email' => 'melroy@flexscore.com',
    ],
    [
        'name'  => 'Dan Tormey',
        'email' => 'dan@flexscore.com',
    ]
];
$email->subject = 'Automated Script Executed: ' . pathinfo(__FILE__, PATHINFO_FILENAME);
$email->body = $eml_message;
$email->send();


?>
