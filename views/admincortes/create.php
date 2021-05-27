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

$this->title = 'Agregar Corte General';
$this->params['breadcrumbs'][] = ['label' => 'Administracion de Cortes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


?>

<div class="control-procesos-create">
    
    <?= $this->render('_form', [
    		'model' => $model,
    	]) ?>

</div>