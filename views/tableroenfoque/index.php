<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TblTableroenfoquesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tbl Tableroenfoques');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-tableroenfoques-index">
    
<?= Html::encode($this->title) ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Tbl Tableroenfoques'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
