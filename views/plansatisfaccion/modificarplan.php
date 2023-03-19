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

$this->title = 'Gestor Plan de Satisfacción - Registrar Plan';
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

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

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
            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Procesos...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanProcesos_Modificar) ?> </label></td>

            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Responsable...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanResponsable_Modificar) ?> </label></td>
          </tr>
          <tr>
            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Actividad...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanActividad_Modificar) ?> </label></td>

            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Rol Responsable...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanRolResponsable_Modificar) ?> </label></td>
          </tr>
          <tr>
            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Dirección...') ?></label></th>
            <?php
              if ($varPlanAreas_String_Modificar != "") {
            ?>
              <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanAreas_String_Modificar) ?> </label></td>
            <?php
              }else{
            ?>
              <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanOperacion_String_Modificar) ?> </label></td>
            <?php
              }
            ?>

            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Estado...') ?></label></th>
            <td  class="text-center"><label style="font-size: 15px;"> <?= Yii::t('app', $varPlanEstados_Modificar) ?> </label></td>
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
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Requerimientos Complementarios') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        
        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Fecha de Implementación') ?></label>            
            <?= $form->field($model, 'fecha_implementacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idfechaimplementacion','readonly'=>'readonly'])?> 
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Seleccionar Indicador') ?></label>
            <?= $form->field($model, 'indicador',['labelOptions' => [], 'template' => $template])->dropDownList($varListIndicadores, ['prompt' => 'Seleccione Indicador...', 'id'=>'idIndicadores', ])?>  
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Seleccionar Acción') ?></label>
            <?= $form->field($model, 'acciones',['labelOptions' => [], 'template' => $template])->dropDownList($varListAcciones, ['prompt' => 'Seleccione Acción...', 'id'=>'idAcciones', ])?> 
          </div>
        </div>

        <br>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Ingresar Puntaje Meta - %') ?></label>

            <?= $form->field($model, 'puntaje_meta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 3, 'id'=>'idpuntaje_meta','placeholder'=>'Ingresar Puntaje Meta - %','onkeypress' => 'return valida(event)'])?> 
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Ingresar Puntaje Actual - %') ?></label>
            
            <?= $form->field($model, 'puntaje_actual', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 3,'id'=>'idpuntaje_actual','placeholder'=>'Ingresar Puntaje Actual - %','onkeypress' => 'return valida(event)'])?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Ingresar Puntaje Final - %') ?></label>
            
            <?= $form->field($model, 'puntaje_final', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 3,'id'=>'idpuntaje_final','placeholder'=>'Ingresar Puntaje Final - %','onkeypress' => 'return valida(event)'])?> 
          </div>
        </div>

      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Conceptos a Mejorar') ?></label>
        <?= Html::button('Agregar Datos', ['value' => url::to(['agregarconceptos','id'=>$id_plan]), 
            'class' => 'btn btn-success', 'id'=>'modalButton',
            'data-toggle' => 'tooltip',
            'title' => 'Agregar Conceptos a Mejorar']) 
        ?> 

        <?php
          Modal::begin([
            'header' => '<h4>Agregar Información - Conceptos a Mejorar</h4>',
            'id' => 'modal',
          ]);

          echo "<div id='modalContent'></div>";
                                                                                                  
          Modal::end(); 
        ?>
        <table id="tblDataConceptos" class="table table-striped table-bordered tblResDetFreed">
          <caption><?= Yii::t('app', 'Resultados') ?></caption>
          <thead>
            <tr>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Conceptos a Mejorar') ?></label></th>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($varListaPlanConceptos_Modificar as $key => $value) {                  
            ?>
              <tr>
                <td><label style="font-size: 12px;"><?php echo  $value['concepto']; ?></label></td>
                <td class="text-center">
                  <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarconceptos','id_conceptos'=> $value['id_conceptos'],'id_plan'=>$id_plan], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                </td>
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
        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Análisis de Causas') ?></label>
        <?= Html::button('Agregar Datos', ['value' => url::to(['agregarcausas','id'=>$id_plan]), 
            'class' => 'btn btn-success', 'id'=>'modalButton1',
            'data-toggle' => 'tooltip',
            'title' => 'Agregar Análisis de Causas']) 
        ?> 

        <?php
          Modal::begin([
            'header' => '<h4>Agregar Información - Análisis de Causas</h4>',
            'id' => 'modal1',
          ]);

          echo "<div id='modalContent1'></div>";
                                                                                                  
          Modal::end(); 
        ?>
        <table id="tblDataCausas" class="table table-striped table-bordered tblResDetFreed">
          <caption><?= Yii::t('app', 'Resultados') ?></caption>
          <thead>
            <tr>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Análisis de Causas') ?></label></th>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($varListaPlanMejoras_Modificar as $key => $value) {                  
            ?>
              <tr>
                <td><label style="font-size: 12px;"><?php echo  $value['mejoras']; ?></label></td>
                <td class="text-center">
                  <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarmejoras','id_mejoras'=> $value['id_mejoras'],'id_plan'=>$id_plan], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                </td>
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
        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Acciones a Seguir') ?></label>
        <?= Html::button('Agregar Datos', ['value' => url::to(['agregaracciones','id'=>$id_plan]), 
            'class' => 'btn btn-success', 'id'=>'modalButton3',
            'data-toggle' => 'tooltip',
            'title' => 'Agregar Acciones a Seguir']) 
        ?> 

        <?php
          Modal::begin([
            'header' => '<h4>Agregar Información - Acciones a Seguir</h4>',
            'id' => 'modal3',
          ]);

          echo "<div id='modalContent3'></div>";
                                                                                                  
          Modal::end(); 
        ?>
        <table id="tblDataAcciones" class="table table-striped table-bordered tblResDetFreed">
          <caption><?= Yii::t('app', 'Resultados') ?></caption>
          <thead>
            <tr>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones a Seguir') ?></label></th>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($varListaPlanAcciones_Modificar as $key => $value) {                  
            ?>
              <tr>
                <td><label style="font-size: 12px;"><?php echo  $value['acciones']; ?></label></td>
                <td class="text-center">
                  <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminaracciones','id_acciones'=> $value['id_acciones'],'id_plan'=>$id_plan], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                </td>
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
        <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Subir Archivos') ?></label>
        <?= Html::button('Subir Archivos', ['value' => url::to(['subirarchivos','id'=>$id_plan]), 
            'class' => 'btn btn-success', 'id'=>'modalButton5',
            'data-toggle' => 'tooltip',
            'title' => 'Subir Archivos']) 
        ?> 

        <?php
          Modal::begin([
            'header' => '<h4>Agregar Información - Subir Archivos</h4>',
            'id' => 'modal5',

          ]);

          echo "<div id='modalContent5'></div>";
                                                                                                  
          Modal::end(); 
        ?>
        <table id="tblDataArchivos" class="table table-striped table-bordered tblResDetFreed">
          <caption><?= Yii::t('app', 'Resultados') ?></caption>
          <thead>
            <tr>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nombre Archivo') ?></label></th>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($varListPlanArchivos_Modificar as $key => $value) {                  
            ?>
              <tr>
                <td><label style="font-size: 12px;"><?php echo  $value['nombre_archivo']; ?></label></td>
                <td class="text-center">
                  <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminararchivos','id_subirarchivos'=> $value['id_subirarchivos'],'id_plan'=>$id_plan], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                </td>
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
    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Registro Información Complementaria') ?></label>
        <?= Html::submitButton(Yii::t('app', 'Aceptar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'varVerificarSecundaria();',
                                'title' => 'Registrar Datos']) 
        ?>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card1 mb" style="background: #ffe6e6;">
        <label style="font-size: 15px;"><em class="fas fa-times" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Estado del Plan') ?></label>
                <?= Html::a('Cerrar',  ['cerrarplan','id_plan'=>$id_plan], ['class' => 'btn btn-danger',                             'data-toggle' => 'tooltip',
                                                'title' => 'Cerrar Plan']) 
                ?>
      </div>
    </div>

    <div class="col-md-4">
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

