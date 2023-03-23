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

$this->title = 'Quejas y Reclamos';
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
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/qyr.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

    .redondo-primary {
        background: #337ab7;
        color: #fff;
        padding: 2px 0px;
        border-radius: 20px;
        /* font-size: 13px; */
        text-align: center;
        font-weight: bold;
        width:60px
    }

    .redondo-danger {
        background: red;
        color: #fff;
        padding: 2px 0px;
        border-radius: 20px;
        /* font-size: 13px; */
        text-align: center;
        font-weight: bold;
        width:60px
    }

    .redondo-success {
        background: #4298b4;
        color: #fff;
        padding: 2px 0px;
        border-radius: 20px;
        /* font-size: 13px; */
        text-align: center;
        font-weight: bold;
        width:70px
    }
    span {
        font-size:14px !important;
    }

    button.dt-button, div.dt-button, a.dt-button, input.dt-button{
        background-color:#4298b4 !important;
        color:#fff !important;
    }

    .text-center{
      
    align-items: center;
    flex-direction: row;
    justify-content:center;
    padding: 10px 5px !important;
    }

    label {
        font-size: 15px;
    }

</style>

<!-- datatable -->
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<!-- Extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<script src="../../js_extensions/chart.min.js"></script>

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
<br>
<br>
<div class="capaTres">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
        ]); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones & Gráficas') ?></label>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">                    
            <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Crear QyR') ?></label>                
                <?= Html::a('Aceptar',  ['crearqyr'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #337ab7',
                        'data-toggle' => 'tooltip',
                        'title' => 'Crear QyR'])
                ?>                                                                    
            </div>
        </div>
        <div class="col-md-6">
            <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Descargar QyR') ?></label>
                <a id="dlink" style="display:none;"></a>
                    <button  class="btn btn-info" style="background-color: #4298B4" id="btn">Aceptar</button>
            </div>
        </div>
       <br>                                                 
    </div>
    <hr>
    <?php ActiveForm::end(); ?>
</div>
<!-- Capa Grafica -->
<div id="capaIdGrafica" class="capaGrafica" style="display: inline;">

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Cantidades de Tipos de Estado') ?></label>
                <div id="containerA" class="highcharts-container" style="height: 150px;"></div> 
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Eficiencia') ?></label>
                <div id="containerB" class="highcharts-container" style="height: 150px;"></div> 
            </div>
        </div>
    </div>

