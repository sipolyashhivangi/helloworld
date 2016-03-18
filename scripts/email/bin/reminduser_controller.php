<?php

require_once(realpath(dirname(__FILE__) . '/../../../service/helpers/PWGen.php'));
require_once(realpath(dirname(__FILE__) . '/../../../service/helpers/PasswordHash.php'));

final class RemindUser_Controller
{
    final public function run($config)
    {
        $model = new RemindUser_Model($config);
        $users = $model->get_users();
        if($users && count($users) > 0):
            foreach ($users as $user):

                // resetting password token
                $passwordGenerator = new PWGen(25, true, true, true, false, false, false);
                $password = $passwordGenerator->generate();
                $hasher = new PasswordHash(8, FALSE);
                $hashedPassword = $hasher->HashPassword($password);
                $model->update_user_passwordtokenkey($user['id'], $hashedPassword);

                $part = 'user-registration-by-advisor';
                $email = new Email();
                $email->subject = 'Welcome to FlexScore';
                $email->recipient['email']    = $user['email'];
                $email->data[$part] = [
                    'advisor-name' => "{$user['advisorfirstname']} {$user['advisorlastname']}",
                    'token' => $hashedPassword,
                ];

                $email->data['unsubscribe']   = true;
                $email->data['recipient_type']  = 'us';
                $email->data['unsubscribe_code'] = $user['unsubscribecode'];
                $email->send();
            endforeach;
        endif;
    }
}
?>
