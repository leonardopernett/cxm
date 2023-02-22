<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\db\Query;

$this->title = 'Reporte Satisfacción Cliente';
$this->params['breadcrumbs'][] = $this->title;

$sesiones =Yii::$app->user->identity->id;   

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

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
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/satisfacioncliente2.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

    .loader {
      border: 16px solid #f3f3f3;
      border-radius: 50%;
      border-top: 16px solid #3498db;
      width: 80px;
      height: 80px;
      -webkit-animation: spin 2s linear infinite; /* Safari */
      animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
      0% { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    tr:nth-child(even) {
    background-color: #c25151;
}

</style>
<!-- datatable -->
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">


<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>
<div class="capaInfo" id="idCapaInfo" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Reporte Satisfacción de clientes"; ?> </label>
      </div>
    </div>
  </div>

  <br>

  <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
              <label><em class="fas fa-cogs" style="font-size: 20px; color: #1e8da7;"></em> Acciones:</label>
                <div class="row">                    
                    <div class="col-md-6">
                        <label style="font-size: 15px;"></label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card1 mb">
                                    <?= Html::a('Regresar',  ['index?varidban=0'], ['class' => 'btn btn-success',
                                                    'style' => 'background-color: #707372',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Regresar']) 
                                    ?>                                                                    
                                </div>
                            </div>
                            <div class="col-md-4">
                               <div class="card1 mb">
                                    <a id="dlink" style="display:none;"></a>
                                    <button  class="btn btn-info" style="background-color: #4298B4" id="btn">Exportar Archivo</button>
                                </div>
                            </div>
                                                      
                        </div>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
        
            <div class="card1 mb">
                <table id="myTable" class="table table-hover table-bordered" style="margin-top:10px;" >
                <caption><label><em class="fas fa-list" style="font-size: 20px; color: #b52aef;"></em> <?= Yii::t('app', 'Reporte Satisfacción de Clientes') ?></label></caption>
                <thead>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Id ') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Área / Operación') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Concepto a Mejorar') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Análisis de Causas') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Acción a Seguir') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Acción') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Indicador') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Puntaje Meta %') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Puntaje Actual %') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Puntaje Final %') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Responsable') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Rol') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Fecha Definición Plan') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Fecha Implement.') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Acción Editar') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Acción Seguim.') ?></label></th>                    
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Anexo') ?></label></th>
                </thead>
                <tbody>
                    <?php
                   $varid = null;

                    foreach ($varListasatisfaccion as $key => $value) {
                        $varid_satis = $value['id_satisfaccion'];
                        $varid_operacion = $value['id_operacion'];
                        $varid_area_apoyo = $value['id_area_apoyo'];
                        $varconcepto_mejora = $value['concepto_mejora'];
                        $varanalisis_causa = $value['analisis_causa'];
                        $varaccion_seguir = $value['accion_seguir'];
                        $varaccion = $value['accion'];
                        $varid_indicador = $value['id_indicador'];
                        $varpuntaje_meta = $value['puntaje_meta'];
                        $varpuntaje_actual = $value['puntaje_actual'];
                        $varpuntaje_final = $value['puntaje_final'];
                        $varfecha_definicion = $value['fecha_definicion'];
                        $varfecha_implementacion = $value['fecha_implementacion'];
                        $varestado = $value['estado'];
                        $varfechacreacion = $value['fechacreacion'];
                        $varresponsable_area = $value['responsable_area'];
                        $varEstado = $value['estado'];
                        
                        if($varEstado == 0){
                            $varEstado = 'Activo';
                        }else{
                            $varEstado = 'Inactivo';
                        }
                        if ($varid_area_apoyo){ 
                            $data2 = (new \yii\db\Query())
                                ->select(['tbl_areasapoyo_gptw.nombre'])
                                ->from(['tbl_areasapoyo_gptw'])
                                ->where(['=','tbl_areasapoyo_gptw.id_areaapoyo',$varid_area_apoyo])
                                ->Scalar();
                                                        
                            $varid = $data2;
                            
                        }else{
                            $data3= (new \yii\db\Query())
                                ->select(['tbl_usuarios_evalua.clientearea'])
                                ->from(['tbl_usuarios_evalua'])
                                ->where(['=','tbl_usuarios_evalua.idusuarioevalua',$varid_operacion])
                                ->Scalar();
                            $varid = $data3;                           
                            
                        }  
                         
                        // busca usuario responsable                           
                            $data4 = (new \yii\db\Query())
                            ->select(['usua_nombre'])
                            ->from(['tbl_usuarios'])
                            ->where(['=','usua_id',$varresponsable_area])
                            ->Scalar();
                            $varresponsable_area = $data4;
                            
                        // Busca Rol
                        $txtRta = (new \yii\db\Query())
                        ->select(['tbl_usuarios_jarvis_cliente.posicion'])
                        ->from(['tbl_usuarios_jarvis_cliente'])
                        ->where(['=','tbl_usuarios_jarvis_cliente.idusuarioevalua',$varid_satis])
                        ->Scalar();
                         // Busca indicador
                         $txtindicador = (new \yii\db\Query())
                         ->select(['tbl_indicadores_satisfaccion_cliente.nombre'])
                         ->from(['tbl_indicadores_satisfaccion_cliente'])
                         ->where(['=','tbl_indicadores_satisfaccion_cliente.id_indicador',$varid_indicador])
                         ->Scalar();
                        // Busca anexo
                        $txtanexo = (new \yii\db\Query())
                        ->select(['tbl_satisfaccion_archivos.anexo'])
                        ->from(['tbl_satisfaccion_archivos'])
                        ->where(['=','tbl_satisfaccion_archivos.id_satisfaccion',$varid_satis])
                        ->Scalar();
                         

                    ?>
                    <tr><i class="fa-sharp fa-solid fa-comment-pen"></i>
                        <td class="text-center"><label style="font-size: 9px;"><?php echo  $varid_satis; ?></label></td>
                        <td class="text-center"><label style="font-size: 9px;"><?php echo  $varid; ?></label></td>
                        <td class="text-center"><label style="font-size: 9px;"><?php echo  $varconcepto_mejora; ?></label></td>
                        <td class="text-center"><label style="font-size: 9px;"><?php echo  $varanalisis_causa; ?></label></td>
                        <td class="text-center"><label style="font-size: 9px;"><?php echo  $varaccion_seguir; ?></label></td>
                        <td class="text-center"><label style="font-size: 9px;"><?php echo  $varaccion; ?></label></td>
                        <td class="text-center"><label style="font-size: 9px;"><?php echo  $txtindicador; ?></label></td>        
                        <td class="text-center"><label style="font-size: 9px;"><?php echo  $varpuntaje_meta; ?></label></td>        
                        <td class="text-center"><label style="font-size: 9px;"><?php echo  $varpuntaje_actual; ?></label></td>        
                        <td class="text-center"><label style="font-size: 9px;"><?php echo  $varpuntaje_final; ?></label></td>                                
                        <td class="text-center"><label style="font-size: 9px;"><?php echo  $varresponsable_area; ?></label></td>
                        <td class="text-center"><label style="font-size: 9px;"><?php echo  $txtRta ; ?></label></td>
                        <td class="text-center"><label style="font-size: 9px;"><?php echo  $varfecha_definicion; ?></label></td>
                        <td class="text-center"><label style="font-size: 9px;"><?php echo  $varfecha_implementacion; ?></label></td>
                        <td class="text-center">
                        <?= Html::a('<em class="fas fa-pencil-alt" style="font-size: 12px; color: #d95416;"></em>',  ['updatesatisfaccion','id_satisfaccion'=> $value['id_satisfaccion']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Editar']) ?>
                        </td>
                        <td class="text-center">
                            <?= Html::a('<em class="fas fa-plus-square" style="font-size: 12px; color: #d95416;"></em>',  ['agregarsatisfaccion','id_satisfac'=> $value['id_satisfaccion']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Agregar']) ?>                        
                        </td>

                        <td class="text-center">
                                    <?php if ($txtanexo != "") { ?>
                                        
                                        <div class="text-center">
                                        <?= 
                                            Html::a(Yii::t('app', '<em class="fas fa-check" style="font-size: 20px; color: #26cd33;" ></em>'),
                                                        'javascript:void(0)',
                                                        [
                                                            'title' => Yii::t('app', 'Ver Anexo'),
                                                            'onclick' => "
                                                                $.ajax({
                                                                    type     :'get',
                                                                    cache    : false,
                                                                    url  : '" . Url::to(['viewimage', 'varid'=> $varid_satis]) . "',
                                                                    success  : function(response) {
                                                                        $('#ajax_result').html(response);
                                                                    }
                                                                });
                                                            return false;",
                                            ]);
                                        ?>
                                    </div>
                                    <?php }else{ ?>
                                        <label><em class="fas fa-times" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', '') ?></label>
                                    <?php } ?>
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
<div id="tableid" class="clasetable" style="display: none;">
    <div class="row" >

        <div class="col-md-12">
        
            <div class="card1 mb">
                <table id="myTable2" class="table table-hover table-bordered" style="margin-top:20px" >
                <caption ><label><em class="fas fa-list" style="font-size: 14px; color: #b52aef;"></em> <?= Yii::t('app', 'Reporte Plan Satisfacción de Clientes') ?></label></caption>
                <thead>
                <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Id Satisfacción') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Área / Operación') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Concepto a Mejorar') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Análisis de Causas') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Acción a Seguir') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Acción') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Indicador') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Puntaje Meta %') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Puntaje Actual %') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Puntaje Final %') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Responsable') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Rol') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Fecha Definición Plan') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Fecha Implementación') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Estado') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Eficacia') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Fecha Elaboración') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Proceso') ?></label></th>                    
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Anexo') ?></label></th>
                    
                </thead>
                <tbody>
                    <?php
                   $varid = null;
                   //proceso para exportar a Excel
                                                                    
                    foreach ($varListasatisdetalle as $key => $value) {
                        $varid_satisfaccion = $value['id_satisfaccion'];
                        $varid_area_opera1 = $value['nombre'];
                        $varid_clientearea = $value['clientearea'];
                        $varconcepto_mejora = $value['concepto_mejora'];                        
                        $varanalisis_causa = $value['analisis_causa'];
                        $varaccion_seguir = $value['accion_seguir'];
                        $varaccion = $value['accion'];
                        $varid_indicador = $value['id_indicador'];
                        $varpuntaje_meta = $value['puntaje_meta'];
                        $varpuntaje_actual = $value['puntaje_actual'];
                        $varpuntaje_final = $value['puntaje_final'];
                        $varusua_nombre = $value['usua_nombre'];
                        $varfecha_definicion = $value['fecha_definicion'];
                        $varfecha_implementacion = $value['fecha_implementacion'];
                        $varEstado = $value['estado'];
                        $vareficacia = $value['eficacia'];
                        $varfecha_avance = $value['fecha_avance'];
                        $varnombreproceso = $value['nombre2'];
                        if($varEstado == 0){
                            $varEstado = 'Activo';
                        }else{
                            $varEstado = 'Inactivo';
                        }
                       
                        if ($varid_area_opera1){ 
                                       
                            $varid1 = $varid_area_opera1;
                            
                        }else{
                           
                            $varid1 = $varid_clientearea;
                            
                        }     
                        // Busca Rol
                        $txtRta = (new \yii\db\Query())
                        ->select(['tbl_usuarios_jarvis_cliente.posicion'])
                        ->from(['tbl_usuarios_jarvis_cliente'])
                        ->where(['=','tbl_usuarios_jarvis_cliente.idusuarioevalua',$varid_satisfaccion])
                        ->Scalar();
                        
                        // Busca indicador
                        $txtindicador = (new \yii\db\Query())
                        ->select(['tbl_indicadores_satisfaccion_cliente.nombre'])
                        ->from(['tbl_indicadores_satisfaccion_cliente'])
                        ->where(['=','tbl_indicadores_satisfaccion_cliente.id_indicador',$varid_indicador])
                        ->Scalar();
                         // Busca anexo
                         $txtanexo = (new \yii\db\Query())
                         ->select(['tbl_satisfaccion_archivos.anexo'])
                         ->from(['tbl_satisfaccion_archivos'])
                         ->where(['=','tbl_satisfaccion_archivos.id_satisfaccion',$varid_satisfaccion])
                         ->Scalar();

                    ?>
                    <tr><i class="fa-sharp fa-solid fa-comment-pen"></i>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varid_satisfaccion; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varid1; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varconcepto_mejora; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varanalisis_causa; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varaccion_seguir; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varaccion; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $txtindicador; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varpuntaje_meta; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varpuntaje_actual; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varpuntaje_final; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varusua_nombre; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $txtRta; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varfecha_definicion; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varfecha_implementacion; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varEstado; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $vareficacia; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varfecha_avance; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varnombreproceso; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $txtanexo; ?></label></td>
                        
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
                </table>
            </div>

        </div>

    </div>
    
    <?php ActiveForm::end(); ?>

</div>
<hr>
<?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
?>
<script type="text/javascript">
  function verificar(){
    var varidEquipo = document.getElementById("idTexto").text;

    if (varidEquipo == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Se debe llenar un Pilar","warning");
      return;
    }
  };

  $(document).ready( function () {
    $('#myTable').DataTable({
      responsive: true,
      fixedColumns: true,
      select: false,
      "language": {
        "lengthMenu": "Cantidad de Datos a Mostrar _MENU_ ",
        "zeroRecords": "No se encontraron datos ",
        "info": "Mostrando p&aacute;gina _PAGE_ a _PAGES_ de _MAX_ registros",
        "infoEmpty": "No hay datos aun",
        "infoFiltered": "(Filtrado un _MAX_ total)",
        "search": "Buscar:",
        "paginate": {
          "first":      "Primero",
          "last":       "Ultimo",
          "next":       "Siguiente",
          "previous":   "Anterior"
        }
      }
    });
  });
   var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta charset="utf-8"/><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64 = function (s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            }, format = function (s, c) {
                return s.replace(/{(\w+)}/g, function (m, p) {
                    return c[p];
                })
            }
        return function (table, name) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = {
                worksheet: name || 'Worksheet',
                table: table.innerHTML
            }
            console.log(uri + base64(format(template, ctx)));
            document.getElementById("dlink").href = uri + base64(format(template, ctx));
            document.getElementById("dlink").download = "Reporte Satisfacción de Clientes";
            document.getElementById("dlink").target = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('myTable2', 'Archivo Plan', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);
</script>