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

$this->title = 'Control de Dimensionamiento';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
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

    $yearActual = date("Y");

    $txtCountId = Yii::$app->db->createCommand("select count(*) from tbl_control_dimensionamiento where usua_id = $sessiones")->queryScalar(); 

          $querys =  new Query;
          $querys ->select(['*'])->distinct()
                    ->from('tbl_control_dimensionamiento')  
                    ->where(['tbl_control_dimensionamiento.anulado' => 'null'])
                    ->andwhere('tbl_control_dimensionamiento.usua_id = '.$sessiones.'');
          $command = $querys->createCommand();
          $data = $command->queryAll(); 

      $txtMes = null;
      $txtvalorMes = null;
      $txtduralla = null;
      $txttimeA = null;
      $txtactuales = null;
      $txtotras_actividad = null;
      $txtturno_promedio = null;
      $txtausentismo = null;
      $txtvaca_permi_licen = null;
      $txtIdDimensionar = null;
      $txtduracion_ponde = null;
      $txtocupacion = null;
      $txthorasCNX = null;
      $txtuti_gentes = null;
      $txthoras_laboral_mes = null;
      $txtp_monit = null;
      $txtp_otras_actividad = null;
      $txtpnas_vacaciones = null;
      $txtpnas_ausentismo = null;
      $txtexceso_deficit = null;

