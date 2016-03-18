<?php

/**
 * This is the model class for table "advisorpersonalinfo".
 *
 */
class Productservice extends CActiveRecord
{
//	remove if not needed 12/18/14
//	public $advisor_id;
//	public $productserviceid;
//	public $other;
	public $productservicename = array('401(K)' => '401(K)',
			'Tax Planning' => 'Tax Planning',
			'Insurance Planning' => 'Insurance Planning',
			'Debt Management' => 'Debt Management',
			'Annuities' => 'Annuities',
			'Long Term Care' => 'Long Term Care',
			'College Cost Planning' => 'College Cost Planning',
			'Investment Management' => 'Investment Management',
			'Retirement Planning' => 'Retirement Planning',
			'Estate Planning' => 'Estate Planning',
			'Other' => 'Other'
		);


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
		return 'productandservice';
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


		return new CActiveDataProvider('Productservice', array(
			'criteria'=>$criteria,
		));
	}
}