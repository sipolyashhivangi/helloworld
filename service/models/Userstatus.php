<?php

/**
 * This is the model class for table "userstatus".
 *
 * The followings are the available columns in table 'userstatus':
 * @property integer $user_id
 * @property string $profilestatus
 * @property string $fadstatus
 * @property integer $retirementstatus
 * @property integer $retirementage
 */
class Userstatus extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Userstatus the static model class
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
		return 'userstatus';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, profilestatus, fadstatus, retirementstatus, retirementage', 'required'),
			array('user_id, retirementstatus, retirementage', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, profilestatus, fadstatus, retirementstatus, retirementage', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'u' => array(self::BELONGS_TO, 'User', 'id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user_id' => 'Uid',
			'profilestatus' => 'Profilestatus',
			'fadstatus' => 'Fadstatus',
			'retirementstatus' => 'Retirementstatus',
			'retirementage' => 'Retirementage',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('user_id',$this->user_id);

		$criteria->compare('profilestatus',$this->profilestatus,true);

		$criteria->compare('fadstatus',$this->fadstatus,true);

		$criteria->compare('retirementstatus',$this->retirementstatus);

		$criteria->compare('retirementage',$this->retirementage);

		return new CActiveDataProvider('Userstatus', array(
			'criteria'=>$criteria,
		));
	}
}