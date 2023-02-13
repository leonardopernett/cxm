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
use app\models\SpeechCategorias;
use app\models\Dashboardservicios;

$this->title = 'Procesos Administrador - Glosario';
$this->params['breadcrumbs'][] = $this->title;

$sesiones =Yii::$app->user->identity->id;   

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';


$rol =  new Query;
$rol    ->select(['tbl_roles.role_id'])
        ->from('tbl_roles')
        ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
        ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
        ->where('tbl_usuarios.usua_id = '.$sesiones.'');                    
$command = $rol->createCommand();
$roles = $command->queryScalar();

$vartotalAuto = 0;

$varTipoCategoria = (new \yii\db\Query())
        ->select(['tipocategoria, count(tipocategoria) as cantidad'])
        ->from(['tbl_speech_glosario'])
        ->where(['=','anulado',0])        
        ->groupBy('tipocategoria');          

?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
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

    .card2 {
            height: 170px;
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
        background-image: url('../../images/Dashboard-Escuchar-+.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

    .lds-ring {
      display: inline-block;
      position: relative;
      width: 100px;
      height: 100px;
    }
    .lds-ring div {
      box-sizing: border-box;
      display: block;
      position: absolute;
      width: 80px;
      height: 80px;
      margin: 10px;
      border: 10px solid #3498db;
      border-radius: 70%;
      animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
      border-color: #3498db transparent transparent transparent;
    }
    .lds-ring div:nth-child(1) {
      animation-delay: -0.45s;
    }
    .lds-ring div:nth-child(2) {
      animation-delay: -0.3s;
    }
    .lds-ring div:nth-child(3) {
      animation-delay: -0.15s;
    }
    @keyframes lds-ring {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
    }

</style>
<!-- datatable -->
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >

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
            <label style="font-size: 20px; color: #FFFFFF;">Ficha T&eacute;cnica - <?php echo $varNombreCliente; ?>  - <?php echo $varNombrePcrc; ?> </label>
            </div>
        </div>
    </div>

    <br> 
    <div class="row">   
        <div class="col-md-4">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #b52aef;"></em> <?= Yii::t('app', 'Descargar Glosario') ?></label> 
                <a id="dlink" style="display:none;"></a>
                <button  class="btn btn-info" style="background-color: #337ab7" id="btn" rel="stylesheet" type="text/css" title="Descagar Glosario"><?= Yii::t('app', 'Descargar Glosario') ?></button>
            </div>
        
            <hr>

            <div class="card1 mb">
            <table style="width:100%">
                  <th scope="col" class="text-center" style="width: 100px;">
                    <label style="font-size: 15px;"><em class="fas fa-chart-pie" style="font-size: 15px; color: #559FFF;"></em> <?= Yii::t('app', 'Grafica Tipos de Categoria') ?></label>
                      <div id="containerA" class="highcharts-container" style="height: 150px;"></div> 
                  </th>
            </table>
            </div>

            <hr>
            
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-calendar" style="font-size: 15px; color: #b52aef;"></em> <?= Yii::t('app', 'Última Fecha Actualización:') ?></label> 
                <label  style="font-size: 20px; text-align: center;"><?= Yii::t('app', $varfechaMax) ?></label>
            </div>

        </div>
        
        <div class="col-md-8"><!-- inicio del div de la tarjeta de tamaño 8 -->
            <div class="card1 mb"><!-- div de la carta principal donde van a ir la informacion ---( me pone la tarjeta)-------------------> 

                <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" ><!--Titulo de la tabla no se muestra-->
                    <caption><label><em class="fas fa-list" style="font-size: 20px; color: #b52aef;"></em> <?= Yii::t('app', 'Glosario') ?></label></caption><!--Titulo de la tabla si se muestra-->
                     <thead><!--Emcabezados de la tabla -->
                    <th scope="col" class="text-center" style="background-color: #6b97b1"><label style="font-size: 15px; width:140px; color:white;" ><?= Yii::t('app', 'Tipo Categoria') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #6b97b1;"><label style="font-size: 15px; width:140px; color:white;"><?= Yii::t('app', 'Marca/Canal/Agente') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #6b97b1;"><label style="font-size: 15px; width:140px; color:white;"><?= Yii::t('app', 'Nombre de la Categoria') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #6b97b1;"><label style="font-size: 15px; width:10; color:white;"><?= Yii::t('app', 'Descripción') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #6b97b1;"><label style="font-size: 15px; width:10; color:white;"><?= Yii::t('app', 'Variables y Ejemplos') ?></label></th>
                    </thead>
                    <tbody><!--Tbody de la tabla -->

                  
                
          
                    <?php
                    foreach ($varData as $key => $value) {

                    ?>

                    <tr><!--Filas de la tabla -->
                    <td class="text-center"><label style="font-size: 12px;"><?php echo $value['tipocategoria']; ?></label></td>
                    <td class="text-center"><label style="font-size: 12px;"><?php echo $value['marca_canal_agente']; ?></label></td>
                    <td class="text-center"><label style="font-size: 12px;"><?php echo $value['nombrecategoria']; ?></label></td>
                    <td class="text-center"><label style="font-size: 12px;"><?php echo $value['descripcioncategoria']; ?></label></td>
                    <td class="text-center"><label style="font-size: 12px;"><?php echo $value['variablesejemplos']; ?></label></td>
                    </tr>
                    <?php  }   ?>  
                
          
                    </tbody><!--fin Tbody de la tabla -->
                </table><!--fin  de la tabla -->
            </div>   
        </div>
    </div><!--fin de la div donde esta el tamaña de la tarjeta -->
</div>
<hr>

<?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
?>
<script type="text/javascript">

    function regresar(){
        var varidcliente = "<?php echo $varidcliente; ?>";        
        
    window.location.href='indexvoice?txtCodPcrcs='+varidcliente;
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
        "infoEmpty": "No hay datos aún",
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
            document.getElementById("dlink").download = "Glosario";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('myTable', 'Plantilla_Glosario', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download); 
    

        Highcharts.chart('containerA', {
                 chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: 0,
                    plotShadow: false
                },
                title: {
                    text: '<label style="font-size: 20px;"><?php echo ''; ?></label>',
                    align: 'center',
                    verticalAlign: 'middle',
                    y: 60
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        dataLabels: {
                            enabled: true,
                            distance: -50,
                            style: {
                                fontWeight: 'bold',
                                color: 'white'
                            }
                        },
                    startAngle: -90,
                    endAngle: 90,
                    center: ['50%', '110%'],
                    size: '220%',
                    width: '200%'
                    }
                },
                series: [{
                    type: 'pie',
                    name: '',
                    innerSize: '50%',
                    data: [
                        <?php                         
                        foreach($varTipoCategoria->each() as $categoria){?>
                        {
                            name: "<?php echo $categoria['tipocategoria'];?>",
                            y: parseFloat("<?php echo $categoria['cantidad'];?>"),                            
                            dataLabels: {
                                enabled: false
                            }
                        },
                        <?php }?>
                        
                    ]
                }]
            });
            
</script>
