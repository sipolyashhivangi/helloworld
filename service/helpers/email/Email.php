<?php
/**
 *******************************************************************************
 *  SAMPLE USAGE
 *******************************************************************************
    // Create a class instance...
    $email = new Email();
    // REQUIRED: Specify a subject.
    $email->subject                 = 'Welcome to FlexScore';
    // REQUIRED: Specify a recipient's email.
    $email->recipient['email']      = $user->email;
    // RECOMMENDED: Specify a recipient's name.
    $email->recipient['name']       = $user->name;
    // OPTIONAL: Specify a base url; defaults to https://www.flexscore.com
    $email->data['base-url']        = 'https://dev.flexscore.com/test';
    // OPTIONAL: Specify an unsubscribe url; defaults to nothing
    $email->data['unsubscribe']     = 'https://server';
    // OPTIONAL: Specify a namespaced ($TEMPLATE_NAME) array
    // of data for interpolation in a template
    $email->data[$TEMPLATE_NAME]    = [
        'param-1'   => 'value',
        'param-2'   => 'value',
        'param-3'   => [
            'subparam-1'    => 'value',
            'subparam-1'    => 'value',
            'subparam-1'    => 'value',
        ],
    ];
    // OPTIONAL: Specify some body text in lieu of
    // or in addition to template and data
    $email->body                    = '<p>Some body text...</p>';
    // Sends the email...
    // OPTIONAL: Specify some intro/subtitle text
    $email->intro                   = '<p>Some intro text...</p>';
    // Sends the email...
    $email->send();
**/
class Email
{
    const   TEST_MODE           = true, // When true, does not send email.
            TEST_OUTPUT_DIR     = 'C:\emails';
    private $DS                 = DIRECTORY_SEPARATOR,
            $mailchimp_lists    = null,
            $mailchimp          = null,
            $mandrill           = null,
            $mustache           = null,
            $message            = null,
            $template           = 'base'; // This is private for now to prevent overriding.
    public  $app_url            = null,
            $img_url            = null,
            $sender             = [
                'email' => 'noreply@flexscore.com',
                'name'  => 'FlexScore',
            ],
            $recipients         = [],
            $recipient          = [],
            $recipient_type     = null,
            $unsubscribe_code   = null,
            $intro              = null,
            $body               = null,
            $subject            = null,
            $data               = null;
    private function compare_emails ($a,$b)
    {
        return strcmp($a['email'], $b['email']);
    }
    private function load_requirements ()
    {
        $service_dir    = realpath(dirname(dirname(__DIR__)));
        require "{$service_dir}/lib/autoload.php";
        $config_dir     = "{$service_dir}{$this->DS}config";
        $config         = require "{$config_dir}{$this->DS}params-local.php";
        $api_ini        = parse_ini_file("{$config_dir}{$this->DS}apis.ini",1);
        $this->base_url = $config['applicationUrl'];
        $this->mailchimp = new Mailchimp( $api_ini['MailChimp']['key'] );
        $this->mailchimp_lists = new Mailchimp_Lists( $this->mailchimp );

        $this->mandrill = new Mandrill($api_ini['Mandrill']['key']);
        $this->mustache = new Mustache_Engine([
            'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . "{$this->DS}srv")
        ]);
        $this->template = $this->mustache->loadTemplate($this->template);
        $this->config = $config;
    }
    public function send ()
    {
        // Quick and dirty error-checking...
        if (!$this->subject)            die('No subject.');
        if (empty($this->recipient) &&
            empty($this->recipients)) die('No recipients.');
        // Load some requirements...
        if (!$this->mandrill) $this->load_requirements();
        // Data Pre-Processing...
        $this->data['app-url']      = !empty($this->data['base-url'])
                                    ? $this->data['base-url']
                                    : $this->base_url;
        $this->data['img-url']      = 'https://www.flexscore.com/';

/*
        // Mandrill's/MailChimp's Unsubscribe Tag (Beware!)
        $this->data['unsubscribe']  = !empty($this->data['unsubscribe'])
                                    ? $this->data['base-url']
                                    : '*|UNSUB:https://www.flexscore.com/unsubscribe|*';

        // FlexScore Subscription Management
        $this->data['unsubscribe']  = !empty($this->data['unsubscribe'])
                                    ? $this->data['base-url']
                                    : 'https://www.flexscore.com/unsubscribe?md_email='
                                        . urlencode($this->recipient['email']);

        $this->data['unsubscribe']  = false; // Hides the unsubscribe link
*/

        $this->data['unsubscribe']  = !empty($this->data['unsubscribe'])
                                    ? $this->data['unsubscribe']
                                    : false;

        $this->data['unsubscribe_code'] = !empty($this->data['unsubscribe_code'])
                                    ? $this->data['unsubscribe_code']
                                    : false;

        $this->data['recipient_type']  = !empty($this->data['recipient_type'])
                                    ? $this->data['recipient_type']
                                    : false;

        $this->data['intro']        = $this->intro;
        $this->data['body']         = $this->body;
        $this->data['subject']      = $this->subject;

        if (!empty($this->recipient)):
            $this->recipients[]         = $this->recipient;
            $this->data['recipient']    = $this->recipient['email'];
        else:
            $this->data['recipient']    = 'multiple recipients';
        endif;

        if (!!$this->data['unsubscribe']):
            $unsubscribed = [];
            $mc_response = $this->mailchimp->call('lists/member-info',
                [
                    'id'        => $this->config['mailchimp']['list']['LoggedIn'],
                    'emails'    => $this->recipients
                ]
            );
            if ($mc_response['errors']):
                foreach ($mc_response['errors'] as $error):
                    $unsubscribed[] = [
                        'email' => $error['email']['email']
                    ];
                endforeach;
            endif;
            if ($mc_response['data']):
                foreach ($mc_response['data'] as $data):
                    if ($data['status'] !== 'subscribed'):
                        $unsubscribed[] = [
                            'email' => $data['email']
                        ];
                    endif;
                endforeach;
            endif;
            $dont_send          = array_uintersect($this->recipients, $unsubscribed, array($this, 'compare_emails'));
            $this->recipients   = array_udiff($this->recipients, $dont_send, array($this, 'compare_emails'));
        endif;

        if (empty($this->recipients)) return;

        $html = $this->template->render($this->data);
        // Building the message array for Mandrill...
        $this->message['html']          = $html;
        $this->message['subject']       = $this->subject;
        $this->message['from_email']    = $this->sender['email'];
        $this->message['from_name']     = $this->sender['name'];
        $this->message['to']            = $this->recipients;
        // Send (or log) email...
        if (false === self::TEST_MODE) {
            // Use Mandrill to send...
            try {
                $async      = false;
                $ip_pool    = 'Main Pool';
                $send_at    = null; // null sends immediately;
                $result     = $this->mandrill->messages->send(
                                $this->message,
                                $async,
                                $ip_pool,
                                $send_at );
            } catch(Mandrill_Error $e) {
                echo 'A mandrill error occurred: ', get_class($e), ' - ', $e->getMessage();
                throw $e;
                return false;
            }
            return true;
        } else {
            //OR just write the file to testing directory...
    //        $now = microtime(true);
    //        file_put_contents(
    //        self::TEST_OUTPUT_DIR."{$this->DS}email_test_{$now}.html",
    //        $html.PHP_EOL.PHP_EOL.'<!--'.PHP_EOL.json_encode($this->message,JSON_PRETTY_PRINT).PHP_EOL.'-->'
    //        );
            return true;
        }
    }
}
?>
