<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BaseSatisfaccionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

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
                    'timePicker' => false,
                    'format' => 'Y-m-d',
                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                    'endDate' => date("Y-m-d"),
                    'opens' => 'left'
                ],
                'pluginEvents' => [
                ]
            ]);
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
            'Desempeno',
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
                    'update' => function ($url) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                    'title' => Yii::t('app', 'lead-update'),
                        ]);
                    },
                    'delete' => function ($url) {

                        if ($url == "asda") {
                            //code...
                        }
                    },
                    'view' => function ($url) {

                        if ($url == "asda") {
                            //code...
                        }
                    },
                ],
                'urlCreator' => function ($action, $model) {

                    if ($action === 'update') {
                        $url ='showalertadesempeno?form_id='.base64_encode($model->id).'&lider=no';
                        return $url;
                    }


                  }
          ],
        ],
    ]); ?>
</div>




