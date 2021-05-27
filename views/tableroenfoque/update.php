<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TblTableroenfoques */

$this->title = Yii::t('app', 'Update Tbl Tableroenfoques: ') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tbl Tableroenfoques'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="tbl-tableroenfoques-update">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
