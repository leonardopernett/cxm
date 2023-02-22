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
   
    $data2 = (new \yii\db\Query())
        ->select(['usua_id', 'usua_nombre'])
        ->from(['tbl_usuarios'])
        ->where(['=','usua_activo','S'])
        ->orderby('usua_nombre')
        ->All();
   
    $listData2 = ArrayHelper::map($data2, 'usua_id', 'usua_nombre');
    
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
      ->select(['id_pilares', 'nombre_pilar'])
      ->from(['tbl_pilares_gptw'])
      ->where(['=','anulado',0])
      ->All();

    $listData3 = ArrayHelper::map($datanew, 'id_pilares', 'nombre_pilar');

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

    foreach ($varListasatisfaccion as $key => $value) {
        $varId = $value['id_satisfaccion'];
        $varIdopera = $value['id_operacion'];
        $varIdarea = $value['id_area_apoyo'];
        $varMejora = $value['concepto_mejora'];
        $varanalisis_causa = $value['analisis_causa'];
        $varaccion_seguir = $value['accion_seguir'];
        $varaccion = $value['accion'];
        $varresponsable_area = $value['responsable_area'];
        $varfecha_definicion = $value['fecha_definicion'];
        $varfecha_implementacion = $value['fecha_implementacion'];
        $varfecha_cierre = $value['fecha_cierre'];
        $varestado = $value['estado'];
        $varpuntaje_meta = $value['puntaje_meta'];
        $varpuntaje_actual = $value['puntaje_actual'];
        $varpuntaje_final = $value['puntaje_final'];
 }

        $varRol = (new \yii\db\Query())
          ->select(['tbl_usuarios_jarvis_cliente.posicion'])
          ->from(['tbl_usuarios_jarvis_cliente'])
          ->where(['=','tbl_usuarios_jarvis_cliente.idusuarioevalua',$varresponsable_area])
          ->Scalar();
    
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
        background-image: url('../../images/satisfacioncliente3.png');
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
<?php
if($sessiones == "6832" || $sessiones == "3205" || $sessiones == "3468" || $sessiones == "3229" || $sessiones == "2915" || $sessiones == "2953" || $sessiones == "57" || $sessiones == "4043" || $sessiones == "611" || $sessiones == "4040" || $sessiones == "4090" || $sessiones == "4045" || $sessiones == "4039" || $sessiones == "4041" || $sessiones == "4443" || $sessiones == "4458" || $sessiones == "6544" || $sessiones == "6706" || $sessiones == "69" || $sessiones == "1083" || $sessiones == "8"){ ?>
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
                            <div class="col-md-4">
                                <div class="card1 mb">
                                  <div onclick="varGuardar();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                                    Guardar información
                                  </div>                                                                    
                                </div>
                            </div>                            
                            <div class="col-md-4">
                                <div class="card1 mb">
                                    <?= Html::a('Regresar',  ['reportesatisfaccion'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #337ab7',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Regresar'])
                                    ?>
                                </div>
                            </div>
                            <?php if ($varId == 0) { ?>
                            <div id="subir1" class="col-md-3" style="display:none">
                            <?php } else { ?>
                              <div id="subir1" class="col-md-3" style="display:inline">
                              <?php }?>
                              <div class="card1 mb">                             
                                  <?= Html::button('Actualizar archivo', ['value' => url::to(['importardocumentoedit','varId'=>$id]), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                                          'data-toggle' => 'tooltip',
                                          'title' => 'Selección de archivo']) ?> 

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
                        </div>
                    </div>
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
                <label><em class="far fa-address-card" style="font-size: 25px; color: #827DF9;"></em> Registro de Información</label>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <label for="txtproceso" style="font-size: 14px;">Proceso</label>
                        <div id="proceso" >  
                          <?= $form->field($model6, 'id_proceso_satis',['labelOptions' => [], 'template' => $template])->dropDownList($listData3, ['prompt' => 'Seleccione...', 'id'=>'txtproceso', ])?>                       
                        </div>                                                     
                    </div>
                    <div class="col-md-6">
                    <label for="txtarea" style="font-size: 14px;">Área / Operación</label>
                       <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="color" value="area" id = "requiereno" onclick="lista1()" <?php if($varIdarea) {?> checked <?php }?>> Área &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                       <label><input type="radio" name="color" value="opera" id = "requieresi" onclick="lista2()" <?php if(!$varIdarea) {?> checked <?php }?>> Operación </label>                          
                        <br>
                      <div id="area" style="display:inline" > 
                     
                          <?=  $form->field($model4, 'id_areaapoyo', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\areaGptw::find()->orderBy(['nombre'=> SORT_ASC])->all(), 'id_areaapoyo', 'nombre'),
                                          [
                                              'prompt'=>'Seleccionar...',
                                              'id'=>'txtopera',
                                          ]
                                  )->label(''); 
                          ?>
                      </div>
                      <div id="operacion" style="display:none">
                     
                          <?=  $form->field($model3, 'idusuarioevalua', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\usuariosEvalua::find()->groupby('clientearea')->orderBy(['clientearea'=> SORT_ASC])->all(), 'idusuarioevalua', 'clientearea'),
                                          [
                                              'prompt'=>'Seleccionar...',
                                              'id'=>'txtarea',
                                          ]
                                  )->label(''); 
                          ?>
                        
                      </div>
                    </div>                                                    
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <label for="txtmodelogptw" style="font-size: 14px;" >Concepto a Mejorar </label>
                        <br>
                        <textarea type="text" class="form-control" style = 'resize: vertical; height: 67px;' id="txtconcepto" data-toggle="tooltip" title="Concepto a Mejorar"><?php echo ($varMejora); ?></textarea>                           
                    </div>
                    <div class="col-md-6">
                      <label for="txtCedula" style="font-size: 14px;">Análisis de Causas&nbsp;&nbsp;  </label><em class="fas fa-info-circle" style="font-size: 20px; color: #db2c23;" title=" Utilizar Metodología de los 5 ¿por qué? "></em>
                      <textarea type="text" class="form-control" style = 'resize: vertical;' id="txtanalisis" data-toggle="tooltip" title="Análisis de Causas"><?php echo ($varanalisis_causa); ?></textarea>                           
                    </div>                    
                </div>            
                <br>
                <div class="row">
                    <div class="col-md-6">
                      <label for="txtNombre" style="font-size: 14px;">Acción a Seguir &nbsp;&nbsp; </label><em class="fas fa-info-circle" style="font-size: 20px; color: #db2c23;" title=" Documentar las acciones a seguir iniciando con un verbo en infinitivo ej: Realizar, diseñar, controlar"></em>
                      <textarea type="text" class="form-control" style = 'resize: vertical;' id="txtaccionseguir" data-toggle="tooltip" title="Análisis de Causas"><?php echo ($varaccion_seguir); ?></textarea>   
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
                      <label for="txtindicador" style="font-size: 14px;">Indicador </label>
                      <?= $form->field($model7, 'id_indicador',['labelOptions' => [], 'template' => $template])->dropDownList($listData5, ['prompt' => 'Seleccione...', 'id'=>'txtindicador', ])?>                       
                    </div>
                    <div class="col-md-6">
                    <label for="txtNombre" style="font-size: 14px;">Puntaje Meta %</label>
                      <input type="number" min="1" max="100"  type="number" maxlength="3" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" class="form-control" id="txtPuntajemeta" data-toggle="tooltip" title="Puntaje Meta" value="<?php echo ($varpuntaje_meta);?>">   
                    </div>
                </div>                
                <br> 
                <div class="row">
                    <div class="col-md-6">
                      <label for="txtCedula" style="font-size: 14px;">Puntaje Actual %</label>
                      <input type="number" min="1" max="100" maxlength="3" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" class="form-control" id="txtPuntajeactual" data-toggle="tooltip" title="Puntaje Actual" value="<?php echo ($varpuntaje_actual);?>">   
                     </div>
                    <div class="col-md-6">
                      <label for="txtNombre" style="font-size: 14px;">Puntaje Final %</label>
                      <input type="number" min="1" max="100"  type="number" maxlength="3" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" class="form-control" id="txtPuntajefinal" data-toggle="tooltip" title="Puntaje Meta"  value="<?php echo ($varpuntaje_final);?>">   
                    </div>
                </div>
                <br>

                <div class="row">                    
                    <div class="col-md-6">
                      <label for="txtResponsable" style="font-size: 14px;">Rol Responsable </label>
                      <?= $form->field($model5, 'usua_id',['labelOptions' => [], 'template' => $template])->dropDownList($listData2, ['prompt' => 'Seleccione...', 'id'=>'txtResponsable', ])?> 
                    </div>
                    <div class="col-md-6">
                      <label for="txtRol" style="font-size: 14px;">Rol</label>
                      <input type="text" class="form-control" id="txtRol" data-toggle="tooltip" title="Rol" readonly="readonly" value="<?php echo ($varRol);?>">   
                    </div>
                </div>                
                <br>    
                <div class="row">
                    <div class="col-md-6">
                      <label for="txtFechaavan" style="font-size: 14px;">Fecha Definición Plan</label>
                      <input type="date" id="txtFechadefine" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha de avance" value="<?php echo date($varfecha_definicion);?>">
                    </div>
                    <div class="col-md-6">
                      <label for="txtFechaavan" style="font-size: 14px;">Fecha Implementación</label>
                      <input type="date" id="txtFechaimplementa" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha de avance" value="<?php echo date($varfecha_implementacion);?>">
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
                      <input type="date" id="txtFechacierre" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha de cierre" value="<?php echo date($varfecha_cierre);?>">
                    </div>                    
                </div>
                <br>                     
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
                            <div class="col-md-4">
                                <div class="card1 mb">
                                  <div onclick="varGuardar();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                                    Guardar información
                                  </div>                                                                    
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card1 mb">
                                    <?= Html::a('Regresar',  ['reportesatisfaccion'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #337ab7',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Regresar'])
                                    ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div><br>
            </div>
        </div>
    </div><br>
    <?php ActiveForm::end(); ?>
</div>
<hr>
<?php if($sessiones == "2953" || $sessiones == "2911" || $sessiones == "57" || $sessiones == "3205" || $sessiones == "2915"){ ?>
  <div class="formularios-form" style="display:none">
    <div class="row">
      <div class="col-md-12">
        <div class="card1 mb">
          <label><em class="fas fa-pen-square" style="font-size: 20px; color: #827DF9;"></em> Registrar items:</label>
            <div class="row">
              <div class="col-mod-4">  
                      <?= Html::button('Agregar Momento', ['value' => url::to('momento'), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Agregar', 'style' => 'background-color: #337ab7']) 
                      ?> 

                      <?php

                        Modal::begin([
                              'header' => '<h4>Creacion de momento</h4>',
                              'id' => 'modal1',
                            ]);

                        echo "<div id='modalContent1'></div>";
                                                  
                        Modal::end(); 
                      ?> 

                      <?= Html::button('Agregar Detalle Momento', ['value' => url::to('detallemomento'), 'class' => 'btn btn-success', 'id'=>'modalButton2',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Agregar', 'style' => 'background-color: #337ab7']) 
                      ?> 

                      <?php
                        Modal::begin([
                              'header' => '<h4>Creacion de Detalle de Momento </h4>',
                              'id' => 'modal2',
                            ]);

                        echo "<div id='modalContent2'></div>";
                                                  
                        Modal::end(); 
                      ?>    
              </div>                    
            </div>
        </div>
      </div>
    </div>
  </div>
 <hr>
 <?php } ?>
<?php } else { ?>
  <div class="Seis">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
              <label><em class="fas fa-info-circle" style="font-size: 20px; color: #1e8da7;"></em> Información:</label>
              <label style="font-size: 14px;">No tiene los permisos para ingresar a esta opción... Debe diregirse al administrador de la aplicación</label>
                </div><br>
            </div>
        </div>  
    </div>
  </div><br>

  <?php } ?>
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

    function lista1(){
      var varParteT = document.getElementById("operacion");
      var varParteT2 = document.getElementById("area");
      varParteT.style.display = 'none';    
      varParteT2.style.display = 'inline';
    };

    function lista2(){
      var varPartTe1 = document.getElementById("operacion");
      var varPartTe12 = document.getElementById("area");    
      varPartTe1.style.display = 'inline';    
      varPartTe12.style.display = 'none';            
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
     var varpcrcid = document.getElementById("requester").value;
     
        $.ajax({
              method: "post",
              url: "cargadatocc",
              data : {
                idcentrocos : varpcrcid,                
              },
              success : function(response){ 
                          var Rta =   JSON.parse(response);    
                          console.log(Rta);
                          //ciudad, director_programa, gerente_cuenta
                          document.getElementById("txtCiudad").value = Rta[0].ciudad;
                          document.getElementById("txtDirector").value = Rta[0].director_programa;
                          document.getElementById("txtGerente").value = Rta[0].gerente_cuenta;
                          
                      }
              
          }); 
        
    };

  function varGuardar(){
      var varidsatisfa = "<?php echo $varId; ?>";
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
      var varFechacierre = document.getElementById("txtFechacierre").value;
      var varindicador = document.getElementById("txtindicador").value;
      var vartPuntajemeta = document.getElementById("txtPuntajemeta").value;
      var varPuntajeactual = document.getElementById("txtPuntajeactual").value;
      var varPuntajefinal = document.getElementById("txtPuntajefinal").value;
    
      if(varEstado=='Seleccione...'){
        varEstado="";
      }
      
      if(varResponsable=='Seleccione...'){
        varResponsable="";
      }

      /*var varFechacierre = document.getElementById("txtFechacierre").value;
      if(varFechacierre){
        varestado = 'cerrado';
      } else {
        varestado = 'abierto';
      }*/
    
      //var varRespuestar = document.getElementById("txtRespuestar").value;
      
      if (varConcepto == ""){
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
      } /*else if(varEstado == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Estado Seguimiento","warning");
          document.getElementById("txtestado").style.border = '1px solid #ff2e2e';
          return;
      } else if(varFechacierre == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Fecha de Cierre","warning");
          document.getElementById("txtFechacierre").style.border = '1px solid #ff2e2e';
          return;
      }*/
      
      $.ajax({
                  method: "get",
                  url: "createsatisfaccion",
                  data : {
                    txtvaridsatisfa : varidsatisfa,
                    txtvarea : varArea,
                    txtvopera : varOpera,
                    txtvConcepto : varConcepto,
                    txtvAnalisis : varAnalisis,
                    txtvAccionseguir : varAccionseguir,
                    txtvAccion : varAccion,
                    txtvResponsable : varResponsable,
                    txtvFechadefine : varFechadefine,
                    txtvFechaimplementa : varFechaimplementa,
                    txtvrEstado : varEstado,
                    txtvFechacierre : varFechacierre,
                    txtvindicador : varindicador,
                    txtvPuntajemeta : vartPuntajemeta,
                    txtvPuntajeactual : varPuntajeactual,
                    txtvPuntajefinal : varPuntajefinal,

                  },
                  success : function(response){ 
                              var numRta =   JSON.parse(response);    
                              console.log(response);
                              if (numRta != 0) {
                                jQuery(function(){
                                    swal.fire({type: "success",
                                        title: "!!! OK !!!",
                                        text: "Datos guardados correctamente."
                                    }).then(function() {                                   
                                      window.location.href = 'index';
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
