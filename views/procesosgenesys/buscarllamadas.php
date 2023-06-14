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

$this->title = 'Gestor Procesos GenesysCloud - Llamadas';
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
        <label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Seleccionar Asesor') ?></label>
        <?=
                    $form->field($model, 'nombre_asesor', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['id'=>'idAsesor','placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'multiple' => false,
                            'allowClear' => true,
                            'minimumInputLength' => 4,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['reportes/evaluadolistmultiple']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['evaluadolistmultiple']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                        ]
                            ]
            );
            ?>


        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Seleccionar Programa/Pcrc') ?></label>
        <?=
              $form->field($model, 'documento_asesor', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
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


        <label style="font-size: 15px;"><em class="fas fa-calendar" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Seleccionar Rango de Fechas') ?></label>
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
            <table id="tblDataLlamadas" class="table table-striped table-bordered tblResDetFreed">
              <caption><?= Yii::t('app', 'Resultados '.$varNombreArbol) ?></caption>
              <thead>
                <tr>
                  <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Id Genesys') ?></label></th>
                  <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Nombre Asesor') ?></label></th>
                  <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Url Interacción') ?></label></th>
                  <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 12px;"><?= Yii::t('app', 'Tipo Interacción') ?></label></th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach ($varArrayllamadas as $key => $value) {
                  $urlGns = 'https://apps.mypurecloud.com/directory/#/engage/admin/interactions/'.$value['varConnid'];
                ?>
                  <tr>
                    <td><label style="font-size: 11px;"><?php echo  " ".$varIdGenesys; ?></label></td>
                    <td><label style="font-size: 11px;"><?php echo  " ".$varNombreAsesor; ?></label></td>
                    <td><label style="font-size: 12px;"><a href="<?php echo  $urlGns; ?>" target="_blank"> <?= Yii::t('app', 'Abrir Enlace de Interacción -> '.$value['varConnid']) ?> </a></label></td>
                    <td><label style="font-size: 11px;"><?php echo  " ".$value['varOrigen']; ?></label></td>
                  </tr>
                <?php
                }
                ?>
              </tbody>
            </table>

          <?php
            }
          ?>

        <?php
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
        $('#tblDataLlamadas').DataTable({
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

  function varVerficar(){
    var varidvarAsesor = document.getElementById("idAsesor").value;
    var varidvarArbol = document.getElementById("idvarArbol").value;
    var varfecha = document.getElementById("genesysparametroasesor-fechacreacion").value;

    var varFechasJS = document.getElementById("genesysparametroasesor-fechacreacion").value;
    var varArrayFechas = varFechasJS.split(" ",10);
    var varFechaInicios = new Date(varArrayFechas[0]);
    var varFechaFines = new Date(varArrayFechas[2]);

    var varDiferencia = varFechaFines.getTime() - varFechaInicios.getTime();
    var varDias = varDiferencia / 1000 / 60 / 60 / 24;

    if (varidvarAsesor == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de seleccionar el asesor","warning");
      return;
    }
    if (varidvarArbol == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de seleccionar el programa/pcrc","warning");
      return;
    }
    if (varfecha == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de seleccionar un rango de fecha","warning");
      return;
    }

    if (varFechasJS != "") {
      if (varDias > 7) {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Rango de días seleccionado supera los 7 dias. Por favor ingresar nuevo rango de fecha menor a 7 días.","warning");
        return;
      }
    }
  }
</script>