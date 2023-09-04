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
use app\models\GestorEvaluacionPreguntas;
use app\models\GestorEvaluacionRespuestas;

$this->title = 'Gestor Evaluación de Desarrollo - Parametrizador';
$this->params['breadcrumbs'][] = $this->title;

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

  $option_nombre_evaluacion = ArrayHelper::map(\app\models\EvaluacionNombre::find()
  ->select(['nombreeval', 'idevaluacionnombre'])
  ->where("anulado = 0")
  ->all(),
  'idevaluacionnombre',
  'nombreeval'
  );

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
   	font-family: "Nunito",sans-serif;
    font-size: 150%;    
    text-align: left;    
  }

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

  hr {border:0;border-top:1px solid #eee;margin:20px 0}
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
    
    .help-block {
    font-size: 12px; /* Ajusta el tamaño de fuente de las validaciones */
    }

    .dataTables_filter input {
        width: 150px; /* Ajusta el ancho del campo de búsqueda según tus necesidades */
        font-size: 12px; /* Ajusta el tamaño de fuente del campo de búsqueda según tus necesidades */
    }

    .size_font_dataTable {
        font-size: 14px;
    }

    .pagination {
        display: inline-block;
        padding-left: 0;
        margin: 20px 0;
        border-radius: 4px;
    }
     
    .column-font-size {
        font-size: 14px;
    }

    body #tablePreguntas tbody tr td,
    body #tablePreguntas tbody tr td a,
    body #tablePreguntas thead tr th a {
        font-size: 13px !important;
    }

    body #tableRespuestas tbody tr td,
    body #tableRespuestas tbody tr td a,
    body #tableRespuestas thead tr th a {
        font-size: 13px !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        font-size: 11px;
        padding: 5px 10px !important;        
    }
    
    .dataTables_wrapper .dataTables_info {
        padding-top: 0em  !important;        
    }

    .dataTables_wrapper .dataTables_paginate {
        padding-top: 0em  !important; 
    }

    .height-text-area {
        width: 570px;
        height: 80px;        
    }

    .table-container {
        margin: 10px;
        padding: 10px;
    }
    .font-size-title{
        font-size: 15px;        
    }
    
    .color-required{
        color: #db2c23;
    }   

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
<?php 
    if ($roles==270 || $roles==300) {    
?>
<!-- Capa principal -->
<div id="capaIdPrincipal" class="capaPrincipal" style="display: inline;">
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
        <div class="row">
            <div class="col-md-12">  
                      
                <!-- CARD -->
                <div class="card1 mb">
                    <!-- TAB LINKS -->
                    <div class="w3-container">                        
                        <div class="w3-row">
                            <a href="javascript:void(0)" onclick="openCity(event, 'preguntas');">
                                <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                                    <label><em class="fas fa-chart-bar" style="font-size: 20px; color: #827DF9;"></em><strong>  <?= Yii::t('app', 'Competencias') ?></strong></label>
                                </div>
                            </a>
                            <a href="javascript:void(0)" onclick="openCity(event, 'respuestas');">
                                <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                                    <label><em class="fas fa-users" style="font-size: 20px; color: #C148D0;"></em><strong>  <?= Yii::t('app', 'Respuestas') ?></strong></label>
                                </div>
                            </a>
                            <a href="javascript:void(0)" onclick="openCity(event, 'cargamasiva');">
                                <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                                    <label><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><strong>  <?= Yii::t('app', 'Carga Masiva') ?></strong></label>
                                </div>
                            </a>
                        </div>

                        <!-- TAB CONTENT -->
                        <div class="tab-content">                       
                            <!-- Submodulo Preguntas -->
                            <div id="preguntas" class="w3-container city tabcontent" style="display:inline;">
                            <br>
                            
                                <div class="row"> 
                                    <div class="col-md-5">   
                                        <div class="card1 mb">                                 
                                            <div class="row" >  
                                                    <div class="col-md-12">                                        
                                                        <label style="font-size: 15px;"><em class="fa fa-info-circle" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Evaluación') ?><span class="color-required"> *</span></label></label>
                                                        <?=  $form->field($modalPreguntas, 'id_evaluacionnombre', ['labelOptions' => ['class' => 'col-md-12'],
                                                                'template' => $template])->dropDownList($option_nombre_evaluacion,
                                                                [
                                                                    'id' => 'id_nombre_evaluacion',
                                                                    'prompt'=>'Seleccionar Evaluación...',
                                                                    'onchange' => 'cargarDatosTablaPreguntas()'
                                                                ]
                                                            )->label(''); 
                                                        ?>
                                                    </div>                                      
                                                    <div class="col-md-12">                                        
                                                        <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Ingresar Competencia') ?><span class="color-required"> *</span></label>
                                                        <?= $form->field($modalPreguntas, 'nombrepregunta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => true,'id'=>'id_nom_pregunta','placeholder'=>''])?>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em><?= Yii::t('app', ' Ingresar Descripción') ?><span class="color-required"> *</span></label></label>
                                                        <?= $form->field($modalPreguntas, 'descripcionpregunta',  ['labelOptions' => ['class' => 'col-md-12 '], 'template' => $template])->textArea(['maxlength' => true, 'id'=>'id_descripcion_pregunta'])?>                                                    
                                                    </div>
                                            </div>
                                            <div class="row" style="margin-top: 18px; margin-bottom: 18px;">
                                                <div class="col-md-12">
                                                    <?= Html::button('Guardar Datos', ['class' => 'btn btn-success btn-block', 'id' => 'guardarCambios', 'onClick' => 'crearPregunta()']) ?>
                                                </div>                                                
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-7">
                                        <div class="card1 mb" style="width:100%"> 
                                            <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9; margin-top:1.5%;"></em> <?= Yii::t('app', 'Lista de Competencias') ?></label>
                                            <label id="emptyMessage" style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 18px; color: #827DF9; margin-top:1.5%;"></em> <?= Yii::t('app', 'No hay datos para mostrar') ?></label>
                                            
                                            <div class="table-responsive table-container" id="container_table_preguntas">                                
                                                <table id="tablePreguntas" class="table table-hover table-striped table-bordered table-condensed table-celda" style="width:100% !important;" aria-hidden="true" >
                                                </table>    
                                            </div>            
                                        </div> 
                                        <!-- Modal Editar Pregunta -->
                                        <?php
                                            Modal::begin([
                                                'id' => 'modalEditar',
                                                'header' => '<h4>Editar Competencia</h4>',
                                                'footer' => Html::button('Actualizar datos', ['class' => 'btn btn-success btn-block', 'style'=>'margin-top: 1.5%; padding:0.5%', 'id' => 'guardarCambios', 'onClick' => 'editarPregunta();']),
                                            ]);

                                            ActiveForm::begin([
                                                'id' => 'formEditarPregunta', 
                                            ]);
                                           
                                            echo '<div class="row" id="modal_edit_pregunta">';
                                            echo '<div class="col-md-12">';
                                            echo '<label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> Ingresar Competencia </label>';
                                            echo $form->field($modalPreguntas, 'nombrepregunta',  ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'nombre_pregunta_edit','placeholder'=>'Ingresar pregunta']);
                                            echo '</div>';
                                            echo '<div class="col-md-12" style="margin-top: 20px">';
                                            echo '<label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> Ingresar descripción</label>';           
                                            echo $form->field($modalPreguntas, 'descripcionpregunta',  ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['id'=>'descripcion_pregunta_edit', 'style'=>'width: 570px; height: 80px;']);
                                            echo '</div>';
                                            echo '</div>';
                                            
                                            
                                            ActiveForm::end();

                                            Modal::end();
                                        ?> 
                                        <!-- Modal Editar Pregunta Fin -->
                                    </div>
                                </div>                            
                            </div>
                            <!-- Submodulo Respuestas -->
                            <div id="respuestas" class="w3-container city tabcontent" style="display:none;">
                                <br> 
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card1 mb" >
                                            <label  style="font-size: 18px; color: #db2c23;"><em class="fas fa-exclamation-triangle" style="font-size: 20px; color: #db2c23;"></em> <?= Yii::t('app', 'Para tener en cuenta: ') ?></label>
                                            <label style="font-size: 15px;"> <?= Yii::t('app', 'Las respuestas ingresadas se asignarán por igual a todas las competencias.') ?></label>
                                        </div>
                                    </div> 
                                </div>   
                                <hr> 
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="card1 mb">
                                            <div class="row">  
                                                <div class="col-md-12">                                        
                                                    <label style="font-size: 15px;"><em class="fa fa-info-circle" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Evaluación') ?><span class="color-required"> *</span></label></label>
                                                    <?=  $form->field($modalRespuestas, 'id_evaluacionnombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                                                    ->dropDownList($option_nombre_evaluacion,
                                                        [
                                                            'id' => 'id_nombre_evaluacion_rta',
                                                            'prompt'=>'Seleccionar Evaluación...',
                                                            'onchange' => 'cargarDatosTablaRespuestas()'
                                                        ]
                                                    )->label(''); 
                                                    ?>
                                                </div>                                              
                                                <div class="col-md-12">
                                                    <label style="font-size: 15px;"><em class="fas fa-pencil-alt" style="font-size: 18px; color: #C148D0; margin-top:1.5%;"></em> <?= Yii::t('app', ' Ingresar Respuesta:') ?><span class="color-required"> *</span></label></label>
                                                    <?= $form->field($modalRespuestas, 'nombre_respuesta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 100,  'id'=>'id_nombre_respuesta']) ?> 
                                                </div>
                                                <div class="col-md-12">
                                                    <label style="font-size: 15px;"> <em class="fas fa-pencil-alt" style="font-size: 18px; color: #C148D0; margin-top:1.5%;"></em> <?= Yii::t('app', ' Ingresar Valor:') ?><span class="color-required"> *</span></label> </label>
                                                    <?= $form->field($modalRespuestas, 'valornumerico_respuesta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'id_valor_respuesta', 'onkeypress' => 'return valida(event)']) ?>
                                                </div>
                                                <div class="col-md-12">
                                                    <label style="font-size: 15px;"> <em class="fas fa-pencil-alt" style="font-size: 18px; color: #C148D0; margin-top:1.5%;"></em> <?= Yii::t('app', ' Ingresar Descripción:') ?><span class="color-required"> *</span></label> </label>
                                                    <?= $form->field($modalRespuestas, "descripcion_respuesta", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['id'=>'id_descripcion_rta', 'maxlength' => true]) ?>
                                                </div> 
                                                
                                            </div>  
                                            
                                            <div class="row" style="margin-top: 18px;">
                                                <div class="col-md-12">
                                                    <?= Html::button(Yii::t('app', 'Guardar Datos'),
                                                        ['class' => 'btn btn-success btn-block' ,
                                                        'data-toggle' => 'tooltip',
                                                        'onclick' => 'crearRespuesta();',
                                                        'style' => '',
                                                        'title' => 'Guardar datos']) 
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-7">
                                        <div class="card1 mb" style="width:100%"> 
                                                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9; margin-top:1.5%;"></em> <?= Yii::t('app', 'Lista de Respuestas') ?></label>
                                                <label id="emptyMessageRespuestas" style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 18px; color: #827DF9; margin-top:1.5%;"></em> <?= Yii::t('app', 'No hay datos para mostrar') ?></label>
                                                
                                                <div class="table-responsive table-container" id="container_table_respuestas">                                
                                                    <table id="tableRespuestas" class="table table-hover table-striped table-bordered table-condensed" style="width:100% !important;" aria-hidden="true" >
                                                        
                                                    </table>    
                                                </div>            
                                        </div>
                                        
                                        <!-- Modal Editar Respuesta -->
                                        <?php
                                            Modal::begin([
                                                'id' => 'modalEditarRta',
                                                'header' => '<h4>Editar Respuesta</h4>',
                                                'footer' => Html::button('Actualizar datos', ['class' => 'btn btn-success btn-block', 'style'=>'margin-top: 1.5%; padding:0.5%', 'id' => 'guardarCambios_rta', 'onClick' => 'editarRespuesta();']),
                                            ]);

                                            ActiveForm::begin([
                                                'id' => 'formEditarRespuesta', 
                                            ]);
                                           
                                            echo '<div class="row" id="modal_edit_rta">';
                                            echo '<div class="col-md-12">';
                                            echo '<label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> Ingresar Respuesta </label>';
                                            echo $form->field($modalRespuestas, 'nombre_respuesta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 100,  'id'=>'nombre_respuesta_edit']); 
                                            echo '</div>';
                                            echo '<div class="col-md-12" style="margin-top: 20px">';
                                            echo '<label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> Ingresar Valor </label>';           
                                            echo $form->field($modalRespuestas, 'valornumerico_respuesta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'valor_respuesta_edit', 'onkeypress' => 'return valida(event)']);
                                            echo '</div>';
                                            echo '<div class="col-md-12" style="margin-top: 20px">';
                                            echo '<label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> Ingresar descripción</label>';           
                                            echo $form->field($modalRespuestas, "descripcion_respuesta", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['id'=>'descripcion_rta_edit', 'maxlength' => true]);
                                            echo '</div>';
                                            echo '</div>';
                                            
                                            
                                            ActiveForm::end();

                                            Modal::end();
                                        ?> 
                                        <!-- Modal Editar Respuesta Fin -->
                                        
                                    </div>

                                </div>
                            </div>
                            <!-- Submodulo Carga Masiva -->
                            <div id="cargamasiva" class="w3-container city tabcontent" style="display:none;">
                                <br>
                                <div class="row">
                                    <div class="col-md-6">

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card1 mb">
                                                    <label style="font-size: 18px;"><em class="fas fa-file" style="font-size: 18px; color: #b52aef;"></em> <?= Yii::t('app', 'Plantilla') ?></label>
                                                    <label style="font-size: 15px;"><?= Yii::t('app', 'Recuerda no borrar ni alterar el orden de las columnas.') ?></label>                                        
                                                    <a style="font-size: 15px;" class="text-danger" rel="stylesheet" href="../../downloadfiles/Plantilla_CargaMasiva_EvDllo.xlsx" target="_blank">Descargar Archivo</a>                                            
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12">                                                
                                                <div class="card1 mb"> 
                                                    <label style="font-size: 18px;"><em class="fa fa-upload" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Subir Carga Masiva') ?></label>
                                                    
                                                    <?= Html::button('Aceptar', ['value' => url::to(['viewcargamasiva']),
                                                                    'class' => 'btn btn-success', 'id'=>'modalButton',
                                                                    'data-toggle' => 'tooltip',
                                                                    'title' => 'Cargar Datos Masivos']) 
                                                    ?> 

                                                    <?php
                                                        Modal::begin([
                                                            'header' => '<h4>Carga Masiva</h4>',
                                                            'id' => 'modal',
                                                        ]);

                                                        echo "<div id='modalContent'></div>";
                                                                                                                                    
                                                        Modal::end(); 
                                                    ?>
                                                </div>  
                                            </div>                                            
                                        </div>  
                                        <br> 
                                        <!-- Alerta para carga masiva-->
                                        <?php if (Yii::$app->session->hasFlash('success')): 
                                            $mensaje= Yii::$app->session->getFlash('success');
                                            ?>
                                            <script>
                                                 swal.fire("",'<?= $mensaje ?>',"success");    
                                            </script>
                                        <?php endif; ?>
                                                                           
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card1 mb" >
                                                <label  style="font-size: 18px; color: #db2c23; margin-botton: 10px;"><em class="fas fa-exclamation-triangle" style="font-size: 20px; color: #db2c23;"></em> <?= Yii::t('app', 'Para tener en cuenta: ') ?></label>
                                                <label style="font-size: 15px;"> <?= Yii::t('app', '- Solo se permiten archivos con extension') ?><?= Html::tag('span', ' .xlsx', ['class' => 'text-danger']); ?> </label>
                                                <label style="font-size: 15px;"> <?= Yii::t('app', '- Tamaño máximo del archivo debe ser 2048 KB (2MB)') ?></label>
                                                <label style="font-size: 15px;"> <?= Yii::t('app', '- Todos los campos deben ser texto, excepto la cédula que debe ser numérica') ?></label>
                                                <label style="font-size: 15px;"> <?= Yii::t('app', '- Toda la información debe estar en la hoja 0 del archivo') ?></label>
                                                <label style="font-size: 15px;"> <?= Yii::t('app', '- Eliminar completamente las filas vacias dentro del archivo') ?></label>
                                                <label style="font-size: 15px;"> <?= Yii::t('app', '- Todos los campos deben estar como valores (sin fórmulas)') ?></label>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                
                                
                                
                            </div>
                        </div>
                        <!-- TAB CONTENT END-->
                        <br>
                        <br>
                    </div>
                    <!-- TAB LINKS END -->
                </div>
                <!-- CARD END -->
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<hr>
<?php 
} else {   
?>
<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 18px; color: #db2c23;"><em class="fa fa-info-circle" style="font-size: 20px; color: #db2c23;"></em> Aviso </label>
                <label style="font-size: 15px;"> <?= Yii::t('app', 'Tu usuario no tiene permisos para parametrizar la Evaluación de Desarrollo. Si crees que se trata de un error, por favor comunicarse con el administrador.') ?></label>
            </div>
        </div>
    </div>
</div>
<hr>
<?php 
    } 
?>


<script type="text/javascript">

    // FUNCION PARA VALIDAR SOLO ENTRADA NUMERICA
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
    // FUNCION PARA VALIDAR SOLO ENTRADA NUMERICA FIN
  

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

  //FUNCION CREAR PREGUNTA
  function crearPregunta(){    
    var id_evaluacion_selector = document.getElementById("id_nombre_evaluacion");
    var nombre_pregunta_selector = document.getElementById("id_nom_pregunta");
    var descrip_pregunta_selector = document.getElementById("id_descripcion_pregunta");

    //obtener valor de los campos
    var id_evaluacion_txt = id_evaluacion_selector.value;
    var nombre_pregunta_txt = nombre_pregunta_selector.value;
    var descrip_pregunta_txt = descrip_pregunta_selector.value;
    
    //Validacion de campos    
    if (id_evaluacion_txt == "") {
        swal.fire("!!! Advertencia !!!","No ha seleccionado la Evaluación","warning");
        return;
    }
    if (nombre_pregunta_txt == "") {
        swal.fire("!!! Advertencia !!!","No ha ingresado el nombre de la competencia","warning");
        return;
    }
    if (descrip_pregunta_txt == "") {
        swal.fire("!!! Advertencia !!!","No ha ingresado la descripción de la competencia","warning");
        return;
    }

    //AJAX
    $.ajax({
        method: "post",
        url: "crearpregunta",
        data: {
            id_evaluacion: id_evaluacion_txt,
            nom_pregunta: nombre_pregunta_txt.trim(),
            descripcion_pregunta: descrip_pregunta_txt.trim(),
            _csrf:'<?=\Yii::$app->request->csrfToken?>'
        },
        success: function(response) {

            if (response.status === 'error') {
                swal.fire("",response.data,"error");
                return;
            } 
            // Procesar la respuesta exitosa
            if (response.status === 'success') {

                $( "#container_table_preguntas" ).show();
                cargarDatosTablaPreguntas();

                nombre_pregunta_selector.value = '';
                descrip_pregunta_selector.value = '';

                swal.fire("",response.data,"info");
                return;
            }                  
            
        },
        error: function(jqXHR, textStatus, errorThrown) {
            
            swal.fire("","Error obteniendo datos en: crearPregunta","error");
            
        }
    });
    //AJAX end           
                
    };
    //FUNCION CREAR PREGUNTA FIN

    //FUNCION EDITAR PREGUNTA 
    function editarPregunta(){

        //Obtener valor ingresado
        var id_evaluacion_txt = $('#modal_edit_pregunta').data("id_gestor");
        var nombre_pregunta_txt = document.getElementById("nombre_pregunta_edit").value;
        var descrip_pregunta_txt = document.getElementById("descripcion_pregunta_edit").value;
        
        //Validacion de campos
        if (id_evaluacion_txt == "") {
            swal.fire("!!! Advertencia !!!","No se pudo obtener el id del nuevo registro","warning");
            return;
        }

        if (nombre_pregunta_txt == "") {
            swal.fire("!!! Advertencia !!!","No ha ingresado el nombre de la competencia","warning");
            return;
        }
        if (descrip_pregunta_txt == "") {
            swal.fire("!!! Advertencia !!!","No ha ingresado la descripción de la competencia","warning");
            return;
        }

        $.ajax({
            method: "post",
            url: "editarpregunta",
            data: {
                id_evaluacion_pregunta: id_evaluacion_txt,
                pregunta_edit: nombre_pregunta_txt.trim(),
                descripcion_edit : descrip_pregunta_txt.trim(),
                _csrf:'<?=\Yii::$app->request->csrfToken?>'
            },
            success: function(response) {

                if(response.status=="error"){
                    swal.fire("!!! Error !!!",response.data,"error");
                    return;                    
                }

                if(response.status=="success"){
                    cargarDatosTablaPreguntas();
                    swal.fire("",response.data,"success");                    
                    $('#modalEditar').modal('hide');                                        
                }

            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Manejar el error
                console.log("Error al cargar los datos en editarPregunta: ", errorThrown);
            }
        });

    }
    //FUNCION EDITAR PREGUNTA FIN

    //FUNCION ELIMINAR PREGUNTA
    function eliminarPregunta(id_pregunta){ 

        var id_pregunta_eliminar = id_pregunta;        

        if (id_pregunta_eliminar == "") {
            swal.fire("!!! Error !!!","No llegó el id de la competencia a eliminar","error");
            return;
        }

        $.ajax({
            method: "post",
            url: "eliminarpregunta",
            data: {
                id_pregunta: id_pregunta_eliminar,                
                _csrf:'<?=\Yii::$app->request->csrfToken?>'
            },
            success: function(response) {

                if(response.status=="error"){
                    swal.fire("!!! Error !!!",response.data,"error");
                    return;                    
                }

                if(response.status=="success"){
                    
                    swal.fire("",response.data,"success");
                    cargarDatosTablaPreguntas();                                        
                }

            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Manejar el error
                console.log("Error al cargar los datos eliminarPregunta: ", errorThrown);
            }
        });

    }
    //FUNCION ELIMINAR PREGUNTA FIN

    // FUNCIÓN AGREGAR FILA A UN DATATABLE
    function agregarFilaEnDatatable(tabla, datos) {        
        tabla.row.add(datos).draw();
    }
    // FUNCIÓN AGREGAR FILA A UN DATATABLE FIN

    // FUNCION PARA MOSTRAR DATOS DE UNA LISTA USANDO EL MISMO DATATABLE 
    function cargarDatosTablaPreguntas() {
        var selectedId = $("#id_nombre_evaluacion").val();

        $.ajax({
            method: "get",
            url: "cargardatostablapreguntas",
            data: {
                id: selectedId,
            },
            success: function(response) {
                
                if(response.data.length>0) {
                    $( "#emptyMessage" ).hide();
                    $( "#container_table_preguntas" ).show();

                    // Limpiar datatable 
                    $( "#container_table_preguntas" ).empty();
                    $( "#container_table_preguntas" ).classList= "table-container";
                    var new_table = document.createElement("table");
                    new_table.setAttribute("id","tablePreguntas");                 
                    new_table.classList = "table table-hover table-striped table-bordered table-condensed dataTable no-footer";
                    document.getElementById("container_table_preguntas").appendChild(new_table); 
                
                    init_table_preguntas(response.data);
                }

                if(response.data.length==0){
                    $( "#container_table_preguntas" ).hide();
                    $( "#emptyMessage" ).show();
                   // init_table_preguntas(response.data);
                }                
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Manejar el error
                swal.fire("", "Error al cargar los datos en cargarDatosTablaPreguntas: " + errorThrown + ".", "error" );
                console.log("Error al cargar los datos:", errorThrown);
            }
        });
    }
    // FUNCION PARA MOSTRAR DATOS DE UNA LISTA USANDO EL MISMO DATATABLE FIN

    function init_table_preguntas(data) {

        if(data.length > 0) {            
            var tabla_preguntas = $('#tablePreguntas').DataTable({
            
            select: true,
            "autoWidth": true,
            data:data,
            select: false,
            language: {
                "decimal": "",
                "emptyTable": "No hay datos disponibles en la Tabla",
                "lengthMenu": "<span class='size_font_dataTable'> Cantidad de Datos a Mostrar _MENU_ </span>",
                "zeroRecords": "No se encontraron datos ",
                "info": "<span style='font-size: 14px;'> Mostrando _START_ a _END_ de _TOTAL_ registros </span>",
                "infoEmpty": "<span style='font-size: 14px;'> Mostrando 0 de 0 registros </span>",
                "infoFiltered": "(Filtrado un _MAX_ total)",
                "infoPostFix": "",
                "thousands": ",",
                "search": "<span style='font-size: 14px;'>Buscar:</span>",
                "loadingRecords": "Cargando...",
                "processing":     "Procesando...",
                "paginate": {
                "first":      "<span class='size_font_dataTable'> Primero </span>",
                "last":       "<span class='size_font_dataTable'> Ultimo </span>",
                "next":       "<span class='size_font_dataTable'> Siguiente </span>",
                "previous":   "<span class='size_font_dataTable'> Anterior </span>"
                },
                "order": [[ 0, "desc" ]],
                autoWidth : false,
                "table-layout": "fixed",
                paging: true,      
            },
            
                "lengthMenu": [5, 10, 25, 50],
                "pageLength": 5,
            columnDefs: [
                { targets: '_all', className: 'column-font-size' }
            ],
            columns: [
                {   title: "Id",
                    data: 'id_gestorevaluacionpreguntas',
                    visible : false                   
                },
                {   title: "Competencia",
                    data: 'nombrepregunta',
                    width: '20%'
                   
                },
                {    title: "Descripción",
                    data: 'descripcionpregunta',
                    width: '70%'
                },
                {   title: "Acción",
                    defaultContent : "<button class='btn btn-xs btn-info edit_btn' data-toggle='tooltip' data-container='body' data-trigger='hover' title='Editar'>  <span class='fas fa-pencil-alt'></span> </button> <button class='btn btn-xs btn-danger delete_btn' data-toggle='tooltip' data-container='body' data-trigger='hover' title='Eliminar' > <span class='fa fa-trash'> </span> </button>",
                    searchable : false,
                    width: '10%'
                    
                }
            ],
            initComplete : function(){

                // Click Boton Editar
                $('#tablePreguntas tbody').on( 'click', '.edit_btn', function () {                     
                    event.preventDefault();               
                    var fila = $(this).closest('tr'); // Obtener la fila correspondiente al click                
                    var datos = tabla_preguntas.row(fila).data(); // Obtener los datos de la fila 
                    
                    $('#nombre_pregunta_edit').val(datos.nombrepregunta);
                    $('#descripcion_pregunta_edit').val(datos.descripcionpregunta);                
                    $('#modal_edit_pregunta').data('id_gestor', datos.id_gestorevaluacionpreguntas );
                    $('#modalEditar').modal('show');
                });
                // Click Boton Editar Fin

                // Click Boton Eliminar
                $('#tablePreguntas tbody').on( 'click', '.delete_btn', function () {
                    event.preventDefault();
                    var datos = tabla_preguntas.row($(this).closest('tr')).data(); // Obtener los datos de la fila
                    eliminarPregunta(datos.id_gestorevaluacionpreguntas); //enviar id a eliminar
                });
                // Click Boton Eliminar Fin
            }
            // INITCOMPLETE END
            });        

            //Inicializar en la primer página del datatable
            $("#tablePreguntas").DataTable().page( 0 ).draw( false );

        } 
    }

    //---------RESPUESTAS ------
    // FUNCION PARA MOSTRAR DATOS DE UNA LISTA USANDO EL MISMO DATATABLE 
    function cargarDatosTablaRespuestas() {
        var selectedId = $("#id_nombre_evaluacion_rta").val();

        $.ajax({
            method: "get",
            url: "cargardatostablarespuestas",
            data: {
                id: selectedId,
            },
            success: function(response) {
                
                if(response.data.length>0) {
                    $( "#emptyMessageRespuestas" ).hide();
                    $( "#container_table_respuestas" ).show();

                    // Limpiar datatable 
                    $( "#container_table_respuestas" ).empty();
                    $( "#container_table_respuestas" ).classList= "table-container";
                    var new_table = document.createElement("table");
                    new_table.setAttribute("id","tableRespuestas");                 
                    new_table.classList = "table table-hover table-striped table-bordered table-condensed dataTable no-footer";
                    document.getElementById("container_table_respuestas").appendChild(new_table); 
                
                    init_table_respuestas(response.data);
                }

                if(response.data.length==0){
                    $( "#container_table_respuestas" ).hide();
                    $( "#emptyMessageRespuestas" ).show();
                }                
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Manejar el error
                swal.fire("", "Error al cargar los datos en cargar Datos Tabla Respuestas: " + errorThrown + ".", "error" );
                console.log("Error al cargar los datos:", errorThrown);
            }
        });
    }
    // FUNCION PARA MOSTRAR DATOS DE UNA LISTA USANDO EL MISMO DATATABLE FIN

    //FUNCION INICIALIZAR DATATABLE PARA LA TABLA RESPUESTAS
    function init_table_respuestas(data) {

        if(data.length > 0) {            
            var tabla_respuestas = $('#tableRespuestas').DataTable({
            
            select: true,
            "autoWidth": true,
            data:data,
            select: false,
            language: {
                "decimal": "",
                "emptyTable": "No hay datos disponibles en la Tabla",
                "lengthMenu": "<span class='size_font_dataTable'> Cantidad de Datos a Mostrar _MENU_ </span>",
                "zeroRecords": "No se encontraron datos ",
                "info": "<span style='font-size: 14px;'> Mostrando _START_ a _END_ de _TOTAL_ registros </span>",
                "infoEmpty": "<span style='font-size: 14px;'> Mostrando 0 de 0 registros </span>",
                "infoFiltered": "(Filtrado un _MAX_ total)",
                "infoPostFix": "",
                "thousands": ",",
                "search": "<span style='font-size: 14px;'>Buscar:</span>",
                "loadingRecords": "Cargando...",
                "processing":     "Procesando...",
                "paginate": {
                "first":      "<span class='size_font_dataTable'> Primero </span>",
                "last":       "<span class='size_font_dataTable'> Ultimo </span>",
                "next":       "<span class='size_font_dataTable'> Siguiente </span>",
                "previous":   "<span class='size_font_dataTable'> Anterior </span>"
                },
                "order": [[ 0, "desc" ]],
                autoWidth : false,
                "table-layout": "fixed",
                paging: true,      
            },
            
                "lengthMenu": [5, 10, 25, 50],
                "pageLength": 5,
            columnDefs: [
                { targets: '_all', className: 'column-font-size' }
            ],
            columns: [
                {   title: "Id",
                    data: 'id_gestorevaluacionrespuestas',
                    visible : false                   
                },
                {   title: "Respuesta",
                    data: 'nombre_respuesta',
                    width: '20%'
                   
                },
                {   title: "Valor",
                    data: 'valornumerico_respuesta',
                    width: '10%'
                   
                },
                {    title: "Descripción",
                    data: 'descripcion_respuesta',
                    width: '60%'
                },
                {   title: "Acción",
                    defaultContent : "<button class='btn btn-xs btn-info edit_btn_rta' data-toggle='tooltip' data-container='body' data-trigger='hover' title='Editar'>  <span class='fas fa-pencil-alt'></span> </button> <button class='btn btn-xs btn-danger delete_btn_rta' data-toggle='tooltip' data-container='body' data-trigger='hover' title='Eliminar' > <span class='fa fa-trash'> </span> </button>",
                    searchable : false,
                    width: '10%'
                    
                }
            ],
            initComplete : function(){

                // Click Boton Editar
                $('#tableRespuestas tbody').on( 'click', '.edit_btn_rta', function () {                     
                    event.preventDefault();               
                    var fila = $(this).closest('tr'); // Obtener la fila correspondiente al click                
                    var datos = tabla_respuestas.row(fila).data(); // Obtener los datos de la fila 
                    
                    $('#nombre_respuesta_edit').val(datos.nombre_respuesta);
                    $('#valor_respuesta_edit').val(datos.valornumerico_respuesta);
                    $('#descripcion_rta_edit').val(datos.descripcion_respuesta);                
                    $('#modal_edit_rta').data('id_gestor', datos.id_gestorevaluacionrespuestas );
                    $('#modalEditarRta').modal('show');
                });
                // Click Boton Editar Fin

                // Click Boton Eliminar
                $('#tableRespuestas tbody').on( 'click', '.delete_btn_rta', function () {
                    event.preventDefault();
                    var datos = tabla_respuestas.row($(this).closest('tr')).data(); // Obtener los datos de la fila
                    deleteRespuesta(datos.id_gestorevaluacionrespuestas); //enviar id a eliminar
                });
                // Click Boton Eliminar Fin
            }
            // INITCOMPLETE END
            });        

            //Inicializar en la primer página del datatable
            $("#tableRespuestas").DataTable().page( 0 ).draw( false );

        } 
    }
     //FUNCION INICIALIZAR DATATABLE PARA LA TABLA RESPUESTAS END

    //FUNCION CREAR RESPUESTA
    function crearRespuesta(){  

    var id_evaluacion_selector = document.getElementById("id_nombre_evaluacion_rta");
    var nombre_rta_selector = document.getElementById("id_nombre_respuesta");
    var valor_rta_selector = document.getElementById("id_valor_respuesta");
    var descrip_rta_selector = document.getElementById("id_descripcion_rta");
        
        
    var id_evaluacion_txt = id_evaluacion_selector.value;
    var nombre_rta_txt = nombre_rta_selector.value;
    var valor_rta_txt = valor_rta_selector.value;
    var descrip_rta_txt = descrip_rta_selector.value;

    //Validacion de campos    
    if (id_evaluacion_txt == "") {
        swal.fire("!!! Advertencia !!!","No ha seleccionado la Evaluación","warning");
        return;
    }
    if (nombre_rta_txt == "") {
        swal.fire("!!! Advertencia !!!","No ha ingresado la respuesta","warning");
        return;
    }
    if (valor_rta_txt == "") {
        swal.fire("!!! Advertencia !!!","No ha ingresado valor asociado a la respuesta","warning");
        return;
    }
    if (descrip_rta_txt == "") {
        swal.fire("!!! Advertencia !!!","No ha ingresado la descripción","warning");
        return;
    }

    //AJAX
    $.ajax({
        method: "post",
        url: "createrespuesta",
        data: {
            id_evaluacion: id_evaluacion_txt,
            nom_respuesta: nombre_rta_txt.trim(),
            valor_respuesta: valor_rta_txt,
            descripcion_respuesta: descrip_rta_txt.trim(),
            _csrf:'<?=\Yii::$app->request->csrfToken?>'
        },
        success: function(response) {

            if (response.status === 'error') {
                swal.fire("",response.data,"error");
                return;
            }

            if (response.status === 'success') {

                nombre_rta_selector.value = '';
                valor_rta_selector.value = '';
                descrip_rta_selector.value = '';

                swal.fire("",response.data,"info");

                $( "#container_table_respuestas" ).show();
                cargarDatosTablaRespuestas();
                
                return;
            }                  
            
        },
        error: function(jqXHR, textStatus, errorThrown) {
            
            swal.fire("","Error obteniendo datos en: crear respuesta","error");
            
        }
    });
    //AJAX end           
                
    };
    //FUNCION CREAR RESPUESTA FIN

    //FUNCION EDITAR RESPUESTA 
    function editarRespuesta(){

        //Obtener valor ingresado
        var id_evaluacion_txt = $('#modal_edit_rta').data("id_gestor");
        var nombre_rta_txt = document.getElementById("nombre_respuesta_edit").value;
        var valor_rta_txt = document.getElementById("valor_respuesta_edit").value;
        var descrip_rta_txt = document.getElementById("descripcion_rta_edit").value;

        //Validacion de campos
        if (id_evaluacion_txt == "") {
            swal.fire("!!! Advertencia !!!","No se pudo obtener el id del nuevo registro","warning");
            return;
        }

        if (nombre_rta_txt == "") {
            swal.fire("!!! Advertencia !!!","No ha ingresado la respuesta","warning");
            return;
        }
        if (valor_rta_txt == "") {
            swal.fire("!!! Advertencia !!!","No ha ingresado valor asociado a la respuesta","warning");
            return;
        }
        if (descrip_rta_txt == "") {
            swal.fire("!!! Advertencia !!!","No ha ingresado la descripción","warning");
            return;
        }

        $.ajax({
            method: "post",
            url: "editrespuesta",
            data: {
                id_evaluacion_rta: id_evaluacion_txt,
                nom_rta_edit: nombre_rta_txt.trim(),
                valor_rta_edit: valor_rta_txt,
                descripcion_rta_edit : descrip_rta_txt.trim(),
                _csrf:'<?=\Yii::$app->request->csrfToken?>'
            },
            success: function(response) {

                if(response.status=="error"){
                    swal.fire("!!! Error !!!",response.data,"error");
                    return;                    
                }

                if(response.status=="success"){
                    cargarDatosTablaRespuestas();
                    swal.fire("",response.data,"success");                    
                    $('#modalEditarRta').modal('hide');                                        
                }

            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Manejar el error
                console.log("Error al cargar los datos ", errorThrown);
            }
        });

    }
    //FUNCION EDITAR RESPUESTA FIN

    //FUNCION ELIMINAR RESPUESTA
    function deleteRespuesta(id_respuesta){ 

        var id_rta_eliminar = id_respuesta;        

        if (id_rta_eliminar == "") {
            swal.fire("!!! Error !!!","No llegó el id de la pregunta a eliminar","error");
            return;
        }

        $.ajax({
            method: "post",
            url: "deleterespuesta",
            data: {
                id_rta: id_rta_eliminar,                
                _csrf:'<?=\Yii::$app->request->csrfToken?>'
            },
            success: function(response) {

                if(response.status=="error"){
                    swal.fire("!!! Error !!!",response.data,"error");
                    return;                    
                }

                if(response.status=="success"){
                    
                    swal.fire("",response.data,"success");
                    cargarDatosTablaRespuestas();                                        
                }

            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Manejar el error
                console.log("Error al cargar los datos: ", errorThrown);
            }
        });

    }
    //FUNCION ELIMINAR RESPUESTA FIN

</script>