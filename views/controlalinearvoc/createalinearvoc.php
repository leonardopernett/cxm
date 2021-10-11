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

$this->title = 'Instrumento Alinear + VOC';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $valor = null;

    $varIdArbol = $txtPcrcS;
    $varIdValora = $txtValoradorS;
    $varNomArbol = $txtNomPcrc;
    $varNomValora = $txtNomValora;
    $varDimension =  $txtDimensionS;
    $varsesion = $txtsesionS;
    $varSesionUsa = $varsesion;
    $fechaactual = date("Y-m-d");
    $varControl = 0;
    if($varsesion == 3){
       $varSesionUsa = '1, 2';
    }

    $txtEquipo = Yii::$app->db->createCommand("select equipo_id from tbl_equipos_evaluados where evaluado_id = '$varIdValora'")->queryScalar();

  $txtLider = Yii::$app->db->createCommand("select usua_id from tbl_equipos where id = '$txtEquipo'")->queryScalar();
  $txtNameLider = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = '$txtLider'")->queryScalar();

?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
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
        background-image: url('../../images/Alinear-+.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
      }
</style>

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
<div class="ceroBloque" style="display: none;" id="ceros">
    <div class="col-md-offset-2 col-sm-8">
      <div class="row">
        <div class="col-md-12">
          <div class="card1 mb">
            <label style="font-size: 20px;"><i class="fas fa-check-circle" style="font-size: 20px; color: #51EB47;"></i> Los datos han sido guardados satisfactoriamente...</label><br>
            <div style="text-align: center">
              <?= Html::a('Aceptar',  ['index'], ['class' => 'btn btn-success',
                          'style' => 'background-color: #707372',]) 
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
<div  id="unos" class="principal" style="display: inline;">
<div class="PrimerBloque" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
      <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
        <label><i class="fas fa-edit" style="font-size: 20px; color: #559FFF;"></i> Información de partida:</label>
          <div class="row">
            <div class="col-md-6">
              <label for="txtPcrc" style="font-size: 14px;">Programa o PCRC</label>
              <input type="text" class="form-control" readonly="readonly" id="txtPcrc" value="<?php echo $varNomArbol; ?>" data-toggle="tooltip" title="Programa o PCRC.">
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
               <label for="txtFechaHora" style="font-size: 14px;">Fecha</label>
               <input type="date" id="txtFechaHora" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha">
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
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label><i class="fas fa-tags" style="font-size: 20px; color: #49941e;"></i> Categorías:</label>
        <div class="row">
          <div class="col-md-12">
            <label for="txtIndiGlo" style="font-size: 14px;">Indicadores</label>
            <?=
                  $form->field($model2, 'participan_id', ['labelOptions' => ['class' => 'col-md-8'] ])
                    ->widget(Select2::classname(), [
                        'language' => 'es',
                        'options' => ['id' => 'idparticipa','placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'multiple' => true,
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['participantemultiple']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['participantemultiple']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                        ]
                  ]
            );
            ?>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-md-12">
           <table class="table table-striped table-bordered detail-view formDinamico" id="tablacate">
              <thead>
                <tr>
                  <th class="text-center"><?= Yii::t('app', 'Id listado') ?></th>
                  <th class="text-center"><?= Yii::t('app', 'Servicio') ?></th>
                  <th class="text-center"><?= Yii::t('app', 'Nombre Sesion') ?></th>
                  <th class="text-center"><?= Yii::t('app', 'Nombre Categoria') ?></th>
                  <th class="text-center"><?= Yii::t('app', 'Nombre Atributos') ?></th>
                  <th class="text-center"><?= Yii::t('app', 'Medir Atributo') ?></th>
                  <th class="text-center"><?= Yii::t('app', 'Conclusiones') ?></th>
                </tr>      
              </thead>
              <tbody>    
                <?php
                $txtQuery2 =  new Query;
                          $txtQuery2  ->select(['tbl_categorias_alinear.id_categ_ali','tbl_arbols.name','tbl_sesion_alinear.sesion_nombre','tbl_categorias_alinear.categoria_nombre', 'tbl_atributos_alinear.atributo_nombre', 'tbl_atributos_alinear.id_atrib_alin'])
                                      ->from('tbl_categorias_alinear')
                                      ->join('INNER JOIN', 'tbl_sesion_alinear',
                                          'tbl_categorias_alinear.sesion_id = tbl_sesion_alinear.sesion_id')
                                      ->join('INNER JOIN', 'tbl_arbols',
                                          'tbl_categorias_alinear.arbol_id = tbl_arbols.id')
                                      ->join('INNER JOIN', 'tbl_atributos_alinear',
                                          'tbl_categorias_alinear.id_categ_ali = tbl_atributos_alinear.id_categ_ali')
                                      ->where(['tbl_arbols.id' => $varIdArbol])
                                      ->andwhere('tbl_categorias_alinear.sesion_id IN (' . $varSesionUsa . ')');
                          $command = $txtQuery2->createCommand();
                          $dataProvider = $command->queryAll();
                  
                  $index=0;
                  foreach ($dataProvider as $key => $value) {
                      $varIdList = $value['id_categ_ali'];
                      $varNomList = $value['name'];         
                      $varSesionnomList = $value['sesion_nombre'];
                      $varCategonomList = $value['categoria_nombre'];
                      $varAtributnomList = $value['atributo_nombre'];            
                      $varIdatributoList = $value['id_atrib_alin'];
                      $index++;       
                ?>
                <tr>
                  <td class="text-center"><?php echo $varIdList; ?></td>
                  <td class="text-center"><?php echo $varNomList; ?></td>
                  <td class="text-center"><?php echo $varSesionnomList; ?></td>
                  <td class="text-center"><?php echo $varCategonomList; ?></td>
                  <td class="text-center"><?php echo $varAtributnomList; ?></td>
                  <td> <select id="txtmedir_<?php echo $index?>" class ='form-control'>
                          <option value="" disabled selected>Alineado Si/No</option>
                          <option value="Si">SI</option>
                          <option value="No">NO</option>
                          <option value="NA">N/A</option>
                        </select>
                  </td>
                  <td><input type="text" class="form-control" id="txtAcuerdo_<?php echo $index?>" data-toggle="tooltip" title="Acuerdo Alineacion"></td>
                  <td style="display:none;"><input type="text" class="form-control" id="txtatribu_<?php echo $index?>" value="<?php echo $varIdatributoList; ?>"></td>
                </tr>
                <?php
                  }
                ?>
              </tbody>    
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<hr>
<div class="TerceroBloque" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label><i class="fas fa-tasks" style="font-size: 20px; color: #C148D0;"></i> Plan de Acción:</label>
        <div class="row">
          <div class="col-md-6">
              <div id="dtbloque2" class="col-sm-12" style="display: inline">
                <p>
                    <b>&nbsp;&nbsp; Se requiere realizar plan de accion?</b> <br>

                    &nbsp;&nbsp;<label><input id = "requieresi" type="radio" name="plan" value="si" onclick="planaccion()"> SI </label><br>

                    &nbsp;&nbsp;<label><input id = "requiereno" type="radio" name="plan" value="no" onclick="planaccion2()"> NO </label><br>   
                </p>
              </div>
          </div>
      </div>
    </div>
        <div class="col-md-12" id="tablesi" style="display: none">
        <hr>
          <div class="card1 mb">  
            <div class="row">
              <div class="col-md-6"> 
                <label for="txtConcepto_mejora">Concepto de mejora</label>
                    <input type="text" class="form-control"  id="txtConcepto_mejora"  data-toggle="tooltip" title="Concepto Mejora">  
              </div>
              <div class="col-md-6">      
                <label for="txtAnalisis_causa">Analisis de Causa</label>
                  <input type="text" id="txtAnalisis_causa" name="txtAnalisis_causa"  class="form-control" data-toggle="tooltip" title="Analisis de Causa">
              </div>
            </div>  
            <div class="row">
              <div class="col-md-6"> 
                <label for="txtAccion_seguir">Acción a Seguir</label>
                    <input type="text" id="txtAccion_seguir" name="txtAccion_seguir" class="form-control" data-toggle="tooltip" title="Acci�n a Seguir">            
              </div>
              <div class="col-md-6">      
                <label for="txtTipo_accion">Tipo de acción</label>                  
                <select id="txtTipo_accion" class ='form-control'>
                  <option value="" disabled selected>Seleccione accion</option>
                  <option value="Correctiva">Correctiva</option>
                  <option value="Preventiva">Preventiva</option>
                  <option value="Mejora">Mejora</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6"> 
                <label for="txtResponsable">Responsable</label>
                  <input type="text" id="txtResponsable" name="txtResponsable" class="form-control" data-toggle="tooltip" title="Responsable">              
              </div>
              <div class="col-md-6">      
                <label for="txtFecha_plan">Fecha Plan</label>                    
                  <input type="date" class="form-control" id="txtFecha_plan" name="txtFecha_plan" data-toggle="tooltip" title="Fecha Plan">
              </div>              
            </div> 
            <div class="row">
              <div class="col-md-6"> 
                <label for="txtFecha_implementa">Fecha de Implementación</label>
                    <input type="date" class="form-control" id="txtFecha_implementa" name="txtFecha_implementa" data-toggle="tooltip" title="Fecha de Implementacion">               
              </div>
              <div class="col-md-6">      
                <label for="txtEstado">Estado</label>
                <select id="txtEstado" class ='form-control'>
                  <option value="" disabled selected>Seleccione estado</option>
                  <option value="Inicio">Inicio</option>
                  <option value="Encurso">En Curso</option>
                  <option value="Finalizado">Finalizado</option>
                </select>
              </div>              
            </div>
            <div class="row">
              <div class="col-md-6"> 
              <label for="txtObservaciones">Observaciones</label>
                    <textarea type="text" class="form-control" id="txtObservaciones" data-toggle="tooltip" title="Observaciones"></textarea>              
              </div>
            </div>
      </div>
    </div>
  </div>
