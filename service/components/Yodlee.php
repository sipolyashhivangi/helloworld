<?php
/**********************************************************************
* Filename: Yodlee.php
* Folder: components
* Description: Yodlee Component class handles yodlee connection 
* @author Subramanya HS (For TruGlobal Inc)
* @copyright (c) 2012 - 2013
* Change History:
* Version         Author               Change Description
**********************************************************************/
//include nu soap client
require_once(realpath(dirname(__FILE__) . '/../lib/nusoap/nusoap.php'));

class Yodlee extends CApplicationComponent {

    //yodlee connection variables
    public $cobrandId = null;
    public $applicationId = null;
    public $loginName = null;
    public $password = null;
    public $url = null;
    //yodlee token string returned by cobrand login service
    public $sessionToken = null;
    //NASTY some how it is not taking sessiontoken when we create the cobrand context
    public $cobrandContext = null;
    public $wsUserContext = null;

    /**
     * 
     */
    public function init() {
        if (!Yii::app()->getSession()->get('cobrandContextSession')) {
            $this->cobrandLogin();
        }
    }

    /**
     * Yodlee account cobrand login method 
     * stores cobrand login details in session 
     * @param wsContextName
     * 
     */
    public function cobrandLogin($wsContextName = "cobrandContext") {
        try {
            $serviceToCall = "CobrandLoginService";
            //login to yodlee cobrand
            // Init this component
            $wsCobrandLoginClientObject = new nusoap_client($this->url . "/" . $serviceToCall);
            //yodlee uses sslv3 protocol
            $wsCobrandLoginClientObject->setCurlOption(CURLOPT_SSLVERSION, 3);
            //request to soap service
            $wsCobrandLoginRequestXml = <<<END
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
        <loginCobrand xmlns="http://cobrandlogin.login.core.soap.yodlee.com">
                <cobrandId xmlns="">{$this->cobrandId}</cobrandId>
                <applicationId xmlns="">{$this->applicationId}</applicationId>
                <locale xmlns="">
                        <country>US</country>
                </locale>
                <cobrandCredentials xmlns:q1="http://login.ext.soap.yodlee.com" xsi:type="q1:CobrandPasswordCredentials" xmlns="">
                        <loginName>{$this->loginName}</loginName>
                        <password>{$this->password}</password>
                </cobrandCredentials>
        </loginCobrand>
</soap:Body>
</soap:Envelope>
END;
            //Login to co brand service from yodlee
            $wsCobrandLoginResponseArray = $wsCobrandLoginClientObject->send($wsCobrandLoginRequestXml, 'loginCobrand', '');

            //catch any exception and throw to calling module
            $err = $wsCobrandLoginClientObject->getError();
            if ($err) {
                return false;
            }

            if ($wsCobrandLoginClientObject->fault) {
                //throw the exception 
                return false;
            } else {
                //result is good 
                //store the ticket in the variable
                //convert to make context 
                $wsCobrandResponseRaw = $wsCobrandLoginClientObject->responseData;
                //to store the serialed object in session 
                $wsXmlResponseDom = new DOMDocument();
                $wsXmlResponseDom->loadXML($wsCobrandResponseRaw);
                $xmlCobrand = (string) $wsXmlResponseDom->saveXML($wsXmlResponseDom->getElementsByTagName("loginCobrandReturn")->item(0));

                //cobrand context store for 2 hours 
                $this->cobrandContext = str_replace("loginCobrandReturn", $wsContextName, $xmlCobrand);
                Yii::app()->getSession()->add('cobrandContextSession', serialize($this->cobrandContext));
                //store the token 
                $this->sessionToken = $wsCobrandLoginResponseArray["loginCobrandReturn"]["cobrandConversationCredentials"]["sessionToken"];
            }
        } catch (SoapFault $E) {
            return false;
        }
    }

