<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\db\Query;
use app\models\ProcesosClienteCentrocosto;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;

$this->title = 'Plan Acción Satisfacción ';
$this->params['breadcrumbs'][] = $this->title;
   
    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
  
    $valor = null;
   
   /* $data2 = (new \yii\db\Query())
        ->select(['usua_id', 'usua_nombre'])
        ->from(['tbl_usuarios'])
        ->join('LEFT JOIN', 'rel_usuarios_roles',
              'tbl_usuarios.usua_id = rel_usuarios_roles.rel_usua_id')
        ->join('LEFT JOIN', 'tbl_roles',
              'rel_usuarios_roles.rel_role_id = tbl_roles.role_id')
        ->where(['in','tbl_roles.role_id',[278, 293, 301, 303, 300]])
        ->orderby('tbl_usuarios.usua_nombre')
        ->All();*/
        $data2 = (new \yii\db\Query())
          ->select(['tbl_usuarios_jarvis_cliente.idusuarioevalua', "UPPER(trim(replace(tbl_usuarios_jarvis_cliente.nombre_completo,'\n',''))) AS nombre"])
          ->from(['tbl_usuarios_jarvis_cliente'])
          ->where(['not in','tbl_usuarios_jarvis_cliente.id_dp_funciones',[364, 312, 206, 981]])
          ->orderBY ('nombre')
          ->All();
   
    $listData2 = ArrayHelper::map($data2, 'idusuarioevalua', 'nombre');
    
    $data = (new \yii\db\Query())
      ->select(['tbl_usuarios_evalua.idusuarioevalua', 'tbl_usuarios_evalua.clientearea'])
      ->from(['tbl_usuarios_evalua'])
      ->where(['IS not','tbl_usuarios_evalua.clientearea',NULL])
      ->andwhere(['<>','tbl_usuarios_evalua.idusuarioevalua',2202])
      ->groupBy('tbl_usuarios_evalua.clientearea')
      ->orderBY ('tbl_usuarios_evalua.clientearea')
      ->All();
    $listData = ArrayHelper::map($data, 'idusuarioevalua', 'clientearea');
    $datanew = (new \yii\db\Query())
      ->select(['id_proceso_satis', 'nombre'])
      ->from(['tbl_procesos_satisfaccion_cliente'])
      ->where(['=','anulado',0])
      ->All();

    $listData3 = ArrayHelper::map($datanew, 'id_proceso_satis', 'nombre');

    $datanew2 = (new \yii\db\Query())
      ->select(['id_areaapoyo', 'nombre'])
      ->from(['tbl_areasapoyo_gptw'])
      ->where(['=','anulado',0])
      ->All();

    $listData4 = ArrayHelper::map($datanew2, 'id_areaapoyo', 'nombre');

    $datanew5 = (new \yii\db\Query())
      ->select(['id_indicador', 'nombre'])
      ->from(['tbl_indicadores_satisfaccion_cliente'])
      ->where(['=','anulado',0])
      ->All();

    $listData5 = ArrayHelper::map($datanew5, 'id_indicador', 'nombre');
    $varidbande = $_GET['varidban'];
  
