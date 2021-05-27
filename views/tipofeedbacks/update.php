<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Tipofeedbacks */

$this->title = Yii::t('app', 'Update Tipofeedbacks: ') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tipofeedbacks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="tipofeedbacks-update">

    <h3><?= Html::encode($this->title) ?></h3>    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
