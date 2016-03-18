<?php

class Device extends CActiveRecord
{

    /* Remove if not needed 12/18/14
     * public $id;
     * public $user_id;
     */

    public static function model ($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations ()
    {
        return array(
            'users'         => array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }

    public function tableName ()
    {
        return 'device';
    }

}

?>
