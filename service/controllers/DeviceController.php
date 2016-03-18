<?php

class DeviceController extends Controller
{
    /**
     * This is a temporary alias method for create() that can be safely deleted
     * once the Mobile App is relying on the REST API.
    **/
    public function actionCreate ()
    {
        $user       = Yii::app()->getSession()->get('wsuser');
        $user_id    = isset($user->id) ? $user->id : false;
        if (!$user_id) {
            $this->sendResponse(
                200,
                CJSON::encode(
                    array(
                        "status" => "ERROR",
                        "message" => "Session Expired"
                    )
                )
            );
            die;
        }
        return $this->create();
    }
    /**
     * This is a temporary alias method for delete() that can be safely deleted
     * once the Mobile App is relying on the REST API.
    **/
    public function actionDelete ()
    {
        $user       = Yii::app()->getSession()->get('wsuser');
        $user_id    = isset($user->id) ? $user->id : false;
        if (!$user_id) {
            $this->sendResponse(
                200,
                CJSON::encode(
                    array(
                        "status" => "ERROR",
                        "message" => "Session Expired"
                    )
                )
            );
            die;
        }
        return $this->delete();
    }
    public function accessRules ()
    {
        return array_merge(
            array(
                array(
                    'allow',
                    'users' => array('?')
                )
            ),
            parent::accessRules() // Include parent access rules
        );
    }
    public function create ()
    {

        $params = $_POST;
        if (Yii::app()->getRequest()->getIsPutRequest()) {
            $params = Yii::app()->getRequest()->restParams;
        }
        if (empty($params)) {
            $this->sendResponse(
                200,
                CJSON::encode(
                    array(
                        "status" => "ERROR",
                        "message" => "Please make sure you have specified the required parameters in application/x-www-form-urlencoded format."
                    )
                )
            );
            die;
        }

        $user       = Yii::app()->getSession()->get('wsuser');
        $user_id    = isset($user->id) ? $user->id : false;
        if (!$user_id) {
            $this->sendResponse(
                200,
                CJSON::encode(
                    array(
                        "status" => "ERROR",
                        "message" => "Session Expired"
                    )
                )
            );
        }
        $device = new Device();
        $token = isset($params['token']) ? $params['token']: false;
        if (!$token) {
            $this->sendResponse(
                200,
                CJSON::encode(
                    array(
                        "status" => "ERROR",
                        "message" => "Missing token."
                    )
                )
            );
            die;
        }
        $os = isset($params['os']) ? $params['os']: false;
        if (!$os) {
            $this->sendResponse(
                200,
                CJSON::encode(
                    array(
                        "status" => "ERROR",
                        "message" => "Missing OS."
                    )
                )
            );
            die;
        }

        $condition    = "user_id <> :user_id and token = :token and os = :os";
        $params       = array('user_id' => $user_id, 'token' => $token,  'os' => $os);
        $device->deleteAll($condition, $params);

        $criteria = new CDbCriteria();
        $criteria->condition = "user_id = :user_id";
        $criteria->addCondition("token = :token");
        $criteria->addCondition("os = :os");
        $criteria->params = array(
          ':user_id' => $user_id,
          ':token' => $token,
          ':os' => $os
        );
        $oldDevice = $device->find($criteria);
        if ($oldDevice) {
           $output = array(
                "status" => "SUCCESS",
                "message" => "Device saved.",
                "device" => $oldDevice->getAttributes(false)
            );
            $this->sendResponse(
                200,
                CJSON::encode($output)
            );
            die;
        }
        $device->isNewRecord = true;
        $device->user_id = $user_id;
        $device->token = $token;
        $device->os = $os;
        if ($device->save()) {
            $output = array(
                "status" => "SUCCESS",
                "message" => "Device saved.",
                "device" => $device->getAttributes(false)
            );
            $this->sendResponse(
                200,
                CJSON::encode($output)
            );
        } else {
            $this->sendResponse(
                200,
                CJSON::encode(
                    array(
                        "status" => "ERROR",
                        "message" => "Device could not be saved."
                    )
                )
            );
        }
    }
    public function delete ()
    {
        $device = new Device();
        $condition    = "id = :id";
        $params       = array(':id' => $_GET['id']);
        if ($device->deleteAll($condition, $params)) {
            $this->sendResponse(
                200,
                CJSON::encode(
                    array(
                        "status" => "SUCCESS",
                        "message" => "Device deleted."
                    )
                )
            );
        } else {
            $this->sendResponse(
                200,
                CJSON::encode(
                    array(
                        "status" => "ERROR",
                        "message" => "Device could not be deleted."
                    )
                )
            );
        }
    }
}
?>
