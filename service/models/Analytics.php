<?php

/**
 * This is the model class for table "analytics".
 *
 * The followings are the available columns in table 'analytics':
 * @property integer $id
 * @property datetime $datetime
 * @property string $details
 */
class Analytics extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return Actionstepmeta the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'analytics';
    }

}