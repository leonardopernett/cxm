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
use app\models\SpeechCategorias;
use app\models\Dashboardservicios;

$this->title = 'Dashboard Escuchar +';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Dashboard Escuchar +';

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $varFechasFocalizado = explode(" ", $txtFechas);

    $varFechaInicioFocalizado = $varFechasFocalizado[0];
    $varFechaFinFocalizado = date('Y-m-d',strtotime($varFechasFocalizado[2]));

    $varResponsableAgente = 0;
    $varResponsableMarca = 0;
    $varResponsableCanal = 0;
    $varResponsableGeneral = 0;

    $varFuncionIndicadorOpen = null;
    $varFuncionIndicadorClose = null;

    $arrayVariablesVoice = array();
    $arrayPorcentajesVariableVoice = array();

    $varListCantidad = array();
    $varListDuracion = array();
    $arrayMotivosVoice = array();

    $varListLogin = array();
    $varListCantiVar = array();
    $varListLogin5 = array();
    $varListCantiVar5 = array();
    $varListLogin0 = array();
    $varListCantiVar0 = array();
    $vartotallogin = 0;
    $contador = 0;

    if ($varVariableAsesor != null) {

      $vartotalreg = count($varListAsesores);
      if($vartotalreg > 20){
        $varulti = 9;
        $varprim = 11;
      } else {
        $varulti = 4;
        $varprim = 6;
      }
      $varultimo5 = $vartotalreg - $varulti;
      
      foreach ($varListAsesores as $key => $value) {
        $varlogin = $value['login_id'];      
        $varcantivar = $value['cantidad'];
        $canlogin = substr_count($varlogin,".");
        
        if($canlogin > 0){
          $vartotallogin = $vartotallogin + 1;
        }
        $contador += 1;
        if($contador < $varprim){
          array_push($varListLogin, $varlogin);
          array_push($varListCantiVar, $varcantivar);
        }
        if($contador == $varultimo5){
          $varultimo5 += 1;
          array_push($varListLogin5, $varlogin);
          array_push($varListCantiVar5, $varcantivar);
        }
        if($vartotalreg < 11){
          array_push($varListLogin0, $varlogin);
          array_push($varListCantiVar0, $varcantivar);
        }
      }

    }

