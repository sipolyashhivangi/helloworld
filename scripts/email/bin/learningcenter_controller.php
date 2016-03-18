<?php
final class LearningCenter_Controller
{
    final public function run ($config)
    {
        $model = new LearningCenter_Model($config);
        $users = $model->get_users();
        foreach ($users as $user):
            $learningcenter = $model->get_learningcenter($user['id']);
            if (empty($learningcenter)) continue;
            $post_ids = array();
            foreach ($learningcenter as $item):
                $post_ids[] = $item['post_id'];
            endforeach;
            $learningcenter_images = $model->get_learningcenter_images($post_ids);
            foreach ($learningcenter as &$item):
                if (!isset($item['post_id']) ||
                    !isset($learningcenter_images[$item['post_id']])) continue;
                if (!empty($learningcenter_images[$item['post_id']]['post_mime_type']) && $learningcenter_images[$item['post_id']]['post_mime_type'] == 'video/x-flv'):
                    $key = $learningcenter_images[$item['post_id']]['post_content'];
                    $url = "https://img.youtube.com/vi/{$key}/maxresdefault.jpg";
                else:
                    $url = $learningcenter_images[$item['post_id']]['guid'];
                endif;
                $item['image'] = $url;
            endforeach;
            $data = array(
                'learningcenter' => $learningcenter,
            );
            $email = new Email();
            $email->subject                 = 'Recommended Reading';
            $email->recipient['email']      = $user['email'];
            $email->recipient['name']       = "{$user['firstname']} {$user['lastname']}";
            $email->data['learning-center'] = $data;
            $email->data['unsubscribe']     = true;
            $email->data['recipient_type']  = 'us';
            $email->data['unsubscribe_code'] = $user['unsubscribecode'];
            $email->send();
        endforeach;
    }
}
?>
