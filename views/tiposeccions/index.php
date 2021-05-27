<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TiposeccionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tiposeccions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tiposeccions-index">
    
<!--<div class="page-header">
    <h3><?= Html::encode($this->title) ?></h3>
</div>-->

    <p>
        <?= Html::a(Yii::t('app', 'Create Tiposeccions'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'nmumbral',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
