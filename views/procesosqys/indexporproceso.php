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
use app\models\ProcesosClienteCentrocosto;

  $this->title = 'Procesos Q&S - Búsqueda Por Procesos';
  $this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

  $sessiones = Yii::$app->user->identity->id;

  $rol =  new Query;
  $rol->select(['tbl_roles.role_id'])
      ->from('tbl_roles')
      ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                  'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
      ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                  'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
      ->where(['=','tbl_usuarios.usua_id',$sessiones]);                    
  $command = $rol->createCommand();
  $roles = $command->queryScalar();

  // Colores en Codigo;
  $varMalo = '#D01E53';
  $varBueno = '#00968F';
  $varEstable = '#FFC72C';

  $varSinData = '-';

  // Procesos Manuales
  $varManualPEC = 0;
  $varManualPENC = 0;
  $varManualSFC = 0;
  $varManualProceso = 0;
  $varManualExp = 0;
  $varManualProm = 0;
  $varManualScore = 0;

  $varLiderPEC = 0;
  $varLiderPENC = 0;
  $varLiderSFC = 0;
  $varLiderProceso = 0;
  $varLiderExp = 0;
  $varLiderProm = 0;
  $varLiderScore = 0;

  $varManualPECarray = array();
  $varManualPENCarray = array();
  $varManualSFCarray = array();
  $varManualProcesoarray = array();
  $varManualExparray = array();
  $varManualPromarray = array();
  $varManualScorearray = array();

  $varLiderPECarray = array();
  $varLiderPENCarray = array();
  $varLiderSFCarray = array();
  $varLiderProcesoarray = array();
  $varLiderExparray = array();
  $varLiderPromarray = array();
  $varLiderScorearray = array();

  $varLiderCantidad = 0;

  $varDataFormularios = array();

  $varCantidadFeedbacks = 0;
  $varArrayConteoGestionada = 0;
  $varArrayConteoNoGestionada = 0;
  $varArrayConteoEstadoOk = 0;
  $varArrayConteoEstadoKo = 0;
  $varCantidadSegundo = 0;

  $titulos = array();

