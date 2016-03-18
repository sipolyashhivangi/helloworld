<?php
ini_set('display_errors',1);
error_reporting(-1);

$config = parse_ini_file('api-test.ini', true);
$handle = curl_init();
$output = [];

session_start();
if (!isset($_SESSION['token']) ||
    isset($_POST['close'])):
    $_SESSION['token'] = false;
endif;

if (!$_SESSION['token']):
    if (!isset($config['oauth']['password'])):
        $data = array(
            'grant_type'      => $config['oauth']['grant_type'],
            'client_id'       => $config['oauth']['client_id'],
            'client_secret'   => $config['oauth']['client_secret'],
            'email'        => $config['oauth']['email'],
            'password'        => false,
            'pin'             => $config['oauth']['pin']
        );
    else:
        $data = array(
            'grant_type'      => $config['oauth']['grant_type'],
            'client_id'       => $config['oauth']['client_id'],
            'client_secret'   => $config['oauth']['client_secret'],
            'email'        => $config['oauth']['email'],
            'password'        => $config['oauth']['password']
        );
    endif;
    $options = array(
        CURLOPT_URL => $config['oauth']['token_url'],
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $data
    );
    curl_setopt_array($handle, $options);
    $reply = curl_exec($handle);
    if($reply === false)
    {
        echo 'Curl error: ' . curl_error($handle);
    }
    else
    {
        $output[] = $reply;
        $reply = json_decode($reply,1);
    }
    if (isset($reply['access_token'])):
        $_SESSION['token'] = $reply['access_token'];
    endif;
endif;

$options = array(
    CURLOPT_URL => $config['oauth']['resource_url'],
    CURLOPT_HEADER => false,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => "access_token={$_SESSION['token']}"
);
curl_setopt_array($handle, $options);
$reply = curl_exec($handle);

if ($reply === false):
    echo 'Curl error: ' . curl_error($handle);
else:
    $output[] = $reply;
endif;

$command = "curl http://localhost/flexscore/service/lib/oauth2-server/resource.php -d 'access_token={$_SESSION['token']}'";
$now = microtime(1);
$output = join("\n-----\n", $output);

echo "<pre>{$now}\n{$command}\n--\n{$output}</pre>";

curl_close($handle);

?>
<form action='' method='post'>
    <button type='submit' name='refresh'>Refresh</button>
    <button type='submit' name='close'>Close</button>
</form>
