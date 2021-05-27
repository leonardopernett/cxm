<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Tipofeedbacks */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tipofeedbacks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipofeedbacks-view">

    <h3><?= Html::encode($this->title) ?></h3>
    <hr>

    <p>
        <?= Html::a(Yii::t('app', 'Update'),
                ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])
        ?>
        <?=
        Html::a(Yii::t('app', 'Delete'),
                ['delete', 'id' => $model->id,
            'categoriafeedback_id' => $model->categoriafeedback_id],
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
                ['index', 'categoriafeedback_id' => $model->categoriafeedback_id],
                ['class' => 'btn btn-default'])
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',            
            [
                'attribute' => 'categoriafeedback_id',
                'value' => $model->categoriafeedback->name
            ],
            'name',            
            [
                'attribute' => 'snaccion_correctiva',
                'value' => $model->getStringBoolean($model->snaccion_correctiva)
            ],            
            [
                'attribute' => 'sncausa_raiz',
                'value' => $model->getStringBoolean($model->sncausa_raiz)
            ],            
            [
                'attribute' => 'sncompromiso',
                'value' => $model->getStringBoolean($model->sncompromiso)
            ],            
            [
                'attribute' => 'cdtipo_automatico',
                'value' => $model->getStringBoolean($model->cdtipo_automatico)
            ],
            'dsmensaje_auto',
        ],
    ]) ?>

</div>
