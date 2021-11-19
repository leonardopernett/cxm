<?php

use yii\helpers\Html;
use yii\grid\GridView;
//Agregar-----------------------------------------------------------------------
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BloquedetallesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Bloquedetalles');

$this->registerJs(
   "$(function(){
       $('#modal-bloquesDetalles').on('hidden.bs.modal', function (e) {
           location.reload();
        });    
})"
);
?>
<style type="text/css">
.masthead {
 height: 25vh;
 min-height: 100px;
 background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
 background-size: cover;
 background-position: center;
 background-repeat: no-repeat;
 /*background: #fff;*/
 border-radius: 5px;
 box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
}
</style>
<header class="masthead">
 <div class="container h-100">
   <div class="row h-100 align-items-center">
     <div class="col-12 text-center">
     </div>
   </div>
 </div>
</header>
<br>
<br>
<?php if ($isAjax) : ?>
    <?php
    Modal::begin([
        'header' => Yii::t('app', 'Bloques Detalles'),
        'id' => 'modal-bloquesDetalles',
        'size' => Modal::SIZE_LARGE,
        'clientOptions' => [
            'show' => true,
        ],
    ]);
    ?>
    <div class="bloquedetalles-index">
        <?php Pjax::begin(['id' => 'bloquesDetalles-pj', 'timeout' => false,
            'enablePushState' => false]);
        ?>
        <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
            echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
        ?>
        <p>
            <?= Html::a(Yii::t('app', 'Create Bloquedetalles'),
                    ['create', 'bloque_id' => $searchModel->bloque_id],
                    ['class' => 'btn btn-success'])
            ?>
        </p>   

        <?= GridView::widget([
            'id' => 'grid-bloquesDetalles',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],
                'id',
                'name',
                'nmorden',     
                [
                    'attribute' => 'bloqueName',                    
                    'value' => 'bloque.name'
                ],
                [
                    'attribute' => 'seccionName',                    
                    'value' => 'bloque.seccion.name'
                ],
                [
                    'attribute' => 'formularioName',                    
                    'value' => 'bloque.seccion.formulario.name'
                ],                
                //'calificacion_id',
                //'tipificacion_id',                       
                ['class' => 'yii\grid\ActionColumn',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                                            $url,
                                            [
                                        'title' => Yii::t('yii', 'view'),
                                        'data-pjax' => 'w0',
                            ]);
                        },
                                'update' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                                            $url,
                                            [
                                        'title' => Yii::t('yii', 'update'),
                                        'data-pjax' => 'w0',
                            ]);
                        },
                                'delete' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                            Url::to(['delete', 'id'=>$model->id, 'bloque_id'=>$model->bloque_id]),
                                            [
                                        'title' => Yii::t('yii', 'delete'),
                                        'data-pjax' => 'w0',
                                        'onclick' => "
                                            if (confirm('" . Yii::t('app',
                                                'Are you sure you want to delete this item?') . "')) {                                                            
                                                return true;
                                            }else{
                                                return false;
                                            }",
                            ]);
                        }
                            ]
                        ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
    <?php Modal::end(); ?>
<?php else: ?>
    <?php
    $this->title = Yii::t('app', 'Bloques');
    $this->params['breadcrumbs'][] = $this->title;
    ?>
    <div class="bloques-index">

           <?= Html::encode($this->title) ?>
       
        <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
            echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
        ?>
        <?php if($filterBloque): ?>
            <p>
                <?= Html::a(Yii::t('app', 'Create Bloquedetalles'),
                        ['create', 'bloque_id' => $searchModel->bloque_id],
                        ['class' => 'btn btn-success'])
                ?>
            </p>
            <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],
                'id',                
                'name',
                'nmorden',     
                [
                    'attribute' => 'bloqueName',                    
                    'value' => 'bloque.name'
                ],
                [
                    'attribute' => 'seccionName',                    
                    'value' => 'bloque.seccion.name'
                ],
                [
                    'attribute' => 'formularioName',                    
                    'value' => 'bloque.seccion.formulario.name'
                ],
                //'calificacion_id',
                //'tipificacion_id',                        
                ['class' => 'yii\grid\ActionColumn',
                    'buttons' => [
                        'view' => function ($url, $model) {                                                        
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                                        ['view', 
                                            'bloque_id'=>$model->bloque_id, 
                                            'id'=>$model->id]);
                        },
                        'update' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                                            ['update', 
                                            'bloque_id'=>$model->bloque_id, 
                                            'id'=>$model->id]);
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                            ['delete', 
                                            'bloque_id'=>$model->bloque_id, 
                                            'id'=>$model->id], 
                                            ['data-method' => 'post',
                                            'data-confirm' => Yii::t('app',
                                        'Are you sure you want to delete this item?')]);
                        }
                    ]
                ],
            ],
            ]); ?>
        <?php else: ?>
            <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],
                'id',
                //'bloque_id',
                'name',
                [
                    'attribute' => 'bloqueName',   
                    'filter' => true,
                    'enableSorting' => true,
                    'value' => 'bloque.name'
                ],
                [
                    'attribute' => 'seccionName',                    
                    'value' => 'bloque.seccion.name'
                ],
                [
                    'attribute' => 'formularioName',                    
                    'value' => 'bloque.seccion.formulario.name'
                ],
                //'calificacion_id',
                //'tipificacion_id',
                // 'nmorden',            
                ['class' => 'yii\grid\ActionColumn'],
            ],
            ]); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="bloquedetalles-index">    
