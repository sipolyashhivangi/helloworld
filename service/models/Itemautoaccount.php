<?php

/**
 * This is the model class for table "itemautoaccount".
 *
 */
class Itemautoaccount extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return Itemautoaccount the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }


    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'itemautoaccount';
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
        array('itemid, user_id', 'required'),
        array('itemid, user_id, accessstatus, isactive', 'numerical', 'integerOnly' => true),
        array('displayname', 'length', 'max' => 255),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('id, itemid, user_id, displayname, accessstatus, isactive', 'safe', 'on' => 'search'),
        );
    }


    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        'accounts' => array(self::HAS_MANY, 'Account', 'itemrefid'),
        'u' => array(self::BELONGS_TO, 'User', 'id'),
        );
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
        'id' => 'Id',
        'itemid' => 'Itemid',
        'user_id' => 'Uid',
        'displayname' => 'Displayname',
        'accessstatus' => 'Accessstatus',
        'isactive' => 'Isactive',
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

        $criteria->compare('id', $this->id);

        $criteria->compare('itemid', $this->itemid);

        $criteria->compare('user_id', $this->user_id);

        $criteria->compare('displayname', $this->displayname, true);

        $criteria->compare('accessstatus', $this->accessstatus);

        $criteria->compare('isactive', $this->isactive);

        return new CActiveDataProvider('Itemautoaccount', array(
        'criteria' => $criteria,
        ));
    }


}
