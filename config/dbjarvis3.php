<?php

return [
   'class'        => 'yii\db\Connection',
   'dsn'          => 'mysql:host=172.102.180.194;port=3306;dbname=jarvis',
   'username'     => 'consulta_jarvis',
   'password'     => 'C1nc53nt0',
   'charset'      => 'utf8',

   // 'class'        => 'yii\db\Connection',
   // 'dsn'          => 'mysql:host=172.102.180.196;port=3306;dbname=jarvis',
   // 'username'     => 'EXP_CONSULTA',
   // 'password'     => 'Konecta2020*',
   // 'charset'      => 'utf8',


   'on afterOpen' => function ($event) {
       $event->sender->createCommand("SET lc_time_names = 'es_CO'")->execute();
   },
];
