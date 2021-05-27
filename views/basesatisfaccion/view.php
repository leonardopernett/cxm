<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\BaseSatisfaccion */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Base Satisfaccions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="base-satisfaccion-view">

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
            'identificacion',
            'nombre',
            'ani',
            'agente',
            'agente2',
            'ano',
            'mes',
            'dia',
            'hora',
            'chat_transfer',
            'ext',
            'rn',
            'industria',
            'institucion',
            'pcrc',
            'cliente',
            'tipo_servicio',
            'pregunta1',
            'pregunta2',
            'pregunta3',
            'pregunta4',
            'pregunta5',
            'pregunta6',
            'pregunta7',
            'pregunta8',
            'pregunta9',
            'connid',
            'tipo_encuesta',
            'comentario:ntext',
            'lider_equipo',
            'coordinador',
            'jefe_operaciones',
            'tipologia',
            'estado',
            'llamada:ntext',
            'buzon:ntext',
            'responsable',
            'usado',
            'fecha_gestion',
        ],
    ]) ?>

</div>
