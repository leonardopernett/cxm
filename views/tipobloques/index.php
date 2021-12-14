<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TipobloquesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tipobloques');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipobloques-index">
    
<?= Html::encode($this->title) ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Tipobloques'), ['create'], ['class' => 'btn btn-success']) ?>
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
