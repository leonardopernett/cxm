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

$this->title = 'Instrumento Escucha Focalizada - VOC -';
$this->params['breadcrumbs'][] = $this->title;

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

$varNomValora = Yii::$app->db->createCommand("select name from tbl_evaluados where id = $txtUsuario")->queryScalar();
$varNomArbol = Yii::$app->db->createCommand("select distinct pcrc from tbl_speech_categorias where anulado = 0 and cod_pcrc in ('$txtServicio')")->queryScalar();
$varDimension = "Escucha Focalizada";

$varNombreClient = Yii::$app->db->createCommand("select distinct nameArbol from tbl_speech_servicios where anulado = 0 and id_dp_clientes = $txtCliente")->queryScalar();

$txtEquipo = Yii::$app->db->createCommand("select equipo_id from tbl_equipos_evaluados where evaluado_id = '$txtUsuario'")->queryScalar();

$txtLider = Yii::$app->db->createCommand("select usua_id from tbl_equipos where id = '$txtEquipo'")->queryScalar();
$txtNameLider = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = '$txtLider'")->queryScalar();

?>
<link rel="stylesheet" href="https://qa.grupokonecta.local/qa_managementv2/web/font_awesome_local/css.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<style type="text/css">
  @import url('https://fonts.googleapis.com/css?family=Nunito');
    .card {
            height: 200px;
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
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }

    .card:hover .card1:hover {
        top: -15%;
    }

      .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/Inst.-Escucha-Focalizada.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
      }
