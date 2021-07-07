<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\modal;

$this->title = 'Desvinculacion Tecnico del Equipo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

?>
<style type="text/css">
.masthead {
 height: 25vh;
 min-height: 100px;
 background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
 background-size: cover;
 background-position: center;
 background-repeat: no-repeat;
 /*background: #fff;*/
 border-radius: 5px;
 box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
}
</style>
<link rel="stylesheet" href="https://qa.grupokonecta.local/qa_managementv2/web/css/font-awesome/css/font-awesome.css"  >
<header class="masthead">
 <div class="container h-100">
   <div class="row h-100 align-items-center">
     <div class="col-12 text-center">
       <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
       <p class="lead">A great starter layout for a landing page</p> -->
     </div>
   </div>
 </div>
</header>
<br>
<br>
<div class="control-procesos-index">
        <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                    'attribute' => 'Id Peticion',
                    'value' => 'iddesvincular',
            ],
            [
                    'attribute' => 'Coordinador Solicitante',
                    'value' => function($data){
                    return $data->getObtenerName($data->iddesvincular);
                }                
            ],
                                    [
                    'attribute' => 'Valorador Solicitado',
                    'value' => 'usuarios.usua_nombre',                
            ],
            [
                    'attribute' => 'Coordinador Actual',
                    'value' => function($data){
                    return $data->getObtenerName2($data->iddesvincular);
                }               
            ],            
            [
                    'attribute' => 'Motivo',
                    'value' => 'motivo',                
            ],
            [
                    'attribute' => 'Fecha solicitud',
                    'value' => 'fechacreacion',                
            ],            
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['style' => 'color:#337ab7'],
                //'template' => '{view}{update}{delete}',
                'template' => '{update}',
                'buttons' => 
                [
                    'update' => function ($url, $model) {
                        return Html::a('<span class="far fa-check-circle" style="font-size: 20px; color: #28c916;" ></span>',['update', 'iddesvincular' => $model->iddesvincular], [
                            'class' => '',
                            'title' => 'Aceptar desvinculación',
                            'data' => [
                                'method' => 'post',
                            ],
                        ]);
                    },
                ]
              
        ],
        
        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['style' => 'color:#337ab7'],
            //'template' => '{view}{update}{delete}',
            'template' => '{update}',
            'buttons' => 
            [
                'update' => function ($url, $model) {
                    return Html::a('<span class="far fa-times-circle" style="font-size: 20px; color: #cf1b1b;" ></span>',['update2', 'iddesvincular' => $model->iddesvincular], [
                        'class' => '',
                        'title' => 'Rechazar desvinculación',
                        'data' => [
                            'method' => 'post',
                        ],
                    ]);
                },
            ]
        ],  
        ],
    ]) ?>
</div>

