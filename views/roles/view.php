<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Roles */

$this->title = $model->role_nombre;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="roles-view">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->role_id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->role_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])
        ?>
    <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->session['rolPage'], ['class' => 'btn btn-default']) ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'role_id',
            'role_nombre',
            'role_descripcion',
            [
                'attribute' => 'per_cuadrodemando',
                'value' => $model->getStringBoolean($model->per_cuadrodemando)
            ],
            [
                'attribute' => 'per_estadisticaspersonas',
                'value' => $model->getStringBoolean($model->per_estadisticaspersonas)
            ],
            [
                'attribute' => 'per_hacermonitoreo',
                'value' => $model->getStringBoolean($model->per_hacermonitoreo)
            ],
            [
                'attribute' => 'per_reportes',
                'value' => $model->getStringBoolean($model->per_reportes)
            ],
            [
                'attribute' => 'per_modificarmonitoreo',
                'value' => $model->getStringBoolean($model->per_modificarmonitoreo)
            ],
            [
                'attribute' => 'per_adminsistema',
                'value' => $model->getStringBoolean($model->per_adminsistema)
            ],
            [
                'attribute' => 'per_adminprocesos',
                'value' => $model->getStringBoolean($model->per_adminprocesos)
            ],
            [
                'attribute' => 'per_editarequiposvalorados',
                'value' => $model->getStringBoolean($model->per_editarequiposvalorados)
            ],
            [
                'attribute' => 'per_inboxaleatorio',
                'value' => $model->getStringBoolean($model->per_inboxaleatorio)
            ],
            [
                'attribute' => 'per_desempeno',
                'value' => $model->getStringBoolean($model->per_desempeno)
            ],
            [
                'attribute' => 'per_abogado',
                'value' => $model->getStringBoolean($model->per_abogado)
            ],
            [
                'attribute' => 'per_jefeop',
                'value' => $model->getStringBoolean($model->per_jefeop)
            ],
            [
                'attribute' => 'per_tecdesempeno',
                'value' => $model->getStringBoolean($model->per_tecdesempeno)
            ],
            [
                'attribute' => 'per_alertas',
                'value' => $model->getStringBoolean($model->per_alertas)
            ],
            [
                'attribute' => 'per_evaluacion',
                'value' => $model->getStringBoolean($model->per_evaluacion)
            ],            
            [
                'attribute' => 'per_externo',
                'value' => $model->getStringBoolean($model->per_externo)
            ],                        
            [
                'attribute' => 'per_directivo',
                'value' => $model->getStringBoolean($model->per_directivo)
            ],                        
            [
                'attribute' => 'per_asesormas',
                'value' => $model->getStringBoolean($model->per_asesormas)
            ],
        ],
    ])
    ?>

</div>
