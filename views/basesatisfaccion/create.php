<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\BaseSatisfaccion */

$this->title = Yii::t('app', 'Create Base Satisfaccion');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Base Satisfaccions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="base-satisfaccion-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
