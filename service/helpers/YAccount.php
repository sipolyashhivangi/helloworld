<?php
/**
 * YAccount handles the yodlee account assigning for user
 *  - Currently we are taking the limit from the global configuration (yodleeAccLimit)
 * 
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012
 */
class YAccount {
    
    /**
     * 
     */
    public function getYAccount($userObject){
        //limit of the yodlee account 
        $limitPerAcc = Yii::app()->params->yodleeAccLimit;
        
        $yodleeAccountObject = new YodleeAccount;
        
        //send the last yodlee account record
        $yodleeAccRes = $yodleeAccountObject->findBySql("SELECT id FROM `yodleeaccount` WHERE linkedusers < ".$limitPerAcc." limit 1");
        $yodleeAccId = 0;
        if (isset($yodleeAccRes) && !empty($yodleeAccRes)){
            $yodleeAccId = $yodleeAccRes->id;
        }else{
            //create new yodlee account and send the id 
            $wsYodleeObject = Yii::app()->yodlee;

            //create user object 
            $wsYodleeUserObject = new stdClass();
            $wsYodleeUserObject->yodleeEmailAddress = $userObject->email;
            $wsYodleeUserObject->yodleeUserName = $userObject->email;

            //generate yodlee password
            $wsYodleeUserObject->yodleePassword = $wsYodleeObject->generatePassword();

            $wsYodleeAccountId = $wsYodleeObject->addUserRegistrationService($wsYodleeUserObject);

            $yodleeAccountObject->username = $userObject->email;
            $yodleeAccountObject->ypassword = $wsYodleeUserObject->yodleePassword;
            $yodleeAccountObject->yuid = $wsYodleeAccountId;
            
            //save the details to table 
            $yodleeAccountObject->save();
                    
            $yodleeAccId = $yodleeAccountObject->id;
        }
        return $yodleeAccId;
    }
}
?>