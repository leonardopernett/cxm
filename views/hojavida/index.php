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
use app\models\Dashboardcategorias;
use app\models\Dashboardservicios;

$this->title = 'Gestor de Clientes';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Gestor de Clientes';

  $template = '<div class="col-md-12">'
  . ' {input}{error}{hint}</div>';

  $sessiones = Yii::$app->user->identity->id;

  $rol =  new Query;
  $rol    ->select(['tbl_roles.role_id'])
          ->from('tbl_roles')
          ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                	'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
          ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                  'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
          ->where(['=','tbl_usuarios.usua_id',$sessiones]);                      
  $command = $rol->createCommand();
  $roles = $command->queryScalar();

  $varPermisos = (new \yii\db\Query())
                ->select(['*'])
                ->from(['tbl_hojavida_permisosacciones'])
                ->where(['=','usuario_registro',$sessiones]) 
                ->andwhere(['=','anulado',0])
                ->all();

  $varEliminar = null;
  $varResumen = null;
  $varInformacion = null;
  $varCargar = null;
  $varEditar = null;
  foreach ($varPermisos as $key => $value) {
    $varEliminar = $value['hveliminar'];
    $varResumen = $value['hvverresumen'];
    $varInformacion = $value['hvdatapersonal'];
    $varCargar = $value['hvcasrgamasiva'];
    $varEditar = $value['hveditar'];
  }
  
  $varArrayCiudadCliente = array();
  $varArrayConteoCliente = array();

?>
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
   	font-family: "Nunito";
    font-size: 150%;    
    text-align: left;    
  }

  .card2 {
    height: 355px;
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
    font-family: "Nunito",sans-serif;
    font-size: 150%;    
    text-align: left;    
  }

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Gestor_de_Clientes.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
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
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >


<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>

<br>
<br>

