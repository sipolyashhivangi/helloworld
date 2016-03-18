<?php

/**
 * This is the model class for table "rolepermission".
 */
class Rolepermission extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return Rolepermission the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }


    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'rolepermission';
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
        array('roleid, moduleid, permission, startdate, enddate', 'required'),
        array('roleid, moduleid, permission, startdate, enddate', 'numerical', 'integerOnly' => true),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('roleid, moduleid, permission, startdate, enddate', 'safe', 'on' => 'search'),
        );
    }


    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        'role' => array(self::BELONGS_TO, 'Otlt', 'roleid'),
        'module' => array(self::BELONGS_TO, 'Otlt', 'moduleid'),
        );
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
        'roleid' => 'Roleid',
        'moduleid' => 'Moduleid',
        'permission' => 'Permission',
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

        $criteria->compare('roleid', $this->roleid);

        $criteria->compare('moduleid', $this->moduleid);

        $criteria->compare('permission', $this->permission);

        $criteria->compare('startdate', $this->startdate);

        $criteria->compare('enddate', $this->enddate);

        return new CActiveDataProvider('Rolepermission', array(
        'criteria' => $criteria,
        ));
    }


}
