<?php

/* * ********************************************************************
 * Filename: UserScore.php
 * Folder: models
 * Description:  user score calculated 
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2013 - 2014
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class UserScore extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    // Creating the table ORM :

    public function tableName() {
        return 'userscore';
    }

}
?>