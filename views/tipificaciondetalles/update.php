<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Tipificaciondetalles */

$this->title = Yii::t('app', 'Update Tipificaciondetalles: ') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tipificaciondetalles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="tipificaciondetalles-update">
    
    <h3><?= Html::encode($this->title) ?></h3>    

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
