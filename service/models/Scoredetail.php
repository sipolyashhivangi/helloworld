<?php

/**
 * This is the model class for table "scoredetail".
 */
class Scoredetail extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return Scoredetail the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }


    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'scoredetail';
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
        array('user_id, quidamountscore, latestscore, timestamp, flexi1, flexi2, flexi3', 'required'),
        array('user_id, latestscore', 'numerical', 'integerOnly' => true),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('user_id, quidamountscore, latestscore, timestamp, flexi1, flexi2, flexi3', 'safe', 'on' => 'search'),
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
        'user_id' => 'Uid',
        'quidamountscore' => 'Quidamountscore',
        'latestscore' => 'Latestscore',
        'timestamp' => 'Timestamp',
        'flexi1' => 'Flexi1',
        'flexi2' => 'Flexi2',
        'flexi3' => 'Flexi3',
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

        $criteria->compare('user_id', $this->user_id);

        $criteria->compare('quidamountscore', $this->quidamountscore, true);

        $criteria->compare('latestscore', $this->latestscore);

        $criteria->compare('timestamp', $this->timestamp, true);

        $criteria->compare('flexi1', $this->flexi1, true);

        $criteria->compare('flexi2', $this->flexi2, true);

        $criteria->compare('flexi3', $this->flexi3, true);

        return new CActiveDataProvider('Scoredetail', array(
        'criteria' => $criteria,
        ));
    }


}
