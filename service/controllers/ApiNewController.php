<?php

class ApiNewController extends Controller
{
    private $request,
            $storage,
            $server,
            $response,
            $user_id,
            $private_fields = [ // NEVER VISIBLE
                'password'          => null,
                'pin'               => null,
            ],
            $protected_fields = [ // VISIBLE, BUT SET BY SYSTEM
                'id'                => null,
                'uid'               => null,
                'userid'            => null,
                'user_id'           => null,
                'createdtimestamp'  => null,
                'modifiedtimestamp' => null,
            ],
            $user_id_aliases = [
                'uid'               => null,
                'userid'            => null,
                'user_id'           => null,
            ];

    private function get_user_id_field ($attributes)
    {
        return array_intersect_key($attributes, $this->user_id_aliases);
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
        $this->user_id = Yii::app()->getSession()->get('wsuser')->id;
    }

    private function process_request ()
    {
/*
        $data           = [];
        $php_input      = file_get_contents('php://input');
        parse_str($php_input, $data);
*/
        $input = [
            'rawbody'       => Yii::app()->request->getRawBody(),
            'json'          => json_decode(Yii::app()->request->getRawBody(), 1),
            'restparams'    => Yii::app()->request->getRestParams(),
            'request'       => $_REQUEST,
        ];
        $input = json_decode(Yii::app()->request->getRawBody(), 1);
        $ini   = parse_ini_file('config/api-map.v3.ini', 1);

        $primary_model = (object)[
            'id'        =>  null,
            'name'      =>  null,
        ];
        $related_model = (object)[
            'id'        =>  null,
            'name'      =>  null,
        ];

        $model = (object)[
            'id'        =>  null,
            'name'      =>  null,
            'file'      =>  null,
        ];
        $submodel = (object)[
            'id'        =>  null,
            'name'      =>  null,
            'file'      =>  null,
        ];
        $model->id      = !isset($_REQUEST['model_id'])
                        ? false
                        : $_REQUEST['model_id'];
        $model->name    = !isset($_REQUEST['model'])
                        ? false
                        : strtolower($_REQUEST['model']);
        $model->file    = !isset($ini['Models'][$model->name])
                        ? false
                        : $ini['Models'][$model->name];
        $submodel->id   = !isset($_REQUEST['submodel_id'])
                        ? false
                        : $_REQUEST['submodel_id'];
        $submodel->name = !isset($_REQUEST['submodel'])
                        ? false
                        : strtolower($_REQUEST['submodel']);
        $submodel->file = !isset($ini['Models'][$submodel->name])
                        ? false
                        : $ini['Models'][$submodel->name];
        return (object)[
            'input'     => $input,
            'model'     => $model,
            'submodel'  => $submodel,
        ];
    }

    private function sanitize ($input)
    {
        return array_diff_key($input, $this->private_fields);
    }

    private function set_owner ($model)
    {
        foreach ($this->user_id_aliases as $a)
            if (property_exists($model, $a))
                $model->{$a} = $this->user_id;
//        return $model;
    }

    public function accessRules ()
    {
        return array_merge(
                array(array('allow', 'users' => array('*'))),
                parent::accessRules()
        );
    }

    public function actionToken ()
    {
        $this->initServer();
        $request = \OAuth2\Request::createFromGlobals();
        $this->server->handleTokenRequest($request)->send();
    }

    public function actionCreate ()
    {
        header('Content-Type:application/json');
        $this->authorize();
        $request = $this->process_request();
        $input = $this->sanitize($request->input);
        if (!$request->submodel->name):
            $model = $request->model->file;
            $model = $model::model();
            $model->setIsNewRecord(true);
            $model->setAttributes($input, false);
        else:
            $model = $request->submodel->file;
            $model = $model::model();
            $model->setIsNewRecord(true);
            $model->setAttributes($input, false);
        endif;
        $this->set_owner($model);
        $status = $model->save();
        $this->sendResponse(200, CJSON::encode([
            'status'    => $status ? 'OK' : 'ERROR',
            'response'  => $model
        ]));
    }

