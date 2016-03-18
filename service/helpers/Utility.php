<?php

/**
 * Utility all utility functions
 *
 * @author Thayub (For TruGlobal Inc)
 * @copyright (c) 2012
 */
class Utility {

    /**
     * Amount format before saving to database
     */
    public static function amountToDB($amount) {
        $b = str_replace(',', '', $amount);

        if (is_numeric($b)) {
            $amount = $b;
        }
        return $amount;
    }

     /**
     * Amount format before saving to database
     */
    public static function tickerAmountToDB($amount) {
        $b = str_replace('USD', '', $amount);

        if (is_numeric($b)) {
            $amount = $b;
        }
        return $amount;
    }
    


    /**
     * Function to remove from logs passwords and security responses sent to and 
     * received from cashedge.
    */
    public static function cleanCashedgeLog($xmlstring) {
        
        $cleanString = preg_replace("/(<UserPassword>).*?(<\/UserPassword>)/s", "", $xmlstring);
        $cleanString = preg_replace("/(<CryptParamVal>).*?(<\/CryptParamVal>)/s", "", $cleanString);
        return $cleanString;
    }
    

}

?>