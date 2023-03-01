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

$this->title = 'Plan de acción GPTW';
$this->params['breadcrumbs'][] = $this->title;
   
    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $valor = null;

    
   /* $data2 = Yii::$app->get('dbexperience')->createCommand("select usuarios.id, usuarios.nombre 
                                                        FROM usuarios
                                                        INNER JOIN cargos ON usuarios.cargo = cargos.id
                                                        ORDER BY usuarios.nombre")->queryAll();*/
    $data2 = (new \yii\db\Query())
        ->select(['usua_id', 'usua_nombre'])
        ->from(['tbl_usuarios'])
        ->where(['=','usua_activo','S'])
        ->All();
    
    $listData2 = ArrayHelper::map($data2, 'usua_id', 'usua_nombre');
    
                                                            
    $data = Yii::$app->db->createCommand("SELECT u.idusuarioevalua, u.clientearea FROM tbl_usuarios_evalua u WHERE u.clientearea IS NOT NULL AND u.idusuarioevalua NOT IN(2202) group BY u.clientearea order BY u.clientearea")->queryAll();
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
      ->orderBY ('nombre')
      ->All();

    $listData4 = ArrayHelper::map($datanew2, 'id_areaapoyo', 'nombre');
    
     foreach ($varListaplangptw as $key => $value) {
            $varId = $value['id_gptw'];
            $varIdopera = $value['id_operacion'];
            $varIdarea = $value['id_area_apoyo'];
            $varIdpilares = $value['id_pilares'];
            $varporcentaje_actual = $value['porcentaje_actual'];
            $varporcentaje_meta = $value['porcentaje_meta'];
            $varacciones = $value['acciones'];
            $varfecha_registro = $value['fecha_registro'];
            $varfecha_avance = $value['fecha_avance'];
            $varobservaciones = $value['observaciones'];
            $varresponsable_area = $value['responsable_area'];
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
        background-image: url('../../images/GPTW_Banner 3.jpg');
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
        <div class="col-md-6">
          <div class="card1 mb" style="background: #285185; ">
             <label style="font-size: 20px; color: #ffffff;"> <?= Yii::t('app', 'Hola lider K,') ?></label>
             <label style="font-size: 15px; color: #ffffff;"> <?= Yii::t('app', 'Hoy queremos seguir escribiendo una historia increible junto a ti, llevando a Konecta a otro nivel como un gran lugar para crecer, disfrutar, soñar, reconocer y conectar. Sabemos que el plan de acción que diseñaste con tu equipo maximizara nuestro potencial. Tu aporte en el ambiente laboral, es el diferenciador clave para fortalecer el orgullo organizacional y este es nuestro principal reto Great Place To Work') ?></label>
          </div>
        </div> 
        <div class="col-md-6">          
          <div class="card1 mb" >
             <label  style="font-size: 18px; color: #db2c23;"><em class="fas fa-exclamation-triangle" style="font-size: 20px; color: #db2c23;"></em> <?= Yii::t('app', 'Para tener en cuenta: ') ?></label>
             <label style="font-size: 13px;"> <?= Yii::t('app', 'Te invitamos a crear un registro por cada pilar Great Place to Work a trabajar: Credibilidad - Orgullo - Compañerismo - Ecuanimidad - Respeto') ?></label>
          </div>
          <br>
          <div class="card1 mb" style="background: #5c54a1;">
             <label style="font-size: 20px; color: #ffffff;"><em class="fas fa-hand-point-right" style="font-size: 28px; color: #f5770a;"></em> <?= Yii::t('app', '&nbsp Este lugar de trabajo lo hacemos tu y yo.   ¡Manos a la obra!') ?></label>           
          </div>
          
        </div>
          
    </div>
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
                <label><em class="far fa-address-card" style="font-size: 25px; color: #827DF9;"></em> Editar Información</label>
                <br>
                <div class="row">
                    <div class="col-md-6">
                      <label for="txtarea" style="font-size: 14px;">Área / Operación</label>
                      <br>
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
                     
                          <?=  $form->field($model6, 'idusuarioevalua', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\usuariosEvalua::find()->groupby('clientearea')->orderBy(['clientearea'=> SORT_ASC])->all(), 'idusuarioevalua', 'clientearea'),
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
                        <label for="txtmodelogptw" style="font-size: 14px;" >FOCO DE MEJORA EN MODELO GPTW  </label>

                    <?= $form->field($model5, 'id_pilares', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\pilaresgptw::find()->orderBy(['nombre_pilar'=> SORT_ASC])->all(), 'id_pilares', 'nombre_pilar'), 
                    ['prompt' => 'Seleccione...', 'name' => 'evaluador[]', 'id'=>'foco', 'multiple' => true, 'minimumInputLength' => 3, 'title' => 'Foco de mejora', 'prompt'=>'Seleccionar...',
                        'onchange' => '
                            $.get(
                                "' . Url::toRoute('listardetallepilar') . '", 
                                {id: $(this).val()}, 
                                function(res){
                                
                                    $("#requester").html(res);
                                }
                            );

                            
                        ',])?>
                        
                    </div>  
                    <div class="col-md-6">
                      <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Items Pilares: </label>
                        <?= $form->field($model7,'id_detalle_pilar', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\DetallesPilaresGptw::find()->orderBy(['nombre'=> SORT_ASC])->all(), 'id_detalle_pilar', 'nombre'),
                                                   
                                                    [
                                                        'prompt' => 'Seleccionar...',
                                                        'id' => 'requester',
                                                        'multiple' => true,
                                                    ]
                                                )->label('');
                        ?>
                    </div> 
                        
                                                  
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                      <label for="txtCedula" style="font-size: 14px;">PUNTAJE ACTUAL %</label>
                      <input type="number" min="1" max="100" maxlength="3" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" class="form-control" id="txtPuntajeactual" data-toggle="tooltip" title="Puntaje Actual"  value="<?php echo $varporcentaje_actual;?>">   
                      
                    </div>
                    <div class="col-md-6">
                      <label for="txtNombre" style="font-size: 14px;">PUNTAJE META %</label>
                      <input type="number" min="1" max="100"  type="number" maxlength="3" oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" class="form-control" id="txtPuntajemeta" data-toggle="tooltip" title="Puntaje Meta"  value="<?php echo $varporcentaje_meta;?>">   
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                      <label for="txtCaso" style="font-size: 14px;">ACCION CIERRE BRECHA </label>
                      <textarea type="text" class="form-control" style = 'resize: vertical;' id="txtAccion" data-toggle="tooltip" title="Descripción caso" ><?php echo ($varacciones); ?></textarea>   
                    </div>                               
                </div>
                <br>
                <div class="row" >
                    <div class="col-md-6">
                      <label for="txtFechar" style="font-size: 14px;">FECHA DE REGISTRO </label>
                      <input type="date" id="txtFechareg" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha de registro"  value="<?php echo date($varfecha_registro);?>">
                    </div>
                    <div class="col-md-6">
                      <label for="txtResponsable" style="font-size: 14px;">RESPONSABLE DE AREA </label>                      
                      <?=  $form->field($model3, 'usua_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Usuarios::find()->orderBy(['usua_nombre'=> SORT_ASC])->all(), 'usua_id', 'usua_nombre'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                        ]
                                )->label(''); 
                      ?>
                    </div>
                </div>
                <br>
                <div class="row"  style="display:none">
                    <div class="col-md-6">
                    <label for="txtCaso" style="font-size: 14px;">OBSERVACIONES DE SEGUIMIENTO</label>
                      <textarea type="text" class="form-control" style = 'resize: vertical;' id="txtobservacion" data-toggle="tooltip" title="Descripción caso"  ><?php echo $varobservaciones;?></textarea>   
                    </div>
                    <div class="col-md-6">
                      <label for="txtFechaavan" style="font-size: 14px;">FECHA DE AVANCE</label>
                      <input type="date" id="txtFechaavan" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha de avance"  value="<?php echo date($varfecha_avance);?>">
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
                            <div class="col-md-4">
                                <div class="card1 mb">
                                  <div onclick="varGuardar();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                                    Guardar información
                                  </div>                                                                    
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card1 mb">
                                    <?= Html::a('Regresar',  ['reporteplanacciongptw'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
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
<?php if($sessiones == "2953" || $sessiones == "2911" || $sessiones == "57" || $sessiones == "3205" || $sessiones == "2915" || $sessiones == "1290" || $sessiones == "6080" || $sessiones == "8103"){ ?>
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
    var varPartT = document.getElementById("operacion");
    var varPartT2 = document.getElementById("area");
      varPartT.style.display = 'inline';    
      varPartT2.style.display = 'none';
  };
  function planaccion(){
    var varPartT = document.getElementById("operacion");
    var varPartT2 = document.getElementById("area");
   
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
    var varOpera = document.getElementById("txtopera").value;
      var varArea = document.getElementById("txtarea").value;
      var varIdgptw = "<?php echo $varId; ?>";
      var varArea = document.getElementById("txtarea").value;
      
      var varOpera = document.getElementById("txtopera").value;
      
      var varFocomejora = document.getElementById("foco");
      const listafoco = [];
      for(var i = 0; i < varFocomejora.options.length; i++){
        if(varFocomejora.options[i].selected){
        var datos = varFocomejora.options[i].value;      
        listafoco.push(datos);
        }       
      }
     
      var varPuntajeactual = document.getElementById("txtPuntajeactual").value;
      var varPuntajemeta = document.getElementById("txtPuntajemeta").value;
    
      var varAccion = document.getElementById("txtAccion").value;
      var varFechareg = document.getElementById("txtFechareg").value;
     
      //var varFechaavan = document.getElementById("txtFechaavan").value;
      //var varObservacion = document.getElementById("txtobservacion").value;
      var varResponsable = document.getElementById("usuarios-usua_id").value;
      //alert(varResponsable);
      var variddetalle = document.getElementById("requester");
      const listadetalle = [];
      for(var i = 0; i < variddetalle.options.length; i++){
        if(variddetalle.options[i].selected){
        var datos = variddetalle.options[i].value;      
        listadetalle.push(datos);
        }       
      }
      
      if(varFocomejora=='Seleccione...'){
        varFocomejora="";
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
      
      if (varFocomejora == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Foco de Mejora.","warning");                    
          document.getElementById("foco").style.border = '1px solid #ff2e2e';
          return;
      } else if(varPuntajeactual == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Puntaje Actual","warning");
          document.getElementById("txtPuntajeactual").style.border = '1px solid #ff2e2e';
          return;
      } else if(varPuntajemeta == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Puntaje Meta","warning");
          document.getElementById("txtPuntajemeta").style.border = '1px solid #ff2e2e';
          return;
      } else if(varAccion == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Acción Cierre Brecha","warning");
          document.getElementById("txtAccion").style.border = '1px solid #ff2e2e';
          return;      
      } else if(varFechareg == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Fecha de registro","warning");
          document.getElementById("txtFechareg").style.border = '1px solid #ff2e2e';
          return;
      
      } else if(varResponsable == ""){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en Responsable de Área","warning");
          document.getElementById("txtResponsable").style.border = '1px solid #ff2e2e';
          return;
      }
      
      $.ajax({
                  method: "get",
                  url: "updateplanaccion",
                  data : {
                    txtvidgptw : varIdgptw,  
                    txtvarea : varArea,
                    txtvopera : varOpera,
                    txtvfocomejora : listafoco,
                    txtvpuntajeactual : varPuntajeactual,
                    txtvpuntajemeta : varPuntajemeta,
                    txtvaccion : varAccion,
                    txtvfechareg : varFechareg,
                    txtvresponsable : varResponsable,                    
                    txtvlistadetalle : listadetalle, 
                    
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
                                      window.location.href = 'reporteplanacciongptw';
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
    if ($("#foco option:checked").length > 1) {
      $("#foco").val(ultimoValorValido);
    } else {
      ultimoValorValido = $("#foco").val();
    }
});
    
</script>