    public function actionUpdate ()
    {
        header('Content-Type:application/json');
        $this->authorize();
        $request = $this->process_request();
        $input = $this->sanitize($request->input);
        if (!$request->submodel->name):
            $model = $request->model->file;
            $model = $model::model()->findByPk($request->model->id);
            $model->setAttributes($input, false);
        else:
            $model = $request->submodel->file;
            $model = $model::model()->findByPk($request->submodel->id);
            $model->setAttributes($input, false);
        endif;
        $this->set_owner($model);
        $status = $model->save();
        $this->sendResponse(200, CJSON::encode([
            'status'    => $status ? 'OK' : 'ERROR',
            'response'  => $model
        ]));
    }

    public function actionReplace ()
    {
        header('Content-Type:application/json');
        $this->authorize();
        $request = $this->process_request();
        $input = $this->sanitize($request->input);
        if (!$request->submodel->name):
            $model = $request->model->file;
            $model = $model::model()->findByPk($request->model->id);
            $model->setAttributes($input, false);
        else:
            $model = $request->submodel->file;
            $model = $model::model()->findByPk($request->submodel->id);
            $model->setAttributes($input, false);
        endif;
        $this->set_owner($model);
        $status = $model->save();
        $this->sendResponse(200, CJSON::encode([
            'status'    => $status ? 'OK' : 'ERROR',
            'response'  => $model
        ]));
    }

    public function actionDelete ()
    {
        header('Content-Type:application/json');
        $this->authorize();
        $request = $this->process_request();
        if (!$request->submodel->name):
            $model = $request->model->file;
            $model = $model::model()->findByPk($request->model->id);
        else:
            $model = $request->submodel->file;
            $model = $model::model()->findByPk($request->submodel->id);
        endif;
        $status = $model->delete();
        $this->sendResponse(200, CJSON::encode([
            'status'    => $status ? 'OK' : 'ERROR',
            'response'  => $model
        ]));
    }

    public function actionView ()
    {
        header('Content-Type:application/json');
        $this->authorize();
        $request = $this->process_request();
        if (!$request->submodel->name):
            $model  = $request->model->file;
            $model  = $model::model()->findByPk($request->model->id);
        else:
            $model  = $request->submodel->file;
            $model  = $model::model()->findByPk($request->submodel->id);
        endif;
        $status = !!count($model);
        if (isset($model->password)) unset($model->password);
        if (isset($model->pin)) unset($model->pin);
        $this->sendResponse(200, CJSON::encode([
            'status'    => $status ? 'OK' : 'ERROR',
            'response'  => $model
        ]));
    }

    public function actionList ()
    {

        header('Content-Type:application/json');
        $this->authorize();
        $request = $this->process_request();

        $user_id_field_name = $this->get_user_id_field($model::model()->getAttributes());

        $model = $request->model->file;
        $primary_model = $model::model()->findAllByAttributes(array(
            $user_id_field_name =>$this->user_id
        ));

        $related_model = $model::model()->findAllByAttributes(array($user_id_field_name =>$this->user_id));

        $model = $request->model->file;
        $records = $model::model()->findAllByAttributes(array('uid'=>$this->user_id));
        if (!$records) $records = $model::model()->findAllByAttributes(array('userid'=>$this->user_id));
        if (!$records) $records = $model::model()->findAllByAttributes(array('user_id'=>$this->user_id));
        if ($request->submodel->name):
            $records = $model::model()->findAllByAttributes(array('user_id'=>$this->user_id))->getRelated($request->submodel->name);
        endif;
        $status = !!count($records);
        foreach ($records as $m):
            if (isset($m->password)) unset($m->password);
            if (isset($m->pin)) unset($m->pin);
        endforeach;
        $this->sendResponse(200, CJSON::encode([
            'status'    => $status ? 'OK' : 'ERROR',
            'response'  => $records
        ]));
    }

}
?>
