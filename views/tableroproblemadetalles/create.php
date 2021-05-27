<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Tableroproblemadetalles */

$this->title = Yii::t('app', 'Create Tableroproblemadetalles');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tableroproblemadetalles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tableroproblemadetalles-create">
        
    <h3><?= Html::encode($this->title) ?></h3>    
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