$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='popover']").popover(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js); 
?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css">
<style type="text/css">
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

    .card2 {
        height: 80px;
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

    .card3 {
        height: 270px;
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

    .card4 {
        height: 180px;
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

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

  hr{border:0;border-top:1px solid #eee;margin:20px 0}
    .w3-image{max-width:100%;height:auto}img{vertical-align:middle}a{color:inherit}
    .w3-table,.w3-table-all{border-collapse:collapse;border-spacing:0;width:100%;display:table}.w3-table-all{border:1px solid #ccc}
    .w3-bordered tr,.w3-table-all tr{border-bottom:1px solid #ddd}.w3-striped tbody tr:nth-child(even){background-color:#f1f1f1}
    .w3-table-all tr:nth-child(odd){background-color:#fff}.w3-table-all tr:nth-child(even){background-color:#f1f1f1}
    .w3-hoverable tbody tr:hover,.w3-ul.w3-hoverable li:hover{background-color:#ccc}.w3-centered tr th,.w3-centered tr td{text-align:center}
    .w3-table td,.w3-table th,.w3-table-all td,.w3-table-all th{padding:8px 8px;display:table-cell;text-align:left;vertical-align:top}
    .w3-table th:first-child,.w3-table td:first-child,.w3-table-all th:first-child,.w3-table-all td:first-child{padding-left:16px}
    .w3-btn,.w3-button{border:none;display:inline-block;padding:8px 16px;vertical-align:middle;overflow:hidden;text-decoration:none;color:inherit;background-color:inherit;text-align:center;cursor:pointer;white-space:nowrap}
    .w3-btn:hover{box-shadow:0 8px 16px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19)}
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
    @media (min-width:601px){.w3-col.m1{width:8.33333%}.w3-col.m2{width:16.66666%}.w3-col.m3,.w3-quarter{width:24.99999%}.w3-col.m4,.w3-third{width:19.9%}
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
    .w3-code{width:auto;background-color:#fff;padding:8px 12px;border-left:4px solid #4CAF50;word-wrap:break-word}
    .w3-codespan{color:crimson;background-color:#f1f1f1;padding-left:4px;padding-right:4px;font-size:110%}
    .w3-card,.w3-card-2{box-shadow:0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12)}
    .w3-card-4,.w3-hover-shadow:hover{box-shadow:0 4px 10px 0 rgba(0,0,0,0.2),0 4px 20px 0 rgba(0,0,0,0.19)}
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
    .w3-tiny{font-size:10px!important}.w3-small{font-size:12px!important}.w3-medium{font-size:15px!important}.w3-large{font-size:18px!important}
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
    .w3-padding-small{padding:4px 8px!important}.w3-padding{padding:8px 16px!important}.w3-padding-large{padding:12px 24px!important}
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
</style>

<!-- Data extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<script src="../../js_extensions/chart.min.js"></script>

<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>

<!-- Capa Procesos del encabezado -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>

<!-- Capa de Informacion -->
<div id="idCapaInformativa" class="capaInformativas" style="display: inline;">

  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Ficha Técnica') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    
    <div class="col-md-3">
      <div class="card2 mb">
        <label ><em class="fas fa-id-card" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Servicio:') ?></label>        
        <label style="text-align: center;"><?= Yii::t('app', $varNombreServicio) ?></label> 
      </div> 
    </div>

    <div class="col-md-3">
      <div class="card2 mb">
        <label ><em class="fas fa-list-alt" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Programa/Pcrc:') ?></label>  

        <div class="col-md-12 text-center">          
          <div onclick="opennovedad();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbn1" >
            <label style="text-align: center;"><?= Yii::t('app', 'Abrir Listado Programa/Pcrc [ + ]') ?></label>
          </div> 
                              
          <div onclick="closenovedad();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="idtbn2" >
            <label style="text-align: center;"><?= Yii::t('app', 'Cerrar Listado Programa/Pcrc [ - ]') ?></label>
          </div> 
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card2 mb">
        <label ><em class="fas fa-list" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Dimensión:') ?></label>  
        <label style="text-align: center;"><?= Yii::t('app', $varTextoDimensionp) ?></label>       
      </div> 
    </div>

    <div class="col-md-3">
      <div class="card2 mb">
        <label ><em class="fas fa-calendar" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Rango de Fechas:') ?></label>  
        <label style="text-align: center;"><?= Yii::t('app', $varFechainicial.' - '.$varFechaFinal) ?></label>       
      </div> 
    </div>


  </div>

  <div class="capaListaGerentes" id="capaListaGerentes" style="display: none;">
    <br>
    <div class="row">
      <div class="col-md-12">
        <div class="card1 mb">
          <label ><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Lista Gerentes:') ?></label>   
          <?php
            foreach ($varNombreCC as $key => $value) {
          ?>
            <label style="text-align: left;"><?= Yii::t('app', '* '.$value['NamePcrc']) ?></label>
          <?php
            }
          ?>
        </div>
      </div>
    </div>
    <br>
  </div>

</div>

<hr>

<!-- Capa de Estadistica -->
<div id="idCapaEstadistica" class="capaEstadistica" style="display: inline;">

  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resultados por Procesamientos') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-6">
      <div class="card3 mb">
        <label style="font-size: 15px;"><em class="fas fa-chart-bar" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Detalle de Procesmiento Mixto') ?></label>
        <table style="width:100%">
          <caption><?= Yii::t('app', ' .') ?></caption>
          <tr>
            <td scope="col" class="text-center" style="width: 100px;">
              <label style="font-size: 15px;"><?= Yii::t('app', 'Calidad: General Konecta') ?></label>
            </td>
            <td scope="col" class="text-center" style="width: 100px;">
              <label style="font-size: 15px;"><?= Yii::t('app', 'Calidad y Consistencia') ?></label>
            </td>
            <td scope="col" class="text-center" style="width: 100px;">
              <label style="font-size: 15px;"><?= Yii::t('app', 'Procesamiento Automático') ?></label>
            </td>
          </tr>
          <tr>
            <?php
              $varArraySumeAutomaticoP = array();
              $varArraySumaMixtoP = array();


              foreach ($varListasClienteIdealP as $key => $value) {
                $varCentrosCostos = $value['cod_pcrc'];

                $varResponsableAutomaticoIdealP = (new \yii\db\Query())
                                            ->select(['round(AVG(agente),2) AS ProAgente','round(AVG(marca),2) AS ProMarca','round(AVG(canal),2) AS ProCanal'])
                                            ->from(['tbl_ideal_responsabilidad'])            
                                            ->where(['=','anulado',0])
                                            ->andwhere(['=','id_dp_cliente',$varIdDpCliente])
                                            ->andwhere(['=','cod_pcrc',$varCentrosCostos])
                                            ->andwhere(['=','extension',$varIdExtensionc])
                                            ->andwhere(['>=','fechainicio',$varFechainicial.' 05:00:00'])
                                            ->andwhere(['<=','fechafin',$varFechaFinal.' 05:00:00'])
                                            ->all(); 

                $varArrayAutomaticoIdealP = 0;
                foreach ($varResponsableAutomaticoIdealP as $key => $value) {
                  $varArrayAutomaticoIdealP = round(($value['ProAgente'] + $value['ProCanal']) / 2,2);
                }

                array_push($varArraySumeAutomaticoP, $varArrayAutomaticoIdealP);

                $vaListarFormulariosGIdealP = (new \yii\db\Query())
                                              ->select(['formulario_id'])
                                              ->from(['tbl_ideal_novedades'])            
                                              ->where(['=','anulado',0])
                                              ->andwhere(['=','id_dp_cliente',$varIdDpCliente])
                                              ->andwhere(['=','cod_pcrc',$varCentrosCostos])
                                              ->andwhere(['=','extension',$varIdExtensionc])
                                              ->andwhere(['>=','fechainicio',$varFechainicial.' 05:00:00'])
                                              ->andwhere(['<=','fechafin',$varFechaFinal.' 05:00:00'])
                                              ->groupby(['formulario_id'])
                                              ->All();

                $varArrayCallidProcesoIdealP =  array();
                foreach ($vaListarFormulariosGIdealP as $key => $value) {
                  $varFormularioGIdIdealP = $value['formulario_id'];

                  $varScoreGIdealP = (new \yii\db\Query())
                                    ->select(['score'])
                                    ->from(['tbl_ejecucionformularios'])         
                                    ->where(['=','id',$varFormularioGIdIdealP])
                                    ->andwhere(['between','created',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                    ->scalar();

                  if ($varScoreGIdealP) {
                    array_push($varArrayCallidProcesoIdealP, $varScoreGIdealP);
                  }else{
                    array_push($varArrayCallidProcesoIdealP, 0);
                  }                 

                }

                if (count($varArrayCallidProcesoIdealP) != 0) {
                  $varListarMixtasIdealP = round(array_sum($varArrayCallidProcesoIdealP) / count($varArrayCallidProcesoIdealP),2);
                }else{
                  $varListarMixtasIdealP = 0;
                }

                array_push($varArraySumaMixtoP, $varListarMixtasIdealP);
            
              }

              if (count($varListasClienteIdealP) != 0) {
                $varPormedioResponsableP = round((array_sum($varArraySumeAutomaticoP) / count($varListasClienteIdealP)),2);
              }else{
                $varPormedioResponsableP = 0;
              }
              

              if (count($varListasClienteIdealP) != 0) {
                $varListasMixtasP = round((array_sum($varArraySumaMixtoP) / count($varListasClienteIdealP)),2);
              }else{
                $varListasMixtasP = 0;
              }
              


              $varListarGeneralResponsabilidadP = round(($varPormedioResponsableP + $varListasMixtasP) / 2,2);              

              $varRestanteGeneralP = round(100 - $varListarGeneralResponsabilidadP,2);
              if ($varListarGeneralResponsabilidadP < '80') {
                $varTColorGeneralP = $varMalo;
              }else{
                if ($varListarGeneralResponsabilidadP >= '90') {
                  $varTColorGeneralP = $varBueno;
                }else{
                  $varTColorGeneralP = $varEstable;
                }
              }

              $varRestanteMixtoP = round(100 - $varListasMixtasP,2);
              if ($varListasMixtasP < '80') {
                $varTColorMixtoP = $varMalo;
              }else{
                if ($varListasMixtasP >= '90') {
                  $varTColorMixtoP = $varBueno;
                }else{
                  $varTColorMixtoP = $varEstable;
                }
              }

              $varRestanteAutosP = round(100 - $varPormedioResponsableP,2);
              if ($varPormedioResponsableP < '80') {
                $varTColorAutoP = $varMalo;
              }else{
                if ($varListasMixtasP >= '90') {
                  $varTColorAutoP = $varBueno;
                }else{
                  $varTColorAutoP = $varEstable;
                }
              }
            ?>
            <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerGP"></canvas></div><span style="font-size: 15px;"><?php echo $varListarGeneralResponsabilidadP.' %'; ?></span></td> 
            <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerMP"></canvas></div><span style="font-size: 15px;"><?php echo $varListasMixtasP.' %'; ?></span></td> 
            <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerAP"></canvas></div><span style="font-size: 15px;"><?php echo $varPormedioResponsableP.' %'; ?></span></td>
          </tr>
        </table>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card3 mb">
        <label style="font-size: 15px;"><em class="fas fa-chart-bar" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Detalle Procesamiento Automatico') ?></label>
        <div id="chartContainerResposnabilidadesP" class="highcharts-container" style="height: 200px;"></div>
      </div>
    </div>
  </div>

</div>

<hr>

<!-- Capa de Acciones -->
<div id="idCapaAcciones" class="capaAcciones" style="display: inline;">

  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resultado del Proceso') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">

        <div class="w3-container">
            
            <div class="w3-row">

              <a href="javascript:void(0)" onclick="openCity(event, 'Mixto');">
                <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                  <label><em class="fas fa-list-alt" style="font-size: 20px; color: #827DF9;"></em><strong>  <?= Yii::t('app', 'Proceso Mixto') ?></strong></label>
                </div>
              </a>
              <a href="javascript:void(0)" onclick="openCity(event, 'Automatico');">
                <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                  <label><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em><strong>  <?= Yii::t('app', 'Proceso Automático') ?></strong></label>
                </div>
              </a>
              <a href="javascript:void(0)" onclick="openCity(event, 'Manual');">
                <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                  <label><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><strong>  <?= Yii::t('app', 'Calidad y Consistencia') ?></strong></label>
                </div>
              </a>
              <a href="javascript:void(0)" onclick="openCity(event, 'Calidad');">
                <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                  <label><em class="fas fa-list-alt" style="font-size: 20px; color: #C6C6C6;"></em><strong>  <?= Yii::t('app', 'Comportamiento Calidad') ?></strong></label>
                </div>
              </a>
              <a href="javascript:void(0)" onclick="openCity(event, 'Mejora');">
                <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                  <label><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em><strong>  <?= Yii::t('app', 'Gestión de la Mejora') ?></strong></label>
                </div>
              </a>

            </div>

            <!-- Proceso Mixto -->
            <div id="Mixto" class="w3-container city" style="display:inline;">

              <?php
              foreach ($varListasClienteIdealP as $key => $value) {
                $varCentrosCostosMixtos = $value['cod_pcrc'];

                $varNombreCentroCostos = (new \yii\db\Query())
                                        ->select(['concat(cod_pcrc," - ",pcrc) as NamePcrc'])
                                        ->from(['tbl_speech_categorias'])            
                                        ->where(['=','anulado',0])
                                        ->andwhere(['in','cod_pcrc',$varCentrosCostosMixtos])
                                        ->groupby(['cod_pcrc'])
                                        ->Scalar(); 

                $varCantidadLlamadasIdealP = (new \yii\db\Query())
                                ->select(['sum(cantidad)'])
                                ->from(['tbl_ideal_llamadas'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','id_dp_cliente',$varIdDpCliente])
                                ->andwhere(['=','cod_pcrc',$varCentrosCostosMixtos])
                                ->andwhere(['=','tipoextension',$varIdExtensionc])
                                ->andwhere(['>=','fechainicio',$varFechainicial.' 05:00:00'])
                                ->andwhere(['<=','fechafin',$varFechaFinal.' 05:00:00'])
                                ->Scalar(); 

                $varResponsableAutomaticoP = (new \yii\db\Query())
                                            ->select(['round(AVG(agente),2) AS ProAgente','round(AVG(marca),2) AS ProMarca','round(AVG(canal),2) AS ProCanal'])
                                            ->from(['tbl_ideal_responsabilidad'])            
                                            ->where(['=','anulado',0])
                                            ->andwhere(['=','id_dp_cliente',$varIdDpCliente])
                                            ->andwhere(['=','cod_pcrc',$varCentrosCostosMixtos])
                                            ->andwhere(['=','extension',$varIdExtensionc])
                                            ->andwhere(['>=','fechainicio',$varFechainicial.' 05:00:00'])
                                            ->andwhere(['<=','fechafin',$varFechaFinal.' 05:00:00'])
                                            ->all(); 

                $varArrayAutomaticoP = 0;
                $varAgentePersonaP = null;
                $varMarcaPersonaP = null;
                $varCanalPersonaP = null;
                foreach ($varResponsableAutomaticoP as $key => $value) {
                  $varArrayAutomaticoP = round(($value['ProAgente'] + $value['ProCanal']) / 2,2);
                  $varAgentePersonaP = $value['ProAgente'];      
                  $varMarcaPersonaP = $value['ProMarca'];      
                  $varCanalPersonaP = $value['ProCanal'];      
                }

                if ($varArrayAutomaticoP < '80') {
                  $varTColorAutoIdealP = $varMalo;
                }else{
                  if ($varArrayAutomaticoP >= '90') {
                    $varTColorAutoIdealP = $varBueno;
                  }else{
                    $varTColorAutoIdealP = $varEstable;
                  }
                }

                if ($varAgentePersonaP < '80') {
                  $varTColorAgenteIdealP = $varMalo;
                }else{
                  if ($varAgentePersonaP >= '90') {
                    $varTColorAgenteIdealP = $varBueno;
                  }else{
                    $varTColorAgenteIdealP = $varEstable;
                  }
                }
                if ($varMarcaPersonaP < '80') {
                  $varTColorMarcaIdealP = $varMalo;
                }else{
                  if ($varMarcaPersonaP >= '90') {
                    $varTColorMarcaIdealP = $varBueno;
                  }else{
                    $varTColorMarcaIdealP = $varEstable;
                  }
                }
                if ($varCanalPersonaP < '80') {
                  $varTColorCanalIdealP = $varMalo;
                }else{
                  if ($varCanalPersonaP >= '90') {
                    $varTColorCanalIdealP = $varBueno;
                  }else{
                    $varTColorCanalIdealP = $varEstable;
                  }
                }

                $vaListarFormulariosIdealP =  (new \yii\db\Query())
                                              ->select(['formulario_id'])
                                              ->from(['tbl_ideal_novedades'])            
                                              ->where(['=','tbl_ideal_novedades.id_dp_cliente',$varIdDpCliente])
                                              ->andwhere(['=','tbl_ideal_novedades.cod_pcrc',$varCentrosCostosMixtos])
                                              ->andwhere(['>=','tbl_ideal_novedades.fechainicio',$varFechainicial.' 05:00:00'])
                                              ->andwhere(['<=','tbl_ideal_novedades.fechafin',$varFechaFinal.' 05:00:00'])
                                              ->andwhere(['=','tbl_ideal_novedades.anulado',0])
                                              ->andwhere(['=','tbl_ideal_novedades.extension',$varIdExtensionc])
                                              ->groupby(['formulario_id'])
                                              ->All();


                $varArrayValoracionesP = array();
                $varArrayCallidsIdealP = array();
                $varArrayFeedbacksP = array();
                $varArrayPECP = array();
                $varArrayPENCP = array();
                $varArraySFCP = array();
                $varArrayProcesoP = array();
                $varArrayExperienciaP = array();
                $varArrayPromesaP = array();
                foreach ($vaListarFormulariosIdealP as $key => $value) {
                  $varFormularioIdIdealP = $value['formulario_id'];
                  array_push($varArrayValoracionesP, 1);

                  $varScoreIdealP = (new \yii\db\Query())
                                  ->select(['score'])
                                  ->from(['tbl_ejecucionformularios'])         
                                  ->where(['=','id',$varFormularioIdIdealP])
                                  ->scalar();
                  array_push($varArrayCallidsIdealP, $varScoreIdealP);

                  $varCantidadFeedbacksIdealP = (new \yii\db\Query())
                                                ->select(['id'])
                                                ->from(['tbl_ejecucionfeedbacks'])         
                                                ->where(['=','ejecucionformulario_id',$varFormularioIdIdealP])
                                                ->count();
                  array_push($varArrayFeedbacksP, $varCantidadFeedbacksIdealP);

                  $varPecIdealP = (new \yii\db\Query())
                                  ->select(['i1_nmcalculo'])
                                  ->from(['tbl_ejecucionformularios'])         
                                  ->where(['=','id',$varFormularioIdIdealP])
                                  ->scalar();
                  array_push($varArrayPECP, $varPecIdealP);

                  $varPencIdealP = (new \yii\db\Query())
                                  ->select(['i2_nmcalculo'])
                                  ->from(['tbl_ejecucionformularios'])         
                                  ->where(['=','id',$varFormularioIdIdealP])
                                  ->scalar();
                  array_push($varArrayPENCP, $varPencIdealP);

                  $varSfcIdealP = (new \yii\db\Query())
                                  ->select(['i3_nmcalculo'])
                                  ->from(['tbl_ejecucionformularios'])         
                                  ->where(['=','id',$varFormularioIdIdealP])
                                  ->scalar();
                  array_push($varArraySFCP, $varSfcIdealP);

                  $varProcesoIdealP = (new \yii\db\Query())
                                  ->select(['i5_nmcalculo'])
                                  ->from(['tbl_ejecucionformularios'])         
                                  ->where(['=','id',$varFormularioIdIdealP])
                                  ->scalar();
                  array_push($varArrayProcesoP, $varProcesoIdealP);

                  $varExpIdealP = (new \yii\db\Query())
                                  ->select(['i6_nmcalculo'])
                                  ->from(['tbl_ejecucionformularios'])         
                                  ->where(['=','id',$varFormularioIdIdealP])
                                  ->scalar();
                  array_push($varArrayExperienciaP, $varExpIdealP);

                  $varPromIdealP = (new \yii\db\Query())
                                  ->select(['i7_nmcalculo'])
                                  ->from(['tbl_ejecucionformularios'])         
                                  ->where(['=','id',$varFormularioIdIdealP])
                                  ->scalar();
                  array_push($varArrayPromesaP, $varPromIdealP);
                }


                $varTotalValoracionesP = array_sum($varArrayValoracionesP);
                $varTotalFeedbacksP = array_sum($varArrayFeedbacksP);

                  if (array_sum($varArrayPECP) != 0 && $varTotalValoracionesP != 0) {
                    $varTotalPecP = round((array_sum($varArrayPECP) / $varTotalValoracionesP),2);
                  }else{
                    $varTotalPecP = 0;
                  }
                  if (array_sum($varArrayPENCP) != 0 && $varTotalValoracionesP != 0) {
                    $varTotalPencP = round((array_sum($varArrayPENCP) / $varTotalValoracionesP),2);
                  }else{
                    $varTotalPencP = 0;
                  }
                  if (array_sum($varArraySFCP) != 0 && $varTotalValoracionesP != 0) {
                    $varTotalSfcP = round((array_sum($varArraySFCP) / $varTotalValoracionesP),2);
                  }else{
                    $varTotalSfcP = 0;
                  }
                  if (array_sum($varArrayProcesoP) != 0 && $varTotalValoracionesP != 0) {
                    $varTotalProcesoP = round((array_sum($varArrayProcesoP) / $varTotalValoracionesP),2);
                  }else{
                    $varTotalProcesoP = 0;
                  }
                  if (array_sum($varArrayExperienciaP) != 0 && $varTotalValoracionesP != 0) {
                    $varTotalExperienciaP = round((array_sum($varArrayExperienciaP) / $varTotalValoracionesP),2);
                  }else{
                    $varTotalExperienciaP = 0;
                  }
                  if (array_sum($varArrayPromesaP) != 0 && $varTotalValoracionesP != 0) {
                    $varTotalPromesaP = round((array_sum($varArrayPromesaP) / $varTotalValoracionesP),2);
                  }else{
                    $varTotalPromesaP = 0;
                  }

                  if ($varTotalFeedbacksP < '80') {
                    $varTColorFeebackP = $varMalo;
                  }else{
                    if ($varTotalFeedbacksP >= '90') {
                      $varTColorFeebackP = $varBueno;
                    }else{
                      $varTColorFeebackP = $varEstable;
                    }
                  }

                  if (count($varArrayCallidsIdealP) != 0) {
                    $varListarMixtasIdealP = round(array_sum($varArrayCallidsIdealP) / count($varArrayCallidsIdealP),2);
                  }else{
                    $varListarMixtasIdealP = 0;
                  }

                  if ($varListarMixtasIdealP < '80') {
                    $varTColorMixtaAIdealP = $varMalo;
                  }else{
                    if ($varListarMixtasIdealP >= '90') {
                      $varTColorMixtaAIdealP = $varBueno;
                    }else{
                      $varTColorMixtaAIdealP = $varEstable;
                    }
                  }

                  $varGeneralConsistenciaIdealP = round((($varArrayAutomaticoP + $varListarMixtasIdealP) / 2),2);

                  if ($varGeneralConsistenciaIdealP < '80') {
                    $varTColorGeneralIdealP = $varMalo;
                  }else{
                    if ($varGeneralConsistenciaIdealP >= '90') {
                      $varTColorGeneralIdealP = $varBueno;
                    }else{
                      $varTColorGeneralIdealP = $varEstable;
                    }
                  }

              ?>
                <br>
                <div class="row">
                  <div class="col-md-12">
                    
                    <div class="card1 mb">
                      <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', $varNombreCentroCostos) ?></label>

                      <div class="row">
                        <div class="col-md-4">
                          <div class="card4 mb">
                            <table id="myTableInformacion" class="table table-hover table-bordered" style="margin-top:10px" >
                              <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Resultados Procesamientos') ?></label></caption>
                              <thead>
                                <tr>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calidad - General Konecta') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calidad & Consistencia') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Procesamiento Automático') ?></label></th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorGeneralIdealP ?>"><?php echo  $varGeneralConsistenciaIdealP.' %'; ?></label></td>
                                  <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorMixtaAIdealP ?>"><?php echo  $varListarMixtasIdealP.' %'; ?></label></td>
                                  <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorAutoIdealP ?>"><?php echo  $varArrayAutomaticoP.' %'; ?></label></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>

                        <div class="col-md-4">
                          <div class="card4 mb">
                            <table id="myTableAuto" class="table table-hover table-bordered" style="margin-top:10px" >
                              <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Detalle Procesamiento Automático') ?></label></caption>
                              <thead>
                                <tr>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Interacciones') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Agente') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Marca') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Canal') ?></label></th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td class="text-center"><label style="font-size: 12px;"><?php echo  $varCantidadLlamadasIdealP; ?></label></td>
                                  <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorAgenteIdealP; ?>"><?php echo  $varAgentePersonaP.' %'; ?></label></td>
                                  <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorMarcaIdealP; ?>"><?php echo  $varMarcaPersonaP.' %'; ?></label></td>
                                  <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorCanalIdealP; ?>"><?php echo  $varCanalPersonaP.' %'; ?></label></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>

                        <div class="col-md-4">
                          <div class="card4 mb">
                            <table id="myTableGestion" class="table table-hover table-bordered" style="margin-top:10px" >
                              <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Detalle Gestión de la Mejora') ?></label></caption>
                              <thead>
                                <tr>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Feedbacks') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Alertas') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calibraciones') ?></label></th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorFeebackP; ?>"><?php echo  $varTotalFeedbacksP; ?></label></td>
                                  <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorFeebackP; ?>"><?php echo  '0'; ?></label></td>
                                  <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorFeebackP; ?>"><?php echo  '0'; ?></label></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>

                      <br>

                      <div class="row">
                        <div class="col-md-12">
                          <div class="card4 mb">
                            <table id="myTableCalidad" class="table table-hover table-bordered" style="margin-top:10px" >
                              <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Detalle Calidad y Consistencia - Manual') ?></label></caption>
                              <thead>
                                <tr>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Valoraciones') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PEC') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PENC') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'SPC/SFR') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Proceso') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Experiencia') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Promesa Marca') ?></label></th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalValoracionesP; ?></label></td>
                                  <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalPecP; ?></label></td>
                                  <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalPencP; ?></label></td>
                                  <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalSfcP; ?></label></td>
                                  <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalProcesoP; ?></label></td>
                                  <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalExperienciaP; ?></label></td>
                                  <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalPromesaP; ?></label></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>                          
                        </div>
                      </div>

                      <br>

                      <div class="row">
                        <div class="col-md-12">
                          <div class="card4 mb">
                          <table id="myTableEquipos" class="table table-hover table-bordered" style="margin-top:10px" >
                              <caption><label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Listado de Equipos') ?></label></caption>
                              <thead>
                                <tr>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Lider') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Asesor') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Interacciones') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Valoraciones') ?></label></th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php                                  

                                  foreach ($varListarEquipos as $key => $value) {
                                    $varValorSpeech = $value['varAsesorSpeech'];

                                    $varListaIdFormularios = (new \yii\db\Query())
                                                  ->select(['tbl_speech_mixta.formulario_id'])

                                                  ->from(['tbl_speech_mixta']) 

                                                  ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                                                    'tbl_speech_mixta.callid = tbl_dashboardspeechcalls.callId')    

                                                  ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                                                  ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varNombreSpeech])
                                                  ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                                                  ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesM])
                                                  ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varLlamada])
                                                  ->andwhere(['=','tbl_dashboardspeechcalls.login_id',$varValorSpeech])
                                                  ->groupby(['tbl_speech_mixta.formulario_id'])
                                                  ->All();
                                ?>
                                  <tr>
                                    <td class="text-center"><label style="font-size: 12px;"><?php echo  $value['varLider']; ?></label></td>
                                    <td class="text-center"><label style="font-size: 12px;"><?php echo  $varValorSpeech; ?></label></td>
                                    <td class="text-center"><label style="font-size: 12px;"><?php echo  $value['varCantidad']; ?></label></td>
                                    <td class="text-center"><label style="font-size: 12px;"><?php echo  count($varListaIdFormularios); ?></label></td>
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

              <?php
            }
              ?>

            </div>

            <!-- Proceso Auto -->
            <div id="Automatico" class="w3-container city" style="display:none;">

              <?php
              $varNumeroContainerA = 0;
              foreach ($varListasClienteIdealP as $key => $value) {
                $varCentrosCostosAutos = $value['cod_pcrc'];

                $varNombreCentroCostosA = (new \yii\db\Query())
                                              ->select(['concat(cod_pcrc," - ",pcrc) as NamePcrc'])
                                              ->from(['tbl_speech_categorias'])            
                                              ->where(['=','anulado',0])
                                              ->andwhere(['in','cod_pcrc',$varCentrosCostosAutos])
                                              ->groupby(['cod_pcrc'])
                                              ->Scalar(); 


                $varListaResponsabilidadesAuto = (new \yii\db\Query())
                                              ->select(['tbl_ideal_indicadores.indicador', 'tbl_ideal_indicadores.cantidad_indicador', 'tbl_ideal_responsabilidad.agente', 'tbl_ideal_responsabilidad.marca', 'tbl_ideal_responsabilidad.canal'])
                                              ->from(['tbl_ideal_responsabilidad'])  
                                              ->join('LEFT OUTER JOIN', 'tbl_ideal_indicadores',
                                                'tbl_ideal_responsabilidad.cod_pcrc = tbl_ideal_indicadores.cod_pcrc AND tbl_ideal_responsabilidad.id_categoriai = tbl_ideal_indicadores.id_categoriai AND tbl_ideal_responsabilidad.anulado = tbl_ideal_indicadores.anulado AND tbl_ideal_responsabilidad.fechainicio = tbl_ideal_indicadores.fechainicio AND tbl_ideal_responsabilidad.fechafin = tbl_ideal_indicadores.fechafin')            
                                              ->where(['=','tbl_ideal_indicadores.id_dp_cliente',$varIdDpCliente])
                                              ->andwhere(['=','tbl_ideal_indicadores.cod_pcrc',$varCentrosCostosAutos])
                                              ->andwhere(['>=','tbl_ideal_indicadores.fechainicio',$varFechainicial.' 05:00:00'])
                                              ->andwhere(['<=','tbl_ideal_indicadores.fechafin',$varFechaFinal.' 05:00:00'])
                                              ->andwhere(['=','tbl_ideal_indicadores.anulado',0])
                                              ->andwhere(['=','tbl_ideal_indicadores.extension',$varIdExtensionc])
                                              ->groupby(['tbl_ideal_indicadores.id_indicadores'])
                                              ->All();

                $varListaIndicadoresAuto = (new \yii\db\Query())
                                              ->select(['tbl_ideal_indicadores.id_categoriai','tbl_ideal_indicadores.indicador', 'tbl_ideal_indicadores.cantidad_indicador'])
                                              ->from(['tbl_ideal_responsabilidad'])  
                                              ->join('LEFT OUTER JOIN', 'tbl_ideal_indicadores',
                                                'tbl_ideal_responsabilidad.cod_pcrc = tbl_ideal_indicadores.cod_pcrc AND tbl_ideal_responsabilidad.id_categoriai = tbl_ideal_indicadores.id_categoriai AND tbl_ideal_responsabilidad.anulado = tbl_ideal_indicadores.anulado AND tbl_ideal_responsabilidad.fechainicio = tbl_ideal_indicadores.fechainicio AND tbl_ideal_responsabilidad.fechafin = tbl_ideal_indicadores.fechafin')            
                                              ->where(['=','tbl_ideal_indicadores.id_dp_cliente',$varIdDpCliente])
                                              ->andwhere(['=','tbl_ideal_indicadores.cod_pcrc',$varCentrosCostosAutos])
                                              ->andwhere(['>=','tbl_ideal_indicadores.fechainicio',$varFechainicial.' 05:00:00'])
                                              ->andwhere(['<=','tbl_ideal_indicadores.fechafin',$varFechaFinal.' 05:00:00'])
                                              ->andwhere(['=','tbl_ideal_indicadores.anulado',0])
                                              ->andwhere(['=','tbl_ideal_indicadores.extension',$varIdExtensionc])
                                              ->groupby(['tbl_ideal_indicadores.id_indicadores'])
                                              ->All();

                
                $varNumeroContainerA += 1;
              ?>

                <br>

                <div class="row">
                  <div class="col-md-12">

                    <div class="card1 mb">
                      <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', $varNombreCentroCostosA) ?></label>

                      <div class="row">
                        <div class="col-md-6">
                          <div class="card1 mb">                            

                            <table id="myTableResponAuto" class="table table-hover table-bordered" style="margin-top:10px" >
                                <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Resultados Responsabilidades') ?></label></caption>
                                <thead>
                                  <tr>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indicador') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Total') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Agente') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Marca') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Canal') ?></label></th>
                                  </tr>
                                </thead>
                                <tbody>
                              <?php
                              foreach ($varListaResponsabilidadesAuto as $key => $value) {
                                $varNombreIndicador = $value['indicador'];
                                $varTotalesIndicador = $value['cantidad_indicador'];
                                $varAgenteIndicador = $value['agente'];
                                $varMarcaIndicador = $value['marca'];
                                $varCanalIndicador = $value['canal'];

                                if ($varAgenteIndicador < '80') {
                                  $varTColorAgenteIndicador = $varMalo;
                                }else{
                                  if ($varAgenteIndicador >= '90') {
                                    $varTColorAgenteIndicador = $varBueno;
                                  }else{
                                    $varTColorAgenteIndicador = $varEstable;
                                  }
                                }

                                if ($varMarcaIndicador < '80') {
                                  $varTColorMarcaIndicador = $varMalo;
                                }else{
                                  if ($varMarcaIndicador >= '90') {
                                    $varTColorMarcaIndicador = $varBueno;
                                  }else{
                                    $varTColorMarcaIndicador = $varEstable;
                                  }
                                }

                                if ($varCanalIndicador < '80') {
                                  $varTColorCanalIndicador = $varMalo;
                                }else{
                                  if ($varCanalIndicador >= '90') {
                                    $varTColorCanalIndicador = $varBueno;
                                  }else{
                                    $varTColorCanalIndicador = $varEstable;
                                  }
                                }

                                
                              ?>
                              
                                  <tr>
                                    <td class="text-center"><label style="font-size: 12px;"><?php echo  $varNombreIndicador; ?></label></td>
                                    <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalesIndicador.' %'; ?></label></td>
                                    <td class="text-center"><label style="font-size: 12px;  color: <?php echo $varTColorAgenteIndicador ?>"><?php echo  $varAgenteIndicador.' %'; ?></label></td>
                                    <td class="text-center"><label style="font-size: 12px;  color: <?php echo $varTColorMarcaIndicador ?>"><?php echo  $varMarcaIndicador.' %'; ?></label></td>
                                    <td class="text-center"><label style="font-size: 12px;  color: <?php echo $varTColorCanalIndicador ?>"><?php echo  $varCanalIndicador.' %'; ?></label></td>                                    
                                
                            <?php
                            }
                            ?>
                              </tbody>
                            </table>

                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="card1 mb">
                            
                            <table id="myTableResponsIndicador" class="table table-hover table-bordered" style="margin-top:10px" >
                              <caption><label style="font-size: 15px;"><em class="fas fa-chart-bar" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Resultados Indicadores') ?></label></caption>
                              <thead>
                                <tr>
                                <?php
                                  $titulos = array();
                                  $porcentajes = array();
                                  foreach ($varListaResponsabilidadesAuto as $key => $value) {
                                    array_push($porcentajes, $value['indicador']);
                                    array_push($titulos, $value['cantidad_indicador']);
                                ?>
                                  
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', $value['indicador']) ?></label></th>
                                  
                                <?php
                                  }
                                ?>
                                </tr>                                
                              </thead>
                              <tbody>
                                <tr>
                                  <?php
                                    $varNumeroContainer = 0;

                                    foreach ($varListaResponsabilidadesAuto as $key => $value) {
                                      $varIndicador = $value['indicador'];
                                      $varTotalesIndicador = $value['cantidad_indicador'];

                                      $varResultados = round((100 - $varTotalesIndicador),2);
                                      $arrayNumeros = [$varTotalesIndicador, $varResultados];
                                      
                                      $varNumeroContainer += 1;
                                      $prueba = 'doughnut-chart'.$varCentrosCostosAutos.'_'.$varNumeroContainer;
                                  ?>
                                    <td class="text-center" ><div style="width: 80px; height: 80px;  display:block; margin:auto;"><canvas id="<?php echo $prueba; ?>"></canvas></div><label style="font-size: 12px;"><?php echo round($varTotalesIndicador,2).' %'; ?></label></td>

                                    <script type="text/javascript">
                                      
                                      var yValues = [<?= implode(",", $arrayNumeros); ?>];
                                      var barColors = [
                                        "#559FFF",
                                        "#C6C6C6"
                                      ];

                                      new Chart("<?php echo $prueba; ?>", {
                                        type: "doughnut",
                                        data: {
                                          
                                          datasets: [{
                                            backgroundColor: barColors,
                                            data: yValues
                                          }]
                                        },
                                        options: {
                                          title: {
                                            display: false,
                                            text: "_"
                                          }
                                        }
                                      });
                                    </script>
                                  <?php
                                    }
                                  ?>
                                </tr>
                              </tbody>
                            </table>

                          </div>
                        </div>
                      </div>

                      <br>

                      <div class="row">
                        <div class="col-md-12">
                          <div class="card1 mb">

                            <?php
                            foreach ($varListaIndicadoresAuto as $key => $value) {
                              $varIdIndicadorAuto = $value['id_categoriai'];
                              $varTextoIndicadorAuto = $value['indicador'];

                              $varListaVariableAuto = (new \yii\db\Query())
                                              ->select(['if(tbl_speech_categorias.responsable =1,"Agente",if(tbl_speech_categorias.responsable=2,"Marca",if(tbl_speech_categorias.responsable=3,"Canal","NA"))) AS responsabilidadvar','tbl_ideal_variables.variable', 'tbl_ideal_variables.cantidad_variable', 'tbl_ideal_variables.porcentaje_variable'])
                                              ->from(['tbl_speech_categorias'])  
                                              ->join('LEFT OUTER JOIN', 'tbl_ideal_variables',
                                                'tbl_speech_categorias.cod_pcrc = tbl_ideal_variables.cod_pcrc AND tbl_speech_categorias.idcategoria = tbl_ideal_variables.id_categoria_variable')            
                                              ->where(['=','tbl_ideal_variables.id_dp_cliente',$varIdDpCliente])
                                              ->andwhere(['=','tbl_ideal_variables.cod_pcrc',$varCentrosCostosAutos])
                                              ->andwhere(['>=','tbl_ideal_variables.fechainicio',$varFechainicial.' 05:00:00'])
                                              ->andwhere(['<=','tbl_ideal_variables.fechafin',$varFechaFinal.' 05:00:00'])
                                              ->andwhere(['=','tbl_ideal_variables.id_categoria_indicador',$varIdIndicadorAuto])
                                              ->andwhere(['=','tbl_ideal_variables.anulado',0])
                                              ->andwhere(['=','tbl_ideal_variables.extension',$varIdExtensionc])
                                              ->All();


                            ?>  
                              <table id="myTableIndicaAuto" class="table table-hover table-bordered" style="margin-top:10px" >
                                <caption><label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', $varTextoIndicadorAuto) ?></label></caption>
                                <thead>
                                  <tr>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Responsable') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Variable') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Interacciones') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '% de Participación') ?></label></th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                  foreach ($varListaVariableAuto as $key => $value) {
                                    $varResponsabilidadAutos = $value['responsabilidadvar'];
                                    $varNombreVariable = $value['variable'];
                                    $varCatindadVariable = $value['cantidad_variable'];
                                    $varPorcientoVariable = $value['porcentaje_variable'];

                                  ?>
                                    <tr>
                                      <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varResponsabilidadAutos; ?></label></td>
                                      <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varNombreVariable; ?></label></td>
                                      <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varCatindadVariable; ?></label></td>
                                      <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varPorcientoVariable.' %'; ?></label></td>
                                    </tr>
                                  <?php
                                  }
                                  ?>
                                </tbody>
                              </table>
                            <?php
                            }
                            ?>
                            
                          </div>
                        </div>
                      </div>

                      <!-- Aqui va lo del tema equipo lider y asesor auto -->

                    </div>                   
                    
                  </div>
                </div>

              <?php
              }
              ?>

            </div>

            <!-- Proceso Manual -->
            <div id="Manual" class="w3-container city" style="display:none;">

              <?php

              foreach ($varListasClienteIdealP as $key => $value) {
                $varCentrosCostosManual = $value['cod_pcrc'];
                
                $varNombreCentroCostosM = (new \yii\db\Query())
                                              ->select(['concat(cod_pcrc," - ",pcrc) as NamePcrc'])
                                              ->from(['tbl_speech_categorias'])            
                                              ->where(['=','anulado',0])
                                              ->andwhere(['in','cod_pcrc',$varCentrosCostosManual])
                                              ->groupby(['cod_pcrc'])
                                              ->Scalar();

                $varNombreServicioSpeech = (new \yii\db\Query())
                                              ->select(['programacategoria'])
                                              ->from(['tbl_speech_categorias'])            
                                              ->where(['=','anulado',0])
                                              ->andwhere(['in','cod_pcrc',$varCentrosCostosManual])
                                              ->groupby(['programacategoria'])
                                              ->Scalar();


                $varListaIdFormularios = (new \yii\db\Query())
                                              ->select(['formulario_id'])
                                              ->from(['tbl_ideal_novedades'])            
                                              ->where(['=','tbl_ideal_novedades.id_dp_cliente',$varIdDpCliente])
                                              ->andwhere(['=','tbl_ideal_novedades.cod_pcrc',$varCentrosCostosManual])
                                              ->andwhere(['>=','tbl_ideal_novedades.fechainicio',$varFechainicial.' 05:00:00'])
                                              ->andwhere(['<=','tbl_ideal_novedades.fechafin',$varFechaFinal.' 05:00:00'])
                                              ->andwhere(['=','tbl_ideal_novedades.anulado',0])
                                              ->andwhere(['=','tbl_ideal_novedades.extension',$varIdExtensionc])
                                              ->groupby(['formulario_id'])
                                              ->All();

                // $varListaIdFormularios = (new \yii\db\Query())
                //                                   ->select(['tbl_speech_mixta.formulario_id'])

                //                                   ->from(['tbl_speech_mixta']) 

                //                                   ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                //                                     'tbl_speech_mixta.callid = tbl_dashboardspeechcalls.callId')    

                //                                   ->where(['=','tbl_dashboardspeechcalls.nulado',0])
                //                                   ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varNombreServicioSpeech])
                //                                   ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                //                                   ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varIdExtensionc])
                //                                   ->groupby(['tbl_speech_mixta.formulario_id'])
                //                                   ->All();



                if (count($varListaIdFormularios) != 0) {
                  $arralistforms = array();
                  foreach ($varListaIdFormularios as $key => $value) {
                    array_push($arralistforms, intval($value['formulario_id']));
                  }
                  $listaformsarray = implode(", ", $arralistforms);                  

                  $arrayIdFormularios= intval($listaformsarray);
                  $varDataFormularios = explode(",", str_replace(array("#", "'", ";", " "), '', $listaformsarray));

                  $varListaGeneralFormularios = (new \yii\db\Query())
                                                  ->select(['tbl_usuarios.usua_nombre', 'COUNT(tbl_ejecucionformularios.id) AS cantidad', 
                                                    'ROUND(AVG(tbl_ejecucionformularios.score),2) AS score',
                                                    'ROUND(AVG(tbl_ejecucionformularios.i1_nmcalculo),2) AS pec',
                                                    'ROUND(AVG(tbl_ejecucionformularios.i2_nmcalculo),2) AS penc',
                                                    'ROUND(AVG(tbl_ejecucionformularios.i3_nmcalculo),2) AS sfc_frc',
                                                    'ROUND(AVG(tbl_ejecucionformularios.i5_nmcalculo),2) AS indiceproceso',
                                                    'ROUND(AVG(tbl_ejecucionformularios.i6_nmcalculo),2) AS indiceexperiencia',
                                                    'ROUND(AVG(tbl_ejecucionformularios.i6_nmcalculo),2) AS indicepromesa'])

                                                  ->from(['tbl_usuarios']) 

                                                  ->join('LEFT OUTER JOIN', 'tbl_ejecucionformularios',
                                                    'tbl_usuarios.usua_id = tbl_ejecucionformularios.usua_id_lider')    

                                                  ->where(['IN','tbl_ejecucionformularios.id',$varDataFormularios])
                                                  ->groupby(['tbl_usuarios.usua_id'])
                                                  ->All();

                  $varScoreConteo = 0;
                  $varPecConteo = 0;
                  $varPencConteo = 0;
                  $varSfcConteo = 0;
                  $varIndiceProcesoConteo = 0;
                  $varExperienciaConteo = 0;
                  $varPromesaConteo = 0;
                  foreach ($varListaGeneralFormularios as $key => $value) {
                    if ($value['score'] != '') {
                      array_push($varManualScorearray, $value['score']);
                      $varScoreConteo += 1;
                    }

                    if ($value['pec'] != '') {
                      array_push($varManualPECarray, $value['pec']);
                      $varPecConteo += 1;
                    }
                    
                    if ($value['penc'] != '') {
                      array_push($varManualPENCarray, $value['penc']);
                      $varPencConteo += 1;
                    }
                    
                    if ($value['sfc_frc'] != '') {
                      array_push($varManualSFCarray, $value['sfc_frc']);
                      $varSfcConteo += 1;
                    }
                    
                    if ($value['indiceproceso'] != '') {
                      array_push($varManualProcesoarray, $value['indiceproceso']);
                      $varIndiceProcesoConteo += 1;
                    }
                    
                    if ($value['indiceexperiencia'] != '') {
                      array_push($varManualExparray, $value['indiceexperiencia']);
                      $varExperienciaConteo += 1;
                    }
                    
                    if ($value['indicepromesa'] != '') {
                      array_push($varManualPromarray, $value['indicepromesa']);
                      $varPromesaConteo += 1;
                    }
                    
                  }

                  if ($varPecConteo != 0) {
                    $varManualPEC = round((array_sum($varManualPECarray) / $varPecConteo),2);
                  }else{
                    $varManualPEC = 0;
                  }

                  if ($varPencConteo != 0) {
                    $varManualPENC = round((array_sum($varManualPENCarray) / $varPencConteo),2);
                  }else{
                    $varManualPENC = 0;
                  }
                  
                  if ($varSfcConteo != 0) {
                    $varManualSFC = round((array_sum($varManualSFCarray) / $varSfcConteo),2);
                  }else{
                    $varManualSFC = 0;
                  }
                  
                  if ($varIndiceProcesoConteo != 0) {
                    $varManualProceso = round((array_sum($varManualProcesoarray) / $varIndiceProcesoConteo),2);
                  }else{
                    $varManualProceso = 0;
                  }
                  
                  if ($varExperienciaConteo != 0) {
                    $varManualExp = round((array_sum($varManualExparray) / $varExperienciaConteo),2);
                  }else{
                    $varManualExp = 0;
                  }
                  
                  if ($varPromesaConteo != 0) {
                    $varManualProm = round((array_sum($varManualPromarray) / $varPromesaConteo),2);
                  }else{
                    $varManualProm = 0;
                  }
                  
                  $varManualScore = round((array_sum($varManualScorearray) / $varScoreConteo),2);


                }else{
                  $varManualPEC = 0;
                  $varManualPENC = 0;
                  $varManualSFC = 0;
                  $varManualProceso = 0;
                  $varManualExp = 0;
                  $varManualProm = 0;
                  $varManualScore = 0;
                }

                
              ?>
                <br>

                <div class="row">
                  <div class="col-md-12">

                    <div class="card1 mb">
                      <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em> <?= Yii::t('app', $varNombreCentroCostosM) ?></label>

                      <div class="row">
                        <div class="col-md-12">
                          <div >
                            <?php if (count($varListaIdFormularios) != 0) {  ?>
                              <div id="varContainerGeneralManual" class="highcharts-container" style="height: 300px; width: auto;"></div>
                            <?php }else{ ?>
                              <div id="varContainerGeneralManualNo" style="height: auto; width: auto;">

                                <div class="panel panel-default">
                                  <div class="panel-body" style="background-color: #f0f8ff;">
                                    
                                    <div class="row">
                                      <div class="col-md-12">
                                        <label  style="font-size: 15px;" ><em class="fas fa-info-circle" style="font-size: 25px; color: #D01E53;"></em><?= Yii::t('app', ' Actualmente no hay resultados para la gráfica.') ?></label>
                                      </div>
                                    </div>
                                    
                                  </div>
                                </div>

                              </div>
                            <?php } ?>
                          </div>
                        </div>
                        
                      </div>

                      <br>

                      <div class="row">
                        <div class="col-md-12">
                          <div class="card1 mb">
                            <table id="myTableIndicaAuto" class="table table-hover table-bordered" style="margin-top:10px" >
                              <caption><label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Resultados Equipos') ?></label></caption>
                              <thead>
                                <tr>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Lider de Equipo') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Valoraciones') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Score') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Pec') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Penc') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Sfc/Frc') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Proceso') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Experiencia') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice Promesa de Marca') ?></label></th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php 

                                  $varResultadosLideresMixto = (new \yii\db\Query())
                                                  ->select(['tbl_usuarios.usua_id', 'tbl_usuarios.usua_nombre', 'COUNT(tbl_ejecucionformularios.id) AS cantidad', 
                                                    'ROUND(AVG(tbl_ejecucionformularios.score),2) AS score',
                                                    'ROUND(AVG(tbl_ejecucionformularios.i1_nmcalculo),2) AS pec',
                                                    'ROUND(AVG(tbl_ejecucionformularios.i2_nmcalculo),2) AS penc',
                                                    'ROUND(AVG(tbl_ejecucionformularios.i3_nmcalculo),2) AS sfc_frc',
                                                    'ROUND(AVG(tbl_ejecucionformularios.i5_nmcalculo),2) AS indiceproceso',
                                                    'ROUND(AVG(tbl_ejecucionformularios.i6_nmcalculo),2) AS indiceexperiencia',
                                                    'ROUND(AVG(tbl_ejecucionformularios.i6_nmcalculo),2) AS indicepromesa'])

                                                  ->from(['tbl_usuarios']) 

                                                  ->join('LEFT OUTER JOIN', 'tbl_ejecucionformularios',
                                                    'tbl_usuarios.usua_id = tbl_ejecucionformularios.usua_id_lider')    

                                                  ->where(['IN','tbl_ejecucionformularios.id',$varDataFormularios])
                                                  ->groupby(['tbl_usuarios.usua_id'])
                                                  ->All();


                                  foreach ($varResultadosLideresMixto as $key => $value) {


                                ?>

                                  <tr>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $value['usua_nombre']; ?></label></td>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $value['cantidad']; ?></label></td>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $value['score']; ?></label></td>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $value['pec']; ?></label></td>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $value['penc']; ?></label></td>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $value['sfc_frc']; ?></label></td>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $value['indiceproceso']; ?></label></td>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $value['indiceexperiencia']; ?></label></td>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $value['indicepromesa']; ?></label></td>
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

              <?php
              }
              ?>

            </div>

            <!-- Proceso Calidad -->
            <div id="Calidad" class="w3-container city" style="display:none;">

              <?php
              $varNumeroContainer = 0;
              foreach ($varListasClienteIdealP as $key => $value) {
                $varCentrosCostosCalidad = $value['cod_pcrc'];
                
                $varNombreCentroCostosC = (new \yii\db\Query())
                                              ->select(['concat(cod_pcrc," - ",pcrc) as NamePcrc'])
                                              ->from(['tbl_speech_categorias'])            
                                              ->where(['=','anulado',0])
                                              ->andwhere(['in','cod_pcrc',$varCentrosCostosCalidad])
                                              ->groupby(['cod_pcrc'])
                                              ->Scalar();
              ?>

              <br>

                <div class="row">
                  <div class="col-md-12">

                    <div class="card1 mb">
                      <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em> <?= Yii::t('app', $varNombreCentroCostosC) ?></label>

                      <?php
                        $varNumeroContainer += 1;
                        $prueba = 'myChart'.$varCentrosCostosAutos.'_'.$varNumeroContainer;
                        $varFechaInicioResta = date('Y-m-d',strtotime($varFechainicial."- 2 month"));

                        $varListasProcesamientos = (new \yii\db\Query())
                                                      ->select(['ELT(MONTH(tbl_ideal_llamadas.fechainicio), "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre") AS Mes','sum(tbl_ideal_llamadas.cantidad) as cantidad'])
                                                      ->from(['tbl_ideal_llamadas'])            
                                                      ->where(['=','tbl_ideal_llamadas.id_dp_cliente',$varIdDpCliente])
                                                      ->andwhere(['=','tbl_ideal_llamadas.cod_pcrc',$varCentrosCostosCalidad])
                                                      ->andwhere(['>=','tbl_ideal_llamadas.fechainicio',$varFechaInicioResta.' 05:00:00'])
                                                      ->andwhere(['<=','tbl_ideal_llamadas.fechafin',$varFechaFinal.' 05:00:00'])
                                                      ->andwhere(['=','tbl_ideal_llamadas.anulado',0])
                                                      ->andwhere(['=','tbl_ideal_llamadas.extension',$varIdExtensionc])
                                                      ->groupby(['Mes'])
                                                      ->orderby(['Mes'=>SORT_DESC])
                                                      ->All();
                        
                        $varListasCantidadForms = (new \yii\db\Query())
                                                      ->select(['ELT(MONTH(tbl_ideal_novedades.fechainicio), "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre") AS Mes','count(tbl_ideal_novedades.formulario_id) as cantidad'])
                                                      ->from(['tbl_ideal_novedades'])            
                                                      ->where(['=','tbl_ideal_novedades.id_dp_cliente',$varIdDpCliente])
                                                      ->andwhere(['=','tbl_ideal_novedades.cod_pcrc',$varCentrosCostosCalidad])
                                                      ->andwhere(['>=','tbl_ideal_novedades.fechainicio',$varFechaInicioResta.' 05:00:00'])
                                                      ->andwhere(['<=','tbl_ideal_novedades.fechafin',$varFechaFinal.' 05:00:00'])
                                                      ->andwhere(['=','tbl_ideal_novedades.anulado',0])
                                                      ->andwhere(['=','tbl_ideal_novedades.extension',$varIdExtensionc])
                                                      ->groupby(['Mes'])
                                                      ->orderby(['Mes'=>SORT_DESC])
                                                      ->All();
                        
                      ?>             

                      <div class="row">
                        <div class="col-md-6">
                          <div class="card1 mb">

                            <table id="myTableIndicaAuto" class="table table-hover table-bordered" style="margin-top:10px" >
                              <caption><label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Resultados Cantidades Procesamiento Automático') ?></label></caption>
                              <thead>
                                <tr>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Mes') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Interacciones') ?></label></th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php
                                  foreach ($varListasProcesamientos as $key => $value) {
                                ?>
                                  <tr>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $value['Mes']; ?></label></td>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $value['cantidad']; ?></label></td>
                                  </tr>
                                      
                                <?php
                                  }
                                ?>
                              </tbody>
                            </table>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="card1 mb">
                            <table id="myTableIndicaAuto" class="table table-hover table-bordered" style="margin-top:10px" >
                              <caption><label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Resultados Cantidades Procesamiento Manual') ?></label></caption>
                              <thead>
                                <tr>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Mes') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Valoraciones') ?></label></th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php
                                  foreach ($varListasCantidadForms as $key => $value) {
                                ?>
                                  <tr>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $value['Mes']; ?></label></td>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $value['cantidad']; ?></label></td>
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

              <?php
              }
              ?>
              

            </div>

            <!-- Proceso Mejora -->
            <div id="Mejora" class="w3-container city" style="display:none;">

              <?php
                foreach ($varListasClienteIdealP as $key => $value) {
                  $varCentrosCostosMejora = $value['cod_pcrc'];
                  
                  $varNombreCentroCostosMejora = (new \yii\db\Query())
                                                ->select(['concat(cod_pcrc," - ",pcrc) as NamePcrc'])
                                                ->from(['tbl_speech_categorias'])            
                                                ->where(['=','anulado',0])
                                                ->andwhere(['in','cod_pcrc',$varCentrosCostosMejora])
                                                ->groupby(['cod_pcrc'])
                                                ->Scalar();


                  $varListaFormulariosMejora = (new \yii\db\Query())
                                                ->select(['formulario_id'])
                                                ->from(['tbl_ideal_novedades'])            
                                                ->where(['=','tbl_ideal_novedades.id_dp_cliente',$varIdDpCliente])
                                                ->andwhere(['=','tbl_ideal_novedades.cod_pcrc',$varCentrosCostosMejora])
                                                ->andwhere(['>=','tbl_ideal_novedades.fechainicio',$varFechainicial.' 05:00:00'])
                                                ->andwhere(['<=','tbl_ideal_novedades.fechafin',$varFechaFinal.' 05:00:00'])
                                                ->andwhere(['=','tbl_ideal_novedades.anulado',0])
                                                ->andwhere(['=','tbl_ideal_novedades.extension',$varIdExtensionc])
                                                ->groupby(['formulario_id'])
                                                ->All();  
                                              
                  if (count($varListaFormulariosMejora) != 0) {
                    $varArrayForms = array();
                    foreach ($varListaFormulariosMejora as $key => $value) {
                      array_push($varArrayForms, $value['formulario_id']);
                    }

                    $varArrayListarForms = implode(", ", $varArrayForms);

                    $varDataFormulariosMejoras = explode(",", str_replace(array("#", "'", ";", " "), '', $varArrayListarForms));


                    $varListaGeneralFormulariosMejora = (new \yii\db\Query())
                                                  ->select(['tbl_ejecucionfeedbacks.snaviso_revisado'])

                                                  ->from(['tbl_ejecucionfeedbacks']) 

                                                  ->join('LEFT OUTER JOIN', 'tbl_ejecucionformularios',
                                                    'tbl_ejecucionfeedbacks.ejecucionformulario_id = tbl_ejecucionformularios.id')    

                                                  ->where(['IN','tbl_ejecucionformularios.id',$varDataFormulariosMejoras])
                                                  ->All();

                    $varCantidadFeedbacks = count($varListaGeneralFormulariosMejora);

                    $varArrayConteoGestionada = 0;
                    $varArrayConteoNoGestionada = 0;
                    $varArrayConteoEstadoOk = 0;
                    $varArrayConteoEstadoKo = 0;
                    foreach ($varListaGeneralFormulariosMejora as $key => $value) {
                      if ($value['snaviso_revisado'] == 1) {
                        $varArrayConteoGestionada += 1;
                      }

                      if ($value['snaviso_revisado'] == 0) {
                        $varArrayConteoNoGestionada += 1;
                      }
                    }

                    $varListaSegundoclificadorMejora = (new \yii\db\Query())
                                                  ->select(['tbl_segundo_calificador.estado_sc'])

                                                  ->from(['tbl_segundo_calificador']) 

                                                  ->join('LEFT OUTER JOIN', 'tbl_ejecucionformularios',
                                                    'tbl_segundo_calificador.id_ejecucion_formulario = tbl_ejecucionformularios.id')    

                                                  ->where(['IN','tbl_ejecucionformularios.id',$varDataFormulariosMejoras])
                                                  ->All();

                    $varCantidadSegundo = count($varListaSegundoclificadorMejora);

                    foreach ($varListaSegundoclificadorMejora as $key => $value) {
                      if ($value['estado_sc'] == 'Abierto') {
                        $varArrayConteoEstadoOk += 1;
                      }
                      if ($value['estado_sc'] == 'Aceptado') {
                        $varArrayConteoEstadoKo += 1;
                      }
                    }

                    if ($varCantidadFeedbacks != 0) {
                      $varProcentajeGestionFeedback = round(($varArrayConteoGestionada/$varCantidadFeedbacks) * 100, 2);
                    }else{
                      $varProcentajeGestionFeedback = 0;
                    }

                    if ($varCantidadFeedbacks != 0) {
                      $varProcentajeGestionSegundo = round(($varArrayConteoEstadoKo/$varCantidadFeedbacks) * 100, 2);
                    }else{
                      $varProcentajeGestionSegundo = 0;
                    }
                    

                    

                    $varConteoAlertas = (new \yii\db\Query())
                                      ->select(['tbl_alertascx.id'])
                                      ->from(['tbl_alertascx'])  
                                      ->join('LEFT OUTER JOIN', 'tbl_ejecucionformularios',
                                          'tbl_alertascx.pcrc = tbl_ejecucionformularios.arbol_id')
                                      ->where(['in','tbl_ejecucionformularios.id',$varDataFormulariosMejoras])
                                      ->count();

                    $varConteoAlCalibraciones = (new \yii\db\Query())
                                      ->select(['tbl_arbols.id'])
                                      ->from(['tbl_arbols'])  
                                      ->join('LEFT OUTER JOIN', 'tbl_ejecucionformularios',
                                          'tbl_arbols.id = tbl_ejecucionformularios.arbol_id')
                                      ->where(['in','tbl_ejecucionformularios.id',$varDataFormulariosMejoras])
                                      ->andwhere(['like','tbl_arbols.name','alibracion'])
                                      ->count();

                  }else{
                    $varCantidadFeedbacks = $varSinData;
                    $varCantidadSegundo = $varSinData;
                    $varArrayConteoGestionada = $varSinData;
                    $varArrayConteoNoGestionada = $varSinData;
                    $varArrayConteoEstadoOk = $varSinData;
                    $varArrayConteoEstadoKo = $varSinData;
                    $varProcentajeGestionFeedback = $varSinData;
                    $varProcentajeGestionSegundo = $varSinData;
                    $varConteoAlertas = $varSinData;
                    $varConteoAlCalibraciones = $varSinData;
                  }                            
              ?>

                <br>

                <div class="row">
                  <div class="col-md-12">
                    <div class="card1 mb">
                      <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 15px; color: #559FFF;"></em> <?= Yii::t('app', $varNombreCentroCostosMejora) ?></label>

                      <div class="row">
                        <div class="col-md-3">
                          <div class="card1 mb">
                            <table id="myTableMejoraFeedback" class="table table-hover table-bordered" style="margin-top:10px" >
                              <caption><label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Resultados Cantidades Feedbacks') ?></label></caption>
                              <thead>
                                <tr>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Total') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '% de Gestión') ?></label></th>
                                </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varCantidadFeedbacks; ?></label></td>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varProcentajeGestionFeedback; ?></label></td>
                                  </tr>
                                  <tr>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Gestionadas') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Pendientes') ?></label></th>
                                  </tr>
                                  <tr>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varArrayConteoGestionada; ?></label></td>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varArrayConteoNoGestionada; ?></label></td>
                                  </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>

                        <div class="col-md-3">
                          <div class="card1 mb">
                            <table id="myTableMejoraSegundo" class="table table-hover table-bordered" style="margin-top:10px" >
                              <caption><label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Resultados Segundo Calificador') ?></label></caption>
                              <thead>
                                <tr>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Total') ?></label></th>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '% de Gestión') ?></label></th>
                                </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varCantidadSegundo; ?></label></td>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varProcentajeGestionSegundo; ?></label></td>
                                  </tr>
                                  <tr>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Gestionadas') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Pendientes') ?></label></th>
                                  </tr>
                                  <tr>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varArrayConteoEstadoKo; ?></label></td>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varArrayConteoEstadoOk; ?></label></td>
                                  </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>

                        <div class="col-md-3">
                          <div class="card1 mb">
                            <table id="myTableMejoraAlertas" class="table table-hover table-bordered" style="margin-top:10px" >
                              <caption><label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Resultados Alertas del Proceso') ?></label></caption>
                              <thead>
                                <tr>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Total') ?></label></th>
                                </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varCantidadSegundo; ?></label></td>
                                  </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>

                        <div class="col-md-3">
                          <div class="card1 mb">
                            <table id="myTableMejoraCalibraciones" class="table table-hover table-bordered" style="margin-top:10px" >
                              <caption><label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Resultados Calibraciones') ?></label></caption>
                              <thead>
                                <tr>
                                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Total') ?></label></th>
                                </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varConteoAlCalibraciones; ?></label></td>
                                  </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>

              <?php
                }
              ?>

            </div>

        </div>
        
      </div>
    </div>
  </div>

