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

$this->title = 'Genesys - Buscar por Asesor';
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

    $varNombreAsesorGenesys = null;
    if ($varTmpEvaluadoId != "") {
      $varNombreAsesorGenesys = (new \yii\db\Query())
                                ->select(['tbl_evaluados.name'])
                                ->from(['tbl_evaluados'])
                                ->where(['=','tbl_evaluados.id',$varTmpEvaluadoId])
                                ->scalar();
    }

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
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Procesos Genesys - Buscar por Asesor') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    
    <div class="col-md-4">

      <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?>
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-address-card" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Datos de Búsqueda') ?></label>

        <?= $form->field($model, 'identificacion')->textInput(['id'=>'idAsesor', 'placeholder'=>'Ingresar identificación asesor'])->label('') ?> 

        <?=
            $form->field($model, 'fechacreacion', [
                                'labelOptions' => ['class' => 'col-md-12'],
                                'template' => 
                                '<div class="col-md-12"><div class="input-group">'
                                . '<span class="input-group-addon" id="basic-addon1">'
                                . '<i class="glyphicon glyphicon-calendar"></i>'
                                . '</span>{input}</div>{error}{hint}</div>',
                                'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                                'options' => ['class' => 'drp-container form-group']
                            ])->label('')->widget(DateRangePicker::classname(), [
                                'useWithAddon' => true,
                                'convertFormat' => true,
                                'presetDropdown' => true,
                                'readonly' => 'readonly',
                                'pluginOptions' => [
                                    'timePicker' => false,
                                    'format' => 'Y-m-d',
                                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                    'endDate' => date("Y-m-d"),
                                    'opens' => 'right',
            ]]);
        ?>

        <br>

        <?= Html::submitButton(Yii::t('app', 'Buscar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'varVerificar();',
                                'title' => 'Buscar Interacciones']) 
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


        <?php
          if ($varMensaje == 0) {
            
            if ($varDataList != null) {
             
        ?>          

          <table id="myTableInteracciones" class="table table-hover table-bordered" style="margin-top:10px" >
            <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Lista de Interacciones - '.$varNombreAsesorGenesys) ?></label></caption>
            <thead>
              <tr>
                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Url Genesys') ?></label></th>
              </tr>
            </thead>
            <tbody>
              <?php
                foreach ($varDataList as $key => $value) {    

                  $varNombreAsesor =  (new \yii\db\Query())
                                ->select(['tbl_evaluados.name'])
                                ->from(['tbl_evaluados'])
                                ->where(['=','tbl_evaluados.id',$value['evaluado_id']])
                                ->scalar();         
              ?>
                  <tr>
                    <td><label style="font-size: 12px;"><a href="<?php echo  $value['urlgenesys']; ?>" target="_blank"> <?= Yii::t('app', 'Abrir Enlace de Interacción -> '.$value['connid']) ?> </a></label></td>
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

        <?php
          if ($varMensaje == 1) {
            
        ?>
          
          <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 30px; color: #827DF9;"></em> <?= Yii::t('app', ' Actualmente el rango de fecha seleccionado esta fuera de los limites de la API Genesys de 7 dias. Por favor realizar nuevamente la consulta con rango de fecha no superior a 7 dias.') ?></label>
            
        <?php
          }
        ?>

        <?php
          if ($varMensaje == 2) {
            
        ?>

          <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 30px; color: #827DF9;"></em> <?= Yii::t('app', ' Actualmente el asesor '.$varNombreAsesorGenesys.' no tiene ningún tipo de interacción desde genesys en el rango de fechas seleccionado.') ?></label>

        <?php
          }
        ?>

        
      </div>
    </div>

  </div>

</div>

<hr>

<script type="text/javascript">

  function varVerificar(){
    var varIdAsesor = document.getElementById("idAsesor").value;
    var varFechas = document.getElementById("evaluados-fechacreacion").value;

    if (varIdAsesor == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Se debe de ingresar una identificación del asesor.","warning");
      return;
    }

    if (varFechas == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Se debe de ingresar un rango de fecha.","warning");
      return;
    }

  }

  $(document).ready( function () {
    $('#myTableInteracciones').DataTable({
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