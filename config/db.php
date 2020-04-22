<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host='.$DBCONFIG['DB_HOST'].';dbname='.$DBCONFIG['DB_NAME'],
    'username' => $DBCONFIG['DB_USER'],
    'password' => $DBCONFIG['DB_PASSWORD'],
    'charset' => 'utf8',
];
