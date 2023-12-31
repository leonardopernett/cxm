<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UsuariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Usuarios');
$this->params['breadcrumbs'][] = $this->title;
$sessiones = Yii::$app->user->identity->id;

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
        'id' => 'modal-usuarios',
        'size' => Modal::SIZE_LARGE,
        'clientOptions' => [
            'show' => true,
        ],
    ]);
    ?>
    <div class="usuarios-index">
        <?php
        Pjax::begin(['id' => 'bloques-pj', 'timeout' => false,
            'enablePushState' => false]);
        ?> 
        <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
            echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
        ?>
        <?php
        $form = ActiveForm::begin([
            'options' => ["id" => "formUsuarios"],
            'layout' => 'horizontal',
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
              ]
            ])
        ?>
        <p>
            <?= Html::a(Yii::t('app', 'Create Usuarios'), ['create', 'grupo_id' => $grupo_id], ['class' => 'btn btn-success']) ?>
        </p>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'usua_id',
                    'filter' => false,
                    'value' => 'usua_id'
                ],
                [
                    'attribute' => 'usua_usuario',
                    'filter' => false,
                    'value' => 'usua_usuario'
                ],
                [
                    'attribute' => 'usua_nombre',
                    'filter' => false,
                    'value' => 'usua_nombre'
                ],
                [
                    'attribute' => 'usua_identificacion',
                    'filter' => false,
                    'value' => 'usua_identificacion'
                ],
                [
                    'attribute' => 'usua_activo',
                    'filter' => false,
                    'value' => 'usua_activo'
                ],
                [
                    'attribute' => 'rol',
                    'filter' => false,
                    'value' => 'relUsuariosRoles.roles.role_descripcion'
                ],
                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{view}{update}{delete}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            if ($url == "asda") {
                                #code...
                            }
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['view', 'id' => $model->usua_id, 'grupo_id' => $model->grupo]), [
                                        'title' => Yii::t('yii', 'view'),
                                        'data-pjax' => 'w0',
                            ]);
                        },
                                'update' => function ($url, $model) {
                                    if ($url == "asda") {
                                        #code...
                                    }
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['update', 'id' => $model->usua_id, 'grupo_id' => $model->grupo]), [
                                        'title' => Yii::t('yii', 'update'),
                                        'data-pjax' => 'w0',
                            ]);
                        },
                                'delete' => function ($url, $model) {
                                    if ($url == "asda") {
                                        #code...
                                    }
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                    Url::to(['deleterel', 
                                        'usuario_id' => $model->usua_id, 'grupo_id' => $model->grupo]),
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
                <?php ActiveForm::end(); ?>

                <?php Pjax::end(); ?>

            </div>


            <script type="text/javascript">
                $(document).ready(function () {
                    $('#exportar').click(function () {
                        var datosUsuarios = $("#formUsuarios");
                        datosUsuarios.attr('action', '<?php echo Url::to(['usuarios/export']); ?>');
                        datosUsuarios.submit();
                    });
                });
            </script>
            <?php Modal::end(); ?>


        <?php else: ?>
            <div class="usuarios-index">

            <?php
            foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
            }
            ?>
                <?php
                $form = ActiveForm::begin([
                    'options' => ["id" => "formUsuarios"],
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'inputOptions' => ['autocomplete' => 'off']
                      ]
                    ])
                ?>
                <p>
                <?= Html::a(Yii::t('app', 'Create Usuarios'), ['create'], ['class' => 'btn btn-success']) ?>
                <?= Html::a(Yii::t('app', 'Export Logeventsadmin'), "javascript:void(0)", ['class' => 'btn btn-danger', "id" => "exportar"]) ?>
                <?= Html::a(Yii::t('app', 'Limpiar Filtros'), ['limpiarfiltros'], ['class' => 'btn btn-default']) ?>
		

                </p>

                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            'usua_id',
                            'usua_usuario',
                            'usua_nombre',
                            'usua_identificacion',
                            'usua_activo',
                            [
                                'attribute' => 'rol',
                                'value' => 'relUsuariosRoles.roles.role_descripcion'
                            ],
                            [
                                'attribute' => 'usuarios',
                                'format' => 'raw',
                                'value' => function ($data) {
                                    return Html::a('Ver Grupos de Usuario', 'javascript:void(0)', [
                                                'onclick' => "                                    
                                    $.ajax({
                                    type     :'POST',
                                    cache    : false,
                                    url  : '" . Url::to(['gruposusuarios/index',
                                                    'usuario_id' => $data->usua_id]) . "',
                                    success  : function(response) {
                                        $('#ajax_result').html(response);
                                    }
                                   });
                                   return false;",
                                    ]);
                                }
                                    ],
                                    ['class' => 'yii\grid\ActionColumn',
                                        'template' => '{view}{update}'],
                                ],
                            ]);
                            ?>
                        <?php ActiveForm::end(); ?>


                    </div>
            <?php
            echo Html::tag('div', '', ['id' => 'ajax_result']);
            ?>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $('#exportar').click(function () {
                                var datosUsuarios = $("#formUsuarios");
                                datosUsuarios.attr('action', '<?php echo Url::to(['usuarios/export']); ?>');
                                datosUsuarios.submit();
                            });
                        });
                    </script>
                   


                <?php endif; ?>