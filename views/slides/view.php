<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Slides */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Slides'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="slides-view">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])
        ?>
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'titulo',
            'descripcion',
            [
                'attribute' => 'imagen',
                'format' => 'html',
                'value' => Html::img(Url::to('@web/images/uploads/') . $model->imagen, ['width' => '300'])
            ],
            [
                'attribute' => 'activo',
                'value' => ($model->activo == 0) ? "NO" : "SI"
            ],
            'created',
            'created_by',
            'modified',
            'modified_by',
        ],
    ])
    ?>

</div>
