<?php

require_once 'lib/autoload.php';
use UrbanAirship\Airship;
use UrbanAirship\UALog;
use UrbanAirship\Push as P;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

class NotificationController extends Controller
{
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
    /**
     * This is a temporary alias method for create() that can be safely deleted
     * once the Mobile App team is done with development.
    **/
    public function actionCreate ()
    {
        $user       = Yii::app()->getSession()->get('wsuser');
        $user_id    = isset($user->id) ? $user->id : false;

        $config = file_exists('config/params-local.php')
                ? require('config/params-local.php')
                : array();
        $params = $_POST;
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

        if (!isset($config['urbanairship'])) {
            $this->sendResponse(
                200,
                CJSON::encode(
                    array(
                        "status" => "ERROR",
                        "message" => "UrbanAirship not configured."
                    )
                )
            );
            die;
        }
        if (!isset($config['urbanairship']['key'])) {
            $this->sendResponse(
                200,
                CJSON::encode(
                    array(
                        "status" => "ERROR",
                        "message" => "UrbanAirship not configured."
                    )
                )
            );
            die;
        }
        if (!isset($config['urbanairship']['secret'])) {
            $this->sendResponse(
                200,
                CJSON::encode(
                    array(
                        "status" => "ERROR",
                        "message" => "UrbanAirship not configured."
                    )
                )
            );
            die;
        }

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

        $message = isset($params['message']) ? $params['message']: false;
        if (!$message) {
            $this->sendResponse(
                200,
                CJSON::encode(
                    array(
                        "status" => "ERROR",
                        "message" => "Missing message."
                    )
                )
            );
            die;
        }

        UALog::setLogHandlers(
            array(
                new RotatingFileHandler("/tmp/ualog")
            )
        );
        $airship = new Airship(
            $config['urbanairship']['key'],
            $config['urbanairship']['secret']
        );
        $response = $airship->push()
                    ->setAudience(P\deviceToken($token))
                    ->setNotification(P\notification($message))
                    ->setDeviceTypes(P\all)
                    ->send();
        if (!$response) {
            $this->sendResponse(
                200,
                CJSON::encode(
                    array(
                        "status" => "ERROR",
                        "message" => "Message could not be sent."
                    )
                )
            );
            die;
        } else {
            $this->sendResponse(
                200,
                CJSON::encode(
                    array(
                        "status" => "SUCCESS",
                        "message" => "Message was successfully sent."
                    )
                )
            );
            die;
        }
    }
}
?>
