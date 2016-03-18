<?php

/* * ********************************************************************
 * Filename: CashEdge.php
 * Folder: components
 * Description: CashEdge Component class handles cashedge interaction
 * @author Thayub Hashim (For TruGlobal Inc)
 * @copyright (c) 2012 - 2013
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */
require_once(realpath(dirname(__FILE__) . '/../helpers/Messages.php'));
require_once(realpath(dirname(__FILE__) . '/../helpers/Utility.php'));

class CashEdge extends CApplicationComponent {

    //cashedge connection variables
    public $partnerId = null;
    public $homeId = null;
    public $adminUserId = null;
    public $adminUserPassword = null;
    //https://websrviqa.wm.cashedge.com/WealthManagementWeb/ws/
    public $url = null;

    /**
     * Generic function
     *
     */
    public function callCashEdgeApi($serviceToCall, $request) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . $serviceToCall);
        /* use method POST */
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        /* set header 'Content-Type' */
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=UTF-8'));
        /* set HTTP body */
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);


        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        
        Yii::log("Time:" . date('r'), CLogger::LEVEL_TRACE, "cashedgeapi");
        Yii::log("API Called -" . $serviceToCall, CLogger::LEVEL_TRACE, "cashedgeapi");

        $cleanedRequest = Utility::cleanCashedgeLog($request);
        Yii::log("Time:" . date('r'), CLogger::LEVEL_TRACE, "cashedgecategory");
        Yii::log("Service:" . $serviceToCall . ":Request:" . $cleanedRequest, CLogger::LEVEL_TRACE, "cashedgecategory");
        Yii::log(":Response:" . $response, CLogger::LEVEL_TRACE, "cashedgecategory");
        Yii::log(":Error:" . $error, CLogger::LEVEL_TRACE, "cashedgecategory");
        Yii::log("END ============================================================", CLogger::LEVEL_TRACE, "cashedgecategory");

        if (!$error) {
            $xml = new DOMDocument();
            if ($xml->loadXML($response)) {
                return $xml;
            } else {
                return false;
            }
        } else {
            //handle the error and send the response
            return false;
        }
        curl_close($ch);
    }

/*
* UserMgmt/createUser - add/register the user with AllData Advisor server.
*
* To add new accounts or add more account to existing parent CFI
* INPUT:  adminUserId, adminUserPassword
*         homeId,
*         username, homeId , password
* OUTPUT: CEUserID
* ERROR:
*/

    public function createUser($userObject) {
        try {
            $serviceToCall = "UserMgmt/createUser";

            //request to cash edge
            $wsCreateUserRequestXml = <<<END
<UserAddRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>LS-createUser with Opt element</RqUID>
    <SignonRq>
        <UserInfo>
            <UserID>{$this->adminUserId}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$this->adminUserPassword}</CryptVal>
            </UserPassword>
            <Role>Admin</Role>
        </UserInfo>
    </SignonRq>
    <UserAddRq>
        <UserInfo>
            <UserID>{$userObject->username}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$userObject->password}</CryptVal>
            </UserPassword>
        </UserInfo>
    </UserAddRq>
