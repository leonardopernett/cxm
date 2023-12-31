<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;



    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

?>
<style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Nunito');

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/Equipo-de-Trabajo.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
       
      </div>
    </div>
  </div>
</header>
<br><br>
<div class="control-procesos-index">
    <?= Html::a('Equipo de Trabajo',  ['index'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #337ab7',
                        'data-toggle' => 'tooltip',
                        'title' => 'Equipo de Trabajo']) 
    ?>
    <br>
    <br>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
                'attribute' => 'Tipos de cortes',
                'value' => 'tipocortetc',
        ],
        [
                'attribute' => 'Fecha Inicio',
                'value' => 'fechainiciotc',                
        ],
        [
                'attribute' => 'Fecha Fin',
                'value' => 'fechafintc',                
        ], 
        [
                'attribute' => 'Dias',
                'value' => 'diastc',
        ],    
        [
                'attribute' => 'Cant. Dias',
                'value' => 'cantdiastc',
        ],  
        [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['style' => 'color:#337ab7'],
                'template' => '{view}',
                'buttons' => 
                [
                    'view' => function ($url, $model) {                        
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',  ['viewcortes', 'idtc' => $model->idtc], [
                            'class' => '',
                            'data' => [
                                'method' => 'post',
                            ],
                        ]);
                    }
                ]              
        ],  
    ],
]) ?>

</div>