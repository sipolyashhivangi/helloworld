<?php

class ApiController extends Controller
{
    private $request,
            $storage,
            $server,
            $response;

    public function accessRules ()
    {
        return array_merge(
                array(array('allow', 'users' => array('?'))),
                // Include parent access rules
                parent::accessRules()
        );
    }
    private function initServer ()
    {
        $params = file_exists('config/params-local.php')
                ? require('config/params-local.php')
                : array();

        $config = <<<________CONFIG
            {
                "oauth":{
                    "autoloader":{
                        "name":"OAuth2\\\\Autoloader",
                        "path":"lib/OAuth2/Autoloader.php"
                    }
                },
                "database":{
                    "dsn":"mysql:dbname={$params['db.name']};host=localhost",
                    "username":"{$params['db.username']}",
                    "password":"{$params['db.password']}"
                }
            }
________CONFIG;
        $config = json_decode($config, 1);

        require_once($config['oauth']['autoloader']['path']);
        $config['oauth']['autoloader']['name']::register();

        $this->request    = \OAuth2\Request::createFromGlobals();
        $this->storage    = new \OAuth2\Storage\FlexscorePdo(
            array(
                'dsn'       => $config['database']['dsn'],
                'username'  => $config['database']['username'],
                'password'  => $config['database']['password']
            )
        );
        $config = array(
            'allow_public_clients' => true
        );
        $this->server     = new \OAuth2\Server($this->storage);
        $this->server->addGrantType(new \OAuth2\GrantType\FlexscoreClientCredentials($this->storage));
        $this->server->addGrantType(new \OAuth2\GrantType\FlexscoreUserCredentials($this->storage));
        $this->server->addGrantType(new \OAuth2\GrantType\RefreshToken($this->storage));
    }
    private function authorize ()
    {
        $this->initServer();

        $this->server->verifyResourceRequest($this->request);
        $this->server->handleTokenRequest($this->request);

        if (!$this->server->verifyResourceRequest($this->request)):
            Yii::app()->session->destroy();
            $this->server->getResponse()->send();
            die;
        endif;
        $token = str_replace('Bearer ', '', $this->request->headers('AUTHORIZATION'));
        $token = $this->storage->getAccessToken($token);
        $user_id = $token['user_id'];
        Yii::app()->getSession()->add(
            'wsuser',
            (object)$this->storage->getUser($user_id)
        );

    }

    function actionLogin ()
    {
    }

    function actionLogout ()
    {
    }

    /**
     * This method is called with
     * POST http://www.flexscore.com.dev/service/api/v2/token
    **/
    function actionToken ()
    {
        $this->initServer();
        $request = \OAuth2\Request::createFromGlobals();
        $this->server->handleTokenRequest($request)->send();
    }

    /**
     * This method is called with
     * GET http://www.flexscore.com.dev/service/api/v2/<model:\w+>/<id:\d+>
    **/
    function actionView ()
    {
        $this->call_internal_api('view');
    }

    /**
     * This method is called with
     * GET http://www.flexscore.com.dev/service/api/v2/<model:\w+>
    **/
    function actionList ()
    {
        $this->call_internal_api('list');
    }

    /**
     * This method is called with
     * PUT http://www.flexscore.com.dev/service/api/v2/<model:\w+>/<id:\d+>
    **/
    function actionUpdate ()
    {
        $php_input = file_get_contents('php://input');
        parse_str($php_input, $_POST);
        $this->call_internal_api('update');
    }

    /**
     * This method is called with
     * DELETE http://www.flexscore.com.dev/service/api/v2/<model:\w+>/<id:\d+>
    **/
    function actionDelete ()
    {
        $this->call_internal_api('delete');
    }

    /**
     * This method is called with
     * POST http://www.flexscore.com.dev/service/api/v2/<model:\w+>
    **/
    function actionCreate ()
    {
        $this->call_internal_api('create');
    }

    private function call_internal_api ($action)
    {
        header('Content-Type:application/json');

        $ini = parse_ini_file('config/api-map.ini', 1);

        if (!$ini['enabled']) {
            $this->authorize();
            $response = $this->server->getResponse();
            $response->setStatusCode(503);
            $response->addParameters(
                array(
                    'message' => 'API is disabled.',
                )
            );
            $response->send();
            die;
        }
        $pathinfo   = Yii::app()->request->pathInfo;
        $pathinfo   = explode('/', $pathinfo, 3);
        $pathinfo   = explode('/', $pathinfo[2]);
        $model      = strtolower($pathinfo[0]);
        $id         = !isset($pathinfo[1])
                    ? false
                    : strtolower($pathinfo[1]);
        $submodel   = !isset($pathinfo[2])
                    ? false
                    : strtolower($pathinfo[2]);
        if (!!$id) {
            $_POST['id']        =
            $_GET['id']         =
            $_GET['fiid']       =
            $_GET['uid']        =
            $_GET['accTok']     =
            $_GET['debt']       =
            $_GET['insurance']  =
            $_GET['action']     =
            $_GET['user_id']    =
            $_GET['range']      =
            $_GET['asset']      =
            $_GET['forceUser']  =
            $_GET['stepscount'] =
            $_GET['type']       =
            $_GET['email']      =
            $_GET['code']       =
            $_GET['finame']     =
            $_GET['cid']        =
            $_GET['AcctId']     =
            $_GET['risk_value'] =
            $_GET['sess']       = $id;
        }
        if (!!$submodel) {
            if (!isset($ini[$submodel]['oauth']) || true == $ini[$submodel]['oauth']) {
                $this->authorize();
            }
            if (!isset($ini[$submodel][$action]))
            {
                $response = $this->server->getResponse();
                $response->addParameters(
                    array(
                        'message' => "Action '{$action}' not defined for submodel '{$submodel}' of model '{$model}'.",
                    )
                );
                $response->send();
                die;
            }
            $method     = $ini[$submodel][$action]['method'];
            $model      = $ini[$submodel][$action]['model'];
        } else {
            if (!isset($ini[$model]['oauth']) || true == $ini[$model]['oauth']) {
                $this->authorize();
            }
            if (!isset($ini[$model][$action])) {
                $response = $this->server->getResponse();
                $response->addParameters(
                    array(
                        'message' => "Action '{$action}' not defined for model '{$model}'.",
                    )
                );
                $response->send();
                die;
            }
            $method     = $ini[$model][$action]['method'];
            $model      = $ini[$model][$action]['model'];
        }
        $model = new $model(1);
        $data = $model->{$method}();
        if (!$data) {
            $response = $this->server->getResponse();
            $response->addParameters(
                array(
                    'message' => 'Something went wrong.',
                )
            );
            $response->send();
        }
    }

}
?>
