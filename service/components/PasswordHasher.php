<?php

/* * ********************************************************************
 * Filename: PasswordHasher
 * Folder: components
 * Description: Interaction with the PashwordHash helper 
 * @author Dan Tormey (For FlexScore)
 * @copyright (c) 2014
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

//include Stripe
require_once(realpath(dirname(__FILE__) . '/../helpers/PasswordHash.php'));


class PasswordHasher extends CApplicationComponent {

    private $hash_cost_log2;
    private $hash_portable;
        
    public function __Construct() {
        $this->hash_cost_log2 = 8;
        $this->hash_portable = FALSE;
    }

    public static function factory() 
    { 
        $hasher = new PasswordHasher();
        return new PasswordHash($hasher->hash_cost_log2, $hasher->hash_portable);
    }
}

?>
