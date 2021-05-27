<?php

use yii\helpers\Html;
use \app\models\BaseSatisfaccionSearch;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\bootstrap\modal;
use yii\widget\Block;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model app\models\ControlProcesos */

$this->title = 'Agregar Valorado';
$this->params['breadcrumbs'][] = ['label' => 'Equipo de Trabajo'];
$this->params['breadcrumbs'][] = $this->title;


?>

<div class="control-procesos-create">
    
    <?= $this->render('_form', [
        'model' => $model,
        'model2' => $model2,
        'dataProvider' => $dataProvider,
        'id_valorado'=>$id_valorado,
        'count1'=>$count1,
	    'count2'=>$count2,	
	    'count3'=>$count3,
	    'count4'=>$count4,
        'txtusua_id'=>$txtusua_id,
    ]) ?>

</div>