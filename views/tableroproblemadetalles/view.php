<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Tableroproblemadetalles */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app',
            'Tableroproblemadetalles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tableroproblemadetalles-view">

    <h3><?= Html::encode($this->title) ?></h3>    
    <hr>
    <p>
        <?=
        Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id],
                ['class' => 'btn btn-primary'])
        ?>
        <?=
        Html::a(Yii::t('app', 'Delete'),
                [
            'delete', 'id' => $model->id,
            'tableroproblema_id' => $model->tableroproblema_id],
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
                ['index', 'tableroproblema_id' => $model->tableroproblema_id],
                ['class' => 'btn btn-default'])
        ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            //'tableroproblema_id',
            [
                'attribute' => 'tableroproblema_id',
                'value' => $model->tableroproblema->name
            ],
            //'tableroenfoque_id',
            [
                'attribute' => 'tableroenfoque_id',
                'value' => $model->tableroenfoque->name
            ],
        ],
    ])
    ?>

</div>
