<?php
final class ScoreChange_Controller
{
    final public function run ($config)
    {
        $model = new ScoreChange_Model($config);
        $users = $model->get_users();
        foreach ($users as $id => $user):
            $data = array(
                'score'         => $model->get_score($user['id']),
                'scorechange'   => $model->get_scorechange($user['id']),
                'actionstep'    => $model->get_actionstep($user['id']),
                'goals'         => $model->get_goals($user['id']),
            );
            $data['scoreimg'] = ceil($data['score']['totalscore']/50);
            if (!isset($data['scorechange']['scorechange']) ||
                empty($data['scorechange']['scorechange'])):
                $data['scorechange']['scorechange'] = 0;
            endif;
            $email = new Email();
            $email->subject                 = 'Your FlexScore Changed';
            $email->recipient['email']      = $user['email'];
            $email->recipient['name']       = "{$user['firstname']} {$user['lastname']}";
            $email->data['score-change']    = $data;
            $email->data['unsubscribe']     = true;
            $email->data['recipient_type']  = 'us';
            $email->data['unsubscribe_code'] = $user['unsubscribecode'];
            $email->intro                   = <<<____________BODY
...which is normal.
But, did it go in the direction you wanted?
There’s always room for improvement, right?
Your Action Steps are refreshed and ready for you now,
so log in to your FlexScore account to see how to make your score even better.
____________BODY;
            $email->send();
        endforeach;
    }
}
?>
