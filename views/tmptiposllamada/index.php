<?php

use yii\helpers\Html;
use yii\grid\GridView;
//Agregar-----------------------------------------------------------------------
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TmptiposllamadaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//EVITAR DOBLE CARGA EN EL AJAX
$this->registerJs(
   "$(function(){
       $('#modal-tmptiposllamada').on('hidden.bs.modal', function (e) {
            var guardarFormulario = $('#guardarFormulario');
            guardarFormulario.attr('action', '" . Url::to(['formularios/guardarformulario']) . "');
            guardarFormulario.removeAttr('onSubmit');
            guardarFormulario.submit();
            guardarFormulario.attr('action', '" . Url::to(['formularios/guardaryenviarformulario']) . "');
            guardarFormulario.attr('onSubmit', 'return validarFormulario();');
           /*location.reload();*/
        });    
})"
);
?>

<?php
Modal::begin([
    'header' => Yii::t('app', 'Create Tiposllamadas'),
    'id' => 'modal-tmptiposllamada',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>
<div class="tmptiposllamada-index">
    <?php
    Pjax::begin(['id' => 'tmptiposllamada-pj', 'timeout' => false,
        'enablePushState' => false]);
    ?>
    
    <p>
        <?=
        Html::a(Yii::t('app', 'Create Tiposllamadas'), ['create', 'tmp_formulario_id' => $searchModel->tmpejecucionformulario_id], ['class' => 'btn btn-success'])
        ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'tiposllamadasdetalle_id',                
                'value' => 'tiposllamadasdetalle.name',
                'filter' => false,
                'enableSorting' => false,
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
                                        'tmp_formulario_id'=>$model->tmpejecucionformulario_id]),
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
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
<?php Modal::end(); ?> 