    /**
     * 
     */
    public function addUserRegistrationService($userObject) {
        try {
            $serviceToCall = "UserRegistrationService";
            //login to yodlee cobrand
            // Init this component
            $wsCreateUserClientObject = new nusoap_client($this->url . "/" . $serviceToCall);
            //yodlee uses sslv3 protocol
            $wsCreateUserClientObject->setCurlOption(CURLOPT_SSLVERSION, 3);

            $cobrandContextSession = unserialize(Yii::app()->getSession()->get('cobrandContextSession'));
            //requeest to soap service
            $wsCreateUserRequestXml = <<<END
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
        <register3 xmlns="">
             {$cobrandContextSession}
             <userCredentials xmlns:ns6="http://login.ext.soap.yodlee.com" xsi:type="ns6:PasswordCredentials" xmlns="">
                    <loginName>{$userObject->yodleeUserName}</loginName>
                    <password>{$userObject->yodleePassword}</password>
             </userCredentials>
             <userProfile xmlns="">
                <values>
                    <table>
                        <key xsi:type="xsd:string">EMAIL_ADDRESS</key>
                        <value xsi:type="xsd:string">{$userObject->yodleeEmailAddress}</value>
                    </table>
                </values>
             </userProfile>
        </register3>
</soap:Body>
</soap:Envelope>
END;
            $wsCreateUserClientResponseArray = $wsCreateUserClientObject->send($wsCreateUserRequestXml, 'register3', '');
            print_r($wsCreateUserClientResponseArray);die;
            //catch any exception and throw to calling module
            $err = $wsCreateUserClientObject->getError();
            if ($err) {
                return false;
            }

            if ($wsCreateUserClientObject->fault) {
                //throw the exception 
                return false;
            } else {
                //user sucessfully created update the table to add the yodlee 
                //users details in the table  
                if (isset($wsCreateUserClientResponseArray["register3Return"]["userId"])) {
                    return $wsCreateUserClientResponseArray["register3Return"]["userId"];
                } else {
                    return 0;
                }
            }
        } catch (SoapFault $E) {
            return false;
        }
    }

    /**
     * 
     */
    public function loginUser($userObject) {
        try {
            $yodleeServiceToCall = "LoginService";
            //login to yodlee cobrand
            // Init this component
            $wsLoginUserClientObject = new nusoap_client($this->url . "/" . $yodleeServiceToCall);
            //yodlee uses sslv3 protocol
            $wsLoginUserClientObject->setCurlOption(CURLOPT_SSLVERSION, 3);

            $cobrandContextSession = unserialize(Yii::app()->getSession()->get('cobrandContextSession'));
            //requeest to soap service
            $wsLoginUserRequestXml = <<<END
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
        <login2 xmlns="">
             {$cobrandContextSession}
             <userCredentials xmlns:ns6="http://login.ext.soap.yodlee.com" xsi:type="ns6:PasswordCredentials" xmlns="">
                    <loginName>{$userObject->yodleeUserName}</loginName>
                    <password>{$userObject->yodleePassword}</password>
             </userCredentials>
        </login2>
</soap:Body>
</soap:Envelope>
END;
            $wsLoginUserResponseArray = $wsLoginUserClientObject->send($wsLoginUserRequestXml, 'login2', '');

            //catch any exception and throw to calling module
            $err = $wsLoginUserClientObject->getError();
            if ($err) {
                throw new CException('Problem in contacting server');
            }

            if ($wsLoginUserClientObject->fault) {
                //throw the exception 
                return false;
            } else {
                //result is good store the ticket in the variable
                $wsLoginUserResponseXML = $wsLoginUserClientObject->responseData;
                $xml = new DOMDocument();
                $xml->loadXML($wsLoginUserResponseXML);

                $this->wsUserContext = (string) $xml->saveXML($xml->getElementsByTagName("userContext")->item(0));
//                    $this->wsUserContext = preg_replace('/userContext xmlns[^=]*="[^"]*"/i', 'userContext', $this->wsUserContext);
//                    $this->wsUserContext = preg_replace('/userContext xmlns[^=]*="[^"]*"/i', 'userContext', $this->wsUserContext);
                $this->wsUserContext = preg_replace('/<userContext(.*?)>/i', '<userContext xmlns:ns8="http://common.soap.yodlee.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:type="ns8:UserContext" xmlns="">', $this->wsUserContext);

                Yii::app()->getSession()->add('userContextSession', serialize($this->wsUserContext));
            }
        } catch (SoapFault $E) {
            return false;
        }
    }

