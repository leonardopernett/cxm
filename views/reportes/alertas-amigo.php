<?php
/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Mis valoraciones');
?>

<?php
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
$diasMes = date("d");
?>

<div class="page-header">
    <h3><?= $this->title ?></h3>
</div>

<?php
foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
    echo '<div class="alert alert-' . $key . '" role="alert">' . $message . '</div>';
}
?>

<div class="equipos-evaluados-form">    

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
        ]); ?>
    <?php echo Html::hiddenInput('Notificaciones[id]', $model->id); ?>
    <div class="row">
        <div class="col-md-6">  
            <?=
            $form->field($model, 'fecha_ingreso', [                
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
                    'format' => 'Y-m-d',
                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                    'endDate' => date("Y-m-d"),
                    'opens' => 'center',
                    'minDate' => date("Y-m-d", strtotime(date("Y-m-d") . "-$diasMes day -1 month")),
                    'maxDate' => date("Y-m-d", strtotime(date("Y-m-d") . " now"))
            ]]);
            ?>
        </div>
        <div class="col-md-6">
            <div class="form-group">               
                    <?=
                    Html::submitButton(Yii::t('app', 'Buscar'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
                    ?>                      
            </div>
        </div>
    </div>


    <?php ActiveForm::end(); ?>    
</div>

<?php if ($showGrid): ?>    
    <?php
    $text = app\models\Textos::find()->asArray()->all();
    $gridColumns = [
        ['class' => 'yii\grid\ActionColumn',
            'template' => '{preview}{notificacion}',
            'buttons' => [
                    ]
                ],
        [
            'header' => 'Fecha',
            'value' => 'created'
        ],
        [
            'header' => 'Asesor',
            'value' => 'asesor'
        ],
        [
            'header' => 'Líder',
            'value' => 'lider'
        ],
        [
            'header' => 'Asesor Notificado',
            'attribute' => 'notificado_asesor'
        ],
        [
            'header' => 'Respuesta Asesor',
            'value' => 'respuesta_asesor'
        ],
        [
            'header' => 'Lider Notificado',
            'value' => 'notificado_lider'
        ],
        [
            'header' => 'Respuesta Lider',
            'value' => 'respuesta_lider'
        ],
        
        
            ];
            ?>
            <br/><br/>
            <?php
            echo kartik\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumns,
            ]);
            ?>
        <?php endif; ?>
        <?php
        echo Html::tag('div', '', ['id' => 'ajax_result']);
        ?>