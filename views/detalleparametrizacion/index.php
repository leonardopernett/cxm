<?php

use yii\helpers\Html;
use yii\grid\GridView;
//Agregar-----------------------------------------------------------------------
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DetalleparametrizacionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Detalleparametrizacions');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs(
        "$(function(){
       $('#modal-detalleparametrizacion').on('hidden.bs.modal', function (e) {
           location.reload();
        });    
})"
);
?>
<?php if ($isAjax) : ?>
    <?php
    Modal::begin([
        'header' => 'Detalle Parametrizacion',
        'id' => 'modal-detalleparametrizacion',
        'size' => Modal::SIZE_LARGE,
        'clientOptions' => [
            'show' => true,
        ],
    ]);
    ?>
    <?php
    Pjax::begin(['id' => 'detalle-pj', 'timeout' => false,
        'enablePushState' => false]);
    ?>  
    <div class="detalleparametrizacion-index">

        <div class="page-header">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'options' => ['data-pjax' => true],
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
              ]
            ]); ?>
        <?php echo $form->field($modelCategoriaGestion, 'name')->textInput(['id' => 'nombre']) ?>
        <?= $form->field($modelCategoriaGestion, 'prioridad')->input('number',['id' => 'prioridad','min'=>1, 'max'=> 10, 'step'=>1]);?>
        <?php echo Html::hiddenInput('id_parametrizacion', $id_parametrizacion, ['id' => 'id_parametrizacion']); ?>
        <?php echo Html::hiddenInput('idcategoriagestion', $idcategoriagestion, ['id' => 'idcategoriagestion']); ?>
        <?php ActiveForm::end(); ?>
        <p>
            <?= Html::a(Yii::t('app', 'Guardar Categoría de Gestión'), "javascript:void(0)", ['class' => 'btn btn-danger guardarCategoria']) ?>
            <?= Html::a(Yii::t('app', 'Create Detalleparametrizacion'), "javascript:void(0)", ['class' => 'btn btn-success adicionarConfiguracion']) ?>
            
        </p>

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [

                'categoria0.nombre',
                'configuracion',
                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{update}{delete}',
                    'buttons' => [
                        
                                'update' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['update',
                                                'id' => $model->id]), [
                                        'title' => Yii::t('yii', 'update'),
                                        'data-pjax' => 'w0',
                            ]);
                        },
                                'delete' => function ($url, $model, $id_parametrizacion) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', "javascript:void(0)", [
                                        'title' => Yii::t('yii', 'delete'),
                                        'onclick' => "eliminar(" . $model->id . "," . $id_parametrizacion . "," . $model->id_categoriagestion . ");",
                                            ]
                            );
                        }
                            ]
                        ],
                    ],
                ]);
                ?>

            </div>
            <?php Pjax::end(); ?>
            <?php Modal::end(); ?>
        <?php endif; ?>

        <script type="text/javascript">
            $(document).ready(function () {
                $(".adicionarConfiguracion").click(function () {
                    var idcategoriagestion = $('#idcategoriagestion').val();
                    var idparame = $('#id_parametrizacion').val();
                    var nombre = $('#nombre').val();
                    var prioridad = $('#prioridad').val();
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo Url::to(['create']); ?>',
                        dataType: 'text',
                        data: {
                            idparame: idparame,
                            idcategoriagestion: idcategoriagestion,
                            nombre: nombre,
                            prioridad:prioridad
                        },
                        success: function (response) {
                            $('#ajax_result').html(response);
                        }
                    });
                });
                
                $(".guardarCategoria").click(function () {
                    var idcategoriagestion = $('#idcategoriagestion').val();
                    var idparame = $('#id_parametrizacion').val();
                    var nombre = $('#nombre').val();
                    var prioridad = $('#prioridad').val();
                    if(nombre==""||prioridad==""){
                        alert("El campo nombre y prioridad debe estar diligenciados");
                        return;
                    }
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo Url::to(['guardarcategoria']); ?>',
                        dataType: 'text',
                        data: {
                            idparame: idparame,
                            idcategoriagestion: idcategoriagestion,
                            nombre: nombre,
                            prioridad:prioridad
                        }
                    });
                });
            });

            function eliminar(id, parame, gestion) {
                if (confirm('Esta Seguro que desea eliminar este elemento?')) {
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo Url::to(['delete']); ?>',
                dataType: 'text',
                data: {
                    id: id,
                    idparame: parame,
                    gestion: gestion
                },
                success: function (response) {
                    $('#ajax_result').html(response);
                }
            });
        } else {
            return false;
        }

    }
</script>

