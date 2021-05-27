<?php

use yii\helpers\Html;
use \app\models\BaseSatisfaccionSearch;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model app\models\ControlProcesos */

$this->title = 'Actualizar Valoraciones: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Control Procesos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Actualizar Valoraciones';
?>
<div class="control-procesos-update">

    <?= $this->render('_formupdate2', [
    			'model' => $model,
    			'varIdusua' => $varIdusua,
    			'varName' => $varName,
    			'varCortes' => $varCortes,
			'dataProvider' => $dataProvider,
			'txtProcesos' => $txtProcesos,
    ]) ?>

</div>
