<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\ReportesAdministracion;

$this->title = 'Administrar Reportes PBI';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Administrar Reportes PBI';

    $template = '<div class="col-md-3">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

$sessiones = Yii::$app->user->identity->id;
$txtIdPcrc = 1;
$txtareatrabajo = "";
$txtreporte= "";
$areatrab="prueba";
$listaworkspaces = json_decode(json_encode($listaworkspaces), true);
?>
 <style>
    #container_report{
      min-height:90vh;
    }
    #container_report iframe{
      min-height: 90vh;
      border: none !important;
      }
  </style>

<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
<style type="text/css">
    .card {
            height: 200px;
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

    .card2 {
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
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .card:hover .card1:hover {
        top: -15%;
    }

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Reporte-Power-BI.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
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
<div class="Principal">
  <div id="capaUno" style="display: inline">    
    <div id="dtbloque0" class="form-group col-sm-12" style="display: inline;">
      <div class="primeralinea">
        <div class="row">
          <div class="col-md-6">
              <div class="card mb">         
                <label><em class="far fa-edit" style="font-size: 20px; color: #559FFF;"></em> &Aacuterea de trabajo:</label>
                <br>
                <label>Seleccione Nombre:</label>
                <select class ='form-control' id="txtAreatrabajos" data-toggle="tooltip" title="Area de trabajo" onchange="nombreareatrab();">
                              <option value="" disabled selected>Seleccionar...</option>  
                               <?php                          
                                    foreach ($listaworkspaces as $key => $value) {
                                       echo "<option value = '".$value['id']."'>".$value['name']."</option>";
                                    }
                                ?>
                </select>
                <br>
                        
                <div class="row" style="text-align:center;">      
                    <div  class="btn btn-primary"  method='post' id="botones2"  onclick="listarep();">
                        Buscar Reporte
                    </div>    
                </div>
                
              </div>
          </div>
          <div class="col-md-6">
              <div class="card mb">
                <label><em class="far fa-chart-bar" style="font-size: 20px; color: #ed346f;"></em> &Aacuterea de reportes:</label>
                <br>
                <label>Seleccione reporte:</label>
                <select class ='form-control' id="txtReportes" data-toggle="tooltip" title="Reportes" align="center" disabled>
                               <option value="" disabled selected>Seleccionar...</option>                                         
                </select>
                <br>    
                <div class="row" style="text-align:center;">      
                  <div  class="btn btn-primary"  method='post' id="botones2" onclick="generarepor();">
                         Generar Reporte
                  </div>    
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <br>
<?php if ($sessiones == "2953" || $sessiones == "3229" || $sessiones == "3205" || $sessiones == "2991" || $sessiones == "4457" || $sessiones == "565" || $sessiones == "6639" || $sessiones == "6636" || $sessiones == "7952") {?>
  
  <br>
  <h3>&nbsp;</h3>
  <div id="capaDos" style="display: inline">    
    <div id="dtbloque1" class="form-group col-sm-12" style="display: inline;">
      <div class="segundalinea">
        <div class="row">
          <div class="col-md-2">
              <div class="card mb">            
                <label><em class="far fa-plus-square" style="font-size: 20px; color: #559FFF;"></em> Crear workspace:</label>
                <br>
                <br>
                <?= Html::button('Crear WorKSpace', ['value' => url::to('crearworkspace'), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Crear area de trabajo', 'style' => 'background-color: #337ab7']) 
                          ?> 

                          <?php
                            Modal::begin([
                                  'header' => '<h4>Creaci�n de WorkSpace</h4>',
                                  'id' => 'modal1',
                                ]);

                            echo "<div id='modalContent1'></div>";
                                                      
                            Modal::end();                             
                          ?>
                
              </div>
          </div>
          <div class="col-md-2">
              <div class="card mb">          
                <label><em class="far fa-minus-square" style="font-size: 20px; color: #ed3443;"></em> Elimina workspace:</label>
                <br>
                <br>
                <div  class="btn btn-primary"  method='post' id="botonelimws" onclick="eliminarws();">
                                    Eliminar WorKSpace
                          </div>
                          
                          <?php
                            Modal::begin([
                                  'header' => '<h4>Creacion de Reportes PBI </h4>',
                                  'id' => 'modal2',
                                ]);

                            echo "<div id='modalContent2'></div>";
                                                      
                            Modal::end(); 
                          ?>
                
              </div>
          </div>
          <div class="col-md-2">
              <div class="card mb">             
                <label><em class="far fa-copy" style="font-size: 20px; color: #edac34;"></em> Duplicar reporte:</label>
                <br>
                <br>
                <?= Html::button('Duplicar Reporte', ['value' => url::to(['duplicarreporte','nombrearea'=>$txtareatrabajo]), 'class' => 'btn btn-success', 'id'=>'modalButton5',
                              'data-toggle' => 'tooltip',
                              'title' => 'Duplicar reporte', 'style' => 'background-color: #5bc0de']) 
                          ?> 

                          <?php
                            Modal::begin([
                                  'header' => '<h4>Duplicar Reportes PBI</h4>',
                                  'id' => 'modal5',
                                ]);

                            echo "<div id='modalContent5'></div>";
                                                      
                            Modal::end(); 
                           ?>
                
              </div>
          </div>
          <div class="col-md-2">
              <div class="card mb">            
                <label><em class="far fa-file-excel" style="font-size: 20px; color: #ed5934;"></em> Eliminar reporte:</label>
                <br>
                <br>
                <div  class="btn btn-info"  method='post' id="botoneliminarep" onclick="eliminarrep();">
                          Eliminar Reporte
                </div>                
              </div>
          </div>
 <?php } ?>
<?php if ($sessiones == "2953" || $sessiones == "7" || $sessiones == "3205" || $sessiones == "2991" || $sessiones == "3468" || $sessiones == "3229" || $sessiones == "57"  || $sessiones == "4457" || $sessiones == "565" || $sessiones == "6639" || $sessiones == "6636" || $sessiones == "1475" || $sessiones == "7952") {?>

          <div class="col-md-2">
              <div class="card mb">            
                <label><em class="fas fa-unlock-alt" style="font-size: 20px; color: #0cf56d;"></em> Permisos reporte:</label>
                <br>
                <br>
                <div  class="btn btn-info"  method='post' id="botonpermisorep" onclick="Permisorep();">
                             Permisos de  Reporte
                </div>
              </div>
          </div>
<?php } ?>
<?php if ($sessiones == "2953" || $sessiones == "3229" || $sessiones == "3205" || $sessiones == "2991"  || $sessiones == "4457" || $sessiones == "565" || $sessiones == "6639" || $sessiones == "6636" || $sessiones == "1475" || $sessiones == "7952") {?>

          <div class="col-md-2">
              <div class="card mb">           
                <label><em class="far fa-id-badge" style="font-size: 20px; color: #ecf23a;"></em> Permisos colaborador:</label>
                <br>
                <div  class="btn btn-info"  method='post' id="botonpermisocolab" onclick="Permisocolab();">
                             Permisos Colaborador
                </div>
              </div>
          </div>          
        </div>
      </div>
    </div>
  </div> 
  <?php } ?>
</div>
<br>

<script type="text/javascript">

  $(document).ready(function () {
        $("#graficar0").click(function () {
            $("#dtbloque0").toggle("slow");
        });

        $("#graficar").click(function () {
            $("#dtbloque1").toggle("slow");
        });

        $("#graficar2").click(function () {
            $("#dtbloque2").toggle("slow");
        });
       
    });

    
    function listarep(){
      var varareatrabajoid = document.getElementById("txtAreatrabajos").value;
     if (varareatrabajoid == "") {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","Debe seleccionar un area de trabajo.","warning");
			return;
		  }else{
        $.ajax({
              method: "post",
              url: "get_reports_by_workspace",
              data : {
                workspace_id : varareatrabajoid,                
              },
              success : function(response){ 
                          var Rta =   JSON.parse(response);             
                          document.getElementById("txtReportes").innerHTML = "";
                          var node = document.createElement("OPTION");
                          node.setAttribute("value", "");
                          var textnode = document.createTextNode("Seleccionar...");
                          node.appendChild(textnode);
                          document.getElementById("txtReportes").appendChild(node);
                          for (var i = 0; i < Rta.data.length; i++) {
                              var node = document.createElement("OPTION");
                              node.setAttribute("value", Rta.data[i].id);
                              var textnode = document.createTextNode(Rta.data[i].name);
                              node.appendChild(textnode);
                              document.getElementById("txtReportes").appendChild(node);
                          }
                          document.getElementById("txtReportes").options[0].disabled = true;
			  var x=document.getElementById("txtReportes");
                          x.disabled=false;
                      }
          }); 
        }
    }

    function eliminarws(){
      var varareatrabajoid = document.getElementById("txtAreatrabajos").value;
     if (varareatrabajoid == "") {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","Debe seleccionar un area de trabajo.","warning");
			return;
		  }else{
        $.ajax({
              method: "post",
              url: "delete_workspace",
              data : {
                workspace : varareatrabajoid,                
              },
              success : function(response){ 
                          var Rta =   JSON.parse(response);    
                          console.log(Rta);
                          if (Rta == 1) {
                                    event.preventDefault();
							        	swal.fire("!!! Informacion !!!","Se elimino satisfactoriamente el area de trabajo.","success"); 
                        window.location.href='reporte';
                      }
              }
          }); 
        }
    }
    function eliminarrep(){
      var varareatrabajoid = document.getElementById("txtAreatrabajos").value;
      var varreporteid = document.getElementById("txtReportes").value;
      var tipo = 1;
      var new_name_report = "na"
     if (varareatrabajoid == "" || varreporteid == "") {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","Falta seleccionar un dato.","warning");
			return;
      }else{
        $.ajax({
              method: "post",
              url: "alter_report",
              data : { 
                tipo : tipo,
                workspace : varareatrabajoid,
                reporte : varreporteid,
                new_name_report : new_name_report,                 
              },
              success : function(response){ 
                          var Rta =   JSON.parse(response);    
                          console.log(Rta);
                          if (Rta.status = "1") {
                                    event.preventDefault();
							        	swal.fire("!!! Informacion !!!","Se elimino satisfactoriamente el reporte.","success"); 
                      }
              }
        }); 
      }
    }

    function nombreareatrab(){
      var varareatrab = document.getElementById("txtAreatrabajos").value;
    }

    function Permisorep(){
      var varareatrabajoid = document.getElementById("txtAreatrabajos").value;
      var varreporteid = document.getElementById("txtReportes").value;
      var lista = document.getElementById("txtReportes");
      var varnombrerep = lista.options[lista.selectedIndex].text;
     if (varareatrabajoid == "" || varreporteid == "") {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","Falta seleccionar un dato.","warning");
			return;
      }else{
        $.ajax({
              method: "post",
              url: "permisoreporteusua",
              data : {
                workspace : varareatrabajoid,
                reporte : varreporteid,                
              },
              success : function(response){ 
                          var Rta =   JSON.parse(response);    
                          console.log(Rta);
                          window.location.href='permisosreporte?model='+Rta+'&workspace='+varareatrabajoid+'&reporte='+varreporteid+'&nombrerepor='+varnombrerep;
              }
        }); 
      }
    }

    function Permisocolab(){
      var varareatrabajoid = document.getElementById("txtAreatrabajos").value;      
      var varreporteid = document.getElementById("txtReportes").value;
      var lista = document.getElementById("txtReportes");
      var varnombrerep = lista.options[lista.selectedIndex].text;
     if (varareatrabajoid == "") {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","Falta seleccionar el area de trabajo","warning");
			return;
      }else{
        $.ajax({
              method: "post",
              url: "search_workspace_contributors",
              data : {
                workspace : varareatrabajoid,        
              },
              success : function(response){ 
                          var Rta =   JSON.parse(response);
                          var rta2 = 1    
                          console.log(Rta);

                          window.location.href='permisocolabora?dataper='+JSON.stringify(Rta)+'&workspace='+varareatrabajoid+'&reporte='+varreporteid+'&nombrerepor='+varnombrerep;
              }
        }); 
      }
    }
    
    function generarepor(){
      var varareatrabajoid = document.getElementById("txtAreatrabajos").value;
      var listaarea = document.getElementById("txtAreatrabajos");
      var indice = listaarea.selectedIndex;
      var opcionSeleccionada = listaarea.options[indice];
      var varnombrearea = opcionSeleccionada.text;
      var varreporteid = document.getElementById("txtReportes").value;
      var lista = document.getElementById("txtReportes");
      var varnombrerep = lista.options[lista.selectedIndex].text;      
      
     if (varareatrabajoid == "" || varreporteid == "") {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","Falta seleccionar un dato.","warning");
			return;
      }else{
        $.ajax({
              method: "post",
              url: "search_report",
              data : {
                report_id : varreporteid,
                workspace_id : varareatrabajoid,
                reportname : varnombrerep,
                areaname : varnombrearea,
              },
              success : function(response){ 
                    var txtRta =   JSON.parse(response); 
                    var models = window['powerbi-client'].models;
  
                    var embedConfiguration = {
                      type: 'report',
                      id: varreporteid,
                      embedUrl: 'https://app.powerbi.com/reportEmbed',
                      tokenType: models.TokenType.Embed,
                      accessToken: txtRta.data
                    };
              
                    var $reportContainer = $('#container_report');
                    var report = powerbi.embed($reportContainer.get(0), embedConfiguration); 
                    $("#modal_report").modal("show");
                }
          }); 
      }  
    }

    
   
</script>
<!-- Modal -->
<div id="modal_report" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg" style=" margin-top: 11px !important; width: 98% !important; margin-bottom: 0px;">
    <!-- Modal content-->
    <div class="modal-content" style = "">
      <div class="modal-header" style = "padding-top: 4px !important; padding-bottom: 9px !important;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Reporte PBI</h4>
      </div>
      <div class="modal-body pa0" style="padding: 0px !important;" >
        <div id="container_report" style="min-height:90vh;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>

