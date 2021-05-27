<?php

return [
  'class'        => 'yii\db\Connection',
  'dsn'          => 'mysql:host=172.102.100.140;port=3306;dbname=experience',
  'username'     => 'cdg',
  'password'     => 'Konecta2020*',
  'charset'      => 'utf8',

  'on afterOpen' => function ($event) {
      $event->sender->createCommand("SET lc_time_names = 'es_CO'")->execute();
  },
];