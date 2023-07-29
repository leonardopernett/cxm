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

$this->title = 'Gestor Procesos GenesysCloud - Encuestas';
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
    height: 95px;
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
    background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
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

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<!-- Capa Ficha Procesos-->
<div id="capaIdFicha" class="capaFicha" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Procesos & Información') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Seleccionar Programa/Pcrc') ?></label>
        <?=
              $form->field($model, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                                    ->widget(Select2::classname(), [
                                        'language' => 'es',
                                        'options' => ['id'=>'idvarArbol','placeholder' => Yii::t('app', 'Select ...')],
                                        'pluginOptions' => [
                                            'allowClear' => false,
                                            'minimumInputLength' => 3,
                                            'ajax' => [
                                                'url' => \yii\helpers\Url::to(['formularios/getarbolesbyroles']),
                                                'dataType' => 'json',
                                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                            ],
                                        ]
                                            ]
              )->label('');
        ?>

        <br>

        <label style="font-size: 15px;"><em class="fas fa-calendar" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Seleccionar Rango de Fecha') ?></label>

        <?=
                $form->field($model, 'fechacreacion', [
                                'labelOptions' => ['class' => 'col-md-12'],
                                'template' => '<div class="col-md-12">{label}</div>'
                                . '<div class="col-md-12"><div class="input-group">'
                                . '<span class="input-group-addon" id="basic-addon1">'
                                . '<i class="glyphicon glyphicon-calendar"></i>'
                                . '</span>{input}</div>{error}{hint}</div>',
                                'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                                'options' => ['class' => 'drp-container form-group']
                ])->widget(DateRangePicker::classname(), [
                                'useWithAddon' => true,
                                'convertFormat' => true,
                                'presetDropdown' => true,
                                'readonly' => 'readonly',
                                'useWithAddon' => true,
                                'pluginOptions' => [
                                    'autoApply' => true,
                                    'clearBtn' => true,
                                    'timePicker' => false,
                                    'format' => 'Y-m-d',
                                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                    'endDate' => date("Y-m-d"),
                                    'opens' => 'left'
                                ],
                                'pluginEvents' => [
                                ]
                ])->label('');
        ?>
        
        <br>

        <?= Html::submitButton(Yii::t('app', 'Buscar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'varVerficar();',
                                'title' => 'Buscar Encuestas']) 
        ?>
        
      </div>

      <br>

      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?></label>
        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
        ?>
      </div>

      <?php
      if ($varGenerarLista == 1) {
        
      ?>

      <br>

      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Descarga Proceso') ?></label>
        <a id="dlink" style="display:none;"></a>
        <button  class="btn btn-info" style="background-color: #4298B4" id="btn"><?= Yii::t('app', ' Descargar') ?></button>
      </div>

      <?php
      }
      ?>

    </div>

    <div class="col-md-8">
      <div class="card1 mb">

        <?php
        if ($varGenerarLista == 0) {
          
        ?>
          <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', ' Listado de Encuestas') ?></label>
        <?php
        }else{
        ?>

          <?php
            if ($varGenerarLista == 2) {
              
          ?>
            <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #ff453c;"></em> <?= Yii::t('app', 'Importante: No se encontraron resultados para la búsqueda actual con los filtros anteriormente indicados.') ?></label>
          <?php
            }else{
          ?>
            <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', ' Listado de Encuestas') ?></label>
            <table id="tblDataEncuestas" class="table table-striped table-bordered tblResDetFreed">
              <caption><?= Yii::t('app', ' Resultados de '.$varNombreArbol.'...') ?></caption>
              <thead>
                <tr>
                  <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Connid') ?></label></th>
                  <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Nombre Asesor') ?></label></th>
                  <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Fecha & Hora Encuesta') ?></label></th>
                  <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Servicio GNS') ?></label></th>
                  <?php
                    foreach ($varPreguntas as $value) {                      
                  ?>
                    <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', $value['enunciado_pre']) ?></label></th>
                  <?php
                    }
                  ?>
                </tr>
              </thead>
              <tbody>
                <?php
                $varConteo = 0;
                foreach ($varArrayEncuestas as $value) {
                  $varNombreAgente = $value['AgentNames'];

                  // Se genera procesos para el detalle de Año, Mes, Dia y Hora
                  $varAnnioGNS = date("Y",strtotime($value['AddDates']));
                  $varMesGNS = date("m",strtotime($value['AddDates']));
                  $vardiaGNS = substr($value['AddDates'], 8, -14);

                  $varReplaceHour = str_replace("T", " ", $value['AddDates']);
                  $varHoraGNS = substr($varReplaceHour, 11, -11).date("is",strtotime($varReplaceHour));

                  $varTiempoInteraccion = $varAnnioGNS."-".$varMesGNS."-".$vardiaGNS." ".substr($varReplaceHour, 11, -11).date(":i:s",strtotime($varReplaceHour));

                  $varNombreCola = $value['QueueNames'];

                  $varConnid = $value['ConversationIDs'];

                ?>
                  <tr>
                    <td><label style="font-size: 11px;"><?php echo  " ".$varConnid; ?></label></td>
                    <td><label style="font-size: 11px;"><?php echo  " ".$varNombreAgente; ?></label></td>
                    <td><label style="font-size: 11px;"><?php echo  " ".$varTiempoInteraccion; ?></label></td>
                    <td><label style="font-size: 11px;"><?php echo  " ".$varNombreCola; ?></label></td>
                    <?php
                    for ($i=0; $i < count($varPreguntas); $i++) { 
                      $varSumatoria = $i + 1;
                    ?>
                    <td><label style="font-size: 11px;"><?php echo  " ".$value['Answer'.$varSumatoria.'s']; ?></label></td>
                    <?php
                    }
                    ?>
                  </tr>
                <?php
                }
                ?>
              </tbody>
            </table>

        <?php
            }
          }
        ?>
      </div>
    </div>
  </div>

</div>

<hr>


<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $(document).ready( function () {
        $('#tblDataEncuestas').DataTable({
          responsive: true,
          fixedColumns: true,
          select: true,
          "language": {
            "lengthMenu": "Cantidad de Datos a Mostrar _MENU_",
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
            document.getElementById("dlink").download = "Gestor Encuestas GNS - CXM";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblDataEncuestas', 'Archivo Encuestas Masivo GNS - CXM', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);


    function varVerficar(){
      var varidvarArbol = document.getElementById("idvarArbol").value;
      var varfecha = document.getElementById("genesysformularios-fechacreacion").value;

      if (varidvarArbol == "") {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Debe de seleccionar el Programa/Pcrc","warning");
        return;
      }
      if (varfecha == "") {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Debe de seleccionar un rango de fecha","warning");
        return;
      }
    }
</script>