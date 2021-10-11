<?php
/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Reporte Historico Formularios');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Repotes'), 'url' => ['historicoformularios']];
$this->params['breadcrumbs'][] = $this->title;
?>

<head>
<meta charset="UTF-8"/>
</head>
<?php
$template = '<div class="col-md-3">{label}</div><div class="col-md-9">'
        . ' {input}{error}{hint}</div>';
?>

<!--<div class="page-header">
    <h3><?php //echo $this->title     ?></h3>
</div>-->

<?php
foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
    echo '<div class="alert alert-' . $key . '" role="alert">' . $message . '</div>';
}
?>
<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../Reporte-Historico.png');
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
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br>

<div class="equipos-evaluados-form">    

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <div class="row col-md-12">       
        <div class="col-md-6">  
            <?=
            $form->field($model, 'created', [
                //'addon' => ['prepend' => ['content' => '<i class="glyphicon glyphicon-calendar"></i>']],                 
//                'inputTemplate' => '<div class="input-group col-md-12">'
//                . '<span class="input-group-addon">'
//                . '<i class="glyphicon glyphicon-calendar"></i>'
//                . '</span>{input}{error}{hint}</div>',
                'labelOptions' => ['class' => 'col-md-12'],
                'template' => '<div class="col-md-3">{label}</div>'
                . '<div class="col-md-9"><div class="input-group">'
                . '<span class="input-group-addon" id="basic-addon1">'
                . '<i class="glyphicon glyphicon-calendar"></i>'
                . '</span>{input}</div>{error}{hint}</div>',
                'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                'options' => ['class' => 'drp-container form-group']
            ])->widget(DateRangePicker::classname(), [
                'useWithAddon' => true,
                'convertFormat' => true,
                'presetDropdown' => true,
                'readonly' => 'readonly',
                'pluginOptions' => [
                    'timePicker' => false,
                    //'timePickerIncrement' => 15,
                    'format' => 'Y-m-d',
                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                    'endDate' => date("Y-m-d"),
                    'opens' => 'right',
            ]]);
            ?>
        </div>
        <div class="col-md-6">
            <?=
                    $form->field($model, 'usua_id_lider', ['labelOptions' => ['class' => 'col-md-12',], 'template' => $template])
                    ->widget(Select2::classname(), [
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...'),],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 4,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['lidereslist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['lidereslist']) . '?id=" + id, {
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

    <div class="row col-md-12">
        <div class="col-md-6">
            <?=
                    $form->field($model, 'usua_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'multiple' => true,
                            'allowClear' => true,
                            'minimumInputLength' => 4,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['usuariolist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['usuariolist']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                        ]
                            ]
            );
            ?>
        </div>  
        <div class="col-md-6">
            <?=
                    $form->field($model, 'evaluado_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'multiple' => true,
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
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                        ]
                            ]
            );
            ?>
        </div>
    </div>
    <div class="row col-md-12">
        <div class="col-md-6">
            <?php
            //$form->field($model, 'dimension_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($model->getDimensionsList(), ['prompt' => 'Seleccione ...'])
            ?>
            <?=
                    $form->field($model, 'dimension_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'multiple' => true,
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['dimensionlist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['dimensionlist']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                        ]
                            ]
            );
            ?>
        </div>
        <div class="col-md-6">
            <?=
                    $form->field($model, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'multiple' => true,
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['getarboles']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['getarboles']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                        ]
                            ]
            );
            ?>
            <?php /* =
              $form->field($model, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($model->getArbolesByRoles(), ['prompt' => 'Seleccione ...'])
             */ ?>
        </div>
    </div>
    <div class="row col-md-12">
        <div class="col-md-6">
            <?=
                    $form->field($model, 'equipo_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 4,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['equiposlist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['equiposlist']) . '?id=" + id, {
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


    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton(Yii::t('app', 'Buscar'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>
        </div>        
    </div>


    <?php ActiveForm::end(); ?>    
</div>

<?php if ($showGrid): ?>
<!--    <div class="page-header">
        <h3><?= Yii::t('app', 'Resultados') ?></h3>
    </div>-->
    <?php
    $text = app\models\Textos::find()->asArray()->all();
    $gridColumns = [
        ['class' => 'yii\grid\ActionColumn',
            'template' => '{preview}{update}{calculate}{notificacion}{delete}',
            'buttons' => [
                'preview' => function ($url, $model) {
                    $ejecucion = \app\models\Ejecucionformularios::findOne(["id" => $model["fid"]]);
                    $fecha = date('Y-m-d H:i:s');
                    $nuevafecha = strtotime('-2 month', strtotime($fecha));
                    $nuevafecha = date('Y-m-d H:i:s', $nuevafecha);
                    if (isset($ejecucion->basesatisfaccion_id)) {
                        $modelBase = app\models\BaseSatisfaccion::findOne($ejecucion->basesatisfaccion_id);
                    }
                    if ($model['created'] >= $nuevafecha) {
                        if ($ejecucion->basesatisfaccion_id == '' || empty($ejecucion->basesatisfaccion_id) || is_null($ejecucion->basesatisfaccion_id)) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>'
                                            , Url::to(['formularios/showformulariodiligenciadohistorico'
                                                , 'tmp_id' => $model["id"],'view'=>"reportes/historicoformularios"]), [
                                        'title' => Yii::t('yii', 'ver formulario'),
                                        'target' => "_blank"
                            ]);
                        } else {
                            //if ($modelBase->estado == "Cerrado") {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['basesatisfaccion/showformulariogestion'
                                                , 'basesatisfaccion_id' => $modelBase->id, 'banderaescalado'=> 0, 'aleatorio' => false ,'preview' => 1, 'fill_values' => true,'view'=>"reportes/historicoformularios"]), [
                                        'title' => Yii::t('yii', 'ver formulario'),
                                        'target' => "_blank"
                            ]);
                            //}
                        }
                    }
                },
                        'update' => function ($url, $model) {
                    $fecha = date('Y-m-d H:i:s');
                    $nuevafecha = strtotime('-4 month', strtotime($fecha));
                    $nuevafecha = date('Y-m-d H:i:s', $nuevafecha);
                    $ejecucion = \app\models\Ejecucionformularios::findOne(["id" => $model["fid"]]);
                    if (isset($ejecucion->basesatisfaccion_id)) {
                        $modelBase = app\models\BaseSatisfaccion::findOne($ejecucion->basesatisfaccion_id);
                    }
                    if ($model['created'] >= $nuevafecha) {
                        if ($ejecucion->basesatisfaccion_id == '' || empty($ejecucion->basesatisfaccion_id) || is_null($ejecucion->basesatisfaccion_id)) {

                            if (Yii::$app->user->identity->isAdminSistema() || Yii::$app->user->identity->id == $model['ideva'] || Yii::$app->user->identity->isModificarMonitoreo()) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>'
                                                , Url::to(['formularios/editarformulariodiligenciado'
                                                    , 'tmp_id' => $model["id"],'view'=>"reportes/historicoformularios"]), [
                                            'title' => Yii::t('yii', 'Update'),
                                            'target' => "_blank",
                                ]);
                            }
                        } else {
                            if ($modelBase->estado == "Cerrado" && (Yii::$app->user->identity->isAdminSistema() || Yii::$app->user->identity->id == $model['ideva'] || Yii::$app->user->identity->isModificarMonitoreo())) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['basesatisfaccion/showformulariogestion'
                                                    , 'basesatisfaccion_id' => $modelBase->id, 'banderaescalado'=> 0, 'aleatorio' => false, 'preview' => 0, 'fill_values' => false,'view'=>"reportes/historicoformularios"]), [
                                            'title' => Yii::t('yii', 'Update'),
                                            'target' => "_blank"
                                ]);
                            }
                        }
                    }
                },
                        'calculate' => function ($url, $model) {
                    $fecha = date('Y-m-d H:i:s');
                    $fechaantigua = strtotime('-6 month', strtotime($fecha));
                    $fechaantigua = date('Y-m-d H:i:s', $fechaantigua);
                    if ($model['created'] >= $fechaantigua) {
                        return Html::a('<span class="glyphicon glyphicon-stats"></span>', '', [
                                    'title' => Yii::t('yii', 'Calculos'),
                                    'data-pjax' => 'w0',
                                    'onclick' => "                                    
                                    $.ajax({
                                    type     :'POST',
                                    cache    : false,
                                    url  : '" . Url::to(['reportes/calculatefeedback'
                                        , 'formulario_id' => $model["id"]]) . "',
                                    success  : function(response) {
                                        $('#ajax_result').html(response);
                                    }
                                   });
                                   return false;",
                        ]);
                    }
                },
                    'notificacion' => function ($url, $model) {
                    $notificacion = \app\models\SegundoCalificador::find()->where(['id_ejecucion_formulario' => $model["fid"]])->one();
                    if (Yii::$app->user->identity->id == $model['usua_id_lider']) {
                        if (!isset($notificacion)) {
                            return Html::a('<span style="color: #fa142f;" class="glyphicon glyphicon-hand-up"></span>', '', [
                                        'title' => Yii::t('yii', 'notificaciones segundo calificador'),
                                        'data-pjax' => 'w0',
                                        'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['site/create'
                                            , 'id' => $model["fid"], 'bandera' => 0,'historico'=>1, 'esLider' => 1]) . "',
                                success  : function(response) {
                                    $('#ajax_result').html(response);
                                }
                               });
                               return false;",
                            ]);
                        } else {
                            return Html::a('<span style="color: green" class="glyphicon glyphicon-hand-up"></span>', '', [
                                        'title' => Yii::t('yii', 'notificaciones segundo calificador'),
                                        'data-pjax' => 'w0',
                                        'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['site/create'
                                            , 'id' => $model["fid"], 'bandera' => 1,'historico'=>1]) . "',
                                success  : function(response) {
                                    $('#ajax_result').html(response);
                                }
                               });
                               return false;",
                            ]);
                        }
                    }
                },
                        'delete' => function ($url, $model) {
                    //ENLACE PARA BORRAR VALORACIONES                   
                    if (in_array(Yii::$app->user->identity->id, Yii::$app->params["idUsersDelete"])) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['formularios/borrarformulariodiligenciado',
                                            'tmp_id' => $model["id"]]), [
                                    'title' => Yii::t('yii', 'delete'),
                                    'data-pjax' => 'w0',
                                    'onclick' => "
                                                if (confirm('"
                                    . Yii::t('app', 'Are you sure '
                                            . 'you want to delete '
                                            . 'this item?') . "')) {                                                            
                                                    return true;
                                                }else{
                                                    return false;
                                                }",
                                        ]
                        );
                    }
                },
                    ]
                ],
        [
            'header' => 'Fecha',
            'value' => 'created'
        ],
        [
            'header' => 'Valorador',
            'value' => 'usuario'
        ],
	[
            'header' => 'Valorador C茅dula',
            'value' => 'ident_valorador'
        ],
        [
            'header' => 'Equipo',
            'value' => 'equipoName'
        ],
        [
            'header' => 'L铆der',
            'value' => 'usuarioLider'
        ],
        [
            'header' => 'Valorado C茅dula',
            'attribute' => 'eidentificacion'
        ],
        [
            'header' => 'Valorado',
            'value' => 'evaluado'
        ],
        [
            'header' => 'Formulario',
            'value' => 'formulario'
        ],
        [
            'header' => 'Programa/PCRC',
            'value' => 'nmarbol'
        ],
	[
            'header' => Yii::t('app', 'Cliente'),
            'attribute' => 'score',
            'value' => function($data) {
                if (!isset($data['codcliente']) || empty ($data['codcliente'])) {
                    return '-';
                } else {
                    return $data['codcliente'];
                }
                //return $data->getGestionado($data->snaviso_revisado);
            }
            
        ],
        [
             'header' => Yii::t('app', 'Centro Costo'),
            'attribute' => 'score',
            'value' => function($data) {
                if (!isset($data['centrocosto']) || empty ($data['centrocosto'])) {
                    return '-';
                } else {
                    return $data['centrocosto'];
                }
                //return $data->getGestionado($data->snaviso_revisado);
            }
            
        ],
        [
            'header' => Yii::t('app', 'Ciudad'),
            'attribute' => 'score',
            'value' => function($data) {
                if (!isset($data['ciudad']) || empty ($data['ciudad'])) {
                    return '-';
                } else {
                    return $data['ciudad'];
                }
                //return $data->getGestionado($data->snaviso_revisado);
            }
            
        ],
        [
             'header' => Yii::t('app', 'Director'),
            'attribute' => 'score',
            'value' => function($data) {
                if (!isset($data['director']) || empty ($data['director'])) {
                    return '-';
                } else {
                    return $data['director'];
                }
                //return $data->getGestionado($data->snaviso_revisado);
            }
            
        ],
        [
            'header' => Yii::t('app', 'Gerente'),
           'attribute' => 'score',
           'value' => function($data) {
               if (!isset($data['gerente']) || empty ($data['gerente'])) {
                   return '-';
               } else {
                   return $data['gerente'];
               }
               //return $data->getGestionado($data->snaviso_revisado);
           }
           
       ],
        [
            'header' => 'Rol',
            'value' => 'role_nombre'
        ],
        [
            'header' => 'Dimensi贸n',
            'value' => 'dimension'
        ],
        //'score',
        [
            'header' => Yii::t('app', 'score'),
            'attribute' => 'score',
            'value' => function($data) {
                if (!isset($data['score'])) {
                    return '-';
                } else {
                    return $data['score'];
                }
                //return $data->getGestionado($data->snaviso_revisado);
            }
        ],
        //'pec_rac',
        [
            'header' => Yii::t('app', 'pec_rac'),
            'attribute' => 'pec_rac',
            'value' => function($data) {
                if (!isset($data['pec_rac'])) {
                    return '-';
                } else {
                    return $data['pec_rac'];
                }
                //return $data->getGestionado($data->snaviso_revisado);
            }
        ],
        [
            'header' => $text[0]['detexto'],
            'attribute' => 'i1_nmcalculo',
            'value' => function($data) {
                if (!isset($data['i1_nmcalculo'])) {
                    return '-';
                } else {
                    return $data['i1_nmcalculo'];
                }
                //return $data->getGestionado($data->snaviso_revisado);
            }
        ],
        [
            'header' => $text[1]['detexto'],
            'attribute' => 'i2_nmcalculo',
            'value' => function($data) {
                if (!isset($data['i2_nmcalculo'])) {
                    return '-';
                } else {
                    return $data['i2_nmcalculo'];
                }
                //return $data->getGestionado($data->snaviso_revisado);
            }
        ],
        [
            'header' => $text[2]['detexto'],
            'attribute' => 'i3_nmcalculo',
            'value' => function($data) {
                if (!isset($data['i3_nmcalculo'])) {
                    return '-';
                } else {
                    return $data['i3_nmcalculo'];
                }
                //return $data->getGestionado($data->snaviso_revisado);
            }
        ],
        [
            'header' => $text[3]['detexto'],
            'attribute' => 'i4_nmcalculo',
            'value' => function($data) {
                if (!isset($data['i4_nmcalculo'])) {
                    return '-';
                } else {
                    return $data['i4_nmcalculo'];
                }
                //return $data->getGestionado($data->snaviso_revisado);
            }
        ],
        [
            'header' => $text[4]['detexto'],
            'attribute' => 'i5_nmcalculo',
            'value' => function($data) {
                if (!isset($data['i5_nmcalculo'])) {
                    return '-';
                } else {
                    return $data['i5_nmcalculo'];
                }
                //return $data->getGestionado($data->snaviso_revisado);
            }
        ],
        [
            'header' => $text[5]['detexto'],
            'attribute' => 'i6_nmcalculo',
            'value' => function($data) {
                if (!isset($data['i6_nmcalculo'])) {
                    return '-';
                } else {
                    return $data['i6_nmcalculo'];
                }
                //return $data->getGestionado($data->snaviso_revisado);
            }
        ],
        [
            'header' => $text[6]['detexto'],
            'attribute' => 'i7_nmcalculo',
            'value' => function($data) {
                if (!isset($data['i7_nmcalculo'])) {
                    return '-';
                } else {
                    return $data['i7_nmcalculo'];
                }
                //return $data->getGestionado($data->snaviso_revisado);
            }
        ],
        [
            'header' => $text[7]['detexto'],
            'attribute' => 'i8_nmcalculo',
            'value' => function($data) {
                if (!isset($data['i8_nmcalculo'])) {
                    return '-';
                } else {
                    return $data['i8_nmcalculo'];
                }
                //return $data->getGestionado($data->snaviso_revisado);
            }
        ],
        [
            'header' => $text[8]['detexto'],
            'value' => 'i9_nmcalculo',
            'value' => function($data) {
                if (!isset($data['i9_nmcalculo'])) {
                    return '-';
                } else {
                    return $data['i9_nmcalculo'];
                }
                //return $data->getGestionado($data->snaviso_revisado);
            }
        ],
        [
            'header' => $text[9]['detexto'],
            'attribute' => 'i10_nmcalculo',
            'value' => function($data) {
                if (!isset($data['i10_nmcalculo'])) {
                    return '-';
                } else {
                    return $data['i10_nmcalculo'];
                }
                //return $data->getGestionado($data->snaviso_revisado);
            }
        ],
        [
            'header' => Yii::t('app', 'Dscomentario'),
            'attribute' => 'dscomentario',
            'contentOptions' => [
                'style'=> (!$export) ? 'min-width: 200px; overflow: auto; word-wrap: break-word;' : ''
            ],
            'value' => (!$export) ? function($data) {
                    return substr($data['dscomentario'], 0, 100) . '...';
                } : function($data) {
                return $data['dscomentario'];
            }
        ],
        [
            'header' => Yii::t('app', 'Id Usuario/Modificaci贸n'),
            'attribute' => 'usua_id_modifica'
        ],
        [
            'header' => Yii::t('app', 'Fecha de Modificaci贸n'),
            'attribute' => 'modified'
        ],
        [
            'header' => 'ID Evaluado',
            'value' => 'evaluado_id'
        ],
        'formulario_id',
        [
            'header' => 'ID Arbol',
            'value' => 'fid'
        ],                
		[
            'header' => 'Valoraci贸n Adicional y/o Escalada',
            'value' => function($data) {
                if ($data['escalado']=='1') {
                    return 'Valoraci髇 Escalada';
                } else {
                    if ($data['escalado']!='') {
                        return 'Valoraci髇 Adicional, Id valoraci髇 principal:'.$data['ejec_principal'];
                    }                    
                }
                return 'N/A';
                //return $data->getGestionado($data->snaviso_revisado);
            }
        ],
        
            ];

            $fullExportMenu = ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'target' => '_blank',
    //'fontAwesome' => true,
             'exportRequestParam' => 'exportformularios',
             'filename' => Yii::t('app', 'Reporte_formularios') . '_' . date('Ymd'),
    'asDropdown' => false, // this is important for this case so we just need to get a HTML list    
    'dropdownOptions' => [
        'label' => Yii::t('app', 'Export All'),
                    'class' => 'btn btn-default'
    ],
             'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_PDF => false,
                    ExportMenu::FORMAT_HTML => false,
                ],'columnSelectorOptions' => [
                    'label' => Yii::t('app', 'Columns'),
                ],
]);
           /* echo ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumns,
                'columnSelectorOptions' => [
                    'label' => Yii::t('app', 'Columns'),
                ],
                'dropdownOptions' => [
                    'label' => Yii::t('app', 'Export All'),
                    'class' => 'btn btn-default'
                ],
                //'fontAwesome' => true,
                'showConfirmAlert' => false,
                'target' => '_blank',
                'filename' => Yii::t('app', 'Reporte_formularios') . '_' . date('Ymd'),
                'exportRequestParam' => 'exportformularios',
                'columnBatchToggleSettings' => [
                    'label' => Yii::t('app', 'All')
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_PDF => false,
                    ExportMenu::FORMAT_HTML => false,
                ]

            ]);*/
            ?>
            <br/><br/>
            <?php
            echo kartik\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumns,
				    'panel' => [
        'type' => kartik\grid\GridView::TYPE_DEFAULT,
        //'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-book"></i> Library</h3>',
    ],
    // the toolbar setting is default
    'toolbar' => [
        '{export}',
    ],
    // configure your GRID inbuilt export dropdown to include additional items
    'export' => [
        'label' => Yii::t('app', 'Export All'),
        //'fontAwesome' => true,
        'itemsBefore'=> [
            '<li role="presentation" class="divider"></li>',
            '<li class="dropdown-header">Exportar todos los datos</li>',
            $fullExportMenu
        ]
    ],
            ]);
            ?>
        <?php endif; ?>
        <?php
        echo Html::tag('div', '', ['id' => 'ajax_result']);
        ?>