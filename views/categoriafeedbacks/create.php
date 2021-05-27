<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Categoriafeedbacks */

$this->title = Yii::t('app', 'Create Categoriafeedbacks');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Categoriafeedbacks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="categoriafeedbacks-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
