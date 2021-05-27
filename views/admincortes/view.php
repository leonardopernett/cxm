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

$this->params['breadcrumbs'][] = ['label' => 'Administración de cortes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Ver Tipo de Corte';

?>
<div class="control-procesos-view">

    <?= $this->render('_formview', [
    			'model' => $model,
				'dataProvider' => $dataProvider,
				'CorteID' => $CorteID,
    ]) ?>

</div>

