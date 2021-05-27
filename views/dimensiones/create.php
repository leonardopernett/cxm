<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Dimensiones */

$this->title = Yii::t('app', 'Create Dimensiones');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Dimensiones'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dimensiones-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
