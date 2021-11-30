<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\Dashboardcategorias;

$this->title = 'Dashboard -- VOC --';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Parametrización de Categorias -- QA & Speech --';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $FechaActual = date("Y-m-d");
    $MesAnterior = date("m") - 1;

?>
<script src='../../js_extensions/fontawesome/a076d05399.js'></script>
  &nbsp; 
  <?= Html::a('Regresar',  ['categoriasconfig'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
  ?>
  &nbsp;   
  <?= Html::button('Modificar Parametros', ['value' => url::to(['categoriasparametros', 'arbol_idV' => $txtarbolid]), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                        'data-toggle' => 'tooltip',
                        'title' => 'Modificar Parametros', 'style' => 'background-color: #337ab7']) ?> 

  <?php
    Modal::begin([
      'header' => '<h4>Modifcación de Parametros...</h4>',
      'id' => 'modal1',
      'size' => 'modal-lg',
    ]);

    echo "<div id='modalContent1'></div>";
                                  
    Modal::end(); 
  ?> 
<br>
    <div class="page-header" >
        <h3 class="text-center"><?= Html::encode($this->title) ?></h3>
    </div> 
<br>
<div>
    <br>
    
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
            'columns' => [
                    [
                        'attribute' => 'Centro de Costos',
                        'value' => 'cod_pcrc'
                    ],
                    [
                        'attribute' => 'Pcrc',
                        'value' => function($data){
                            return $data->getListaCC($data->cod_pcrc);
                        },
                    ],    
                    [
                        'attribute' => 'Total Categorias',
                        'value' => function($data){
                            return $data->getTotalesCC($data->cod_pcrc);
                        },
                    ], 
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'template' => '{view} {btnida} {btnhalla} {btndefin}',
                        'buttons' => 
                        [ 
                            'view' => function ($url, $model) {
                                return Html::a('<i class="fas fa-search"  style="font-size: 18px;"></i>',['categoriasverificar', 'txtServicioCategorias' => $model->cod_pcrc], [
                                        'class' => '',
                                        'data-toggle' => 'tooltip',
	                                    'title' => 'Buscar',
                                        'data' => [
                                            'method' => 'post',
                                        ],
                                ]);                                
                            },
                            'btnida' => function($url, $model) {
                                return Html::a('<i class="far fa-list-alt" style="font-size: 18px;"></i>',['categoriasida', 'txtServicioCategorias' => $model->cod_pcrc], [
                                        'class' => '',
                                        'data-toggle' => 'tooltip',
	                                'title' => 'Dashboard IDA',
                                        'data' => [
                                            'method' => 'post',
                                        ],
                                ]);                                
                            },
                            'btnhalla' => function($url, $model) {
                                return Html::a('<i class="far fa-check-circle"" style="font-size: 18px;"></i>',['categoriashalla', 'txtServicioCategorias' => $model->cod_pcrc], [
                                        'class' => '',
                                        'data-toggle' => 'tooltip',
	                                    'title' => 'Hallazgos',
                                        'data' => [
                                            'method' => 'post',
                                        ],
                                ]);          
                            },
                            'btndefin' => function($url, $model) {
                                return Html::a('<i class="far fa-edit"" style="font-size: 18px;"></i>',['categoriasdefinicion', 'txtServicioCategorias' => $model->cod_pcrc], [
                                        'class' => '',
                                        'data-toggle' => 'tooltip',
	                                    'title' => 'Definicion',
                                        'data' => [
                                            'method' => 'post',
                                        ],
                                ]);          
                            }
                        ]                    
                    ],
                ],
            ]); 
        ?>
    
</div>
