<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;

$this->title = 'Administracion de Cortes';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

?>
<div class="control-procesos-index">
     
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
<header class="masthead">
 <div class="container h-100">
   <div class="row h-100 align-items-center">
     <div class="col-12 text-center">
       
     </div>
   </div>
 </div>
</header>
<br>
<br>

    <?php $form = ActiveForm::begin([
        'options' => ["id" => "buscarMasivos"],
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>
    
	    <div class="text-center" style="text-align:left;">
		 
            <?= Html::a('Agregar Cortes',  ['create'], ['class' => $model->isNewRecord ? 'btn btn-success': 'btn btn-primary',
                        'style' => 'background-color: #337ab7',
                        'data-toggle' => 'tooltip',
                        'title' => 'Agregar Cortes']) 
            ?>   
        </div>
        <br>	    		
	    
	<?php $form->end() ?> 

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
                'template' => '{view}{update}{delete}',
                //'template' => '{view}',
                'buttons' => 
                [
                    'view' => function ($url, $model) {                        
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',  ['view', 'idtc' => $model->idtc], [
                            'class' => '',
                            'data' => [
                                'method' => 'post',
                            ],
                        ]);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',['update', 'idtc' => $model->idtc], [
                            'class' => '',
                            'data' => [
                                'method' => 'post',
                            ],
                        ]);
                    },
                     'delete' => function($url, $model){
                         return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'idtc' => $model->idtc], [
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