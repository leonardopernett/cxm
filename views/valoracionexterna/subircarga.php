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


$this->title = 'Gestor Valoraciones Externas';
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
    height: 355px;
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
    background-image: url('../../images/valoracionExt.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
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


<!-- Capa Principal -->
<?php $form = ActiveForm::begin(["method" => "post","enableClientValidation" => true,'options' => ['enctype' => 'multipart/form-data'],'fieldConfig' => ['inputOptions' => ['autocomplete' => 'off']]]) ?>


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

<!-- Capa Principal -->
<div id="capaIdPrincipal" class="capaPrincipal" style="display: inline;">
    
  <div class="row">
    <div class="col-md-4">
        <div class="card1 mb" style="background: #6b97b1; ">
            <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Ficha Tecnica   '.$varNombreArbol) ?></label>
        </div>
    </div>
  </div>
  <hr><br>
  <div class="row">
    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 20px; color: #C31CB4;"></em><?= Yii::t('app', ' Descargar Plantilla') ?></label>
        <a id="dlink" style="display:none;"></a><br>
        <button  class="btn btn-info" style="background-color: #4298B4" id="btn"><?= Yii::t('app', ' Descargar') ?></button>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card1 mb">
        <label><em class="fas fa-upload" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', '  Seleccionar archivo') ?></label>
          <?= $form->field($model, "file[]",['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->fileInput(['id'=>'idinput','multiple' => false])->label('') ?>
                
          <?= Html::submitButton("Subir", ["class" => "btn btn-primary"]) ?>
      </div>
    </div>
  
    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?></label><br>
          <?= Html::a('Cancelar y Regresar',  ['index'], ['class' => 'btn btn-success',
                                              'style' => 'background-color: #707372',
                                              'data-toggle' => 'tooltip',
                                              'title' => 'Regresar']) 
          ?>
        </div>
      </div>
    </div>
  <br><hr>

  <table id="tablaDescarga" hidden="hidden" class="table table-striped table-bordered tblResDetFreed">
    <caption><?= Yii::t('app',$varNombreArbol) ?></caption>
    <thead>
      <tr>
        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cédula Asesor') ?></label></th>
        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cédula Valorador') ?></label></th>
        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Campo Dimensión') ?></label></th>
        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Campo Fuente') ?></label></th>
        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Campo Score') ?></label></th>
        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Campo Comentarios') ?></label></th>
    

            <?php
            foreach ($varPreguntas as $value) {                      
          ?>
            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', $value['nameP']) ?></label></th>
          <?php
            }
          ?>

        </tr>
    </thead>
    <tbody>
      
      
    </tbody>
  </table>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

<script type="text/javascript">

function download() {
  $(document).find('tfoot').remove();

  var table = document.getElementById('tablaDescarga');

  var wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
  XLSX.write(wb, { bookType: 'xlsx', type: 'base64' });

  var dataUri = 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,' + XLSX.write(wb, { bookType: 'xlsx', type: 'base64' });

  var link = document.createElement("a");
  link.href = dataUri;
  link.download = "Plantilla_Valoracion_Masiva.xlsx";
  link.target = "_blank";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

var btn = document.getElementById("btn");
btn.addEventListener("click", download);

    <?php  
    if(base64_decode(Yii::$app->request->get("varAlerta")) === "1"){ ?>       
      swal.fire("Información","Accion ejecutada Correctamente","success"); 
    <?php } ?>
</script>