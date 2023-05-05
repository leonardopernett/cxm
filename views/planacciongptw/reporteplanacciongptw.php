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

$this->title = 'Reporte Plan de acción GPTW';
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
        background-image: url('../../images/GPTW_Banner 2.jpg');
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
        <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Reporte Plan de acción GPTW Abiertos"; ?> </label>
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
                                    <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
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
                <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
                <caption><label><em class="fas fa-list" style="font-size: 20px; color: #b52aef;"></em> <?= Yii::t('app', 'Reporte Plan de acción GPTW Abiertos') ?></label></caption>
                <thead>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Id GPTW') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Área / Operación') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Foco de Mejora') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Detalle mejora') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Puntaje Actual') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Puntaje Meta') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Acciones cierre Brecha') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Fecha Registro') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Fecha Cierre') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Respons. Área') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Estado') ?></label></th> 
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Acción Editar') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 14px;"><?= Yii::t('app', 'Acción Seguimiento') ?></label></th>
                </thead>
                <tbody>
                    <?php
                   $varid = null;
                    foreach ($varListaplangptw as $key => $value) {
                        $varid_gptw = $value['id_gptw'];
                        $varid_area_opera = $value['id_operacion'];
                        $varid_area_apoyo = $value['id_area_apoyo'];
                        $varid_pilares = $value['id_pilares'];
                        $varid_detallepilares = $value['id_detalle_pilar'];
                        $varporcentaje_actual = $value['porcentaje_actual'];
                        $varEporcentaje_meta = $value['porcentaje_meta'];
                        $varacciones = $value['acciones'];
                        $varfecha_registro = $value['fecha_registro'];
                        $varfecha_avance = $value['fecha_avance'];
                        $varfecha_cierre = $value['fecha_cierre'];
                        $varobservaciones = $value['observaciones'];
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
                            ->select(['tbl_proceso_cliente_centrocosto.cliente'])
                            ->from(['tbl_proceso_cliente_centrocosto'])
                            ->where(['=','tbl_proceso_cliente_centrocosto.idvolumendirector',$varid_area_opera])
                            ->Scalar();
                        $varid = $data3;                           
                            
                        }
                        // busca el foco
                         
                         $nombre_p = null;
                         $varid_pilares2 = explode(",", $varid_pilares);
                         
                         for ($i = 0; $i< count($varid_pilares2); $i++) {
                             $element = $varid_pilares2[$i];
                             $datanew = (new \yii\db\Query())
                             ->select(['nombre_pilar'])
                             ->from(['tbl_pilares_gptw'])
                             ->where(['in','id_pilares',[$element]])
                             ->Scalar();
                             $nombre_p = $nombre_p . $datanew . ', ';
                             
                         }
                        
                         $nombre_p = substr($nombre_p, 0, -2);
                         // busca el detalles pilar
                         
                         $nombredetalle_p = null;
                         $varid_detallepilares2 = explode(",", $varid_detallepilares);
                         
                         for ($i = 0; $i< count($varid_detallepilares2); $i++) {
                             $element = $varid_detallepilares2[$i];
                             $datanew = (new \yii\db\Query())
                             ->select(['nombre'])
                             ->from(['tbl_detalle_pilaresgptw'])
                             ->where(['in','id_detalle_pilar',[$element]])
                             ->Scalar();
                             $nombredetalle_p = $nombredetalle_p . $datanew . ', ';
                             
                         }
                        
                         $nombredetalle_p = substr($nombredetalle_p, 0, -2);
                         $varParamsCodigo = [':txtIdpilar'=>$varid_pilares];
                         $datanew = Yii::$app->db->createCommand("Select tbl_pilares_gptw.nombre_pilar
                            FROM tbl_pilares_gptw WHERE tbl_pilares_gptw.id_pilares in(:txtIdpilar) 
                            ")->bindValues($varParamsCodigo )->queryScalar();
                            
                            $varid_pilares = $datanew;

                            $data4 = (new \yii\db\Query())
                                    ->select(['usua_nombre'])
                                    ->from(['tbl_usuarios'])
                                    ->where(['=','usua_id',$varresponsable_area])
                                    ->Scalar();
                                    
                            $varresponsable_area = $data4;

                    ?>
                    <tr><em class="fa-sharp fa-solid fa-comment-pen"></em>
                        <td class="text-center"><label style="font-size: 11px;"><?php echo  $varid_gptw; ?></label></td>
                        <td class="text-center"><label style="font-size: 11px;"><?php echo  $varid; ?></label></td>
                        <td class="text-center"><label style="font-size: 11px;"><?php echo  $nombre_p; ?></label></td>
                        <td class="text-center"><label style="font-size: 11px;"><?php echo  $nombredetalle_p; ?></label></td>
                        <td class="text-center"><label style="font-size: 11px;"><?php echo  $varporcentaje_actual; ?></label></td>
                        <td class="text-center"><label style="font-size: 11px;"><?php echo  $varEporcentaje_meta; ?></label></td>
                        <td class="text-center"><label style="font-size: 11px;"><?php echo  $varacciones; ?></label></td>
                        <td class="text-center"><label style="font-size: 11px;"><?php echo  $varfecha_registro; ?></label></td>
                        <td class="text-center"><label style="font-size: 11px;"><?php echo  $varfecha_cierre; ?></label></td>
                        <td class="text-center"><label style="font-size: 11px;"><?php echo  $varresponsable_area; ?></label></td>
                        <td class="text-center"><label style="font-size: 11px;"><?php echo  $varEstado; ?></label></td>
                        <td class="text-center">
                        <?= Html::a('<em class="fas fa-pencil-alt" style="font-size: 15px; color: #FC4343;"></em>',  ['updatecargaplan','id_gptw'=> $value['id_gptw']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Editar']) ?>
                        </td>
                        <td class="text-center">
                            <?= Html::a('<em class="fas fa-plus-square" style="font-size: 15px; color: #FC4343;"></em>',  ['updateplan','id_gptw'=> $value['id_gptw']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Agregar']) ?>
                        
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
                <caption><label><em class="fas fa-list" style="font-size: 20px; color: #b52aef;"></em> <?= Yii::t('app', 'Reporte Plan de acción GPTW Abiertos') ?></label></caption>
                <thead>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Id GPTW') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Área / Operación') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Foco de Mejora') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Detalle mejora') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Puntaje Actual') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Puntaje Meta') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Acciones cierre Brecha') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha Registro') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha Avance') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Seguimiento') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Respons. Área') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Estado') ?></label></th> 
                </thead>
                <tbody>
                    <?php
                   $varid = null;
                   //proceso para exportar a Excel
                   $varid_detallepilares2 = null;
                                                                    
                    foreach ($varListaplangptwrep as $key => $value) {
                        $varid_gptw1 = $value['id_gptw'];
                        $varid_area_opera1 = $value['nombre'];
                        $varid_area_apoyo1 = $value['cliente'];
                        $varid_pilares1 = $value['id_pilares'];
                        $varid_detallepilares1 = $value['id_detalle_pilar'];                        
                        $varporcentaje_actual1 = $value['porcentaje_actual'];
                        $varEporcentaje_meta1 = $value['porcentaje_meta'];
                        $varacciones1 = $value['acciones'];
                        $varfecha_registro1 = $value['fecha_registro'];
                        $varfecha_avance1 = $value['fecha_avance'];
                        $varobservaciones1 = $value['observaciones'];
                        $varresponsable_area1 = $value['usua_nombre'];
                        if($varEstado == 0){
                            $varEstado = 'Activo';
                        }else{
                            $varEstado = 'Inactivo';
                        }
                       
                        if ($varid_area_opera1){ 
                                       
                            $varid1 = $varid_area_opera1;
                            
                        }else{
                           
                            $varid1 = $varid_area_apoyo1;
                            
                            
                        }
                        // busca el foco
                         
                         $nombre_p1 = null;
                         $varid_pilares2 = explode(",", $varid_pilares1);
                         
                         for ($i = 0; $i< count($varid_pilares2); $i++) {
                             $element = $varid_pilares2[$i];
                             $datanew = (new \yii\db\Query())
                             ->select(['nombre_pilar'])
                             ->from(['tbl_pilares_gptw'])
                             ->where(['in','id_pilares',[$element]])
                             ->Scalar();
                             $nombre_p1 = $nombre_p1 . $datanew . ', ';
                             
                         }
                         $nombre_p1 = substr($nombre_p1, 0, -2);
                         // busca el detalles pilar
                         
                         $nombredetalle_p1 = null;
                         $varid_detallepilares21 = explode(",", $varid_detallepilares1);
                         
                         for ($i = 0; $i< count($varid_detallepilares21); $i++) {
                             $element = $varid_detallepilares21[$i];
                             $datanew = (new \yii\db\Query())
                             ->select(['nombre'])
                             ->from(['tbl_detalle_pilaresgptw'])
                             ->where(['in','id_detalle_pilar',[$element]])
                             ->Scalar();
                             $nombredetalle_p1 = $nombredetalle_p1 . $datanew . ', ';
                             
                         }
                        
                         $nombredetalle_p1 = substr($nombredetalle_p1, 0, -2);
                         $varParamsCodigo = [':txtIdpilar'=>$varid_pilares1];
                        $datanew = Yii::$app->db->createCommand("Select tbl_pilares_gptw.nombre_pilar
                            FROM tbl_pilares_gptw WHERE tbl_pilares_gptw.id_pilares in(:txtIdpilar) 
                            ")->bindValues($varParamsCodigo )->queryScalar();
                            
                            $varid_pilares1 = $datanew;                      

                    ?>
                    <tr><em class="fa-sharp fa-solid fa-comment-pen"></em>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varid_gptw1; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varid1; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $nombre_p1; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $nombredetalle_p1; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varporcentaje_actual1; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varEporcentaje_meta1; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varacciones1; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varfecha_registro1; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varfecha_avance1; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varobservaciones1; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varresponsable_area1; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varEstado; ?></label></td>
                        
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
            document.getElementById("dlink").download = "Reporte Plan de accion GPTW";
            document.getElementById("dlink").traget = "_blank";
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