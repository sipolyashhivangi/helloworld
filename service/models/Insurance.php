<?php

/**
 * This is the model class for table "expense".
 */
class Insurance extends CActiveRecord {

    public $iavg;
    public $icount;

    /**
     * Returns the static model of the specified AR class.
     * @return expense the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'insurance';
    }

    public $count_insurance = 0;
    public $total_cashvalue = 0;
    public $total_cash_insurance = 0;
    public $life_insu_sum = 0;

    public function getDefaultName( $type ) {
        $name = 'Other';
        switch ($type)
        {
            case 'LIFE':
                $name = 'Life Insurance';
                break;
            case 'DISA':
                $name = 'Disability Insurance';
                break;
            case 'LONG':
                $name = 'Long Term Care Insurance';
                break;
            case 'HOME':
                $name = "Home Owner's/Renter's Insurance";
                break;
            case 'VEHI':
                $name = 'Vehicle Insurance';
                break;
            case 'UMBR':
                $name = 'Umbrella Insurance';
                break;
            case 'HEAL':
                $name = 'Health Insurance';
                break;
            case 'OTHE':
                $name = 'Other';
                break;
            default:
                $name = 'Other';
        }
        return $name;
    }
    
}

?>