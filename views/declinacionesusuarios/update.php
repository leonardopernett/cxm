<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DeclinacionesUsuarios */

$this->title = Yii::t('app', 'Update Declinaciones Usuarios: ') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Declinaciones Usuarios'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="declinaciones-usuarios-update">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
