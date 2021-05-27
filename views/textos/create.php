<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Textos */

$this->title = Yii::t('app', 'Create Textos');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Textos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="textos-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
