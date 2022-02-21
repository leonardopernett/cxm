<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Seccions */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Seccions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="seccions-view">
    <?php if ($isAjax): ?>
        <h3><?= Html::encode($this->title) ?></h3>
    <?php else: ?>
        <div class="page-header">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>
    <?php endif; ?>    

    <p>
        <?=
        Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])
        ?>

        <?php if ($isAjax): ?>
            <?=
            Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id, 'formulario_id' => $formulario_id], [
                'class' => 'btn btn-danger',
                'onclick' => "
                    if (confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "')) {                                                            
                        return true;
                    }else{
                        return false;
                    }",
                'data' => [
                    'pjax' => 'w0',
                ],
            ])
            ?>
            <?=
            Html::a(Yii::t('app', 'Cancel'), ['index', 'formulario_id' => $formulario_id], ['class' => 'btn btn-default'])
            ?>
        <?php else: ?>
            <?=
            Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
            <?=
            Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-default'])
            ?>
        <?php endif; ?>


    </p>

        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'name',
                [
                    'attribute' => 'formulario_id',
                    'value' => $model->formulario->name
                ],
                [
                    'attribute' => 'tiposeccion_id',
                    'value' => $model->tiposeccion->name
                ],
                'nmorden',
                [
                    'attribute' => 'sndesplegar_comentario',
                    'value' => $model->stringBoolean($model->sndesplegar_comentario)
                ],
                [
                    'attribute' => 'i1_cdtipo_eval',
                    'value' => $model->getOption($model->i1_cdtipo_eval)
                ],
                [
                    'attribute' => 'i2_cdtipo_eval',
                    'value' => $model->getOption($model->i2_cdtipo_eval)
                ],
                [
                    'attribute' => 'i3_cdtipo_eval',
                    'value' => $model->getOption($model->i3_cdtipo_eval)
                ],
                [
                    'attribute' => 'i4_cdtipo_eval',
                    'value' => $model->getOption($model->i4_cdtipo_eval)
                ],
                [
                    'attribute' => 'i5_cdtipo_eval',
                    'value' => $model->getOption($model->i5_cdtipo_eval)
                ],
                [
                    'attribute' => 'i6_cdtipo_eval',
                    'value' => $model->getOption($model->i6_cdtipo_eval)
                ],
                [
                    'attribute' => 'i7_cdtipo_eval',
                    'value' => $model->getOption($model->i7_cdtipo_eval)
                ],
                [
                    'attribute' => 'i8_cdtipo_eval',
                    'value' => $model->getOption($model->i8_cdtipo_eval)
                ],
                [
                    'attribute' => 'i9_cdtipo_eval',
                    'value' => $model->getOption($model->i9_cdtipo_eval)
                ],
                [
                    'attribute' => 'i10_cdtipo_eval',
                    'value' => $model->getOption($model->i10_cdtipo_eval)
                ],
                'i1_nmfactor',
                'i2_nmfactor',
                'i3_nmfactor',
                'i4_nmfactor',
                'i5_nmfactor',
                'i6_nmfactor',
                'i7_nmfactor',
                'i8_nmfactor',
                'i9_nmfactor',
                'i10_nmfactor',
                'is_pits',
                'sdescripcion'
            ],
        ])
        ?>

</div>
