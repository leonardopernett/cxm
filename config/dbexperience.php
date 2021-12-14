<?php

return [
  'class'        => 'yii\db\Connection',
  'dsn'          => 'mysql:host='.MY_SQL_HOST_EXPERIENCE.';port='.MY_SQL_PORT_EXPERIENCE.';dbname='.MY_SQL_DB_NAME_EXPERIENCE,
  'username'     => USER_DB_EXPERIENCE,
  'password'     => PASSWORD_DB_EXPERIENCE,
  'charset'      => 'utf8',

  'on afterOpen' => function ($event) {
      $event->sender->createCommand("SET lc_time_names = 'es_CO'")->execute();
  },
];