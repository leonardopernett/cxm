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

$varidlist = Yii::$app->db->createCommand("select ue.nombre_completo, ue.documento from tbl_usuarios_evalua ue where ue.documento_jefe = '$vardocument' and ue.documento != '$vardocument' order by ue.nombre_completo asc")->queryAll();
$listData = [
    '1' => 'Usuario 1',
    '2' => 'Usuario 2',
    '3' => 'Usuario 3',
    '4' => 'Usuario 4'
];

$varTipos = ['Persona no esta a mi cargo' => 'Persona no esta a mi cargo', 'Falta persona a mi cargo' => 'Falta persona a mi cargo', 'Otros inconvenientes' => 'Otros inconvenientes' ];

$queryj = Yii::$app->db->createCommand("select ue.documento_jefe, ue.nombre_jefe from tbl_usuarios_evalua ue 
group by ue.documento_jefe, ue.id_cargo_jefe order by ue.nombre_jefe asc")->queryAll();
$listDataj = ArrayHelper::map($queryj, 'documento_jefe', 'nombre_jefe');

$listdelete = ['Retiro konecta' => 'Retiro konecta', 'No debe realizar evaluacion' => 'No debe realizar evaluacion'];



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
                    
                    <?= $form->field($model, "id_evaluacionnombre")->dropDownList($listData, ['prompt' => 'Seleccionar Una Persona', 'id'=>"id_nombre_evaluacion", 'style' => 'margin-bottom: 20px;']) ?>
                    
                    <?= Html::submitButton(Yii::t('app', 'Aceptar'),
                                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',                
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Evaluar Persona a Cargo',
                                    'onclick' => 'evaluarpersonaacargo();',
                                    'id'=>'ButtonSearch',
                                    'style' => 'display: inline; margin-bottom: 20px;']) 
                    ?>
                </div>                
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>