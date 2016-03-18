<?php
/**********************************************************************
 * Filename: Otlt.php
 * Folder: models
 * Description:  One time Look Up table for constants
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2013
 * Change History:
 * Version         Author               Change Description
 **********************************************************************/
class Otlt extends MActiveRecord {

    /**
     *
     * @param type $className
     * @return type
     */
	 
	public $name; 
	 
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    /**
     *
     * @return string
     */
    public function tableName() {
        return 'otlt';
    }

    public $AcctName=0;

}
?>