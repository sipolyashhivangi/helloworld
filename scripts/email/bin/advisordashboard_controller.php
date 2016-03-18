<?php
final class AdvisorDashboard_Controller
{
    final public function run($config)
    {
        $model = new AdvisorDashboard_Model($config);
        $advisors = $model->get_advisors();

        foreach ($advisors as $advisor):

            $advConsumers = $model->get_consumers($advisor['id']);
            $topScoreChangedConsumers = $model->get_top_scorechanged_consumers($advConsumers);

            if($topScoreChangedConsumers && count($topScoreChangedConsumers) > 0):

                $data = array(
                    'advConsumers'              => $advConsumers,
                    'topScoreChangedConsumers'  => $topScoreChangedConsumers
                );

                $email = new Email();
                $email->subject                     = 'Your Dashboard';
                $email->recipient['email']          = $advisor['email'];
                $email->recipient['name']           = "{$advisor['firstname']} {$advisor['lastname']}";
                $email->data['advisor-dashboard']   = $data;
                $email->data['unsubscribe']         = true;
                $email->data['recipient_type']      = 'ad';
                $email->data['unsubscribe_code']    = $advisor['unsubscribecode'];
                $email->send();

            endif;

        endforeach;
    }
}
?>
