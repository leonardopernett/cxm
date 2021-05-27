<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of indexCortes
 *
 * @author ingeneo
 */
?>
<?php
Modal::begin([
    //'header' => Yii::t('app', 'Create Tbl Pregunta'),
    'id' => 'modal-corte',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>
<div class="cortes-index">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Semana</a></li>
        <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Mes</a></li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="home">
            <?php
            Pjax::begin(['id' => 'indexcorte-pj', 'timeout' => false,
                'enablePushState' => false]);
            ?> 
            <p>
                <?= Html::a(Yii::t('app', 'Create cut'), 'javascript:void(0)', ['class' => 'btn btn-success crearSemana']) ?>
            </p>
 <?= Html::input("hidden", "fecha", $rangofecha,['id'=>'fecha']); ?>
            <?=
            GridView::widget([
                'dataProvider' => $dataProviderSemana,
                'filterModel' => $searchModel,
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],
                    //'corte_id',
                    //'tipo_corte',
                    [
                        'attribute' => 'tipo_corte',
                        'filter' => false,
                        'value' => function($data) {
                            if ($data->tipo_corte == 1) {
                                return 'Semana';
                            } else {
                                return 'Mes';
                            }
                        }
                    ],
                    //'corte_descripcion',
                    [
                        'attribute' => 'corte_descripcion',
                        'filter' => false,
                        'value' => function($data) {
                            $mes = ["January" => "Enero", "February" => "Febrero", "March" => "Marzo", "April" => "Abril", "May" => "Mayo", "June" => "Junio", "July" => "Julio", "August" => "Agosto", "September" => "Septiembre", "October" => "Octubre", "November" => "Noviembre", "December" => "Diciembre"];
                            return $mes[$data->corte_descripcion];
                        }
                            ],
                            ['class' => 'yii\grid\ActionColumn',
                                'template' => '{update}{delete}',
                                'buttons' => [
                                    'view' => function ($url, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                                    'title' => Yii::t('yii', 'view'),
                                                    'data-pjax' => 'w0',
                                        ]);
                                    },
                                            'update' => function ($url, $modelCorte) {

                                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', 'javascript:void(0)', [
                                                    //'title' => Yii::t('app', 'Tbl Opcions'),
                                                    //'data-pjax' => '0',
                                                    'onclick' => "  
                                       var fecha = $('#fecha').val();
                                    $.ajax({
                                    type     :'POST',
                                    cache    : false,
                                    url  : '" . Url::to(['update', 'corte_id' => $modelCorte->corte_id]) . "&rangofecha='+fecha,
                                    success  : function(response) {
                                        $('#ajax_result').html(response);
                                    }
                                   });
                                   return false;",
                                        ]);
                                    },
                                            'delete' => function ($url, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', 'javascript:void(0)', [
                                                    //'title' => Yii::t('yii', 'delete'),
                                                    //'data-pjax' => 'w0',
                                                    'onclick' => "
                                            var fecha = $('#fecha').val();
                                            bootbox.confirm({ 
  buttons: {
        confirm: {
            label: 'Confirmar',
            className: 'btn-success'
        },
        cancel: {
            label: 'Cancelar',
            className: 'btn-danger'
        }
    },
  message: '¿Esta seguro de eliminar éste registro?', 
  callback: function(result){ 
    if (result) { 
    $.ajax({
        type     :'POST',
        cache    : false,
        url  : '" . Url::to(['delete']) . "?id=" . $model->corte_id . "&rangofecha='+fecha,
        success  : function(response) {
        $('#ajax_result').html(response);
        }
    });

    }
  }
})",
                                        ]);
                                    }
                                        ]
                                    ],
                                ],
                            ]);
                            ?>
                            <?php Pjax::end(); ?>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="profile">
                            <?php
                            Pjax::begin(['id' => 'indexcorte-pj', 'timeout' => false,
                                'enablePushState' => false]);
                            ?> 
                            <p>
                                <?= Html::a(Yii::t('app', 'Create cut'), 'javascript:void(0)', ['class' => 'btn btn-success crearMes']) ?>
                            </p>
                             <?= Html::input("hidden", "fecha", $rangofecha,['id'=>'fecha']); ?>
                            <?=
                            GridView::widget([
                                'dataProvider' => $dataProviderMes,
                                'filterModel' => $searchModel,
                                'columns' => [
                                    //['class' => 'yii\grid\SerialColumn'],
                                    //'corte_id',
                                    //'tipo_corte',
                                    [
                                        'attribute' => 'tipo_corte',
                                        'filter' => false,
                                        'value' => function($data) {
                                            if ($data->tipo_corte == 1) {
                                                return 'Semana';
                                            } else {
                                                return 'Mes';
                                            }
                                        }
                                    ],
                                    //'corte_descripcion',
                                    [
                                        'attribute' => 'corte_descripcion',
                                        'filter' => false,
                                        'value' => function($data) {
                                            $mes = ["January" => "Enero", "February" => "Febrero", "March" => "Marzo", "April" => "Abril", "May" => "Mayo", "June" => "Junio", "July" => "Julio", "August" => "Agosto", "September" => "Septiembre", "October" => "Octubre", "November" => "Noviembre", "December" => "Diciembre"];
                                            return $mes[$data->corte_descripcion];
                                        }
                                            ],
                                            ['class' => 'yii\grid\ActionColumn',
                                                'template' => '{update}{delete}',
                                                'buttons' => [
                                                    'view' => function ($url, $model) {
                                                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                                                    'title' => Yii::t('yii', 'view'),
                                                                    'data-pjax' => 'w0',
                                                        ]);
                                                    },
                                                            'update' => function ($url, $modelCorte) {

                                                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', 'javascript:void(0)', [
                                                                    //'title' => Yii::t('app', 'Tbl Opcions'),
                                                                    //'data-pjax' => '0',
                                                                    'onclick' => "  
                                       var fecha = $('#fecha').val();
                                    $.ajax({
                                    type     :'POST',
                                    cache    : false,
                                    url  : '" . Url::to(['update', 'corte_id' => $modelCorte->corte_id]) . "&rangofecha='+fecha,
                                    success  : function(response) {
                                        $('#ajax_result').html(response);
                                    }
                                   });
                                   return false;",
                                                        ]);
                                                    },
                                                            'delete' => function ($url, $model) {
                                                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', 'javascript:void(0)', [
                                                                    //'title' => Yii::t('yii', 'delete'),
                                                                    //'data-pjax' => 'w0',
                                                                    'onclick' => "
                                            var fecha = $('#fecha').val();
                                            if (confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "')) { 
                                                    $.ajax({
                                    type     :'POST',
                                    cache    : false,
                                    url  : '" . Url::to(['delete']) . "?id=" . $model->corte_id . "&rangofecha='+fecha,
                                    success  : function(response) {
                                        $('#ajax_result').html(response);
                                    }
                                   });
                                   return false;
                                                
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
                                    </div>
                                </div>
                                <script type="text/javascript">

                                    $(".crearSemana").click(function () {
                                        ruta = '<?php
                                echo Url::to(['vistacorte', 'Tipo_corte' => 1, 'rangofecha' => $rangofecha]);
                                ?>';
                                        $.ajax({
                                            type: 'POST',
                                            cache: false,
                                            url: ruta,
                                            success: function (response) {
                                                $('#ajax_result').html(response);
                                            }
                                        });
                                    });
                                     $(".crearMes").click(function () {
                                        ruta = '<?php
                                echo Url::to(['vistacorte', 'Tipo_corte' => 2, 'rangofecha' => $rangofecha]);
                                ?>';
                                        $.ajax({
                                            type: 'POST',
                                            cache: false,
                                            url: ruta,
                                            success: function (response) {
                                                $('#ajax_result').html(response);
                                            }
                                        });
                                    });
                                </script>
                                <?php Modal::end(); ?>
