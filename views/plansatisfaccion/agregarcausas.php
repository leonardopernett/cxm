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

$this->title = 'Gestor Plan de Satisfacción - Agregar Análisis de Causas';
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
                
                <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Ingresar Análisis de Causas') ?></label>
                <?= $form->field($model, 'mejoras', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id'=>'idCausas', 'placeholder' => 'Ingresar Análisis de Causas'])?> 

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
                                'onclick' => 'varVerificarCausas();',
                                'title' => 'Registro General']) 
                ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?></label>
                <?= Html::a('Regresar',  ['registrarplan','id_plan'=>$id], ['class' => 'btn btn-success',
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
    function varVerificarCausas(){
        var varidcausas = document.getElementById("idCausas").value;

        if (varidcausas == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar el análisis de causas","warning");
            return;
        }
    };
</script>