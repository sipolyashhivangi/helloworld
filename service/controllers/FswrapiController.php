<?php

/* * ********************************************************************
 * Filename: FswrapiController.php
 * Folder: controllers
 * Description: Wrapper controller for mobile service calls.
 * @author Manju Sheshadri (For TruGlobal Inc)
 * Reviewed By Ganesh Manoharan(For TruGlobal Inc)
 * @copyright (c) 2014 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

//include nu soap client
require_once(realpath(dirname(__FILE__) . '/../lib/OAuth2/Autoloader.php'));

 class FswrapiController extends Scontroller {
    function actionGettoken(){
    	Autoloader::register();
		$this->storage = new OAuth2\Storage\FlexscorePdo(array('dsn' => $params['db.host'], 'username' => $params['db.username'], 'password' => $params['db.password']));
		$this->server = new OAuth2\Server($this->storage);

		// Add the "Client Credentials" grant type (it is the simplest of the grant types)
		//$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));

		// Add the "Authorization Code" grant type (this is where the oauth magic happens)
		//$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));

		// Add the "User Credentials" grant type (this is where the oauth magic happens)
		$this->server->addGrantType(new OAuth2\GrantType\FlexscoreUserCredentials($this->storage));
    }

    function actionAuthorize(){
    	Autoloader::register();
		$this->storage = new OAuth2\Storage\FlexscorePdo(array('dsn' => $params['db.host'], 'username' => $params['db.username'], 'password' => $params['db.password']));
		$this->server = new OAuth2\Server($this->storage);

		// Add the "Client Credentials" grant type (it is the simplest of the grant types)
		//$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));

		// Add the "Authorization Code" grant type (this is where the oauth magic happens)
		//$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));

		// Add the "User Credentials" grant type (this is where the oauth magic happens)
		$this->server->addGrantType(new OAuth2\GrantType\FlexscoreUserCredentials($this->storage));
    }

 }
?>