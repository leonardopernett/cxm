<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\Dashboardcategorias;


$this->title = 'Configuracion de categorias';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Configuración de Categorias Calidad Entto';
?>
<div class="capaUno">
    <div class="row">
    	<div class="col-md-12">
    		<div class="formularios-form" id="capaUno" style="display: inline">
			    <?php $form = ActiveForm::begin([
					'options' => ['enctype' => 'multipart/form-data'],
					'fieldConfig' => [
						'inputOptions' => ['autocomplete' => 'off']
					]
					]) ?>

			        <?= $form->field($model, 'file')->fileInput()->label('') ?>

			        <br>

			        <button class="form-control", style="width:25%; background: #4298B4;" id="buttonID" >Importar</button>

			    <?php ActiveForm::end() ?>
			</div>
    	</div>
    </div>
</div>