<?php

/**
 *
 *
 * @author Subramanya
 * Modified by Dan Tormey
 */
class Messages {

    //Generic message fils
    //handles cashedge, ls messages
    //before sending to UI
    public static function getERRORMapping($status, $code,$id) {
        //
        $message = "";
        $cashedgeMapping = array(
            '0' => array('status' => 'INFO', 'msg' => 'Success! The service provider successfully processed the request.', "code" => "0"),
            '50' => array('status' => 'WARNING', 'msg' => 'Partial success.', "code" => "50"),
            '100' => array('status' => 'ERROR', 'msg' => 'There was an error that prevented the service provider from processing the transaction. No additional information is provided.', "code" => "100"),
            '103' => array('status' => 'ERROR', 'msg' => 'Network failure, try updating again.', "code" => "103"),
            '104' => array('status' => 'ERROR', 'msg' => 'We cannot establish a connection to your financial institution website at this time.', "code" => "104"),
            '105' => array('status' => 'ERROR', 'msg' => 'We cannot establish a connection to your financial institution website at this time.', "code" => "105"),
            '106' => array('status' => 'ERROR', 'msg' => 'We are not able to update your account because of high network traffic.', "code" => "106"),
            '107' => array('status' => 'ERROR', 'msg' => 'We are not able to update your account because of high network traffic.', "code" => "107"),
            '108' => array('status' => 'ERROR', 'msg' => 'Your Financial Institution website is not available currently. Please try again at a later time.', "code" => "108"),
            '109' => array('status' => 'ERROR', 'msg' => 'We cannot establish a connection to your financial institution website at this time. Please try again in a few minutes.', "code" => "109"),
            '110' => array('status' => 'ERROR', 'msg' => 'We did not find any relevant accounts on the Financial Institution website for this account. Please check your account status in financial institution website.', "code" => "110"),
            '121' => array('status' => 'ERROR', 'msg' => 'There was a delay in updating this account due to a system issue. Please try again in a few minutes.', "code" => "121"),
            '200' => array('status' => 'ERROR', 'msg' => 'We are not able to update this account at this time as we are currently upgrading our data collection process for this financial institution. Please try again later', "code" => "200"),
            '201' => array('status' => 'ERROR', 'msg' => 'We can no longer find this account online at the financial institution website.', "code" => "201"),
            '202' => array('status' => 'ERROR', 'msg' => 'We are not able to update this account at this time as we are currently upgrading our data collection process for this financial institution', "code" => "202"),
            '203' => array('status' => 'ERROR', 'msg' => 'It appears that this account can no longer be located within the institution\'s web site. Please confirm this by logging into the Institution\'s web site and delete this account from FlexScore if it doesn\'t exist.', "code" => "203"),
            '204' => array('status' => 'ERROR', 'msg' => 'This account no longer exists in the institution’s web site. Please delete this account.', "code" => "204"),
            '205' => array('status' => 'ERROR', 'msg' => 'We are not able to update this account at this time as we are currently upgrading our data collection process for this financial institution.', "code" => "205"),
            '208' => array('status' => 'ERROR', 'msg' => 'We are unable to determine the status of your accounts at this financial institution.', "code" => "208"),
            '209' => array('status' => 'ERROR', 'msg' => 'Invalid classification for this account. Please reclassify the account accurately.', "code" => "209"),
            '300' => array('status' => 'ERROR', 'msg' => 'Please verify that the financial institution username/password that you entered is correct. If the login credentials are correct, please try again later.', "code" => "300"),
            '301' => array('status' => 'ERROR', 'msg' => 'We cannot login with username/password combination you provided. Please make sure the login information is correct.', "code" => "301"),
            '302' => array('status' => 'ERROR', 'msg' => 'Your financial institution requires you to sign an agreement in order for us to aggregate your accounts. Please log into your online account and follow the instructions presented by the financial institution.', "code" => "302"),
            '303' => array('status' => 'ERROR', 'msg' => 'Your financial institution website requires additional information to proceed.', "code" => "303"),
            '304' => array('status' => 'ERROR', 'msg' => 'Please make sure to provide the correct answer to the challenge question.', "code" => "304"),
            '305' => array('status' => 'ERROR', 'msg' => 'Please make sure that you provide the correct client or account identifier.', "code" => "305"),
            '306' => array('status' => 'ERROR', 'msg' => 'Please select the correct financial institution to add your accounts.', "code" => "306"),
            '307' => array('status' => 'ERROR', 'msg' => 'Your account has been locked at this financial institution. Please get in touch with your financial institution for more information.', "code" => "307"),
            '311' => array('status' => 'ERROR', 'msg' => 'We are in the process of upgrading our product to rectify this problem. Please try again later.', "code" => "311"),
            '400' => array('status' => 'ERROR', 'msg' => 'We failed to update your information in our database. Please try updating again in a few minutes', "code" => "400"),
            '600' => array('status' => 'ERROR', 'msg' => 'The request received from the partner is not supported at present.', "code" => "600"),
            '701' => array('status' => 'ERROR', 'msg' => 'We were not able to find any accounts at this institution in your last attempt to add the accounts. If you think that this is an error and at least one account should have been retrieved then please retry after two business days to allow us to resolve the error.', "code" => "701"),
            '999' => array('status' => 'ERROR', 'msg' => 'Internal software error. Please try updating again in a few minutes', "code" => "999"),
            '1020' => array('status' => 'ERROR', 'msg' => 'Required element is missing in the request received  from the partner.'),
            '1740' => array('status' => 'ERROR', 'msg' => 'The customer could not be authenticated due to an incorrect HomeID, login ID or password.'),
            '2740' => array('status' => 'ERROR', 'msg' => 'The currency code specified in the request is not valid.'),
            '4000' => array('status' => 'ERROR', 'msg' => 'The UserID specified in the request is invalid.'),
            '4010' => array('status' => 'ERROR', 'msg' => 'The partnerID specified in the request is invalid.'),
            '4012' => array('status' => 'ERROR', 'msg' => 'The HomeID specified in the request is invalid.'),
            '4020' => array('status' => 'ERROR', 'msg' => 'The Password specified in the request is invalid.'),
            '4030' => array('status' => 'ERROR', 'msg' => 'The user identified by partnerID:HomeID:UserID is already registered with the Server. '),
            '4040' => array('status' => 'ERROR', 'msg' => 'The user identified by partnerID:HomeID:UserID is not registered with the Server.'),
            '4050' => array('status' => 'ERROR', 'msg' => 'The encryption scheme specified in the request is not valid.'),
            '4060' => array('status' => 'INFO', 'msg' => 'No data is available to satisfy the request. This could mean that the user has not registered any financial institutions, there are no transactions in the specified date range, and so on.'),
            '4070' => array('status' => 'ERROR', 'msg' => 'One or more input fields were not valid.'),
            '4080' => array('status' => 'ERROR', 'msg' => 'The CashEdge Server was unable to create a SessINFO security token.'),
            '4090' => array('status' => 'ERROR', 'msg' => 'IThe SessINFO token specified in the request is invalid.'),
            '4100' => array('status' => 'ERROR', 'msg' => 'The FIAcctId specified in the request is invalid.'),
            '4110' => array('status' => 'ERROR', 'msg' => 'The account specified in the request is not of the status specified in the request.'),
            '4120' => array('status' => 'ERROR', 'msg' => 'The FILogin Account or FI Account for which a user is requesting details does not belong to the user.'),
            '4130' => array('status' => 'ERROR', 'msg' => 'A signon has been attempted by a user in a role other than the authorized role.'),
            '4140' => array('status' => 'ERROR', 'msg' => 'The FILoginAcctId specified in the request is invalid.'),
            '4150' => array('status' => 'ERROR', 'msg' => 'The Acctstatus or ExtAcctstatus specified in the request are invalid.'),
            '4210' => array('status' => 'ERROR', 'msg' => 'The request received from the partner Site is not a valid XML document.'),
            '4220' => array('status' => 'ERROR', 'msg' => 'The UserID specified in the request is not available for registration.'),
            '4240' => array('status' => 'ERROR', 'msg' => 'Attempt to push a user’s data failed.'),
            '4250' => array('status' => 'ERROR', 'msg' => 'Transaction status specified in the request is invalid. '),
            '4260' => array('status' => 'ERROR', 'msg' => 'Date specified in the request is invalid.'),
            '4270' => array('status' => 'ERROR', 'msg' => 'Amount specified in the request is invalid.'),
            '4280' => array('status' => 'ERROR', 'msg' => 'Search criterion specified in the request is invalid.'),
            '4290' => array('status' => 'ERROR', 'msg' => 'The BroadcastMsgID specified in the request is invalid.'),
            '4300' => array('code'   => '4300', 'status' => 'ERROR', 'msg' => 'This account already exists. No need to add it again.'),
            '4310' => array('status' => 'ERROR', 'msg' => 'An error was generated due to a harvesting error during the account add or add more accounts operation.'),
            '4320' => array('status' => 'ERROR', 'msg' => 'An error generated when an add more accounts operation was attempted for a low trust account without providing the login credentials.'),
            '4330' => array('status' => 'ERROR', 'msg' => 'If the harvest ID provided in the request is invalid.'),
            '4340' => array('status' => 'WARNING', 'msg' => 'A warning message was generated, when the harvest action was successful during a "account add" or "add more accounts" operation, but no accounts were fetched from the FI site. This is typically a scripting error or issue.'),
            '4350' => array('status' => 'ERROR', 'msg' => 'An update is already in progress. A new update (update or add) request cannot be submitted unless the existing update is completed.'),
            '4360' => array('status' => 'ERROR', 'msg' => 'The data provided for Request completion is not sufficient. Typically insufficient data provided for add new accounts, add more accounts or account maintenance operations and so on.'),
            '4370' => array('status' => 'ERROR', 'msg' => 'The account attributes combinations required for account classification is invalid. Instrument,AccountOwnership RetirementStatus'),
            '4380' => array('status' => 'ERROR', 'msg' => 'An error message generated when the data contained in the HarvestAddFetchAcctList does not match with the FILoginAcctId. Especially created for the HarvestCreateAcctsRq.'),
            '4390' => array('status' => 'ERROR', 'msg' => 'Incorrect FILoginAcctId was submitted in HarvestCreateAcctsRq'),
            '4400' => array('status' => 'ERROR', 'msg' => 'The system was unable to unregister any of the user(s) in AdvUnsubscribeRq.'),
            '4405' => array('status' => 'WARNING', 'msg' => 'Ths system was able to unregister some of the users in AdvUnsubscribeRq but not all.'),
            '4410' => array('status' => 'ERROR', 'msg' => 'The FIId provided is not supported or does not exist in the database or the number provided is null or non-numerical.'),
            '4420' => array('status' => 'INFO', 'msg' => 'Count of Accounts is high for this LoginAcctId. If the number of accounts eligible for data pull submitted in AdvFILoginAcctInqRq is greater than the configured value (cashedge.pi.MaxAccountCount).'),
            '4430' => array('status' => 'ERROR', 'msg' => 'The query criteria did not match with any accounts in the database eligible for update.'),
            '4440' => array('status' => 'ERROR', 'msg' => 'The maximum duration allowed for deleted  transactions API is 3 days. '),
            '4450' => array('status' => 'WARNING', 'msg' => 'A warning message generated when the harvest action was successful during an account add or add more accounts operation but no new accounts were fetched from FI site. '),
            '4460' => array('status' => 'ERROR', 'msg' => 'FI Login Credential exceed max length Login credentials entered by user exceed the maximum length allowed at financial institution web site.'),
            '4470' => array('status' => 'ERROR', 'msg' => 'The FI Login Account is suspended for harvesting. Please correct the harvesting error to resume aggregation activity. When harvesting account login credentials are rejected, the Account Harvest Status (AcctHarvestStatus) is disabled. The FI Login Account INFO List Inquiry Request (FILoginAcctINFOListInqRq) will return this error message in the response (FILoginAcctINFOListInqRs) to convey that the account is locked in the CashEdge system and the institution login credentials should be corrected.'),
            '4471' => array('status' => 'ERROR', 'msg' => 'This account is at a financial institution that is not supported within this service. The account data will not be able to be refreshed/updated because support is now unavailable for this financial institution.The partner should expect to receive status code '),
            '4480' => array('status' => 'ERROR', 'msg' => 'The AcctId in the request does not match with any accounts for this user or is null.'),
            '4510' => array('status' => 'ERROR', 'msg' => 'The account status id provided is invalid and does not exist in the database or is null'),
            '4530' => array('status' => 'ERROR', 'msg' => 'The balance status provided is invalid and does not exist in the database. '),
            '4540' => array('status' => 'ERROR', 'msg' => 'Account Group specified in the request is invalid The account group provided is invalid and does not exist in the database. Possible values are : CASH, INVESTMENT, OTHERBILL, Credit '),
            '4550' => array('status' => 'ERROR', 'msg' => 'For most transaction pull APIs date range can be maximum of 90 days. Deleted transactions API has a maximum allowable date range of 3 days.'),
            '4560' => array('status' => 'ERROR', 'msg' => 'FI URL provided in the request is empty. FI URL is a mandatory field and should have valid content.'),
            '4570' => array('status' => 'ERROR', 'msg' => 'FI Name provided in the request is empty. FI Name is a mandatory field and should have valid content'),
            '4580' => array('status' => 'ERROR', 'msg' => 'FI has already been requested and is under development The scripting team is already working on developing scripts for this request FI'),
            '4590' => array('status' => 'ERROR', 'msg' => 'The requested FI is already supported. Partner should search for this FI or refresh the FI seed data.'),
            '4591' => array('status' => 'ERROR', 'msg' => 'The FI Search criteria provided did not match with any FIs in the database'),
            '4600' => array('status' => 'ERROR', 'msg' => 'Request ID (RqUID) is a mandatory field. Each request should be sent with a unique request id so that Partners can reconcile the responses sent back.'),
            '5013' => array('status' => 'ERROR', 'msg' => 'This error was generated when creating or updating an offline account, an empty or null value was sent for account name element'),
            '7601' => array('status' => 'ERROR', 'msg' => 'There is no transaction with given criteria '),
            '7602' => array('status' => 'ERROR', 'msg' => 'The category provided does not exist in the Categorization Engine seed data'),
            '7603' => array('status' => 'ERROR', 'msg' => 'The subcategory provided does not exist in the Categorization Engine seed data.'),
            '7604' => array('status' => 'ERROR', 'msg' => 'This error can occur when the user is trying to add a category that already exists.'),
            '7605' => array('status' => 'ERROR', 'msg' => 'This error is related to category engine processing indicating that the transaction category update was not processed.'),
            '9001' => array('status' => 'ERROR', 'msg' => 'The ABA number provided is not supported.'),
            '9002' => array('status' => 'ERROR', 'msg' => 'FlexScore is currently working on supporting the given ABA routing number.')
        );
        $flexscoreMapping = array(
            '1000' => array('status' => 'SUCCESS', 'msg' => 'We successfully processed the request.'),
            '1001' => array('status' => 'OK', 'msg' => 'It will only take about a minute to download all of your account details the first time. Please continue to connect other accounts and complete your profile.'),
            //'1002' => array('status' => 'ERROR', 'msg' => 'We are unable to process your request at this time. Please try again at a later time.'),
            '1002' => false,
            '1003' => array('status' => 'ERROR', 'msg' => 'The account update is still in progress. Please check back in a few minutes.'),
        );
        if ($status == "CASHEDGE") {

           /* $clientServerErrorArray = array(100,103,104,105,106,107,108,109,110,121);
            if(in_array($code, $clientServerErrorArray)) {
                $message = array ('status' => '' , 'code' => $code , "id" => $id);
            }
            $accountErrorArray = array(200,201,202,203,204,205,208,209);
            if(in_array($code, $accountErrorArray)) {
                $message = array ('status' => '' , 'code' => $code , "id" => $id);
            }
            $loginErrorArray = array(300,301,302,305,306,307);
            if(in_array($code, $loginErrorArray)) {
                $message = array ('status' => '' , 'code' => $code , "id" => $id);
            }
            $loginHarvestErrorArray = array(311,400,600,701,999);
            if(in_array($code, $loginHarvestErrorArray)) {
                $message = array ('status' => '' , 'code' => $code , "id" => $id);
            } */
            $data = isset($cashedgeMapping[$code]['msg']) ? $cashedgeMapping[$code]['msg'] : 0 ;
            $message = array ('status' => $data , 'code' => $code , "id" => $id);
        } else if ($status == "FLEXSCORE"){
            $message = array('status' => 'Check the component' , 'code' => $code , "id" => 'Nil');
            #$flexscoreMapping[$code];
        }

        return $message;
    }

