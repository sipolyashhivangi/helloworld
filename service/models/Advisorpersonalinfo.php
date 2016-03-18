<?php

/**
 * This is the model class for table "advisorpersonalinfo".
 *
 */
class Advisorpersonalinfo extends CActiveRecord {

    public $advisor_id;
    public $firstname;
    public $lastname;
    public $advisortype;
    public $firmname;
    public $description;
    public $designation;
    public $areaofspez;
    public $stateregistered;
    public $avgacntbalanceperclnt;
    public $minasstsforpersclient;
    public $typeofcharge;
    public $flexi1;
    public $flexi2;
    public $flexi3;
    public $profilepic;
    public $individualcrd;
    
    public $notify;
    public $unassign;
    public $advhash;
    public $productservice;

    /**
     * Returns the static model of the specified AR class.
     * @return Advisorpersonalinfo the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }


    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'advisorpersonalinfo';
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
        //array('advisor_id, firstname, lastname, firmname, avgacntbalanceperclnt, minasstsforpersclient, typeofcharge, individualcrd', 'required'),
        array('advisor_id,individualcrd', 'numerical', 'integerOnly' => true),
        //array('firstname, lastname', 'length', 'max'=>100),
        //array('advisortype', 'length', 'max'=>7),
        //array('firmname', 'length', 'max'=>200),
        // Increasing the length for typeofcharge, bacause of change in requirement.
        //array('typeofcharge', 'length', 'max'=>11),
        //array('typeofcharge', 'length', 'max'=>255),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('advisor_id, firstname, lastname, advisortype, firmname, description, designation, areaofspez, stateregistered, avgacntbalanceperclnt, minasstsforpersclient, typeofcharge, individualcrd, flexi1, flexi2, flexi3', 'safe', 'on' => 'search'),
        );
    }


    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        'adv' => array(self::BELONGS_TO, 'User', 'advisor_id'),
        'users' => array(self::MANY_MANY, 'User', 'consumervsadvisor(user_id, advisor_id)'),
        );
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
        'advisor_id' => 'advisor_id',
        'firstname' => 'Firstname',
        'lastname' => 'Lastname',
        'advisortype' => 'Advisortype',
        'firmname' => 'Firmname',
        'description' => 'Description',
        'designation' => 'Designation',
        'areaofspez' => 'Areaofspez',
        'stateregistered' => 'Stateregistered',
        'avgacntbalanceperclnt' => 'Avgacntbalanceperclnt',
        'minasstsforpersclient' => 'Minasstsforpersclient',
        'typeofcharge' => 'Typeofcharge',
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

        $criteria->compare('advisor_id', $this->advisor_id);

        $criteria->compare('firstname', $this->firstname, true);

        $criteria->compare('lastname', $this->lastname, true);

        $criteria->compare('advisortype', $this->advisortype, true);

        $criteria->compare('firmname', $this->firmname, true);

        $criteria->compare('description', $this->description, true);

        $criteria->compare('designation', $this->designation, true);

        $criteria->compare('areaofspez', $this->areaofspez, true);

        $criteria->compare('stateregistered', $this->stateregistered, true);

        $criteria->compare('avgacntbalanceperclnt', $this->avgacntbalanceperclnt);

        $criteria->compare('minasstsforpersclient', $this->minasstsforpersclient);

        $criteria->compare('typeofcharge', $this->typeofcharge, true);

        $criteria->compare('flexi1', $this->flexi1, true);

        $criteria->compare('flexi2', $this->flexi2, true);

        $criteria->compare('flexi3', $this->flexi3, true);

        return new CActiveDataProvider('Advisorpersonalinfo', array(
        'criteria' => $criteria,
        ));
    }


}
