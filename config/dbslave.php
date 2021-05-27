<?php

return [
    'class' => 'yii\db\Connection',
    // 'dsn' => 'mysql:host=localhost;dbname=nuevo_qa',
    // 'username' => 'root',
    // 'password' => '',
    'dsn' => 'mysql:host=172.20.100.192;port=3390;dbname=ci_monitoreov2',
    'username' => 'root',
    'password' => 'allus2016*',
    'charset' => 'utf8',

    'on afterOpen' =>function ($event) {
    	$event->sender->createCommand("SET lc_time_names = 'es_CO'")->execute();
    }
];