<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Tiposllamadasdetalles */

$this->title = Yii::t('app', 'Create Tiposllamadasdetalles');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tiposllamadasdetalles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tiposllamadasdetalles-create">        
    <h3><?= Html::encode($this->title) ?></h3>    

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
