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

  $varSinData = '--';

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
  $varArrayConteoEstadoFail = 0;
  $varArrayConteoAceptado = 0;
  $varCantidadSegundo = 0;

  $titulos = array();

  $varArrayNombreMejora = array();
  $varArrayConteoMejora = array();

  

  $varConteoMejoraFeedback = 0;

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
          <label ><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Lista Programa/Pcrc:') ?></label>   
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
    <?php
      $varArrayRtaAutoGeneral = array();
      $varArrayRtaAgenteGeneral = array();
      $varArrayRtaCanalGeneral = array();
      $varArrayRtaMarcaGeneral = array();
      $varCantidadCodPcrcGeneral = 0;

      foreach ($varListasClienteIdealP as $key => $value) {
        $varCodPcrcGeneral = $value['cod_pcrc'];
        $varCantidadCodPcrcGeneral += 1;

        $varAgenteGeneral =  (new \yii\db\Query())
                            ->select(['ROUND(AVG(tbl_ideal_responsabilidad.agente),1) AS varMarca'])
                            ->from(['tbl_ideal_responsabilidad']) 
                            ->where(['=','tbl_ideal_responsabilidad.id_dp_cliente',$varIdDpCliente])
                            ->andwhere(['=','tbl_ideal_responsabilidad.cod_pcrc',$varCodPcrcGeneral])
                            ->andwhere(['>=','tbl_ideal_responsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                            ->andwhere(['<=','tbl_ideal_responsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                            ->andwhere(['=','tbl_ideal_responsabilidad.extension',$varIdExtensionc])
                            ->andwhere(['=','tbl_ideal_responsabilidad.anulado',0])
                            ->andwhere(['!=','tbl_ideal_responsabilidad.agente',0])
                            ->scalar();                

        array_push($varArrayRtaAgenteGeneral, $varAgenteGeneral);

        $varMarcaGeneral = (new \yii\db\Query())
                          ->select(['ROUND(AVG(tbl_ideal_responsabilidad.marca),1) AS varMarca'])
                          ->from(['tbl_ideal_responsabilidad']) 
                          ->where(['=','tbl_ideal_responsabilidad.id_dp_cliente',$varIdDpCliente])
                          ->andwhere(['=','tbl_ideal_responsabilidad.cod_pcrc',$varCodPcrcGeneral])
                          ->andwhere(['>=','tbl_ideal_responsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                          ->andwhere(['<=','tbl_ideal_responsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                          ->andwhere(['=','tbl_ideal_responsabilidad.extension',$varIdExtensionc])
                          ->andwhere(['=','tbl_ideal_responsabilidad.anulado',0])
                          ->andwhere(['!=','tbl_ideal_responsabilidad.marca',0])
                          ->scalar();

        array_push($varArrayRtaMarcaGeneral, $varMarcaGeneral);

        $varCanalMixtos = (new \yii\db\Query())
                          ->select(['ROUND(AVG(tbl_ideal_responsabilidad.canal),1) AS varMarca'])
                          ->from(['tbl_ideal_responsabilidad']) 
                          ->where(['=','tbl_ideal_responsabilidad.id_dp_cliente',$varIdDpCliente])
                          ->andwhere(['=','tbl_ideal_responsabilidad.cod_pcrc',$varCodPcrcGeneral])
                          ->andwhere(['>=','tbl_ideal_responsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                          ->andwhere(['<=','tbl_ideal_responsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                          ->andwhere(['=','tbl_ideal_responsabilidad.extension',$varIdExtensionc])
                          ->andwhere(['=','tbl_ideal_responsabilidad.anulado',0])
                          ->andwhere(['!=','tbl_ideal_responsabilidad.canal',0])
                          ->scalar();


        array_push($varArrayRtaCanalGeneral, $varCanalMixtos);

      }

      $varArrayRtaScoreGeneral = (new \yii\db\Query())
                          ->select(['ROUND(AVG(tbl_ejecucionformularios.score)*100,2) AS varScore'])
                          ->from(['tbl_ejecucionformularios']) 

                          ->join('LEFT OUTER JOIN', 'tbl_speech_mixta',
                                  'tbl_ejecucionformularios.id = tbl_speech_mixta.formulario_id')

                          ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                                  'tbl_speech_mixta.callid = tbl_dashboardspeechcalls.callId')

                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varNombreSpeech])
                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesM])
                          ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varLlamada])
                          ->scalar();

      if ($varArrayRtaScoreGeneral == "") {
        $varArrayRtaScoreGeneral = 0;
      }

      $varArrayRestanteScoreGeneral = round( 100-$varArrayRtaScoreGeneral,2);

      if ($varArrayRestanteScoreGeneral < '80') {
        $varTColorMixtoP = $varMalo;
      }else{
        if ($varArrayRestanteScoreGeneral >= '90') {
          $varTColorMixtoP = $varBueno;
        }else{
          $varTColorMixtoP = $varEstable;
        }
      }

      $varArrayAgenteP = round( array_sum($varArrayRtaAgenteGeneral) / $varCantidadCodPcrcGeneral,2);
      $varArrayMarcaP = round( array_sum($varArrayRtaMarcaGeneral) / $varCantidadCodPcrcGeneral,2);
      $varArrayCanalP = round( array_sum($varArrayRtaCanalGeneral) / $varCantidadCodPcrcGeneral,2);

      $varArrayPromedioAutoGeneral = round( ($varArrayAgenteP + $varArrayCanalP) / 2,2);
      $varArrayRestanteAutoGeneral = round( 100 - $varArrayPromedioAutoGeneral,2);

      if ($varArrayPromedioAutoGeneral < '80') {
        $varTColorAutoP = $varMalo;
      }else{
        if ($varArrayPromedioAutoGeneral >= '90') {
          $varTColorAutoP = $varBueno;
        }else{
          $varTColorAutoP = $varEstable;
        }
      }

      if ($varArrayRtaScoreGeneral != 0) {
        $varArrayKonectaGeneral = round( ($varArrayRtaScoreGeneral+$varArrayPromedioAutoGeneral)/2,2);
        $varArrayRestanteKGeneral = round(100-$varArrayKonectaGeneral,2);
      }else{
        $varArrayKonectaGeneral = 0;
        $varArrayRestanteKGeneral = 100;
      }
      
      if ($varArrayKonectaGeneral < '80') {
        $varTColorGeneralP = $varMalo;
      }else{
        if ($varArrayKonectaGeneral >= '90') {
          $varTColorGeneralP = $varBueno;
        }else{
          $varTColorGeneralP = $varEstable;
        }
      }
    ?>

    <div class="col-md-6">
      <div class="card3 mb">
        <label style="font-size: 15px;"><em class="fas fa-chart-bar" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Detalle de Procesamiento Mixto') ?></label>

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
            <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerGP"></canvas></div><span style="font-size: 15px;"><?php echo $varArrayKonectaGeneral.' %'; ?></span></td> 
            <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerMP"></canvas></div><span style="font-size: 15px;"><?php echo $varArrayRtaScoreGeneral.' %'; ?></span></td> 
            <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerAP"></canvas></div><span style="font-size: 15px;"><?php echo $varArrayPromedioAutoGeneral.' %'; ?></span></td>
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

              <br>

              <?php
              $varConteoProcesoMixto = 0;
              foreach ($varListasClienteIdealP as $key => $value) {
                $varConteoProcesoMixto += 1;

                $varCodPcrcMixtos = $value['cod_pcrc'];

                if ($varIdExtensionc > '1') {
                  $varRnIdealM =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcMixtos])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['=','tipoparametro',$varIdExtensionc])
                                ->groupby(['rn'])
                                ->all();
                }else{
                  $varRnIdealM =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcMixtos])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['rn'])
                                ->all();
                }

                if (count($varRnIdealM) != 0) {
                  $varArrayRnM = array();
                  foreach ($varRnIdealM as $key => $value) {
                    array_push($varArrayRnM, $value['rn']);
                  }

                  $varExtensionesArraysM = implode("', '", $varArrayRnM);
                  $arrayExtensiones_downM = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysM);
                  $varExtensionesMixtas = explode(",", $arrayExtensiones_downM);
                }else{

                  if ($varIdExtensionc > '1') {
                    $varExtM =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcMixtos])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['=','tipoparametro',$varIdExtensionc])
                                ->groupby(['ext'])
                                ->all();
                  }else{
                    $varExtM =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcMixtos])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['ext'])
                                ->all();
                  }

                  if (count($varExtM) != 0) {
                    $varArrayExtM = array();
                    foreach ($varExtM as $key => $value) {
                      array_push($varArrayExtM, $value['ext']);
                    }

                    $varExtensionesArraysM = implode("', '", $varArrayExtM);
                    $arrayExtensiones_downM = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysM);
                    $varExtensionesMixtas = explode(",", $arrayExtensiones_downM);
                  }else{

                    if ($varIdExtensionc > '1') {
                      $varUsuaM =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcMixtos])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['=','tipoparametro',$varIdExtensionc])
                                ->groupby(['usuared'])
                                ->all();
                    }else{
                      $varUsuaM =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcMixtos])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['usuared'])
                                ->all();
                    }


                    if (count($varUsuaM) != 0) {
                      $varArrayUsuaM = array();
                      foreach ($varUsuaM as $key => $value) {
                        array_push($varArrayUsuaM, $value['usuared']);
                      }

                      $varExtensionesArraysM = implode("', '", $varArrayUsuaM);
                      $arrayExtensiones_downM = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysM);
                      $varExtensionesMixtas = explode(",", $arrayExtensiones_downM);
                    }else{
                      $varExtensionesMixtas = "N0A";
                    }
                  }
                }

                $varNombreCodPcrcMixtos = (new \yii\db\Query())
                                          ->select(['concat(cod_pcrc," - ",pcrc) as NamePcrc'])
                                          ->from(['tbl_speech_categorias'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['in','cod_pcrc',$varCodPcrcMixtos])
                                          ->groupby(['cod_pcrc'])
                                          ->Scalar(); 

                $varAgenteMixtos = (new \yii\db\Query())
                                          ->select(['ROUND(AVG(tbl_ideal_responsabilidad.agente),1) AS varMarca'])
                                          ->from(['tbl_ideal_responsabilidad']) 
                                          ->where(['=','tbl_ideal_responsabilidad.id_dp_cliente',$varIdDpCliente])
                                          ->andwhere(['=','tbl_ideal_responsabilidad.cod_pcrc',$varCodPcrcMixtos])
                                          ->andwhere(['>=','tbl_ideal_responsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                                          ->andwhere(['<=','tbl_ideal_responsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['=','tbl_ideal_responsabilidad.extension',$varIdExtensionc])
                                          ->andwhere(['=','tbl_ideal_responsabilidad.anulado',0])
                                          ->andwhere(['!=','tbl_ideal_responsabilidad.agente',0])
                                          ->scalar();       

                if ($varAgenteMixtos < '80') {
                  $varTColorAgenteIdealP = $varMalo;
                }else{
                  if ($varAgenteMixtos >= '90') {
                    $varTColorAgenteIdealP = $varBueno;
                  }else{
                    $varTColorAgenteIdealP = $varEstable;
                  }
                }                        

                $varMarcaMixtos = (new \yii\db\Query())
                                          ->select(['ROUND(AVG(tbl_ideal_responsabilidad.marca),1) AS varMarca'])
                                          ->from(['tbl_ideal_responsabilidad']) 
                                          ->where(['=','tbl_ideal_responsabilidad.id_dp_cliente',$varIdDpCliente])
                                          ->andwhere(['=','tbl_ideal_responsabilidad.cod_pcrc',$varCodPcrcMixtos])
                                          ->andwhere(['>=','tbl_ideal_responsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                                          ->andwhere(['<=','tbl_ideal_responsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['=','tbl_ideal_responsabilidad.extension',$varIdExtensionc])
                                          ->andwhere(['=','tbl_ideal_responsabilidad.anulado',0])
                                          ->andwhere(['!=','tbl_ideal_responsabilidad.marca',0])
                                          ->scalar();

                if ($varMarcaMixtos < '80') {
                  $varTColorMarcaIdealP = $varMalo;
                }else{
                  if ($varMarcaMixtos >= '90') {
                    $varTColorMarcaIdealP = $varBueno;
                  }else{
                    $varTColorMarcaIdealP = $varEstable;
                  }
                }

                $varCanalMixtos = (new \yii\db\Query())
                                          ->select(['ROUND(AVG(tbl_ideal_responsabilidad.canal),1) AS varMarca'])
                                          ->from(['tbl_ideal_responsabilidad']) 
                                          ->where(['=','tbl_ideal_responsabilidad.id_dp_cliente',$varIdDpCliente])
                                          ->andwhere(['=','tbl_ideal_responsabilidad.cod_pcrc',$varCodPcrcMixtos])
                                          ->andwhere(['>=','tbl_ideal_responsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                                          ->andwhere(['<=','tbl_ideal_responsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['=','tbl_ideal_responsabilidad.extension',$varIdExtensionc])
                                          ->andwhere(['=','tbl_ideal_responsabilidad.anulado',0])
                                          ->andwhere(['!=','tbl_ideal_responsabilidad.canal',0])
                                          ->scalar();

                if ($varCanalMixtos < '80') {
                  $varTColorCanalIdealP = $varMalo;
                }else{
                  if ($varCanalMixtos >= '90') {
                    $varTColorCanalIdealP = $varBueno;
                  }else{
                    $varTColorCanalIdealP = $varEstable;
                  }
                }
                
                $varArrayRtaAutoMixta = round( ($varAgenteMixtos+$varCanalMixtos)/2,2);

                if ($varArrayRtaAutoMixta < '80') {
                  $varTColorAutoIdealP = $varMalo;
                }else{
                  if ($varArrayRtaAutoMixta >= '90') {
                    $varTColorAutoIdealP = $varBueno;
                  }else{
                    $varTColorAutoIdealP = $varEstable;
                  }
                }


                $varArrayRtaScoreMixta = (new \yii\db\Query())
                                          ->select(['ROUND(AVG(tbl_ejecucionformularios.score)*100,2) AS varScore'])
                                          ->from(['tbl_ejecucionformularios']) 

                                          ->join('LEFT OUTER JOIN', 'tbl_speech_mixta',
                                                  'tbl_ejecucionformularios.id = tbl_speech_mixta.formulario_id')

                                          ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                                                  'tbl_speech_mixta.callid = tbl_dashboardspeechcalls.callId')

                                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varNombreSpeech])
                                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesMixtas])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varLlamada])
                                          ->scalar();


                if ($varArrayRtaScoreMixta == "") {
                  $varArrayRtaScoreMixta = 0;
                }

                if ($varArrayRtaScoreMixta < '80') {
                  $varTColorMixtaAIdealP = $varMalo;
                }else{
                  if ($varArrayRtaScoreMixta >= '90') {
                    $varTColorMixtaAIdealP = $varBueno;
                  }else{
                    $varTColorMixtaAIdealP = $varEstable;
                  }
                }

                
                $varArrayKonectaMixta = round( ($varArrayRtaScoreMixta+$varArrayRtaAutoMixta)/2,2);
                

                if ($varArrayKonectaMixta < '80') {
                  $varTColorGeneralIdealP = $varMalo;
                }else{
                  if ($varArrayKonectaMixta >= '90') {
                    $varTColorGeneralIdealP = $varBueno;
                  }else{
                    $varTColorGeneralIdealP = $varEstable;
                  }
                }

                $varArrayCantidadSpeechMixta = (new \yii\db\Query())
                                          ->select(['tbl_dashboardspeechcalls.callid'])
                                          ->from(['tbl_dashboardspeechcalls']) 
                                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varNombreSpeech])
                                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesMixtas])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varLlamada])
                                          ->count();

                $varListaDataArbolsMixtos = (new \yii\db\Query())
                                                ->select(['*'])
                                                ->from(['tbl_speech_pcrcformularios'])            
                                                ->where(['=','anulado',0])
                                                ->andwhere(['=','cod_pcrc',$varCodPcrcMixtos])
                                                ->andwhere(['=','id_dp_clientes',$varIdDpCliente])
                                                ->all();

                $varArrayArbolsMixtos = array();
                foreach ($varListaDataArbolsMixtos as $key => $value) {
                  array_push($varArrayArbolsMixtos, $value['arbol_id']);
                }
                $varArray_ArbolMixtos = implode(", ", $varArrayArbolsMixtos);
                $arrayArboles_downM = str_replace(array("#", "'", ";", " "), '', $varArray_ArbolMixtos);
                $varArbolesMixtas = explode(",", $arrayArboles_downM);


                $varListaDataCalibracionesMixto = (new \yii\db\Query())
                                                ->select(['*'])
                                                ->from(['tbl_arbols'])            
                                                ->where(['=','tbl_arbols.activo',0])
                                                ->andwhere(['in','tbl_arbols.id',$varArray_ArbolMixtos])
                                                ->all();

                $varArrayArbolsCalibracionesMixto = array();
                foreach ($varListaDataCalibracionesMixto as $key => $value) {
                  array_push($varArrayArbolsCalibracionesMixto, $value['arbol_id']);
                }
                $varArray_ArbolCaliMixto = implode(", ", $varArrayArbolsCalibracionesMixto);

                $varTotalFeedbacksP = (new \yii\db\Query())
                                                ->select(['tbl_ejecucionfeedbacks.snaviso_revisado'])
                                                ->from(['tbl_ejecucionfeedbacks'])  

                                                ->join('LEFT OUTER JOIN', 'tbl_ejecucionformularios',
                                                    'tbl_ejecucionfeedbacks.ejecucionformulario_id = tbl_ejecucionformularios.id') 

                                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_ejecucionformularios.arbol_id = tbl_arbols.id') 

                                                ->where(['in','tbl_arbols.id',$varArray_ArbolMixtos])
                                                ->andwhere(['=','tbl_arbols.activo',0])
                                                ->andwhere(['between','tbl_ejecucionfeedbacks.created',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                                ->andwhere(['in','tbl_ejecucionfeedbacks.snaviso_revisado',[0,1]])
                                                ->andwhere(['in','tbl_ejecucionformularios.dimension_id',$varDimensionesId])
                                                ->count();

                if ($varTotalFeedbacksP < '80') {
                  $varTColorFeebackMixta = $varMalo;
                }else{
                  if ($varTotalFeedbacksP >= '90') {
                    $varTColorFeebackMixta = $varBueno;
                  }else{
                    $varTColorFeebackMixta = $varEstable;
                  }
                }

                $varConteoAlertasMixtas = (new \yii\db\Query())
                                      ->select(['tbl_alertascx.id'])
                                      ->from(['tbl_alertascx'])  
                                      
                                      ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                          'tbl_alertascx.pcrc = tbl_arbols.id')
                                      
                                      ->where(['in','tbl_arbols.id',$varArray_ArbolMixtos])
                                      ->andwhere(['=','tbl_arbols.activo',0])
                                      ->andwhere(['between','tbl_alertascx.fecha',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                      ->count();

                if ($varConteoAlertasMixtas < '80') {
                  $varTColorAlertasMixta = $varMalo;
                }else{
                  if ($varConteoAlertasMixtas >= '90') {
                    $varTColorAlertasMixta = $varBueno;
                  }else{
                    $varTColorAlertasMixta = $varEstable;
                  }
                }

                $varConteoCalibracionesMixtas = (new \yii\db\Query())
                                      ->select(['tbl_ejecucionformularios.id'])
                                      ->from(['tbl_ejecucionformularios'])

                                      ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                          'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')

                                      ->where(['in','tbl_arbols.arbol_id',$varArray_ArbolCaliMixto])
                                      ->andwhere(['like','tbl_arbols.name','alibra'])
                                      ->andwhere(['=','tbl_arbols.activo',0])
                                      ->andwhere(['between','tbl_ejecucionformularios.created',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                      ->andwhere(['in','tbl_ejecucionformularios.dimension_id',$varDimensionesId])
                                      ->count();

                if ($varConteoCalibracionesMixtas < '80') {
                  $varTColorCaliMixta = $varMalo;
                }else{
                  if ($varConteoCalibracionesMixtas >= '90') {
                    $varTColorCaliMixta = $varBueno;
                  }else{
                    $varTColorCaliMixta = $varEstable;
                  }
                }

              ?>

                <div class="row">
                  <div class="col-md-12">
                    <div class="card1 mb" style="font-size: 15px;">

                      <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', $varNombreCodPcrcMixtos) ?></label>

                      <br>

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
                                    <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorGeneralIdealP ?>"><?php echo  $varArrayKonectaMixta.' %'; ?></label></td>
                                    <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorMixtaAIdealP ?>"><?php echo  $varArrayRtaScoreMixta.' %'; ?></label></td>
                                    <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorAutoIdealP ?>"><?php echo  $varArrayRtaAutoMixta.' %'; ?></label></td>
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
                                    <td class="text-center"><label style="font-size: 12px;"><?php echo  $varArrayCantidadSpeechMixta; ?></label></td>
                                    <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorAgenteIdealP; ?>"><?php echo  $varAgenteMixtos.' %'; ?></label></td>
                                    <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorMarcaIdealP; ?>"><?php echo  $varMarcaMixtos.' %'; ?></label></td>
                                    <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorCanalIdealP; ?>"><?php echo  $varCanalMixtos.' %'; ?></label></td>
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
                                    <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorFeebackMixta; ?>"><?php echo  $varTotalFeedbacksP; ?></label></td>
                                    <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorAlertasMixta; ?>"><?php echo  $varConteoAlertasMixtas; ?></label></td>
                                    <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorCaliMixta; ?>"><?php echo  $varConteoCalibracionesMixtas; ?></label></td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>

                        </div>

                        <hr>
                        
                        <div class="row">
                          <div class="col-md-12">
                            <div class="card1 mb">
                              <table id="myTableCalidad" class="table table-hover table-bordered" style="margin-top:10px" >
                                <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Detalle Calidad y Consistencia - Manual') ?></label></caption>
                                <thead>
                                  <tr>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Valoraciones') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Score General') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PEC') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PENC') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'SPC/SFR') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Proceso') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Experiencia') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Promesa Marca') ?></label></th>

                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                    $varListRtaFormulariosMixtos = (new \yii\db\Query())
                                          ->select([
                                            'COUNT(tbl_ejecucionformularios.id) AS varConteo',
                                            'ROUND(AVG(tbl_ejecucionformularios.score)*100,2) AS varScore',
                                            'ROUND(AVG(tbl_ejecucionformularios.i1_nmcalculo)*100,2) AS varPec',
                                            'ROUND(AVG(tbl_ejecucionformularios.i2_nmcalculo)*100,2) AS varPenc',
                                            'ROUND(AVG(tbl_ejecucionformularios.i3_nmcalculo)*100,2) AS varSfc',
                                            'ROUND(AVG(tbl_ejecucionformularios.i5_nmcalculo)*100,2) AS varIndiceProceso',
                                            'ROUND(AVG(tbl_ejecucionformularios.i6_nmcalculo)*100,2) AS varIndiceExperienca',
                                            'ROUND(AVG(tbl_ejecucionformularios.i7_nmcalculo)*100,2) AS varIndicePromesa'
                                            ])
                                          ->from(['tbl_ejecucionformularios']) 

                                          ->join('LEFT OUTER JOIN', 'tbl_speech_mixta',
                                                  'tbl_ejecucionformularios.id = tbl_speech_mixta.formulario_id')

                                          ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                                                  'tbl_speech_mixta.callid = tbl_dashboardspeechcalls.callId')

                                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varNombreSpeech])
                                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesMixtas])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varLlamada])
                                          ->all();

                                    foreach ($varListRtaFormulariosMixtos as $key => $value) {
                                      
                                      if ($value['varConteo'] != "") {
                                        $varConteoFormMixto = $value['varConteo'];
                                      }else{
                                        $varConteoFormMixto = $varSinData;
                                      }

                                      if ($value['varScore'] != "") {
                                        $varScoreFormMixto = $value['varScore'].' %';
                                      }else{
                                        $varScoreFormMixto = $varSinData;
                                      }

                                      if ($value['varPec'] != "") {
                                        $varPecFormMixto = $value['varPec'].' %';
                                      }else{
                                        $varPecFormMixto = $varSinData;
                                      }

                                      if ($value['varPenc'] != "") {
                                        $varPencFormMixto = $value['varPec'].' %';
                                      }else{
                                        $varPencFormMixto = $varSinData;
                                      }

                                      if ($value['varSfc'] != "") {
                                        $varSfcFormMixto = $value['varSfc'].' %';
                                      }else{
                                        $varSfcFormMixto = $varSinData;
                                      }

                                      if ($value['varIndiceProceso'] != "") {
                                        $varProcesoFormMixto = $value['varIndiceProceso'].' %';
                                      }else{
                                        $varProcesoFormMixto = $varSinData;
                                      }

                                      if ($value['varIndiceExperienca'] != "") {
                                        $varExperienciaFormMixta = $value['varIndiceExperienca'].' %';
                                      }else{
                                        $varExperienciaFormMixta = $varSinData;
                                      }

                                      if ($value['varIndicePromesa'] != "") {
                                        $varPromesa = $value['varIndicePromesa'].' %';
                                      }else{
                                        $varPromesa = $varSinData;
                                      }
                                  ?>
                                    <tr>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varConteoFormMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varScoreFormMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varPecFormMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varPencFormMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varSfcFormMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varProcesoFormMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varExperienciaFormMixta; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varPromesa; ?></label></td>
                                    </tr>
                                  <?php
                                    }
                                  ?>
                                </tbody>
                              </table>
                            </div>
                          </div>  
                        </div>

                        <hr>

                        <div class="row">
                          <div class="col-md-12">
                            

                              <?php
                                $varNombreTablaEquipo = "myTableEquipos_".$varConteoProcesoMixto;
                              ?>

                              <table id="<?php echo $varNombreTablaEquipo; ?>"  class="table table-hover table-bordered" style="margin-top:10px; font-size: 15px;" >
                                <caption><label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Listado de Equipos') ?></label></caption>
                                <thead>
                                  <tr>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Lider') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Asesor') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Llamadas Procesadas') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Total Automatico') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Agente') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Canal') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Valoraciones') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Score') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PEC') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PENC') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'SPC/SFR') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice Proceso') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice Experiencia') ?></label></th>
                                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice P. Marca') ?></label></th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php
                                    $vaListaRtaEquipos = (new \yii\db\Query())
                                          ->select([
                                            'tbl_usuarios.usua_nombre AS varLider',
                                            'tbl_evaluados.name AS varAsesor',
                                            'tbl_dashboardspeechcalls.login_id AS varLogin',
                                            'COUNT(tbl_dashboardspeechcalls.callId) AS varTotalLlamadas',
                                            'COUNT(tbl_ejecucionformularios.id) AS varTotalValoraciones', 
                                            'ROUND(AVG(tbl_ejecucionformularios.score)*100,2) AS varScore',
                                            'ROUND(AVG(tbl_ejecucionformularios.i1_nmcalculo)*100,2) AS varPec',
                                            'ROUND(AVG(tbl_ejecucionformularios.i2_nmcalculo)*100,2) AS varPenc',
                                            'ROUND(AVG(tbl_ejecucionformularios.i3_nmcalculo)*100,2) AS varSfc',
                                            'ROUND(AVG(tbl_ejecucionformularios.i5_nmcalculo)*100,2) AS varIndiceProceso',
                                            'ROUND(AVG(tbl_ejecucionformularios.i6_nmcalculo)*100,2) AS varIndiceExperienca',
                                            'ROUND(AVG(tbl_ejecucionformularios.i7_nmcalculo)*100,2) AS varIndicePromesa'
                                            ])
                                          ->from(['tbl_dashboardspeechcalls']) 

                                          ->join('LEFT OUTER JOIN', 'tbl_evaluados',
                                                  'tbl_evaluados.dsusuario_red = tbl_dashboardspeechcalls.login_id')

                                          ->join('LEFT OUTER JOIN', 'tbl_equipos_evaluados',
                                                  'tbl_equipos_evaluados.evaluado_id = tbl_evaluados.id')

                                          ->join('LEFT OUTER JOIN', 'tbl_equipos',
                                                  'tbl_equipos.id = tbl_equipos_evaluados.equipo_id')

                                          ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                                  'tbl_usuarios.usua_id = tbl_equipos.usua_id')

                                          ->join('LEFT OUTER JOIN', 'tbl_speech_mixta',
                                                  'tbl_speech_mixta.callid = tbl_dashboardspeechcalls.callId')

                                          ->join('LEFT OUTER JOIN', 'tbl_ejecucionformularios',
                                                  'tbl_ejecucionformularios.id = tbl_speech_mixta.formulario_id')

                                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varNombreSpeech])
                                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesMixtas])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varLlamada])
                                          ->groupby(['tbl_dashboardspeechcalls.login_id'])
                                          ->orderby(['tbl_usuarios.usua_nombre'=>SORT_DESC])
                                          ->all();

                                    foreach ($vaListaRtaEquipos as $key => $value) {
                                      $varLoginFormsMixto = $value['varLogin'];

                                      $txtTotalAgentesMixto = (new \yii\db\Query())
                                          ->select(['tbl_ideal_loginresponsabilidad.agente'])
                                          ->from(['tbl_ideal_loginresponsabilidad']) 
                                          ->where(['=','tbl_ideal_loginresponsabilidad.id_dp_cliente',$varIdDpCliente])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.cod_pcrc',$varCodPcrcMixtos])
                                          ->andwhere(['>=','tbl_ideal_loginresponsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                                          ->andwhere(['<=','tbl_ideal_loginresponsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.extension',$varIdExtensionc])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.login_id',$varLoginFormsMixto])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.anulado',0])
                                          ->scalar();

                                      
                                      $varTotalAgentesMixto = $txtTotalAgentesMixto.' %';
                                      

                                      $txtTotalMarcaMixto = (new \yii\db\Query())
                                          ->select(['tbl_ideal_loginresponsabilidad.marca'])
                                          ->from(['tbl_ideal_loginresponsabilidad']) 
                                          ->where(['=','tbl_ideal_loginresponsabilidad.id_dp_cliente',$varIdDpCliente])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.cod_pcrc',$varCodPcrcMixtos])
                                          ->andwhere(['>=','tbl_ideal_loginresponsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                                          ->andwhere(['<=','tbl_ideal_loginresponsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.extension',$varIdExtensionc])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.login_id',$varLoginFormsMixto])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.anulado',0])
                                          ->scalar();

                                      
                                      $varTotalMarcaMixto = $txtTotalMarcaMixto.' %';
                                      

                                      $txtTotalCanalMixto = (new \yii\db\Query())
                                          ->select(['tbl_ideal_loginresponsabilidad.canal'])
                                          ->from(['tbl_ideal_loginresponsabilidad']) 
                                          ->where(['=','tbl_ideal_loginresponsabilidad.id_dp_cliente',$varIdDpCliente])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.cod_pcrc',$varCodPcrcMixtos])
                                          ->andwhere(['>=','tbl_ideal_loginresponsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                                          ->andwhere(['<=','tbl_ideal_loginresponsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.extension',$varIdExtensionc])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.login_id',$varLoginFormsMixto])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.anulado',0])
                                          ->scalar();

                                      
                                      $varTotalCanalMixto = $txtTotalCanalMixto.' %';
                                      

                                      if ($varTotalAgentesMixto != "0" || $varTotalCanalMixto != "0") {
                                        $varTotalAutoMixto = round( (intval($varTotalAgentesMixto)+intval($varTotalCanalMixto))/2,2).' %';
                                      }else{
                                        $varTotalAutoMixto = 0;
                                      }
                                      

                                      
                                      if ($value['varLider'] != "") {
                                        $varLiderFormsMixto = $value['varLider'];
                                      }else{
                                        $varLiderFormsMixto = 'Sin Información';
                                      }

                                      if ($value['varAsesor'] != "") {
                                        $varAsesorFormsMixto = $value['varAsesor'];
                                      }else{
                                        $varAsesorFormsMixto = $varSinData;
                                      }

                                      if ($value['varTotalLlamadas'] != "") {
                                        $varTotalLlamadasFormsMixto = $value['varTotalLlamadas'];
                                      }else{
                                        $varTotalLlamadasFormsMixto = $varSinData;
                                      }

                                      if ($value['varTotalValoraciones'] != "") {
                                        $varTotalValoracionesFormsMixto = $value['varTotalValoraciones'];
                                      }else{
                                        $varTotalValoracionesFormsMixto = $varSinData;
                                      }

                                      if ($value['varScore'] != "") {
                                        $varScoreFormsMixtos = $value['varScore'];
                                      }else{
                                        $varScoreFormsMixtos = $varSinData;
                                      }

                                      if ($value['varPec'] != "") {
                                        $varPecFormsMixto = $value['varPec'].' %';
                                      }else{
                                        $varPecFormsMixto = $varSinData;
                                      }

                                      if ($value['varPenc'] != "") {
                                        $varPencFormsMixto = $value['varPenc'].' %';
                                      }else{
                                        $varPencFormsMixto = $varSinData;
                                      }

                                      if ($value['varSfc'] != "") {
                                        $varSFCFormsMixto = $value['varSfc'].' %';
                                      }else{
                                        $varSFCFormsMixto = $varSinData;
                                      }

                                      if ($value['varIndiceProceso'] != "") {
                                        $varIndiceProcesoFormsMixto = $value['varIndiceProceso'].' %';
                                      }else{
                                        $varIndiceProcesoFormsMixto = $varSinData;
                                      }

                                      if ($value['varIndiceExperienca'] != "") {
                                        $varIndiceExpeFormsMixto = $value['varIndiceExperienca'].' %';
                                      }else{
                                        $varIndiceExpeFormsMixto = $varSinData;
                                      }

                                      if ($value['varIndicePromesa'] != "") {
                                        $varIndicePromesaFormsMixta = $value['varIndicePromesa'].' %';
                                      }else{
                                        $varIndicePromesaFormsMixta = $varSinData;
                                      }

                                  ?>
                                    <tr>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varLiderFormsMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varAsesorFormsMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalLlamadasFormsMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalAutoMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalAgentesMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalCanalMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalValoracionesFormsMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varScoreFormsMixtos; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varPecFormsMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varPencFormsMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varSFCFormsMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varIndiceProcesoFormsMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varIndiceExpeFormsMixto; ?></label></td>
                                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varIndicePromesaFormsMixta; ?></label></td>
                                    </tr>
                                  <?php
                                    }
                                  ?>
                                </tbody>
                              </table>

                              <script type="text/javascript">
                                
                                  $('#'+"<?php echo $varNombreTablaEquipo; ?>").DataTable({
                                    responsive: true,
                                    fixedColumns: true,

                                    select: true,
                                    "language": {
                                      "lengthMenu": "Cantidad de Datos a Mostrar _MENU_",
                                      "zeroRecords": "No se encontraron datos ",
                                      "info": "Mostrando p&aacute;gina _PAGE_ a _PAGES_ de _MAX_ registros",
                                      "infoEmpty": "No hay datos aun",
                                      "infoFiltered": "(Filtrado un _MAX_ total)",
                                      "search": "Buscar:",
                                      "paginate": {
                                        "first":      "Primero",
                                        "last":       "Ultimo",
                                        "next":       "Siguiente",
                                        "previous":   "Anterior"
                                      }
                                    } 
                                  });

                                
                              </script>
                            
                          </div>
                        </div>
                      
                    </div>
                  </div>
                </div>

                <hr>

              <?php

              }

              ?>

            </div>

            <!-- Proceso Automatico -->
            <div id="Automatico" class="w3-container city" style="display:none;">

              <br>

              <?php
              $varNumeroContainer = 0;
              foreach ($varListasClienteIdealP as $key => $value) {
                
                $varCodPcrcAuto = $value['cod_pcrc'];
                $varNombreTablaEquipoAuto = "myTableEquipos_".$varCodPcrcAuto;;

                if ($varIdExtensionc > '1') {
                  $varRnIdealA =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcAuto])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['=','tipoparametro',$varIdExtensionc])
                                ->groupby(['rn'])
                                ->all();
                }else{
                  $varRnIdealA =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcAuto])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['rn'])
                                ->all();
                }

                if (count($varRnIdealA) != 0) {
                  $varArrayRnA = array();
                  foreach ($varRnIdealA as $key => $value) {
                    array_push($varArrayRnA, $value['rn']);
                  }

                  $varExtensionesArraysA = implode("', '", $varArrayRnA);
                  $arrayExtensiones_downA = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysA);
                  $varExtensionesAuto = explode(",", $arrayExtensiones_downA);
                }else{

                  if ($varIdExtensionc > '1') {
                    $varExtA =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcAuto])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['=','tipoparametro',$varIdExtensionc])
                                ->groupby(['ext'])
                                ->all();
                  }else{
                    $varExtA =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcAuto])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['ext'])
                                ->all();
                  }

                  if (count($varExtA) != 0) {
                    $varArrayExtA = array();
                    foreach ($varExtA as $key => $value) {
                      array_push($varArrayExtA, $value['ext']);
                    }

                    $varExtensionesArraysA = implode("', '", $varArrayExtA);
                    $arrayExtensiones_downA= str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysA);
                    $varExtensionesAuto = explode(",", $arrayExtensiones_downA);
                  }else{

                    if ($varIdExtensionc > '1') {
                      $varUsuaA =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcAuto])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['=','tipoparametro',$varIdExtensionc])
                                ->groupby(['usuared'])
                                ->all();
                    }else{
                      $varUsuaA =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcAuto])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['usuared'])
                                ->all();
                    }


                    if (count($varUsuaA) != 0) {
                      $varArrayUsuaA = array();
                      foreach ($varUsuaA as $key => $value) {
                        array_push($varArrayUsuaA, $value['usuared']);
                      }
                      $varExtensionesArraysA = implode("', '", $varArrayUsuaA);
                      $arrayExtensiones_downA = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysA);
                      $varExtensionesAuto = explode(",", $arrayExtensiones_downA);
                    }else{
                      $varExtensionesAuto = "N0A";
                    }
                  }
                }

                $varNombreCodPcrcAuto = (new \yii\db\Query())
                                          ->select(['concat(cod_pcrc," - ",pcrc) as NamePcrc'])
                                          ->from(['tbl_speech_categorias'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['in','cod_pcrc',$varCodPcrcAuto])
                                          ->groupby(['cod_pcrc'])
                                          ->Scalar(); 


                $varListaResponsabilidadesAuto = (new \yii\db\Query())
                                              ->select(['tbl_ideal_indicadores.indicador', 'tbl_ideal_indicadores.cantidad_indicador', 'tbl_ideal_responsabilidad.agente', 'tbl_ideal_responsabilidad.marca', 'tbl_ideal_responsabilidad.canal'])
                                              ->from(['tbl_ideal_responsabilidad'])  

                                              ->join('LEFT OUTER JOIN', 'tbl_ideal_indicadores',
                                                'tbl_ideal_responsabilidad.cod_pcrc = tbl_ideal_indicadores.cod_pcrc AND tbl_ideal_responsabilidad.id_categoriai = tbl_ideal_indicadores.id_categoriai AND tbl_ideal_responsabilidad.anulado = tbl_ideal_indicadores.anulado AND tbl_ideal_responsabilidad.fechainicio = tbl_ideal_indicadores.fechainicio AND tbl_ideal_responsabilidad.fechafin = tbl_ideal_indicadores.fechafin')        

                                              ->where(['=','tbl_ideal_indicadores.id_dp_cliente',$varIdDpCliente])
                                              ->andwhere(['=','tbl_ideal_indicadores.cod_pcrc',$varCodPcrcAuto])
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
                                              ->andwhere(['=','tbl_ideal_indicadores.cod_pcrc',$varCodPcrcAuto])
                                              ->andwhere(['>=','tbl_ideal_indicadores.fechainicio',$varFechainicial.' 05:00:00'])
                                              ->andwhere(['<=','tbl_ideal_indicadores.fechafin',$varFechaFinal.' 05:00:00'])
                                              ->andwhere(['=','tbl_ideal_indicadores.anulado',0])
                                              ->andwhere(['=','tbl_ideal_indicadores.extension',$varIdExtensionc])
                                              ->groupby(['tbl_ideal_indicadores.id_indicadores'])
                                              ->All();

              ?>

              <div class="row">
                <div class="col-md-12">
                  <div class="card1 mb" style="font-size: 15px;">
                  
                    <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', $varNombreCodPcrcAuto) ?></label>

                    <br>

                    <div class="row">
                      <div class="col-md-6">
                        <div class="card1 mb">
                          
                          <table id="myTableResponAuto" class="table table-hover table-bordered" style="margin-top:10px" >
                                <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Resultados Responsabilidades') ?></label></caption>
                            <thead>
                              <tr>
                                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indicador') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Totales') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Agente') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Marca') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Canal') ?></label></th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                                $varNumeroContainer += 1;
                                $prueba = 'doughnut-chart'.$varCodPcrcAuto.'_'.$varNumeroContainer;

                                $varArrayNombreIndicador = array();
                                $varArrayPorcentajes = array();
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

                                  $varIndicador = $value['indicador'];
                                  $varTotalesIndicadorGrafica = $value['cantidad_indicador'];

                                  $varResultados = round((100 - $varTotalesIndicadorGrafica),2);
                                  $arrayNumeros = [$varTotalesIndicadorGrafica, $varResultados];
                                        
                                  array_push($varArrayNombreIndicador, $varNombreIndicador);
                                  array_push($varArrayPorcentajes, $varTotalesIndicadorGrafica);
                              ?>
                                <tr>
                                  <td class="text-center"><label style="font-size: 12px;"><?php echo  $varNombreIndicador; ?></label></td>
                                  <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalesIndicadorGrafica. '%'; ?></label></td> 
                                  <td class="text-center"><label style="font-size: 12px;  color: <?php echo $varTColorAgenteIndicador ?>"><?php echo  $varAgenteIndicador.' %'; ?></label></td>
                                  <td class="text-center"><label style="font-size: 12px;  color: <?php echo $varTColorMarcaIndicador ?>"><?php echo  $varMarcaIndicador.' %'; ?></label></td>
                                  <td class="text-center"><label style="font-size: 12px;  color: <?php echo $varTColorCanalIndicador ?>"><?php echo  $varCanalIndicador.' %'; ?></label></td> 
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

                          <div id="<?php echo $prueba; ?>" class="highcharts-container" style="height: 400px; width: 550px;"></div>
                          <script type="text/javascript">

                              var varvalues = "<?php echo implode($varArrayNombreIndicador, ","); ?>";
                              varvalues = varvalues.split(",");
                                      
                              $('#'+"<?php echo $prueba; ?>").highcharts({

                                chart: {
                                  borderColor: '#DAD9D9',
                                  borderRadius: 7,
                                  borderWidth: 1,
                                  type: 'bar'
                                }, 

                                title: {
                                  text: 'Resumen General',
                                  style: {
                                    color: '#3C74AA'
                                  }
                                },

                                xAxis: {
                                  categories: varvalues,
                                  title: {
                                    text: null
                                  },
                                  crosshair: true
                                },

                                series: [
                                  {
                                    name: 'Porcentaje',
                                    data: [<?= join($varArrayPorcentajes, ',') ?>],
                                    color: '#C148D0'
                                  }
                                ]

                              });
                          </script>
                          
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
                                              ->andwhere(['=','tbl_ideal_variables.cod_pcrc',$varCodPcrcAuto])
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

                    <br>

                    <div class="row">
                      <div class="col-md-12">
                        <div class="card1 mb" style="font-size: 15px;">

                          <table id="<?php echo $varNombreTablaEquipoAuto; ?>"  class="table table-hover table-bordered" style="margin-top:10px; font-size: 15px;" >
                            <caption><label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Listado de Equipos') ?></label></caption>
                            <thead>
                              <tr>
                                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Lider') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Asesor') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Llamadas Procesadas') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Total Automatico') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Agente') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Canal') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Marca') ?></label></th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                                $vaListaRtaEquiposAuto = (new \yii\db\Query())
                                          ->select([
                                            'tbl_usuarios.usua_nombre AS varLiderAuto',
                                            'tbl_evaluados.name AS varAsesorAuto',
                                            'tbl_dashboardspeechcalls.login_id AS varLoginAuto',
                                            'COUNT(tbl_dashboardspeechcalls.callId) AS varTotalLlamadasAuto'
                                            ])
                                          ->from(['tbl_dashboardspeechcalls']) 

                                          ->join('LEFT OUTER JOIN', 'tbl_evaluados',
                                                  'tbl_evaluados.dsusuario_red = tbl_dashboardspeechcalls.login_id')

                                          ->join('LEFT OUTER JOIN', 'tbl_equipos_evaluados',
                                                  'tbl_equipos_evaluados.evaluado_id = tbl_evaluados.id')

                                          ->join('LEFT OUTER JOIN', 'tbl_equipos',
                                                  'tbl_equipos.id = tbl_equipos_evaluados.equipo_id')

                                          ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                                  'tbl_usuarios.usua_id = tbl_equipos.usua_id')

                                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varNombreSpeech])
                                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesAuto])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varLlamada])
                                          ->groupby(['tbl_dashboardspeechcalls.login_id'])
                                          ->orderby(['tbl_usuarios.usua_nombre'=>SORT_DESC])
                                          ->all();

                                foreach ($vaListaRtaEquiposAuto as $key => $value) {
                                  $varLoginFormsAuto = $value['varLoginAuto'];

                                  if ($value['varLiderAuto'] != "") {
                                    $varLiderFormsAuto = $value['varLiderAuto'];
                                  }else{
                                    $varLiderFormsAuto = 'Sin Información';
                                  }

                                  if ($value['varAsesorAuto'] != "") {
                                    $varAsesorFormsAuto = $value['varAsesorAuto'];
                                  }else{
                                    $varAsesorFormsAuto = $varSinData;
                                  }

                                  if ($value['varTotalLlamadasAuto'] != "") {
                                    $varTotalLlamadasFormsAuto = $value['varTotalLlamadasAuto'];
                                  }else{
                                    $varTotalLlamadasFormsAuto = $varSinData;
                                  }

                                  $txtTotalAgentesAutos = (new \yii\db\Query())
                                          ->select(['tbl_ideal_loginresponsabilidad.agente'])
                                          ->from(['tbl_ideal_loginresponsabilidad']) 
                                          ->where(['=','tbl_ideal_loginresponsabilidad.id_dp_cliente',$varIdDpCliente])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.cod_pcrc',$varCodPcrcAuto])
                                          ->andwhere(['>=','tbl_ideal_loginresponsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                                          ->andwhere(['<=','tbl_ideal_loginresponsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.extension',$varIdExtensionc])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.login_id',$varLoginFormsAuto])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.anulado',0])
                                          ->scalar();

                                      
                                  $varTotalAgentesAutos = $txtTotalAgentesAutos.' %';

                                  $txtTotalMarcaAutos = (new \yii\db\Query())
                                          ->select(['tbl_ideal_loginresponsabilidad.marca'])
                                          ->from(['tbl_ideal_loginresponsabilidad']) 
                                          ->where(['=','tbl_ideal_loginresponsabilidad.id_dp_cliente',$varIdDpCliente])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.cod_pcrc',$varCodPcrcAuto])
                                          ->andwhere(['>=','tbl_ideal_loginresponsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                                          ->andwhere(['<=','tbl_ideal_loginresponsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.extension',$varIdExtensionc])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.login_id',$varLoginFormsAuto])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.anulado',0])
                                          ->scalar();

                                      
                                  $varTotalMarcaAutos = $txtTotalMarcaAutos.' %';

                                  $txtTotalCanalAutos = (new \yii\db\Query())
                                          ->select(['tbl_ideal_loginresponsabilidad.canal'])
                                          ->from(['tbl_ideal_loginresponsabilidad']) 
                                          ->where(['=','tbl_ideal_loginresponsabilidad.id_dp_cliente',$varIdDpCliente])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.cod_pcrc',$varCodPcrcAuto])
                                          ->andwhere(['>=','tbl_ideal_loginresponsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                                          ->andwhere(['<=','tbl_ideal_loginresponsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.extension',$varIdExtensionc])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.login_id',$varLoginFormsAuto])
                                          ->andwhere(['=','tbl_ideal_loginresponsabilidad.anulado',0])
                                          ->scalar();

                                      
                                  $varTotalCanalAutos = $txtTotalCanalAutos.' %';
                                      

                                  if ($varTotalAgentesAutos != "0" || $varTotalCanalAutos != "0") {
                                    $varTotalAutoAutos = round( (intval($varTotalAgentesAutos)+intval($varTotalCanalAutos))/2,2).' %';
                                  }else{
                                    $varTotalAutoAutos = 0;
                                  }
                                  
                              ?>
                                <tr>
                                  <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varLiderFormsAuto; ?></label></td>
                                  <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varAsesorFormsAuto; ?></label></td>
                                  <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varTotalLlamadasFormsAuto; ?></label></td>
                                  <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varTotalAutoAutos; ?></label></td>
                                  <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varTotalAgentesAutos; ?></label></td>
                                  <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varTotalCanalAutos; ?></label></td>
                                  <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varTotalMarcaAutos; ?></label></td>
                                </tr>
                              <?php
                                }
                              ?>
                            </tbody>
                          </table>

                          <script type="text/javascript">
                                
                                  $('#'+"<?php echo $varNombreTablaEquipoAuto; ?>").DataTable({
                                    responsive: true,
                                    fixedColumns: true,

                                    select: true,
                                    "language": {
                                      "lengthMenu": "Cantidad de Datos a Mostrar _MENU_",
                                      "zeroRecords": "No se encontraron datos ",
                                      "info": "Mostrando p&aacute;gina _PAGE_ a _PAGES_ de _MAX_ registros",
                                      "infoEmpty": "No hay datos aun",
                                      "infoFiltered": "(Filtrado un _MAX_ total)",
                                      "search": "Buscar:",
                                      "paginate": {
                                        "first":      "Primero",
                                        "last":       "Ultimo",
                                        "next":       "Siguiente",
                                        "previous":   "Anterior"
                                      }
                                    } 
                                  });
                                
                          </script>

                        </div>
                      </div>
                    </div>


                  </div>
                </div>                
              </div>

              <br>

              <?php

              }

              ?>

            </div>

            <!-- Proceso Manual -->
            <div id="Manual" class="w3-container city" style="display:none;">
              
              <?php
              $varNumeroContainerManual = 0;
              foreach ($varListasClienteIdealP as $key => $value) {
                $varCodPcrcManual = $value['cod_pcrc'];

                $varNumeroContainerManual += 1;
                $pruebaManual = 'doughnut-chartManual'.$varCodPcrcManual.'_'.$varNumeroContainerManual;

                $varNombreCodPcrcManual = (new \yii\db\Query())
                                          ->select(['concat(cod_pcrc," - ",pcrc) as NamePcrc'])
                                          ->from(['tbl_speech_categorias'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['in','cod_pcrc',$varCodPcrcManual])
                                          ->groupby(['cod_pcrc'])
                                          ->Scalar(); 

                if ($varIdExtensionc > '1') {
                  $varRnIdealManual =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcManual])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['=','tipoparametro',$varIdExtensionc])
                                ->groupby(['rn'])
                                ->all();
                }else{
                  $varRnIdealManual =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcManual])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['rn'])
                                ->all();
                }

                if (count($varRnIdealManual) != 0) {
                  $varArrayRnManual = array();
                  foreach ($varRnIdealManual as $key => $value) {
                    array_push($varArrayRnManual, $value['rn']);
                  }

                  $varExtensionesArraysManual = implode("', '", $varArrayRnManual);
                  $arrayExtensiones_downManual = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysManual);
                  $varExtensionesManual = explode(",", $arrayExtensiones_downManual);
                }else{

                  if ($varIdExtensionc > '1') {
                    $varExtManual =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcManual])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['=','tipoparametro',$varIdExtensionc])
                                ->groupby(['ext'])
                                ->all();
                  }else{
                    $varExtManual =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcManual])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['ext'])
                                ->all();
                  }

                  if (count($varExtManual) != 0) {
                    $varArrayExtManual = array();
                    foreach ($varExtManual as $key => $value) {
                      array_push($varArrayExtManual, $value['ext']);
                    }

                    $varExtensionesArraysManual = implode("', '", $varArrayExtManual);
                    $arrayExtensiones_downManual = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysManual);
                    $varExtensionesManual = explode(",", $arrayExtensiones_downManual);
                  }else{

                    if ($varIdExtensionc > '1') {
                      $varUsuaManual =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcManual])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['=','tipoparametro',$varIdExtensionc])
                                ->groupby(['usuared'])
                                ->all();
                    }else{
                      $varUsuaManual =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcManual])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['usuared'])
                                ->all();
                    }


                    if (count($varUsuaManual) != 0) {
                      $varArrayUsuaManual = array();
                      foreach ($varUsuaManual as $key => $value) {
                        array_push($varArrayUsuaManual, $value['usuared']);
                      }

                      $varExtensionesArraysManual = implode("', '", $varArrayUsuaManual);
                      $arrayExtensiones_downManual = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysManual);
                      $varExtensionesManual = explode(",", $arrayExtensiones_downManual);
                    }else{
                      $varExtensionesManual = "N0A";
                    }
                  }
                }
              ?>

              <div class="row">
                <div class="col-md-12">

                  <br>
                  
                  <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em> <?= Yii::t('app', $varNombreCodPcrcManual) ?></label>

                    <br>

                    <?php
                      $varListGraficaFormManual = (new \yii\db\Query())
                                          ->select([
                                            'COUNT(tbl_ejecucionformularios.id) AS varConteo',
                                            'ROUND(AVG(tbl_ejecucionformularios.score)*100,2) AS varScore',
                                            'ROUND(AVG(tbl_ejecucionformularios.i1_nmcalculo)*100,2) AS varPec',
                                            'ROUND(AVG(tbl_ejecucionformularios.i2_nmcalculo)*100,2) AS varPenc',
                                            'ROUND(AVG(tbl_ejecucionformularios.i3_nmcalculo)*100,2) AS varSfc',
                                            'ROUND(AVG(tbl_ejecucionformularios.i5_nmcalculo)*100,2) AS varIndiceProceso',
                                            'ROUND(AVG(tbl_ejecucionformularios.i6_nmcalculo)*100,2) AS varIndiceExperienca',
                                            'ROUND(AVG(tbl_ejecucionformularios.i7_nmcalculo)*100,2) AS varIndicePromesa'
                                            ])
                                          ->from(['tbl_ejecucionformularios']) 

                                          ->join('LEFT OUTER JOIN', 'tbl_speech_mixta',
                                                  'tbl_ejecucionformularios.id = tbl_speech_mixta.formulario_id')

                                          ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                                                  'tbl_speech_mixta.callid = tbl_dashboardspeechcalls.callId')

                                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varNombreSpeech])
                                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesManual])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varLlamada])
                                          ->all();


                        $varArrayScoreFormsManual = array();
                        $varArrayPecFormsManual = array();
                        $varArrayPencFormsManual = array();
                        $varArraySFCFormsManual = array();
                        $varArrayIProcesoFormsManual = array();
                        $varArrayIExperienciaFormsManual = array();
                        $varArrayIPromesaFormsManual = array();
                        $varCantidadValoracionesManual = 0;
                        foreach ($varListGraficaFormManual as $key => $value) {
                          $varCantidadValoracionesManual = $value['varConteo'];

                          array_push($varArrayScoreFormsManual, $value['varScore']);
                          array_push($varArrayPecFormsManual, $value['varPec']);
                          array_push($varArrayPencFormsManual, $value['varPenc']);
                          array_push($varArraySFCFormsManual, $value['varSfc']);
                          array_push($varArrayIProcesoFormsManual, $value['varIndiceProceso']);
                          array_push($varArrayIExperienciaFormsManual, $value['varIndiceExperienca']);
                          array_push($varArrayIPromesaFormsManual, $value['varIndicePromesa']);
                        }
                    ?>


                    <div class="row">
                      <div class="col-md-8">
                        <div class="card1 mb">
                          <div id="<?php echo $pruebaManual; ?>" class="highcharts-container" style="height: 250px; width: auto;"></div>
                          <script type="text/javascript">
                            $('#'+"<?php echo $pruebaManual; ?>").highcharts({

                                    chart: {
                                      borderColor: '#DAD9D9',
                                      borderRadius: 7,
                                      borderWidth: 1,
                                      type: 'column'
                                    }, 

                                    title: {
                                      text: 'Resumen General Valoraciones',
                                      style: {
                                        color: '#3C74AA'
                                      }
                                    },

                                    xAxis: {
                                      categories: '-',
                                      title: {
                                        text: null
                                      },
                                      crosshair: true
                                    },

                                    series: [
                                      {
                                        name: 'Score',
                                        data: [<?= join($varArrayScoreFormsManual, ',') ?>],
                                        color: '#827DF9'
                                      },{
                                        name: 'Pec',
                                        data: [<?= join($varArrayPecFormsManual, ',') ?>],
                                        color: '#FBCE52'
                                      },{
                                        name: 'Penc',
                                        data: [<?= join($varArrayPencFormsManual, ',') ?>],
                                        color: '#4298B5'
                                      },{
                                        name: 'Sfc',
                                        data: [<?= join($varArraySFCFormsManual, ',') ?>],
                                        color: '#C148D0'
                                      },{
                                        name: 'Indice Proceso',
                                        data: [<?= join($varArrayIProcesoFormsManual, ',') ?>],
                                        color: '#C6C6C6'
                                      },{
                                        name: 'Indice Experiencia',
                                        data: [<?= join($varArrayIExperienciaFormsManual, ',') ?>],
                                        color: '#559FFF'
                                      },{
                                        name: 'Promesa Marca',
                                        data: [<?= join($varArrayIPromesaFormsManual, ',') ?>],
                                        color: '#4298b5'
                                      }
                                    ]

                            });
                          </script>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="card1 mb">
                          <table id="myTableCantidadesValoracionManual" class="table table-hover table-bordered" style="margin-top:10px" >
                            <caption><label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></em> <?= Yii::t('app', 'Cantidades Valoraciones') ?></label></caption>
                            <thead>
                              <tr>
                                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad valoraciones') ?></label></th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td class="text-center" colspan="1"><label style="font-size: 20px;"><?php echo  $varCantidadValoracionesManual; ?></label></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>                        
                      </div>
                    </div>

                    <br>

                    <div class="row">
                      <div class="col-md-12">
                        <div class="card1 mb"  style="font-size: 15px;">

                          <?php
                            $varNombreTablaEquipoManul = "myTableEquiposManual_".$varCodPcrcManual;
                          ?>

                          <table id="<?php echo $varNombreTablaEquipoManul; ?>" class="table table-hover table-bordered" style="margin-top:10px" >
                            <caption><label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #FFC72C;"></em> <?= Yii::t('app', 'Resultados Equipos') ?></label></caption>
                            <thead>
                              <tr>
                                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
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
                                  $varListaDataArbolsManual = (new \yii\db\Query())
                                                ->select(['*'])
                                                ->from(['tbl_speech_pcrcformularios'])            
                                                ->where(['=','anulado',0])
                                                ->andwhere(['=','cod_pcrc',$varCodPcrcManual])
                                                ->andwhere(['=','id_dp_clientes',$varIdDpCliente])
                                                ->all();

                                  $varArrayArbolsManual = array();
                                  foreach ($varListaDataArbolsManual as $key => $value) {
                                    array_push($varArrayArbolsManual, $value['arbol_id']);
                                  }
                                  $varArray_ArbolManual = implode(", ", $varArrayArbolsManual);
                                  $arrayArboles_downManual = str_replace(array("#", "'", ";", " "), '', $varArray_ArbolManual);
                                  $varArbolesManuales = explode(",", $arrayArboles_downManual);

                                  

                                  $varListadoLideresManual = (new \yii\db\Query())
                                                ->select([
                                                  'tbl_usuarios.usua_id AS varLiderId',
                                                  'tbl_usuarios.usua_nombre AS varLider'
                                                ])
                                                ->from(['tbl_usuarios'])  

                                                ->join('LEFT OUTER JOIN', 'tbl_distribucion_asesores',
                                                  'tbl_usuarios.usua_identificacion = tbl_distribucion_asesores.cedulalider')

                                                ->where(['=','tbl_distribucion_asesores.id_dp_clientes',$varIdDpCliente])
                                                ->groupby(['tbl_distribucion_asesores.cedulalider'])
                                                ->all();


                                  foreach ($varListadoLideresManual as $key => $value) {
                                    $vaNombreLiderManual = $value['varLider'];
                                    $varLider_id = $value['varLiderId'];

                                    $varListadoValoracionLiderManual = (new \yii\db\Query())
                                          ->select([
                                            'COUNT(tbl_ejecucionformularios.id) AS varConteoMA',
                                            'ROUND(AVG(tbl_ejecucionformularios.score)*100,2) AS varScoreMA',
                                            'ROUND(AVG(tbl_ejecucionformularios.i1_nmcalculo)*100,2) AS varPecMA',
                                            'ROUND(AVG(tbl_ejecucionformularios.i2_nmcalculo)*100,2) AS varPencMA',
                                            'ROUND(AVG(tbl_ejecucionformularios.i3_nmcalculo)*100,2) AS varSfcMA',
                                            'ROUND(AVG(tbl_ejecucionformularios.i5_nmcalculo)*100,2) AS varIndiceProcesoMA',
                                            'ROUND(AVG(tbl_ejecucionformularios.i6_nmcalculo)*100,2) AS varIndiceExperiencaMA',
                                            'ROUND(AVG(tbl_ejecucionformularios.i7_nmcalculo)*100,2) AS varIndicePromesaMA'
                                            ])
                                          ->from(['tbl_ejecucionformularios']) 

                                          ->join('LEFT OUTER JOIN', 'tbl_speech_mixta',
                                                  'tbl_ejecucionformularios.id = tbl_speech_mixta.formulario_id')

                                          ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                                                  'tbl_speech_mixta.callid = tbl_dashboardspeechcalls.callId')

                                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varNombreSpeech])
                                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesManual])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varLlamada])                                          
                                          ->andwhere(['=','tbl_ejecucionformularios.usua_id_lider',$value['varLiderId']])
                                          ->andwhere(['in','tbl_ejecucionformularios.arbol_id',$varArbolesManuales])
                                          ->all();

                                    foreach ($varListadoValoracionLiderManual as $key => $value) {

                                      if ($value['varConteoMA'] != "") {
                                        $varConteoFormsManualV = $value['varConteoMA'];
                                      }else{
                                        $varConteoFormsManualV = $varSinData;
                                      }

                                      if ($value['varScoreMA'] != "") {
                                        $varScoreFormManualValo = $value['varScoreMA'];
                                      }else{
                                        $varScoreFormManualValo = $varSinData;
                                      }

                                      if ($value['varPecMA'] != "") {
                                        $varPecFormsManualV = $value['varPecMA'].' %';
                                      }else{
                                        $varPecFormsManualV = $varSinData;
                                      }

                                      if ($value['varPencMA'] != "") {
                                        $varPencFormsManualV = $value['varPencMA'].' %';
                                      }else{
                                        $varPencFormsManualV = $varSinData;
                                      }

                                      if ($value['varSfcMA'] != "") {
                                        $varSFCFormsManualV = $value['varSfcMA'].' %';
                                      }else{
                                        $varSFCFormsManualV = $varSinData;
                                      }

                                      if ($value['varIndiceProcesoMA'] != "") {
                                        $varIndiceProcesoFormsManualV = $value['varIndiceProcesoMA'].' %';
                                      }else{
                                        $varIndiceProcesoFormsManualV = $varSinData;
                                      }

                                      if ($value['varIndiceExperiencaMA'] != "") {
                                        $varIndiceExpeFormsManualV = $value['varIndiceExperiencaMA'].' %';
                                      }else{
                                        $varIndiceExpeFormsManualV = $varSinData;
                                      }

                                      if ($value['varIndicePromesaMA'] != "") {
                                        $varIndicePromesaFormsManualV = $value['varIndicePromesaMA'].' %';
                                      }else{
                                        $varIndicePromesaFormsManualV = $varSinData;
                                      }
                                                                        
                              ?>
                                <tr>

                                  <td class="text-center" colspan="1">

                                    <?php
                                      if ($varConteoFormsManualV != 0) {
                                    ?>
                                      
                                      <?= 
                                        Html::a(Yii::t('app', '<em id="idimage" class="fas fa-paper-plane" style="font-size: 15px; color: #FFC72C;"></em>'),
                                                                'javascript:void(0)',
                                                                [
                                                                    'title' => Yii::t('app', 'Verificar Resultado Formularios'),
                                                                    'onclick' => "   
                                                                        $.ajax({
                                                                            type     :'get',
                                                                            cache    : false,
                                                                            url  : '" . Url::to(['verformulariomanual', 'liderid'=>$varLider_id, 'clienteid'=>$varIdDpCliente, 'codpcrcid'=>$varCodPcrcManual, 'arbolsid'=>$varArray_ArbolManual, 'extensionid'=>$varIdExtensionc, 'llamadaid'=>$varLlamada, 'nombrespeechid'=>$varNombreSpeech, 'fechainicioid'=>$varFechainicial, 'fechafinid'=>$varFechaFinal]) . "',
                                                                            success  : function(response) {
                                                                                $('#ajax_result').html(response);
                                                                            }
                                                                        });
                                                                    return false;",
                                                                ]);
                                      ?>
                                    <?php
                                      }else{
                                    ?>

                                      <label style="font-size: 12px;"><?= Yii::t('app', '--') ?></label>

                                    <?php
                                      }
                                    ?>
                                   

                                  </td>

                                  <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $vaNombreLiderManual; ?></label></td>
                                  <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varConteoFormsManualV; ?></label></td>
                                  <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varScoreFormManualValo; ?></label></td>
                                  <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varPecFormsManualV; ?></label></td>
                                  <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varPencFormsManualV; ?></label></td>
                                  <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varSFCFormsManualV; ?></label></td>
                                  <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varIndiceProcesoFormsManualV; ?></label></td>
                                  <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varIndiceExpeFormsManualV; ?></label></td>
                                  <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varIndicePromesaFormsManualV; ?></label></td>

                                </tr>
                              <?php
                                    }
                                  }
                              ?>                              
                            </tbody>
                          </table>

                          <script type="text/javascript">
                            $('#'+"<?php echo $varNombreTablaEquipoManul; ?>").DataTable({
                                    responsive: true,
                                    fixedColumns: true,

                                    select: true,
                                    "language": {
                                      "lengthMenu": "Cantidad de Datos a Mostrar _MENU_",
                                      "zeroRecords": "No se encontraron datos ",
                                      "info": "Mostrando p&aacute;gina _PAGE_ a _PAGES_ de _MAX_ registros",
                                      "infoEmpty": "No hay datos aun",
                                      "infoFiltered": "(Filtrado un _MAX_ total)",
                                      "search": "Buscar:",
                                      "paginate": {
                                        "first":      "Primero",
                                        "last":       "Ultimo",
                                        "next":       "Siguiente",
                                        "previous":   "Anterior"
                                      }
                                    } 
                            });
                          </script>
                          
                        </div>
                      </div>
                    </div>
                    

                  </div>

                </div>
              </div>

              <br>

              <?php
              }
              ?>

            </div>

            <!-- Proceso Calidad -->
            <div id="Calidad" class="w3-container city" style="display:none;">

              <br>

              <div class="row">
                <div class="col-md-12">
                  <div class="card1 mb">
                    <div class="panel panel-default" style="background-color: #e9f9e8;">
                      <div class="panel-body">
                        <label style="font-size: 14px;"><?= Yii::t('app', ' Actualmente el proceso esta en construccion, Importante tener un historico del proceso de la BD ideal.') ?></label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- Proceso Mejora -->
            <div id="Mejora" class="w3-container city" style="display:none;">

              <br>

              <?php
              $varNumeroContainerMejora = 0;
              foreach ($varListasClienteIdealP as $key => $value) {
                $varCodPcrcMejora = $value['cod_pcrc'];

                $varNumeroContainerMejora += 1;
                $pruebaMejora = 'doughnut-chartManual'.$varCodPcrcMejora.'_'.$varNumeroContainerMejora;

                $varNombreCodPcrcMejora = (new \yii\db\Query())
                                          ->select(['concat(cod_pcrc," - ",pcrc) as NamePcrc'])
                                          ->from(['tbl_speech_categorias'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['in','cod_pcrc',$varCodPcrcMejora])
                                          ->groupby(['cod_pcrc'])
                                          ->Scalar(); 

                $varListaDataArbolsMejora = (new \yii\db\Query())
                                                ->select(['*'])
                                                ->from(['tbl_speech_pcrcformularios'])            
                                                ->where(['=','anulado',0])
                                                ->andwhere(['=','cod_pcrc',$varCodPcrcMejora])
                                                ->andwhere(['=','id_dp_clientes',$varIdDpCliente])
                                                ->all();

                $varArrayArbolsMejora = array();
                foreach ($varListaDataArbolsMejora as $key => $value) {
                  array_push($varArrayArbolsMejora, $value['arbol_id']);
                }
                $varArray_ArbolMejora = implode(", ", $varArrayArbolsMejora);
                $arrayArboles_downMejora = str_replace(array("#", "'", ";", " "), '', $varArray_ArbolMejora);
                $varArbolesMejora = explode(",", $arrayArboles_downMejora);

                $varListaDataCalibraciones = (new \yii\db\Query())
                                                ->select(['*'])
                                                ->from(['tbl_arbols'])            
                                                ->where(['=','tbl_arbols.activo',0])
                                                ->andwhere(['in','tbl_arbols.id',$varArbolesMejora])
                                                ->all();

                $varArrayArbolsCalibraciones = array();
                foreach ($varListaDataCalibraciones as $key => $value) {
                  array_push($varArrayArbolsCalibraciones, $value['arbol_id']);
                }
                $varArray_ArbolCaliMejora = implode(", ", $varArrayArbolsCalibraciones);
                $arrayArboles_downCaliMejora = str_replace(array("#", "'", ";", " "), '', $varArray_ArbolCaliMejora);
                $varArray_ArbolCali = explode(",", $arrayArboles_downCaliMejora);

                $varGraficaFeedbackMejoraMejora = (new \yii\db\Query())
                                                ->select(['tbl_tipofeedbacks.id'])
                                                ->from(['tbl_tipofeedbacks'])  

                                                ->join('LEFT OUTER JOIN', 'tbl_ejecucionfeedbacks',
                                                    'tbl_tipofeedbacks.id = tbl_ejecucionfeedbacks.tipofeedback_id') 

                                                ->join('LEFT OUTER JOIN', 'tbl_ejecucionformularios',
                                                    'tbl_ejecucionfeedbacks.ejecucionformulario_id = tbl_ejecucionformularios.id') 

                                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_ejecucionformularios.arbol_id = tbl_arbols.id') 

                                                ->where(['in','tbl_arbols.id',$varArbolesMejora])
                                                ->andwhere(['=','tbl_arbols.activo',0])
                                                ->andwhere(['between','tbl_ejecucionfeedbacks.created',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                                ->andwhere(['in','tbl_ejecucionformularios.dimension_id',$varDimensionesId])
                                                ->andwhere(['in','tbl_ejecucionfeedbacks.snaviso_revisado',[0,1]])
                                                ->andwhere(['like','tbl_tipofeedbacks.name','ejora'])
                                                ->count();

                $varGraficaFeedbackFelicitacionMejora = (new \yii\db\Query())
                                                ->select(['tbl_tipofeedbacks.id'])
                                                ->from(['tbl_tipofeedbacks'])  

                                                ->join('LEFT OUTER JOIN', 'tbl_ejecucionfeedbacks',
                                                    'tbl_tipofeedbacks.id = tbl_ejecucionfeedbacks.tipofeedback_id') 

                                                ->join('LEFT OUTER JOIN', 'tbl_ejecucionformularios',
                                                    'tbl_ejecucionfeedbacks.ejecucionformulario_id = tbl_ejecucionformularios.id') 

                                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_ejecucionformularios.arbol_id = tbl_arbols.id') 

                                                ->where(['in','tbl_arbols.id',$varArbolesMejora])
                                                ->andwhere(['=','tbl_arbols.activo',0])
                                                ->andwhere(['between','tbl_ejecucionfeedbacks.created',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                                ->andwhere(['in','tbl_ejecucionformularios.dimension_id',$varDimensionesId])
                                                ->andwhere(['in','tbl_ejecucionfeedbacks.snaviso_revisado',[0,1]])
                                                ->andwhere(['like','tbl_tipofeedbacks.name','elicitac'])
                                                ->count();

                $varGraficaFeedbackOtrosMejora = (new \yii\db\Query())
                                                ->select(['tbl_tipofeedbacks.id'])
                                                ->from(['tbl_tipofeedbacks'])  

                                                ->join('LEFT OUTER JOIN', 'tbl_ejecucionfeedbacks',
                                                    'tbl_tipofeedbacks.id = tbl_ejecucionfeedbacks.tipofeedback_id') 

                                                ->join('LEFT OUTER JOIN', 'tbl_ejecucionformularios',
                                                    'tbl_ejecucionfeedbacks.ejecucionformulario_id = tbl_ejecucionformularios.id') 

                                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_ejecucionformularios.arbol_id = tbl_arbols.id') 

                                                ->where(['in','tbl_arbols.id',$varArbolesMejora])
                                                ->andwhere(['=','tbl_arbols.activo',0])
                                                ->andwhere(['between','tbl_ejecucionfeedbacks.created',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                                ->andwhere(['in','tbl_ejecucionformularios.dimension_id',$varDimensionesId])
                                                ->andwhere(['in','tbl_ejecucionfeedbacks.snaviso_revisado',[0,1]])
                                                ->andwhere(['not like','tbl_tipofeedbacks.name','elicitac'])
                                                ->andwhere(['not like','tbl_tipofeedbacks.name','ejora'])
                                                ->count();


                $varListaFeedbacksRevisado = (new \yii\db\Query())
                                                ->select(['tbl_ejecucionfeedbacks.snaviso_revisado'])
                                                ->from(['tbl_ejecucionfeedbacks'])  

                                                ->join('LEFT OUTER JOIN', 'tbl_ejecucionformularios',
                                                    'tbl_ejecucionfeedbacks.ejecucionformulario_id = tbl_ejecucionformularios.id') 

                                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_ejecucionformularios.arbol_id = tbl_arbols.id') 

                                                ->where(['in','tbl_arbols.id',$varArbolesMejora])
                                                ->andwhere(['=','tbl_arbols.activo',0])
                                                ->andwhere(['between','tbl_ejecucionfeedbacks.created',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                                ->andwhere(['in','tbl_ejecucionfeedbacks.snaviso_revisado',[0,1]])
                                                ->andwhere(['in','tbl_ejecucionformularios.dimension_id',$varDimensionesId])
                                                ->All();

                if (count($varListaFeedbacksRevisado) != 0) {
                  $varCantidadFeedbacks = count($varListaFeedbacksRevisado);

                  $varArrayConteoGestionada = 0;
                  $varArrayConteoNoGestionada = 0;
                  $varArrayConteoEstadoOk = 0;
                  $varArrayConteoEstadoKo = 0;
                  foreach ($varListaFeedbacksRevisado as $key => $value) {
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

                                                  ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')     
                                                ->where(['in','tbl_arbols.id',$varArbolesMejora])
                                                ->andwhere(['=','tbl_arbols.activo',0])
                                                ->andwhere(['between','tbl_ejecucionformularios.created',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                                ->andwhere(['in','tbl_ejecucionformularios.dimension_id',$varDimensionesId])
                                                ->All(); 

                  $varCantidadSegundo = count($varListaSegundoclificadorMejora);

                  foreach ($varListaSegundoclificadorMejora as $key => $value) {
                    if ($value['estado_sc'] == 'Abierto') {
                      $varArrayConteoEstadoOk += 1;
                    }
                    if ($value['estado_sc'] == 'Aceptado') {
                      $varArrayConteoEstadoKo += 1;
                    }
                    if ($value['estado_sc'] == 'Rechazado') {
                      $varArrayConteoEstadoFail += 1;
                    }
                    if ($value['estado_sc'] == "Escalado") {
                      $varArrayConteoAceptado += 1;
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
                                      
                                      ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                          'tbl_alertascx.pcrc = tbl_arbols.id')
                                      
                                      ->where(['in','tbl_arbols.id',$varArbolesMejora])
                                      ->andwhere(['=','tbl_arbols.activo',0])
                                      ->andwhere(['between','tbl_alertascx.fecha',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                      ->count();

                  $varConteoMejoraMejora = (new \yii\db\Query())
                                      ->select(['tbl_alertascx.id'])
                                      ->from(['tbl_alertascx'])  
                                      
                                      ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                          'tbl_alertascx.pcrc = tbl_arbols.id')
                                      
                                      ->where(['in','tbl_arbols.id',$varArbolesMejora])
                                      ->andwhere(['=','tbl_arbols.activo',0])
                                      ->andwhere(['between','tbl_alertascx.fecha',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                      ->andwhere(['like','tbl_alertascx.tipo_alerta','ejora'])
                                      ->count();

                  $varConteoFelicitacionMejora = (new \yii\db\Query())
                                      ->select(['tbl_alertascx.id'])
                                      ->from(['tbl_alertascx'])  
                                      
                                      ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                          'tbl_alertascx.pcrc = tbl_arbols.id')
                                      
                                      ->where(['in','tbl_arbols.id',$varArbolesMejora])
                                      ->andwhere(['=','tbl_arbols.activo',0])
                                      ->andwhere(['between','tbl_alertascx.fecha',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                      ->andwhere(['like','tbl_alertascx.tipo_alerta','elicitac'])
                                      ->count();

                  $varConteoSeguimientoMejora = (new \yii\db\Query())
                                      ->select(['tbl_alertascx.id'])
                                      ->from(['tbl_alertascx'])  
                                      
                                      ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                          'tbl_alertascx.pcrc = tbl_arbols.id')
                                      
                                      ->where(['in','tbl_arbols.id',$varArbolesMejora])
                                      ->andwhere(['=','tbl_arbols.activo',0])
                                      ->andwhere(['between','tbl_alertascx.fecha',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                      ->andwhere(['like','tbl_alertascx.tipo_alerta','eguimiento'])
                                      ->count();


                  $varConteoAlCalibraciones = (new \yii\db\Query())
                                      ->select(['tbl_ejecucionformularios.id'])
                                      ->from(['tbl_ejecucionformularios'])

                                      ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                          'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')

                                      ->where(['in','tbl_arbols.arbol_id',$varArray_ArbolCali])
                                      ->andwhere(['like','tbl_arbols.name','alibra'])
                                      ->andwhere(['=','tbl_arbols.activo',0])
                                      ->andwhere(['between','tbl_ejecucionformularios.created',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                      ->andwhere(['in','tbl_ejecucionformularios.dimension_id',$varDimensionesId])
                                      ->count();

                }else{
                  $varCantidadFeedbacks = $varSinData;
                  $varCantidadSegundo = $varSinData;
                  $varArrayConteoGestionada = $varSinData;
                  $varArrayConteoNoGestionada = $varSinData;
                  $varArrayConteoEstadoOk = 0;
                  $varArrayConteoEstadoKo = 0;
                  $varProcentajeGestionFeedback = $varSinData;
                  $varProcentajeGestionSegundo = $varSinData;
                  $varConteoAlertas = $varSinData;
                  $varConteoAlCalibraciones = $varSinData;
                  $varGraficaFeedbackMejoraMejora = 0;
                  $varGraficaFeedbackOtrosMejora = 0;
                  $varGraficaFeedbackFelicitacionMejora = 0;
                  $varConteoMejoraMejora = 0;
                  $varConteoFelicitacionMejora = 0;
                  $varConteoSeguimientoMejora = 0;
                }

                $varConteoMejoraFeedback += 1;
                $varJavaScript = 'chartContainerFeedbackMejora_'.$varCodPcrcMejora.'_'.$varConteoMejoraFeedback;             
                $varJavaScriptSegundo = 'chartContainerSegundoMejora_'.$varCodPcrcMejora.'_'.$varConteoMejoraFeedback;     
                $varJavaScriptAlerta = 'chartContainerAlertaMejora_'.$varCodPcrcMejora.'_'.$varConteoMejoraFeedback;  

              ?>

                <div class="row">
                  <div class="col-md-12">
                    
                    <br>

                    <div class="card1 mb">

                      <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', $varNombreCodPcrcMejora) ?></label>

                      <br>

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
                                    <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varConteoAlertas; ?></label></td>
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

                      <br>

                      <div class="row">

                        <div class="col-md-3">
                          <div class="card1 mb">
                            <div id="<?php echo $varJavaScript; ?>" class="highcharts-container" style="height: 300px;"></div>
                            <script type="text/javascript">

                              $('#'+"<?php echo $varJavaScript; ?>").highcharts({

                                  chart: {
                                    borderColor: '#DAD9D9',
                                    borderRadius: 7,
                                    borderWidth: 1,
                                    type: 'column'
                                  }, 

                                  title: {
                                    text: 'Resultados Por Feedbacks',
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
                                      name: 'Resultados Por Mejora',
                                      data: [<?= $varGraficaFeedbackMejoraMejora ?>],
                                      color: '#FBCE52'
                                    },{
                                      name: 'Resultados Por Felicitacion',
                                      data: [<?= $varGraficaFeedbackFelicitacionMejora ?>],
                                      color: '#4298B5'
                                    },{
                                      name: 'Resultados Por Otros',
                                      data: [<?= $varGraficaFeedbackOtrosMejora ?>],
                                      color: '#C6C6C6'
                                    }
                                  ]

                                });
                            </script>
                          </div>
                        </div>

                        <div class="col-md-3">
                          <div class="card1 mb">
                            <div id="<?php echo $varJavaScriptSegundo; ?>" class="highcharts-container" style="height: 300px;"></div>
                            <script type="text/javascript">

                              $('#'+"<?php echo $varJavaScriptSegundo; ?>").highcharts({

                                  chart: {
                                    borderColor: '#DAD9D9',
                                    borderRadius: 7,
                                    borderWidth: 1,
                                    type: 'column'
                                  }, 

                                  title: {
                                    text: 'Resultados Por Segundo Calificador',
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
                                      name: 'Abiertos',
                                      data: [<?= $varArrayConteoEstadoOk ?>],
                                      color: '#FBCE52'
                                    },{
                                      name: 'Aceptado',
                                      data: [<?= $varArrayConteoEstadoKo ?>],
                                      color: '#4298B5'
                                    },{
                                      name: 'Rechazados',
                                      data: [<?= $varArrayConteoEstadoFail ?>],
                                      color: '#C6C6C6'
                                    },{
                                      name: 'Ecalado',
                                      data: [<?= $varArrayConteoAceptado ?>],
                                      color: '#4298C3'
                                    }
                                  ]

                                });
                            </script>
                          </div>
                        </div>

                        <div class="col-md-3">
                          <div class="card1 mb">
                            <div id="<?php echo $varJavaScriptAlerta; ?>" class="highcharts-container" style="height: 300px;"></div>
                            <script type="text/javascript">

                              $('#'+"<?php echo $varJavaScriptAlerta; ?>").highcharts({

                                  chart: {
                                    borderColor: '#DAD9D9',
                                    borderRadius: 7,
                                    borderWidth: 1,
                                    type: 'column'
                                  }, 

                                  title: {
                                    text: 'Resultados Por Alertas',
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
                                      name: 'Mejora',
                                      data: [<?= $varConteoMejoraMejora ?>],
                                      color: '#FBCE52'
                                    },{
                                      name: 'Felicitación',
                                      data: [<?= $varConteoFelicitacionMejora ?>],
                                      color: '#4298B5'
                                    },{
                                      name: 'Seguimiento',
                                      data: [<?= $varConteoSeguimientoMejora ?>],
                                      color: '#C6C6C6'
                                    }
                                  ]

                                });
                            </script>
                          </div>
                        </div>

                      </div>
                      
                    </div>

                    <br>

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

<?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
?>

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
        data: ["<?php echo $varArrayKonectaGeneral; ?>","<?php echo $varArrayRestanteKGeneral; ?>"],
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
        data: ["<?php echo $varArrayRtaScoreGeneral; ?>","<?php echo $varArrayRestanteScoreGeneral; ?>"],
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
        data: ["<?php echo $varArrayPromedioAutoGeneral; ?>","<?php echo $varArrayRestanteAutoGeneral; ?>"],
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

  });

</script>