<?php

use yii\helpers\Html;
use yii\grid\GridView;
//Agregar-----------------------------------------------------------------------
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EquiposEvaluadosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerJs(
   "$(function(){
       $('#modal-equipos-evaluados').on('hidden.bs.modal', function (e) {
           location.reload();
        });    
})"
);
?>

<?php
Modal::begin([
    'header' => Yii::t('app', 'Equipos'),
    'id' => 'modal-equipos-evaluados',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>
<div class="equipos-evaluados-index">
    <?php
    Pjax::begin(['id' => 'equipos-evaluados-pj', 'timeout' => false,
        'enablePushState' => false]);
    ?>    
    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
    }
    ?>
    <?php echo $this->render('_formEquipos', ['model' => $model]); ?>

    <?=
    GridView::widget([
        'id' => 'grid-equipos-evaluados',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,        
        'columns' => [            
            'id',            
            [
                'attribute' => 'equipoName',
                'filter' => true,
                'enableSorting' => true,
                'value' => 'equipo.name'
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function ($url, $model) {
                        if ($url == "asda") {
                            #code...
                        }
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                        Url::to(['deleteequipo', 'id'=>$model->id, 'evaluadores_id'=>$model->evaluado_id]),
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
                        ]],
                ],
            ]);
            ?>

            <?php Pjax::end(); ?>
        </div>
        <?php Modal::end(); ?>

