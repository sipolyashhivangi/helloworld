<?php

/**
 * This is the model class for table "goal".
 *
 */
class Goal extends CActiveRecord {

    public $goalstartYear=0;
    public $goalstartMonth=0;
    public $goalstartDay=0;

    public $goalendYear=0;
    public $goalendMonth=0;
    public $goalendDay=0;
    public $goalamount_sum=0;
    public $retirementage = 0;

    /**
     * Returns the static model of the specified AR class.
     * @return Goalsetting the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'goal';
    }

    /**
     * Includes all goals
     * @param type $user_id
     * @return type
     */
    function getUserGoals($user_id) {

        //Checking if the data is there. !
        $userGoalSql = "SELECT * FROM goal WHERE user_id=:user_id and goalstatus = 1";
        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userGoalSql)->bindValue('user_id', $user_id);
        $row = $command->queryAll();
        return $row;
    }

    public function getDefaultName( $type ) {
        $name = 'Other';
        switch ($type)
        {
            case 'RETIREMENT':
                $name = 'Retirement';
                break;
            case 'COLLEGE':
                $name = 'Save For College';
                break;
            case 'HOUSE':
                $name = 'Buy a House';
                break;
            case 'DEBT':
                $name = 'Pay Off Debt';
                break;
            case 'CUSTOM':
                $name = 'Custom';
                break;
            default:
                $name = 'Custom';
        }
        return $name;
    }
}
?>