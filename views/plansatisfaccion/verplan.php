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

$this->title = 'Gestor Plan de Satisfacción - Ver Plan de Satisfacción';
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

  $varPlanFechaImplementacion = null;
  $varPlanFechaDefinicion = null;
  $varPlanFechaCierre = null;
  $varPlanIndicador = null;
  $varPlanAcciones = null;
  $varPlanPuntajeMeta = null;
  $varPlanPuntjaeActual = null;
  $varPuntajeFinal = null;
  foreach ($varPlanListaSecundaria as $key => $value) {
  	$varPlanFechaImplementacion = $value['fecha_implementacion'];
	  $varPlanFechaDefinicion = $value['fecha_definicion'];
	  $varPlanFechaCierre = $value['fecha_cierre'];
	  $varPlanIndicador =	(new \yii\db\Query())
                            ->select(['nombre'])
                            ->from(['tbl_indicadores_satisfaccion_cliente'])
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_indicador',$value['indicador']])
                            ->scalar(); 
	  $varPlanAcciones = $value['acciones'];
	  $varPlanPuntajeMeta = $value['puntaje_meta'];
	  $varPlanPuntjaeActual = $value['puntaje_actual'];
	  $varPuntajeFinal = $value['puntaje_final'];
  }

?>
<style>
  .card1 {
    height: auto;
    width: auto;
   	margin-top: auto;
    margin-bottom: auto;
    background: #FFFFFF;
    position: relative;
    display: flex;
    justify-content: center;
   	flex-direction: column;
    padding: 10px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 5px;    
   	font-family: "Nunito";
    font-size: 150%;    
    text-align: left;    
  }

  .card2 {
    height: 355px;
    width: auto;
    margin-top: auto;
    margin-bottom: auto;
    background: #FFFFFF;
    position: relative;
    display: flex;
    justify-content: center;
    flex-direction: column;
    padding: 10px;
    box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
    -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
    -moz-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
    border-radius: 5px;    
    font-family: "Nunito",sans-serif;
    font-size: 150%;    
    text-align: left;    
  }

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/satisfacioncliente1.png');
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
      </div>
    </div>
  </div>
</header>

<br>
<br>

<!-- Capa Ficha Tecnica -->
<div id="capaIdFicha" class="capaFicha" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Ficha Técnica') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">

        <table id="myTableInfo" class="table table-hover table-bordered" style="margin-top:10px" >
          <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Lista Ficha Técnica del Plan') ?></label></caption>
          <tr>
            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Procesos...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanProcesos) ?> </label></td>

            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Responsable...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanResponsable) ?> </label></td>
          </tr>
          <tr>
            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Actividad...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanActividad) ?> </label></td>

            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Rol Responsable...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanRolResponsable) ?> </label></td>
          </tr>
          <tr>
            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Dirección...') ?></label></th>
            <?php
              if ($varPlanAreas_String != "") {
            ?>
              <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanAreas_String) ?> </label></td>
            <?php
              }else{
            ?>
              <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanOperacion_String) ?> </label></td>
            <?php
              }
            ?>

            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Estado...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanEstados) ?> </label></td>
          </tr>
        
        </table>


      </div>
    </div>
  </div>

</div>

<hr>

<!-- Capa Informacion Secundaria -->
<div id="capaIdSecundaria" class="capaSecundaria" style="display: inline;">
  
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">

      	<table id="myTableInfo" class="table table-hover table-bordered" style="margin-top:10px" >
          <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Lista de Requerimientos del Plan') ?></label></caption>
          <tr>
            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha de Implementación...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanFechaImplementacion) ?> </label></td>

            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha de Definición...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanFechaDefinicion) ?> </label></td>            
            
          </tr>
          <tr>
          	<th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha de Cierre...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanFechaCierre) ?> </label></td>

          	<th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Puntaje Meta - %...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanPuntajeMeta.'%') ?> </label></td>
            
          </tr>
          <tr>
          	<th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Puntaje Actual - %...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanPuntjaeActual.'%') ?> </label></td>            

            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Puntaje Final - %...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPuntajeFinal.'%') ?> </label></td>
          </tr>
          <tr>
          	<th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Indicador...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanIndicador) ?> </label></td>

            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Acción...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanAcciones) ?> </label></td>
            
          </tr>
          
        
        </table>

      </div>
    </div>
  </div>  

</div>

<hr>

<!-- Capa Listas -->
<div id="capaIdListas" class="capaListas" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb">
				<table id="tblDataConceptos" class="table table-striped table-bordered tblResDetFreed">
          <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Lista de Conceptos a Mejorar') ?></label></caption>
          <thead>
            <tr>
              <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Conceptos a Mejorar') ?></label></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($varListaPlanConceptos as $key => $value) {                  
            ?>
              <tr>
                <td><label style="font-size: 15px;"><?php echo  $value['concepto']; ?></label></td>
              </tr>
            <?php
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card1 mb">
				<table id="tblDataCausas" class="table table-striped table-bordered tblResDetFreed">
          <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Lista de Análisis de Causas') ?></label></caption>
          <thead>
            <tr>
              <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Análisis de Causas') ?></label></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($varListaPlanMejoras as $key => $value) {                  
            ?>
              <tr>
                <td><label style="font-size: 15px;"><?php echo  $value['mejoras']; ?></label></td>
              </tr>
            <?php
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
  	<div class="col-md-6">
  		<div class="card1 mb">
  			<table id="tblDataAcciones" class="table table-striped table-bordered tblResDetFreed">
          <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Lista de Acciones a Seguir') ?></label></caption>
          <thead>
            <tr>
              <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Acciones a Seguir') ?></label></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($varListaPlanAcciones as $key => $value) {                  
            ?>
              <tr>
                <td><label style="font-size: 15px;"><?php echo  $value['acciones']; ?></label></td>
              </tr>
            <?php
              }
            ?>
          </tbody>
        </table>
  		</div>
  	</div>

  	<div class="col-md-6">
  		<div class="card1 mb">
  			<table id="tblDataArchivos" class="table table-striped table-bordered tblResDetFreed">
          <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Lista de Archivos') ?></label></caption>
          <thead>
            <tr>
              <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Nombre Archivo') ?></label></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($varListPlanArchivos as $key => $value) {                  
            ?>
              <tr>
                <td><label style="font-size: 15px;"><?php echo  $value['nombre_archivo']; ?></label></td>
              </tr>
            <?php
              }
            ?>
          </tbody>
        </table>
  		</div>
  	</div>
  </div>

</div>

<hr>

<!-- Capa Eficacia -->
<div id="capaIdEficacia" class="capaEficacia" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Ficha de Eficacia') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
  	<div class="col-md-12">
  		<div class="card1 mb">
  			<table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
          <caption><label><em class="fas fa-list" style="font-size: 20px; color: #b52aef;"></em> <?= Yii::t('app', 'Listado de Eficacia') ?></label></caption>
          <thead>
            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Eficacia') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha Elaboración') ?></label></th>
          </thead>
          <tbody>
            <?php
              foreach ($varListaPlansatisfac as $key => $value) {
                              
            ?>
              <tr>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $value['eficacia']; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $value['fechacreacion']; ?></label></td>
              </tr>
            <?php
              }
            ?>
          </tbody>
        </table>
  		</div>
  	</div>
  </div>
	
</div>

<hr>

<!-- Capa Botonera de acciones -->
<div id="capaIdBtn" class="capaBtn" style="display: inline;">
  
  <div class="row">
    
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

<hr>
