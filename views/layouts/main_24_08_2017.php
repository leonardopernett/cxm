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
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta http-equiv="X-UA-Compatible" content="IE=9" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>        
        <?= Html::tag("link", "", ["rel" => "shortcut icon", "type" => "image/x-icon", "href" => Url::to("@web/favicon.ico")]); ?>
        <?php $this->head() ?>
    </head>
    <body>

        <?php $this->beginBody() ?>
        <header>
            <div class="container-fluid menu-top">
                <?php
                NavBar::begin([
                    'brandLabel' => Html::img(Url::to("@web/images/qa-logo.png"),
                            ["alt" => "home QA","style" => "width: 200px; margin-top: 10px"]),
                    'brandUrl' => Yii::$app->homeUrl,
                    'options' => [
                        'class' => 'navbar navbar-default navbar-qa',
                    ],
                ]);

                if (!\Yii::$app->user->isGuest) {
                    /*Se inicia una instancia de tmpejecucion para llamar el metodo
                    q contara la cantidad de valaoraciones escaladas */
                    $modeltmpeje = new \app\models\Tmpejecucionformularios();
                    echo Nav::widget([
                        'encodeLabels' => false,
                        'options' => ['class' => 'navbar-nav navbar-right menu-qa'],
                        'items' => [
                            /*
                             *TO-DO : DESCOMENTAR CUANDO REQUIERAN LA PARTE DE CUADRO DE MANDO O CONTROL
                             */
                             [
                                'label' => Yii::t('app', 'Cuadro de mando'),
                                //'url' => ['/site/dashboard'],
                                'visible' => Yii::$app->user->identity->isCuadroMando(),
                                'items'=>[
                                    [
                                        'label' => Yii::t('app', 'Resumen Graficado'),
                                        'url' => ['/site/dashboard'],
                                        'visible' => Yii::$app->user->identity->isCuadroMando(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Alerta resumen'),
                                        'url' => ['/site/dashboardalertas'],
                                        'visible' => Yii::$app->user->identity->isCuadroMando(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'segundo calificador'),
                                        'url' => ['/site/segundocalificador'],
                                        'visible' => Yii::$app->user->identity->isCuadroMando(),
                                    ],
                                ]
                            ],
                            [
                                'label' => Yii::t('app', 'Control'),
                                //'url' => ['/site/dashboard'],
                                'visible' => Yii::$app->user->identity->isControlProcesoCX(),
                                'items'=>[
                                    [
                                        'label' => Yii::t('app', 'Control de Proceso CX'),
                                        'url' => ['control/index'],
                                        'visible' => Yii::$app->user->identity->isControlProcesoCX(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Control persona'),
                                        'url' => ['control/indexpersona'],
                                        'visible' => Yii::$app->user->identity->isControlProcesoCX(),
                                    ],

                                ]
                            ],
                            [
                                'label' => ($modeltmpeje->searchTmpejecucionformcount()!=0)?Yii::t('app', 'Valorar interaccion').Html::tag('span', $modeltmpeje->searchTmpejecucionformcount(), ['class'=>'badge']):Yii::t('app', 'Valorar interaccion'),
                                'visible' => Yii::$app->user->identity->isHacerMonitoreo(),
                                'items' => [
                                    [
                                        'label' => Yii::t('app', 'Interaccion Manual'),
                                        'url' => ['/formularios/interaccionmanual'],
                                        'visible' => Yii::$app->user->identity->isHacerMonitoreo(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Enviar Feedback Express'),
                                        'url' => ['/feedback/create'],
                                        'visible' => Yii::$app->user->identity->isHacerMonitoreo(),
                                    ]
                                    ,
                                    [
                                        'label' => Yii::t('app', 'Encuestas Telefónicas'),
                                        'url' => ['/basesatisfaccion/encuestatelefonica'],
                                        'visible' => Yii::$app->user->identity->isHacerMonitoreo(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Gestion Satisfaccion'),
                                        'url' => ['/basesatisfaccion/index'],
                                        'visible' => Yii::$app->user->identity->isHacerMonitoreo(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Gestion Satisfaccion Proceso'),
                                        'url' => ['/basesatisfaccion/inboxaleatorio'],
                                        'visible' => Yii::$app->user->identity->isVerInboxAleatorio(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'valoracion escaladas'),
                                        'url' => ['/formularios/indexescalados'],
                                        'visible' => Yii::$app->user->identity->isHacerMonitoreo() ||
										Yii::$app->user->identity->isHacerMonitoreo(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Bandeja de salida escalamientos'),
                                        'url' => ['/formularios/indexescaladosenviados'],
                                        'visible' => Yii::$app->user->identity->isHacerMonitoreo() ||
                                        Yii::$app->user->identity->isHacerMonitoreo(),
                                    ],
                                ]
                            ],
                            [
                                'label' => Yii::t('app', 'Reportes'),
                                'visible' => Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isModificarMonitoreo(),
                                'items' => [
                                    [
                                        'label' => Yii::t('app', 'Gestión Feedback'),
                                        'url' => ['/reportes/feedbackexpress'],
                                        'visible' => Yii::$app->user->identity->isReportes(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Histórico Formularios'),
                                        'url' => ['/reportes/historicoformularios'],
                                        'visible' => Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isModificarMonitoreo(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Reporte Valorados'),
                                        'url' => ['/reportes/valorados'],
                                        'visible' => Yii::$app->user->identity->isReportes(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Extractar Formularios'),
                                        'url' => ['/reportes/extractarformulario'],
                                        'visible' => Yii::$app->user->identity->isReportes(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Tablero de Experiencias'),
                                        'url' => ['/reportes/tableroexperiencias'],
                                        'visible' => Yii::$app->user->identity->isReportes(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Prom de Calificaciones'),
                                        'url' => ['/reportes/promcalificaciones'],
                                        'visible' => Yii::$app->user->identity->isReportes(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Reporte por Variables'),
                                        'url' => ['/reportes/variables'],
                                        'visible' => Yii::$app->user->identity->isReportes(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Declinaciones'),
                                        'url' => ['/reportes/declinaciones'],
                                        'visible' => Yii::$app->user->identity->isReportes(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Reporte satisfaccion'),
                                        'url' => ['/reportes/satisfaccion'],
                                        'visible' => Yii::$app->user->identity->isReportes(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Reporte Control de Satisfacción'),
                                        'url' => ['/reportes/controlsatisfaccion'],
                                        'visible' => Yii::$app->user->identity->isReportes(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Reporte Histórico Satisfacción'),
                                        'url' => ['/reportes/historicosatisfaccion'],
                                        'visible' => Yii::$app->user->identity->isReportes(),
                                    ],
									[
                                        'label' => Yii::t('app', 'Reporte Segundo Calificador'),
                                        'url' => ['/reportes/reportesegundocalificador'],
                                        'visible' => Yii::$app->user->identity->isReportes(),
                                    ],
                                ]
                            ],
                            [
                                'label' => Yii::t('app', 'Admin. Form'),
                                'visible' => Yii::$app->user->identity->isAdminProcesos() ||
                                Yii::$app->user->identity->isEdEqipoValorado(),
                                'items' => [
                                    [
                                        'label' => Yii::t('app', 'Formularios'),
                                        'url' => ['/formularios/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Seccions'),
                                        'url' => ['/seccions/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Bloques'),
                                        'url' => ['/bloques/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Bloque Detalle'),
                                        'url' => ['/bloquedetalles/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Texts'),
                                        'url' => ['/textos/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Typing'),
                                        'url' => ['/tipificaciones/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Trees'),
                                        'url' => ['/arboles/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Teams'),
                                        'url' => ['/equipos/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isEdEqipoValorado(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Valued'),
                                        'url' => ['/evaluados/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isEdEqipoValorado(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Equipo valoradores'),
                                        'url' => ['/equiposevaluadores/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isEdEqipoValorado(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Módulo Parametrización'),
                                        'url' => ['/parametrizacion-encuesta/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ]
                                ]
                            ],
                            [
                                'label' => Yii::t('app', 'Admin. General'),
                                'visible' => Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isAdminSistema(),
                                'items' => [
                                    [
                                        'label' => Yii::t('app', 'Roles'),
                                        'url' => ['/roles/index'],
                                        'visible' => Yii::$app->user->identity->isAdminSistema(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Enfoques'),
                                        'url' => ['/tableroenfoque/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Problemas'),
                                        'url' => ['/tableroproblema/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Interacciones'),
                                        'url' => ['/transacions/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Dimensiones'),
                                        'url' => ['/dimensiones/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Declinaciones'),
                                        'url' => ['/declinaciones/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Calificaciones'),
                                        'url' => ['/calificacions/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Tipo Bloques'),
                                        'url' => ['/tipobloques/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Tipo Llamadas'),
                                        'url' => ['/tiposllamadas/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Tipo Secciones'),
                                        'url' => ['/tiposeccions/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Usuarios'),
                                        'url' => ['/usuarios/index'],
                                        'visible' => Yii::$app->user->identity->isAdminSistema(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Gruposusuarios'),
                                        'url' => ['/gruposusuarios/index'],
                                        'visible' => Yii::$app->user->identity->isAdminSistema(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Feedbacks'),
                                        'url' => ['/categoriafeedbacks/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Noticias'),
                                        'url' => ['/noticias/index'],
                                        'visible' => Yii::$app->user->identity->isAdminSistema(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Slides'),
                                        'url' => ['/slides/index'],
                                        'visible' => Yii::$app->user->identity->isAdminSistema(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Reglanegocios'),
                                        'url' => ['/reglanegocio/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Errores Satu'),
                                        'url' => ['/erroressatu/index'],
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Informe aleatoriedad'),
                                        'url' => ['/informeinboxaleatorio/index'],
                                        'visible' => Yii::$app->user->identity->isAdminProcesos(),
                                    ],
                                    [
                                        'label' => Yii::t('app', 'Logeventsadmins'),
                                        'url' => ['/logeventsadmin/index'],
                                        'visible' => Yii::$app->user->identity->isAdminSistema(),
                                    ],
                                ]
                            ],
                            ['label' => '<span class="glyphicon glyphicon-off"></span> (' . Yii::$app->user->identity->username . ')',
                                'url' => ['/site/logout'],
                                'linkOptions' => ['data-method' => 'post', 'class' => 'cerrarsession'],                                
                            ],
                        ],
                    ]);
                }
                NavBar::end();
                ?>
            </div>
        </header>
        <div class="wrap">
            <div class="container-fluid contenido">
                <?=
                Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ])
                ?>
                <?= $content ?>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="pull-left">&copy; QA Management <?= date('Y') ?></p>
                <p class="pull-right">Desarrollado por <a href="http://www.ingeneo.com.co/" target="_blank" rel="noopener noreferrer external">Ingeneo S.A.S</a></p>
            </div>
        </footer>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