</style>
<link rel="stylesheet" type="text/css" href="web/../../../assets/6418f0aa/css/daterangepicker.css">
<link rel="stylesheet" type="text/css" href="web/../../../assets/6418f0aa/css/daterangepicker-kv.css">
<script type="text/javascript" src="web/../../../assets/6418f0aa/js/moment.js"></script>
<script type="text/javascript" src="web/../../../assets/6418f0aa/js/daterangepicker.js"></script>
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
<div  id="unos" class="principal" style="display: inline;">
<div class="PrimerBloque" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label><i class="fas fa-edit" style="font-size: 20px; color: #559FFF;"></i> Información general:</label>
          <div class="row">
            <div class="col-md-6">
              <label for="txtPcrc" style="font-size: 14px;">Programa o PCRC</label>
              <input type="text" class="form-control" readonly="readonly" id="txtPcrc" value="<?php echo $varNombreClient.':  '.$txtServicio.' - '.$varNomArbol; ?>" data-toggle="tooltip" title="Programa o PCRC.">
            </div>
            <div class="col-md-6">
              <label for="txtValorado" style="font-size: 14px;">Valorado</label>
              <input type="text" id="txtValorado" name="datetimes" readonly="readonly" value="<?php echo $varNomValora; ?>" class="form-control" data-toggle="tooltip" title="Valorado">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <label for="txtIDExtSp" style="font-size: 14px;">ID Externo Speech</label>
              <input type="text" class="form-control" id="txtIDExtSp" data-toggle="tooltip" title="Id Externo Speech.">   
            </div>
            <div class="col-md-6">
               <label for="txtFechaHora" style="font-size: 14px;">Fecha y Hora</label>
                <input type="datetime-local" id="txtFechaHora" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha & Hora">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <label for="txtUsuAge" style="font-size: 14px;">Usuario de Agente</label>
              <input type="text" class="form-control" id="txtUsuAge" data-toggle="tooltip" title="Usuario de Agente">    
            </div>
            <div class="col-md-6">
               <label for="txtDuracion" style="font-size: 14px;">Duración en segundos</label>                    
                    <input type="text" class="form-control" id="txtDuracion" data-toggle="tooltip" title="Duracion de la llamada" onkeypress="return valida(event)">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <label for="txtExtencion" style="font-size: 14px;">Extensión</label>
              <input type="text" class="form-control" id="txtExtencion" onkeypress="return valida(event)" data-toggle="tooltip" title="Extensión">    
            </div>
            <div class="col-md-6">
               <label for="txtDimension" style="font-size: 14px;">Dimensión</label>
                <input type="text" class="form-control" id="txtDimension" readonly="readonly" value="<?php echo $varDimension; ?>" data-toggle="tooltip" title="Dimensión">  
                <input type="text" class="form-control" style="display: none" id="txtLider" readonly="readonly" value="<?php echo $txtLider; ?>" data-toggle="tooltip" title="Dimensión">
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
<hr>
<div class="SegundoBloque" style="display: inline;">
    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
            <label><i class="fas fa-headphones" style="font-size: 20px; color: #C148D0;"></i> Escucha focalizada:</label>
                <div class="row">
                    <div class="col-md-6">
                    <label for="txtIndiGlo" style="font-size: 14px;">Indicadores</label>
                    <?=  $form->field($model, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechCategorias::find()->distinct()->where("anulado = 0")->andwhere("idcategorias = 1")->andwhere("cod_pcrc in ('$txtServicio')")->orderBy(['nombre'=> SORT_ASC])->all(), 'idspeechcategoria', 'nombre'),
                                        [
                                            'id' => 'txtIndiGlo',
                                            'prompt'=>'Seleccionar indicador...',
                                            'onchange' => '
                                                $.post(
                                                    "' . Url::toRoute('formulariovoc/listarvariables') . '", 
                                                    {id: $(this).val()}, 
                                                    function(res){
                                                        $("#txtVariable").html(res);
                                                    }
                                                );
                                            ',

                                        ]
                            )->label(''); 
                    ?>
                    </div>
                    <div class="col-md-6">
                        <label for="txtIndiGlo" style="font-size: 14px;">Variables</label>
                        <?= $form->field($model,'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                        [],
                                        [
                                            'prompt' => 'Seleccionar variable...',
                                            'id' => 'txtVariable'
                                        ]
                                    )->label('');
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="txtIndiGlo" style="font-size: 14px;">Motivos de contacto</label>
                        <?=  $form->field($model, 'tipoindicador', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechCategorias::find()->distinct()->where("anulado = 0")->andwhere("idcategorias = 3")->andwhere("cod_pcrc in ('$txtServicio')")->orderBy(['nombre'=> SORT_ASC])->all(), 'idspeechcategoria', 'nombre'),
                                        [
                                            'id' => 'txtMotivoC',
                                            'prompt'=>'Seleccionar motivo de contacto...',

                                        ])->label(''); 
                        ?>
                    </div>
                    <div class="col-md-6">
                        <label for="txtIndiGlo" style="font-size: 14px;">Detalle motivos de contacto</label>
                        <?=  $form->field($model, 'tipocategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechCategorias::find()->distinct()->where("anulado = 0")->andwhere("idcategorias = 4")->andwhere("cod_pcrc in ('$txtServicio')")->orderBy(['nombre'=> SORT_ASC])->all(), 'idspeechcategoria', 'nombre'),
                                        [
                                            'id' => 'txtMotivoL',
                                            'prompt'=>'Seleccionar detalle motivo...',

                                        ])->label(''); 
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="txtIndiGlo" style="font-size: 14px;">Puntos de dolor -Si-No-</label>
                                <?php $var = ['1' => 'Si', '2' => 'No']; ?>
                                <?= $form->field($model, 'orientacionform', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($var, ['prompt' => 'Seleccionar opción...', 'id'=>"txtPuntosDolor", 'onchange'=>'accionpuntos();'])->label('') ?> 
                            </div>
                            <div class="col-md-8">
                                <label for="txtIndiGlo" style="font-size: 14px;">Ingresar detalle del punto de dolor</label>
                                <?= $form->field($model, 'otros', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200,  'id'=>'txtPuntoD', 'placeholder'=>'Ingresar el detalle en Punto de dolor', 'readonly'=>'false']) ?> 
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="txtIndiGlo" style="font-size: 14px;">Llamada Categorizada -Si-No-</label>
                                <?php $var = ['1' => 'Si', '2' => 'No']; ?>
                                <?= $form->field($model, 'orientacionform', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($var, ['prompt' => 'Seleccionar opción...', 'id'=>"txtCategorizada", 'onchange'=>'accionorientacion();'])->label('') ?> 
                            </div>
                            <div class="col-md-8">
                                <label for="txtIndiGlo" style="font-size: 14px;">Ingresar detalle de la llamada categorizada</label>
                                <?= $form->field($model, 'otros', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200,  'id'=>'txtAjusteC', 'placeholder'=>'¿Por que no esta categorizada?', 'readonly'=>'false']) ?> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="txtIndiGlo" style="font-size: 14px;">% Indicador afectado de la variable o motivo o el punto de dolor</label>
                        <?= $form->field($model, 'extension', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200,  'id'=>'txtPorcentajeAfe', 'placeholder'=>'Agregar solo numero', 'onkeypress'=>'return valida(event)']) ?> 
                    </div>
                    <div class="col-md-6">
                        <label for="txtIndiGlo" style="font-size: 14px;">Agente (Detalle de Responsabilidad)</label>
                        <?=  $form->field($model, 'usabilidad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\FormvocAcciones::find()->distinct()->where("anulado = 0")->andwhere("idacciones = 2")->andwhere("iddetalle = 1")->orderBy(['acciones'=> SORT_ASC])->all(), 'idformvocacciones', 'acciones'),
                                        [
                                            'id' => 'txtAgente',
                                            'prompt'=>'Seleccionar agente detalle...',

                                        ])->label(''); 
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="txtIndiGlo" style="font-size: 14px;">Marca (Detalle de Responsabilidad)</label>
                        <?=  $form->field($model, 'usabilidad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\FormvocAcciones::find()->distinct()->where("anulado = 0")->andwhere("idacciones = 2")->andwhere("iddetalle = 3")->orderBy(['acciones'=> SORT_ASC])->all(), 'idformvocacciones', 'acciones'),
                                        [
                                            'id' => 'txtMarca',
                                            'prompt'=>'Seleccionar marca detalle...',

                                        ])->label(''); 
                        ?>
                    </div>
                    <div class="col-md-6">
                        <label for="txtIndiGlo" style="font-size: 14px;">Canal (Detalle de Responsabilidad)</label>
                        <?=  $form->field($model, 'usabilidad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\FormvocAcciones::find()->distinct()->where("anulado = 0")->andwhere("idacciones = 2")->andwhere("iddetalle = 2")->orderBy(['acciones'=> SORT_ASC])->all(), 'idformvocacciones', 'acciones'),
                                        [
                                            'id' => 'txtCanal',
                                            'prompt'=>'Seleccionar canal detalle...',

                                        ])->label(''); 
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="txtIndiGlo" style="font-size: 14px;">Atributos de calidad</label>
                        <?=  $form->field($model, 'usabilidad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\FormvocAcciones::find()->distinct()->where("anulado = 0")->andwhere("idacciones = 3")->andwhere("iddetalle = 4")->orderBy(['acciones'=> SORT_ASC])->all(), 'idformvocacciones', 'acciones'),
                                        [
                                            'id' => 'txtatributos',
                                            'prompt'=>'Seleccionar atributo de calidad...',

                                        ])->label(''); 
                        ?>
                    </div>
                    <div class="col-md-6">
                        <label for="txtIndiGlo" style="font-size: 14px;">Mapa de interesados 1</label>
                        <?=  $form->field($model, 'usabilidad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\FormvocAcciones::find()->distinct()->where("anulado = 0")->andwhere("idacciones = 4")->andwhere("iddetalle = 4")->orderBy(['acciones'=> SORT_ASC])->all(), 'idformvocacciones', 'acciones'),
                                        [
                                            'id' => 'txtMapa1',
                                            'prompt'=>'Seleccionar mapa de interesados 1...',

                                        ])->label(''); 
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="txtIndiGlo" style="font-size: 14px;">Mapa de interesados 2</label>
                        <?=  $form->field($model, 'usabilidad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\FormvocAcciones::find()->distinct()->where("anulado = 0")->andwhere("idacciones = 4")->andwhere("iddetalle = 4")->orderBy(['acciones'=> SORT_ASC])->all(), 'idformvocacciones', 'acciones'),
                                        [
                                            'id' => 'txtMapa2',
                                            'prompt'=>'Seleccionar mapa de interesados 2...',

                                        ])->label(''); 
                        ?>
                    </div>
                    <div class="col-md-6">
                        <label for="txtIndiGlo" style="font-size: 14px;">Mapa de interesados 3</label>
                        <?=  $form->field($model, 'usabilidad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\FormvocAcciones::find()->distinct()->where("anulado = 0")->andwhere("idacciones = 4")->andwhere("iddetalle = 4")->orderBy(['acciones'=> SORT_ASC])->all(), 'idformvocacciones', 'acciones'),
                                        [
                                            'id' => 'txtMapa3',
                                            'prompt'=>'Seleccionar mapa de interesados 1...',

                                        ])->label(''); 
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="txtIndiGlo" style="font-size: 14px;">Detalle cualitativo (Detalle de Responsabilidad)</label>
                        <?= $form->field($model, 'extension', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500,  'id'=>'txtDcualitativo', 'placeholder'=>'Ingresar el detalle del proceso']) ?> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<hr>
