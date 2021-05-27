<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Bloquedetalles */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bloquedetalles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bloquedetalles-view">
    <?php if ($isAjax): ?>
        <h3><?= Html::encode($this->title) ?></h3>
    <?php else: ?>
        <div class="page-header">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>
    <?php endif; ?>

    <p>        
        <?php if ($isAjax): ?>
            <?=
            Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id],
                    ['class' => 'btn btn-primary'])
            ?>
            <?=
            Html::a(Yii::t('app', 'Delete'),
                    ['delete', 'id' => $model->id,
                'bloque_id' => $model->bloque_id],
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
                    ['index', 'bloque_id' => $model->bloque_id],
                    ['class' => 'btn btn-default'])
            ?>
        <?php else: ?>       
            <?php if ($filterBloque): ?>  
                <?=
                Html::a(Yii::t('app', 'Update'),
                        ['update',
                    'id' => $model->id, 'bloque_id' => $model->bloque_id],
                        ['class' => 'btn btn-primary'])
                ?>
                <?=
                Html::a(Yii::t('app', 'Delete'),
                        ['delete',
                    'id' => $model->id, 'bloque_id' => $model->bloque_id],
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
                        ['index', 'bloque_id' => $model->bloque_id],
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
            //'bloque_id',
            'name',
            [
                'attribute' => 'bloque_id',
                'value' => $model->bloque->name
            ],
            [
                'attribute' => 'seccionName',
                'value' => $model->bloque->seccion->name
            ],
            [
                'attribute' => 'formularioName',
                'value' => $model->bloque->seccion->formulario->name
            ],
            //'calificacion_id',
            [
                'attribute' => 'calificacion_id',
                'value' => (!empty($model->calificacion_id)) ? $model->calificacion->name
                            : null
            ],
            //'tipificacion_id',
            [
                'attribute' => 'tipificacion_id',
                'value' => (!empty($model->tipificacion_id)) ? $model->tipificacion->name
                            : null
            ],
            'nmorden',
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
            'c_pits',
            'descripcion',
        ],
    ])
    ?>

</div>
