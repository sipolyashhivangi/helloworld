<?php

/* * ********************************************************************
 * Filename: SUser.php
 * Folder: models
 * Description:  This is the model for the Score Engine DB User table - (db3)
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2013 - 2014
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class SUser extends SActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    // Creating the table ORM :

    public function tableName() {
        return 'user';
    }

}
?>