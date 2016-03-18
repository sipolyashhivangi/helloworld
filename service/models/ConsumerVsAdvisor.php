<?php

/* * ********************************************************************
 * Filename: ConstantsLastUpdated.php
 * Folder: models
 * Description:  This is the model class for table "constantslastupdated"
 * @author Manju Sheshadri (For TruGlobal Inc)
 * @copyright (c) 2012 - 2013
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class ConsumerVsAdvisor extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'consumervsadvisor';
    }
}

?>