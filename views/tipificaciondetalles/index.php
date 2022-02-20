<?php

use yii\helpers\Html;
use yii\grid\GridView;
//Agregar-----------------------------------------------------------------------
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TipificaciondetallesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->registerJs(
   "$(function(){
       $('#modal-Tipificaciondetalles').on('hidden.bs.modal', function (e) {
           location.reload();
        });    
})"
);
?>

<?php
Modal::begin([
    'header' => Yii::t('app', 'Detalle Tipificacion'),
    'id' => 'modal-Tipificaciondetalles',
    'size' => Modal::SIZE_LARGE,    
    'clientOptions' => [        
        'show' => true,        
    ],
]);
?>
<div class="tipificaciondetalles-index">
    <?php
    Pjax::begin(['id' => 'Tipificaciondetalles-pj', 'timeout' => false,
        'enablePushState' => false]);
    ?>
    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
    }
    ?>
    <p>
        <?= Html::a(Yii::t('app', 'Create Tipificaciondetalles'), 
        ['create', 'tipificacion_id' => $searchModel->tipificacion_id], 
        ['class' => 'btn btn-success']) ?>
    </p>
    
    <?= GridView::widget([
        'id'=>'grid-Tipificaciondetalles',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',                        
            [
                'attribute' => 'subTipificacionName',                
                'value' => 'subtipificacion.name'
            ],            
            [
                'attribute' => 'tipificacion_id',
                'filter' => false,
                'enableSorting' => false,
                'value' => 'tipificacion.name'
            ],
            //'nmorden',            
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
                                    'tipificacion_id'=>$model->tipificacion_id]),
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

    