<div class="TercerBloque" style="display: inline;">
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb">
        <label><i class="fas fa-cogs" style="font-size: 20px; color: #1e8da7;"></i> Acciones:</label>
          <div class="row">
            <div class="col-md-6">
              <div class="card2 mb">  
                <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372']) 
                ?>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card2 mb"> 
                <div onclick="generated();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                  Guardar Informacion
                </div> 
              </div>
            </div>
          </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card1 mb">
        <label><i class="fas fa-paperclip" style="font-size: 20px; color: #FFC72C;"></i> Feedback & Alertas:</label>
          <div class="row">            
            <div class="col-md-6">
              <div class="card2 mb">
                  <?= Html::button('Crear Feedback', ['value' => url::to(['controlvoc/indexfeedback','valoradoid' => $txtUsuario, 'varatributo' => 1]), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                              'data-toggle' => 'tooltip',
                              'title' => 'Crear Dimensionamiento', 'style' => 'background-color: #4298b4']) 
                  ?> 

                  <?php
                    Modal::begin([
                      'header' => '<h4></h4>',
                      'id' => 'modal1',
                      //'size' => 'modal-lg',
                    ]);

                    echo "<div id='modalContent1'></div>";
                                          
                    Modal::end(); 
                  ?>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card2 mb">
                  <div onclick="enviar();" class="btn btn-primary" style="display:inline; height: 34px;" method='post' id="botones2" >
                    Crear Alertas
                </div>
              </div>                
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
<hr>
</div>

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

  $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
  });
    
    function accionpuntos(){
        var vartxtPuntosDolor = document.getElementById("txtPuntosDolor").value;

        if (vartxtPuntosDolor == "2") {
          document.getElementById("txtPuntoD").readOnly = true;
        }else{
            if (vartxtPuntosDolor == "1") {
                document.getElementById("txtPuntoD").readOnly = false;
                document.getElementById("txtPuntoD").value = "";
            }else{
                if (vartxtPuntosDolor == "") {
                    document.getElementById("txtPuntoD").readOnly = false;
                    document.getElementById("txtPuntoD").value = "";
                }
            }          
        }
    };

    function accionorientacion(){
        var varcategoriza = document.getElementById("txtCategorizada").value;

        if (varcategoriza == "1") {
          document.getElementById("txtAjusteC").readOnly = true;
        }else{
            if (varcategoriza == "2") {
                document.getElementById("txtAjusteC").readOnly = false;
                document.getElementById("txtAjusteC").value = "";
            }else{
                if (varcategoriza == "") {
                    document.getElementById("txtAjusteC").readOnly = false;
                    document.getElementById("txtAjusteC").value = "";
                }
            }          
        }
    };

    function enviar(){
        window.open('https://qa.grupokonecta.local/qa_managementv2/web/index.php/basesatisfaccion/alertas','_blank');
    };

    function generated(){
    var varCeros = document.getElementById("ceros");
    var varUnos = document.getElementById("unos");

    var varArbol = "<?php echo $txtCliente; ?>";
    var varValoraddo = "<?php echo $txtUsuario; ?>";
    // console.log(varValoraddo);

    var vartxtPcrc = "<?php echo $txtServicio; ?>";
    var vartxtValorado = document.getElementById("txtValorado").value;
    var vartxtIDExtSp = document.getElementById("txtIDExtSp").value;
    var vartxtFechaHora = document.getElementById("txtFechaHora").value;
    var vartxtUsuAge = document.getElementById("txtUsuAge").value;
    var vartxtDuracion = document.getElementById("txtDuracion").value;
    var vartxtExtencion = document.getElementById("txtExtencion").value;
    var vartxtDimension = document.getElementById("txtDimension").value;

    var vartxtIndiGlo = document.getElementById("txtIndiGlo").value;
    var vartxtVariable = document.getElementById("txtVariable").value;
    var vartxtMotivoC = document.getElementById("txtMotivoC").value;
    var vartxtMotivoL = document.getElementById("txtMotivoL").value;
    var vartxtPuntoD = document.getElementById("txtPuntoD").value;
    var vartxtCategorizada = document.getElementById("txtCategorizada").value;
    var vartxtAjusteC = document.getElementById("txtAjusteC").value;
    var vartxtPorcentajeAfe = document.getElementById("txtPorcentajeAfe").value;
    var vartxtAgente = document.getElementById("txtAgente").value;
    var vartxtMarca = document.getElementById("txtMarca").value;
    var vartxtCanal = document.getElementById("txtCanal").value;
    var vartxtDcualitativo = document.getElementById("txtDcualitativo").value;
    var vartxtatributos = document.getElementById("txtatributos").value;
    var vartxtMapa1 = document.getElementById("txtMapa1").value;
    var vartxtMapa2 = document.getElementById("txtMapa2").value;
    var vartxtMapa3 = document.getElementById("txtMapa3").value;

    if (vartxtPcrc == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Falta el campo programa o PCRC.","warning");          
      document.getElementById("txtPcrc").style.border = '1px solid #ff2e2e';
      return; 
    }else{
      if (vartxtValorado == "") {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Falta el campo valorado.","warning");          
        document.getElementById("txtValorado").style.border = '1px solid #ff2e2e';
        return; 
      }else{
        if (vartxtIDExtSp == "") {
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","Falta el campo Id Speech","warning");          
          document.getElementById("txtIDExtSp").style.border = '1px solid #ff2e2e';
          return; 
        }else{
          if (vartxtFechaHora == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Falta el campo fecha y hora","warning");          
            document.getElementById("txtFechaHora").style.border = '1px solid #ff2e2e';
            return; 
          }else{
            if (vartxtUsuAge == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Falta el campo usuario del agente","warning");
              document.getElementById("txtUsuAge").style.border = '1px solid #ff2e2e';
              return; 
            }else{
              if (vartxtDuracion == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Falta el campo duración llamada","warning");
                document.getElementById("txtDuracion").style.border = '1px solid #ff2e2e';
                return; 
              }else{
                if (vartxtExtencion == "") {
                  event.preventDefault();
                  swal.fire("!!! Advertencia !!!","Falta el campo extensión","warning");
                  document.getElementById("txtExtencion").style.border = '1px solid #ff2e2e';
                  return; 
                }else{
                  if (vartxtDimension == "") {
                    event.preventDefault();
                    swal.fire("!!! Advertencia !!!","Falta el campo dimension","warning");
                    document.getElementById("txtDimension").style.border = '1px solid #ff2e2e';
                    return; 
                  }else{
                    if (vartxtMotivoC == "1") {
                      // event.preventDefault();
                      // swal.fire("!!! Advertencia !!!","Falta el campo motivo de contacto","warning");
                      // document.getElementById("txtMotivoC").style.border = '1px solid #ff2e2e';
                      // return; 
                    }else{
                      if (vartxtCategorizada == "") {
                        event.preventDefault();
                        swal.fire("!!! Advertencia !!!","Falta el campo de llamada categorizada","warning");
                        document.getElementById("txtCategorizada").style.border = '1px solid #ff2e2e';
                        return; 
                      }else{
                        if (vartxtIndiGlo == "") {
                          event.preventDefault();
                          swal.fire("!!! Advertencia !!!","Falta el campo indicador","warning");
                          document.getElementById("txtIndiGlo").style.border = '1px solid #ff2e2e';
                          return; 
                        }else{
                          if (vartxtPorcentajeAfe == "") {
                            event.preventDefault();
                            swal.fire("!!! Advertencia !!!","Falta el campo de porcentaje indicador afectado","warning");
                            document.getElementById("txtPorcentajeAfe").style.border = '1px solid #ff2e2e';
                            return; 
                          }else{
                            if (vartxtAgente == "") {
                              event.preventDefault();
                              swal.fire("!!! Advertencia !!!","Falta el campo agente","warning");
                              document.getElementById("txtAgente").style.border = '1px solid #ff2e2e';
                              return; 
                            }else{
                              if (vartxtMarca == "") {
                                event.preventDefault();
                                swal.fire("!!! Advertencia !!!","Falta el campo marca","warning");
                                document.getElementById("txtMarca").style.border = '1px solid #ff2e2e';
                                return; 
                              }else{
                                if (vartxtCanal == "") {
                                  event.preventDefault();
                                  swal.fire("!!! Advertencia !!!","Falta el campo canal","warning");
                                  document.getElementById("txtCanal").style.border = '1px solid #ff2e2e';
                                  return; 
                                }else{
                                  if (vartxtDcualitativo == "") {
                                    event.preventDefault();
                                    swal.fire("!!! Advertencia !!!","Falta el campo detalle cualitativo","warning");
                                    document.getElementById("txtDcualitativo").style.border = '1px solid #ff2e2e';
                                    return; 
                                  }else{
                                    if (vartxtatributos == "") {
                                      event.preventDefault();
                                      swal.fire("!!! Advertencia !!!","Falta el campo atributos","warning");
                                      document.getElementById("txtatributos").style.border = '1px solid #ff2e2e';
                                      return; 
                                    }else{
                                      if (vartxtVariable == "") {
                                        event.preventDefault();
                                        swal.fire("!!! Advertencia !!!","Falta el campo variables","warning");
                                        document.getElementById("txtVariable").style.border = '1px solid #ff2e2e';
                                        return; 
                                      }else{
                                        $.ajax({
                                          method: "get",
                                          url: "createfocalizada",
                                          data: {
                                            txtPcrc : varArbol,
                                            txtcodpcrc : vartxtPcrc,
                                            txtValorado : vartxtValorado,
                                            txtIDExtSp : vartxtIDExtSp,
                                            txtFechaHora : vartxtFechaHora,
                                            txtUsuAge : vartxtUsuAge,
                                            txtDuracion : vartxtDuracion,
                                            txtExtencion : vartxtExtencion,
                                            txtDimension : vartxtDimension,
                                            txtValoraddo : varValoraddo,
                                          },
                                          success : function(response){ 
                                            var numRta =   JSON.parse(response);    
                                            console.log(numRta);

                                            if (numRta != 0) {
                                              $.ajax({
                                                method: "get",
                                                url: "createfocalizadapart2",
                                                data: {
                                                    txtPcrc : varArbol,
                                                    txtcodpcrc : vartxtPcrc,
                                                    txtValorado : vartxtValorado,
                                                    txtIDExtSp : vartxtIDExtSp,
                                                    txtFechaHora : vartxtFechaHora,
                                                    txtUsuAge : vartxtUsuAge,
                                                    txtDuracion : vartxtDuracion,
                                                    txtExtencion : vartxtExtencion,
                                                    txtDimension : vartxtDimension,
                                                    txtValoraddo : varValoraddo,

                                                    txtIndiGlo : vartxtIndiGlo,
                                                    txtVariable : vartxtVariable,
                                                    txtMotivoC : vartxtMotivoC,
                                                    txtMotivoL : vartxtMotivoL,
                                                    txtPuntoD : vartxtPuntoD,
                                                    txtCategorizada : vartxtCategorizada,
                                                    txtAjusteC : vartxtAjusteC,
                                                    txtPorcentajeAfe : vartxtPorcentajeAfe,
                                                    txtAgente : vartxtAgente,
                                                    txtMarca : vartxtMarca,
                                                    txtCanal : vartxtCanal,
                                                    txtDcualitativo : vartxtDcualitativo,
                                                    txtatributos : vartxtatributos,
                                                    txtMapa1 : vartxtMapa1,
                                                    txtMapa2 : vartxtMapa2,
                                                    txtMapa3 : vartxtMapa3,
                                                },
                                                success : function(response){ 
                                                    var numRta2 =   JSON.parse(response);    
                                                    console.log(numRta2);
                                                    // window.open('https://qa.grupokonecta.local/qa_managementv2/web/index.php/formulariovoc/index','_self');
                                                    window.open('https://qa.grupokonecta.local/qa_managementv2/web/index.php/formulariovoc/index','_self');
                                                }
                                              });
                                            }else{
                                              event.preventDefault();
                                              swal.fire("!!! Advertencia !!!","No se pueden guardar los datos.","warning");
                                              return;
                                            }
                                          }
                                        });
                                      }
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }      
    }
  };

</script>