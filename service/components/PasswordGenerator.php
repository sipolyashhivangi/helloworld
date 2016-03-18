<?php

/* * ********************************************************************
 * Filename: Password Generator
 * Folder: components
 * Description: Interaction with the PWGen helper file
 * @author Dan Tormey (For FlexScore)
 * @copyright (c) 2014
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

//include Stripe
require_once(realpath(dirname(__FILE__) . '/../helpers/PWGen.php'));


class PasswordGenerator extends CApplicationComponent {

    private $length;
    private $secure;
    private $numerals;
    private $capitalize;
    private $ambiguous;
    private $no_vovels;
    private $symbols;
         
    public function __Construct() {
        $this->length = 25;
        $this->secure = true;
        $this->numerals = true; 
        $this->capitalize = true;
        $this->ambiguous = false; 
        $this->no_vovels = false; 
        $this->symbols = false;
    }

    public static function factory() 
    { 
        $passwordGenerator = new PasswordGenerator();
        return new PWGen(
            $passwordGenerator->length,
            $passwordGenerator->secure,
            $passwordGenerator->numerals,
            $passwordGenerator->capitalize,
            $passwordGenerator->ambiguous,
            $passwordGenerator->no_vovels,
            $passwordGenerator->symbols
        );
    }
}

?>
