<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of dashboardsegundocalificador
 *
 * @author ingeneo
 */
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = Yii::t('app', 'notificaciones segundo calificador');
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
<div class="slides-index">





    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}{notificacion}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        $ejecucion = \app\models\Ejecucionformularios::findOne(["id" => $model->id_ejecucion_formulario]);
                        if (isset($ejecucion->basesatisfaccion_id)) {
                            $modelBase = app\models\BaseSatisfaccion::findOne($ejecucion->basesatisfaccion_id);
                        }
                        if ($ejecucion->basesatisfaccion_id == '' || empty($ejecucion->basesatisfaccion_id) || is_null($ejecucion->basesatisfaccion_id)) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>'
                                            , Url::to(['formularios/showformulariodiligenciadohistorico'
                                                , 'tmp_id' => $model->id_ejecucion_formulario,'view'=>"site/segundocalificador"]), [
                                        'title' => Yii::t('yii', 'ver formulario'),
                                        'target' => "_blank"
                            ]);
                        } else {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['basesatisfaccion/showformulariogestion'
                                                , 'basesatisfaccion_id' => $modelBase->id, 'preview' => 1, 'fill_values' => true,'view'=>"site/segundocalificador"]), [
                                        'title' => Yii::t('yii', 'ver formulario'),
                                        'target' => "_blank"
                            ]);
                        }
                    },
                            'update' => function ($url, $model) {
                        $ejecucion = \app\models\Ejecucionformularios::findOne(["id" => $model->id_ejecucion_formulario]);
                        if (isset($ejecucion->basesatisfaccion_id)) {
                            $modelBase = app\models\BaseSatisfaccion::findOne($ejecucion->basesatisfaccion_id);
                        }
                        $sql = 'SELECT u.usua_id,u.usua_usuario,r.grupo_id,g.usua_id_responsable AS resp FROM  tbl_usuarios u '
                                . 'INNER JOIN rel_grupos_usuarios r ON u.usua_id = r.usuario_id '
                                . 'INNER JOIN tbl_grupos_usuarios g ON g.grupos_id = r.grupo_id'
                                . ' INNER JOIN tbl_permisos_grupos_arbols pga ON pga.grupousuario_id = g.grupos_id'
                                . ' WHERE u.usua_id = ' . $ejecucion->usua_id . ' AND pga.arbol_id = ' . $ejecucion->arbol_id;
                        $liderEvaluador = \Yii::$app->db->createCommand($sql)->queryAll();
                        if ($ejecucion->basesatisfaccion_id == '' || empty($ejecucion->basesatisfaccion_id) || is_null($ejecucion->basesatisfaccion_id)) {

                            if ($model->id_evaluador == $model->id_responsable && Yii::$app->user->identity->id == $model->id_responsable) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>'
                                                , Url::to(['formularios/editarformulariodiligenciado'
                                                    , 'tmp_id' => $model->id_ejecucion_formulario, 'view' => "site/segundocalificador"]), [
                                            'title' => Yii::t('yii', 'Update'),
                                            //'target' => "_blank",
                                ]);
                            } else {
                                if (count($liderEvaluador) > 0) {
                                    if ($liderEvaluador[0]['resp'] == $model->id_responsable && Yii::$app->user->identity->id == $model->id_responsable) {
                                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>'
                                                        , Url::to(['formularios/editarformulariodiligenciado'
                                                            , 'tmp_id' => $model->id_ejecucion_formulario, 'view' => "site/segundocalificador"]), [
                                                    'title' => Yii::t('yii', 'Update'),
                                                    //'target' => "_blank",
                                        ]);
                                    }
                                }
                            }
                        } else {
                            if ($model->id_evaluador == $model->id_responsable && Yii::$app->user->identity->id == $model->id_responsable) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['basesatisfaccion/showformulariogestion'
                                                    , 'basesatisfaccion_id' => $modelBase->id, 'preview' => 0, 'fill_values' => false, 'view' => "site/segundocalificador"]), [
                                            'title' => Yii::t('yii', 'Update'),
                                            //'target' => "_blank"
                                ]);
                            }
                        }
                    },
                            'notificacion' => function ($url, $model) {

                        return Html::a('<span style="color: #fa142f" class="glyphicon glyphicon-hand-up"></span>', '', [
                                    'title' => Yii::t('yii', 'notificaciones segundo calificador'),
                                    'data-pjax' => 'w0',
                                    'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['site/viewnotificacion'
                                        , 'id' => $model->id_segundo_calificador, 'id_caso' => $model->id_caso]) . "',
                                success  : function(response) {
                                    $('#ajax_result').html(response);
                                }
                               });
                               return false;",
                        ]);
                    },
                            'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['delete', 'id' => $model->id_segundo_calificador]), [
                                    'title' => Yii::t('yii', 'delete'),
                                    'data-pjax' => 'w0', //                                        
                                    'onclick' => "
                            if (confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "')) {                                                            
                                return true;
                            }else{
                                return false;
                            }",
                        ]);
                    },
                        ]
                    ],
                    [
                        'attribute' => 'id_caso',
                        'filter' => false,
                    ],
                    [
                        'attribute' => 's_fecha',
                        'filter' => false,
                    ],
                    [
                        'attribute' => 'tipo_notifi',
                        'filter' => false,
                        'value' => function($data) {

                            return 'Segundo Calificador';
                        }
                    ],
                    [
                        'attribute' => 'formulario',
                        'value' => 'idEjecucionFormulario.formulario.name',
                        'filter' => false,
                    ],
                    [
                        'attribute' => 'id_solicitante',
                        'value' => 'idSolicitante.name',
                        'filter' => false,
                    ],
                    [
                        'attribute' => 'id_evaluador',
                        'value' => 'idValorador.usua_nombre',
                        'filter' => false,
                    ],
                    [
                        'format' => 'html',
                        'attribute' => 'argumento',
                        'filter' => false,
                    ],
                    [
                        'format' => 'html',
                        'attribute' => 'estado',
                        'value' => function($data) {
                            return $data->getEstados($data->estado_sc);
                        }
                    ],
                ],
            ]);
            ?>

        </div>
        <?php
        echo Html::tag('div', '', ['id' => 'ajax_result']);
        ?>