<?php
return array(

    'db.username' => 'leapdbadmin',
    'db.password' => 'k$REW;l',
    'db.name'     => 'leapscoremaster', //leapscore database V6
    'db.name2'    => 'leapscoremeta', //storing meta information about yodlee and external sources etc
    'db.cms'      => 'cms', // DB added for Wordpress
    'db.host'     => 'localhost',

    'smtp.username' => 'email', //optional
    'smtp.password' => 'pass',  //optional

    'cashedge.partnerId' => '55550050',
    'cashedge.homeId' => '55550051',
    'cashedge.adminUserId' => 'LeapScoreadmn',
    'cashedge.adminUserPassword' => 'cashedge1',
    'cashedge.url' => 'https://websrviqa.wm.cashedge.com/WealthManagementWeb/ws/',

    'env' => 'private',
    'transPerPage' => 20,
    'mailType' => 0, //0 for plain text template 1 for html template

    'calcXML.username' =>"valleywealth",
    'calcXML.password' =>"4vAlleyW-88",
    'calcXML.serviceURL'=> "http://www.calcxml.com/services/",

    'wordpress.username'=>"ls@cms",
    'wordpress.password'=>'k$REW;l',
    'wordpress.xmlrpcUrl'=>"http://localhost/Flexscore/cms/xmlrpc.php",

    'applicationUrl' => "http://localhost/Flexscore/",
    'batchpath' => "/var/www/dev/scripts/batchfiles",
    'cePrefix' => "fsdev",

    'stripe.secret_key' => "sk_test_CpF5SsZyKrGR0MqmLzcuRNeq",
    'stripe.publishable_key' => "pk_test_OK3GC04t6AVacOP9ICofiXId",

    'mixpanel' => [
        'token' => '0bab57138ebc59b05d0d3a610197ff23'
    ],

    'urbanairship' => [
      'dev' => [
        'key'       => 'x4yGnWm1SgGe7Ij9FN1Zhw',
        'secret'    => 'rnlaQc9aQdmyeqCTHKyEgA',
      ]
    ],

    'mailchimp' => [
        'key' => 'd9e57be1d3b8897e043101dc17780d75-us4',
        'list' => [
            'LoggedIn' => "a8126cbdb3",
        ],
    ],

    'mandrill' => [
        'key' => 'ewabyj3VZLAoQHFftcrk9w',
    ]
);
