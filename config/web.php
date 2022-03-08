<?php
date_default_timezone_set('America/Bogota');
$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
            'layout' => 'left-menu',
            'controllerMap' => [
                'assignment' => [
                    'class' => 'mdm\admin\controllers\AssignmentController',
                ]
            ],
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module'
// enter optional module parameters below - only if you need to
// use your own export download action or custom translation
// message source
// 'downloadAction' => 'gridview/export/download',
// 'i18n' => []
        ]
    ],
    'components' => [
        'headers' => [
            'class' => '\hyperia\security\Headers',
            'strictTransportSecurity' => [
                'max-age' => 15552000,
                'includeSubDomains' => true,
                'preload' => false
            ],
            'xssProtection' => true,
            'contentTypeOptions' => 'nosniff',
            'xFrameOptions' => 'DENY',
            'xPoweredBy' => 'Hyperia',
            'referrerPolicy' => 'no-referrer',
            'cacheControl' => ['no-cache', 'no-store', 'must-revalidate'],
            'pragma' => 'no-cache',
            'featurePolicyDirectives' => [
                'camera' => "'none'",
                'geolocation' => "'none'",
                'microphone' => "'none'",
                'payment' => "'none'",
                'usb' => "'none'",
            ],
            'cspDirectives' => [
                'script-src' => "'self' 'unsafe-inline'",
                'style-src' => "'self' 'unsafe-inline'",
                'img-src' => "'self' data:",
                'connect-src' => "'self'",
                'font-src' => "'self'",
                'object-src' => "'self'",
                'media-src' => "'self'",
                'form-action' => "'self'",
                'frame-src' => "'self'",
                'child-src' => "'self'"
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'rules' => [
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                ['class' => 'yii\rest\UrlRule', 'controller' => 'basesatisfaccionws',
                    'except' => ['delete', 'index', 'view'],],
            ],
        ],
        'mychart' => [
            'class' => 'app\components\MyChart',
        ],
        'webservices' => [
            'class' => 'app\components\WebServices',
        ],
	'webservicesamigo' => [
            'class' => 'app\components\WebServicesAmigo',
        ],

        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '5G7dj5viBHnskiFz-yUMMjgPADhhtbrD',
            'csrfCookie' => [

                'httpOnly' => true,
    
                'secure' => true,
    
            ],
        ],
        'session' => [

            'class' => 'yii\web\Session',
            
            'cookieParams' => [
    
                'httpOnly' => true,
    
                'secure' => true,
    
            ],
            
    
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            
            'autoRenewCookie' => true,

            'enableAutoLogin' => true,

            'identityCookie' => [

                'name' => '_identity',
    
                'httpOnly' => true,
    
                'secure' => true,

               
    
            ],
        ],
        'errorHandler' => [
            'maxSourceLines' => 20,
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => HOST_SMTP,
                /* 'username' => 'Nombre de usuario',
                'password' => 'xxxxxxxxx', */
                'port' => '25',
            ],

        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'categories' => ['db'],
                    'logFile' => '@app/runtime/DB/db.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 10,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'categories' => ['exception'],
                    'logFile' => '@app/runtime/exception/exc.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 10,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'categories' => ['redbox'],
                    'logFile' => '@app/runtime/redbox/redbox' . date('Ymd') . '.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 10,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'categories' => ['basesatisfaccion'],
                    'logFile' => '@app/runtime/basesatisfaccion/basesatisfaccion' . date('Ymd') . '.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 10,
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        /*BASE DE DATOS DE SLAVE QA*/
        'dbslave' => require(__DIR__ . '/dbslave.php'),
        /* BASE DE DATOS PARA TEO */
        'dbTeo'         => require __DIR__ . '/dbTeo.php',
        /*BASE DE DATOS DE TEO*/
        'dbTeo2' => require(__DIR__ . '/dbTeo2.php'),
        /*BASE DE DATOS DE JARVIS*/
        'dbjarvis' => require(__DIR__ . '/dbjarvis.php'),
        /*BASE DE DATOS DE JARVIS*/
        'dbjarvis3' => require(__DIR__ . '/dbjarvis3.php'),
        /*BASE DE DATOS DE JARVIS2*/
        'dbjarvis2' => require(__DIR__ . '/dbjarvis2.php'),
	/*BASE DE DATOS DE experience*/
        'dbexperience' => require(__DIR__ . '/dbexperience.php'),
        /* BASE DE DATOS PARA REDBOX */
        'dbredbox' => require(__DIR__ . '/dbredbox.php'),
        /* BASE DE DATOS PARA REDBOX BOGOTA */
        'dbredboxBogota' => require(__DIR__ . '/dbredboxBogota.php'),
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ]
    ],
    'params' => $params,
    'language' => 'es',
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
