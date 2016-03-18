<?php
/**********************************************************************
* Filename: MixPanelClient.php
* Folder: components
* Description: Mixpanel Component class for tracking events from server side
* @author Chaya Sathish (For TruGlobal Inc)
* @copyright (c) 2013 - 2014
* Change History:
* Version         Author               Change Description
**********************************************************************/
//include mixpanel php library
require_once(realpath(dirname(__FILE__) . '/../lib/mixpanel/Mixpanel.php'));

class MixPanelClient extends CApplicationComponent {

    public $token;
    public $status;

    public function __Construct() {
        $this->status = Yii::app()->params['mixpanel']['status'];
        $this->token = Yii::app()->params['mixpanel']['token'];
    }

}

?>