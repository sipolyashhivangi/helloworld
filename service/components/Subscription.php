<?php

/* * ********************************************************************
 * Filename: Stripe.php
 * Folder: components
 * Description: Interaction with the Stripe payment system
 * @author Dan Tormey (For FlexScore)
 * @copyright (c) 2014
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

//include Stripe
require_once(realpath(dirname(__FILE__) . '/../lib/stripe/Stripe.php'));


class Subscription extends CApplicationComponent {

    public $secret_key;

    public function __Construct() {
        $this->secret_key = Yii::app()->params['stripe.secret_key'];
    }


    public static function factory()
    {
        $subscriptionObj = new Subscription();
        Stripe::setApiKey($subscriptionObj->secret_key);
    }
}

?>
