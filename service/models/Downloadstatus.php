<?php

/**
 * This is the model class for table "downloadstatus".
 *
 */
class Downloadstatus extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return Downloadstatus the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }


    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'downloadstatus';
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
        array('did, user_id, dloadname, dloadstatus, errormessage, lastdownloadedtime', 'required'),
        array('did, user_id, dloadstatus', 'numerical', 'integerOnly' => true),
        array('dloadname, errormessage', 'length', 'max' => 255),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('did, user_id, dloadname, dloadstatus, errormessage, lastdownloadedtime', 'safe', 'on' => 'search'),
        );
    }


    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        'u' => array(self::BELONGS_TO, 'User', 'id'),
        );
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
        'did' => 'Did',
        'user_id' => 'Uid',
        'dloadname' => 'Dloadname',
        'dloadstatus' => 'Dloadstatus',
        'errormessage' => 'Errormessage',
        'lastdownloadedtime' => 'Lastdownloadedtime',
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

        $criteria->compare('did', $this->did);

        $criteria->compare('user_id', $this->user_id);

        $criteria->compare('dloadname', $this->dloadname, true);

        $criteria->compare('dloadstatus', $this->dloadstatus);

        $criteria->compare('errormessage', $this->errormessage, true);

        $criteria->compare('lastdownloadedtime', $this->lastdownloadedtime, true);

        return new CActiveDataProvider('Downloadstatus', array(
        'criteria' => $criteria,
        ));
    }


}
