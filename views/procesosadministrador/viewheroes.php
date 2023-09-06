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

$this->title = 'Héroes - Descargar Listados';
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

  .card2 {
    height: 180px;
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
    background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
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
<!-- Data extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<script src="../../js_extensions/chart.min.js"></script>

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

<br>
<br>
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => [
            'autocomplete' => 'off'
        ]
    ]
  ]); 
?>

<!-- Capa Filtros de Busqueda -->
<div class="capaBuscar" id="capaIdBuscar" style="display: inline;">
  
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Procesos & Acciones') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Descargar Datos') ?></label>
                <a id="dlink" style="display:none;"></a>
                <button  class="btn btn-info" style="background-color: #4298B4" id="btn"><?= Yii::t('app', ' Descargar') ?></button>
            </div>
        </div>
    </div>    

</div>

<hr>

<!-- Capa Listado -->
<div class="capaListado" id="capaIdListado" style="display: none;">
  
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table id="tblListadoHeroesInvisible" class="table table-striped table-bordered tblResDetFreed">
                    <caption><?= Yii::t('app', ' Resultados de Héroes Valoradas...') ?></caption>
                    <thead>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Id') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha Valoración') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Programa/Pcrc') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Identificación Valorador') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Valorador') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Identificación Asesor') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Asesor') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Pregunta Del Analista') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Respuesta Analista') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Pregunta de la Operación') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Respuesta Operación') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cliente') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($varListadoHeroesValoraciones as $value) {
                      
                    ?>
                        <tr>
                            <td><label style="font-size: 11px;"><?php echo  $value['id']; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $value['created']; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $value['varProgramaPcrc']; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $value['varCCValorador']; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $value['varValorador']; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $value['varCCAsesor']; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $value['varAsesor']; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $value['Pregunta_Analista']; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $value['Respuesta_Analista']; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $value['Pregunta_Operacion']; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $value['Respuesta_Operacion']; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $value['Cliente']; ?></label></td>
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

<?php $form->end() ?>

<script type="text/javascript">
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
            document.getElementById("dlink").download = "Reporte de Valoración Heores";
            document.getElementById("dlink").target = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblListadoHeroesInvisible', 'Archivo ', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);
</script>