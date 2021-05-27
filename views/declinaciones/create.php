<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Declinaciones */

$this->title = Yii::t('app', 'Create Declinaciones');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Declinaciones'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="declinaciones-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