</UserAddRqHeader>
END;
            $xml = self::callCashEdgeApi($serviceToCall, $wsCreateUserRequestXml);

            if ($xml) {
                $userRes = $xml->getElementsByTagName("UserAddRs")->item(0);
                $statCode = $userRes->getElementsByTagName("StatusCode")->item(0)->nodeValue;

                if ($statCode != 0) {
                    return Messages::getERRORMapping("CASHEDGE", $statCode, '');
                }

                $CEUserID = $userRes->getElementsByTagName("CEUserID")->item(0)->nodeValue;

                return $CEUserID;
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

/*
* AccountMgmt/initiateAddAccounts - Add item to user account
*
* To add new accounts or add more account to existing parent CFI
* INPUT:  FI ID
*         trust mode,
*
* OUTPUT: HarvestID , FILoginAccountID
* ERROR:  Account already exist error
*         Error condition will occur with specific error code
*/

    public function addAccountToUser($contentServiceId, $wsFields, $userName, $userPassword, $ceSession = "") {
        try {
            $cashedgeServiceToCall = "AccountMgmt/initiateAddAccounts";

            $credentialFieldsString = "";

            foreach ($wsFields as $wsFieldKey => $wsFieldVal) {
                $credentialFieldsString .= "<FILoginParam>";
                $credentialFieldsString .= "<ParamName>" . $wsFieldKey . "</ParamName>";
                $credentialFieldsString .= "<CryptParamVal>";
                $credentialFieldsString .= "<CryptType>None</CryptType>";
                $credentialFieldsString .= "<CryptVal>" . $wsFieldVal . "</CryptVal>";
                $credentialFieldsString .= "</CryptParamVal>";
                $credentialFieldsString .= "</FILoginParam>";
            }
            if ($ceSession == "") {
                $wsAddItemRequestXml = <<<END
<HarvestAddRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>LS-initiateAddAccounts</RqUID>
    <SignonRq>
        <UserInfo>
            <UserID>{$userName}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$userPassword}</CryptVal>
            </UserPassword>
            <Role>User</Role>
        </UserInfo>
    </SignonRq>
    <HarvestAddRq>
            <AddNewAccts>
                <FIId>{$contentServiceId}</FIId>
                <TrustMode>High</TrustMode>
                <FILoginParamList>
                    {$credentialFieldsString}
                </FILoginParamList>
            </AddNewAccts>
    </HarvestAddRq>
</HarvestAddRqHeader>
END;
            } else {
                $wsAddItemRequestXml = <<<END
<HarvestAddRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>LS-initiateAddAccounts</RqUID>
    <SignonRq>
           <SessionInfo>
                 <SessionToken>{$ceSession}</SessionToken>
           </SessionInfo>
    </SignonRq>
    <HarvestAddRq>
            <AddNewAccts>
                <FIId>{$contentServiceId}</FIId>
                <TrustMode>High</TrustMode>
                <FILoginParamList>
                    {$credentialFieldsString}
                </FILoginParamList>
            </AddNewAccts>
    </HarvestAddRq>
</HarvestAddRqHeader>
END;
            }
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);
            if ($xml) {

                $addRes = $xml->getElementsByTagName("HarvestAddRs")->item(0);
                $statCode = $addRes->getElementsByTagName("StatusCode")->item(0)->nodeValue;

                if ($statCode != 0) {

                    $FILoginAcctId = isset($xml->getElementsByTagName("FILoginAcctId")->item(0)->nodeValue) ? $xml->getElementsByTagName("FILoginAcctId")->item(0)->nodeValue : 0;
                    if ($FILoginAcctId != 0){
                        return Messages::getERRORMapping("CASHEDGE", $statCode, $FILoginAcctId);
                    }else{
                        return Messages::getERRORMapping("CASHEDGE", $statCode, $statCode);
                    }
                }
                $addAccountDetails = new stdClass();
                $harvestNode = $xml->getElementsByTagName("HarvestAddID");
                $runId = $xml->getElementsByTagName("RunId");

                if ($runId->length > 0) {
                    $addAccountDetails->RunId = $runId->item(0)->nodeValue;
                }
                $FILoginAcctIdNode = $xml->getElementsByTagName("FILoginAcctId");

                $addAccountDetails->HarvestAddID = $harvestNode->item(0)->nodeValue;
                $addAccountDetails->FILoginAcctId = $FILoginAcctIdNode->item(0)->nodeValue;

                $acctHarvestStatus = $xml->getElementsByTagName("AcctHarvestStatus");
                if ($acctHarvestStatus->length > 0) {
                    $addAccountDetails->AcctHarvestStatus = $acctHarvestStatus->item(0)->nodeValue;
                }
                $classifidStatus = $xml->getElementsByTagName("ClassifiedStatus");
                if ($classifidStatus->length > 0) {
                    $addAccountDetails->ClassifiedStatus = $classifidStatus->item(0)->nodeValue;
                }
                return array("status" => 1, "details" => $addAccountDetails);
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

/*
* AccountMgmt/getAddAccountStatus - Check the account added to the user
*
*   INPUT:  HarvestID
*           HarvestAddId
*           login parameters
*
*   OUTPUT: status -> ‘completed’, ‘harvest error’ or ‘in progress’
*
*   ERROR:  login failure (301)
*           multi-factor authentication (303)
*           wrong MFA answer(304) ...
*/

    public function checkAccountStatus($runId, $harvestAddId, $userName, $userPassword, $ceSession = "") {
        try {
            $cashedgeServiceToCall = "AccountMgmt/getAddAccountStatus";

            if ($ceSession == "") {
                $wsAddItemRequestXml = <<<END
 <HarvestAddStsInqRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>LS-getAddAccountStatus</RqUID>
    <SignonRq>
        <UserInfo>
            <UserID>{$userName}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$userPassword}</CryptVal>
            </UserPassword>
            <Role>User</Role>
        </UserInfo>
    </SignonRq>
    <HarvestAddStsInqRq>
        <RunId>{$runId}</RunId>
        <HarvestAddID>{$harvestAddId}</HarvestAddID>
    </HarvestAddStsInqRq>
</HarvestAddStsInqRqHeader>
END;
            } else {
                $wsAddItemRequestXml = <<<END
 <HarvestAddStsInqRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>LS-getAddAccountStatus</RqUID>
    <SignonRq>
            <SessionInfo>
                 <SessionToken>{$ceSession}</SessionToken>
           </SessionInfo>
    </SignonRq>
    <HarvestAddStsInqRq>
        <RunId>{$runId}</RunId>
        <HarvestAddID>{$harvestAddId}</HarvestAddID>
    </HarvestAddStsInqRq>
</HarvestAddStsInqRqHeader>
END;
            }
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);

            if ($xml) {

                Yii::trace("Time:" . date('r'), "cashedgedata");
                Yii::trace(":API call:" . "RESPONSE OF getAddAccountStatus API CALL", "cashedgedata");
                Yii::trace(":Type:" . gettype($xml), "cashedgedata");
                Yii::trace(":Data:" . CVarDumper::dumpAsString($xml), "cashedgedata");
                Yii::trace("END ==============================", "cashedgedata");

                $harvestReqStat = $xml->getElementsByTagName("HarvestRqStatus");
                $harvestResponse = $xml->getElementsByTagName("HarvestAddStsInqRs");
                if ($harvestReqStat->length > 0) {
                    $status = $harvestReqStat->item(0)->nodeValue;
                    //check the status
                    if ($status == "InProgress") {

                        return array("status" => "InProgress", "msg" => "Need to call this API again");
                    } else if ($status == "Completed") {

                        $updateErrorCode = $xml->getElementsByTagName("UpdateErrorCode");
                        if ($updateErrorCode->length > 0) {
                            $updateErrorCodeVal = $updateErrorCode->item(0)->nodeValue;

                            //MFA authentication
                            if ($updateErrorCodeVal == 303) {
                                //get the FILoginParamList for MFA
                                $loginParams = $xml->getElementsByTagName("FILoginParam");
                                if ($loginParams->length > 0) {
                                    //
                                    $mfaStore = array();
                                    $parameters = "";
                                    foreach ($loginParams as $loginValues) {
                                        $mfaStoreEach = array();
                                        $mfaStoreEach["paramName"] = $loginValues->getElementsByTagName("ParamName")->item(0)->nodeValue;
                                        $parameters .= $mfaStoreEach["paramName"] . "#";
                                        $cryptParamVal = $loginValues->getElementsByTagName("CryptParamVal")->item(0);
                                        $mfaStoreEach["cryptType"] = $cryptParamVal->getElementsByTagName("CryptType")->item(0)->nodeValue;
                                        $mfaStoreEach["cryptVal"] = $cryptParamVal->getElementsByTagName("CryptVal")->item(0)->nodeValue;
                                        $mfaStore[] = $mfaStoreEach;
                                    }
                                    $HarvestID = $xml->getElementsByTagName("HarvestID")->item(0)->nodeValue;

                                    return array("status" => "MFA", "ismfa" => true, "mfaStore" => $mfaStore, "parameters" => $parameters, "harvestid" => $HarvestID);
                                }
                            } elseif ($updateErrorCodeVal == 301) {
                                return array("status" => "LOGIN", "msg" => "The login credentials are not correct");
                            }
                            elseif (in_array($updateErrorCodeVal , array(10400,12345))) {
                                return array("status" => "ERROR", "msg" => "Cannot connect now, please try again later.");
                            } else {
                                //send the message to UI
                                $returnMsg = Messages::getErrorMapping("CASHEDGE", $updateErrorCodeVal, '');
                                return array("status" => "ERROR", "msg" => $returnMsg['status'], "code" => $updateErrorCodeVal);
                            }
                        } else {
                            return array("status" => "OK", "msg" => "completed");
                        }
                    }
                } elseif ($harvestResponse->length > 0) {

                    $test = $xml->getElementsByTagName("HarvestAddStsInqRs");
                    $statusNode = $test->item(0)->nodeValue;
                    preg_match('/(\d+)/', $statusNode, $matches);
                    $statusVal = $matches[0];

                    $returnMsg = Messages::getErrorMapping("CASHEDGE", $statusVal, '');
                    return array("status" => "ERROR", "msg" => $returnMsg['status'], "code" => $statusVal);

                } {
                    return Messages::getERRORMapping("FLEXSCORE", "1002", '');
                }
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

################################################################################
#      /AccountMgmt/getNewAccounts - API is invoked after the initiateAddAccounts
#                                    & getAddAccountStatus request has completed.
################################################################################
#   INPUT:  RunID
#
#
#   OUTPUT: list of financial accounts found on the FIwebsite.
#
#   ERROR:
#
#   INFO:   At this point these accounts are not created in CashEdge database.
################################################################################

    public function getNewAccounts($RunId, $harvestAddId, $userName, $userPassword, $ceSession = "") {
        try {
            $cashedgeServiceToCall = "AccountMgmt/getNewAccounts";

            if ($ceSession == "") {
                $wsAddItemRequestXml = <<<END
   <HarvestAddFetchRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>LS-getNewAccounts</RqUID>
    <SignonRq>
        <UserInfo>
            <UserID>{$userName}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$userPassword}</CryptVal>
            </UserPassword>
            <Role>User</Role>
        </UserInfo>
    </SignonRq>
    <HarvestAddFetchRq>
        <RunId>{$RunId}</RunId>
        <HarvestAddID>{$harvestAddId}</HarvestAddID>
    </HarvestAddFetchRq>
</HarvestAddFetchRqHeader>
END;
            } else {
                $wsAddItemRequestXml = <<<END
   <HarvestAddFetchRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>LS-getNewAccounts</RqUID>
    <SignonRq>
       <SessionInfo>
                 <SessionToken>{$ceSession}</SessionToken>
           </SessionInfo>
    </SignonRq>
    <HarvestAddFetchRq>
        <RunId>{$RunId}</RunId>
        <HarvestAddID>{$harvestAddId}</HarvestAddID>
    </HarvestAddFetchRq>
</HarvestAddFetchRqHeader>
END;
            }
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);

            if ($xml) {
                $allAccountsArr = array();

                $listNode = $xml->getElementsByTagName("HarvestAddFetchAcct");
                $allAccount = "";
                $allAccountWType = "";
                if ($listNode->length > 0) {
                    foreach ($listNode as $list) {
                        $acctTypePresent = ($list->getElementsByTagName("AcctTypeId")->length > 0 ) ? true : false;
                        if ($acctTypePresent) {
                            $allAccount .= $xml->saveXML($list);
                        } else {
                            //get the account type
                            // for non account type id
                            $allAccountWType .= $xml->saveXML($list);
                        }
                    }
                    #$allAccountsArr = self::createAccounts($RunId, $harvestAddId, $allAccount, $userName, $userPassword);
                    #$result = array("createdAccounts" => $allAccountsArr, "pendingAccounts" => $allAccountWType);
                    $result = array("createdAccounts" => $allAccount, "pendingAccounts" => $allAccountWType);
                    return $result;
                    //return $allAccountsArr;
                } else {
                    $nodeV = $xml->getElementsByTagName("HarvestAddFetchRs");
                    if ($nodeV->length > 0) {
                        $code = $nodeV->item(0)->getElementsByTagName("StatusCode")->item(0)->nodeValue;
                        return Messages::getERRORMapping("CASHEDGE", $code, '');
                    }
                    return Messages::getERRORMapping("FLEXSCORE", "1001", '');
                }
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

################################################################################
#      /AccountMgmt/createAccounts - API is invoked with XML result of getNewAccounts
#                                    response.
################################################################################
#   INPUT:  RunID
#           HarvestID
#           Entire XML reponse from getNewAccounts API call.
#
#   OUTPUT:
#
#   ERROR:
#
#   INFO:   We need to call the update account API upon successful completion.
################################################################################

    public function createAccounts($runId, $harvestId, $accounts, $userName, $userPassword, $ceSession = "") {
        try {
            $accounts = str_replace("&", "&amp;", $accounts);        
            $cashedgeServiceToCall = "AccountMgmt/createAccounts";
            if ($ceSession == "") {
                $wsAddItemRequestXml = <<<END
<HarvestAddCreateAcctsRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>LS-createAccounts</RqUID>
    <SignonRq>
       <UserInfo>
            <UserID>{$userName}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$userPassword}</CryptVal>
            </UserPassword>
            <Role>User</Role>
        </UserInfo>
    </SignonRq>
    <HarvestAddCreateAcctsRq>
        <RunId>{$runId}</RunId>
        <HarvestAddID>{$harvestId}</HarvestAddID>
            <HarvestAddFetchAcctList>
            {$accounts}
            </HarvestAddFetchAcctList>
    </HarvestAddCreateAcctsRq>
</HarvestAddCreateAcctsRqHeader>
END;
            } else {
                $wsAddItemRequestXml = <<<END
<HarvestAddCreateAcctsRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>LS-createAccounts</RqUID>
    <SignonRq>
      <SessionInfo>
                 <SessionToken>{$ceSession}</SessionToken>
           </SessionInfo>
    </SignonRq>
    <HarvestAddCreateAcctsRq>
        <RunId>{$runId}</RunId>
        <HarvestAddID>{$harvestId}</HarvestAddID>
            <HarvestAddFetchAcctList>
            {$accounts}
            </HarvestAddFetchAcctList>
    </HarvestAddCreateAcctsRq>
</HarvestAddCreateAcctsRqHeader>
END;
            }
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);

            if ($xml) {
                $accountsNode = $xml->getElementsByTagName("FIAcctIdentifier");
                if ($accountsNode->length > 0) {
                    //iterate through accounts
                    foreach ($accountsNode as $account) {

                        $eachAccount = array();

                        $FIAcctId = $account->getElementsByTagName("FIAcctId")->item(0);
                        $eachAccount["AcctId"] = $FIAcctId->getElementsByTagName("AcctId")->item(0)->nodeValue;
                        $eachAccount["AcctType"] = $FIAcctId->getElementsByTagName("AcctType")->item(0)->nodeValue;

                        $eachAccount["ExtAcctType"] = $FIAcctId->getElementsByTagName("ExtAcctType")->item(0)->nodeValue;
                        $accountDetails = $account->getElementsByTagName("HarvestAddFetchAcct")->item(0);

                        $eachAccount["FIId"] = $accountDetails->getElementsByTagName("FIId")->item(0)->nodeValue;
                        $eachAccount["AcctNumber"] = $accountDetails->getElementsByTagName("AcctNumber")->item(0)->nodeValue;

                        if ($account->getElementsByTagName("FIAcctName")->length > 0) {
                            $fiiAcc = $account->getElementsByTagName("FIAcctName")->item(0);
                            $eachAccount["FIAcctNameParamName"] = $fiiAcc->getElementsByTagName("ParamName")->item(0)->nodeValue;
                            $eachAccount["FIAcctNameParamVal"] = $fiiAcc->getElementsByTagName("ParamVal")->item(0)->nodeValue;
                        }
                        $eachAccount["AcctTypeId"] = $accountDetails->getElementsByTagName("AcctTypeId")->item(0)->nodeValue;

                        if ($eachAccount["AcctType"] == "INV") {
                            //call the investment position call
                            #$ticker = self::getInvestmentPos($eachAccount["AcctId"], $eachAccount["AcctType"], $eachAccount["ExtAcctType"], $userName, $userPassword);
                            #$eachAccount["Ticker"] = $ticker;
                        }

                        if ($eachAccount["AcctType"] == "INS") {
                            $eachAccount["cashvalue"] = ($account->getElementsByTagName("CashSurrenderValue")->length > 0 ) ? $account->getElementsByTagName("CashSurrenderValue")->item(0)->nodeValue : "";
                            $eachAccount["premium"] = ($account->getElementsByTagName("InsurancePremium")->length > 0) ? $account->getElementsByTagName("InsurancePremium")->item(0)->nodeValue : "";
                            $eachAccount["deathbenefit"] = ($account->getElementsByTagName("DeathBenefit")->length > 0) ? $account->getElementsByTagName("DeathBenefit")->item(0)->nodeValue : "";
                        }

                        $fiiAccBal = $accountDetails->getElementsByTagName("AcctBal");
                        if ($fiiAccBal->length > 0) {
                            foreach ($fiiAccBal as $accBal) {

                                $accountBal = array();

                                $accountBal["AccBalType"] = $accBal->getElementsByTagName("BalType")->item(0)->nodeValue;
                                $curAmtNode = $accBal->getElementsByTagName("CurAmt")->item(0);
                                $accountBal["CurAmt"] = $curAmtNode->getElementsByTagName("Amt")->item(0)->nodeValue;
                                $accountBal["CurCode"] = $curAmtNode->getElementsByTagName("CurCode")->item(0)->nodeValue;

                                $eachAccount["AccBal"][] = $accountBal;
                            }
                        } else {
                            $accountBal = array();
                            $accountBal["CurAmt"] = 0;
                            $eachAccount["AccBal"][] = $accountBal;
                        }
                        $allAccountsArr[] = $eachAccount;
                    }

                    //return the account array
                    #if ($FILoginAcctIdID != 0){
                    # $updatedAccounts = self::updateAccounts( $FILoginAcctIdID, $userName, $userPassword, $ceSession = "");
                    # return array("status"=>"OK","updatedAccounts" => $updatedAccounts , "accounts" => $allAccountsArr);
                    #}


                    Yii::trace("Time:" . date('r'));
                    Yii::trace(":API call:" . "CREATEACCOUNTS IN CE COMPONENT", "cashedgedata");
                    Yii::trace(":Data:" . CVarDumper::dumpAsString($allAccountsArr), "cashedgedata");
                    Yii::trace("END ==============================", "cashedgedata");
                    $result = array("status" => "OK", "accounts" => $allAccountsArr);
                    return $result;
                } else {
                    $nodeV = $xml->getElementsByTagName("HarvestAddCreateAcctsRs");
                    if ($nodeV->length > 0) {
                        $code = $nodeV->item(0)->getElementsByTagName("StatusCode")->item(0)->nodeValue;
                        return Messages::getERRORMapping("CASHEDGE", $code, '');
                    } else {
                        $nodeV = $xml->getElementsByTagName("SignonRs");
                        if($nodeV){
                            #$details = $nodeV->item(0)->getElementsByTagName("StatusCode")->item(0)->nodeValue;
                            #return Messages::getERRORMapping("FLEXSCORE", $details, '');
                        }
                    }
                }
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

################################################################################
#      /HarvestAccountData/updateAccounts:
#           API is invoked after the success of create accounts to initiate/trigger
#     On Demand Account Harvesting.
################################################################################
#   INPUT:  FILoginAcctId
#
#
#   OUTPUT: HarvestID (RunID)
#
#   ERROR:
#
#   INFO:   We need to call the update account API upon successful completion.
################################################################################

    public function updateAccounts($FILoginAcctId, $userName, $userPassword, $ceSession = "") {
        try {
            $cashedgeServiceToCall = "HarvestAccountData/updateAccounts";
            if ($ceSession == "") {
                $wsAddItemRequestXml = <<<END
<HarvestRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
partnerID="{$this->partnerId}" version="version1">
    <RqUID>LS-updateAccounts</RqUID>
    <SignonRq>
        <UserInfo>
            <UserID>{$userName}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$userPassword}</CryptVal>
            </UserPassword>
            <Role>User</Role>
        </UserInfo>
      </SignonRq>
    <HarvestRq>
      <HarvestFILoginAcctList>
          <HarvestFILoginAcct>
             <FILoginAcctId>{$FILoginAcctId}</FILoginAcctId>
          </HarvestFILoginAcct>
      </HarvestFILoginAcctList>
    </HarvestRq>
</HarvestRqHeader>
END;
            } else {
                $wsAddItemRequestXml = <<<END
<HarvestRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
partnerID="{$this->partnerId}" version="version1">
    <RqUID>LS-updateAccounts</RqUID>
    <SignonRq>
      <SessionInfo>
                 <SessionToken>{$ceSession}</SessionToken>
           </SessionInfo>
      </SignonRq>
    <HarvestRq>
      <HarvestFILoginAcctList>
          <HarvestFILoginAcct>
             <FILoginAcctId>{$FILoginAcctId}</FILoginAcctId>
          </HarvestFILoginAcct>
      </HarvestFILoginAcctList>
    </HarvestRq>
</HarvestRqHeader>
END;
            }
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);
            //get new run id and harvest id and update the database
            if ($xml) {
                //get the ticker
                if (isset($xml->getElementsByTagName("RunId")->item(0)->nodeValue)){
                    $newRunId = $xml->getElementsByTagName("RunId")->item(0)->nodeValue;
                    $newHarvestAddID = $xml->getElementsByTagName("HarvestAddID")->item(0)->nodeValue;
                    return array("status" => "OK", "newharvestid" => $newHarvestAddID, "newrunid" => $newRunId);
                }else{
                    // This is added in case UpdateAccouts in called for REFRESH, and they return an error.
                    $harvest = $xml->getElementsByTagName("HarvestRs");
                    $code = $harvest->item(0)->getElementsByTagName("StatusCode")->item(0)->nodeValue;
                    $msg = $harvest->item(0)->getElementsByTagName("StatusDesc")->item(0)->nodeValue;

                    return array("status" => "OK", "errorcode" => $code, "msg" => $msg);
                }

            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

################################################################################
#      /HarvestAccountData/getHarvestStatus:
#          The API returns back the status of the update doneinthe updateAccounts API.
################################################################################
#   INPUT:  new HarvestID from the updateAccounts API call
#           new RunID from the updateAccount API call
#
#   OUTPUT: status -> completed, In progress
#
#   ERROR:
#
#   INFO:   This marks the completion of the account addition, and enabling on demand
#           harvesting
################################################################################

    public function getHarvestStatus($userName, $userPassword, $newHarvestAddID, $newRunId) {
        try {
            $cashedgeServiceToCall = "HarvestAccountData/getHarvestStatus";

            $wsAddItemRequestXml = <<<END
<?xml version="1.0" encoding="UTF-8"?>
<HarvestStsInqRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>LS-harvestAccountStatus</RqUID>
    <SignonRq>
        <UserInfo>
            <UserID>{$userName}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$userPassword}</CryptVal>
            </UserPassword>
            <Role>User</Role>
        </UserInfo>
           </SignonRq>
    <HarvestStsInqRq>
        <HarvestAddID>{$newHarvestAddID}</HarvestAddID>
        <RunId>{$newRunId}</RunId>
        <IncludeDetail>IncludeDetail0</IncludeDetail>
    </HarvestStsInqRq>
</HarvestStsInqRqHeader>
END;
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);

            if ($xml) {
                Yii::trace(":REFRESH HARVEST CASHEDGE.XML" . CVarDumper::dumpAsString($xml), "cashedgedata");
                $harStatus = $xml->getElementsByTagName("HarvestRqStatus");
                if ($harStatus->length > 0) {

                    $status = $harStatus->item(0)->nodeValue;

                    if ($status != 'Completed') {
                        sleep(5);
                        return $this->getHarvestStatus($userName, $userPassword, $newHarvestAddID, $newRunId);
                    }

                    if (isset($xml->getElementsByTagName("UpdateErrorCode")->item(0)->nodeValue)){
                        $mfaStore = array();
                        $errorCode = $errorCode = $xml->getElementsByTagName("UpdateErrorCode")->item(0)->nodeValue;

                        /* Error handling for MFA */
                        if ($errorCode == '303'){
                            $loginParams = $xml->getElementsByTagName("FILoginParam");
                                if ($loginParams->length > 0) {
                                    //$mfaStore = array();
                                    $parameters = "";
                                    $mfaStoreEach = array();
                                        foreach ($loginParams as $loginValues) {

                                            $mfaStoreEach["paramName"] = $loginValues->getElementsByTagName("ParamName")->item(0)->nodeValue;
                                            $parameters .= $mfaStoreEach["paramName"] . "#";
                                            $cryptParamVal = $loginValues->getElementsByTagName("CryptParamVal")->item(0);
                                            $mfaStoreEach["cryptType"] = $cryptParamVal->getElementsByTagName("CryptType")->item(0)->nodeValue;
                                            $mfaStoreEach["cryptVal"] = $cryptParamVal->getElementsByTagName("CryptVal")->item(0)->nodeValue;
                                            $mfaStore[] = $mfaStoreEach;
                                        }
                                }

                            Yii::trace(":REFRESH HARVEST CASHEDGE.PHP" . CVarDumper::dumpAsString($mfaStore), "cashedgedata");
                            $returnMfaStoreValue = array('status' => 'OK', 'code' => $errorCode ,"mfaStore" => $mfaStore, "parameters" => $parameters);
                            return $returnMfaStoreValue;
                            //$ok = 'ok';
                            //return $ok;
                        }


                    }


                    /* This status is used to set lsstatus in cashedgefiitem table*/
                    return array("status" => "OK", "msg" => "getHarvestStatus API comlpete");

                    /*if (!empty($mfaStore)){
                        return (array('status' => 'OK', 'message' => $errorCode ,
                                "mfaStore" => $mfaStore, "parameters" => $parameters));
                    }else{
                        return (array("status" => "OK" , "msg" => "getHarvestStatus API complete"));
                    }*/
                }
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $e) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

################################################################################
#      /HarvestAccountData/getHarvestStatus:
#          The API returns back the status of the update doneinthe updateAccounts API.
################################################################################
#   INPUT:  new HarvestID from the updateAccounts API call
#           new RunID from the updateAccount API call
#
#   OUTPUT: status -> completed, In progress
#
#   ERROR:
#
#   INFO:   This marks the completion of the account addition, and enabling on demand
#           harvesting
################################################################################

    public function getHarvestStatusMFA($FILoginAcctId, $sessionToken, $mfares, $runId) {
        try {
            $cashedgeServiceToCall = "HarvestAccountData/updateAccounts";

            $FILoginParam = '';

            //iterate over accounts
            foreach ($mfares as $res) {
                $FILoginParam .= <<<END
                <FILoginParam>
                <ParamName>{$res["ParamName"]}</ParamName>
                        <CryptParamVal>
                            <CryptType>{$res["CryptType"]}</CryptType>
                            <CryptVal>{$res["CryptVal"]}</CryptVal>
                        </CryptParamVal>
            </FILoginParam>
END;
            }

            $wsAddItemRequestXml = <<<END
<HarvestRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
  <RqUID>RefreshMFA-Answer</RqUID>
  <SignonRq>
    <SessionInfo>
      <SessionToken>{$sessionToken}</SessionToken>
    </SessionInfo>
  </SignonRq>
  <HarvestRq>
    <HarvestFILoginAcctList>
      <HarvestFILoginAcct>
        <FILoginAcctId>{$FILoginAcctId}</FILoginAcctId>
        <FILoginParamList>
                {$FILoginParam}
        </FILoginParamList>
      </HarvestFILoginAcct>
    </HarvestFILoginAcctList>
    <RunId>{$runId}</RunId>
  </HarvestRq>
</HarvestRqHeader>
END;
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);
            return (array("status" => "OK" , "msg" => $xml));
            if ($xml) {
                $harStatus = $xml->getElementsByTagName("HarvestRqStatus");
                if ($harStatus->length > 0) {

                    $status = $harStatus->item(0)->nodeValue;

                    if ($status != 'Completed') {
                        sleep(5);
                        #return $this->getHarvestStatus($userName, $userPassword, $newHarvestAddID, $newRunId);
                    }
                    if ($status == 'Completed') {
                        #return $this->getHarvestStatus($userName, $userPassword, $newHarvestAddID, $newRunId);
                    }
                }
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $e) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

################################################################################
#      /UserOFILoginCredentialsMod
#      This API is used to modify the FI login information in case wrong credentials
#       are entered.
################################################################################
#   INPUT:
#
#
#   OUTPUT:
#
#   ERROR:
#
#   INFO:
#
################################################################################

    public function updateNewFILoginInfo($userName, $userPassword, $FILoginAcctId,$wsFields) {
        try {
            $cashedgeServiceToCall = "AccountMgmt/updateAccountCredentials";

            $credentialFieldsString = '';

            foreach ($wsFields as $wsFieldKey => $wsFieldVal) {
                $credentialFieldsString .= "<FILoginParam>";
                $credentialFieldsString .= "<ParamName>" . $wsFieldKey . "</ParamName>";
                $credentialFieldsString .= "<CryptParamVal>";
                $credentialFieldsString .= "<CryptType>None</CryptType>";
                $credentialFieldsString .= "<CryptVal>" . $wsFieldVal . "</CryptVal>";
                $credentialFieldsString .= "</CryptParamVal>";
                $credentialFieldsString .= "</FILoginParam>";
            }

            $wsAddItemRequestXml = <<<END
<?xml version="1.0" encoding="UTF-8"?>
<UserOFILoginCredentialsModRqHeader xmlns="http://www.cashedge.com/wm"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
<RqUID>LS-UpdateFIinfo</RqUID>
    <SignonRq>
        <UserInfo>
            <UserID>{$userName}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$userPassword}</CryptVal>
            </UserPassword>
            <Role>User</Role>
        </UserInfo>
    </SignonRq>
<UserOFILoginCredentialsModRq>
    <FILoginAcctId>{$FILoginAcctId}</FILoginAcctId>
    <TrustMode>High</TrustMode>
    <FILoginParamList>
        {$credentialFieldsString}
    </FILoginParamList>
</UserOFILoginCredentialsModRq>
</UserOFILoginCredentialsModRqHeader>
END;

            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);
            if ($xml) {
                $nodeV = $xml->getElementsByTagName("UserOFILoginCredentialsModRs");
                $details = $nodeV->item(0)->getElementsByTagName("StatusDesc")->item(0)->nodeValue;
                return array("status" => "OK", "msg" => $details, "id" => '');

            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $e) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

##########################################################################################################################
#      /getAccountsSummary
#      This API is used to get the account summmary of an user using FILoginAcctId
##########################################################################################################################
#   INPUT:
#
#   OUTPUT:
#
#   ERROR:
#
#   INFO:
#
##########################################################################################################################

    public function getAccountsSummary($userName, $userPassword, $FILoginAcctId) {
        try {
            $cashedgeServiceToCall = "AccountDataInq/getAccountDetails";

            $wsAddItemRequestXml = <<<END
<FILoginAcctInfoListInqRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>RqUID0</RqUID>
     <SignonRq>
             <UserInfo>
                 <UserID>{$userName}</UserID>
                 <HomeID>{$this->homeId}</HomeID>
                 <UserPassword>
                     <CryptType>None</CryptType>
                     <CryptVal>{$userPassword}</CryptVal>
                 </UserPassword>
                 <Role>User</Role>
             </UserInfo>
    </SignonRq>
    <FILoginAcctInfoListInqRq>
        <FILoginAcctList>
            <HomeID>{$this->homeId}</HomeID>
            <UserID>{$userName}</UserID>
            <FILoginAcctId>$FILoginAcctId</FILoginAcctId>
         </FILoginAcctList>
           <IncludeDetail>FIAcctSummary</IncludeDetail>
</FILoginAcctInfoListInqRq>
</FILoginAcctInfoListInqRqHeader>
END;

#      <AcctType>DDA</AcctType>
#      <ExtAcctType>DDA</ExtAcctType>

            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);


            if ($xml) {
                $resStatus = $xml->getElementsByTagName("FIAcctSummary");
                $id = $xml->getElementsByTagName("FILoginAcctId");
                if ($resStatus->length > 0) {
                    return array("status" => "OK", "msg" => $resStatus, "id" => $id);
                } else {
                    return array("status" => "ERROR", "msg" => $xml);
                }
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $e) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

    public function getAccountsSummarytest($userName, $userPassword, $FILoginAcctId) {
        try {
            $cashedgeServiceToCall = "AccountDataInq/getAccountDetails";

            $wsAddItemRequestXml = <<<END
<FILoginAcctInfoListInqRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>RqUID0</RqUID>
     <SignonRq>
             <UserInfo>
                 <UserID>{$userName}</UserID>
                 <HomeID>{$this->homeId}</HomeID>
                 <UserPassword>
                     <CryptType>None</CryptType>
                     <CryptVal>{$userPassword}</CryptVal>
                 </UserPassword>
                 <Role>User</Role>
             </UserInfo>
    </SignonRq>
    <FILoginAcctInfoListInqRq>
        <FILoginAcctList>
            <HomeID>{$this->homeId}</HomeID>
            <UserID>{$userName}</UserID>
            <FILoginAcctId>$FILoginAcctId</FILoginAcctId>
         </FILoginAcctList>
           <IncludeDetail>FIAcctSummary</IncludeDetail>
           <IncludeDetail>InvAcctPos</IncludeDetail>
</FILoginAcctInfoListInqRq>
</FILoginAcctInfoListInqRqHeader>
END;
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);

            if ($xml) {
                $signOnRsNode = $xml->getElementsByTagName("SignonRs");
                $opArr = array();
                if ($signOnRsNode->length > 0) {
                    foreach ($signOnRsNode->item(0)->childNodes as $nodeV) {

                        if ($nodeV->nodeName == "SessInfo") {
                            //upate the database and set sess info
                            $opArr["session"] = $nodeV->nodeValue;
                        }
                    }
                }
                $FILoginAcctIdRs = $xml->getElementsByTagName("FILoginAcctInfo");
                if ($FILoginAcctIdRs->length > 0) {
                    foreach ($FILoginAcctIdRs->item(0)->childNodes as $nodeV) {

                        if ($nodeV->nodeName == "FILoginAcctId") {
                            //upate the database and set sess info
                            $opArr["id"] = $nodeV->nodeValue;
                        }
                    }
                }


                $allAccounts = array();
                $FILoginAcctInfoNode = $xml->getElementsByTagName("FIAcctSummary");
                if ($FILoginAcctInfoNode->length > 0) {
                    foreach ($FILoginAcctInfoNode as $account) {
                        $eachAccount = array();

                        $FIAcctId = $account->getElementsByTagName("FIAcctId")->item(0);
                        $eachAccount["AcctId"] = $FIAcctId->getElementsByTagName("AcctId")->item(0)->nodeValue;
                        $eachAccount["AcctType"] = $FIAcctId->getElementsByTagName("AcctType")->item(0)->nodeValue;

                        $eachAccount["ExtAcctType"] = $FIAcctId->getElementsByTagName("ExtAcctType")->item(0)->nodeValue;

                        if ($account->getElementsByTagName("FIAcctName")->length > 0) {
                            $fiiAcc = $account->getElementsByTagName("FIAcctName")->item(0);
                            $eachAccount["FIAcctNameParamName"] = $fiiAcc->getElementsByTagName("ParamName")->item(0)->nodeValue;
                            $eachAccount["FIAcctNameParamVal"] = $fiiAcc->getElementsByTagName("ParamVal")->item(0)->nodeValue;
                        }

                        if ($eachAccount["AcctType"] == "INS") {
                            $eachAccount["cashvalue"] = ($account->getElementsByTagName("CashSurrenderValue")->length > 0 ) ? $account->getElementsByTagName("CashSurrenderValue")->item(0)->nodeValue : "";
                            $eachAccount["premium"] = ($account->getElementsByTagName("InsurancePremium")->length > 0) ? $account->getElementsByTagName("InsurancePremium")->item(0)->nodeValue : "";
                            $eachAccount["deathbenefit"] = ($account->getElementsByTagName("DeathBenefit")->length > 0) ? $account->getElementsByTagName("DeathBenefit")->item(0)->nodeValue : "";
                        }

                        $fiiAccBal = $account->getElementsByTagName("AcctBal");
                        if ($fiiAccBal->length > 0) {

                            foreach ($fiiAccBal as $accBal) {

                                $accountBal = array();

                                $accountBal["AccBalType"] = $accBal->getElementsByTagName("BalType")->item(0)->nodeValue;
                                $curAmtNode = $accBal->getElementsByTagName("CurAmt")->item(0);
                                $accountBal["CurAmt"] = $curAmtNode->getElementsByTagName("Amt")->item(0)->nodeValue;
                                $accountBal["CurCode"] = $curAmtNode->getElementsByTagName("CurCode")->item(0)->nodeValue;

                                $eachAccount["AccBal"][] = $accountBal;

                            }
                        } else {
                            $accountBal = array();
                            $accountBal["CurAmt"] = 0;
                            $eachAccount["AccBal"][] = $accountBal;
                        }

                        /* Incase there is any MFA is required to capture the error */

                        if ($account->getElementsByTagName("UpdateErrorCode")->length > 0){
                            $UpdateErrorCode = $account->getElementsByTagName("UpdateErrorCode")->item(0)->nodeValue;
                        }

                        // Getting the classifications status:
                        $fiiAccProp = $account->getElementsByTagName("Property");
                        if ($fiiAccProp->length > 0) {
                            foreach ($fiiAccProp as $property) {
                                $prop = array();
                                $prop["Name"] = $property->getElementsByTagName("ParamName")->item(0)->nodeValue;
                                $prop["Val"] = $property->getElementsByTagName("ParamVal")->item(0)->nodeValue;

                                $eachAccount["Property"][] = $prop;

                            }
                        }

                        $allAccounts[] = $eachAccount;
                    }
                }
                $allTickers = array();
                $invAcctPos = $xml->getElementsByTagName("InvAcctPos");
                if ($invAcctPos->length > 0) {
                    foreach ($invAcctPos as $invAcc) {
                        $invPos = $invAcc->getElementsByTagName("InvPos");
                        $acctId = $invAcc->getElementsByTagName("AcctId")->item(0)->nodeValue;
                        $invPosArr = array();

                        foreach ($invPos as $invEach) {

                            $tick = $invEach->getElementsByTagName("Ticker")->item(0)->nodeValue;
                            $secDesc = $invEach->getElementsByTagName("SecDesc")->item(0)->nodeValue;
                            $unitPrice = $invEach->getElementsByTagName("UnitPrice")->item(0)->nodeValue;
                            $units = $invEach->getElementsByTagName("Units")->item(0)->nodeValue;
                            $marketValue = $invEach->getElementsByTagName("MarketValue")->item(0)->nodeValue;
                            $amount = $marketValue;//Utility::tickerAmountToDB($unitPrice)*$units;
                            $eachTicker = array(
                                'ticker' => $tick,
                                'secdesc' => $secDesc,
                                'unitprice' => $unitPrice,
                                'unit' => $units,
                                'marketvalue' => $marketValue,
                                'amount' => $amount
                            );
                            $invPosArr[] = $eachTicker;
                        }
                        $accTicker = array(
                            'acctid' => $acctId,
                            'invpos' => $invPosArr
                        );
                        $allTickers[] = $accTicker;
                    }

                }
                $opArr["accounts"] = $allAccounts;
                $opArr["tickers"] = $allTickers;
                $opArr["errorCode"] = isset($UpdateErrorCode) ? $UpdateErrorCode : 0;

                return $opArr;
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002");
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002");
        }
    }


##########################################################################################################################
#      /maintainAccount
#      This API is used to update the detail of the INV as of now.
##########################################################################################################################
#   INPUT:
#
#   OUTPUT:
#
#   ERROR:
#
#   INFO:
#
##########################################################################################################################
public function maintainAccount($userName,$userPassword,$FIId,$AcctId,$AcctType,$ExtAcctType,$AcctTypeId,$Instrument,$RetirementStatus,$AccountOwnerShip) {
        try {
            $cashedgeServiceToCall = "AccountMgmt/maintainAccount";

            $wsAddItemRequestXml =<<<END
<UserAcctModRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>RqUID010</RqUID>
     <SignonRq>
             <UserInfo>
                 <UserID>{$userName}</UserID>
                 <HomeID>{$this->homeId}</HomeID>
                 <UserPassword>
                     <CryptType>None</CryptType>
                     <CryptVal>{$userPassword}</CryptVal>
                 </UserPassword>
                 <Role>User</Role>
             </UserInfo>
    </SignonRq>
    <UserAcctModRq>
        <FIAcctMod>
            <FIId>$FIId</FIId>
            <FIAcctId>
                <AcctId>$AcctId</AcctId>
                <AcctType>$AcctType</AcctType>
                <ExtAcctType>$ExtAcctType</ExtAcctType>
            </FIAcctId>
            <AcctTypeId>$AcctTypeId</AcctTypeId>
            <Property>
                     <ParamName>Instrument</ParamName>
                     <ParamVal>$Instrument</ParamVal>
            </Property>
             <Property>
                     <ParamName>RetirementStatus</ParamName>
                     <ParamVal>$RetirementStatus</ParamVal>
            </Property>
             <Property>
                     <ParamName>AccountOwnerShip</ParamName>
                     <ParamVal>$AccountOwnerShip</ParamVal>
            </Property>
            <CurCode>USD</CurCode>
            </FIAcctMod>
    </UserAcctModRq>
</UserAcctModRqHeader>
END;
         $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);


           if ($xml) {
                $harStatus = $xml->getElementsByTagName("UserAcctModRs");

                        return array("status" => "OK" , "msg" => $harStatus);
                    }
             else {
                return Messages::getERRORMapping("FLEXSCORE", "1002",'100');
            }
        } catch (Exception $e) {
            return Messages::getERRORMapping("FLEXSCORE", "1002");
        }
    }

##########################################################################################################################
#      /ClientMgmt/signon
#      This API is used to signon and get the session token in case it has expired.
##########################################################################################################################
#   INPUT: username, password, HomeId (from currrent object)
#
#   OUTPUT: session information for that user
#
#   ERROR: Dunno
#
#   INFO: This was developed exclusively for session expiration issues when dealing with notifications
#
##########################################################################################################################


public function getNewSession($userName, $userPassword) {
        try {
            $cashedgeServiceToCall = "ClientMgmt/signon";

            $wsGetSessionXml = <<<END
<SignonRqHeader version="version1" partnerID="{$this->partnerId}" xmlns="http://www.cashedge.com/wm">
   <RqUID>FSRQID0</RqUID>
   <SignonRq>
      <UserInfo>
         <UserID>{$userName}</UserID>
         <HomeID>{$this->homeId}</HomeID>
         <UserPassword>
            <CryptType>None</CryptType>
            <CryptVal>{$userPassword}</CryptVal>
         </UserPassword>
         <Role>User</Role>
      </UserInfo>
      <GenSessInfo>false</GenSessInfo>
   </SignonRq>
</SignonRqHeader>
END;

            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsGetSessionXml);


            if ($xml) {
                $resStatus = $xml->getElementsByTagName("SignonRs");
                if ($resStatus->length > 0) {

                    $sessinfo = $xml->getElementsByTagName("SessInfo");
                    $sess = $sessinfo->item(0)->nodeValue;

                    return array("status" => "OK", "session" => $sess);
                } else {
                    return array("status" => "ERROR", "msg" => $xml);
                }
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $e) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

##########################################################################################################################
##########################################################################################################################

public function getNewSessionCheck($ceSession) {
        try {
            $cashedgeServiceToCall = "ClientMgmt/signon";

            $wsGetSessionXml = <<<END
<SignonRqHeader version="version1" partnerID="{$this->partnerId}" xmlns="http://www.cashedge.com/wm">
    <RqUID>LS-updateAccounts</RqUID>
    <SignonRq>
        <UserInfo>
            <HomeID>{$this->homeId}</HomeID>
        </UserInfo>
        <SessionInfo>
            <SessionToken>{$ceSession}</SessionToken>
        </SessionInfo>
    </SignonRq>
</SignonRqHeader>
END;

            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsGetSessionXml);


            if ($xml) {
                $resStatus = $xml->getElementsByTagName("Status");
                if ($resStatus->length > 0) {

                    $sessinfo = $xml->getElementsByTagName("SessInfo");
                    $sess = $sessinfo->item(0)->nodeValue;

                    return array("status" => "OK", "session" => $sess);
                } else {
                    return array("status" => "ERROR", "msg" => $xml);
                }
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $e) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }
##########################################################################################################################
##########################################################################################################################






































































































































































































































































































    /**
     * Add MFA values to the account after answering the security question wrongly:
     *
     */
    public function addMFAToAccountsLater($FILoginAcctId, $userName, $userPassword) {
        try {
            $cashedgeServiceToCall = "AccountMgmt/initiateAddAccounts";
            $wsAddItemRequestXml = <<<END
            <HarvestAddRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="1.4.6">
    <RqUID>initiateAddAccounts After wrong attempt OR Re adding deleted accounts</RqUID>
    <SignonRq>
        <UserInfo>
            <UserID>{$userName}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$userPassword}</CryptVal>
            </UserPassword>
            <Role>User</Role>
        </UserInfo>
    </SignonRq>
    <HarvestAddRq>
        <AddMoreAccts>
                <FILoginAcctId>{$FILoginAcctId}</FILoginAcctId>
        </AddMoreAccts>
</HarvestAddRq>
</HarvestAddRqHeader>
END;

            Yii::trace("Add MFA To Accounts Later Answer", "cashedgedata");
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);
            Yii::trace(":Data:" . CVarDumper::dumpAsString($xml), "cashedgedata");

            if ($xml) {
                $newRunID = $xml->getElementsByTagName("RunId");
                if ($newRunID->length > 0) {
                    $runId = $newRunID->item(0)->nodeValue;
                }
                $newHarID = $xml->getElementsByTagName("HarvestAddID");
                if ($newHarID->length > 0) {
                    $harId = $newHarID->item(0)->nodeValue;
                }
                return array("status" => "OK", "harvestId" => $runId, "harvestAddId" => $harId);
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

  /**
     * delete Accounta By AcctId
     *
     */



    public function deleteAccountByAcctId($FIAcctId, $userName, $userPassword, $ceSession = "") {
        try {
            $cashedgeServiceToCall = "AccountMgmt/deleteAccounts";

            if ($ceSession == "") {
                $wsAddItemRequestXml = <<<END
 <FIDeleteRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>RqUID0</RqUID>
    <SignonRq>
        <UserInfo>
            <UserID>{$userName}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$userPassword}</CryptVal>
            </UserPassword>
            <Role>User</Role>
        </UserInfo>
        <GenSessInfo>false</GenSessInfo>
    </SignonRq>
    <FIDeleteRq>
        <FIAcctId>
                {$FIAcctId}
        </FIAcctId>
    </FIDeleteRq>
</FIDeleteRqHeader>
END;
            } else {
                $wsAddItemRequestXml = <<<END
 <FIDeleteRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>RqUID0</RqUID>
    <SignonRq>
        <UserInfo>
           <SessionInfo>
                 <SessionToken>{$ceSession}</SessionToken>
           </SessionInfo>
        </UserInfo>
    </SignonRq>
    <FIDeleteRq>
        <FIAcctId>
                {$FIAcctId}
        </FIAcctId>
    </FIDeleteRq>
</FIDeleteRqHeader>
END;
            }
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);

            if ($xml) {
                $deleteNode = $xml->getElementsByTagName("FIDeleteRs")->item(0)->childNodes;

                if ($deleteNode->length > 0) {
                    foreach ($deleteNode->item(0)->childNodes as $nodeV) {

                        if ($nodeV->nodeName == "StatusDesc" && $nodeV->nodeValue == "Success") {
                            return true;
                        }
                    }
                } else {
                    return false;
                }
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

    /**
     * Add MFA values to the account
     *
     */
    public function addMFAToAccounts($FILoginAcctId, $runId, $harvestAddId, $mfares, $userName, $userPassword, $ceSession = "") {
        try {
            $cashedgeServiceToCall = "AccountMgmt/initiateAddAccounts";
            $FILoginParam = '';
            //iterate over accounts
            foreach ($mfares as $res) {
                $FILoginParam .= <<<END
                <FILoginParam>
                <ParamName>{$res["ParamName"]}</ParamName>
                        <CryptParamVal>
                            <CryptType>{$res["CryptType"]}</CryptType>
                            <CryptVal>{$res["CryptVal"]}</CryptVal>
                        </CryptParamVal>
            </FILoginParam>
END;
            }

            if ($ceSession == "") {
                $wsAddItemRequestXml = <<<END
<HarvestAddRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>LS-initiateAddAccounts</RqUID>
    <SignonRq>
        <UserInfo>
            <UserID>{$userName}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$userPassword}</CryptVal>
            </UserPassword>
            <Role>User</Role>
        </UserInfo>
    </SignonRq>
    <HarvestAddRq>
            <AddMoreAccts>
                <FILoginAcctId>{$FILoginAcctId}</FILoginAcctId>
                <FILoginParamList>
                            {$FILoginParam}
                </FILoginParamList>
                <RunId>{$runId}</RunId>
                <HarvestAddID>{$harvestAddId}</HarvestAddID>
            </AddMoreAccts>
    </HarvestAddRq>
</HarvestAddRqHeader>
END;
            } else {
                $wsAddItemRequestXml = <<<END
   <HarvestAddRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>1</RqUID>
    <SignonRq>
           <SessionInfo>
                 <SessionToken>{$harvestAddId}</SessionToken>
           </SessionInfo>
    </SignonRq>
    <HarvestAddRq>
            <AddMoreAccts>
                <FILoginAcctId>{$FILoginAcctId}</FILoginAcctId>
                <FILoginParamList>
                            {$FILoginParam}
                </FILoginParamList>
                <RunId>{$runId}</RunId>
                <HarvestAddID>{$harvestAddId}</HarvestAddID>
            </AddMoreAccts>
    </HarvestAddRq>
</HarvestAddRqHeader>
END;
            }
            Yii::trace("Add MFA to Accounts Answer:", "cashedgedata");
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);
            Yii::trace(":Data:" . CVarDumper::dumpAsString($xml), "cashedgedata");
            if ($xml) {
                $accountsNode = $xml->getElementsByTagName("HarvestAddRs");
                if ($accountsNode->length > 0) {

                    foreach ($accountsNode->item(0)->childNodes as $nodeV) {

                        $statusCode = $nodeV->getElementsByTagName("StatusCode")->item(0)->nodeValue;

                        //get the messsage
                        if ($statusCode == 0) {
                            //call the activation method
                            #$harvestAddID = ($nodeV->getElementsByTagName("HarvestAddID")->length > 0 ) ? $nodeV->getElementsByTagName("HarvestAddID")->item(0)->nodeValue : "";
                            $status =
                                    $details = array(
                                'harvestId' => $runId,
                                #'harvestAddId' => $harvestAddID,
                                'statusCode' => $statusCode
                            );
                            return $details;
                        } else {
                            $test['statuscode'] = $statusCode;
                            $test['xml'] = $xml;
                            return $test;
                            #return Messages::getErrorMapping("CASHEDGE", $statusCode, '');
                        }
                    }
                }else{
                    $accountsNode1 = $xml->getElementsByTagName("Status");
                    $statusCode = $accountsNode1->item(0)->getElementsByTagName("StatusCode")->item(0)->nodeValue;
                    $result["code"] = $statusCode;
                    return $result;
                }
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

    /**
     * GetAccounts and Signon:
     *
     * https://websrviqa.wm.cashedge.com/WealthManagementWeb/ws/AccountDataInq/getAccountDetails
     */
    public function getAccountDetails($userName, $userPassword) {
        try {
            $cashedgeServiceToCall = "AccountDataInq/getAccountDetails";

            $wsAddItemRequestXml = <<<END
   <ce:FILoginAcctInfoListInqRqHeader xmlns:ce="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <ce:RqUID>LS-getAccountDetails</ce:RqUID>
     <ce:SignonRq>
             <ce:UserInfo>
                 <ce:UserID>{$userName}</ce:UserID>
                 <ce:HomeID>{$this->homeId}</ce:HomeID>
                 <ce:UserPassword>
                     <ce:CryptType>None</ce:CryptType>
                     <ce:CryptVal>{$userPassword}</ce:CryptVal>
                 </ce:UserPassword>
                 <ce:Role>User</ce:Role>
             </ce:UserInfo>
    <ce:GenSessInfo>true</ce:GenSessInfo>
    </ce:SignonRq>
    <ce:FILoginAcctInfoListInqRq>
    <ce:IncludeDetail>FIAcctSummary</ce:IncludeDetail>
    <ce:IncludeDetail>InvAcctPos</ce:IncludeDetail>
</ce:FILoginAcctInfoListInqRq>
</ce:FILoginAcctInfoListInqRqHeader>
END;
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);

            if ($xml) {
                $signOnRsNode = $xml->getElementsByTagName("SignonRs");
                $opArr = array();
                if ($signOnRsNode->length > 0) {
                    foreach ($signOnRsNode->item(0)->childNodes as $nodeV) {

                        if ($nodeV->nodeName == "SessInfo") {
                            //upate the database and set sess info
                            $opArr["session"] = $nodeV->nodeValue;
                        }
                    }
                }
                $FILoginAcctIdRs = $xml->getElementsByTagName("FILoginAcctInfo");
                if ($FILoginAcctIdRs->length > 0) {
                    foreach ($FILoginAcctIdRs->item(0)->childNodes as $nodeV) {

                        if ($nodeV->nodeName == "FILoginAcctId") {
                            //upate the database and set sess info
                            $opArr["id"] = $nodeV->nodeValue;
                        }
                    }
                }


                $allAccounts = array();
                $FILoginAcctInfoNode = $xml->getElementsByTagName("FIAcctSummary");
                if ($FILoginAcctInfoNode->length > 0) {
                    foreach ($FILoginAcctInfoNode as $account) {
                        $eachAccount = array();

                        $FIAcctId = $account->getElementsByTagName("FIAcctId")->item(0);
                        $eachAccount["AcctId"] = $FIAcctId->getElementsByTagName("AcctId")->item(0)->nodeValue;
                        $eachAccount["AcctType"] = $FIAcctId->getElementsByTagName("AcctType")->item(0)->nodeValue;

                        $eachAccount["ExtAcctType"] = $FIAcctId->getElementsByTagName("ExtAcctType")->item(0)->nodeValue;

                        if ($account->getElementsByTagName("FIAcctName")->length > 0) {
                            $fiiAcc = $account->getElementsByTagName("FIAcctName")->item(0);
                            $eachAccount["FIAcctNameParamName"] = $fiiAcc->getElementsByTagName("ParamName")->item(0)->nodeValue;
                            $eachAccount["FIAcctNameParamVal"] = $fiiAcc->getElementsByTagName("ParamVal")->item(0)->nodeValue;
                        }

                        if ($eachAccount["AcctType"] == "INS") {
                            $eachAccount["cashvalue"] = ($account->getElementsByTagName("CashSurrenderValue")->length > 0 ) ? $account->getElementsByTagName("CashSurrenderValue")->item(0)->nodeValue : "";
                            $eachAccount["premium"] = ($account->getElementsByTagName("InsurancePremium")->length > 0) ? $account->getElementsByTagName("InsurancePremium")->item(0)->nodeValue : "";
                            $eachAccount["deathbenefit"] = ($account->getElementsByTagName("DeathBenefit")->length > 0) ? $account->getElementsByTagName("DeathBenefit")->item(0)->nodeValue : "";
                        }

                        $fiiAccBal = $account->getElementsByTagName("AcctBal");
                        if ($fiiAccBal->length > 0) {
                            foreach ($fiiAccBal as $accBal) {

                                $accountBal = array();

                                $accountBal["AccBalType"] = $accBal->getElementsByTagName("BalType")->item(0)->nodeValue;
                                $curAmtNode = $accBal->getElementsByTagName("CurAmt")->item(0);
                                $accountBal["CurAmt"] = $curAmtNode->getElementsByTagName("Amt")->item(0)->nodeValue;
                                $accountBal["CurCode"] = $curAmtNode->getElementsByTagName("CurCode")->item(0)->nodeValue;

                                $eachAccount["AccBal"][] = $accountBal;
                            }
                        } else {
                            $accountBal = array();
                            $accountBal["CurAmt"] = 0;
                            $eachAccount["AccBal"][] = $accountBal;
                        }
                        $allAccounts[] = $eachAccount;
                    }
                }
                $allTickers = array();
                $invAcctPos = $xml->getElementsByTagName("InvAcctPos");
                if ($invAcctPos->length > 0) {
                    foreach ($invAcctPos as $invAcc) {
                        $invPos = $invAcc->getElementsByTagName("InvPos");
                        $acctId = $invAcc->getElementsByTagName("AcctId")->item(0)->nodeValue;
                        $invPosArr = array();

                        foreach ($invPos as $invEach) {

                            $tick = $invEach->getElementsByTagName("Ticker")->item(0)->nodeValue;
                            $secDesc = $invEach->getElementsByTagName("SecDesc")->item(0)->nodeValue;
                            $unitPrice = $invEach->getElementsByTagName("UnitPrice")->item(0)->nodeValue;
                            $units = $invEach->getElementsByTagName("Units")->item(0)->nodeValue;
                            $marketValue = $invEach->getElementsByTagName("MarketValue")->item(0)->nodeValue;
                            $amount = $marketValue;//Utility::tickerAmountToDB($unitPrice) * $units;
                            $eachTicker = array(
                                'ticker' => $tick,
                                'secdesc' => $secDesc,
                                'unitprice' => $unitPrice,
                                'unit' => $units,
                                'marketvalue' => $marketValue,
                                'amount' => $amount
                            );
                            $invPosArr[] = $eachTicker;
                        }
                        $accTicker = array(
                            'acctid' => $acctId,
                            'invpos' => $invPosArr
                        );
                        $allTickers[] = $accTicker;
                    }
                }
                $opArr["accounts"] = $allAccounts;
                $opArr["tickers"] = $allTickers;

                return $opArr;
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

    /**
     * deleteAccount by FILoginAcctId
     *
     */
    public function deleteAccountByFiLoginAcctId($FILoginAcctId, $userName, $userPassword, $ceSession = "") {
        try {
            $cashedgeServiceToCall = "AccountMgmt/deleteAccounts";

            if ($ceSession == "") {
                $wsAddItemRequestXml = <<<END
 <FIDeleteRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>RqUID0</RqUID>
    <SignonRq>
        <UserInfo>
            <UserID>{$userName}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$userPassword}</CryptVal>
            </UserPassword>
            <Role>User</Role>
        </UserInfo>
    </SignonRq>
    <FIDeleteRq>
        <FILoginAcctId>
                {$FILoginAcctId}
        </FILoginAcctId>
    </FIDeleteRq>
</FIDeleteRqHeader>
END;
            } else {
                $wsAddItemRequestXml = <<<END
 <FIDeleteRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>RqUID0</RqUID>
    <SignonRq>
        <UserInfo>
           <SessionInfo>
                 <SessionToken>{$ceSession}</SessionToken>
           </SessionInfo>
        </UserInfo>
    </SignonRq>
    <FIDeleteRq>
        <FILoginAcctId>
                {$FILoginAcctId}
        </FILoginAcctId>
    </FIDeleteRq>
</FIDeleteRqHeader>
END;
            }
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);

            if ($xml) {
                $deleteNode = $xml->getElementsByTagName("FIDeleteRs")->item(0)->childNodes;

                if ($deleteNode->length > 0) {
                    foreach ($deleteNode->item(0)->childNodes as $nodeV) {

                        if ($nodeV->nodeName == "StatusDesc" && $nodeV->nodeValue == "Success") {
                            return true;
                        }
                    }
                } else {
                    return false;
                }
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

    /**
     * deleteAccount by deleteAccountByAcctId
     *
     */
    public function deleteAccountByAccountId($AcctId, $AcctType, $ExtAcctType, $userName, $userPassword) {
        try {
            $cashedgeServiceToCall = "AccountMgmt/deleteAccounts";

            $wsAddItemRequestXml = <<<END
 <FIDeleteRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>RqUID0</RqUID>
    <SignonRq>
        <UserInfo>
            <UserID>{$userName}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$userPassword}</CryptVal>
            </UserPassword>
            <Role>User</Role>
        </UserInfo>
    </SignonRq>
    <FIDeleteRq>
        <FIAcctId>
            <AcctId>{$AcctId}</AcctId>
            <AcctType>{$AcctType}</AcctType>
            <ExtAcctType>{$ExtAcctType}</ExtAcctType>
        </FIAcctId>
    </FIDeleteRq>
</FIDeleteRqHeader>
END;
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);

            if ($xml) {
                $deleteNode = $xml->getElementsByTagName("FIDeleteRs")->item(0)->childNodes;

                if ($deleteNode->length > 0) {
                    foreach ($deleteNode->item(0)->childNodes as $nodeV) {

                        if ($nodeV->nodeName == "StatusDesc" && $nodeV->nodeValue == "Success") {
                            return true;
                        }
                    }
                } else {
                    return Messages::getERRORMapping("FLEXSCORE", "1002", '');
                }
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

    /**
     * Get investment Position
     *
     */
    public function getInvestmentPos($AcctId, $AcctType, $ExtAcctType, $userName, $userPassword) {
        try {
            $cashedgeServiceToCall = "AccountDataInq/getInvestmentPos";

            $wsAddItemRequestXml = <<<END
   <InvAcctPosInqRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>RqUID0</RqUID>
    <SignonRq>
       <UserInfo>
            <UserID>{$userName}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$userPassword}</CryptVal>
            </UserPassword>
            <Role>User</Role>
        </UserInfo>
    </SignonRq>
    <InvAcctPosInqRq>
        <FIAcctId>
            <AcctId>{$AcctId}</AcctId>
            <AcctType>{$AcctType}</AcctType>
            <ExtAcctType>{$ExtAcctType}</ExtAcctType>
        </FIAcctId>
    </InvAcctPosInqRq>
</InvAcctPosInqRqHeader>
END;
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);
            Yii::trace("TICKER DUMP", "cashedgedata");
            Yii::trace(CVarDumper::dumpAsString($xml) . "TICKER DUMP VALUE", "cashedgedata");

            if ($xml) {
                //get the ticker
                $invAcctPos = $xml->getElementsByTagName("InvAcctPos");
                if ($invAcctPos->length > 0) {
                    $invAcctPos = $invAcctPos->item(0);
                    $ticker = ($invAcctPos->getElementsByTagName("Ticker")->length > 0 ) ? $invAcctPos->getElementsByTagName("Ticker")->item(0)->nodeValue : "";
                    return $ticker;
                } else {
                    return Messages::getERRORMapping("FLEXSCORE", "1002", '');
                }
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return false;
        }
    }

    /**
     * sign in request
     *
     */
    public function getSignonRq($userName, $userPassword) {
        try {
            $cashedgeServiceToCall = "ClientMgmt/signon";

            $wsAddItemRequestXml = <<<END
<SignonRqHeader xmlns="http://www.cashedge.com/wm"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  partnerID="{$this->partnerId}" version="1.4.6">
<RqUID>LS-updateAccounts</RqUID>
  <SignonRq>
    <UserInfo>
     <UserID>{$userName}</UserID>
     <HomeID>{$this->homeId}</HomeID>
     <UserPassword>
      <CryptType>None</CryptType>
      <CryptVal>{$userPassword}</CryptVal>
     </UserPassword>
      <Role>User</Role>
     </UserInfo>
     <GenSessInfo>true</GenSessInfo>
     </SignonRq>
</SignonRqHeader>
END;
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);

            if ($xml) {
                $signOnRsNode = $xml->getElementsByTagName("SignonRs");
                $opArr = array();
                if ($signOnRsNode->length > 0) {
                    foreach ($signOnRsNode->item(0)->childNodes as $nodeV) {

                        if ($nodeV->nodeName == "SessInfo") {
                            //upate the database and set sess info
                            $opArr["session"] = $nodeV->nodeValue;
                        }
                    }
                }
                return $opArr;
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $e) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

    public function deleteUser($userID) {
        try {
            $cashedgeServiceToCall = "UserMgmt/deleteUser";

            $wsAddItemRequestXml = <<<END
<?xml version="1.0" encoding="UTF-8"?>
<UserDelRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>RqUID0</RqUID>
    <SignonRq>
            <UserInfo>
            <UserID>{$this->adminUserId}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$this->adminUserPassword}</CryptVal>
            </UserPassword>
            <Role>Admin</Role>
            </UserInfo>
            </SignonRq>
    <UserDelRq>
        <HomeID>{$this->homeId}</HomeID>
        <UserID>$userID</UserID>
    </UserDelRq>
</UserDelRqHeader>
END;
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);

            if ($xml) {

                $xmlRes = $xml->getElementsByTagName("StatusDesc");
                if ($xmlRes->length > 0) {

                    if ($xmlRes->item(0)->nodeValue == "Success") {

                        return array("status" => 'OK', "msg" => 'user is deleted');
                    }
                    return array("status" => 'ERROR', "msg" => 'user is NOT deleted');
                }
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $e) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

    /**
     * Check the account added to the user
     * Response will include status such as 'completed', 'harvest error' or 'in progress'
     * Handle errors such as login failure (301), multi-factor authentication (303),
     * wrong MFA answer (304), etc
     *  Additional information is needed in case of MFA
     *
     */
    public function checkAccountForUpdate($runId, $harvestAddId, $userName, $userPassword, $ceSession = "") {
        try {
            $cashedgeServiceToCall = "AccountMgmt/getAddAccountStatus";

            if ($ceSession == "") {
                $wsAddItemRequestXml = <<<END
 <HarvestAddStsInqRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>LS-getAddAccountStatus</RqUID>
    <SignonRq>
        <UserInfo>
            <UserID>{$userName}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$userPassword}</CryptVal>
            </UserPassword>
            <Role>User</Role>
        </UserInfo>
    </SignonRq>
    <HarvestAddStsInqRq>
        <RunId>{$runId}</RunId>
        <HarvestAddID>{$harvestAddId}</HarvestAddID>
    </HarvestAddStsInqRq>
</HarvestAddStsInqRqHeader>
END;
            } else {
                $wsAddItemRequestXml = <<<END
 <HarvestAddStsInqRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>LS-getAddAccountStatus</RqUID>
    <SignonRq>
            <SessionInfo>
                 <SessionToken>{$ceSession}</SessionToken>
           </SessionInfo>
    </SignonRq>
    <HarvestAddStsInqRq>
        <RunId>{$runId}</RunId>
        <HarvestAddID>{$harvestAddId}</HarvestAddID>
    </HarvestAddStsInqRq>
</HarvestAddStsInqRqHeader>
END;
            }
            $xml = self::callCashEdgeApi($cashedgeServiceToCall, $wsAddItemRequestXml);

            if ($xml) {
                $harvestReqStat = $xml->getElementsByTagName("HarvestRqStatus");

                // Added in case the above variable is not set
                $harvestResponse = $xml->getElementsByTagName("StatusDesc");
                if (isset($harvestResponse)) {
                    $sessInfoValue = $harvestResponse->item(0)->nodeValue;
                    if ($sessInfoValue == 'Invalid SessInfo') {
                        self::checkAccountForUpdate($runId, $harvestAddId, $userName, $userPassword, $ceSession);
                    }
                }

                if ($harvestReqStat->length > 0) {
                    $status = $harvestReqStat->item(0)->nodeValue;

                    //check the status
                    if ($status == "InProgress") {
                        sleep(10);
                        self::checkAccountForUpdate($runId, $harvestAddId, $userName, $userPassword, $ceSession);
                    } else if ($status == "Completed") {

                    }
                } else {
                    return Messages::getERRORMapping("FLEXSCORE", "1002", '');
                }
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

    /**
     * Generate the password
     * @return $password
     */
    function generatePassword() {

        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, strlen($alphabet) - 1); //use strlen instead of count
            $pass[$i] = $alphabet[$n];
        }
        return implode($pass);
    }

    /**
     * Add user to cashedge
     *
     */
    public function searchFIs($fiName) {
        try {
            $serviceToCall = "SeedDataInq/searchFinancialInst";
            $wsCreateFiRequestXml = <<<END
 <FISearchRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>RqUID0</RqUID>
     <SignonRq>
        <UserInfo>
            <UserID>{$this->adminUserId}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$this->adminUserPassword}</CryptVal>
            </UserPassword>
            <Role>Admin</Role>
        </UserInfo>
            </SignonRq>
    <FISearchRq>
        <FIName>{$fiName}</FIName>
        <FIInfoRequired>FIIDInfoList</FIInfoRequired>
    </FISearchRq>
</FISearchRqHeader>
END;
            $xml = self::callCashEdgeApi($serviceToCall, $wsCreateFiRequestXml);
            if ($xml) {

                $allFis = array();

                $fis = $xml->getElementsByTagName("FIInfoData");
                foreach ($fis as $fi) {
                    $eachFiArr = array();

                    $eachFiArr["fiName"] = $fi->getElementsByTagName("FIName")->item(0)->nodeValue;
                    $eachFiArr["fiCountry"] = $fi->getElementsByTagName("Country")->item(0)->nodeValue;
                    $eachFiArr["fiid"] = $fi->getElementsByTagName("FIId")->item(0)->nodeValue;

                    $URL = $fi->getElementsByTagName("URL")->item(0)->childNodes;

                    $jsonURL = array();
                    foreach ($URL as $fis) {

                        $jsonURL[$fis->nodeName] = $fis->nodeValue;
                    }
                    $eachFiArr["fiURL"] = $jsonURL;
                    $accounts = $fi->getElementsByTagName("FIAcctData");

                    $jsonParent = array();
                    for ($i = 0; $i < $accounts->length; $i++) {
                        $json = array();
                        $accountChild = $accounts->item($i)->childNodes;

                        foreach ($accountChild as $fis) {

                            $json[$fis->nodeName] = $fis->nodeValue;
                        }
                        $jsonParent[] = $json;
                    }
                    $eachFiArr["fiACC"] = $jsonParent;

                    $params = $fi->getElementsByTagName("FILoginParametersInfo");

                    $jsonAccParams = array();
                    for ($i = 0; $i < $params->length; $i++) {
                        $json = array();
                        $accountChild = $params->item($i)->childNodes;

                        foreach ($accountChild as $fis) {

                            $json[$fis->nodeName] = $fis->nodeValue;
                        }
                        $jsonAccParams[] = $json;
                    }
                    //print_r(json_encode($jsonParent));
                    $eachFiArr["fiLoginParams"] = $jsonAccParams;
                    $allFis[] = $eachFiArr;
                }
                //return array
                return $allFis;
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

    /**
     * Get FI by id
     *
     */
    public function getFIById($fiId) {
        try {
            $serviceToCall = "SeedDataInq/getFinancialInstInfo";

            //request to cash edge
            $wsCreateFiRequestXml = <<<END
   <FIInfoRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>RqUID0</RqUID>
     <SignonRq>
             <UserInfo>
            <UserID>{$this->adminUserId}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$this->adminUserPassword}</CryptVal>
            </UserPassword>
            <Role>Admin</Role>
        </UserInfo>
        </SignonRq>
    <FIInfoRq>
            <FIId>{$fiId}</FIId>
        <FIInfoRequired>FIInfoRequired</FIInfoRequired>
    </FIInfoRq>
</FIInfoRqHeader>
END;
            $xml = self::callCashEdgeApi($serviceToCall, $wsCreateFiRequestXml);
            if ($xml) {
                $allFis = array();

                $fis = $xml->getElementsByTagName("FIInfoData");
                foreach ($fis as $fi) {
                    $eachFiArr = array();

                    $eachFiArr["fiName"] = $fi->getElementsByTagName("FIName")->item(0)->nodeValue;
                    $eachFiArr["fiCountry"] = $fi->getElementsByTagName("Country")->item(0)->nodeValue;
                    $eachFiArr["fiid"] = $fi->getElementsByTagName("FIId")->item(0)->nodeValue;

                    $URL = $fi->getElementsByTagName("URL")->item(0)->childNodes;

                    $jsonURL = array();
                    foreach ($URL as $fis) {

                        $jsonURL[$fis->nodeName] = $fis->nodeValue;
                    }
                    $eachFiArr["fiURL"] = $jsonURL;
                    $accounts = $fi->getElementsByTagName("FIAcctData");

                    $jsonParent = array();
                    for ($i = 0; $i < $accounts->length; $i++) {
                        $json = array();
                        $accountChild = $accounts->item($i)->childNodes;

                        foreach ($accountChild as $fis) {

                            $json[$fis->nodeName] = $fis->nodeValue;
                        }
                        $jsonParent[] = $json;
                    }
                    $eachFiArr["fiACC"] = $jsonParent;

                    $params = $fi->getElementsByTagName("FILoginParametersInfo");

                    $jsonAccParams = array();
                    for ($i = 0; $i < $params->length; $i++) {
                        $json = array();
                        $accountChild = $params->item($i)->childNodes;

                        foreach ($accountChild as $fis) {

                            $json[$fis->nodeName] = $fis->nodeValue;
                        }
                        $jsonAccParams[] = $json;
                    }
                    //print_r(json_encode($jsonParent));
                    $eachFiArr["fiLoginParams"] = $jsonAccParams;
                    $allFis[] = $eachFiArr;
                }
                //return array
                return $allFis;
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

    /**
     * Get FI by date used in cron job
     *
     */
    public function getFIByDate($startDate, $endDate) {
        //date format 2009-10-10
        try {
            $serviceToCall = "SeedDataInq/getFinancialInstInfo";

            //request to cash edge
            $wsCreateFiRequestXml = <<<END
   <FIInfoRqHeader xmlns="http://www.cashedge.com/wm"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 partnerID="{$this->partnerId}" version="version1">
    <RqUID>RqUID0</RqUID>
     <SignonRq>
             <UserInfo>
            <UserID>{$this->adminUserId}</UserID>
            <HomeID>{$this->homeId}</HomeID>
            <UserPassword>
                <CryptType>None</CryptType>
                <CryptVal>{$this->adminUserPassword}</CryptVal>
            </UserPassword>
            <Role>Admin</Role>
        </UserInfo>
        </SignonRq>
        <FIInfoRq>
            <SelRangeDt>
                <StartDt>{$startDate}</StartDt>
                <EndDt>{$endDate}</EndDt>
            </SelRangeDt>
            <FIInfoRequired>FIInfoRequired</FIInfoRequired>
        </FIInfoRq>
</FIInfoRqHeader>
END;

            $xml = self::callCashEdgeApi($serviceToCall, $wsCreateFiRequestXml);

            if ($xml) {
                $allFis = array();

                $fis = $xml->getElementsByTagName("FIInfoData");
                foreach ($fis as $fi) {
                    $eachFiArr = array();

                    $eachFiArr["fiName"] = $fi->getElementsByTagName("FIName")->item(0)->nodeValue;
                    $eachFiArr["fiCountry"] = $fi->getElementsByTagName("Country")->item(0)->nodeValue;
                    $eachFiArr["fiid"] = $fi->getElementsByTagName("FIId")->item(0)->nodeValue;

                    $URL = $fi->getElementsByTagName("URL")->item(0)->childNodes;

                    $jsonURL = array();
                    foreach ($URL as $fis) {

                        $jsonURL[$fis->nodeName] = $fis->nodeValue;
                    }
                    $eachFiArr["fiURL"] = $jsonURL;
                    $accounts = $fi->getElementsByTagName("FIAcctData");

                    $jsonParent = array();
                    for ($i = 0; $i < $accounts->length; $i++) {
                        $json = array();
                        $accountChild = $accounts->item($i)->childNodes;

                        foreach ($accountChild as $fis) {

                            $json[$fis->nodeName] = $fis->nodeValue;
                        }
                        $jsonParent[] = $json;
                    }
                    $eachFiArr["fiACC"] = $jsonParent;

                    $params = $fi->getElementsByTagName("FILoginParametersInfo");

                    $jsonAccParams = array();
                    for ($i = 0; $i < $params->length; $i++) {
                        $json = array();
                        $accountChild = $params->item($i)->childNodes;

                        foreach ($accountChild as $fis) {

                            $json[$fis->nodeName] = $fis->nodeValue;
                        }
                        $jsonAccParams[] = $json;
                    }
                    //print_r(json_encode($jsonParent));
                    $eachFiArr["fiLoginParams"] = $jsonAccParams;
                    $allFis[] = $eachFiArr;
                }
                //return array
                return $allFis;
            } else {
                return Messages::getERRORMapping("FLEXSCORE", "1002", '');
            }
        } catch (Exception $E) {
            return Messages::getERRORMapping("FLEXSCORE", "1002", '');
        }
    }

    public $result;

    function function1($var) {

        if ($var == 1) {
            echo 'inside f1 -' . $var;
            return $result['value1'] = '1';
        } else {
            return self::function2($var);
        }
    }

    function function2($var) {
        echo $var;
        if ($var == 2) {
            echo 'inside f2 -' . $var;
            return $result['value2'] = '2';
        } else {
            return self::function3($var);
        }
    }

    function function3($var) {
        echo $var;
        if ($var == 3) {
            echo 'inside f3 -' . $var;
            //echo gettype($result);

            return gettype($result['value3'] = '3');
        } else {
            return $result['value4'] = 'END';
        }
    }

}

?>