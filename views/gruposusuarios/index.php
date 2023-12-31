<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GruposusuariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Gruposusuarios');
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
        'id' => 'modal-grupos-usuarios',
        'size' => Modal::SIZE_LARGE,
        'clientOptions' => [
            'show' => true,
        ],
    ]);
    ?>
    <div class="gruposusuarios-index">
        <?php
        Pjax::begin(['id' => 'grupos-usuarios-pj', 'timeout' => false,
            'enablePushState' => false]);
        ?>  
       <?= Html::encode($this->title) ?>
        <?php echo $this->render('_formGrupos', ['model' => $model, 'usuario_id' => $usuario_id]); ?>
        

        <p>
        
        </p>

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [

                'grupos_id',
                'nombre_grupo',
                'grupo_descripcion',
                [
                    'attribute' => 'per_realizar_valoracion',
                    'filter' => false,
                    'value' => function ($data) {
                        if ($data->per_realizar_valoracion == 1) {
                            return 'Si';
                        } else {
                            return 'No';
                        }
                    },
                ],
                ['class' => 'yii\grid\ActionColumn',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            if ($url == "asda") {
                                #code...
                            }
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['view', 'id' => $model->grupos_id, 'usuario_id' => $model->usuario]), [
                                        'title' => Yii::t('yii', 'view'),
                                        'data-pjax' => 'w0',
                            ]);
                        },
                                'update' => function ($url, $model) {
                                    if ($url == "asda") {
                                        #code...
                                    }
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['update', 'id' => $model->grupos_id, 'usuario_id' => $model->usuario]), [
                                        'title' => Yii::t('yii', 'update'),
                                        'data-pjax' => 'w0',
                            ]);
                        },
                                'delete' => function ($url, $model) {
                                    if ($url == "asda") {
                                        #code...
                                    }
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['delete', 'id' => $model->grupos_id, 'usuario_id' => $model->usuario]), [
                                        'title' => Yii::t('yii', 'delete'),
                                        'data-pjax' => 'w0',
                                        'onclick' => "
                            if (confirm('" . Yii::t('app', 'Are you sure you want to delete this item?') . "')) {                                                            
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
\yii\bootstrap\Modal::begin([
    'id' => 'modalCamposRoles'
    , 'header' => "Advertencia"
    , 'size' => \yii\bootstrap\Modal::SIZE_SMALL
]);
echo Yii::t("app", "Seleccione los Grupos de usuarios");
\yii\bootstrap\Modal::end();
?>
            <div class="gruposusuarios-index">
<?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
    }
    ?>
                                <?= Html::encode($this->title) ?>

                
<?php

    $form = ActiveForm::begin([
        'options' => ["id" => "permisosMasivos"],
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ])
    ?>
                <p>
                    <?= Html::a(Yii::t('app', 'Create Gruposusuarios'), ['create'], ['class' => 'btn btn-success']) ?>
                    <?= Html::a(Yii::t('app', 'Export Logeventsadmin'), "javascript:void(0)", ['class' => 'btn btn-danger', "id" => "exportar"]) ?>
                    <?= Html::a(Yii::t('app', 'Limpiar Filtros'), ['limpiarfiltros'], ['class' => 'btn btn-default']) ?>
                    <?= Html::a(Yii::t('app', 'Asignar Permiso'), "javascript:void(0)", ['class' => 'btn btn-warning', 'id' => 'permisos']) ?>

                </p>

                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [

                        'grupos_id',
                        'nombre_grupo',
                        'grupo_descripcion',
                        [
                            'attribute' => 'per_realizar_valoracion',
                            'filter' => false,
                            'value' => function ($data) {
                                if ($data->per_realizar_valoracion == 1) {
                                    return 'Si';
                                } else {
                                    return 'No';
                                }
                            },
                        ],
                        [
                            'attribute' => 'usuarios',
                            'format' => 'raw',
                            'value' => function ($data) {
                                return Html::a(Yii::t('app', 'Ver Usuarios'), 'javascript:void(0)', [
                                            'title' => Yii::t('app', 'Ver Usuarios'),
                                            'onclick' => "                                    
                            $.ajax({
                            type     :'POST',
                            cache    : false,
                            url  : '" . Url::to(['usuarios/index'
                                                , 'grupo_id' => $data->grupos_id]) . "',
                            success  : function(response) {
                                $('#ajax_result').html(response);
                            }
                           });
                           return false;",
                                ]);
                            }
                                ],
                                [
                                    'class' => 'yii\grid\CheckboxColumn',
                                // you may configure additional properties here
                                ],
                                ['class' => 'yii\grid\ActionColumn'],
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
                                var datosUsuarios = $("#permisosMasivos");
                                datosUsuarios.attr('action', '<?php echo Url::to(['export']); ?>');
                                datosUsuarios.submit();
                            });

                            $('#permisos').click(function () {
                                var actualizarRolesMasivos = $("#permisosMasivos");
                                var usuarios = new Array();
                                
                                $('input[name="selection[]"]:checked').each(function () {
                                    usuarios.push($(this).val());
                                });
                                if (usuarios.length == 0) {
                                    $('#modalCamposRoles').modal('show');
                                    return;
                                }
                                actualizarRolesMasivos.attr('action', '<?php echo Url::to(['permisosmasivos']); ?>');
                                actualizarRolesMasivos.submit();
                            });
                        });
                    </script>
                <?php endif; ?>
