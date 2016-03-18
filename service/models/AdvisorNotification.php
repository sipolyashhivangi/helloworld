<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(-1);

$autoload = realpath(dirname(__DIR__).'/lib/autoload.php');
require $autoload;
use UrbanAirship\Airship;
use UrbanAirship\UALog;
use UrbanAirship\Push as P;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

/**
 * This is the model class for table "advisornotification".
 *
 */
class AdvisorNotification extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return Account the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'advisornotification';
    }

    protected function afterSave ()
    {
        if ($this->status === 1) return parent::afterSave();
        $config = realpath(dirname(__DIR__).'/config/params-local.php');
        $config = file_exists($config)
                ? require($config)
                : false;
        if (!$config):
            file_put_contents('/tmp/config-not-found', "File ({$config}) not found.");
        endif;
        if(isset($this->user_id)):
            $devices = Device::model()->findAll("user_id=:user_id", ["user_id" => $this->user_id]);
            if (count($devices) > 0):
                foreach ($devices as $device):
                $airship = new Airship(
                    $config['urbanairship']['dev']['key'],
                    $config['urbanairship']['dev']['secret']
                );
                try {
                    $response = $airship->push()
                                ->setAudience(P\deviceToken($device->token))
                                ->setNotification(P\notification($notification))
                                ->setDeviceTypes(P\all)
                                ->send();
                } catch (Exception $e) {}
                endforeach;
            endif;
        endif;
        return parent::afterSave();

    }

}

?>
