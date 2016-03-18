<?php

/**
 * This is the model class for table "actionstep".
 *
 */
class Actionstep extends CActiveRecord {

    public $acscount;
    public $maxmodifiedtime;
    public $modifiedtime = null;

    /**
     * Returns the static model of the specified AR class.
     * @return Actionstep the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'actionstep';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, actionsteps, actionstatus, lastmodifiedtime, actionid, points', 'required'),
            array('user_id, actionid, points', 'numerical', 'integerOnly' => true),
            array('actionstatus', 'length', 'max' => 100),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('user_id, actionsteps, actionstatus, lastmodifiedtime, actionid, points', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'user_id' => 'Uid',
            'actionsteps' => 'Actionsteps',
            'actionstatus' => 'Actionstatus',
            'lastmodifiedtime' => 'Lastmodifiedtime',
            'actionid' => 'Actionid',
            'points' => 'Priority',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('actionsteps', $this->actionsteps, true);

        $criteria->compare('actionstatus', $this->actionstatus, true);
        $criteria->compare('lastmodifiedtime', $this->lastmodifiedtime, true);

        $criteria->compare('actionid', $this->actionid);
        $criteria->compare('points', $this->points);

        return new CActiveDataProvider('Actionstep', array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Update Actionstep Order by User
     * @param type $user_id, $actionid, $order
     */
    function updateActionUserOrder($user_id, $actionid, $order) {
        //Checking if the data is there. !
        try {
            $userTitleSql = "UPDATE actionstep SET userorder = :order WHERE user_id = :user_id AND id = :id";
            $connection = Yii::app()->db;
            $connection->active=true;
            $cmd = $connection->createCommand($userTitleSql)->bindValue('order', $order)->bindValue('user_id', $user_id)->bindValue('id', $actionid);
            $cmd->execute();
            $connection->active=false;
            unset($cmd, $connection);
        } catch (Exception $E) {
            echo $E;
        }
    }

    /**
     * Update Actionstep Status by User
     * @param type $id
     */
    function updateActionStepStatus($id, $status) {
        try {
            $actionSql = "UPDATE actionstep SET actionstatus = :status WHERE id = :id";
            $connection = Yii::app()->db;
            $connection->active=true;
            $cmd = $connection->createCommand($actionSql)->bindValue('id', $id)->bindValue('status', $status);
            $cmd->execute();

            $connection->active=false;
            unset($cmd, $connection);
        } catch (Exception $E) {
            echo $E;
        }
    }

    /**
     *
     *
     */
    function getActionStepByComplete($user_id, $id) {
        try {
            $actionSql = "SELECT * FROM actionstep WHERE actionid = :id AND user_id = :user_id AND actionstatus = :status";
            $connection = Yii::app()->db;
            $connection->active=true;
            $command = $connection->createCommand($actionSql)->bindValue('id', $id)->bindValue('user_id', $user_id)->bindValue('status', '3');
            $row = $command->queryAll();
            $connection->active=false;
            unset($command, $command);
            return $row;
        } catch (Exception $E) {
            echo $E;
        }
    }

    /**
     *
     *
     */
    function getActionStepForDisplay($user_id) {
        try {
            $actionSql = "SELECT * FROM actionstep WHERE user_id = :user_id AND actionstatus = :status";
            $connection = Yii::app()->db;
            $connection->active=true;
            $command = $connection->createCommand($actionSql)->bindValue('user_id', $user_id)->bindValue('status', '1');
            $row = $command->queryAll();
            $connection->active=false;
            unset($command, $connection);
            return $row;
        } catch (Exception $E) {
            echo $E;
        }
    }

    /**
     * Update & Remove Actionstep Status by CronFile. Conditions in Trigger are to be true. Then this function works.
     * @param type $user_id, $actionid, $status
     */
    function updateRemoveActionStep($user_id, $actionid, $status, $setmodified) {
        try {
            if ($status == '3') { // an action is 'started'
                $newstatus = '1'; // make that action 'completed'
            } else if ($status == '5') { // action 'deleted'
                $newstatus = '0'; // make it as 'new'
            } else {
                $newstatus = '5'; // 'deleted'
            }
            $lastmodified = '';
            if($setmodified) {
                $lastmodified = ', lastmodifiedtime =:lastmodifiedtime';
            }
            $actionSql = "UPDATE actionstep SET actionstatus = :status $lastmodified WHERE user_id = :id AND actionid = :advisor_id";
            $connection = Yii::app()->db;
            $connection->active=true;

            $cmd = $connection->createCommand($actionSql)->bindValue('id', $user_id)->bindValue('advisor_id', $actionid)->bindValue('status', $newstatus);
            if($setmodified) {
                $cmd->bindValue('lastmodifiedtime',date("Y-m-d H:i:s"));
            }
            $cmd->execute();

            $connection->active=false;
            unset($cmd, $connection);
        } catch (Exception $E) {
            echo $E;
        }
    }

}