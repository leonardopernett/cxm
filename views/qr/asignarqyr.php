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

	$this->title = 'Gestor de PQRSF';
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

    $txtrevisioncxx = (new \yii\db\Query())
      ->select(['usua_nombre'])
      ->from(['tbl_usuarios'])
      ->where(['=','usua_id',$txtrevisioncx])
      ->scalar();

      $txtrevision_gerentee = (new \yii\db\Query())
      ->select(['usua_nombre'])
      ->from(['tbl_usuarios'])
      ->where(['=','usua_id',$txtrevision_gerente])
      ->scalar();
    
     $varEstadoPrincipal = (new \yii\db\Query())
     ->select(['id_estado'])
     ->from(['tbl_qr_casos'])
     ->where(['=','id',$id])
     ->scalar();

     $varid_area = null;
     $varid = null;
     $varid_solicitud = null;
     $varnombre = null;

     foreach ($dataProviderInfo as $value) {
         $varid_area = $value['id_area'];
         $varid = $value['id_tipologia'];
         $varid_solicitud = $value['id_responsable'];
         $varnombre = $value['comentario2'];
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
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 18px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 18px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 18px 0 rgba(0, 0, 0, 0.19);
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
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 18px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 18px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 18px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/Banner_Ev_Desarrollo.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 18px 40px -10px rgba(0, 0, 0, 0.3);

      }  
    hr{border:0;border-top:1px solid #eee;margin:18px 0}
    .w3-image{max-width:100%;height:auto}img{vertical-align:middle}a{color:inherit}
    .w3-table,.w3-table-all{border-collapse:collapse;border-spacing:0;width:100%;display:table}.w3-table-all{border:1px solid #ccc}
    .w3-bordered tr,.w3-table-all tr{border-bottom:1px solid #ddd}.w3-striped tbody tr:nth-child(even){background-color:#f1f1f1}
    .w3-table-all tr:nth-child(odd){background-color:#fff}.w3-table-all tr:nth-child(even){background-color:#f1f1f1}
    .w3-hoverable tbody tr:hover,.w3-ul.w3-hoverable li:hover{background-color:#ccc}.w3-centered tr th,.w3-centered tr td{text-align:center}
    .w3-table td,.w3-table th,.w3-table-all td,.w3-table-all th{padding:8px 8px;display:table-cell;text-align:left;vertical-align:top}
    .w3-table th:first-child,.w3-table td:first-child,.w3-table-all th:first-child,.w3-table-all td:first-child{padding-left:16px}
    .w3-btn,.w3-button{border:none;display:inline-block;padding:8px 16px;vertical-align:middle;overflow:hidden;text-decoration:none;color:inherit;background-color:inherit;text-align:center;cursor:pointer;white-space:nowrap}
    .w3-btn:hover{box-shadow:0 8px 16px 0 rgba(0,0,0,0.2),0 6px 18px 0 rgba(0,0,0,0.19)}
    .w3-btn,.w3-button{-webkit-touch-callout:none;-webkit-user-select:none;-khtml-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}   
    .w3-disabled,.w3-btn:disabled,.w3-button:disabled{cursor:not-allowed;opacity:0.3}.w3-disabled *,:disabled *{pointer-events:none}
    .w3-btn.w3-disabled:hover,.w3-btn:disabled:hover{box-shadow:none}
    .w3-badge,.w3-tag{background-color:#000;color:#fff;display:inline-block;padding-left:8px;padding-right:8px;text-align:center}.w3-badge{border-radius:50%}
    .w3-ul{list-style-type:none;padding:0;margin:0}.w3-ul li{padding:8px 16px;border-bottom:1px solid #ddd}.w3-ul li:last-child{border-bottom:none}
    .w3-tooltip,.w3-display-container{position:relative}.w3-tooltip .w3-text{display:none}.w3-tooltip:hover .w3-text{display:inline-block}
    .w3-ripple:active{opacity:0.5}.w3-ripple{transition:opacity 0s}
    .w3-input{padding:8px;display:block;border:none;border-bottom:1px solid #ccc;width:100%}
    .w3-select{padding:9px 0;width:100%;border:none;border-bottom:1px solid #ccc}
    .w3-dropdown-click,.w3-dropdown-hover{position:relative;display:inline-block;cursor:pointer}
    .w3-dropdown-hover:hover .w3-dropdown-content{display:block}
    .w3-dropdown-hover:first-child,.w3-dropdown-click:hover{background-color:#ccc;color:#000}
    .w3-dropdown-hover:hover > .w3-button:first-child,.w3-dropdown-click:hover > .w3-button:first-child{background-color:#ccc;color:#000}
    .w3-dropdown-content{cursor:auto;color:#000;background-color:#fff;display:none;position:absolute;min-width:160px;margin:0;padding:0;z-index:1}
    .w3-check,.w3-radio{width:24px;height:24px;position:relative;top:6px}
    .w3-sidebar{height:100%;width:200px;background-color:#fff;position:fixed!important;z-index:1;overflow:auto}
    .w3-bar-block .w3-dropdown-hover,.w3-bar-block .w3-dropdown-click{width:100%}
    .w3-bar-block .w3-dropdown-hover .w3-dropdown-content,.w3-bar-block .w3-dropdown-click .w3-dropdown-content{min-width:100%}
    .w3-bar-block .w3-dropdown-hover .w3-button,.w3-bar-block .w3-dropdown-click .w3-button{width:100%;text-align:left;padding:8px 16px}
    .w3-main,#main{transition:margin-left .4s}
    .w3-modal{z-index:3;display:none;padding-top:100px;position:fixed;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:rgb(0,0,0);background-color:rgba(0,0,0,0.4)}
    .w3-modal-content{margin:auto;background-color:#fff;position:relative;padding:0;outline:0;width:600px}
    .w3-bar{width:100%;overflow:hidden}.w3-center .w3-bar{display:inline-block;width:auto}
    .w3-bar .w3-bar-item{padding:8px 16px;float:left;width:auto;border:none;display:block;outline:0}
    .w3-bar .w3-dropdown-hover,.w3-bar .w3-dropdown-click{position:static;float:left}
    .w3-bar .w3-button{white-space:normal}
    .w3-bar-block .w3-bar-item{width:100%;display:block;padding:8px 16px;text-align:left;border:none;white-space:normal;float:none;outline:0}
    .w3-bar-block.w3-center .w3-bar-item{text-align:center}.w3-block{display:block;width:100%}
    .w3-responsive{display:block;overflow-x:auto}
    .w3-container:after,.w3-container:before,.w3-panel:after,.w3-panel:before,.w3-row:after,.w3-row:before,.w3-row-padding:after,.w3-row-padding:before,
    .w3-cell-row:before,.w3-cell-row:after,.w3-clear:after,.w3-clear:before,.w3-bar:before,.w3-bar:after{content:"";display:table;clear:both}
    .w3-col,.w3-half,.w3-third,.w3-twothird,.w3-threequarter,.w3-quarter{float:left;width:100%}
    .w3-col.s1{width:8.33333%}.w3-col.s2{width:16.66666%}.w3-col.s3{width:24.99999%}.w3-col.s4{width:24.33333%}
    .w3-col.s5{width:41.66666%}.w3-col.s6{width:49.99999%}.w3-col.s7{width:58.33333%}.w3-col.s8{width:66.66666%}
    .w3-col.s9{width:74.99999%}.w3-col.s10{width:83.33333%}.w3-col.s11{width:91.66666%}.w3-col.s12{width:99.99999%}
    @media (min-width:601px){.w3-col.m1{width:8.33333%}.w3-col.m2{width:16.66666%}.w3-col.m3,.w3-quarter{width:24.99999%}.w3-col.m4,.w3-third{width:33.3%}
    .w3-col.m5{width:41.66666%}.w3-col.m6,.w3-half{width:49.99999%}.w3-col.m7{width:58.33333%}.w3-col.m8,.w3-twothird{width:66.66666%}
    .w3-col.m9,.w3-threequarter{width:74.99999%}.w3-col.m10{width:83.33333%}.w3-col.m11{width:91.66666%}.w3-col.m12{width:99.99999%}}
    @media (min-width:993px){.w3-col.l1{width:8.33333%}.w3-col.l2{width:16.66666%}.w3-col.l3{width:24.99999%}.w3-col.l4{width:33.33333%}
    .w3-col.l5{width:41.66666%}.w3-col.l6{width:49.99999%}.w3-col.l7{width:58.33333%}.w3-col.l8{width:66.66666%}
    .w3-col.l9{width:74.99999%}.w3-col.l10{width:83.33333%}.w3-col.l11{width:91.66666%}.w3-col.l12{width:99.99999%}}
    .w3-rest{overflow:hidden}.w3-stretch{margin-left:-16px;margin-right:-16px}
    .w3-content,.w3-auto{margin-left:auto;margin-right:auto}.w3-content{max-width:980px}.w3-auto{max-width:1140px}
    .w3-cell-row{display:table;width:100%}.w3-cell{display:table-cell}
    .w3-cell-top{vertical-align:top}.w3-cell-middle{vertical-align:middle}.w3-cell-bottom{vertical-align:bottom}
    .w3-hide{display:none!important}.w3-show-block,.w3-show{display:block!important}.w3-show-inline-block{display:inline-block!important}
    @media (max-width:1205px){.w3-auto{max-width:95%}}
    @media (max-width:600px){.w3-modal-content{margin:0 10px;width:auto!important}.w3-modal{padding-top:30px}
    .w3-dropdown-hover.w3-mobile .w3-dropdown-content,.w3-dropdown-click.w3-mobile .w3-dropdown-content{position:relative}  
    .w3-hide-small{display:none!important}.w3-mobile{display:block;width:100%!important}.w3-bar-item.w3-mobile,.w3-dropdown-hover.w3-mobile,.w3-dropdown-click.w3-mobile{text-align:center}
    .w3-dropdown-hover.w3-mobile,.w3-dropdown-hover.w3-mobile .w3-btn,.w3-dropdown-hover.w3-mobile .w3-button,.w3-dropdown-click.w3-mobile,.w3-dropdown-click.w3-mobile .w3-btn,.w3-dropdown-click.w3-mobile .w3-button{width:100%}}
    @media (max-width:768px){.w3-modal-content{width:500px}.w3-modal{padding-top:50px}}
    @media (min-width:993px){.w3-modal-content{width:900px}.w3-hide-large{display:none!important}.w3-sidebar.w3-collapse{display:block!important}}
    @media (max-width:992px) and (min-width:601px){.w3-hide-medium{display:none!important}}
    @media (max-width:992px){.w3-sidebar.w3-collapse{display:none}.w3-main{margin-left:0!important;margin-right:0!important}.w3-auto{max-width:100%}}
    .w3-top,.w3-bottom{position:fixed;width:100%;z-index:1}.w3-top{top:0}.w3-bottom{bottom:0}
    .w3-overlay{position:fixed;display:none;width:100%;height:100%;top:0;left:0;right:0;bottom:0;background-color:rgba(0,0,0,0.5);z-index:2}
    .w3-display-topleft{position:absolute;left:0;top:0}.w3-display-topright{position:absolute;right:0;top:0}
    .w3-display-bottomleft{position:absolute;left:0;bottom:0}.w3-display-bottomright{position:absolute;right:0;bottom:0}
    .w3-display-middle{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);-ms-transform:translate(-50%,-50%)}
    .w3-display-left{position:absolute;top:50%;left:0%;transform:translate(0%,-50%);-ms-transform:translate(-0%,-50%)}
    .w3-display-right{position:absolute;top:50%;right:0%;transform:translate(0%,-50%);-ms-transform:translate(0%,-50%)}
    .w3-display-topmiddle{position:absolute;left:50%;top:0;transform:translate(-50%,0%);-ms-transform:translate(-50%,0%)}
    .w3-display-bottommiddle{position:absolute;left:50%;bottom:0;transform:translate(-50%,0%);-ms-transform:translate(-50%,0%)}
    .w3-display-container:hover .w3-display-hover{display:block}.w3-display-container:hover span.w3-display-hover{display:inline-block}.w3-display-hover{display:none}
    .w3-display-position{position:absolute}
    .w3-circle{border-radius:50%}
    .w3-round-small{border-radius:2px}.w3-round,.w3-round-medium{border-radius:4px}.w3-round-large{border-radius:8px}.w3-round-xlarge{border-radius:16px}.w3-round-xxlarge{border-radius:32px}
    .w3-row-padding,.w3-row-padding>.w3-half,.w3-row-padding>.w3-third,.w3-row-padding>.w3-twothird,.w3-row-padding>.w3-threequarter,.w3-row-padding>.w3-quarter,.w3-row-padding>.w3-col{padding:0 8px}
    .w3-container,.w3-panel{padding:0.01em 16px}.w3-panel{margin-top:16px;margin-bottom:16px}
    .w3-code{width:auto;background-color:#fff;padding:8px 15px;border-left:4px solid #4CAF50;word-wrap:break-word}
    .w3-codespan{color:crimson;background-color:#f1f1f1;padding-left:4px;padding-right:4px;font-size:110%}
    .w3-card,.w3-card-2{box-shadow:0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12)}
    .w3-card-4,.w3-hover-shadow:hover{box-shadow:0 4px 10px 0 rgba(0,0,0,0.2),0 4px 18px 0 rgba(0,0,0,0.19)}
    .w3-spin{animation:w3-spin 2s infinite linear}@keyframes w3-spin{0%{transform:rotate(0deg)}100%{transform:rotate(359deg)}}
    .w3-animate-fading{animation:fading 10s infinite}@keyframes fading{0%{opacity:0}50%{opacity:1}100%{opacity:0}}
    .w3-animate-opacity{animation:opac 0.8s}@keyframes opac{from{opacity:0} to{opacity:1}}
    .w3-animate-top{position:relative;animation:animatetop 0.4s}@keyframes animatetop{from{top:-300px;opacity:0} to{top:0;opacity:1}}
    .w3-animate-left{position:relative;animation:animateleft 0.4s}@keyframes animateleft{from{left:-300px;opacity:0} to{left:0;opacity:1}}
    .w3-animate-right{position:relative;animation:animateright 0.4s}@keyframes animateright{from{right:-300px;opacity:0} to{right:0;opacity:1}}
    .w3-animate-bottom{position:relative;animation:animatebottom 0.4s}@keyframes animatebottom{from{bottom:-300px;opacity:0} to{bottom:0;opacity:1}}
    .w3-animate-zoom {animation:animatezoom 0.6s}@keyframes animatezoom{from{transform:scale(0)} to{transform:scale(1)}}
    .w3-animate-input{transition:width 0.4s ease-in-out}.w3-animate-input:focus{width:100%!important}
    .w3-opacity,.w3-hover-opacity:hover{opacity:0.60}.w3-opacity-off,.w3-hover-opacity-off:hover{opacity:1}
    .w3-opacity-max{opacity:0.25}.w3-opacity-min{opacity:0.75}
    .w3-greyscale-max,.w3-grayscale-max,.w3-hover-greyscale:hover,.w3-hover-grayscale:hover{filter:grayscale(100%)}
    .w3-greyscale,.w3-grayscale{filter:grayscale(75%)}.w3-greyscale-min,.w3-grayscale-min{filter:grayscale(50%)}
    .w3-sepia{filter:sepia(75%)}.w3-sepia-max,.w3-hover-sepia:hover{filter:sepia(100%)}.w3-sepia-min{filter:sepia(50%)}
    .w3-tiny{font-size:10px!important}.w3-small{font-size:15px!important}.w3-medium{font-size:16px!important}.w3-large{font-size:18px!important}
    .w3-xlarge{font-size:24px!important}.w3-xxlarge{font-size:36px!important}.w3-xxxlarge{font-size:48px!important}.w3-jumbo{font-size:64px!important}
    .w3-left-align{text-align:left!important}.w3-right-align{text-align:right!important}.w3-justify{text-align:justify!important}.w3-center{text-align:center!important}
    .w3-border-0{border:0!important}.w3-border{border:1px solid #ccc!important}
    .w3-border-top{border-top:1px solid #ccc!important}.w3-border-bottom{border-bottom:1px solid #ccc!important}
    .w3-border-left{border-left:1px solid #ccc!important}.w3-border-right{border-right:1px solid #ccc!important}
    .w3-topbar{border-top:6px solid #ccc!important}.w3-bottombar{border-bottom:6px solid #ccc!important}
    .w3-leftbar{border-left:6px solid #ccc!important}.w3-rightbar{border-right:6px solid #ccc!important}
    .w3-section,.w3-code{margin-top:16px!important;margin-bottom:16px!important}
    .w3-margin{margin:16px!important}.w3-margin-top{margin-top:16px!important}.w3-margin-bottom{margin-bottom:16px!important}
    .w3-margin-left{margin-left:16px!important}.w3-margin-right{margin-right:16px!important}
    .w3-padding-small{padding:4px 8px!important}.w3-padding{padding:8px 16px!important}.w3-padding-large{padding:15px 24px!important}
    .w3-padding-16{padding-top:16px!important;padding-bottom:16px!important}.w3-padding-24{padding-top:24px!important;padding-bottom:24px!important}
    .w3-padding-32{padding-top:32px!important;padding-bottom:32px!important}.w3-padding-48{padding-top:48px!important;padding-bottom:48px!important}
    .w3-padding-64{padding-top:64px!important;padding-bottom:64px!important}
    .w3-left{float:left!important}.w3-right{float:right!important}
    .w3-button:hover{color:#000!important;background-color:#ccc!important}
    .w3-transparent,.w3-hover-none:hover{background-color:transparent!important}
    .w3-hover-none:hover{box-shadow:none!important}
    /* Colors */
    .w3-amber,.w3-hover-amber:hover{color:#000!important;background-color:#ffc107!important}
    .w3-aqua,.w3-hover-aqua:hover{color:#000!important;background-color:#00ffff!important}
    .w3-blue,.w3-hover-blue:hover{color:#fff!important;background-color:#2196F3!important}
    .w3-light-blue,.w3-hover-light-blue:hover{color:#000!important;background-color:#87CEEB!important}
    .w3-brown,.w3-hover-brown:hover{color:#fff!important;background-color:#795548!important}
    .w3-cyan,.w3-hover-cyan:hover{color:#000!important;background-color:#00bcd4!important}
    .w3-blue-grey,.w3-hover-blue-grey:hover,.w3-blue-gray,.w3-hover-blue-gray:hover{color:#fff!important;background-color:#607d8b!important}
    .w3-green,.w3-hover-green:hover{color:#fff!important;background-color:#4CAF50!important}
    .w3-light-green,.w3-hover-light-green:hover{color:#000!important;background-color:#8bc34a!important}
    .w3-indigo,.w3-hover-indigo:hover{color:#fff!important;background-color:#3f51b5!important}
    .w3-khaki,.w3-hover-khaki:hover{color:#000!important;background-color:#f0e68c!important}
    .w3-lime,.w3-hover-lime:hover{color:#000!important;background-color:#cddc39!important}
    .w3-orange,.w3-hover-orange:hover{color:#000!important;background-color:#ff9800!important}
    .w3-deep-orange,.w3-hover-deep-orange:hover{color:#fff!important;background-color:#ff5722!important}
    .w3-pink,.w3-hover-pink:hover{color:#fff!important;background-color:#e91e63!important}
    .w3-purple,.w3-hover-purple:hover{color:#fff!important;background-color:#9c27b0!important}
    .w3-deep-purple,.w3-hover-deep-purple:hover{color:#fff!important;background-color:#673ab7!important}
    .w3-red,.w3-hover-red:hover{color:#fff!important;background-color:#f44336!important}
    .w3-sand,.w3-hover-sand:hover{color:#000!important;background-color:#fdf5e6!important}
    .w3-teal,.w3-hover-teal:hover{color:#fff!important;background-color:#009688!important}
    .w3-yellow,.w3-hover-yellow:hover{color:#000!important;background-color:#ffeb3b!important}
    .w3-white,.w3-hover-white:hover{color:#000!important;background-color:#fff!important}
    .w3-black,.w3-hover-black:hover{color:#fff!important;background-color:#000!important}
    .w3-grey,.w3-hover-grey:hover,.w3-gray,.w3-hover-gray:hover{color:#000!important;background-color:#9e9e9e!important}
    .w3-light-grey,.w3-hover-light-grey:hover,.w3-light-gray,.w3-hover-light-gray:hover{color:#000!important;background-color:#f1f1f1!important}
    .w3-dark-grey,.w3-hover-dark-grey:hover,.w3-dark-gray,.w3-hover-dark-gray:hover{color:#fff!important;background-color:#616161!important}
    .w3-pale-red,.w3-hover-pale-red:hover{color:#000!important;background-color:#ffdddd!important}
    .w3-pale-green,.w3-hover-pale-green:hover{color:#000!important;background-color:#ddffdd!important}
    .w3-pale-yellow,.w3-hover-pale-yellow:hover{color:#000!important;background-color:#ffffcc!important}
    .w3-pale-blue,.w3-hover-pale-blue:hover{color:#000!important;background-color:#ddffff!important}
    .w3-text-amber,.w3-hover-text-amber:hover{color:#ffc107!important}
    .w3-text-aqua,.w3-hover-text-aqua:hover{color:#00ffff!important}
    .w3-text-blue,.w3-hover-text-blue:hover{color:#2196F3!important}
    .w3-text-light-blue,.w3-hover-text-light-blue:hover{color:#87CEEB!important}
    .w3-text-brown,.w3-hover-text-brown:hover{color:#795548!important}
    .w3-text-cyan,.w3-hover-text-cyan:hover{color:#00bcd4!important}
    .w3-text-blue-grey,.w3-hover-text-blue-grey:hover,.w3-text-blue-gray,.w3-hover-text-blue-gray:hover{color:#607d8b!important}
    .w3-text-green,.w3-hover-text-green:hover{color:#4CAF50!important}
    .w3-text-light-green,.w3-hover-text-light-green:hover{color:#8bc34a!important}
    .w3-text-indigo,.w3-hover-text-indigo:hover{color:#3f51b5!important}
    .w3-text-khaki,.w3-hover-text-khaki:hover{color:#b4aa50!important}
    .w3-text-lime,.w3-hover-text-lime:hover{color:#cddc39!important}
    .w3-text-orange,.w3-hover-text-orange:hover{color:#ff9800!important}
    .w3-text-deep-orange,.w3-hover-text-deep-orange:hover{color:#ff5722!important}
    .w3-text-pink,.w3-hover-text-pink:hover{color:#e91e63!important}
    .w3-text-purple,.w3-hover-text-purple:hover{color:#9c27b0!important}
    .w3-text-deep-purple,.w3-hover-text-deep-purple:hover{color:#673ab7!important}
    .w3-text-red,.w3-hover-text-red:hover{color:#f44336!important}
    .w3-text-sand,.w3-hover-text-sand:hover{color:#fdf5e6!important}
    .w3-text-teal,.w3-hover-text-teal:hover{color:#009688!important}
    .w3-text-yellow,.w3-hover-text-yellow:hover{color:#d2be0e!important}
    .w3-text-white,.w3-hover-text-white:hover{color:#fff!important}
    .w3-text-black,.w3-hover-text-black:hover{color:#000!important}
    .w3-text-grey,.w3-hover-text-grey:hover,.w3-text-gray,.w3-hover-text-gray:hover{color:#757575!important}
    .w3-text-light-grey,.w3-hover-text-light-grey:hover,.w3-text-light-gray,.w3-hover-text-light-gray:hover{color:#f1f1f1!important}
    .w3-text-dark-grey,.w3-hover-text-dark-grey:hover,.w3-text-dark-gray,.w3-hover-text-dark-gray:hover{color:#3a3a3a!important}
    .w3-border-amber,.w3-hover-border-amber:hover{border-color:#ffc107!important}
    .w3-border-aqua,.w3-hover-border-aqua:hover{border-color:#00ffff!important}
    .w3-border-blue,.w3-hover-border-blue:hover{border-color:#2196F3!important}
    .w3-border-light-blue,.w3-hover-border-light-blue:hover{border-color:#87CEEB!important}
    .w3-border-brown,.w3-hover-border-brown:hover{border-color:#795548!important}
    .w3-border-cyan,.w3-hover-border-cyan:hover{border-color:#00bcd4!important}
    .w3-border-blue-grey,.w3-hover-border-blue-grey:hover,.w3-border-blue-gray,.w3-hover-border-blue-gray:hover{border-color:#607d8b!important}
    .w3-border-green,.w3-hover-border-green:hover{border-color:#4CAF50!important}
    .w3-border-light-green,.w3-hover-border-light-green:hover{border-color:#8bc34a!important}
    .w3-border-indigo,.w3-hover-border-indigo:hover{border-color:#3f51b5!important}
    .w3-border-khaki,.w3-hover-border-khaki:hover{border-color:#f0e68c!important}
    .w3-border-lime,.w3-hover-border-lime:hover{border-color:#cddc39!important}
    .w3-border-orange,.w3-hover-border-orange:hover{border-color:#ff9800!important}
    .w3-border-deep-orange,.w3-hover-border-deep-orange:hover{border-color:#ff5722!important}
    .w3-border-pink,.w3-hover-border-pink:hover{border-color:#e91e63!important}
    .w3-border-purple,.w3-hover-border-purple:hover{border-color:#9c27b0!important}
    .w3-border-deep-purple,.w3-hover-border-deep-purple:hover{border-color:#673ab7!important}
    .w3-border-red,.w3-hover-border-red:hover{border-color:#f44336!important}
    .w3-border-sand,.w3-hover-border-sand:hover{border-color:#fdf5e6!important}
    .w3-border-teal,.w3-hover-border-teal:hover{border-color:#009688!important}
    .w3-border-yellow,.w3-hover-border-yellow:hover{border-color:#ffeb3b!important}
    .w3-border-white,.w3-hover-border-white:hover{border-color:#fff!important}
    .w3-border-black,.w3-hover-border-black:hover{border-color:#000!important}
    .w3-border-grey,.w3-hover-border-grey:hover,.w3-border-gray,.w3-hover-border-gray:hover{border-color:#9e9e9e!important}
    .w3-border-light-grey,.w3-hover-border-light-grey:hover,.w3-border-light-gray,.w3-hover-border-light-gray:hover{border-color:#f1f1f1!important}
    .w3-border-dark-grey,.w3-hover-border-dark-grey:hover,.w3-border-dark-gray,.w3-hover-border-dark-gray:hover{border-color:#616161!important}
    .w3-border-pale-red,.w3-hover-border-pale-red:hover{border-color:#ffe7e7!important}.w3-border-pale-green,.w3-hover-border-pale-green:hover{border-color:#e7ffe7!important}
    .w3-border-pale-yellow,.w3-hover-border-pale-yellow:hover{border-color:#ffffcc!important}.w3-border-pale-blue,.w3-hover-border-pale-blue:hover{border-color:#e7ffff!important}

    

  .nav-pills > li.active > a, .nav-pills > li.active > a:hover, .nav-pills > li.active > a:focus {
  color: #fff;
  background-color: #00968F;
  }   

</style>
<!-- datatable -->
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<!-- Extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<script src="../../js_extensions/chart.min.js"></script>

<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>
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
<div class="capaUno">
  <?php $form = ActiveForm::begin(["method" => "post","enableClientValidation" => true,'options' => ['enctype' => 'multipart/form-data'],'fieldConfig' => ['inputOptions' => ['autocomplete' => 'off']]]) ?>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">

        <!-- Nav tabs -->
        <ul class="nav nav-pills mb-3" role="tablist" id="pills-tab">
          <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Información General</a></li>
          <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Asignar</a></li>
          <li role="presentation"><a href="#respuesta" aria-controls="respuesta" role="tab" data-toggle="tab">Enviar Respuesta</a></li>
          <li role="presentation"><a href="#revisioncx" aria-controls="revisioncx" role="tab" data-toggle="tab">Revisión CX</a></li>
          <li role="presentation"><a href="#revisioncomercial" aria-controls="revisioncomercial" role="tab" data-toggle="tab">Revisión Comercial</a></li>
        </ul>
      
        <div class="tab-content">

          <!-- Tab informacion general -->
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

          <!-- Tab Asignación -->
          <?php if ($varEstadoPrincipal == 9) { ?>
          <div role="tabpanel" class="tab-pane" id="profile">
            <br>
            <div class="row">
              <div class="col-md-12">
                <div class="card1 mb">
                  <label style="font-size: 18px;"><em class="fas fa-users" style="font-size: 18px; color: #00968F;"></em> Asignar Caso a:</label>                

                  <br> 
                  <div class="row">

                    <div class="col-md-6">

                    <label for="txtcliente" style="font-size: 16px;"><em class="fas fa-check" style="font-size: 18px; color: #00968F;"></em><?= Yii::t('app', ' Área de Asignación') ?><span style="color:red;"> *</span></label>
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
                                                            'required' => 'required',
                                                        ]
                                            ); 
                        ?>
                    </div>

                    <div class="col-md-6">
                      <label for="txtgerente" style="font-size: 16px;"><em class="fas fa-check" style="font-size: 18px; color: #00968F;"></em><?= Yii::t('app', ' Tipolgia') ?><span style="color:red;"> *</span></label>
                      <?=  $form->field($model8, 'id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Areasqyr::find()->distinct()->where("anulado = 0")->orderBy(['nombre'=> SORT_ASC])->all(), 'id', 'nombre'),
                                                        [
                                                            'prompt' => 'Seleccionar...',                                         
                                                            'id' => 'requester',
                                                            'required' => 'required',
                                                        
                                                        ]
                                                    );
                        ?>
                    </div>
                  </div>

                  <div class="row">

                    <div class="col-md-6">
                      <label for="txtResponsable" style="font-size: 16px;"><em class="fas fa-check" style="font-size: 18px; color: #00968F;"></em><?= Yii::t('app', ' Responsable') ?><span style="color:red;"> *</span></label>
                      <?=  $form->field($model2, 'id_responsable', ['labelOptions' => [], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Usuarios::find()->where(['not like', 'usua_nombre', '%no usar%', false])->orderBy(['usua_nombre'=> SORT_ASC])->all(), 'usua_id', 'usua_nombre'),
                                                [
                                                    'prompt'=>'Seleccionar...',
                                                    'required' => 'required'
                                                ]
                                        )->label(''); 
                            ?>
                    </div>

                    <div class="col-md-6">
                      <label  style="font-size: 16px;"><em class="fas fa-check" style="font-size: 18px; color: #00968F;"></em><?= Yii::t('app', ' Tipo de Respuesta') ?><span style="color:red;"> *</span></label>
                        <select id="txttiporespuesta" requerid class ='form-control' onchange="tipo();">
                          <option value="" disabled selected>Seleccione...</option>
                          <option value="Interna">Interna</option>
                          <option value="Externa">Externa</option>
                        </select>
                    </div>
                  </div>

                  <div class="row">
                    
                    <div class="col-md-6">      
                      <label for="txtResponsable" style="font-size: 16px;"><em class="fas fa-check" style="font-size: 18px; color: #00968F;"></em><?= Yii::t('app', ' Tipo de PQRSF') ?><span style="color:red;"> *</span></label>
                      <?=  $form->field($model4, 'id', ['labelOptions' => [], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Tipopqrs::find()->orderBy(['tipo_de_dato'=> SORT_ASC])->all(), 'id', 'tipo_de_dato'),
                                          [
                                              'prompt'=>'Seleccionar...',
                                              'required' => 'required',
                                          ]
                                  )->label(''); 
                      ?>
                    </div>
                    <div class="col-md-6">                  
                      <label for="txtResponsable" style="font-size: 16px;"><em class="fas fa-check" style="font-size: 18px; color: #00968F;"></em><?= Yii::t('app', ' Tipo de Estado') ?><span style="color:red;"> *</span></label>
                        <?=  $form->field($model5, 'id_estado', ['labelOptions' => [], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Estadosqyr::find()->orderBy(['nombre'=> SORT_ASC])->all(), 'id_estado', 'nombre'),
                                            [
                                                'prompt'=>'Seleccionar...',
                                                'required' => 'required',
                                            ]
                                    )->label(''); 
                        ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6" style="display:none"> 
                      <?= $form->field($model6, 'ccdirector', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idtipo'])?>
                    </div> 
                  </div>
                  <?= $form->field($model5, 'id_estado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['values' => 4, 'class' => 'hidden'])->label('') ?>
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
                        <?= Html::submitButton("Guardar - Enviar", ["class" => "btn btn-primary"]) ?>
                      </div>
                    </div>   
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php 
            }else{ ?>
              <div role="tabpanel" class="tab-pane" id="profile">
                <br>
                <div class="row">
                  <div class="col-md-12">
                    <div class="card1 mb">
                      <label style="font-size: 18px;"><em class="fas fa-exclamation" style="font-size: 18px; color: #00968F;"></em> Hola, te comentamos que no tienes permisos para visualizar esta sesión.</label>                
                    </div>
                  </div>
                </div> 
              </div>
            <?php
            }
          ?>
 
          <!-- Tab Respuesta -->
          <?php if ($varEstadoPrincipal == 4) { ?>
          <div role="tabpanel" class="tab-pane" id="respuesta">
            <div class="row" style='display:none;'>
              <div class="col-md-12">        
                <div class="card1 mb">
                  <label><em class="far fa-clipboard" style="font-size: 20px; color: #e8701a; "></em> Gestión del Caso</label>                
                  <br> 
                  <div class="row" >

                    <div class="col-md-6">
                        <label for="txtcliente" style="font-size: 14px;">Área de Asignación</label>
                        <?= $form->field($model2, 'id_area', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['value' => $varid_area, 'readonly'=>true])?> 
                        
                    </div>
                    <div class="col-md-6">
                        <label for="txtgerente" style="font-size: 14px;">Tipología</label>
                        <?= $form->field($model8, 'id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['value' => $varid, 'readonly'=>true])?> 
                        
                    </div>
                  </div>
                  <div class="row" >
                    <div class="col-md-6">                  
                        <label for="txtResponsable" style="font-size: 14px;">Responsable Asignación</label>         
                        <?= $form->field($model2, 'id_solicitud', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['value' => $varid_solicitud, 'readonly'=>true])?>              
                        
                    </div>
                    <div class="col-md-6">                                      
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Comentarios') ?></label>
                        <?= $form->field($model2, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['value' => $varnombre, 'readonly'=>true])?>
                          
                    </div>
                  </div>
                  <br>                                        
                </div>
              </div>    
            </div>
            <br>
            <div class="row">
              <div class="col-md-12">
                <div class="card1 mb">
                  <label style="font-size: 18px;"><em class="fas fa-paper-plane" style="font-size: 18px; color: #00968F;"></em> Dar Respuesta del Caso</label>                
                  <br>                    
                
                  <div class="row" >
                    <div class="col-md-6"> 
                      <div class="card1 mb">
                        <label style="font-size: 16px;"><em class="fas fa-download" style="font-size: 25px; color: #00968F;"></em><?= Yii::t('app', ' Descargar Carta de Respuesta') ?></label><br>                        
                        <a style=" background-color: #337ab7" class="btn btn-success" rel="stylesheet" type="text/css" href="../../images/uploads/Carta respuesta Q&R.docx" title="Descagar Carta de Respuesta" target="_blank"> Descargar Carta</a>  
    
                      </div>
                    </div>

                    <div class="col-md-6"> 
                      <div class="card1 mb">                         
                        <label style="font-size: 16px;"><em class="fas fa-upload" style="font-size: 25px; color: #00968F;"></em><?= Yii::t('app', ' Anexar Carta de Respuesta') ?></label>                        
                        <?= $form->field($model, 'file')->fileInput(["class"=>"input-file" ,'id'=>'idfile','style'=>'font-size: 15px;','extensions' => ' pdf',' docx', 'maxSize' => 1024*1024*1024])->label('') ?>  
                        <label style="font-size: 15px;"> <?= Yii::t('app', ' ___________________ ') ?></label><br>
                        <label style="font-size: 16px;"> <?= Yii::t('app', ' Tener en cuenta... ') ?></label><br>
                        <p style="font-size: 15px;"> <?= Yii::t('app', ' * Los archivos a subir deben tener la extensión correcta .docx, .pdf ') ?></p>
                        <p style="font-size: 15px;"> <?= Yii::t('app', ' * Recomendable que los archivos no tengan espacios o signos de puntuación de más.') ?></p>
                      </div>                         
                    </div>
                    
                  </div>
                  <?= $form->field($model5, 'id_estado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['values' => 8, 'class' => 'hidden'])->label('') ?>
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
                        <?= Html::submitButton("Guardar - Enviar", ["class" => "btn btn-primary"]) ?>
                      </div>
                    </div>   
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php 
            }else{ ?>
              <div role="tabpanel" class="tab-pane" id="respuesta">
                <br>
                <div class="row">
                  <div class="col-md-12">
                    <div class="card1 mb">
                      <label style="font-size: 18px;"><em class="fas fa-exclamation" style="font-size: 18px; color: #00968F;"></em> Hola, te comentamos que no tienes permisos para visualizar esta sesión.</label>                
                    </div>
                  </div>
                </div> 
              </div>
            <?php
            }
          ?>

          <!-- Tab Revisión CX -->
          <?php if ($varEstadoPrincipal == 8) { ?>
          <div role="tabpanel" class="tab-pane" id="revisioncx">
            <div class="row" style='display:none;'>
              <div class="col-md-12">        
                <div class="card1 mb">
                  <label><em class="far fa-clipboard" style="font-size: 20px; color: #e8701a; "></em> Gestión del Caso</label>                
                  <br> 
                  <div class="row" >

                    <div class="col-md-6">
                        <label for="txtcliente" style="font-size: 14px;">Área de Asignación</label>
                        <?= $form->field($model2, 'id_area', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['value' => $varid_area, 'readonly'=>true])?> 
                        
                    </div>
                    <div class="col-md-6">
                        <label for="txtgerente" style="font-size: 14px;">Tipología</label>
                        <?= $form->field($model8, 'id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['value' => $varid, 'readonly'=>true])?> 
                        
                    </div>
                  </div>
                  <div class="row" >
                    <div class="col-md-6">                  
                        <label for="txtResponsable" style="font-size: 14px;">Responsable Asignación</label>         
                        <?= $form->field($model2, 'id_solicitud', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['value' => $varid_solicitud, 'readonly'=>true])?>              
                        
                    </div>
                    <div class="col-md-6">                                      
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Comentarios') ?></label>
                        <?= $form->field($model2, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['value' => $varnombre, 'readonly'=>true])?>
                          
                    </div>
                  </div>
                  <br>                                        
                </div>
              </div>    
            </div>
            <br>
            <div class="row">
              <div class="col-md-12">
                <div class="card1 mb">
                  <label style="font-size: 18px;"><em class="fas fa-user" style="font-size: 18px; color: #00968F;"></em> Revisión Caso CX</label>                
                  <br>                    
                
                  <div class="row" >
                    <div class="col-md-6"> 
                      <div class="card1 mb">
                        <label style="font-size: 16px;"><em class="fas fa-edit" style="font-size: 18px; color: #00968F;"></em> Tipo de Respuesta <span style="color:red;"> *</span></label>
                        <select required id="respuestacx" class ='form-control' onchange="respuesta();">
                            <option value="" disabled selected>Seleccione...</option>
                            <option value="Aprobada">Aprobada</option>
                            <option value="Rechazada">Rechazada</option>
                        </select>
                      </div>
                      <div class="col-md-6" style="display:none"> 
                        <?= $form->field($model13, 'ccdirector', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idrespuesta'])?>                                                    
                      </div> 
                    </div>
                  </div>
                  <?= $form->field($model5, 'id_estado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['value' => 5, 'class' => 'hidden'])->label('') ?>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="card1 mb">
                  <label style="font-size: 18px;"><em class="fas fa-cogs" style="font-size: 18px; color: #00968F;"></em> Acciones:</label>                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="card1 mb">
                        <?= Html::submitButton("Guardar - Enviar", ["class" => "btn btn-primary"]) ?>
                      </div>
                    </div>   
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php 
            }else{ ?>
              <div role="tabpanel" class="tab-pane" id="revisioncx">
                <br>
                <div class="row">
                  <div class="col-md-12">
                    <div class="card1 mb">
                      <label style="font-size: 18px;"><em class="fas fa-exclamation" style="font-size: 18px; color: #00968F;"></em> Hola, te comentamos que no tienes permisos para visualizar esta sesión.</label>                
                    </div>
                  </div>
                </div> 
              </div>
            <?php
            }
          ?>


          <!-- Tab Revisión Comercial -->
          <?php if ($varEstadoPrincipal == 5) { ?>
          <div role="tabpanel" class="tab-pane" id="revisioncomercial">
            <div class="row" style='display:none;'>
              <div class="col-md-12">        
                <div class="card1 mb">
                  <label><em class="far fa-clipboard" style="font-size: 20px; color: #e8701a; "></em> Gestión del Caso</label>                
                  <br> 
                  <div class="row" >

                    <div class="col-md-6">
                        <label for="txtcliente" style="font-size: 14px;">Área de Asignación</label>
                        <?= $form->field($model2, 'id_area', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['value' => $varid_area, 'readonly'=>true])?> 
                        
                    </div>
                    <div class="col-md-6">
                        <label for="txtgerente" style="font-size: 14px;">Tipología</label>
                        <?= $form->field($model8, 'id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['value' => $varid, 'readonly'=>true])?> 
                        
                    </div>
                  </div>
                  <div class="row" >
                    <div class="col-md-6">                  
                        <label for="txtResponsable" style="font-size: 14px;">Responsable Asignación</label>         
                        <?= $form->field($model2, 'id_solicitud', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['value' => $varid_solicitud, 'readonly'=>true])?>              
                        
                    </div>
                    <div class="col-md-6">                                      
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Comentarios') ?></label>
                        <?= $form->field($model2, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['value' => $varnombre, 'readonly'=>true])?>
                          
                    </div>
                  </div>
                  <br>                                        
                </div>
              </div>    
            </div>
            <br>
            <div class="row">
              <div class="col-md-12">
                <div class="card1 mb">
                  <label style="font-size: 18px;"><em class="fas fa-user-plus" style="font-size: 18px; color: #00968F;"></em> Revisión Caso Gerente</label>                
                  <br>                    
                
                  <div class="row" >
                    <div class="col-md-6"> 
                      <div class="card1 mb">
                        <label style="font-size: 16px;"><em class="fas fa-edit" style="font-size: 18px; color: #00968F;"></em> Tipo de Respuesta <span style="color:red;"> *</span></label>
                        <select required id="rtacomercial" class ='form-control' onchange="comercial();"> >
                            <option value="" disabled selected>Seleccione...</option>
                            <option value="Aprobada">Aprobada</option>
                            <option value="Rechazada">Rechazada</option>
                        </select>
                      </div>
                      <div class="col-md-6" style="display:none"> 
                        <?= $form->field($model12, 'ccdirector', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idrespuestacomercial'])?>                                                    
                      </div> 
                    </div>
                  </div>
                  <?= $form->field($model5, 'id_estado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['value' => 2, 'class' => 'hidden'])->label('') ?>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="card1 mb">
                  <label style="font-size: 18px;"><em class="fas fa-cogs" style="font-size: 18px; color: #00968F;"></em> Acciones:</label>                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="card1 mb">
                        <?= Html::submitButton("Guardar - Enviar", ["class" => "btn btn-primary",'id' => 'guardar-enviar-button']) ?>
                      </div>
                    </div>   
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php 
            }else{ ?>
              <div role="tabpanel" class="tab-pane" id="revisioncomercial">
                <br>
                <div class="row">
                  <div class="col-md-12">
                    <div class="card1 mb">
                      <label style="font-size: 18px;"><em class="fas fa-exclamation" style="font-size: 18px; color: #00968F;"></em> Hola, te comentamos que no tienes permisos para visualizar esta sesión.</label>                
                    </div>
                  </div>
                </div> 
              </div>
            <?php
            }
          ?>

          <hr>
          <br>
        </div> 

      </div>
    </div>
  </div>    
</div>
<br>
<hr>

<script>
 document.getElementById('idfile').addEventListener('change', function () {
        var fileInput = this;
        var allowedExtensions = ['pdf ','docx'];
        var maxSize = <?= 1024*1024*1024 ?>; // Tamaño máximo en bytes
        var fileSize = fileInput.files[0].size;
        var fileExtension = fileInput.value.split('.').pop().toLowerCase();

        if (allowedExtensions.indexOf(fileExtension) === -1) {
            swal.fire("Aviso!!!",'No se permiten extensiones de archivo diferentes de ' + allowedExtensions.join(', '));
            fileInput.value = ''; // Limpiar el campo de entrada
        } else if (fileSize > maxSize) {
            swal.fire("Aviso!!!",'El tamaño del archivo debe ser menor o igual a '  + (maxSize / (1024 * 1024)) + ' MB');
            fileInput.value = ''; // Limpiar el campo de entrada
        }
    });


    
  function respuesta(){
    var varRta = document.getElementById("respuestacx").value;
    document.getElementById("idrespuesta").value = varRta;
  };

  function comercial(){
    var varRta = document.getElementById("rtacomercial").value;
    document.getElementById("idrespuestacomercial").value = varRta;
  };

  function tipo(){
    var varRta = document.getElementById("txttiporespuesta").value;
    document.getElementById("idtipo").value = varRta;
  };
   
  <?php  if(base64_decode(Yii::$app->request->get("varAlerta")) === "1"){ ?>       
    swal.fire("Éxito","Datos Guardados Correctamente","success"); 
  <?php }
    
  if(base64_decode(Yii::$app->request->get("varAlerta")) === "2"){?>
    swal.fire("Aviso","Hubo un error al enviar los datos","warning");
  <?php }   ?> 
    


  function opennovedaddatos(){
        var varidtbnt12 = document.getElementById("idtbnt12");
        var varidtbnt22 = document.getElementById("idtbnt22");
        var varidnovedadt = document.getElementById("capa00t");

        varidtbnt12.style.display = 'none';
        varidtbnt22.style.display = 'inline';
        varidnovedadt.style.display = 'inline';

    };

    function closenovedaddatos(){
        var varidtbnt12 = document.getElementById("idtbnt12");
        var varidtbnt22 = document.getElementById("idtbnt22");
        var varidnovedadt = document.getElementById("capa00t");

        varidtbnt12.style.display = 'inline';
        varidtbnt22.style.display = 'none';
        varidnovedadt.style.display = 'none';
    };

    function opennovedadp(){
        var varidtbnp13 = document.getElementById("idtbnp13");
        var varidtbnp23 = document.getElementById("idtbnp23");
        var varidnovedadp = document.getElementById("capa00p");

        varidtbnp13.style.display = 'none';
        varidtbnp23.style.display = 'inline';
        varidnovedadp.style.display = 'inline';

    };

    function closenovedadp(){
        var varidtbnp13 = document.getElementById("idtbnp13");
        var varidtbnp23 = document.getElementById("idtbnp23");
        var varidnovedadp = document.getElementById("capa00p");

        varidtbnp13.style.display = 'inline';
        varidtbnp23.style.display = 'none';
        varidnovedadp.style.display = 'none';
    };

    function opennovedadinf(){
        var varidtbnp11 = document.getElementById("idtbnp11");
        var varidtbnp22 = document.getElementById("idtbnp22");
        var varidnovedadinf = document.getElementById("capa00tt");

        varidtbnp11.style.display = 'none';
        varidtbnp22.style.display = 'inline';
        varidnovedadinf.style.display = 'inline';

    };

    function closenovedadinf(){
        var varidtbnp11 = document.getElementById("idtbnp11");
        var varidtbnp22 = document.getElementById("idtbnp22");
        var varidnovedadinf = document.getElementById("capa00tt");

        varidtbnp11.style.display = 'inline';
        varidtbnp22.style.display = 'none';
        varidnovedadinf.style.display = 'none';
    };
    

</script>