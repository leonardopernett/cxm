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

$this->title = Yii::t('app', 'Tablero de Experiencias');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Reportes'), 'url' => ['tableroexperiencias']];
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
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['formularios/getarbolesbypermisos']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['formularios/getarbolesbypermisos']) . '?id=" + id, {
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
            $form->field($model, 'created',
                    [
                'labelOptions' => ['class' => 'col-md-12'],
                'template' => '<div class="col-md-4">{label}</div>'
                . '<div class="col-md-8"><div class="input-group">'
                . '<span class="input-group-addon" id="basic-addon1">'
                . '<i class="glyphicon glyphicon-calendar"></i>'
                . '</span>{input}</div>{error}{hint}</div>',
                'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                'options' => ['class' => 'drp-container form-group']
            ])->widget(DateRangePicker::classname(),
                    [
                'useWithAddon' => true,
                'convertFormat' => true,
                'presetDropdown' => true,
                'readonly' => 'readonly',
                'pluginOptions' => [
                    'timePicker' => false,
                    'format' => 'Y-m-d',
                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") ." -1 day")),
                    'endDate' => date("Y-m-d"),
                    'opens' => 'left'
            ]]);
            ?>
        </div>
	</div>
	
	<div class="row">
         <div class="col-sm-6">
          <?= $form->field($model, 'tipoReporte', ['labelOptions' => ['class' => 'col-md-6']])->radioList(array('1'=>'Todos los Registros','2'=>'Evolución por enfoque', '3'=>'Evolución por problema')); ?>

        </div>  
	</div>
	<div class="row">
         <div class="col-sm-6">
            <?=
            Html::submitButton(Yii::t('app', 'Buscar'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>
        </div>  
         
           
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php if ($showGrid): ?>
<br>
    <?= Yii::t('app', 'Resultados') ?>
    <?php
        echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columnSelectorOptions'=>[
            'label' => Yii::t('app', 'Columns'),
        ],
        'dropdownOptions' => [
            'label' => Yii::t('app', 'Export All'),
            'class' => 'btn btn-default'
        ],
        'showConfirmAlert' => false,
        'target' => '_blank',
        'filename' => Yii::t('app', 'Reporte_experiencias') . '_' . date('Ymd'),
        'exportRequestParam' => 'exporttablero',
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
    ]);
    ?>
<?php endif; ?>