<?php

/* * ********************************************************************
 * Filename: Tickerinfo.php
 * Folder: models
 * Description:  This is the model class for table "pertrack"
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2013
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class Tickerinfo extends MActiveRecord {

    public $countticker=0;
    public $prefscore = 0;
    public $tickerRiskVal = 0;

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'tickerinfo';
    }
    public $itemType;
    public $tickers;
}

?>