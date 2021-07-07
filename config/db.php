<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=172.20.100.50;dbname=ci_monitoreov2',
    //'dsn' => 'mysql:host=localhost;dbname=ci_monitoreov2',
    'username' => 'ci_monitoreo',
    'password' => 'monit000r300',
    'charset' => 'utf8',

    'on afterOpen' =>function ($event) {
  	$event->sender->createCommand("SET lc_time_names = 'es_CO'")->execute();
}
];
