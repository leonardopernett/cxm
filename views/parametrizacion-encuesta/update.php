<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ParametrizacionEncuesta */

$this->title = Yii::t('app', 'Update Parametrizacion Encuesta: ') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Parametrizacion Encuestas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="parametrizacion-encuesta-update">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