    /**
     * 
     */
    public function searchService($keyword) {
        try {
            $serviceToCall = "SearchService";

            $wsSearchServiceClientObject = new nusoap_client($this->url . "/" . $serviceToCall);
            //yodlee uses sslv3 protocol
            $wsSearchServiceClientObject->setCurlOption(CURLOPT_SSLVERSION, 3);

            $cobrandContextSession = unserialize(Yii::app()->getSession()->get('cobrandContextSession'));

            //requeest to soap service
            $wsSearchServiceRequestXml = <<<END
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
        <searchContentServicesByContainerType xmlns="">
             {$cobrandContextSession}
             <containerTypes>
<elements>bank</elements>
<elements>credits</elements>
</containerTypes>
            <keywords>{$keyword}</keywords>     
        </searchContentServicesByContainerType>
</soap:Body>
</soap:Envelope>
END;

            $wsSearchServiceResponseArray = $wsSearchServiceClientObject->send($wsSearchServiceRequestXml, 'searchContentServicesByContainerType', '');

            if ($wsSearchServiceClientObject->fault) {
                //throw the exception 
                return false;
            } else {

                if ($wsSearchServiceResponseArray["searchContentServicesByContainerTypeReturn"]) {
                    if (isset($wsSearchServiceResponseArray["searchContentServicesByContainerTypeReturn"]["elements"])) {
                        $itemArray = $wsSearchServiceResponseArray["searchContentServicesByContainerTypeReturn"]["elements"];
                    } else {
                        $itemArray = $wsSearchServiceResponseArray["searchContentServicesByContainerTypeReturn"];
                    }
                    $loopFlag = false;
                    foreach ($itemArray as $sResults) {

                        if (!is_array($sResults)) {

                            $sResults = $itemArray;
                            $loopFlag = true;
                        }

                        $wsEachResult = array(
                            'serviceId' => $sResults["contentServiceId"],
                            'displayName' => $sResults["contentServiceDisplayName"],
                            'loginURL' => $sResults["loginUrl"],
                            'homeURL' => $sResults["homeUrl"],
                            'registrationURL' => $sResults["registrationUrl"]
                        );
                        $wsRequiredField[] = $wsEachResult;

                        if ($loopFlag) {
                            break;
                        }
                    }
                    return array("items" => $wsRequiredField);
                } else {
                    return array("items" => array());
                }
            }
        } catch (SoapFault $E) {
            return false;
        }
    }

    /**
     * 
     */
    public function getFieldsByServiceId($contentServiceId) {
        try {
            $yodleeServiceToCall = "ItemManagementService";
            //login to yodlee cobrand
            // Init this component
            $wsAddAccountUserClientObject = new nusoap_client($this->url . "/" . $yodleeServiceToCall);
            //yodlee uses sslv3 protocol
            $wsAddAccountUserClientObject->setCurlOption(CURLOPT_SSLVERSION, 3);

            $cobrandContextSession = unserialize(Yii::app()->getSession()->get('cobrandContextSession'));

            //requeest to soap service
            $wsGetCredentialFieldsForContentXml = <<<END
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
        <getLoginFormForContentService xmlns="">
             {$cobrandContextSession}
             <contentServiceId>{$contentServiceId}</contentServiceId>
        </getLoginFormForContentService>
</soap:Body>
</soap:Envelope>
END;
            $wsGetCredentialFieldsForContentResponseArray = $wsAddAccountUserClientObject->send($wsGetCredentialFieldsForContentXml, 'getLoginFormForContentService', '');

            if ($wsAddAccountUserClientObject->fault) {
                //throw the exception 
                return false;
            } else {
                $wsItemsFields = array();
                //get all the fields 
                foreach ($wsGetCredentialFieldsForContentResponseArray["getLoginFormForContentServiceReturn"]["componentList"]["elements"] as $wsFieldsToPass) {
                    //multi level fields 
                    if (isset($wsFieldsToPass["fieldInfoList"])) {
                        foreach ($wsFieldsToPass["fieldInfoList"]["elements"] as $wsEachFieldstPass) {
                            $wsItemsFields[] = $wsEachFieldstPass;
                        }
                    } else {
                        if ($wsFieldsToPass["isOptional"] == "false") {
                            $wsItemsFields[] = $wsFieldsToPass;
                        }
                    }
                }
                return array("itemData" => $wsItemsFields);
            }
        } catch (SoapFault $E) {
            return false;
        }
    }

