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

	$this->title = 'Editar Bitácora Universo';
	$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8"><div class="col-md-12">'
    . ' {input}{error}{hint}</div>';
    /*$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';*/

    $sessiones = Yii::$app->user->identity->id;
    foreach ($dataprovider as $key => $value) {
        $txtcliente = $value['cliente'];
        $txtpcrc = $value['cod_pcrc'];
        $txtcod_pcrcnom = $value['pcrc'];
        $txtpcrc = $txtpcrc." - ".$txtcod_pcrcnom;
        $txtciudad = $value['ciudad'];
        $txtdirector = $value['director'];
        $txtgerente = $value['gerente'];
        $txtmediocont = $value['medio_contacto'];
        $txtcedula = $value['cedula'];
        $txtnombre = $value['nombre'];
        $txttelf_mov = $value['telefono_movil'];
        $txtfechareg = $value['fecha_registro'];              
        $txtgrupo = $value['grupo'];
        $txtnivel = $value['nivel_caso'];
        $txtnombremom = $value['nombre_momento'];
        $txtmotivo = $value['detalle_momento'];
        $txtnombre_tutor = $value['nombre_tutor'];
        $txtnombre_lider = $value['nombre_lider'];
        $txtcaso = $value['descripcion_caso'];
        $txtescalamiento = $value['escalamiento'];
        $txtresponsable = $value['responsable'];
        $txtfecha_escal = $value['fecha_escalamiento'];
        $txtid_bitacora_uni = $value['id_bitacora_uni'];
    
    }
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
            font-family: "Nunito";
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
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../../images/Bitacora_univer_r.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<link rel="stylesheet" href="https://qa.grupokonecta.local/qa_managementv2/web/css/font-awesome/css/font-awesome.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br> 
<div class="capaUno">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="far fa-address-card" style="font-size: 20px; color: #2CA5FF;"></i> Registro del asesor: </label>
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Cliente: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtcliente; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Centro de costo: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtpcrc; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Ciudad: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtciudad; ?></label>
                    </div>
                </div><hr>
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Director: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtdirector; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Gerente: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtgerente; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Medio de contacto: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtmediocont; ?></label>
                    </div>
                </div><hr>
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Cédula: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtcedula; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Nombre: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtnombre; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Número Celular: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txttelf_mov; ?></label>
                    </div>
                </div><hr> 
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Fecha de registro: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtfechareg; ?></label>
                    </div>
		    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Grupo: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtgrupo; ?></label>
                    </div>
		    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Nivel del caso: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtnivel; ?></label>
                    </div>
                </div><hr>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="capaDos">
    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="far fa-clipboard" style="font-size: 20px; color: #e8701a;"></i> Momentos</label>                
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #e8701a;"></i> Momento: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtnombremom; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #e8701a;"></i> Motivo: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtmotivo; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #e8701a;"></i> Nombre Tutor: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtnombre_tutor; ?></label>
                    </div>
                </div><hr> 
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #e8701a;"></i> Nombre Líder: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtnombre_lider; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #e8701a;"></i> Descripción Caso: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtcaso; ?></label>
                    </div>
                    <?php
                    if($txtescalamiento == 'si') { ?>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #e8701a;"></i> Responsable: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtresponsable; ?></label>
                    </div>          
                </div><hr>
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #e8701a;"></i> Fecha de escalamiento: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtfecha_escal; ?></label>
                    </div>
                </div>
                <?php } ?>
                <br>
                <div class="row">
                    <div class="col-md-6">                      
                        <label for="txtFechar" style="font-size: 14px;">Fecha de cierre</label>
                        <input type="date" id="txtFechacierre" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha de registro">
                    </div>                                 
                    <div class="col-md-6"> 
                        <label for="txtFechar" style="font-size: 14px;">Respuesta</label>
                        <textarea type="text" class="form-control" id="txtRespuestar" data-toggle="tooltip" title="Nombre Tutor"></textarea> 
                    </div>                       
                </div>
            </div>            
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<hr>
<div class="capaTres">
    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
              <label><i class="fas fa-cogs" style="font-size: 20px; color: #1e8da7;"></i> Acciones:</label>
                <div class="row">                    
                    <div class="col-md-6">
                        <label style="font-size: 15px;"></label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card1 mb">
                                    <?= Html::a('Regresar',  ['reportebitacorauni'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Regresar']) 
                                    ?>
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="card1 mb">
                                  <div onclick="Guardardato();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" id="botones2" >
                                    Guardar información
                                  </div>                                                                    
                                </div>
                            </div>                                                      
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><br>
    <?php ActiveForm::end(); ?>
</div>


<script type="text/javascript">    
    function Guardardato(){
      var varidbitauni = "<?php echo $txtid_bitacora_uni; ?>";
      var varFechacierre = document.getElementById("txtFechacierre").value;
      if(varFechacierre){
        varestado = 'cerrado';
      } else {
        varestado = 'abierto';
      }
      var varRespuestar = document.getElementById("txtRespuestar").value;
      
      if(!varFechacierre){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Fecha de Cierre (Información de Partida).","warning");
          document.getElementById("txtFechacierre").style.border = '1px solid #ff2e2e';
          return;
      } else {    
        $.ajax({
                  method: "post",
                  url : "https://qa.grupokonecta.local/qa_managementv2/web/index.php/bitacorauniverso/actualbitacora",
                  data : {
                    txtvidbitauni : varidbitauni,
                    txtvFechacierre : varFechacierre,
                    txtvestado : varestado,
                    txtvRespuestar : varRespuestar,
                  },
                  success : function(response){ 
                              var numRta =   JSON.parse(response);    
                              console.log(numRta);
                              if (numRta != 0) {
                                jQuery(function(){
                                    swal.fire({type: "success",
                                        title: "!!! OK !!!",
                                        text: "Datos guardados correctamente."
                                    }).then(function() {                                   
                                      window.location.href = 'https://qa.grupokonecta.local/qa_managementv2/web/index.php/bitacorauniverso/reportebitacorauni';
                                    });
                                });
                              }else{
                                event.preventDefault();
                                  swal.fire("!!! Advertencia !!!","No se pueden guardar los datos.","warning");
                                return;
                              }
                          }
            }); 
         }
        };


    function guardardato_old(){

        var varidbitacora = "<?php echo $txtid_bitacora_uni; ?>";
        var varfechacierre = document.getElementById("txtFechacierre").value;
        var varestado = null;
        if(varfechacierre){
            varestado = 'cerrado';
        } else {
            varestado = 'abierto';
        }
        var varRespuestar = document.getElementById("txtRespuestar").value; 

        if(!varfechacierre){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Fecha de Cierre (Información de Partida).","warning");
          return;
        } else {
            
            $.ajax({
                method : "post",
                url : "https://qa.grupokonecta.local/qa_managementv2/web/index.php/bitacorauniverso/updatebitacora",
                data : {
                    txtvaridbitacora : varidbitacora,
                    txtvarfechacierre : varfechacierre,
                    txtvarestado : varestado,
                    txtvarRespuestar : varRespuestar,
                       },
                success : function(response){
                    var numRta =   JSON.parse(response);
                    console.log(numRta); 
                }   

            });
        }


    }
  
    
</script>
