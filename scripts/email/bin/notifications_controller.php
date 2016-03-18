<?php
final class Notifications_Controller
{
    final public function run ($config)
    {
        $model = new Notifications_Model($config);
        $users = $model->get_users();
        foreach ($users as $id => $user):
            $notifications = $model->get_notifications($user['id']);
            if (empty($notifications)) continue;
            foreach ($notifications as $key => $notification) {
                $info = json_decode($notification["info"], true);
                $notifications[$key]["info"]    = $info["finame"];
                $notifications[$key]["img"]     = "ep.gif";
                if ($notification["template"] == "info") {
                    $notifications[$key]["img"] = "cp.png";
                }
            }
            $data = array(
                'notifications' => array_values($notifications),
            );
            $email = new Email();
            $email->subject                 = 'Your Notifications';
            $email->recipient['email']      = $user['email'];
            $email->recipient['name']       = "{$user['firstname']} {$user['lastname']}";
            $email->data['notifications']   = $data;
            $email->data['unsubscribe']     = true;
            $email->data['recipient_type']  = 'us';
            $email->data['unsubscribe_code'] = $user['unsubscribecode'];
            $email->send();
        endforeach;
    }
}
?>
