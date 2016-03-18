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

class Advisor extends CActiveRecord {

    private $_identity;

    public $totcount;
    public $rememberMe;
    public $urole = 999;

    // Advisor status and role constants
    const USER_IS_INACTIVE = 0;
    const USER_IS_ACTIVE = 1;
    const USER_IS_DISABLED = 2;
    const USER_IS_ADVISOR = 999;


    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'advisor';
    }

    public function rules() {
        return array(
            array('createdtimestamp', 'default', 'value' => new CDbExpression('NOW()'), 'setOnEmpty' => false, 'on' => 'insert'),
        );
    }


    /**
     * authenticateIdentity() provides basic authentication when performing
     * password and pin updates, changing passwords, etc. for advisors.
     */
    public function authenticateIdentity() {
        $this->_identity = new UserIdentity($this->email, $this->password, $this->urole);
        return $this->_identity->authenticateAdvisor();
    }


    /**
     * authenticateLogin() is used for authenticating advisors when they
     * are signing up or logging in.
     */
    public function authenticateLogin() {
        $this->_identity = new UserIdentity($this->email, $this->password, $this->urole);

        if ($this->_identity->authenticateAdvisor()) {
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days
            Yii::app()->user->login($this->_identity, $duration);
            //remove password before adding to session
            $wsadvisor = $this->_identity->user;
            $wsadvisor["password"] = "";
            Yii::app()->getSession()->add('wsadvisor', $wsadvisor);
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