?>
<style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Nunito');

    .card {
            height: 500px;
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
        background-image: url('../../images/satisfacioncliente1.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>

<div class="capaCinco">
    <?php $form = ActiveForm::begin([
      'layout' => 'horizontal',
      'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
      ]
      ]); ?>
      
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
              <label><em class="fas fa-cogs" style="font-size: 20px; color: #1e8da7;"></em> Acciones:</label>
                <div class="row">                    
                    <div class="col-md-6">
                        <label style="font-size: 15px;"></label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card1 mb">
                                  <div onclick="varGuardar();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                                    Guardar
                                  </div>                                                                    
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card1 mb">
                                    <?= Html::a('Nueva consulta',  ['index?varidban=0'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Nueva Consulta'])
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card1 mb">
                                    <?= Html::a('Ir al reporte',  ['reportesatisfaccion'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #337ab7',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Ir al reporte'])
                                    ?>
                                </div>
                            </div>
                            <?php if ($varidbande == 0) { ?>
                            <div id="subir1" class="col-md-3" style="display:none">
                            <?php } else { ?>
                              <div id="subir1" class="col-md-3" style="display:inline">
                              <?php }?>
                              <div class="card1 mb">                                
                                  <?= Html::button('Anexar Archivo', ['value' => url::to(['importardocumento']), 'class' => 'btn btn-warning', 'style' => 'background-color:#f7812d;', 'id'=>'modalButton1',
                                          'data-toggle' => 'tooltip',
                                          'title' => 'Selección Documento']) ?> 

                                  <?php
                                      Modal::begin([
                                          'header' => '<h4>Agregar Información</h4>',
                                          'id' => 'modal1',
                                          'size' => 'modal-lg',
                                      ]);

                                      echo "<div id='modalContent1'></div>";
                                                                                                            
                                      Modal::end(); 
                                  ?>
                              </div> 
                            </div>  
                            <div class="col-md-6">
                              
                                               
                            </div>
                    </div>
                    <br>
                </div><br>
            </div>
        </div>
    </div><br>
    <?php ActiveForm::end(); ?>
</div>
<hr>
<div class="capaUno">
    <?php $form = ActiveForm::begin([
      'layout' => 'horizontal',
      'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
      ]
      ]); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="far fa-address-card" style="font-size: 28px; color: #715bf5;"></em> Registro de Información</label>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <label for="txtproceso" style="font-size: 14px;">Proceso</label>
                        <div id="proceso" >  
                          <?= $form->field($model3, 'id_proceso_satis',['labelOptions' => [], 'template' => $template])->dropDownList($listData3, ['prompt' => 'Seleccione...', 'id'=>'txtproceso', ])?>                       
                        </div>                                                     
                    </div>
                    <div class="col-md-6">
                      <label for="txtarea" style="font-size: 14px;">Área / Operación</label>
                      <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="color" value="opera" id = "requiereno" onclick="planaccion()"> Área &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                      <label><input type="radio" name="color" value="area" id = "requieresi" onclick="planaccion2()" checked> Operación </label>
                      <br>
                      <div id="area" style="display:inline" >  
                        <?= $form->field($model2, 'id_gptw',['labelOptions' => [], 'template' => $template])->dropDownList($listData, ['prompt' => 'Seleccione...', 'id'=>'txtarea', ])?>                       
                      </div>
                      <div id="operacion" style="display:none">
                        <?= $form->field($model2, 'id_gptw',['labelOptions' => [], 'template' => $template])->dropDownList($listData4, ['prompt' => 'Seleccione...', 'id'=>'txtopera', ])?>                       
                      </div>
                    </div> 
                </div>
                <br>
                <div class="row">                    
                    <div class="col-md-6">
                        <label for="txtmodelogptw" style="font-size: 14px;" >Concepto a Mejorar </label>
                        <br>
                        <textarea type="text" class="form-control" style = 'resize: vertical; height: 67px;' id="txtconcepto" data-toggle="tooltip" title="Concepto a Mejorar"></textarea>   
                    </div>
                    <div class="col-md-6">
                      <label for="txtCedula" style="font-size: 14px;">Análisis de Causas&nbsp;&nbsp;  </label><em class="fas fa-info-circle" style="font-size: 20px; color: #db2c23;" title=" Utilizar Metodología de los 5 ¿por qué? "></em>
                      <textarea type="text" class="form-control" style = 'resize: vertical;' id="txtanalisis" data-toggle="tooltip" title="Análisis de Causas"></textarea>   
                    </div>                                
                </div>
                <br>
                <div class="row">                    
                    <div class="col-md-6">
                      <label for="txtNombre" style="font-size: 14px;">Acción a Seguir &nbsp;&nbsp; </label><em class="fas fa-info-circle" style="font-size: 20px; color: #db2c23;" title=" Documentar las acciones a seguir iniciando con un verbo en infinitivo ej: Realizar, diseñar, controlar"></em>
                      <textarea type="text" class="form-control" style = 'resize: vertical;' id="txtaccionseguir" data-toggle="tooltip" title="Acción a Seguir"></textarea>   
                    </div>
                    <div class="col-md-6">
                      <label for="txtaccion" style="font-size: 14px;">Acción </label>
                      <select id="txtaccion" class ='form-control'>
                          <option value="" disabled selected>seleccione...</option>
                          <option value="Correctiva">Correctiva</option>
                          <option value="Mejora">Mejora</option>
                          <option value="Preventiva">Preventiva</option>
                      </select>
                    </div>
                </div>            
                <br>
                <div class="row">                    
                    <div class="col-md-6">
                      <label for="txtResponsable" style="font-size: 14px;">Indicador </label>
                      <?= $form->field($model5, 'id_indicador',['labelOptions' => [], 'template' => $template])->dropDownList($listData5, ['prompt' => 'Seleccione...', 'id'=>'txtindicador', ])?>                       
                    </div>
                    <div class="col-md-6">
                    <label for="txtNombre" style="font-size: 14px;">Puntaje Meta %</label>
                      <input type="number" min="1" max="100"  type="number" maxlength="3" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" class="form-control" id="txtPuntajemeta" data-toggle="tooltip" title="Puntaje Meta">   
                    </div>
                </div>                
                <br> 
                <div class="row">
                    <div class="col-md-6">
                      <label for="txtCedula" style="font-size: 14px;">Puntaje Actual %</label>
                      <input type="number" min="1" max="100" maxlength="3" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" class="form-control" id="txtPuntajeactual" data-toggle="tooltip" title="Puntaje Actual">   
                     </div>
                    <div class="col-md-6">
                      <label for="txtNombre" style="font-size: 14px;">Puntaje Final %</label>
                      <input type="number" min="1" max="100"  type="number" maxlength="3" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" class="form-control" id="txtPuntajefinal" data-toggle="tooltip" title="Puntaje Meta">   
                    </div>
                </div>
                <br>
                <div class="row">                    
                    <div class="col-md-6">
                      <label for="txtResponsable" style="font-size: 14px;">Responsable </label>
                      <?= $form->field($model2, 'id_gptw',['labelOptions' => [], 'template' => $template])->dropDownList($listData2, ['prompt' => 'Seleccione...', 'id'=>'txtResponsable', "onchange"=>"carguedato();",])?> 
                    </div>
                    <div class="col-md-6">
                      <label for="txtRol" style="font-size: 14px;">Rol</label>
                      <input type="text" class="form-control" id="txtRol" data-toggle="tooltip" title="Rol" readonly="readonly">   
                    </div>
                </div>                
                <br>    
                <div class="row">
                    <div class="col-md-6">
                      <label for="txtFechaavan" style="font-size: 14px;">Fecha Definición Plan</label>
                      <input type="date" id="txtFechadefine" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha de avance">
                    </div>
                    <div class="col-md-6">
                      <label for="txtFechaavan" style="font-size: 14px;">Fecha Implementación</label>
                      <input type="date" id="txtFechaimplementa" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha de avance">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                      <label for="txtestado" style="font-size: 14px;">Estado Seguimiento </label>
                      <select id="txtestado" class ='form-control'>
                          <option value="" disabled selected>seleccione...</option>
                          <option value="Abierto">Abierto</option>
                          <option value="Cerrado">Cerrado</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label for="txtFechacierre" style="font-size: 14px;">Fecha de Cierre</label>
                      <input type="date" id="txtFechacierre" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha de cierre">
                    </div>                    
                </div>
                <br> 
                <div class="row" style="display:none">
                    <div class="col-md-6">              
                        <label style="font-size: 18px;"><em class="fas fa-hand-pointer" style="font-size: 18px; color: #3d7d58;"></em><?= Yii::t('app', ' Anexar Documento') ?></label>
                        <div class="row">
                            <div class="col-md-4">
                            <input type="text" class="form-control" id="txtidsatis" data-toggle="tooltip" title="Rol" readonly="readonly">   
                                <?= $form->field($model4, 'file')->fileInput(["class"=>"input-file" ,'id'=>'idfile', 'style'=>'font-size: 18px;'])->label('') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<hr>

<div class="capaTres">
    <?php $form = ActiveForm::begin([
      'layout' => 'horizontal',
      'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
      ]
      ]); ?>
    <div class="row">
    <div class="col-md-12">
            <div class="card1 mb">
              <label><em class="fas fa-cogs" style="font-size: 20px; color: #1e8da7;"></em> Acciones:</label>
                <div class="row">                    
                    <div class="col-md-6">
                        <label style="font-size: 15px;"></label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card1 mb">
                                  <div onclick="varGuardar();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                                    Guardar
                                  </div>                                                                    
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card1 mb">
                                    <?= Html::a('Nueva consulta',  ['index?varidban=0'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Nueva Consulta'])
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card1 mb">
                                    <?= Html::a('Ir al reporte',  ['reportesatisfaccion'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #337ab7',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Ir al reporte'])
                                    ?>
                                </div>
                            </div>
                            <div id="subir2"  class="col-md-3" style="display:none">
                              <div class="card1 mb">                                
                                        <?= Html::button('Anexar Archivo', ['value' => url::to(['importardocumento']), 'class' => 'btn btn-warning', 'id'=>'modalButton',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Selección Documento']) ?> 

                                  <?php
                                      Modal::begin([
                                        'header' => '<h4>Seleccionar Documento</h4>',
                                        'id' => 'modal',
                                        'size' => 'modal-lg',
                                      ]);

                                      echo "<div id='modalContent'></div>";
                                                                                        
                                      Modal::end(); 
                                  ?>
                              </div>                            
                        </div>
                    </div>
                    <br>
                </div><br>
            </div>
        </div>
    </div><br>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    function varVerificar(){
        var varcliente = document.getElementById("speechcategorias-programacategoria").value;
        var varservicio = document.getElementById("requester").value;
        var varvalorado = document.getElementById("tecnicosid").value;

        if (varcliente == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe seleccionar el cliente.","warning");
            return;
        }else{
            if (varservicio == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Debe seleccionar el servicio.","warning");
                return;
            }else{
                if (varvalorado == "") {
                    event.preventDefault();
                    swal.fire("!!! Advertencia !!!","Debe seleccionar el valorado.","warning");
                    return;
                }
            }
        }

    };
    function planaccion2(){
    var varPartT = document.getElementById("area");
    var varPartT2 = document.getElementById("operacion");
      varPartT.style.display = 'inline';    
      varPartT2.style.display = 'none';
  };
  function planaccion(){
    var varPartT = document.getElementById("area");
    var varPartT2 = document.getElementById("operacion");
   
      varPartT.style.display = 'none';    
      varPartT2.style.display = 'inline';
    
     
  };
  function cierre(){
    var varRta = document.getElementById("txtFechacierre").value;
    var varPartT2 = document.getElementById("tablecierre");
    if (varRta) {
      varPartT2.style.display = 'inline';
    }else{
      varPartT2.style.display = 'none';
    }    
  };
  
  function carguedato(){
     var varid = document.getElementById("txtResponsable").value;
     //alert(varid);
        $.ajax({
              method: "post",
              url: "cargadatocc",
              data : {
                varid : varid,                
              },
              success : function(response){ 
                          var Rta =   JSON.parse(response);    
                          console.log(Rta);
                          document.getElementById("txtRol").value = Rta;
                          
                      }
              
          }); 
        
    };

    function varGuardar(){
      var varOpera = document.getElementById("txtarea").value;
      
      var varArea = document.getElementById("txtopera").value;
      var varConcepto= document.getElementById("txtconcepto").value;      
      var varAnalisis = document.getElementById("txtanalisis").value;
      var varAccionseguir = document.getElementById("txtaccionseguir").value;
      var varAccion = document.getElementById("txtaccion").value;
      var varResponsable = document.getElementById("txtResponsable").value;
      var varFechadefine = document.getElementById("txtFechadefine").value;      
      var varFechaimplementa = document.getElementById("txtFechaimplementa").value;
      var varEstado = document.getElementById("txtestado").value;
      var varProceso = document.getElementById("txtproceso").value;
      var varFechacierre = document.getElementById("txtFechacierre").value;
      var varIndicador = document.getElementById("txtindicador").value;      
      var varPuntajemeta = document.getElementById("txtPuntajemeta").value;
      var varPuntajeactual = document.getElementById("txtPuntajeactual").value;
      var varPuntajefinal = document.getElementById("txtPuntajefinal").value;
      
      if(varEstado=='Seleccione...'){
        varEstado="";
      }
      if(varProceso=='Seleccione...'){
        varProceso="";
      }
      
      if(varResponsable=='Seleccione...'){
        varResponsable="";
      }      
      
      if (varProceso == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Proceso","warning");
          document.getElementById("txtproceso").style.border = '1px solid #ff2e2e';
          return;
      } else if(varConcepto == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Concepto a mejorar.","warning");                    
          document.getElementById("txtconcepto").style.border = '1px solid #ff2e2e';
          return;
      } else if(varAnalisis == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Análisis de Causas","warning");
          document.getElementById("txtanalisis").style.border = '1px solid #ff2e2e';
          return;
      } else if(varAccionseguir == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Acción a Seguir","warning");
          document.getElementById("txtaccionseguir").style.border = '1px solid #ff2e2e';
          return;
      } else if(varAccion == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Acción","warning");
          document.getElementById("txtaccion").style.border = '1px solid #ff2e2e';
          return;
      } else if(varIndicador == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Indicador","warning");
          document.getElementById("txtindicador").style.border = '1px solid #ff2e2e';
          return;
      } else if(varPuntajemeta == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Puntaje Meta","warning");
          document.getElementById("txtPuntajemeta").style.border = '1px solid #ff2e2e';
          return;
      } else if(varPuntajeactual == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Puntaje Actual","warning");
          document.getElementById("txtPuntajeactual").style.border = '1px solid #ff2e2e';
          return;
      /*} else if(varPuntajefinal == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Puntaje Final","warning");
          document.getElementById("txtPuntajefinal").style.border = '1px solid #ff2e2e';
          return;*/
      } else if(varResponsable == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Rol Responsable","warning");
          document.getElementById("txtResponsable").style.border = '1px solid #ff2e2e';
          return;
      } else if(varFechadefine == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Fecha Definición Plan","warning");
          document.getElementById("txtFechadefine").style.border = '1px solid #ff2e2e';
          return;
      } else if(varFechaimplementa == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Fecha Implementación","warning");
          document.getElementById("txtFechaimplementa").style.border = '1px solid #ff2e2e';
          return;
      } 
     
      $.ajax({
                  method: "get",
                  url: "createsatisfaccion",
                  data : {
                    txtvarea : varArea,
                    txtvopera : varOpera,
                    txtvConcepto : varConcepto,
                    txtvAnalisis : varAnalisis,
                    txtvAccionseguir : varAccionseguir,
                    txtvAccion : varAccion,
                    txtvResponsable : varResponsable,
                    txtvFechadefine : varFechadefine,
                    txtvFechaimplementa : varFechaimplementa,
                    txtvFechacierre : varFechacierre,
                    txtvProceso : varProceso,
                    txtvIndicador : varIndicador,
                    txtvPuntajemeta : varPuntajemeta,
                    txtvPuntajeactual : varPuntajeactual,
                    txtvPuntajefinal : varPuntajefinal,
                    
                  },
                  success : function(response){ 
                              var numRta =   JSON.parse(response);    
                              console.log(response);
                              if (numRta != 0) {
                               // document.getElementById('idsatisfa').value=numRta;
                               
                                jQuery(function(){
                                    swal.fire({type: "success",
                                        title: "!!! OK !!!",
                                        text: "Datos guardados correctamente. ------ SE ACTIVA BOTON ANEXAR DOCUMENTO ------"
                                    }).then(function() {                                   
                                      window.location.href = 'index?varidban='+numRta;                                     
                                    });
                                });
                              }else{
                                event.preventDefault();
                                  swal.fire("!!! Advertencia !!!","No se pueden guardar los datos.","warning");
                                return;
                              }
                          }
            }); 
      
  };
    var ultimoValorValido = null;
    $("#foco").on("change", function() {
    if ($("#foco option:checked").length > 3) {
      $("#foco").val(ultimoValorValido);
    } else {
      ultimoValorValido = $("#foco").val();
    }
});
    
</script>
