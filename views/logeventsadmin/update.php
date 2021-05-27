<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Logeventsadmin */

$this->title = Yii::t('app', 'Update Logeventsadmin: ') . ' ' . $model->id_log;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Logeventsadmins'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_log, 'url' => ['view', 'id' => $model->id_log]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="logeventsadmin-update">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
