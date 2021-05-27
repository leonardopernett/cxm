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

$this->title = 'Actualizar Tipo corte: ' . ' ' . $model->idtc;
$this->params['breadcrumbs'][] = 'Actualizar Tipo Corte General';
?>
<div class="control-procesos-update">

    <?= $this->render('_formupdate', [
        'model' => $model,
        'dataProvider' => $dataProvider,
        'nameVal' => $nameVal,
    ]) ?>

</div>
