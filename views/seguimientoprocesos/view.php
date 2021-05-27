<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use \app\models\BaseSatisfaccionSearch;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;

$this->params['breadcrumbs'][] = ['label' => 'Seguimiento del tecnico', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Vista Valorador';

?>
<div class="control-procesos-view">

    <?= $this->render('_formview', [
				'model' => $model,
				'nomValorador' => $nomValorador,
				'idValorador' => $idValorador,
				'dataProvider2' => $dataProvider2,
				'varNametc' => $varNametc,
				'fechainiC' => $fechainiC,
				'fechafinC' => $fechafinC,
    ]) ?>

</div>
