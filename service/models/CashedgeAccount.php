<?php
/**********************************************************************
 * Filename: CashedgeAccount.php
 * Folder: models
 * Description:  This is the model class for table "cashedge account"
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2013
 * Change History:
 * Version         Author               Change Description
 **********************************************************************/
class CashedgeAccount extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'cashedgeaccount';
    }
}
?>