<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

$this->title = 'Evaluacion de desarrollo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $query2 = Yii::$app->get('dbjarvis2')->createCommand("select * from dp_posicion where estado = 1")->queryAll();

    $listData2 = ArrayHelper::map($query2, 'id_dp_posicion', 'posicion');

    $var2 = ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10'];
    $var3 = ['30' => 'Lider', '37' => 'Tecnico', '0' => 'Todos'];
    $var4 = ['0' => 'Buena Gente', '1' => 'Gente Buena'];

    $txtlisttipo = Yii::$app->db->createCommand("select * from tbl_evaluacion_tipoeval where anulado = 0")->queryAll();

    $txtlistevaluaciones = Yii::$app->db->createCommand("select * from tbl_evaluacion_nombre where anulado = 0")->queryAll();

    $varnameeval = Yii::$app->db->createCommand("select nombreeval from tbl_evaluacion_nombre where anulado = 0")->queryScalar();

    $varideval = Yii::$app->db->createCommand("select idevaluacionnombre from tbl_evaluacion_nombre where anulado = 0")->queryScalar();

    $query3 = Yii::$app->db->createCommand("select idevaluacioncompetencia, concat(namecompetencia, ' - ',idevaluacionnivel) as competencia from tbl_evaluacion_competencias where anulado = 0")->queryAll();
    $listData3 = ArrayHelper::map($query3, 'idevaluacioncompetencia', 'competencia');
    $query4 = Yii::$app->db->createCommand("select * from tbl_evaluacion_comportamientos where anulado = 0")->queryAll();
    $listData4 = ArrayHelper::map($query4, 'idevaluacionpregunta', 'namepregunta');
    $query5 = Yii::$app->db->createCommand("select * from tbl_evaluacion_bloques where anulado = 0")->queryAll();
    $listData5 = ArrayHelper::map($query5, 'idevaluacionbloques', 'namebloque');

