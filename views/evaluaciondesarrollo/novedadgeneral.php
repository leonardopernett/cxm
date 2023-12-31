<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

$this->title = 'Evaluacion de desarrollo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $varidlist = Yii::$app->db->createCommand("select * from tbl_evaluacion_tipoeval where anulado = 0")->queryAll();
    $varTipos = ArrayHelper::map($varidlist, 'idevaluaciontipo', 'tipoevaluacion'); 

?>
<style>
    @import url('https://fonts.googleapis.com/css?family=Nunito');

    .card1 {
            height: auto;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    


    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }

</style>
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >

<div id="idCapaUno" style="display: inline">
    <div id="capaUno" style="display: inline">
        <div class="row">
        	<div class="col-md-12">
                <div class="card1 mb">
                    <?php $form = ActiveForm::begin([
                        'layout' => 'horizontal',
                        'fieldConfig' => [
                            'inputOptions' => ['autocomplete' => 'off']
                          ]
                        ]); ?> 
                    <label style="font-size: 20px;"><em class="fas fa-search" style="font-size: 20px; color: #C148D0;"></em></em> Seleccionar tipo evaluación para verificar novedades:</label>
                    <?= $form->field($model, "tipoevaluacion")->dropDownList($varTipos, ['prompt' => 'Seleccionar evaluaciones', 'id'=>"idtipoeva"]) ?>
                    <div class="text-center">
                        <?= Html::submitButton(Yii::t('app', 'Realizar Búsqueda'),
                                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',                
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Buscar',
                                    'onclick' => 'validarvalor();',
                                    'id'=>'ButtonSearch']) 
                        ?>  
                    </div>                     
                    <?php ActiveForm::end(); ?> 
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function validarvalor(){
        var varidtipoeva = document.getElementById("idtipoeva").value;

        if (varidtipoeva == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Seleccione un tipo de evaluación","warning");
            return; 
        }
    };
</script>