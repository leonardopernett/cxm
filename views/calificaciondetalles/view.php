<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Calificaciondetalles */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app',
            'Calificaciondetalles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calificaciondetalles-view">

    <h3><?= Html::encode($this->title) ?></h3>
    <hr>

    <p>
        <?= Html::a(Yii::t('app', 'Update'),
                ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])
        ?>
        <?=
        Html::a(Yii::t('app', 'Delete'),
                ['delete', 'id' => $model->id,
            'calificacion_id' => $model->calificacion_id],
                [
            'class' => 'btn btn-danger',
            'onclick' => "
                    if (confirm('" . Yii::t('app',
                    'Are you sure you want to delete this item?') . "')) {                                                            
                        return true;
                    }else{
                        return false;
                    }",
            'data' => ['pjax' => 'w0'],
        ])
        ?>
        <?=
        Html::a(Yii::t('app', 'Cancel'),
                ['index', 'calificacion_id' => $model->calificacion_id],
                ['class' => 'btn btn-default'])
        ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'attribute' => 'sndespliega_tipificaciones',
                'value' => $model->getStringBoolean($model->sndespliega_tipificaciones)
            ],
            [
                'attribute' => 'calificacion_id',
                'value' => $model->calificacion->name
            ],
            'nmorden',
            'i1_povalor',
            'i2_povalor',
            'i3_povalor',
            'i4_povalor',
            'i5_povalor',
            'i6_povalor',
            'i7_povalor',
            'i8_povalor',
            'i9_povalor',
            'i10_povalor',
            [
                'attribute' => 'i1_snopcion_na',
                'value' => $model->getStringBoolean($model->i1_snopcion_na)
            ],
            [
                'attribute' => 'i2_snopcion_na',
                'value' => $model->getStringBoolean($model->i2_snopcion_na)
            ],
            [
                'attribute' => 'i3_snopcion_na',
                'value' => $model->getStringBoolean($model->i3_snopcion_na)
            ],
            [
                'attribute' => 'i4_snopcion_na',
                'value' => $model->getStringBoolean($model->i4_snopcion_na)
            ],
            [
                'attribute' => 'i5_snopcion_na',
                'value' => $model->getStringBoolean($model->i5_snopcion_na)
            ],
            [
                'attribute' => 'i6_snopcion_na',
                'value' => $model->getStringBoolean($model->i6_snopcion_na)
            ],
            [
                'attribute' => 'i7_snopcion_na',
                'value' => $model->getStringBoolean($model->i7_snopcion_na)
            ],
            [
                'attribute' => 'i8_snopcion_na',
                'value' => $model->getStringBoolean($model->i8_snopcion_na)
            ],
            [
                'attribute' => 'i9_snopcion_na',
                'value' => $model->getStringBoolean($model->i9_snopcion_na)
            ],
            [
                'attribute' => 'i10_snopcion_na',
                'value' => $model->getStringBoolean($model->i10_snopcion_na)
            ],
        ],
    ])
    ?>

</div>
