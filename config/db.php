<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=172.20.1.36;dbname=ci_monitoreov2',
    'username' => 'root',
    'password' => 'qx4fdh9z',
    'charset' => 'utf8',

    'on afterOpen' =>function ($event) {
  	$event->sender->createCommand("SET lc_time_names = 'es_CO'")->execute();
}
];