    public static function getBatchfileNotifications() {
        #Errors that have been seen to exist in csv batchfiles:  100,104,105,106,107,108,109,110,121,200,201,202,203,205,209,302,303,307,400,701
        $batchfileNotifications = array(
            '100' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'There was an error that prevented the service provider from processing the transaction. No additional information is provided.', "code" => "100"),
            '103' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'Network failure, try updating again.', "code" => "103"),
            '104' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'We cannot establish a connection to your financial institution website at this time.', "code" => "104"),
            '105' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'We cannot establish a connection to your financial institution website at this time.', "code" => "105"),
            '106' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'We are not able to update your account because of high network traffic.', "code" => "106"),
            '107' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'We are not able to update your account because of high network traffic.', "code" => "107"),
            '108' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'Your Financial Institution website is not available currently. Please try again at a later time.', "code" => "108"),
            '109' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'We cannot establish a connection to your financial institution website at this time. Please try again in a few minutes.', "code" => "109"),
            '110' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'We did not find any relevant accounts on the Financial Institution website for this account. Please check your account status in financial institution website.', "code" => "110"),
            '121' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'There was a delay in updating this account due to a system issue. Please try again in a few minutes.', "code" => "121"),
            '200' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'We are in the process of upgrading our product to rectify this problem. Please try again at a later time.', "code" => "200"),
            '201' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'We can no longer find this account online at the financial institution website.', "code" => "201"),
            '202' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'We are not able to update this account at this time as we are currently upgrading our data collection process for this financial institution.', "code" => "202"),
            '203' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'It appears that this account can no longer be located within the institution\'s web site. Please confirm this by logging into the Institution\'s web site and delete this account from FlexScore if it doesn\'t exist.', "code" => "203"),
            '204' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'This account no longer exists in the institution’s web site. Please delete this account.', "code" => "204"),
            '205' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'We are not able to update this account at this time as we are currently upgrading our data collection process for this financial institution.', "code" => "205"),
            '208' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'We are unable to determine the status of your accounts at this financial institution.', "code" => "208"),
            '209' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'Invalid classification for this account. Please reclassify the account accurately.', "code" => "209"),
            '300' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'Please verify that the financial institution username/password that you entered is correct. If the login credentials are correct, please try again later.', "code" => "300"),
            '301' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'We cannot login with username/password combination you provided. Please make sure the login information is correct.', "code" => "301"),
            '302' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'Your financial institution requires you to sign an agreement in order for us to aggregate your accounts. Please log into your online account and follow the instructions presented by the financial institution.', "code" => "302"),
            '303' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'Your financial institution website requires additional information to proceed.', "code" => "303"),
            '304' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'Please make sure to provide the correct answer to the challenge question.', "code" => "304"),
            '305' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'Please make sure that you provide the correct client or account identifier.', "code" => "305"),
            '306' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'Please select the correct financial institution to add your accounts.', "code" => "306"),
            '307' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'Your account has been locked at this financial institution. Please get in touch with your financial institution for more information.', "code" => "307"),
            '311' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'We are in the process of upgrading our product to rectify this problem. Please try again later.', "code" => "311"),
            '400' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'We failed to update your information in our database. Please try updating again in a few minutes.', "code" => "400"),
            '600' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'The request received from the partner is not supported at present.', "code" => "600"),
            '701' => array('type' => 'ERROR', 'context' => 'LOGIN',  'template' => 'info', 'msg' => 'We were not able to find any accounts at this institution in your last attempt to add the accounts. If you think that this is an error and at least one account should have been retrieved then please retry after two business days to allow us to resolve the error.', "code" => "701"),
        );
        return $batchfileNotifications;
    }
}

?>
