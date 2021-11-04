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
use app\models\ControlProcesosPlan;
use yii\db\Query;

$this->title = 'Hoja de Vida - Data Personal';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sesiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sesiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $varmaximoid = Yii::$app->db->createCommand("SELECT max(hv_idpersonal) from tbl_hojavida_datapersonal WHERE  anulado = 0")->queryScalar();
    $variddatapersonal = $varmaximoid + 1;

    $varRespuesta = ['1' => 'No', '2' => 'Si'];
    $varAfinidad = ['1' => 'Relación Directa', '2' => 'Relación de Interés'];
    $varTipo = ['1' => 'Decisor', '2' => 'No Decisor'];
    $varNivel = ['1' => 'Estratégico', '2' => 'Operativo'];
    $varEstado = ['1' => 'Activo', '2' => 'No Activo'];
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
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
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
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<div id="idcapauno" class="capaPrincipal" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-info" style="font-size: 15px; color: #FFC72C;"></em> Datos Personales </label>

        <div class="row">          
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Nombre Completo: </label>
            <?= $form->field($model, 'nombre_full', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idnombrefull', 'placeholder'=>'Ingresar Nombres y Apellidos'])?>
          </div>

          <div  class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> N&uacute;mero de Identidad: </label>
            <?= $form->field($model, 'identificacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'ididentificacion', 'placeholder'=>'Ingresar Documento de Identidad','onkeypress' => 'return valida(event)'])?>
          </div>

          <div  class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Correo Electr&oacute;nico Corporativo: </label>
            <?= $form->field($model, 'email', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idemail', 'placeholder'=>'Ingresar Correo Corportativo'])?>
          </div>
        </div>

        <div class="row">          
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> N&uacute;mero Celular: </label>
            <?= $form->field($model, 'numero_movil', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idnumeromovil', 'placeholder'=>'Ingresar Número Celular','onkeypress' => 'return valida(event)'])?>
          </div>

          <div  class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> T&eacute;lefono Oficina: </label>
            <?= $form->field($model, 'numero_fijo', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idnumerooficina', 'placeholder'=>'Ingresar Número Oficina','onkeypress' => 'return valida(event)'])?>
          </div>

          <div  class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Modalidad de Trabajo: </label>
            <?=  $form->field($model, 'hv_idmodalidad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvModalidadTrabajo::find()->orderBy(['hv_idmodalidad'=> SORT_DESC])->all(), 'hv_idmodalidad', 'modalidad'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                        ]
                                )->label(''); 
            ?>
          </div>
        </div>

        <div class="row">          
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span>Direcci&oacute;n Oficina: </label>
            <?= $form->field($model, 'direccion_oficina', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'iddireccionoficiona', 'placeholder'=>'Ingresar Número Celular'])?>
          </div>

          <div  class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Direcci&oacute;n Domicilio: </label>
            <?= $form->field($model, 'direccion_casa', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'iddireccioncasa', 'placeholder'=>'Ingresar Número Oficina'])?>
          </div>

          <div  class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Autoriza el Tratamiento de Datos Personales: </label>
            <?= $form->field($model, "tratamiento_data", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varRespuesta, ['prompt' => 'Seleccionar...', 'id'=>"idautoriza"]) ?>
          </div>
        </div>

        <div class="row">          
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Pa&iacute;s: </label>
            <?=  $form->field($model, 'hv_idpais', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvPais::find()->distinct()->orderBy(['pais'=> SORT_ASC])->all(), 'hv_idpais', 'pais'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                            'onchange' => '
                                                $.get(
                                                    "' . Url::toRoute('listarciudades') . '", 
                                                    {id: $(this).val()}, 
                                                    function(res){
                                                        $("#idciudad").html(res);
                                                    }
                                                );
                                            ',

                                        ]
                            )->label(''); 
            ?>
          </div>

          <div  class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Ciudad: </label>
            <?= $form->field($model,'hv_idciudad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                        [],
                                        [
                                            'prompt' => 'Seleccionar...',
                                            'id' => 'idciudad'
                                        ]
                                    )->label('');
            ?>
          </div>

          <div  class="col-md-4">

            <div class="row">
              <div class="col-md-6">
                <label style="font-size: 15px;"> Es Susceptibe a Encuestar: </label>
                <?= $form->field($model, "suceptible", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varRespuesta, ['prompt' => 'Seleccionar...', 'id'=>"idsusceptible"]) ?>
              </div>

              <div class="col-md-6">
                <label style="font-size: 15px;"> Indicador Satu: (%)</label>
                <?= $form->field($model, 'indicador_satu', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idsatu', 'placeholder'=>'Ingresar Porcentaje Satu', 'type'=>'number', 'onkeypress' => 'return valida(event)'])?>
              </div>
            </div>            
            
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<hr>
<div id="idcapados" class="capaDos" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-plus-square" style="font-size: 15px; color: #FFC72C;"></em> Datos Laborales </label>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Rol: </label>
            <?= $form->field($model2, 'rol', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idrol', 'placeholder'=>'Ingresar El Rol'])?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Antiguedad del Rol: </label>
            <?=  $form->field($model2, 'hv_id_antiguedad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvAntiguedadRol::find()->orderBy(['hv_id_antiguedad'=> SORT_DESC])->all(), 'hv_id_antiguedad', 'antiguedad'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                        ]
                                )->label(''); 
            ?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Fecha Inicio Como Contacto: </label>

            <?= $form->field($model2, 'fecha_inicio_contacto', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->widget(\yii\jui\DatePicker::className(), ['dateFormat' => 'yyyy-MM-dd','options' => ['class' => 'form-control','readonly' => 'readonly', 'id'=>'idfechainicio'],]) ?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Nombre del Jefe: </label>
            <?= $form->field($model2, 'nombre_jefe', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idnombrejefe', 'placeholder'=>'Ingresar Nombre del Jefe'])?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"> Cargo del Jefe: </label>
            <?= $form->field($model2, 'cargo_jefe', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idcargojefe', 'placeholder'=>'Ingresar Cargo del Jefe'])?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"> Trabajo Anterior: </label>
            <?= $form->field($model2, 'trabajo_anterior', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idtrabajoanterior', 'placeholder'=>'Ingresar Trabajo Anterior'])?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Afinidad: </label>
            <?= $form->field($model2, "afinidad", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varAfinidad, ['prompt' => 'Seleccionar...', 'id'=>"idafinidad", 'onchange' => 'habilitar();']) ?>
          </div>

          <div id="idafinidades" style="display: none;">
            <div class="col-md-4">
              <label style="font-size: 15px;"> Tipo Afinidad: </label>
              <?= $form->field($model2, "tipo_afinidad", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varTipo, ['prompt' => 'Seleccionar...', 'id'=>"idtipoafinidad"]) ?>
            </div>

            <div class="col-md-4">
              <label style="font-size: 15px;"> Nivel Afinidad: </label>
              <?= $form->field($model2, "nivel_afinidad", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varNivel, ['prompt' => 'Seleccionar...', 'id'=>"idnivelafinidad"]) ?>
            </div>
          </div>          
        </div>

      </div>
    </div>
  </div>
</div>
<hr>
<div id="idcapatres" class="capaTres" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-check-circle" style="font-size: 15px; color: #FFC72C;"></em> Datos de la Cuenta </label>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Cliente: </label>
            <?=  $form->field($model4, 'id_dp_cliente', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechServicios::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                          [
                                              'prompt'=>'Seleccionar...',
                                              'onchange' => '
                                                  $.post(
                                                      "' . Url::toRoute('formulariovoc/listarpcrcindex') . '", 
                                                      {id: $(this).val()}, 
                                                      function(res){
                                                          $("#requester").html(res);
                                                      }
                                                  );

                                                  $.get(
                                                      "' . Url::toRoute('hojavida/listardirectores') . '", 
                                                      {id: $(this).val()}, 
                                                      function(res){
                                                          $("#requester2").html(res);
                                                      }
                                                  );

                                                  $.get(
                                                      "' . Url::toRoute('hojavida/listargerentes') . '", 
                                                      {id: $(this).val()}, 
                                                      function(res){
                                                          $("#requester3").html(res);
                                                      }
                                                  );
                                              ',

                                          ]
                              )->label(''); 
            ?>
          </div>

          <div class="col-md-8">
            <div class="panel panel-default">
              <div class="panel-body">
                <label style="font-size: 13px;"> Ten presente, para seleccionar varias opciones, es necesario seleccionar el cliente y luego mantener presionado la tecla Ctrl y seleccionas con clic los items que necesites.</label>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Programa Pcrc: </label>
            <?= $form->field($model4,'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                        [],
                                        [
                                            'prompt' => 'Seleccionar...',
                                            'id' => 'requester',
                                            'multiple' => true,
                                        ]
                                    )->label('');
            ?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Director: </label>
            <?= $form->field($model5,'ccdirector', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                        [],
                                        [
                                            'prompt' => 'Seleccionar...',
                                            'id' => 'requester2',
                                            'multiple' => true,
                                        ]
                                    )->label('');
            ?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Gerente: </label>
            <?= $form->field($model6,'ccgerente', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                        [],
                                        [
                                            'prompt' => 'Seleccionar...',
                                            'id' => 'requester3',
                                            'multiple' => true,
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
<div id="idcapacuatro" class="capaCuatro" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-plus" style="font-size: 15px; color: #FFC72C;"></em> Datos Acad&eacute;micos </label>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"> Profesi&oacute;n: </label>
            <?=  $form->field($model3, 'idhvcursosacademico', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvCursosacademico::find()->where("idhvacademico = 4")->orderBy(['idhvcursosacademico'=> SORT_DESC])->all(), 'idhvcursosacademico', 'hv_cursos'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                        ]
                                )->label(''); 
            ?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"> Especializaci&oacute;n: </label>
            <?=  $form->field($model3, 'anulado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvCursosacademico::find()->where("idhvacademico = 2")->orderBy(['idhvcursosacademico'=> SORT_DESC])->all(), 'idhvcursosacademico', 'hv_cursos'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                        ]
                                )->label(''); 
            ?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"> Maestr&iacute;a: </label>
            <?=  $form->field($model3, 'hv_idpersonal', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvCursosacademico::find()->where("idhvacademico = 3")->orderBy(['idhvcursosacademico'=> SORT_DESC])->all(), 'idhvcursosacademico', 'hv_cursos'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                        ]
                                )->label(''); 
            ?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"> Doctorado: </label>
            <?=  $form->field($model3, 'usua_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvCursosacademico::find()->where("idhvacademico = 1")->orderBy(['idhvcursosacademico'=> SORT_DESC])->all(), 'idhvcursosacademico', 'hv_cursos'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                        ]
                                )->label(''); 
            ?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"> Estado: </label>
            <?= $form->field($model3, "activo", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varEstado, ['prompt' => 'Seleccionar...', 'id'=>"idestado"]) ?>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<hr>
<div class="capaBtn" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      
      <div class="row">
        <div class="col-md-6">
          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
            <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Regresar']) 
            ?>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Guardar Datos: </label>
            <div onclick="generated();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                  Guardar Informacion
            </div> 
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php $form->end() ?>
<hr>

<script type="text/javascript">
  function valida(e){
    tecla = (document.all) ? e.keyCode : e.which;

    //Tecla de retroceso para borrar, siempre la permite
    if (tecla==8){
      return true;
    }
            
    // Patron de entrada, en este caso solo acepta numeros
    patron =/[0-9]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
  };

  function habilitar(){
    var varidafinidad = document.getElementById("idafinidad").value;
    var varidafinidades = document.getElementById("idafinidades");

    if (varidafinidad == "1") {
      varidafinidades.style.display = 'inline';
    }else{
      varidafinidades.style.display = 'none';
    }
  }

  function generated(){
    var varidnombrefull = document.getElementById("idnombrefull").value;
    var varididentificacion = document.getElementById("ididentificacion").value;
    var varidemail = document.getElementById("idemail").value;
    var varidnumeromovil = document.getElementById("idnumeromovil").value;
    var varidnumerooficina = document.getElementById("idnumerooficina").value;
    var varidmdoalidad = document.getElementById("hojavidadatapersonal-hv_idmodalidad").value;
    var variddireccionoficiona = document.getElementById("iddireccionoficiona").value;
    var variddireccioncasa = document.getElementById("iddireccioncasa").value;
    var varidautoriza = document.getElementById("idautoriza").value;
    var varidpais = document.getElementById("hojavidadatapersonal-hv_idpais").value;
    var varididciudad = document.getElementById("idciudad").value;
    var varidsusceptible = document.getElementById("idsusceptible").value;
    var varidsatu = document.getElementById("idsatu").value;
    var varautoincrement = "<?php echo $variddatapersonal; ?>";

    if (varididentificacion == "") {

      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar documento de identidad","warning");
      return;

    }else{

      $.ajax({
        method: "get",
        url: "verificacedula",
        data: {
          txtvarididentificacion : varididentificacion,
        },
        success : function(response){
          numRta =   JSON.parse(response);
          
          if (numRta != 0) {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Usuario ya esta registrado en el sistema","warning");
            return;
          }else{
                        
            if (varidnombrefull == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de ingresar el nombre completo","warning");
              return;
            }
            if (varidemail == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de ingresar el correo corporativo","warning");
              return;
            }
            if (varidnumeromovil == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de ingresar el numero movil","warning");
              return;
            }
            if (varidnumerooficina == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de ingresar el numero de oficiona","warning");
              return;
            }
            if (varidmdoalidad == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar la modalidad","warning");
              return;
            }
            if (variddireccionoficiona == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de ingresar el direccion oficina","warning");
              return;
            }
            if (variddireccioncasa == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de ingresar el direccion casa","warning");
              return;
            }
            if (varidautoriza == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar la autorizacion","warning");
              return;
            }
            if (varidpais == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar el pais","warning");
              return;
            }
            if (varididciudad == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar la ciudad","warning");
              return;
            }

            // Esta accion permite guardar el primer bloque...
            $.ajax({
              method: "get",
              url: "guardarpersonal",
              data: {
                txtvaridnombrefull: varidnombrefull,
                txtvarididentificacion : varididentificacion,
                txtvaridemail : varidemail,
                txtvaridnumeromovil : varidnumeromovil,
                txtvaridnumerooficina : varidnumerooficina,
                txtvaridmdoalidad : varidmdoalidad,
                txtvariddireccionoficiona : variddireccionoficiona,
                txtvariddireccioncasa : variddireccioncasa,
                txtvaridautoriza : varidautoriza,
                txtvaridpais : varidpais,
                txtvarididciudad : varididciudad,
                txtvaridsusceptible : varidsusceptible,
                txtvaridsatu : varidsatu,
              },
              success : function(response){
                numRta =   JSON.parse(response);
              }
            });

            var varidrol = document.getElementById("idrol").value;
            var varidantiguedad = document.getElementById("hojavidadatalaboral-hv_id_antiguedad").value;
            var varidfechainicio = document.getElementById("idfechainicio").value;
            var varidnombrejefe = document.getElementById("idnombrejefe").value;
            var varidcargojefe = document.getElementById("idcargojefe").value;
            var varidtrabajoanterior = document.getElementById("idtrabajoanterior").value;
            var varidafinidad = document.getElementById("idafinidad").value;
            var varidtipoafinidad = document.getElementById("idtipoafinidad").value;
            var varidnivelafinidad = document.getElementById("idnivelafinidad").value;

            if (varidrol == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de ingresa el rol","warning");
              return;
            }
            if (varidantiguedad == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar la antiguedad","warning");
              return;
            }
            if (varidfechainicio == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar la fecha","warning");
              return;
            }
            if (varidnombrejefe == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de ingresar el nombre del jefe","warning");
              return;
            }
            if (varidafinidad == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar la afinidad","warning");
              return;
            }

            // Esta accion permite guardar el segundo bloque...
            $.ajax({
              method: "get",
              url: "guardarlaboral",
              data: {
                txtvarautoincrement : varautoincrement,
                txtvarididentificacion : varididentificacion,
                txtvaridrol : varidrol,
                txtvaridantiguedad : varidantiguedad,
                txtvaridfechainicio : varidfechainicio,
                txtvaridnombrejefe : varidnombrejefe,
                txtvaridcargojefe : varidcargojefe,
                txtvaridtrabajoanterior : varidtrabajoanterior,
                txtvaridafinidad : varidafinidad,
                txtvaridtipoafinidad : varidtipoafinidad,
                txtvaridnivelafinidad : varidnivelafinidad,
              },
              success : function(response){
                numRta =   JSON.parse(response);
              }
            });
            
            var varid_dp_cliente = document.getElementById("hojavidadatacuenta-id_dp_cliente").value;
            var varidrequester = document.getElementById("requester").value;
            var varcodpcrc = document.querySelectorAll('#requester option:checked');
            var varlistcodpcrc = Array.from(varcodpcrc).map(el => el.value);
            var varidrequester2 = document.getElementById("requester2").value;
            var vardirector = document.querySelectorAll('#requester2 option:checked');
            var varlistdirector = Array.from(vardirector).map(el => el.value);
            var varidrequester3 = document.getElementById("requester3").value;
            var vargerente = document.querySelectorAll('#requester3 option:checked');
            var varlistgerente = Array.from(vargerente).map(el => el.value);

            if (varid_dp_cliente == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar un cliente","warning");
              return;
            }
            if (varidrequester == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar al menos un pcrc","warning");
              return;
            }
            if (varidrequester2 == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar el menos un gerente","warning");
              return;
            }
            if (varidrequester3 == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar al menos un director","warning");
              return;
            }

            // Esta accion permite guardar el tercer bloque...
            $.ajax({
              method: "get",
              url: "guardarcuentas",
              data: {
                txtvarautoincrement : varautoincrement,
                txtvarid_dp_cliente : varid_dp_cliente,
                txtvaridrequester : varlistcodpcrc,
                txtvaridrequester2 : varlistdirector,
                txtvaridrequester3 : varlistgerente,

              },
              success : function(response){
                numRta =   JSON.parse(response);
                console.log(numRta);
              }
            });


            var varidprofesion = document.getElementById("hojavidadataacademica-idhvcursosacademico").value;
            var varidespecializacion = document.getElementById("hojavidadataacademica-anulado").value;
            var varidmaestria = document.getElementById("hojavidadataacademica-hv_idpersonal").value;
            var variddoctorado = document.getElementById("hojavidadataacademica-usua_id").value;
            var varidestado = document.getElementById("idestado").value;

            // Esta accion permite guardar el cuarto bloque...
            $.ajax({
              method: "get",
              url: "guardaracademicos",
              data: {
                txtvarautoincrement : varautoincrement,
                txtvaridprofesion : varidprofesion,
                txtvaridespecializacion : varidespecializacion,
                txtvaridmaestria : varidmaestria,
                txtvariddoctorado : variddoctorado,
                txtvaridestado : varidestado,

              },
              success : function(response){
                numRta =   JSON.parse(response);
              }
            });

            window.open('../hojavida/index','_self');

          }

        }
      });

    }


  }
</script>