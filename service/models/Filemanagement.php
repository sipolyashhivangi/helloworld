<?php

/**
 * This is the model class for table "filemanagement".
 *
 */
class Filemanagement extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return Filemanagement the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }


    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'filemanagement';
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
        array('fid, user_id, filename, filepath, filetype, filesize, filetemporaryname', 'required'),
        array('fid, user_id, filesize', 'numerical', 'integerOnly' => true),
        array('filename', 'length', 'max' => 32),
        // The following rule is used by search().
        // Please remove those attributes that should not be searched.
        array('fid, user_id, filename, filepath, filetype, filesize, filetemporaryname', 'safe', 'on' => 'search'),
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
        'fid' => 'Fid',
        'user_id' => 'Uid',
        'filename' => 'Filename',
        'filepath' => 'Filepath',
        'filetype' => 'Filetype',
        'filesize' => 'Filesize',
        'filetemporaryname' => 'Filetemporaryname',
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

        $criteria->compare('fid', $this->fid);

        $criteria->compare('user_id', $this->user_id);

        $criteria->compare('filename', $this->filename, true);

        $criteria->compare('filepath', $this->filepath, true);

        $criteria->compare('filetype', $this->filetype, true);

        $criteria->compare('filesize', $this->filesize);

        $criteria->compare('filetemporaryname', $this->filetemporaryname, true);

        return new CActiveDataProvider('Filemanagement', array(
        'criteria' => $criteria,
        ));
    }


}
