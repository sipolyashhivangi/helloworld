<?php
/**********************************************************************
 * Filename: UserAccess.php
 * Folder: models
 * Description:  Model class for table "useraccess". If a user exceeds the
 * failed login attempt count within the failed login time limit, then the
 * user will be locked out for the amount specified by the lockout duration.
 * @author Dan Tormey
 * @copyright (c) 2014
 **********************************************************************/
class UserAccess extends CActiveRecord {

    public $failedAttemptCount;
    public $firstFailedAttempt;
    public $lastFailedAttempt;

    // indicates whether the login succeeded or failed
    const FAILED = 0;
    const SUCCESS = 1;
    // indicates whether the attempt is active or archived (current = true means active)
    const FALSE = 0;
    const TRUE = 1;
    // indicates whether a password or pin was used for the login attempt
    const PASSWORD = 0;
    const PIN = 1;

    const FAILED_LOGIN_COUNT_LIMIT = 5;
    const FAILED_LOGIN_TIME_LIMIT = 600;  // 10 minutes in seconds
    const LOCKOUT_DURATION = 28800;   // 8 hours in seconds



    /**
     *
     * @param type $className
     * @return type
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     *
     * @return string
     */
    public function tableName() {
        return 'useraccess';
    }

}
?>