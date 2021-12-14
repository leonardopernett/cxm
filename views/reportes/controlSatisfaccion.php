<?php
/* @var $this yii\web\View */


use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Reporte Control de Satisfacción');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Repotes'), 'url' => ['satisfaccion']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
?>

<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Reporte-control-de-satisfacción.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
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
            $form->field($model, 'pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
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
            $form->field($model, 'fecha', [
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
                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") ." -1 day")),
                    'endDate' => date("Y-m-d"),
                    'opens' => 'left'
            ]]);
            ?>
        </div>
    </div>    

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton(Yii::t('app', 'Buscar'), 
                    ['class' =>'btn btn-primary'])?>
        </div>        
    </div>
    <?php ActiveForm::end(); ?>  
</div>

<?php if($showGrid): ?>
<hr>    
    <?php  
      
    echo ExportMenu::widget([
        'dataProvider' => $dataProvider['result'],
        'columns' => $dataProvider['titulos'],
        'columnSelectorOptions' => [
            'label' => Yii::t('app', 'Columns'),
        ],
        'dropdownOptions' => [
            'label' => Yii::t('app', 'Export All'),
            'class' => 'btn btn-default'
        ],
        'showConfirmAlert' => false,
        'target' => '_blank',
        'filename' => Yii::t('app', 'Rpt_Ctrl_Satisfaccion') .'('
            .str_replace(' - ', '__', $model->fecha).')',
        'exportRequestParam' => 'exportControlsatisfaccion',
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
            'dataProvider' => $dataProvider['result'],
            'columns' => $dataProvider['titulos'],
        ]);
    ?>                   
<?php endif; ?>               