<?php

header("X-Frame-Options: DENY");
ini_set('date.timezone', 'Europe/Moscow');

date_default_timezone_set('Europe/Moscow');
// change the following paths if necessary


define('ENV_DEVELOPMENT', 'development');
define('ENV_PRODUCTION', 'production');
$ENVS = array(
		   	'westbtl.loc' => 'local.test',
		   	'westbtl.dev' => 'westbtl.dev',
		   	'westbtl.dev.shavrak.ru' => 'dev.shavrak',
		   	
		   	
		);

if(isset($ENVS[$_SERVER['SERVER_NAME']])){
		$ENV = $ENVS[$_SERVER['SERVER_NAME']];
  
        // comment out the following two lines when deployed to production
        defined('YII_DEBUG') or define('YII_DEBUG', true);
        defined('YII_ENV') or define('YII_ENV', 'dev');
                
}    
else{
		$ENV = ENV_PRODUCTION;
                
}
		


// глобальное определения места
define('APP_ENV', $ENV);

// *********************************************
if( $ENV != ENV_PRODUCTION)
{
        
	error_reporting(E_ALL);
	ini_set('display_errors', true);
	
}




require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();
