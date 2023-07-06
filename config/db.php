<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host='.MY_SQL_HOST.';dbname='.MY_SQL_DB_NAME.'',
    'username' => USER_DB,
    'password' => PASSWORD_DB,
    'charset' => 'utf8',

    'on afterOpen' =>function ($event) {
  	$event->sender->createCommand("SET lc_time_names = 'es_CO'")->execute();
}
];



