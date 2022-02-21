<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host='.MY_SQL_HOST_SLAVE.';port='.MY_SQL_PORT_SLAVE.';dbname='.MY_SQL_DB_NAME_SLAVE,
    'username' => USER_DB_SLAVE,
    'password' => PASSWORD_DB_SLAVE,
    'charset' => 'utf8',

    'on afterOpen' =>function ($event) {
    	$event->sender->createCommand("SET lc_time_names = 'es_CO'")->execute();
    }
];