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

$this->title = 'Reportes LockerStudio';
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

  $varConteoExist = (new \yii\db\Query())
                                ->select(['tbl_comdata_permisosreportestudio.id_dp_clientes'])
                                ->from(['tbl_comdata_permisosreportestudio'])            
                                ->where(['=','tbl_comdata_permisosreportestudio.anulado',0])
                                ->andwhere(['=','tbl_comdata_permisosreportestudio.usuario_permiso',$sessiones])
                                ->count();

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
    height: 100px;
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

<!-- Capa Loader -->
<div class="capaLoader" id="idCapa" style="display: none;">
  
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <table class="text-center">
        <caption><?= Yii::t('app', 'Procesando Información') ?></caption>
          <thead>
            <tr>
              <th scope="col" class="text-center">
                  <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
              </th>
              <th scope="col"><?= Yii::t('app', '') ?>                
              </th>
              <th scope="col" class="text-justify">
                  <h4><?= Yii::t('app', 'Actualmente CXM esta procesando la informacion de los filtros previamente seleccionado para visualizar el dashboard del LookerStudio...') ?></h4>
              </th>
            </tr>            
          </thead>
        </table>
      </div>
    </div>
  </div>
  <hr>
</div>

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<!-- Capa Principal Acciones -->
<div id="capaIdPrincipal" class="capaPrincipal" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones & Reportes') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">

      <div class="col-md-12">
        <div class="card1 mb">


          <div class="row">
            <div class="col-md-6">
              <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Listado de Clientes') ?></label>

                <?=  $form->field($model, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosClienteCentrocosto::find()->distinct()->select(['id_dp_clientes','CONCAT(cliente," - ",id_dp_clientes) as cliente'])->where(['=','anulado',0])->andwhere(['=','estado',1])->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                                      [
                                                          'id' => 'txtidclientes',
                                                          'prompt'=>'Seleccionar...',
                                                          'onchange' => '
                                                              $.get(
                                                                  "' . Url::toRoute('listarpcrcs') . '", 
                                                                  {id: $(this).val()}, 
                                                                  function(res){
                                                                      $("#requester").html(res);
                                                                  }
                                                              );
                                                              
                                                          ',

                                                      ]
                                          )->label(''); 
                ?>

            </div>

            <div class="col-md-6">
              <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Listado de Centros de Costos') ?></label>
              <?= $form->field($model,'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                                [],
                                                [
                                                    
                                                    'prompt' => 'Seleccionar...',
                                                    'id' => 'requester',
                                                ]
                                            )->label('');
              ?> 
            </div>
          </div>


          
        </div>
      </div>

    </div>

</div>

<hr>

<!-- Capa Secundario Botones -->
<div id="capaIdBtn" class="capaBtn" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card2 mb">
        <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Generar Reporte') ?></label>
        <?= Html::submitButton(Yii::t('app', 'Reporte'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'varVerificar();',
                                'title' => 'Generar Reporte']) 
        ?>        
      </div>
    </div>

    <div class="col-md-6">
      <div class="card2 mb" style="background: #e6edff;">
        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #ff453c;"></em> <?= Yii::t('app', 'Importante: Tener presente que si el dash no es visible, es por temas de permisos al dash de LokerStudio. Por lo tanto se debe pedir los permisos correspondientes. Permisos a Sociedad Comdata.') ?></label>
      </div>
    </div>
  </div>

</div>

<hr>



