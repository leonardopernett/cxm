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
    <?php echo Html::hiddenInput('Ejecucionformularios[evaluado_id]', $model->evaluado_id);?>
    <div class="row">
        <div class="col-md-6">  
            <?=
            $form->field($model, 'created', [                
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
                'preview' => function ($url, $model) {
                    if ($url == "asda") {
                        #code...
                    }
                        $cliente = \app\models\Arboles::findOne(["id" => $model["cliente"]]);
                        
                        $ejecucion = base64_encode($model["fid"]);
                       
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['basesatisfaccion/showencuestaamigo'
                                            , 'form_id' => $ejecucion]), [
                                    'title' => Yii::t('yii', 'ver formulario'),
                                    'target' => "_blank"
                        ]);
                      
                },
                    ]
                ],
        [
            'header' => 'Fecha',
            'value' => 'created'
        ],
        [
            'header' => 'Programa/PCRC',
            'value' => 'name'
        ],
        [
            'header' => 'Agente',
            'value' => 'agente'
        ],
        [
            'header' => 'Lider',
            'attribute' => 'lider_equipo'
        ],
        [
            'header' => 'Ext',
            'value' => 'ext'
        ],
        [
            'header' => 'Tipo',
            'value' => 'tipo_encuesta'
        ],
        [
            'header' => 'Tipologia',
            'value' => 'tipologia'
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