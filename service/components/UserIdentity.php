<?php

/* * ********************************************************************
 * Filename: UserIdentity.php
 * Folder: components
 * Description: UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2014
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class UserIdentity extends CUserIdentity {

/* UserIdentity Base class error constants
 *  const ERROR_NONE = 0;
 *  const ERROR_USERNAME_INVALID = 1;
 *  const ERROR_PASSWORD_INVALID = 2;
 *  const ERROR_UNKNOWN_IDENTITY = 100;
 */

// Flexscore error constants
    const ERROR_USER_IS_INACTIVE = 3;
    const ERROR_USER_IS_DISABLED = 4;

    private $_user_id;
    private $_user;


    /*
     * Authenticates user and admins based on password and whether they are
     * not inactive or disabled.  Returns an error code that is available
     * to be processed for responses to users.  The errorCode for no errors = 0,
     * so the flip side !$this->errorCode is returned as boolean for success or
     * failure.
     */
    public function authenticateUser() {
        $user = User::model()->findByAttributes(array('email' => $this->email));
        $this->_user = $user;
        if ($user) {
            $hasher = PasswordHasher::factory();
            if ($user->isactive === User::USER_IS_INACTIVE) {
                $this->errorCode = self::ERROR_USER_IS_INACTIVE;
            } elseif ($user->isactive == User::USER_IS_DISABLED) {
                $this->errorCode = self::ERROR_USER_IS_DISABLED;
            }
            // temporary elseif clause to hash current md5 passwords once upon login
            elseif (md5($this->password) === $user->password) {
                $user->password = $hasher->HashPassword($this->password);
                $user->save();
                $this->_user_id = $user->id;
                $this->errorCode = self::ERROR_NONE;
            } elseif ($hasher->CheckPassword($this->password, $user->password)) {
                $this->_user_id = $user->id;
                $this->errorCode = self::ERROR_NONE;
            } else {
                $this->errorCode = self::ERROR_PASSWORD_INVALID;
            }
            unset($hasher);
        } else {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        }
        return !$this->errorCode;
    }


    /*
     * Authenticates advisors based on password and whether they are
     * not inactive or disabled.  Returns an error code that is available
     * to be processed for responses to users.  The errorCode for no errors = 0,
     * so the flip side !$this->errorCode is returned as boolean for success or
     * failure.
     */
    public function authenticateAdvisor() {
        $advisor = Advisor::model()->findByAttributes(array('email' => $this->email));
        $this->_user = $advisor;
        if ($advisor) {
            $hasher = PasswordHasher::factory();
            if ($advisor->isactive == Advisor::USER_IS_INACTIVE) {
                $this->errorCode = self::ERROR_USER_IS_INACTIVE;
            }
            elseif ($advisor->isactive == Advisor::USER_IS_DISABLED) {
                $this->errorCode = self::ERROR_USER_IS_DISABLED;
            }
            elseif (md5($this->password) === $advisor->password) {
                $advisor->password = $hasher->HashPassword($this->password);
                $advisor->save();
                $this->_user_id = $advisor->id;
                $this->errorCode = self::ERROR_NONE;
            }
            elseif ($hasher->CheckPassword($this->password, $advisor->password)) {
                $this->_user_id = $advisor->id;
                $this->errorCode = self::ERROR_NONE;
            }
            else {
                $this->errorCode = self::ERROR_PASSWORD_INVALID;
            }
            unset($hasher);
        } else {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        }
        return !$this->errorCode;
    }


    /*
     * Authenticates user and admins based on pin and whether they are
     * not inactive or disabled.  Returns an error code that is available
     * to be processed for responses to users.  The errorCode for no errors = 0,
     * so the flip side !$this->errorCode is returned as boolean for success or
     * failure.
     */
    public function authenticateByPin() {
        $user = User::model()->findByAttributes(array('email' => $this->email));
        $this->_user = $user;
        if ($user && isset($user->isactive)) {
            $hasher = PasswordHasher::factory();
            if ($user->isactive == User::USER_IS_INACTIVE) {
                $this->errorCode = self::ERROR_USER_IS_INACTIVE;
            } elseif ($user->isactive == User::USER_IS_DISABLED) {
                $this->errorCode = self::ERROR_USER_IS_DISABLED;
            } elseif ($user->pin != null && $user->pin != "" && md5($this->password) === $user->pin) {
                $user->pin = $hasher->HashPassword($this->password);
                $user->save();
                $this->_user_id = $user->id;
                $this->errorCode = self::ERROR_NONE;
            } elseif ($user->pin != null && $user->pin != "" && $hasher->CheckPassword($this->password, $user->pin)) {
                $this->_user_id = $user->id;
                $this->errorCode = self::ERROR_NONE;
            } else {
                $this->errorCode = self::ERROR_PASSWORD_INVALID;
            }
            unset($hasher);
        } else {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        }
        return !$this->errorCode;
    }


    public function getId() {
        return $this->_user_id;
    }


    public function getUser() {
        return $this->_user;
    }


}
