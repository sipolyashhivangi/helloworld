<?php
/**
 * Config file used to 'setup' the basic example with your own data.
 *
 * @author      Freek Lijten <freek@procurios.nl>
 */

// Consumer key and secret. Change these into the key and secret you generated
$consumerKey 	= 'f412484e0423ff3b57729401b5d862cf400c05b6';
$consumerSecret = '99fb1ae1f6c5d47f74932786ee089b1e43b3e36e';

// Access token and secret. Change these into the ones you received at the end of the OAuth handshake cycle
$token			= '2eda4fe5fb5ed9f72d92ca16f11fccf030dd0779';
$tokenSecret	= 'ff38af410acbaa219c1d4f5208ada0f8efceadf7ms';

// Endpoints, at least change the urls to where you left the endpoint scripts
$apiURL	 		= 'http://localhost/oauth/src/example/provider/api.php';
$accessURL	 	= 'http://localhost/oauth/src/example/provider/access_token.php';
$requestURL 	= 'http://localhost/oauth/src/example/provider/request_token.php';
$authorizeURL   = 'http://localhost/oauth/src/example/provider/authorize.php';
$callbackURL    = 'http://localhost/oauth/src/example/consumer/get_access_token.php';