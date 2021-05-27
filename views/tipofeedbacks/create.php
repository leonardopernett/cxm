<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Tipofeedbacks */

$this->title = Yii::t('app', 'Create Tipofeedbacks');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tipofeedbacks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipofeedbacks-create">
    
    <h3><?= Html::encode($this->title) ?></h3>    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
