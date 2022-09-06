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

$this->title = 'Bitácora Universo';
$this->params['breadcrumbs'][] = $this->title;
   
    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $valor = null;

    $data = Yii::$app->get('dbexperience')->createCommand("select usuarios.id, usuarios.nombre 
                                                        FROM usuarios
                                                        INNER JOIN cargos ON usuarios.cargo = cargos.id
                                                        WHERE cargos.id = 1 AND usuarios.id <> 1
                                                        ORDER BY usuarios.nombre")->queryAll();
    
    $listData = ArrayHelper::map($data, 'id', 'nombre');
    $data2 = Yii::$app->get('dbexperience')->createCommand("select usuarios.id, usuarios.nombre 
                                                        FROM usuarios
                                                        INNER JOIN cargos ON usuarios.cargo = cargos.id
                                                        WHERE cargos.id = 11
                                                        ORDER BY usuarios.nombre")->queryAll();
    
    $listData2 = ArrayHelper::map($data2, 'id', 'nombre');
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
        background-image: url('../../images/Bitacora_univer_r.png');
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
if($sessiones == "6832" || $sessiones == "3205" || $sessiones == "3468" || $sessiones == "3229" || $sessiones == "2915" || $sessiones == "2953" || $sessiones == "57" || $sessiones == "4043" || $sessiones == "611" || $sessiones == "4040" || $sessiones == "4090" || $sessiones == "4045" || $sessiones == "4039" || $sessiones == "4041" || $sessiones == "4443" || $sessiones == "4458" || $sessiones == "6544" || $sessiones == "6706" || $sessiones == "69" || $sessiones == "1083"){ ?>
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
                                    <?= Html::a('Nueva consulta',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Nueva Consulta'])
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card1 mb">
                                    <?= Html::a('Ir al reporte',  ['reportebitacorauni'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #337ab7',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Ir al reporte'])
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
                <label><em class="far fa-address-card" style="font-size: 20px; color: #827DF9;"></em> Registro del asesor</label>
                <div class="row">
                    <div class="col-md-6">
                    <label for="txtPcrc" style="font-size: 14px;">Cliente</label>
                        <?=  $form->field($model, 'id_dp_clientes', ['labelOptions' => [], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosClienteCentrocosto::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                                        [
                                                            'prompt'=>'Seleccione Cliente...',
                                                            'onchange' => '
                                                                $.post(
                                                                    "' . Url::toRoute('listarpcrc') . '", 
                                                                    {id: $(this).val()}, 
                                                                    function(res){
                                                                        $("#requester").html(res);
                                                                    }
                                                                );
                                                            ',

                                                        ]
                                            ); 
                        ?>
                    </div>
                    <div class="col-md-6">
                    <label for="txtPcrc" style="font-size: 14px;">Centro de Costos</label>
                        <?= $form->field($model,'cod_pcrc', ['labelOptions' => [], 'template' => $template])->dropDownList(
                                                        [],
                                                        [
                                                            'prompt' => 'Seleccione Centro de Costos...',
                                                            "onchange"=>"carguedato();",                                                            
                                                            'id' => 'requester'
                                                        ]
                                                    );
                        ?>
                    </div>
                </div>
               
            <div class="row">
              <div class="col-md-6">
                <label for="txtCiudad" style="font-size: 14px;">Ciudad</label>
                <input type="text" class="form-control" id="txtCiudad" data-toggle="tooltip" title="Ciudad" readonly="readonly">   
              </div>
              <div class="col-md-6">
                <label for="txtDirector" style="font-size: 14px;">Director</label>
                <input type="text" class="form-control" id="txtDirector" data-toggle="tooltip" title="Director" readonly="readonly">   
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <label for="txtGerente" style="font-size: 14px;">Gerente</label>
                <input type="text" class="form-control" id="txtGerente" data-toggle="tooltip" title="Gerente" readonly="readonly">   
              </div>
              <div class="col-md-6">
                <label for="txtmedio" style="font-size: 14px;">Medio de Contacto</label>
                <select id="txtmedio" class ='form-control'>
                          <option value="" disabled selected>seleccione...</option>
                          <option value="Presencial">Presencial</option>
                          <option value="Virtual">Virtual</option>
                          <option value="Telefonico">Telefónico</option>
                        </select>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <label for="txtCedula" style="font-size: 14px;">Número de Cédula</label>
                <input type="text" class="form-control" id="txtCedula" data-toggle="tooltip" title="Número de cédula">   
              </div>
              <div class="col-md-6">
                <label for="txtNombre" style="font-size: 14px;">Nombre</label>
                <input type="text" class="form-control" id="txtNombre" data-toggle="tooltip" title="Nombre">   
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <label for="txtCelular" style="font-size: 14px;">Número de Celular</label>
                <input type="text" class="form-control" id="txtCelular" data-toggle="tooltip" title="Número de Celular">   
              </div>
              <div class="col-md-6">
                <label for="txtFechar" style="font-size: 14px;">Fecha de registro</label>
                <input type="date" id="txtFechar" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha de registro">
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <label for="txtCelular" style="font-size: 14px;">Grupo</label>
                <input type="text" class="form-control" id="txtGrupo" data-toggle="tooltip" title="Grupo">   
              </div>
              <div class="col-md-6">
                <label for="txtnivelcaso" style="font-size: 14px;">Nivel del caso</label>
                <select id="txtnivelcaso" class ='form-control'>
                          <option value="" disabled selected>seleccione...</option>
                          <option value="Bajo">Bajo</option>
                          <option value="Moderado">Moderado</option>
                          <option value="Crítico">Crítico</option>
                        </select>
              </div>
            </div>
               
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<hr>

<div class="capaDos">
    <?php $form = ActiveForm::begin([
      'layout' => 'horizontal',
      'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
      ]
      ]); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="far fa-clipboard" style="font-size: 20px; color: #e8701a;"></em> Momentos</label>
                <div class="row">
                    <div class="col-md-6">
                      <label for="txtCiudad" style="font-size: 14px;">Momento</label>
                      <?=  $form->field($model2, 'id_momento', ['labelOptions' => [], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Controlmomento::find()->distinct()->where("anulado = 0")->orderBy(['id_momento'=> SORT_ASC])->all(), 'id_momento', 'nombre_momento'),
                                                        [
                                                            'prompt'=>'Seleccione Momento...',
                                                            'onchange' => '
                                                                $.post(
                                                                    "' . Url::toRoute('listarmomentos') . '", 
                                                                    {id: $(this).val()}, 
                                                                    function(res){
                                                                        $("#requester2").html(res);
                                                                    }
                                                                );
                                                            ',

                                                        ]
                                            ); 
                        ?>
                      
                    </div>
                    <div class="col-md-6">
                      <label for="requester2" style="font-size: 14px;">Motivos</label>                      
                      <?= $form->field($model2,'id_detalle_momento', ['labelOptions' => [], 'template' => $template])->dropDownList(
                                                        [],
                                                        [
                                                            'prompt' => 'Seleccione Motivo...',
                                                            'id' => 'requester2'
                                                        ]
                                                    );
                        ?>
                    </div>            
                </div>
                <div class="row">
                    <div class="col-md-6">
                      <label for="txtCiudad" style="font-size: 14px;">Nombre Tutor</label>
                      <?= $form->field($model2, 'id_momento',['labelOptions' => [], 'template' => $template])->dropDownList($listData, ['prompt' => 'Seleccione...', 'id'=>'txtNombretutor', ])?> 
                      
                    </div>
                    <div class="col-md-6">
                      <label for="txtDirector" style="font-size: 14px;">Nombre Lider</label>
                      <?= $form->field($model2, 'id_momento',['labelOptions' => [], 'template' => $template])->dropDownList($listData2, ['prompt' => 'Seleccione...', 'id'=>'txtNombrelider', ])?> 
                      
                    </div>            
                </div>
                <div class="row">
                    <div class="col-md-12">
                      <label for="txtCaso" style="font-size: 14px;">Descripción Caso</label>
                      <textarea type="text" class="form-control" id="txtCaso" data-toggle="tooltip" title="Descripción caso"></textarea>   
                    </div>                               
                </div>
                <br>
                <div class="row">
                  <div class="col-md-6">
                      
                      <label><em class="far fa-question-circle" style="font-size: 20px; color: #8be81a;"></em> Requiere escalamiento?</label><br>
                      <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="color" value="si" id = "requieresi" onclick="planaccion()"> Si &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                      <label><input type="radio" name="color" value="no" id = "requiereno" onclick="planaccion2()" checked> No </label>
                        <br>                          
                    
                  </div>
                </div>
                <div class="col-md-12" id="tablesi" style="display: none">
                  <div class="row">
                    <div class="col-md-12">                  
                        <div class="col-md-4"> 
                          <label for="txtConcepto_mejora">Responsable</label>
                          <input type="text" class="form-control"  id="txtResponsable"  data-toggle="tooltip" title="Concepto Mejora">                 
                        </div>
                        <div class="col-md-4">
                          <label for="txtFechaesc" style="font-size: 14px;">Fecha de escalamiento</label>
                          <input type="date" id="txtFechaesc" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha de registro">
                        </div> 
                        <div class="col-md-4"> 
                          <label for="txtFechar" style="font-size: 14px;">Fecha de cierre</label>
                          <input type="date" id="txtFechacierre" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha de registro" onchange="cierre()">
                        </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-12" id="tablecierre" style="display: none">
                  <div class="row">
                    <div class="col-md-12">                  
                        <div class="col-md-12"> 
                          <label for="txtConcepto_mejora">Respuesta</label>
                          <textarea type="text" class="form-control" id="txtRespuestar" data-toggle="tooltip" title="Nombre Tutor"></textarea> 
                        </div>
                       
                    </div>
                  </div>
                </div>
            </div>
    </div><br>
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
                                    <?= Html::a('Nueva consulta',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Nueva Consulta'])
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card1 mb">
                                    <?= Html::a('Ir al reporte',  ['reportebitacorauni'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #337ab7',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Ir al reporte'])
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
  <div class="formularios-form">
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
    function planaccion(){
    var varRta = document.getElementById("requieresi").value;
    var varPartT = document.getElementById("tablesi");
    if (varRta == "si") {
      varPartT.style.display = 'inline';
    }else{
      varPartT.style.display = 'none';
    }    
  };
  function planaccion2(){
    var varRta = document.getElementById("requiereno").value;
    var varPartT = document.getElementById("tablesi");
    var varPartT2 = document.getElementById("tablecierre");
    if (varRta == "no") {
      document.getElementById("txtFechacierre").value='';
      varPartT.style.display = 'none';
      varPartT2.style.display = 'none';
    }else{
      varPartT.style.display = 'inline';
    }    
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
      var varcliente = document.getElementById("speechparametrizar-id_dp_clientes").value;
      var varcentrocosto = document.getElementById("requester").value;
      var varCiudad = document.getElementById("txtCiudad").value;
      var varDirector = document.getElementById("txtDirector").value;
      var varGerente = document.getElementById("txtGerente").value;
      var varMedio = document.getElementById("txtmedio").value;
      var varCedula = document.getElementById("txtCedula").value;
      var varNombre = document.getElementById("txtNombre").value;
      var varCelular = document.getElementById("txtCelular").value;
      var varFechar = document.getElementById("txtFechar").value;
      var varGrupo = document.getElementById("txtGrupo").value;
      var varNivel = document.getElementById("txtnivelcaso").value;

      var varmomento = document.getElementById("controldetallemomento-id_momento").value;
      var varmotivo = document.getElementById("requester2").value;

      var lista3 = document.getElementById("controldetallemomento-id_momento");
      var varnombremomento = lista3.options[lista3.selectedIndex].text;
      
      var varidtutor = document.getElementById("txtNombretutor").value;      
      var lista = document.getElementById("txtNombretutor");
      var varNombretutor = lista.options[lista.selectedIndex].text;
      if(varNombretutor=='Seleccione...'){
        varNombretutor="";
      }

      var varidlider = document.getElementById("txtNombrelider").value;
      var lista2 = document.getElementById("txtNombrelider");
      var varNombrelider = lista2.options[lista2.selectedIndex].text;
      if(varNombrelider=='Seleccione...'){
        varNombrelider="";
      }
      var varCaso = document.getElementById("txtCaso").value;
      if (document.getElementById("requieresi").checked){
        var varrequiere = 'si';
      } else {
        var varrequiere = 'no';
      }

      
      var varResponsable = document.getElementById("txtResponsable").value;
      var varFechaesc = document.getElementById("txtFechaesc").value;

      var varFechacierre = document.getElementById("txtFechacierre").value;
      if(varFechacierre){
        varestado = 'cerrado';
      } else {
        varestado = 'abierto';
      }
    
      var varRespuestar = document.getElementById("txtRespuestar").value;
      
      if (varcliente == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Clientes (Información de Partida).","warning");                    
          document.getElementById("speechparametrizar-id_dp_clientes").style.border = '1px solid #ff2e2e';
          return;
      } else if(varcentrocosto == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Centros de costo","warning");
          document.getElementById("requester").style.border = '1px solid #ff2e2e';
          return;
      } else if(varCiudad == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Ciudad","warning");
          document.getElementById("txtCiudad").style.border = '1px solid #ff2e2e';
          return;
      } else if(varDirector == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Director","warning");
          document.getElementById("txtDirector").style.border = '1px solid #ff2e2e';
          return;
      } else if(varGerente == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Gerente","warning");
          document.getElementById("txtGerente").style.border = '1px solid #ff2e2e';
          return;
      } else if(varMedio == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Medio de contacto","warning");
          document.getElementById("txtmedio").style.border = '1px solid #ff2e2e';
          return;
      } else if(varCedula == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Número de cédula","warning");
          document.getElementById("txtCedula").style.border = '1px solid #ff2e2e';
          return;
      } else if(varNombre == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Nombre","warning");
          document.getElementById("txtNombre").style.border = '1px solid #ff2e2e';
          return;
      } else if(varCelular == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Número de celular","warning");
          document.getElementById("txtCelular").style.border = '1px solid #ff2e2e';
          return;
      } else if(varFechar == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Fecha de registro","warning");
          document.getElementById("txtFechar").style.border = '1px solid #ff2e2e';
          return;
      } else if(varGrupo == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Grupo","warning");
          document.getElementById("txtGrupo").style.border = '1px solid #ff2e2e';
          return;
      } else if(varNivel == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Nivel del Caso","warning");
          document.getElementById("txtGrupo").style.border = '1px solid #ff2e2e';
          return;
      } else if(varmomento == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Momento","warning");
          document.getElementById("controldetallemomento-id_momento").style.border = '1px solid #ff2e2e';
          return;
      } else if(varmotivo == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Motivos","warning");
          document.getElementById("requester2").style.border = '1px solid #ff2e2e';
          return;
      }  
    
      if (varnombremomento == "Aprendizaje") {
          if (varidtutor == ""){
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","No hay datos a registrar en Tutor","warning");
            document.getElementById("txtNombretutor").style.border = '1px solid #ff2e2e';
            return;
          }
      }
      if (varnombremomento === "Operación") {
          if(varidlider == ""){
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","No hay datos a registrar en Lider","warning");
            document.getElementById("txtNombrelider").style.border = '1px solid #ff2e2e';
            return;
          }
      }
      if(varCaso == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Descripción de caso","warning");
          document.getElementById("txtCaso").style.border = '1px solid #ff2e2e';
          return;      
        }
      if (varrequiere == "si") {
          if(varResponsable == ""){
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","No hay datos a registrar en Responsable","warning");
            document.getElementById("varResponsable").style.border = '1px solid #ff2e2e';
            return;
          } else if(varFechaesc == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Fecha escalamiento","warning");
          document.getElementById("varFechaesc").style.border = '1px solid #ff2e2e';
          return;
          } 
      } else {
        varestado = 'cerrado';
      }
      if (varFechacierre) {
          if(varRespuestar == ""){
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","No hay datos a registrar en Respuesta","warning");
            document.getElementById("varFechacierre").style.border = '1px solid #ff2e2e';
            return;
          } 
      }
      
      $.ajax({
                  method: "post",
                  url: "createbitacora",
                  data : {
                    txtvcliente : varcliente,
                    txtvcentrocosto : varcentrocosto,
                    txtvCiudad : varCiudad,
                    txtvDirector : varDirector,
                    txtvGerente : varGerente,
                    txtvMedio : varMedio,
                    txtvCedula : varCedula,
                    txtvNombre : varNombre,
                    txtvCelular : varCelular,
                    txtvFechar : varFechar,
                    txtvGrupo : varGrupo,
                    txtvNivel : varNivel,
                    txtvmomento : varmomento,
                    txtvmotivo : varmotivo,
                    txtvNombretutor : varNombretutor,
                    txtvNombrelider : varNombrelider,
                    txtvCaso : varCaso,                
                    txtvrequiere : varrequiere,
                    txtvResponsable : varResponsable,
                    txtvFechaesc : varFechaesc,
                    txtvFechacierre : varFechacierre,
                    txtvestado : varestado,
                    txtvRespuestar : varRespuestar,
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

    
</script>