?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
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

    .card2 {
            height: 170px;
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
        background-image: url('../../images/Dashboard-Escuchar-+.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

    .lds-ring {
      display: inline-block;
      position: relative;
      width: 100px;
      height: 100px;
    }
    .lds-ring div {
      box-sizing: border-box;
      display: block;
      position: absolute;
      width: 80px;
      height: 80px;
      margin: 10px;
      border: 10px solid #3498db;
      border-radius: 70%;
      animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
      border-color: #3498db transparent transparent transparent;
    }
    .lds-ring div:nth-child(1) {
      animation-delay: -0.45s;
    }
    .lds-ring div:nth-child(2) {
      animation-delay: -0.3s;
    }
    .lds-ring div:nth-child(3) {
      animation-delay: -0.15s;
    }
    @keyframes lds-ring {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
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
    @media (min-width:601px){.w3-col.m1{width:8.33333%}.w3-col.m2{width:16.66666%}.w3-col.m3,.w3-quarter{width:24.99999%}.w3-col.m4,.w3-third{width:50%}
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
<!-- Extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">

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

<!-- Capa Informacion Dash -->
<div id="capaInfoId" class="capaInfo" style="display: inline;">
    
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
        <img src="<?= Url::to("@web/images/servicios/$txtServicioImg.png"); ?>" alt="<?=$txtServicioImg?>" style="height: 150px;">
      </div>
    </div>

    <div class="col-md-3">
      <div class="card2 mb">
        
        <div class="row">
          <div class="col-md-12">
            <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em><?= Yii::t('app', ' Servicio') ?></label><br>
            <label style="font-size: 15px;"><?= Yii::t('app', $varNombreServicioVoice) ?></label>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-md-12">
            <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em><?= Yii::t('app', ' Programa/Pcrc') ?></label><br>
            <label style="font-size: 15px;"><?= Yii::t('app', $varNombrePcrcVoice) ?></label>
          </div>
        </div>

      </div>
    </div>

    <div class="col-md-3">
      <div class="card2 mb">
        
        <div class="row">
          <div class="col-md-12">
            <label style="font-size: 15px;"><em class="fas fa-calendar" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Rango de Fecha') ?></label><br>
            <label style="font-size: 15px;"><?= Yii::t('app', $txtFechas) ?></label>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-md-12">
            <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Párametros Seleccionados') ?></label><br>

            <?php 
            if (count($varListaExtensionesVoice) >= 3) { 
              for ($i=0; $i < count($varListaExtensionesVoice); $i++) { 
                  
            ?>
                <label style="font-size: 9px;"><?= Yii::t('app', $varListaExtensionesVoice[$i]) ?></label>
            <?php 
              } 
            }else{ 
            ?>
              <label style="font-size: 15px;"><?= Yii::t('app', $txtExtensiones) ?></label>
            <?php } ?>
            
          </div>
        </div>

      </div>
    </div>

    <div class="col-md-3">
      <div class="card2 mb">
        
        <div class="row">
          <div class="col-md-12">
            <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Cantidad de Interacciones') ?></label><br>
            <div style="text-align: center;">
              <label style="font-size: 80px;"><?= Yii::t('app', $txtCantidad) ?></label>
            </div>            
          </div>
        </div>

      </div>
    </div>

  </div>

</div>

<hr>

<!-- Capa Dashboard -->
<div id="capaDashbId" class="capaDash" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resultados por Indicadores &  Responsabilidades') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">

        <div class="row">
          
          <div class="col-md-6">

            <table id="myTableResponsabilidades" class="table table-hover table-bordered" style="margin-top:10px" >
              <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #2CA53F;"></em> <?= Yii::t('app', 'Resultados & Gráficas - Indicadores y Responsabilidades -') ?></label></caption>
              <thead>
                <tr>
                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Indicador') ?></label></th>
                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Total') ?></label></th>
                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Agente') ?></label></th>
                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Marca') ?></label></th>
                  <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Canal') ?></label></th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $arrayIndicadoresVoice = array();
                  $arrayPorcentajesIVoice = array();
                  $arraySumAgenteVoice = array();
                  $arraySumMarcaVoice = array();
                  $arraySumCanalVoice = array();
                  $varConteoAgenteVoice = 0;
                  $varConteoMarvaVoice = 0;
                  $varConteoCanalVoice = 0; 
                  foreach ($varListarIndicadoresVoice as $key => $value) {
                    $txtIdIndicadores = $value['idcategoria'];
                    $varNombreIndicador = $value['nombre'];
                    $varTipoParametro = $value['tipoparametro'];
                    $txtTipoFormIndicador = $value['orientacionform'];

                    array_push($arrayIndicadoresVoice, $varNombreIndicador);

                    $varListVariablesI = (new \yii\db\Query())
                                ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform','responsable'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['in','cod_pcrc',$varListaCodPcrcVoice])
                                ->andwhere(['=','idcategorias',2])
                                ->andwhere(['=','tipoindicador',$varNombreIndicador])
                                ->all();

                    $arrayListOfVar = array();
                    $arraYListOfVarMas = array();
                    $arraYListOfVarMenos = array();

                    $varSumarPositivas = 0;
                    $varSumarNegativas = 0;

                    $arrayRAgente = array();
                    $arrayRMarca = array();
                    $arrayRCanal = array();
                    foreach ($varListVariablesI as $key => $value) {
                      $varOrienta = $value['orientacionsmart'];
                      $varResponsable = $value['responsable'];
                      array_push($arrayListOfVar, $value['idcategoria']);

                      if ($varOrienta == 1) {
                        array_push($arraYListOfVarMenos, $value['idcategoria']);
                        $varSumarNegativas = $varSumarNegativas + 1;
                      }else{
                        array_push($arraYListOfVarMas, $value['idcategoria']);
                        $varSumarPositivas = $varSumarPositivas + 1;
                      }

                      if ($varResponsable == 1) {
                        array_push($arrayRAgente, $value['idcategoria']);
                      }else{
                        if ($varResponsable == 2) {
                          array_push($arrayRCanal, $value['idcategoria']);
                        }else{
                          if ($varResponsable == 3) {
                            array_push($arrayRMarca, $value['idcategoria']);
                          }else{
                            $varna = 0;
                          }                    
                        }
                      }
                    }

                    $arrayVariableList = implode(", ", $arrayListOfVar);
                    $arrayVariable_down = str_replace(array("#", "'", ";", " "), '', $arrayVariableList);
                    $arrayVariable = explode(",", $arrayVariable_down);

                    $arrayVariableMasLit = implode(", ", $arraYListOfVarMas);
                    $arrayVariableMas_down = str_replace(array("#", "'", ";", " "), '', $arrayVariableMasLit);
                    $arrayVariableMas = explode(",", $arrayVariableMas_down);

                    $arrayVariableMenosList = implode(", ", $arraYListOfVarMenos);              
                    $arrayVariableMenos_down = str_replace(array("#", "'", ";", " "), '', $arrayVariableMenosList);
                    $arrayVariableMenos = explode(",", $arrayVariableMenos_down);

                    $arrayRAgenteList = implode(", ", $arrayRAgente);
                    $arrayRAgente_down = str_replace(array("#", "'", ";", " "), '', $arrayRAgenteList);
                    $arrayAgente = explode(",", $arrayRAgente_down);

                    $arrayRCanalList = implode(", ", $arrayRCanal);
                    $arrayRCanal_down = str_replace(array("#", "'", ";", " "), '', $arrayRCanalList);
                    $arrayCanal = explode(",", $arrayRCanal_down);

                    $arrayRMarcaList = implode(", ", $arrayRMarca);
                    $arrayRMarca_down = str_replace(array("#", "'", ";", " "), '', $arrayRMarcaList);
                    $arrayMarca = explode(",", $arrayRMarca_down);

                    $varTotalvariables = count($varListVariablesI);

                    if ($varTipoParametro == "2") {

                      if ($varSumarPositivas == $varTotalvariables) {

                        $varconteo = (new \yii\db\Query())
                                          ->select(['callid','SUM(cantproceso)'])
                                          ->from(['tbl_speech_general'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['=','programacliente',$txtServicio])
                                          ->andwhere(['in','extension',$varListaExtensionesVoice])
                                          ->andwhere(['between','fechallamada',$varFechaInicioVoice.' 05:00:00',$varFechaFinTresVoice.' 05:00:00'])
                                          ->andwhere(['in','callid',$varCallidsIndicadoresVoice])
                                          ->andwhere(['in','idindicador',$arrayVariable])
                                          ->andwhere(['in','idvariable',$arrayVariable])
                                          ->groupby(['callid'])
                                          ->count();

                        if ($varconteo == null) {
                          $varconteo = 0;
                        }

                      }else{
                        $varconteo = (new \yii\db\Query())  
                                          ->select(['callid','SUM(cantproceso)'])
                                          ->from(['tbl_speech_general'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['=','programacliente',$txtServicio])
                                          ->andwhere(['in','extension',$varListaExtensionesVoice])
                                          ->andwhere(['between','fechallamada',$varFechaInicioVoice.' 05:00:00',$varFechaFinTresVoice.' 05:00:00'])
                                          ->andwhere(['in','callid',$varCallidsIndicadoresVoice])
                                          ->andwhere(['in','idindicador',$arrayVariableMenos])
                                          ->andwhere(['in','idvariable',$arrayVariableMenos])
                                          ->groupby(['callid'])
                                          ->count();

                        if ($varconteo != null) {
                          $varconteo = round(count($varCallidsIndicadoresVoice) - $varconteo);                
                        }else{
                          $varconteo = 0;
                        }

                      }
                    }else{
                      if ($arrayVariableMas != "") {
                        $varconteo = (new \yii\db\Query())
                                          ->select(['callid','SUM(cantproceso)'])
                                          ->from(['tbl_speech_general'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['=','programacliente',$txtServicio])
                                          ->andwhere(['in','extension',$varListaExtensionesVoice])
                                          ->andwhere(['between','fechallamada',$varFechaInicioVoice.' 05:00:00',$varFechaFinTresVoice.' 05:00:00'])
                                          ->andwhere(['in','callid',$varCallidsIndicadoresVoice])
                                          ->andwhere(['in','idindicador',$arrayVariableMas])
                                          ->andwhere(['in','idvariable',$arrayVariableMas])
                                          ->groupby(['callid'])
                                          ->count();
                      }else{
                        $varconteo = 0;
                      }

                      if ($arrayVariableMenos != "") {
                        $varconteo = $varconteo = (new \yii\db\Query())
                                          ->select(['callid','SUM(cantproceso)'])
                                          ->from(['tbl_speech_general'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['=','programacliente',$txtServicio])
                                          ->andwhere(['in','extension',$varListaExtensionesVoice])
                                          ->andwhere(['between','fechallamada',$varFechaInicioVoice.' 05:00:00',$varFechaFinTresVoice.' 05:00:00'])
                                          ->andwhere(['in','callid',$varCallidsIndicadoresVoice])
                                          ->andwhere(['in','idindicador',$arrayVariableMas])
                                          ->andwhere(['in','idvariable',$arrayVariableMas])
                                          ->groupby(['callid'])
                                          ->count();
                      }else{
                        $varconteo = 0;
                      }
                    }


                    if ($varconteo != 0 && $txtCantidad != 0) {
                      if ($txtTipoFormIndicador == 0) {
                        $txtRtaProcentaje = (round(($varconteo / $txtCantidad) * 100, 1));
                      }else{

                        $txtRtaProcentaje = (100 - (round(($varconteo / $txtCantidad) * 100, 1)));
                      }
                    }else{
                      if ($txtTipoFormIndicador == 0) {
                        $txtRtaProcentaje = 100;
                      }else{
                        $txtRtaProcentaje = 0;
                      }
                    }


                    array_push($arrayPorcentajesIVoice, $txtRtaProcentaje);

                    $varconteoAgente =  (new \yii\db\Query())
                                  ->select(['callid','SUM(cantproceso)'])
                                  ->from(['tbl_speech_general'])            
                                  ->where(['=','anulado',0])
                                  ->andwhere(['=','programacliente',$txtServicio])
                                  ->andwhere(['in','extension',$varListaExtensionesVoice])
                                  ->andwhere(['between','fechallamada',$varFechaInicioVoice.' 05:00:00',$varFechaFinTresVoice.' 05:00:00'])
                                  ->andwhere(['in','callid',$varCallidsIndicadoresVoice])
                                  ->andwhere(['in','idindicador',$arrayAgente])
                                  ->andwhere(['in','idvariable',$arrayAgente])
                                  ->groupby(['callid'])
                                  ->count();

                    if ($varconteoAgente == null) {
                      $varconteoAgente = 0;
                    }

                    if ($varconteoAgente != 0) {
                      if ($txtTipoFormIndicador == 0) {
                        $txtRtaAgente = (round(($varconteoAgente / $txtCantidad) * 100, 1));
                      }else{
                        $txtRtaAgente = (100 - (round(($varconteoAgente / $txtCantidad) * 100, 1)));
                      }
                    }else{
                      $txtRtaAgente = 0;
                    }

                    array_push($arraySumAgenteVoice, $txtRtaAgente);

                    if ($txtRtaAgente == '0') {
                      $varTColorAgente = '#000000';
                    }else{
                      $varConteoAgenteVoice += 1;

                      if ($txtRtaAgente < '80') {
                        $varTColorAgente = '#D01E53';
                      }else{
                        if ($txtRtaAgente >= '90') {
                          $varTColorAgente = '#00968F';
                        }else{
                          $varTColorAgente = '#FFC72C';
                        }
                      }
                    }

                    $varconteoMarca =  (new \yii\db\Query())
                                  ->select(['callid','SUM(cantproceso)'])
                                  ->from(['tbl_speech_general'])            
                                  ->where(['=','anulado',0])
                                  ->andwhere(['=','programacliente',$txtServicio])
                                  ->andwhere(['in','extension',$varListaExtensionesVoice])
                                  ->andwhere(['between','fechallamada',$varFechaInicioVoice.' 05:00:00',$varFechaFinTresVoice.' 05:00:00'])
                                  ->andwhere(['in','callid',$varCallidsIndicadoresVoice])
                                  ->andwhere(['in','idindicador',$arrayMarca])
                                  ->andwhere(['in','idvariable',$arrayMarca])
                                  ->groupby(['callid'])
                                  ->count();

                    if ($varconteoMarca == null) {
                      $varconteoMarca = 0;
                    }
                        
                    if ($varconteoMarca != 0) {
                      if ($txtTipoFormIndicador == 0) {
                        $txtRtaMarca = (round(($varconteoMarca / $txtCantidad) * 100, 1));
                      }else{
                        $txtRtaMarca = (100 - (round(($varconteoMarca / $txtCantidad) * 100, 1)));
                      }
                    }else{
                      $txtRtaMarca = 0;
                    }

                    array_push($arraySumMarcaVoice, $txtRtaMarca);

                    if ($txtRtaMarca == '0') {
                      $varTColorMarca = '#000000';
                    }else{
                      $varConteoMarvaVoice += 1;

                      if ($txtRtaMarca < '80') {
                        $varTColorMarca = '#D01E53';
                      }else{
                        if ($txtRtaMarca >= '90') {
                          $varTColorMarca = '#00968F';
                        }else{
                          $varTColorMarca = '#FFC72C';
                        }
                      }
                    }

                    $varconteoCanal =  (new \yii\db\Query())
                                  ->select(['callid','SUM(cantproceso)'])
                                  ->from(['tbl_speech_general'])            
                                  ->where(['=','anulado',0])
                                  ->andwhere(['=','programacliente',$txtServicio])
                                  ->andwhere(['in','extension',$varListaExtensionesVoice])
                                  ->andwhere(['between','fechallamada',$varFechaInicioVoice.' 05:00:00',$varFechaFinTresVoice.' 05:00:00'])
                                  ->andwhere(['in','callid',$varCallidsIndicadoresVoice])
                                  ->andwhere(['in','idindicador',$arrayCanal])
                                  ->andwhere(['in','idvariable',$arrayCanal])
                                  ->groupby(['callid'])
                                  ->count();

                    if ($varconteoCanal == null) {
                      $varconteoCanal = 0;
                    }

                    if ($varconteoCanal != 0) {
                      if ($txtTipoFormIndicador == 0) {
                        $txtRtaCanal = (round(($varconteoCanal / $txtCantidad) * 100, 1));
                      }else{
                        $txtRtaCanal = (100 - (round(($varconteoCanal / $txtCantidad) * 100, 1)));
                      }
                    }else{
                      $txtRtaCanal = 0;
                    }

                    array_push($arraySumCanalVoice, $txtRtaCanal);

                    if ($txtRtaCanal == '0') {
                      $varTColorCanal = '#000000';
                    }else{
                      $varConteoCanalVoice += 1;

                      if ($txtRtaCanal < '80') {
                        $varTColorCanal = '#D01E53';
                      }else{
                        if ($txtRtaCanal >= '90') {
                          $varTColorCanal = '#00968F';
                        }else{
                          $varTColorCanal = '#FFC72C';
                        }
                      }
                    }
                    
                ?>
                  <tr>
                    <td><label style="font-size: 13px;"><?= Yii::t('app', $varNombreIndicador) ?></label></td>
                    <td class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $txtRtaProcentaje.' %') ?></label></td>
                    <td class="text-center">
                      <label style="font-size: 13px; color: <?php echo $varTColorAgente; ?>">
                        <?php if ($txtRtaAgente != 0) { ?>                          
                          <?= Yii::t('app', $txtRtaAgente.' %') ?>                        
                        <?php }else{ ?>
                          <?= Yii::t('app', '--') ?>
                        <?php } ?>
                      </label>
                    </td>
                    <td class="text-center">
                      <label style="font-size: 13px; color: <?php echo $varTColorMarca; ?>">
                        <?php if ($txtRtaMarca != 0) { ?>                          
                          <?= Yii::t('app', $txtRtaMarca.' %') ?>                        
                        <?php }else{ ?>
                          <?= Yii::t('app', '--') ?>
                        <?php } ?>
                      </label>
                    </td>
                    <td class="text-center">
                      <label style="font-size: 13px; color: <?php echo $varTColorCanal; ?>">
                        <?php if ($txtRtaCanal != 0) { ?>                          
                          <?= Yii::t('app', $txtRtaCanal.' %') ?>                        
                        <?php }else{ ?>
                          <?= Yii::t('app', '--') ?>
                        <?php } ?>
                      </label>
                    </td>
                  </tr>
                <?php
                  }

                  if (array_sum($arraySumAgenteVoice) != 0 && $varConteoAgenteVoice != 0) {
                    $varResponsableAgente = round(array_sum($arraySumAgenteVoice)/$varConteoAgenteVoice,2);
                    $totalvieAgente = 100 - $varResponsableAgente;
                  }else{
                    $varResponsableAgente = 0;
                    $totalvieAgente = 100 - $varResponsableAgente;
                  }

                  if ($varResponsableAgente < '80') {
                    $varTColorA = '#D01E53';
                  }else{
                    if ($varResponsableAgente >= '90') {
                      $varTColorA = '#00968F';
                    }else{
                      $varTColorA = '#FFC72C';
                    }
                  }

                  if (array_sum($arraySumMarcaVoice) != 0 && $varConteoMarvaVoice != 0) {
                    $varResponsableMarca = round(array_sum($arraySumMarcaVoice)/$varConteoMarvaVoice,2);
                    $totalvieMarca = 100 - $varResponsableMarca;
                  }else{
                    $varResponsableMarca = 0;
                    $totalvieMarca = 100 - $varResponsableMarca;
                  }
                  if ($varResponsableMarca < '80') {
                    $varTColorM = '#D01E53';
                  }else{
                    if ($varResponsableMarca >= '90') {
                      $varTColorM = '#00968F';
                    }else{
                      $varTColorM = '#FFC72C';
                    }
                  }

                  if (array_sum($arraySumCanalVoice) != 0 && $varConteoCanalVoice) {
                    $varResponsableCanal = round(array_sum($arraySumCanalVoice)/$varConteoCanalVoice,2);
                    $totalvieCanal = 100 - $varResponsableCanal;
                  }else{
                    $varResponsableCanal = 0;
                    $totalvieCanal = 100 - $varResponsableCanal;
                  }
                  if ($varResponsableCanal < '80') {
                    $varTColorC = '#D01E53';
                  }else{
                    if ($varResponsableCanal >= '90') {
                      $varTColorC = '#00968F';
                    }else{
                      $varTColorC = '#FFC72C';
                    }
                  }

                  $varResponsableGeneral = round(($varResponsableAgente + $varResponsableMarca + $varResponsableCanal) / 3, 2);
                  $totalvieK = 100 - $varResponsableGeneral;
                  if ($varResponsableGeneral < '80') {
                    $varTColorK = '#D01E53';
                  }else{
                    if ($varResponsableGeneral >= '90') {
                      $varTColorK = '#00968F';
                    }else{
                      $varTColorK = '#FFC72C';
                    }
                  }

                ?>
              </tbody>
            </table>

          </div>

          <div class="col-md-6">
            <br>
            <div id="containerIndicadoresVoice" class="highcharts-container" style="height: 300px;"></div>
          </div>

        </div>

        <div class="row">

          <div class="col-md-12">
            <table style="width:100%">
              <caption>...</caption>
                <tr>
                  <th scope="col" class="text-center" style="width: 100px;">
                    <label style="font-size: 15px;"><?php echo "Agente"; ?></label>
                  </th>
                  <th scope="col" class="text-center" style="width: 100px;">
                    <label style="font-size: 15px;"><?php echo "Marca"; ?></label>
                  </th>
                  <th scope="col" class="text-center" style="width: 100px;">
                    <label style="font-size: 15px;"><?php echo "Canal"; ?></label>
                  </th>
                  <th scope="col" class="text-center" style="width: 100px;">
                    <label style="font-size: 15px;"><?php echo "Calidad: General Konecta"; ?></label>
                  </th>
                </tr>
                <tr>
                  <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerA"></canvas></div><span style="font-size: 15px;"><?php echo $varResponsableAgente.' %'; ?></span></td>
                  <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerM"></canvas></div><span style="font-size: 15px;"><?php echo $varResponsableMarca.' %'; ?></span></td> 
                  <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerC"></canvas></div><span style="font-size: 15px;"><?php echo $varResponsableCanal.' %'; ?></span></td> 
                  <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerK"></canvas></div><span style="font-size: 15px;"><?php echo $varResponsableGeneral.' %'; ?></span></td>  
                </tr>
            </table>
          </div>
          
        </div>
        
        
      </div>
    </div>
  </div>

</div>

<hr>

<!-- Capa Indicadores -->
<div id="capaIndiId" class="capaIndi" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Detales por Indicador') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

        <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 20px; color: #615E9B;"></em> <?= Yii::t('app', 'Lista de Indicadores') ?></label>

        <div class="row">

          <div id="capaIndiVarId" class="capaIndiVar" style="display: inline;">
            <div class="col-md-10">
              <?=  $form->field($model, 'idcategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechCategorias::find()->distinct()->where(['=','anulado',0])->andwhere(['in','cod_pcrc',$varListaCodPcrcVoice])->andwhere(['=','idcategorias',1])->orderBy(['nombre'=> SORT_ASC])->all(), 'idcategoria', 'nombre'),
                  [
                    'id' => 'idcapaIndicador',
                    'prompt'=>'Seleccionar Indicador...',
                  ]
                )->label(''); 
              ?>
            </div>

            <div class="col-md-2">
              <?= Html::submitButton(Yii::t('app', 'Buscar Indicador'),
                              ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                  'data-toggle' => 'tooltip',
                                  'onclick' => 'openview();',
                                  'title' => 'Buscar']) 
              ?>
            </div>
          </div>

          <div id="capaIndiVarBuscarId" class="capaIndiVarBuscar" style="display: none;">
            <table align="center">
              <thead>
                <tr>
                  <th class="text-center"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></th>
                  <th><?= Yii::t('app', '') ?></th>
                  <th class="text-justify"><h4><?= Yii::t('app', 'Buscando Variables del indicador previamente seleccionado...') ?></h4></th>
                </tr>            
              </thead>
            </table>
          </div>          
          
        </div>

        <hr>

        <div id="varGrafsVarsid" class="varGrafsVars" style="display: inline;">          

        <?php 
          if ($varIndicadorVariableVoice != null) {

            $varListarIndicadoresV = (new \yii\db\Query())
                                ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['in','cod_pcrc',$varListaCodPcrcVoice])
                                ->andwhere(['=','idcategorias',1])
                                ->andwhere(['=','idcategoria',$varIndicadorVariableVoice])
                                ->all(); 

        ?>
        
          <div class="row">

            <div class="col-md-6">

              <table id="myTableVariable" class="table table-hover table-bordered" style="margin-top:10px" >
                <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #615E9B;"></em> <?= Yii::t('app', 'Resultados de Variables - Indicador de '.$varIndicadorVariableNombreVoice) ?></label></caption>
                <thead>
                  <tr>
                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Responsabilidad') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Variable') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Interacciones') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Porcentaje de Participacion') ?></label></th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                    foreach ($varListarIndicadoresV as $key => $value) {
                      $txtIdIndicadoresV = $value['idcategoria'];
                      $varNombreIndicadorV = $value['nombre'];
                      $varTipoParametroV = $value['tipoparametro'];
                      $txtTipoFormIndicadorV = $value['orientacionform'];

                      $varListVariablesV = (new \yii\db\Query())
                                  ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform','responsable'])
                                  ->from(['tbl_speech_categorias'])            
                                  ->where(['=','anulado',0])
                                  ->andwhere(['in','cod_pcrc',$varListaCodPcrcVoice])
                                  ->andwhere(['=','idcategorias',2])
                                  ->andwhere(['=','tipoindicador',$varNombreIndicadorV])
                                  ->all();

                      
                      foreach ($varListVariablesV as $key => $value) {
                        $varVariables = $value['idcategoria'];
                        $varNombreVariable = $value['nombre'];
                        $varResponsables = $value['responsable'];

                        if ($varResponsables == 1) {
                          $txtResponsable = 'Agente';
                        }elseif ($varResponsables == 2) {
                          $txtResponsable = 'Canal';
                        }elseif ($varResponsables == 3) {
                          $txtResponsable = 'Marca';
                        }else{
                          $txtResponsable = '--';
                        }

                        $varConteoPorVariable =  (new \yii\db\Query())
                                            ->select(['callid','SUM(cantproceso)'])
                                            ->from(['tbl_speech_general'])            
                                            ->where(['=','anulado',0])
                                            ->andwhere(['=','programacliente',$txtServicio])
                                            ->andwhere(['in','extension',$varListaExtensionesVoice])
                                            ->andwhere(['between','fechallamada',$varFechaInicioVoice.' 05:00:00',$varFechaFinTresVoice.' 05:00:00'])
                                            ->andwhere(['in','callid',$varCallidsIndicadoresVoice])
                                            ->andwhere(['in','idindicador',$varVariables])
                                            ->andwhere(['in','idvariable',$varVariables])
                                            ->groupby(['callid'])
                                            ->count();

                        if ($varConteoPorVariable != 0 && $txtCantidad != 0) {
                          if ($txtTipoFormIndicador == 0) {
                            $txtRtaPorcentajeVariable = (round(($varConteoPorVariable / $txtCantidad) * 100, 1));
                          }else{
                            $txtRtaPorcentajeVariable = (100 - (round(($varConteoPorVariable / $txtCantidad) * 100, 1)));
                          }
                        }else{
                          $txtRtaPorcentajeVariable = 0;
                        }

                        array_push($arrayVariablesVoice, $varNombreVariable);
                        array_push($arrayPorcentajesVariableVoice, $txtRtaPorcentajeVariable);

                        $varLlamadasVariable =  (new \yii\db\Query())
                                            ->select(['idcategoria'])
                                            ->from(['tbl_dashboardspeechcalls'])            
                                            ->where(['=','anulado',0])
                                            ->andwhere(['=','servicio',$txtServicio])
                                            ->andwhere(['in','extension',$varListaExtensionesVoice])
                                            ->andwhere(['between','fechallamada',$varFechaInicioVoice.' 05:00:00',$varFechaFinTresVoice.' 05:00:00'])
                                            ->andwhere(['in','idcategoria',$varVariables])
                                            ->groupby(['callid'])
                                            ->count();


                  ?>
                    <tr>
                      <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $txtResponsable) ?></label></td>
                      <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $varNombreVariable) ?></label></td>
                      <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $varLlamadasVariable) ?></label></td>
                      <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $txtRtaPorcentajeVariable.' %') ?></label></td>
                    </tr>
                  <?php
                      }
                    }
                  ?>

                </tbody>
              </table>

            </div>

            <div class="col-md-6">
              <br>
              <div id="containerVariableVoice" class="highcharts-container"></div>
            </div>
            
          </div>

        <?php 
          }
        ?>    

        </div>  

        <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>

