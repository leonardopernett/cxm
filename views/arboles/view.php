<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Arboles */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Arboles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="arboles-view">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'] , ['class' => 'btn btn-default']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'arbol_id',
            'snhoja',
            'formulario_id',
            'nmfactor_proceso',
            'nmumbral_verde',
            'nmumbral_amarillo',
            'nmumbral_positivo',
            'usua_id_responsable',
            'dsorden',
            'tableroproblema_id',
            'tiposllamada_id',
            'dsname_full',
            'bloquedetalle_id',
            'snactivar_problemas',
            'snactivar_tipo_llamada',
        ],
    ]) ?>

</div>
