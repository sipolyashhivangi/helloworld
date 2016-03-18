<?php

/**
 * This is the model class for table "qstnandscorelkup".
 *
 */
class Qstnandscorelkup extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Qstnandscorelkup the static model class
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
		return 'qstnandscorelkup';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('qid, pid, question, score, constantid, affectedqid, affectedsection, startdate, enddate', 'required'),
			array('qid, pid, score, constantid, affectedqid', 'numerical', 'integerOnly'=>true),
			array('question, affectedsection', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('qid, pid, question, score, constantid, affectedqid, affectedsection, startdate, enddate', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'qid' => 'Qid',
			'pid' => 'Pid',
			'question' => 'Question',
			'score' => 'Score',
			'constantid' => 'Constantid',
			'affectedqid' => 'Affectedqid',
			'affectedsection' => 'Affectedsection',
			'startdate' => 'Startdate',
			'enddate' => 'Enddate',
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

		$criteria->compare('qid',$this->qid);

		$criteria->compare('pid',$this->pid);

		$criteria->compare('question',$this->question,true);

		$criteria->compare('score',$this->score);

		$criteria->compare('constantid',$this->constantid);

		$criteria->compare('affectedqid',$this->affectedqid);

		$criteria->compare('affectedsection',$this->affectedsection,true);

		$criteria->compare('startdate',$this->startdate,true);

		$criteria->compare('enddate',$this->enddate,true);

		return new CActiveDataProvider('Qstnandscorelkup', array(
			'criteria'=>$criteria,
		));
	}
}