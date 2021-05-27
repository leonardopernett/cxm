<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Tiposeccions */

$this->title = Yii::t('app', 'Create Tiposeccions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tiposeccions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tiposeccions-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
