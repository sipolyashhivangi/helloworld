<?php

/**
 * This is the model class for table "advisorpersonalinfo".
 *
 */
class AdvisorClientRelated extends CActiveRecord
{
        public $credentials;

/*      Remove if not needed 12/18/14
        public $advisor_id;
	public $user_id;
	public $permission;
	public $message;
	public $topic;
	public $mode;
	public $email;
	public $phone;
	public $dateconnect;
	public $indemnification_check;
 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return Advisorpersonalinfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'consumervsadvisor';
	}


	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */

}