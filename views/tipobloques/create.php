<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Tipobloques */

$this->title = Yii::t('app', 'Create Tipobloques');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tipobloques'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipobloques-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
