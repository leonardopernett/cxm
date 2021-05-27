<?php

use yii\helpers\Html;
use yii\grid\GridView;
//Agregar-----------------------------------------------------------------------
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TmptableroexperienciasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */



//EVITAR DOBLE CARGA EN EL AJAX
$this->registerJs(
        "$(function(){
       $('#modal-tmptableroexperiencias').on('hidden.bs.modal', function (e) {
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
    'header' => Yii::t('app', 'Create Tableroproblemadetalles'),
    'id' => 'modal-tmptableroexperiencias',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>
<div class="tmptableroexperiencias-index">


    <p>
        <?=
        Html::a(Yii::t('app', 'Create Tmptableroexperiencias'), 'javascript:void(0)', ['class' => 'btn btn-success crear'])
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
                'attribute' => 'tableroenfoque_id',
                'value' => 'tableroenfoque.name',
                'filter' => false,
                'enableSorting' => false,
            ],
            [
                'attribute' => 'tableroproblemadetalle_id',
                'value' => 'tableroproblemadetalle.name',
                'filter' => false,
                'enableSorting' => false,
            ],
            'detalle:ntext',
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{verProblema}{update}{delete}',
                'buttons' => [
                    'verProblema' => function ($url, $model) {
                        $ruta = Yii::$app->request->get('arbol_id');
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0)', [
                                    'title' => 'hola',
                                    'data-pjax' => 'w0',
                                    'onclick' => "ver($model->id,$ruta);",
                        ]);
                    },
                    'view' => function ($url, $model) {
                        $ruta = Url::to(['view'
                                        , 'id' => $model->id, 'arbol_id' => Yii::$app->request->get('arbol_id')]);
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 'javascript:void(0)', [
                                    'title' => Yii::t('yii', 'view'),
                                    'data-pjax' => 'w0',
                                    'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" .$ruta. "',
                                success  : function(response) {
                                    $('#ajax_div_problemas').html(response);
                                }
                               });
                               return false;",
                        ]);
                    },
                            'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', 'javascript:void(0)', [
                                    'title' => Yii::t('yii', 'update'),
                                    'data-pjax' => 'w0',
                                    'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['update'
                                        , 'id' => $model->id, 'arbol_id' => Yii::$app->request->get('arbol_id')]) . "',
                                success  : function(response) {
                                    $('#ajax_div_problemas').html(response);
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
                                        'tmp_formulario_id' => $model->tmpejecucionformulario_id,
                                        'arbol_id' => Yii::$app->request->get('arbol_id')]) . "',
                                success  : function(response) {
                                    $('#ajax_div_problemas').html(response);
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

        </div>
        <script type="text/javascript">


            $(".crear").click(function () {
                ruta = '<?php echo Url::to(['tmptableroexperiencias/create', 'tmp_formulario_id' => $searchModel->tmpejecucionformulario_id, 'arbol_id' => $arbol_id]); ?>';
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: ruta,
                    success: function (response) {
                        $('#ajax_div_problemas').html(response);
                    }
                });
            });
            
            function ver(id,arbol_id){
                ruta = '<?php echo Url::to(['tmptableroexperiencias/view']); ?>?id='+id+'&arbol_id='+arbol_id;
                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : ruta,
                                success  : function(response) {
                                    $('#ajax_div_problemas').html(response);
                                }
                               });
                               return false;
            }
        </script>
        <?php Modal::end(); ?> 