<!-- Capa Permisos y Configuraciones -->
<div id="capaIdConfig" class="capaConfig" style="display: inline;">

  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Configuraciones & Permisos') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">

    <?php
      if ($varConteoExist != 0) {
        
    ?>

    <div class="col-md-6">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 20px; color: #981F40;"></em><?= Yii::t('app', ' Configurar Url Cliente/Pcrc') ?></label>
        <?= Html::button('Configurar', ['value' => url::to(['configurarcomdata']), 'class' => 'btn btn-danger', 
          'id'=>'modalButton',
          'data-toggle' => 'tooltip',
          'title' => 'Configurar Url']) 
        ?> 

        <?php
          Modal::begin([
            'header' => '<h4>Configuración Procesos de Url - LokerStudio</h4>',
            'id' => 'modal',
            'size' => 'modal-lg',
          ]);

          echo "<div id='modalContent'></div>";
                                                                                                  
          Modal::end(); 
        ?>
      </div>
    </div>

    <?php
      }else{
    ?>
      <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #ff453c;"></em> <?= Yii::t('app', 'Importante: Actualmente no tienes permisos de configuracion para el ingreso de la url de LookerStudio.') ?></label>
    <?php
      }
    ?>

    <?php
      if ($sessiones == '2953' || $sessiones == '57' || $sessiones == '7952' || $sessiones == '1699' || $sessiones == '5658' || $sessiones == '8659' || $sessiones == '69' || $sessiones == '8685') {
        
    ?>

    <div class="col-md-6">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 20px; color: #981F40;"></em><?= Yii::t('app', ' Habilitar Permisos') ?></label>
        <?= Html::a('Permisos',  ['permisoscomdata'], ['class' => 'btn btn-danger',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Permisos']) 
        ?>       
      </div>
    </div>

    <?php
      }
    ?>

    
  </div>
  
</div>

<hr>



<?php ActiveForm::end(); ?>

<script type="text/javascript">
  function varVerificar(){
    var vartxtidclientes = document.getElementById("txtidclientes").value;
    var varrequester = document.getElementById("requester").value;

    var varcapaLoader = document.getElementById("idCapa");
    var varcapaIdPrincipal = document.getElementById("capaIdPrincipal");
    var varcapaIdBtn = document.getElementById("capaIdBtn");
    var varcapaIdConfig = document.getElementById("capaIdConfig");
    

    if (vartxtidclientes == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de seleccionar un cliente","warning");
      return;
    }else{
      if (varrequester == "") {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Debe de seleccionar un centro de costos","warning");
        return;
      }else{
        varcapaLoader.style.display = 'inline';
        varcapaIdPrincipal.style.display = 'none';
        varcapaIdBtn.style.display = 'none';
        varcapaIdConfig.style.display = 'none';
      }
    }    
      
    
  };

  $(document).ready(function () {
    var varComdata = "<?php echo $varComdataUrl; ?>";
    var varcapaLoaderc = document.getElementById("idCapa");
    var varcapaIdPrincipalc = document.getElementById("capaIdPrincipal");
    var varcapaIdBtnc = document.getElementById("capaIdBtn");
    var varcapaIdConfigc = document.getElementById("capaIdConfig");

    
    if (varComdata == "SinProceso") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","El Centro de Costos seleccionado no tiene un dash de LookerStudio, Contactarse con el analista encargado para verificar información.","warning");
      return;
    }else{
      if (varComdata != "") {
        $("#modal_report").modal("show");
      }
    }
  });

</script>

<!-- Modal -->
<div id="modal_report" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg" style=" margin-top: 11px !important; width: 98% !important; margin-bottom: 0px;">
    <!-- Modal content-->
    <div class="modal-content" style = "">
      <div class="modal-header" style = "padding-top: 4px !important; padding-bottom: 9px !important;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?= Yii::t('app', 'Reporte LookerStudio: '.$varFullName) ?></h4>
      </div>
      <div class="modal-body pa0" style="padding: 0px !important;" >
        <div id="container_report" style="min-height:90vh;">
          <iframe title="new-page"  style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" src='<?php echo $varComdataUrl; ?>'
            allowfullscreen=""></iframe>
        </div>
      </div>
      <hr>
      <div class="modal-footer">
        <div class="row">
          <div class="col-md-12">
            <?= Html::a('Cerrar',  ['index'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Cerrar']) 
            ?>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
