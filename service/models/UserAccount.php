<?php
/**********************************************************************
 * Filename: User.php
 * Folder: models
 * Description:  This is the model class for table "accounts".
 * @author Subramanya HS (For TruGlobal Inc)
 * @copyright (c) 2012 - 2013
 * Change History:
 * Version         Author               Change Description
 **********************************************************************/
class UserAccount extends CActiveRecord {

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
        return 'account';
    }

    /**
     * Get all the user accounts using two tables
     *
     * @param type $id
     */
    public function getUserAccounts($userId) {

        $userAccSql = "SELECT a.name AS accountName, a.amount as availableBalance,
                ia.displayname AS itemDisplayName, ia.`itemid` AS `itemId` , ia.`accessstatus` AS iaaccessstatus,
                a.actid as itemAccountId, a.`isactive` AS `activeAccount`,ia.isactive as itemAccessStatus
                FROM `account` `a`,itemautoaccount ia WHERE a.user_id =:user_id and ia.id=a.itemrefid";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($userAccSql)->bindValue('user_id', $userId);

        $dataReader = $command->query();
        return $dataReader;
    }
}
?>