</div>

<hr>

<!-- Capa Motivos Dash -->
<div id="capaMotivoId" class="capaMotivo" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Detalles por Motivos de Contacto & Gráfica TMO') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">

        <div class="w3-container">

          <div class="w3-row">

            <a href="javascript:void(0)" onclick="openCity(event, 'ListaMotivo');">
                <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                  <label><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em><strong>  <?= Yii::t('app', 'Lista Resutlados Motivos de Contacto') ?></strong></label>
                </div>
            </a>
            <a href="javascript:void(0)" onclick="openCity(event, 'GraficaMotivo');">
              <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                <label><em class="fas fa-chart-line" style="font-size: 20px; color: #FFC72C;"></em><strong>  <?= Yii::t('app', 'Gráfica Motivos Contacto VS TMO') ?></strong></label>
              </div>
            </a>
            
            <!-- Proceso Lista Motivos -->
            <div id="ListaMotivo" class="w3-container city" style="display:inline;">

              <div id="capaIndiVarMotBuscarId" class="capaIndiVarMotBuscar" style="display: none;">
                <table align="center">
                  <thead>
                    <tr>
                      <th class="text-center"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></th>
                      <th><?= Yii::t('app', '') ?></th>
                      <th class="text-justify"><h4><?= Yii::t('app', 'Buscando Variables del indicador previamente seleccionado...') ?></h4></th>
                    </tr>            
                  </thead>
                </table>
              </div> 

              <div id="capaIndiVarMotDataId" class="capaIndiVarMotData" style="display: inline;">

                <div class="row">
                  <br>
                  <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
                  
                  <div class="col-md-4">
                    <?=  $form->field($model2, 'idspeechcategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechCategorias::find()->distinct()->where("anulado = 0")->andwhere(['in','cod_pcrc',$varListaCodPcrcVoice])->andwhere(['=','idcategorias',1])->orderBy(['nombre'=> SORT_ASC])->all(), 'idspeechcategoria', 'nombre'),
                                          [
                                              'prompt'=>'Seleccionar Indicador...',
                                              'id' => 'idIndicador2',
                                              'onchange' => '
                                                  $.get(
                                                      "' . Url::toRoute('listarvariables') . '", 
                                                      {id: $(this).val()}, 
                                                      function(res){
                                                          $("#requester").html(res);
                                                      }
                                                  );
                                              ',

                                          ]
                                  )->label(''); 
                    ?>
                  </div>

                  <div class="col-md-4">
                    <?= $form->field($model2,'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                                  [],
                                                  [
                                                      
                                                      'prompt' => 'Seleccionar Variable...',
                                                      'id' => 'requester',
                                                  ]
                                              )->label('');
                    ?>
                  </div>

                  <div class="col-md-4 text-center">
                    <?= Html::submitButton(Yii::t('app', 'Buscar Acciones'),
                                ['class' => $model2->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'onclick' => 'varVerificar();',
                                    'title' => 'Buscar']) 
                    ?>
                  </div>

                  <?php ActiveForm::end(); ?>
                </div>

                <hr>

                <div class="row">
                  <div class="col-md-12">
                    
                    <table id="myTableMotivos" class="table table-hover table-bordered" style="margin-top:10px" >
                      <caption> <?= Yii::t('app', '...') ?></caption>
                      <thead>
                        <tr>
                          <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Motivos de Interacciones') ?></label></th>
                          <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', '% de Motivos') ?></label></th>
                          <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cantidad de Motivos') ?></label></th>
                          <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', '% de Motivo Por '.$varVariableMotivoNombre) ?></label></th>
                          <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cantidad de Motivos Por '.$varVariableMotivoNombre) ?></label></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          foreach ($varlistaMotivosTmp as $key => $value) {
                            $varMotivoVoice = intval($value['id_motivo']);
                            $varNombreMotivo = $value['motivo'];
                            $txtRtaPorcentajeMotivo = $value['porcentaje'];
                            $varConteoPorMotivos = $value['cantidadmotivos'];
                            array_push($arrayMotivosVoice, $varNombreMotivo);
                            array_push($varListCantidad, $varConteoPorMotivos);
                            array_push($varListDuracion, $value['duracionllamada']);

                            if ($varVariableMotivo != null) {
                              
                              $varVariableId = intval($varVariableMotivo);
                              $varArregloCategoria = [$varMotivoVoice,$varVariableMotivo];


                              $varConteoPorMotivosVariable =  (new \yii\db\Query())
                                            ->select(['callid'])
                                            ->from(['tbl_dashboardspeechcalls'])            
                                            ->where(['=','anulado',0])
                                            ->andwhere(['=','servicio',$txtServicio])
                                            ->andwhere(['in','extension',$varListaExtensionesVoice])
                                            ->andwhere(['between','fechallamada',$varFechaInicioVoice.' 05:00:00',$varFechaFinTresVoice.' 05:00:00'])
                                            ->andwhere(['in','idcategoria',$varArregloCategoria])
                                            ->groupby(['callid'])
                                            ->count();

                              $varCantidadMotivosVoice =  (new \yii\db\Query())
                                            ->select(['callid'])
                                            ->from(['tbl_dashboardspeechcalls'])            
                                            ->where(['=','anulado',0])
                                            ->andwhere(['=','servicio',$txtServicio])
                                            ->andwhere(['in','extension',$varListaExtensionesVoice])
                                            ->andwhere(['between','fechallamada',$varFechaInicioVoice.' 05:00:00',$varFechaFinTresVoice.' 05:00:00'])
                                            ->andwhere(['=','idcategoria',$varMotivoVoice])
                                            ->count();

                              if ($varConteoPorMotivosVariable != 0 && $varCantidadMotivosVoic != 0) {
                                if ($varConteoPorMotivosVariable != null) {
                                  $txtRtaPorcentajeMotivoVariable = (round(($varConteoPorMotivosVariable / $varCantidadMotivosVoice) * 100, 1));
                                }else{
                                  $txtRtaPorcentajeMotivoVariable = 0;
                                }
                              }else{
                                $txtRtaPorcentajeMotivoVariable = 0;
                              }

                            }else{
                              $varConteoPorMotivosVariable = 0;
                              $txtRtaPorcentajeMotivoVariable = 0;
                            }                         

                            
                        ?>
                          <tr>
                            <td><label style="font-size: 13px;"><?= Yii::t('app', $varNombreMotivo) ?></label></td>
                            <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $txtRtaPorcentajeMotivo.' %') ?></label></td>
                            <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $varConteoPorMotivos) ?></label></td>
                            <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $txtRtaPorcentajeMotivoVariable.' %') ?></label></td>
                            <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $varConteoPorMotivosVariable) ?></label></td>
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

            <!-- Proceso Grafica Motivos -->
            <div id="GraficaMotivo" class="w3-container city" style="display:none;">
              <br>
              <div id="containerTMO" class="highcharts-container"> </div>
              <br>
            </div>            

          </div>

        </div>
        
      </div>
    </div>
  </div>

