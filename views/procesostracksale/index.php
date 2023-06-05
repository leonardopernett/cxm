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

$this->title = 'Gestor Procesos TrackSale - Index';
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
    background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>

<!-- Extensiones -->
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
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
<!-- Capa Principal -->
<div class="capaPrincipal" id="capaIdPrincipal" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">

        <div class="row">
          <div class="col-md-6">
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
          </div>

          <div class="col-md-6">
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
          </div>
        </div>
        
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-3">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Buscar Encuestas TrackSale') ?></label>

        <?= Html::submitButton(Yii::t('app', 'Buscar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'varVerficar();',
                                'title' => 'Buscar Encuestas']) 
        ?> 
      </div>
    </div>

    <div class="col-md-3">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Exportar Información') ?></label>

        <a id="dlink" style="display:none;"></a>
        <button  class="btn btn-info"  id="btn"><?= Yii::t('app', ' Exportar') ?></button>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Nueva Consulta') ?></label>

        <?= Html::a('Nuevo',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Nueva Consulta']) 
        ?>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Verificar Servicios TrackSale') ?></label>
        <?= Html::button('Verificar', ['value' => url::to(['verificartracksale']), 'class' => 'btn btn-success', 'id'=>'modalButton',
                                'data-toggle' => 'tooltip',
                                'title' => 'Verificar Servicios TrackSale']) 
        ?> 

        <?php
          Modal::begin([
            'header' => '<h4>Lista de Servicios TrackSale</h4>',
            'id' => 'modal',
            'size' => 'modal-lg',
          ]);

          echo "<div id='modalContent'></div>";
                                                                                                  
          Modal::end(); 
        ?>
      </div>

    </div>

    
  </div>

</div>

<hr>

<!-- Capa Principal -->
<div class="capaPrincipal" id="capaIdPrincipal" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Reporteria '.$varNombreArbol) ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">

        <?php
          if ($varListaDatos == null) { 
        ?>
          <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Resultado Encuestas') ?></label>
        <?php
          }else{
        ?>

        <?php
          if ($varListaDatos == "NA") {            
        ?>  
          <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Encuestas no encontradas para el servicio '.$varNombreArbol.' en el rango de fecha indicado. Por favor realizar nueva búsqueda.') ?></label>
        <?php
          }else{           
        ?>  

          <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Resultado Encuestas') ?></label>
          <table id="tblDataTrackSale" class="table table-striped table-bordered tblResDetFreed">
            <caption><?= Yii::t('app', 'Resultados de '.$varNombreArbol) ?></caption>
            <thead>
              <tr>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Id') ?></label></th>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Campaña') ?></label></th>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Codigo Compaña') ?></label></th>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Cliente') ?></label></th>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Email') ?></label></th>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Mes') ?></label></th>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Nombre de Atendimiento') ?></label></th>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Fecha Operación') ?></label></th>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Código Cliente') ?></label></th>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Ciudad') ?></label></th>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Pais') ?></label></th>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Asesor') ?></label></th>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Comentarios') ?></label></th>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Encuesta en CXM') ?></label></th>
              </tr>
            </thead>
            <tbody>
              <?php
                foreach ($varListaDatos as $value) {      
                  $varTimes = $value['time'];
                  $varExisteConnid = (new \yii\db\Query())
                          ->select(['tbl_base_satisfaccion.connid'])
                          ->from(['tbl_base_satisfaccion']) 
                          ->where(['=','tbl_base_satisfaccion.connid','000000'.$varTimes])
                          ->count();          
              ?>
                <tr>
                  <td><label style="font-size: 11px;"><?php echo  " ".$value['id']; ?></label></td>
                  <td><label style="font-size: 11px;"><?php echo  " ".$value['campaign_name']; ?></label></td>
                  <td><label style="font-size: 11px;"><?php echo  " ".$value['campaign_code']; ?></label></td>
                  <td><label style="font-size: 11px;"><?php echo  " ".$value['name']; ?></label></td>
                  <td><label style="font-size: 11px;"><?php echo  " ".$value['email']; ?></label></td>
                  <td><label style="font-size: 11px;"><?php echo  " ".$value['tags'][0]['value']; ?></label></td>
                  <td><label style="font-size: 11px;"><?php echo  " ".$value['tags'][14]['value']; ?></label></td>
                  <td><label style="font-size: 11px;"><?php echo  " ".$value['tags'][13]['value']; ?></label></td>
                  <td><label style="font-size: 11px;"><?php echo  " ".$value['tags'][11]['value']; ?></label></td>
                  <td><label style="font-size: 11px;"><?php echo  " ".$value['tags'][2]['value']; ?></label></td>
                  <td><label style="font-size: 11px;"><?php echo  " ".$value['tags'][1]['value']; ?></label></td>
                  <td><label style="font-size: 11px;"><?php echo  " ".$value['nps_answer']; ?></label></td>
                  <td><label style="font-size: 11px;"><?php echo  " ".$value['nps_comment']; ?></label></td>
                  <td class="text-center">
                    <?php
                      if ($varExisteConnid != 0) {
                    ?>                      
                      <label><em class="fas fa-check" style="font-size: 20px; color: #26cd33;"></em><?= Yii::t('app', '') ?></label>                      
                    <?php
                      }else{
                    ?>                      
                      <label><em class="fas fa-times" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', '') ?></label>                      
                    <?php
                      }
                    ?>
                  </td>
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
  function varVerficar(){
    var varidvarArbol = document.getElementById("idvarArbol").value;
    var varfecha = document.getElementById("tracksaleparametrizarformulario-fechacreacion").value;

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
  };

  $(document).ready( function () {
        $('#tblDataTrackSale').DataTable({
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
            document.getElementById("dlink").download = "Seguimiento Encuestas TrackSale";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
  })();
  
  function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblDataTrackSale', 'Archivo Seguimiento', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

  };
  var btn = document.getElementById("btn");
  btn.addEventListener("click",download);

</script>
