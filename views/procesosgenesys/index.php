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

$this->title = 'Gestor Procesos GenesysCloud - Index';
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

  $varMes = date("n");
  $varMesActual = null;
  switch ($varMes) {
    case '01':
      $varMesActual = "Enero";
      break;
    case '02':
      $varMesActual = "Febrero";
      break;
    case '03':
      $varMesActual = "Marzo";
      break;
    case '04':
      $varMesActual = "Abril";
      break;
    case '05':
      $varMesActual = "Mayo";
      break;
    case '06':
      $varMesActual = "Junio";
      break;
    case '07':
      $varMesActual = "Julio";
      break;
    case '08':
      $varMesActual = "Agosto";
      break;
    case '09':
      $varMesActual = "Septiembre";
      break;
    case '10':
      $varMesActual = "Octubre";
      break;
    case '11':
      $varMesActual = "Noviembre";
      break;
    case '12':
      $varMesActual = "Diciembre";
      break;
    default:
      # code...
      break;
  }
  
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
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Procesos & Acciones') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Buscar Encuestas GNS - Con Api') ?></label>
        <?= Html::a('Aceptar',  ['buscarencuestas'], ['class' => 'btn btn-primary','data-toggle' => 'tooltip','title' => 'Buscar Encuestas']) 
        ?>
      </div>

      <br>

      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Buscar Llamadas GNS - Con Api') ?></label>
        <?= Html::a('Aceptar',  ['buscarllamadas'], ['class' => 'btn btn-primary','data-toggle' => 'tooltip','title' => 'Buscar Llamadas']) 
        ?>
      </div>

      <br>

      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Verificar Asesores') ?></label>
        <?= Html::button('Verificar', ['value' => url::to(['verificarasesores']), 'class' => 'btn btn-success', 'id'=>'modalButton',
                                'data-toggle' => 'tooltip',
                                'title' => 'Verificar Asesores']) 
        ?> 

        <?php
          Modal::begin([
            'header' => '<h4>Lista de Asesores</h4>',
            'id' => 'modal',
            'size' => 'modal-lg',
          ]);

          echo "<div id='modalContent'></div>";
                                                                                                  
          Modal::end(); 
        ?>
      </div>

      <br>

      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Verificar Colas & Formularios') ?></label>
        <?= Html::button('Verificar', ['value' => url::to(['verificarcolas']), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                                'data-toggle' => 'tooltip',
                                'title' => 'Verificar Servicios']) 
        ?> 

        <?php
          Modal::begin([
            'header' => '<h4>Lista de Servicios</h4>',
            'id' => 'modal1',
            'size' => 'modal-lg',
          ]);

          echo "<div id='modalContent1'></div>";
                                                                                                  
          Modal::end(); 
        ?>
      </div>

      <br>

      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Verificar Novedades Asesor') ?></label>
        <?= Html::a('Aceptar',  ['novedadesasesor'], ['class' => 'btn btn-primary','data-toggle' => 'tooltip','title' => 'Novedades Asesor']) 
        ?>
        
      </div>
    </div>

    <div class="col-md-8">
      
      <div class="row">
        <div class="col-md-4">
          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Cantidad Asesores Genesys') ?></label>
            <label style=" text-align: center; font-size: 30px;"><?php echo $varCantidadAsesores; ?></label>
          </div>          
        </div>

        <div class="col-md-4">
          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Cantidad Colas & Formularios') ?></label>
            <label style=" text-align: center; font-size: 30px;"><?php echo $varCantidadCola; ?></label>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Cantidad de Novedades Asesor') ?></label>
            <label style=" text-align: center; font-size: 30px;"><?php echo $varCantidadNovedades; ?></label>
          </div>
        </div>
      </div>

      <br>

      <div class="row">
        <div class="col-md-12">
          <div class="card1 mb">
            
          </div>
        </div>
      </div>

    </div>
  </div>

</div>
<hr>
<?php ActiveForm::end(); ?>