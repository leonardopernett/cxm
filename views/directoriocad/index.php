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

$this->title = 'Directorio CAD';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Directorio CAD';

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
  
  $varSectortwo = (new \yii\db\Query())
                ->select(['id_sectorcad','nombre'])
                ->from(['tbl_sector_cad'])
                ->where(['=','tbl_sector_cad.anulado',0])
                ->all(); 
  
  $listData2 = ArrayHelper::map($varSectortwo, 'id_sectorcad', 'nombre');

  $varProveedorestwo = (new \yii\db\Query())
            ->select(['id_proveedorescad','name'])
            ->from(['tbl_proveedores_cad'])
            ->where(['=','tbl_proveedores_cad.anulado',0])
            ->all(); 

  $listData3 = ArrayHelper::map($varProveedorestwo, 'id_proveedorescad', 'name');


  $varTipotwo = (new \yii\db\Query())
            ->select(['id_tipocad','nombre'])
            ->from(['tbl_tipo_cad'])
            ->where(['=','tbl_tipo_cad.anulado',0])
            ->all(); 

  $listData4 = ArrayHelper::map($varTipotwo, 'id_tipocad', 'nombre');


  $varTipoCanaltwo = (new \yii\db\Query())
            ->select(['id_tipocanalcad','nombre'])
            ->from(['tbl_tipocanal_cad'])
            ->where(['=','tbl_tipocanal_cad.anulado',0])
            ->all(); 

  $listData5 = ArrayHelper::map($varTipoCanaltwo, 'id_tipocanalcad', 'nombre');

  
  $varEtapatwo = (new \yii\db\Query())
            ->select(['id_etapacad','nombre'])
            ->from(['tbl_etapa_cad'])
            ->where(['=','tbl_etapa_cad.anulado',0])
            ->all(); 

  $listData6 = ArrayHelper::map($varEtapatwo, 'id_etapacad', 'nombre');

  $varSociedadtwo = (new \yii\db\Query())
            ->select(['id_sociedadcad','nombre'])
            ->from(['tbl_sociedad_cad'])
            ->where(['=','tbl_sociedad_cad.anulado',0])
            ->all(); 

  $listData9 = ArrayHelper::map($varSociedadtwo, 'id_sociedadcad', 'nombre');

  $varCiudadtwo = (new \yii\db\Query())
            ->select(['id_ciudad_cad','nombre'])
            ->from(['tbl_ciudad_cad'])
            ->where(['=','tbl_ciudad_cad.anulado',0])
            ->all(); 

  $listData7 = ArrayHelper::map($varCiudadtwo, 'id_ciudad_cad', 'nombre');

  $varVicetwo = (new \yii\db\Query())
            ->select(['id_vicepresidentecad','nombre'])
            ->from(['tbl_vicepresidente_cad'])
            ->where(['=','tbl_vicepresidente_cad.anulado',0])
            ->all();

  $listData8 = ArrayHelper::map($varVicetwo, 'id_vicepresidentecad', 'nombre');

  $varCantidades = (new \yii\db\Query())
              ->select(['COUNT(tipo) as cantidad','tipo'])
              ->from(['tbl_directorio_cad'])
              ->where(['=','tbl_directorio_cad.anulado',0])
              ->groupBy(['tipo'])
              ->all(); 

  $canClientes = (new \yii\db\Query())
              ->select(['*'])
              ->from(['tbl_directorio_cad'])
              ->where(['=','tbl_directorio_cad.anulado',0])
              ->count();

  $varTotalCAD = (new \yii\db\Query())
              ->select(['count(distinct(cliente))'])
              ->from(['tbl_directorio_cad'])
              ->where(['=','tbl_directorio_cad.anulado',0])
              ->scalar();
              
  $canRedes  = (new \yii\db\Query())
              ->select(['tipo'])
              ->from(['tbl_directorio_cad'])
              ->where(['=','tipo',1])
              ->andwhere(['=','tbl_directorio_cad.anulado',0])
              ->count();

  $canCanales = (new \yii\db\Query())
              ->select(['tipo'])
              ->from(['tbl_directorio_cad'])
              ->where(['=','tipo',2])
              ->andwhere(['=','tbl_directorio_cad.anulado',0])
              ->count();

  $varCanal = 0;
  $varRedes = 0;

  foreach ($varCantidades as $key => $value) {
    
    if ($value['tipo'] == '1') {
      $varRedes = ($canRedes / $canClientes) * 100;
    }
    if ($value['tipo'] == '2') {
      $varCanal = ($canCanales / $canClientes) * 100;
    }
  }    
  

  $varCantidadDirector = (new \yii\db\Query())
              ->select(['(SELECT DISTINCT(tbl_proceso_cliente_centrocosto.director_programa) FROM tbl_proceso_cliente_centrocosto
                          WHERE 
                          tbl_proceso_cliente_centrocosto.documento_director = tbl_directorio_cad.directorprog
                          ) AS NombreDirector','tbl_directorio_cad.directorprog','COUNT(tbl_directorio_cad.cliente) AS cantidadesD'])
              ->from(['tbl_directorio_cad'])
              ->where(['=','tbl_directorio_cad.anulado',0])
              ->groupBy(['tbl_directorio_cad.directorprog'])
              ->all(); 
              
  $varCantidadProveedores = (new \yii\db\Query())
              ->select(['(SELECT DISTINCT(tbl_proveedores_cad.name) FROM tbl_proveedores_cad
                          WHERE
                          tbl_proveedores_cad.id_proveedorescad = tbl_directorio_cad.proveedores
                          ) AS Proveedores','tbl_directorio_cad.proveedores','COUNT(tbl_directorio_cad.proveedores) AS cantidadesP'])
              ->from(['tbl_directorio_cad'])
              ->where(['=','tbl_directorio_cad.anulado',0])
              ->groupBy(['tbl_directorio_cad.proveedores'])
              ->all();



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
    background-image: url('../../images/directorio_cad.png');
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
<script src="sweetalert2.all.min.js"></script>
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
                  <label style="font-size: 20px;"><em class="fas fa-chart-bar" style="font-size: 22px; color: #827DF9;"></em><strong>  <?= Yii::t('app', 'Resumen General') ?></strong></label>
                </div>
              </a>
              <a href="javascript:void(0)" onclick="openCity(event, 'Usuarios');">
                <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                  <label style="font-size: 20px;"><em class="fas fa-users" style="font-size: 22px; color: #C148D0;"></em><strong>  <?= Yii::t('app', 'Registro') ?></strong></label>
                </div>
              </a>
              <a href="javascript:void(0)" onclick="openCity(event, 'Ver');">
                <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding">
                  <label style="font-size: 20px;"><em class="fas fa-list-alt" style="font-size: 22px; color: #FFC72C;"></em><strong>  <?= Yii::t('app', 'Ver Información') ?></strong></label>
                </div>
              </a>

            </div>

            <!-- Proceso de resumen general -->
            <div id="Resumen" class="w3-container city" style="display:inline;">

                <br>
                <div class="row">
                    <div class="col-md-12">

                        <div class="row">
                            
                            <div class="col-md-12">
                                <div class="card1 mb">

                                  <div class="row">
                                    <div class="col-md-4">
                                      <div class="card1 mb">

                                        <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 22px; color: #827DF9;"></em><?= Yii::t('app', ' Total CAD') ?></label>
                                        <label style="font-size: 30px;" class="text-center"><?= Yii::t('app', $varTotalCAD) ?></label>

                                      </div>
                                    </div>
                                    <div class="col-md-4">
                                      <div class="card1 mb">

                                        <label style="font-size: 15px;"><em class="fas fa-compass" style="font-size: 22px; color: #827DF9;"></em><?= Yii::t('app', ' Canales Digitales') ?></label>
                                        <label style="font-size: 30px;" class="text-center"><?= Yii::t('app', round($varCanal).'%') ?></label>

                                      </div>
                                    </div>
                                    <div class="col-md-4">
                                      <div class="card1 mb">

                                        <label style="font-size: 15px;"><em class="fas fa-globe" style="font-size: 22px; color: #827DF9;"></em><?= Yii::t('app', ' Redes Sociales') ?></label>
                                        <label style="font-size: 30px;" class="text-center"><?= Yii::t('app', round($varRedes).'%') ?></label>

                                      </div>
                                    </div>
                                  </div>
                                  <br>

                                  <div class="row">
                                    <div class="col-md-6">
                                      <div class="card1 mb">
                                        <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Gráfica por Cantidad de Director') ?></label>
                                        <div id="containerdirector" class="highcharts-container" style="height: 300;"></div> 
                                      </div>
                                    </div>
                                   

                                    <div class="col-md-6">
                                      <div class="card1 mb">
                                        <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Gráfica por Cantidad de Proveedor') ?></label>
                                        <div id="containerproveedor" class="highcharts-container" style="height: 300;"></div> 
                                      </div>
                                    </div>
                                  </div>

                                  <br><hr><br>

                                  <div class="row" >
                                    <div class="col-md-6">
                                      <div class="card1 mb">
                                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Modulo Parametrizador') ?></label>
                                          <?= Html::a('Parametrizar',  ['parametrizar'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #4498b2',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Parametrizar']) 
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


             <!-- Proceso de agregar usuarios  -->
            <div id="Usuarios" class="w3-container city" style="display:none;">

                <br>
                <div class="row">
                    <div class="col-md-12">

                        <div class="row">
                            
                            <div class="col-md-12">
                                <div class="card1 mb">

                                    <div class="row">
                                    
                                        <div class="col-md-6">

                                            <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Cliente:') ?></label> 
                                            <?=  $form->field($model, 'cliente', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosClienteCentrocosto::find()->distinct()->select(['id_dp_clientes','CONCAT(cliente," - ",id_dp_clientes) as cliente'])->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                                            [
                                                              'id' => 'idinfocliente',
                                                              'prompt'=>'Seleccionar Servicio...',
                                                              'onchange' => '
                                                                  $.get(
                                                                      "' . Url::toRoute('directoriocad/listardirectores') . '", 
                                                                      {id: $(this).val()}, 
                                                                      function(res){
                                                                          $("#requester2").html(res);
                                                                      }
                                                                  );
                                                               
                                                                  $.get(
                                                                      "' . Url::toRoute('directoriocad/listargerentes') . '", 
                                                                      {id: $(this).val()}, 
                                                                      function(res){
                                                                          $("#requester3").html(res);
                                                                      }
                                                                   );
                                                              ',
                                                            ]
                                              )->label(''); 
                                            ?> 
            
                                            <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Vicepresidente:') ?></label> 
                                            <?= $form->field($model,'vicepresidente',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($listData8,['prompt'=>'Seleccionar...', 'id' => 'vicepresidente'])?>
                                
                                                                          
                                            <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Director del Programa:') ?></label> 
                                            <?= $form->field($model,'directorprog', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                                [],
                                                [
                                                    'prompt' => 'Seleccionar Director...',
                                                    'id' => 'requester2',
                                                    'multiple' => false,
                                                    'onclick' => '
                                                        
                                                        $.get(
                                                            "' . Url::toRoute('directoriocad/listarpcrcindex') . '", 
                                                            {id: $(this).val()}, 
                                                            function(res){
                                                                  $("#requester").html(res);
                                                            }
                                                        );
                                                    ',
                                                ]
                                                  )->label('');
                                              ?> 
                                              
                                              
                                              <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Gerente:') ?></label> 
                                            <?= $form->field($model,'gerente', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                                [],
                                                [
                                                  'prompt' => 'Seleccionar Gerente...',
                                                  'id' => 'requester3',
                                                  'multiple' => false,
                                                  'onclick' => '
                                                      
                                                      $.get(
                                                          "' . Url::toRoute('directoriocad/listarpcrcindex') . '", 
                                                          {id: $(this).val()}, 
                                                          function(res){
                                                                $("#requester").html(res);
                                                          }
                                                      );
                                                  ',   
                                                ]
                                                  )->label('');
                                              ?>

                                            <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Ingrese la Sociedad:') ?></label> 
                                            <?= $form->field($model, 'sociedad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($listData9,['prompt'=>'Seleccionar...', 'id' => 'sociedad'])?>
                                
                                            <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Ciudad:') ?></label> 
                                            <?= $form->field($model,'ciudad',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($listData7,['prompt'=>'Seleccionar...', 'id' => 'ciudad'])?>
                                
                                            <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Sector:') ?></label> 
                                            <?= $form->field($model,'sector',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($listData2,['prompt'=>'Seleccionar...', 'id' => 'sector'])?>
                                
                                                                          
                                        

                                        </div>

                                        <div class="col-md-6">
            
                                            <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Tipo:') ?></label> 
                                            <?= $form->field($model,'tipo',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($listData4,['prompt'=>'Seleccionar...', 'id' => 'tipo'])?>
                                
                                            <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Tipo de Canal:') ?></label> 
                                            <?= $form->field($model,'tipo_canal',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($listData5,['prompt'=>'Seleccionar...',"onchange" => 'varValidaOtros();','id' => 'tipo_canal'])?>
                                
                                            <div id="IdBloque2" style="display:none;">
                                              <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Otro Canal:') ?></label> 
                                              <?= $form->field($model, 'otro_canal', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'otro_canal','placeholder'=>'Otro Canal...'])->label('') ?>
                                            </div>
                                            
                                            <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Proveedores:') ?></label> 
                                            <?= $form->field($model,'proveedores',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($listData3,['prompt'=>'Seleccionar...',"onchange" => 'varValida();', 'id' => 'proveedores'])?>
                                
                                            <div id="IdBloque" style="display:none;">
                                              <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Nombre de la Plataforma:') ?></label> 
                                              <?= $form->field($model, 'nom_plataforma', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'nom_plataforma', 'placeholder'=>'Nombre de la Plataforma...'])->label('') ?>
                                            </div>

                                            <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Etapa:') ?></label> 
                                            <?= $form->field($model,'etapa',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($listData6,['prompt'=>'Seleccionar...', 'id' => 'etapa','multiple' => true])?>

                                            <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Guardar y Enviar') ?></label><br>
                                            <?= Html::submitButton(Yii::t('app', 'Guardar y Enviar'),
                                                          ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                                              'data-toggle' => 'tooltip',
                                                              'onclick' => 'varVerificar();',
                                                              'title' => 'Registro General']) 
                                            ?>
                                           
                                
                                        </div>  

                                    </div>
                                    
                                </div>
                                <br>

                                <div class="card1 mb">

                                    <label style="font-size: 15px;"><em class="fas fa-paperclip" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Subir Carga Masiva de Cuentas') ?></label> 
                                    <br>
                                    <?= Html::button('Aceptar', ['value' => url::to(['subircarga']), 'class' => 'btn btn-success', 'id'=>'modalButton',
                                  'data-toggle' => 'tooltip',
                                                        'title' => 'Crear Procesos Parametrizador']) 
                                    ?> 

                                    <?php
                                    Modal::begin([
                                        'header' => '<h4>Acciones</h4>',
                                        'id' => 'modal',
                                        'size' => 'modal-lg',
                                    ]);

                                    echo "<div id='modalContent'></div>";
                                                                                                                            
                                    Modal::end(); 
                                    ?>
                                    <br>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <!-- Proceso ver y editar usuarios  -->
            <div id="Ver" class="w3-container city" style="display:none;">

                <br>
                <div class="row">
                    <div class="col-md-12">

                        <div class="row">
                            
                            <div class="col-md-12">
                                <div class="card1 mb">
                                <table id="tblDataInteracciones" class="table table-striped table-bordered tblResDetFreed">
                                  <caption><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', '  Resultados') ?></caption>
                                  <thead>
                                    <tr>
                                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Vicepresidente') ?></label></th>
                                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Director Programa') ?></label></th>
                                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Gerente') ?></label></th>
                                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Sociedad') ?></label></th>
                                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Ciudad') ?></label></th>
                                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Sector') ?></label></th>
                                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cliente') ?></label></th>
                                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo') ?></label></th>
                                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo de Canal') ?></label></th>
                                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Otro Canal') ?></label></th>
                                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Proveedores') ?></label></th>
                                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nombre Plataforma') ?></label></th>
                                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Etapa') ?></label></th>
                                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Editar') ?></label></th>
                                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Eliminar') ?></label></th>

                                    </tr>
                                  </thead>
                                  <tbody>
                                   
                                  <?php
                                    foreach ($varListaGeneral as $key => $value) {
                                    

                                    $varEtapas = (new \yii\db\Query())
                                                  ->select(['tbl_etapa_cad.nombre'])
                                                  ->from(['tbl_etapamultiple_cad'])
                                                  ->join('INNER JOIN','tbl_etapa_cad',
                                                    'tbl_etapa_cad.id_etapacad = tbl_etapamultiple_cad.id_etapacad')
                                                  ->where(['=','tbl_etapamultiple_cad.anulado',0])
                                                  ->andwhere(['=','tbl_etapamultiple_cad.id_directorcad',$value['id_directorcad']])
                                                  ->limit(1)
                                                  ->scalar();
                                                  
                                  $varCliente  = (new \yii\db\Query()) 
                                                  ->select([
                                                  'pc.cliente'
                                                  ])
                                                  ->from(['tbl_directorio_cad'])  
                                                  ->join('INNER JOIN','tbl_proceso_cliente_centrocosto pc',
                                                  'pc.id_dp_clientes = tbl_directorio_cad.cliente ') 
                                                  ->where(['=','tbl_directorio_cad.anulado',0])
                                                  ->andwhere(['=','tbl_directorio_cad.id_directorcad',$value['id_directorcad']])
                                                  ->groupBy(['tbl_directorio_cad.id_directorcad'])
                                                  ->scalar();
                                    
                                  ?>
                                  
                                  <tr>
                                      <td><label style="font-size: 12px;"><?php echo  $value['vicepresidente']; ?></label></td>
                                      <td><label style="font-size: 12px;"><?php echo  $value['director_programa']; ?></label></td>
                                      <td><label style="font-size: 12px;"><?php echo  $value['gerente_cuenta']; ?></label></td>
                                      <td><label style="font-size: 12px;"><?php echo  $value['sociedad']; ?></label></td>
                                      <td><label style="font-size: 12px;"><?php echo  $value['ciudad']; ?></label></td>
                                      <td><label style="font-size: 12px;"><?php echo  $value['sector']; ?></label></td>
                                      
                                      <td><label style="font-size: 12px;"><?= Yii::t('app', $varCliente) ?></label></td> 
                                      <td><label style="font-size: 12px;"><?php echo  $value['tipo']; ?></label></td>
                                      <td><label style="font-size: 12px;"><?php echo  $value['tipo_canal']; ?></label></td>
                                      <?php if (isset($value['otro_canal']) && $value['otro_canal'] != "") {?>
                                        <td><label style="font-size: 12px;"><?php echo  $value['otro_canal']; ?></label></td>
                                      <?php  } else { ?>
                                        <td><label style="font-size: 12px;">N/A</label></td>
                                      <?php  } ?>
                                      
                                      <td><label style="font-size: 12px;"><?php echo  $value['proveedores']; ?></label></td>
                                      <?php if (isset($value['nom_plataforma']) && $value['nom_plataforma'] != "") {?>
                                        <td><label style="font-size: 12px;"><?php echo  $value['nom_plataforma']; ?></label></td>
                                      <?php  } else { ?>
                                        <td><label style="font-size: 12px;">N/A</label></td>
                                      <?php  } ?>
                                      
                                      <td><label style="font-size: 12px;"><?= Yii::t('app', $varEtapas) ?></label></td>
                                      <td class="text-center">

                                        <?= 
                                          Html::a('<em class="fas fa-edit" style="font-size: 15px; color: #FFC72C;"></em>',  ['editarusu','id_directorcad'=> $value['id_directorcad']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Editar Usuario']) 
                                        ?>

                                      </td>
                                      <td class="text-center">

                                        <?= 
                                          Html::a('<em class="fas fa-times" style="font-size: 15px; color: red;"></em>', ['eliminarcuenta','id_directorcad'=> $value['id_directorcad']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar Cuenta'])  ?>
                                      </td>
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
            <?php ActiveForm::end(); ?>

            <br>
</div>
           
<br><hr><br>

<script>

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
        $('#tblDataInteracciones').DataTable({
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


    function varValida(){
    var varidSeleccionar = document.getElementById("proveedores").value;
    var varBloque =  document.getElementById("IdBloque");

    if (varidSeleccionar == "2") {
      varBloque.style.display='inline';
    }else{
      varBloque.style.display='none';
    }
  }

  

    function varValidaOtros(){
    var varidSeleccionarOtro = document.getElementById("tipo_canal").value;
    var varBloque2 =  document.getElementById("IdBloque2");

    if (varidSeleccionarOtro == "29") {
      varBloque2.style.display='inline';
    }else{
      varBloque2.style.display='none';
    }
  }

  $('#containerdirector').highcharts({
        chart: {                
            type: 'column'
        },

        yAxis: {
            title: {
                text: 'Cantidad por Director'
            }
        }, 

        title: {
            text: '',
            style: {
                color: '#3C74AA'
            }
        },

        xAxis: {
            categories: " ",
            title: {
                text: null,
            }
        },

        series:  [              
            <?php   foreach ($varCantidadDirector as $key => $value) {?>
                {
                    name: "<?php echo $value['NombreDirector'];?>",
                    data: [<?php echo $value['cantidadesD'];?> ]                         
                },
            <?php   }   ?> 
        ],              
  });

  $('#containerproveedor').highcharts({
        chart: {                
            type: 'column'
        },

        yAxis: {
            title: {
                text: 'Canales por Proveedor'
            }
        }, 

        title: {
            text: '',
            style: {
                color: '#3C74AA'
            }
        },

        xAxis: {
            categories: " ",
            title: {
                text: null,
            }
        },

        series: [              
              <?php   foreach ($varCantidadProveedores as $key => $value) {?>
                    {
                        name: "<?php echo $value['Proveedores'];?>",
                        data: [<?php echo $value['cantidadesP'];?> ]                         
                    },
                <?php   }   ?> 
        ],                
  });

    <?php  if(base64_decode(Yii::$app->request->get("varAlerta")) === "1"){ ?>       
      swal.fire("Información","Accion ejecutada Correctamente","success"); 
    <?php }
    
    if(base64_decode(Yii::$app->request->get("varAlerta")) === "2"){?>
      swal.fire("Aviso","No cumple con los criterios establecidos","warning");
    <?php }   ?> 

    function varVerificar() {

    var cliente = document.getElementById("idinfocliente").value;  
    var vicepresidente = document.getElementById("vicepresidente").value;
    var sociedad = document.getElementById("sociedad").value;
    var ciudad = document.getElementById("ciudad").value;  
    var sector = document.getElementById("sector").value;  
    var tipo = document.getElementById("tipo").value;
    var tipo_canal = document.getElementById("tipo_canal").value;  
    var proveedores = document.getElementById("proveedores").value;
    var etapa = document.getElementById("etapa").value;  
   

    if (cliente == "") {
      event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe seleccionar cliente","warning");
            return;
    }
    if (vicepresidente == "") {
      event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe seleccionar vicepresidente","warning");
            return;
    }
    if (sociedad == "") {
      event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe ingresar la sociedad","warning");
            return;
    }
    if (ciudad == "") {
      event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe seleccionar la ciudad","warning");
            return;
    }
    if (sector == "") {
      event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe seleccionar el sector","warning");
            return;
    }
    if (tipo == "") {
      event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe seleccionar el tipo","warning");
            return;
    }
    if (tipo_canal == "") {
      event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe seleccionar el tipo de canal","warning");
            return;
    }
    if (proveedores == "") {
      event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe seleccionar el proveedor","warning");
            return;
    }
    if (etapa == "") {
      event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe seleccionar la etapa","warning");
            return;
    }


    
    
  }

  
  
</script>
            