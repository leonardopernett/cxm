<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\EquiposEvaluados */

$this->title = Yii::t('app', 'Create Equipos Evaluados');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Equipos Evaluados'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipos-evaluados-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
