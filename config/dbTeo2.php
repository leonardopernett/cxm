<?php

return [
   'class'        => 'yii\db\Connection',
   'dsn'          => 'mysql:host='.MY_SQL_HOST_TEO_2.';port='.MY_SQL_PORT_TEO_2.';dbname='.MY_SQL_DB_NAME_TEO_2,
   'username'     => USER_DB_TEO_2,
   'password'     => PASSWORD_DB_TEO_2,
   'charset'      => 'utf8',

   'on afterOpen' => function ($event) {
       $event->sender->createCommand("SET lc_time_names = 'es_CO'")->execute();
   },
];