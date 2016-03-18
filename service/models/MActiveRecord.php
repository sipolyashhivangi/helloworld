<?php
/**********************************************************************
 * Filename: MActiveRecord.php
 * Folder: models
 * Description:  For using yodlee meta configuration and data DB
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2013
 * Change History:
 * Version         Author               Change Description
 **********************************************************************/
class MActiveRecord extends CActiveRecord {

    public static $metadb;

    /**
     * 
     * @return type
     * @throws CDbException
     */
    public function getDbConnection() {

        if (self::$metadb !== null)
            return self::$metadb;

        else {

            self::$metadb = Yii::app()->metadb;

            if (self::$metadb instanceof CDbConnection) {

                self::$metadb->setActive(true);

                return self::$metadb;
            }

            else
                throw new CDbException(Yii::t('yii', 'Active Record requires a "db" CDbConnection application component.'));
        }
    }

}

?>