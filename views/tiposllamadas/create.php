<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Tiposllamadas */

$this->title = Yii::t('app', 'Create Tiposllamadas');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tiposllamadas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tiposllamadas-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
