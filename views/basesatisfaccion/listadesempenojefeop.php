<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BaseSatisfaccionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $('[data-toggle="tooltip"]').tooltip();
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);


$this->title = Yii::t('app', 'valoracion escaladas');

$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
        
if (!isset($aleatorio) || !$aleatorio) {
    $aleatorio = false;
}
?>
<div class="base-satisfaccion-index">

    <div class="page-header">
        <h3>Notificaciones de bajo desempeño</h3>
    </div>
    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
    }
    ?>

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?=
            $form->field($model, 'fecha_ingreso', [
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
        <div class="col-md-6">
            <?=
                    $form->field($model, 'asesor', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['asesorlist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                    var id=$(element).val();
                    if (id !== "") {
                        $.ajax("' . Url::to(['asesorlist']) . '?id=" + id, {
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
                $form->field($model, 'lider', ['labelOptions' => ['class' => 'col-md-12',], 'template' => $template])
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
    
    <div class="row">
        <div class="col-md-6">
            <?php
            //agregar escalado por
            ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton(Yii::t('app', 'Buscar'), ['class' => 'btn btn-primary'])
            ?>           
        </div>        
    </div>
    <?php ActiveForm::end(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
                [
                    'attribute' => 'Fecha de Notificacion',
                    'value' => 'fecha_ingreso',
                    'enableSorting' => false,
                ],
            'asesor',
            'Nombre',
            'Identificacion',
                [
                    'attribute' => 'Lider',
                    'value' => 'namelider',
                ],
                [   
                    'header' => "<a data-toggle='tooltip' title='Explicacion para ese caso.'>Desempeno</a>",
                    'attribute' => 'Desempeno',
                ],
                [
                    'attribute' => '# de Notificacion',
                    'value' => 'notificacion',
                ],
                [
                    'attribute' => 'Compromiso Asesor',
                    'value' => 'notificado_asesor',
                ],
                [
                    'attribute' => 'Feedback Lider',
                    'value' => 'notificado_lider',
                ],
            'fecha_finalizacion',

            ['class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'template' => '{view}{update}{delete}',
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                    'title' => Yii::t('app', 'lead-update'),
                        ]);
                    },
                    'delete' => function ($url, $model) {

                        if ($url == "asda") {
                            //code...
                        }
                    },
                    'view' => function ($url, $model) {

                        if ($url == "asda") {
                            //code...
                        }
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {

                    if ($action === 'update') {
                        $url ='showalertadesempeno?form_id='.base64_encode($model->id).'&lider=no&jefeop=si';
                        return $url;
                    }


                  }
          ],
        ],
    ]);

    $gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    'asesor',
    'Nombre',
    'Identificacion',
    'Desempeno',
    'lider',
    'notificacion',
    'notificado_asesor',
    'notificado_lider',
    'fecha_finalizacion',
    ['class' => 'yii\grid\ActionColumn'],
];

// Renders a export dropdown menu
echo ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns
]); ?>
</div>


