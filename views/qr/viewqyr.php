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
    $txtfecha_revisioncx = null;
    $txtfecha_revision_gerente = null;
    $txtfecha_respuesta = null;
    $txtfecha_asignacion = null;
    $txtrevision_gerente = null;
    $txtrevisioncx = null;
    $txtarchivo = null;
    $id = null;

    foreach ($dataprovider as $key => $value) {
        $txtrevision_gerente = $value['revision_gerente'];
        $txtrevisioncx = $value['revision_cx'];
        $txtfecha_respuesta = $value['fecha_respuesta'];
        $txtfecha_asignacion = $value['fecha_asignacion'];
        $txtfecha_revisioncx = $value['fecha_revisioncx'];
        $txtfecha_revision_gerente = $value['fecha_revision_gerente'];
        $txtfecha_creacion = $value['fecha_creacion'];
        $txnumero_caso = $value['numero_caso'];
        $txtnombre = $value['nombre'];
        $txtdocumento = $value['documento'];
        $txtcorreo = $value['correo'];
        $txtcomentario = $value['comentario']; 
        $txtarea = $value['area'];
        $txttipologia = $value['tipologia']; 
        $txtarchivo = $value['archivo'];
        $rutas = $txtarchivo;  
        $id = $value['id'];
    }

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
        $txtcomentario2 = $value['comentario2'];
        $txtarchivo2 = $value['archivo2'];
        $ruta = $txtarchivo2;
        $txtarchivo = $value['archivo'];
        $rutas = $txtarchivo;
           
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

    <div role="tabpanel" class="tab-pane active" id="home">
            <br>
            <div class="row">
              <div class="col-md-12">
                <div class="card1 mb">

                  <div class="col-md-12">
                    <div class="card1 mb">
                      <label style="font-size: 20px;"><em class="far fa-address-card" style="font-size: 25px; color: #00968F;"></em> Información del Caso: </label>
                      <div class="col-md-12 right">
                        <div onclick="opennovedadinf();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbnp11" ><?= Yii::t('app', '[ Abrir + ]') ?>                                
                        </div> 
                        <div onclick="closenovedadinf();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="idtbnp22" ><?= Yii::t('app', '[ Cerrar - ]') ?>                                
                        </div> 
                      </div>
                      <div class="capaExt" id="capa00tt" style="display: none;">

                        <table id="tblDataInfo" class="table table-striped table-bordered tblResDetFreed">
                          <caption></caption>
                          <tbody>
                            <tr>
                                <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Fecha Creación:') ?></label></th>
                                <td><label style="font-size: 15px; width: 300px;"><?php echo  $txtfecha_creacion; ?></label></td>
                                <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Número de Caso:') ?></label></th>
                                <td><label style="font-size: 15px; width: 300px;"><?php echo  $txnumero_caso; ?></label></td>
                            </tr>
                            <tr>
                                <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Nombre Solicitante:') ?></label></th>
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
                            <?php if (isset($rutas)) { ?>
                            <tr>
                              <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Archivo Adjunto:') ?></label></th>
                              <td colspan="4"><a href="<?php echo "../../".$rutas?>" style="font-size: 18px;"><strong style="font-size: 15px;"> Descargar Documento Caso </strong>&nbsp;&nbsp;&nbsp; <em class="fas fa-download" style="font-size: 25px; color: #2CA5FF;"></em></a></td> 
                            </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <hr>

                    <div class="card1 mb">
                      <label style="font-size: 20px;"><em class="fas fa-plus-square" style="font-size: 25px; color: #00968F;"></em> Datos Adicionales: </label>
                      <div class="col-md-12 right">
                        <div onclick="opennovedaddatos();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbnt12" ><?= Yii::t('app', '[ Abrir + ]') ?>                                
                        </div> 
                        <div onclick="closenovedaddatos();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="idtbnt22" ><?= Yii::t('app', '[ Cerrar - ]') ?>                                
                        </div> 
                      </div>
                      <div class="capaExt" id="capa00t" style="display: none;">

                        <?php
                          $varClasificacion = null;
                          foreach ($dataProviderInfo as $key => $value) {   
                            $varIdClientes = $value['cliente']; 
                            
                            $varClasificacion = (new \yii\db\Query())
                                                ->select(['tbl_proceso_cliente_centrocosto.ciudad'])
                                                ->from(['tbl_proceso_cliente_centrocosto'])
                                                ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$varIdClientes])
                                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
                                                ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                                                ->Scalar();

                            $varCliente = (new \yii\db\Query())
                                                ->select(['tbl_proceso_cliente_centrocosto.cliente'])
                                                ->from(['tbl_proceso_cliente_centrocosto'])
                                                ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$varIdClientes])
                                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
                                                ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                                                ->Scalar();

                            $VarDirectoresList = (new \yii\db\Query())
                                                ->select(['tbl_proceso_cliente_centrocosto.director_programa'])
                                                ->from(['tbl_proceso_cliente_centrocosto'])
                                                ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$varIdClientes])
                                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
                                                ->groupby(['tbl_proceso_cliente_centrocosto.director_programa'])
                                                ->All();

                            $varArrayDirectores = array();
                            foreach ($VarDirectoresList as $key => $value) {
                                array_push($varArrayDirectores, $value['director_programa']);
                            }
                            $varDirectoresListado = implode(" - ", $varArrayDirectores);

                            $varGerentesList = (new \yii\db\Query())
                                                ->select(['tbl_proceso_cliente_centrocosto.gerente_cuenta'])
                                                ->from(['tbl_proceso_cliente_centrocosto'])
                                                ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$varIdClientes])
                                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
                                                ->groupby(['tbl_proceso_cliente_centrocosto.gerente_cuenta'])
                                                ->All();

                            $varArrayGerentes = array();
                            foreach ($varGerentesList as $key => $value) {
                                array_push($varArrayGerentes, $value['gerente_cuenta']);
                            }
                            $varGerentesListado = implode(" - ", $varArrayGerentes);


                            $varPcrcList = (new \yii\db\Query())
                                                ->select(['CONCAT(tbl_proceso_cliente_centrocosto.cod_pcrc," - ",tbl_proceso_cliente_centrocosto.pcrc) AS varListPcrc'])
                                                ->from(['tbl_proceso_cliente_centrocosto'])
                                                ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$varIdClientes])
                                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
                                                ->groupby(['tbl_proceso_cliente_centrocosto.pcrc'])
                                                ->limit('5')
                                                ->All();

                            $varArrayPcrc = array();
                            foreach ($varPcrcList as $key => $value) {
                                array_push($varArrayPcrc, $value['varListPcrc']);
                            }
                            $varVerificaPcrc = implode("; ", $varArrayPcrc);

                            $varsociedad = (new \yii\db\Query())
                                            ->select([
                                            'tbl_hojavida_sociedad.sociedad'
                                            ])
                                            ->from(['tbl_hojavida_sociedad'])
                                            ->join('LEFT OUTER JOIN', 'tbl_hojavida_datapersonal',
                                                  'tbl_hojavida_sociedad.id_sociedad = tbl_hojavida_datapersonal.id_sociedad')
                                            ->join('LEFT OUTER JOIN', 'tbl_hojavida_datapcrc',
                                                  ' tbl_hojavida_datapersonal.hv_idpersonal = tbl_hojavida_datapcrc.hv_idpersonal')
                                            ->where(['=','tbl_hojavida_datapcrc.id_dp_cliente',$varIdClientes])
                                            ->groupby(['tbl_hojavida_sociedad.id_sociedad'])
                                            ->Scalar();  

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
                            
                            $cumplimiento = 100 -(($diastrans / $meta) * 100);
              
                        ?>
                        <table id="tblDataInfo" class="table table-striped table-bordered tblResDetFreed">
                          <caption></caption>
                          <tbody>
                          <tr>
                            <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Cliente:') ?></label></th>
                            <td><label style="font-size: 15px; width: 300px;"><?php echo  $varCliente; ?></label></td>
                            <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Programa Pcrc:') ?></label></th>
                            <td><label style="font-size: 15px; width: 300px;"><?php echo  $varVerificaPcrc; ?></label></td>                   
                          </tr>
                          <tr>                    
                              <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Director:') ?></label></th>
                              <td><label style="font-size: 15px; width: 300px;"><?php echo  $varDirectoresListado; ?> </label></td>
                              <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Gerente:') ?></label></th>
                              <td><label style="font-size: 15px; width: 300px;"><?php echo  $varGerentesListado; ?></label></td>
                          </tr>
                          <tr>
                              <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Sociedad:') ?></label></th>
                              <td><label style="font-size: 15px; width: 300px;"><?php echo  $varsociedad; ?></label></td>
                              <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Ciudad:') ?></label></th>
                              <td><label style="font-size: 15px; width: 300px;"><?php echo  $varClasificacion; ?></label></td>
                          </tr> 
                          <tr>
                              <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Días de Vencidos:') ?></label></th>
                              <td><label style="font-size: 15px; width: 300px;"><?php echo  $diastrans."  días"; ?></label></td>
                              <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Cumplimiento %:') ?></label></th>
                              <td><label style="font-size: 15px; width: 300px;"><?php echo  $cumplimiento; ?></label></td>
                          </tr> 
                            <?php
                            }
                            ?>
                          </tbody>
                        </table>
                      </div> 
                    </div>


                    <hr>
                    <div class="card1 mb">
                      <label style="font-size: 20px;"><em class="far fa-map" style="font-size: 25px; color: #00968F;"></em> Historial del Caso: </label>
                      <div class="col-md-12 right">
                        <div onclick="opennovedadp();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbnp13" ><?= Yii::t('app', '[ Abrir + ]') ?>                                
                        </div> 
                        <div onclick="closenovedadp();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="idtbnp23" ><?= Yii::t('app', '[ Cerrar - ]') ?>                                
                        </div> 
                      </div>

                      <div class="capaExt" id="capa00p" style="display: none;">

                        <table id="tblDataInfo" class="table table-striped table-bordered tblResDetFreed">
                          <caption></caption>
                          <tbody>
                            <tr>
                              <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Radicado por:') ?></label></th>
                              <td><label style="font-size: 15px; width: 300px;" ><?php echo  $txtnombre; ?></label></td>
                              <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Fecha de Radicación:') ?></label></th>
                              <td><label style="font-size: 15px; width: 300px;"><?php echo  $txtfecha_creacion; ?></label></td>                   
                            </tr>
                            <tr>
                                <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Asignado a:') ?></label></th>
                                <td><label style="font-size: 15px; width: 300px;"><?php echo  $txtusua_nombre; ?></label></td>
                                <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Fecha de Asignación:') ?></label></th>
                                <td><label style="font-size: 15px; width: 300px;"><?php echo  $txtfecha_asignacion; ?></label></td>
                            </tr>
                            <tr>
                                <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Respuesta del Caso:') ?></label></th>
                                <?php if (isset($ruta)) { ?>
                                <td><a href="<?php echo "../../".$ruta?>" download style="font-size: 18px;"><strong style="font-size: 15px;"> Descargar Documento Respuesta </strong>&nbsp;&nbsp;&nbsp; <em class="fas fa-download" style="font-size: 25px; color: #2CA5FF;"></em></a></td>
                                <?php }else{?>
                                <td><label style="font-size: 15px; width: 300px;" ><?= Yii::t('app', 'No se ha respondido aún.') ?></label></td>
                                <?php } ?>
                                <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Fecha de Respuesta:') ?></label></th>
                                <td><label style="font-size: 15px; width: 300px;" ><?php echo  $txtfecha_respuesta; ?></label></td>
                            </tr>
                            <tr>
                                <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Revisión CX:') ?></label></th>
                                <td><label style="font-size: 15px; width: 300px;"><?php echo  $txtrevisioncxx; ?></label></td>
                                <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Fecha de Revisión CX:') ?></label></th>
                                <td><label style="font-size: 15px; width: 300px;"><?php echo $txtfecha_revisioncx; ?></label></td>
                            </tr>
                            <tr>
                                <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Revisión Comercial:') ?></label></th>
                                <td><label style="font-size: 15px; width: 300px;"><?php echo  $txtrevision_gerentee; ?></label></td>
                                <th scope="col" style="background-color: #97b4bf; width: 250px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Fecha de Revisión Comercial:') ?></label></th>
                                <td><label style="font-size: 15px; width: 300px;"><?php echo $txtfecha_revision_gerente; ?></label></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-md-12">
                <div class="card1 mb">
                  <label style="font-size: 18px;"><em class="fas fa-cogs" style="font-size: 18px; color: #00968F;"></em> Acciones:</label>                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="card1 mb">
                      <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                'style' => 'background-color: #707372',
                                'data-toggle' => 'tooltip',
                                'title' => 'Regresar']) 
                      ?>                      </div>
                    </div>   
                  </div>
                </div>
              </div>
            </div>
          </div>
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