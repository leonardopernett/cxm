<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Equipos */

$this->title = Yii::t('app','Create Equipos valoradores');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Equipo valoradores'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipos-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
