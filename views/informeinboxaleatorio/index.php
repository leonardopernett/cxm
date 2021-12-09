<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InformeInboxAleatorioSatuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Informe Inbox Aleatorios');
$this->params['breadcrumbs'][] = $this->title;
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
?>
<div class="informe-inbox-aleatorio-index">

<!--    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>-->

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>
    <div class="col-md-6">  
        <?=
        $form->field($searchModel, 'fecha_creacion', [
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
            'pluginOptions' => [
                'timePicker' => false,
                'format' => 'Y-m-d'
        ]]);
        ?>
    </div>
    <div class="col-md-6">
        <?=
        $form->field($searchModel, 'estado'
                , ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template
        ])->dropDownList(
                [
            "NORMAL" => "NORMAL",
            "AUTOCOMPLETA LA CUOTA" => "AUTOCOMPLETA LA CUOTA",
            "CRITICA POR CAPACIDAD DEL PROCESO" => "CRITICA POR CAPACIDAD DEL PROCESO"
                ]
                , ['prompt' => 'Seleccione ...'])
        ?>
    </div>        


    <div class="row">
        <div class="col-md-6">
            <?=
            $form->field($searchModel, 'pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput()
            ?>
        </div>        
    </div>


    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton(Yii::t('app', 'Buscar'), ['class' => 'btn btn-primary'])
            ?>
        </div>        
    </div>
    <?php ActiveForm::end(); ?>  

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            'fecha_creacion',
            'pcrc',
            'encu_diarias_pcrc',
            'encu_diarias_totales',
            //'encu_mes_pcrc',
            //'encu_mes_totales',
            'faltaron',
            'disponibles',
            [
                'attribute' => 'estado',
                'format' => 'raw',
                'value' => function ($data) {
                    switch ($data->estado) {
                        case "CRITICA POR CAPACIDAD DEL PROCESO":
                            $span = Html::tag("span", $data->estado, ["class" => "label label-danger"]);
                            break;
                        case "NORMAL":
                            $span = Html::tag("span", $data->estado, ["class" => "label label-primary"]);
                            break;
                        case "AUTOCOMPLETA LA CUOTA":
                            $span = Html::tag("span", $data->estado, ["class" => "label label-success"]);
                            break;
                        default:
                            $span = $data->estado;
                            break;
                    }
                    return $span;
                }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                    ],
                ],
            ]);
            ?>

</div>
