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

$this->title = 'Parametrización de Categorias -- CXM & Speech --';

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
   &nbsp;
  <a style=" background-color: #337ab7" class="btn btn-success" rel="stylesheet" type="text/css" href="..\..\downloadfiles\Plantilla_Glosario.xlsx" title="Descagar Plantilla del Glosario" target="_blank">
  <em class="fas fa-download" style="font-size: 15px; color: #E6E6E6;"></em>  Descargar Plantilla</a>     
  &nbsp; 
<br>
    <div class="page-header" >
        <h3 class="text-center"><?= Html::encode($this->title) ?></h3>
    </div> 
<br>
<div>
    <br>
    
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
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
                        'template' => '{view} {btnida} {btnhalla} {btndefin} {btnaleatorio} {btnpec} {btnsubirglosario} {btnformularios} {btnsociedad}',
                        'buttons' => 
                        [ 
                            'view' => function ($url, $model) {
                                return Html::a('<em class="fas fa-search"  style="font-size: 18px;"></em>',['categoriasverificar', 'txtServicioCategorias' => $model->cod_pcrc], [
                                        'class' => '',
                                        'data-toggle' => 'tooltip',
	                                    'title' => 'Buscar',
                                        'data' => [
                                            'method' => 'post',
                                        ],
                                ]);                                
                            },
                            'btnida' => function($url, $model) {
                                return Html::a('<em class="far fa-list-alt" style="font-size: 18px;"></em>',['categoriasida', 'txtServicioCategorias' => $model->cod_pcrc], [
                                        'class' => '',
                                        'data-toggle' => 'tooltip',
	                                'title' => 'Dashboard IDA',
                                        'data' => [
                                            'method' => 'post',
                                        ],
                                ]);                                
                            },
                            'btnhalla' => function($url, $model) {
                                return Html::a('<em class="far fa-check-circle"" style="font-size: 18px;"></em>',['categoriashalla', 'txtServicioCategorias' => $model->cod_pcrc], [
                                        'class' => '',
                                        'data-toggle' => 'tooltip',
	                                    'title' => 'Hallazgos',
                                        'data' => [
                                            'method' => 'post',
                                        ],
                                ]);          
                            },
                            'btndefin' => function($url, $model) {
                                return Html::a('<em class="far fa-edit"" style="font-size: 18px;"></em>',['categoriasdefinicion', 'txtServicioCategorias' => $model->cod_pcrc], [
                                        'class' => '',
                                        'data-toggle' => 'tooltip',
	                                    'title' => 'Definicion',
                                        'data' => [
                                            'method' => 'post',
                                        ],
                                ]);          
                            },
                            'btnaleatorio' => function($url, $model) {
                                return Html::a('<em class="fas fa-random"" style="font-size: 18px;"></em>',['paramsaleatorio', 'txtServicioCategorias' => $model->cod_pcrc], [
                                        'class' => '',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Programar Aleatorio',
                                        'data' => [
                                            'method' => 'post',
                                        ],
                                ]);          
                            },
                            'btnpec' => function($url, $model) {
                                return Html::a('<em class="fas fa-check"" style="font-size: 18px;"></em>',['paramspecservicio', 'txtServicioCategorias' => $model->cod_pcrc], [
                                        'class' => '',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Programar PEC',
                                        'data' => [
                                            'method' => 'post',
                                        ],
                                ]);          
                            },
                            'btnsubirglosario'=> function($url, $model) {
                                return Html::a('<em class="fas fa-upload" style="font-size: 18px;"></em>',['subirglosario', 'txtServicioCategorias' => $model->cod_pcrc], [
                                        'class' => '',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Subir Glosario',
                                        'data' => [
                                            'method' => 'post',
                                        ],
                                ]);          
                            },
                            'btnformularios' => function($url, $model) {
                                return Html::a('<em class="fas fa-paper-plane"" style="font-size: 18px;"></em>',['paramsformularios', 'txtServicioCategorias' => $model->cod_pcrc], [
                                        'class' => '',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Programar Formularios Valoraciones',
                                        'data' => [
                                            'method' => 'post',
                                        ],
                                ]);          
                            },
                            'btnsociedad' => function($url, $model) {
                                return Html::a('<em class="fas fa-hand-point-up"" style="font-size: 18px;"></em>',['paramspcrcsociedad', 'txtServicioCategorias' => $model->cod_pcrc], [
                                        'class' => '',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Programa Sociedad A Codigos Pcrc',
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
