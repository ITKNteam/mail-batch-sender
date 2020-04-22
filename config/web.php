<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'btlwest',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
     'runtimePath'=> $runtime,
     'modules'=>[
         'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ],
         'admin' => [
            'class' => '\mdm\admin\Module',
             'layout' => 'left-menu',
//            'menus' => [
//                'assignment' => [
//                    'label' => 'Grant Access' // change label
//                ],
//                'route' => null, // disable menu
//            ],
          // 'mainLayout' => '@app/views/layouts/admin.php',
            'controllerMap' => [
                 'assignment' => [
                    'class' => '\mdm\admin\controllers\AssignmentController',
                     'userClassName' => 'app\models\User',  // fully qualified class name of your User model
                    // Usually you don't need to specify it explicitly, since the module will detect it automatically
                    'idField' => 'id',        // id field of your User model that corresponds to Yii::$app->user->id
                    'usernameField' => 'username', // username field of your User model
                 //   'searchClass' => 'app\models\UserSearch'    // fully qualified class name of your User model for searching
                ]
            ],
        ]
    ],
    'components' => [
         'assetManager' => [
            
            //'sourcePath' => '@web',
            'basePath' =>  DATA_DIR, 
            'baseUrl' =>   BASE_URL,
         //   'basePath' =>  '@webroot', 
          //  'baseUrl' =>   '@web',
            'linkAssets' => true,
            'appendTimestamp' => true,
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '065a4fsJWabGAkjp4PoQ4-MlJjtmNOZh',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
             'rules' => [
                'site/login' => 'site/login',
                'site/logout' => 'site/logout',
                'site/error' => 'site/error',
                

                'loginsocial/<service:vkontakte|facebook|odnoklassniki>' => 'users/login',
            ],
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mail' => [
            
            'class' => 'yii\swiftmailer\Mailer',
         //  'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.yandex.ru',  // e.g. smtp.mandrillapp.com or smtp.gmail.com
                'username' => 'support@ps.ru',
                'password' => 'H3c7',
//                'port' => '587', // Port 25 is a very common port too
//                'encryption' => 'tls', // It is often used, check your provider or mail server specs
             //    'port' => '465', // Port 25 is a very common port too
             //   'encryption' => 'ssl', // It is often used, check your provider or mail server specs
                'port' => '587', // Port 25 is a very common port too
                'encryption' => 'tls', 
                
                
                ],
            'useFileTransport' => false,
        ],
        'config'=>[
            'class' => 'app\components\DConfig',
            
        ],
        'unisender'=>[
            'class' => 'app\components\Unisender',
            
        ],
        
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
         'authManager' => [
             //'class' => 'yii\rbac\PhpManager',
           'class' => 'yii\rbac\DbManager',
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
         //'class' => 'mdm\admin\classes\AccessControl',

        'allowActions' => [
            'users/registration',
            'users/email-activation',
            'users/reg-thanks',
            'users/password-recovery',
            'users/login',
            'users/reg-end',
            'users/checkpass',
            'users/code',
            'users/add-like',
            'users/learn-done',
            'users/upload-vidos',
            'site/*', // add or remove allowed actions to this list
            'admin/*', // add or remove allowed actions to this list
        ]
    ],
    'params' => $params,
    
    
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
