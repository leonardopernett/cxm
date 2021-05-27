<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Calificacions */

$this->title = Yii::t('app', 'Create Calificacions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Calificacions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calificacions-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
