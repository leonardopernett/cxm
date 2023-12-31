<?php
/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;

$this->title = Yii::t('app', 'Prom de Calificaciones');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Reportes'), 'url' => ['promcalificaciones']];
$this->params['breadcrumbs'][] = $this->title;
?>


<?php
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
?>
<div class="equipos-evaluados-form">    

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
        ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?=
                    $form->field($model, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
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
        </div>
        <div class="col-md-6">  
            <?=
            $form->field($model, 'created', [
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
                    'format' => 'Y-m-d',
                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                    'endDate' => date("Y-m-d"),
                    'opens' => 'left'
            ]]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?=
                    $form->field($model, 'dimension', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
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
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?=
            Html::submitButton(Yii::t('app', 'Buscar'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>
        </div>  


    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php if ($showGrid): ?>
<br>
    <?= Yii::t('app', 'Resultados') ?>
    <?php
    $text = app\models\Textos::find()->asArray()->all();
    $gridColumns = [
        [
            'attribute' => 'Programa/PCRC',
            'value' => 'arbol'
        ],
        [
            'attribute' => 'Dimension',
            'value' => 'tdimension'
        ],
        [
            'attribute' => 'Nombre',
            'value' => 'pregunta'
        ],
        [
            'attribute' => 'conteo',
            'value' => 'registros'
        ],
        [
            'attribute' => $text[0]['detexto'],
            'value' => function($data) {
                if($data['i1'] == '0.00') {
                    return '-';
                }else{
                  return $data['i1'];  
                }
            }
        ],
        [
            'attribute' => $text[1]['detexto'],
            'value' => function($data) {
                if($data['i2'] == '0.00') {
                    return '-';
                }else{
                  return $data['i2'];  
                }
            }
        ],
        [
            'attribute' => $text[2]['detexto'],
            'value' => function($data) {
                if($data['i3'] == '0.00') {
                    return '-';
                }else{
                  return $data['i3'];  
                }
            }
        ],
        [
            'attribute' => $text[3]['detexto'],
            'value' => function($data) {
                if($data['i4'] == '0.00') {
                    return '-';
                }else{
                  return $data['i4'];  
                }
            }
        ],
        [
            'attribute' => $text[4]['detexto'],
            'value' => function($data) {
                if($data['i5'] == '0.00') {
                    return '-';
                }else{
                  return $data['i5'];  
                }
            }
        ],
        [
            'attribute' => $text[5]['detexto'],
            'value' => function($data) {
                if($data['i6'] == '0.00') {
                    return '-';
                }else{
                  return $data['i6'];  
                }
            }
        ],
        [
            'attribute' => $text[6]['detexto'],
            'value' => function($data) {
                if($data['i7'] == '0.00') {
                    return '-';
                }else{
                  return $data['i7'];  
                }
            }
        ],
        [
            'attribute' => $text[7]['detexto'],
            'value' => function($data) {
                if($data['i8'] == '0.00') {
                    return '-';
                }else{
                  return $data['i8'];  
                }
            }
        ],
        [
            'attribute' => $text[8]['detexto'],
            'value' => function($data) {
                if($data['i9'] == '0.00') {
                    return '-';
                }else{
                  return $data['i9'];  
                }
            }
        ],
        [
            'attribute' => $text[9]['detexto'],
            'value' => function($data) {
                if($data['i10'] == '0.00') {
                    return '-';
                }else{
                  return $data['i10'];  
                }
            }
        ],
    ];

    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'columnSelectorOptions' => [
            'label' => Yii::t('app', 'Columns'),
        ],
        'dropdownOptions' => [
            'label' => Yii::t('app', 'Export All'),
            'class' => 'btn btn-default'
        ],
        'showConfirmAlert' => false,
        'target' => '_blank',
        'filename' => Yii::t('app', 'Reporte_calificaciones') . '_' . date('Ymd'),
        'exportRequestParam' => 'exportcalificaciones',
        'columnBatchToggleSettings' => [
            'label' => Yii::t('app', 'All')
        ],
        'exportConfig' => [
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_PDF => false,
            ExportMenu::FORMAT_HTML => false,
        ]
    ]);
    ?>
    <br/><br/>
    <?php
    echo kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
    ]);
    ?>
<?php endif; ?>
