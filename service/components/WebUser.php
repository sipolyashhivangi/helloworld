<?php
/**********************************************************************
* Filename: WebUser.php
* Folder: components
* Description: WebUser component class
* @author Subramanya HS (For TruGlobal Inc)
* @copyright (c) 2012 - 2013
* Change History:
* Version         Author               Change Description
**********************************************************************/
class WebUser extends CWebUser {

    public $allowAutoLogin = true;
    public $loginRequiredAjaxResponse = 'Login Required!';
    
// Store model to not repeat query.
    private $_model;

    public function toJSON() {
        return CJSON::encode(array(
                    'username' => $this->name,
                    'id' => $this->id,
                ));
    }

// Return first name.
// access it by Yii::app()->user->first_name
    function getFirst_Name() {
        $user = $this->loadUser(Yii::app()->user->id);
        return $user->first_name;
    }

// This is a function that checks the field 'role'
// in the User model to be equal to 1, that means it's admin
// access it by Yii::app()->user->isAdmin()
    function isAdmin() {
        $user = $this->loadUser(Yii::app()->user->id);
        return intval($user->role) == 1;
    }

// Load user model.
    protected function loadUser($id = null) {
        if ($this->_model === null) {
            if ($id !== null)
                $this->_model = User::model()->findByPk($id);
        }
        return $this->_model;
    }

}
