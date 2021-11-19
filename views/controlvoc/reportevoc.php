<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;

$this->title = 'Reporte Escucha Focalizada - VOC -';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-3">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $valor = null;
    $txtIds = $txtIdBloques1;
    $txtValorador = $txtIdBloques1[1];
    $txtArbol_id = $txtIdBloques1[2];
    $txtFechacreacion = $txtIdBloques1[3];
    $txtTecnico = $txtIdBloques1[4]; 
    

?>
<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Reporte-VOC.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
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

    <?= Html::encode($this->title) ?>

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
        
                <?=
                    $form->field($model, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->label('Programa/PCRC')->widget(Select2::classname(), [
                                    'id' => 'ButtonSelect',
                                    'name' => 'BtnSelectes',
                                    'attribute' => 'Valorador',
                                    'language' => 'es',
                                    'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'minimumInputLength' => 4,
                                        'ajax' => [
                                            'url' => Url::to(['basesatisfaccion/getarbolesbypcrc']),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                            'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                        ],
                                        'initSelection' => new JsExpression('function (element, callback) {
                                                    var id=$(element).val();
                                                    if (id !== "") {
                                                        $.ajax("' . Url::to(['basesatisfaccion/getarbolesbypcrc']) . '?id=" + id, {
                                                            dataType: "json",
                                                            type: "post"
                                                        }).done(function(data) { callback(data.results[0]);});
                                                    }
                                                }')
                                    ]
                                        ]
                    );
                ?>              
                <?=
                    $form->field($model, 'fechacreacion', [
                        'labelOptions' => ['class' => 'col-md-12'],
                        'template' => '<div class="col-md-3">{label}</div>'
                        . '<div class="col-md-8"><div class="input-group">'
                        . '<span class="input-group-addon" id="basic-addon1">'
                        . '<i class="glyphicon glyphicon-calendar"></i>'
                        . '</span>{input}</div>{error}{hint}</div>',
                        'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                        'options' => ['class' => 'drp-container form-group']
                    ])->label('Rango de Fechas')->widget(DateRangePicker::classname(), [
                        'useWithAddon' => true,
                        'convertFormat' => true,
                        'presetDropdown' => true,
                        'readonly' => 'readonly',
                        'useWithAddon' => true,
                        'pluginOptions' => [
                            'autoApply' => true,
                            'useWithAddon'=>true,
                            'timePicker' => false,
                            'format' => 'Y-m-d',
                            'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                            'endDate' => date("Y-m-d"),
                            'clearBtn' => true,
                            'opens' => 'left'
                        ]
                    ]);
                ?>
         
                <?=
                    $form->field($model, 'valorador_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->label('Valorador')->widget(Select2::classname(), [
                                    'id' => 'ButtonSelect',
                                    'name' => 'BtnSelectes',
                                    'attribute' => 'Valorador',
                                    'language' => 'es',
                                    'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'minimumInputLength' => 4,
                                        'ajax' => [
                                            'url' => Url::to(['reportes/usuariolist']),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                            'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                        ],
                                        'initSelection' => new JsExpression('function (element, callback) {
                                                    var id=$(element).val();
                                                    if (id !== "") {
                                                        $.ajax("' . Url::to(['reportes/usuariolist']) . '?id=" + id, {
                                                            dataType: "json",
                                                            type: "post"
                                                        }).done(function(data) { callback(data.results[0]);});
                                                    }
                                                }')
                                    ]
                                        ]
                    );
                ?>  
           
                <?=
                    $form->field($model, 'tecnico_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->label('Valorado')->widget(Select2::classname(), [
                                    'id' => 'ButtonSelect',
                                    'name' => 'BtnSelectes',
                                    'attribute' => 'Valorador',
                                    'language' => 'es',
                                    'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'minimumInputLength' => 4,
                                        'ajax' => [
                                            'url' => Url::to(['evaluadolistmultiple']),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                            'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                        ],
                                        'initSelection' => new JsExpression('function (element, callback) {
                                                    var id=$(element).val();
                                                    if (id !== "") {
                                                        $.ajax("' . Url::to(['evaluadolistmultiple']) . '?id=" + id, {
                                                            dataType: "json",
                                                            type: "post"
                                                        }).done(function(data) { callback(data.results[0]);});
                                                    }
                                                }')
                                    ]
                                        ]
                    );
                ?>              
          
            <?= Html::submitButton(Yii::t('app', 'Buscar Reporte VOC'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'title' => 'Buscar Reporte VOC']) 
            ?> 
	    <?= 


              Html::button('Descargar Formularios', ['value' => url::to(['datosformulariosvoc', 'idbloque1' => $txtIds]), 'class' => 'btn btn-success', 
                            'id'=>'modalButton3',
                            'data-toggle' => 'tooltip',
                            'title' => 'Descarga de Datos', 
                            'style' => 'background-color: #707372']) 
            ?> 

            <?php
              Modal::begin([
                              'header' => '<h4>Procesando datos en el archivo de excel... </h4>',
                              'id' => 'modal3',
                              //'size' => 'modal-lg',
                            ]);

              echo "<div id='modalContent3'></div>";
                                                          
              Modal::end(); 
            ?> 
      
    <?php ActiveForm::end(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'Id',
                'value' => 'idbloque1',
		
            ],
            [
                'attribute' => 'Fecha',
                'value' => 'fechacreacion',
            ],
            [
                'attribute' => 'Valorador',
                'value' => 'usuarios.usua_nombre',
            ],           
            [   
                'attribute' => 'Identificacion Valorado',
                'value' => 'evaluados.identificacion',
            ],
            [   
                'attribute' => 'Valorado',
                'value' => 'evaluados.name',
            ],
            [
                'attribute' => 'Programa/PCRC',
                'value' => 'arboles.dsname_full',
            ],
            [
                'attribute' => 'Dimension',
                'value' => 'dimensions',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['style' => 'color:#337ab7'],
                'template' => '{view}',
                'buttons' => 
                [
                    'view' => function ($url, $model) {                        
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',  ['verlistasvoc', 'id' => $model->idbloque1], [
                            'class' => '',
                            'title' => 'Ver',
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