<?php
/**********************************************************************
 * Filename: UserAutoAccount.php
 * Folder: models
 * Description:  This is the model class for table "itemautoaccount".
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2013
 * Change History:
 * Version         Author               Change Description
 **********************************************************************/
class UserAutoAccount extends CActiveRecord {

    /**
     * 
     * @param type $className
     * @return type
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 
     * @return string
     */
    public function tableName() {
        return 'itemautoaccount';
    }
}
?>