<?php

return [
   'class'        => 'yii\db\Connection',
   'dsn'          => 'mysql:host=172.20.1.223;port=3306;dbname=reporting_rgd',
   'username'     => 'CONS_EXPERIENCIA',
   'password'     => '8*1PyDm2',
   'charset'      => 'utf8',

   'on afterOpen' => function ($event) {
       $event->sender->createCommand("SET lc_time_names = 'es_CO'")->execute();
   },
];