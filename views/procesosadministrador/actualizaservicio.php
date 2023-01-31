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

$this->title = 'Genesys - Actualizar Programa/Pcrc';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from(['tbl_roles'])
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where(['=','tbl_usuarios.usua_id',$sessiones]);                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $valor = null;  

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
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">

<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>

<!-- Capa Procesos del encabezado -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>

<!-- Capa Busqueda y Resultados -->
<div class="capaProcesos" style="display: inline;" id="capaProcesosId">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Procesos Asignar Programa/Pcrc - Genesys') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    
    <div class="col-md-4">

      <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?>
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-address-card" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Seleccionar Programa/Pcrc') ?></label>

        <?=
          $form->field($model, 'arbol_id')
            ->widget(Select2::classname(), [                
              'language' => 'es',
              'options' => ['id'=>'idselectarbol','placeholder' => Yii::t('app', 'Select ...')],
              'pluginOptions' => [
                'initialize' => true,
                'allowClear' => true,
                'minimumInputLength' => 3,
                'ajax' => [
                  'url' => \yii\helpers\Url::to(['basesatisfaccion/getarbolesbypcrc']),
                  'dataType' => 'json',
                  'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                  'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                ],
                'initSelection' => new JsExpression('function (element, callback) {
                  var id=$(element).val();
                  if (id !== "") {
                    $.ajax("' . $valor . '" + id, {
                      dataType: "json",
                      type: "post"
                    }).done(function(data) { callback(data.results[0]);});
                  }
                }')
              ]
            ]
            )->label('');
        ?> 

        <br>

        <label style="font-size: 15px;"><em class="fas fa-cog" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Ingresar Cola Genesys') ?></label>
        <?= $form->field($model, 'pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'id_TextColaGenesys','placeholder'=>'Ingresar Nombre Cola Genesys']) ?> 

        <br>

        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Ingresar Id Cola Genesys') ?></label>
        <?= $form->field($model, 'comentarios', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'id_ColaGenesys','placeholder'=>'Ingresar Id Cola Genesys']) ?>       
        

        <br>

        <?= Html::submitButton(Yii::t('app', 'Guardar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'varVerificar();',
                                'title' => 'Guardar Programa/Pcrc']) 
        ?>



      </div>
      <?php $form->end() ?>      

      <br>

      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Cancelar y Regresar') ?></label>
        <?= Html::a('Regresar',  ['admingenesys'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
        ?>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card1 mb">


        <table id="myTableInfo" class="table table-hover table-bordered" style="margin-top:10px" >
          <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Lista de Programas/Pcrc ') ?></label></caption>
          <thead>
            <tr>
              <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Programa/Pcrc - CXM') ?></label></th>
              <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nombre Cola - Genesys') ?></label></th>
              <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($varLisArbolid as $key => $value) {    

                  $varNombrepcrc =  (new \yii\db\Query())
                                ->select(['tbl_arbols.name'])
                                ->from(['tbl_arbols'])
                                ->where(['=','tbl_arbols.id',$value['arbol_id']])
                                ->scalar();         
            ?>
                  <tr>
                    <td><label style="font-size: 12px;"> <?= Yii::t('app', $varNombrepcrc) ?> </label></td>
                    <td><label style="font-size: 12px;"> <?= Yii::t('app', $value['cola_genesys']) ?> </label></td>
                    <td class="text-center">
                      <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deletegenesysarbol','id'=> $value['id_genesysformularios']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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

  function varVerificar(){
    var varServicio = document.getElementById("idselectarbol").value;
    var vartextogenesys = document.getElementById("id_TextColaGenesys").value;
    var varidgenesys = document.getElementById("id_TextColaGenesys").value;

    if (varServicio == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Se debe de seleccionar un programa/pcrc.","warning");
      return;
    }
    if (vartextogenesys == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Se debe de ingresar el nombre de la cola del servicio de genesys.","warning");
      return;
    }
    if (varidgenesys == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Se debe de id de genesys sobre la cola del servicio","warning");
      return;
    }


  }

  $(document).ready( function () {
    $('#myTableInfo').DataTable({
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

</script>