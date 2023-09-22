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

	$this->title = 'Gestor de PQRSF - Tipificación de los Casos';
	$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';
    $sessiones = Yii::$app->user->identity->id;
    $txtfecha_creacion = null;
    $txnumero_caso = null;
    $txtnombre = null;
    $txtdocumento = null;
    $txtcorreo = null;
    $txtcomentario = null; 
    $txtarea = null;
    $txttipologia = null;
    $txtarchivo = null;
    

    foreach ($dataprovider as $key => $value) {
        $txtarchivo = $value['archivo'];
        $ruta = "../../".$txtarchivo;
        $txtfecha_creacion = $value['fecha_creacion'];
        $txnumero_caso = $value['numero_caso'];
        $txtnombre = $value['nombre'];
        $txtdocumento = $value['documento'];
        $txtcorreo = $value['correo'];
        $txtcomentario = $value['comentario']; 
        $txtarea = $value['area'];
        $txttipologia = $value['tipologia'];   
    }
    $varNA = "Sin datos";
    $listadata = (new \yii\db\Query())
                  ->select(['tbl_qr_casos.id as idcaso','tbl_qr_casos.numero_caso','tbl_qr_tipos_de_solicitud.tipo_de_dato','tbl_qr_casos.comentario','tbl_qr_casos.cliente','tbl_qr_casos.nombre','tbl_qr_casos.documento','tbl_qr_casos.correo','tbl_qr_estados_casos.estado','tbl_qr_estados_casos.id as idestado','tbl_qr_casos.fecha_creacion', 'tbl_qr_areas.nombre area','tbl_qr_tipologias.tipologia',])
                  ->from(['tbl_qr_casos'])
                  ->join('LEFT OUTER JOIN', 'tbl_qr_tipos_de_solicitud',
                                  'tbl_qr_casos.id_solicitud = tbl_qr_tipos_de_solicitud.id') 
                  ->join('LEFT OUTER JOIN', 'tbl_qr_estados_casos',
                                  'tbl_qr_casos.id_estado_caso = tbl_qr_estados_casos.id')
                  ->join('LEFT JOIN', 'tbl_qr_areas', 'tbl_qr_casos.id_area = tbl_qr_areas.id' )
                  ->join('LEFT JOIN', 'tbl_qr_tipologias', 'tbl_qr_casos.id_tipologia = tbl_qr_tipologias.id')     
                  ->All();

    $datanew2 = (new \yii\db\Query())
      ->select(['id_areaapoyo', 'nombre'])
      ->from(['tbl_areasapoyo_gptw'])
      ->where(['=','anulado',0])
      ->orderBY ('nombre')
      ->All();

    $listData = ArrayHelper::map($datanew2, 'id_areaapoyo', 'nombre');
    $dataD = (new \yii\db\Query())
    ->select(['tbl_hojavida_datadirector.hv_iddirector', 'tbl_proceso_cliente_centrocosto.director_programa'])
    ->from(['tbl_hojavida_datadirector'])
    ->join('INNER JOIN', 'tbl_proceso_cliente_centrocosto',
    'tbl_hojavida_datadirector.ccdirector = tbl_proceso_cliente_centrocosto.documento_director')
    ->groupBy ('tbl_proceso_cliente_centrocosto.director_programa')
    ->orderBY ('tbl_proceso_cliente_centrocosto.director_programa')
    ->All();

    $varListatotalDirectores = ArrayHelper::map($dataD, 'hv_iddirector', 'director_programa');

  

    $dataG = (new \yii\db\Query())
    ->select(['tbl_hojavida_datagerente.hv_idgerente', 'tbl_proceso_cliente_centrocosto.gerente_cuenta'])
    ->from(['tbl_hojavida_datagerente'])
    ->join('INNER JOIN', 'tbl_proceso_cliente_centrocosto',
    'tbl_hojavida_datagerente.ccgerente = tbl_proceso_cliente_centrocosto.documento_gerente')
    ->groupBy ('tbl_proceso_cliente_centrocosto.gerente_cuenta')
    ->orderBY ('tbl_proceso_cliente_centrocosto.gerente_cuenta')
    ->All();

    $varListatotalGerentes = ArrayHelper::map($dataG, 'hv_idgerente', 'gerente_cuenta');


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
        background-image: url('../../images/qyr.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<link rel="stylesheet" href="../../../css/font-awesome/css/font-awesome.css"  >
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br> 
<div class="capaUno">

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Reporte de Casos de PQRSF"; ?> </label>
            </div>
        </div>
    </div>   
    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="far fa-address-card" style="font-size: 30px; color: #2CA5FF;"></em> Información Caso: </label>
                <br>
                <table id="tblDataInfo" class="table table-striped table-bordered tblResDetFreed">
                    <tbody>
                    <tr>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Fecha Radicación:') ?></label></th>
                        <td><label style="font-size: 15px;" width: 300px;><?php echo  $txtfecha_creacion; ?></label></td>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Número de Caso:') ?></label></th>
                        <td><label style="font-size: 15px; width: 300px;"><?php echo  $txnumero_caso; ?></label></td>
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Radicado por:') ?></label></th>
                        <td><label style="font-size: 15px; width: 300px;"><?php echo  $txtnombre; ?></label></td>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Documento:') ?></label></th>
                        <td><label style="font-size: 15px; width: 300px;" ><?php echo  $txtdocumento; ?></label></td>
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Correo:') ?></label></th>
                        <td><label style="font-size: 15px; width: 300px;"><?php echo  $txtcorreo; ?></label></td>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Comentarios:') ?></label></th>
                        <td><label style="font-size: 15px; width: 300px;"><?php echo  $txtcomentario; ?></label></td>
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Archivo Adjunto:') ?></label></th>
                        <td colspan="4"><a href="<?php echo $ruta?>" ><strong style="font-size: 15px;" > Descargar Documento Caso </strong>&nbsp;&nbsp;&nbsp; <em class="fas fa-download" style="font-size: 25px; color: #2CA5FF;"></em></a></td>
                    </tr>
                        
                    </tbody>
                </table>
            </div>
        </div>
                
    </div>
</div>
<hr>
<!-- Capa carga de información -->
    
<div class="capaDos">
<?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            "method" => "post",
            "enableClientValidation" => true,
            'options' => ['enctype' => 'multipart/form-data'],
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
            ]
        ]) 
    ?>
        <div class="row">    
            <div class="col-md-6">
                <div class="card1 mb" style="background: #6b97b1; ">
                    <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Tipificaciones "; ?> </label>
                </div>
            </div>
        </div>             
        <br>
        <div class="row">
            <div class="col-md-12">        
                <div class="card1 mb">
                    <label><em class="far fa-clipboard" style="font-size: 30px; color: #2CA5FF;"></em> Tipificación</label>                
                    <br> 
                    <div class="row" >

                        <div class="col-md-6">
                            <label for="txtcliente" style="font-size: 16px;">Área de Asignación</label>
                            <?=  $form->field($model2, 'id_area', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Areasqyr::find()->distinct()->where("anulado = 0")->orderBy(['nombre'=> SORT_ASC])->all(), 'id', 'nombre'),
                                                            [
                                                                'prompt'=>'Seleccionar...',
                                                                'onchange' => '
                                                                    $.post(
                                                                        "' . Url::toRoute('listartipologia') . '", 
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
                            <label for="txtgerente" style="font-size: 16px;">Tipología</label>
                            <?= $form->field($model8,'id', ['labelOptions' => [], 'template' => $template])->dropDownList(
                                                            [],
                                                            [
                                                                'prompt' => 'Seleccionar...',                                         
                                                                'id' => 'requester'
                                                            ]
                                                        );
                            ?>
                        </div>
                    </div>
                    <div class="row" >
                        <div class="col-md-6">                  
                            <label for="txtResponsable" style="font-size: 16px;">Responsable </label>                      
                            <?=  $form->field($model3, 'id', ['labelOptions' => [], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Usuarios::find()->where(['not like', 'usua_nombre', '%no usar%', false])->orderBy(['usua_nombre'=> SORT_ASC])->all(), 'usua_id', 'usua_nombre'),
                                                [
                                                    'prompt'=>'Seleccionar...',
                                                ]
                                        )->label(''); 
                            ?>
                        </div>
                        <div class="col-md-6">
                            <label style="font-size: 16px;"> Tipo de respuesta </label>
                            <select id="txttiporespuesta" class ='form-control' onchange="respuesta();">
                                <option value="" disabled selected>Seleccione...</option>
                                <option value="Interna">Interna</option>
                                <option value="Externa">Externa</option>
                            </select>
                        </div>
                    </div>
                    <div class="row" >
                       
                        <div class="col-md-6">                  
                            <label for="txtResponsable" style="font-size: 16px;">Tipo de PQRS </label>                      
                            <?=  $form->field($model4, 'id', ['labelOptions' => [], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Tipopqrs::find()->orderBy(['tipo_de_dato'=> SORT_ASC])->all(), 'id', 'tipo_de_dato'),
                                                [
                                                    'prompt'=>'Seleccionar...',
                                                ]
                                        )->label(''); 
                            ?>
                        </div>
                        <div class="col-md-6">                  
                            <label for="txtResponsable" style="font-size: 16px;">Estado </label>                      
                            <?=  $form->field($model5, 'id_estado', ['labelOptions' => [], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Estadosqyr::find()->orderBy(['nombre'=> SORT_ASC])->all(), 'id_estado', 'nombre'),
                                                [
                                                    'prompt'=>'Seleccionar...',
                                                ]
                                        )->label(''); 
                            ?>
                        </div>
                    </div>
                    <div class="row" >
                        <div class="col-md-6" style="display:none"> 
                                <?= $form->field($model6, 'ccdirector', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idrespuesta'])?>
                                                    
                        </div> 
                    </div> 
                    
                    <br>
                    
                </div>
            </div>    
        </div>
</div>
<hr>
<div class="capaTres">    
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
              <label><em class="fas fa-cogs" style="font-size: 30px; color: #1e8da7;"></em> Acciones:</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card1 mb">
                                    <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Regresar']) 
                                    ?>
                                </div>
                            </div> 
                            <div class="col-md-6">
                                <div class="card1 mb">
                                    <?= Html::submitButton("Guardar", ["class" => "btn btn-primary", ]) ?>
                                </div>
                            </div>                                                      
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><br>    
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
function respuesta(){
    var varRta = document.getElementById("txttiporespuesta").value;
    document.getElementById("idrespuesta").value = varRta;
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
      varPartT.style.display = 'none';
      varPartT2.style.display = 'none';
    }else{
      varPartT.style.display = 'inline';
    }    
  };
    
  function carguedatod(){
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
</script>