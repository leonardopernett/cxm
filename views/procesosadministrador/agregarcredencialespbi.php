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
use app\models\Dashboardservicios;

$this->title = 'Gestor Parametrizar Power BI - Credenciales';
$this->params['breadcrumbs'][] = $this->title;

  $template = '<div class="col-md-12">'
  . ' {input}{error}{hint}</div>';

  $sessiones = Yii::$app->user->identity->id;

  $rol =  new Query;
  $rol    ->select(['tbl_roles.role_id'])
          ->from('tbl_roles')
          ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                	'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
          ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                  'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
          ->where(['=','tbl_usuarios.usua_id',$sessiones]);                      
  $command = $rol->createCommand();
  $roles = $command->queryScalar();

?>

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<!-- Capa Registrar parametros -->
<div class="capaParametros" id="capaIdParametros" style="display: inline;">
  
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        
        <div class="row">
          <div class="col-md-6">
            <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', ' Ingresar Tenant_Id') ?></label>
            <?= $form->field($model, 'id_workspace', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idTenant','placeholder'=>'Ingresar Tenant Id'])?> 
          </div>

          <div class="col-md-6">
            <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', ' Ingresar Client_Id') ?></label>
            <?= $form->field($model, 'nombre_workspace', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idClient','placeholder'=>'Ingresar Client Id'])?> 
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', ' Ingresar Client_Secret') ?></label>
            <?= $form->field($model, 'id_reporte', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idSecret','placeholder'=>'Ingresar Secret Id'])?> 
          </div>

          <div class="col-md-6">
            <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', ' Ingresar Resource PBI') ?></label>
            <?= $form->field($model, 'nombre_reporte', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idResource','value'=>'https://analysis.windows.net/powerbi/api', 'readonly'=>true])?> 
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', ' Ingresar Power BI Url') ?></label>
            <?= $form->field($model, 'roles', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idUrl','value'=>'https://api.powerbi.com', 'readonly'=>true])?> 
          </div>
        </div>

      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-12">
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
  </div>

</div>

<?php ActiveForm::end(); ?>

<script type="text/javascript">
  function varVerificar(){
    var varidTenant = document.getElementById("idTenant").value;
    var varidClient = document.getElementById("idClient").value;
    var varidSecret = document.getElementById("idSecret").value;

    if (varidTenant == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar el Tenant id","warning");
      return;
    }
    if (varidClient == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar el Client id","warning");
      return;
    }
    if (varidSecret == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar el Secret id","warning");
      return;
    }
  };
</script>