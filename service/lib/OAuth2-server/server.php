<?php
$dsn      = 'mysql:dbname=leapscoremaster;host=localhost';
$username = 'root';
$password = '';

ini_set('display_errors',1);error_reporting(E_ALL);

require_once('../OAuth2/Autoloader.php');
OAuth2\Autoloader::register();

$storage = new OAuth2\Storage\FlexscorePdo(array());

$server = new OAuth2\Server($storage);

// Add the "Client Credentials" grant type (it is the simplest of the grant types)
//$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));

// Add the "Authorization Code" grant type (this is where the oauth magic happens)
//$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));

// Add the "User Credentials" grant type (this is where the oauth magic happens)
$server->addGrantType(new OAuth2\GrantType\FlexscoreUserCredentials($storage));

?>
