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

$this->title = 'Reporte Alinear + VOC';
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
    //var_dump($txtFechacreacion); 

?>

<style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Nunito');

    .card {
            height: 500px;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }

    .card1 {
            height: auto;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/Reporte-Alinear-+-Voc.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
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
<br><br>



<div class="SegundoBloque" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label><em class="fas fa-filter" style="font-size: 20px; color: #f2711b;"></em> Filtros:</label>
        <div class="row">
          <div class="col-md-12">
                <div class="form-grou" style="display: inline" id="CapaCero">
                    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
                        <div class="row">
                            <div class="col-md-6">
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
                                                            'url' => \yii\helpers\Url::to(['basesatisfaccion/getarbolesbypcrc']),
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
                            </div>        
                            <div class="col-md-6">
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
                                    ])->label('Rango de Fecha')->widget(DateRangePicker::classname(), [
                                'useWithAddon' => true,
                                'convertFormat' => true,
                                'presetDropdown' => true,
                                'readonly' => 'readonly',
                                'pluginOptions' => [
                                    'timePicker' => false,
                                    'format' => 'Y-m-d',
                                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                    'endDate' => date("Y-m-d"),
                                    'opens' => 'right',
                            ]]);
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
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
                                                            'url' => \yii\helpers\Url::to(['reportes/usuariolist']),
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
                            </div>
                            <div class="col-md-6">
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
                                                            'url' => \yii\helpers\Url::to(['evaluadolistmultiple']),
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>

    <div class="CuartoBloque" style="display: inline;">
        <div class="row">
            <div class="col-md-6">
                <div class="card1 mb">
                    <label><em class="fas fa-cogs" style="font-size: 20px; color: #1e8da7;"></em> Acciones:</label>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card1 mb">  
                                <?= Html::submitButton(Yii::t('app', 'Buscar Reporte Alinear + VOC'),
                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Buscar Reporte Alinear + VOC']) 
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card1 mb"> 
                            <?= 
                                Html::button('Descargar Formularios', ['value' => url::to(['datosformulariosalinearvoc', 'idbloque1' => $txtIds]), 'class' => 'btn btn-success', 
                                            'id'=>'modalButton3',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Descarga de Datos', 
                                            'style' => 'background-color: #707372']) 
                            ?> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <br>    
        <div class="row" align="center">            
            <?php
              Modal::begin([
                              'header' => '<h4>Procesando datos en el archivo de excel... </h4>',
                              'id' => 'modal3',
                              //'size' => 'modal-lg',
                            ]);

              echo "<div id='modalContent3'></div>";
                                                          
              Modal::end(); 
            ?> 
        </div>    
    <?php ActiveForm::end(); ?>
</div>
<hr>  
<div class="TercerBloque" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label><em class="far fa-list-alt" style="font-size: 20px; color: #5ff21b;"></em> Listado:</label>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
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
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',  ['verlistasalinearvoc', 'id' => $model->idbloque1], [
                                    'class' => '',
                                    'title' => 'Ver Alinear +',
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
      </div>
   </div>
</div>