    /**
     * Add item to user account 
     */
    public function addAccountToUser($contentServiceId, $wsFields) {
        try {
            $yodleeServiceToCall = "ItemManagementService";
            //login to yodlee cobrand
            // Init this component
            $wsAddAccountUserClientObject = new nusoap_client($this->url . "/" . $yodleeServiceToCall);
            //yodlee uses sslv3 protocol
            $wsAddAccountUserClientObject->setCurlOption(CURLOPT_SSLVERSION, 3);

            $userContextSession = unserialize(Yii::app()->getSession()->get('userContextSession'));

            $credentialFieldsString = "";
            $typeCount = 3;

            foreach ($wsFields as $wsFieldKey => $wsFieldVal) {
                $credentialFieldsString .= "<elements xsi:type=\"ns" . $typeCount . ":FieldInfoSingle\" xmlns:ns" . $typeCount . "=\"http://common.soap.yodlee.com\">";
                $credentialFieldsString .= "<name>" . $wsFieldKey . "</name>";
                $credentialFieldsString .= "<isEditable>true</isEditable>";
                $credentialFieldsString .= "<isOptional>false</isOptional>";
                $credentialFieldsString .= "<isEscaped>false</isEscaped>";
                $credentialFieldsString .= "<isOptionalMFA>false</isOptionalMFA>";
                $credentialFieldsString .= "<isMFA>false</isMFA>";
                $credentialFieldsString .= "<value>" . $wsFieldVal . "</value>";
                $credentialFieldsString .= "<valueIdentifier>" . $wsFieldKey . "</valueIdentifier>";
                if (strstr($wsFieldKey, 'PASSWORD')) {
                    $credentialFieldsString .= "<fieldType>PASSWORD</fieldType>";
                } else {
                    $credentialFieldsString .= "<fieldType>TEXT</fieldType>";
                }
                $credentialFieldsString .= "<size>20</size>";
                $credentialFieldsString .= "<maxlength>22</maxlength>";
                $credentialFieldsString .= "</elements>";
                $typeCount = $typeCount + 1;
            }

            $wsAddItemRequestXml = <<<END
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
        <addItemForContentService1 xmlns="http://itemmanagement.accountmanagement.core.soap.yodlee.com">
                 {$userContextSession}
        <contentServiceId xmlns="">{$contentServiceId}</contentServiceId>
            <credentialFields xmlns="">
                {$credentialFieldsString}
            </credentialFields>
            <shareCredentialsWithinSite xmlns="">false</shareCredentialsWithinSite>
            <startRefreshItemOnAddition xmlns="">false</startRefreshItemOnAddition>
        </addItemForContentService1>
</soap:Body>
</soap:Envelope>
END;
            $wsAddItemRequestXmlArray = $wsAddAccountUserClientObject->send($wsAddItemRequestXml, 'addItemForContentService1', '');

            if (isset($wsAddItemRequestXmlArray["addItemForContentService1Return"])) {
                return array("status" => $wsAddItemRequestXmlArray["addItemForContentService1Return"]);
            } else {
                return array("status" => "0");
            }
        } catch (SoapFault $E) {
            return false;
        }
    }

    /**
     * Retrive all the user item added to the account
     * 
     */
    public function getUserItems() {
        try {
            $serviceToCall = "DataService";

            $wsDataServiceClientObject = new nusoap_client($this->url . "/" . $serviceToCall);
            //yodlee uses sslv3 protocol
            $wsDataServiceClientObject->setCurlOption(CURLOPT_SSLVERSION, 3);

            $userContextSession = unserialize(Yii::app()->getSession()->get('userContextSession'));
            $userContextSession = preg_replace('/userContext/', 'ctx', $userContextSession);
            //requeest to soap service
            $wsUserItemRequestXml = <<<END
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
        <getItemSummaries xmlns="">
             {$userContextSession}
        </getItemSummaries>
</soap:Body>
</soap:Envelope>
END;

            $wsDataSearchServiceResponseArray = $wsDataServiceClientObject->send($wsUserItemRequestXml, 'getItemSummaries ', '');

            if ($wsDataServiceClientObject->fault || isset($wsDataSearchServiceResponseArray["faultcode"])) {
                return array();
            } else {
                $itemArray = array();
                if (isset($wsDataSearchServiceResponseArray["getItemSummariesReturn"])) {
                    //multiple records present 
                    if (isset($wsDataSearchServiceResponseArray["getItemSummariesReturn"]["elements"])) {
                        $itemArray = $wsDataSearchServiceResponseArray["getItemSummariesReturn"]["elements"];
                    } else {
                        $itemArray = $wsDataSearchServiceResponseArray["getItemSummariesReturn"];
                    }
                    //to store bank details
                    $wsEachUserItemBankAccounts = array();
                    $loopFlag = false;
                    foreach ($itemArray as $wsUserAccount) {
                        if (!is_array($wsUserAccount)) {

                            $wsUserAccount = $itemArray;
                            $loopFlag = true;
                        }

                        $itemDataArray = array();

                        if (isset($wsUserAccount["itemData"]["accounts"]["elements"])) {
                            $itemDataArray = $wsUserAccount["itemData"]["accounts"]["elements"];
                        } else {
                            $itemDataArray = isset($wsUserAccount["itemData"]) ? $wsUserAccount["itemData"]["accounts"] : null;
                        }


                        $itemAccessStatus = true;
                        if ($wsUserAccount["refreshInfo"]["itemAccessStatus"] == "ACCESS_NOT_VERIFIED") {
                            $itemAccessStatus = false;
                        }
                        //if user has multiple bank accounts in single user id
                        $innerLoopFlag = false;

                        if (isset($itemDataArray)) {
                            foreach ($itemDataArray as $bankAccounts) {
                                if (!is_array($bankAccounts)) {

                                    $bankAccounts = $itemDataArray;
                                    $innerLoopFlag = true;
                                }

                                $wsEachUserItemBankAccount = array(
                                    'bankAccountId' => $bankAccounts["bankAccountId"],
                                    'itemAccountId' => isset($bankAccounts["itemAccountId"]) ? $bankAccounts["itemAccountId"] : 0,
                                    'accountNumber' => isset($bankAccounts["accountNumber"]) ? $bankAccounts["accountNumber"] : "xxxx",
                                    'accountHolder' => isset($bankAccounts["accountHolder"]) ? $bankAccounts["accountHolder"] : $wsUserAccount["itemDisplayName"],
                                    'availableBalance' => $bankAccounts["availableBalance"]["amount"],
                                    'availableAmountCurrency' => $bankAccounts["availableBalance"]["currencyCode"],
                                    'currentBalance' => $bankAccounts["currentBalance"]["amount"],
                                    'currentAmountCurrency' => $bankAccounts["currentBalance"]["currencyCode"],
                                    'itemId' => $wsUserAccount["itemId"],
                                    'contentServiceId' => $wsUserAccount["contentServiceId"],
                                    'itemDisplayName' => $wsUserAccount["itemDisplayName"],
                                    'itemAccessStatus' => $itemAccessStatus,
                                );
                                $wsEachUserItemBankAccounts[] = $wsEachUserItemBankAccount;
                                if ($innerLoopFlag) {
                                    break;
                                }
                            }
                        }
                        if ($loopFlag) {
                            break;
                        }
                    }
                    return $wsEachUserItemBankAccounts;
                } else {
                    return array();
                }
            }
        } catch (SoapFault $E) {
            //log the error
            return array();
        }
    }

