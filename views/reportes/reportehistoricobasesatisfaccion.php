<?php
/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Reporte Histórico Base Satisfacción');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Repotes'), 'url' => ['satisfaccion']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs(
        "
    function changePcrc(varPcrc){
        if(varPcrc != ''){
            $.ajax({
                url: '" . Url::to(['reglanegocio/padreclientedato']) . "',
                type:'POST',
                dataType: 'json',
                data: {
                    'pcrc' : varPcrc
                },
                success : function(objRes){
                    $('#basesatisfaccionsearch-cliente').prop('value',objRes.value);
                }
            });
        }else{
            $('#basesatisfaccionsearch-cliente').prop('value','');
        }
    }
"
);
?>

<?php
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
?>
<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Reporte-Historico.png');
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
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br>
<!--<div class="page-header">
    <h3><?php // $this->title ?></h3>
</div>-->

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
                                    $.ajax({
                                        url: "' . Url::to(['reglanegocio/padreclientedato']) . '",
                                        type:"POST",
                                        dataType: "json",
                                        data: {
                                            "pcrc" : id
                                        },
                                        success : function(objRes){
                                            $("#reglanegocio-cliente").prop("value",objRes.value);
                                        }
                                    });
                                    $.ajax("' . Url::to(['basesatisfaccion/getarbolesbypcrc']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post",
                                    }).done(function(data) {
                                        callback(data.results[0]); 
                                         
                                    });
                                }
                            }')
                        ],
                        'pluginEvents' => [
                            "change" => "function() { changePcrc($(this).val()); }",
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
            ])->label(Yii::t('app', 'Fecha Gestion'))->widget(DateRangePicker::classname(), [
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
        
        <div class="col-md-6">
            <?=
            $form->field($model, 'dimension', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($model->getDimensionsList(), ['prompt' => 'Seleccione ...'])
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
</div>


<?php if (isset($_POST['exporthistorico'])): ?>
    <?php if($export):?>
        <?php
            $fileName = Yii::t('app', 'Reporte_Gestion') . '_' . date('Ymd') . "_" .
                Yii::$app->user->identity->id . ".xlsx";
        echo Html::a("Descargar " . $fileName, Url::to("@web/files/" . $fileName));
        /*           
        echo ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $titulos,
            'columnSelectorOptions' => [
                'label' => Yii::t('app', 'Columns'),
            ],
            'dropdownOptions' => [
                'label' => Yii::t('app', 'Export All'),
                'class' => 'btn btn-default'
            ],
            'showConfirmAlert' => false,
            'target' => '_blank',
            'filename' => Yii::t('app', 'Reporte_historicobase') . '_' . date('Ymd'),
            'exportRequestParam' => 'exporthistorico',
            'columnBatchToggleSettings' => [
                'label' => Yii::t('app', 'All')
            ],
            'exportConfig' => [
                ExportMenu::FORMAT_TEXT => false,
                ExportMenu::FORMAT_PDF => false,
                ExportMenu::FORMAT_HTML => false,
            ]
        ]);*/
        ?>
    <?php else: ?>
        <div class="alert alert-warning" role="alert">
            <?= Yii::t("app", "No matching records found"); ?>
        </div>
    <?php endif; ?>               
<?php endif; ?>              