<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use app\models\Dashboardcategorias;
use app\models\Planprocesos;

$this->title = 'Gestor Plan de Satisfacción - Crear Plan';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

?>

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<!-- Capa Proceso Informacion General -->
<div id="capaIdGeneral" class="capaGeneral" style="display: inline;">
    
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                
                <div class="row">
                    <div class="col-md-4">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Seleccionar Proceso') ?></label>
                        <?=  $form->field($model, 'id_proceso', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Planprocesos::find()->orderBy(['proceso'=> SORT_DESC])->all(), 'id_procesos', 'proceso'),
                                                    [
                                                        'id' => 'idProcesos',
                                                        'prompt'=>'Seleccionar Procesos...',
                                                    ]
                                            )->label(''); 
                        ?> 
                    </div>

                    <div class="col-md-4">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Seleccionar Área/Operación') ?></label>
                        <?= $form->field($model, 'id_actividad',['labelOptions' => [], 'template' => $template])->dropDownList($varListActividad, ['prompt' => 'Seleccione Area/Operación...', 'onchange' => 'varHabilitar();', 'id'=>'idActividad', ])?>                       
                    </div>

                    <div class="col-md-4">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Seleccionar Producción') ?></label>

                        <div class="capaArea" id="capaIdArea" style="display: none;">
                            <?= $form->field($model, 'id_dp_area',['labelOptions' => [], 'template' => $template])->dropDownList($varListApoyo, ['prompt' => 'Seleccione Área...', 'id'=>'idActividad_Area', ])?>  
                        </div>

                        <div class="capaOperacion" id="capaIdOperacion" style="display: none;">
                            <?= $form->field($model, 'id_dp_clientes',['labelOptions' => [], 'template' => $template])->dropDownList($varListOperacion, ['prompt' => 'Seleccione Operación...', 'id'=>'idActividad_Operacion', ])?>
                        </div>

                    </div>
                </div>

                <br>

                <div class="row">
                    <div class="col-md-4">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Seleccionar Responsable') ?></label>
                        <?= $form->field($model, 'cc_responsable',['labelOptions' => [], 'template' => $template])->dropDownList($varListResponsable, ['prompt' => 'Seleccione Resposnable...', 'id'=>'idResponsable'])?> 
                    </div>

                    <div class="col-md-4">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Estado del Plan') ?></label>
                        <?= $form->field($model, 'estado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 20, 'id'=>'idEstado', 'readonly'=>'readonly', 'value'=>'Abierto'])?>                       
                    </div>

                </div>

            </div>
        </div>
    </div>

</div>

<br>

<!-- Capa Proceso Botones -->
<div id="capaIdBtn" class="capaBtn" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Registro General') ?></label>
                <?= Html::submitButton(Yii::t('app', 'Aceptar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'varVerificar();',
                                'title' => 'Registro General']) 
                ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?></label>
                <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
                ?>
            </div>
        </div>
    </div>

</div>

<?php ActiveForm::end(); ?>


<script type="text/javascript">
    function varHabilitar(){
        var varidActividad = document.getElementById("idActividad").value;
        var varcapaIdArea = document.getElementById("capaIdArea");
        var varcapaIdOperacion = document.getElementById("capaIdOperacion");

        if (varidActividad == 1) {
            varcapaIdArea.style.display = 'inline';
            varcapaIdOperacion.style.display = 'none';
        }

        if (varidActividad == 2) {
            varcapaIdArea.style.display = 'none';
            varcapaIdOperacion.style.display = 'inline';
        }

        if (varidActividad == "") {
            varcapaIdArea.style.display = 'none';
            varcapaIdOperacion.style.display = 'none';
        }
    };

    function varVerificar(){
        var varidProcesos = document.getElementById("idProcesos").value;
        var varidActividad = document.getElementById("idActividad").value;
        var varidResponsable = document.getElementById("idResponsable").value;

        if (varidProcesos == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un proceso","warning");
            return;
        }
        if (varidActividad == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar una actividad en area u operación","warning");
            return;
        }
        if (varidResponsable == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un responsable","warning");
            return;
        }
    };
</script>