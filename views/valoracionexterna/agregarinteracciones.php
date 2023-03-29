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

$this->title = 'Gestor Valoraciones Externas - Agregar Campos Interacciones';
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
                    <div class="col-md-6">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Ingresar Pregunta o Interacción') ?></label>
                        <?= $form->field($model, 'atributos', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idatributos', 'placeholder' => 'Ingresar Pregunta o Interacción'])?> 
                    </div>

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Ingresar Campo de la Interacción') ?></label>
                        <?= $form->field($model, 'resp_atributos', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 3, 'id'=>'idresp_atributos', 'placeholder' => 'Ingresar Campo de la Interacción - Columna de Excel'])?> 
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
                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Registrar Dato') ?></label>
                <?= Html::submitButton(Yii::t('app', 'Aceptar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'varVerificarAcciones();',
                                'title' => 'Registro General']) 
                ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?></label>
                <?= Html::a('Regresar',  ['agregarparametros','id_general'=>$id_general], ['class' => 'btn btn-success',
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
    function varVerificarAcciones(){
        var varidatributos = document.getElementById("idatributos").value;
        var varidresp_atributos = document.getElementById("idresp_atributos").value;

        if (varidatributos == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar la pregunta o interacción","warning");
            return;
        }

        if (varidresp_atributos == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar el campo de excel de la respuesta a la pregunta","warning");
            return;
        }
    };
</script>