?>
<style>
    @import url('https://fonts.googleapis.com/css?family=Nunito');

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
        background-image: url('../../images/Banner_Ev_Desarrollo.png');
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
    @media (min-width:601px){.w3-col.m1{width:8.33333%}.w3-col.m2{width:16.66666%}.w3-col.m3,.w3-quarter{width:24.99999%}.w3-col.m4,.w3-third{width:14.2%}
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
    /*.w3-code,.w3-codespan{font-family:Consolas,"courier new";font-size:16px}*/
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
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        
      </div>
    </div>
  </div>
</header>
<br><br>
<div id="capaCinco" style="display: inline"> 
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 17px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 16px;"><em class="fas fa-minus-circle" style="font-size: 17px; color: #FFC72C;"></em> Regresar: </label> 
                                <?= Html::a('Regresar',  ['paramsevaluacion'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
                        ?>                            
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="CapaUno" style="display: inline;">
    <div class="row">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>
        <div class="col-md-12">
            <div class="card1 mb">

                <div class="w3-container">
                    <div class="w3-row">
                        <a href="javascript:void(0)" onclick="openCity(event, 'Params1');">
                            <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding"><em class="fas fa-tag" style="font-size: 20px; color: #49de70;"></em><strong> Niveles</strong></div>
                        </a>
                        <a href="javascript:void(0)" onclick="openCity(event, 'Params2');">
                            <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding"><em class="fas fa-list" style="font-size: 20px; color: #559FFF;"></em><strong> Evaluaciones</strong></div>
                        </a>                              
                        <a href="javascript:void(0)" onclick="openCity(event, 'Params3');">
                            <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding"><em class="fas fa-certificate" style="font-size: 20px; color: #FFC72C;"></em><strong> Competencias</strong></div>
                        </a>                 
                        <a href="javascript:void(0)" onclick="openCity(event, 'Params4');">
                            <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding"><em class="fas fa-sitemap" style="font-size: 15px; color: #827DF9;"></em><strong> Comportamiento</strong></div>
                        </a>
                        <a href="javascript:void(0)" onclick="openCity(event, 'Params5');">
                            <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding"><em class="fas fa-comment-alt" style="font-size: 20px; color: #30C5C8;"></em><strong> Respuestas</strong></div>
                        </a>
                        <a href="javascript:void(0)" onclick="openCity(event, 'Params6');">
                            <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding"><em class="fas fa-th-large" style="font-size: 20px; color: #258a40;"></em><strong> Bloques</strong></div>
                        </a>
			            <a href="javascript:void(0)" onclick="openCity(event, 'Params7');">
                            <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding"><em class="far fa-comments" style="font-size: 20px; color: #bf0678;"></em><strong><SPAN title="Agregar Mensajes FeedBack"> Mensajes</SPAN></strong></div>
                        </a>
                        <a href="javascript:void(0)" onclick="openCity(event, 'Params8');">
                            <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding"><em class="far fa-comment-alt" style="font-size: 20px; color: #21ccbb;"></em><strong> Mens. Result.</strong></div>                        </a>
                    </div>
                    <br>    
                    
                        <div id="Params1" class="w3-container city" style="display:inline">
                            <div class="row">
                                <div class="col-md-6">
                                    <label style="font-size: 15px;"> Seleccionar Nivel: </label>
                                    <?= $form->field($model, "nivel")->dropDownList($var2, ['prompt' => 'Seleccionar Nivel', 'id'=>"idnivel"]) ?>
                                </div>
                                <div class="col-md-6">
                                    <label style="font-size: 15px;"> Seleccionar Cargo: </label>
                                    <?php  echo $form->field($model, 'cargo')->dropDownList($listData2, ['prompt' => 'Seleccionar Cargo', 'id'=>'idcargo',])?>                                         
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">   
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card1 mb"> 
                                                <div onclick="saveniveles();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones1" >
                                                    Guardar
                                                </div>   
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card1 mb">  
                                                <?= 
                                                    Html::button('Ver reporte', ['value' => url::to(['verniveles']), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Verificar', 'style' => 'background-color: #707372'
                                                        ])
                                                ?>
                                                <?php
                                                     Modal::begin([
                                                        'header' => '<h4></h4>',
                                                        'id' => 'modal1',
                                                    ]);

                                                    echo "<div id='modalContent1'></div>";
                                                                            
                                                    Modal::end(); 
                                                ?>  
                                            </div>
                                        </div>
                                    </div>                     
                                </div>
                            </div>
                        </div>

                        <div id="Params2" class="w3-container city" style="display:none">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card1 mb">
                                        <?php if($sessiones == '2953' || $sessiones == '0' || $sessiones == '3205' || $sessiones == '3468' || $sessiones == '3229' || $sessiones == '6080' || $sessiones == '69' || $sessiones == '5658' || $sessiones == "1290" || $sessiones == "7756" || $sessiones == "6845") { ?>
                                            <label style="font-size: 15px;"> Ingresar Nombre Evaluacion: </label>
                                            <?= $form->field($model2, 'nombreeval')->textInput(['maxlength' => 250, 'id'=>'IdEvaluacion']) ?>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-12">  
                                                    <div class="card1 mb">
                                                        <div onclick="saveevaluacion();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                                                            Guardar Nombre
                                                        </div>
                                                    </div>
                                                </div>                                            
                                            </div>
                                            <br>
                                        <?php } ?>
                                        <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                                            <caption>Tabla datos</caption>
                                            <thead>             
                                                <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Id"; ?></label></th>
                                                <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Nombre Evaluaci�n"; ?></label></th>     
                                            </thead>
                                            <tbody>
                                                <?php
                                                    foreach ($txtlistevaluaciones as $key => $value) {
                                                        
                                                ?>
                                                    <tr>
                                                        <td><label style="font-size: 12px;"><?php echo  $value['idevaluacionnombre']; ?></label></td>
                                                        <td><label style="font-size: 12px;"><?php echo  $value['nombreeval']; ?></label></td>
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
                                        <?php if($sessiones == '2953' || $sessiones == '0' || $sessiones == '3205' || $sessiones == '3468' || $sessiones == '3229' || $sessiones == '6080' || $sessiones == '69' || $sessiones == '5658' || $sessiones == "1290" || $sessiones == "7756" || $sessiones == "6845") { ?>
                                            <label style="font-size: 15px;"> Ingresar Tipo Evaluacion: </label>
                                            <?= $form->field($model3, 'tipoevaluacion')->textInput(['maxlength' => 250,  'id'=>'IdTipoEvaluacion']) ?>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-12">  
                                                    <div class="card1 mb">
                                                        <div onclick="savetipo();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones3" >
                                                            Guardar Tipo
                                                        </div>
                                                    </div>
                                                </div>                                            
                                            </div>
                                            <br>
                                        <?php } ?>
                                        <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                                        <caption>Tabla datos</caption>
                                            <thead>             
                                                <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Id"; ?></label></th>
                                                <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Tipo Evaluaci�n"; ?></label></th>       
                                            </thead>
                                            <tbody>
                                                <?php
                                                    foreach ($txtlisttipo as $key => $value) {
                                                        
                                                ?>
                                                    <tr>
                                                        <td><label style="font-size: 12px;"><?php echo  $value['idevaluaciontipo']; ?></label></td>
                                                        <td><label style="font-size: 12px;"><?php echo  $value['tipoevaluacion']; ?></label></td>
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

                        <div id="Params3" class="w3-container city" style="display:none">
                            <div class="row">
                                <div class="col-md-6">
                                    <label style="font-size: 15px;"> Seleccionar Nivel: </label>
                                    <?= $form->field($model4, "idevaluacionnivel")->dropDownList($var2, ['prompt' => 'Seleccionar Nivel', 'id'=>"idnivel2"]) ?>
                                </div>
                                <div class="col-md-6">
                                    <label style="font-size: 15px;"> Ingresar Competencia: </label>
                                   <?= $form->field($model4, 'namecompetencia')->textInput(['maxlength' => 250,  'id'=>'Idnamecompetencia']) ?>                                        
                                </div>
                            </div>
			    <div class="row">   
                                <div class="col-md-6">
                                    <label style="font-size: 15px;"> Seleccionar Bloque: </label>
                                    <?= $form->field($model4, "idevaluacionbloques")->dropDownList($listData5, ['prompt' => 'Seleccionar Bloque', 'id'=>"idbloque"]) ?>
                                </div>
                                <div class="col-md-6">
                                    <label style="font-size: 15px;"> Evaluacion: </label>
                                   <?= $form->field($model4, 'idevaluaciontipo')->textInput(['maxlength' => 250,  'id'=>'IdEvaluacion2', 'readonly' => 'readonly', 'value' => $varnameeval]) ?>                                        
                                </div>
                            </div>

                            <br>
                            <div class="row">
                                <div class="col-md-6">   
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card1 mb"> 
                                                <div onclick="savecompetencias();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones4" >
                                                    Guardar
                                                </div>   
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card1 mb">  
                                                <?= 
                                                    Html::button('Ver reporte', ['value' => url::to(['vercompetencia']), 'class' => 'btn btn-success', 'id'=>'modalButton2', 'data-toggle' => 'tooltip', 'title' => 'Verificar', 'style' => 'background-color: #707372'
                                                        ])
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
                                    </div>
                                </div>
                                <div class="col-md-6">   
                                    <div class="row">     
                                        <div class="col-md-6">
                                            <div class="card1 mb"> 
                                                <?= Html::button('Importar competencias', ['value' => url::to('importarcompetencia'), 'class' => 'btn btn-success', 'id'=>'modalButton7',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Importar competencias', 'style' => 'background-color: #337ab7']) 
                                                ?>  
                                                <?php
                                                    Modal::begin([
                                                            'header' => '<h4>Importar Competencias </h4>',
                                                            'id' => 'modal7',
                                                        ]);

                                                    echo "<div id='modalContent7'></div>";
                                                                                
                                                    Modal::end(); 
                                                ?>
                                            </div>
                                        </div> 
                                    </div>                       
                                </div>                     
                                </div>
                            </div>
                        </div>

                        <div id="Params4" class="w3-container city" style="display:none">
                            <div class="row">                                
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Ingresar Comportamiento: </label>
                                   <?= $form->field($model5, 'namepregunta')->textInput(['maxlength' => 250,  'id'=>'Idnamepregunta']) ?>                                        
                                </div>
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Seleccionar Competencia: </label>
                                    <?= $form->field($model5, "idevaluacioncompetencia")->dropDownList($listData3, ['prompt' => 'Seleccionar Compentencia', 'id'=>"idcompetencia"]) ?>
                                </div>
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Evaluacion: </label>
                                   <?= $form->field($model5, 'idevaluacionnombre')->textInput(['maxlength' => 250,  'id'=>'IdEvaluacion2', 'readonly' => 'readonly', 'value' => $varnameeval]) ?>                                        
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">   
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card1 mb"> 
                                                <div onclick="savecomportamiento();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones5" >
                                                    Guardar
                                                </div>   
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card1 mb">  
                                                <?= 
                                                    Html::button('Ver reporte', ['value' => url::to(['vercomportamiento']), 'class' => 'btn btn-success', 'id'=>'modalButton3', 'data-toggle' => 'tooltip', 'title' => 'Verificar', 'style' => 'background-color: #707372'
                                                        ])
                                                ?>
                                                <?php
                                                     Modal::begin([
                                                        'header' => '<h4></h4>',
                                                        'id' => 'modal3',
                                                    ]);

                                                    echo "<div id='modalContent3'></div>";
                                                                            
                                                    Modal::end(); 
                                                ?>  
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="col-md-6">   
                                    <div class="row">     
                                        <div class="col-md-6">
                                            <div class="card1 mb"> 
                                                <?= Html::button('Importar comportamientos', ['value' => url::to('importarcomporta'), 'class' => 'btn btn-success', 'id'=>'modalButton6',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Importar comportamientos', 'style' => 'background-color: #337ab7']) 
                                                ?>  
                                                <?php
                                                    Modal::begin([
                                                            'header' => '<h4>Importar Comportamientos </h4>',
                                                            'id' => 'modal6',
                                                        ]);

                                                    echo "<div id='modalContent6'></div>";
                                                                                
                                                    Modal::end(); 
                                                ?>
                                            </div>
                                        </div> 
                                    </div>                       
                                </div>
                            </div>
                        </div>

                        <div id="Params5" class="w3-container city" style="display:none">
                        <div class="row">                                
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Ingresar Respuesta: </label>
                                   <?= $form->field($model6, 'namerespuesta')->textInput(['maxlength' => 250,  'id'=>'Idnamerespuesta']) ?>                                        
                                </div>
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Ingresar Valor: </label>
                                   <?= $form->field($model6, 'valor')->textInput(['maxlength' => 250,  'id'=>'Idvalorres']) ?>                                        
                                </div>
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Evaluacion: </label>
                                   <?= $form->field($model6, 'idevaluacionnombre')->textInput(['maxlength' => 250,  'id'=>'IdEvaluacion2', 'readonly' => 'readonly', 'value' => $varnameeval]) ?>                                        
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">   
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card1 mb"> 
                                                <div onclick="saverespuesta();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones5" >
                                                    Guardar
                                                </div>   
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card1 mb">  
                                                <?= 
                                                    Html::button('Ver reporte', ['value' => url::to(['verrespuesta']), 'class' => 'btn btn-success', 'id'=>'modalButton5', 'data-toggle' => 'tooltip', 'title' => 'Ver reporte', 'style' => 'background-color: #707372'
                                                        ])
                                                ?>
                                                <?php
                                                     Modal::begin([
                                                        'header' => '<h4></h4>',
                                                        'id' => 'modal5',
                                                    ]);

                                                    echo "<div id='modalContent5'></div>";
                                                                            
                                                    Modal::end(); 
                                                ?>  
                                            </div>
                                        </div>
                                    </div>                     
                                </div>
                            </div>
                        </div>
                        <div id="Params6" class="w3-container city" style="display:none">
                        <div class="row">                                
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Ingresar Bloque: </label>
                                   <?= $form->field($model7, 'namebloque')->textInput(['maxlength' => 250,  'id'=>'Idnamebloque']) ?>                                        
                                </div>
                                
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Evaluacion: </label>
                                   <?= $form->field($model7, 'idevaluacionnombre')->textInput(['maxlength' => 250,  'id'=>'IdEvaluacion2', 'readonly' => 'readonly', 'value' => $varnameeval]) ?>                                        
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">   
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card1 mb"> 
                                                <div onclick="savebloque();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones8" >
                                                    Guardar
                                                </div>   
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card1 mb">  
                                                <?= 
                                                    Html::button('Ver bloque', ['value' => url::to(['verbloque']), 'class' => 'btn btn-success', 'id'=>'modalButton8', 'data-toggle' => 'tooltip', 'title' => 'Ver bloque', 'style' => 'background-color: #707372'
                                                        ])
                                                ?>
                                                <?php
                                                     Modal::begin([
                                                        'header' => '<h4></h4>',
                                                        'id' => 'modal8',
                                                    ]);

                                                    echo "<div id='modalContent8'></div>";
                                                                            
                                                    Modal::end(); 
                                                ?>  
                                            </div>
                                        </div>
                                    </div>                     
                                </div>
                            </div>
                        </div>
			    <div id="Params7" class="w3-container city" style="display:none">
                            <div class="row">                                
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Ingresar Mensajes feedback: </label>
                                   <?= $form->field($model8, 'mensaje')->textInput(['maxlength' => 1000,  'id'=>'Idnamemensaje']) ?>                                        
                                </div>
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Seleccionar Competencia: </label>
                                    <?= $form->field($model8, "idevaluacioncompetencia")->dropDownList($listData3, ['prompt' => 'Seleccionar Compentencia', 'id'=>"idcompetencia2"]) ?>
                                </div>
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Seleccionar Cargo: </label>
                                    <?= $form->field($model8, "rol_competencia")->dropDownList($var3, ['prompt' => 'Seleccionar Cargo', 'id'=>"idcargo2"]) ?>
                                </div>
                            </div>
                            <div class="row">                                
                            	<div class="col-md-4">
                                    <label style="font-size: 15px;"> Seleccionar Tipo Competencia: </label>
                                    <?= $form->field($model8, "tipocompetencia")->dropDownList($var4, ['prompt' => 'Seleccionar tipo', 'id'=>"idtipocompetencia"]) ?>
                                </div>                                
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Evaluacion: </label>
                                   <?= $form->field($model8, 'idevaluacionnombre')->textInput(['maxlength' => 250,  'id'=>'IdEvaluacion2', 'readonly' => 'readonly', 'value' => $varnameeval]) ?>                                        
                                </div>
                            </div>

                            
                            <br>
                            <div class="row">
                                <div class="col-md-6">   
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card1 mb"> 
                                                <div onclick="savemensaje();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones5" >
                                                    Guardar
                                                </div>   
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card1 mb">  
                                                <?= 
                                                    Html::button('Ver reporte', ['value' => url::to(['vermensajes']), 'class' => 'btn btn-success', 'id'=>'modalButton10', 'data-toggle' => 'tooltip', 'title' => 'Ver mensaje', 'style' => 'background-color: #707372'
                                                        ])
                                                ?>
                                                <?php
                                                     Modal::begin([
                                                        'header' => '<h4></h4>',
                                                        'id' => 'modal10',
                                                    ]);

                                                    echo "<div id='modalContent10'></div>";
                                                                            
                                                    Modal::end(); 
                                                ?>  
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="col-md-6">   
                                    <div class="row">     
                                        <div class="col-md-6">
                                            <div class="card1 mb"> 
                                                <?= Html::button('Importar mensajes', ['value' => url::to('importarmensaje'), 'class' => 'btn btn-success', 'id'=>'modalButton9',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Importar mensajes', 'style' => 'background-color: #337ab7']) 
                                                ?>  
                                                <?php
                                                    Modal::begin([
                                                            'header' => '<h4>Importar mensajes </h4>',
                                                            'id' => 'modal9',
                                                        ]);

                                                    echo "<div id='modalContent9'></div>";
                                                                                
                                                    Modal::end(); 
                                                ?>
                                            </div>
                                        </div> 
                                    </div>                       
                                </div>
                            </div>
                        </div>                    
                
                
                <div id="Params8" class="w3-container city" style="display:none">
                            <div class="row">                                
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Ingresar Mensajes resultado menor 85: </label>
                                   <?= $form->field($model8, 'mensaje')->textInput(['maxlength' => 250,  'id'=>'Idnamemensaje2']) ?>                                        
                                </div> 
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Ingresar Mensajes resultado mayor 85: </label>
                                   <?= $form->field($model8, 'mensaje')->textInput(['maxlength' => 250,  'id'=>'Idnamemensaje22']) ?>                                        
                                </div>
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Seleccionar Competencia: </label>
                                    <?= $form->field($model8, "idevaluacioncompetencia")->dropDownList($listData3, ['prompt' => 'Seleccionar Compentencia', 'id'=>"idcompetencia22"]) ?>
                                </div>                                
                            </div>
                            <div class="row"> 
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Seleccionar Cargo: </label>
                                    <?= $form->field($model8, "rol_competencia")->dropDownList($var3, ['prompt' => 'Seleccionar Cargo', 'id'=>"idcargo22"]) ?>
                                </div>                               
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Seleccionar Tipo Competencia: </label>
                                    <?= $form->field($model8, "tipocompetencia")->dropDownList($var4, ['prompt' => 'Seleccionar tipo', 'id'=>"idtipocompetencia2"]) ?>
                                </div>                                
                                <div class="col-md-4">
                                    <label style="font-size: 15px;"> Evaluacion: </label>
                                   <?= $form->field($model8, 'idevaluacionnombre')->textInput(['maxlength' => 250,  'id'=>'IdEvaluacion22', 'readonly' => 'readonly', 'value' => $varnameeval]) ?>                                        
                                </div>
                            </div>

                            
                            <br>
                            <div class="row">
                                <div class="col-md-6">   
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card1 mb"> 
                                                <div onclick="savemensaje2();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones5" >
                                                    Guardar
                                                </div>   
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card1 mb">  
                                                <?= 
                                                    Html::button('Ver reporte', ['value' => url::to(['vermensajesresul']), 'class' => 'btn btn-success', 'id'=>'modalButton11', 'data-toggle' => 'tooltip', 'title' => 'Verificar', 'style' => 'background-color: #707372'
                                                        ])
                                                ?>
                                                <?php
                                                     Modal::begin([
                                                        'header' => '<h4></h4>',
                                                        'id' => 'modal11',
                                                    ]);

                                                    echo "<div id='modalContent11'></div>";
                                                                            
                                                    Modal::end(); 
                                                ?>  
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="col-md-6">   
                                    <div class="row">     
                                        <div class="col-md-6">
                                            <div class="card1 mb"> 
                                                <?= Html::button('Importar mensajes', ['value' => url::to('importarmensajeres'), 'class' => 'btn btn-success', 'id'=>'modalButton12',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Importar mensajes', 'style' => 'background-color: #337ab7']) 
                                                ?>  
                                                <?php
                                                    Modal::begin([
                                                            'header' => '<h4>Importar mensajes </h4>',
                                                            'id' => 'modal12',
                                                        ]);

                                                    echo "<div id='modalContent12'></div>";
                                                                                
                                                    Modal::end(); 
                                                ?>
                                            </div>
                                        </div> 
                                    </div>                       
                                </div>
                            </div>
                        </div>
                    
                </div>


            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<hr>


<script type="text/javascript">
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

    function saveniveles(){
        var varnivel = document.getElementById("idnivel").value;
        var varcargo = document.getElementById("idcargo").value;

        if (varnivel == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","No ha seleccionado el nivel","warning");
            return; 
        }else{
            if (varcargo == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","No ha seleccionado el cargo","warning");
                return; 
            }else{
                $.ajax({
                    method: "get",
                    url: "createnivel",
                    data: {
                        txtvarnivel : varnivel,
                        txtvarcargo : varcargo,
                    },
                    success : function(response){
                        var numRta2 =   JSON.parse(response);
                        console.log(numRta2);
                        if (numRta2 > 0) {
                            event.preventDefault();
                            swal.fire("!!! Advertencia !!!","Los datos ya estan en base de datos. Por favor ingrese nueva informacion","warning");
                            return; 
                        }else{
                            location.reload();
                        }                        
                    }
                });
            }
        }
    };

    function saveevaluacion(){
        var varIdEvaluacion = document.getElementById("IdEvaluacion").value;

        if (varIdEvaluacion == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","No ha ingresado el nombre de evaluaci�n","warning");
            return; 
        }else{
            $.ajax({
                method: "get",
                url: "createeval",
                data: {
                    txtvarIdEvaluacion : varIdEvaluacion,
                },
                success : function(response){
                    var numRtaEv =   JSON.parse(response);
                    location.reload();
                }
            });
        }
    };

    function savetipo(){
        var varIdTipoEvaluacion = document.getElementById("IdTipoEvaluacion").value;

        if (varIdTipoEvaluacion == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","No ha ingresado el tipo de evaluacion","warning");
            return; 
        }else{
            $.ajax({
                method: "get",
                url: "createtipo",
                data: {
                    txtvarIdTipoEvaluacion : varIdTipoEvaluacion,
                },
                success : function(response){
                    var numRtaT =   JSON.parse(response);
                    location.reload();
                }
            });
        }
    };

    function savecompetencias(){
        var varidnivel2 = document.getElementById("idnivel2").value;
        var varIdnamecompetencia = document.getElementById("Idnamecompetencia").value;
        var varIdTipoEvaluacion2 = "<?php echo $varideval; ?>";
	var varIdBloque = document.getElementById("idbloque").value;

        if (varidnivel2 == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","No ha seleccionado el nivel","warning");
            return; 
        }else{
            if (varIdnamecompetencia == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","No ha ingresado el nombre de la Competencia","warning");
                return; 
            }else{
                $.ajax({
                    method: "get",
                    url: "createcompetencia",
                    data: {
                        txtvaridnivel2 : varidnivel2,
                        txtvarIdnamecompetencia : varIdnamecompetencia,
                        txtvarIdTipoEvaluacion2 : varIdTipoEvaluacion2,
			txtvarIdBloque : varIdBloque,
                     },
                    success : function(response){
                        var numRtaT =   JSON.parse(response);
                        location.reload();
                    }
                });
            }
        }
    };

    function savecomportamiento(){
        var varIdnamepregunta = document.getElementById("Idnamepregunta").value;
        var varidcompetencia = document.getElementById("idcompetencia").value;
        var varIdEvaluacion2 = "<?php echo $varideval; ?>";

        if (varIdnamepregunta == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","No ha ingresado la pregunta","warning");
            return; 
        }else{
            if (varidcompetencia == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","No ha seleccionado la competencia","warning");
                return; 
            }else{
                $.ajax({
                    method: "get",
                    url: "createpreguntas",
                    data: {
                        txtvarIdnamepregunta : varIdnamepregunta,
                        txtvaridcompetencia : varidcompetencia,
                        txtvarIdEvaluacion2 : varIdEvaluacion2,
                    },
                    success : function(response){
                        var numRtaT =   JSON.parse(response);
                        location.reload();
                    }
                });
            }
        }
    };

    function saverespuesta(){
        var varIdnamerespuesta = document.getElementById("Idnamerespuesta").value;
        var varvalor= document.getElementById("Idvalorres").value;
        var varIdEvaluacion2 = "<?php echo $varideval; ?>";

        if (varIdnamerespuesta == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","No ha ingresado la respuesta","warning");
            return; 
        }else{
            if (varvalor == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","No ha ingresado el valor","warning");
                return; 
            }else{
                $.ajax({
                    method: "get",
                    url: "createrespuestas",
                    data: {
                        txtvarIdnamerespuesta : varIdnamerespuesta,
                        txtvarvalor : varvalor,
                        txtvarIdEvaluacion2 : varIdEvaluacion2,
                    },
                    success : function(response){
                        var numRtaT =   JSON.parse(response);
                        location.reload();
                    }
                });
            }
        }
    };

    function savebloque(){
        var varIdnamebloque = document.getElementById("Idnamebloque").value;
        var varIdEvaluacion2 = "<?php echo $varideval; ?>";

        if (varIdnamebloque == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","No ha ingresado el bloque","warning");
            return; 
        }else{
                $.ajax({
                    method: "get",
                    url: "createbloque",
                    data: {
                        txtvarIdnamebloque : varIdnamebloque,
                        txtvarIdEvaluacion2 : varIdEvaluacion2,
                    },
                    success : function(response){
                        var numRtaT =   JSON.parse(response);
                        location.reload();
                    }
                });
            }
        
    };

    function savemensaje(){
        
        var varIdcompetencia = document.getElementById("idcompetencia2").value;
        var varmensaje = document.getElementById("Idnamemensaje").value;
        var varIdcargo2 = document.getElementById("idcargo2").value;
        var varIdtipocompetencia = document.getElementById("idtipocompetencia").value;
        var varIdEvaluacion2 = "<?php echo $varideval; ?>";
        
    
            if (varmensaje == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","No ha ingresado un mensaje","warning");
                return; 
            }else{
            if (varIdcompetencia == "") {
                    event.preventDefault();
                    swal.fire("!!! Advertencia !!!","No ha ingresado una competencia","warning");
                    return; 
                }else{
                    if (varIdcargo2 == "") {
                       event.preventDefault();
                       swal.fire("!!! Advertencia !!!","No ha ingresado un cargo","warning");
                       return; 
                    }else{
                         if (varIdtipocompetencia == "") {
                            event.preventDefault();
                            swal.fire("!!! Advertencia !!!","No ha ingresado un tipo de competencia","warning");
                            return; 
                        }else{
     
                                $.ajax({
                                    method: "get",
                                    url: "createmensaje",
                                    data: {
                                        txtvarIdcompetencia : varIdcompetencia ,
                                        txtvarmensaje : varmensaje,
                                        txtvarIdcargo  : varIdcargo2 ,
                                        txtvarIdtipocompetencia  : varIdtipocompetencia ,
                                        txtvarIdEvaluacion2 : varIdEvaluacion2,
                                    },
                                    success : function(response){
                                        var numRtaT =   JSON.parse(response);
                                        location.reload();
                                    }
                                });
                            }
                        }
                    }
                }
                    
        };
        function savemensaje2(){
        
        var varIdcompetencia = document.getElementById("idcompetencia22").value;
        var varmensaje = document.getElementById("Idnamemensaje2").value;
        var varmensajemas85 = document.getElementById("Idnamemensaje22").value;
        var varIdcargo2 = document.getElementById("idcargo22").value;
        var varIdtipocompetencia = document.getElementById("idtipocompetencia2").value;
        var varIdEvaluacion2 = "<?php echo $varideval; ?>";
       
    
            if (varmensaje == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","No ha ingresado un mensaje resultado menor a 85","warning");
                return; 
            }else{
                if (varmensajemas85 == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","No ha ingresado un mensaje resultado mayor a 85","warning");
                return; 
              }else{
                if (varIdcompetencia == "") {
                        event.preventDefault();
                        swal.fire("!!! Advertencia !!!","No ha ingresado una competencia","warning");
                        return; 
                    }else{
                        if (varIdcargo2 == "") {
                        event.preventDefault();
                        swal.fire("!!! Advertencia !!!","No ha ingresado un cargo","warning");
                        return; 
                        }else{
                            if (varIdtipocompetencia == "") {
                                event.preventDefault();
                                swal.fire("!!! Advertencia !!!","No ha ingresado un tipo de competencia","warning");
                                return; 
                            }else{
     
                                $.ajax({
                                    method: "get",
                                    url: "createmensajeres",
                                    data: {
                                        txtvarIdcompetencia : varIdcompetencia ,
                                        txtvarmensaje : varmensaje,
                                        txtvarmensajemas85 : varmensajemas85,
                                        txtvarIdcargo  : varIdcargo2 ,
                                        txtvarIdtipocompetencia  : varIdtipocompetencia ,
                                        txtvarIdEvaluacion2 : varIdEvaluacion2,
                                    },
                                    success : function(response){
                                        var numRtaT =   JSON.parse(response);
                                        location.reload();
                                    }
                                });
                            }
                        }
                    }
                }
            }
                    
        };
</script>