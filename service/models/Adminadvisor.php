<?php

/**
 * This is the model class for table "adminadvisors".
 *
 * The followings are the available columns in table 'adminadvisors':
 *
 */
class Adminadvisor extends CActiveRecord {

//    Remove if not needed 12/18/14
//    public $id;
//    public $advisor_id;
//    public $user_id;

    /**
     * Returns the static model of the specified AR class.
     * @return Advisorpersonalinfo the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }


    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'adminadvisors';
    }


}