    /**
     * Transaction search for item transactions 
     * (like bank) 
     * 
     */
    public function transactionSearchService($itemAccId, $limitStart = 1, $limitEnd = 20, $fcall = true) {
        try {
            $serviceToCall = "TransactionSearchService";

            $wsDataServiceClientObject = new nusoap_client($this->url . "/" . $serviceToCall);
            //yodlee uses sslv3 protocol
            $wsDataServiceClientObject->setCurlOption(CURLOPT_SSLVERSION, 3);

            $userContextSession = unserialize(Yii::app()->getSession()->get('userContextSession'));

            //requeest to soap service
            $wsDataSearchServiceRequestXml = <<<END
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
        <executeUserSearchRequest xmlns="">
             {$userContextSession}
        <transactionSearchRequest xmlns="">
        <searchFilter>
            <itemAccountId><identifier>{$itemAccId}</identifier></itemAccountId>
            <sortByContainer>false</sortByContainer>
            <sortByItemAccountId>false</sortByItemAccountId>
        </searchFilter>
        <resultRange>
            <startNumber>{$limitStart}</startNumber>
            <endNumber>{$limitEnd}</endNumber>
        </resultRange>
        <ignoreUserInput>true</ignoreUserInput>
        <containerType>all</containerType>
        <ignoreManualTransactions>false</ignoreManualTransactions>
        <ignorePaymentTransactions>false</ignorePaymentTransactions>
        <ignoreFTTransactions>false</ignoreFTTransactions>
        <ignorePendingTransactions>false</ignorePendingTransactions>
        <computeRunningBalance>false</computeRunningBalance>
        <computeProjectedBalance>false</computeProjectedBalance>
        <fetchFutureTransactions>false</fetchFutureTransactions>
        <includeAggregatedTransactions>true</includeAggregatedTransactions>
        <calculateTransactionBalance>true</calculateTransactionBalance>
        <ignoreTransferCategoryTransactions>false</ignoreTransferCategoryTransactions>
        <isSharedAccountTransactionReq>false</isSharedAccountTransactionReq>
        <searchClients>DEFAULT_SERVICE_CLIENT</searchClients>      
        <firstCall>{$fcall}</firstCall>
        <isAvailable>false</isAvailable>
        <ignoreBankTransactions>false</ignoreBankTransactions>
        <ignoreCardTransactions>false</ignoreCardTransactions>
        <ignoreLoanTransactions>false</ignoreLoanTransactions>
        <ignoreInsuranceTransactions>false</ignoreInsuranceTransactions>
        <ignoreInvestmentTransactions>false</ignoreInvestmentTransactions>
        <forSpendingReports>false</forSpendingReports>
        <enableProjectedTransactionPartitioning>false</enableProjectedTransactionPartitioning>
        </transactionSearchRequest>
     </executeUserSearchRequest>
</soap:Body>
</soap:Envelope>
END;

            $wsDataSearchServiceResponseArray = $wsDataServiceClientObject->send($wsDataSearchServiceRequestXml, 'executeUserSearchRequest', '');

            if ($wsDataServiceClientObject->fault || isset($wsDataSearchServiceResponseArray["faultcode"])) {
                return array("totalrecords" => "0");
            } else {
                $wsTransactionResponse = array();
                if (isset($wsDataSearchServiceResponseArray["executeUserSearchRequestReturn"])) {

                    if (isset($wsDataSearchServiceResponseArray["executeUserSearchRequestReturn"]["searchIdentifier"]["identifier"])) {
                        $searchIdentifier = $wsDataSearchServiceResponseArray["executeUserSearchRequestReturn"]["searchIdentifier"]["identifier"];
                        $numberOfHits = $wsDataSearchServiceResponseArray["executeUserSearchRequestReturn"]["numberOfHits"];

                        //each transaction
                        foreach ($wsDataSearchServiceResponseArray["executeUserSearchRequestReturn"]["searchResult"]["transactions"]["elements"] as $transaction) {

                            $wsEachTransaction = array(
                                'transactionId' => $transaction["viewKey"]["transactionId"],
                                'description' => $transaction["description"]["description"],
                                'amount' => $transaction["amount"]["amount"],
                                'currencyCode' => $transaction["amount"]["currencyCode"],
                                'category' => $transaction["category"]["categoryName"],
                                'postDate' => date('d-m-Y H:i:s', strtotime($transaction["postDate"]))
                            );
                            $wsTransactionResponse[] = $wsEachTransaction;
                        }
                        return array(
                            "totalrecords" => $numberOfHits,
                            "searchIdentifier" => $searchIdentifier,
                            "transactions" => $wsTransactionResponse
                        );
                    } else {
                        return array("totalrecords" => "0");
                    }
                } else {
                    return array("totalrecords" => "0");
                }
            }
        } catch (SoapFault $E) {
            //log the error 
            return array("totalrecords" => "0");
        }
    }

