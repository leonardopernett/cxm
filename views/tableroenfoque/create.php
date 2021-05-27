<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TblTableroenfoques */

$this->title = Yii::t('app', 'Create Tbl Tableroenfoques');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tbl Tableroenfoques'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-tableroenfoques-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