<?php ActiveForm::end(); ?>

<script type="text/javascript">
  function valida(e){
    tecla = (document.all) ? e.keyCode : e.which;

    //Tecla de retroceso para borrar, siempre la permite
    if (tecla==8){
      return true;
    }
            
    // Patron de entrada, en este caso solo acepta numeros
    patron =/[0-9]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
  };

  function varVerificarSecundaria(){
    var varidfechaimplementacion = document.getElementById("idfechaimplementacion").value;
    var varidIndicadores = document.getElementById("idIndicadores").value;
    var varidAcciones = document.getElementById("idAcciones").value;
    var vartxtPuntajemeta = document.getElementById("idpuntaje_meta").value;
    var vartxtPuntajeactual = document.getElementById("idpuntaje_actual").value;
    var vartxtPuntajefinal = document.getElementById("idpuntaje_final").value;

    if (varidfechaimplementacion == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de seleccionar una fecha de implementación","warning");
      return;
    }

    if (varidIndicadores == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de seleccionar un indicador","warning");
      return;
    }

    if (varidAcciones == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de seleccionar una acción","warning");
      return;
    }

    if (vartxtPuntajemeta == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar un puntaje de meta","warning");
      return;
    }else{
      if (vartxtPuntajemeta > 100) {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","El puntaje de meta no debe de superar el 100%","warning");
        return;
      }
    }

    if (vartxtPuntajeactual == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar un puntaje actual","warning");
      return;
    }else{
      if (vartxtPuntajeactual > 100) {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","El puntaje actual no debe de superar el 100%","warning");
        return;
      }
    }

    if (vartxtPuntajefinal == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar un puntaje final","warning");
      return;
    }else{
      if (vartxtPuntajefinal > 100) {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","El puntaje final no debe de superar el 100%","warning");
        return;
      }
    }


  };
</script>