<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use app\models\ControlProcesosPlan;
use yii\db\Query;
use app\models\Hojavidaroles;

$this->title = 'Gestor Plan de Satisfacción - Subir Archivos';
$this->params['breadcrumbs'][] = $this->title;

    $sesiones =Yii::$app->user->identity->id;

    $template = '<div class="col-md-12">'
  . ' {input}{error}{hint}</div>';
    
?>
<!-- Capa Proceso -->
<div id="capaIdProceso" class="capaProceso" style="display: inline;">

    <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            "method" => "post",
            "enableClientValidation" => true,
            'options' => ['enctype' => 'multipart/form-data'],
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
            ]
        ]) 
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">

                <label style="font-size: 15px;"> <?= Yii::t('app', ' Anexo del Contrato') ?></label>
                        
                <?= $form->field($model, 'file', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->fileInput(["class"=>"input-file" ,'id'=>'idfile'])->label('') ?>                                 

            </div>
        </div>
        
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #C148D0;"></em><?= Yii::t('app', ' Agregar Datos') ?></label>
               
                <?= Html::submitButton("Subir", ["class" => "btn btn-primary", "onclick" => "cargar();"]) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #C148D0;"></em><?= Yii::t('app', ' Cancelar y Regresar') ?></label>
                <?= Html::a('Regresar',  ['registrarplan','id_plan'=>$id], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
                ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