    /**
     * Pagination Transaction search for item transactions 
     * (like bank) 
     * Called after 1st page
     * 
     */
    public function transactionSearchServicePagination($searchIdentifier, $limitStart = 1, $limitEnd = 20) {
        try {
            $serviceToCall = "TransactionSearchService";

            $wsDataServiceClientObject = new nusoap_client($this->url . "/" . $serviceToCall);
            //yodlee uses sslv3 protocol
            $wsDataServiceClientObject->setCurlOption(CURLOPT_SSLVERSION, 3);

            $userContextSession = unserialize(Yii::app()->getSession()->get('userContextSession'));

            //requeest to soap service
            $wsDataSearchServiceRequestXml = <<<END
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
        <getUserTransactions xmlns="">
             {$userContextSession}
            <searchFetchRequest xmlns="">
            <searchIdentifier>
            <identifier>{$searchIdentifier}</identifier>
            </searchIdentifier>
            <searchResultRange>
            <startNumber>{$limitStart}</startNumber>
            <endNumber>{$limitEnd}</endNumber>
            </searchResultRange>
            </searchFetchRequest>
     </getUserTransactions>
</soap:Body>
</soap:Envelope>
END;

            $wsDataSearchServiceResponseArray = $wsDataServiceClientObject->send($wsDataSearchServiceRequestXml, 'getUserTransactions', '');

            if ($wsDataServiceClientObject->fault || isset($wsDataSearchServiceResponseArray["faultcode"])) {
                //throw the exception 
                return array("totalrecords" => "0");
            } else {
                $wsTransactionResponse = array();
                if (isset($wsDataSearchServiceResponseArray["getUserTransactionsReturn"])) {

                    if (isset($wsDataSearchServiceResponseArray["getUserTransactionsReturn"]["transactions"])) {
                        //each transaction
                        foreach ($wsDataSearchServiceResponseArray["getUserTransactionsReturn"]["transactions"]["elements"] as $transaction) {

                            $wsEachTransaction = array(
                                'transactionId' => $transaction["viewKey"]["transactionId"],
                                'description' => $transaction["description"]["description"],
                                'amount' => $transaction["amount"]["amount"],
                                'currencyCode' => $transaction["amount"]["currencyCode"],
                                'category' => $transaction["category"]["categoryName"],
                                'postDate' => date('d-m-Y H:i:s', strtotime($transaction["postDate"]))
                            );
                            $wsTransactionResponse[] = $wsEachTransaction;
                        }
                        return array(
                            "searchIdentifier" => $searchIdentifier,
                            "transactions" => $wsTransactionResponse
                        );
                    } else {
                        return array("totalrecords" => "0");
                    }
                } else {
                    return array("totalrecords" => "0");
                }
            }
        } catch (SoapFault $E) {
            //log the error 
            return array("totalrecords" => "0");
        }
    }

