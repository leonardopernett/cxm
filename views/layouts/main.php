<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <!--Fontawesome CDN-->
        <link rel="stylesheet" href="https://qa.grupokonecta.local/qa_managementv2/web/css/font-awesome/css/font-awesome.css"  >
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta http-equiv="X-UA-Compatible" content="IE=9" />
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>        
        <?= Html::tag("link", "", ["rel" => "shortcut icon", "type" => "image/x-icon", "href" => Url::to("@web/30x30.png")]); ?>
        <?php $this->head() ?>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
        <style type="text/css">
            .cxlogueo {
                font-family: "Nunito";
                font-weight: bold;
                font-size: 150%;
                font-color: #FFFFFF;
                margin-top: 0;
                margin-bottom: 1rem;
                margin: 0 5px;
                margin-top: 8px;
                margin-right: -90px;
                font-color: #FFFFFF;
            }
            .dropdown {
                font-family: "Nunito";                
                font-weight: bold;
                font-size: 150%;
                margin-top: 8px;
            }
            .dropdown-menu {
                font-family: "Nunito";
                font-weight: normal;
                /* font-size: 90%; */
                color: #777777;
                /*background-color: #002855;*/
                /*background-color: #c9cacc;*/
                /*background-color: #d3d3d4;*/
                background-color: #fff;
                /*width: 1000px;    */
                min-width: max-content;
                
            }    
            .dropdown-menu > li > a {
                display: block;
                padding: 3px 20px;
                clear: both;
                font-weight: normal;
                line-height: 1.42857143;
                color: #999;
                white-space: nowrap;
            }   
            
            .navbar-nav > li {
                float: left;
                /*margin-right: 180px;*/
            }     

            .navbar-inverse .navbar-nav > .open > a, .navbar-inverse .navbar-nav > .open > a:hover, .navbar-inverse .navbar-nav > .open > a:focus {
                text-decoration: none !important;
                color: #002855;
                background-color: #eaeaea;
                /*background-color: #222222;*/
            }      
            .navbar-inverse .navbar-nav > .active > a, .navbar-inverse .navbar-nav > .active > a:hover, .navbar-inverse .navbar-nav > .active > a:focus {
                text-decoration: none !important;
                color: #002855;
                background-color: #eaeaea;
                /*background-color: #222222;*/
            }  
            a {
                color: #002855;
                text-decoration: none !important;
            }
            .dropdown-menu > .row > .col-md-3 > li > a:hover, .dropdown-menu > .row > .col-md-3 > li > a:focus{
                color: #00968F;
                text-decoration: none !important;
                /*background-color: #a0daeb;*/
        	font-weight: bold;
        	font-feature-settings: "frac";
            }
	    .dropdown-menu > .row > .col-md-6 > li > a:hover, .dropdown-menu > .row > .col-md-3 > li > a:focus{
                color: #00968F;
                text-decoration: none !important;
                /*background-color: #a0daeb;*/
                font-weight: bold;
                font-feature-settings: "frac";
            }
            .menutitulos {
                font-family: "Nunito";
                font-size: 130%;
                color: #999;
                font-weight: bold;
            }  
            .dropdown-headercx {
                display: block;
                font-weight: bold;
                /* padding: 3px 20px; */
                font-size: 15px;
                line-height: 1.42857143;
                color: #777;
                white-space: nowrap;
            }
            .navbar-inverse .navbar-nav > li > a {
                color: #002855;
            }
            .navbar-inverse .navbar-nav > li > a:hover, .navbar-inverse .navbar-nav > li > a:focus {
               color: #00968F;
               background-color: transparent;
           }
           .dropdown-headercx2 {
                display: block;
                font-weight: bold;
                /* padding: 3px 20px; */
                font-size: 15px;
                line-height: 1.42857143;
                color: #CE0F69 ;
                white-space: nowrap;
            }
            .navbar-inverse {    
                border-color: #c5bfbf;
            }
            
            .footer2 {
                height: auto ! important;
                
                /* background-color: #f5f5f5; */
                /* border-top: 1px solid #ddd; */
                padding-top: 20px;
            }

        </style>
    </head>
    <body>
        <?php $this->beginBody() ?>
        <nav id='cssmenu'>
            <?php
                NavBar::begin([
                    'brandLabel' => Html::img(Url::to("@web/images/banner-superior.png"),
                    // 'brandLabel' => 'CX-MANAGEMENT',
                            // ["alt" => "home QA","style" => "width: 250px; margin-top: 8px; margin-left: -10px"]),
                            ["alt" => "home QA","style" => "width: 250px; margin-top: 10px"]),
                    'brandUrl' => Yii::$app->homeUrl,
                    'options' => [
                        'class' => 'navbar navbar-inverse navbar-static-top',
                        'style' => 'background-color: #fff; box-shadow: 2px 2px 3px #b8b8b8;',
                    ],
                ]);

                if (!\Yii::$app->user->isGuest) {
                    echo nav::widget([
                        'activateItems' => true,
                        'activateParents' => true,
                        'encodeLabels' => false,
                        'options' => ['class' => 'navbar-nav navbar-right'],
                        'items' => [                      
                            [
                                'class' => 'ico1',
                                'label' => '<img src="https://qa.grupokonecta.local/qa_managementv2/web/images/Vocn.png" width="40" height="25">'.Yii::t('app', '&nbsp;VOC&nbsp;&nbsp;&nbsp;&nbsp;'),
                                'visible' => Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerDesempeno(),
                                'items' => [
                                    '<div class="row">',
                                        '<div class="col-md-6">',
                                            '<li class="dropdown-headercx2">&nbsp;Escucha</li>',
                                            '<li class="dropdown-headercx">&nbsp;Aplicación de la Encuesta</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Encuesta (Por definir)'),
                                                    // 'url' => ['/site/dashboard'],
                                                    // 'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                ],
                                                // [
                                                //     'label' => Yii::t('app', '&nbsp;&nbsp;Resumen Graficado'),
                                                //     'url' => ['/site/dashboard'],
                                                //     'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                // ],
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx2">&nbsp;Analiza y decide</li>',
                                            '<li class="dropdown-headercx">&nbsp;Tableros automáticos</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Reporte Power BI'),
                                                    'url' => ['/reportepbi/reporte'],
                                                    'visible' => Yii::$app->user->identity->isVerDesempeno() || Yii::$app->user->identity->isReportes(),
                                                ],
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx">&nbsp;Text Analytics</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Text (Por definir)'),
                                                    // 'url' => ['/site/dashboard'],
                                                    // 'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                ],
                                                // [
                                                //     'label' => Yii::t('app', '&nbsp;&nbsp;Resumen Graficado'),
                                                //     'url' => ['/site/dashboard'],
                                                //     'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                // ],
                                            
                                            
                                                // [
                                                //     'label' => Yii::t('app', '&nbsp;&nbsp;Resumen Graficado'),
                                                //     'url' => ['/site/dashboard'],
                                                //     'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                // ],                                            
                                        '</div>',  
                                        '<div class="col-md-6">',
                                        '<li class="dropdown-headercx2">&nbsp;Protege y mejora</li>',
                                            '<li class="dropdown-headercx">Gestión de Coaching</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Coaching (Por definir)'),
                                                    // 'url' => ['/site/dashboard'],
                                                    // 'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                ],
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx2">Administrador</li>',                                            
                                            '<li class="dropdown-headercx">Gestión de Usuarios</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Configurar formuarios(Por definir)'),
                                                    // 'url' => ['/site/dashboard'],
                                                    // 'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                ],    
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Gestión (Por definir)'),
                                                    // 'url' => ['/site/dashboard'],
                                                    // 'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                ],
                                                // [
                                                //     'label' => Yii::t('app', '&nbsp;&nbsp;Resumen Graficado'),
                                                //     'url' => ['/site/dashboard'],
                                                //     'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                // ],                                            
                                                // [
                                                //     'label' => Yii::t('app', '&nbsp;&nbsp;Resumen Graficado'),
                                                //     'url' => ['/site/dashboard'],
                                                //     'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                // ],
                                            
                                                // [
                                                //     'label' => Yii::t('app', '&nbsp;&nbsp;Resumen Graficado'),
                                                //     'url' => ['/site/dashboard'],
                                                //     'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                // ],                                            
					    '<li class="divider"></li>',
                                            '<li class="dropdown-headercx">Gestión de Encuestas</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Parametrización Encuestas'),
                                                    'url' => ['/controlencuestas/index'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                ],                                            
                                            
                                        '</div>',  
                                        '<div class="col-md-6">',
                                            
                                        '</div>',                                        
                                    '</div>',
                                ],
                                
                            ], 
                            [
                                
                                'label' => '<img src="https://qa.grupokonecta.local/qa_managementv2/web/images/Voen.png" width="40" height="25">'.Yii::t('app','&nbsp;VOE&nbsp;&nbsp;&nbsp;&nbsp;'),
                                'visible' => Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerDesempeno() || Yii::$app->user->identity->isVerevaluacion() || Yii::$app->user->identity->isVerevaluacion(),
                                'items' => [
                                    '<div class="row">',
                                        '<div class="col-md-6">',
                                            '<li class="dropdown-headercx2">&nbsp;Escucha</li>',
                                            '<li class="dropdown-headercx ico1">&nbsp;Aplicación de la Encuesta</li>',
                                                [                                                    
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Encuesta (Por definir)'),
                                                    // 'url' => ['/site/dashboard'],
                                                    // 'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                ],
                                                // [
                                                //     'label' => Yii::t('app', '&nbsp;&nbsp;Resumen Graficado'),
                                                //     'url' => ['/site/dashboard'],
                                                //     'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                // ],
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx">&nbsp;Evaluación de Desarrollo</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Gestionar evaluaciones'),
                                                    'url' => ['/evaluaciondesarrollo/index'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerevaluacion(),
                                                ],
						[
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Gestionar feedback'),
                                                    'url' => ['/evaluaciondesarrollo/evaluacionfeedback','model'=>"",'documento'=>0],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerevaluacion(),
                                                ],
						[
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Mis Resultados'),
                                                    'url' => ['/evaluaciondesarrollo/resultadoevaluacion','model'=>""],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerevaluacion(),
                                                ],
[
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Dashboard'),
                                                    'url' => ['/evaluaciondesarrollo/resultadodashboard'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                ],
                                                // [
                                                //     'label' => Yii::t('app', '&nbsp;&nbsp;Resumen Graficado'),
                                                //     'url' => ['/site/dashboard'],
                                                //     'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                // ],                                            
                                            '<li class="divider"></li>',
					    '<li class="dropdown-headercx">&nbsp;Bitácora Universo</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Gestión Bitácora Universo'),
                                                     'url' => ['/bitacorauniverso/index'],
                                                     'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerDesempeno(),
                                                ],                                            
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx2">&nbsp;Analiza y decide</li>',
                                            '<li class="dropdown-headercx">&nbsp;Tableros automáticos</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Reporte Power BI'),
                                                    'url' => ['/reportepbi/reporte'],
                                                    'visible' => Yii::$app->user->identity->isReportes(),
                                                ],
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx">&nbsp;Text Analytics</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Text (Por definir)'),
                                                    // 'url' => ['/site/dashboard'],
                                                    // 'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                ],   
                                              
                                            
                                                // [
                                                //     'label' => Yii::t('app', '&nbsp;&nbsp;Resumen Graficado'),
                                                //     'url' => ['/site/dashboard'],
                                                //     'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                // ],                                       
                                        '</div>',                                          
                                        '<div class="col-md-6">',
                                        '<li class="dropdown-headercx2">&nbsp;Protege y mejora</li>',
                                        '<li class="dropdown-headercx">Gestión de Coaching</li>',
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Coaching (Por definir)'),
                                                // 'url' => ['/site/dashboard'],
                                                // 'visible' => Yii::$app->user->identity->isCuadroMando(),
                                            ],
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx2">Administrador</li>',
                                            '<li class="dropdown-headercx">Gestión de Formularios</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Gestión (Por definir)'),
                                                    // 'url' => ['/site/dashboard'],
                                                    // 'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                ],
                                                // [
                                                //     'label' => Yii::t('app', '&nbsp;&nbsp;Resumen Graficado'),
                                                //     'url' => ['/site/dashboard'],
                                                //     'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                // ], 
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx">Gestión de Usuarios</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Gestión (Por definir)'),
                                                    // 'url' => ['/site/dashboard'],
                                                    // 'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                ],
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx">Gestión de Evaluaciones</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Gestionar novedades'),
                                                    'url' => ['/evaluaciondesarrollo/gestionnovedades'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Modulo parametrizador'),
                                                    'url' => ['/evaluaciondesarrollo/paramsevaluacion'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                ],
                                                // [
                                                //     'label' => Yii::t('app', '&nbsp;&nbsp;Resumen Graficado'),
                                                //     'url' => ['/site/dashboard'],
                                                //     'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                // ],                                            
                                                // [
                                                //     'label' => Yii::t('app', '&nbsp;&nbsp;Resumen Graficado'),
                                                //     'url' => ['/site/dashboard'],
                                                //     'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                // ],
                                        '</div>',  
                                        '<div class="col-md-6">',
                                            
                                        '</div>',                                        
                                    '</div>',
                                ],
                                
                            ], 
                            [                              
                                'label' => '<img src="https://qa.grupokonecta.local/qa_managementv2/web/images/Vouxn.png" width="40" height="25">'.Yii::t('app', '&nbsp;VOUX&nbsp;&nbsp;&nbsp;&nbsp;'),                                
                                'visible' => Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isEdEqipoValorado() || Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isModificarMonitoreo() || Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isAdminSistema()  || Yii::$app->user->identity->isveralertas() || Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerexterno()  || Yii::$app->user->identity->isVerBA() || Yii::$app->user->identity->isControlProcesoCX(),                                
                                'items' => [
                                    '<div class="row">',
                                        '<div class="col-md-3">',
                                            '<li class="dropdown-headercx2">&nbsp;Escucha</li>',
                                            '<li class="dropdown-headercx">&nbsp;Aplicación Valoración</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Valorar Interacción'),
                                                    'url' => ['/formularios/interaccionmanual'],
                                                    'visible' => Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isVerexterno(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Encuestas Telefónicas'),
                                                    'url' => ['/basesatisfaccion/encuestatelefonica'],
                                                    'visible' => Yii::$app->user->identity->isHacerMonitoreo(),
                                                ],
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx2">&nbsp;Analiza y decide</li>',
                                            '<li class="dropdown-headercx">&nbsp;Tableros Automáticos CX</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Resumen Graficado'),
                                                    'url' => ['/site/dashboard'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                ],
                                                [
                                                   'label' => Yii::t('app', '&nbsp;&nbsp;Dashboard Escuchar +'),
                                                    'url' => ['/dashboardspeech/index'],
                                                    'visible' => Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerBA(),
                                                ],

                                                [
                                                   'label' => Yii::t('app', '&nbsp;&nbsp;Dashboard Ejecutivo'),
                                                    'url' => ['/dashboardvoz/index'],
                                                    'visible' => Yii::$app->user->identity->isControlProcesoCX(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Control de Proceso CX'),
                                                    'url' => ['control/index'],
                                                    'visible' => Yii::$app->user->identity->isControlProcesoCX(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Reporte satisfacción'),
                                                    'url' => ['/reportes/satisfaccion'],
                                                    'visible' => Yii::$app->user->identity->isReportes(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Reporte Control de &nbsp;&nbsp;Satisfacción'),
                                                    'url' => ['/reportes/controlsatisfaccion'],
                                                    'visible' => Yii::$app->user->identity->isReportes(),
                                                ],
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx">&nbsp;Escucha Focalizada</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Inst. Escucha Focalizada'),
                                                    'url' => ['/formulariovoc/index'],
                                                    'visible' => Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isVerexterno(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Gestión Satisfacción'),
                                                    'url' => ['/basesatisfaccion/index'],
                                                    'visible' => Yii::$app->user->identity->isHacerMonitoreo(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Gestión Satisfacción Proceso'),
                                                    'url' => ['/basesatisfaccion/inboxaleatorio'],
                                                    'visible' => Yii::$app->user->identity->isVerInboxAleatorio(),
                                                ],
                                    		[
                                        	    'label' => Yii::t('app', '&nbsp;&nbsp;Gestion Satisfaccion &nbsp;&nbsp;Declinaciones'),
                                        	    'url' => ['/basesatisfaccion/inboxdeclinadas'],
                                        	    'visible' => Yii::$app->user->identity->isVerInboxAleatorio(),
		                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Gestión Chat'),
                                                    'url' => ['/basechat/index'],
                                                    'visible' => Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isVerexterno(),
                                                ],                                    
                                        '</div>',  
                                        '<div class="col-md-3">', 
                                            '<li class="dropdown-headercx2">&nbsp;Protege y mejora</li>',
                                            '<li class="dropdown-headercx">&nbsp;Gestión de Alertas</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Enviar Feedback Express'),
                                                    'url' => ['/feedback/create'],
                                                    'visible' => Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isVerexterno(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Crear Alerta'),
                                                    'url' => ['basesatisfaccion/alertas'],
                                                    'visible' => Yii::$app->user->identity->isverAlertas() || Yii::$app->user->identity->isVerexterno(),
                                                ],
                                                '<li class="divider"></li>',
                                            '<li class="dropdown-headercx">&nbsp;Gestión de Coaching</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Gestión Feedback'),
                                                    'url' => ['/reportes/feedbackexpress'],
                                                    'visible' => Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isVerexterno(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Alerta resumen'),
                                                    'url' => ['/site/dashboardalertas'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerexterno(),
                                                ],
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx">&nbsp;Alinear +</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Alinear +'),
                                                    'url' => ['/controlalinearvoc/index'],
                                                    'visible' => Yii::$app->user->identity->isHacerMonitoreo(),
                                                ],
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx2">Responsable de Experiencia</li>',
                                            '<li class="dropdown-headercx">Planeación del Proceso</li>',
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Equipo de Trabajo'),
                                                'url' => ['/controlprocesos/index'],
                                                'visible' => Yii::$app->user->identity->isControlProcesoCX(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Seguimiento Equipo de Trabajo'),
                                                'url' => ['/seguimientoplan/index'],
                                                'visible' => Yii::$app->user->identity->isControlProcesoCX(),
                                            ],
                                    	    [
                                        	'label' => Yii::t('app', '&nbsp;&nbsp;Control de Dimensionamiento'),
                                        	'url' => ['/controldimensionamiento/index'],
                                        	'visible' => Yii::$app->user->identity->isControlProcesoCX(),
                                    	    ],
                                            
                                        '</div>', 
                                        '<div class="col-md-3">',
                                                                                    
                                        '<li class="dropdown-headercx">Data de Informes</li>',
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Histórico Formularios'),
                                                'url' => ['/reportes/historicoformularios'],
                                                'visible' => Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isModificarMonitoreo() || Yii::$app->user->identity->isVerexterno(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Extractar Formularios'),
                                                'url' => ['/reportes/extractarformulario'],
                                                'visible' => Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isVerexterno(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Reporte Valorados'),
                                                'url' => ['/reportes/valorados'],
                                                'visible' => Yii::$app->user->identity->isReportes(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Declinaciones'),
                                                'url' => ['/reportes/declinaciones'],
                                                'visible' => Yii::$app->user->identity->isReportes(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Reporte Segundo Calificador'),
                                                'url' => ['/reportes/reportesegundocalificador'],
                                                'visible' => Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isVerexterno(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Alertas de Valoración'),
                                                'url' => ['basesatisfaccion/alertasvaloracion'],
                                                'visible' => Yii::$app->user->identity->isverAlertas() || Yii::$app->user->identity->isVerexterno(),
                                            ], 
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Plan de Valoración Técnico'),
                                                'url' => ['/planvaloracion/index'],
                                                'visible' => Yii::$app->user->identity->isReportes(),
                                            ], 
                                            //[
                                            //    'label' => Yii::t('app', '&nbsp;&nbsp;Reporte Escucha Focalizada'),
                                            //    'url' => ['/controlvoc/reportevoc'],
                                            //    'visible' => Yii::$app->user->identity->isReportes(),
                                            //], 
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Reporte - VOC -'),
                                                'url' => ['/formulariovoc/reportformvoc'],
                                                'visible' => Yii::$app->user->identity->isReportes(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Reporte Alinear + - VOC -'),
                                                'url' => ['/controlalinearvoc/reportealinearvoc'],
                                                'visible' => Yii::$app->user->identity->isReportes(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Reporte Histórico &nbsp;&nbsp;Satisfacción'),
                                                'url' => ['/reportes/historicosatisfaccion'],
                                                'visible' => Yii::$app->user->identity->isReportes(),
                                            ],
                        [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Reporte Power BI'),
                                                'url' => ['/reportepbi/reporte'],
                                                'visible' => Yii::$app->user->identity->isReportes(),
                                            ],
                                        '<li class="divider"></li>',

                                        '<li class="dropdown-headercx2">Administrador</li>',
                                        '<li class="dropdown-headercx">Gestión de Usuarios</li>',
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Roles'),
                                                'url' => ['/roles/index'],
                                                'visible' => Yii::$app->user->identity->isAdminSistema(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Usuarios'),
                                                'url' => ['/usuarios/index'],
                                                'visible' => Yii::$app->user->identity->isAdminSistema(),
                                            ],                                                
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Grupos usuarios'),
                                                'url' => ['/gruposusuarios/index'],
                                                'visible' => Yii::$app->user->identity->isAdminSistema(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Equipo de Evaluados'),
                                                'url' => ['/equipos/index'],
                                                'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isEdEqipoValorado(),
                                            ],
                                    	    [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Valorados'),
                                        	'url' => ['/evaluados/index'],
                                        	'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isEdEqipoValorado(),
                                    	    ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Cambios en administradores '),
                                                'url' => ['/logeventsadmin/index'],
                                                'visible' => Yii::$app->user->identity->isAdminSistema(),
                                            ],
					    [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Fuentes de Información '),
                                                'url' => ['/fuenteinformacion/index'],
                                                'visible' => Yii::$app->user->identity->isAdminSistema(),
                                            ],
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx">Segundo Calificador</li>',
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Segundo calificador'),
                                                'url' => ['/site/segundocalificador'],
                                                'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerexterno(),
                                            ],
                                                                                    
                                        '</div>',
                                        '<div class="col-md-3">',
                                        '<li class="dropdown-headercx">Gestión de Formularios</li>',
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Formularios'),
                                                'url' => ['/formularios/index'],
                                                'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Secciones'),
                                                'url' => ['/seccions/index'],
                                                'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Bloques'),
                                                'url' => ['/bloques/index'],
                                                'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Bloque Detalle'),
                                                'url' => ['/bloquedetalles/index'],
                                                'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Textos'),
                                                'url' => ['/textos/index'],
                                                'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Tipificaciones'),
                                                'url' => ['/tipificaciones/index'],
                                                'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Programa PCRC'),
                                                'url' => ['/arboles/index'],
                                                'visible' => Yii::$app->user->identity->isEdEqipoValorado(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Interacciones'),
                                                'url' => ['/transacions/index'],
                                                'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Dimensiones'),
                                                'url' => ['/dimensiones/index'],
                                                'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Declinaciones'),
                                                'url' => ['/declinaciones/index'],
                                                'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Calificaciones'),
                                                'url' => ['/calificacions/index'],
                                                'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Feedbacks'),
                                                'url' => ['/categoriafeedbacks/index'],
                                                'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                            ],
                                        '<li class="divider"></li>',
                                        '<li class="dropdown-headercx">Gestión BD - Público Objetivo&nbsp;&nbsp;</li>',
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Administrar Cortes'),
                                                'url' => ['/admincortes/index'],
                                                'visible' => Yii::$app->user->identity->isAdminSistema(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Desvinculación Equipos'),
                                                'url' => ['/peticionequipos/index'],
                                                'visible' => Yii::$app->user->identity->isAdminSistema(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Monitoreo de Categorización'),
                                                'url' => ['/categorizacion/index'],
                                                'visible' => Yii::$app->user->identity->isAdminSistema(),
                                            ],
                                            [
                                                'label' => Yii::t('app', '&nbsp;&nbsp;Buzones Kaliope'),
                                                'url' => ['/buzoneskaliope/index'],
                                                'visible' => Yii::$app->user->identity->isAdminSistema(),
                                            ],
                                        '<li class="divider"></li>',
                                        
                                        '<li class="dropdown-headercx">&nbsp;Encuestas de Satisfacción</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Módulo Parametrización'),
                                                    'url' => ['/parametrizacion-encuesta/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Errores Satu'),
                                                    'url' => ['/erroressatu/index'],
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Regla de Negocios'),
                                                    'url' => ['/reglanegocio/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                                ],
                                    		[
                                        	    'label' => Yii::t('app', '&nbsp;&nbsp;Informe aleatoriedad'),
                                        	    'url' => ['/informeinboxaleatorio/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    		],

                                        '</div>',                                            
                                    '</div>',
                                ],
                            ],        
                            [
                                'label' =>
                                '<p class="cxlogueo">&nbsp;&nbsp;&nbsp;&nbsp;'.ucwords(strtolower(Yii::$app->user->identity->fullName)).' <i class="fas fa-sign-out-alt"></i></p>',
                                'url' => ['/site/logout'],
                                'linkOptions' => ['data-method' => 'post', 'class' => 'cerrarsession'],                      
                            ],                            
                        ],
                        
                    ]);
                }

                NavBar::end();
            ?>
        </nav>

            <script defer src="https://qa.grupokonecta.local/qa_managementv2/web/font_awesome_local/js.js"></script>
            
        <div class="wrap">
            <div class="container-fluid">		
                <?=
                Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ])
                ?>
                <?= $content ?>
            </div>
        </div>
            
        <footer class="footer2">
            <div class="container1">
                <div class="col-md-12" style="background-image: url('https://qa.grupokonecta.local/qa_managementv2/web/images/link.png');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;"><br>
                    <div class="row">
                        <div class="col-md-2">                
                            <img src="<?= Url::to("@web/images/link1.png"); ?>" alt="Card image cap">
                        </div>
                        <div class="col-md-2">                
                            
                        </div>
                        <div class="col-md-2">                
                            <a class="direccionar" href="https://nik.grupokonecta.co:7070/#/app" target="_blank"><img src="<?= Url::to("@web/images/link80.png"); ?>" style="height: 106px; width: 215px;"></a>
                        </div>
                        <div class="col-md-2">                
                            <a class="direccionar" href="https://amigo.grupokonecta.local/AmigoV1/index.php/component/users/?view=login" target="_blank"><img src="<?= Url::to("@web/images/link3.png"); ?>"></a>
                        </div>
                        <div class="col-md-2">                
                            <a class="direccionar" href="https://galeria.allus.com.co/galeriaexperiencias/index.php/component/users/?view=login" target="_blank"><img src="<?= Url::to("@web/images/link2.png"); ?>" ></a>
                        </div>   
                        <div class="col-md-2">                
                            <a class="direccionar" href="https://konectados/" target="_blank"><img src="<?= Url::to("@web/images/link8.png"); ?>" style="width: 200px; height: 106px;"></a>
                        </div>                     
                    </div>                
                    <br>
                    <div class="row">
                                <p class="pull-left" style="color : #f5f5f5;"><label> &nbsp; &nbsp; &copy; CX-Management <?= date('Y') ?> - Desarrollado por Konecta</label></p>
                    </div>
                </div>
            </div>
        </footer>

        <?php $this->endBody() ?>
    </body>    

<?php $this->endPage() ?>




