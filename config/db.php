<?php

return [
    'class' => 'yii\db\Connection',
    // 'dsn' => 'mysql:host=localhost;dbname=nuevo_qa',
    // 'username' => 'root',
    // 'password' => '',

  /*   'dsn' => 'mysql:host=172.20.1.36;dbname=ci_monitoreov2',
    'username' => 'leonardo.pernett',
    'password' => 'Kcolombia123*',
    'charset' => 'utf8',
 */
    'dsn' => 'mysql:host='.MY_SQL_HOST.';dbname='.MY_SQL_DB_NAME,
    //'dsn' => 'mysql:host=localhost;dbname=ci_monitoreov2',
    'username' => USER_DB, //ci_monitoreo
    'password' => PASSWORD_DB, // monit000r300 

    // 'dsn' => 'mysql:host=172.20.100.50;dbname=nuevo_qa',
    // 'username' => 'andersson.moreno',
    // 'password' => 'Kcolombia123*',
    // 'charset' => 'utf8',

    'on afterOpen' =>function ($event) {
    	$event->sender->createCommand("SET lc_time_names = 'es_CO'")->execute();
    }
];