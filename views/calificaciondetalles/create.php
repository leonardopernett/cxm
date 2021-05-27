<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Calificaciondetalles */

$this->title = Yii::t('app', 'Create Calificaciondetalles');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Calificaciondetalles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calificaciondetalles-create">
    
    <h3><?= Html::encode($this->title) ?></h3>    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
