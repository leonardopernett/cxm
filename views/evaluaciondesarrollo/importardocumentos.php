<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;

$this->title = 'Extractar resultados - Evaluacion de desarrollo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='popover']").popover(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);
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
            font-family: "Nunito", sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/Banner_Ev_Desarrollo.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>
<div class="capaCero" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            

            <div class="row">
                <div class="col-md-4">

                    <div class="row">                        
                        <div class="col-md-12">
                            <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-info" style="font-size: 15px; color: #981F40;"></em> Seleccionar Archivo: </label>
                                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <?= $form->field($model, "file[]")->fileInput(['multiple' => false]) ?>
                                        </div>
                                    </div>
                                    <br>       
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?= Html::submitButton("Subir", ["class" => "btn btn-primary"]) ?>
                                        </div>
                                    </div>                     
                                    

                                <?php ActiveForm::end() ?>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-backward" style="font-size: 15px; color: #981F40;"></em> Regresar: </label>
                                <?= Html::a('Regresar',  ['exportarrtadashboard'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
                                ?>
                            </div>
                        </div>
                    </div>

                </div>
                

                <div class="col-md-8">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #981F40;"></em> Datos Registrados: </label>
                        <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                            <caption><?= Yii::t('app', 'Datos...') ?></caption>
                            <thead>
                                <tr>
                                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Documentos') ?></label></th>
                                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha de Ingreso') ?></label></th>
                                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach ($dataList as $key => $value) {
                                        
                                ?>
                                    <tr>
                                        <td><label style="font-size: 10px;"><?php echo  $value['documentosna']; ?></label></td>
                                        <td><label style="font-size: 10px;"><?php echo  $value['fechacreacion']; ?></label></td>
                                        <td class="text-center">
                                          <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminardocumentos','id'=> $value['idevaldodumentosna']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                                        </td>
                                    </tr>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<hr>
