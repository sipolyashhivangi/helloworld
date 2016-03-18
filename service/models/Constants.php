<?php

/**
 * This is the model class for table "constants".
 *
 */
class Constants extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return Constants the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }


    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'constants';
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
        array('constantid, constantname, constanttype, constantvalue, startdate', 'required'),
        array('constantid, constantvalue', 'numerical', 'integerOnly' => true),
        array('constantname', 'length', 'max' => 255),
        array('constanttype', 'length', 'max' => 50),
        array('enddate', 'safe'),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('constantid, constantname, constanttype, constantvalue, startdate, enddate', 'safe', 'on' => 'search'),
        );
    }


    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
        'constantid' => 'Constantid',
        'constantname' => 'Constantname',
        'constanttype' => 'Constanttype',
        'constantvalue' => 'Constantvalue',
        'startdate' => 'Startdate',
        'enddate' => 'Enddate',
        );
    }


    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('constantid', $this->constantid);

        $criteria->compare('constantname', $this->constantname, true);

        $criteria->compare('constanttype', $this->constanttype, true);

        $criteria->compare('constantvalue', $this->constantvalue);

        $criteria->compare('startdate', $this->startdate, true);

        $criteria->compare('enddate', $this->enddate, true);

        return new CActiveDataProvider('Constants', array(
        'criteria' => $criteria,
        ));
    }


}
