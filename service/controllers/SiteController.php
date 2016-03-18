<?php

/* * ********************************************************************
 * Filename: SiteController.php
 * Folder: controllers
 * Description: Main controller for the application
 * @author Subramanya HS (For TruGlobal Inc)
 *         Thayub Hashim
 *         Melroy Saldanha
 * @copyright (c) 2012 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */
require_once(realpath(dirname(__FILE__) . '/../helpers/email/Email.php'));
require_once(realpath(dirname(__FILE__) . '/../lib/stripe/Stripe.php'));

class SiteController extends Controller {

    // Define access control
    public function accessRules() {
        return array_merge(
                array(array('allow', 'users' => array('?'))),
                // Include parent access rules
                parent::accessRules()
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        echo "No service here";
        die;
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        $error = Yii::app()->errorHandler->error;
        if ($error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }else {
            throw new CHttpException(400, Yii::t('err', 'bad request'));
        }
    }

    public function actionGetcachedata() {
        $md5hashval = "b28159c334ecd24e2f8d17ad64407362";
        if (!isset($_GET['accTok']) || $_GET['accTok'] == '' || md5($_GET['accTok']) != $md5hashval) {
            header('HTTP/1.1 403 Unauthorized');
            exit;
        }

        if (isset($_GET['flush']) && $_GET['flush'] == "true") {
            Yii::app()->cache->flush();
        }

        if (!isset($_GET['uid'])) {
            $this->sendResponse(200, CJSON::encode(array('status' => "OK", 'score' => false, 'montecarlo' => false, 'actionstep' => false, 'notification' => false, 'login' => false, 'logout' => false)));
        }

        $user_id = $_GET['uid'];
        $this->sendResponse(200, CJSON::encode(array('status' => "OK", 'score' => Yii::app()->cache->get("score" . $user_id), 'montecarlo' => Yii::app()->cache->get("montecarlo" . $user_id), 'actionstep' => Yii::app()->cache->get("actionstep" . $user_id), 'notification' => Yii::app()->cache->get("notification" . $user_id), 'login' => Yii::app()->cache->get("login" . $user_id), 'logout' => Yii::app()->cache->get("logout" . $user_id))));
    }


    /**
     * actionLogin in the UserController performs login check for Users and Admins
     */
    public function actionLogin() {
        $email = Yii::app()->request->getParam('email');
        $password = Yii::app()->request->getParam('password');

        if (!isset($email) || empty($email) || !isset($password) || empty($password)) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Incorrect email/password combination.')));
            return;
        }

        $userDetails = User::model()->find(array('condition' => 'email = :email AND isactive = "'.User::USER_IS_ACTIVE.'"',
                    'params' => array(':email' => $email)));
        if ($userDetails) {
            // if the user has recent failed login attempts, get the count of attempts, the time of the first try, and the time of the last try
            $failedLoginAttempts = UserAccess::model()->findAll(array('select' => 'id, accesstimestamp',
                        'condition' => 'attempt = '.UserAccess::FAILED.' and user_id = :user_id AND current = '.UserAccess::TRUE.
                        ' AND authentication = '.UserAccess::PASSWORD, 'params' => array(':user_id' => $userDetails->id)));

            // Check if the user has reached the limit of login attempt limit
            if ($failedLoginAttempts && count($failedLoginAttempts) >= UserAccess::FAILED_LOGIN_COUNT_LIMIT) {

                // if the difference between the last and first of the five failed login attempts is less than the login attempt time limit, lock the account
                $firstFailedAttemptTime = array_values($failedLoginAttempts)[0]['accesstimestamp'];
                $lastFailedAttemptTime = array_values($failedLoginAttempts)[count($failedLoginAttempts)-1]['accesstimestamp'];

                if ( (strtotime($lastFailedAttemptTime) - strtotime($firstFailedAttemptTime)) < UserAccess::FAILED_LOGIN_TIME_LIMIT ) {
                    // while we are within the lockout duration, keep the user locked out
                    if ( (strtotime(date("Y-m-d H:i:s")) - strtotime($lastFailedAttemptTime)) < UserAccess::LOCKOUT_DURATION ) {
                        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'type' => 'locked',
                            'message' => 'Your account has been locked. Please use the Forgot Password link below to reset your password.')));
                    // once the lockout period is over, archive all of the old unsuccessful attempts by setting current to false
                    } else {
                        UserAccess::model()->updateAll(array('current' => UserAccess::FALSE),'user_id = ' . $userDetails->id .
                            ' and attempt = '.UserAccess::FAILED. ' and authentication = '.UserAccess::PASSWORD);
                    }
                // otherwise, archive the first login and add a new one, so that the login attempt time limit is a rolling difference between first and last attempts
                } else {
                    $firstFailedAttemptId = array_values($failedLoginAttempts)[0]['id'];
                    $firstFailedAttempt = UserAccess::model()->find(array('condition' => 'id = :id',
                            'params' => array(':id' => $firstFailedAttemptId)));
                    $firstFailedAttempt->current = UserAccess::FALSE;
                    $firstFailedAttempt->save();
                }
            }

            $userModel = new User();
            $userModel->email = $email;
            $userModel->password = $password;
            $userModel->urole = User::USER_IS_CONSUMER;

            if ($userModel->authenticateLogin()) {
                Yii::app()->getSession()->remove('wsadvisor');
                $notiCount = Notification::model()->count("user_id=:user_id and status=0", array("user_id" => Yii::app()->getSession()->get('wsuser')->id));

                $authenticatedUser = User::model()->find("id=:id", array("id" => $userDetails->id));
                $authenticatedUser->lastaccesstimestamp = new CDbExpression('NOW()');
                $authenticatedUser->save();

                // a successful password login archives all previous unsuccessful password attempts in the useraccess table.
                UserAccess::model()->updateAll(array('current' => UserAccess::FALSE),'user_id = ' . $authenticatedUser->id .
                            ' and attempt = '.UserAccess::FAILED. ' and authentication = '.UserAccess::PASSWORD);

                // add a new successful password login record
                $userAccess = new UserAccess();
                $userAccess->user_id = $authenticatedUser->id;
                $userAccess->attempt = UserAccess::SUCCESS;
                $userAccess->authentication = UserAccess::PASSWORD;
                $userAccess->accesstimestamp = date("Y-m-d H:i:s");
                $userAccess->current = UserAccess::TRUE;
                $userAccess->save();

                $hasPin = false;
                if (isset($authenticatedUser->pin) && $authenticatedUser->pin != "") {
                    $hasPin = true;
                }
                $wsArrayToSend = array(
                    "status" => 'OK',
                    "id" => Yii::app()->getSession()->get('wsuser')->id,
                    "email" => Yii::app()->getSession()->get('wsuser')->email,
                    "urole" => Yii::app()->getSession()->get('wsuser')->roleid,
                    "sess" => Yii::app()->session->sessionID,
                    "notification" => $notiCount,
                    "type" => 'login',
                    "uniquehash" => md5(Yii::app()->getSession()->get('wsuser')->email),
                    "hasPin" => $hasPin
                );

                $user_id = Yii::app()->getSession()->get('wsuser')->id;
                $array = Yii::app()->cache->get('login' . $user_id);
                if ($array === false) {
                    $array = array();
                }
                $array[Yii::app()->session->sessionID] = date("Y-m-d H:i:s");
                Yii::app()->cache->set('login' . $user_id, $array);

                $ActionStepObj = new ActionstepController(1);
                $wsArrayToSend['mixPanelData'] = $ActionStepObj->updateComponents();

                /** Start - mixpanel tracking * */
                $jsMixpanelCall = Yii::app()->request->getParam('jsMixpanelCall');
                if (!isset($jsMixpanelCall)) {
                    $mixPanelClientObj = new MixPanelClient();
                    if ($mixPanelClientObj->status == 'active') {
                        $mixPanelObj = Mixpanel::getInstance($mixPanelClientObj->token);
                        $userUniqueHash = md5(trim($email));
                        $mixPanelObj->identify($userUniqueHash);
                        $mixPanelArr = array(
                            'Last Login' => date('Y-m-d H:i:s'),
                            'Score' => $wsArrayToSend['mixPanelData']['currentScore'],
                            'Age' => $wsArrayToSend['mixPanelData']['age'],
                            'Has Connected Accounts' => $wsArrayToSend['mixPanelData']['connectedAccounts'],
                            'Auto Loan Rate' => $wsArrayToSend['mixPanelData']['autoLoanRate'],
                            'IRA Contribution' => $wsArrayToSend['mixPanelData']['IRAcontribution'],
                            'CR User Contribution' => $wsArrayToSend['mixPanelData']['CRcontribution'],
                            'CR Employer Contribution' => $wsArrayToSend['mixPanelData']['CREmpContribution'],
                            'No. of children' => $wsArrayToSend['mixPanelData']['noofchildren'],
                            'Weighted Mortgage Rate' => $wsArrayToSend['mixPanelData']['wmortrate'],
                            'Weighted CC Rate' => $wsArrayToSend['mixPanelData']['wccrate'],
                            'Weighted Loan Rate' => $wsArrayToSend['mixPanelData']['wloanrate'],
                            'Income' => $wsArrayToSend['mixPanelData']['monthlyIncome'],
                            'Credit Score' => $wsArrayToSend['mixPanelData']['creditScore'],
                            'Total Assets' => $wsArrayToSend['mixPanelData']['totalAssets'],
                            'Total Debts' => $wsArrayToSend['mixPanelData']['totalDebts']
                        );
                        if (isset($wsArrayToSend['mixPanelData']['hasWill'])) {
                            $mixPanelArr['Has Will'] = $wsArrayToSend['mixPanelData']['hasWill'];
                        }
                        if (isset($wsArrayToSend['mixPanelData']['willReviewed'])) {
                            $mixPanelArr['Will Reviewed'] = $wsArrayToSend['mixPanelData']['willReviewed'];
                        }
                        if (isset($wsArrayToSend['mixPanelData']['riskValue'])) {
                            $mixPanelArr['Risk'] = $wsArrayToSend['mixPanelData']['riskValue'];
                        }
                        if (isset($wsArrayToSend['mixPanelData']['profileCompleteness'])) {
                            $mixPanelArr['Profile Completeness'] = $wsArrayToSend['mixPanelData']['profileCompleteness'];
                        }
                        if (isset($wsArrayToSend['mixPanelData']['collegeAmount'])) {
                            $mixPanelArr['College Balance'] = $wsArrayToSend['mixPanelData']['collegeAmount'];
                        }
                        if (isset($wsArrayToSend['mixPanelData']['lifeInsuranceNeeded'])) {
                            $mixPanelArr['Life Insurance Needed'] = $wsArrayToSend['mixPanelData']['lifeInsuranceNeeded'];
                        }
                        if (isset($wsArrayToSend['mixPanelData']['disabilityInsuranceNeeded'])) {
                            $mixPanelArr['Disability Insurance Needed'] = $wsArrayToSend['mixPanelData']['disabilityInsuranceNeeded'];
                        }
                        $mixPanelObj->people->setOnce($userUniqueHash, $mixPanelArr);
                        $mixPanelObj->track("User Logged In", array(
                            'user_logged_in' => $userUniqueHash,
                            'score' => $wsArrayToSend['mixPanelData']['currentScore']
                        ));
                    }
                }
                /** End - mixpanel tracking * */
                $this->sendResponse(200, CJSON::encode($wsArrayToSend));
            } else {
                // add a new unsuccessful login attempt record
                $userAccess = new UserAccess();
                $userAccess->user_id = $userDetails->id;
                $userAccess->attempt = UserAccess::FAILED;
                $userAccess->authentication = UserAccess::PASSWORD;
                $userAccess->accesstimestamp = date("Y-m-d H:i:s");
                $userAccess->current = UserAccess::TRUE;
                $userAccess->save();
                unset($userAccess);
            }
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => 'Incorrect email/password combination.')));
    }


    /**
     *  actionLoginByPin() is used by mobile devices to log in using a pin.
     */
    public function actionLoginByPin() {
        $email = Yii::app()->request->getParam('email');
        $pin = Yii::app()->request->getParam('pin');
        if (!isset($email) || empty($email) || !isset($pin) || empty($pin)) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Incorrect email/pin combination.')));
            return;
        }
        $userDetails = User::model()->find(array('condition' => 'email = :email AND isactive = "'.User::USER_IS_ACTIVE.'"',
                    'params' => array(':email' => $email)));
        if ($userDetails) {
            // if the user has recent failed login attempts, get the count of attempts, the time of the first try, and the time of the last try
            $failedLoginAttempts = UserAccess::model()->findAll(array('select' => 'id, accesstimestamp',
                        'condition' => 'attempt = '.UserAccess::FAILED.' and user_id = :user_id AND current = '.UserAccess::TRUE.
                        ' AND authentication = '.UserAccess::PIN, 'params' => array(':user_id' => $userDetails->id)));

            // Check if the user has reached the limit of login attempt limit
            if ($failedLoginAttempts && count($failedLoginAttempts) >= UserAccess::FAILED_LOGIN_COUNT_LIMIT) {

                // if the difference between the last and first of the five failed login attempts is less than the login attempt time limit, lock the account
                $firstFailedAttemptTime = array_values($failedLoginAttempts)[0]['accesstimestamp'];
                $lastFailedAttemptTime = array_values($failedLoginAttempts)[count($failedLoginAttempts)-1]['accesstimestamp'];

                if ( (strtotime($lastFailedAttemptTime) - strtotime($firstFailedAttemptTime)) < UserAccess::FAILED_LOGIN_TIME_LIMIT ) {
                    // while we are within the lockout duration, keep the user locked out
                    if ( (strtotime(date("Y-m-d H:i:s")) - strtotime($lastFailedAttemptTime)) < UserAccess::LOCKOUT_DURATION ) {

                        $unlockTime = strtotime($lastFailedAttemptTime) + UserAccess::LOCKOUT_DURATION;
                        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => 'Your pin access has been locked. '
                            . 'Please log in with your username and password to reset your pin.')));
                        return;
                    // once the lockout period is over, archive all of the old unsuccessful attempts by setting current to false
                    } else {
                        UserAccess::model()->updateAll(array('current' => UserAccess::FALSE),'user_id = ' . $userDetails->id .
                            ' and attempt = '.UserAccess::FAILED. ' and authentication = '.UserAccess::PIN);
                    }
                // otherwise, archive the first login and add a new one, so that the login attempt time limit is a rolling difference between first and last attempts
                } else {
                    $firstFailedAttemptId = array_values($failedLoginAttempts)[0]['id'];
                    $firstFailedAttempt = UserAccess::model()->find(array('condition' => 'id = :id',
                            'params' => array(':id' => $firstFailedAttemptId)));
                    $firstFailedAttempt->current = UserAccess::FALSE;
                    $firstFailedAttempt->save();
                }
            }

            $userModel = new User();
            $userModel->email = $email;
            $userModel->pin = $pin;
            $userModel->urole = User::USER_IS_CONSUMER;
            if ($userModel->authenticateByPin()) {

                $authenticatedUser = User::model()->find("id=:id", array("id" => $userDetails->id));
                $authenticatedUser->lastaccesstimestamp = new CDbExpression('NOW()');
                $authenticatedUser->save();

                $notiCount = Notification::model()->count("user_id=:user_id and status=0", array("user_id" => Yii::app()->getSession()->get('wsuser')->id));
                $wsArrayToSend = array(
                    "status" => 'OK',
                    "id" => Yii::app()->getSession()->get('wsuser')->id,
                    "email" => Yii::app()->getSession()->get('wsuser')->email,
                    "urole" => Yii::app()->getSession()->get('wsuser')->roleid,
                    "sess" => Yii::app()->session->sessionID,
                    "notification" => $notiCount,
                    "type" => 'login',
                    "uniquehash" => md5(Yii::app()->getSession()->get('wsuser')->email),
                );

                // a successful pin login archives all previous unsuccessful pin attempts in the useraccess table.
                UserAccess::model()->updateAll(array('current' => UserAccess::FALSE),'user_id = ' . $authenticatedUser->id .
                            ' and attempt = '.UserAccess::FAILED. ' and authentication = '.UserAccess::PIN);

                // add a new successful pin login record
                $userAccess = new UserAccess();
                $userAccess->user_id = $authenticatedUser->id;
                $userAccess->attempt = UserAccess::SUCCESS;
                $userAccess->authentication = UserAccess::PIN;
                $userAccess->accesstimestamp = date("Y-m-d H:i:s");
                $userAccess->current = UserAccess::TRUE;
                $userAccess->save();



                $ActionStepObj = new ActionstepController(1);
                $wsArrayToSend['mixPanelData'] = $ActionStepObj->updateComponents();

                $mixPanelClientObj = new MixPanelClient();
                if ($mixPanelClientObj->status == 'active') {
                    $mixPanelObj = Mixpanel::getInstance($mixPanelClientObj->token);
                    $userUniqueHash = md5(trim($email));
                    $mixPanelObj->identify($userUniqueHash);
                    $mixPanelArr = array(
                        'Last Login' => date('Y-m-d H:i:s'),
                        'Score' => $wsArrayToSend['mixPanelData']['currentScore'],
                        'Age' => $wsArrayToSend['mixPanelData']['age'],
                        'Has Connected Accounts' => $wsArrayToSend['mixPanelData']['connectedAccounts'],
                        'Auto Loan Rate' => $wsArrayToSend['mixPanelData']['autoLoanRate'],
                        'IRA Contribution' => $wsArrayToSend['mixPanelData']['IRAcontribution'],
                        'CR User Contribution' => $wsArrayToSend['mixPanelData']['CRcontribution'],
                        'CR Employer Contribution' => $wsArrayToSend['mixPanelData']['CREmpContribution'],
                        'No. of children' => $wsArrayToSend['mixPanelData']['noofchildren'],
                        'Weighted Mortgage Rate' => $wsArrayToSend['mixPanelData']['wmortrate'],
                        'Weighted CC Rate' => $wsArrayToSend['mixPanelData']['wccrate'],
                        'Weighted Loan Rate' => $wsArrayToSend['mixPanelData']['wloanrate'],
                        'Income' => $wsArrayToSend['mixPanelData']['monthlyIncome'],
                        'Credit Score' => $wsArrayToSend['mixPanelData']['creditScore'],
                        'Total Assets' => $wsArrayToSend['mixPanelData']['totalAssets'],
                        'Total Debts' => $wsArrayToSend['mixPanelData']['totalDebts']
                    );

                    if (isset($wsArrayToSend['mixPanelData']['hasWill'])) {
                        $mixPanelArr['Has Will'] = $wsArrayToSend['mixPanelData']['hasWill'];
                    }
                    if (isset($wsArrayToSend['mixPanelData']['willReviewed'])) {
                        $mixPanelArr['Will Reviewed'] = $wsArrayToSend['mixPanelData']['willReviewed'];
                    }
                    if (isset($wsArrayToSend['mixPanelData']['riskValue'])) {
                        $mixPanelArr['Risk'] = $wsArrayToSend['mixPanelData']['riskValue'];
                    }
                    if (isset($wsArrayToSend['mixPanelData']['profileCompleteness'])) {
                        $mixPanelArr['Profile Completeness'] = $wsArrayToSend['mixPanelData']['profileCompleteness'];
                    }
                    if (isset($wsArrayToSend['mixPanelData']['collegeAmount'])) {
                        $mixPanelArr['College Balance'] = $wsArrayToSend['mixPanelData']['collegeAmount'];
                    }
                    if (isset($wsArrayToSend['mixPanelData']['lifeInsuranceNeeded'])) {
                        $mixPanelArr['Life Insurance Needed'] = $wsArrayToSend['mixPanelData']['lifeInsuranceNeeded'];
                    }
                    if (isset($wsArrayToSend['mixPanelData']['disabilityInsuranceNeeded'])) {
                        $mixPanelArr['Disability Insurance Needed'] = $wsArrayToSend['mixPanelData']['disabilityInsuranceNeeded'];
                    }
                    $mixPanelObj->people->setOnce($userUniqueHash, $mixPanelArr);
                    $mixPanelObj->track("User Logged In", array(
                        'user_logged_in' => $userUniqueHash,
                        'score' => $wsArrayToSend['mixPanelData']['currentScore']
                    ));
                }

                $user_id = Yii::app()->getSession()->get('wsuser')->id;
                $array = Yii::app()->cache->get('login' . $user_id);
                if ($array === false) {
                    $array = array();
                }
                $array[Yii::app()->session->sessionID] = date("Y-m-d H:i:s");
                Yii::app()->cache->set('login' . $user_id, $array);

                $this->sendResponse(200, CJSON::encode($wsArrayToSend));
            } else {
                // add a new unsuccessful login attempt record
                $userAccess = new UserAccess();
                $userAccess->user_id = $userDetails->id;
                $userAccess->attempt = UserAccess::FAILED;
                $userAccess->authentication = UserAccess::PIN;
                $userAccess->accesstimestamp = date("Y-m-d H:i:s");
                $userAccess->current = UserAccess::TRUE;
                $userAccess->save();

                unset($userAccess);
            }
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => 'Incorrect email/pin combination.')));
    }

    /**
     *  actionAddPin() is used by mobile devices to add a pin immediately registering.
     *  A ssession already exists so email in not required.
     */
    function actionAddPin() {
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $pin = Yii::app()->request->getParam('pin');
            if (!isset($pin) || empty($pin)) {
                $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Pin required.')));
                return;
            }

            $user_id = Yii::app()->getSession()->get('wsuser')->id;
            $userDetails = User::model()->findByPk($user_id);

            if (!isset($userDetails->pin) || $userDetails->pin == "") {
                $hasher = PasswordHasher::factory();
                $userDetails->pin = $hasher->HashPassword($pin);
                $userDetails->save();
                unset($hasher);
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => 'Pin successfully updated.')));
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => 'Pin already exists.')));
            }
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => 'Session Expired')));
        }
    }

    /**
     *  actionEditPin() is used by mobile devices to update a pin.  A password param
     *  is used for authentication.
     */
    function actionEditPin() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            return;
        }
        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $userDetails = User::model()->findByPk($user_id);

        $pin = Yii::app()->request->getParam('pin');
        if (!isset($pin) || empty($pin)) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Pin required.')));
            return;
        }

        $password = Yii::app()->request->getParam('password');
        if (!isset($password) || empty($password)) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Invalid password provided.')));
            return;
        }

        $userModel = new User();
        $userModel->email = $userDetails->email;
        $userModel->password = $password;
        $userModel->urole = User::USER_IS_CONSUMER;
        if ($userModel->authenticateIdentity()) {
            $hasher = PasswordHasher::factory();
            $userDetails->pin = $hasher->HashPassword($pin);
            $userDetails->save();
            unset($hasher);

            // when a pin is reset, archive unsuccessful pin attempts in the useraccess table.
            UserAccess::model()->updateAll(array('current' => UserAccess::FALSE),'user_id = ' . $userDetails->id .
                        ' and attempt = '.UserAccess::FAILED. ' and authentication = '.UserAccess::PIN);

            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => 'Pin successfully updated.')));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => 'Invalid password provided.')));
        }
    }

    public function actionIdemnificationcheck() {

        ##idemnification check if user is created by advisor ##
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $userIdemnificationObject = AdvisorClientRelated::model()->findBySql("SELECT indemnification_check, user_id, advisor_id,"
            . "permission FROM consumervsadvisor WHERE user_id=:user_id", array("user_id" => $user_id));
        if (count($userIdemnificationObject) == 0) {
            $this->sendResponse(200, CJSON::encode(array("status" => "Null")));
        } else {
            ##idemnification check if user is created by advisor ##
            $advisorDetails = Advisorpersonalinfo::model()->findBySql("SELECT firstname, lastname, profilepic FROM advisorpersonalinfo WHERE advisor_id=:advisorid", array("advisorid" => $userIdemnificationObject->advisor_id));
            $advisorDesignations = Designation::model()->findBySql("SELECT advisor_id, GROUP_CONCAT(desig_name) as desig_name FROM `adv_designations` WHERE advisor_id=:advisorid AND status = 1 AND deleted = 0 GROUP BY advisor_id", array("advisorid" => $userIdemnificationObject->advisor_id));
            $userAdvDetails = new stdClass();
            $userAdvDetails->indemnification_check = isset($userIdemnificationObject->indemnification_check) ? $userIdemnificationObject->indemnification_check : "0";
            $userAdvDetails->user_id = $userIdemnificationObject->user_id;
            $userAdvDetails->advisor_id = $userIdemnificationObject->advisor_id;
            $userAdvDetails->permission = $userIdemnificationObject->permission;
            if ($advisorDetails) {
                $userAdvDetails->advisor_firstname = $advisorDetails->firstname;
                $userAdvDetails->advisor_lastname = $advisorDetails->lastname;
                $userAdvDetails->advisor_profilepic = $advisorDetails->profilepic;
            }
            if (!isset($userAdvDetails->advisor_profilepic) || $userAdvDetails->advisor_profilepic == "") {
                $userAdvDetails->advisor_profilepic = "ui/images/advisor_default.png";
            }
            if ($advisorDesignations) {
                $userAdvDetails->advisor_designations = $advisorDesignations->desig_name;
                $userAdvDetails->advisor_designations = str_replace(",", ", ", $userAdvDetails->advisor_designations);
            }
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'data' => $userAdvDetails)));
        }
    }

    public function actionSendverificationemail() {
        // Get the user ID
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $userObject = User::model()->findByPk($user_id);

        if (!$userObject || $userObject->isactive != User::USER_IS_ACTIVE) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'type' => 'email',
                'message' => 'Error: the user does not exist.')));
        } elseif ($userObject->verified == 1) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'type' => 'email',
                'message' => 'Error: the user has already been verified.')));
        }

        $verificationexpiration = new DateTime($userObject->verificationexpiration);
        $today = new DateTime();
        if($verificationexpiration < $today) {
            $passwordGenerator = PasswordGenerator::factory();
            $randomPassword = $passwordGenerator->generate();
            unset($passwordGenerator);
            $hasher = PasswordHasher::factory();
            $userObject->verificationcode = $hasher->HashPassword($randomPassword);
            unset($hasher);
            $userObject->verificationexpiration = new CDbExpression('NOW() + INTERVAL 3 DAY');
            $userObject->save();
        }
        //data is valid and is successfully inserted/updated
        //send the email for verification
        $part = 'email-verification';
        $email = new Email();
        $email->subject = 'Please verify your email address';
        $email->recipient['email'] = $userObject->email;
        $email->recipient['name'] = "{$userObject->firstname} {$userObject->lastname}";
        $email->data[$part] = [
            'code' => $userObject->verificationcode
        ];
        $email->send();
        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'type' => 'email',
            'message' => 'A verification email has been sent successfully to ' . $userObject->email .
            '. Please log in to your email account and follow the simple directions to quickly complete the verification process.')));
    }

    /**
     * Used in sign up for the new user
     */
    public function actionSignup() {

        $email = Yii::app()->request->getParam('email');
        $password = Yii::app()->request->getParam('password');
        $pin = Yii::app()->request->getParam('pin');

        if (!isset($email) || empty($email) || !isset($password) || empty($password)) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'type' => 'email', 'message' => 'Mandatory fields left blank.')));
            return;
        }

        /* Check password requirements, including 8 or more characters, at least 1 number,
         * at least 1 symbol, at least upper and one lower case letter */
        $passwordErrorExists = false;
        if (preg_match('/\s/', $password ) || strlen($password) < 8) {
            $passwordErrorExists = true;
        }
        else if (!preg_match('@[a-z]@', $password)) {
            $passwordErrorExists = true;
        }
        else if (!preg_match('@[A-Z]@', $password)) {
            $passwordErrorExists = true;
        }
        else if (!preg_match('@[0-9]@', $password)) {
            $passwordErrorExists = true;
        }
        else if (!preg_match('@[\W]@', $password)) {
            $passwordErrorExists = true;
        }
        if ($passwordErrorExists == true) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => "Passwords must be at least eight characters in length with no spaces and include at least one uppercase letter, lowercase letter, symbol, and number.")));
        }

        $userObject = User::model()->find(array('condition' => 'email = :email', 'params' => array(':email' => $email)));
        if ($userObject && $userObject->isactive != User::USER_IS_INACTIVE) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'type' => 'email', 'message' => 'A user already exists with this email address.')));
            return;
        }
        if (!isset($userObject)) {
            $userObject = new User();
        }

        $hasher = PasswordHasher::factory();
        $userObject->password = $hasher->HashPassword($password);
        unset($hasher);
        $userObject->email = $email;
        if (isset($pin) && !empty($pin)) {
            $pinhasher = PasswordHasher::factory();
            $userObject->pin = $pinhasher->HashPassword($pin);
            unset($pinhasher);
        }
        $userObject->roleid = 888;
        $userObject->isactive = User::USER_IS_ACTIVE;

        // create a random verification code
        $passwordGenerator = PasswordGenerator::factory();
        $randomPassword = $passwordGenerator->generate();
        unset($passwordGenerator);
        $hasher = PasswordHasher::factory();
        $userObject->verificationcode = $hasher->HashPassword($randomPassword);
        unset($hasher);

        // create a random unsubscribe code
        $unsubscribecodeGenerator = PasswordGenerator::factory();
        $randomUnsubscribecode = $unsubscribecodeGenerator->generate();
        unset($unsubscribecodeGenerator);
        $codehasher = PasswordHasher::factory();
        $userObject->unsubscribecode = $codehasher->HashPassword($randomUnsubscribecode);
        unset($codehasher);

        $userObject->requestinvitetokenkey = 'SUCCESS';
        $userObject->verificationexpiration = new CDbExpression('NOW() + INTERVAL 3 DAY');

        //save the values to database
        if ($userObject->save()) {
            Yii::app()->getSession()->remove('wsadvisor');
            //data is valid and is successfully inserted/updated
            //send the email for verification
            $part = 'user-registration';
            $welcomeEmail = new Email();
            $welcomeEmail->subject = 'Welcome to FlexScore';
            $welcomeEmail->recipient['email'] = $userObject->email;
            $welcomeEmail->data[$part] = [
                'code' => $userObject->verificationcode,
            ];
            $welcomeEmail->send();

            $userObject->update("user_id = :user_id", array(':user_id' => $userObject->id));

            $wsuser = new stdClass();
            $wsuser->id = $userObject->id;
            $wsuser->email = $userObject->email;
            $wsuser->roleid = $userObject->roleid;
            $wsuser->firstname = $userObject->firstname;
            $wsuser->lastname = $userObject->lastname;

            Yii::app()->getSession()->add('wsuser', $wsuser);
            $notiCount = Notification::model()->count("user_id=:user_id and status=0", array("user_id" => $userObject->id));
            $wsArrayToSend = array(
                "status" => 'OK',
                "id" => Yii::app()->getSession()->get('wsuser')->id,
                "email" => Yii::app()->getSession()->get('wsuser')->email,
                "urole" => Yii::app()->getSession()->get('wsuser')->roleid,
                "sess" => Yii::app()->session->sessionID,
                "notification" => $notiCount,
                "type" => 'signup',
                "uniquehash" => md5(Yii::app()->getSession()->get('wsuser')->email),
            );
            //add as default row in user profile
            $userPerDetails = Userpersonalinfo::model()->find("user_id = :user_id", array(':user_id' => $wsuser->id));
            if (!$userPerDetails) {
                $userPerDetails = new Userpersonalinfo();
                $userPerDetails->user_id = $wsuser->id;
            }

            $userPerDetails->retirementstatus = 0;
            $userPerDetails->retirementage = 65;
            $userPerDetails->maritalstatus = 'Single';
            $userPerDetails->noofchildren = 0;

            if (!$userPerDetails->save()) {

            }
            //To create a default Goal
            $SControllerObj = new Scontroller(1);
            $SControllerObj->setEngine();
            $SControllerObj->setupDefaultRetirementGoal();
            $SControllerObj->calculateScore("GOAL", $wsuser->id);

            $ASObj = new ActionstepController(1);
            $ASObj->updateComponents();

            $UserControllerObj = new UserController(1);
            $UserControllerObj->setSubscriptionStatus("Subscribe");

            /** Start - mixpanel tracking - for mobile app * */
            $jsMixpanelCall = Yii::app()->request->getParam('jsMixpanelCall');
            if (!isset($jsMixpanelCall)) {
                $mixPanelClientObj = new MixPanelClient();
                if ($mixPanelClientObj->status == 'active') {
                    $mixPanelObj = Mixpanel::getInstance($mixPanelClientObj->token);
                    $mixPanelObj->identify($wsArrayToSend['uniquehash']);
                    $mixPanelObj->people->setOnce($wsArrayToSend['uniquehash'], array('First Login Date' => date('Y-m-d H:i:s')));
                    $mixPanelObj->track("New User", array(
                        'new_user' => $wsArrayToSend['uniquehash'],
                        'Created By' => 'user'
                    ));
                }
            }
            /** End - mixpanel tracking - for mobile app * */
            $this->sendResponse(200, CJSON::encode($wsArrayToSend));
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'type' => 'email', 'message' => 'This user can not be added at this time. Please try again later.')));
        }
        unset($userObject);
    }

    /**
     * Used in User verification account
     */
    public function actionUserverify() {
        if (isset($_GET['code']) && $_GET['code'] != "") {

            $verificationCode = $_GET['code'];

            $userObject = new User();
            $userDetails = $userObject->find('verificationcode = :vcode AND verificationexpiration > NOW()', array(':vcode' => $verificationCode));
            //save the values to database
            if (isset($userDetails)) {
                // data is valid activate the user
                $userDetails->isactive = 1;
                $userDetails->verified = 1;
                $userDetails->save();
                $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => 'User verified sucessfully')));
            } else {
                // data is invalid. call getErrors() to retrieve error messages
                $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Unable to verify user')));
            }
            unset($userObject);
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Verification code is required')));
        }
    }

    /**
     * check login before login
     */
    public function actionChecklogin() {

        if (isset($_GET["sess"]) && Yii::app()->getSession()->sessionID == $_GET["sess"]) {
            $wsArrayToSend = array(
                "status" => 'ERROR'
            );

            if (Yii::app()->getSession()->get('wsuser')) {
                $connectAccountstatus = '';
                $insurancestatus = '';
                $debtstatus = '';
                /* create folder with user_id hash value and move profile pic in new folder from old folder if profile pic is there */
                // create the hash for user_id //
                $user_id = Yii::app()->getSession()->get('wsuser')->id;
                $hasher = PasswordHasher::factory();
                $userHashObj = User::model()->find(array('condition' => "id = :user_id", 'params' => array("user_id" => $user_id), 'select' => 'uidhashvalue'));
                if ($userHashObj) {
                    if ($userHashObj->uidhashvalue == "") {
                        $userHashObj->uidhashvalue = str_replace("/", "", $hasher->HashPassword($user_id));
                        User::model()->updateByPk($user_id, array('uidhashvalue' => $userHashObj->uidhashvalue));
                        $uidhashvalue = $userHashObj->uidhashvalue;
                    } else {
                        $uidhashvalue = $userHashObj->uidhashvalue;
                    }
                }
                unset($hasher);
                $folderPath = realpath(dirname(__FILE__) . '/../..');
                if (is_dir($folderPath . '/ui/usercontent/user/' . $user_id . '/')) {
                    rename($folderPath . '/ui/usercontent/user/' . $user_id, $folderPath . '/ui/usercontent/user/' . $uidhashvalue);
                } else if (!is_dir($folderPath . '/ui/usercontent/user/' . $user_id . '/') && !is_dir($folderPath . '/ui/usercontent/user/' . $uidhashvalue . '/')) {
                    mkdir($folderPath . '/ui/usercontent/user/' . $uidhashvalue . '/');
                }
                //check user has pic or not if has then move to new location //
                $userPicDetails = Userpersonalinfo::model()->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $user_id), 'select' => 'userpic'));
                if ($userPicDetails && $userPicDetails->userpic != "") {
                    $userpicOldPathArray = explode("/", $userPicDetails->userpic);
                    $userpicName = $userpicOldPathArray[3];
                    $userPicNewPath = 'ui/usercontent/user/' . $uidhashvalue . '/' . $userpicName;
                    if (is_file($folderPath . "/" . $userPicDetails->userpic) && $userpicOldPathArray[2] == 'uploadedPic') {
                        copy($folderPath . "/" . $userPicDetails->userpic, $folderPath . "/" . $userPicNewPath);
                        Userpersonalinfo::model()->updateByPk($user_id, array('userpic' => $userPicNewPath));
                    }
                }
                $userInfo = User::model()->findBySql("SELECT verified, datediff(now(),createdtimestamp) as createdtimestamp FROM user WHERE id=:user_id", array("user_id" => Yii::app()->getSession()->get('wsuser')->id));
                $echoUser = EchoUser::model()->count("email=:email and permission='0'", array("email" => Yii::app()->getSession()->get('wsuser')->email));
                $notiCount = Notification::model()->count("user_id=:user_id and status=0", array("user_id" => Yii::app()->getSession()->get('wsuser')->id));
                //Ganesh Modified the below query from Select * to as below
                $profile = Userpersonalinfo::model()->findBySql("SELECT retirementstatus,userpic, connectAccountPreference, debtsPreference, insurancePreference  FROM userpersonalinfo WHERE user_id=:user_id", array("user_id" => Yii::app()->getSession()->get('wsuser')->id));
                $retirementstatus = 0;
                $profilePic = '';
                if ($profile) {
                    $retirementstatus = $profile->retirementstatus;
                    $profilePic = $profile->userpic;
                    $connectAccountstatus = $profile->connectAccountPreference;
                    $debtstatus = $profile->debtsPreference;
                    $insurancestatus = $profile->insurancePreference;
                }
                $userDetails = array(
                    "id" => Yii::app()->getSession()->get('wsuser')->id,
                    "email" => Yii::app()->getSession()->get('wsuser')->email,
                    "firstname" => Yii::app()->getSession()->get('wsuser')->firstname,
                    "lastname" => Yii::app()->getSession()->get('wsuser')->lastname,
                    "urole" => Yii::app()->getSession()->get('wsuser')->roleid,
                    "sess" => Yii::app()->session->sessionID,
                    "retirementstatus" => $retirementstatus,
                    "connectAccountstatus" => $connectAccountstatus,
                    "debtstatus" => $debtstatus,
                    "insurancestatus" => $insurancestatus,
                    "notification" => $notiCount,
                    "type" => 'check',
                    "uniquehash" => md5(Yii::app()->getSession()->get('wsuser')->email),
                    "image" => $profilePic,
                    "daysofcreation" => $userInfo->createdtimestamp,
                    "verified" => $userInfo->verified,
                    "echoUserCount" => $echoUser
                );
                $wsArrayToSend["status"] = 'OK';
                $wsArrayToSend["user"] = $userDetails;
            }
            if (Yii::app()->getSession()->get('wsadvisor')) {

                /* create folder with advisor_id hash value and move profile pic in new folder from old folder if profile pic is there */
                // create the hash for advisor_id //
                $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
                $hasher = PasswordHasher::factory();
                $advisorHashObj = Advisor::model()->find(array('condition' => "id = :advisor_id", 'params' => array("advisor_id" => $advisorId), 'select' => 'advidhashvalue'));
                if ($advisorHashObj) {
                    if ($advisorHashObj->advidhashvalue == "") {
                        $advisorHashObj->advidhashvalue = str_replace("/", "", $hasher->HashPassword($advisorId));
                        Advisor::model()->updateByPk($advisorId, array('advidhashvalue' => $advisorHashObj->advidhashvalue));
                        $advidhashvalue = $advisorHashObj->advidhashvalue;
                    } else {
                        $advidhashvalue = $advisorHashObj->advidhashvalue;
                    }
                }
                unset($hasher);
                $folderPath = realpath(dirname(__FILE__) . '/../..');
                if (is_dir($folderPath . '/ui/usercontent/advisor/' . $advisorId . '/')) {
                    rename($folderPath . '/ui/usercontent/advisor/' . $advisorId, $folderPath . '/ui/usercontent/advisor/' . $advidhashvalue);
                } else if (!is_dir($folderPath . '/ui/usercontent/advisor/' . $advisorId . '/') && !is_dir($folderPath . '/ui/usercontent/advisor/' . $advidhashvalue . '/')) {
                    mkdir($folderPath . '/ui/usercontent/advisor/' . $advidhashvalue . '/');
                }
                //check advisor has pic or not if has then move to new location //
                $advPicDetails = Advisorpersonalinfo::model()->find(array('condition' => "advisor_id = :advisor_id", 'params' => array("advisor_id" => $advisorId), 'select' => 'profilepic'));
                if ($advPicDetails && $advPicDetails->profilepic != "") {
                    $advpicOldPathArray = explode("/", $advPicDetails->profilepic);
                    $advpicName = $advpicOldPathArray[3];
                    $advPicNewPath = 'ui/usercontent/advisor/' . $advidhashvalue . '/' . $advpicName;
                    if (is_file($folderPath . "/" . $advPicDetails->profilepic) && $advpicOldPathArray[2] == 'uploadedPic') {
                        copy($folderPath . "/" . $advPicDetails->profilepic, $folderPath . "/" . $advPicNewPath);
                        Advisorpersonalinfo::model()->updateByPk($advisorId, array('profilepic' => $advPicNewPath));
                    }
                }

                $notiCount = AdvisorNotification::model()->count("advisor_id=:advisor_id and status=0", array("advisor_id" => Yii::app()->getSession()->get('wsadvisor')->id));
                $advisorDetails = array(
                    "id" => Yii::app()->getSession()->get('wsadvisor')->id,
                    "email" => Yii::app()->getSession()->get('wsadvisor')->email,
                    "firstname" => Yii::app()->getSession()->get('wsadvisor')->firstname,
                    "lastname" => Yii::app()->getSession()->get('wsadvisor')->lastname,
                    "urole" => Yii::app()->getSession()->get('wsadvisor')->roleid,
                    "sess" => Yii::app()->session->sessionID,
                    "notification" => $notiCount,
                    "type" => 'check',
                    "uniquehash" => md5(Yii::app()->getSession()->get('wsadvisor')->email),
                );
                $wsArrayToSend["status"] = 'OK';
                $wsArrayToSend["advisor"] = $advisorDetails;

                $advisorInfo = Advisor::model()->find("id=:advisor_id", array("advisor_id" => Yii::app()->getSession()->get('wsadvisor')->id));
                if ($advisorInfo) {
                    $earlyBirdEndDate = new DateTime("2015-01-01 00:00:00");
                    $createdDate = new DateTime($advisorInfo->createdtimestamp);
                    if ($createdDate < $earlyBirdEndDate) {
                        $time = 60;
                    } else {
                        $time = 7;
                    }
                    $startTime = strtotime(date($advisorInfo->createdtimestamp));
                    $trialEndString = date("D M d Y H:i:s", strtotime("+" . $time . " day", $startTime)) . " UTC";
                    $today = new DateTime();
                    $trialEndDate = new DateTime($trialEndString);
                    $wsArrayToSend["advisor"]["trialend"] = $trialEndString;
                    $wsArrayToSend["advisor"]["profilepic"] = $advPicDetails->profilepic;
                }
            }
            if (Yii::app()->getSession()->get('wsadvisor') != null && Yii::app()->getSession()->get('wsuser') != null) {
                $advisorPermission = AdvisorClientRelated::model()->findBySql("SELECT permission FROM consumervsadvisor WHERE user_id=:user_id AND advisor_id=:advisor_id", array("user_id" => Yii::app()->getSession()->get('wsuser')->id, "advisor_id" => Yii::app()->getSession()->get('wsadvisor')->id));
                $wsArrayToSend["permission"] = $advisorPermission['permission'];
            }
            if ($wsArrayToSend["status"] == "ERROR") {
                $this->sendResponse(200, CJSON::encode(array('message' => 'Session Expired', 'status' => 'ERROR')));
            }
        } else {
            $this->sendResponse(200, CJSON::encode(array('message' => 'Session Expired', 'status' => 'ERROR')));
        }
        $this->sendResponse(200, CJSON::encode($wsArrayToSend));
    }

