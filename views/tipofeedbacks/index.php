<?php

use yii\helpers\Html;
use yii\grid\GridView;
//Agregar-----------------------------------------------------------------------
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TipofeedbacksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->registerJs(
   "$(function(){
       $('#modal-Tipofeedbacks').on('hidden.bs.modal', function (e) {
           location.reload();
        });    
})"
);
?>

<?php
Modal::begin([
    'header' => Yii::t('app', 'Lista Tipos Feedback'),
    'id' => 'modal-Tipofeedbacks',
    'size' => Modal::SIZE_LARGE,    
    'clientOptions' => [        
        'show' => true,        
    ],
]);
?>
<div class="tipofeedbacks-index">
    <?php
    Pjax::begin(['id' => 'Tipofeedbacks-pj', 'timeout' => false,
        'enablePushState' => false]);
    ?>
    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
    }
    ?>
    <p>
        <?= Html::a(Yii::t('app', 'Create Tipofeedbacks'), 
        ['create', 'categoriafeedback_id' => $searchModel->categoriafeedback_id], 
        ['class' => 'btn btn-success']) ?>
    </p>
    
    <?= GridView::widget([
        'id'=>'grid-Tipofeedbacks',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',            
            'name',            
            [
                'attribute' => 'categoriafeedback_id',
                'filter' => false,
                'enableSorting' => false,
                'value' => 'categoriafeedback.name'
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
                            if ($url == "asda") {
                                #code...
                            }
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                    Url::to(['delete', 
                                        'id'=>$model->id, 
                                        'categoriafeedback_id'=>$model->categoriafeedback_id]),
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

    


