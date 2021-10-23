<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RolesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Roles');
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
<?php
\yii\bootstrap\Modal::begin([
    'id' => 'modalCamposRoles'
    , 'header' => "Advertencia"
    , 'size' => \yii\bootstrap\Modal::SIZE_SMALL
]);
echo Yii::t("app", "Seleccione Roles y Permisos para realizar esta accion");
\yii\bootstrap\Modal::end();
?>
<div class="roles-index">
    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
    }
    ?>
<!--    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>-->
    <?php
    //Html::beginForm(Url::to(['roles/index']), "post", ["class" => "form-horizontal", "id" => "permisosMasivos"]);
    $form = ActiveForm::begin(['options' => ["id" => "permisosMasivos"], 'layout' => 'horizontal'])
    ?>
    <div class="form-group">

        <div class="col-sm-offset-0 col-sm-10">

            <?= Html::a(Yii::t('app', 'Create Roles'), ['create'], ['class' => 'btn btn-success']) ?>

            <?= Html::a(Yii::t('app', 'Mostrar Permisos'), "javascript:void(0)", ['class' => 'btn btn-default', 'id' => 'verPermisos']) ?>
            <?php //Html::a(Yii::t('app', 'Export Logeventsadmin'), "javascript:void(0)", ['class' => 'btn btn-danger', "id" => "exportar"]) ?>

        </div>

    </div>

    <div id="checkpermisos" class="well">
        <?= Html::hiddenInput('campoVerDiv', $value = '1', ['id' => 'campoVerDiv']) ?>
        <div class="form-group">

            <div class="col-sm-offset-0 col-sm-10">
                <?= $form->field($model, 'per_cuadrodemando')->checkbox() ?>

                <?= $form->field($model, 'per_estadisticaspersonas')->checkbox() ?>

                <?= $form->field($model, 'per_hacermonitoreo')->checkbox() ?>

                <?= $form->field($model, 'per_reportes')->checkbox() ?>

                <?= $form->field($model, 'per_modificarmonitoreo')->checkbox() ?>

                <?= $form->field($model, 'per_adminsistema')->checkbox() ?>

                <?= $form->field($model, 'per_adminprocesos')->checkbox() ?>

                <?= $form->field($model, 'per_editarequiposvalorados')->checkbox() ?>

                <?= $form->field($model, 'per_inboxaleatorio')->checkbox() ?>

                <?= $form->field($model, 'per_desempeno')->checkbox() ?>

                <?= $form->field($model, 'per_abogado')->checkbox() ?>

                <?= $form->field($model, 'per_jefeop')->checkbox() ?>

                <?= $form->field($model, 'per_tecdesempeno')->checkbox() ?>
                
                <?= $form->field($model, 'per_alertas')->checkbox() ?>

                <?= $form->field($model, 'per_evaluacion')->checkbox() ?>

		<?= $form->field($model, 'per_evaluacion')->checkbox() ?>

                <?= $form->field($model, 'per_externo')->checkbox() ?>

                <?= $form->field($model, 'per_ba')->checkbox() ?>

                <?= $form->field($model, 'per_directivo')->checkbox() ?>
                
                <?= $form->field($model, 'per_asesormas')->checkbox() ?>

            </div>
        </div>
        <div class="form-group">

            <div class="col-sm-offset-5 col-sm-10">
                <?= Html::a(Yii::t('app', 'Asignar Permisos'), "javascript:void(0)", ['class' => 'btn btn-warning', 'id' => 'permisos']) ?>
            </div>
        </div>
    </div>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'role_id',
            'role_nombre',
            //'role_descripcion',
            //'per_cuadrodemando',
            //'per_estadisticaspersonas',
            // 'per_hacermonitoreo',
            // 'per_reportes',
            // 'per_modificarmonitoreo',
            // 'per_adminsistema',
            // 'per_adminprocesos',
            // 'per_editarequiposvalorados',
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
<script type="text/javascript">
    $(document).ready(function () {
        $('#checkpermisos').hide();

        $('#verPermisos').click(function () {
            if ($('#campoVerDiv').val() == '1') {
                $('#checkpermisos').show('slow');
                $('#campoVerDiv').attr('value', '0');
            } else {
                $('#checkpermisos').hide('slow');
                $('#campoVerDiv').attr('value', '1');
            }
        });

        $('#permisos').click(function () {
            var actualizarRolesMasivos = $("#permisosMasivos");
            var roles = new Array();
            var bandera = 0;
            if (!($('#roles-per_cuadrodemando').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_estadisticaspersonas').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_hacermonitoreo').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_reportes').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_modificarmonitoreo').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_adminsistema').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_adminprocesos').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_editarequiposvalorados').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_inboxaleatorio').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_desempeno').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_abogado').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_jefeop').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_tecdesempeno').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_alertas').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_evaluacion').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_externo').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_ba').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_directivo').is(':checked'))) {
                bandera++;
            }
            if (!($('#roles-per_asesormas').is(':checked'))) {
                bandera++;
            }
            if (bandera == 9) {
                $('#modalCamposRoles').modal('show');
                return;
            }
            $('input[name="selection[]"]:checked').each(function () {
                roles.push($(this).val());
            });
            if (roles.length == 0) {
                $('#modalCamposRoles').modal('show');
                return;
            }
            actualizarRolesMasivos.attr('action', '<?php echo Url::to(['roles/rolesmasivos']); ?>');
            actualizarRolesMasivos.submit();
        });
        $('#exportar').click(function () {
            var actualizarRolesexport = $("#permisosMasivos");
            actualizarRolesexport.attr('action', '<?php echo Url::to(['roles/export']); ?>');
            actualizarRolesexport.submit();
        });
    });

</script>