<!-- Capa Seleccion y Procesos -->
<div id="capaIdPrincipal" class="capaPrincipal" style="display: inline;">
  
  <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="row">
      <div class="col-md-12">
        <div class="card1 mb">         
                
          <div class="w3-container">
            
            <div class="w3-row">

              <a href="javascript:void(0)" onclick="openCity(event, 'Resumen');">
                <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                  <label><em class="fas fa-chart-bar" style="font-size: 20px; color: #827DF9;"></em><strong>  <?= Yii::t('app', 'Resumen General') ?></strong></label>
                </div>
              </a>
              <a href="javascript:void(0)" onclick="openCity(event, 'Contactos');">
                <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                  <label><em class="fas fa-users" style="font-size: 20px; color: #C148D0;"></em><strong>  <?= Yii::t('app', 'Contactos') ?></strong></label>
                </div>
              </a>
              <a href="javascript:void(0)" onclick="openCity(event, 'Anexos');">
                <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                  <label><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><strong>  <?= Yii::t('app', 'Anexo del Contrato') ?></strong></label>
                </div>
              </a>

            </div>

            <!-- Proceso de ver graficas del resumen de la data de clientes -->
            <div id="Resumen" class="w3-container city" style="display:inline;">

              <br>
              <div class="row">
                <div class="col-md-12">

                  <!-- Proceso Total Servicios -->
                  <div class="row">
                        
                        <div class="col-md-3">
                            <div class="card1 mb">
                                <table id="myTableResultadoClientes" class="table table-hover table-bordered" style="margin-top:20px">
                                    <caption><label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Resultados Por Clientes') ?></label></caption>
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Ciudad') ?></label></th>
                                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Total') ?></label></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $varSumarClientes = 0;
                                        foreach ($varListarClientes as $key => $value) {
                                            $varCiudadCliente = $value['ciudadclasificacion'];

                                            $varConteoClientes = (new \yii\db\Query())
                                                ->select([
                                                  'tbl_hojavida_datapersonal.hv_idpersonal'])
                                                ->from(['tbl_hojavida_datapersonal']) 

                                                ->join('INNER JOIN', 'tbl_hojavida_dataacademica', 
                                                  ' tbl_hojavida_dataacademica.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal') 

                                                ->join('INNER JOIN', 'tbl_hojavida_dataclasificacion', 
                                                  'tbl_hojavida_dataclasificacion.hv_idclasificacion = tbl_hojavida_datapersonal.clasificacion') 

                                                ->where(['=','tbl_hojavida_datapersonal.anulado',0])
                                                ->andwhere(['=','tbl_hojavida_dataacademica.activo',1])
                                                ->andwhere(['=','tbl_hojavida_datapersonal.clasificacion',$value['hv_idclasificacion']])
                                                ->groupby(['tbl_hojavida_datapersonal.hv_idpersonal'])
                                                ->count();

                                            $varSumarClientes += $varConteoClientes;

                                            array_push($varArrayCiudadCliente, $varCiudadCliente);
                                            array_push($varArrayConteoCliente, $varConteoClientes);
                                        ?>
                                        <tr>
                                            <td><label style="font-size: 13px;"><?= Yii::t('app', $varCiudadCliente) ?></label></td>
                                            <td><label style="font-size: 13px;"><?= Yii::t('app', $varConteoClientes) ?></label></td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <td><label style="font-size: 13px;"><?= Yii::t('app', 'Total') ?></label></td>
                                            <td><label style="font-size: 13px;"><?= Yii::t('app', $varSumarClientes) ?></label></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card1 mb">
                                <table id="myTableResultadoDecisores" class="table table-hover table-bordered" style="margin-top:20px">
                                    <caption><label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Resultados Por Decisores') ?></label></caption>
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Ciudad') ?></label></th>
                                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Total') ?></label></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $varSumarDecisores = 0;
                                        foreach ($varListarClientes as $key => $value) {
                                            $varCiudadDecisores = $value['ciudadclasificacion'];

                                            $varConteoDecisores = (new \yii\db\Query())
                                                ->select([
                                                  'tbl_hojavida_datapersonal.hv_idpersonal'])
                                                ->from(['tbl_hojavida_datapersonal']) 

                                                ->join('INNER JOIN', 'tbl_hojavida_dataacademica', 
                                                  ' tbl_hojavida_dataacademica.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal') 

                                                ->join('INNER JOIN', 'tbl_hojavida_dataclasificacion', 
                                                  'tbl_hojavida_dataclasificacion.hv_idclasificacion = tbl_hojavida_datapersonal.clasificacion') 

                                                ->join('INNER JOIN', 'tbl_hojavida_datalaboral', 
                                                  'tbl_hojavida_datalaboral.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal') 

                                                ->join('INNER JOIN', 'tbl_hojavida_datatipoafinidad', 
                                                  'tbl_hojavida_datatipoafinidad.hv_idtipoafinidad = tbl_hojavida_datalaboral.tipo_afinidad')

                                                ->where(['=','tbl_hojavida_datapersonal.anulado',0])
                                                ->andwhere(['=','tbl_hojavida_dataacademica.activo',1])
                                                ->andwhere(['=','tbl_hojavida_datapersonal.clasificacion',$value['hv_idclasificacion']])
                                                ->andwhere(['=','tbl_hojavida_datatipoafinidad.hv_idtipoafinidad',1])
                                                ->groupby(['tbl_hojavida_datapersonal.hv_idpersonal'])
                                                ->count();

                                            $varSumarDecisores += $varConteoDecisores;
                                        ?>
                                        <tr>
                                            <td><label style="font-size: 13px;"><?= Yii::t('app', $varCiudadDecisores) ?></label></td>
                                            <td><label style="font-size: 13px;"><?= Yii::t('app', $varConteoDecisores) ?></label></td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <td><label style="font-size: 13px;"><?= Yii::t('app', 'Total') ?></label></td>
                                            <td><label style="font-size: 13px;"><?= Yii::t('app', $varSumarDecisores) ?></label></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card1 mb">
                                <table id="myTableResultadoEstrategicos" class="table table-hover table-bordered" style="margin-top:20px">
                                    <caption><label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Resultados Por Clientes Estratégicos') ?></label></caption>
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Ciudad') ?></label></th>
                                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Total') ?></label></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $varSumarEstrategico = 0;
                                        foreach ($varListarClientes as $key => $value) {
                                            $varCiudadEstrategico= $value['ciudadclasificacion'];

                                            $varConteoEstrategico = (new \yii\db\Query())
                                                ->select([
                                                  'tbl_hojavida_datapersonal.hv_idpersonal'])
                                                ->from(['tbl_hojavida_datapersonal']) 

                                                ->join('INNER JOIN', 'tbl_hojavida_dataacademica', 
                                                  ' tbl_hojavida_dataacademica.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal') 

                                                ->join('INNER JOIN', 'tbl_hojavida_dataclasificacion', 
                                                  'tbl_hojavida_dataclasificacion.hv_idclasificacion = tbl_hojavida_datapersonal.clasificacion') 

                                                ->join('INNER JOIN', 'tbl_hojavida_datalaboral', 
                                                  'tbl_hojavida_datalaboral.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal') 

                                                ->join('INNER JOIN', 'tbl_hojavida_datanivelafinidad', 
                                                  'tbl_hojavida_datanivelafinidad.hv_idinvelafinidad = tbl_hojavida_datalaboral.nivel_afinidad')

                                                ->where(['=','tbl_hojavida_datapersonal.anulado',0])
                                                ->andwhere(['=','tbl_hojavida_dataacademica.activo',1])
                                                ->andwhere(['=','tbl_hojavida_datapersonal.clasificacion',$value['hv_idclasificacion']])
                                                ->andwhere(['=','tbl_hojavida_datanivelafinidad.hv_idinvelafinidad',1])
                                                ->groupby(['tbl_hojavida_datapersonal.hv_idpersonal'])
                                                ->count();

                                            $varSumarEstrategico += $varConteoEstrategico;
                                        ?>
                                        <tr>
                                            <td><label style="font-size: 13px;"><?= Yii::t('app', $varCiudadEstrategico) ?></label></td>
                                            <td><label style="font-size: 13px;"><?= Yii::t('app', $varConteoEstrategico) ?></label></td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <td><label style="font-size: 13px;"><?= Yii::t('app', 'Total') ?></label></td>
                                            <td><label style="font-size: 13px;"><?= Yii::t('app', $varSumarEstrategico) ?></label></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card1 mb">
                                <table id="myTableResultadoOperativos" class="table table-hover table-bordered" style="margin-top:20px">
                                    <caption><label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Resultados Por Clientes Opertivos') ?></label></caption>
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Ciudad') ?></label></th>
                                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Total') ?></label></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $varSumarOperativo = 0;
                                        foreach ($varListarClientes as $key => $value) {
                                            $varCiudadOperativo = $value['ciudadclasificacion'];

                                            $varConteoOperativo = (new \yii\db\Query())
                                                ->select([
                                                  'tbl_hojavida_datapersonal.hv_idpersonal'])
                                                ->from(['tbl_hojavida_datapersonal']) 

                                                ->join('INNER JOIN', 'tbl_hojavida_dataacademica', 
                                                  ' tbl_hojavida_dataacademica.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal') 

                                                ->join('INNER JOIN', 'tbl_hojavida_dataclasificacion', 
                                                  'tbl_hojavida_dataclasificacion.hv_idclasificacion = tbl_hojavida_datapersonal.clasificacion') 

                                                ->join('INNER JOIN', 'tbl_hojavida_datalaboral', 
                                                  'tbl_hojavida_datalaboral.hv_idpersonal = tbl_hojavida_datapersonal.hv_idpersonal') 

                                                ->join('INNER JOIN', 'tbl_hojavida_datanivelafinidad', 
                                                  'tbl_hojavida_datanivelafinidad.hv_idinvelafinidad = tbl_hojavida_datalaboral.nivel_afinidad')

                                                ->where(['=','tbl_hojavida_datapersonal.anulado',0])
                                                ->andwhere(['=','tbl_hojavida_dataacademica.activo',1])
                                                ->andwhere(['=','tbl_hojavida_datapersonal.clasificacion',$value['hv_idclasificacion']])
                                                ->andwhere(['=','tbl_hojavida_datanivelafinidad.hv_idinvelafinidad',2])
                                                ->groupby(['tbl_hojavida_datapersonal.hv_idpersonal'])
                                                ->count();

                                            $varSumarOperativo += $varConteoOperativo;
                                        ?>
                                        <tr>
                                            <td><label style="font-size: 13px;"><?= Yii::t('app', $varCiudadOperativo) ?></label></td>
                                            <td><label style="font-size: 13px;"><?= Yii::t('app', $varConteoOperativo) ?></label></td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                        <tr>
                                            <td><label style="font-size: 13px;"><?= Yii::t('app', 'Total') ?></label></td>
                                            <td><label style="font-size: 13px;"><?= Yii::t('app', $varSumarOperativo) ?></label></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
              </div>

              <hr>

              <div class="row">
                <div class="col-md-12">                  

                  <!-- Procesos en graficas -->
                  <div class="row">
                    
                    <div class="col-md-6">
                      <div class="card2 mb">
                        <label  style="font-size: 15px;" ><em class="fas fa-chart-bar" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Gráfica: Resumen General') ?></label>
                        <div id="containerResumen" class="highcharts-container" style="height: 300px;"></div>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="card2 mb">
                        <label  style="font-size: 15px;" ><em class="fas fa-chart-bar" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Gráfica: Resultados Por Cliente') ?></label>
                        <div id="chartContainerCiudadCliente" class="highcharts-container" style="height: 300px;"></div>
                      </div>
                    </div>

                  </div>

                </div>
              </div>

              <hr>

              <div class="row">
                <div class="col-md-12">

                  <!-- Procesos en graficas parte especificas-->
                  <div class="row">
                    
                    <div class="col-md-6">
                      <div class="card2 mb">
                        <label  style="font-size: 15px;" ><em class="fas fa-chart-bar" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Gráfica: Procesos Por Directores') ?></label>
                        <div id="chartContainerDirector" class="highcharts-container" style="height: 300px;"></div>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="card2 mb">
                        <label  style="font-size: 15px;" ><em class="fas fa-chart-bar" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Gráfica: Procesos Por Clientes') ?></label>
                        <div id="chartContainerClientes" class="highcharts-container" style="height: 300px;"></div>
                      </div>
                    </div>

                  </div>

                </div>
              </div>

              <hr>

            </div>

            <!-- Proceso para generar y poder ver los contactos generados en el sistema -->
            <div id="Contactos" class="w3-container city" style="display:none;">

              <br>
              <div class="row">
                <div class="col-md-12">

                  <div class="row">
                    
                    <?php if ($varInformacion == 1) { ?>
                      <div class="col-md-6">
                        <div class="card1 mb">
                          <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Crear Contacto') ?></label>
                          <?= Html::a('Aceptar',  ['informacionpersonal'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Informacion personal']) 
                          ?>
                        </div>
                      </div>
                    <?php } ?>

                    <?php if ($varCargar == 1) { ?>
                      <div class="col-md-6">
                        <div class="card1 mb">
                          <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Carga Masiva') ?></label>
                          <a href="" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal3"><?= Yii::t('app', ' Aceptar') ?></a>
                        </div>
                      </div>
                    <?php } ?>

                  </div>

                  <hr>

                  <div class="row">
                    
                    <div class="col-md-12">
                      

                        <label style="font-size: 15px;"><em class="fas fa-address-book" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Listado') ?></label>

                        <?php if ($sessiones != "0") { ?>
                          <div class="row">
                            <div class="col-md-6">
                              <a href=""  class="btn btn-success" data-toggle="modal" data-target="#exampleModal4"><?= Yii::t('app', ' Exportar Usuarios ') ?><em class="fa fa-file-archive" aria-hidden="true"></em>
                              </a>

                              <a href=""  class="btn btn-success" data-toggle="modal" data-target="#exampleModal5"><?= Yii::t('app', ' Exportar Eventos ') ?><em class="fa fa-file-archive" aria-hidden="true"></em>
                              </a>
                            </div>
                          </div>

                          <br>

                        <?php } ?>

                        <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
                          <caption><?= Yii::t('app', '.') ?></caption>
                          <thead>
                            <tr>
                              <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                              <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Director') ?></label></th>
                              <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Gerente') ?></label></th>
                              <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cliente') ?></label></th>
                              <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Programa') ?></label></th>
                              <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo') ?></label></th>
                              <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nivel') ?></label></th>
                              <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Contacto') ?></label></th>
                              <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cargo') ?></label></th>
                              <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Pais') ?></label></th>
                              <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Estado') ?></label></th>
                              <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Susceptible a encuestar') ?></label></th>
                              <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Sociedad') ?></label></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php 
                              foreach ($dataProviderhv as $key => $value) {
                                $varIdHv = $value['idHojaVida'];
                                $varCliente = $value['cliente'];
                                $varTipo = $value['tipo'];
                                $varNivel = $value['nivel'];
                                $varNombre = $value['nombre_full'];
                                $varRol = $value['rol'];
                                $varPais = $value['pais'];
                                $varEstado = $value['estado'];
                                $varSuceptible = $value['suceptible'];
                                $varSociedades = $value['sociedad'];
                                
                                $paramspcrc = [':codpcrc' => $varIdHv ];
                                $varVerifica = Yii::$app->db->createCommand('
                                  SELECT GROUP_CONCAT(cc.cod_pcrc," - ",cc.pcrc SEPARATOR "; ") AS Programa
                                    FROM tbl_hojavida_datapcrc dc 
                                    INNER JOIN tbl_proceso_cliente_centrocosto cc ON 
                                      dc.cod_pcrc = cc.cod_pcrc
                                    WHERE dc.hv_idpersonal = :codpcrc')->bindValues($paramspcrc)->queryScalar(); 

                                $varlistdirector = Yii::$app->db->createCommand('
                                  SELECT  cc.director_programa  AS Programa
                                    FROM tbl_hojavida_datadirector dc 
                                      INNER JOIN tbl_proceso_cliente_centrocosto cc ON 
                                        dc.ccdirector = cc.documento_director
                                      WHERE dc.hv_idpersonal = :codpcrc GROUP BY cc.director_programa')->bindValues($paramspcrc)->queryAll();
                                $vararraydirector = array();
                                foreach ($varlistdirector as $key => $value) {
                                  array_push($vararraydirector, $value['Programa']);
                                }
                                $varDirectores = implode("; ", $vararraydirector);

                                $varlistgerente = Yii::$app->db->createCommand('
                                  SELECT  cc.gerente_cuenta  AS Programagerente
                                    FROM tbl_hojavida_datagerente dc 
                                      INNER JOIN tbl_proceso_cliente_centrocosto cc ON 
                                        cc.documento_gerente = dc.ccgerente
                                      WHERE dc.hv_idpersonal = :codpcrc GROUP BY cc.gerente_cuenta')->bindValues($paramspcrc)->queryAll();
                                $vararraygerente = array();
                                foreach ($varlistgerente as $key => $value) {
                                  array_push($vararraygerente, $value['Programagerente']);
                                }
                                $varGerentes = implode("; ", $vararraygerente);

                                if ($varSuceptible == 1) {
                                  $varSuceptible = 'No';
                                }else{
                                  $varSuceptible = 'Si';
                                }

                            ?>
                              <tr>
                                <td class="text-center">
                                  <?= Html::a('<em class="fas fa-search" style="font-size: 12px; color: #B833FF;"></em>',  ['viewinfo','idinfo' => $varIdHv], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700; border-color: #4298b500 !important; color:#000000;", 'title' => 'Ver datos']) ?>

                                <?php if($varEditar == 1) { ?>
                                  <?= Html::a('<em class="fas fa-edit" style="font-size: 12px; color: #495057;"></em>',  ['editinfo','idinfo' => $varIdHv], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700; border-color: #4298b500 !important; color:#000000;", 'title' => 'Editar datos']) ?>
                                <?php } ?>

                                <?php if ($varEliminar == 1) { ?>

                                  <?= Html::a('<em class="fas fa-trash" style="font-size: 12px; color: #FC4343;"></em>',  ['deleteinfo','idinfo' => $varIdHv], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700; border-color: #4298b500 !important; color:#000000;", 'title' => 'Eliminar datos']) ?>
                                <?php } ?>
                                </td>
                                <td><label style="font-size: 12px;"><?php echo  $varDirectores; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varGerentes; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varCliente; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varVerifica; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varTipo; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varNivel; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varNombre; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varRol; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varPais; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varEstado; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varSuceptible; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varSociedades; ?></label></td>
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

              <hr>

            </div>

            <!-- Proceso para anexar informacion del contrato -->
            <div id="Anexos" class="w3-container city" style="display:none;">

              <br>
              <div class="row">
                <div class="col-md-12">

                	<div class="row">
                		<div class="col-md-6">
                			<div class="card1 mb">
                				<label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Agregar Contrato') ?></label>
                                <?= Html::button('Aceptar', ['value' => url::to(['seleccioncontrato']), 'class' => 'btn btn-success', 'id'=>'modalButton',
                                'data-toggle' => 'tooltip',
                                'title' => 'Selección del Cliente']) ?> 

									        <?php
									            Modal::begin([
									              'header' => '<h4>Seleccionar Información Cliente</h4>',
									              'id' => 'modal',
									              'size' => 'modal-lg',
									            ]);

									            echo "<div id='modalContent'></div>";
									                                                              
									            Modal::end(); 
									        ?>
                			</div>
                		</div>

                        <div class="col-md-6">
                            <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Descarga General') ?></label>
                                <?= Html::button('Aceptar', ['value' => url::to(['descargageneral']), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Desacarga General']) ?> 

                                <?php
                                    Modal::begin([
                                        'header' => '<h4>Envio de Información Por Correo</h4>',
                                        'id' => 'modal1',
                                    ]);

                                    echo "<div id='modalContent1'></div>";
                                                                                                  
                                    Modal::end(); 
                                ?>
                            </div>
                        </div>
                	</div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-chart-bar" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Porcentajes de Servicios Ingresados con Contrato') ?></label>
                                <div id="containerPorcentaje" class="highcharts-container" style="height: 150px;"></div>
                            </div>  
                        </div>

                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-chart-bar" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Cantidades - Contratos por Servicios') ?></label>
                                <div id="containerS" class="highcharts-container" style="height: 150px;"></div>
                            </div>  
                        </div>

                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-chart-bar" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Cantidades - Contratos por PCRC') ?></label>
                                <div id="containerP" class="highcharts-container" style="height: 150px;"></div>
                            </div>                            
                        </div>
                    </div>

                	<hr>

                    <div class="row">
                        <div class="col-md-12">
                            <table id="myTableContrato" class="table table-hover table-bordered" style="margin-top:20px" >
                                <caption><?= Yii::t('app', 'Resultados') ?></caption>
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Servicio del Contrato') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Usuario Registrado') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha de Ingreso Contrato') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Requerimientos Sobre Roles') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Requerimientos Sobre Entregable') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Requerimientos Sobre Herramienta') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Requerimientos Sobre Métricas') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Requerimientos Sobre Recursos Fisicos') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        foreach ($varListaContratos as $key => $value) {
                                        
                                        $varServicioNombre = (new \yii\db\Query())
                                                            ->select(['cliente'])
                                                            ->from(['tbl_proceso_cliente_centrocosto'])
                                                            ->where(['=','id_dp_clientes',$value['id_dp_clientes']])
                                                            ->andwhere(['=','estado',1])
                                                            ->andwhere(['=','anulado',0])
                                                            ->groupby(['cliente'])
                                                            ->Scalar();

                                        $varUsuanombre = (new \yii\db\Query())
                                                            ->select(['usua_nombre'])
                                                            ->from(['tbl_usuarios'])
                                                            ->where(['=','usua_id',$value['usua_id']])
                                                            ->groupby(['usua_nombre'])
                                                            ->Scalar();

                                        $varBloquePersona = (new \yii\db\Query())
                                                            ->select(['*'])
                                                            ->from(['tbl_hojavida_bloquepersona'])
                                                            ->where(['=','id_contratogeneral',$value['id_contratogeneral']])
                                                            ->andwhere(['=','anulado',0])
                                                            ->count();

                                        $varBloqueEntregable = (new \yii\db\Query())
                                                            ->select(['*'])
                                                            ->from(['tbl_hojavida_bloqueinformes'])
                                                            ->where(['=','id_contratogeneral',$value['id_contratogeneral']])
                                                            ->andwhere(['=','anulado',0])
                                                            ->count();

                                        $varBloqueHerramienta = (new \yii\db\Query())
                                                            ->select(['*'])
                                                            ->from(['tbl_hojavida_bloqueherramienta'])
                                                            ->where(['=','id_contratogeneral',$value['id_contratogeneral']])
                                                            ->andwhere(['=','anulado',0])
                                                            ->count();

                                        $varBloqueMetricas = (new \yii\db\Query())
                                                            ->select(['*'])
                                                            ->from(['tbl_hojavida_bloquekpis'])
                                                            ->where(['=','id_contratogeneral',$value['id_contratogeneral']])
                                                            ->andwhere(['=','anulado',0])
                                                            ->count();

                                        $varSalasExclusivas = (new \yii\db\Query())
                                                            ->select(['if(exclusivas=1,"Si","No") as Exclusiva'])
                                                            ->from(['tbl_hojavida_bloquesalas'])
                                                            ->where(['=','id_contratogeneral',$value['id_contratogeneral']])
                                                            ->andwhere(['=','anulado',0])
                                                            ->Scalar();
                                    ?>
                                    <tr>
                                        <td><label style="font-size: 12px;"><?php echo  $varServicioNombre; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $varUsuanombre; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $value['fechacreacion']; ?></label></td>
                                        <td class="text-center">
                                            <?php if ($varBloquePersona != 0) { ?>
                                                <label><em class="fas fa-check" style="font-size: 20px; color: #26cd33;"></em><?= Yii::t('app', '') ?></label>
                                            <?php }else{ ?>
                                                <label><em class="fas fa-times" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', '') ?></label>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($varBloqueEntregable != 0) { ?>
                                                <label><em class="fas fa-check" style="font-size: 20px; color: #26cd33;"></em><?= Yii::t('app', '') ?></label>
                                            <?php }else{ ?>
                                                <label><em class="fas fa-times" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', '') ?></label>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($varBloqueHerramienta != 0) { ?>
                                                <label><em class="fas fa-check" style="font-size: 20px; color: #26cd33;"></em><?= Yii::t('app', '') ?></label>
                                            <?php }else{ ?>
                                                <label><em class="fas fa-times" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', '') ?></label>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($varBloqueMetricas != 0) { ?>
                                                <label><em class="fas fa-check" style="font-size: 20px; color: #26cd33;"></em><?= Yii::t('app', '') ?></label>
                                            <?php }else{ ?>
                                                <label><em class="fas fa-times" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', '') ?></label>
                                            <?php } ?>
                                        </td>
                                        <td><label style="font-size: 12px;"><?php echo  $varSalasExclusivas; ?></label></td>
                                        <td class="text-center">
                                            <?= Html::a('<em class="fas fa-edit" style="font-size: 15px; color: #4298B4;"></em>',  ['informacioncontrato','id_contrato'=>$value['id_contratogeneral']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700; border-color: #4298b500 !important; color:#000000;", 'title' => 'Verificar Contrato']) 
                                            ?>

                                            <?= 
                                                Html::a(Yii::t('app', '<em id="idimage" class="fas fa-download" style="font-size: 15px; color: #4298B4;"></em>'),
                                                                'javascript:void(0)',
                                                                [
                                                                    'title' => Yii::t('app', 'Descargar Servicio'),
                                                                    'onclick' => "    
                                                                        $.ajax({
                                                                            type     :'get',
                                                                            cache    : false,
                                                                            url  : '" . Url::to(['descargaservicio',
                                                                            'id_contrato' => $value['id_contratogeneral']]) . "',
                                                                            success  : function(response) {
                                                                                $('#ajax_result').html(response);
                                                                            }
                                                                        });
                                                                    return false;",
                                                                ]);
                                    ?>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr>

                </div>
              </div>

            </div>

          </div>

        </div>
      </div>
    </div>
  <?php ActiveForm::end(); ?>

</div>

<?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
?>

<hr>

<?php
if ($sessiones == "2953" || $sessiones == "3468" || $sessiones == "57" || $sessiones == "637" || $sessiones == "5658" || $sessiones == "0" || $sessiones == "4395" || $sessiones == "69" || $sessiones == "1083"  || $sessiones == "1483" || $sessiones == "7952" ) {
?>
<!-- Capa Procesos Adminitrativos -->
<div id="capaIdAdmin" class="capaAdmin" style="display: inline;">

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 20px; color: #981F40;"></em><?= Yii::t('app', ' Procesos Adminitrativos') ?></label>

                <br>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 20px; color: #981F40;"></em><?= Yii::t('app', ' Parametrización de Contactos') ?></label>

                            <div class="row">
                                
                                <div class="col-md-4">
                                    <div class="card1 mb">
                                        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #981F40;"></em><?= Yii::t('app', ' Eventos') ?></label>
                                        <?= Html::a('Crear',  ['eventos'], ['class' => 'btn btn-danger',                                        
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Crear Eventos']) 
                                        ?>
                                    </div>                                    
                                </div>

                                <div class="col-md-4">
                                    <div class="card1 mb">
                                        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #981F40;"></em><?= Yii::t('app', ' País & Ciudad') ?></label>
                                        <?= Html::a('Crear',  ['paisciudad'], ['class' => 'btn btn-danger',                                        
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Crear País & Ciudad']) 
                                        ?>
                                    </div>                                    
                                </div>

                                <div class="col-md-4">
                                    <div class="card1 mb">
                                        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #981F40;"></em><?= Yii::t('app', ' Modalidad de Trabajo') ?></label>
                                        <?= Html::a('Crear',  ['crearmodalidad'], ['class' => 'btn btn-danger',                                        
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Crear Modalidad']) 
                                        ?>
                                    </div>                                    
                                </div>

                            </div>

                            <br>

                            <div class="row">
                                
                                <div class="col-md-4">
                                    <div class="card1 mb">
                                        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #981F40;"></em><?= Yii::t('app', ' Datos Académicos') ?></label>
                                        <?= Html::a('Crear',  ['academico'], ['class' => 'btn btn-danger',                                        
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Crear Datos Académicos']) 
                                        ?>
                                    </div>                                    
                                </div>

                                <div class="col-md-4">
                                    <div class="card1 mb">
                                        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #981F40;"></em><?= Yii::t('app', ' Editar Permisos') ?></label>
                                        <?= 
                                            Html::button('Crear & Editar', ['value' => url::to(['asignarpermisos']), 'class' => 'btn btn-danger',  'id'=>'modalButton2', 'data-toggle' => 'tooltip', 'title' => 'Crear & Editar Permisos'])
                                        ?>
                                        <?php
                                            Modal::begin([
                                                'header' => '<h4></h4>',
                                                'id' => 'modal2',
                                            ]);

                                            echo "<div id='modalContent2'></div>";
                                                                                    
                                            Modal::end(); 
                                        ?> 
                                    </div>                                    
                                </div>

                                <div class="col-md-4">
                                    <div class="card1 mb">
                                        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #981F40;"></em><?= Yii::t('app', ' Complementos') ?></label>
                                        <?= Html::a('Crear',  ['complementoshv'], ['class' => 'btn btn-danger',                                        
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Crear Complmentos']) 
                                        ?>
                                    </div>                                    
                                </div>

                            </div>

                        </div>
                    </div>
               
                    <div class="col-md-6">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 20px; color: #981F40;"></em><?= Yii::t('app', ' Parametrización de Contratos') ?></label>

                            <div class="row">
                                
                                <div class="col-md-4">
                                    <div class="card1 mb">
                                        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #981F40;"></em><?= Yii::t('app', ' Roles') ?></label>
                                        <?= Html::a('Crear',  ['contratorol'], ['class' => 'btn btn-danger',                                        
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Crear roles']) 
                                        ?>
                                    </div>                                    
                                </div>

                                <div class="col-md-4">
                                    <div class="card1 mb">
                                        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #981F40;"></em><?= Yii::t('app', ' Entregables') ?></label>
                                        <?= Html::a('Crear',  ['contratoinforme'], ['class' => 'btn btn-danger',                                        
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Crear informe']) 
                                        ?>
                                    </div>                                    
                                </div>

                                <div class="col-md-4">
                                    <div class="card1 mb">
                                        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #981F40;"></em><?= Yii::t('app', ' Periocidad') ?></label>
                                        <?= Html::a('Crear',  ['contratopriocidad'], ['class' => 'btn btn-danger',                                        
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Crear periocidad']) 
                                        ?>
                                    </div>                                    
                                </div>

                            </div>

                            <br>

                            <div class="row">
                                
                                <div class="col-md-4">
                                    <div class="card1 mb">
                                        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #981F40;"></em><?= Yii::t('app', ' Metricas') ?></label>
                                        <?= Html::a('Crear',  ['contratometrica'], ['class' => 'btn btn-danger',                                        
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Crear Metricas']) 
                                        ?>
                                    </div>                                    
                                </div>

                            </div>

                        </div>
                    </div>
                </div>   
                <br>
                             
            </div>
        </div>
    </div>   
    

</div>

<hr>

<?php
}
?>
<div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel"><?= Yii::t('app', 'Agregar Carga Masiva') ?></h3>
            </div>
            <div class="modal-body body">
                <div class="card1 mb">
                    <?php $form = ActiveForm::begin([
                        'action'=>['hojavida/export'],
                        'method'=>'POST',
                        'options'=>['enctype'=>'multipart/form-data'],
                        'fieldConfig' => [
                          'inputOptions' => ['autocomplete' => 'off']
                        ]]) 
                    ?>
                    <div class="input-area">
                        <div class="input-text" id="text"> <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 15px; color: #FFC72C;"></em> <?= Yii::t('app', 'Seleccione o arrastre el archivo') ?> </label></div>
                        <?= $form->field($modelos, 'file')->fileInput(["class"=>"input-file" ,'id'=>'file']) ?>
                    </div> 
                    <br>
                    <div class="button">
                        <button  class="btn btn-success">Agregar Archivo <em class="fa fa-plus" style="padding-top:5px"></em> </button>
                    </div>
                    <?php ActiveForm::end() ?>
                    <br>
                    <a href="../../archivos/Plantilla-GestorClientes.xlsx" download><?= Yii::t('app', 'Descargar Plantilla de Ejemplo') ?></a>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="exampleModal4" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalLabel"><?= Yii::t('app', 'Enviar información por correo de los usuarios') ?></h3>
      </div>
        <div class="modal-body">

        <?php if($roles == 270 || $roles == 309):  ?>
            <?php $form = Activeform::begin([
              'action'=>['hojavida/excelexportadmin'],
              'method'=>'post',
              'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
              ]
              ]) ?>
              <div class="form-group">
                 <label for=""><?= Yii::t('app', 'Correo Destinatario') ?></label>
                 <input type="text" class="form-control" autocomplete="off" name="email" placeholder="Example@correo.com" required>
              </div>
              <button class="btn btn-primary btn-block" style="margin-top:10px"><?= Yii::t('app', 'Enviar') ?> <em class="fa fa-paper-plane"></em> </button>
            <?php  Activeform::end() ?>
          <?php endif  ?>

          <?php if($roles != 270 && $roles != 309):  ?>
            <?php $form = Activeform::begin([
              'action'=>['hojavida/excelexport'],
              'method'=>'post',
              'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
              ]
              ]) ?>
              <div class="form-group">
                 <label for=""><?= Yii::t('app', 'Correo Destinatario') ?></label>
                 <input type="text" class="form-control" autocomplete="off" name="email" placeholder="correo destinatario" required>
              </div>
              <button class="btn btn-primary btn-block" style="margin-top:10px"><?= Yii::t('app', 'Enviar') ?> <em class="fa fa-paper-plane"></em></button>
            <?php  Activeform::end() ?>
          <?php endif  ?>
              
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModal5" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalLabel"><?= Yii::t('app', 'Enviar información por correo de los eventos') ?></h3>
      </div>
        <div class="modal-body">

        <?php if($roles == 270 || $roles == 309):  ?>
            <?php $form = Activeform::begin([
              'action'=>['hojavida/excelexporteventosadmin'],
              'method'=>'post',
              'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
              ]
              ]) ?>
              <div class="form-group">
                 <label for=""><?= Yii::t('app', 'Correo Destinatario') ?></label>
                 <input type="text" class="form-control" autocomplete="off" name="email" placeholder="Example@correo.com" required>
              </div>
              <button class="btn btn-primary btn-block" style="margin-top:10px"><?= Yii::t('app', 'Enviar') ?> <em class="fa fa-paper-plane"></em> </button>
            <?php  Activeform::end() ?>
          <?php endif  ?>

          <?php if($roles != 270 && $roles != 309):  ?>
            <?php $form = Activeform::begin([
              'action'=>['hojavida/excelexporteventos'],
              'method'=>'post',
              'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
              ]
              ]) ?>
              <div class="form-group">
                 <label for=""><?= Yii::t('app', 'Correo Destinatario') ?></label>
                 <input type="text" class="form-control" autocomplete="off" name="email" placeholder="correo destinatario" required>
              </div>
              <button class="btn btn-primary btn-block" style="margin-top:10px"><?= Yii::t('app', 'Enviar') ?> <em class="fa fa-paper-plane"></em></button>
            <?php  Activeform::end() ?>
          <?php endif  ?>
              
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready( function () {
    $('#myTable').DataTable({
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

    $('#myTableContrato').DataTable({
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

  $(function() {

    var DirectorListado = "<?php echo implode($arrayListaDirector,",");?>";
    DirectorListado = DirectorListado.split(",");

    var ClienteListado = "<?php echo implode($arrayListaCliente,",");?>";
    ClienteListado = ClienteListado.split(",");

    var CiudadClienteListado = "<?php echo implode($varArrayCiudadCliente, ","); ?>";
    CiudadClienteListado = CiudadClienteListado.split(",");

    Highcharts.setOptions({
      lang: {
        numericSymbols: null,
        thousandsSep: ','
      }
    });

    $('#containerResumen').highcharts({

      chart: {
        borderColor: '#DAD9D9',
        borderRadius: 7,
        borderWidth: 1,
        type: 'column'
      }, 

      title: {
        text: 'Resumen General',
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
          name: 'Resultados Por Clientes',
          data: [<?= $varSumarClientes ?>],
          color: '#FBCE52'
        },{
          name: 'Total Decisores',
          data: [<?= $varSumarDecisores ?>],
          color: '#4298B5'
        },{
          name: 'Total Estratégicos',
          data: [<?= $varSumarEstrategico ?>],
          color: '#C6C6C6'
        },{
          name: 'Total Operativos',
          data: [<?= $varSumarOperativo ?>],
          color: '#559FFF'
        }
      ]

    });

    $('#chartContainerCiudadCliente').highcharts({

      chart: {
        borderColor: '#DAD9D9',
        borderRadius: 7,
        borderWidth: 1,
        type: 'column'
      }, 

      title: {
        text: 'Cantidades Por Ciudad',
        style: {
          color: '#3C74AA'
        }
      },

      xAxis: {
        categories: CiudadClienteListado,
        title: {
          text: null
        },
        crosshair: true
      },

      series: [
        {
          name: 'Resultados Por Ciudad',
          data: [<?= join($varArrayConteoCliente, ',')?>],
          color: '#559FFF'
        }
      ]

    });

    $('#chartContainerDirector').highcharts({

      chart: {
        borderColor: '#DAD9D9',
        borderRadius: 7,
        borderWidth: 1,
        type: 'column'
      }, 

      title: {
        text: 'Cantidades Por Director',
        style: {
          color: '#3C74AA'
        }
      },

      xAxis: {
        categories: DirectorListado,
        title: {
          text: null
        },
        crosshair: true
      },

      series: [
        {
          name: 'Resultados Por Directores',
          data: [<?= join($arrayListaDirectorCantidad, ',')?>],
          color: '#FBCE52'
        }
      ]

    });

    $('#chartContainerClientes').highcharts({

      chart: {
        borderColor: '#DAD9D9',
        borderRadius: 7,
        borderWidth: 1,
        type: 'column'
      }, 

      title: {
        text: 'Cantidades Por Clientes',
        style: {
          color: '#3C74AA'
        }
      },

      xAxis: {
        categories: ClienteListado,
        title: {
          text: null
        },
        crosshair: true
      },

      series: [
        {
          name: 'Resultados Por Clientes',
          data: [<?= join($arrayListaClienteCantidad, ',')?>],
          color: '#4298B5'
        }
      ]

    });

  });


    Highcharts.chart('containerS', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false
        },
        title: {
            text: '<label style="font-size: 20px;"><?php echo $varServiciosRegistrados.''; ?></label>',
            align: 'center',
            verticalAlign: 'middle',
            y: 60
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    distance: -50,
                    style: {
                        fontWeight: 'bold',
                        color: 'white'
                    }
                },
                startAngle: -90,
                endAngle: 90,
                center: ['50%', '110%'],
                size: '220%',
                width: '200%'
            }
        },
        series: [{
            type: 'pie',
            name: '',
            innerSize: '50%',
            data: [
                {
                    name: 'Servicios Registrados en Contrato',
                    y: parseFloat("<?php echo $varServiciosRegistrados;?>"),
                    color: '#4298B5',
                    dataLabels: {
                        enabled: false
                    }
                },{
                    name: 'Servicios No Registrados en Contrato',
                    y: parseFloat("<?php echo $varServiciosNoRegistrados;?>"),
                    color: '#FBCE52',
                    dataLabels: {
                        enabled: false
                    }
                }
            ]
        }]
    });

    Highcharts.chart('containerP', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false
        },
        title: {
            text: '<label style="font-size: 20px;"><?php echo $varPcrcRegistrados.''; ?></label>',
            align: 'center',
            verticalAlign: 'middle',
            y: 60
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    distance: -50,
                    style: {
                        fontWeight: 'bold',
                        color: 'white'
                    }
                },
                startAngle: -90,
                endAngle: 90,
                center: ['50%', '110%'],
                size: '220%',
                width: '200%'
            }
        },
        series: [{
            type: 'pie',
            name: '',
            innerSize: '50%',
            data: [
                {
                    name: 'Pcrc Registrados en Contrato',
                    y: parseFloat("<?php echo $varPcrcRegistrados;?>"),
                    color: '#4298B5',
                    dataLabels: {
                        enabled: false
                    }
                },{
                    name: 'Pcrc No Registrados en Contrato',
                    y: parseFloat("<?php echo $varPcrcNoRegistrados;?>"),
                    color: '#FBCE52',
                    dataLabels: {
                        enabled: false
                    }
                }
            ]
        }]
    });

    Highcharts.chart('containerPorcentaje', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false
        },
        title: {
            text: '<label style="font-size: 20px;"><?php echo $varPorcentajeServicios.'%'; ?></label>',
            align: 'center',
            verticalAlign: 'middle',
            y: 60
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    distance: -50,
                    style: {
                        fontWeight: 'bold',
                        color: 'white'
                    }
                },
                startAngle: -90,
                endAngle: 90,
                center: ['50%', '110%'],
                size: '220%',
                width: '200%'
            }
        },
        series: [{
            type: 'pie',
            yValueFormatString: "#,##0.0\"%\"",
            name: '',
            innerSize: '50%',
            data: [
                {
                    name: 'Porcentaje Servicios con Contrato',
                    y: parseFloat("<?php echo $varPorcentajeServicios;?>"),
                    color: '#4298B5',
                    dataLabels: {
                        enabled: false
                    }
                },{
                    name: 'Porcentaje Servicios sin Contrato',
                    y: parseFloat("<?php echo $varRestantesPorcentaje;?>"),
                    color: '#FBCE52',
                    dataLabels: {
                        enabled: false
                    }
                }
            ]
        }]
    });

</script>