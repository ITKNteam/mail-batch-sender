<?php

$ENV =  APP_ENV;

if( $ENV == ENV_PRODUCTION)
{
    define('BASE_URL', '@web/data/');
    define('DATA_DIR', dirname(__FILE__).'/../../../data');
    define('UPLOAD_DIR', dirname(__FILE__).'/../../../data/uploads/');
    define('DATA_DIR_ALL', dirname(__FILE__).'/../../../dataall');
    $DBCONFIG = parse_ini_file(dirname(__FILE__).'/../../../conf/database');
}
else if( $ENV == 'dev.shavrak')
{
    define('BASE_URL', '@web/data/');
    define('DATA_DIR', dirname(__FILE__).'/../../../data');
    define('UPLOAD_DIR', dirname(__FILE__).'/../../../data/uploads/');
    define('DATA_DIR_ALL', dirname(__FILE__).'/../../../dataall');
    $DBCONFIG = parse_ini_file(dirname(__FILE__).'/../../../conf/database');
}

else if( $ENV == 'westbtl.dev')
{
    define('BASE_URL', '@web/assets/');
    
    define('UPLOAD_DIR', dirname(__FILE__).'/../htdocs/data/uploads/');
    define('DATA_DIR', dirname(__FILE__).'/../htdocs/assets/');
    //define('DATA_DIR', dirname(__FILE__).'/../../data');
    define('DATA_DIR_ALL', dirname(__FILE__).'/../../dataall');
    $DBCONFIG = parse_ini_file(dirname(__FILE__).'/../../conf/database');
    
}


$runtime = DATA_DIR.'/runtime/';


return [
    'adminEmail' => 'kxxb@yandex.com',
   
    
];