?>
<br>
<?php if($roles == "274" || $roles == "270" || $roles == "276" || $roles == "293") {?>
<div class="page-header" >
    <h3><center><?= Html::encode($this->title) ?></center></h3>
</div> 
<br>
<div class="control-procesos-index">
  <div align="left">
    
      &nbsp;&nbsp;
        <?= Html::button('Crear dimensionamiento', ['value' => url::to('createdimensionar'), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                          'data-toggle' => 'tooltip',
                          'title' => 'Crear Dimensionamiento', 'style' => 'background-color: #4298b4']) 
        ?> 

        <?php
	
          Modal::begin([
            'header' => '<h4>Crear Dimensionamiento</h4>',
            'id' => 'modal1',
            //'size' => 'modal-lg',
          ]);

          echo "<div id='modalContent1'></div>";
                                
          Modal::end(); 
        ?> 
    <?php if($txtCountId != 0) {?>
      &nbsp;&nbsp;    
        <?= Html::button('Actualizar dimensionamiento', ['value' => url::to('updatedimensionar'), 'class' => 'btn btn-success', 'id'=>'modalButton2',
                          'data-toggle' => 'tooltip',
                          'title' => 'Actualizar Dimensionamiento', 'style' => 'background-color: #337ab7']) 
        ?> 


        <?php
          Modal::begin([
            'header' => '<h4>Actualizar Dimensionamiento</h4>',
            'id' => 'modal2',
            //'size' => 'modal-lg',
          ]);

          echo "<div id='modalContent2'></div>";
                                
          Modal::end(); 
        ?> 
    <?php } ?>
    
  </div>
  <br>

  <div align="center" style="display: inline">
    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
      <thead>
        <tr>
          <th scope="row"></th>
          <?php 
          foreach ($data as $key => $value) {
            $txtMes = $value['month'];          
          ?>
          <th class="text-center"><?php echo $txtMes; ?></th>
          <?php } ?>
<!--           <th class="text-center">Febrero</th>
          <th class="text-center">Marzo</th>
          <th class="text-center">Abril</th>
          <th class="text-center">Mayo</th>
          <th class="text-center">Junio</th>
          <th class="text-center">Julio</th>
          <th class="text-center">Agosto</th>
          <th class="text-center">Septiembre</th>
          <th class="text-center">Octubre</th>
          <th class="text-center">Noviembre</th>
          <th class="text-center">Diciembre</th> -->
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>Valoraciones al mes</th> 
        <?php 
          foreach ($data as $key => $value) {
                  $txtvalorMes = $value['cant_valor'];
        ?>                   
          <td class="text-center"><?php echo $txtvalorMes; ?></td>
        <?php } ?>
        </tr>

        <tr>
          <th>Duración llamadas Muestreo (segundos)</th>
        <?php 
          foreach ($data as $key => $value) {
                  $txtduralla = $value['tiempo_llamada'];
        ?>  
          <td class="text-center"><?php echo $txtduralla; ?></td>  
        <?php } ?>
        </tr>

        <tr>
          <th>Tiempo adicional al muestreo (seg)</th> 
        <?php 
          foreach ($data as $key => $value) {
                  $txttimeA = $value['tiempoadicional'];
        ?>                  
          <td class="text-center"><?php echo $txttimeA; ?></td> 
        <?php } ?> 
        </tr>
        <tr>
          <th>Tecnicos Cx Actuales (incluye encargos y Oficiales)</th> 
        <?php 
          foreach ($data as $key => $value) {
                  $txtactuales = $value['actuales'];
        ?>            
          <td class="text-center"><?php echo $txtactuales; ?></td>  
        <?php } ?>
        </tr>
        <tr>
          <th>%  del tiempo de tecnico que invierte a en otras actividades</th>
        <?php 
          foreach ($data as $key => $value) {
                  $txtotras_actividad = $value['otras_actividad'];
        ?>                    
          <td class="text-center"><?php echo $txtotras_actividad; ?>%</td>
        <?php } ?>
        </tr>
        <tr>
          <th>Turno Promedio en la semana del tecnico </th>   
        <?php 
          foreach ($data as $key => $value) {
                  $txtturno_promedio = $value['turno_promedio'];
        ?>                    
          <td class="text-center"><?php echo $txtturno_promedio; ?></td> 
        <?php } ?> 
        </tr>
        <tr>
          <th>Ausentismo</th>  
        <?php 
          foreach ($data as $key => $value) {
                  $txtausentismo = $value['ausentismo'];
        ?>                  
          <td class="text-center"><?php echo $txtausentismo; ?>%</td> 
        <?php } ?>
        </tr>
        <tr>
          <th>Vacaciones Permisos y Licencias</th> 
        <?php 
          foreach ($data as $key => $value) {
                  $txtvaca_permi_licen = $value['vaca_permi_licen'];
        ?>                  
          <td class="text-center"><?php echo $txtvaca_permi_licen; ?>%</td>
        <?php } ?>
        </tr>
        <tr>
          <td colspan="13" style="background-color: #ffffff"></td> 
        </tr>
        <tr>
          <td colspan="13" style="background-color: #ffffff"></td> 
        </tr>
        <tr>
          <th >Duración Ponderada Actividades (Segundos)</th>
        <?php 
          foreach ($data as $key => $value) {
                  $txtIdDimensionar = $value['iddimensionamiento'];

                  $txtduracion_ponde = Yii::$app->db->createCommand("select duracion_ponde from tbl_control_dimensionar where anulado = 0 and iddimensionamiento = $txtIdDimensionar")->queryScalar();
        ?>            
          <td class="text-center" ><?php echo $txtduracion_ponde; ?></td>
        <?php } ?>          
       
        </tr>
        <tr>
          <th >Ocupación</th>
        <?php 
          foreach ($data as $key => $value) {
                  $txtIdDimensionar = $value['iddimensionamiento'];

                  $txtocupacion = Yii::$app->db->createCommand("select ocupacion * 100 from tbl_control_dimensionar where anulado = 0 and iddimensionamiento = $txtIdDimensionar")->queryScalar();
        ?> 
          <td class="text-center" ><?php echo $txtocupacion; ?>%</td> 
        <?php } ?> 
        </tr>
        <tr>       
          <th >Carga de Trabajo</th>
        <?php 
          foreach ($data as $key => $value) {
                  $txtIdDimensionar = $value['iddimensionamiento'];

                  $txtcarga_trabajo = Yii::$app->db->createCommand("select round(carga_trabajo) from tbl_control_dimensionar where anulado = 0 and iddimensionamiento = $txtIdDimensionar")->queryScalar();
        ?>            
          <td class="text-center" ><?php echo $txtcarga_trabajo; ?></td> 
        <?php } ?> 
        </tr>
        <tr>
          <th >Horas Conexión</th>
        <?php 
          foreach ($data as $key => $value) {
                  $txtIdDimensionar = $value['iddimensionamiento'];

                  $txthorasCNX = Yii::$app->db->createCommand("select round(horasCNX) from tbl_control_dimensionar where anulado = 0 and iddimensionamiento = $txtIdDimensionar")->queryScalar();
        ?>            
          <td class="text-center" ><?php echo $txthorasCNX; ?></td>  
        <?php } ?>
        </tr>
        <tr>
          <th >Utilización de Agentes</th>
        <?php 
          foreach ($data as $key => $value) {
                  $txtIdDimensionar = $value['iddimensionamiento'];

                  $txtuti_gentes = Yii::$app->db->createCommand("select uti_gentes * 100 from tbl_control_dimensionar where anulado = 0 and iddimensionamiento = $txtIdDimensionar")->queryScalar();
        ?>             
          <td class="text-center" ><?php echo $txtuti_gentes; ?>%</td>
        <?php } ?> 
        </tr>
        <tr>
          <th >Horas Nómina Monitoreo</th>
        <?php 
          foreach ($data as $key => $value) {
                  $txtIdDimensionar = $value['iddimensionamiento'];

                  $txthoras_nomina_monit = Yii::$app->db->createCommand("select round(horas_nomina_monit) from tbl_control_dimensionar where anulado = 0 and iddimensionamiento = $txtIdDimensionar")->queryScalar();
        ?>             
          <td class="text-center" ><?php echo $txthoras_nomina_monit; ?></td> 
        <?php } ?>
        </tr>
        <tr>
          <th >Horas laborables mes</th>
        <?php 
          foreach ($data as $key => $value) {
                  $txtIdDimensionar = $value['iddimensionamiento'];

                  $txthoras_laboral_mes = Yii::$app->db->createCommand("select horas_laboral_mes from tbl_control_dimensionar where anulado = 0 and iddimensionamiento = $txtIdDimensionar")->queryScalar();
        ?>           
          <td class="text-center" ><?php echo $txthoras_laboral_mes; ?></td> 
        <?php } ?>
        </tr>
        <tr>
          <th >FTE</th>
        <?php 
          foreach ($data as $key => $value) {
                  $txtIdDimensionar = $value['iddimensionamiento'];

                  $txtFTE = Yii::$app->db->createCommand("select round(FTE) from tbl_control_dimensionar where anulado = 0 and iddimensionamiento = $txtIdDimensionar")->queryScalar();
        ?>           
          <td class="text-center" ><?php echo $txtFTE; ?></td>  
        <?php } ?>
        </tr>
        <tr>
          <th >Técnicos en monitoreo</th>
        <?php 
          foreach ($data as $key => $value) {
                  $txtIdDimensionar = $value['iddimensionamiento'];

                  $txtp_monit = Yii::$app->db->createCommand("select round(p_monit) from tbl_control_dimensionar where anulado = 0 and iddimensionamiento = $txtIdDimensionar")->queryScalar();
        ?>           
          <td class="text-center" ><?php echo $txtp_monit; ?></td> 
        <?php } ?>
        </tr>
        <tr>
          <th >Técnicos en otras actividades</th>
        <?php 
          foreach ($data as $key => $value) {
                  $txtIdDimensionar = $value['iddimensionamiento'];

                  $txtp_otras_actividad = Yii::$app->db->createCommand("select round(p_otras_actividad) from tbl_control_dimensionar where anulado = 0 and iddimensionamiento = $txtIdDimensionar")->queryScalar();
        ?>            
          <td class="text-center" ><?php echo $txtp_otras_actividad; ?></td>
        <?php } ?> 
        </tr>
        <tr>
          <th >Técnicos CX requeridos</th>
        <?php 
          foreach ($data as $key => $value) {
                  $txtIdDimensionar = $value['iddimensionamiento'];

                  $txtpersonas = Yii::$app->db->createCommand("select round(personas) from tbl_control_dimensionar where anulado = 0 and iddimensionamiento = $txtIdDimensionar")->queryScalar();
        ?>           
          <td class="text-center" ><?php echo $txtpersonas; ?></td>  
        <?php } ?>
        </tr>
        <tr>
          <th >Técnicos CX para vacaciones</th>
        <?php 
          foreach ($data as $key => $value) {
                  $txtIdDimensionar = $value['iddimensionamiento'];

                  $txtpnas_vacaciones = Yii::$app->db->createCommand("select round(pnas_vacaciones) from tbl_control_dimensionar where anulado = 0 and iddimensionamiento = $txtIdDimensionar")->queryScalar();
        ?>           
          <td class="text-center" ><?php echo $txtpnas_vacaciones; ?></td>   
        <?php } ?>
        </tr>
        <tr>
          <th >Técnicos CX ausentismo</th>
        <?php 
          foreach ($data as $key => $value) {
                  $txtIdDimensionar = $value['iddimensionamiento'];

                  $txtpnas_ausentismo = Yii::$app->db->createCommand("select round(pnas_ausentismo) from tbl_control_dimensionar where anulado = 0 and iddimensionamiento = $txtIdDimensionar")->queryScalar();
        ?>            
          <td class="text-center" ><?php echo $txtpnas_ausentismo; ?></td>    
        <?php } ?>
        </tr>
        <tr>
          <th >Técnicos Exceso/Deficit</th>
       <?php 
          foreach ($data as $key => $value) {
                  $txtIdDimensionar = $value['iddimensionamiento'];

                  $txtexceso_deficit = Yii::$app->db->createCommand("select round(exceso_deficit) from tbl_control_dimensionar where anulado = 0 and iddimensionamiento = $txtIdDimensionar")->queryScalar();
        ?>           
          <td class="text-center" ><?php echo $txtexceso_deficit; ?></td>
        <?php } ?>   
        </tr>
      </tbody>
    </table>
  </div>
  <br>
</div>
<?php }else{ ?>
<div class="panel panel-warning">
  <div class="panel-heading">Importante</div>
  <div class="panel-body">No es posible ver resultados ya que no tiene pemisos a utilizar este módulo.</div>
</div> 
<?php } ?>