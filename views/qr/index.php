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

  $varCedulaGerente =  (new \yii\db\Query())
  ->select(['tbl_proceso_cliente_centrocosto.documento_gerente'])
  ->from(['tbl_usuarios'])
  ->join('INNER JOIN','tbl_proceso_cliente_centrocosto',
        'tbl_proceso_cliente_centrocosto.documento_gerente = tbl_usuarios.usua_identificacion')
  ->where(['=','tbl_usuarios.usua_id',94])
  ->groupBy(['tbl_proceso_cliente_centrocosto.documento_gerente'])
  ->scalar();

  $varCedulaDirector =  (new \yii\db\Query())
  ->select(['tbl_proceso_cliente_centrocosto.documento_director'])
  ->from(['tbl_usuarios'])
  ->join('INNER JOIN','tbl_proceso_cliente_centrocosto',
        'tbl_proceso_cliente_centrocosto.documento_director = tbl_usuarios.usua_identificacion')
  ->where(['=','tbl_usuarios.usua_id',854])
  ->groupBy(['tbl_proceso_cliente_centrocosto.documento_director'])
  ->scalar();
  
  $varClienteDirector  =  (new \yii\db\Query())
  ->select(['tbl_proceso_cliente_centrocosto.id_dp_clientes',
            'tbl_proceso_cliente_centrocosto.cliente'])
  ->from(['tbl_proceso_cliente_centrocosto'])
  ->where(['=','tbl_proceso_cliente_centrocosto.documento_director',$varCedulaDirector])
  ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
  ->groupBy(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
  ->all();

  $varClienteGerente  =  (new \yii\db\Query())
  ->select(['tbl_proceso_cliente_centrocosto.id_dp_clientes',
            'tbl_proceso_cliente_centrocosto.cliente'])
  ->from(['tbl_proceso_cliente_centrocosto'])
  ->where(['=','tbl_proceso_cliente_centrocosto.documento_gerente',$varCedulaGerente])
  ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
  ->groupBy(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
  ->all();

  $varClienteAdmi  =  (new \yii\db\Query())
  ->select(['tbl_proceso_cliente_centrocosto.id_dp_clientes',
            'tbl_proceso_cliente_centrocosto.cliente'])
  ->from(['tbl_proceso_cliente_centrocosto'])
  ->groupBy(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
  ->all();

  $varArrayClientesGerente = array();
  foreach ($varClienteGerente as $key => $value) {
    array_push($varArrayClientesGerente, $value['id_dp_clientes']);
  }

  $varArrayClientesDirector = array();
  foreach ($varClienteDirector as $key => $value) {
    array_push($varArrayClientesDirector, $value['id_dp_clientes']);
  }

  $varArrayClientesAdmi = array();
  foreach ($varClienteAdmi as $key => $value) {
    array_push($varArrayClientesAdmi, $value['id_dp_clientes']);
  }

  $varArrayClientesGerentes = explode(",", str_replace(array("#", "'", ";", " "), '', implode(', ', $varArrayClientesGerente)));

  $varArrayClientesDirectors = explode(",", str_replace(array("#", "'", ";", " "), '', implode(', ', $varArrayClientesDirector)));
  
  $varArrayClientesAdmis = explode(",", str_replace(array("#", "'", ";", " "), '', implode(', ', $varArrayClientesAdmi)));

  $varListaClientes = array();

    if ($roles == '293' || $roles == '299') {
        $varListaClientes = $varArrayClientesGerentes;
    }else{
        if ($roles == '301') {
            $varListaClientes = $varArrayClientesDirectors;
        }else{
            if ($roles == '270') {
                $varListaClientes = $varArrayClientesAdmis;
            }else{
                $varListaClientes = 0;
            }
        }
    }


  $varTotalCasos =  (new \yii\db\Query())
  ->select(['*'])
  ->from(['tbl_qr_casos'])
  ->where(['=','tbl_qr_casos.estatus',0])
  ->count(); 


  $varTotalEstados = (new \yii\db\Query())
  ->select(['COUNT(id_estado) as Cantidad','id_estado' ])
  ->from(['tbl_qr_casos'])
  ->where(['=','tbl_qr_casos.estatus',0])
  ->groupBy(['id_estado'])
  ->all();

  $varEstados = (new \yii\db\Query())
  ->select(['id_estado' ])
  ->from(['tbl_qr_casos'])
  ->where(['=','tbl_qr_casos.estatus',0])
  ->groupBy(['id_estado'])
  ->all();

  

  foreach ($varTotalEstados as $key => $value) {
    
    if ($value['id_estado'] == 2) {
        $varCerrado = ($value['Cantidad']);
        $varColorA = '#FFC72C';
    }
    if ($value['id_estado'] == 4) {
        $varProceso =($value['Cantidad']) ;
        $varColorM = '#00968F';
    }
    if ($value['id_estado'] == 9) {
        $varAbierto = ($value['Cantidad']) ;
        $varColorC = '#0072CE';
    }
    if ($value['id_estado'] == 8) {
        $varRevisionCX = ($value['Cantidad'] ) ;
        $varColorK = '#6F7271';
    }
    if ($value['id_estado'] == 5) {
        $varRevisionGer = ($value['Cantidad']) ;
        $varColorN = '#4F758B';
    }
  } 
  $varAlerta = 0;


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
        background-image: url('../../images/Banner_Ev_Desarrollo.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

    .redondo-primary {
        background: #337ab7;
        color: #fff;
        padding: 2px 0px;
        border-radius: 20px;
        /* font-size: 13px; */
        text-align: center;
        font-weight: bold;
        width:60px
    }

    .redondo-danger {
        background: red;
        color: #fff;
        padding: 2px 0px;
        border-radius: 20px;
        /* font-size: 13px; */
        text-align: center;
        font-weight: bold;
        width:60px
    }

    .redondo-success {
        background: #4298b4;
        color: #fff;
        padding: 2px 0px;
        border-radius: 20px;
        /* font-size: 13px; */
        text-align: center;
        font-weight: bold;
        width:70px
    }
    span {
        font-size:14px !important;
    }

    button.dt-button, div.dt-button, a.dt-button, input.dt-button{
        background-color:#4298b4 !important;
        color:#fff !important;
    }

    .text-center{
      
    align-items: center;
    flex-direction: row;
    justify-content:center;
    padding: 10px 5px !important;
    }

    label {
        font-size: 16px;
    }
    .cell-with-text {
        white-space: nowrap;      /* Evita saltos de línea */
        overflow: hidden;         /* Oculta el contenido que excede el contenedor */
        text-overflow: ellipsis;  /* Muestra puntos suspensivos (...) cuando el texto se corta */
    }

    .semaforo-verde {
    position: absolute;
    width: 4rem;
    height: 14px;
    border: 2px solid green;
    border-radius: 50px;
    display: block;
    background: green;

    }


    .semaforo-amarillo {
    width: 4rem;
    height: 14px;
    border:2px solid #ffaf00;
    border-radius:50px;
    display:block;
    position: absolute;
    background: linear-gradient(to right, #ffaf00 60%, transparent 60%)
    }

    .semaforo-rojo {
    width:4rem;
    height:14px;
    border:2px solid #E83718;
    border-radius:50px;
    display:block;
    position:absolute;
    background: linear-gradient(to right, #E83718 30%, transparent 30%)
    }

    
    .dias{
    font-size: 14px;
    position: relative;
    left: 45px;
    font-weight: bold;
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
    .w3-code{width:auto;background-color:#fff;padding:8px 15px;border-left:4px solid #4CAF50;word-wrap:break-word}
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
<br>
<br>
<div id="capaIdPrincipal" class="capaPrincipal" style="display: inline;">


    <?php $form = ActiveForm::begin(["method" => "post","enableClientValidation" => true,'options' => ['enctype' => 'multipart/form-data'],'fieldConfig' => ['inputOptions' => ['autocomplete' => 'off']]]) ?>

    

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">         
                
                <div class="w3-container">
            
                    <div class="w3-row">

                        <a href="javascript:void(0)" onclick="openCity(event, 'Resumen');">
                            <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                            <label style="font-size: 20px;"><em class="fas fa-chart-bar" style="font-size: 22px; color: #00968F;"></em><strong>  <?= Yii::t('app', 'Resumen General') ?></strong></label>
                            </div>
                        </a>
                        <a href="javascript:void(0)" onclick="openCity(event, 'Registro');">
                            <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                            <label style="font-size: 20px;"><em class="fas fa-tag" style="font-size: 22px; color: #CE0F69;"></em><strong>  <?= Yii::t('app', 'Crear Caso') ?></strong></label>
                            </div>
                        </a>
                        <a href="javascript:void(0)" onclick="openCity(event, 'Ver');">
                            <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                            <label style="font-size: 20px;"><em class="fas fa-list-alt" style="font-size: 22px; color: #0072CE;"></em><strong>  <?= Yii::t('app', 'Ver Información') ?></strong></label>
                            </div>
                        </a>

                    </div>

                    <!-- Proceso de resumen general -->
                    <div id="Resumen" class="w3-container city" style="display:inline;">

                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card1 mb">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card1 mb">
                                                <label style="font-size: 16px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #00968F;"></em><?= Yii::t('app', ' Cantidad de Casos') ?></label>
                                                <label style="font-size: 25px;" class="text-center"><?= Yii::t('app', $varTotalCasos) ?></label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card1 mb">
                                                <label style="font-size: 16px;"><em class="fas fa-exclamation" style="font-size: 20px; color: #00968F;"></em><?= Yii::t('app', ' Información Importante') ?></label>
                                                <label style="font-size: 16px;" ><?= Yii::t('app', 'Te comentamos que estos datos están actualizados del último año, si quieres vizualizar años anteriores te envitamos a ingresar al módulo de PBI.') ?></label>
                                            </div>
                                        </div>  
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card1 mb">
                                                <label style="font-size: 16px;"><em class="fas fa-chart-pie" style="font-size: 20px; color: #00968F;"></em><?= Yii::t('app', ' Cantidades Tipo de Estados') ?></label>
                                                <div id="containerA" class="highcharts-container" style="height: 345px;"></div> 
                                            </div>
                                        </div> 

                                        <div class="col-md-6">
                                            <div class="card1 mb">
                                                <label style="font-size: 16px;"><em class="fas fa-chart-pie" style="font-size: 20px; color: #00968F;"></em><?= Yii::t('app', ' Eficiencia de Respuesta') ?></label>
                                                <div id="containerB" class="highcharts-container" style="height: 345px;"></div> 
                                            </div>
                                        </div>  
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>

                    <!-- Proceso de crear caso--> 
                    <div id="Registro" class="w3-container city" style="display:none;">
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card1 mb">

                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="col-md-6">
                                                <label style="font-size: 16px;"><em class="fas fa-check" style="font-size: 20px; color: #CE0F69;"></em><?= Yii::t('app', ' Seleccionar Cliente') ?><span style="color:red;"> *</span></label>
                                                <?=  $form->field($modelcaso, 'cliente', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosClienteCentrocosto::find()->where(['=','anulado',0])->andwhere(['=','estado',1])->andwhere(['IN','id_dp_clientes',$varListaClientes])->groupby(['id_dp_clientes'])->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                                    [
                                                        'prompt'=>'Seleccionar...',
                                                        'id'=>'iddvarCliente',
                                                        'required' => 'required' ,
                                                ])->label('');
                                                ?> 
                                            </div>

                                            <div class="col-md-6">
                                                <label style="font-size: 16px;"><em class="fas fa-check" style="font-size: 20px; color: #CE0F69;"></em><?= Yii::t('app', ' Seleccionar Tipo Solicitud') ?><span style="color:red;"> *</span></label>
                                                <?=  $form->field($modelcaso, 'id_estado_caso', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Tipopqrs::find()->orderBy(['id'=> SORT_DESC])->all(), 'id', 'tipo_de_dato'),
                                                [
                                                    'id' => 'iddvarSolicitud',
                                                    'prompt'=>'Seleccionar...',
                                                    'required' => 'required' ,
                                                ])->label(''); 
                                                ?>   
                                            </div>
                                            
                                        
                                            
                                            <div class="col-md-6">
                                                <label style="font-size: 16px;"><em class="fas fa-check" style="font-size: 20px; color: #CE0F69;"></em><?= Yii::t('app', ' Ingresar Comentarios') ?><span style="color:red;"> *</span></label>
                                                <?= $form->field($modelcaso, 'comentario',['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['maxlength' => 10000000000, 'id'=>'idvarComentarios', 'placeholder'=>'Ingresar Comentarios', 'required' => 'required' ,'style' => 'resize: vertical;','rows' => '8'])?>
                                            </div>
                                    

                                            <div class="col-md-6">
                                                <label style="font-size: 16px;"><em class="fas fa-check" style="font-size: 20px; color: #CE0F69;"></em><?= Yii::t('app', ' Anexar Documentos') ?></label>
                                                <br>
                                                <?= $form->field($modelo, 'file', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->fileInput(["class"=>"input-file" ,'id'=>'idvarFile', 'style'=>'font-size: 18px;','extensions' => ' pdf','docx', 'maxSize' => 1024*1024*1024])->label('') ?>
                                                <label style="font-size: 15px;"> <?= Yii::t('app', ' ___________________ ') ?></label><br>
                                                <label style="font-size: 16px;"> <?= Yii::t('app', ' Tener en cuenta... ') ?></label><br>
                                                <p style="font-size: 15px;"> <?= Yii::t('app', ' * Los archivos a subir deben tener la extensión correcta .docx, .pdf ') ?></p>
                                                <p style="font-size: 15px;"> <?= Yii::t('app', ' * Recomendable que los archivos no tengan espacios o signos de puntuación de más.') ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card1 mb">
                                            <label style="font-size: 16px;"><em class="fas fa-save" style="font-size: 16px; color: #CE0F69;"></em><?= Yii::t('app', ' Guardar Datos') ?></label>
                                            
                                            <?= Html::submitButton("Guardar", ["class" => "btn btn-primary", "onclick" => "verificardata();"]) ?>
                                        </div>
                                    </div>
                                
                            
                                
                                    
                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>                                              
                                       
                    <!-- Proceso de ver tabla -->
                    <div id="Ver" class="w3-container city" style="display:none;">
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card1 mb">
                                    <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
                                    <caption><label><em class="fas fa-list" style="font-size: 25px; color: #0072CE;"></em> <?= Yii::t('app', 'Reporte Casos PQRSF') ?></label></caption>
                                        <thead>
                                            <tr>
                                                <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Id Caso ') ?></label></th>
                                                <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Tipo de Solicitud') ?></label></th>                            
                                                <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Cliente ') ?></label></th>
                                                <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Radicado Por:') ?></label></th>
                                                <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 16px; width:80px;"><?= Yii::t('app', 'Fecha de Radicación ') ?></label></th>
                                                <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 16px; width:80px;"><?= Yii::t('app', 'Fecha de Cierre ') ?></label></th>
                                                <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Detalle PQRS ') ?></label></th>
                                                <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 16px; width:125px;"><?= Yii::t('app', 'Estado Actual') ?></label></th>
                                                <th scope="col" class="text-center" style="background-color: #b0cdd6; width:125px;"><label style="font-size: 16px;"><?= Yii::t('app', 'Días Transcurridos') ?></label></th>
                                                <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Cumplimiento') ?></label></th>
                                                
                                                <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Ver Historico') ?></label></th>
                                                <?php if ($roles == 270) { ?>
                                                <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Acción Gestionar ') ?></label></th>
                                                <?php } ?>

                                                <?php if ($varEstados != 9 && $roles != 270) {?>
                                                    <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Acción Gestionar ') ?></label></th>
                                                    
                                                <?php  } ?>

                                                <?php if ($roles == 270) { ?>
                                                <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Acción Eliminar ') ?></label></th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                foreach ($model as $key => $value) {
                                                    $varIdCaso = $value['idcaso'];
                                                    $varNumCaso = $value['numero_caso'];
                                                    $varTipoDato = $value['tipo_de_dato'];
                                                    $varComentarios = $value['comentario'];
                                                    $varCliente = $value['cliente'];
                                                    $varNombreCliente = $value['clientearea'];                                
                                                    $varUsuario = $value['nombre'];
                                                    $varDocUsuario = $value['documento'];
                                                    $varFechaRespuesta = $value['fecha_respuesta'];

                                                    $fechaFormateada = date("Y-m-d", strtotime($varFechaRespuesta));

                                                    $varParams = [':varParams' => $varDocUsuario];
                                                    $varEmail = Yii::$app->dbjarvis->createCommand('
                                                    SELECT 
                                                    email 
                                                    FROM dp_usuarios_red 
                                                    WHERE 
                                                        dp_usuarios_red.documento = :varParams ')->bindValues($varParams)->queryScalar();

                                                    $varArea = $value['area'];
                                                    $varTipologia = $value['tipologia'];
                                                    $varEstado = $value['estado'];
                                                    $varIdEstado = $value['idestado'];
                                                    $varFechaCreacion = $value['fecha_creacion'];
                                                    $varEstadoproceso = $value['id_estado'];                                
                                                    $varnombreestado = $value['estado1'];

                                                    $varNombreCliente = (new \yii\db\Query())
                                                                        ->select(['tbl_proceso_cliente_centrocosto.cliente'])
                                                                        ->from(['tbl_proceso_cliente_centrocosto'])
                                                                        ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$varCliente])
                                                                        ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                                                                        ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                                                                        ->scalar();

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
                                                    $fechaFormateadaRadicacion = date("Y-m-d", strtotime($varFechaCreacion));



                                                    foreach ($listacumplimiento as $key => $valuee) {
                                                        $meta = $valuee['indicador'];
                                                        $diaverde1 = $valuee['diaverde1'];
                                                        $diaverde2 = $valuee['diaverde2'];
                                                        $diaamarillo1 = $valuee['diaamarillo1'];
                                                        $diaamarillo2 = $valuee['diaamarillo2'];
                                                        $diarojo1 = $valuee['diarojo1'];
                                                        $diarojo2 = $valuee['diarojo2'];                        
                                                    }

                                                    $fecha1 = new Datetime($varFechaCreacion); //fecha creacion
                                                    $fecha2= new datetime('now'); //fecha actual
                                                    $dias = $fecha1->diff($fecha2); //diferencia entre la fecha de creacion y fecha actual 
                                                    
                                                    //mp($fecha1);
                                                    //mp($fecha2);
                                                    //mp($dias); 

                                                    $diastrans = $dias->days; //dias trasncurridos
                                                    $diasfaltan =$diastrans -  $meta; //dias que han pasado sin responder

                                                    //mp($diastrans);
                                                    //mp($diasfaltan);

                                            

                                            ?>
                                                <tr>
                                                    <td><label style="font-size: 15px;"><?php echo  $varNumCaso; ?></label></td>
                                                    <td><label style="font-size: 15px;"><?php echo  $varTipoDato; ?></label></td>                                
                                                    <td><label style="font-size: 15px;"><?php echo  $varNombreCliente; ?></label></td>
                                                    <td><label style="font-size: 15px;"><?php echo  $varUsuario; ?></label></td>                               
                                                    <td><label style="font-size: 15px;"><?php echo  $fechaFormateadaRadicacion; ?></label></td>
                                                    <?php if ($varnombreestado == 'Cerrado') { ?>
                                                        <td><label style="font-size: 15px;"><?php echo $fechaFormateada; ?></label></td>
                                                    <?php
                                                    }else{ ?>                                    
                                                            <td><label style="font-size: 15px;"><?= Yii::t('app', '-') ?></label></td>
                                                    <?php } ?>
                                                    <td class="resumen-celda"><label style="font-size: 15px;"></label>
                                                    <div class="resumen"><label style="font-size: 15px;"><?php echo strlen($varComentarios) > 50 ? substr($varComentarios, 0, 50)."..." : $varComentarios; ?></label></div>
                                                    <div class="completo hidden"><label style="font-size: 15px;"><?php echo $varComentarios; ?></label></div></td>
                                                    <?php if ($varnombreestado == 'Cerrado') { ?>
                                                        <td><label style="font-size: 15px;"><?php echo $varnombreestado; ?></label></td>
                                                        <?php
                                                        }else{ ?>                                    
                                                            <td><label style="font-size: 15px;"><?php echo  $varnombreestado; ?></label></td>
                                                        <?php } ?>

                                                

                                                    <?php if($diastrans === 0){?>
                                                        <td style="width: 100px;">
                                                            <div>
                                                            <span class="semaforo-verde"></span>
                                                            <label class="dias"><?= Yii::t('app', 'Tienes '.$diasfaltan.' Días') ?></label>
                                                            </div>
                                                        </td>

                                                    <?php } ?>
                                                    <?php if(($diastrans >= $diaverde1) && ($diastrans <= $diaverde2)){?>
                                                        <td style="width: 100px;" ><span class="semaforo-verde"></span><label class="dias"><?= Yii::t('app','Tienes '.$diasfaltan.' Días') ?></label></td>

                                                    <?php } ?>
                                                    <?php if(($diastrans >= $diaamarillo1) && ($diastrans <= $diaamarillo2)){?>
                                                        <td style="width: 100px;" ><span class="semaforo-amarillo" ></span><label class="dias"><?= Yii::t('app','Tienes '.$diasfaltan.' Días') ?></label></td>

                                                    <?php } ?>
                                                    <?php if(($diastrans >= $diarojo1)){?>
                                                        <td style="width: 100px;" ><span class="semaforo-rojo" ></span><label class="dias"><?= Yii::t('app', 'Días Vencidos  '.$diasfaltan) ?> </label></td>

                                                    <?php } ?>

                                                    <?php if(($diastrans > $meta) && ($varEstadoproceso != 2)){?>
                                                        <td><label style="font-size: 15px;"><?= Yii::t('app', 'No haz Respondido') ?></label></td>

                                                    <?php } ?>  
                                                    <?php if(($diastrans > $meta) && ($varEstadoproceso == 2)){?>
                                                        <td><label style="font-size: 15px;"><?= Yii::t('app', 'Cumplió fuera de la Fecha') ?></label></td>

                                                    <?php } ?>
                                                    <?php if(($diastrans <= $meta)&& ($varEstadoproceso != 2)){?>
                                                        <td><label style="font-size: 15px;"><?= Yii::t('app', 'Tienes tiempo para Cumplir') ?></label></td>

                                                    <?php } ?>
                                                    <?php if(($diastrans <= $meta)&& ($varEstadoproceso == 2)){?>
                                                        <td><label style="font-size: 15px;"><?= Yii::t('app', 'Cumplió') ?></label></td>

                                                    <?php } ?>
                                                    
                                                    
                                                    <td class="text-center">
                                                        <?= Html::a('<em class="fas fa-search" style="font-size: 12px; color: #3e95b8;"></em>',  ['verqyr','idcaso'=> $value['idcaso']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Buscar']) ?>
                                                    </td>                                                 
                                                    <td class="text-center">

                                                    <?php if ($varEstadoproceso == 4 && $roles != 301 && $roles != 270) {?>

                                                    <?= Html::a('<em class="fas fa-pencil-alt" style="font-size: 15px; color: #43ba45;"></em>',  ['asignarqyr','idcaso'=> $value['idcaso']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Gestionar']) ?>

                                                    <?php } elseif ($varEstadoproceso == 5 && $roles == '301') {?>

                                                    <?= Html::a('<em class="fas fa-pencil-alt" style="font-size: 15px; color: #43ba45;"></em>',  ['asignarqyr','idcaso'=> $value['idcaso']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Gestionar']) ?>

                                                    <?php } elseif ($varEstadoproceso == 9 && $roles == 270) {?>

                                                    <?= Html::a('<em class="fas fa-pencil-alt" style="font-size: 15px; color: #43ba45;"></em>',  ['asignarqyr','idcaso'=> $value['idcaso']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Gestionar']) ?>

                                                    <?php } elseif ($varEstadoproceso == 8 && $roles == 270) {?>

                                                    <?= Html::a('<em class="fas fa-pencil-alt" style="font-size: 15px; color: #43ba45;"></em>',  ['asignarqyr','idcaso'=> $value['idcaso']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Gestionar']) ?>

                                                    <?php } else { ?>

                                                    <!-- Agrega aquí el icono de equis -->
                                                    <label><em class="fas fa-times" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', '') ?></label>

                                                    <?php } ?>

                                                       
                                                    </td>
                                                    <?php if ($roles == 270) { ?>
                                                    <td class="text-center">
                                                        <?= Html::a('<em class="fas fa-trash-alt" style="font-size: 12px; color: #d95416;"></em>',  ['deleteqr','idcaso'=> $value['idcaso']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                                                    </td>
                                                    <?php } ?>
                                                </tr>
                                            <?php 
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card1 mb">
                                                <label style="font-size: 20px;"><em class="fas fa-download" style="font-size: 20px; color: #0072CE;"></em><?= Yii::t('app', ' Descargar Información:') ?></label>
                                                    <a id="dlink" style="display:none;"></a>
                                                    <button class="btn btn-info" style="background-color: #4298B4" id="btn2">Aceptar</button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card1 mb">
                                                <label style="font-size: 16px;"><em class="fas fa-download" style="font-size: 20px; color: #0072CE;"></em><?= Yii::t('app', ' Descargar Casos PQRS') ?></label>
                                                <a id="dlink" style="display:none;"></a>
                                                <button  class="btn btn-info" style="background-color: #4298B4" id="btn">Aceptar</button>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <table id="tablaDescarga" hidden="hidden" class="table table-striped table-bordered tblResDetFreed">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Director') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Caso') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Sociedad') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Ciudad') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Cliente') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Tipo') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Tipología') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Descripción') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Radicada por') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Fecha de Radicación') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Asignado a') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Fecha de Asignación') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Fecha Envío Respuesta') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Fecha Revisión CX') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Revisión CX') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Fecha Revisión Gerente') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Revisión Gerente') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Estado Actual') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Días Vencidos') ?></label></th>
                                            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 16px;"><?= Yii::t('app', 'Días Restantes') ?></label></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    
                                        <?php
                                                
                                                 

                                                    foreach ($dataProviderInfo as $key => $value) {

                                                        $txtrespuesta = $value['fecha_respuesta'];
                                                        $txtradicada = $value['nombre'];
                                                        $txtasignada = $value['numero_caso'];
                                                        $txtcaso_id = $value['numero_caso'];
                                                        $txtfecha_radicacion = $value['fecha_creacion'];
                                                        $txtfecha_asignacion = $value['fecha_asignacion'];
                                                        $txtrevision_cx = $value['revision_cx'];
                                                        $txtfecha_revision_cx = $value['fecha_revisioncx'];
                                                        $txtrevision_gerente = $value['revision_gerente'];
                                                        $txtfecha_revision_gerente = $value['fecha_revision_gerente']; 
                                                        $txtfecha_creacion = $value['fecha_creacion'];
                                                        $varIdClientes = $value['cliente']; 
                                                        $varIdComentario = $value['comentario']; 
                                                        $varFecha_radicado = $value['fecha_creacion'];
                                                        $txttipo_solicitud_id = $value['id_solicitud'];
                                                        $txtestado_actual_id = $value['id_estado'];
                                                        $txttipologia_id  = $value['id_estado'];

                                                        $txtrevisioncxx = (new \yii\db\Query())
                                                                            ->select(['usua_nombre'])
                                                                            ->from(['tbl_usuarios'])
                                                                            ->where(['=','usua_id',$txtrevision_cx])
                                                                            ->scalar();


                                                        $txtrevision_gerentee = (new \yii\db\Query())
                                                                            ->select(['usua_nombre'])
                                                                            ->from(['tbl_usuarios'])
                                                                            ->where(['=','usua_id',$txtrevision_gerente])
                                                                            ->scalar();

                                                        $varCiudad = (new \yii\db\Query())
                                                                            ->select(['tbl_proceso_cliente_centrocosto.ciudad'])
                                                                            ->from(['tbl_proceso_cliente_centrocosto'])
                                                                            ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$varIdClientes])
                                                                            ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                                                                            ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
                                                                            ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                                                                            ->Scalar();

                                                                        
                                                        $txttipo_solicitud  = (new \yii\db\Query())
                                                                            ->select(['tipo_de_dato'])
                                                                            ->from(['tbl_qr_tipos_de_solicitud'])
                                                                            ->where(['=','id',$txttipo_solicitud_id])
                                                                            ->Scalar();

                                                        $txtestado_actual= (new \yii\db\Query())
                                                                            ->select(['nombre'])
                                                                            ->from(['tbl_qr_estados'])
                                                                            ->where(['=','id_estado',$txtestado_actual_id])
                                                                            ->Scalar();

                                                        $txttipologia  = (new \yii\db\Query())
                                                                            ->select(['tipologia'])
                                                                            ->from(['tbl_qr_tipologias'])
                                                                            ->where(['=','id',$txttipologia_id])
                                                                            ->Scalar();

                                                        $varCliente = (new \yii\db\Query())
                                                                            ->select(['tbl_proceso_cliente_centrocosto.cliente'])
                                                                            ->from(['tbl_proceso_cliente_centrocosto'])
                                                                            ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$varIdClientes])
                                                                            ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                                                                            ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
                                                                            ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                                                                            ->Scalar();

                                                                        // var_dump($varCliente);
                                                
                                                        $VarDirectoresList = (new \yii\db\Query())
                                                                            ->select(['tbl_proceso_cliente_centrocosto.director_programa'])
                                                                            ->from(['tbl_proceso_cliente_centrocosto'])
                                                                            ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$varIdClientes])
                                                                            ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                                                                            ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
                                                                            ->groupby(['tbl_proceso_cliente_centrocosto.director_programa'])
                                                                            ->All();

                                                        //var_dump($VarDirectoresList);
                                                        $varArrayDirectores = array();
                                                    // var_dump($VarDirectoresList);
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
                                                        //var_dump($varGerentesList);

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
                                                                            //mp($varPcrcList);
                                                        $varArrayPcrc = array();
                                                        foreach ($varPcrcList as $key => $value) {
                                                            array_push($varArrayPcrc, $value['varListPcrc']);
                                                        }
                                                        $varVerificaPcrc = implode("; ", $varArrayPcrc);
                                                        //mp($varPcrcList);

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

                                                        //var_dump($varsociedad);

                                                        $listacumplimiento = (new \yii\db\Query())
                                                                        ->select(['*'])
                                                                        ->from(['tbl_qr_cumplimiento'])
                                                                        ->where(['=','anulado',0])
                                                                        ->All();

                                                        //var_dump($listacumplimiento);
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
                                                        
                                                        
                                                        $fecha1= new Datetime($txtfecha_creacion); //fecha creacion
                                                        $fecha2= new datetime('now'); //fecha actual
                                                        $dias = $fecha1->diff($fecha2); //diferencia entre la fecha de creacion y fecha actual 
                                                        
                                                         

                                                        $diastrans = $dias->days; //dias trasncurridos
                                                        $diasfaltan = $meta - $diastrans; //dias que han pasado sin responder

                                                       

                                                        if ($diastrans < 1) {
                                                            $diastrans = 0;
                                                        }
                                                    
                                                        $cumplimiento = 0 - (($diastrans / $meta) * 100);// porcentaje del cumplimiento 
                                        
                                                ?>
                                        <tr>
                                            <td><label style="font-size: 15px;"><?php echo  $varDirectoresListado; ?></label></td>
                                            <td><label style="font-size: 15px;"><?php echo  $txtcaso_id; ?></label></td>                                
                                            <td><label style="font-size: 15px;"><?php echo  $varsociedad; ?></label></td>
                                            <td><label style="font-size: 15px;"><?php echo  $varCiudad; ?></label></td>                               
                                            <td><label style="font-size: 15px;"></label></td>
                                            <td><label style="font-size: 15px;"><?php echo  $txttipo_solicitud; ?></label></td>                                
                                            <td><label style="font-size: 15px;"><?php echo  $txttipologia; ?></label></td>
                                            <td><label style="font-size: 15px;"><?php echo  $varIdComentario; ?></label></td>                               
                                            <td><label style="font-size: 15px;"><?php echo  $txtradicada; ?></label></td>
                                            <td><label style="font-size: 15px;"><?php echo  $txtfecha_radicacion; ?></label></td>
                                            <td><label style="font-size: 15px;"><?php echo  $txtasignada; ?></label></td>                                
                                            <td><label style="font-size: 15px;"><?php echo  $txtfecha_asignacion; ?></label></td>
                                            <td><label style="font-size: 15px;"><?php echo  $txtrespuesta; ?></label></td>                               
                                            <td><label style="font-size: 15px;"><?php echo  $txtfecha_revision_cx; ?></label></td>
                                            <td><label style="font-size: 15px;"><?php echo  $txtrevisioncxx; ?></label></td>
                                            <td><label style="font-size: 15px;"><?php echo  $txtfecha_revision_gerente; ?></label></td>                                
                                            <td><label style="font-size: 15px;"><?php echo  $txtrevision_gerentee; ?></label></td>
                                            <td><label style="font-size: 15px;"><?php echo  $txtestado_actual; ?></label></td>                               
                                            <td><label style="font-size: 15px;"><?php echo  $diastrans; ?></label></td>
                                            <td><label style="font-size: 15px;"><?php echo  $diasfaltan; ?></label></td>                                
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
    </div>
   
  <?php ActiveForm::end(); ?>
</div>
<hr>
<script type="text/javascript">
    

    $(document).ready( function () {
    $('#myTable').DataTable({
      responsive: true,
      fixedColumns: true,
      select: true,
      order:[[0, 'desc']],
      "language": {
        "lengthMenu": "Cantidad de Datos a Mostrar _MENU_ ",
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
   var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta charset="utf-8"/><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64 = function (s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            }, format = function (s, c) {
                return s.replace(/{(\w+)}/g, function (m, p) {
                    return c[p];
                })
            }
        return function (table, name) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = {
                worksheet: name || 'Worksheet',
                table: table.innerHTML
            }
            console.log(uri + base64(format(template, ctx)));
            document.getElementById("dlink").href = uri + base64(format(template, ctx));
            document.getElementById("dlink").download = "Reporte Quejas y Reclamos";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('myTable', 'Archivo Plan', name+'.xls')
    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

    // Data retrieved from https://netmarketshare.com
// Data retrieved from https://olympics.com/en/olympic-games/beijing-2022/medals
// Data retrieved from https://olympics.com/en/olympic-games/beijing-2022/medals
// Data retrieved from https://netmarketshare.com/
// Build the chart



Highcharts.chart('containerA', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: '',
        align: ''
    },
    accessibility: {
        point: {
            valueSuffix: '%'
        }
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: false
            },
            showInLegend: true
        }
    },
    series: [{
        name: 'Data:',
        colorByPoint: true,
        data: [<?php           
                    foreach ($varCantidadestados as $key => $value) {
                        $varColor = null;

                        if ($value['id_estado'] == 2) {
                            $varColor = '#FFC72C';
                        }
                        if ($value['id_estado'] == 4) {
                            $varColor = '#00968F';
                        }
                        if ($value['id_estado'] == 5) {
                            $varColor = '#0072CE';
                        }
                        if ($value['id_estado'] == 7) {
                            $varColor = '#6F7271';
                        }                        
                        if ($value['id_estado'] == 9) {
                            $varColor = '#CE0F69';
                        }
                        if ($value['id_estado'] == 8) {
                            $varColor = '#4F758B';
                        }
                ?>
                    {
                        name: "<?php echo $value['nombre'];?>",
                        y: parseFloat("<?php echo $value['Cantidad'];?>"),
                        color: "<?php echo $varColor; ?>",
                        dataLabels: {
                            enabled: false
                        }
                    },
                <?php 
                    }
                ?>
]
    }]
});

    Highcharts.chart('containerB', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: '',
        align: ''
    },
    accessibility: {
        point: {
            valueSuffix: '%'
        }
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: false
            },
            showInLegend: true
        }
    },
    series: [{
        name: 'Data:',
        colorByPoint: true,
        data: [<?php 
                $varvalor = '';          
                    foreach ($varCantidadtranscurre as $key => $value) {
                        $varColor = null;

                        if ($value['num'] == 1) {
                            $varColor = '#002855';
                            $varvalor = 'Cumplió';
                        }
                        if ($value['num'] == 2) {
                            $varColor = '#0072CE';
                            $varvalor = 'Tienes Tiempo para Cumplir';
                        }
                        if ($value['num'] == 3) {
                            $varColor = '#FFC72C';
                            $varvalor = 'Cumplió Fuera de la Fecha';
                        }
                        if ($value['num'] == 4) {
                            $varColor = '#00968F';
                            $varvalor = 'No Haz Respondido';
                        }
                        
                ?>
                    {
                        name: "<?php echo $varvalor;?>",
                        y: parseFloat("<?php echo $value['canti'];?>"),
                        color: "<?php echo $varColor; ?>",
                        dataLabels: {
                            enabled: false
                        }
                    },
                <?php 
                    }
                ?>
]
    }]
});




</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

<script type="text/javascript">

  function download() {
  $(document).find('tfoot').remove();

  var table = document.getElementById('tablaDescarga');

  var wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
  XLSX.write(wb, { bookType: 'xlsx', type: 'base64' });

  var dataUri = 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,' + XLSX.write(wb, { bookType: 'xlsx', type: 'base64' });

  var link = document.createElement("a");
  link.href = dataUri;
  link.download = "Informe_Q&R.xlsx";
  link.target = "_blank";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  }

  var btn = document.getElementById("btn2");
  btn.addEventListener("click", download);





$(document).ready(function() {
  $('#myTable').DataTable(); // Inicializa la tabla DataTable



  $('.resumen-celda').each(function() {
    const $celda = $(this);
    const $resumen = $celda.find('.resumen');
    const $completo = $celda.find('.completo');

    $resumen.on('click', function() {
      if ($completo.hasClass('hidden')) {
        $resumen.addClass('hidden');
        $completo.removeClass('hidden');
      } 
    });

    $completo.on('click', function() {
      if ($resumen.hasClass('hidden')) {
        $completo.addClass('hidden');
        $resumen.removeClass('hidden');
      } 
    });
    
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

  function verificardata(){

        var varidvarCliente = document.getElementById("idvarCliente").value;
        var varidvarSolicitud = document.getElementById("idvarSolicitud").value;
        var varidvarComentarios = document.getElementById("idvarComentarios").value;

        if (varidvarCliente == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un cliente","warning");
            return;
        }
        if (varidvarSolicitud == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un tipo de solicitud","warning");
            return;
        }
        if (varidvarComentarios == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar comentarios","warning");
            return;
        }


        


    }

    document.getElementById('idvarFile').addEventListener('change', function () {
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
   
    <?php  if(base64_decode(Yii::$app->request->get("varAlerta")) === "1"){ ?>       
      swal.fire("Éxito","Datos Guardados Correctamente","success"); 
    <?php } ?> 

</script>