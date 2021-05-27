<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Transacions */

$this->title = Yii::t('app', 'Create Transacions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transacions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transacions-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
