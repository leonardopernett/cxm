<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TableroproblemasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tableroproblemas');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tableroproblemas-index">
    
<?= Html::encode($this->title) ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Tableroproblemas'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            [
                'attribute' => 'Detalle',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a(Yii::t('app','Ver detalle problemas'), 
                            'javascript:void(0)',
                        [
                            'title' => Yii::t('app', 'Ver detalle problemas'),
                            'onclick' => "                                    
                            $.ajax({
                            type     :'POST',
                            cache    : false,
                            url  : '" . Url::to(['tableroproblemadetalles/index'
                                , 'tableroproblema_id' => $data->id]) . "',
                            success  : function(response) {
                                $('#ajax_result').html(response);
                            }
                           });
                           return false;",
                    ]);
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php
        echo Html::tag('div', '', ['id' => 'ajax_result']);
    ?>
</div>