    /**
     * refresh service to refresh the item data
     * 
     */
    public function refreshService($itemId) {
        try {
            $serviceToCall = "RefreshService";

            $wsRefreshServiceClientObject = new nusoap_client($this->url . "/" . $serviceToCall);
            //yodlee uses sslv3 protocol
            $wsRefreshServiceClientObject->setCurlOption(CURLOPT_SSLVERSION, 3);

            $userContextSession = unserialize(Yii::app()->getSession()->get('userContextSession'));

            //requeest to soap service
            $wsRefreshSearchServiceRequestXml = <<<END
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
        <startRefresh4 xmlns="">
             {$userContextSession}
             <itemId>{$itemId}</itemId>
             <refreshPriority>1</refreshPriority>
        </startRefresh4>
</soap:Body>
</soap:Envelope>
END;
            $wsRefreshSearchServiceResponseArray = $wsRefreshServiceClientObject->send($wsRefreshSearchServiceRequestXml, 'startRefresh4', '');

            if ($wsRefreshServiceClientObject->fault || isset($wsRefreshSearchServiceResponseArray["faultcode"])) {
                //throw the exception 
                return false;
            } else {
                //
                if (isset($wsRefreshSearchServiceResponseArray["startRefresh4Return"])) {
                    return array("message" => $wsRefreshSearchServiceResponseArray["startRefresh4Return"]);
                }
            }
        } catch (SoapFault $E) {
            return false;
        }
    }

    /**
     * Refresh service to refresh the item data
     * 
     */
    public function mfaRefreshService($itemId) {
        try {
            $serviceToCall = "RefreshService";

            $wsRefreshServiceClientObject = new nusoap_client($this->url . "/" . $serviceToCall);
            //yodlee uses sslv3 protocol
            $wsRefreshServiceClientObject->setCurlOption(CURLOPT_SSLVERSION, 3);

            $userContextSession = unserialize(Yii::app()->getSession()->get('userContextSession'));

            //requeest to soap service
            $wsRefreshSearchServiceRequestXml = <<<END
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
        <getMFAResponse xmlns="">
             {$userContextSession}
             <itemId>{$itemId}</itemId>
        </getMFAResponse>
</soap:Body>
</soap:Envelope>
END;
            $wsRefreshSearchServiceResponseArray = $wsRefreshServiceClientObject->send($wsRefreshSearchServiceRequestXml, 'getMFAResponse', '');

            if ($wsRefreshServiceClientObject->fault || isset($wsRefreshSearchServiceResponseArray["faultcode"])) {
                //throw the exception 
                return false;
            } else {

                //
                if (isset($wsRefreshSearchServiceResponseArray["getMFAResponseReturn"])) {
                    //if there is field info 
                    if (isset($wsRefreshSearchServiceResponseArray["getMFAResponseReturn"]["isMessageAvailable"]) && $wsRefreshSearchServiceResponseArray["getMFAResponseReturn"]["isMessageAvailable"] == "true") {
                        //questions to login to the bank
                        //Example Bank: US Bank
                        if (isset($wsRefreshSearchServiceResponseArray["getMFAResponseReturn"])) {
                            $lsQuestionArray = array();
                            foreach ($wsRefreshSearchServiceResponseArray["getMFAResponseReturn"]["fieldInfo"]["questionAndAnswerValues"] as $lsQuestions) {
                                $lsQuestion = array(
                                    'questionFieldType' => $lsQuestions["questionFieldType"],
                                    'responseFieldType' => $lsQuestions["responseFieldType"],
                                    'isRequired' => $lsQuestions["isRequired"],
                                    'sequence' => $lsQuestions["sequence"],
                                    'metaData' => $lsQuestions["metaData"],
                                    'question' => $lsQuestions["question"]
                                );
                                $lsQuestionArray[] = $lsQuestion;
                            }
                            return array("mfaquestions" => $lsQuestionArray);
                        }
                    }
                }
                //send empty 
                return array("mfaquestions" => "");
            }
        } catch (SoapFault $E) {
            return false;
        }
    }

