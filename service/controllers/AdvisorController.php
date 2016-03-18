<?php

/* * ********************************************************************
 * Filename: AdvisorController.php
 * Folder: controllers
 * Description: Advisor Related controller action class
 * @author Vijay Gautam (For chetu Inc)
 * @copyright (c) 2013 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */
//for email sending for verification email
require_once(realpath(dirname(__FILE__) . '/../helpers/email/Email.php'));
require_once(realpath(dirname(__FILE__) . '/../lib/stripe/Stripe.php'));

class AdvisorController extends Controller {

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
        // $this->render('index');
        echo "No service here";
    }

    /**
     *
     * @throws CHttpException
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }else {
            throw new CHttpException(400, Yii::t('err', 'bad request'));
        }
    }

    /**
     *
     * @param string $list_type
     * @param integer $user_id
     * @param string $called_for
     * @param integer $role_id
     * @return string or array
     */
    private function getQuery($list_type, $user_id, $called_for = null, $role_id = 999) {

        $query = Yii::app()->db->createCommand()
                ->select('aa.advisor_id, a.firstname, a.lastname, DATE_FORMAT(a.createdtimestamp,"%Y-%m-%d") as createdtimestamp, advinfo.individualcrd, a.email, a.isactive, aa.user_id, a.id')
                ->from('advisor a')
                ->leftJoin('adminadvisors aa', 'a.id = aa.advisor_id')
                ->leftJoin('advisorpersonalinfo advinfo', 'a.id = advinfo.advisor_id');

        switch ($list_type) {

            case 'unassigned':
                $where = " AND a.isactive = '1' AND aa.user_id is NULL";
                break;

            case 'assigned':
                $where = " AND a.isactive = '1' AND aa.user_id = $user_id";
                break;

            case 'deleted':
                $where = " AND a.isactive ='2' ";
                break;

            case 'others':
                $where = " AND a.isactive = '1' AND aa.user_id != $user_id";
                break;

            case 'all':
                $where = "";
                break;

            default :
                $where = " AND aa.user_id = $user_id";
                break;
        }

        if ($called_for == 'count') {
            return $query->where("a.roleid = $role_id  $where")->queryAll();
        } else {
            return $query->where("a.roleid = $role_id  $where");
        }
    }

    /**
     * @desc To get list of advisors
     * @param none
     * @return json array
     */
    public function actionList() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
             $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        if (Yii::app()->getSession()->get('wsuser')->roleid != User::USER_IS_ADMIN) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Please login as an Administrator")));
        }

        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $list_type = Yii::app()->request->getParam('list_type');
        $sort_order = Yii::app()->request->getParam('sort_order');
        $sort_by = Yii::app()->request->getParam('sort_by');
        $current_page = Yii::app()->request->getParam('current_page');
        $result = $this->getQuery($list_type, $user_id, 'count');
        $advisorCount = count($result);
        $paginator = new Paginator($advisorCount);
        $paginator->setPageSize(10);
        $paginator->setCurrentPage($current_page);
        $result = $this->getQuery($list_type, $user_id);
        $result = $result->limit($paginator->getPageSize(), isset($current_page) ? $current_page * $paginator->getPageSize() - $paginator->getPageSize() : 0)
                ->order($sort_by . ' ' . $sort_order)
                ->queryAll();

        $page = $paginator->paginatorHtml();
        if (count($result)) {

            if ($list_type == 'assigned') {

                foreach ($result as $key => $advisor) {

                    $verified_designations = array();
                    $unverified_designations = array();
                    $verified = array();
                    $unverified = array();
                    $advisor_designations = $this->getSelectedDesignations($advisor['id']);
                    $result[$key]['advhash'] = md5('AdvisorHash' . $advisor['id']);
                    $result[$key]['individualcrd'] = empty($advisor['individualcrd']) ? 'N/A' : $advisor['individualcrd'];
                    $result[$key]['verified'] = empty($advisor_designations['verified']) ? 'N/A' : $advisor_designations['verified'];
                    $result[$key]['unverified'] = empty($advisor_designations['unverified']) ? 'N/A' : rtrim($advisor_designations['unverified'], ', ');
                    $result[$key]['all'] = $advisor_designations['all'];
                    $result[$key]['count'] = $advisor_designations['count'];
                }
            } else {

                foreach ($result as $key => $advisor) {

                    //$result[$key]['email'] = 'N/A';
                    $result[$key]['advhash'] = md5('AdvisorHash' . $advisor['id']);
                    $result[$key]['individualcrd'] = empty($advisor['individualcrd']) ? 'N/A' : $advisor['individualcrd'];
                    $result[$key]['verified'] = 'N/A';
                    $result[$key]['unverified'] = 'N/A';

                    if ($list_type == 'all' || $list_type == "others") {

                        if (!empty($advisor['user_id'])) {

                            $assigned_to = $this->getUser($advisor['user_id']);
                            $result[$key]['assigned_to'] = isset($assigned_to) ? $assigned_to : 'N/A';
                        } else {

                            $result[$key]['assigned_to'] = 'N/A';
                        }
                    }
                }
            }
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "userdata" => $result, 'pagination' => $page, 'total' => $advisorCount)));
        } else {

            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => 'No Record Exists.', 'pagination' => $page, 'total' => '0')));
        }
    }

    /*
     * * Get the name of advisor to whom the user is assigned.
     */

    private function getUser($user_id) {
        $Criteria = new CDbCriteria();
        $Criteria->condition = "id = $user_id";
        $users = User::model()->findAll($Criteria);
        $assigned_to = '';
        foreach ($users as $key => $user) {
            $assigned_to = $user->firstname . ' ' . $user->lastname;
        }

        return $assigned_to;
    }

    /**
     * @desc To get advisors designations
     * @param advisorids
     * @array advisor credentials/ Permissions
     */
    public function getDesignations($advisorId) {
        ##remove this query after testing##

        $advisorDesignations = Designation::model()->findAllBySql("SELECT * FROM adv_designations WHERE advisor_id = $advisorId and deleted =0");
        $designation_ids_1 = array();
        $designation_ids = array();
        $others = array();
        foreach ($advisorDesignations as $value) {
            $designation_ids[] = $value['desig_name'];
            if ($value['status'] == 1)
                $designation_ids_1[] = $value['desig_name'];
            if ($value['other'] == 1) {
                $others[] = $value['desig_name'];
            }
        }
        $others = implode(",", $others);

        $advisorDesignations['verified'] = array();
        $advisorDesignations['unverified'] = array();
        $advisorDesignations['credentials'] = array();
        $designations = Designation::model()->desig_name;

        $index_verified = 0;
        $index_unverified = 0;
        $loop = 0;
        foreach ($designations as $key => $designation) {
            $advisorDesignations['credentials'][$loop]['designation_name'] = $designation['name'];
            $advisorDesignations['credentials'][$loop]['designation_id'] = $designation['id'];
            $advisorDesignations['credentials'][$loop]['adv_id'] = isset($value['advisor_id']) ? $value['advisor_id'] : '';
            //change the designation->id to $designation->name on line 229
            if (in_array($designation['name'], $designation_ids)) {
                $advisorDesignations['verified'][$index_verified]['designation_name'] = $designation['name'];
                $advisorDesignations['verified'][$index_verified]['adv_id'] = $value['advisor_id'];
                $advisorDesignations['credentials'][$loop]['status'] = 1;
                $index_verified++;
            } else {
                $advisorDesignations['unverified'][$index_unverified]['designation_name'] = $designation['name'];
                $advisorDesignations['unverified'][$index_unverified]['designation_id'] = $designation['id'];
                $advisorDesignations['unverified'][$index_unverified]['adv_id'] = isset($value['advisor_id']) ? $value['advisor_id'] : '';
                $index_unverified++;
            }
            $loop++;
        }
        $advisorDesignations['credentials'][$loop - 1]['others'] = $others;

        return $advisorDesignations;
    }

    /**
     * @desc To display advisors Designations
     * @param advisorids
     *
     */
    public function actionDisplayDesignation() {
        $advisor_id = Yii::app()->request->getParam('adv_id');
        $advisor_designations = $this->getDesignations($advisor_id);
        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'advisor_designation' => $advisor_designations)));
    }

    /**
     * @desc To get advisors credentials
     * @param advisorids
     * @array advisor credentials/ Permissions
     */
    public function actionAssignAdvisor() {

        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
             $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        if (Yii::app()->getSession()->get('wsuser')->roleid != User::USER_IS_ADMIN) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Please login as an Administrator")));
        }

        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $advisor_id = Yii::app()->request->getParam('adv_id');
        if (isset($advisor_id) && $advisor_id > 0) {
            $advisorAlredyAssigned = Adminadvisor::model()->findBySql("SELECT advisor_id, user_id FROM adminadvisors WHERE advisor_id=:advisor_id", array("advisor_id" => $advisor_id));
            if ($advisorAlredyAssigned) {
                $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Advisor already assigned.')));
            }
        }

        $adminadvisor = new Adminadvisor();
        $adminadvisor->advisor_id = $advisor_id;
        $adminadvisor->user_id = $user_id;
        $adminadvisor->save();
        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => 'Advisor assigned successfully.')));
    }

    /**
     * @desc To hard delete advisor from the database by admin
     */
    public function actionRemoveAdvisor() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
             $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        if (Yii::app()->getSession()->get('wsuser')->roleid != User::USER_IS_ADMIN) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Please login as an Administrator")));
        }

        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $advisor_id = Yii::app()->request->getParam('advisor_id');

        $adminadvisor = new Adminadvisor();
        $advisorDeleted = Adminadvisor::model()->deleteAll("advisor_id = $advisor_id AND user_id = $user_id");

        //unverify advisors after unassign them //
        $verifyArray = array('verified' => 0);
        $result = Advisor::model()->updateAll($verifyArray, "id = '" . $advisor_id . "'");

        if ($advisorDeleted) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => 'Advisor removed successfully.')));
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Advisor can not be removed.')));
        }
    }

    /**
     * @desc To get advisors unassigncredentials
     * @param advisorids
     * @array advisor credentials/ Permissions
     */
    public function actionUnassignedadvisorcount() {
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
            // First get all assigned advisor id

            if (Yii::app()->getSession()->get('wsuser')->roleid != User::USER_IS_ADMIN) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Please login as an Administrator")));
            } else {
                $query = Yii::app()->db->createCommand()
                        ->select('u.id')
                        ->from('advisor u')
                        ->leftJoin('adminadvisors aa', 'u.id = aa.advisor_id')
                        ->where("u.roleid = '999' AND u.isactive = '1' AND aa.user_id = $user_id");
                $resultSet = $query->queryColumn();
                $assignedAdvisor = implode(',', (array_values($resultSet)));

                if (!$assignedAdvisor) {
                    $assignedAdvisor = "\"\"";
                }

                $unverfiedDesigAssignedAdvisor = Yii::app()->db->createCommand(" SELECT count(distinct(advisor_id))
                                                                                            FROM adv_designations
                                                                                            WHERE (advisor_id) NOT IN
                                                                                                    (SELECT advisor_id FROM adv_designations where status = 1)
                                                                                                    AND advisor_id IN($assignedAdvisor)")->queryColumn();

                //  get all un assigned advisor id
                $query = Yii::app()->db->createCommand()
                        ->selectDistinct('u.id')
                        ->from('advisor u')
                        ->leftJoin('adminadvisors aa', 'u.id = aa.advisor_id')
                        ->where("u.roleid = '999' AND u.isactive = '1' AND aa.user_id is NULL");
                $resultSet = $query->queryColumn();
                $totalItem = count(array_values($resultSet)) + $unverfiedDesigAssignedAdvisor[0];
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "total" => $totalItem)));
            }
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => 'Session Expired')));
        }
    }

    /**
     * @desc To verify whether given email already exists or not in database
     * @param none
     * @return
     */
    public function actionValidateEmails() {

        $userObject = User::model()->findBySql("SELECT email FROM user WHERE email=:email", array("email" => $_POST["email"]));
        $states = new State;
        $statearr = $states->states_name;
        if ($userObject) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'type' => 'email', 'message' => 'A user already exists with this email address. Please <a href="./login">sign in</a> instead.')));
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'statesArray' => $statearr)));
        }
    }

    /**
     * actionLogin in the AdvisorController performs login check for Advisors
     */
    public function actionLogin() {
        $email = Yii::app()->request->getParam('email');
        $password = Yii::app()->request->getParam('password');

        if (!isset($email) || empty($email) || !isset($password) || empty($password)) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Incorrect email/password combination.')));
            return;
        }

        $advisorDetails = Advisor::model()->find(array('condition' => 'email = :email AND isactive = "'.Advisor::USER_IS_ACTIVE.'"',
                    'params' => array(':email' => $email)));
        if ($advisorDetails) {
            // if the user has recent failed login attempts, get the count of attempts, the time of the first try, and the time of the last try
            $failedLoginAttempts = AdvisorAccess::model()->findAll(array('select' => 'id, accesstimestamp',
                        'condition' => 'attempt = '.AdvisorAccess::FAILED.' and advisor_id = :advisor_id AND current = '.AdvisorAccess::TRUE,
                        'params' => array(':advisor_id' => $advisorDetails->id)));

            // Check if the user has reached the limit of login attempt limit
            if ($failedLoginAttempts && count($failedLoginAttempts) >= AdvisorAccess::FAILED_LOGIN_COUNT_LIMIT) {

                // if the difference between the last and first of the five failed login attempts is less than the login attempt time limit, lock the account
                $firstFailedAttemptTime = array_values($failedLoginAttempts)[0]['accesstimestamp'];
                $lastFailedAttemptTime = array_values($failedLoginAttempts)[count($failedLoginAttempts)-1]['accesstimestamp'];

                if ( (strtotime($lastFailedAttemptTime) - strtotime($firstFailedAttemptTime)) < AdvisorAccess::FAILED_LOGIN_TIME_LIMIT ) {
                    // while we are within the lockout duration, keep the user locked out
                    if ( (strtotime(date("Y-m-d H:i:s")) - strtotime($lastFailedAttemptTime)) < AdvisorAccess::LOCKOUT_DURATION ) {
                        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'type' => 'locked',
                        'message' => 'Your account has been locked. Please use the Forgot Password link below to reset your password.')));
                    // once the lockout period is over, archive all of the old unsuccessful attempts by setting current to false
                    } else {
                        AdvisorAccess::model()->updateAll(array('current' => AdvisorAccess::FALSE),'advisor_id = ' . $advisorDetails->id . ' and attempt = '.AdvisorAccess::FALSE);
                    }
                // otherwise, archive the first login and add a new one, so that the login attempt time limit is a rolling difference between first and last attempts
                } else {
                    $firstFailedAttemptId = array_values($failedLoginAttempts)[0]['id'];
                    $firstFailedAttempt = AdvisorAccess::model()->find(array('condition' => 'id = :id',
                            'params' => array(':id' => $firstFailedAttemptId)));
                    $firstFailedAttempt->current = AdvisorAccess::FALSE;
                    $firstFailedAttempt->save();
                }
            }

            $advisorModel = new Advisor();
            $advisorModel->email = $email;
            $advisorModel->password = $password;
            $advisorModel->urole = Advisor::USER_IS_ADVISOR;

            if ($advisorModel->authenticateLogin()) {
                $notiCount = AdvisorNotification::model()->count("advisor_id=:advisor_id and status=0", array("advisor_id" => Yii::app()->getSession()->get('wsadvisor')->id));

                //  update last access timestamp
                $authenticatedAdvisor = Advisor::model()->find("id=:id", array("id" => $advisorDetails->id));
                $authenticatedAdvisor->lastaccesstimestamp = new CDbExpression('NOW()');
                $authenticatedAdvisor->save();

                AdvisorAccess::model()->updateAll(array('current' => AdvisorAccess::FALSE),'advisor_id = ' . $advisorDetails->id);

                // and add a new successful attempt record
                $advisorAccess = new AdvisorAccess();
                $advisorAccess->advisor_id = $authenticatedAdvisor->id;
                $advisorAccess->attempt = AdvisorAccess::SUCCESS;
                $advisorAccess->accesstimestamp = date("Y-m-d H:i:s");
                $advisorAccess->current = AdvisorAccess::TRUE;
                $advisorAccess->save();

                $wsArrayToSend = array(
                    "status" => 'OK',
                    "advid" => Yii::app()->getSession()->get('wsadvisor')->id,
                    "email" => Yii::app()->getSession()->get('wsadvisor')->email,
                    "urole" => Yii::app()->getSession()->get('wsadvisor')->roleid,
                    "sess" => Yii::app()->session->sessionID,
                    "notification" => $notiCount,
                    "type" => 'login',
                    "uniquehash" => md5(Yii::app()->getSession()->get('wsadvisor')->email),
                );

                $advisorSubscription = new AdvisorSubscription();
                $subscriptionMessage = $advisorSubscription->updateAdvisorSubscription(Yii::app()->getSession()->get('wsadvisor')->id);
                $this->sendResponse(200, CJSON::encode($wsArrayToSend));
            } else {
                $advisorAccess = new AdvisorAccess();
                $advisorAccess->advisor_id = $advisorDetails->id;
                $advisorAccess->attempt = AdvisorAccess::FAILED;
                $advisorAccess->accesstimestamp = date("Y-m-d H:i:s");
                $advisorAccess->current = AdvisorAccess::TRUE;
                $advisorAccess->save();
                unset($advisorAccess);
            }
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'message' => 'Incorrect email/password combination.')));
    }

    /**
     * actionCreatenewclientsignup() is used by advisors to create new clients internally
     */
    public function actionCreatenewclientsignup() {
        $email = Yii::app()->request->getParam('email');
        if (!isset($email) || empty($email)) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Mandatory fields left blank.')));
            return;
        }

        $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
        $userObject = User::model()->find(array('condition' => 'email=:email', 'params' => array("email" => $email)));
        if ($userObject) {
            $advisorDetails = AdvisorClientRelated::model()->count("user_id = :user_id and advisor_id = :advisor_id", array("user_id" => $userObject->id, "advisor_id" => $advisorId));
            if ($advisorDetails > 0) {
                $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'type' => 'email', 'message' => 'You have already associated with this user.')));
                return;
            }
            if ($userObject->isactive != User::USER_IS_INACTIVE) {
                $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'type' => 'email', 'message' => 'We are unable to process your request because this email address may already be associated with an account.')));
                return;
            }
        } else {
            $userObject = new User();
        }

        $userObject->email = $email;
        $passwordGenerator = PasswordGenerator::factory();
        $randomPassword = $passwordGenerator->generate();
        unset($passwordGenerator);
        $hasher = PasswordHasher::factory();
        $userObject->password = $hasher->HashPassword($randomPassword);
        unset($hasher);

        // create a random unsubscribe code
        $unsubscribecodeGenerator = PasswordGenerator::factory();
        $randomUnsubscribecode = $unsubscribecodeGenerator->generate();
        unset($unsubscribecodeGenerator);
        $codehasher = PasswordHasher::factory();
        $userObject->unsubscribecode = $codehasher->HashPassword($randomUnsubscribecode);
        unset($codehasher);

        $userObject->roleid = User::USER_IS_CONSUMER;
        $userObject->isactive = User::USER_IS_ACTIVE;
        $userObject->requestinvitetokenkey = 'SUCCESS';
        $userObject->createdby = $advisorId;
        $userObject->lastaccesstimestamp = date("Y-m-d H:i:s");

        ##save the values to database
        if ($userObject->save()) {
            $userObject->update("id = :id", array(':id' => $userObject->id));
            $wsAdvisorClient = new stdClass();
            $wsAdvisorClient->id = $userObject->id;
            $wsAdvisorClient->email = $userObject->email;
            $wsAdvisorClient->roleid = $userObject->roleid;
            $wsAdvisorClient->firstname = $userObject->firstname;
            $wsAdvisorClient->lastname = $userObject->lastname;

            // Logout any existing user
            if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $user_id = Yii::app()->getSession()->get('wsuser')->id;
                $array = Yii::app()->cache->get('logout' . $user_id);
                if ($array === false) {
                    $array = array();
                }
                $array[Yii::app()->session->sessionID] = date("Y-m-d H:i:s");
                Yii::app()->cache->set('logout' . $user_id, $array);
            }
            unset(Yii::app()->session["sengine"]);

            // Add New User
            Yii::app()->getSession()->add('wsuser', $wsAdvisorClient);
            $wsArrayToSend = array(
                "status" => 'OK',
                "uid" => Yii::app()->getSession()->get('wsuser')->id,
                "email" => Yii::app()->getSession()->get('wsuser')->email,
                "urole" => Yii::app()->getSession()->get('wsuser')->roleid,
                "sess" => Yii::app()->session->sessionID,
                "notification" => 0,
                "type" => 'signup',
                "uniquehash" => md5(Yii::app()->getSession()->get('wsuser')->email),
            );

            // Login new user
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
            $array = Yii::app()->cache->get('login' . $user_id);
            if ($array === false) {
                $array = array();
            }
            $array[Yii::app()->session->sessionID] = date("Y-m-d H:i:s");
            Yii::app()->cache->set('login' . $user_id, $array);

            ##add as default row in user profile
            $userPerDetails = Userpersonalinfo::model()->find("user_id = :user_id", array(':user_id' => $wsAdvisorClient->id));

            if (!$userPerDetails) {
                $userPerDetails = new Userpersonalinfo();
                $userPerDetails->user_id = $wsAdvisorClient->id;
            }
            $userPerDetails->retirementstatus = 0;
            $userPerDetails->retirementage = 65;
            $userPerDetails->maritalstatus = 'Single';
            $userPerDetails->noofchildren = 0;
            $userPerDetails->save();

            $SControllerObj = new Scontroller(1);
            $SControllerObj->unsetEngine();
            $SControllerObj->setEngine();
            $SControllerObj->setupDefaultRetirementGoal();
            $SControllerObj->calculateScore("GOAL");

            $ASObj = new ActionstepController(1);
            $ASObj->updateComponents();

            $UserControllerObj = new UserController(1);
            $UserControllerObj->setSubscriptionStatus("Subscribe", true);

            ##To get new user credentials details.
            $usercredentials = User::model()->findByPk($wsAdvisorClient->id);
            $advisorDetails = Advisorpersonalinfo::model()->findByPk($advisorId);

            // Generate a reset password token which validates during update of password
            $passwordGenerator = PasswordGenerator::factory();
            $randomPassword = $passwordGenerator->generate();
            unset($passwordGenerator);
            $hasher = PasswordHasher::factory();
            $usercredentials->resetpasswordcode = $hasher->HashPassword($randomPassword);
            $usercredentials->resetpasswordexpiration = new CDbExpression('NOW() + INTERVAL 10 DAY');
            $usercredentials->save();

            ## Code to send email
            $part = 'user-registration-by-advisor';
            $welcomeEmail = new Email();
            $welcomeEmail->subject = 'Welcome to FlexScore';
            $welcomeEmail->recipient['email'] = $wsAdvisorClient->email;
            $welcomeEmail->data[$part] = [
                'advisor-name' => "{$advisorDetails->firstname} {$advisorDetails->lastname}",
                'token' => $usercredentials->resetpasswordcode,
            ];
            $welcomeEmail->send();
            Yii::app()->getSession()->remove('wsuser');

            ## Code to send email
            if ($this->actionAdvisorRelatedClient($advisorId, $userObject->id, 'other_function')) {
                $this->sendResponse(200, CJSON::encode($wsArrayToSend));
            }
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'type' => 'email', 'message' => 'This user can not be added at this time. Please try again later.')));
        }
    }

    /*
     * * Generate Random Password.
     */

    public function actionRandomPassword() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 16; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /**
     *
     * @desc Create new Client By Advisor
     */
    public function actionAdvisorRelatedClient($advisorID = null, $clientID = null, $call_from = null) {

        if (empty($advisorID) && empty($clientID)) {
            $advisorID = Yii::app()->request->getParam("advid");
            $clientID = Yii::app()->request->getParam("uid");
        }

        $currentDate = date('Y-m-d');
        $advisorUserObject = new AdvisorClientRelated;
        $advisorUserObject->user_id = $clientID;
        $advisorUserObject->advisor_id = $advisorID;
        $advisorUserObject->permission = 'RW';
        $advisorUserObject->dateconnect = $currentDate;
        $advisorUserObject->status = '1';
        $advisorUserObject->indemnification_check = '0';
        if ($advisorUserObject->save()) {

            if (!empty($call_from)) {
                return true;
            }
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => 'Success', 'userData' => $advisorUserObject)));
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => 'Unable to add the user')));
        }
    }

    /**
     *
     * @desc  based on advisor role id to show back to dashboard link
     */
    public function actionBacktodashboard() {

        if (Yii::app()->getSession()->get('wsadvisor')) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'data' => Yii::app()->getSession()->get('wsadvisor'), 'data2' => Yii::app()->getSession()->get('wsuser'))));
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'data' => Yii::app()->getSession()->get('wsuser')->roleid)));
        }
    }

    /**
     *
     * @desc TO Destroy WS USER CLIENT SESSION
     */
    public function actionDestroyclientsession() {
        // Logout any existing user
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
            $array = Yii::app()->cache->get('logout' . $user_id);
            if ($array === false) {
                $array = array();
            }
            $array[Yii::app()->session->sessionID] = date("Y-m-d H:i:s");
            Yii::app()->cache->set('logout' . $user_id, $array);
        }

        unset(Yii::app()->session['wsuser']);
        unset(Yii::app()->session["sengine"]);
        $this->sendResponse(200, CJSON::encode(array('status' => 'OK')));
    }

    /**
     *
     * @desc to view finance details
     */
    public function actionViewfinances() {
        if (!isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }

        $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
        $email = $_POST['email'];
        $userObject = User::model()->findBySql("SELECT id,email,roleid,firstname,lastname FROM user WHERE email=:email", array("email" => $email));
        if (!$userObject) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "You do not have permissions to view this client.")));
        }

        $userAdvisorObject = AdvisorClientRelated::model()->find("user_id=:user_id and advisor_id=:advisor_id", array("user_id" => $userObject->id, "advisor_id" => $advisorId));
        if (!$userAdvisorObject || $userAdvisorObject->permission == 'N') {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "You do not have permissions to view this client.")));
        }

        $wsuserclient = new stdClass();
        $wsuserclient->id = $userObject->id;
        $wsuserclient->email = $userObject->email;
        $wsuserclient->roleid = $userObject->roleid;
        $wsuserclient->firstname = $userObject->firstname;
        $wsuserclient->lastname = $userObject->lastname;
        $wsuserclient->permission = $userAdvisorObject->permission;

        // Logout any existing user
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
            $array = Yii::app()->cache->get('logout' . $user_id);
            if ($array === false) {
                $array = array();
            }
            $array[Yii::app()->session->sessionID] = date("Y-m-d H:i:s");
            Yii::app()->cache->set('logout' . $user_id, $array);
        }
        unset(Yii::app()->session["sengine"]);

        Yii::app()->getSession()->add('wsuser', $wsuserclient);
        $notiCount = Notification::model()->count("user_id=:user_id and status=0", array("user_id" => $userObject->id));
        $wsArrayToSend = array(
            "status" => 'OK',
            "id" => Yii::app()->getSession()->get('wsuser')->id,
            "email" => Yii::app()->getSession()->get('wsuser')->email,
            "urole" => Yii::app()->getSession()->get('wsuser')->roleid,
            "permission" => Yii::app()->getSession()->get('wsuser')->permission,
            "sess" => Yii::app()->session->sessionID,
            "notification" => $notiCount,
            "type" => 'signup',
            "uniquehash" => md5(Yii::app()->getSession()->get('wsuser')->email),
        );

        // Login new user
        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $array = Yii::app()->cache->get('login' . $user_id);
        if ($array === false) {
            $array = array();
        }
        $array[Yii::app()->session->sessionID] = date("Y-m-d H:i:s");
        Yii::app()->cache->set('login' . $user_id, $array);

        $ASObj = new ActionstepController(1);
        $wsArrayToSend["mixPanelData"] = $ASObj->updateComponents();

        $this->sendResponse(200, CJSON::encode($wsArrayToSend));
    }

    /**
     *
     * @desc Get advisor My Client list( name&contact information, user score, permission, dateconnect)
     * @return either query or resultset
     */
    private function getAdvisorListQuery($user_id, $called_for = null) {
        $query = Yii::app()->db->createCommand()
                ->select('ca.user_id, ca.advisor_id, ca.message, ca.permission, ca.dateconnect, ca.status, ca.phone, u.id, u.firstname, u.lastname, u.email, ROUND(us.totalscore + 250 * (IF(mcu.montecarloprobability is NULL, us.montecarloprobability, mcu.montecarloprobability) - us.montecarloprobability)) as totalscore')
                ->from('consumervsadvisor ca')
                ->leftJoin('user u', 'ca.user_id=u.id')
                ->leftJoin('userscore us', 'us.user_id=u.id')
                ->leftJoin('montecarlouser mcu', 'mcu.user_id=u.id')
                ->where(" ca.advisor_id = $user_id AND u.isactive = '1'")
                ->order("ca.status ASC");


        if ($called_for == 'count') {
            return $query->queryAll();
        } else {
            return $query;
        }
    }

    private function getASListQuery($called_for = null) {
        $query = Yii::app()->db->createCommand()
                ->select('actionid, actionname, description')
                ->from('leapscoremeta.actionstepmeta')
                ->where("externallink!=''")
                ->order("actionid ASC");


        if ($called_for == 'count') {
            return $query->queryAll();
        } else {
            return $query;
        }
    }

    /**
     * *Get client list associated with advisor on dashboard,
     * *perform sorting and pagination on details.
     */
    public function actionAdvDetails() {
        if (isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $user_id = Yii::app()->getSession()->get('wsadvisor')->id;
            $sort_order = Yii::app()->request->getParam('sort_order');

            $sort_by = Yii::app()->request->getParam('sort_by');
            $current_page = Yii::app()->request->getParam('current_page');
            $result = $this->getAdvisorListQuery($user_id, 'count');
            $advisorCount = count($result);
            $advisor = Advisor::model()->findByPk($user_id);
            $recordPerPage = 25;
            if (isset($_POST['record_per_page'])) {
                $recordPerPage = $_POST['record_per_page'];
            }
            if ($advisorCount > 0) {
                $paginator = new Paginator($advisorCount);
                $paginator->setPageSize($recordPerPage);

                if (!isset($current_page)) {
                    $current_page = 1;
                }
                if (isset($current_page) && isset($advisorCount)) {
                    if (($advisorCount / $recordPerPage) < ($current_page - 1)) {
                        $current_page = 1;
                    }
                }

                $paginator->setCurrentPage($current_page);
                $result = $this->getAdvisorListQuery($user_id);
                $result = $result->limit($paginator->getPageSize(), isset($current_page) ? $current_page * $paginator->getPageSize() - $paginator->getPageSize() : 0);
                $order = '';
                switch ($sort_by) {
                    case 'name':
                        $order = " u.firstname $sort_order";
                        break;

                    case 'score':
                        $order = " totalscore $sort_order";
                        break;

                    case 'date':
                        $order = " ca.dateconnect $sort_order";
                        break;

                    default :
                        $order = " ca.status $sort_order"; //default sort by pending status
                        break;
                }
                $result = $result->order($order)->queryAll();

                if (count($result) <= 0 && $current_page != 1) {
                    $current_page = $current_page - 1;
                    $paginator->setCurrentPage($current_page);
                    $result = $this->getAdvisorListQuery($user_id);
                    $result = $result->limit($paginator->getPageSize(), isset($current_page) ? $current_page * $paginator->getPageSize() - $paginator->getPageSize() : 0);
                    $order = '';
                    switch ($sort_by) {
                        case 'name':
                            $order = " u.firstname $sort_order";
                            break;

                        case 'score':
                            $order = " totalscore $sort_order";
                            break;

                        case 'date':
                            $order = " ca.dateconnect $sort_order";
                            break;

                        default :
                            $order = " ca.status $sort_order"; //default sort by pending status
                            break;
                    }
                    $result = $result->order($order)->queryAll();
                }

                // Show the points change to 3 months always. //
                //If 3 months doesnt exist, subtract current score - 202. //
                $threeMC = "";
                foreach ($result as $key => $value) {
                    $totalScore = $value['totalscore'];
                    $user_id = $value['user_id'];

                    // create object of scorechange model //
                    $scoreChangeObj = new ScoreChange();
                    $scoreChangeRecord = $scoreChangeObj->find("user_id=:user_id", array("user_id" => $user_id));

                    $resultScoreArray = array();
                    if (isset($scoreChangeRecord) && $scoreChangeRecord["scorechange"] != "") {
                        $sparsedScoreObjArray = json_decode($scoreChangeRecord["scorechange"], true);
                        $first = strtotime(date(key($sparsedScoreObjArray)));
                        $current = strtotime(date("Y-m-d"));

                        while ($first <= $current) {
                            $dateKey = date("Y-m-d", $first);
                            if (isset($sparsedScoreObjArray["$dateKey"])) {
                                $resultScoreArray["$dateKey"] = $sparsedScoreObjArray["$dateKey"];
                            } else {
                                $resultScoreValues = array_values($resultScoreArray);
                                $lastScoreDetails = end($resultScoreValues);
                                $resultScoreArray["$dateKey"] = array('Total' => $lastScoreDetails['Total'], 'Change' => 0, 'Reason' => '');
                            }
                            $first = strtotime('+1 day', $first);
                        }
                    }
                    $cnt = count($resultScoreArray);

                    if ($cnt >= 90) {
                        $min = date('Y-m-d', strtotime("-89 days"));
                        $threeMC = $totalScore - $resultScoreArray[$min]["Total"];
                        if ($threeMC > 0) {
                            $result[$key]['scoretype'] = "+";
                            $threeMC = "+ " . $threeMC . " pts";
                            $result[$key]['daychangestext'] = " last quarter";
                        }
                        if ($threeMC < 0) {
                            $result[$key]['scoretype'] = "-";
                            $threeMC = $threeMC . " pts";
                            $result[$key]['daychangestext'] = " last quarter";
                        }
                    } else {
                        $threeMC = $totalScore - 202;
                        if ($threeMC > 0) {
                            $result[$key]['scoretype'] = "+";
                            $threeMC = "+ " . $threeMC . " pts";
                        }
                        if ($threeMC < 0) {
                            $result[$key]['scoretype'] = "-";
                            $threeMC = $threeMC . " pts";
                            $result[$key]['daychangestext'] = "";
                        }
                    }
                    $result[$key]['daychanges'] = $threeMC;

                    $notification = array();
                    $userNotifications = Notification::model()->findAllBySql("SELECT info, DATE_FORMAT(lastmodified,'%b %e, %Y') as lastmodified, message, user_id, refid FROM notification WHERE status = 0 and user_id  ='" . $user_id . "'");
                    if (isset($userNotifications)) {
                        foreach ($userNotifications as $key1 => $value1) {
                            $info = json_decode($value1->info, true);
                            $notification[$key1]["finame"] = $info['finame'];
                            $notification[$key1]["fid"] = $info['fid'];
                            $notification[$key1]["message"] = $value1['message'];
                            $notification[$key1]["lastmodified"] = $value1['lastmodified'];
                        }
                        //print_r($notification);
                        $result[$key]['notifications'] = $notification;
                    } else {
                        $result[$key]['notifications'] = "";
                    }
                }

                ##Apply Pagination
                $page = $paginator->paginatorHtml();
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "userdata" => $result, "totalClient" => $advisorCount, 'pagination' => $page, 'sortBy' => $sort_by, 'sortOrder' => $sort_order, 'verified' => $advisor->verified)));
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "msg" => 'No record exists.', "userdata" => '', 'sortBy' => "", 'sortOrder' => $sort_order, 'verified' => $advisor->verified)));
            }
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "msg" => 'No record exists.', "userdata" => '', 'sortBy' => "", 'sortOrder' => $sort_order, 'verified' => $advisor->verified)));
        }
    }

    /*
     * *List of advisor related to Flex Score User on myscore page
     */

    public function actionUseradvisorlist() {

        $userId = Yii::app()->getSession()->get('wsuser')->id;
        $createdBy = "";
        $user = Yii::app()->db->createCommand()
                ->select('indemnification_check')
                ->from('user u')
                ->join('consumervsadvisor ca', 'u.createdby=ca.advisor_id and u.id=ca.user_id')
                ->where('ca.user_id=:id ', array(':id' => $userId))
                ->queryAll();
        if (isset($user)) {
            foreach ($user as $value) {
                $indemnification_check = $value['indemnification_check'];
                if ($indemnification_check == 1) {
                    $createdBy = 'advisor';
                } else {
                    $createdBy = "";
                }
            }
        }
        $userAdvisor = Yii::app()->db->createCommand()
                ->select('ca.user_id, ca.advisor_id, ca.permission, ca.status, u.email, advp.firstname, advp.lastname, advp.profilepic')
                ->from('consumervsadvisor ca')
                ->join('advisor u', 'ca.advisor_id=u.id')
                ->join('advisorpersonalinfo advp', 'ca.advisor_id=advp.advisor_id')
                ->where(' ca.user_id =:userId ', array(':userId' => $userId))
                ->queryAll();

        if (count($userAdvisor) != 0) {
            $advisorPermission = array();
            foreach ($userAdvisor as $value) {

                $advisorPermission[$value['permission']][] = $value;
            }
            krsort($advisorPermission);
            $advisorDetails = array();
            foreach ($advisorPermission as $key => $sortvalue) {

                foreach ($sortvalue as $value) {

                    $credentials = $this->getDesignations($value['advisor_id']);

                    $value['credentials'] = $credentials['verified'];
                    $value['advhash'] = md5('AdvisorHash' . $value['advisor_id']);
                    $advisorDetails[] = $value;
                }
            }

            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "userdata" => $advisorDetails, "loggedin_user_created_by" => $createdBy)));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => 'OK', "connectedAdv" => "NULL", "loggedin_user_created_by" => $createdBy)));
        }
    }

    /*
     * * Advisor profile details "About you on Dashboard page"
     */

    public function actionGetadvisorprofiledetails() {
        $user_id = Yii::app()->getSession()->get('wsadvisor')->id;
        $userObj = Advisor::model()->findByPk($user_id);
        $userPerObj = Advisorpersonalinfo::model()->findByPk($user_id);

        $userData = array(
            'firstname' => $userObj->firstname,
            'lastname' => $userObj->lastname,
            'email' => $userObj->email,
            'verified' => $userObj->verified
        );

        $userData['advhash'] = md5('AdvisorHash' . $user_id);
        $userData['firstname'] = isset($userPerObj->firstname) ? $userPerObj->firstname : '';
        $userData['lastname'] = isset($userPerObj->lastname) ? $userPerObj->lastname : '';
        $userData['firmname'] = isset($userPerObj->firmname) ? $userPerObj->firmname : '';
        $userData['description'] = isset($userPerObj->description) ? $userPerObj->description : '';
        $userData['advisortype'] = isset($userPerObj->advisortype) ? $userPerObj->advisortype : '';
        $userData['typeofcharge'] = !empty($userPerObj->typeofcharge) ? unserialize($userPerObj->typeofcharge) : '';
        $userData['avgacntbalanceperclnt'] = isset($userPerObj->avgacntbalanceperclnt) ? $userPerObj->avgacntbalanceperclnt : '';
        $userData['minasstsforpersclient'] = isset($userPerObj->minasstsforpersclient) ? $userPerObj->minasstsforpersclient : '';
        $userData['individualcrd'] = isset($userPerObj->individualcrd) ? $userPerObj->individualcrd : '';
        $userData['advisortype'] = isset($userPerObj->advisortype) ? $userPerObj->advisortype : '';
        $userData['states'] = $this->getAllStates($user_id);
        $allDesignations = $this->getDesignations($user_id);
        $userData['designation'] = isset($allDesignations['credentials']) ? $allDesignations['credentials'] : '';
        $allServices = $this->getAllProductServiceOfAdvisor($user_id);
        $userData['productservice'] = $allServices;

        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "userdata" => $userData)));
    }

    /*
     * * Update the advisor personal information.
     */

    public function actionUpdateAdvisorProfile() {
        $user_id = Yii::app()->getSession()->get('wsadvisor')->id;
        $wsUserObject = Yii::app()->getSession()->get('wsadvisor');

        ##Getting advisor updated details
        $firstname = Yii::app()->request->getParam('firstname');

        $lastname = Yii::app()->request->getParam('lastname');
        $wsUserObject->firstname = $firstname;
        $wsUserObject->lastname = $lastname;

        $firmname = Yii::app()->request->getParam('firmname');
        $advisortype = Yii::app()->request->getParam('advisortype');
        $description = Yii::app()->request->getParam('description');
        $chargetype = serialize(Yii::app()->request->getParam('chargetype'));
        $minAssist = Yii::app()->request->getParam('min_assist');
        $avgBal = Yii::app()->request->getParam('avg_bal');
        $individualCrd = Yii::app()->request->getParam('individualCrd');

        ##Array to update advisor personal information tables
        $updateProfile = array('firstname' => $firstname, 'lastname' => $lastname, 'advisortype' => $advisortype, 'firmname' => $firmname, 'description' => $description, 'typeofcharge' => $chargetype, 'minasstsforpersclient' => $minAssist, 'avgacntbalanceperclnt' => $avgBal, 'individualcrd' => $individualCrd);

        ##Updating advisor table
        $result = Advisor::model()->updateAll($updateProfile, "id = '" . $user_id . "'");
        $notificationORM = AdvisorNotification::model()->find("advisor_id = :advisor_id and status=0 and template = 'about'", array(':advisor_id' => $wsUserObject->id));
        if ($notificationORM) {
            $notificationORM->status = 1;
            $notificationORM->lastmodified = date("Y-m-d H:i:s");
            $notificationORM->save();
        }
        $notificationCount = AdvisorNotification::model()->count("advisor_id = :advisor_id and status=0", array(':advisor_id' => $wsUserObject->id));

        ##Updating advisor personal info table
        $resultAdvisor = Advisorpersonalinfo::model()->updateAll($updateProfile, "advisor_id = '" . $user_id . "'");

        ##Manageing advisor old and updated designations
        $designation = Yii::app()->request->getParam('designation');
        $updatedDesignations = explode(",", $designation);
        if (in_array('multiselect-all', $updatedDesignations)) {
            $pos = array_search('multiselect-all', $updatedDesignations);
            unset($updatedDesignations[$pos]);
        }

        $updatedOtherDesignations = Yii::app()->request->getParam('extradesigvalue');
        $updatedOtherDesignations = explode(',', $updatedOtherDesignations);
        $count = 0;
        //Check if space exists in substrings
        foreach ($updatedOtherDesignations as $explode_tag) {
            if (strpos(trim($explode_tag), ' ') == false) { //strpos better for checking existance
                $count++;
            }
        }

        $advisorExistingDesignations = $this->getAdvisorDesignations($user_id);
        $total_designations = count($advisorExistingDesignations);
        $existingFlexScoreDesignstions = array();
        $existingOtherDesignstions = array();
        $existingDesignations = array();
        foreach ($advisorExistingDesignations as $designation) {
            if ($designation['other'] == 0) {
                $existingFlexScoreDesignstions[] = $designation['desig_name'];
            } else {
                $existingOtherDesignstions[] = $designation['desig_name'];
            }
            $existingDesignations[] = $designation['desig_name'];
        }

        $advisorStatus = Designation::model()->findAllByAttributes(array('advisor_id' => $user_id, 'deleted' => 1));
        $existingDesig_deleted = array();
        foreach ($advisorStatus as $designation) {
            $existingDesig_deleted[] = $designation['desig_name'];
        }
        for ($i = 0; $i < count($existingDesig_deleted); $i++) {
            if (in_array($existingDesig_deleted[$i], $updatedDesignations)) {
                $updateStatusZero = array('deleted' => 0);
                foreach ($existingDesig_deleted as $designation) {
                    $update = Designation::model()->updateAll($updateStatusZero, "advisor_id = '" . $user_id . "' AND desig_name in ('" . $designation . "')");
                }
            }
        }

        $updatedDesignations = array_merge($updatedDesignations, $updatedOtherDesignations);
        $newDesignations = array_diff($updatedDesignations, $existingDesignations);

        $otherDesignation = 0;

        foreach ($newDesignations as $designation) {

            if ($designation == 'Other') {
                $otherDesignation = 1;
                continue;
            }
            if (!empty($designation)) {
                $designationObject = new Designation;
                $designationObject->advisor_id = $user_id;
                $designationObject->desig_name = $designation;
                $designationObject->other = $otherDesignation;
                $designationObject->save();
            }
        }
        $removeDesignations = array_diff($existingDesignations, $updatedDesignations);

        $updateStatusOne = array('deleted' => 1);
        foreach ($removeDesignations as $designation) {
            $update = Designation::model()->updateAll($updateStatusOne, "advisor_id = '" . $user_id . "' AND desig_name in ('" . $designation . "')");
        }
        ##End Manageing advisor old and updated designations
        ##Manage Product And services
        $newFlexScorePNS = Yii::app()->request->getParam('productservice');
        $updatedAdvisorPNS = explode(",", $newFlexScorePNS);
        if (in_array('multiselect-all', $updatedAdvisorPNS)) {
            $pos = array_search('multiselect-all', $updatedAdvisorPNS);
            unset($updatedAdvisorPNS[$pos]);
        }
        $newOtherPNS = Yii::app()->request->getParam('extraprod');
        $newOtherPNS = explode(',', $newOtherPNS);
        ##Check if space exists in substrings
        $count = 0;
        foreach ($newOtherPNS as $explode_tag) {
            if (strpos(trim($explode_tag), ' ') == false) { //strpos better for checking existance
                $count++;
            }
        }
        $updatedPNS = array_merge($updatedAdvisorPNS, $newOtherPNS);
        $existingProductService = $this->advisorProductService($user_id);
        $existingFlexScorePNS = array();
        $existingOtherPNS = array();
        $existingPNS = array();
        foreach ($existingProductService as $PNS) {

            if ($PNS['other'] == 0) {
                $existingFlexScorePNS[] = $PNS['productserviceid'];
            } else {
                $existingOtherPNS[] = $PNS['productserviceid'];
            }
            $existingPNS[] = $PNS['productserviceid'];
        }

        $newPNS = array_diff($updatedPNS, $existingPNS);
        $otherPNS = 0;

        foreach ($newPNS as $PNS) {

            if ($PNS == 'Other') {
                $otherPNS = 1;
                continue;
            }
            if (!empty($PNS)) {
                $PNSObject = new Productservice;
                $PNSObject->advisor_id = $user_id;
                $PNSObject->other = $otherPNS;
                $PNSObject->productserviceid = $PNS;
                $PNSObject->save();
            }
        }
        $removePNS = array_diff($existingPNS, $updatedPNS);

        foreach ($removePNS as $PNS) {
            $productServiceDeleted = Productservice::model()->deleteAll("advisor_id = '" . $user_id . "' AND     productserviceid in ('" . $PNS . "')");
        }
        ##End Manage Product And services
        //=state update=
        $existingStates = array();

        $oldAdvisorStates = $this->getStatesInRegistered($user_id);

        if ((count($oldAdvisorStates)) && isset($oldAdvisorStates[0]['id'])) {
            foreach ($oldAdvisorStates as $registeredStates) {
                $existingStates[] = $registeredStates['id'];
            }
        }

        $state = Yii::app()->request->getParam('state');
        $postStates = explode(",", $state);
        $newlySelectedStates = array_diff($postStates, $existingStates);
        ## delete state array
        $deleteStates = array_diff($postStates, $newlySelectedStates);
        $totalStateDeleted = array_diff($existingStates, $deleteStates);

        foreach ($totalStateDeleted as $value) {
            $stateDeleted = State::model()->deleteAll("advisor_id = '" . $user_id . "' AND stateregistered in ('" . $value . "')");
        }
        $reindexNewStates = array_values($newlySelectedStates);
        $addNewState = array('stateregistered' => $reindexNewStates);

        if (isset($addNewState['stateregistered'][0]) && $addNewState['stateregistered'][0] == 'multiselect-all') {
            unset($addNewState['stateregistered'][0]);
        }

        if ((count($addNewState))) {
            $val = array_values($addNewState['stateregistered']);
            for ($index = 0; $index < count($val); $index++) {
                $userObject = new State();
                $userObject->advisor_id = $user_id;

                if ($val[0] != "") {
                    $userObject->stateregistered = $val[$index];
                    if (!$userObject->save()) {
                        $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Unable to add states')));
                    }
                }
            }
        }

        if (empty($firstname) || empty($lastname) || empty($advisortype) || empty($state) || empty($firmname) || empty($description) || empty($chargetype) || empty($minAssist) || empty($avgBal) || empty($individualCrd) || empty($newFlexScorePNS)) {
            $updates = array('status' => 0, 'lastmodified' => date("Y-m-d H:i:s"));
            $result = AdvisorNotification::model()->updateAll($updates, 'advisor_id = "' . $user_id . '" and template in ("about")');
            $notificationCount = AdvisorNotification::model()->count("advisor_id = :advisor_id and status=0", array(':advisor_id' => $user_id));
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'notificationCount' => $notificationCount)));
        } else {

            $updates = array('status' => 1, 'lastmodified' => date("Y-m-d H:i:s"));
            $result = AdvisorNotification::model()->updateAll($updates, 'advisor_id = "' . $user_id . '" and template in ("about")');
            $notificationCount = AdvisorNotification::model()->count("advisor_id = :advisor_id and status=0", array(':advisor_id' => $user_id));
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'notificationCount' => $notificationCount)));
        }

        //$this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'notificationCount' => $notificationCount)));
        //=end of state update=
    }

    /*
     *
     * Get the id of the Advisor and the Client to whom delete
     */

    public function actionDelete() {
        if (isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
            $deleteId = Yii::app()->request->getParam('id');
            $status = Yii::app()->request->getParam('status');
            $userObj = User::model()->findByPk($deleteId);
            $clientEmail = $userObj->email;
            $firstName = $userObj->firstname; //firstname
            $lastName = $userObj->lastname; //lastname
            $userAdvisorObject = AdvisorClientRelated::model()->findBySql("SELECT message,status,topic,phone FROM consumervsadvisor WHERE user_id=:user_id AND advisor_id=:advisor_id", array("user_id" => $deleteId, "advisor_id" => $advisorId));
            $params = array(
                "advisor_id" => $advisorId,
                "user_id" => $deleteId,
                "firstname" => $firstName,
                "lastname" => $lastName,
                "message" => stripslashes($userAdvisorObject['message']),
                "status" => $userAdvisorObject['status'],
                "topic" => $userAdvisorObject['topic'],
                "phone" => $userAdvisorObject['phone'],
            );
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "data" => $params, "connectionStatus" => $status, "clientEmail" => $clientEmail)));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "msg" => 'Please try again')));
        }
    }

    public function actionDeleteclient() {
        if (isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
            $clientId = Yii::app()->request->getParam('id');
            $connectionStatus = Yii::app()->request->getParam('status');
            $clientEmail = Yii::app()->request->getParam('clientEmail'); //recipient email
            $advisorDetails = Advisorpersonalinfo::model()->findByPk($advisorId);
            $clientDetails = User::model()->findByPk($clientId);
            $advisorDetails->firstname; //advisor firstname
            $advisorDetails->lastname; //advisor lastname
            $params = array(
                "advid" => $advisorId,
                "userid" => $clientId
            );
            $advisorsIndemnificationDetail = AdvisorClientRelated::model()->find("advisor_id = :advisorId AND user_id = :clientId", array("advisorId" => $advisorId, "clientId" => $clientId));
            $deleteClient = AdvisorClientRelated::model()->deleteAll("advisor_id = :advisorId AND user_id = :clientId", array("advisorId" => $advisorId, "clientId" => $clientId));
            // Check accepted the connection or not.  If accepted the connection, the no mail should be sent.
            if ($advisorsIndemnificationDetail && $advisorsIndemnificationDetail['status'] == 0) {

                ## Code to send mail
                if ($connectionStatus == 0) {
                    $part = 'connection-request-declined';
                    $email = new Email();
                    $email->subject = 'Your connection request was declined';
                    $email->recipient['email'] = $clientEmail;
                    $email->data[$part] = [
                        'advisor-name' => "{$advisorDetails->firstname} {$advisorDetails->lastname}",
                    ];
                    $email->data['unsubscribe'] = true;
                    $email->data['recipient_type'] = 'us';
                    $email->data['unsubscribe_code'] = $clientDetails->unsubscribecode;
                    $email->send();
                }
                ## Code end to send mail
            }

            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "data" => $params)));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "msg" => 'Session timeout')));
        }
    }

    /*
     *
     * Delete Advisor By Admin
     */

    public function actionDeleteadvisor() {
        $advisorId = Yii::app()->request->getParam('advisor_id');
        $roleid = Advisor::USER_IS_ADVISOR;
        $statusArray = array('isactive' => 2);
        $result = Advisor::model()->updateAll($statusArray, "id = '" . $advisorId . "'");
        if ($result) {
            $consumerVsAdvisor = ConsumerVsAdvisor::model()->deleteAll(array('condition' => "advisor_id=:advisor_id",
                'params' => array("advisor_id" => $advisorId)));

            $advisorSubscription = AdvisorSubscription::model()->find(array('condition' => "advisor_id=:advisor_id",
                'params' => array("advisor_id" => $advisorId)));

            if ($advisorSubscription) {
                $advisorSubscription = new AdvisorSubscription();
                $subscriptionResponse = $advisorSubscription->CancelAdvisorSubscription($advisorId);
            }
            $this->sendResponse(200, CJSON::encode(array("status" => "OK")));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'msg' => 'Advisor can not delete....Please try later.')));
        }
    }

    /*
     * *Delete Advisor By User
     *
     */

    public function actionDeleteuseradvisor() {
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $advisorId = Yii::app()->request->getParam('id');
            $user_id = Yii::app()->getSession()->get('wsuser')->id;

            $deleteClient = AdvisorClientRelated::model()->deleteAll("advisor_id = '" . $advisorId . "' AND user_id in ('" . $user_id . "')");
            $params = array(
                "userid" => $user_id,
                "id" => $advisorId
            );
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => 'You have successfully deleted the advisor.', 'deletedId' => $params)));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => 'Please try again', 'deletedId' => $params)));
        }
    }

    /*
     * * Update permission of advisor by user.
     *
     */

    public function actionUpdateadvisorpermission() {
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $userId = Yii::app()->getSession()->get('wsuser')->id;
            $advisorId = Yii::app()->request->getParam('id');
            $updatedPermission = Yii::app()->request->getParam('permission');

            if ($updatedPermission == "RW") {

                $advisorsPermissionDetails = AdvisorClientRelated::model()->findBySql("SELECT indemnification_check,permission FROM consumervsadvisor WHERE user_id=:user_id AND permission=:permission", array("user_id" => $userId, "permission" => 'RW'));

                $totalDetail = count($advisorsPermissionDetails);

                if ($totalDetail != 0) {
                    if ($advisorsPermissionDetails['indemnification_check'] == 0) {
                        $updateUserAdvisor = array('permission' => $updatedPermission, 'indemnification_check' => '1');
                        $result = AdvisorClientRelated::model()->updateAll($updateUserAdvisor, 'user_id = "' . $userId . '" and advisor_id in (' . $advisorId . ')');
                    } else {
                        $advisorsPermission = AdvisorClientRelated::model()->find("user_id=:user_id AND advisor_id=:advisorid", array("user_id" => $userId, "advisorid" => $advisorId));
//                        $this->sendResponse(200, CJSON::encode(array('status' => 'Warning', 'message' => 'You already have an advisor with View+Edit permission for your FlexScore account. Are you sure to give View+Edit permission to current advisor?', 'permission' => $advisorsPermission->permission)));
                        $this->sendResponse(200, CJSON::encode(array('status' => 'Warning', 'message' => 'You already have an advisor with View+Edit permission for your FlexScore account. Are you sure to give View+Edit permission to current advisor?', 'permission' => 'RW')));
                    }
                } else {
                    $updateUserAdvisor = array('permission' => $updatedPermission, 'indemnification_check' => '1');
                    ##Updating consumervsadvisor table
                    $result = AdvisorClientRelated::model()->updateAll($updateUserAdvisor, 'user_id = "' . $userId . '" and advisor_id in (' . $advisorId . ')');
                    $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "You have updated the permission.", "permission" => $updatedPermission)));
                }
            } else {
                $updateUserAdvisor = array('permission' => $updatedPermission, 'indemnification_check' => '1');
                ##Updating consumervsadvisor table
                $result = AdvisorClientRelated::model()->updateAll($updateUserAdvisor, 'user_id = "' . $userId . '" and advisor_id in (' . $advisorId . ')');
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "You have updated the permission.")));
            }
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Please Try again")));
        }
    }

    /*
     * * Update the lead advisor bu Flex scoreuser
     */

    public function actionUpdateleadadvisor() {
        $userId = Yii::app()->getSession()->get('wsuser')->id;
        $advisorId = Yii::app()->request->getParam('id');
        $updatedPermission = Yii::app()->request->getParam('permission');

        if ($updatedPermission == "RW") {
            $advisorsPermissionDetails = AdvisorClientRelated::model()->findAllBySql("SELECT * FROM consumervsadvisor WHERE user_id=:user_id AND permission=:permission", array("user_id" => $userId, "permission" => "RW"));
            $advDetails = count($advisorsPermissionDetails);
            if ($advDetails != 0) {
                foreach ($advisorsPermissionDetails as $advisors) {
                    $pervLeadAdv = $advisors['advisor_id'];
                    $updatePrevLeadPermission = array('permission' => 'RO');
                    $result = AdvisorClientRelated::model()->updateAll($updatePrevLeadPermission, 'user_id = "' . $userId . '" and advisor_id in (' . $pervLeadAdv . ')');
                }
            }
        }
        $updateUserAdvisor = array('permission' => $updatedPermission);
        ##Updating consumervsadvisor table
        $result = AdvisorClientRelated::model()->updateAll($updateUserAdvisor, 'user_id = "' . $userId . '" and advisor_id in (' . $advisorId . ')');
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "You have updated the permission.", "permission" => $updatedPermission)));
    }

    /*
     * * Advisor accept the client request Set Status 1.
     */

    public function actionConnectionRequest() {

        $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
        $firstname = Yii::app()->getSession()->get('wsadvisor')->firstname;
        $lastname = Yii::app()->getSession()->get('wsadvisor')->lastname;
        $clientId = Yii::app()->request->getParam('id');
        $clientDetails = User::model()->findByPk($clientId);
        $email = $clientDetails->email;
        $acceptedDate = date('Y-m-d');
        if (empty($advisorId) || empty($clientId)) {
            return;
        }
        $userAdvisorObject = AdvisorClientRelated::model()->findBySql("SELECT permission FROM consumervsadvisor WHERE user_id=:user_id AND advisor_id=:advisor_id", array("user_id" => $clientId, "advisor_id" => $advisorId));
        $update = array('dateconnect' => $acceptedDate, 'status' => '1');
        $result = AdvisorClientRelated::model()->updateAll($update, 'user_id = "' . $clientId . '" and advisor_id in (' . $advisorId . ')');

        $part = 'connection-request-accepted';
        $email = new Email();
        $email->subject = 'Your connection request was accepted';
        $email->recipient['email'] = $clientDetails->email;
        $email->data[$part] = [
            'advisor-name' => "{$firstname} {$lastname}",
        ];
        $email->data['unsubscribe'] = true;
        $email->data['recipient_type'] = 'us';
        $email->data['unsubscribe_code'] = $clientDetails->unsubscribecode;
        $email->send();

        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "message" => "You have accepted the connection request.", "date" => $acceptedDate, "status" => 1, "permission" => $userAdvisorObject['permission'])));
    }

    /*
     *
     * Advisor Designation
     */

    public function actionAdvDesignationVerification() {

        $unvarify_designations = Yii::app()->request->getParam('unvarify_designations');
        $varify_designations = Yii::app()->request->getParam('varify_designations');
        $adv_id = Yii::app()->request->getParam('adv_id');
        $designation = new Designation;
        $advisor = Advisor::model()->findByPk($adv_id);
        $advisor->verified = 1;
        $advisor->save();

        /* Unverifying credentials  */
        if (!empty($varify_designations)) {
            $varify_designation = '';
            $varify_arr = explode(',', $varify_designations);
            foreach ($varify_arr as $key => $value) {
                $varify_designation .="'" . $value . "',";
            }
            $varify_designation = trim($varify_designation, ',');
            $statusArray = array('status' => 1);
            $user = $designation->updateAll($statusArray, 'advisor_id = "' . $adv_id . '" and desig_name in (' . $varify_designation . ')');
        }
        /* Verifying credentials  */
        if (!empty($unvarify_designations)) {
            $unvarify_arr = explode(',', $unvarify_designations);
            $unvarify_designation = '';
            foreach ($unvarify_arr as $key => $value) {
                $unvarify_designation .="'" . $value . "',";
            }
            $unvarify_designation = trim($unvarify_designation, ',');
            $statusArray = array('status' => 0);
            $user = $designation->updateAll($statusArray, 'advisor_id = "' . $adv_id . '" and desig_name in (' . $unvarify_designation . ')');
        }

        $this->sendResponse(200, CJSON::encode(array("status" => "OK", "userdata" => $user, "msg" => 'Credentials updated successfully.')));
    }

    /**
     * actionSignup() is used by advisor to register.
     */
    public function actionSignup() {

        $email = Yii::app()->request->getParam('email');
        $password = Yii::app()->request->getParam('password');
        if (!isset($email) || empty($email) || !isset($password) || empty($password)) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Mandatory fields left blank.')));
            return;
        }

        $userObject = Advisor::model()->findBySql("SELECT id,isactive FROM advisor WHERE email=:email", array("email" => $email));
        if ($userObject && $userObject->isactive != Advisor::USER_IS_INACTIVE) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'type' => 'email', 'message' => 'A user already exists with this email address.')));
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

        if (!isset($userObject)) {
            $userObject = new Advisor();
        }

        $userObject->isactive = 1;
        $userObject->roleid = Advisor::USER_IS_ADVISOR;
        $userObject->verificationcode = md5($userObject->email . $userObject->roleid);
        $userObject->requestinvitetokenkey = 'SUCCESS';
        $userObject->email = $email;
        $hasher = PasswordHasher::factory();
        $userObject->password = $hasher->HashPassword($password);
        unset($hasher);

        // create a random unsubscribe code
        $unsubscribecodeGenerator = PasswordGenerator::factory();
        $randomUnsubscribecode = $unsubscribecodeGenerator->generate();
        unset($unsubscribecodeGenerator);
        $codehasher = PasswordHasher::factory();
        $userObject->unsubscribecode = $codehasher->HashPassword($randomUnsubscribecode);
        unset($codehasher);

        if ($userObject->save()) {
            $advisorInfo = new Advisorpersonalinfo();
            $advisorInfo->advisor_id = $userObject->id;

            $advisorInfo->save();
            ## Code to send mail
            $part = 'advisor-registration';
            $welcomeEmail = new Email();
            $welcomeEmail->subject = 'Welcome to FlexScore';
            $welcomeEmail->recipient['email'] = $userObject->email;
            $welcomeEmail->data[$part] = [
                'token' => $userObject->verificationcode,
            ];
            $welcomeEmail->send();

            ## Code end to send mail
            $wsuser = new stdClass();
            $wsuser->id = $userObject->id;
            $wsuser->email = $userObject->email;
            $wsuser->roleid = $userObject->roleid;
            $wsuser->firstname = $userObject->firstname;
            $wsuser->lastname = $userObject->lastname;
            Yii::app()->getSession()->add('wsadvisor', $wsuser);

            ##notification information
            $message = array(0 => 'Please provide an image to complete your public profile.',
                1 => 'Please provide your information needed for your public profile.');
            $context = array(0 => 'Image Upload', 1 => 'Complete Your Profile');
            $template = array(0 => 'photo', 1 => 'about');
            for ($i = 0; $i < 2; $i++) {
                $createdDate = date('D M d Y H:i:s') . " UTC";
                $notificationInfo = new AdvisorNotification();
                $notificationInfo->advisor_id = $userObject->id;
                $notificationInfo->message = $message[$i];
                $notificationInfo->context = $context[$i];
                $notificationInfo->template = $template[$i];
                $notificationInfo->status = 0;
                $notificationInfo->lastmodified = date("Y-m-d H:i:s");
                $notificationInfo->save();
            }
            $notiCount = 2;
            $wsArrayToSend = array(
                "status" => 'OK',
                "id" => Yii::app()->getSession()->get('wsadvisor')->id,
                "email" => Yii::app()->getSession()->get('wsadvisor')->email,
                "urole" => Yii::app()->getSession()->get('wsadvisor')->roleid,
                "sess" => Yii::app()->session->sessionID,
                "notification" => $notiCount,
                "type" => 'advsignup',
                "uniquehash" => md5(Yii::app()->getSession()->get('wsadvisor')->email),
            );

            $UserControllerObj = new UserController(1);
            $UserControllerObj->setSubscriptionStatus("Subscribe");

            $this->sendResponse(200, CJSON::encode($wsArrayToSend));
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Unable to add advisor')));
        }
    }

    /**
     * Used in User verification account
     */
    public function actionAdvisorverify() {
        if (isset($_GET['code']) && $_GET['code'] != "") {
            $verificationCode = $_GET['code'];
            $advisorObject = new Advisor();
            $userDetails = $advisorObject->find("verificationcode = :vcode", array(':vcode' => $verificationCode));
            //save the values to database
            if (isset($userDetails)) {
                // data is valid activate the user
                $userDetails->isactive = 1;
                $userDetails->update("id = :advisor_id", array(':advisor_id' => $advisorObject->id));
                $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => 'User verified sucessfully')));
            } else {
                // data is invalid. call getErrors() to retrieve error messages
                $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Unable to verify user')));
            }
            unset($advisorObject);
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'Verification code is required')));
        }
    }

    /**
     * Advisor personal information
     */
    public function actionadvisorStepTwoDetails() {

        $wsUserObject = Yii::app()->getSession()->get('wsadvisor');
        $userId = Yii::app()->getSession()->get('wsadvisor')->id;
        ##Advisor Personal Info Model
        $advisorInfo = new Advisorpersonalinfo();
        $advisorInfo->advisor_id = $userId;
        $advisorInfo->firstname = Yii::app()->request->getParam('firstname');
        $advisorInfo->lastname = Yii::app()->request->getParam('lastname');

        $wsUserObject->firstname = $advisorInfo->firstname;
        $wsUserObject->lastname = $advisorInfo->lastname;

        $advisorInfo->advisortype = Yii::app()->request->getParam('advisortype');
        $advisorInfo->firmname = Yii::app()->request->getParam('firmname');
        $advisorInfo->description = Yii::app()->request->getParam('description');
        $advisorInfo->description = trim($advisorInfo->description);
        $advisorInfo->avgacntbalanceperclnt = Yii::app()->request->getParam('avg_bal');
        $advisorInfo->avgacntbalanceperclnt = str_replace(",", "", $advisorInfo->avgacntbalanceperclnt);
        $advisorInfo->minasstsforpersclient = Yii::app()->request->getParam('min_assist');
        $advisorInfo->minasstsforpersclient = str_replace(",", "", $advisorInfo->minasstsforpersclient);
        $advisorInfo->individualcrd = Yii::app()->request->getParam('individualCrd');
        $advisorInfo->individualcrd = str_replace(",", "", $advisorInfo->individualcrd);
        $advisorInfo->typeofcharge = serialize(Yii::app()->request->getParam('chargetype'));
        ##Values to update.

        $updateProfile = array('firstname' => $advisorInfo->firstname, 'lastname' => $advisorInfo->lastname, 'advisortype' => $advisorInfo->advisortype, 'firmname' => $advisorInfo->firmname, 'description' => $advisorInfo->description, 'typeofcharge' => $advisorInfo->typeofcharge, 'minasstsforpersclient' => $advisorInfo->minasstsforpersclient, 'avgacntbalanceperclnt' => $advisorInfo->avgacntbalanceperclnt, 'individualcrd' => $advisorInfo->individualcrd);
        ##Update information in User Table
        $result = Advisor::model()->updateAll($updateProfile, "id = '" . $userId . "'");
        ##Update information in AdvisorPersonalInfo Table
        $resultAdvisor = Advisorpersonalinfo::model()->updateAll($updateProfile, "advisor_id = '" . $userId . "'");


        $states = yii::app()->request->getParam('state');
        $states = explode(',', $states);
        if (!$states[0] == '') {
            $this->saveAdvisorStates($states, $userId);
        }
        ##Designations and Other Designations
        $designations = yii::app()->request->getParam('designation');
        $designations = explode(',', $designations);

        if (!empty($designations)) {
            if (in_array('multiselect-all', $designations)) {
                $pos = array_search('multiselect-all', $designations);
                unset($designations[$pos]);
            }

            $updatedOtherDesignations = Yii::app()->request->getParam('extradesigvalue');
            $updatedOtherDesignations = explode(',', $updatedOtherDesignations);
            $count = 0;

            //Check if space exists in substrings
            foreach ($updatedOtherDesignations as $explode_tag) {
                if (strpos(trim($explode_tag), ' ') == false) { //strpos better for checking existance
                    $count++;
                }
            }

            $advisorExistingDesignations = $this->getAdvisorDesignations($userId);
            $total_designations = count($advisorExistingDesignations);
            $existingFlexScoreDesignstions = array();
            $existingOtherDesignstions = array();
            $existingDesignations = array();
            foreach ($advisorExistingDesignations as $designation) {
                if ($designation['other'] == 0) {
                    $existingFlexScoreDesignstions[] = $designation['desig_name'];
                } else {
                    $existingOtherDesignstions[] = $designation['desig_name'];
                }
                $existingDesignations[] = $designation['desig_name'];
            }

            $advisorStatus = Designation::model()->findAllByAttributes(array('advisor_id' => $userId, 'deleted' => 1));

            $existingDesig_deleted = array();
            foreach ($advisorStatus as $designation) {
                $existingDesig_deleted[] = $designation['desig_name'];
            }
            for ($i = 0; $i < count($existingDesig_deleted); $i++) {
                if (in_array($existingDesig_deleted[$i], $designations)) {
                    $updateStatusZero = array('deleted' => 0);
                    foreach ($existingDesig_deleted as $designation) {
                        $update = Designation::model()->updateAll($updateStatusZero, "advisor_id = '" . $userId . "' AND desig_name in ('" . $designation . "')");
                    }
                }
            }

            $updatedDesignations = array_merge($designations, $updatedOtherDesignations);
            $newDesignations = array_diff($updatedDesignations, $existingDesignations);
            $otherDesignation = 0;

            foreach ($newDesignations as $designation) {

                if ($designation == 'Other') {
                    $otherDesignation = 1;
                    continue;
                }
                if (!empty($designation)) {
                    $designationObject = new Designation;
                    $designationObject->advisor_id = $userId;
                    $designationObject->desig_name = $designation;
                    $designationObject->other = $otherDesignation;
                    $designationObject->save();
                }
            }
            $removeDesignations = array_diff($existingDesignations, $updatedDesignations);
            $updateStatusOne = array('deleted' => 1);
            foreach ($removeDesignations as $designation) {
                $update = Designation::model()->updateAll($updateStatusOne, "advisor_id = '" . $userId . "' AND desig_name in ('" . $designation . "')");
            }
        }

        ##PNS and Other PNS
        $products = yii::app()->request->getParam('productservice');
        $updatedAdvisorPNS = explode(',', $products);
        if (!empty($updatedAdvisorPNS)) {
            if (in_array('multiselect-all', $updatedAdvisorPNS)) {
                $pos = array_search('multiselect-all', $updatedAdvisorPNS);
                unset($updatedAdvisorPNS[$pos]);
            }
            $newOtherPNS = Yii::app()->request->getParam('extraprod');
            $newOtherPNS = explode(',', $newOtherPNS);
            ##Check if space exists in substrings
            $count = 0;
            foreach ($newOtherPNS as $explode_tag) {
                if (strpos(trim($explode_tag), ' ') == false) { //strpos better for checking existance
                    $count++;
                }
            }

            $updatedPNS = array_merge($updatedAdvisorPNS, $newOtherPNS);
            $existingProductService = $this->advisorProductService($userId);
            $existingFlexScorePNS = array();
            $existingOtherPNS = array();
            $existingPNS = array();
            foreach ($existingProductService as $PNS) {

                if ($PNS['other'] == 0) {
                    $existingFlexScorePNS[] = $PNS['productserviceid'];
                } else {
                    $existingOtherPNS[] = $PNS['productserviceid'];
                }
                $existingPNS[] = $PNS['productserviceid'];
            }

            $newPNS = array_diff($updatedPNS, $existingPNS);
            $otherPNS = 0;

            foreach ($newPNS as $PNS) {

                if ($PNS == 'Other') {
                    $otherPNS = 1;
                    continue;
                }
                if (!empty($PNS)) {
                    $PNSObject = new Productservice;
                    $PNSObject->advisor_id = $userId;
                    $PNSObject->other = $otherPNS;
                    $PNSObject->productserviceid = $PNS;
                    $PNSObject->save();
                }
            }
            $removePNS = array_diff($existingPNS, $updatedPNS);

            foreach ($removePNS as $PNS) {
                $productServiceDeleted = Productservice::model()->deleteAll("advisor_id = '" . $userId . "' AND  productserviceid in ('" . $PNS . "')");
            }

            //$this->saveProductService($products, $userId);
        }

        // for the Save and Close button on the createAccountStepOne form
        $showSubscription = true;
        $subscriptionCheck = AdvisorSubscription::model()->find(array('condition' => 'advisor_id=:aid', 'params' => array('aid' => $userId),
            'select' => array('advisor_id')));
        if (!$subscriptionCheck) {
            $showSubscription = "true";
        }


        ## Complete Profile Notification Check.
        $updates = array('status' => 1, 'lastmodified' => date("Y-m-d H:i:s"));
        if (empty($advisorInfo->firstname) || empty($advisorInfo->lastname) || empty($advisorInfo->advisortype) || empty($advisorInfo->firmname) || empty($advisorInfo->description) || empty($advisorInfo->typeofcharge) || empty($advisorInfo->minasstsforpersclient) || empty($advisorInfo->avgacntbalanceperclnt) || empty($advisorInfo->individualcrd) || empty($products)) {
            $updates = array('status' => 0, 'lastmodified' => date("Y-m-d H:i:s"));
        }
        $result = AdvisorNotification::model()->updateAll($updates, 'advisor_id = "' . $userId . '" and template in ("about")');
        $notificationCount = AdvisorNotification::model()->count("advisor_id = :advisor_id and status=0", array(':advisor_id' => $userId));
        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'notificationCount' => $notificationCount,
                    'showSubscription' => $showSubscription)));
    }

    private function getSignUpToken($tokenValue = null) {

        if (empty($tokenValue)) {
            return false;
        }

        try {
            return Accesstoken::model()->findBySql("SELECT * FROM accesstoken WHERE accesstoken=:token", array("token" => $tokenValue));
        } catch (Exception $e) {

            return false;
        }
    }

    //function changed
    private function saveDesignations($designations = null, $user_id = null) {

        $userExtraDesignation = Yii::app()->request->getParam('extradesig');
        if (in_array('Other', $designations) && count($designations) > 0) {
            $pos = array_search('Other', $designations);
            unset($designations[$pos]);
        }

        $otherDesignation = explode(",", $userExtraDesignation);
        $designations = array_merge($designations, $otherDesignation);

        if (!is_array($designations) || empty($user_id)) {
            return false;
        }

        $advisorExistingDesignations = $this->getAdvisorDesignations($user_id);

        $total_designations = count($advisorExistingDesignations);
        $existingDesignations = array();
        foreach ($advisorExistingDesignations as $designation) {
            if ($designation['other'] == 0) {
                $existingFlexScoreDesignstions[] = $designation['desig_name'];
            } else {
                $existingOtherDesignstions[] = $designation['desig_name'];
            }
            $existingDesignations[] = $designation['desig_name'];
        }
        $saveDesignation = array_diff($designations, $existingDesignations);

        foreach ($saveDesignation as $designation) {
            if (!empty($designation)) {
                $designationsObj = new Designation();
                $designationsObj->status = 0;
                $designationsObj->advisor_id = $user_id;
                $designationsObj->desig_name = $designation;
                if (in_array($designationsObj->desig_name, $otherDesignation)) {
                    $designationsObj->other = 1;
                }

                $designationsObj->save();
            }
        }
        return true;
    }

    private function saveAdvisorStates($states = null, $advisor_id = null) {

        if (!is_array($states) || empty($advisor_id)) {
            return false;
        }

        if (in_array('multiselect-all', $states) && count($states) > 0) {
            $pos = array_search('multiselect-all', $states);
            unset($states[$pos]);
        }
        $savestates = array_values($states);
        $savestates = array_combine(range(1, count($savestates)), $savestates);
        if (!empty($savestates)) {
            foreach ($savestates as $stateId) {

                $stateModel = new State();
                $stateModel->advisor_id = $advisor_id;
                $stateModel->stateregistered = $stateId;
                $stateModel->save();
            }
        }
        return true;
    }

    //function changed
    private function saveProductService($products = null, $user_id = null) {

        $userExtraProduct = Yii::app()->request->getParam('extraprod');
        if (!is_array($products) || empty($user_id)) {
            return false;
        }
        if (in_array('Other', $products)) {
            $pos = array_search('Other', $products);
            unset($products[$pos]);
        }
        $otherProduct = explode(",", $userExtraProduct);
        $products = array_merge($products, $otherProduct);

        $existingProductService = $this->advisorProductService($user_id);
        $existingFlexScorePNS = array();
        $existingOtherPNS = array();
        $existingPNS = array();
        foreach ($existingProductService as $PNS) {

            if ($PNS['other'] == 0) {
                $existingFlexScorePNS[] = $PNS['productserviceid'];
            } else {
                $existingOtherPNS[] = $PNS['productserviceid'];
            }
            $existingPNS[] = $PNS['productserviceid'];
        }
        $saveDesignation = array_diff($products, $existingPNS);
        foreach ($saveDesignation as $product) {
            if (!empty($product)) {
                $productserviceObj = new Productservice();
                $productserviceObj->advisor_id = $user_id;
                $productserviceObj->productserviceid = $product;
                if (in_array($productserviceObj->productserviceid, $otherProduct)) {
                    $productserviceObj->other = 1;
                }
                $productserviceObj->save();
            }
        }
        return true;
    }

    private function getStatesInRegistered($advisorId) {
        $advisorStates = State::model()->findAllByAttributes(array('advisor_id' => $advisorId));
        $statesId = array();
        foreach ($advisorStates as $advisorState) {
            $statesId[] = $advisorState->stateregistered;
        }
        $states = State::model()->states_name;
        $stateName = array();
        foreach ($statesId as $state) {
            if ($state > 0)
                $stateName[] = $states[$state];
        }


        if (empty($stateName))
            $stateName[] = array('name' => 'Not Registered in any state.');

        return $stateName;
    }

    public function getAllStates($advisorId) {

        $advisorStates = array();
        $advisorStates = State::model()->findAllBySql("SELECT stateregistered FROM advisorstates WHERE advisor_id=:advisor_id", array("advisor_id" => $advisorId));
        $statesId = array();

        foreach ($advisorStates as $advisorState) {

            $statesId[] = $advisorState['stateregistered'];
        }

        $states = State::model()->states_name;
        $allState = array();
        $loop = 0;
        foreach ($states as $state) {
            $allState[$loop]['name'] = $state['name'];
            $allState[$loop]['id'] = $state['id'];
            $allState[$loop]['title'] = $state['title'];
            if (in_array($state['id'], $statesId)) {
                $allState[$loop]['status'] = '1';
            } else {
                $allState[$loop]['status'] = '0';
            }
            $loop++;
        }

        $allState = $this->arraySortTwoDimByValueKey($statesId, $states);

        return $allState;
    }

    function arraySortTwoDimByValueKey($selectedStates, $allStates) {
        $new_selected_states = array();
        foreach ($allStates as $state => $values) {
            if (in_array($values['id'], $selectedStates)) {
                $new_selected_states[$state]['name'] = $values['name'];
                $new_selected_states[$state]['title'] = $values['title'];
                $new_selected_states[$state]['id'] = $values['id'];
                $new_selected_states[$state]['status'] = '1';
                unset($allStates[$state]);
            }
        }

        $states = array_merge($new_selected_states, $allStates);
        return $states;
    }

    //function changed
    private function getAllProductServiceOfAdvisor($advisorId) {
        $advisorProducts = Productservice::model()->findAllByAttributes(array('advisor_id' => $advisorId));
        $others = array();
        $productsId = array();
        foreach ($advisorProducts as $advisorProduct) {
            $productsId[] = $advisorProduct->productserviceid;
            if ($advisorProduct['other'] == 1)
                $others[] = $advisorProduct['productserviceid'];
        }
        $others = implode(',', $others);
        $productService = Productservice::model()->productservicename;
        $productServiceName = array();
        $loop = 0;
        foreach ($productService as $key => $product) {
            $productServiceName[$loop]['name'] = $productService[$key];
            $productServiceName[$loop]['id'] = $key;
            if (in_array($key, $productsId))
                $productServiceName[$loop]['status'] = 1;
            $loop++;
        }
        if (empty($productServiceName))
            $productServiceName[] = 'No product and service.';
        $productServiceName[$loop - 1]['others'] = $others;
        return $productServiceName;
    }

    private function getAllProductService() {
        $productService = Productservice::model()->productservicename;

        return $productService;
    }

    private function getProductNService($advisorId) {
        $advisorProducts = Productservice::model()->findAllByAttributes(array('advisor_id' => $advisorId));
        $productsId = array();
        foreach ($advisorProducts as $advisorProduct) {
            $productsId[] = $advisorProduct->productserviceid;
        }
        $advPNS = implode(', ', $productsId);
        return $advPNS;
    }

    //function not found
    private function getProductService($advisorId) {
        $advisorProducts = Productservice::model()->findAllByAttributes(array('advisor_id' => $advisorId));
        $productsId = array();
        foreach ($advisorProducts as $advisorProduct) {
            $productsId[] = $advisorProduct->productserviceid;
        }
        $productService = Productservice::model()->productservicename;
        $productServiceName = array();
        $loop = 0;

        foreach ($productsId as $product) {

            $productServiceName[$loop]['name'] = $product;
            //$productServiceName[$loop]['id'] = $product;
            $loop++;
        }

        if (empty($productServiceName))
            $productServiceName[] = array('name' => 'No product and service.');
        return $productServiceName;
    }

    public function actionGetProfile() {

        // Get User Information
        $createdBy = "";
        $userEmail = "";
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $userEmail = Yii::app()->getSession()->get('wsuser')->email;
            $userId = Yii::app()->getSession()->get('wsuser')->id;
            $user = User::model()->findBySql("SELECT createdby FROM user WHERE id=:user_id", array("user_id" => $userId));
            if (isset($user)) {
                $createdBy = $user->createdby;
            }
        }

        // Check Params and retrieve advisor
        $advisorHash = Yii::app()->request->getParam('adv_hash');
        $assigneeId = Yii::app()->request->getParam('assignee_id');
        if (empty($advisorHash)) {
            if (!isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            $advisorHash = md5('AdvisorHash' . Yii::app()->getSession()->get('wsadvisor')->id);
        }

        $advisorInfo = Advisor::model()->find("md5(concat('AdvisorHash',id)) =:advhash and isactive='1'", array("advhash" => $advisorHash));
        if (!$advisorInfo || (isset(Yii::app()->getSession()->get('wsadvisor')->id) && $advisorInfo->id != Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "User does not have permissions to view this advisor.")));
        }
        $advisorId = $advisorInfo->id;

        $advisor = Advisorpersonalinfo::model()->with('adv')->findByAttributes(array('advisor_id' => $advisorId));
        if (!$advisor) {
            $advisor = new Advisorpersonalinfo();
        }
        if (!file_exists('../' . $advisor['profilepic']) OR $advisor['profilepic'] == '') {
            $advisor['profilepic'] = 'ui/images/advisor_default.png';
        }
        $designationList = $this->getSelectedDesignations($advisorId);
        $advisor->designation = $designationList['verified'];
        $advisor->stateregistered = $this->getStatesInRegistered($advisorId);
        $advisor->productservice = $this->getProductService($advisorId);
        $advisorStatus = Adminadvisor::model()->findByAttributes(array('advisor_id' => $advisorId));
        $isAssigned = isset($advisorStatus->id) ? 1 : 0;
        if (empty($advisor->typeofcharge)) {
            $advisor->typeofcharge = array('0' => array('type' => 'N/A'));
        } else {
            $chargeType = array();
            $charge_arr = unserialize($advisor->typeofcharge);
            if (is_array($charge_arr) && !empty($charge_arr)) {
                foreach ($charge_arr as $key => $value) {
                    $chargeType[$key]['type'] = $value;
                }
            } else {
                $chargeType = array('0' => array('type' => 'N/A'));
            }
            $advisor->typeofcharge = $chargeType;
        }
        if ($advisor->advisortype == '') {
            $advisor->advisortype = 'N/A';
        }

        if ($advisor->minasstsforpersclient == '') {
            $advisor->minasstsforpersclient = 0;
        }

        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $loggedinuser = Yii::app()->getSession()->get('wsuser')->id;
        } else {
            $loggedinuser = "";
        }

        if ($loggedinuser == $assigneeId) {
            $advisor['unassign'] = '1';
        } else {
            $advisor['unassign'] = '0';
        }
        $advisor['advhash'] = $advisorHash;

        $connectedStatus = "";
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $connectedStatus = $this->currentStatus($advisorId);
        }
        if (!empty($advisor)) {
            $this->sendResponse(200, json_encode(array("status" => "OK", "userdata" => $advisor, 'assign' => $isAssigned, 'userEmail' => $userEmail, 'connection' => $connectedStatus, 'assigneeId' => $assigneeId, 'loggedinAdv' => $loggedinuser, 'loggedin_user_created_by' => $createdBy)));
        }
    }

    public function actionViewProfile() {
        $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
        $advisor = Advisorpersonalinfo::model()->with('adv')->findByAttributes(array('advisor_id' => $advisorId));
        if (!$advisor) {
            $advisor = new Advisorpersonalinfo();
        }
        if (!file_exists('../' . $advisor['profilepic']) OR $advisor['profilepic'] == '') {
            $advisor['profilepic'] = 'ui/images/advisor_default.png';
        }
        $designationList = $this->getSelectedDesignations($advisorId);
        $advisor->designation = $designationList['verified'];
        $advisor->stateregistered = $this->getStatesInRegistered($advisorId);
        $advisor->productservice = $this->getProductService($advisorId);
        $advisorStatus = Adminadvisor::model()->findByAttributes(array('advisor_id' => $advisorId));
        $isAssigned = isset($advisorStatus->id) ? 1 : 0;
        if (empty($advisor->typeofcharge)) {
            $advisor->typeofcharge = array('0' => array('type' => 'N/A'));
        } else {
            $chargeType = array();
            $charge_arr = unserialize($advisor->typeofcharge);
            if (is_array($charge_arr) && !empty($charge_arr)) {
                foreach ($charge_arr as $key => $value) {
                    $chargeType[$key]['type'] = $value;
                }
            } else {
                $chargeType = array('0' => array('type' => 'N/A'));
            }
            $advisor->typeofcharge = $chargeType;
        }
        if ($advisor->advisortype == '') {
            $advisor->advisortype = 'N/A';
        }

        if ($advisor->minasstsforpersclient == '') {
            $advisor->minasstsforpersclient = 0;
        }

        if (!empty($advisor)) {
            $advisor['advhash'] = md5('AdvisorHash' . $advisor->advisor_id);
            $this->sendResponse(200, json_encode(array("status" => "OK", "userdata" => $advisor, 'assign' => $isAssigned)));
        }
    }

    public function actionRevokeAdvisor() {
        $advisorId = Yii::app()->request->getParam('adv_id');
        $statusArray = array('isactive' => '1');
        $result = Advisor::model()->updateByPk($advisorId, $statusArray);
        if ($result)
            $this->sendResponse(200, CJSON::encode(array("status" => "OK")));
        else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'msg' => 'Advisor can not revoke....Please try later.')));
        }
    }

    public function actionUploadprofilepic() {
        if (isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
            // create the hash for advisorid //
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
            $profileImgPath = $folderPath . '/ui/usercontent/advisor/' . $advidhashvalue . '/';
            $userfolder = "advisor";
        } else {
            $advisorId = Yii::app()->getSession()->get('wsuser')->id;
            // create the hash for user_id //
            $hasher = PasswordHasher::factory();
            $userHashObj = User::model()->find(array('condition' => "id = :user_id", 'params' => array("user_id" => $advisorId), 'select' => 'uidhashvalue'));
            if ($userHashObj) {
                if ($userHashObj->uidhashvalue == "") {
                    $userHashObj->uidhashvalue = str_replace("/", "", $hasher->HashPassword($advisorId));
                    User::model()->updateByPk($advisorId, array('uidhashvalue' => $userHashObj->uidhashvalue));
                    $advidhashvalue = $userHashObj->uidhashvalue;
                } else {
                    $advidhashvalue = $userHashObj->uidhashvalue;
                }
            }
            unset($hasher);
            $folderPath = realpath(dirname(__FILE__) . '/../..');
            if (is_dir($folderPath . '/ui/usercontent/user/' . $advisorId . '/')) {
                rename($folderPath . '/ui/usercontent/user/' . $advisorId, $folderPath . '/ui/usercontent/user/' . $advidhashvalue);
            } else if (!is_dir($folderPath . '/ui/usercontent/user/' . $advisorId . '/') && !is_dir($folderPath . '/ui/usercontent/user/' . $advidhashvalue . '/')) {
                mkdir($folderPath . '/ui/usercontent/user/' . $advidhashvalue . '/');
            }
            $profileImgPath = $folderPath . '/ui/usercontent/user/' . $advidhashvalue . '/';
            $userfolder = "user";
        }



        $filecheck = basename($_FILES['profile_pic']['name']);
        $ext = strtolower(substr($filecheck, strrpos($filecheck, '.') + 1));
        if (($ext == "jpg" || $ext == "gif" || $ext == "png") && ($_FILES["profile_pic"]["size"] < 500000)) {
            $profilepicName = time() . '.' . $ext;
            $profilePicPath = $profileImgPath . $profilepicName;
            move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $profilePicPath);
            // Code to resize the image
            list($originalWidth, $originalHeight) = getimagesize($profilePicPath);
            $targetWidth = $originalWidth;
            $targetHeight = $originalHeight;
            if ($originalWidth > 500 || $originalHeight > 500) {
                $ratio = $originalWidth / $originalHeight;
                $targetWidth = $targetHeight = min(500, max($originalWidth, $originalHeight));
                if ($ratio < 1) {
                    $targetWidth = $targetHeight * $ratio;
                } else {
                    $targetHeight = $targetWidth / $ratio;
                }
                $srcWidth = $originalWidth;
                $srcHeight = $originalHeight;
                $srcX = $srcY = 0;
                $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);
                $originalImage = imagecreatefromstring(file_get_contents($profilePicPath));
                imagecopyresampled($targetImage, $originalImage, 0, 0, $srcX, $srcY, $targetWidth, $targetHeight, $srcWidth, $srcHeight);
                switch ($ext) {
                    case 'png':
                        imagepng($targetImage, $profilePicPath, 9);
                        break;

                    case 'jpg' :
                    case 'jpeg' :
                        imagejpeg($targetImage, $profilePicPath, 100);
                        break;

                    case 'gif':
                        imagegif($targetImage, $profilePicPath);
                        break;
                }
            }

            //End to resize
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'pic' => 'ui/usercontent/'.$userfolder.'/' . $advidhashvalue . '/'.$profilepicName, 'height' => $targetHeight, 'width' => $targetWidth)), 'text/html');
        } elseif ($_FILES["profile_pic"]["size"] > 500000) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'type' => 'profilepic', 'message' => 'Uploaded image size should be less than 500Kb.')), 'text/html');
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'type' => 'profilepic', 'message' => 'Allowed image type should be .gif, .jpeg, .jpg, .png.')), 'text/html');
        }
    }

    private function getSearchQuery($userState, $fname = null, $called_for = null) {
        //calculate liquid assets//
        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $assetObj = new Assets();
        $insuranceObj = new Insurance();

        $assetsTotal = 0;
        $assets = $assetObj->findBySql("select sum(balance) as total from assets where type in ('BANK','IRA', 'CR', 'BROK','EDUC') and status=0 and user_id=:user_id", array("user_id" => $user_id));
        if (isset($assets)) {
            $assetsTotal = $assets->total;
        }

        $totalInsCashValue = 0;
        $insCashValue = $insuranceObj->findBySql("select sum(cashvalue) as total_cashvalue from insurance where type in ('LIFE') and status = 0 and user_id=:user_id and lifeinstype <> 64", array("user_id" => $user_id));
        if (isset($insCashValue)) {
            $totalInsCashValue = $insCashValue->total_cashvalue;
        }
        $liquid_assets = $assetsTotal + $totalInsCashValue;


        $query = Yii::app()->db->createCommand()
                ->select('aa.*, aa.advisor_id as advisor_id, consumer.status as connectedStatus')
                ->from('advisor ad')
                ->Join('advisorpersonalinfo aa', 'ad.id = aa.advisor_id')
                ->leftJoin('adv_designations desig', "desig.advisor_id = aa.advisor_id AND desig.status = 1")
                ->Join('advisorstates states', "states.advisor_id = aa.advisor_id AND states.stateregistered ='" . $userState . "'")
                ->leftJoin('consumervsadvisor consumer', 'consumer.advisor_id = aa.advisor_id')
                ->leftJoin('productandservice pns', 'pns.advisor_id = aa.advisor_id');

        if ($fname != null) {
            $query = $query->Where(" ad.isactive = '1' AND ad.verified = '1' AND ad.roleid = '999' AND aa.minasstsforpersclient <='" . $liquid_assets . "' AND (( CONCAT(aa.firstname,' ',aa.lastname) LIKE '%" . trim($fname) . "%' )|| desig.desig_name LIKE '%" . trim($fname) . "%' || pns.productserviceid LIKE '%" . trim($fname) . "%')");
        } else {
            $query = $query->Where("ad.isactive = '1' AND ad.verified = '1' AND ad.roleid = '999' AND aa.minasstsforpersclient <='" . $liquid_assets . "'");
        }

        $query = $query->group('aa.advisor_id')
                ->queryAll();

        return $query;
    }

    public function actionShowalladvisors($fname = null) {
        //calculate liquid assets//
        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $assetObj = new Assets();

        $insuranceObj = new Insurance();
        $totalAssets = $assetObj->findBySql("select sum(balance) as total from assets where type in ('BANK','IRA', 'CR', 'BROK','EDUC') and status=0 and user_id=:user_id", array("user_id" => $user_id));
        $totalInsCashValue = $insuranceObj->findBySql("select sum(cashvalue) as total_cashvalue from insurance where type in ('LIFE') and status = 0 and user_id=:user_id and lifeinstype <> 64", array("user_id" => $user_id));
        if (isset($totalAssets)) {
            $totalAssets = $totalAssets->total;
        } else {
            $educationalAssets = 0;
        }

        if (isset($totalInsCashValue)) {
            $totalInsCashValue = $totalInsCashValue->total_cashvalue;
        } else {
            $totalInsCashValue = 0;
        }
        $liquid_assets = $totalAssets + $totalInsCashValue;

        $userEmail = Yii::app()->getSession()->get('wsuser')->email;
        $allpns = $this->getAllProductService();
        $fname = Yii::app()->request->getParam('fname');
        $advisors = Yii::app()->db->createCommand()
                ->select('aa.*, aa.advisor_id as advisor_id, consumer.status as connectedStatus')
                ->from('advisor ad')
                ->Join('advisorpersonalinfo aa', 'ad.id = aa.advisor_id')
                ->leftJoin('adv_designations desig', "desig.advisor_id = aa.advisor_id AND desig.status = 1")
                ->leftJoin('consumervsadvisor consumer', 'consumer.advisor_id = aa.advisor_id')
                ->leftJoin('productandservice pns', 'pns.advisor_id = aa.advisor_id');
        if ($fname != null) {
            $advisors = $advisors->Where("ad.isactive = '1' AND ad.verified = '1' AND ad.roleid = '999' AND minasstsforpersclient <= '" . $liquid_assets . "' AND (( CONCAT(aa.firstname,' ',aa.lastname) LIKE '%" . trim($fname) . "%' || desig.desig_name LIKE '%" . trim($fname) . "%' || pns.productserviceid LIKE '%" . trim($fname) . "%'))");
        } else {
            $advisors = $advisors->Where("ad.isactive = '1' AND ad.verified = '1' AND ad.roleid = '999' AND minasstsforpersclient <= '" . $liquid_assets . "'");
        }

        $advisorList = $advisors->group('aa.advisor_id')->queryAll();
        $chkforprofile = array();
        foreach ($advisorList as $key => $advisor) {
            $advisorList[$key]['advisorhash'] = md5('AdvisorHash' . $advisor['advisor_id']);
            $designationList = $this->getDesignations($advisor['advisor_id']);
            $advisorList[$key]['designation'] = $designationList['verified'];
            $designationList = $this->getSelectedDesignations($advisor['advisor_id']);
            $advisorList[$key]['designationverify'] = $designationList['verified'];
            $advisorList[$key]['pns'] = $this->getProductService($advisor['advisor_id']);
            $advisorList[$key]['advpns'] = $this->getProductNService($advisor['advisor_id']);

            if (!file_exists('../' . $advisorList[$key]['profilepic']) OR $advisorList[$key]['profilepic'] == '')
                $advisorList[$key]['profilepic'] = 'ui/images/advisor_default.png';
            $advisorList[$key]['connectionstatus'] = $this->currentStatus($advisor['advisor_id']);
            $chkforprofile[] = $advisor['advisor_id'];
        }
        if (isset($chkforprofile) && count($chkforprofile) > 0) {
            $alladvid = implode(",", $chkforprofile);
        } else {
            $alladvid = 0;
        }

        $createdBy = "";
        $user = Yii::app()->db->createCommand()
                ->select('indemnification_check')
                ->from('user u')
                ->join('consumervsadvisor ca', 'u.createdby=ca.advisor_id and u.id=ca.user_id')
                ->where('ca.user_id=:id ', array(':id' => $user_id))
                ->queryAll();
        if (isset($user)) {
            foreach ($user as $value) {
                $indemnification_check = $value['indemnification_check'];
                if ($indemnification_check == 1) {
                    $createdBy = 'advisor';
                } else {
                    $createdBy = "";
                }
            }
        }
        $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'userdata' => $advisorList, 'total' => count($advisorList), 'pns' => $allpns, 'email' => $userEmail, 'showAll' => 'SET', 'user_liquid_assets' => $liquid_assets, 'loggedin_user_created_by' => $createdBy)));
    }

    public function actionSearchAdvisor() {

        $fname = Yii::app()->request->getParam('fname');
        $userId = Yii::app()->getSession()->get('wsuser')->id;

        $createdBy = "";
        $user = Yii::app()->db->createCommand()
                ->select('indemnification_check')
                ->from('user u')
                ->join('consumervsadvisor ca', 'u.createdby=ca.advisor_id and u.id=ca.user_id')
                ->where('ca.user_id=:id', array(':id' => $userId))
                ->queryAll();
        if (isset($user)) {
            foreach ($user as $value) {
                $indemnification_check = $value['indemnification_check'];
                if ($indemnification_check == 1) {
                    $createdBy = 'advisor';
                } else {
                    $createdBy = "";
                }
            }
        }

        $userZip = User::model()->findByAttributes(array('id' => $userId));
        $allStates = Regiondetails::model()->findAll();
        $userZipCode = trim($userZip['zip']);
        $verifyZipcode = strstr($userZipCode, '-', true);

        if (strlen($userZipCode) < 5 || strlen($verifyZipcode) < 5 && strlen($verifyZipcode) != 0) {
            $showAllAdvisors = $this->actionShowalladvisors();
        } else if (strlen($userZipCode) >= 6 && strlen($verifyZipcode) > 5) {
            $showAllAdvisors = $this->actionShowalladvisors($fname);
        } else {

            if (strlen($userZipCode) >= 6 && !strstr($userZipCode, '-')) {
                $userZiplength = $userZipCode[strlen($userZipCode) - 1];
                $showAllAdvisors = $this->actionShowalladvisors($fname);
            } else {
                $userZipCode = substr($userZipCode, 0, 6);
                if (strstr($userZipCode, '-')) {
                    $userZipCode = strstr($userZipCode, '-', true);
                }
                if (is_numeric($userZipCode)) {
                    $userZipCode = substr($userZipCode, 0, 3);
                } else {
                    $showAllAdvisors = $this->actionShowalladvisors($fname);
                }
            }
        }
        $stateName = '';
        foreach ($allStates as $key => $state) {
            $zipcodeRange = $state->zipcoderangeprefix;
            $range = array();
            if (strpos($zipcodeRange, '|') > 0) {
                $ranges = explode('|', $zipcodeRange);
                foreach ($ranges as $rangeLimit) {
                    if (strpos($rangeLimit, '-')) {
                        $range = explode('-', $rangeLimit);
                        if ($userZipCode >= $range[0] && $userZipCode <= $range[1]) {
                            $stateName = $state->state;
                            break;
                        }
                    } else {
                        if ($userZipCode == $rangeLimit) {
                            $stateName = $state->state;
                            break;
                        }
                    }
                }
            } else {
                if (strpos($zipcodeRange, '-') > 0) {
                    $range = explode('-', $zipcodeRange);
                    if ($userZipCode >= $range[0] && $userZipCode <= $range[1]) {
                        $stateName = $state->state;
                        break;
                    }
                } else {
                    if ($userZipCode == $zipcodeRange) {
                        $stateName = $state->state;
                        break;
                    }
                }
            }
        }

        // Code need to fetch state name
        $stateId = $this->searchForId($stateName);
        $userEmail = Yii::app()->getSession()->get('wsuser')->email;
        $allpns = $this->getAllProductService();
        if (!empty($stateId) && $stateId != null) {
            $advisorList = $this->getSearchQuery($stateId, $fname);
            if (!empty($advisorList) && $advisorList != null) {
                foreach ($advisorList as $key => $advisor) {
                    $advisorList[$key]['advisorhash'] = md5('AdvisorHash' . $advisor['advisor_id']);
                    $designationList = $this->getSelectedDesignations($advisor['advisor_id']);
                    $advisorList[$key]['designationverify'] = $designationList['verified'];
                    $advisorList[$key]['designation'] = explode(', ', $designationList['verified']);
                    $advisorList[$key]['pns'] = $this->getProductService($advisor['advisor_id']);
                    $advisorList[$key]['advpns'] = $this->getProductNService($advisor['advisor_id']);
                    $advisorList[$key]['valid'] = 'valid';
                    if (!file_exists('../' . $advisorList[$key]['profilepic']) OR $advisorList[$key]['profilepic'] == '')
                        $advisorList[$key]['profilepic'] = 'ui/images/advisor_default.png';
                    $advisorList[$key]['connectionstatus'] = $this->currentStatus($advisor['advisor_id']);
                }
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'userdata' => $advisorList, 'total' => count($advisorList), 'pns' => $allpns, 'email' => $userEmail, 'msg' => 'match', 'loggedin_user_created_by' => $createdBy)));
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'userdata' => array(), 'total' => 0, 'email' => $userEmail, 'pns' => $allpns, 'msg' => 'unmatch', 'loggedin_user_created_by' => $createdBy)));
            }
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'userdata' => array(), 'total' => 0, 'email' => $userEmail, 'pns' => $allpns, 'msg' => 'unmatch', 'loggedin_user_created_by' => $createdBy)));
        }
    }

    public function actionNotifySettings() {
        $notify = Yii::app()->request->getParam('notify');
        $user_id = Yii::app()->getSession()->get('wsadvisor')->id;
        $params = array('notify' => $notify);
        $update = Advisorpersonalinfo::model()->updateAll($params, "advisor_id = $user_id");
        $errors = Advisorpersonalinfo::model()->getErrors();
        if ($update OR empty($errors)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'msg' => 'Notification setting updated successfully')));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'msg' => 'Notification settings updation failure.')));
        }
    }

    public function actionGetNotifySettings() {
        if (isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
            $advisor = Advisorpersonalinfo::model()->with('adv')->findByAttributes(array('advisor_id' => $advisorId));
            if (!file_exists('../' . $advisor['profilepic']) OR $advisor['profilepic'] == '') {
                $advisor['profilepic'] = 'ui/images/advisor_default.png';
            }
            if (!empty($advisor)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'userdata' => $advisor)));
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'msg' => 'No advisor found')));
            }
        } else {
            $advisorId = Yii::app()->getSession()->get('wsuser')->id;
            $userPerDetails = Userpersonalinfo::model()->find("user_id = :user_id", array(':user_id' => $advisorId));
            $userpic = str_replace("https://www.flexscore.com", "", $userPerDetails['userpic']);
            if (!file_exists('../' . $userpic) OR $userpic == '') {
                $userPerDetails['userpic'] = 'ui/images/genericAvatar.png';
            }
            if (!empty($userPerDetails)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'userdata' => $userPerDetails)));
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'msg' => 'No user found')));
            }
        }
    }

    public function actionCropPhoto() {
        $height = Yii::app()->request->getParam('height');
        $width = Yii::app()->request->getParam('width');
        $x_axis = Yii::app()->request->getParam('x_axis');
        $y_axis = Yii::app()->request->getParam('y_axis');
        $src = Yii::app()->request->getParam('src');
        $src = '../' . $src;
        $targ_w = $width;
        $targ_h = $height;
        $ext = substr($src, strrpos($src, '.') + 1);
        $imageName = substr($src, 0, strrpos($src, '.'));
        if ($height > 0 && $width > 0 && $x_axis > 0 && $y_axis > 0) {
            $imageName = $imageName . '' . time() . ".$ext";
            switch (strtolower($ext)) {
                case 'png':
                    $img_r = imagecreatefromstring(file_get_contents($src));
                    $dst_r = ImageCreateTrueColor($targ_w, $targ_h);
                    imagecopyresampled($dst_r, $img_r, 0, 0, $x_axis, $y_axis, $targ_w, $targ_h, $width, $height);
                    imagepng($dst_r, $imageName, 9);
                    break;
                case 'jpg' :
                case 'jpeg' :
                    $img_r = imagecreatefromstring(file_get_contents($src));
                    $dst_r = ImageCreateTrueColor($targ_w, $targ_h);
                    imagecopyresampled($dst_r, $img_r, 0, 0, $x_axis, $y_axis, $targ_w, $targ_h, $width, $height);
                    imagejpeg($dst_r, $imageName, 100);
                    break;
                case 'gif':
                    $img_r = imagecreatefromstring(file_get_contents($src));
                    $dst_r = ImageCreateTrueColor($targ_w, $targ_h);
                    imagecopyresampled($dst_r, $img_r, 0, 0, $x_axis, $y_axis, $targ_w, $targ_h, $width, $height);
                    imagegif($dst_r, $imageName);
                    break;
            }
        } else {
            $imageName = $imageName . '.' . $ext;
        }
        //--new
        $postSrc = substr($imageName, 3);
        $params = array('profilepic' => $postSrc);
        $image = array('userpic' => $postSrc);
        if (isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
            $update = Advisorpersonalinfo::model()->updateAll($params, "advisor_id = $advisorId");
            $updates = array('status' => 1, 'lastmodified' => date("Y-m-d H:i:s"));
            $result = AdvisorNotification::model()->updateAll($updates, 'advisor_id = "' . $advisorId . '" and template in ("photo")');
            $notificationCount = AdvisorNotification::model()->count("advisor_id = :advisor_id and status=0", array(':advisor_id' => $advisorId));
            $errors = Advisorpersonalinfo::model()->getErrors();
            if ($update OR empty($errors)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'profilepic' => $postSrc, 'notificationCount' => $notificationCount)));
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'msg' => 'Profile picture  updation failure.')));
            }
        } else {
            $advisorId = Yii::app()->getSession()->get('wsuser')->id;
            $update = Userpersonalinfo::model()->updateAll($image, "user_id = $advisorId");

            if ($update OR empty($errors)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'profilepic' => $postSrc)));
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'msg' => 'Profile picture  updation failure.')));
            }
        }
    }

    /**
     * @desc To get advisors selected designations
     * @param advisorids
     * @array advisor credentials/ Permissions
     */
    public function getSelectedDesignations($advisorId) {
        $advisorDesignations = Designation::model()->findAll("advisor_id = :advId AND deleted = :deleted AND desig_name != ''", array(':advId' => $advisorId, 'deleted' => '0')); //Code modified
        $designations['all'] = '';
        $designations['verified'] = '';
        $loop = 0;
        $verified = 0;
        foreach ($advisorDesignations as $key => $designation) {
            $designations['all'][$loop]['name'] = $designation['desig_name'];
            $designations['all'][$loop]['advisor_id'] = $designation['advisor_id'];
            if ($designation['status'] == 1) {
                $designations['all'][$loop]['status'] = $designation['status'];
                $designations['verified'][] = $designation['desig_name'];
            } else
                $designations['unverified'][] = $designation['desig_name'];
            $loop++;
        }
        if (!empty($designations['verified'])) {
            $designations['verified'] = implode(', ', $designations['verified']);
        } else
            $designations['verified'] = 'N/A';
        if (!empty($designations['unverified'])) {
            $designations['unverified'] = implode(', ', $designations['unverified']);
        } else
            $designations['unverified'] = 'N/A';
        $designations['count'] = count($advisorDesignations);
        /* write code to separate verified and unverified */
        return $designations;
    }

    function getAdvisorDesignations($advisorId, $type = null) {
        $advisorDesignation = Designation::model()->findAll("advisor_id = :advId", array(':advId' => $advisorId)); //Code modified
        return $advisorDesignation;
    }

    function advisorProductService($advisorId) {
        $productService = Productservice::model()->findAll("advisor_id = :advId", array(':advId' => $advisorId)); //Code modified
        return $productService;
    }

    function searchForId($stateName) {
        if (empty($stateName))
            return null;
        $stateList = State::model()->states_name;
        foreach ($stateList as $key => $val) {
            if (strtolower($val['name']) === strtolower($stateName)) {
                return $key;
            }
        }
    }

    function actionSaveConnectMode() {
        $model = new AdvisorClientRelated();
        $model->user_id = Yii::app()->getSession()->get('wsuser')->id;
        $firstname = Yii::app()->getSession()->get('wsuser')->firstname;
        $lastname = Yii::app()->getSession()->get('wsuser')->lastname;
        $model->advisor_id = Yii::app()->request->getParam('adv_id');
        $model->topic = addSlashes(Yii::app()->request->getParam('topic'));
        $model->message = addSlashes(Yii::app()->request->getParam('message'));
        $model->email = Yii::app()->request->getParam('email');
        $model->phone = Yii::app()->request->getParam('phone');
        if ($model->email != null && $model->phone == null) {
            $medium = $model->email;
        } else if ($model->phone != null && $model->email == null) {
            $medium = $model->phone;
        } else if ($model->email != null && $model->phone != null) {
            $medium = $model->email . '&nbsp;' . $model->phone;
        }
        $model->permission = Yii::app()->request->getParam('permission');
        $model->indemnification_check = 1;
        $model->dateconnect = date('Y-m-d');
        $advisor = Advisor::model()->findByPk($model->advisor_id);
        $advisorEmail = $advisor['email'];
        $advisorFirstname = $advisor['firstname'];

        try {
            if ($model->save()) {
                $part = 'connection-request';
                $email = new Email();
                $email->subject = 'You have a new connection request';
                $email->recipient['email'] = $advisorEmail;
                $email->data[$part] = [
                    'full-user-name' => "{$firstname} {$lastname}",
                ];
                $email->data['unsubscribe'] = true;
                $email->data['recipient_type'] = 'ad';
                $email->data['unsubscribe_code'] = $advisor->unsubscribecode;
                $email->send();

                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "adv_name" => $advisor['firstname'] . ' ' . $advisor['lastname'], 'message' => '<div class="center"><h3>Request Sent Successfully</h3> <br /> You should be contacted within 48 hrs. </div><div class="clearOnly
                twentypx"> </div>')));
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "Error", 'message' => 'Server is busy, request will be processed later')));
            }
        } catch (Exception $e) {
            $this->sendResponse(200, CJSON::encode(array("status" => "Error", 'message' => 'Server is busy, request will be processed later')));
        }
    }

    function currentStatus($advisorId) {
        $userId = Yii::app()->getSession()->get('wsuser')->id;
        $result = AdvisorClientRelated::model()->findAllByAttributes(array('advisor_id' => $advisorId, 'user_id' => $userId, 'status' => 1));
        // echo count($result);

        if (count($result) > 0) {
            return 'YES';
        }
        $result = AdvisorClientRelated::model()->findAllByAttributes(array('advisor_id' => $advisorId, 'user_id' => $userId, 'status' => 0));
        if (count($result) > 0) {
            return 'NO';
        }
    }

    function connectedStatus($advisorId) {
        $userId = Yii::app()->getSession()->get('wsuser')->id;

        $result = AdvisorClientRelated::model()->findAll("user_id = :user_id AND advisor_id = :advisor_id", array('user_id' => $userId, 'advisor_id' => $advisorId)); ////Code modified
        if (count($result) > 0) {
            return 'YES';
        } else
            return 'NO';
    }

    function actionGetAdvisorHelp() {
        //*****    Emailer should go here
        ///  UID/ACTION ID
        /// based on actiond get the Title from action step and update the emailtoadvisor table

        $qc = new CDbCriteria();
        $qc->condition = "id = :id";
        $qc->params = array('id' => $_POST['id']);
        $stepval = Actionstep::model()->find($qc);

        if (isset($stepval) && !empty($stepval)) {
            $stepval->advisorhelpstatus = $_POST['advisorhelpstatus'];
            $stepval->save();
        }
        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'helpdata' => $_POST)));
    }

    public function actionGetExternalLinkAS() {
        if (isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $advisor_id = Yii::app()->getSession()->get('wsadvisor')->id;
            $sort_order = Yii::app()->request->getParam('sort_order');

            $sort_by = Yii::app()->request->getParam('sort_by');
            $current_page = Yii::app()->request->getParam('current_page');
            $result = $this->getASListQuery('count');

            $advisor = Advisor::model()->findByPk($advisor_id);

            $asCount = count($result);
            if ($asCount > 0) {
                $paginator = new Paginator($asCount);
                $paginator->setPageSize(10);
                $paginator->setCurrentPage($current_page);
                $result = $this->getASListQuery();
                $result = $result->limit($paginator->getPageSize(), isset($current_page) ? $current_page * $paginator->getPageSize() - $paginator->getPageSize() : 0);
                $order = '';
                $metaActionsteps = Actionstepmeta::model()->findAllBySql("SELECT actionid, actionname, description FROM actionstepmeta WHERE externallink <> '' and status = '0' ORDER BY actionname $sort_order");
                $actionsteps = array();
                if ($metaActionsteps) {
                    foreach ($metaActionsteps as $key => $value) {

                        $advasdesc = AdvisorRecommendation::model()->find("advisor_id = :advisor_id AND actionid = :actionid", array('advisor_id' => $advisor_id, 'actionid' => $value->actionid));

                        if ($advasdesc) {
                            $rec_desc = $advasdesc['description'];
                            $product_name = $advasdesc['product_name'];
                            $product_image = $advasdesc['product_image'];
                            $product_link = $advasdesc['product_link'];
                        } else {
                            $rec_desc = "";
                            $product_name = "";
                            $product_image = "";
                            $product_link =  "";
                        }
                        $flexdescription = $value->description;
                        $flexdescription = str_replace('{{title}}', '', $flexdescription);

                        if (preg_match("/{{lnk}}/i", $flexdescription)) {
                            $lines = explode(".", $flexdescription);
                            $exclude = array();
                            foreach ($lines as $line) {
                                if (strpos($line, '{{lnk}}') !== FALSE) {
                                    continue;
                                }
                                $exclude[] = $line;
                            }
                            $flexdescription = implode(".", $exclude);
                        }

                        $actionsteps[$key]["advid"] = $advisor_id;
                        $actionsteps[$key]["actionid"] = $value->actionid;
                        $actionsteps[$key]["actionname"] = stripslashes(str_replace('${{amt}}', '$X', $value->actionname));
                        $actionsteps[$key]["flexdescription"] = stripslashes(str_replace('${{amt}}', '$X', $flexdescription));
                        $actionsteps[$key]["flexshortdescription"] = stripslashes(substr($value->description, 0, 90));
                        $actionsteps[$key]["product_description"] = stripslashes($rec_desc);
                        $actionsteps[$key]["shortdescription"] = stripslashes(substr($rec_desc, 0, 90));
                        $actionsteps[$key]["product_name"] = $product_name;
                        $actionsteps[$key]["product_image"] = $product_image;
                        $actionsteps[$key]["product_link"] = $product_link;
                    }
                }

                ##Apply Pagination
                $page = $paginator->paginatorHtml();
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "totalAS" => $asCount, 'asdata' => $actionsteps, 'pagination' => $page, 'sortBy' => $sort_by, 'sortOrder' => $sort_order, 'verified' => $advisor->verified)));
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "msg" => 'No record exists.', "userdata" => '', 'sortBy' => "", 'sortOrder' => $sort_order, 'verified' => $advisor->verified)));
            }
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "msg" => 'No record exists.', "userdata" => '', 'sortBy' => "", 'sortOrder' => $sort_order, 'verified' => $advisor->verified)));
        }
    }

    public function actionUpdateASDescription() {
        if (isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $advisor_id = Yii::app()->getSession()->get('wsadvisor')->id;
            $actionid = $_POST['actionid'];
            if(isset($_POST['restore']) && $_POST['restore'] == 'true') {
                AdvisorRecommendation::model()->deleteAll("advisor_id = $advisor_id AND actionid = $actionid");
                $advisorRecommendation = new AdvisorRecommendation();
            }
            else {
                $product_name = $_POST['name'];
                $product_description = addslashes($_POST['description']);
                $product_image = $_POST['image'];
                $product_link = $_POST['link'];

                $advisorRecommendation = AdvisorRecommendation::model()->find("advisor_id = :advisor_id AND actionid = :actionid", array('advisor_id' => $advisor_id, 'actionid' => $actionid));

                if (!$advisorRecommendation) {
                    $advisorRecommendation = new AdvisorRecommendation();
                }

                $advisorRecommendation->advisor_id = $advisor_id;
                $advisorRecommendation->actionid = $actionid;
                $advisorRecommendation->product_name = $product_name;
                $advisorRecommendation->description = $product_description;
                $advisorRecommendation->product_image = $product_image;
                $advisorRecommendation->product_link = $product_link;
                $advisorRecommendation->save();
            }

            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'recommendation' => $advisorRecommendation, 'message' => 'Advisor recommendation updated succesfully')));
        }
    }



    /**
     * @desc To create new client by uploadning the csv
     * @return type
     */
    public function actionUploadClients() {
        if (isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", 'msg' => 'You must be an advisor to access this function.')));
        }
        $filecheck = basename($_FILES['file']['name']);
        $ext = strtolower(substr($filecheck, strrpos($filecheck, '.') + 1));

        $pdfdata = array();

        $filename = $_FILES['file']['tmp_name'];
        if ($ext == "csv" && CFileHelper::getMimeType($filename) == "text/plain" && ($_FILES["file"]["size"] < 500000)) {

            ini_set('auto_detect_line_endings', TRUE);

            /*             * * Start of changes to limit csv entries *** */
            $j = 1;
            $fhandle = fopen("$filename", "r");
            while (($row = fgetcsv($fhandle, 1000, ",")) !== FALSE) {
                for ($i = 0; $i < count($row); $i++) {
                    if (trim($row[$i]) != "") {
                        $j++;
                        break;
                    }
                }
            }
            fclose($fhandle);
            if ($j > 201) {
                $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'type' => 'uploadcsv', 'message' => 'File cannot have more than 200 entries.')), 'text/html');
            }
            /*             * * End of changes to limit csv entries *** */

            $handle = fopen("$filename", "r");
            $error = array();
            $summary = array();
            $i = 0;
            $errorCount = 0;
            $successCount = 0;
            $successUsers = array();
            $result = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $activeRow = false;
                for ($m = 0; $m < count($data); $m++) {
                    if (trim($data[$m]) != "") {
                        $activeRow = true;
                        break;
                    }
                }

                if ($activeRow) {
                    if ($data[0] != "" && filter_var($data[0], FILTER_VALIDATE_EMAIL)) {
                        //check whether the columns in csv file are in proper order//
                        if (count($data) < 3) {
                            $errormail = "";
                            if (isset($data[0])) {
                                $errormail = $data[0];
                            } else {
                                $errormail = "unknown email";
                            }
                            $pdfdata[$i] = array($i + 1 . ".", " ", $errormail, "Error", 'Invalid column structure found.');
                            $errorCount++;
                        } else {
                            // add users in user table and associate with advisor
                            $result = $this->createNewClientCSV($data[0], $data[1], $data[2]);

                            // if user already exists and is associated with the advisor
                            if ($result == 1) {
                                $pdfdata[$i] = array($i + 1 . ".", $data[1] . " " . $data[2], $data[0], "Success", "");
                                $successUsers[] = $data[0];
                                $successCount++;
                            } else {
                                $pdfdata[$i] = array($i + 1 . ".", $data[1] . " " . $data[2], $data[0], "Error", $result);
                                $errorCount++;
                            }
                        }
                    } else {
                        // invalid user email address found in csv file//
                        $pdfdata[$i] = array($i + 1 . ".", $data[1] . " " . $data[2], $data[0], "Error", 'Invalid email address.');
                        $errorCount++;
                    }
                    $i++;
                }
            }
            fclose($handle);
            ini_set('auto_detect_line_endings', FALSE);

            /** Start of changes to include timezone offset * */
            $timeZoneOffset = isset($_REQUEST['timezoneoffset']) ? trim($_REQUEST['timezoneoffset']) : '';
            if ((int) $timeZoneOffset < 0) {
                $timestamp = strtotime(date("Y-m-d H:i:s")) + (abs($timeZoneOffset) * 60);
            } elseif ((int) $timeZoneOffset > 0) {
                $timestamp = strtotime(date("Y-m-d H:i:s")) - (abs($timeZoneOffset) * 60);
            } else {
                $timestamp = '';
            }
            $displayDate = ($timestamp != '') ? date("F jS Y, h:i A", $timestamp) : date("F jS Y, h:i A");
            /** End of changes to include timezone offset * */
            $pdf = new PdfCreator(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetTitle("FlexScore Client Upload Report");
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, " Client Upload Report", " Uploaded $displayDate");
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(TRUE, 15);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetTextColor(80, 80, 80);
            $pdf->AddPage();

            $header = array('', 'Name', 'Email', 'Status', 'Message'); //TODO:you can change this Header information according to your need.Also create a Dynamic Header.

            $pdf->ClientUploadReport($header, $pdfdata);
            // reset pointer to the last page
            $pdf->lastPage();

            // create the hash for advisorid //
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
            // Create a director for the advisor's pdf report if not already there.
            $folderPath = realpath(dirname(__FILE__) . '/../..');
            if (is_dir($folderPath . '/ui/usercontent/advisor/' . $advisorId . '/')) {
                rename($folderPath . '/ui/usercontent/advisor/' . $advisorId, $folderPath . '/ui/usercontent/advisor/' . $advidhashvalue);
            } else if (!is_dir($folderPath . '/ui/usercontent/advisor/' . $advisorId . '/') && !is_dir($folderPath . '/ui/usercontent/advisor/' . $advidhashvalue . '/')) {
                mkdir($folderPath . '/ui/usercontent/advisor/' . $advidhashvalue . '/');
            }
            $filename = "FlexScore_Client_Upload_".date("Y-m-d_H-i-s").'.pdf';
            $pdflink = "./ui/usercontent/advisor/" . $advidhashvalue . "/" . $filename;

            //Close and output PDF document
            $pdf->Output(("../ui/usercontent/advisor/" . $advidhashvalue . "/" . $filename), 'F');

            $summary['totalCount'] = $successCount + $errorCount;
            $summary['errorCount'] = $errorCount;
            $summary['successCount'] = $successCount;

            /** Start - advisor notification added for client upload ** */
            $notificationInfo = new AdvisorNotification();
            $notificationInfo->advisor_id = $advisorId;
            $notificationInfo->message = 'Your client list was uploaded successfully and is available for download.';
            $notificationInfo->context = 'Upload Clients';
            $notificationInfo->template = 'dashboard';
            $notificationInfo->status = 0;
            $notificationInfo->file = $filename;
            $notificationInfo->lastmodified = date("Y-m-d H:i:s");
            $notificationInfo->save();
            /** End - advisor notification added for client upload ** */
            /** Start - mixpanel tracking * */
            if (is_array($successUsers) && count($successUsers) > 0) {
                foreach ($successUsers as $userEmail) {
                    $mixPanelClientObj = new MixPanelClient();
                    if ($mixPanelClientObj->status == 'active') {
                        $mixPanelObj = Mixpanel::getInstance($mixPanelClientObj->token);
                        $userUniqueHash = md5(trim($userEmail));
                        $mixPanelObj->identify($userUniqueHash);
                        $mixPanelObj->people->setOnce($userUniqueHash, array('First Login Date' => date('Y-m-d H:i:s')));
                        $mixPanelObj->track("New User", array(
                            'new_user' => $userUniqueHash,
                            'Created By' => 'advisor'
                        ));
                    }
                }
            }
            /** End - mixpanel tracking * */
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'result' => $result, 'pdflink' => $pdflink, 'pdfdata' => $pdfdata, 'summary' => $summary)));
        } elseif ($_FILES["file"]["size"] > 500000) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'type' => 'uploadcsv', 'message' => 'Uploaded file size should be less than 500Kb.')), 'text/html');
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'type' => 'uploadcsv', 'message' => 'Allowed file type should be .csv only.')), 'text/html');
        }
    }

    public function createNewClientCSV($uemail, $firstname, $lastname) {
        //parent::unsetEngine();
        if ($uemail != "") {
            $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
            $userObject = User::model()->findBySql("SELECT id,firstname,lastname,email,isactive,password,roleid,verificationcode,requestinvitetokenkey FROM user WHERE email=:email", array("email" => $uemail));
            if ($userObject && $userObject->isactive != User::USER_IS_INACTIVE) {
                $advisorDetails = AdvisorClientRelated::model()->count("user_id = :user_id and advisor_id = :advisor_id", array("user_id" => $userObject->id, "advisor_id" => $advisorId));
                if ($advisorDetails > 0) {
                    return $uemail . " is already associated with your account.";
                } else {
                    return $uemail . " may already be associated with an account.";
                }
            } else if (!$userObject) {
                $userObject = new User();
            }

            $userObject->email = $uemail;
            if ($userObject->firstname == "") {
                $userObject->firstname = $firstname;
            }
            if ($userObject->lastname == "") {
                $userObject->lastname = $lastname;
            }
            $passwordGenerator = PasswordGenerator::factory();
            $randomPassword = $passwordGenerator->generate();
            unset($passwordGenerator);
            $hasher = PasswordHasher::factory();
            $userObject->password = $hasher->HashPassword($randomPassword);
            unset($hasher);
            // create a random unsubscribe code
            $unsubscribecodeGenerator = PasswordGenerator::factory();
            $randomUnsubscribecode = $unsubscribecodeGenerator->generate();
            unset($unsubscribecodeGenerator);
            $codehasher = PasswordHasher::factory();
            $userObject->unsubscribecode = $codehasher->HashPassword($randomUnsubscribecode);
            unset($codehasher);

            $userObject->roleid = User::USER_IS_CONSUMER;
            $userObject->isactive = User::USER_IS_ACTIVE;
            $userObject->requestinvitetokenkey = 'SUCCESS';
            $userObject->createdby = $advisorId;
            $userObject->lastaccesstimestamp = date("Y-m-d H:i:s");

            ##save the values to database
            if ($userObject->save()) {
                $userObject->update("id = :user_id", array(':user_id' => $userObject->id));
                $wsAdvisorClient = new stdClass();
                $wsAdvisorClient->id = $userObject->id;
                $wsAdvisorClient->email = $userObject->email;
                $wsAdvisorClient->roleid = $userObject->roleid;
                $wsAdvisorClient->firstname = $userObject->firstname;
                $wsAdvisorClient->lastname = $userObject->lastname;

                // Add New User
                Yii::app()->getSession()->add('wsuser', $wsAdvisorClient);
                $wsArrayToSend = array(
                    "status" => 'OK',
                    "uid" => Yii::app()->getSession()->get('wsuser')->id,
                    "email" => Yii::app()->getSession()->get('wsuser')->email,
                    "urole" => Yii::app()->getSession()->get('wsuser')->roleid,
                    "sess" => Yii::app()->session->sessionID,
                    "notification" => 0,
                    "type" => 'signup',
                    "uniquehash" => md5(Yii::app()->getSession()->get('wsuser')->email),
                );

                ##add as default row in user profile
                $userPerDetails = Userpersonalinfo::model()->find("user_id = :user_id", array(':user_id' => $wsAdvisorClient->id));

                if (!$userPerDetails) {
                    $userPerDetails = new Userpersonalinfo();
                    $userPerDetails->user_id = $wsAdvisorClient->id;
                }
                $userPerDetails->retirementstatus = 0;
                $userPerDetails->retirementage = 65;
                $userPerDetails->maritalstatus = 'Single';
                $userPerDetails->noofchildren = 0;
                $userPerDetails->save();

                $SControllerObj = new Scontroller(1);
                $SControllerObj->unsetEngine();
                $SControllerObj->setEngine();
                $SControllerObj->setupDefaultRetirementGoal();
                $SControllerObj->calculateScore("GOAL");

                $ASObj = new ActionstepController(1);
                $ASObj->updateComponents();

                $UserControllerObj = new UserController(1);
                $UserControllerObj->setSubscriptionStatus("Subscribe", true);

                ##To get new user credentials details.
                $usercredentials = User::model()->findByPk($wsAdvisorClient->id);
                $advisorDetails = Advisorpersonalinfo::model()->findByPk($advisorId);

                // Generate a token which validates during updation password
                $passwordGenerator = PasswordGenerator::factory();
                $randomPassword = $passwordGenerator->generate();
                unset($passwordGenerator);
                $hasher = PasswordHasher::factory();
                $usercredentials->resetpasswordcode = $hasher->HashPassword($randomPassword);
                $usercredentials->resetpasswordexpiration = new CDbExpression('NOW() + INTERVAL 10 DAY');

                $usercredentials->save();
                ## Code to send email

                $part = 'user-registration-by-advisor';
                $email = new Email();
                $email->subject = 'Welcome to FlexScore';
                $email->recipient['email'] = $wsAdvisorClient->email;
                $email->data[$part] = [
                    'advisor-name' => "{$advisorDetails->firstname} {$advisorDetails->lastname}",
                    'token' => $usercredentials->resetpasswordcode,
                ];
                $email->send();
                Yii::app()->getSession()->remove('wsuser');

                ## Code to send email
                if ($this->actionAdvisorRelatedClient($advisorId, $userObject->id, 'other_function')) {

                }
                return 1;
            }
        }
    }

    public function actionSendInvitation() {
        $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
        $advisor = Advisorpersonalinfo::model()->with('adv')->findByAttributes(array('advisor_id' => $advisorId));
        if (!$advisor) {
            $advisor = new Advisorpersonalinfo();
        }
        if (!file_exists('../' . $advisor['profilepic']) OR $advisor['profilepic'] == '') {
            $advisor['profilepic'] = 'ui/images/advisor_default.png';
        }
        $designationList = $this->getSelectedDesignations($advisorId);
        $advisor->designation = $designationList['verified'];
        $advisor->stateregistered = $this->getStatesInRegistered($advisorId);
        $advisor->productservice = $this->getProductService($advisorId);
        $advisorStatus = Adminadvisor::model()->findByAttributes(array('advisor_id' => $advisorId));
        $isAssigned = isset($advisorStatus->id) ? 1 : 0;
        if (empty($advisor->typeofcharge)) {
            $advisor->typeofcharge = array('0' => array('type' => 'N/A'));
        } else {
            $chargeType = array();
            $charge_arr = unserialize($advisor->typeofcharge);
            if (is_array($charge_arr) && !empty($charge_arr)) {
                foreach ($charge_arr as $key => $value) {
                    $chargeType[$key]['type'] = $value;
                }
            } else {
                $chargeType = array('0' => array('type' => 'N/A'));
            }
            $advisor->typeofcharge = $chargeType;
        }
        if ($advisor->advisortype == '') {
            $advisor->advisortype = 'N/A';
        }

        if ($advisor->minasstsforpersclient == '') {
            $advisor->minasstsforpersclient = 0;
        }

        $name = ucwords($advisor['firstname'] . " " . $advisor['lastname']);
        $appUrl = Yii::app()->params->applicationUrl;

        if (isset($_POST['emails'])) {
            if (count($_POST['emails']) > 1) {
                for ($i = 0; $i < count($_POST['emails']); $i++) {
                    $user = User::model()->findBySql("SELECT email FROM user WHERE email=:email", array("email" => $_POST['emails'][$i]));
                    $count = count($user);
                    if ($count > 0) {
                        $chk_register = 1;

                        $part = 'registration-invitation';
                        $email = new Email();
                        $email->subject = "FlexScore Advisor {$name} Opens His/Her Public Profile For You";
                        $email->recipient['email'] = $_POST['emails'][$i];
                        if ($chk_register == 0) {
                            $email->data[$part] = [
                                'name' => $name,
                                'button-text' => 'Create Your Free Account',
                                'button-action' => 'signup',
                                'content' => "Congratulations! You and {$name} are ready to begin your journey "
                                . "toward a FlexScore of 1,000. With this new tool, you will be more "
                                . "motivated to take action to improve your financial future.",
                                'redirect-url' => base64_encode($advisorId),
                            ];
                        } else {
                            $email->data[$part] = [
                                'name' => $name,
                                'button-text' => 'Sign In To Your Account',
                                'button-action' => 'login',
                                'content' => "Login to your FlexScore account and begin your journey toward a "
                                . "FlexScore of 1,000 with {$name}. With this new tool, you will be "
                                . "more motivated to take action to improve your financial future.",
                                'redirect-url' => base64_encode($advisorId),
                            ];
                        }
                        $email->send();
                    }
                }
            } else {
                $postEmail = $_POST['emails'];
                $user = User::model()->findBySql("SELECT email FROM user WHERE email=:email", array("email" => $postEmail));
                $count = count($user);
                if ($count > 0) {
                    $chk_register = 1;
                    $email = new Email();
                    $email->subject = "FlexScore Advisor {$name} Opens His/Her Public Profile For You";
                    $email->recipient['email'] = array_pop($_POST['emails']);
                    $email->data[$part] = [
                        'name' => $name,
                        'button-text' => 'Sign In To Your Account',
                        'button-action' => 'login',
                        'content' => "Login to your FlexScore account and begin your journey toward a "
                        . "FlexScore of 1,000 with {$name}. With this new tool, you will be "
                        . "more motivated to take action to improve your financial future.",
                        'redirect-url' => base64_encode($advisorId),
                    ];
                }
            }
            $this->sendResponse(200, CJSON::encode(array("status" => "OK", "success" => 'Invitation sent successfully.')));
        }
    }

    public function actionGetAdminExternalLinkAS() {
        if (Yii::app()->getSession()->get('wsuser')->roleid != User::USER_IS_ADMIN) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Please login as an Administrator")));
        }
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;

            $metaActionsteps = Actionstepmeta::model()->findAllBySql("SELECT actionid, actionname, description FROM actionstepmeta WHERE externallink <> '' and status = '0' ORDER BY actionname ASC");
            $users = Yii::app()->db->createCommand()
                    ->select('*')
                    ->from('adminasrecommendation')
                    ->queryAll();
            if ($users) {
                foreach ($users as $key => $value) {
                    if ($value['action_id'] != "") {
                        $actionSteps = Actionstepmeta::model()->findAllBySql("SELECT actionname FROM actionstepmeta WHERE actionid IN ($value[action_id])");
                        if ($actionSteps) {
                            $actionstepsArray = array();
                            foreach ($actionSteps as $akey => $avalue) {
                                $actionstepsArray[] = $avalue->actionname;
                            }
                            $actionstepsData = implode(", ", $actionstepsArray);
                            $users[$key]['actionsteps'] = $actionstepsData;
                        }
                    } else {
                        $users[$key]['actionsteps'] = "";
                    }
                }
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'userslist' => $users, 'asdata' => $metaActionsteps)));
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'userslist' => "", 'asdata' => $metaActionsteps)));
            }
        }
    }

    public function actionAddSpecificProductforAS() {
        if (Yii::app()->getSession()->get('wsuser')->roleid != User::USER_IS_ADMIN) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Please login as an Administrator")));
        }
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $adminid = Yii::app()->getSession()->get('wsuser')->id;
            $useremail = $_POST['useremail'];
            $actionid = $_POST['actionid'];
            $productname = $_POST['productname'];
            $productimage = $_POST['productimage'];
            $productdescription = addslashes($_POST['productdescription']);
            $productlink = $_POST['productlink'];

            Yii::app()->db->createCommand()->insert('adminasrecommendation', array(
                'admin_id' => $adminid,
                'user_email' => $useremail,
                'action_id' => $actionid,
                'product_name' => $productname,
                'product_description' => $productdescription,
                'product_link' => $productlink,
                'product_image' => $productimage
            ));
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'addedASdata' => $_POST, 'message' => 'Added successfully.')));
        }
    }

    public function actionUpdateSpecificProductforAS() {
        if (Yii::app()->getSession()->get('wsuser')->roleid != User::USER_IS_ADMIN) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Please login as an Administrator")));
        }
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $adminid = Yii::app()->getSession()->get('wsuser')->id;
            $id = $_POST['id'];
            $product_name = $_POST['productname'];
            $product_image = $_POST['productimage'];
            $product_link = $_POST['productlink'];
            $product_description = addslashes($_POST['productdescription']);

            Yii::app()->db
                    ->createCommand("UPDATE adminasrecommendation SET admin_id=" . $adminid . ", product_name=:product_name, product_image=:product_image, product_link=:product_link, product_description=:product_description WHERE id=:id")
                    ->bindValues(array('product_name' => $product_name, 'product_image' => $product_image, 'product_link' => $product_link, 'product_description' => $product_description, 'id' => $id))
                    ->execute();
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'addedASdata' => $_POST, 'message' => 'Updated successfully.')));
        }
    }

    public function actionDeleteSpecificProductforAS() {
        if (Yii::app()->getSession()->get('wsuser')->roleid != User::USER_IS_ADMIN) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Please login as an Administrator")));
        }
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $id = $_POST['id'];
            Yii::app()->db
                    ->createCommand("delete from adminasrecommendation where id=:id")
                    ->bindValues(array(':id' => $id))
                    ->execute();
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'addedASdata' => $_POST, 'message' => 'Deleted successfully.')));
        }
    }

}

?>
