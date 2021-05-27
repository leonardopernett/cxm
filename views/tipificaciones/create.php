<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Tipificaciones */

$this->title = Yii::t('app', 'Create Tipificaciones');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tipificaciones'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipificaciones-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
