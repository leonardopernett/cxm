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

	$this->title = 'Gestión del Caso QyR';
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
    
    foreach ($dataprovider as $key => $value) {
        $txtfecha_creacion = $value['fecha_creacion'];
        $txnumero_caso = $value['numero_caso'];
        $txtnombre = $value['nombre'];
        $txtdocumento = $value['documento'];
        $txtcorreo = $value['correo'];
        $txtcomentario = $value['comentario']; 
        $txtarea = $value['area'];
        $txttipologia = $value['tipologia'];
        $txtusua_nombre = $value['usua_nombre'];
        $txttipo_respuesta= $value['tipo_respuesta'];
        $txttipo_de_dato = $value['tipo_de_dato'];
        $txtestado = $value['estado'];
           
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
                <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Revisión de Casos de Quejas y Reclamos "; ?> </label>
            </div>
        </div>
    </div>   
    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="far fa-address-card" style="font-size: 28px; color: #2CA5FF;"></em> Información Caso: </label>
                <br>
                <table id="tblDataInfo" class="table table-striped table-bordered tblResDetFreed">
                    <tbody>
                    <tr>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Creación:') ?></label></th>
                        <td><label style="font-size: 12px;" width: 300px;><?php echo  $txtfecha_creacion; ?></label></td>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Número de Caso:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;"><?php echo  $txnumero_caso; ?></label></td>
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nombre Solicitante:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;"><?php echo  $txtnombre; ?></label></td>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Documento:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;" ><?php echo  $txtdocumento; ?></label></td>
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Correo:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;"><?php echo  $txtcorreo; ?></label></td>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Comentarios:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;"><?php echo  $txtcomentario; ?></label></td>
                    </tr>
                        
                    </tbody>
                </table>
                <br>
                <label><em class="fas fa-plus-square" style="font-size: 28px; color: #2CA5FF;"></em> Datos adicionales: </label>
                <br>
                <?php
                $varClasificacion = null;
                    foreach ($dataProviderInfo as $key => $value) {   
                        $varIdAccion = $value['hv_idpersonal'];
                        $varIdClientes = $value['IdCliente'];
                        $varsociedad = $value['sociedad'];

                        $paramsaccion = [':idhvacciones' => $varIdAccion];

                        $paramsclasifica = [':idclasifica' => $value['clasificacion']];

                        $varClasificacion = Yii::$app->db->createCommand('
                            SELECT dc.ciudadclasificacion FROM tbl_hojavida_dataclasificacion dc
                            INNER JOIN tbl_hojavida_datapersonal dp ON 
                            dc.hv_idclasificacion = dp.clasificacion
                            WHERE 
                            dp.hv_idpersonal = :idclasifica ')->bindValues($paramsclasifica)->queryScalar();

                        

                $paramscliente = [':idhvaccion' => $varIdAccion, ':idclientes' => $varIdClientes ];
                $varCliente = Yii::$app->db->createCommand('
                SELECT pc.cliente  FROM tbl_proceso_cliente_centrocosto pc
                    INNER JOIN tbl_hojavida_datapcrc hd ON 
                    pc.id_dp_clientes = hd.id_dp_cliente
                    INNER JOIN tbl_hojavida_datapersonal dp ON 
                    hd.hv_idpersonal = dp.hv_idpersonal
                    WHERE 
                    dp.hv_idpersonal = :idhvaccion AND hd.id_dp_cliente = :idclientes
                    GROUP BY pc.id_dp_clientes')->bindValues($paramscliente)->queryScalar();

                
                $varListaDirectores = Yii::$app->db->createCommand('
                SELECT  cc.director_programa  AS Programa
                    FROM tbl_hojavida_datadirector dc 
                    INNER JOIN tbl_proceso_cliente_centrocosto cc ON 
                        dc.ccdirector = cc.documento_director
                    WHERE dc.hv_idpersonal = :idhvacciones GROUP BY cc.director_programa')->bindValues($paramsaccion)->queryAll();
          
                $varArrayDirectores = array();
                foreach ($varListaDirectores as $key => $value) {
                array_push($varArrayDirectores, $value['Programa']);
                }
                $varDirectoresListado = implode("; ", $varArrayDirectores);


                $varListaGerente = Yii::$app->db->createCommand('
                SELECT  cc.gerente_cuenta  AS Programagerente
                    FROM tbl_hojavida_datagerente dc 
                    INNER JOIN tbl_proceso_cliente_centrocosto cc ON 
                        cc.documento_gerente = dc.ccgerente
                    WHERE dc.hv_idpersonal = :idhvacciones GROUP BY cc.gerente_cuenta')->bindValues($paramsaccion)->queryAll();


                
                $varArrayGerentes = array();
                foreach ($varListaGerente as $key => $value) {
                array_push($varArrayGerentes, $value['Programagerente']);
                }
                $varGerentesListado = implode("; ", $varArrayGerentes);

                $varVerificaPcrc = Yii::$app->db->createCommand('
                SELECT GROUP_CONCAT(cc.cod_pcrc," - ",cc.pcrc SEPARATOR "; ") AS Programa
                    FROM tbl_hojavida_datapcrc dc 
                    INNER JOIN tbl_proceso_cliente_centrocosto cc ON 
                        dc.cod_pcrc = cc.cod_pcrc
                    WHERE dc.hv_idpersonal = :idhvacciones
                    limit 1')->bindValues($paramsaccion)->queryScalar();

                $listacumplimiento = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_qr_cumplimiento'])
                                    ->where(['=','anulado',0])
                                    ->All();
                    $meta = null;
                    $diaverde1 = null;
                    $diaverde2 = null;
                    $diaamarillo1 = null;
                    $diaamarillo2 = null;
                    $diarojo1 = null;
                    $diarojo2 = null; 

                    foreach ($listacumplimiento as $key => $value) {
                        $meta = $value['indicador'];
                        $diaverde1 = $value['diaverde1'];
                        $diaverde2 = $value['diaverde2'];
                        $diaamarillo1 = $value['diaamarillo1'];
                        $diaamarillo2 = $value['diaamarillo2'];
                        $diarojo1 = $value['diarojo1'];
                        $diarojo2 = $value['diarojo2'];                        
                    }
                   
                    $fecha1= new Datetime($txtfecha_creacion);
                    $fecha2= new datetime('now');
                    $dias = $fecha1->diff($fecha2);
                    
                    $diastrans = $dias->days;
                    $diasfaltan = $meta - $diastrans;
                    if ($diastrans < 1) {
                        $diastrans = 0;
                    }
                    //$meta = 10;
                    $cumplimiento = 100 -(($diastrans / $meta) * 100);
           
                ?>
                <table id="tblDataInfo" class="table table-striped table-bordered tblResDetFreed">
                    <tbody>
                    <tr>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Servicio:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;" ><?php echo  $varCliente; ?></label></td>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Programa Pcrc:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;"><?php echo  $varVerificaPcrc; ?></label></td>                   
                    </tr>
                    <tr>                    
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Director:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;" ><?php echo  $varDirectoresListado; ?> </label></td>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Gerente:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;"><?php echo  $varGerentesListado; ?> </label></td>
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Sociedad:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;" ><?php echo  $varsociedad; ?></label></td>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Ciudad:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;"><?php echo  $varClasificacion; ?></label></td>
                    </tr> 
                    <tr>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Días de Gestión:') ?></label></th>
                        <?php if($diastrans == 0){?>
                            <td ><label style="font-size: 12px; width: 250px;"><?php echo  $diastrans; ?><em class="fas fa-smile" style="font-size: 28px; color: #35f02b; float: right;"></em></label>&nbsp;Faltan <?php echo  $diasfaltan; ?> días</td>
                        <?php } ?>
                        <?php if(($diastrans >= $diaverde1) && ($diastrans <= $diaverde2)){?>
                            <td ><label style="font-size: 12px; width: 250px;"><?php echo  $diastrans; ?><em class="fas fa-smile" style="font-size: 28px; color: #35f02b; float: right;"></em></label>&nbsp;Faltan <?php echo  $diasfaltan; ?> días</td>
                        <?php } ?>
                        <?php if(($diastrans >= $diaamarillo1) && ($diastrans <= $diaamarillo2)){?>
                            <td ><label style="font-size: 12px; width: 250px;"><?php echo  $diastrans; ?><em class="fas fa-meh" style="font-size: 28px; color: #fae716; float: right;"></em></label>&nbsp;Faltan <?php echo  $diasfaltan; ?> días</td>
                        <?php } ?>
                        <?php if(($diastrans >= $diarojo1)){?>
                            <td ><label style="font-size: 12px; width: 250px;"><?php echo  $diastrans; ?><em class="fas fa-frown" style="font-size: 28px; color: #f7331e; float: right;"></em></label>&nbsp;Faltan <?php echo  $diasfaltan; ?> días</td>
                        <?php } ?>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cumplimiento %:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;"><?php echo  $cumplimiento; ?></label></td>
                    </tr>    
                    <?php
                        }
                        ?>
                    </tbody>
                </table>                
                <br>
                <label><em class="fa fa-address-card" style="font-size: 28px; color: #26bf30;"></em> Tipificaciones: </label>
                <br>
                <table id="tblTipificación" class="table table-striped table-bordered tblResDetFreed">
                    <tbody>
                    <tr>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Área de Asignación:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;" ><?php echo  $txtarea; ?></label></td>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipología:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;"><?php echo  $txttipologia; ?></label></td>                   
                    </tr>
                    <tr>                    
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Responsable:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;" ><?php echo  $txtusua_nombre; ?> </label></td>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo de Respuesta:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;"><?php echo  $txttipo_respuesta; ?> </label></td>
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo PQRS:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;" ><?php echo  $txttipo_de_dato; ?></label></td>
                        <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 13px;"><?= Yii::t('app', 'Estado:') ?></label></th>
                        <td><label style="font-size: 12px; width: 300px;"><?php echo  $txtestado; ?></label></td>
                    </tr>                                        
                    </tbody>
                </table>                
                <br>
                
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
                    <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Gestión Caso "; ?> </label>
                </div>
            </div>
        </div>             
        <br>
        <div class="row">
            <div class="col-md-12">        
                <div class="card1 mb">
                    <label><em class="far fa-clipboard" style="font-size: 20px; color: #e8701a;"></em> Gestión del Caso</label>                
                    <br> 
                    <div class="row" >

                        <div class="col-md-6">
                            <label for="txtcliente" style="font-size: 14px;">Área de Asignación</label>
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
                            <label for="txtgerente" style="font-size: 14px;">Tipología</label>
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
                            <label for="txtResponsable" style="font-size: 14px;">Responsable Asignación</label>                      
                            <?=  $form->field($model3, 'id_solicitud', ['labelOptions' => [], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\UsuariosEvalua::find()->orderBy(['nombre_completo'=> SORT_ASC])->all(), 'idusuarioevalua', 'nombre_completo'),
                                                [
                                                    'prompt'=>'Seleccionar...',
                                                ]
                                        )->label(''); 
                            ?>
                        </div>
                        <div class="col-md-6">                        
                            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Comentarios') ?></label>
                            <?= $form->field($model3, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['maxlength' => 500, 'id'=>'idperfil', 'placeholder'=>'Ingresar Comentario', 'style' => 'resize: vertical;'])?>
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
            <div class="col-md-6">
                <div class="card1 mb" style="background: #6b97b1; ">
                    <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Dar Respuesta "; ?> </label>
                </div>
            </div>
        </div>             
        <br>
        <div class="row">
            <div class="col-md-12">        
                <div class="card1 mb">
                    <label><em class="fas fa-paper-plane" style="font-size: 20px; color: #3a3af0;"></em> Dar Respuesta del Caso</label>                
                    <br>                    
                    
                    <div class="row" >
                        <div class="col-md-6">                                        
                            <a href="../../images/uploads/Carta respuesta Q&R.docx" download style="font-size: 16px;"><b> Descargar documento </b>&nbsp;&nbsp;&nbsp; <em class="fas fa-download" style="font-size: 25px; color: #26cd33;"></em></a>
                        </div>

                         <div class="col-md-6"> 
                        <label style="font-size: 16px;"><em class="fas fa-hand-pointer" style="font-size: 18px; color: #3d7d58;"></em><?= Yii::t('app', ' Anexar Documento') ?></label>                        
                                <?= $form->field($model, 'file')->fileInput(["class"=>"input-file" ,'id'=>'idfile', 'style'=>'font-size: 16px;'])->label('') ?>                            
                        </div>
                        
                    </div>
                    <br>
                    
                </div>
            </div>    
        </div>
</div>
<hr>
<div class="capaCuatro">    
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
                                    <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Regresar']) 
                                    ?>
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="card1 mb">
                                    <?= Html::submitButton("Guardar - Enviar", ["class" => "btn btn-primary", 
                                                            'onclick' => 'verificar();',]) ?>
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
function verificar(){
    var varenexo = document.getElementById("idfile").text;

    if (varenexo == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Se debe anexar documento diligenciado en formato pdf.","warning");
      return;
    }
  };
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