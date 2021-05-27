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

$this->title = 'Actualizar Valoración: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Control Procesos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Agregar Valorado', 'url' => ['create']];
$this->params['breadcrumbs'][] = 'Actualizar Valoración';
?>
<div class="control-procesos-update">

    <?= $this->render('_formupdate', [
        'model' => $model,
        'txtvarevaluadoId' => $txtvarevaluadoId,
    ]) ?>

</div>
