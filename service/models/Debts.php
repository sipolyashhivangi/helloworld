<?php

/**
 * This is the model class for table "debts".
 *
 */
class Debts extends CActiveRecord {

    public $davg;
    public $dcount;

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
        return 'debts';
    }

    // Variables for Score Engine :

    public $count_debts = 0;
    public $maxamtpermonth = 0;
    public $mincc = 0;
    public $emiloansql = 0;
    public $totalDebts = 0;
    public $debtsProf = 0;
    public $otherDebtsSum = 0;
    public $max_cc = 0;
    public $min_cc = 0;
    public $max_int = 0;
    public $min_int = 0;
    public $total = 0;


    public function getDefaultName( $type ) {
        $name = 'Other';
        switch ($type) {
            case 'CC':
                $name = 'Credit Card';
                break;
            case 'MORT':
                $name = 'Mortgage';
                break;
            case 'LOAN':
                $name = 'Loan';
                break;
            case 'ALOAN':
                $name = 'Auto Loan';
                break;
            case 'BLOAN':
                $name = 'Business Loan';
                break;
            case 'SLOAN':
                $name = 'Student Loan';
                break;
            case 'OTHE':
                $name = 'Loan';
                break;
            default:
                $name = 'Other';
        }
        return $name;
    }
}
?>