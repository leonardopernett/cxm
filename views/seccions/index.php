<?php

use yii\helpers\Html;
use yii\grid\GridView;
//Agregar-----------------------------------------------------------------------
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SeccionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerJs(
   "$(function(){
       $('#modal-seccions').on('hidden.bs.modal', function (e) {
           location.reload();
        });    
})"
);
?>

<?php if ($isAjax) : ?>
    <?php
    Modal::begin([
        'header' => 'Secciones',
        'id' => 'modal-seccions',
        'size' => Modal::SIZE_LARGE,
        'clientOptions' => [
            'show' => true,
        ],
    ]);
    ?>

    <div class="seccions-index">

        <?php
        Pjax::begin(['id' => 'seccions-pj', 'timeout' => false,
            'enablePushState' => false]);
        ?>                

        <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
            echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
        ?>

        <p>
            <?=
            Html::a(Yii::t('app', 'Create Seccions'),
                    ['create', 'formulario_id' => $searchModel->formulario_id],
                    ['class' => 'btn btn-success'])
            ?>
        </p> 

        <?=
        GridView::widget([
            'id' => 'grid-seccions',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,            
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],
                'id',
                'name',
                'nmorden',
                [
                    'attribute' => 'bloques',
                    'format' => 'raw',
                    'value' => function ($data) {
                                return Html::a('Ver Bloques',
                                                ['bloques/index', 'seccion_id' => $data->id],
                                                [
                                            'title' => Yii::t('app', 'Seccions'),
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
                                        'title' => Yii::t('yii',
                                                'update'),
                                        'data-pjax' => 'w0',
                            ]);
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                    Url::to(['delete', 'id'=>$model->id, 'formulario_id'=>$model->formulario_id]),
                                            [
                                        'title' => Yii::t('yii',
                                                'delete'),
//                                        'data-confirm' => Yii::t('app',
//                                                'Are you sure you want to delete this item?'),
                                        'data-pjax' => 'w0',
//                                        'onclick' => "if(!confirm('" . Yii::t('app',
//                                                'Are you sure you want to delete this item?') . "')){"
//                                        . " return false;"
//                                        . "}"
//                                        . "",
                                        'onclick' => "
                            if (confirm('" . Yii::t('app',
                                                'Are you sure you want to delete this item?') . "')) {                                                            
                                return true;
                            }else{
                                return false;
                            }",
                                            //'data-method' => 'post'
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
$this->title = Yii::t('app', 'Seccions');
$this->params['breadcrumbs'][] = $this->title;
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
       <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
       <p class="lead">A great starter layout for a landing page</p> -->
     </div>
   </div>
 </div>
</header>
<br>
<br>
    <div class="seccions-index">
<!--        <div class="page-header">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>-->
        
        <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
            echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
        ?>

        <p>
            <?=
            Html::a(Yii::t('app', 'Create Seccions'),
                    ['create'], ['class' => 'btn btn-success'])
            ?>
        </p>                                                                     

        <?=
        GridView::widget([
            'id' => 'grid-seccions',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'options' => ['push' => true ],
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],
                'id',
                'name',
                'nmorden',
                [
                    'attribute' => 'formularioName',
                    'filter' => true,
                    'enableSorting' => true,
                    'value' => 'formulario.name'
                ],
                [
                    'attribute' => 'bloques',
                    'format' => 'raw',
                    'value' => function ($data) {
                        return Html::a('Ver Bloques',
                                        'javascript:void(0)',
                                        [
                                    'title' => Yii::t('app','Bloques'),
                                    //'data-pjax' => '0',
                                    'onclick' => "                                    
                                    $.ajax({
                                    type     :'POST',
                                    cache    : false,
                                    url  : '" . Url::to(['bloques/index',
                                        'seccion_id' => $data->id]) . "',
                                    success  : function(response) {
                                        $('#ajax_result').html(response);
                                    }
                                   });
                                   return false;",
                        ]);
                    }
                ],
                //'tiposeccion_id',
                //'nmorden',                                        
                // 'sndesplegar_comentario',
                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]);
            ?>                        
    </div>     
    <?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
    ?>
<?php endif; ?>