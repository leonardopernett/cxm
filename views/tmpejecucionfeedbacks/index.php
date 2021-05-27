<?php

use yii\helpers\Html;
use yii\grid\GridView;
//Agregar-----------------------------------------------------------------------
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TmpejecucionfeedbacksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */



//EVITAR DOBLE CARGA EN EL AJAX
$this->registerJs(
        "$(function(){
       $('#modal-tmpejecucionfeedbacks').on('hidden.bs.modal', function (e) {
            var guardarFormulario = $('#guardarFormulario');
            guardarFormulario.attr('action', '" . Url::to(['formularios/guardarformulario']) . "');
            guardarFormulario.removeAttr('onSubmit');
            guardarFormulario.submit();
            guardarFormulario.attr('action', '" . Url::to(['formularios/guardaryenviarformulario']) . "');
            //guardarFormulario.attr('onSubmit', 'return validarFormulario();');
           /*location.reload();*/
        });    
})"
);
?>

<?php
Modal::begin([
    'header' => Yii::t('app', 'Create feedback'),
    'id' => 'modal-tmpejecucionfeedbacks',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>

<div class="tmpejecucionfeedbacks-index">
    <?php
    Pjax::begin(['id' => 'tmpejecucionfeedbacks-pj', 'timeout' => false,
        'enablePushState' => false]);
    ?>

    <p>
        <?=
        Html::a(Yii::t('app', 'Create feedback'), 'javascript:void(0)', ['class' => 'btn btn-success crear'])
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
                'attribute' => 'tipofeedback_id',
                'filter' => false,
                'enableSorting' => false,
                'value' => 'tipofeedback.name'
            ],
            //'usua_id',
            //'created',
            // 'usua_id_lider',
            // 'evaluado_id',
            // 'snavisar',
            // 'snaviso_revisado',
            // 'dsaccion_correctiva:ntext',
            // 'feaccion_correctiva',
            // 'nmescalamiento',
            // 'feescalamiento',
            // 'dscausa_raiz:ntext',
            // 'dscompromiso:ntext',
            'dscomentario:ntext',
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{verfeedback}{update}{delete}',
                'buttons' => [
                    'verfeedback' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0)', [
                                    'title' => 'hola',
                                    'data-pjax' => 'w0',
                                    'onclick' => "ver($model->id);",
                        ]);
                    },
                            'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                    'title' => Yii::t('yii', 'view'),
                                    'data-pjax' => 'w0',
                        ]);
                    },
                            'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',  'javascript:void(0)', [
                                    'title' => Yii::t('yii', 'update'),
                                    'data-pjax' => 'w0',
                                    'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['update'
                                        ,'id' => $model->id
                                       ]) . "',
                                success  : function(response) {
                                    $('#ajax_div_feedbacks').html(response);
                                }
                               });
                               return false;",
                        ]);
                    },
                            'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', 'javascript:void(0)', [
                                    'title' => Yii::t('yii', 'delete'),
                                    'data-pjax' => 'w0',
                                    'onclick' => "
                                            if (confirm('"
                                    . Yii::t('app', 'Are you sure '
                                            . 'you want to delete '
                                            . 'this item?') . "')) {  
                                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['delete'
                                        , 'id' => $model->id,
                                            'tmp_formulario_id' => $model->tmpejecucionformulario_id
                                            , 'usua_id_lider' => $model->usua_id_lider
                                            , 'evaluado_id' => $model->evaluado_id]) . "',
                                success  : function(response) {
                                    $('#ajax_div_feedbacks').html(response);
                                }
                               });
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
        <script type="text/javascript">


            $(".crear").click(function () {
                ruta = '<?php
        echo Url::to(['create', 'tmp_formulario_id' => $searchModel->tmpejecucionformulario_id
            , 'usua_id_lider' => $searchModel->usua_id_lider
            , 'evaluado_id' => $searchModel->evaluado_id
            , 'basessatisfaccion_id' => $searchModel->basessatisfaccion_id]);
        ?>';
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: ruta,
                    success: function (response) {
                        $('#ajax_div_feedbacks').html(response);
                    }
                });
            });

            function ver(id) {
                ruta = '<?php echo Url::to(['view']); ?>?id=' + id;
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: ruta,
                    success: function (response) {
                        $('#ajax_div_feedbacks').html(response);
                    }
                });
                return false;
            }
        </script>
        <?php Modal::end(); ?> 