</div>

<hr>

<!-- Capa Botones -->
<div id="CapaIdBtn" class="capaBtn" style="display: inline;">
  
  <div class="row">
    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Buscar Por Proceso') ?></label>
        <?= Html::button('Buscar', ['value' => url::to(['buscarporproceso']), 'class' => 'btn btn-success', 'id'=>'modalButton2',
          'data-toggle' => 'tooltip',
          'style' => 'background-color: #827DF9', 
          'title' => 'Buscar Por Proceso']) ?> 

        <?php
            Modal::begin([
              'header' => '<h4>Filtros - Búsqueda Por Proceso CXM</h4>',
              'id' => 'modal2',
              'size' => 'modal-lg',
            ]);

            echo "<div id='modalContent2'></div>";
                                                              
            Modal::end(); 
        ?>
      </div>
    </div>

    

    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #FFC72C;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?></label>
        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Regresar']) 
        ?>
      </div>
    </div>
  </div>

</div>

<hr>

<script type="text/javascript">
  function opennovedad(){
    var varidtbn1 = document.getElementById("idtbn1");
    var varidtbn2 = document.getElementById("idtbn2");
    var varidnovedad = document.getElementById("capaListaGerentes");

    varidtbn1.style.display = 'none';
    varidtbn2.style.display = 'inline';
    varidnovedad.style.display = 'inline';
  };

  function closenovedad(){
    var varidtbn1 = document.getElementById("idtbn1");
    var varidtbn2 = document.getElementById("idtbn2");
    var varidnovedad = document.getElementById("capaListaGerentes");

    varidtbn1.style.display = 'inline';
    varidtbn2.style.display = 'none';
    varidnovedad.style.display = 'none';
  };

  function openCity(evt, cityName) {
    var i, x, tablinks;
    x = document.getElementsByClassName("city");
    for (i = 0; i < x.length; i++) {
      x[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablink");
    for (i = 0; i < x.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" w3-border-red", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.firstElementChild.className += " w3-border-red";
  };

  var chartContainerGP = document.getElementById("chartContainerGP");
  var chartContainerMP = document.getElementById("chartContainerMP");
  var chartContainerAP = document.getElementById("chartContainerAP");

  var oilDataGP = {
    datasets: [
      {
        data: ["<?php echo $varListarGeneralResponsabilidadP; ?>","<?php echo $varRestanteGeneralP; ?>"],
        backgroundColor: [
          "<?php echo $varTColorGeneralP; ?>",
                    "#D7CFC7"
        ]
      }]
  };

  var pieChart = new Chart(chartContainerGP, {
    type: 'doughnut',
    data: oilDataGP
  });

  var oilDataMP = {
    datasets: [
      {
        data: ["<?php echo $varListasMixtasP; ?>","<?php echo $varRestanteMixtoP; ?>"],
        backgroundColor: [
          "<?php echo $varTColorMixtoP; ?>",
          "#D7CFC7"
        ]
    }]
  };

  var pieChart = new Chart(chartContainerMP, {
    type: 'doughnut',
    data: oilDataMP
  });

  var oilDataAP = {
    datasets: [
      {
        data: ["<?php echo $varPormedioResponsableP; ?>","<?php echo $varRestanteAutosP; ?>"],
        backgroundColor: [
          "<?php echo $varTColorAutoP; ?>",
          "#D7CFC7"
        ]
    }]
  };

  var pieChart = new Chart(chartContainerAP, {
    type: 'doughnut',
    data: oilDataAP
  });

$(function() {
  
    Highcharts.setOptions({
      lang: {
        numericSymbols: null,
        thousandsSep: ','
      }
    });

    $('#chartContainerResposnabilidadesP').highcharts({

      chart: {
        borderColor: '#DAD9D9',
        borderRadius: 7,
        borderWidth: 1,
        type: 'column'
      }, 

      title: {
        text: '.',
        style: {
          color: '#3C74AA'
        }
      },

      xAxis: {
        categories: '_',
        title: {
          text: null
        },
        crosshair: true
      },


      series: [
        {
          name: 'Agente',
          data: [<?= $varArrayAgenteP ?>],
          color: '#FBCE52'
        },{
          name: 'Marca',
          data: [<?= $varArrayMarcaP ?>],
          color: '#4298B5'
        },{
          name: 'Canal',
          data: [<?= $varArrayCanalP ?>],
          color: '#C6C6C6'
        }
      ]

    });

    $('#varContainerGeneralManual').highcharts({

      chart: {
        borderColor: '#DAD9D9',
        borderRadius: 7,
        borderWidth: 1,
        type: 'column'
      }, 

      title: {
        text: 'Resultados General - Calidad & Consistencia',
        style: {
          color: '#3C74AA'
        }
      },

      xAxis: {
        categories: '_',
        title: {
          text: null
        },
        crosshair: true
      },

      series: [
        {
          name: 'Score',
          data: [<?= $varManualScore ?>],
          color: '#827DF9'
        },{
          name: 'Pec',
          data: [<?= $varManualPEC ?>],
          color: '#FBCE52'
        },{
          name: 'Penc',
          data: [<?= $varManualPENC ?>],
          color: '#4298B5'
        },{
          name: 'Sfc',
          data: [<?= $varManualSFC ?>],
          color: '#C148D0'
        },{
          name: 'Indice Proceso',
          data: [<?= $varManualProceso ?>],
          color: '#C6C6C6'
        },{
          name: 'Indice Experiencia',
          data: [<?= $varManualExp ?>],
          color: '#559FFF'
        },{
          name: 'Promesa Marca',
          data: [<?= $varManualProm ?>],
          color: '#4298b5'
        }
      ]

    });    


  });


</script>