<?php

return [
   'class'        => 'yii\db\Connection',
   'dsn'          => 'mysql:host='.MY_SQL_HOST_JARVIS.';port='.MY_SQL_PORT_JARVIS.';dbname='.MY_SQL_DB_NAME_JARVIS,
   'username'     => USER_DB_JARVIS,
   'password'     => PASSWORD_DB_JARVIS,
   'charset'      => 'utf8',


   'on afterOpen' => function ($event) {
       $event->sender->createCommand("SET lc_time_names = 'es_CO'")->execute();
   },
];