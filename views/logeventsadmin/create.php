<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Logeventsadmin */

$this->title = Yii::t('app', 'Create Logeventsadmin');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Logeventsadmins'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logeventsadmin-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