</div>

<hr>

<!-- Capa ASesores -->
<div id="capaAsesoresId" class="capaAsesores" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Top de Asesores') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
      
        <div id="capaIndiVarAsesorBuscarId" class="capaIndiVarAsesorBuscar" style="display: none;">
          <table align="center">
            <thead>
              <tr>
                <th class="text-center"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></th>
                <th><?= Yii::t('app', '') ?></th>
                <th class="text-justify"><h4><?= Yii::t('app', 'Buscando Variables del indicador previamente seleccionado...') ?></h4></th>
              </tr>            
            </thead>
          </table>
        </div> 

        <div id="capaTopAsesoresId" class="capaTopAsesores" style="display: inline;">
          
          <label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 20px; color: #FFBA3F;"></em> <?= Yii::t('app', 'Buscar Top de Asesores') ?></label>


          <div class="row">
            
            <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
                  
              <div class="col-md-4">
                <?=  $form->field($model3, 'otros', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechCategorias::find()->distinct()->where("anulado = 0")->andwhere(['in','cod_pcrc',$varListaCodPcrcVoice])->andwhere(['=','idcategorias',1])->orderBy(['nombre'=> SORT_ASC])->all(), 'idspeechcategoria', 'nombre'),
                                          [
                                              'prompt'=>'Seleccionar Indicador...',
                                              'id' => 'idIndicador3',
                                              'onchange' => '
                                                  $.get(
                                                      "' . Url::toRoute('listarvariables') . '", 
                                                      {id: $(this).val()}, 
                                                      function(res){
                                                          $("#requester2").html(res);
                                                      }
                                                  );
                                              ',

                                          ]
                                  )->label(''); 
                ?>
              </div>

              <div class="col-md-4">
                <?= $form->field($model3,'usua_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                                  [],
                                                  [
                                                      
                                                      'prompt' => 'Seleccionar Variable...',
                                                      'id' => 'requester2',
                                                  ]
                                              )->label('');
                ?>
              </div>

              <div class="col-md-4 text-center">
                <?= Html::submitButton(Yii::t('app', 'Buscar Top de Asesores'),
                                ['class' => $model3->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'onclick' => 'varVerificarAsesor();',
                                    'title' => 'Buscar']) 
                ?>
              </div>

            <?php ActiveForm::end(); ?>
          </div>

          <hr>

          <div id="capaChartAsesoresID" class="capaChartAsesores" style="display: inline;">
            
            <?php
              if ($varVariableAsesor != null) {              
            ?>

              <div class="row">

                <?php
                  if ($vartotalreg > 10) {                  
                ?>
                    <div class="col-md-6">
                      <div id="containerTOP5" class="highcharts-container"></div>
                    </div>

                    <div class="col-md-6">
                      <div id="containerTOP" class="highcharts-container"></div>
                    </div>
                <?php
                  }else{
                ?>
                    <div class="col-md-12">
                      <div id="containerTOP0" class="highcharts-container"></div>
                    </div>
                <?php
                  }
                ?>
                
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

<!-- Capa Botonera Acciones Dash -->
<div id="capaBtnsId" class="capaBtns" style="display: inline;">

  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones Escuchar +') ?></label>
      </div>
    </div>
  </div>

  <br>
  
  <div class="row">
    
    <div class="col-md-3">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> <?= Yii::t('app', 'Regresar') ?> </label> 
        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
        ?>
      </div>      
    </div>

    <div class="col-md-2">
        <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #FFC72C;"></em> <?= Yii::t('app', 'Exportar Reporte') ?> </label>
            <?= Html::button('Exportar', ['value' => url::to(['categoriasvoice', 'arbol_idV' => $txtServicio, 'parametros_idV' => $txtExtensiones, 'codigoPCRC' => $txtCodPcrcs, 'codparametrizar' => 1, 'indicador' => 0, 'nomFechaI' => $varFechaInicioFocalizado, 'nomFechaF' => $varFechaFinFocalizado]), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                        'data-toggle' => 'tooltip',
                        'title' => 'Exportar', 'style' => 'background-color: #337ab7']) ?> 

            <?php
                Modal::begin([
                  'header' => '<h4>Procesando datos en archivo de excel...</h4>',
                  'id' => 'modal1',
                  // 'size' => 'modal-lg',
                ]);

                echo "<div id='modalContent1'></div>";
                                              
                Modal::end(); 
            ?> 
        </div>
    </div>

    <div class="col-md-2">
        <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #FFC72C;"></em> <?= Yii::t('app', 'Exportar Base') ?> </label>
    
        </div>
    </div>

    <div class="col-md-2">
        <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-phone-square" style="font-size: 15px; color: #FFC72C;"></em> <?= Yii::t('app', 'Análisis Focalizado') ?> </label>
            <?= Html::a('Interacciones',  ['llamadafocalizada', 'varprograma'=>$txtServicio, 'varcodigopcrc'=>$txtCodPcrcs, 'varidcategoria'=>$varidLllamada, 'varextension'=>$txtExtensiones, 'varfechasinicio'=>$varFechaInicioVoice, 'varfechasfin'=>$varFechaFinTresVoice, 'vartcantllamadas'=>$txtCantidad, 'varfechainireal'=>$varFechaInicioFocalizado, 'varfechafinreal'=>$varFechaFinFocalizado,'varcodigos'=>1, 'varaleatorios' => 0], ['class' => 'btn btn-success',
                          'style' => 'background-color: #337ab7', 'target' => "_blank",
                          'data-toggle' => 'tooltip',
                          'title' => 'Buscar llamadas']) 
            ?>    
        </div>
    </div>


  </div>

</div>

<hr>


<script type="text/javascript">

  function openview() {
    var varselectindi = document.getElementById("idcapaIndicador").value;
    var varbtnone = document.getElementById("capaIndiVarId");
    var varbtntwo = document.getElementById("capaIndiVarBuscarId");
    var varbtnthree = document.getElementById("varGrafsVarsid");

    if (varselectindi == "") {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Debes de seleccionar un indicador.","warning");
        return;
    }else{
      varbtnone.style.display = 'none';
      varbtntwo.style.display = 'inline';
      varbtnthree.style.display = 'none';
    }
  };

  function varVerificar(){
    var varidIndicador = document.getElementById("idIndicador2").value;
    var varrequester = document.getElementById("requester").value;

    var varbtnonem = document.getElementById("capaIndiVarMotDataId");
    var varbtntwom = document.getElementById("capaIndiVarMotBuscarId");

    if (varidIndicador == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debes de seleccionar un indicador.","warning");
      return;
    }else{
      if (varrequester == "") {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Debes de seleccionar una variable.","warning");
        return;
      }else{
        varbtnonem.style.display = 'none';
        varbtntwom.style.display = 'inline';
      }
    }
    
  };

  function varVerificarAsesor(){
    var varidIndicador = document.getElementById("idIndicador3").value;
    var varrequester = document.getElementById("requester2").value;

    var varbtnonem = document.getElementById("capaTopAsesoresId");
    var varbtntwom = document.getElementById("capaIndiVarAsesorBuscarId");
    var varbtnthree = document.getElementById("capaChartAsesoresID");

    if (varidIndicador == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debes de seleccionar un indicador.","warning");
      return;
    }else{
      if (varrequester == "") {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Debes de seleccionar una variable.","warning");
        return;
      }else{
        varbtnonem.style.display = 'none';
        varbtntwom.style.display = 'inline';
        varbtnthree.style.display = 'none';
      }
    }
  };

  var chartContainerA = document.getElementById("chartContainerA");
  var chartContainerM = document.getElementById("chartContainerM");
  var chartContainerC = document.getElementById("chartContainerC");
  var chartContainerK = document.getElementById("chartContainerK");

  Chart.defaults.global.defaultFontFamily = "Lato";
  Chart.defaults.global.defaultFontSize = 12;

  var oilDataA = {
    datasets: [
      {
        data: ["<?php echo $varResponsableAgente; ?>","<?php echo $totalvieAgente; ?>"],
        backgroundColor: [
          "<?php echo $varTColorA; ?>",
          "#D7CFC7"
        ]
      }
    ]
  };

  var pieChart = new Chart(chartContainerA, {
    type: 'doughnut',
    data: oilDataA
  });

  var oilDataM = {
    datasets: [
      {
        data: ["<?php echo $varResponsableMarca; ?>","<?php echo $totalvieMarca; ?>"],
        backgroundColor: [
          "<?php echo $varTColorM; ?>",
          "#D7CFC7"
        ]
      }
    ]
  };

  var pieChart = new Chart(chartContainerM, {
    type: 'doughnut',
    data: oilDataM
  });

  var oilDataC = {
    datasets: [
      {
        data: ["<?php echo $varResponsableCanal; ?>","<?php echo $totalvieCanal; ?>"],
        backgroundColor: [
          "<?php echo $varTColorC; ?>",
          "#D7CFC7"
        ]
      }
    ]
  };

  var pieChart = new Chart(chartContainerC, {
    type: 'doughnut',
    data: oilDataC
  });

  var oilDataK = {
        datasets: [
            {
                data: ["<?php echo $varResponsableGeneral; ?>","<?php echo $totalvieK; ?>"],
                backgroundColor: [
                    "<?php echo $varTColorK; ?>",
                    "#D7CFC7"
                ]
            }]
    };

    var pieChart = new Chart(chartContainerK, {
      type: 'doughnut',
      data: oilDataK
    });



  $(function(){
    var Listadot = "<?php echo implode($arrayIndicadoresVoice,",");?>";
    Listadot = Listadot.split(",");

    var ListadoVar = "<?php echo implode($arrayVariablesVoice,",");?>";
    ListadoVar = ListadoVar.split(",");

    var ListadoMotivos = "<?php echo implode($arrayMotivosVoice,",");?>";
    ListadoMotivos = ListadoMotivos.split(",");

    var ListadoL5 = "<?php echo implode($varListLogin5,",");?>";
    ListadoL5 = ListadoL5.split(",");

    var ListadoL = "<?php echo implode($varListLogin,",");?>";
    ListadoL = ListadoL.split(",");

    var ListadoL0 = "<?php echo implode($varListLogin0,",");?>";
    ListadoL0 = ListadoL0.split(",");

    Highcharts.setOptions({
      lang: {
        numericSymbols: null,
        thousandsSep: ','
      }
    });

    $('#containerIndicadoresVoice').highcharts({
      chart: {
        borderColor: '#F0F0F0',
        borderRadius: 7,
        borderWidth: 1,
        type: 'column'
      },

      yAxis: {
        title: {
          text: 'Porcentajes de categorizaci&oacuten'
        }
      }, 

      title: {
        text: '',
        style: {
          color: '#3C74AA'
        }
      },

      xAxis: {
        categories: Listadot,
        title: {
          text: null
        }
      },

      series: [{
        name: 'Indicadores',
        data: [<?= implode($arrayPorcentajesIVoice, ',')?>],
        color: '#4298B5'
      }],

      responsive: {
        rules: [{
          condition: {
            maxWidth: 500
          },
          chartOptions: {
            legend: {
              layout: 'horizontal',
              align: 'center',
              verticalAlign: 'bottom'
            }
          }
        }]
      }
    });

    $('#containerVariableVoice').highcharts({
      chart: {
        borderColor: '#F0F0F0',
        borderRadius: 7,
        borderWidth: 1,
        type: 'bar'
      },

      yAxis: {
        title: {
          text: ''
        }
      }, 

      title: {
        text: '',
        style: {
          color: '#615E9B'
        }
      },

      xAxis: {
        categories: ListadoVar,
        title: {
          text: null
        }
      },

      tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
      },

      series: [{
        name: 'Porcentaje de participaci&oacuten',
        data: [<?= implode($arrayPorcentajesVariableVoice, ',')?>],
        color: '#615E9B'
      }],

      responsive: {
        rules: [{
          condition: {
            maxWidth: 500
          },
          chartOptions: {
            legend: {
              layout: 'horizontal',
              align: 'center',
              verticalAlign: 'bottom'
            }
          }
        }]
      }
    });


    $('#containerTMO').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'column'
            },

            yAxis: [{
              title: {
                text: 'Cantidad de Llamadas'
              }
            },{
              title: {
                text: 'Duracion de Llamadas'
              },
              opposite: true
            }],  

            title: {
              text: 'Motivos de Contacto vs TMO',
            style: {
                    color: '#3C74AA'
              }

            },

            xAxis: {
                  categories: ListadoMotivos,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Cantidad de llamadas',
              data: [<?= implode($varListCantidad, ',')?>],
              color: '#4298B5'
            },{
              name: 'Duración de llamadas',
              data: [<?= implode($varListDuracion, ',')?>],
              color: '#FFc72C',
              type: 'line',
              yAxis: 1
            }],

            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }

          });

    $('#containerTOP5').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'column'
            },

            yAxis: {
              title: {
                text: 'Cantidad de Llamadas'
              }
            }, 

            title: {
              text: 'Top de Asesores MAS categorizadas / ' + '<?php echo $varVariableAsesorNombre; ?>',
            style: {
                    color: '#3C74AA'
              }

            },

            xAxis: {
                  categories: ListadoL5,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Cantidad de llamadas',
              data: [<?= implode($varListCantiVar5, ',')?>],
              color: '#4298B5'
            }],


            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
          });

          $('#containerTOP').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'column'
            },

            yAxis: {
              title: {
                text: 'Cantidad de Llamadas'
              }
            }, 

            title: {
              text: 'Top de Asesores MENOS categorizadas / ' + '<?php echo $varVariableAsesorNombre; ?>',
            style: {
                    color: '#ffa126'
              }

            },

            xAxis: {
                  categories: ListadoL,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Cantidad de llamadas',
              data: [<?= implode($varListCantiVar, ',')?>],
              color: '#ffa126'
            }],


            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }

          });

          $('#containerTOP0').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'column'
            },

            yAxis: {
              title: {
                text: 'Cantidad de Llamadas'
              }
            }, 

            title: {
              text: 'Top de Asesores categorizadas / ' + '<?php echo $varVariableAsesorNombre; ?>',
            style: {
                    color: '#4298B5'
              }

            },

            xAxis: {
                  categories: ListadoL0,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Cantidad de llamadas',
              data: [<?= implode($varListCantiVar0, ',')?>],
              color: '#ffa126'
            }],


            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }

          });


  });

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

    $(document).ready( function () {
        $('#myTableVariable').DataTable({
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

        $('#myTableMotivos').DataTable({
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
    });

</script>