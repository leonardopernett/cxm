<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NoticiasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Noticias');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="noticias-index">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <p>
        <?= Html::a(Yii::t('app', 'Create Noticias'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'id',
            'titulo',
            //'descripcion:ntext',
            [
                'attribute' => 'activa',
                'value' => function($data) {
                    return ($data->activa == 0) ? "NO" : "SI";
                }
            ],
            // 'created',
            // 'created_by',
            // 'modified',
            // 'modified_by',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

</div>
