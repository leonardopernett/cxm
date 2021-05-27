<?php

use yii\helpers\Html;
$this->registerJsFile('@web/js/bootstrap-tagsinput.js');
/* @var $this yii\web\View */
/* @var $model app\models\Reglanegocio */

$this->title = Yii::t('app', 'Update Reglanegocio');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Reglanegocios'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="reglanegocio-update">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
