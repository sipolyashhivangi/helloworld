<?php

/**
 * This is the model class for table "advisorpersonalinfo".
 *
 */
class Designation extends CActiveRecord
{
	public $advisor_id;
	public $desig_name = array(0 => array('name'=>'CFP',
										'id'=>'3016'
									 	),
							   1 => array('name'=>'AAMS',
										'id'=>'3017'
									 	),
							   2 => array('name'=>'ChFC',
										'id'=>'3018'
									 	),
							   3 => array('name'=>'PFS',
										'id'=>'3019'
									 	),
							   4 => array('name'=>'CLU',
										'id'=>'3020'
									 	),
							   5 => array('name'=>'CEP',
										'id'=>'3021'
									 	),
							   6 => array('name'=>'AEP',
										'id'=>'3022'
									 	),
							   7 => array('name'=>'CIMA',
										'id'=>'3023'
									 	),
							   8 => array('name'=>'CRPS',
										'id'=>'3024'
									 	),
							   9 => array('name'=>'AWMA',
										'id'=>'3025'
									 	),
							   10 => array('name'=>'CRPC',
										'id'=>'3026'
									 	),
							   11 => array('name'=>'RFC',
										'id'=>'3027'
									 	),
							   12=> array('name'=>'CPA',
										'id'=>'3028'
									 	),
							   13=> array('name'=>'AIF',
										'id'=>'3029'
									 	),
							   14=> array('name'=>'Other',
											'id'=>'9999'),
										);
	public $status;
	public $other;
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
		return 'adv_designations';
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

		$criteria->compare('advisor_id', $this->advisor_id);


		return new CActiveDataProvider('Designation', array(
			'criteria'=>$criteria,
		));
	}
}