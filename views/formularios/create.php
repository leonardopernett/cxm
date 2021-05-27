<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Formularios */

$this->title = Yii::t('app', 'Create Formularios');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Formularios'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="formularios-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
