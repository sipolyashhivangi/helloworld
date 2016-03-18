<?php
/**********************************************************************
 * Filename: CashedgefiDetails.php
 * Folder: models
 * Description:  This is the model class for table "cashedge fi details "
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2013
 * Change History:
 * Version         Author               Change Description
 **********************************************************************/
class CashedgefiDetails extends MActiveRecord {
    
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
        return 'cashedgefidetails';
    }
}
?>