/*    public function actionCheckemail() {  // Remove if not being used

        if (isset($_GET["email"])) {
            //Ganesh changed the below query from Select * to as below
            $user = User::model()->findBySql("SELECT email FROM user WHERE email=:email", array("email" => $_GET["email"]));
            if ($user) {
                $wsArrayToSend = array(
                    "status" => 'OK',
                );
                $this->sendResponse(200, CJSON::encode($wsArrayToSend));
            } else {
                $this->sendResponse(200, CJSON::encode(array('message' => 'Email address does not exist.', 'status' => 'ERROR')));
            }
        } else {
            $this->sendResponse(200, CJSON::encode(array('message' => 'Email address does not exist.', 'status' => 'ERROR')));
        }
    }
*/


    /**
     * actionResetVerify is the landing page for users when they click the "Change Your Password"
     * button in the email received after a change password request.
     */
    public function actionResetVerify() {
        if (isset($_GET['token']) && $_GET['token'] != "") {

            $verificationToken = $_GET['token'];

            $userObject = new User();
            $userDetails = $userObject->find('resetpasswordcode = :vtoken AND resetpasswordexpiration > NOW()', array(':vtoken' => $verificationToken));
            //save the values to database
            if (isset($userDetails)) {
                $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => 'Valid token')));
            } else {
                // data is invalid. call getErrors() to retrieve error messages
                $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Invalid token')));
            }
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Verification token is required')));
        }
    }


    /**
     * actionResetVerifyAdvisor is the landing page for advisors when they click the "Change Your Password"
     * button in the email received after a change password request.
     */
    public function actionResetVerifyAdvisor() {
        if (isset($_GET['token']) && $_GET['token'] != "") {

            $verificationToken = $_GET['token'];

            $userObject = new Advisor();
            $userDetails = $userObject->find('resetpasswordcode = :vtoken AND resetpasswordexpiration > NOW()', array(':vtoken' => $verificationToken));
            //save the values to database
            if (isset($userDetails)) {
                $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => 'Valid token')));
            } else {
                // data is invalid. call getErrors() to retrieve error messages
                $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Invalid token')));
            }
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Verification token is required')));
        }
    }



    /**
     * check login before refresh queue
     */
    public function actionRefreshall() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        if (Yii::app()->getSession()->get('wsuser')) {
            $wsArrayToSend = array(
                "status" => 'OK',
                "uid" => Yii::app()->getSession()->get('wsuser')->id,
                "email" => Yii::app()->getSession()->get('wsuser')->email
            );
            $this->sendResponse(200, CJSON::encode($wsArrayToSend));
        }
    }

    public function actionRabbitmqsend() {
        //call MQ to pass the job if user is logged in

        $queueClientObject = Yii::app()->wordpressclient;
        $queueClientObject->getPosts();
    }

    public function actionRabbitmqreceive() {
        //call MQ to pass the job if user is logged in
        $queueClientObject = Yii::app()->queueclient;
        $queueClientObject->receive();
    }

    /**
     *
     */
    public function actionLogout() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $uid = Yii::app()->getSession()->get('wsuser')->id;
            $array = Yii::app()->cache->get('logout' . $uid);
            if ($array === false) {
                $array = array();
            }
            $array[Yii::app()->session->sessionID] = date("Y-m-d H:i:s");
            Yii::app()->cache->set('logout' . $uid, $array);
        }
        unset(Yii::app()->session["sengine"]);
        Yii::app()->user->logout();
        Yii::app()->getSession()->remove('wsuser');
        Yii::app()->getSession()->remove('wsadvisor');

        unset($_SESSION);  //need to replace with YII app function
        setcookie("PHPSESSID", "", time() - 3600);
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "success" => 'User logged out Successfully.')));
    }

    /**
     *
     */
    function actionDeactivateUser($user_id = 0) {
        if (!($user_id)) {
            #$user_id = Yii::app()->getSession()->get('wsuser')->id;
            $user_id = $_POST['uid'];
        }
        $userInfo = User::model()->find("id=:user_id", array("user_id" => $user_id));
        if ($userInfo->isactive == 0) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => 'User already inactive')));
        } else {

            $userInfo->isactive = 0;
            if ($userInfo->save()) {
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "success" => 'User successfully deactivated')));
            }
        }
    }

    /**
     * actionChangePwd is a form in the Settings menu that is used by both Users and Advisors
     */
    function actionChangepwd() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) && !isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired.")));
        }

        $oldpassword = Yii::app()->request->getParam('oldpassword');
        $password1 = Yii::app()->request->getParam('password1');
        $password2 = Yii::app()->request->getParam('password2');

        if (!isset($oldpassword) || empty($oldpassword) || !isset($password1) || empty($password1) || !isset($password2) || empty($password2)) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Mandatory fields left blank.')));
            return;
        }

        /* Check that passwords match */
        if ($password1 != $password2) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => 'The passwords you entered don\'t match.')));
        }

        /* Check password requirements, including 8 or more characters, at least 1 number,
         * at least 1 symbol, at least upper and one lower case letter */
        $passwordErrorExists = false;
        if (preg_match('/\s/', $password1 ) || strlen($password1) < 8) {
            $passwordErrorExists = true;
        }
        else if (!preg_match('@[a-z]@', $password1)) {
            $passwordErrorExists = true;
        }
        else if (!preg_match('@[A-Z]@', $password1)) {
            $passwordErrorExists = true;
        }
        else if (!preg_match('@[0-9]@', $password1)) {
            $passwordErrorExists = true;
        }
        else if (!preg_match('@[\W]@', $password1)) {
            $passwordErrorExists = true;
        }
        if ($passwordErrorExists == true) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => "Passwords must be at least eight characters in length with no spaces and include at least one uppercase letter, lowercase letter, symbol, and number.")));
        }

        // if the user is a Client
        if (Yii::app()->getSession()->get('wsadvisor')) {
            $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
            $advisorDetails = Advisor::model()->find("id=:advisor_id", array("advisor_id" => $advisorId));
            if ($advisorDetails) {
                $advisorModel = new Advisor();
                $advisorModel->email = $advisorDetails->email;
                $advisorModel->password = $oldpassword;
                $advisorModel->urole = Advisor::USER_IS_ADVISOR;
                if ($advisorModel->authenticateIdentity()) {
                    if ($oldpassword == $password1) {
                        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "type" => "password1", "message" => 'Please enter a password you haven\'t previously <br /> used with this account.')));
                    } else {
                        $hasher = PasswordHasher::factory();
                        $advisorDetails->password = $hasher->HashPassword($password1);
                        $advisorDetails->save();
                        unset($hasher);
                        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "success" => 'Password was successfully changed.')));
                    }
                } else {
                    $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => 'The password you entered is invalid.')));
                }
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => 'User does not exist.')));
            }
        } elseif (Yii::app()->getSession()->get('wsuser')) {
            $userId = Yii::app()->getSession()->get('wsuser')->id;
            $userDetails = User::model()->find("id=:user_id", array("user_id" => $userId));
            if ($userDetails) {
                $userModel = new User();
                $userModel->email = $userDetails->email;
                $userModel->password = $oldpassword;
                $userModel->urole = User::USER_IS_CONSUMER;
                if ($userModel->authenticateIdentity()) {
                    if ($oldpassword == $password1) {
                        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "type" => "password1", "message" => 'Please enter a password you haven\'t previously <br /> used with this account.')));
                    } else {
                        $hasher = PasswordHasher::factory();
                        $userDetails->password = $hasher->HashPassword($password1);
                        $userDetails->save();
                        unset($hasher);
                        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "success" => 'Password was successfully changed.')));
                    }
                } else {
                    $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => 'The password you entered is invalid.')));
                }
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => 'User does not exist.')));
            }
            // if the user is an Advisor
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => 'Session Expired.')));
        }
    }

    /**
     * Checks registered Email address is available
     * Parameters: Email Address
     * Result: success message / Failure Message
     * Business Logic: Sends email on success of valid registered email address
     * Owner: Vinoth
     */

    /**
     * based on user role id, get user activities from meta_user_info table
     *
     */
    public function actionlistroleactivities() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) && !isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        if (Yii::app()->getSession()->get('wsadvisor')) {
            $roleid = Yii::app()->getSession()->get('wsadvisor')->roleid;
        } else {
            $roleid = Yii::app()->getSession()->get('wsuser')->roleid;
        }


        //$sql = "select activitykey, activityvalue from roleactivities WHERE roleid=:roleid";
        //$getUserMetaInfo = Roleactivities::model()->findAllBySql($sql, array("roleid" => $roleid));
        // cache lifeexpectancy table
        $roleDependency = new CDbCacheDependency('select activitykey, activityvalue from roleactivities');
        $getUserMetaInfo = Roleactivities::model()->cache(QUERY_CACHE_TIMEOUT, $roleDependency)->findAll('roleid = ' . $roleid);

        if (isset($getUserMetaInfo)) {

            foreach ($getUserMetaInfo as $useractvitykey => $useractivityvalue) {
                $activityArray[$useractivityvalue->activitykey] = $useractivityvalue->activityvalue;
            }
            $activityArray['status'] = 'OK';

            $this->sendResponse(200, CJSON::encode($activityArray));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => 'No activities defined for this role.')));
        }
    }

    public function actionresetpassword() {
        $postEmail = $_POST['email'];

        if (!empty($postEmail)) {

            $user = User::model()->findBySql("SELECT * FROM user WHERE email=:email and isactive='1'", array("email" => $postEmail));
            if ($user) {

                // Generate a token which validates during updation password
                $passwordGenerator = PasswordGenerator::factory();
                $randomPassword = $passwordGenerator->generate();
                unset($passwordGenerator);
                $hasher = PasswordHasher::factory();
                $user->resetpasswordcode = $hasher->HashPassword($randomPassword);
                $user->resetpasswordexpiration = new CDbExpression('NOW() + INTERVAL 1 DAY');

                $user->save();

                $name = $user->firstname;
                if (!$name) {
                    $name = $postEmail;
                }

                $appUrl = Yii::app()->params->applicationUrl;

                $part = 'user-password-reset-request';
                $email = new Email();
                $email->subject = 'Reset your FlexScore Password';
                $email->recipient['email'] = $postEmail;
                $email->data[$part] = [
                    'name' => $name,
                    'token' => $user->resetpasswordcode,
                ];
                $email->send();
            }
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'message' => 'Please check your email.')));
    }

    public function actionadvisorresetpassword() {
        $postEmail = $_POST['email'];

        if (!empty($postEmail)) {

            $user = Advisor::model()->findBySql("SELECT * FROM advisor WHERE email=:email and isactive='1'", array("email" => $postEmail));
            if ($user) {

                // Generate a token which validates during updation password
                $passwordGenerator = PasswordGenerator::factory();
                $randomPassword = $passwordGenerator->generate();
                unset($passwordGenerator);
                $hasher = PasswordHasher::factory();
                $user->resetpasswordcode = $hasher->HashPassword($randomPassword);
                $user->resetpasswordexpiration = new CDbExpression('NOW() + INTERVAL 1 DAY');
                unset($hasher);
                $user->save();

                $name = $user->firstname;
                if (!$name) {
                    $name = $postEmail;
                }

                $appUrl = Yii::app()->params->applicationUrl;

                $part = 'advisor-password-reset-request';
                $email = new Email();
                $email->subject = 'Reset your FlexScore Password';
                $email->recipient['email'] = $postEmail;
                $email->data[$part] = [
                    'name' => $name,
                    'token' => $user->resetpasswordcode,
                ];
                $email->send();

                $wsArrayToSend = array(
                    "status" => 'OK',
                    "message" => 'Please check your Email',
                );
                $this->sendResponse(200, CJSON::encode($wsArrayToSend));
            } else {
                $this->sendResponse(200, CJSON::encode(array('message' => 'Email address does not exist.', 'status' => 'ERROR')));
            }
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => 'Empty string not accepted.')));
        }
    }

    /**
     * actionUpdatePassword is used from the Forgot Password form.  It sends
     * an email to the user that contains a token that is used to set a new password.
     */
    public function actionUpdatepassword() {
        $password1 = Yii::app()->request->getParam('password');
        $password2 = Yii::app()->request->getParam('cpassword');
        $token = Yii::app()->request->getParam('token');

        if (!isset($password1) || empty($password1) || !isset($password2) || empty($password2) || !isset($token) || empty($token)) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Mandatory fields left blank.')));
            return;
        }

        /* Check that passwords match */
        if ($password1 != $password2) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => 'The passwords you entered don\'t match.')));
        }

        /* Check password requirements, including 8 or more characters, at least 1 number,
         * at least 1 symbol, at least upper and one lower case letter */
        $passwordErrorExists = false;
        if (preg_match('/\s/', $password1 ) || strlen($password1) < 8) {
            $passwordErrorExists = true;
        }
        else if (!preg_match('@[a-z]@', $password1)) {
            $passwordErrorExists = true;
        }
        else if (!preg_match('@[A-Z]@', $password1)) {
            $passwordErrorExists = true;
        }
        else if (!preg_match('@[0-9]@', $password1)) {
            $passwordErrorExists = true;
        }
        else if (!preg_match('@[\W]@', $password1)) {
            $passwordErrorExists = true;
        }
        if ($passwordErrorExists == true) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => "Passwords must be at least eight characters in length with no spaces and include at least one uppercase letter, lowercase letter, symbol, and number.")));
        }

        $userDetails = User::model()->find(array('condition' => 'isactive="1" AND resetpasswordcode=:token AND resetpasswordexpiration > NOW()',
            'params' => array("token" => $token)));
        if ($userDetails) {
            //update New password
            $hasher = PasswordHasher::factory();
            $userDetails->password = $hasher->HashPassword($password1);
            $userDetails->resetpasswordcode = '';
            $userDetails->resetpasswordexpiration = '0000-00-00 00:00:00';
            $userDetails->verified = 1;
            $userDetails->passwordupdated = 0;
            $userDetails->save();
            unset($hasher);

            // when a password is reset, archive unsuccessful password attempts in the useraccess table.
            UserAccess::model()->updateAll(array('current' => UserAccess::FALSE),'user_id = ' . $userDetails->id .
                        ' and attempt = '.UserAccess::FAILED. ' and authentication = '.UserAccess::PASSWORD);

            $this->sendResponse(200, CJSON::encode(array("status" => 'OK', "message" => 'Your password has been successfully changed. Sign in to continue.')));
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => 'This link has expired. Please request a new verification link.')));
    }

    /**
     *
     * @param type $email = Email Address
     * @param type $newpass = New password from the user
     */
    public function actionAdvisorupdatepassword() {
        $password1 = Yii::app()->request->getParam('password');
        $password2 = Yii::app()->request->getParam('cpassword');
        $token = Yii::app()->request->getParam('token');

        if (!isset($password1) || empty($password1) || !isset($password2) || empty($password2) || !isset($token) || empty($token)) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Mandatory fields left blank.')));
            return;
        }

        /* Check that passwords match */
        if ($password1 != $password2) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => 'The passwords you entered don\'t match.')));
        }

        /* Check password requirements, including 8 or more characters, at least 1 number,
         * at least 1 symbol, at least upper and one lower case letter */
        $passwordErrorExists = false;
        if (preg_match('/\s/', $password1 ) || strlen($password1) < 8) {
            $passwordErrorExists = true;
        }
        else if (!preg_match('@[a-z]@', $password1)) {
            $passwordErrorExists = true;
        }
        else if (!preg_match('@[A-Z]@', $password1)) {
            $passwordErrorExists = true;
        }
        else if (!preg_match('@[0-9]@', $password1)) {
            $passwordErrorExists = true;
        }
        else if (!preg_match('@[\W]@', $password1)) {
            $passwordErrorExists = true;
        }
        if ($passwordErrorExists == true) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => "Passwords must be at least eight characters in length with no spaces and include at least one uppercase letter, lowercase letter, symbol, and number.")));
        }

        $advisorDetails = Advisor::model()->find(array('condition' => 'isactive="1" AND resetpasswordcode=:token AND resetpasswordexpiration > NOW()',
                'params' => array("token" => $token)));
        if ($advisorDetails) {
            //update New password
            $hasher = PasswordHasher::factory();
            $advisorDetails->password = $hasher->HashPassword($password1);
            $advisorDetails->passwordupdated = 0;
            $advisorDetails->resetpasswordcode = '';
            $advisorDetails->resetpasswordexpiration = '0000-00-00 00:00:00';
            $advisorDetails->verified = 1;
            $advisorDetails->save();
            unset($hasher);

            // when a password is successfully eset, archive unsuccessful attempts in the useraccess table.
            AdvisorAccess::model()->updateAll(array('current' => AdvisorAccess::FALSE),'advisor_id = ' . $advisorDetails->id . ' and attempt = '.AdvisorAccess::FALSE);

            $this->sendResponse(200, CJSON::encode(array("status" => 'OK', "message" => 'Your password has been successfully changed. Sign in to continue.')));
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => 'This link has expired. Please request a new verification link.')));
    }




    /* "Deactivate User"
      - Soft Delete User
      - CE Delete User
      - Leave all other tables alone to keep status of user's data at time of delete
     */

    function actionDeleteuseraccount() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id) && !isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $array = array();
        // if the user is an advisor, delete the advisorvconsumer associative table records,
        // cancel any subscription, and set the advisor to inactive.
        if (Yii::app()->getSession()->get('wsadvisor')) {
            $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;

            if ($advisorId) {
                $advisor = Advisor::model()->find("id=:advisor_id", array("advisor_id" => $advisorId));
                if ($advisor) {
                    $advisor->isactive = User::USER_IS_DISABLED;
                    $advisor->save();
                }

                $consumerVsAdvisor = ConsumerVsAdvisor::model()->deleteAll(array('condition' => "advisor_id=:advisor_id",
                    'params' => array("advisor_id" => $advisorId)));

                $advisorSubscription = AdvisorSubscription::model()->find(array('condition' => "advisor_id=:advisor_id",
                    'params' => array("advisor_id" => $advisorId)));

                if ($advisorSubscription) {
                    $advisorSubscription = new AdvisorSubscription();
                    $subscriptionResponse = $advisorSubscription->CancelAdvisorSubscription($advisorId);
                }
                unset(Yii::app()->session["sengine"]);
            }

            // if the user is a user, delete any cashedge accounts and set the advisor to inactive.
        } else if (isset(Yii::app()->getSession()->get('wsuser')->id)) {

            $userId = Yii::app()->getSession()->get('wsuser')->id;
            if ($userId) {
                $user = User::model()->find("id=:user_id", array("user_id" => $userId));
                if (isset($user)) {
                    $user->isactive = User::USER_IS_DISABLED;
                    $user->save();
                }
                $cashedgeObject = new CashedgeAccount();
                $cashedgeAcc = $cashedgeObject->count("user_id=:user_id", array("user_id" => $userId));
                // - CE Delete User
                if ($cashedgeAcc > 0) {
                    $obj = new CashedgeController(1);
                    try {
                        $obj->CEdeleteUser($userId);
                    } catch (Exception $e) {

                    }
                }
                $array = Yii::app()->cache->get('logout' . $userId);
                if ($array === false) {
                    $array = array();
                }
                $array[Yii::app()->session->sessionID] = date("Y-m-d H:i:s");
                Yii::app()->cache->set('logout' . $userId, $array);
            }
            unset(Yii::app()->session["sengine"]);
            Yii::app()->getSession()->remove('wsuser');
        }
        Yii::app()->user->logout();
        unset($_SESSION);  //need to replace with YII app function
        setcookie("PHPSESSID", "", time() - 3600);
        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => 'Your FlexScore account has been removed.')));
    }

}

?>
