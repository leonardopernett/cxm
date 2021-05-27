<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Evaluados */

$this->title = Yii::t('app', 'Create Evaluados');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Evaluados'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="evaluados-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'query2' => $query2,
    ]) ?>

</div>