    /**
     * refresh service to refresh the item data
     * 
     */
    public function mfaputService($itemId, $lsQuestionAndAnswers) {
        try {
            $serviceToCall = "RefreshService";

            $wsRefreshServiceClientObject = new nusoap_client($this->url . "/" . $serviceToCall);
            //yodlee uses sslv3 protocol
            $wsRefreshServiceClientObject->setCurlOption(CURLOPT_SSLVERSION, 3);

            $userContextSession = unserialize(Yii::app()->getSession()->get('userContextSession'));

            foreach ($lsQuestionAndAnswers as $lsQuestionAndAns) {
                $lsQuestionsFields = <<<END
                <elements xsi:type="ns3:QuesAndAnswerDetails">
                               <question xsi:type="xsd:string">{$lsQuestionAndAns["question"]}</question>
                               <answer xsi:type="xsd:string">{$lsQuestionAndAns["answer"]}</answer>
                               <questionFieldType xsi:type="xsd:string">{$lsQuestionAndAns["questionFieldType"]}</questionFieldType>
                               <answerFieldType xsi:type="xsd:string">{$lsQuestionAndAns["answerFieldType"]}</answerFieldType>
                               <metaData xsi:type="xsd:string">{$lsQuestionAndAns["metaData"]}</metaData>
                </elements>                
END;
            }
            //requeest to soap service
            $wsRefreshSearchServiceRequestXml = <<<END
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
        <putMFARequest xmlns="">
             {$userContextSession}
              <userResponse xsi:type="ns3:MFAQuesAnsResponse" xmlns="" xmlns:ns3="http://mfarefresh.core.soap.yodlee.com">
                    <quesAnsDetailArray xsi:type="ns4:ArrayOfQuesAndAnswerDetails" xmlns:ns4="http://mfarefresh.core.collections.soap.yodlee.com">
                       {$lsQuestionsFields}
                    </quesAnsDetailArray>
                </userResponse>
             <itemId>{$itemId}</itemId>
        </putMFARequest>
</soap:Body>
</soap:Envelope>
END;
            $wsRefreshSearchServiceResponseArray = $wsRefreshServiceClientObject->send($wsRefreshSearchServiceRequestXml, 'putMFARequest', '');

            if ($wsRefreshServiceClientObject->fault || isset($wsRefreshSearchServiceResponseArray["faultcode"])) {
                //throw the exception 
                return false;
            } else {
                //if return is sucessfull
                if (isset($wsRefreshSearchServiceResponseArray["putMFARequestReturn"]) && $wsRefreshSearchServiceResponseArray["putMFARequestReturn"]) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (SoapFault $E) {
            return false;
        }
    }

    /**
     * remove item from the user
     * 
     */
    public function removeitemService($itemId) {
        try {
            $serviceToCall = "ItemManagementService";

            $wsRemoveClientObject = new nusoap_client($this->url . "/" . $serviceToCall);
            //yodlee uses sslv3 protocol
            $wsRemoveClientObject->setCurlOption(CURLOPT_SSLVERSION, 3);

            $userContextSession = unserialize(Yii::app()->getSession()->get('userContextSession'));

            //requeest to soap service
            $wsRemoveServiceRequestXml = <<<END
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
        <removeItem xmlns="">
             {$userContextSession}
             <itemId>{$itemId}</itemId>
        </removeItem>
</soap:Body>
</soap:Envelope>
END;
            $wsRemoveServiceResponseArray = $wsRemoveClientObject->send($wsRemoveServiceRequestXml, 'removeItem', '');

            if ($wsRemoveClientObject->fault) {
                //throw the exception 
                return false;
            } else {

                print_r($wsRemoveServiceResponseArray);
                die;
            }
        } catch (SoapFault $E) {
            return false;
        }
    }

    /**
     * Batch user service 
     */
    public function batchUserServices() {
        try {
            $serviceToCall = "BatchUserServicesService";
            //login to yodlee cobrand
            // Init this component
            $wsBatchUserClientObject = new nusoap_client($this->url . "/" . $serviceToCall);
            //yodlee uses sslv3 protocol
            $wsBatchUserClientObject->setCurlOption(CURLOPT_SSLVERSION, 3);

            $cobrandContextSession = unserialize(Yii::app()->getSession()->get('cobrandContextSession'));
            $checkDate = '2013-01-24T00:00:00';
            //requeest to soap service
            $wsBatchUserRequestXml = <<<END
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
        <getRegisteredUsers xmlns="">
             {$cobrandContextSession} 
             <startId>100</startId>
        </getRegisteredUsers>
</soap:Body>
</soap:Envelope>
END;
            $wsBatchUserClientResponseArray = $wsBatchUserClientObject->send($wsBatchUserRequestXml, 'getRegisteredUsers', '');
print_r($wsBatchUserClientResponseArray);die;          
            //catch any exception and throw to calling module
            $err = $wsCreateUserClientObject->getError();
            if ($err) {
                return false;
            }

            if ($wsCreateUserClientObject->fault) {
                //throw the exception 
                return false;
            } else {
                //user sucessfully created update the table to add the yodlee 
                //users details in the table  
                if (isset($wsCreateUserClientResponseArray["register3Return"]["userId"])) {
                    return $wsCreateUserClientResponseArray["register3Return"]["userId"];
                } else {
                    return 0;
                }
            }
        } catch (SoapFault $E) {
            return false;
        }
    }

    /**
     * Fenerate the password 
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

}

?>