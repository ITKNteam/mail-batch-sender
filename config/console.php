<?php


//define('ENV_PRODUCTION', 'production');
define('ENV_DEVELOPMENT', 'development');
define('ENV_PRODUCTION', 'production');
// глобальное определения места

//define('APP_ENV', 'westbtl.dev');
define('APP_ENV', 'dev.shavrak');

//Yii::setAlias('@tests', dirname(__DIR__) . '/tests');
// $DBCONFIG = parse_ini_file(dirname(__FILE__).'/../../../conf/database');
//$DBCONFIG = parse_ini_file(dirname(__FILE__).'/../../conf/database');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');
  



return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'unisender'=>[
            'class' => 'app\components\Unisender',
            
        ],
        'db' => $db,
    ],
    'params' => $params,
];
