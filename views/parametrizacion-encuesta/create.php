<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ParametrizacionEncuesta */

$this->title = Yii::t('app', 'Create Parametrizacion Encuesta');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Parametrizacion Encuestas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parametrizacion-encuesta-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
