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

$this->title = 'Procesos Voc - Registrar Categorias';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Procesos Voc - Registrar Categorias';

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
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
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }

    .card2 {
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
<!-- datatable -->
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
  <div class="CapaInfo" style="display: inline;">
    
    <div class="row">
      <div class="col-md-6">
        <div class="card2 mb" style="background: #6b97b1; ">
          <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Acciones Categorias"; ?> </label>
        </div>
      </div>
    </div>

    <br>

    <div class="row">
      
      <div class="col-md-4">
        <div class="card1 mb">
          <label><em class="fas fa-minus-circle" style="font-size: 20px; color: #FFC72C;"></em> Cancelar y regresar</label>
          <?= Html::a('Aceptar',  ['configcategorias'], ['class' => 'btn btn-success',
                                 'style' => 'background-color: #707372',                        
                                  'data-toggle' => 'tooltip',
                                  'title' => 'Nuevo'])
          ?>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card1 mb">
          <label><em class="fas fa-save" style="font-size: 20px; color: #FFC72C;"></em> Guardar Categoria</label>
          <?= Html::submitButton('Guardar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id'=>'btn_submit', 'onclick'=>'validarcampos();'] ) ?> 
        </div>
      </div>

      <div class="col-md-4">
        <div class="card1 mb">
          <label><em class="fas fa-upload" style="font-size: 20px; color: #FFC72C;"></em> Cargar Archivo Categorias</label>
          <?= Html::button('Importar', ['value' => url::to(['importarcategorias']), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Importar Archivo', 'style' => 'background-color: #337ab7']) 
          ?> 

          <?php
            Modal::begin([
              'header' => '<h4></h4>',
              'id' => 'modal1',
            ]);

            echo "<div id='modalContent1'></div>";
                                                                              
            Modal::end(); 
          ?> 
        </div>
      </div>

    </div>

  </div>
  <br>
  <hr>
  <br>
  <div class="CapaInfo" style="display: inline;">
    
    <div class="row">
      <div class="col-md-6">
        <div class="card2 mb" style="background: #6b97b1; ">
          <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Ingresar Datos"; ?> </label>
        </div>
      </div>
    </div>

    <br>

    <div class="row">
      <div class="col-md-12">
        <div class="card1 mb">          

          <div class="row">

            <div class="col-md-3">
              <label><em class="fas fa-check" style="font-size: 20px; color: #559FFF;"></em> Seleccionar Cliente </label>
              <?=  $form->field($model, 'pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechServicios::find()->distinct()->where("anulado = 0")->andwhere("id_dp_clientes != 1")->orderBy(['nameArbol'=> SORT_ASC])->all(), 'id_dp_clientes', 'nameArbol'),
                                                    [
                                                        'id' => 'txtidclientes',
                                                        'prompt'=>'Seleccionar...',
                                                        'onchange' => '
                                                            $.get(
                                                                "' . Url::toRoute('historicomixto/listarpcrcs') . '", 
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

            <div class="col-md-3">
              <label><em class="fas fa-list" style="font-size: 20px; color: #559FFF;"></em> Seleccionar Programa/Pcrc </label>
              <?= $form->field($model,'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                                [],
                                                [
                                                    
                                                    'prompt' => 'Seleccionar...',
                                                    'id' => 'requester',
                                                    'onchange' => '
                                                            $.get(
                                                                "' . Url::toRoute('listarindicadores') . '", 
                                                                {id: $(this).val()}, 
                                                                function(res){
                                                                    $("#requester2").html(res);
                                                                }
                                                            );
                                                            
                                                        ',
                                                ]
                                            )->label('');
              ?>
            </div>

            <div class="col-md-3">
              <label><em class="fas fa-comment" style="font-size: 20px; color: #559FFF;"></em> Identificacion Categoria </label>
              <?= $form->field($model, 'idcategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 10, 'id'=>'txtidcategoria', 'onkeypress'=>'return valida(event)', 'placeholder'=>'Ingresar Id de Categoria'])?>
            </div>

            <div class="col-md-3">
              <label><em class="fas fa-comment" style="font-size: 20px; color: #559FFF;"></em> Nombre de Categoria </label>
              <?= $form->field($model, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 50, 'id'=>'txtcategoria', 'placeholder'=>'Ingresar Nombre de Categoria'])?>
            </div>

            
          </div>

          <br>

          <div class="row">

            <div class="col-md-3">
              <label><em class="fas fa-comment" style="font-size: 20px; color: #559FFF;"></em> Tipo de Categoria </label>
              <?php $varTipoCategoria = ['1' => 'Indicador', '2' => 'Variable', '3' => 'Motivo de Contacto']; ?>                        
              <?= $form->field($model, 'tipocategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varTipoCategoria, ['prompt' => 'Seleccionar...', 'id'=>"idtipocategoria", 'onclick'=>'habilitarvar();'])->label('') ?> 
            </div>

            <div class="col-md-3">
              <div id="idIndicador" class="capaIdIndicador" style="display: none;">
                <label><em class="fas fa-comment" style="font-size: 20px; color: #559FFF;"></em> Seleccionar Indicador </label>
                <?= $form->field($model,'tipoindicador', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                                  [],
                                                  [
                                                      
                                                      'prompt' => 'Seleccionar...',
                                                      'id' => 'requester2',
                                                  ]
                                              )->label('');
                ?>
              </div>
            </div>

            <div class="col-md-3">
              <div id="idSmart" class="capaSmart" style="display: none;">
                <label><em class="fas fa-comment" style="font-size: 20px; color: #559FFF;"></em> Seleccionar Orientación Smart </label>
                <?php $varSmart = ['1' => 'Negativo', '2' => 'Positivo']; ?>                        
                <?= $form->field($model, 'orientacionsmart', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varSmart, ['prompt' => 'Seleccionar...', 'id'=>"idsmart"])->label('') ?> 
              </div>
            </div>

            <div class="col-md-3">
              <div id="idForm" class="capaForm" style="display: none;">
                <label><em class="fas fa-comment" style="font-size: 20px; color: #559FFF;"></em> Seleccionar Orientación CXM </label>
                <?php $varCxm = ['1' => 'Negativo', '0' => 'Positivo']; ?>                        
                <?= $form->field($model, 'orientacionform', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varCxm, ['prompt' => 'Seleccionar...', 'id'=>"idform"])->label('') ?> 
              </div>
            </div>

          </div>

          <br>

          <div class="row">

            <div class="col-md-3">
              <label><em class="fas fa-comment" style="font-size: 20px; color: #559FFF;"></em> Servicio de Speech </label>
                                     
              <?= $form->field($model, 'programacategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 50, 'id'=>'txtprograma', 'placeholder'=>'Ingresar Servicio de Speech'])?>
              
            </div>

            <div class="col-md-3">
              <div id="idparametro" class="capaParametro" style="display: none;">
                <label><em class="fas fa-comment" style="font-size: 20px; color: #559FFF;"></em> Seleccionar Tipo Parametro </label>
                <?php $varParametro = ['1' => 'Auditoria', '2' => 'Desempeño']; ?>                        
                <?= $form->field($model, 'tipoparametro', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varParametro, ['prompt' => 'Seleccionar...', 'id'=>"idParametro"])->label('') ?> 
              </div>
            </div>
            
          </div>

        </div>
      </div>
    </div>

  </div>
  <hr>
  <br>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
  function valida(e){
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla==8){
      return true;
    }            
    
    patron =/[0-9]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
  };

  function habilitarvar(){
    var varidtipocategoria = document.getElementById("idtipocategoria").value;
    var varidIndicador = document.getElementById("idIndicador");
    var varidSmart = document.getElementById("idSmart");
    var varidForm = document.getElementById("idForm");
    var varidparametro = document.getElementById("idparametro");

    if (varidtipocategoria == "1") {
      varidIndicador.style.display = 'none';
      varidSmart.style.display = 'inline';
      varidForm.style.display = 'inline';
      varidparametro.style.display = 'inline';
    }else{
      if (varidtipocategoria == "2") {
        varidIndicador.style.display = 'inline';
        varidSmart.style.display = 'inline';
        varidForm.style.display = 'inline';
        varidparametro.style.display = 'none';
      }else{
        varidIndicador.style.display = 'none';
        varidSmart.style.display = 'none';
        varidForm.style.display = 'none';
        varidparametro.style.display = 'none';
      }
      
    }

  };

  function validarcampos(){
    var vartxtidclientes = document.getElementById("txtidclientes").value;
    var varrequester = document.getElementById("requester").value;
    var vartxtidcategoria = document.getElementById("txtidcategoria").value;
    var vartxtcategoria = document.getElementById("txtcategoria").value;
    var vartxtprograma = document.getElementById("txtprograma").value;


    if (vartxtidclientes == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de seleccionar servicio","warning");
      return;
    }

    if (varrequester == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de seleccionar programa/pcrc","warning");
      return;
    }

    if (vartxtidcategoria == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar identificacion categoria","warning");
      return;
    }

    if (vartxtcategoria == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar nombre de la categoria","warning");
      return;
    }

    if (vartxtprograma == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar nombre del servicio de speech","warning");
      return;
    }
    

  };
</script>
