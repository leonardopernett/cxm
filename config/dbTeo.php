<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host='.MY_SQL_HOST_TEO.';port='.MY_SQL_PORT_TEO.';dbname='.MY_SQL_DB_NAME_TEO,
    'username' => USER_DB_TEO,
    'password' => PASSWORD_DB_TEO,
    'charset' => 'utf8',

    'on afterOpen' =>function ($event) {
  	$event->sender->createCommand("SET lc_time_names = 'es_CO'")->execute();
}
];

