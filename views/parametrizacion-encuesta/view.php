<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\ParametrizacionEncuesta */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Parametrizacion Encuestas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parametrizacion-encuesta-view">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['parametrizacionencuesta','id' => $model->id], ['class' => 'btn btn-primary updateParametrizacion']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'] , ['class' => 'btn btn-default']) ?>
    </p>

     <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'cliente',
                'value' => $model->cliente0->name
            ],
            [
                'attribute' => 'programa',
                'value' => $model->programa0->name
            ],
        ],
    ])
    ?>

</div>


