<?php

/* * ********************************************************************
 * Filename: User.php
 * Folder: models
 * Description:  ORM for User table
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2013
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class User extends CActiveRecord {

    private $_identity;

/*  remove if not needed 12/18/14
    public $email;
    public $password;
    public $pin;
    public $firstname;
    public $lastname;
    public $zip;
 */
    public $rememberMe;
    public $urole;
    public $totcount;
    public $clientId;
    public $clientEmail;
    public $clientroleid;

    // User status and role constants
    const USER_IS_INACTIVE = 0;
    const USER_IS_ACTIVE = 1;
    const USER_IS_DISABLED = 2;
    const USER_IS_ADMIN = 777;
    const USER_IS_CONSUMER = 888;



    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'user';
    }

    public function rules() {
        return array(
            array('createdtimestamp', 'default', 'value' => new CDbExpression('NOW()'), 'setOnEmpty' => false, 'on' => 'insert'),
        );
    }

    public function relations ()
    {
        return array(
            'devices'   => array(self::HAS_MANY, 'Device',      'user_id'),
            'expenses'  => array(self::HAS_MANY, 'Expense',     'user_id'),
            'incomes'   => array(self::HAS_MANY, 'Income',      'user_id'),
            'debts'     => array(self::HAS_MANY, 'Debts',       'user_id'),
            'assets'    => array(self::HAS_MANY, 'Assets',      'user_id'),
            'goals'     => array(self::HAS_MANY, 'Goal',        'user_id'),
            'usermedia' => array(self::HAS_MANY, 'UserMedia',   'user_id'),
            'cashedgeaccounts' => array(self::HAS_MANY, 'CashedgeAccount',   'user_id'),
        );
    }


    /**
     * authenticateIdentity() provides basic authentication when performing
     * password and pin updates, changing passwords, etc. for users.
     */
    public function authenticateIdentity() {
        $this->_identity = new UserIdentity($this->email, $this->password, $this->urole);
        return $this->_identity->authenticateUser();
    }


    /**
     * authenticateLogin() is used for authenticating users when they
     * are signing up or logging in.
     */
    public function authenticateLogin() {
        $this->_identity = new UserIdentity($this->email, $this->password, $this->urole);
        if ($this->_identity->authenticateUser()) {
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days
            Yii::app()->user->login($this->_identity, $duration);
            //remove password before adding to session
            $wsuser = $this->_identity->user;

            $wsuser["password"] = "";
            $wsuser["pin"] = "";

            if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $user_id = Yii::app()->getSession()->get('wsuser')->id;
                $array = Yii::app()->cache->get('logout' . $user_id);
                    if($array === false) {
                    $array = array();
                    }
                $array[Yii::app()->session->sessionID] = date("Y-m-d H:i:s");
                    Yii::app()->cache->set('logout' . $user_id, $array);
            }
            unset(Yii::app()->session["sengine"]);
            Yii::app()->getSession()->add('wsuser', $wsuser);

            return true;
        } else {
            return false;
        }
    }

    /**
     * authenticateByPin() is used for mobile devices
     * @return boolean
     */
    public function authenticateByPin() {
        $this->_identity = new UserIdentity($this->email, $this->pin, $this->urole);
        if ($this->_identity->authenticateByPin()) {
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days
            Yii::app()->user->login($this->_identity, $duration);
            //remove password before adding to session
            $wsuser = $this->_identity->user;

            $wsuser["password"] = "";
            $wsuser["pin"] = "";

            if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $user_id = Yii::app()->getSession()->get('wsuser')->id;
                $array = Yii::app()->cache->get('logout' . $user_id);
                    if($array === false) {
                    $array = array();
                    }
                $array[Yii::app()->session->sessionID] = date("Y-m-d H:i:s");
                    Yii::app()->cache->set('logout' . $user_id, $array);
            }
            unset(Yii::app()->session["sengine"]);
            Yii::app()->getSession()->add('wsuser', $wsuser);

            return true;
        } else {
            return false;
        }
    }


    public function getErrorCode() {
        return $this->_identity->errorCode;
    }

}

?>
