<?php

/**
 * This is the model class for table "advisorsubscriptioninvoice".
 *
 */
class AdvisorSubscriptionInvoice extends CActiveRecord {

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
        return 'advisorsubscriptioninvoice';
    }
    
    function generateFlexscoreInvoiceNumber(){
        $last_invoice = AdvisorSubscriptionInvoice::model()->find(array('order' => 'id DESC'));
        if (isset($last_invoice)) {
            $last_invoice_number = $last_invoice->id;
            $new_invoice_number = $last_invoice_number + 1;
        } else {
            $new_invoice_number = 1;
        }
        if ($new_invoice_number < 10) {
            $new_fc_invoice_number = "FA".date("Y")."0000" . $new_invoice_number;
        } else if ($new_invoice_number >= 10 && $new_invoice_number < 100) {
            $new_fc_invoice_number = "FA".date("Y")."000" . $new_invoice_number;
        } else if ($new_invoice_number >= 100 && $new_invoice_number < 1000) {
            $new_fc_invoice_number = "FA".date("Y")."00" . $new_invoice_number;
        } else if ($new_invoice_number >= 1000 && $new_invoice_number < 10000) {
            $new_fc_invoice_number = "FA".date("Y")."0" . $new_invoice_number;
        } else if ($new_invoice_number >= 10000) {
            $new_fc_invoice_number = "FA".date("Y").$new_invoice_number;
        }

        return $new_fc_invoice_number;
    }

}

?>