</div>
<hr>
<div class="CuartoBloque" style="display: inline;">
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
                <div onclick="generaatributo();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
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
                  <?= Html::button('Crear Feedback', ['value' => url::to(['indexfeedback','valoradoid' => $varIdValora, 'varatributo' => 1]), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                              'data-toggle' => 'tooltip',
                              'title' => 'Crear Feedback', 'style' => 'background-color: #4298b4']) 
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

/* BOTON DESPLEGAR SECCIONES */
        //$(".soloAbrir").click(function () {
          $("#prueba").on( "click", function() {
            if ($("#prueba").text() == "Desplegar"){
                $("[id*=datos]").css('display', 'block');
                $("#prueba").text('Plegar');
            }else{
                $("[id*=datos]").css('display', 'none');
                $("#prueba").text('Desplegar');
            }
        });
        
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

  var varBloque0 = document.getElementById("idBloques0");
  var varBloque1 = document.getElementById("idBloques1");

  function categorizar(){
    var varRta = document.getElementById("txtCategorizada").value;
    var varPartO = document.getElementById("parteOne");
    var varPartT = document.getElementById("parteTwo");

    if (varRta == "No") {
      varPartT.style.display = 'inline';
      varPartO.style.display = 'inline';
    }else{
      varPartT.style.display = 'none';
      varPartO.style.display = 'none';
    }    
  };
 function seciones(){
    var varRta = document.getElementById("txtSesion").value;
    var varPartT = document.getElementById("table1");
    var varPartO = document.getElementById("table2");
    if (varRta == "1") {
      varPartT.style.display = 'inline';
      varPartO.style.display = 'none';
    }else{
      varPartT.style.display = 'none';
      varPartO.style.display = 'inline';
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
    if (varRta == "no") {
      varPartT.style.display = 'none';
    }else{
      varPartT.style.display = 'inline';
    }    
  };
  function listah(){
    var varMotivo = document.getElementById("txtMotivoC").value;
   
      $.ajax({
              method: "post",
              url: "listashijo",
              data : {
                txtvmotivo : varMotivo,
              },
              success : function(response){ 
                          var Rta =   JSON.parse(response);    
                          console.log(Rta);
                          document.getElementById("txtMotivoL").innerHTML = "";
                          for (var i = 0; i < Rta.length; i++) {
                              var node = document.createElement("OPTION");
                              node.setAttribute("value", Rta[i].idlistahijovoc);
                              var textnode = document.createTextNode(Rta[i].nombrelistah);
                              node.appendChild(textnode);
                              document.getElementById("txtMotivoL").appendChild(node);
                          }
                      }
      });    
  };

  function enviar(){
    window.open('../basesatisfaccion/alertas','_blank');
  };

  function generar(){
   // generaatributo();

    var varArbol = "<?php echo $varIdArbol; ?>";
    var varValoraddo = "<?php echo $varIdValora; ?>";
    var varSpeech = document.getElementById("txtIDExtSp").value;
    var varFH = document.getElementById("txtFechaHora").value;
    var varuSUAgente = document.getElementById("txtUsuAge").value;
    var varDuracion = document.getElementById("txtDuracion").value;
    var varExt = document.getElementById("txtExtencion").value;
    var varDimension = document.getElementById("txtDimension").value;
    var varLider = document.getElementById("txtLider").value;
    
    var varSesion = "<?php echo $varsesion; ?>";
    var varConcepto_mejora = document.getElementById("txtConcepto_mejora").value;
    var varAnalisis_causa = document.getElementById("txtAnalisis_causa").value;
    var varAccion_seguir = document.getElementById("txtAccion_seguir").value;
    var varTipo_accion = document.getElementById("txtTipo_accion").value;
    var varResponsable = document.getElementById("txtResponsable").value;
    var varFecha_plan = document.getElementById("txtFecha_plan").value;
    var varFecha_implementa = document.getElementById("txtFecha_implementa").value;    
    var varEstado = document.getElementById("txtEstado").value;
    var varObservaciones = document.getElementById("txtObservaciones").value;
    var varIdparticipa = document.getElementById("idparticipa").value;
   // alert(varIdparticipa);

   if (varSpeech == ""){
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","No hay datos a registrar en ID Externo Llamada Speech (Informaci�n de Partida).","warning");
      return;
   } else if(varFH == ""){
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","No hay datos a registrar en Fecha y hora (Informaci�n de Partida).","warning");
      return;
   } else if(varuSUAgente == ""){
    event.preventDefault();
      swal.fire("!!! Advertencia !!!","No hay datos a registrar en Usuario de Agente (Informaci�n de Partida).","warning");
      return;
   } else if(varDuracion == ""){
    event.preventDefault();
      swal.fire("!!! Advertencia !!!","No hay datos a registrar en Duraci�n (Informaci�n de Partida).","warning");
      return;
    }
      else if(varExt == ""){
    event.preventDefault();
      swal.fire("!!! Advertencia !!!","No hay datos a registrar en Extensi�n (Informaci�n de Partida).","warning");
      return;  


   /* if (varSpeech == "" || varFH == "" || varuSUAgente == "" || varDuracion == "" || varExt == "" || varDimension == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","No hay datos a registrar.","warning");
      return;*/
    }else{
      $.ajax({
              method: "post",
              url: "createfocalizada",
              data : {
                txtvArbol : varArbol,
                txtvValorado : varValoraddo,
                txtvSpeech : varSpeech,
                txtvFH : varFH,
                txtvAgenteu : varuSUAgente,
                txtvDuracion : varDuracion,
                txtvExt : varExt,
                txtvDimension : varDimension,
                txtvLider : varLider,

                txtSesion : varSesion,
                txtConcepto_mejora : varConcepto_mejora,
                txtAnalisis_causa : varAnalisis_causa,
                txtAccion_seguir : varAccion_seguir,
                txtTipo_accion : varTipo_accion,
                txtResponsable : varResponsable,
                txtFecha_plan : varFecha_plan,
                txtFecha_implementa : varFecha_implementa,
                txtEstado : varEstado,
                txtObservaciones : varObservaciones,
                txtIdparticipa : varIdparticipa,
              },
              success : function(response){ 
                          var numRta =   JSON.parse(response);    
                          console.log(response);
                          //console.log(numRta);
      //  console.log('Prueba');

                          if (numRta != 0) {
                            jQuery(function(){
                                swal.fire({type: "success",
                                    title: "!!! OK !!!",
                                    text: "Datos guardados correctamente."
                                }).then(function() {
            window.location.href = '../controlalinearvoc/index';
            
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

   function generaatributo(){
    var table = document.getElementById("tablacate");
    var rowCount = table.rows.length;  
    
    var varAcuerdo = null;
    var varMedir = null;
    var varIdatrubuti = null;
    var varindica = null;
    var varIdvalorado = "<?php echo $varIdValora; ?>";
    var varfechai = "<?php echo $fechaactual; ?>";
    var varanulado = 0;

    var array_elementos = [];

    for (var x = 0; x < rowCount-1; x++) {

         var current_object = {};

         //var idic1 = "txtmedir_"+(x+1);
         varMedir = document.getElementById("txtmedir_"+(x+1)).value;
         //var idic2 = "txtAcuerdo_"+(x+1);
         varAcuerdo = document.getElementById("txtAcuerdo_"+(x+1)).value;
         //var idic3 = "txtatribu_"+(x+1);         
         varIdatrubuti = document.getElementById("txtatribu_"+(x+1)).value;

         console.log("varMedir",varMedir);
         console.log("varAcuerdo",varAcuerdo);
         console.log("varIdatrubuti",varIdatrubuti);

         current_object.varMedir = varMedir;
         current_object.varAcuerdo = varAcuerdo;
         current_object.varIdatrubuti = varIdatrubuti;
         current_object.varIdvalorado = varIdvalorado;
         current_object.varfechai = varfechai;
         current_object.varanulado = varanulado;

         array_elementos.push(current_object);
    }

    var varSpeech = document.getElementById("txtIDExtSp").value;
    var varFH = document.getElementById("txtFechaHora").value;
    var varuSUAgente = document.getElementById("txtUsuAge").value;
    var varDuracion = document.getElementById("txtDuracion").value;
    var varExt = document.getElementById("txtExtencion").value;

   if (varSpeech == ""){
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","No hay datos a registrar en ID Externo Llamada Speech (Informaci�n de Partida).","warning");
      return;
   } else if(varFH == ""){
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","No hay datos a registrar en Fecha y hora (Informaci�n de Partida).","warning");
      return;
   } else if(varuSUAgente == ""){
    event.preventDefault();
      swal.fire("!!! Advertencia !!!","No hay datos a registrar en Usuario de Agente (Informaci�n de Partida).","warning");
      return;
   } else if(varDuracion == ""){
    event.preventDefault();
      swal.fire("!!! Advertencia !!!","No hay datos a registrar en Duraci�n (Informaci�n de Partida).","warning");
      return;
    }
      else if(varExt == ""){
    event.preventDefault();
      swal.fire("!!! Advertencia !!!","No hay datos a registrar en Extensi�n (Informaci�n de Partida).","warning");
      return;  

    }else{
      
      var varPartT = document.getElementById("botones2");
      varPartT.style.display = 'none';
    
    //AJAX
    $.ajax({
              method: "post",
              url: "createmediratributoalinearvoc",
              data : {
                data : JSON.stringify(array_elementos)
              },
              success : function(response){ 
                //console.log(response);
                var numRta =   JSON.parse(response); 
                //console.log("llego");
                if (numRta != 0) {
                  console.log("resp_aajx",response);
                  generar();
                  $(varModal).modal("hide");
                }else{
                  event.preventDefault();
                  swal.fire("!!! Advertencia !!!","No se pueden guardar los datos.","warning");
                  return;
                }
              }
      });
    //AJAX END
    }
  };
</script>
