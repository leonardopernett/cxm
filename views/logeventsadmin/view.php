<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Logeventsadmin */

$this->title = $model->id_log;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Logeventsadmins'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logeventsadmin-view">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <p>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id_log], [
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
            'id_log',
            'tabla_modificada',
            'datos_ant',
            'datos_nuevos',
            'fecha_modificacion',
            'usuario_modificacion',
            'id_usuario_modificacion',
        ],
    ]) ?>

</div>
