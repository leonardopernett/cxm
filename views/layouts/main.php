<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */

$varFechas = date('m');

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <!--Fontawesome CDN-->
        <link rel="stylesheet" href="<?php echo URL_CXM;?>/qa_managementv2/web/css/font-awesome/css/font-awesome.css"  >
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta http-equiv="X-UA-Compatible" content="IE=9" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>        
        <?= Html::tag("link", "", ["rel" => "shortcut icon", "type" => "image/x-icon", "href" => Url::to("@web/30x30.png")]); ?>
        <?php $this->head() ?>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
        <style type="text/css">
        
            .cxlogueo {
                font-family: "Nunito",sans-serif;
                font-weight: bold;
                font-size: 150%;
                margin: 0 5px;
                margin-top: 8px;
                margin-right: -90px;
            }
            .dropdown {
                font-family: "Nunito",sans-serif;                
                font-weight: bold;
                font-size: 130%;
                margin-top: 8px;
            }
            .dropdown-menu {
                font-family: "Nunito",sans-serif;
                font-weight: normal;
                color: #777777;
                background-color: #fff;
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
            }     

            .navbar-inverse .navbar-nav > .open > a, .navbar-inverse .navbar-nav > .open > a:hover, .navbar-inverse .navbar-nav > .open > a:focus {
                text-decoration: none !important;
                color: #002855;
                background-color: #eaeaea;
            }      
            .navbar-inverse .navbar-nav > .active > a, .navbar-inverse .navbar-nav > .active > a:hover, .navbar-inverse .navbar-nav > .active > a:focus {
                text-decoration: none !important;
                color: #002855;
                background-color: #eaeaea;
            }  
            a {
                color: #002855;
                text-decoration: none !important;
            }
            .dropdown-menu > .row > .col-md-3 > li > a:hover, .dropdown-menu > .row > .col-md-3 > li > a:focus{
                color: #00968F;
                text-decoration: none !important;
            font-weight: bold;
            font-feature-settings: "frac";
            }
            .dropdown-menu > .row > .col-md-6 > li > a:hover, .dropdown-menu > .row > .col-md-3 > li > a:focus{
                color: #00968F;
                text-decoration: none !important;
                font-weight: bold;
                font-feature-settings: "frac";
            }
            .dropdown-menu > .row > .col-md-12 > li > a:hover, .dropdown-menu > .row > .col-md-3 > li > a:focus{
                color: #00968F;
                text-decoration: none !important;
                font-weight: bold;
                font-feature-settings: "frac";
            }
            .dropdown-menu > .row > .col-md-4 > li > a:hover, .dropdown-menu > .row > .col-md-3 > li > a:focus{
                color: #00968F;
                text-decoration: none !important;
                font-weight: bold;
                font-feature-settings: "frac";
            }            .menutitulos {
                font-family: "Nunito",sans-serif;
                font-size: 130%;
                color: #999;
                font-weight: bold;
            }  
            .dropdown-headercx {
                display: block;
                font-weight: bold;
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
                padding-top: 20px;
            }

            .luces {
                background-image: url(https://lh6.googleusercontent.com/-FObwsgxVKrY/TuuwaGBmzOI/AAAAAAAAB-4/C_n2bIXZdnI/s204/luces%25252036.gif);
                background-repeat:repeat-x;
                background-size: 90px;
                height:30px;
                width:100%;
                top:0;
                left:0;
            }

        </style>
        
    </head>
    <body id="body">
    
        <?php $this->beginBody() ?>
        <nav id='cssmenu'>
        
            <?php

                if ($varFechas == '12') {
                    NavBar::begin([
                        'brandLabel' => Html::img(Url::to("@web/images/banner-superiorchristmas.png"),
                                ["alt" => "home QA","style" => "width: 143px; margin-top: 10px"]),
                        'brandUrl' => Yii::$app->homeUrl,
                        'options' => [
                            'class' => 'navbar navbar-inverse navbar-static-top',
                            'style' => 'background-color: #fff; box-shadow: 2px 2px 3px #b8b8b8;',
                        ],
                    ]);
                }else{
                    NavBar::begin([
                        'brandLabel' => Html::img(Url::to("@web/images/banner-superior.png"),
                                ["alt" => "home QA","style" => "width: 170px; margin-top: 17px; margin-left: -30px"]),
                        'brandUrl' => Yii::$app->homeUrl,
                        'options' => [
                            'class' => 'navbar navbar-inverse navbar-static-top',
                            'style' => 'background-color: #fff; box-shadow: 2px 2px 3px #b8b8b8;',
                        ],
                    ]);
                }

                if (!\Yii::$app->user->isGuest) {
                    
                    echo nav::widget([
                        'activateItems' => true,
                        'activateParents' => true,
                        'encodeLabels' => false,
                        'options' => ['class' => 'navbar-nav navbar-right'],
                        'items' => [                                                 
                            [

                                'class' => 'ico1',
                                'label' => '<img src="'.Yii::$app->getUrlManager()->getBaseUrl().'/images/Vocn.png" width="40" height="25">'.Yii::t('app', '&nbsp;VOC&nbsp;&nbsp;&nbsp;&nbsp;'),
                                'visible' => Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerDesempeno() || Yii::$app->user->identity->isVerdirectivo(),
                                'items' => [
                                    '<div class="row">',

                                        '<div class="col-md-12">',                                            
                                            '<li class="dropdown-headercx2">&nbsp;Administrador</li>',    
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Parametrizaci&oacute;n Encuestas&nbsp;&nbsp;'),
                                                    'url' => ['/controlencuestas/index'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando(),
                                                ],                                                
                                                '<br>',
                                            '<li class="dropdown-headercx2">&nbsp;Procesos&nbsp;</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Directorio CAD&nbsp;&nbsp;'),
                                                    'url' => ['/directoriocad/index'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isAdminSistema() || Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo(),
                                                ],    
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Gestor del Cliente&nbsp;&nbsp;'),
                                                    'url' => ['/hojavida/index'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isAdminSistema() || Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Gestor de PQRSF&nbsp;&nbsp;'),
                                                    'url' => ['/qr/index'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerdirectivo(),
                                                ], 
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Plan de Satisfacci&oacute;n Cliente&nbsp;&nbsp;'),
                                                    'url' => ['/plansatisfaccion/index'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isAdminSistema() || Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo(),
                                                ],  
                                                '<br>',
                                            '<li class="dropdown-headercx2">&nbsp;Resultados&nbsp;</li>',    
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Reporte Voc BI&nbsp;&nbsp;'),
                                                    'url' => ['/reportepbi/reporte?varid=1'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isAdminSistema() || Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo(),
                                                ],                                               
                                            
                                        '</div>',  

                                    '</div>',
                                ],
                                
                            ], 
                            [
                                
                                'label' => '<img src="'.Yii::$app->getUrlManager()->getBaseUrl().'/images/Voen.png" width="40" height="25">'.Yii::t('app','&nbsp;VOE&nbsp;&nbsp;&nbsp;&nbsp;'),
                                'visible' => Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerDesempeno() || Yii::$app->user->identity->isVerevaluacion() || Yii::$app->user->identity->isVerevaluacion()|| Yii::$app->user->identity->isVerdirectivo(),
                                'items' => [
                                    '<div class="row">',
                                        
                                        '<div class="col-md-12">',
                                            '<li class="dropdown-headercx2">&nbsp;Escucha</li>',
                                                '<li class="dropdown-headercx ico1">&nbsp;Evaluaci&oacute;n de Desarrollo&nbsp;&nbsp;</li>',                                                
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Realizar Evaluaciones&nbsp;&nbsp;'),
                                                    'url' => ['/gestorevaluaciondesarrollo/index'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerevaluacion()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isReportes(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Realizar Feedbacks&nbsp;&nbsp;'),
                                                    'url' => ['/gestorevaluaciondesarrollo/resultados','model'=>"",'documento'=>0],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerevaluacion()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isReportes(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Mis Resultados&nbsp;&nbsp;'),
                                                    'url' => ['/gestorevaluaciondesarrollo/resultadoindividual','model'=>""],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerevaluacion()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isReportes(),
                                                ],
                                                '<br>',
                                                '<li class="dropdown-headercx ico1">&nbsp;Universo&nbsp;&nbsp;</li>',
                                                [

                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Bit&aacute;cora&nbsp;&nbsp;'),
                                                    'url' => ['/bitacorauniverso/index'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerDesempeno()|| Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                            '<br>',
                                            '<li class="dropdown-headercx ico1">&nbsp;GPTW&nbsp;&nbsp;</li>',
                                                [

                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Plan de Acci&oacute;n&nbsp;&nbsp;'),
                                                    'url' => ['/planacciongptw/index'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isAdminSistema()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerDesempeno(),
                                                ],
                                            '<br>',
                                            '<li class="dropdown-headercx2">&nbsp;Analizar y Decidir</li>',
                                                // [
                                                //     'label' => Yii::t('app', '&nbsp;&nbsp;Resultados Evaluaci&oacute;n Desarrollo&nbsp;&nbsp;'),
                                                //     'url' => ['/evaluaciondesarrollo/resultadodashboard'],
                                                //     'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isReportes(),
                                                // ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Extractar Resultados&nbsp;&nbsp;'),
                                                    'url' => ['/evaluaciondesarrollo/exportarrtadashboard'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando()|| Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                            '<br>',
                                            '<li class="dropdown-headercx2">&nbsp;Administrador</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Realizar Parametrización&nbsp;&nbsp;'),
                                                    'url' => ['/gestorevaluaciondesarrollo/parametrizador'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isReportes(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Novedades Evaluaci&oacute;n Desarrollo&nbsp;&nbsp;'),
                                                    'url' => ['/gestorevaluaciondesarrollo/novedades'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isReportes(),
                                                ],
                                            '<br>',                                           
                                            '<li class="dropdown-headercx2">&nbsp;Resultados&nbsp;</li>',    
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Reporte Voe BI&nbsp;&nbsp;'),
                                                    'url' => ['/reportepbi/reporte?varid=2'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isAdminSistema() || Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo()  || Yii::$app->user->identity->isVerDesempeno(),
                                                ],
                                        '</div>',

                                    '</div>',
                                ],
                                
                            ], 
                            [                              
                                'label' => '<img src="'.Yii::$app->getUrlManager()->getBaseUrl().'/images/Vouxn.png" width="40" height="25">'.Yii::t('app', '&nbsp;VOUX&nbsp;&nbsp;&nbsp;&nbsp;'),                                
                                'visible' => Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isEdEqipoValorado() || Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isModificarMonitoreo() || Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isAdminSistema()  || Yii::$app->user->identity->isveralertas() || Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerexterno()  || Yii::$app->user->identity->isVerBA() || Yii::$app->user->identity->isControlProcesoCX()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast(),                                
                                'items' => [
                                    '<div class="row">',
                                        
                                        '<div class="col-md-6">',
                                            '<li class="dropdown-headercx2">&nbsp;Planear</li>',
                                                '<li class="dropdown-headercx ico1">&nbsp;Asignar Plan de Valoraci&oacute;n&nbsp;&nbsp;</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Dimensionamiento de T&eacute;cnicos&nbsp;&nbsp;'),
                                                    'url' => ['/controldimensionamiento/index'],
                                                    'visible' => Yii::$app->user->identity->isControlProcesoCX()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Asignaci&oacute;n de Planes de Valoraci&oacute;n&nbsp;&nbsp;'),
                                                    'url' => ['/controlprocesos/index'],
                                                    'visible' => Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast() || Yii::$app->user->identity->isHacerMonitoreo(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Seguimiento Plan de Valoraci&oacute;n&nbsp;&nbsp;'),
                                                    'url' => ['/seguimientoplan/index'],
                                                    'visible' => Yii::$app->user->identity->isControlProcesoCX()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast() || Yii::$app->user->identity->isHacerMonitoreo(),
                                                ],
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx2">&nbsp;Escuchar</li>',
                                                '<li class="dropdown-headercx ico1">&nbsp;Gesti&oacute;n del Valorador&nbsp;&nbsp;</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Carga Masiva de Encuestas&nbsp;&nbsp;'),
                                                    'url' => ['/basechat/index'],
                                                    'visible' => Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isVerexterno()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerBA(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Restablecimiento de Encuestas&nbsp;&nbsp;'),
                                                    'url' => ['/basesatisfaccion/inboxaleatorio'],
                                                    'visible' => Yii::$app->user->identity->isVerInboxAleatorio()|| Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Encuestas  Satisfacci&oacute;n&nbsp;&nbsp;'),
                                                    'url' => ['/basesatisfaccion/index'],
                                                    'visible' => Yii::$app->user->identity->isHacerMonitoreo()|| Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Encuestas Telef&oacute;nicas&nbsp;&nbsp;'),
                                                    'url' => ['/basesatisfaccion/encuestatelefonica'],
                                                    'visible' => Yii::$app->user->identity->isHacerMonitoreo()|| Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Valoraci&oacute;n Focalizada&nbsp;&nbsp;'),
                                                    'url' => ['/formulariovoc/index'],
                                                    'visible' => Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isVerexterno()|| Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Valoraci&oacute;n Manual&nbsp;&nbsp;'),
                                                    'url' => ['/formularios/interaccionmanual'],
                                                    'visible' => Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isVerexterno()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Valoraci&oacute;n Semi-Autom&aacute;tica&nbsp;&nbsp;'),
                                                    'url' => ['/dashboardspeechdos/index'],
                                                    'visible' => Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerBA()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerexterno(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Valoración de Héroes por Cliente&nbsp;'),
                                                    'url' => ['heroescliente/reportepostulacion'],
                                                    'visible' => Yii::$app->user->identity->isverAlertas() || Yii::$app->user->identity->isVerexterno()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast() || Yii::$app->user->identity->isControlProcesoCX(),
                                                ],  
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx2">&nbsp;Proteger y Mejorar</li>',
                                                '<li class="dropdown-headercx ico1">&nbsp;Alertas&nbsp;&nbsp;</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Crear Alertas&nbsp;&nbsp;'),
                                                    'url' => ['/alertascxm/registraalerta?id_procesos=0'],
                                                    'visible' => Yii::$app->user->identity->isverAlertas() || Yii::$app->user->identity->isVerexterno()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Gesti&oacute;n de Alertas&nbsp;&nbsp;'),
                                                    'url' => ['/alertascxm/notificacionalertas'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerusuatlmast(),
                                                ],
                                                '<br>',
                                                '<li class="dropdown-headercx ico1">&nbsp;Gesti&oacute;n de Coaching&nbsp;&nbsp;</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Crear Feedback Express&nbsp;&nbsp;'),
                                                    'url' => ['/feedback/create'],
                                                    'visible' => Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isVerexterno()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast(),
                                                ],                   
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Gesti&oacute;n de Feedback&nbsp;&nbsp;'),
                                                    'url' => ['/reportes/feedbackexpress'],
                                                    'visible' => Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isVerexterno()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast(),
                                                ],
                                                '<br>',
                                                '<li class="dropdown-headercx ico1">&nbsp;Calibraci&oacute;n&nbsp;&nbsp;</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Crear Calibraci&oacute;n&nbsp;&nbsp;'),
                                                    'url' => ['/controlalinearvoc/index'],
                                                    'visible' => Yii::$app->user->identity->isHacerMonitoreo()|| Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                        '</div>',
                                        '<div class="col-md-6">',                                                
                                                '<li class="dropdown-headercx ico1">&nbsp;Segundo Calificador&nbsp;&nbsp;</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Gesti&oacute;n Segundo Calificador&nbsp;&nbsp;'),
                                                    'url' => ['/site/segundocalificador'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerexterno()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast(),
                                                ],
                                            '<li class="divider"></li>',
                                            '<li class="dropdown-headercx2">&nbsp;Analizar y Decidir</li>',
                                                '<li class="dropdown-headercx ico1">&nbsp;Resultado de Procesos&nbsp;&nbsp;</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Dashboard Ejecutivo&nbsp;&nbsp;'),
                                                    'url' => ['/dashboardvoz/index'],
                                                    'visible' => Yii::$app->user->identity->isControlProcesoCX()|| Yii::$app->user->identity->isVerdirectivo(),
                                                ],                                                
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Informe Valoraci&oacute;n Semi-Autom&aacute;tica&nbsp;&nbsp;'),
                                                    'url' => ['/procesosqys/index'],
                                                    'visible' => Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerexterno(),
                                                ],

                                                '<br>',
                                                '<li class="dropdown-headercx ico1">&nbsp;Informes - Control de Procesos&nbsp;&nbsp;</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Declinaciones Captura Manual&nbsp;&nbsp;'),
                                                    'url' => ['/reportes/declinaciones'],
                                                    'visible' => Yii::$app->user->identity->isReportes()|| Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Declinaciones Encuestas&nbsp;&nbsp;'),
                                                    'url' => ['/basesatisfaccion/inboxdeclinadas'],
                                                    'visible' => Yii::$app->user->identity->isVerInboxAleatorio()|| Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Resultados de Calidad Percibida&nbsp;&nbsp;'),
                                                    'url' => ['/reportes/satisfaccion'],
                                                    'visible' => Yii::$app->user->identity->isReportes()|| Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                                '<br>',  
                                                '<li class="dropdown-headercx ico1">&nbsp;Bases de Informaci&oacute;n Operativos&nbsp;&nbsp;&nbsp;&nbsp;</li>',                                              
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Extractar Formularios&nbsp;&nbsp;'),
                                                    'url' => ['/reportes/extractarformulario'],
                                                    'visible' => Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isVerexterno()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Hist&oacute;rico de Alertas&nbsp;&nbsp;'),
                                                    'url' => ['/alertascxm/reportealerta'],
                                                    'visible' => Yii::$app->user->identity->isverAlertas() || Yii::$app->user->identity->isVerexterno()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast(),
                                                ],                                                
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Hist&oacute;rico de Calibraci&oacute;n&nbsp;&nbsp;'),
                                                    'url' => ['/controlalinearvoc/reportealinearvoc'],
                                                    'visible' => Yii::$app->user->identity->isReportes()|| Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Hist&oacute;rico de Formularios&nbsp;&nbsp;'),
                                                    'url' => ['/reportes/historicoformularios'],
                                                    'visible' => Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isModificarMonitoreo() || Yii::$app->user->identity->isVerexterno()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Hist&oacute;rico Segundo Calificador&nbsp;&nbsp;'),
                                                    'url' => ['/reportes/reportesegundocalificador'],
                                                    'visible' => Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isVerexterno()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Hist&oacute;rico de Valorados&nbsp;&nbsp;'),
                                                    'url' => ['/reportes/valorados'],
                                                    'visible' => Yii::$app->user->identity->isReportes()|| Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Hist&oacute;rico de Satisfacci&oacute;n&nbsp;&nbsp;'),
                                                    'url' => ['/reportes/historicosatisfaccion'],
                                                    'visible' => Yii::$app->user->identity->isReportes()|| Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Hist&oacute;rico Valoraciones Mixtas&nbsp;&nbsp;'),
                                                    'url' => ['/historicomixto/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminSistema() || Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                                '<br>',
                                            '<li class="dropdown-headercx2">&nbsp;Resultados&nbsp;</li>',    
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Reporte Voux BI&nbsp;&nbsp;'),
                                                    'url' => ['/reportepbi/reporte?varid=3'],
                                                    'visible' => Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isAdminSistema() || Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Reporte Looker Studio&nbsp;&nbsp;'),
                                                    'url' => ['/dashboardcomdata/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminSistema() ||  Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                                '<br>',
                                        '</div>',
                                            
                                    '</div>',
                                ],

                            ],
                            [
                                'label' => '<img src="'.Yii::$app->getUrlManager()->getBaseUrl().'/images/admin.png" width="27" height="27">'.Yii::t('app', '&nbsp;ADMIN&nbsp;&nbsp;&nbsp;&nbsp;'),                                
                                'visible' => Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isEdEqipoValorado() || Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isModificarMonitoreo() || Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isAdminSistema()  || Yii::$app->user->identity->isveralertas() || Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerexterno()  || Yii::$app->user->identity->isVerBA() || Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVermodificaformulario(),                                
                                'items' => [
                                    '<div class="row" >',
                                        '<div class="col-md-6">',
                                            '<li class="dropdown-headercx2">&nbsp;Administrador</li>',
                                                '<li class="dropdown-headercx ico1">&nbsp;Gesti&oacute;n de Usuarios&nbsp;&nbsp;</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Roles&nbsp;&nbsp;'),
                                                    'url' => ['/roles/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminSistema(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Usuarios&nbsp;&nbsp;'),
                                                    'url' => ['/usuarios/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminSistema(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Grupo de Usuarios&nbsp;&nbsp;'),
                                                    'url' => ['/gruposusuarios/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminSistema(),
                                                ],                                                
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Equipo de Evaluados&nbsp;&nbsp;'),
                                                    'url' => ['/equipos/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isEdEqipoValorado() || Yii::$app->user->identity->isVerusuatlmast(),
                                                ],                                                
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Valorados&nbsp;&nbsp;'),
                                                    'url' => ['/evaluados/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isEdEqipoValorado() || Yii::$app->user->identity->isVerusuatlmast(),
                                                ],                                                
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Cambio en Adminitradores&nbsp;&nbsp;'),
                                                    'url' => ['/logeventsadmin/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminSistema(),
                                                ],                                                
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Distribuci&oacute;n Version 2.0&nbsp;&nbsp;'),
                                                    'url' => ['/distribuciondos/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminSistema(),
                                                ],
                                                '<br>',
                                                '<li class="dropdown-headercx ico1">&nbsp;Gesti&oacute;n de Formularios&nbsp;&nbsp;</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Formularios'),
                                                    'url' => ['/formularios/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isVerusuatlmast() || Yii::$app->user->identity->isVermodificaformulario(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Secciones'),
                                                    'url' => ['/seccions/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isVerusuatlmast() || Yii::$app->user->identity->isVermodificaformulario(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Bloques'),
                                                    'url' => ['/bloques/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isVerusuatlmast() || Yii::$app->user->identity->isVermodificaformulario(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Bloque Detalle'),
                                                    'url' => ['/bloquedetalles/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isVerusuatlmast() || Yii::$app->user->identity->isVermodificaformulario(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Textos'),
                                                    'url' => ['/textos/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isVerusuatlmast() || Yii::$app->user->identity->isVermodificaformulario(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Tipificaciones'),
                                                    'url' => ['/tipificaciones/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isVerusuatlmast() || Yii::$app->user->identity->isVermodificaformulario(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Programa PCRC'),
                                                    'url' => ['/arboles/index'],
                                                    'visible' => Yii::$app->user->identity->isEdEqipoValorado() || Yii::$app->user->identity->isVerusuatlmast() || Yii::$app->user->identity->isVermodificaformulario(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Interacciones'),
                                                    'url' => ['/transacions/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isVerusuatlmast() || Yii::$app->user->identity->isVermodificaformulario(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Dimensiones'),
                                                    'url' => ['/dimensiones/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isVerusuatlmast() || Yii::$app->user->identity->isVermodificaformulario(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Declinaciones'),
                                                    'url' => ['/declinaciones/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isVermodificaformulario(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Calificaciones'),
                                                    'url' => ['/calificacions/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isVerusuatlmast() || Yii::$app->user->identity->isVermodificaformulario(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Feedbacks'),
                                                    'url' => ['/categoriafeedbacks/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isVerusuatlmast() || Yii::$app->user->identity->isVermodificaformulario(),
                                                ],
                                        '</div>',
                                        '<div class="col-md-6">',
                                                '<li class="dropdown-headercx">Gesti&oacute;n BD-P&uacute;blico Objetivo&nbsp;&nbsp;&nbsp;&nbsp;</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Administrar Cortes'),
                                                    'url' => ['/admincortes/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminSistema(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Desvinculaci&oacute;n Equipos'),
                                                    'url' => ['/peticionequipos/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminSistema(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Procesos Administrador'),
                                                    'url' => ['/procesosadministrador/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminSistema(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Parametrizar Escuchar +&nbsp;&nbsp;'),
                                                    'url' => ['/dashboardspeech/index'],
                                                    'visible' => Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerBA()|| Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Valoración Externa&nbsp;&nbsp;'),
                                                    'url' => ['/valoracionexterna/index'],
                                                    'visible' => Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerBA()|| Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Distribucción Externa'),
                                                    'url' => ['/distribuccionexterna/index'],
                                                    'visible' => Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerBA()|| Yii::$app->user->identity->isVerdirectivo(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Procesos Alertas 3'),
                                                    'url' => ['/alertascxm/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminSistema(),
                                                ],
                                                '<br>',                                            
                                                '<li class="dropdown-headercx">&nbsp;Encuestas de Satisfacci&oacute;n</li>',
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;M&oacute;dulo Parametrizaci&oacute;n'),
                                                    'url' => ['/parametrizacion-encuesta/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminSistema(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Errores Satu'),
                                                    'url' => ['/erroressatu/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminSistema(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Regla de Negocios'),
                                                    'url' => ['/reglanegocio/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Informe aleatoriedad'),
                                                    'url' => ['/informeinboxaleatorio/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminSistema(),
                                                ],
                                                '<br>',                                            
                                                '<li class="dropdown-headercx">&nbsp;Procesos de Integraciones</li>',                                                
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Procesos GNS'),
                                                    'url' => ['/procesosgenesys/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isAdminSistema() || Yii::$app->user->identity->isControlProcesoCX(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Procesos TrackSale'),
                                                    'url' => ['/procesostracksale/index'],
                                                    'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                                ],
                                                [
                                                    'label' => Yii::t('app', '&nbsp;&nbsp;Procesos Héroes'),
                                                    'url' => ['/heroescliente/index'],
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

            <?php
                if ($varFechas == '12') {
            ?>
                    <div class='luces'></div>
            <?php
                }
            ?>
            
        </nav>

            <script defer src="<?php echo URL_CXM;?>/qa_managementv2/web/font_awesome_local/js.js"></script>
            
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
                <div class="col-md-12" style="background-image: url('<?php echo URL_CXM;?>/qa_managementv2/web/images/link.png');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;"><br>
                    <div class="row">
                        <div class="col-md-2">                
                            <?php
                                if ($varFechas == '12') {
                            ?>
                                <img src="<?= Url::to("@web/images/CXMNavidad.png"); ?>" alt="Card image cap" style="height: 110px;">
                            <?php
                                }else{
                            ?>  
                                <img src="<?= Url::to("@web/images/link1.png"); ?>" alt="Card image cap">
                            <?php
                                }
                            ?>
                        </div>
                        <div class="col-md-4">                
                            
                        </div>
                        <div class="col-md-2">                
                            <a class="direccionar" href="https://nik.grupokonecta.co:7070/#/app" target="_blank" rel="noopener noreferrer"><img src="<?= Url::to("@web/images/nik_update.png"); ?>" style="height: 90px;width: 150px;margin: 16px;" alt="Card image cap"></a>
                        </div>
                        <div class="col-md-2">                
                            <a class="direccionar" href="https://galeria.allus.com.co/galeriaexperiencias/index.php/component/users/?view=login" target="_blank" rel="noopener noreferrer"><img src="<?= Url::to("@web/images/link2.png"); ?>" alt="Card image cap"></a>
                        </div>   
                        <div class="col-md-2">                
                            <a class="direccionar" href="https://konectados/" target="_blank" rel="noopener noreferrer"><img src="<?= Url::to("@web/images/link8.png"); ?>" style="width: 200px; height: 106px;" alt="Card image cap"></a>
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

