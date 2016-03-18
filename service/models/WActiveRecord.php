<?php
/**********************************************************************
 * Filename: SActiveRecord.php
 * Folder: models
 * Description:  For using the data from Score Engine DB
 * @author Thayub Hashim Munnavver (For TruGlobal Inc)
 * @copyright (c) 2013 - 2014
 * Change History:
 * Version         Author               Change Description
 **********************************************************************/
class WActiveRecord extends CActiveRecord {

    public static $cms;

    /**
     * 
     * @return type
     * @throws CDbException
     */
    public function getDbConnection() {

        if (self::$cms !== null)
            return self::$cms;

        else {

            self::$cms = Yii::app()->cms;

            if (self::$cms instanceof CDbConnection) {

                self::$cms->setActive(true);

                return self::$cms;
            }

            else
                throw new CDbException(Yii::t('yii', 'Active Record requires a "db" CDbConnection application component.'));
        }
    }

}

?>