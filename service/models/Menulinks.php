<?php

/**
 * This is the model class for table "menulinks".
 *
 * The followings are the available columns in table 'menulinks':
 * @property integer $mnid
 * @property string $menuname
 * @property string $linkpath
 * @property string $linktitle
 * @property integer $status
 * @property string $timestamp
 */
class Menulinks extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Menulinks the static model class
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
		return 'menulinks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('mnid, menuname, linkpath, linktitle, status, timestamp', 'required'),
			array('mnid, status', 'numerical', 'integerOnly'=>true),
			array('menuname, linkpath, linktitle', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('mnid, menuname, linkpath, linktitle, status, timestamp', 'safe', 'on'=>'search'),
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
			'otlts' => array(self::MANY_MANY, 'Otlt', 'menuperrole(mnid, roleid)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'mnid' => 'Mnid',
			'menuname' => 'Menuname',
			'linkpath' => 'Linkpath',
			'linktitle' => 'Linktitle',
			'status' => 'Status',
			'timestamp' => 'Timestamp',
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

		$criteria->compare('mnid',$this->mnid);

		$criteria->compare('menuname',$this->menuname,true);

		$criteria->compare('linkpath',$this->linkpath,true);

		$criteria->compare('linktitle',$this->linktitle,true);

		$criteria->compare('status',$this->status);

		$criteria->compare('timestamp',$this->timestamp,true);

		return new CActiveDataProvider('Menulinks', array(
			'criteria'=>$criteria,
		));
	}
}