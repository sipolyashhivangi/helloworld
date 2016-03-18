<?php
/**
 * This is the model class for table "riskfactors".
 *
 */
class Riskfactors extends MActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return Account the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'riskfactors';
    }

}
?>