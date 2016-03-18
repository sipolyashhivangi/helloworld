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
class SActiveRecord extends CActiveRecord {

    public static $db3;

    /**
     * 
     * @return type
     * @throws CDbException
     */
    public function getDbConnection() {

        if (self::$db3 !== null)
            return self::$db3;

        else {

            self::$db3 = Yii::app()->db3;

            if (self::$db3 instanceof CDbConnection) {

                self::$db3->setActive(true);

                return self::$db3;
            }

            else
                throw new CDbException(Yii::t('yii', 'Active Record requires a "db" CDbConnection application component.'));
        }
    }

}

?>