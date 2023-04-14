<?php

return [
    'class'        => 'yii\db\Connection',
    'dsn'          => 'mysql:host=172.102.100.34;port=3306;dbname=mercadolibre',
    'username'     => 'cxmuser',
    'password'     => 'BlH3hHjxCCi4J2GKkTeg*',

    'charset'      => 'utf8',

    'on afterOpen' =>function ($event) {
    	$event->sender->createCommand("SET lc_time_names = 'es_CO'")->execute();
    }
];