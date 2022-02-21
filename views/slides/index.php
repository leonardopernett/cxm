<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SlidesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Slides');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="slides-index">

   <?= Html::encode($this->title) ?>
    <p>
        <?= Html::a(Yii::t('app', 'Create Slides'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'titulo',
            'descripcion',
            [
                'attribute' => 'imagen',
                'format' => 'html',
                'value' => function($data) {
                    return Html::img(Url::to("@web/images/uploads/") . $data->imagen, ['width'=>'100']);
                },
            ],            
            [
                'attribute' => 'activo',
                'value' => function($data) {
                    return ($data->activo == 0) ? "NO" : "SI";
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

</div>
