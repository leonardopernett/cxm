<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Tipificaciondetalles */

$this->title = Yii::t('app', 'Create Tipificaciondetalles');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tipificaciondetalles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipificaciondetalles-create">
        
    <h3><?= Html::encode($this->title) ?></h3>    

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
