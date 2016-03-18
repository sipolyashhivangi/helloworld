<?php
/**********************************************************************
 * Filename: CashedgeSearchTerm.php
 * Folder: models
 * Description:  This is the model class for table "cashedgesearchterm"
 * @copyright (c) 2012 - 2013
 * Change History:
 * Version         Author               Change Description
 **********************************************************************/
class CashedgeSearchTerm extends MActiveRecord {
    
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
        return 'cashedgesearchterm';
    }
}
?>