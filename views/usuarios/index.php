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
<?php if ($isAjax) : ?>
    <?php
    Modal::begin([
        //'header' => Yii::t('app', 'Create Tbl Pregunta'),
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
<!--        <div class="page-header">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>-->
        <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
            echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
        ?>
        <?php
        //Html::beginForm(Url::to(['roles/index']), "post", ["class" => "form-horizontal", "id" => "permisosMasivos"]);
        $form = ActiveForm::begin(['options' => ["id" => "formUsuarios"], 'layout' => 'horizontal'])
        ?>
        <p>
            <?= Html::a(Yii::t('app', 'Create Usuarios'), ['create', 'grupo_id' => $grupo_id], ['class' => 'btn btn-success']) ?>
        </p>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],
                //'usua_id',
                [
                    'attribute' => 'usua_id',
                    'filter' => false,
                    'value' => 'usua_id'
                ],
                //'usua_usuario',
                [
                    'attribute' => 'usua_usuario',
                    'filter' => false,
                    'value' => 'usua_usuario'
                ],
                //'usua_nombre',
                [
                    'attribute' => 'usua_nombre',
                    'filter' => false,
                    'value' => 'usua_nombre'
                ],
                //'usua_email:email',
                //'usua_identificacion',
                [
                    'attribute' => 'usua_identificacion',
                    'filter' => false,
                    'value' => 'usua_identificacion'
                ],
                //'usua_activo',
                [
                    'attribute' => 'usua_activo',
                    'filter' => false,
                    'value' => 'usua_activo'
                ],
                //'usua_estado',
                [
                    'attribute' => 'rol',
                    'filter' => false,
                    'value' => 'relUsuariosRoles.roles.role_descripcion'
                ],
                // 'usua_fechhoratimeout',
                /* ['class' => 'yii\grid\ActionColumn',
                  'template' => '{view}{update}'], */
                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{view}{update}{delete}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['view', 'id' => $model->usua_id, 'grupo_id' => $model->grupo]), [
                                        'title' => Yii::t('yii', 'view'),
                                        'data-pjax' => 'w0',
                            ]);
                        },
                                'update' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['update', 'id' => $model->usua_id, 'grupo_id' => $model->grupo]), [
                                        'title' => Yii::t('yii', 'update'),
                                        'data-pjax' => 'w0',
                            ]);
                        },
                                'delete' => function ($url, $model) {
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

<!--                <div class="page-header">
                    <h3><?= Html::encode($this->title) ?></h3>
                </div>-->
            <?php
            foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
            }
            ?>
                <?php
                //Html::beginForm(Url::to(['roles/index']), "post", ["class" => "form-horizontal", "id" => "permisosMasivos"]);
                $form = ActiveForm::begin(['options' => ["id" => "formUsuarios"], 'layout' => 'horizontal'])
                ?>
                <p>
                <?= Html::a(Yii::t('app', 'Create Usuarios'), ['create'], ['class' => 'btn btn-success']) ?>
                <?= Html::a(Yii::t('app', 'Export Logeventsadmin'), "javascript:void(0)", ['class' => 'btn btn-danger', "id" => "exportar"]) ?>
                <?= Html::a(Yii::t('app', 'Limpiar Filtros'), ['limpiarfiltros'], ['class' => 'btn btn-default']) ?>
		<?php
		if( $sessiones == 3205){ ?>
                 <?= Html::a(Yii::t('app', 'Actualiza usuarios Evalua.'), ['usuarios_evalua'], ['class' => 'btn btn-default']) ?>
                <?php }
                ?>

                </p>

                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            //['class' => 'yii\grid\SerialColumn'],

                            'usua_id',
                            'usua_usuario',
                            'usua_nombre',
                            //'usua_email:email',
                            'usua_identificacion',
                            'usua_activo',
                            //'usua_estado',
                            [
                                'attribute' => 'rol',
                                'value' => 'relUsuariosRoles.roles.role_descripcion'
                            ],
                            [
                                'attribute' => 'usuarios',
                                'format' => 'raw',
                                'value' => function ($data) {
                                    return Html::a('Ver Grupos de Usuario', 'javascript:void(0)', [
                                                //'title' => Yii::t('app', 'Tbl Opcions'),
                                                //'data-pjax' => '0',
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
                                    // 'usua_fechhoratimeout',
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