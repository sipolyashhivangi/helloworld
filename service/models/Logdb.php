<?php

/**
 * This is the model class for table "logdb".
 *
 */
class Logdb extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return Logdb the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }


    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'logdb';
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
        array('user_id, moduleid, details, timestamp', 'required'),
        array('user_id, moduleid', 'numerical', 'integerOnly' => true),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('user_id, moduleid, details, timestamp', 'safe', 'on' => 'search'),
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
        'user_id' => 'Uid',
        'moduleid' => 'Moduleid',
        'details' => 'Details',
        'timestamp' => 'Timestamp',
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

        $criteria->compare('moduleid', $this->moduleid);

        $criteria->compare('details', $this->details, true);

        $criteria->compare('timestamp', $this->timestamp, true);

        return new CActiveDataProvider('Logdb', array(
        'criteria' => $criteria,
        ));
    }


}
