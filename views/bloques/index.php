<?php

use yii\helpers\Html;
use yii\grid\GridView;
//Agregar-----------------------------------------------------------------------
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BloquesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->registerJs(
   "$(function(){
       $('#modal-bloques').on('hidden.bs.modal', function (e) {
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
        'header' => Yii::t('app', 'Bloques'),
        'id' => 'modal-bloques',
        'size' => Modal::SIZE_LARGE,
        'clientOptions' => [
            'show' => true,
        ],
    ]);
    ?>
    <div class="bloques-index">        
        <?php Pjax::begin(['id' => 'bloques-pj', 'timeout' => false,
            'enablePushState' => false]);
        ?>    
        <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
            echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
        ?>
        
        <p>
            <?= Html::a(Yii::t('app', 'Create Bloques'),
                    ['create', 'seccion_id' => $searchModel->seccion_id],
                    ['class' => 'btn btn-success'])
            ?>
        </p>
        <?=
        GridView::widget([
            'id' => 'grid-bloques',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'id',
                'name',
                'nmorden',
                [
                    'attribute' => 'seccion_id',
                    'filter' => false,
                    'enableSorting' => false,
                    'value' => 'seccion.name'
                ],
                [
                    'attribute' => 'formularioName',
                    'value' => 'seccion.formulario.name'
                ],
                [
                    'attribute' => 'detalles',
                    'format' => 'raw',
                    'value' => function ($data) {
                                return Html::a(Yii::t('app', 'Ver Detalle'),
                                                ['bloquedetalles/index', 'bloque_id' => $data->id],
                                                [
                                            'title' => Yii::t('app', 'Bloque detalle'),
                                            'data-pjax' => '0',
                                ]);
                            }
                ],
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
                                            Url::to(['delete', 'id'=>$model->id, 'seccion_id'=>$model->seccion_id]),
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
                ]);
                ?>
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
                <?php if($filterSeccion): ?>
                <p>
                    <?= Html::a(Yii::t('app', 'Create Bloques'),
                            ['create', 'seccion_id' => $searchModel->seccion_id],
                            ['class' => 'btn btn-success'])
                    ?>
                </p>
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        'id',
                        'name',
                        'nmorden',
                        [
                            'attribute' => 'seccion_id',
                            'filter' => false,
                            'enableSorting' => false,
                            'value' => 'seccion.name'
                        ],
                        [
                            'attribute' => 'formularioName',
                            'value' => 'seccion.formulario.name'
                        ], 
                        [
                            'attribute' => 'detalles',
                            'format' => 'raw',
                            'value' => function ($data) {
                                return Html::a(Yii::t('app', 'Ver Detalle'),
                                                'javascript:void(0)',
                                                [
                                            'title' => Yii::t('app',
                                                    'Bloques'),
                                            'onclick' => "                                    
                                            $.ajax({
                                            type     :'POST',
                                            cache    : false,
                                            url  : '" . Url::to(['bloquedetalles/index', 
                                                'bloque_id' => $data->id]) . "',
                                            success  : function(response) {
                                                $('#ajax_result-detalle').html(response);
                                            }
                                           });
                                           return false;",
                                ]);
                            }
                        ],
                        ['class' => 'yii\grid\ActionColumn',
                            'buttons' => [
                                'view' => function ($url, $model) {                                                        
                                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                                                ['view', 
                                                    'seccion_id'=>$model->seccion_id, 
                                                    'id'=>$model->id]);
                                },
                                'update' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                                                    ['update', 
                                                    'seccion_id'=>$model->seccion_id, 
                                                    'id'=>$model->id]);
                                },
                                'delete' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                                    ['delete', 
                                                    'seccion_id'=>$model->seccion_id, 
                                                    'id'=>$model->id], 
                                                    ['data-method' => 'post',
                                                    'data-confirm' => Yii::t('app',
                                                'Are you sure you want to delete this item?')]);
                                }
                            ]
                        ],
                    ],
                ]);
                ?>
                <?php
                    echo Html::tag('div', '', ['id' => 'ajax_result-detalle']);
                ?>
                <?php else: ?>
                <p>
                    <?= Html::a(Yii::t('app', 'Create Bloques'),
                            ['create'], ['class' => 'btn btn-success'])
                    ?>
                </p>
                
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        'id',
                        'name',
                        [
                            'attribute' => 'seccionName',
                            'filter' => true,
                            'enableSorting' => true,
                            'value' => 'seccion.name'
                        ],
                        [
                            'attribute' => 'formularioName',
                            'value' => 'seccion.formulario.name'
                        ],
                        [
                            'attribute' => 'detalles',
                            'format' => 'raw',
                            'value' => function ($data) {
                                return Html::a(Yii::t('app', 'Ver Detalle'),
                                                'javascript:void(0)',
                                                [
                                            'title' => Yii::t('app',
                                                    'Bloques'),
                                            'onclick' => "                                    
                                            $.ajax({
                                            type     :'POST',
                                            cache    : false,
                                            url  : '" . Url::to(['bloquedetalles/index', 
                                                'bloque_id' => $data->id]) . "',
                                            success  : function(response) {
                                                $('#ajax_result-detalle').html(response);
                                            }
                                           });
                                           return false;",
                                ]);
                            }
                        ],
                        ['class' => 'yii\grid\ActionColumn'],
                    ],
                ]);
                ?>                
                <?php endif; ?>
            </div>
            <?php
                echo Html::tag('div', '', ['id' => 'ajax_result-detalle']);
            ?>
        <?php endif; ?>



