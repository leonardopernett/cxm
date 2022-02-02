<?php

use yii\helpers\Html;
use yii\grid\GridView;
//Agregar-----------------------------------------------------------------------
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TiposllamadasdetallesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerJs(
   "$(function(){
       $('#modal-Tiposllamadasdetalles').on('hidden.bs.modal', function (e) {
           location.reload();
        });    
})"
);
?>

<?php
Modal::begin([
    'header' => Yii::t('app', 'Detalle Tipo de llamadas'),
    'id' => 'modal-Tiposllamadasdetalles',
    'size' => Modal::SIZE_LARGE,    
    'clientOptions' => [        
        'show' => true,        
    ],
]);
?>
<div class="tiposllamadasdetalles-index">
    <?php
    Pjax::begin(['id' => 'Tiposllamadasdetalles-pj', 'timeout' => false,
        'enablePushState' => false]);
    ?>
    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
    }
    ?>
    <p>
        <?= Html::a(Yii::t('app', 'Create Tiposllamadasdetalles'), 
        ['create', 'tiposllamada_id' => $searchModel->tiposllamada_id], 
        ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'id'=>'grid-Tiposllamadasdetalles',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',            
            [
                'attribute' => 'tiposllamada_id',
                'filter' => false,
                'enableSorting' => false,
                'value' => 'tiposllamada.name'
            ],
            ['class' => 'yii\grid\ActionColumn',
                    'buttons' => [
                        'view' => function ($url) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                                            $url,
                                            [
                                        'title' => Yii::t('yii', 'view'),
                                        'data-pjax' => 'w0',
                            ]);
                        },
                        'update' => function ($url) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                                            $url,
                                            [
                                        'title' => Yii::t('yii',
                                                'update'),
                                        'data-pjax' => 'w0',
                            ]);
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                    Url::to(['delete', 
                                        'id'=>$model->id, 
                                        'tiposllamada_id'=>$model->tiposllamada_id]),
                                            [
                                        'title' => Yii::t('yii','delete'),
                                        'data-pjax' => 'w0',
                                        'onclick' => "
                                            if (confirm('" 
                                                . Yii::t('app', 'Are you sure '
                                                        . 'you want to delete '
                                                        . 'this item?') . "')) {                                                            
                                                return true;
                                            }else{
                                                return false;
                                            }",                                            
                                            ]
                                        );
                                    }
                        ]
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<?php Modal::end(); ?>
