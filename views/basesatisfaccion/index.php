<?php


use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BaseSatisfaccionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Gestion Satisfaccion');
if(isset($aleatorio)){
   $this->title = Yii::t('app', 'Gestion Satisfaccion Proceso'); 
}
if(isset($declinadas)){
   $this->title = Yii::t('app', 'Gestion Satisfaccion Declinadas'); 
}
$this->params['breadcrumbs'][] = $this->title;
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';

if (!isset($aleatorio) || !$aleatorio) {
    $aleatorio = false;
}
?>
<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Cambio-gestión-satisfacción.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
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
<br><br>
<div class="base-satisfaccion-index">
    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
    }
    ?>

    <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],'layout' => 'horizontal']); ?>

    <div class="row">
        <div class="col-md-6">
            <?=
                    $form->field($searchModel, 'pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
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
            $form->field($searchModel, 'fecha', [
                'labelOptions' => ['class' => 'col-md-12'],
                'template' => '<div class="col-md-4">{label}</div>'
                . '<div class="col-md-8"><div class="input-group">'
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
                'useWithAddon' => true,
                'pluginOptions' => [
                    'autoApply' => true,
                    'clearBtn' => true,
                    //'useWithAddon'=>true,
                    'timePicker' => false,
                    'format' => 'Y-m-d',
                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                    'endDate' => date("Y-m-d"),
                    'opens' => 'left'
                ],
                'pluginEvents' => [
                //'cancel.daterangepicker'=>"function(ev, picker) { $('#basesatisfaccionsearch-fecha').val('');}"
                ]
            ]);
            ?>
        </div>        
    </div>
    <div class="row">
        <div class="col-md-6">
            <?=
                    $form->field($searchModel, 'responsable', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['basesatisfaccion/usuariolist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                    var id=$(element).val();
                    if (id !== "") {
                        $.ajax("' . Url::to(['basesatisfaccion/usuariolist']) . '?id=" + id, {
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
                    $form->field($searchModel, 'estado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->dropDownList($searchModel->estadosList(), ['prompt' => Yii::t('app', 'Select ...')]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?=
                    $form->field($searchModel, 'tipologia', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->dropDownList($searchModel->tipologiasList(), ['prompt' => Yii::t('app', 'Select ...')]);
            ?>
        </div>
        <div class="col-md-6">
            <?=
                    $form->field($searchModel, 'id_lider_equipo', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...'),],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 4,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['reportes/lidereslist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['reportes/lidereslist']) . '?id=" + id, {
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
    <div class="row">
        <div class="col-md-6">
            <?=
                    $form->field($searchModel, 'agente', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->label(Yii::t('app','Valorado'))
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 4,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['reportes/evaluadolist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['reportes/evaluadolist']) . '?id=" + id, {
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
            $form->field($searchModel, 'dimension', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($searchModel->getDimensionsList(), ['prompt' => 'Seleccione ...'])
            ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton(Yii::t('app', 'Buscar'), ['class' => 'btn btn-primary'])
            ?>
            <?= Html::a(Yii::t('app', 'Limpiar'), Url::to(["limpiarfiltro", "aleatorio" => $aleatorio]), ['class' => 'btn btn-default'])
            ?>
            <?= Html::a(Yii::t('app', 'Buscar llamadas'),  "javascript:void(0)", ['class' => 'btn btn-warning llamadasMasivas'])
            ?>
            <?= Html::a(Yii::t('app', 'Buscar Buzones'),  "javascript:void(0)", ['class' => 'btn btn-warning buzonesmasivos'])
            ?>
        </div>        
    </div>
    <?php ActiveForm::end(); ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'attribute' => 'fecha',
                'filter' => false,
                'value' => function($data) {
                    return $data->ano . '-' . $data->mes . '-' . $data->dia;
                }
            ],
            [
                'attribute' => 'hora',
                'value' => function($data) {
                    $val = "";
                    if (strlen($data->hora) <= 4) {
                        return $data->hora;
                    }
                    if (strlen($data->hora) % 2 == 0) {
                        $array = str_split($data->hora, 2);
                        $val = $array[0] . ":" . $array[1] . ":" . $array[2];
                    } else {
                        $array = str_split(substr($data->hora, 1, 4), 2);
                        $val = substr($data->hora, 0, 1) . ":" . $array[0] . ":" . $array[1];
                    }
                    return $val;
                }
            ],
            [
                'attribute' => 'cliente',
                'value' => 'cliente0.name'
            ],
            [
                'attribute' => 'pcrc',
                'value' => 'pcrc0.name'
            ],
            [
               'header' => 'Agente',
               'attribute' => 'agente',
            ],
            'ext',
            [
               'header' => 'Tipo',
               'attribute' => 'tipo_encuesta',
            ],
            'tipologia',
            [
                'header' => '<span class="glyphicon glyphicon-earphone"></span>',
                'format' => 'html',
                'attribute' => 'llamada',
                'value' => function($data) {
                    return (!empty($data->llamada)) ?
                            Html::tag('span', 'SI', ['class' => 'label label-success']) :
                            Html::tag('span', 'NO', ['class' => 'label label-danger']);
                }
                    ],
                    [
                        'header' => '<span class="glyphicon glyphicon-envelope"></span>',
                        'format' => 'html',
                        'attribute' => 'buzon',
                        'value' => function($data) {
                            return (!empty($data->buzon)) ?
                                    Html::tag('span', 'SI', ['class' => 'label label-success']) :
                                    Html::tag('span', 'NO', ['class' => 'label label-danger']);
                        }
                            ],
                            [
                                'format' => 'html',
                                'attribute' => 'refresh',
                                'label' => Yii::t('app', 'Buscar llamadas'),
                                'value' => function($data) {
                                    return ((empty($data->buzon) || empty($data->llamada)) && $data->estado != "Cerrado") ?
                                            Html::a('<span class="glyphicon glyphicon-search"></span> Buscar llamadas'
                                                    , ['basesatisfaccion/buscarllamadas',
                                                'connid' => $data->connid, 'id' => $data->id], ['title' => Yii::t('app', 'Buscar llamadas'),
                                                'class' => 'btn btn-default btn-xs',
                                                'style' => 'color: #ffffff !important']) :
                                            "";
                                }
                                    ],
                                    [
                                        'format' => 'html',
                                        'attribute' => 'estado',
                                        'value' => function($data) {
                                            return $data->getEstados($data->estado);
                                        }
                                    ],
                                    'responsable',
                                    ['class' => 'yii\grid\ActionColumn',
                                        'template' => '{vergestion} {gestion} {block}{delete}',
                                        'buttons' => [
                                            'vergestion' => function ($url, $model) use ($aleatorio) {
                                                    $a = "";
                                                    if (Yii::$app->user->identity->isAdminSistema() && $model->estado == 'Cerrado') {
                                                        $a = Html::a('<span class="glyphicon glyphicon-pencil"></span>'
                                                                        , ['basesatisfaccion/reabrirformulariogestionsatisfaccion',
                                                                    'id' => $model->id, 'aleatorio'=> $aleatorio], ['title' => Yii::t('yii', 'Reabrir')]);
                                                    }
                                                    return $a . "  " . Html::a('<span class="glyphicon glyphicon-eye-open"></span>'
                                                                    , ['basesatisfaccion/showformulariogestion',
                                                                'basesatisfaccion_id' => $model->id, 'preview' => 1, 'fill_values' => true, 'banderaescalado' => false, 'aleatorio'=> $aleatorio], ['title' => Yii::t('yii', 'ver formulario')]);

                                            },
                                                    'gestion' => function ($url, $model) use ($aleatorio) {
                                                if (($model->usado == 'NO' || $model->responsable == Yii::$app->user->identity->username) && $model->estado != 'Cerrado') {
                                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>'
                                                                    , Url::to(['basesatisfaccion/showformulariogestion',
                                                                        'basesatisfaccion_id' => $model->id, 'preview' => 0, 'fill_values' => false, 'banderaescalado' => false, 'aleatorio'=> $aleatorio]), ['title' => Yii::t('yii', 'Gestionar')]);


                                                }
                                            },
                                                    'block' => function ($url, $model) {
                                                if ($model->usado != 'NO') {
                                                    return '<span class="glyphicon glyphicon-asterisk" '
                                                            . 'data-toggle="tooltip" data-placement="top" '
                                                            . 'title="Fecha Gestión: ' . $model->fecha_gestion . '"></span>';
                                                } else {
                                                    return '';
                                                }
                                            }, 'delete' => function ($url, $model) {
                                                if (in_array(Yii::$app->user->identity->id, Yii::$app->params["idUsersDelete"])) {
                                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['delete',
                                                                        'id' => $model["id"]]), [
                                                                'title' => Yii::t('yii', 'Delete'),
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
                                        ],
                                    ]);
                                    ?>
</div>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
    $(document).ready(function(){
        $('.llamadasMasivas').click(function(){
            var buscarMasivos = $("#buscarMasivos");
            buscarMasivos.attr('action', '<?php echo Url::to(["buscarllamadasmasivas", "aleatorio" => ($aleatorio)?1:2]); ?>');
            buscarMasivos.submit();
        });

        $('.buzonesmasivos').click(function(){
            var buscarMasivos = $("#buscarMasivos");
            buscarMasivos.attr('action', '<?php echo Url::to(["buscarllamadasbuzones", "aleatorio" => ($aleatorio)?1:2]); ?>');
            buscarMasivos.submit();
        });
    });
</script>
