<?php
/* @var $this yii\web\View */


use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Reporte Valorados');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Repotes'), 'url' => ['valorados']];
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
    background-image: url('../../images/Reporte-valorados.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<!-- Full Page Image Header with Vertically Centered Content -->
<script>
    $(document).ready(function(){
        $.fn.snow();
    });
</script>
<script src="../../js_extensions/mijs.js"> </script>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        
      </div>
    </div>
  </div>
</header>
<br><br>


<div>    

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
        ]); ?>

    <div class="row">
        <div class="col-md-6">  
            <?=
            $form->field($model, 'created',
                    [
                //'addon' => ['prepend' => ['content' => '<i class="glyphicon glyphicon-calendar"></i>']],                 
//                'inputTemplate' => '<div class="input-group col-md-12">'
//                . '<span class="input-group-addon">'
//                . '<i class="glyphicon glyphicon-calendar"></i>'
//                . '</span>{input}{error}{hint}</div>',
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
                    //'timePickerIncrement' => 15,
                    'format' => 'Y-m-d',
                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") ." -1 day")),
                    'endDate' => date("Y-m-d"),
                    'opens' => 'right'
            ]]);
            ?>
        </div>
        <div class="col-md-6">
    	    <?=
	            $form->field($model, 'usua_id',
	                    ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
	            ->widget(Select2::classname(),
	                    [
	                'language' => 'es',
	                'options' => ['placeholder' => Yii::t('app',
	                            'Select ...')],
	                'pluginOptions' => [
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
                                $.ajax("'.Url::to(['usuariolist']).'?id=" + id, {
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
            	$form->field($model, 'dimension_id',
                    ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($model->getDimensionsList(), ['prompt'=>'Seleccione ...'])
            ?>
        </div>
        <div class="col-md-6">
    	    <?=
	            $form->field($model, 'equipo_id',
	                    ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
	            ->widget(Select2::classname(),
	                    [
	                //'data' => array_merge(["" => ""], $data),
	                'language' => 'es',
	                'options' => ['placeholder' => Yii::t('app',
	                            'Select ...')],
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
                                $.ajax("'.Url::to(['equiposlist']).'?id=" + id, {
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
            Html::submitButton(Yii::t('app', 'Buscar'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>
            <?php //Html::a(Yii::t('app', 'Cancel'), ['index'] , ['class' => 'btn btn-default'])     ?>
        </div>        
    </div>


    <?php ActiveForm::end(); ?>    
</div>

<?php if ($showGrid): ?>
<br>
    <?= Yii::t('app', 'Resultados') ?>
    <?php
    
    $gridColumns = [
        //['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'agrupacion',
            'value' => 'col1'
        ],
        [
            'attribute' => 'equipo_id',
            'value' => 'col2'
        ],
        [
            'attribute' => 'lider',
            'value' => 'col3'
        ],
        [
            'attribute' => 'evaluado_id',
            'value' => 'col4'
        ],
        [
            'attribute' => 'Cedula Evaluado',            
            'value' => 'evaluados.identificacion'
        ],
        [
            'attribute' => 'nro_monitoreos',
            'value' => 'col5'
        ],
        [
            'header' => 'Score',
            'attribute' => 'Score',
            //'value' => 'col6',
            'value' => function($data) {
                if($data['col6'] == '0') {
                    return '-';
                }else{
                  return $data['col6'];  
                }
                
            }
        ],
        [
            'header' => Yii::t('app', 'i1_nmcalculo'),
            'attribute' => 'pec',
            //'value' => 'col7',
            'value' => function($data) {
                if($data['col7'] == '0') {
                    return '-';
                }else{
                  return $data['col7'];  
                }
                
            }
        ],
        [
            'header' => Yii::t('app', 'i2_nmcalculo'),
            'attribute' => 'PENC',
            //'value' => 'col8',
            'value' => function($data) {
                if($data['col8'] == '0') {
                    return '-';
                }else{
                  return $data['col8'];  
                }
                
            }
        ],
        [
            'header' => Yii::t('app', 'i4_nmcalculo'),
            'attribute' => 'carino',
            //'value' => 'col10',
            'value' => function($data) {
                if($data['col10'] == '0') {
                    return '-';
                }else{
                  return $data['col10'];  
                }
                
            }
        ],
        [
            'header' => Yii::t('app', 'i5_nmcalculo'),
            'attribute' => 'na',
            //'value' => 'col11',
            'value' => function($data) {
                if($data['col11'] == '0') {
                    return '-';
                }else{
                  return $data['col11'];  
                }
               
            }
        ],
        [
            'header' => Yii::t('app', 'i6_nmcalculo'),
            'attribute' => 'no',
            //'value' => 'col12',
            'value' => function($data) {
                if($data['col12'] == '0') {
                    return '-';
                }else{
                  return $data['col12'];  
                }
             
            }
        ],
        [
            'header' => Yii::t('app', 'i7_nmcalculo'),
            'attribute' => 'texto7',
            //'value' => 'col13',
            'value' => function($data) {
                if($data['col13'] == '0') {
                    return '-';
                }else{
                  return $data['col13'];  
                }
                
            }
        ],
        [
            'header' => Yii::t('app', 'i8_nmcalculo'),
            'attribute' => 'texto8',
            //'value' => 'col14',
            'value' => function($data) {
                if($data['col14'] == '0') {
                    return '-';
                }else{
                  return $data['col14'];  
                }
                
            }
        ],
        [
            'header' => Yii::t('app', 'i9_nmcalculo'),
            'attribute' => 'texto9',
            //'value' => 'col15',
            'value' => function($data) {
                if($data['col15'] == '0') {
                    return '-';
                }else{
                  return $data['col15'];  
                }
               
            }
        ],
        [
            'header' => Yii::t('app', 'i10_nmcalculo'),
            'attribute' => 'texto10',
            //'value' => 'col16',
            'value' => function($data) {
                if($data['col16'] == '0') {
                    return '-';
                }else{
                  return $data['col16'];  
                }
                
            }
        ],
        
    ];

    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'columnSelectorOptions'=>[
            'label' => Yii::t('app', 'Columns'),
        ],
        'dropdownOptions' => [
            'label' => Yii::t('app', 'Export All'),
            'class' => 'btn btn-default'
        ],
        //'fontAwesome' => true,
        'showConfirmAlert' => false,
        'target' => '_blank',
        'filename' => Yii::t('app', 'Reporte_valorados') . '_' . date('Ymd'),
        'exportRequestParam' => 'exportvalorados',
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
