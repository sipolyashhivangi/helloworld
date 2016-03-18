<?php

/**
 * This is the model class for table "usermedia".
 *
 */
class UserMedia extends CActiveRecord {

    /**
     * Returns the static model of the specified userarticle class.
     * @return Account the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'usermedia';
    }

    public function rules() {
        return array(
            array('modified', 'default',
                'value' => new CDbExpression('NOW()'),
                'setOnEmpty' => false, 'on' => 'update'),
            array('created,modified', 'default',
                'value' => new CDbExpression('NOW()'),
                'setOnEmpty' => false, 'on' => 'insert')
        );
    }
}
?>