<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Tableroproblemas */

$this->title = Yii::t('app', 'Create Tableroproblemas');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tableroproblemas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tableroproblemas-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
