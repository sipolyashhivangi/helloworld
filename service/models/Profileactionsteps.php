<?php

/**
 * This is the model class for table "profileactionsteps".
 */
class Profileactionsteps extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return Profileactionsteps the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }


    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'profileactionsteps';
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
        array('user_id, actionsteps, actionstatus, lastmodifiedtime', 'required'),
        array('user_id', 'numerical', 'integerOnly' => true),
        array('actionsteps, actionstatus', 'length', 'max' => 100),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('user_id, actionsteps, actionstatus, lastmodifiedtime', 'safe', 'on' => 'search'),
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
        'actionsteps' => 'Actionsteps',
        'actionstatus' => 'Actionstatus',
        'lastmodifiedtime' => 'Lastmodifiedtime',
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

        $criteria->compare('actionsteps', $this->actionsteps, true);

        $criteria->compare('actionstatus', $this->actionstatus, true);

        $criteria->compare('lastmodifiedtime', $this->lastmodifiedtime, true);

        return new CActiveDataProvider('Profileactionsteps', array(
        'criteria' => $criteria,
        ));
    }


}