</div>
<hr>
<div class="capaInformacion" id="capaIdInfo" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
                <caption><label><em class="fas fa-list" style="font-size: 20px; color: #b52aef;"></em> <?= Yii::t('app', 'Reporte Quejas y Reclamos') ?></label></caption>
                    <thead>
                        <tr>
                            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Id') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Id Caso ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Tipo de Dato ') ?></label></th>                            
                            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cliente ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Nombre Usuario ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Documento Usuario ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Correo Electrónico ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Área ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Tipología ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Comentarios ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Estado Proceso') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha Solicitudes ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Acción Ver ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Acción Gestionar ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Acción Eliminar ') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            foreach ($model as $key => $value) {
                                $varIdCaso = $value['idcaso'];
                                $varNumCaso = $value['numero_caso'];
                                $varTipoDato = $value['tipo_de_dato'];
                                $varComentarios = $value['comentario'];
                                $varCliente = $value['cliente'];
                                $varUsuario = $value['nombre'];
                                $varDocUsuario = $value['documento'];
                                $varEmail = $value['correo'];
                                $varArea = $value['area'];
                                $varTipologia = $value['tipologia'];
                                $varEstado = $value['estado'];
                                $varIdEstado = $value['idestado'];
                                $varFechaCreacion = $value['fecha_creacion'];
                                $varEstadoproceso = $value['id_estado'];                                
                                $varnombreestado = $value['estado1'];

                        ?>
                            <tr>
                                <td><label style="font-size: 12px;"><?php echo  $varIdCaso; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varNumCaso; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varTipoDato; ?></label></td>                                
                                <td><label style="font-size: 12px;"><?php echo  $varCliente; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varUsuario; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varDocUsuario; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varEmail; ?></label></td>                                
                                <td><label style="font-size: 12px;"><?php echo  $varArea; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varTipologia; ?></label></td>      


                                
                                
                                <td><label style="font-size: 12px;"><?php echo  $varComentarios; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varnombreestado; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  Yii::$app->formatter->asDate($varFechaCreacion); ?></label></td>
                                <td class="text-center">
                                    <?= Html::a('<em class="fas fa-search" style="font-size: 12px; color: #3e95b8;"></em>',  ['verqyr','idcaso'=> $value['idcaso']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Buscar']) ?>
                                </td>
                                <td class="text-center">
                                <?php
                                    if ($varEstadoproceso == 9) {
                                ?>                                    
                                        <?= Html::a('<em class="fas fa-pencil-alt" style="font-size: 12px; color: #43ba45;"></em>',  ['viewqyr','idcaso'=> $value['idcaso']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Gestionar']) ?>
                                <?php
                                    } elseif ($varEstadoproceso == 4) {
                                ?>                                                                  
                                        <?= Html::a('<em class="fas fa-pencil-alt" style="font-size: 12px; color: #43ba45;"></em>',  ['gestionqyr','idcaso'=> $value['idcaso']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Gestionar']) ?>
                                <?php
                                    } elseif ($varEstadoproceso == 8) {
                                ?>
                                        <?= Html::a('<em class="fas fa-pencil-alt" style="font-size: 12px; color: #43ba45;"></em>',  ['revisionqyr','idcaso'=> $value['idcaso']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Gestionar']) ?>
                                <?php
                                    } elseif (($varEstadoproceso == 5) && ($roles == 293 || $roles == 299 || $roles == 270) ) {
                                ?>
                                   <?= Html::a('<em class="fas fa-pencil-alt" style="font-size: 12px; color: #43ba45;"></em>',  ['revisiongerenteqyr','idcaso'=> $value['idcaso']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Gestionar']) ?>
                                <?php
                                    }else{ ?>                                    
                                        <label><em class="fas fa-times" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', '') ?></label>
                                <?php } ?>
                                
                                </td>
                                <td class="text-center">
                                    <?= Html::a('<em class="fas fa-trash-alt" style="font-size: 12px; color: #d95416;"></em>',  ['deleteqr','idcaso'=> $value['idcaso']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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
<script type="text/javascript">
    /*$(document).ready( function () {
        $('#myTable').DataTable({
            responsive: true,
            fixedColumns: true,
            dom: 'Bfrtip',
            buttons: [
                { 
                  extend: 'excel',
                  dom: 'Bfrtip',
                  text:'Exportar a excel',
                  className: 'btn btn-primary',
                  title:'Quejas-reclamos'
                } 
             ],
            select: true,
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
    });*/

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
            document.getElementById("dlink").download = "Reporte Quejas y Reclamos";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('myTable', 'Archivo Plan', name+'.xls')
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
                    foreach ($varCantidadestados as $key => $value) {
                        $varColor = null;

                        if ($value['id_estado'] == 2) {
                            $varColor = '#C148D0';
                        }
                        if ($value['id_estado'] == 4) {
                            $varColor = '#49de70';
                        }
                        if ($value['id_estado'] == 5) {
                            $varColor = '#FBCE52';
                        }
                        if ($value['id_estado'] == 7) {
                            $varColor = '#8B70FA';
                        }                        
                        if ($value['id_estado'] == 9) {
                            $varColor = '#22D7CF';
                        }
                ?>
                    {
                        name: "<?php echo $value['nombre'];?>",
                        y: parseFloat("<?php echo $value['Cantidad'];?>"),
                        color: "<?php echo $varColor; ?>",
                        dataLabels: {
                            enabled: false
                        }
                    },
                <?php 
                    }
                ?>
                        
            ]
        }]
    });

    Highcharts.chart('containerB', {
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
                $varvalor = '';          
                    foreach ($varCantidadtranscurre as $key => $value) {
                        $varColor = null;

                        if ($value['num'] == 1) {
                            $varColor = '#49de70';
                            $varvalor = '<= 5 días';
                        }
                        if ($value['num'] == 2) {
                            $varColor = '#F9BD4C';
                            $varvalor = '>5 y <8 días';
                        }
                        if ($value['num'] == 3) {
                            $varColor = '#f5500f';
                            $varvalor = '> 8 días';
                        }
                        
                ?>
                    {
                        name: "<?php echo $varvalor;?>",
                        y: parseFloat("<?php echo $value['canti'];?>"),
                        color: "<?php echo $varColor; ?>",
                        dataLabels: {
                            enabled: false
                        }
                    },
                <?php 
                    }
                ?>
                        
            ]
        }]
    });
</script>
