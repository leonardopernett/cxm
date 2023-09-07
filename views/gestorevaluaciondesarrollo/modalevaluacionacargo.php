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

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

$sessiones = Yii::$app->user->identity->id;

$vardocument = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();

?>

<div id="idCapaUno" style="display: inline">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>
         <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 16px;"><em class="fas fa-bolt" style="font-size: 20px; color: #4D83FE;"></em> Seleccionar persona </label>
                    
                    <?= $form->field($model, "id_evaluacionnombre")->dropDownList($opcion_personas_a_cargo, ['prompt' => 'Seleccionar Una Persona', 'id'=>"id_lista_colaborador_a_cargo", 'style' => 'margin-bottom: 20px;']) ?>
                    
                    <?= Html::submitButton(Yii::t('app', 'Aceptar'),
                                    ['class' => 'btn btn-success',                
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Evaluar Persona a Cargo',
                                    'id'=>'ButtonSearch',
                                    'style' => 'display: inline; margin-bottom: 20px;']) 
                    ?>
                </div>                
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>