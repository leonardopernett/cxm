<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Bloques */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bloques'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bloques-view">
    <?php if ($isAjax): ?>
        <h3><?= Html::encode($this->title) ?></h3>
    <?php else: ?>
        <div class="page-header">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>
    <?php endif; ?>

    <p>        
        <?php if ($isAjax): ?>
            <?= Html::a(Yii::t('app', 'Update'),
                    ['update', 'id' => $model->id],
                    ['class' => 'btn btn-primary']) ?>
            <?=
            Html::a(Yii::t('app', 'Delete'),
                    ['delete', 'id' => $model->id, 'seccion_id' => $model->seccion_id],
                    [
                'class' => 'btn btn-danger',
                'onclick' => "
                    if (confirm('" . Yii::t('app',
                        'Are you sure you want to delete this item?') . "')) {                                                            
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
            Html::a(Yii::t('app', 'Cancel'),
                    ['index', 'seccion_id' => $model->seccion_id],
                    ['class' => 'btn btn-default'])
            ?>
        <?php else: ?>       
            <?php if ($filterSeccion): ?>  
                <?=
                Html::a(Yii::t('app', 'Update'),
                        ['update',
                    'id' => $model->id, 'seccion_id' => $model->seccion_id],
                        ['class' => 'btn btn-primary'])
                ?>
                <?=
                Html::a(Yii::t('app', 'Delete'),
                        ['delete',
                    'id' => $model->id, 'seccion_id' => $model->seccion_id],
                        [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app',
                                'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
                <?=
                Html::a(Yii::t('app', 'Cancel'),
                        ['index', 'seccion_id' => $model->seccion_id],
                        ['class' => 'btn btn-default'])
                ?>
            <?php else: ?>    
                <?=
                Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id],
                        ['class' => 'btn btn-primary'])
                ?>
                <?=
                Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id],
                        [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app',
                                'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
            <?=
            Html::a(Yii::t('app', 'Cancel'), ['index'],
                    ['class' => 'btn btn-default'])
            ?>
        <?php endif; ?>                    
    <?php endif; ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'attribute' => 'seccion_id',
                'value' => $model->seccion->name
            ],
            [
                'attribute' => 'formularioName',
                'value' => $model->seccion->formulario->name
            ],
            [
                'attribute' => 'tipobloque_id',
                'value' => $model->tipoBloque->name
            ],
            'nmorden',
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
            'dstitulo',
            'dsdescripcion',
        ],
    ])
    ?>

</div>
