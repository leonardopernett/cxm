<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Tipificaciondetalles */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tipificaciondetalles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipificaciondetalles-view">   
        <h3><?= Html::encode($this->title) ?></h3>    

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a(Yii::t('app', 'Delete'),
                ['delete', 'id' => $model->id,
            'tipificacion_id' => $model->tipificacion_id],
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
                ['index', 'tipificacion_id' => $model->tipificacion_id],
                ['class' => 'btn btn-default'])
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',            
            [
                'attribute' => 'tipificacion_id',
                'value' => $model->tipificacion->name
            ],            
            [
                'attribute' => 'subtipificacion_id',
                'value' => (!empty($model->subtipificacion_id))? $model->subtipificacion->name: null
            ],
            'nmorden',            
            [
                'attribute' => 'snen_uso',
                'value' => $model->getStringUso()
            ],
        ],
    ]) ?>

</div>
