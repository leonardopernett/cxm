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
use miloschuman\highcharts\Highcharts;

$this->title = 'Dashboard Ejecutivo';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Escuchar + (Programa VOC - Konecta)';


    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
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

    $varMes = date("n");
            $txtMes = null;
            switch ($varMes) {
                case '1':
                    $txtMes = "Enero";
                    break;
                case '2':
                    $txtMes = "Febrero";
                    break;
                case '3':
                    $txtMes = "Marzo";
                    break;
                case '4':
                    $txtMes = "Abril";
                    break;
                case '5':
                    $txtMes = "Mayo";
                    break;
                case '6':
                    $txtMes = "Junio";
                    break;
                case '7':
                    $txtMes = "Julio";
                    break;
                case '8':
                    $txtMes = "Agosto";
                    break;
                case '9':
                    $txtMes = "Septiembre";
                    break;
                case '10':
                    $txtMes = "Octubre";
                    break;
                case '11':
                    $txtMes = "Noviembre";
                    break;
                case '12':
                    $txtMes = "Diciembre";
                    break;
                default:
                    # code...
                    break;
            } 


    $varBeginYear = '2019-01-01';
    $varLastYear = '2050-12-31';

    $varMonthYear = Yii::$app->db->createCommand("select concat_ws('- ', CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1)) as CorteTipo from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a   order by a.mesyear asc")->queryAll();

    $varListCorte = array();
    foreach ($varMonthYear as $key => $value) {
        $varListCort = $value['CorteTipo'];
                                                                            
        $varLTMes = Yii::$app->db->createCommand("select vozfecha from tbl_voz_fecha where anulado = 0 and cortefecha in ('$varListCort')")->queryScalar();

        array_push($varListCorte, $varLTMes);
    }

    $txtCiudades = Yii::$app->db->createCommand(" select id, name, arbol_id from tbl_arbols where id in (98, 2, 1)")->queryAll(); 
    $varListMonth2 = Yii::$app->db->createCommand("select distinct mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 6")->queryAll();                    

    $varFisrtDate = date($varListMonth2[0]["mesyear"]);
    $varLastDate = date($varListMonth2[2]["mesyear"]);

    $varListMonth = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

    $varListMonthMed = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

    $varListMonthBog = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

    $txtTotalKonecta = null;   
    $varListKonecta = array(); 
    $varListKonectaManual = array();
    $varListKonectaSpeech = array();   
    foreach ($varListMonth as $key => $value) {
        $varListYear = $value['mesyear']; 
            
        $txtQuery =  new Query;
        $txtQuery   ->select(['sum(tbl_control_volumenxclientedq.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxclientedq')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                'tbl_control_volumenxclientedq.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id in (2, 98)')
                    ->andwhere(['between','tbl_control_volumenxclientedq.mesyear', $varListYear, $varListYear]);
        $command = $txtQuery->createCommand();
        $txtTotalMonth1 = $command->queryScalar();



        $txtQuery1 =  new Query;
        $txtQuery1  ->select(['sum(tbl_control_volumenxclienteds.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxclienteds')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                'tbl_control_volumenxclienteds.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id in (2, 98)')
                    ->andwhere(['between','tbl_control_volumenxclienteds.mesyear', $varListYear, $varListYear]);
        $command1 = $txtQuery1->createCommand();
        $txtTotalMonth2 = $command1->queryScalar(); 

        $txtTotalKonecta = $txtTotalMonth1 + $txtTotalMonth2;
        array_push($varListKonecta, $txtTotalKonecta);
        array_push($varListKonectaManual, $txtTotalMonth1);
        array_push($varListKonectaSpeech, $txtTotalMonth2);
    }

    $txtTotalMed = null;
    $varListMed = array();
    $varListMedManual = array();
    $varListMedSpeech = array();
    foreach ($varListMonthMed as $key => $value) {
        $varListYear = $value['mesyear']; 

        $txtQuery1 =  new Query;
        $txtQuery1   ->select(['sum(tbl_control_volumenxclientedq.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxclientedq')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                'tbl_control_volumenxclientedq.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id = 2')
                    ->andwhere(['between','tbl_control_volumenxclientedq.mesyear', $varListYear, $varListYear]);
        $command1 = $txtQuery1->createCommand();
        $txtTotalMonth11 = $command1->queryScalar();                       

        $txtQuery21 =  new Query;
        $txtQuery21  ->select(['sum(tbl_control_volumenxclienteds.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxclienteds')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                'tbl_control_volumenxclienteds.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id = 2')
                    ->andwhere(['between','tbl_control_volumenxclienteds.mesyear', $varListYear, $varListYear]); 
        $command21 = $txtQuery21->createCommand();
        $txtTotalMonth21 = $command21->queryScalar(); 

        $txtTotalMed = $txtTotalMonth11 + $txtTotalMonth21;
        array_push($varListMed, $txtTotalMed);
        array_push($varListMedManual, $txtTotalMonth11);
        array_push($varListMedSpeech, $txtTotalMonth21);
    }

    $txtTotalBog = null;
    $varListBog = array();
    $varListBogManual = array();
    $varListBogSpeech = array();
    foreach ($varListMonthBog as $key => $value) {
        $varListYear = $value['mesyear']; 

        $txtQuery2 =  new Query;
        $txtQuery2   ->select(['sum(tbl_control_volumenxclientedq.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxclientedq')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                'tbl_control_volumenxclientedq.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id = 98')
                    ->andwhere(['between','tbl_control_volumenxclientedq.mesyear', $varListYear, $varListYear]);
        $command2 = $txtQuery2->createCommand();
        $txtTotalMonth12 = $command2->queryScalar();                       

        $txtQuery22 =  new Query;
        $txtQuery22  ->select(['sum(tbl_control_volumenxclienteds.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxclienteds')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                'tbl_control_volumenxclienteds.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id = 98')
                    ->andwhere(['between','tbl_control_volumenxclienteds.mesyear', $varListYear, $varListYear]); 
        $command22 = $txtQuery22->createCommand();
        $txtTotalMonth22 = $command22->queryScalar(); 

        $txtTotalBog = $txtTotalMonth12 + $txtTotalMonth22;
        array_push($varListBog, $txtTotalBog);
        array_push($varListBogManual, $txtTotalMonth12);
        array_push($varListBogSpeech, $txtTotalMonth22);
    }

    $txtCiudadese = Yii::$app->db->createCommand(" select id, name, arbol_id from tbl_arbols where id in (98, 2, 1)")->queryAll(); 
    $varListMonth2e = Yii::$app->db->createCommand("select distinct mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear'  group by mesyear order by mesyear desc limit 6")->queryAll();                    

    $varFisrtDatee = date($varListMonth2e[0]["mesyear"]);
    $varLastDatee = date($varListMonth2e[2]["mesyear"]);

    $varListMonthe = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a    order by a.mesyear asc")->queryAll();

    $varListMonthMede = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a   order by a.mesyear asc")->queryAll();

    $varListMonthBoge = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a   order by a.mesyear asc")->queryAll();

    $txtTotalKonectae = null;   
    $varListKonectae = array();    
    foreach ($varListMonthe as $key => $value) {
        $varListYeare = $value['mesyear']; 
            
        $txtQuerye =  new Query;
        $txtQuerye   ->select(['sum(tbl_control_volumenxencuestasdq.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxencuestasdq')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                'tbl_control_volumenxencuestasdq.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id in (2, 98)')
                    ->andwhere(['between','tbl_control_volumenxencuestasdq.mesyear', $varListYeare, $varListYeare]);
        $commande = $txtQuerye->createCommand();
        $txtTotalMonth1e = $commande->queryScalar();

        $txtTotalKonectae = $txtTotalMonth1e;
        array_push($varListKonectae, $txtTotalKonectae);
    }

    $txtTotalMede = null;
    $varListMede = array();
    foreach ($varListMonthMede as $key => $value) {
        $varListYeare = $value['mesyear']; 

        $txtQuery1e =  new Query;
        $txtQuery1e   ->select(['sum(tbl_control_volumenxencuestasdq.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxencuestasdq')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                'tbl_control_volumenxencuestasdq.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id = 2')
                    ->andwhere(['between','tbl_control_volumenxencuestasdq.mesyear', $varListYeare, $varListYeare]);
        $command1e = $txtQuery1e->createCommand();
        $txtTotalMonth11e = $command1e->queryScalar(); 

        $txtTotalMede = $txtTotalMonth11e;
        array_push($varListMede, $txtTotalMede);
    }

    $txtTotalBoge = null;
    $varListBoge = array();
    foreach ($varListMonthBoge as $key => $value) {
        $varListYeare = $value['mesyear']; 

        $txtQuery12e =  new Query;
        $txtQuery12e   ->select(['sum(tbl_control_volumenxencuestasdq.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxencuestasdq')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                'tbl_control_volumenxencuestasdq.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id = 98')
                    ->andwhere(['between','tbl_control_volumenxencuestasdq.mesyear', $varListYeare, $varListYeare]);
        $command12e = $txtQuery12e->createCommand();
        $txtTotalMonth12e = $command12e->queryScalar();  

        $txtTotalBoge = $txtTotalMonth12e;
        array_push($varListBoge, $txtTotalBoge);
    }

?>
<link rel="stylesheet" href="https://qa.grupokonecta.local/qa_managementv2/web/css/font-awesome/css/font-awesome.css"  >
<style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Nunito');
    .datakonecta {
        font-family: "Nunito";
        font-size: 100%;    
        text-align: left;
    }

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
            font-family: "Nunito";
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
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }

    .card2 {
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
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }

    .card:hover .card1:hover {
        top: -15%;
    }

    .datacontainer {
        margin-right: 1px;
        margin-left: 1px;
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
    @media (min-width:601px){.w3-col.m1{width:8.33333%}.w3-col.m2{width:16.66666%}.w3-col.m3,.w3-quarter{width:24.99999%}.w3-col.m4,.w3-third{width:25%}
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
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br>
<!-- <div class="page-header">
    <p style="font-family: 'Nunito';"><h3><center><?= Html::encode($this->title) ?></center></h3></p>
</div>  -->
<div class="Principal">
    <div id="capaUno" style="display: inline">
        <div id="dtbloque0" class="form-group col-sm-12" style="display: inline;">
            <div class="primeralinea">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card1 mb">
                            <div class="datakonecta">
                                <i class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></i><strong> Métricas de valoración (Automáticas y Manuales):</strong>
                            </div>

                            <div class="w3-container">
                              <div class="w3-row">
                                <a href="javascript:void(0)" onclick="openCity(event, 'General');">
                                  <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding"><i class="fas fa-chart-bar" style="font-size: 25px; color: #559FFF;"></i><strong> Métrica General</strong></div>
                                </a>
                                <a href="javascript:void(0)" onclick="openCity(event, 'Konecta');">
                                  <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding"><i class="fas fa-chart-bar" style="font-size: 25px; color: #827DF9;"></i><strong> Métrica Konecta</strong></div>
                                </a>
                                <a href="javascript:void(0)" onclick="openCity(event, 'Bogota');">
                                  <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding"><i class="fas fa-chart-bar" style="font-size: 25px; color: #C148D0;"></i><strong> Métrica Bogotá</strong></div>
                                </a>
                                <a href="javascript:void(0)" onclick="openCity(event, 'Medellin');">
                                  <div class="w3-third tablink w3-bottombar w3-hover-light-grey w3-padding"><i class="fas fa-chart-bar" style="font-size: 25px; color: #FFC72C;"></i><strong> Métrica Medellín</strong></div>
                                </a>
                              </div>

                              <div id="General" class="w3-container city" style="display:inline">
                                <div class="row">
                                    <br>
                                    <div class="col-md-6">
                                        <div class="card2 mb">
                                                    <table class="table table-striped table-bordered detail-view formDinamico" border="0">
                                                                <thead>
                                                                    <th class="text-center" style="background-color: #EEEEEE"><?= Yii::t('app', '') ?></th>
                                                                    <?php

                                                                        $varMonthYear = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a   order by a.mesyear asc")->queryAll();

                                                                        foreach ($varMonthYear as $key => $value) {
                                                                            $varMonth = $value['CorteMes'];
                                                                            $varYear = $value['CorteYear'];
                                                                            $txtTMes = $varMonth.'- '.$varYear;
                                                                            $varTMes = Yii::$app->db->createCommand("select vozfecha from tbl_voz_fecha where anulado = 0 and cortefecha in ('$txtTMes')")->queryScalar();
                                                                    ?>
                                                                        <th class="text-center" style="font-size: 15px; background-color: #EEEEEE"><?php echo $varTMes; ?></th>
                                                                    <?php
                                                                        }
                                                                    ?>           
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                        $txtCiudades = Yii::$app->db->createCommand(" select id, name, arbol_id from tbl_arbols where id in (98, 2, 1)")->queryAll(); 

                                                                        $varListMonth2 = Yii::$app->db->createCommand("select distinct mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 6")->queryAll();                   

                                                                        $varFisrtDate = date($varListMonth2[0]["mesyear"]);
                                                                        $varLastDate = date($varListMonth2[2]["mesyear"]);
                                                                        $txtTotalAVG = null;

                                                                        $txtTotalAVG = null;                    

                                                                        foreach ($txtCiudades as $key => $value) {
                                                                            $txtNameCity = $value['name'];
                                                                            $varIdPcrc = $value['id'];

                                                                            $varListMonth = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a      order by a.mesyear asc")->queryAll();
                                                                    ?>
                                                                    <tr>
                                                                        <td class="text-center"><?php echo $txtNameCity; ?></td>

                                                                        <?php
                                                                            $txtTotalMonth = null;
                                                                            foreach ($varListMonth as $key => $value) {
                                                                                $varListYear = $value['mesyear'];                     

                                                                                if ($varIdPcrc == 1){
                                                                                    $txtQuery =  new Query;
                                                                                    $txtQuery   ->select(['sum(tbl_control_volumenxclientedq.cantidadvalor)'])->distinct()
                                                                                                ->from('tbl_control_volumenxclientedq')
                                                                                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                            'tbl_control_volumenxclientedq.idservicio = tbl_arbols.id')
                                                                                                ->where('tbl_arbols.arbol_id in (2, 98)')
                                                                                                ->andwhere(['between','tbl_control_volumenxclientedq.mesyear', $varListYear, $varListYear]);                    
                                                                                    $command = $txtQuery->createCommand();
                                                                                    $txtTotalMonth1 = $command->queryScalar();
                                                                                    //var_dump($txtTotalMonth1);

                                                                                    $txtQuery1 =  new Query;
                                                                                    $txtQuery1   ->select(['sum(tbl_control_volumenxclienteds.cantidadvalor)'])->distinct()
                                                                                                ->from('tbl_control_volumenxclienteds')
                                                                                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                            'tbl_control_volumenxclienteds.idservicio = tbl_arbols.id')
                                                                                                ->where('tbl_arbols.arbol_id in (2, 98)')
                                                                                                ->andwhere(['between','tbl_control_volumenxclienteds.mesyear', $varListYear, $varListYear]);                    
                                                                                    $command1 = $txtQuery1->createCommand();
                                                                                    $txtTotalMonth2 = $command1->queryScalar(); 

                                                                                    $txtTotalMonth = $txtTotalMonth1 + $txtTotalMonth2;
                                                                                }else{

                                                                                    $txtQuery =  new Query;
                                                                                    $txtQuery   ->select(['sum(tbl_control_volumenxclientedq.cantidadvalor)'])->distinct()
                                                                                                ->from('tbl_control_volumenxclientedq')
                                                                                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                            'tbl_control_volumenxclientedq.idservicio = tbl_arbols.id')
                                                                                                ->where('tbl_arbols.arbol_id = '.$varIdPcrc.'')
                                                                                                ->andwhere(['between','tbl_control_volumenxclientedq.mesyear', $varListYear, $varListYear]);                    
                                                                                    $command = $txtQuery->createCommand();
                                                                                    $txtTotalMonth1 = $command->queryScalar();                       

                                                                                    $txtQuery2 =  new Query;
                                                                                    $txtQuery2   ->select(['sum(tbl_control_volumenxclienteds.cantidadvalor)'])->distinct()
                                                                                                ->from('tbl_control_volumenxclienteds')
                                                                                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                            'tbl_control_volumenxclienteds.idservicio = tbl_arbols.id')
                                                                                                ->where('tbl_arbols.arbol_id = '.$varIdPcrc.'')
                                                                                                ->andwhere(['between','tbl_control_volumenxclienteds.mesyear', $varListYear, $varListYear]);                    
                                                                                    $command2 = $txtQuery2->createCommand();
                                                                                    $txtTotalMonth2 = $command2->queryScalar(); 

                                                                                    $txtTotalMonth = $txtTotalMonth1 + $txtTotalMonth2;
                                                                                }

                                                                        ?>

                                                                            <td class="text-center"><?php echo round($txtTotalMonth); ?></td>

                                                                        <?php
                                                                            }
                                                                        ?>
                                                                    </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                            <br>
                                                    <?= Html::a('Ver Detalle',  ['graficamanuales'], ['class' => 'btn btn-success', 'style' => 'background-color: #337ab7', 'data-toggle' => 'tooltip', 'title' => 'Ver Detalle']) 
                                                    ?> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card2 mb">
                                                    <div id="conatinervaloraciones" class="highcharts-container" style="height: 300px;"></div>
                                        </div>
                                    </div>
                                </div>
                              </div>

                              <div id="Konecta" class="w3-container city" style="display:none">
                                <div class="row">
                                    <br>
                                    <div class="col-md-6">
                                                <div class="card2 mb">
                                                    <table class="table table-striped table-bordered detail-view formDinamico" border="0">
                                                            <thead>
                                                                <th class="text-center" style="background-color: #EEEEEE"><?= Yii::t('app', '') ?></th>
                                                                    <?php

                                                                        $varMonthYear = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a   order by a.mesyear asc")->queryAll();

                                                                        foreach ($varMonthYear as $key => $value) {
                                                                            $varMonth = $value['CorteMes'];
                                                                            $varYear = $value['CorteYear'];
                                                                            $txtTMes = $varMonth.'- '.$varYear;
                                                                            $varTMes = Yii::$app->db->createCommand("select vozfecha from tbl_voz_fecha where anulado = 0 and cortefecha in ('$txtTMes')")->queryScalar();
                                                                    ?>
                                                                        <th class="text-center" style="font-size: 15px; background-color: #EEEEEE"><?php echo $varTMes; ?></th>
                                                                    <?php
                                                                        }
                                                                    ?> 
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-center"><?= Yii::t('app', 'Automatica y Manual') ?></td>
                                                                    <?php
                                                                        $varListMonth = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

                                                                        foreach ($varListMonth as $key => $value) {                        
                                                                            $varListYear = $value['mesyear']; 
                                                                    
                                                                            $txtQuery =  new Query;
                                                                            $txtQuery   ->select(['sum(tbl_control_volumenxclientedq.cantidadvalor)'])->distinct()
                                                                                        ->from('tbl_control_volumenxclientedq')
                                                                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                    'tbl_control_volumenxclientedq.idservicio = tbl_arbols.id')
                                                                                        ->where('tbl_arbols.arbol_id in (2, 98)')
                                                                                        ->andwhere(['between','tbl_control_volumenxclientedq.mesyear', $varListYear, $varListYear]);
                                                                            $command = $txtQuery->createCommand();
                                                                            $txtTotalMonth1 = $command->queryScalar();



                                                                            $txtQuery1 =  new Query;
                                                                            $txtQuery1  ->select(['sum(tbl_control_volumenxclienteds.cantidadvalor)'])->distinct()
                                                                                        ->from('tbl_control_volumenxclienteds')
                                                                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                    'tbl_control_volumenxclienteds.idservicio = tbl_arbols.id')
                                                                                        ->where('tbl_arbols.arbol_id in (2, 98)')
                                                                                        ->andwhere(['between','tbl_control_volumenxclienteds.mesyear', $varListYear, $varListYear]);
                                                                            $command1 = $txtQuery1->createCommand();
                                                                            $txtTotalMonth2 = $command1->queryScalar(); 

                                                                            $txtTotalKonecta = $txtTotalMonth1 + $txtTotalMonth2;
                                                                    ?>
                                                                        <td class="text-center"><?php echo $txtTotalKonecta; ?></td>
                                                                    <?php
                                                                        }
                                                                    ?>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-center"><?= Yii::t('app', 'Automatica') ?></td>
                                                                    <?php
                                                                        $varListMonth = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

                                                                        foreach ($varListMonth as $key => $value) {
                                                                            $varListYear = $value['mesyear']; 
                                                                            
                                                                            $txtQuery1 =  new Query;
                                                                            $txtQuery1  ->select(['sum(tbl_control_volumenxclienteds.cantidadvalor)'])->distinct()
                                                                                        ->from('tbl_control_volumenxclienteds')
                                                                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                    'tbl_control_volumenxclienteds.idservicio = tbl_arbols.id')
                                                                                        ->where('tbl_arbols.arbol_id in (2, 98)')
                                                                                        ->andwhere(['between','tbl_control_volumenxclienteds.mesyear', $varListYear, $varListYear]);
                                                                            $command1 = $txtQuery1->createCommand();
                                                                            $txtTotalMonth2 = $command1->queryScalar();
                                                                    ?>
                                                                        <td class="text-center"><?php echo $txtTotalMonth2; ?></td>
                                                                    <?php
                                                                        }
                                                                    ?>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-center"><?= Yii::t('app', 'Manual') ?></td>
                                                                    <?php
                                                                        $varListMonth = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

                                                                        foreach ($varListMonth as $key => $value) {
                                                                            $varListYear = $value['mesyear']; 

                                                                            $txtQuery =  new Query;
                                                                            $txtQuery   ->select(['sum(tbl_control_volumenxclientedq.cantidadvalor)'])->distinct()
                                                                                        ->from('tbl_control_volumenxclientedq')
                                                                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                    'tbl_control_volumenxclientedq.idservicio = tbl_arbols.id')
                                                                                        ->where('tbl_arbols.arbol_id in (2, 98)')
                                                                                        ->andwhere(['between','tbl_control_volumenxclientedq.mesyear', $varListYear, $varListYear]);
                                                                            $command = $txtQuery->createCommand();
                                                                            $txtTotalMonth1 = $command->queryScalar();
                                                                    ?>
                                                                        <td class="text-center"><?php echo $txtTotalMonth1; ?></td>
                                                                    <?php
                                                                        }
                                                                    ?>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <br>
                                                    <?= Html::a('Ver Detalle',  ['graficamanuales'], ['class' => 'btn btn-success', 'style' => 'background-color: #337ab7', 'data-toggle' => 'tooltip', 'title' => 'Ver Detalle']) 
                                                    ?> 
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card2 mb">
                                                    <div id="conatinerkonecta" class="highcharts-container" style="height: 300px;"></div>
                                                </div>
                                            </div>
                                </div>
                              </div>

                              <div id="Bogota" class="w3-container city" style="display:none">
                                <div class="row">
                                    <br>
                                    <div class="col-md-6">
                                                <div class="card2 mb">
                                                    <table class="table table-striped table-bordered detail-view formDinamico" border="0">
                                                            <thead>
                                                                <th class="text-center" style="background-color: #EEEEEE"><?= Yii::t('app', '') ?></th>
                                                                    <?php

                                                                        $varMonthYear = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a   order by a.mesyear asc")->queryAll();

                                                                        foreach ($varMonthYear as $key => $value) {
                                                                            $varMonth = $value['CorteMes'];
                                                                            $varYear = $value['CorteYear'];
                                                                            $txtTMes = $varMonth.'- '.$varYear;
                                                                            $varTMes = Yii::$app->db->createCommand("select vozfecha from tbl_voz_fecha where anulado = 0 and cortefecha in ('$txtTMes')")->queryScalar();
                                                                    ?>
                                                                        <th class="text-center" style="font-size: 15px; background-color: #EEEEEE"><?php echo $varTMes; ?></th>
                                                                    <?php
                                                                        }
                                                                    ?> 
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-center"><?= Yii::t('app', 'Automatica y Manual') ?></td>
                                                                    <?php
                                                                        $varListMonthBog = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

                                                                        foreach ($varListMonthBog as $key => $value) {                        
                                                                            $varListYearB = $value['mesyear']; 
                                                                    
                                                                            $txtQuery =  new Query;
                                                                            $txtQuery   ->select(['sum(tbl_control_volumenxclientedq.cantidadvalor)'])->distinct()
                                                                                        ->from('tbl_control_volumenxclientedq')
                                                                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                    'tbl_control_volumenxclientedq.idservicio = tbl_arbols.id')
                                                                                        ->where('tbl_arbols.arbol_id in (98)')
                                                                                        ->andwhere(['between','tbl_control_volumenxclientedq.mesyear', $varListYearB, $varListYearB]);
                                                                            $command = $txtQuery->createCommand();
                                                                            $txtTotalMonth1B = $command->queryScalar();



                                                                            $txtQuery1 =  new Query;
                                                                            $txtQuery1  ->select(['sum(tbl_control_volumenxclienteds.cantidadvalor)'])->distinct()
                                                                                        ->from('tbl_control_volumenxclienteds')
                                                                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                    'tbl_control_volumenxclienteds.idservicio = tbl_arbols.id')
                                                                                        ->where('tbl_arbols.arbol_id in (98)')
                                                                                        ->andwhere(['between','tbl_control_volumenxclienteds.mesyear', $varListYearB, $varListYearB]);
                                                                            $command1 = $txtQuery1->createCommand();
                                                                            $txtTotalMonth2B = $command1->queryScalar(); 

                                                                            $txtTotalKonectaB = $txtTotalMonth1B + $txtTotalMonth2B;
                                                                    ?>
                                                                        <td class="text-center"><?php echo $txtTotalKonectaB; ?></td>
                                                                    <?php
                                                                        }
                                                                    ?>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-center"><?= Yii::t('app', 'Automatica') ?></td>
                                                                    <?php
                                                                        $varListMonthBog = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

                                                                        foreach ($varListMonthBog as $key => $value) {
                                                                            $varListYearB = $value['mesyear']; 
                                                                            
                                                                            $txtQuery1 =  new Query;
                                                                            $txtQuery1  ->select(['sum(tbl_control_volumenxclienteds.cantidadvalor)'])->distinct()
                                                                                        ->from('tbl_control_volumenxclienteds')
                                                                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                    'tbl_control_volumenxclienteds.idservicio = tbl_arbols.id')
                                                                                        ->where('tbl_arbols.arbol_id in (98)')
                                                                                        ->andwhere(['between','tbl_control_volumenxclienteds.mesyear', $varListYearB, $varListYearB]);
                                                                            $command1 = $txtQuery1->createCommand();
                                                                            $txtTotalMonth2B = $command1->queryScalar();
                                                                    ?>
                                                                        <td class="text-center"><?php echo $txtTotalMonth2B; ?></td>
                                                                    <?php
                                                                        }
                                                                    ?>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-center"><?= Yii::t('app', 'Manual') ?></td>
                                                                    <?php
                                                                        $varListMonthBog = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

                                                                        foreach ($varListMonthBog as $key => $value) {
                                                                            $varListYearB = $value['mesyear']; 

                                                                            $txtQuery =  new Query;
                                                                            $txtQuery   ->select(['sum(tbl_control_volumenxclientedq.cantidadvalor)'])->distinct()
                                                                                        ->from('tbl_control_volumenxclientedq')
                                                                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                    'tbl_control_volumenxclientedq.idservicio = tbl_arbols.id')
                                                                                        ->where('tbl_arbols.arbol_id in (98)')
                                                                                        ->andwhere(['between','tbl_control_volumenxclientedq.mesyear', $varListYearB, $varListYearB]);
                                                                            $command = $txtQuery->createCommand();
                                                                            $txtTotalMonth1B = $command->queryScalar();
                                                                    ?>
                                                                        <td class="text-center"><?php echo $txtTotalMonth1B; ?></td>
                                                                    <?php
                                                                        }
                                                                    ?>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <br>
                                                    <?= Html::a('Ver Detalle',  ['graficamanuales'], ['class' => 'btn btn-success', 'style' => 'background-color: #337ab7', 'data-toggle' => 'tooltip', 'title' => 'Ver Detalle']) 
                                                    ?> 
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card2 mb">
                                                    <div id="conatinerbogota" class="highcharts-container" style="height: 300px;"></div>
                                                </div>
                                            </div>
                                </div>
                              </div>

                              <div id="Medellin" class="w3-container city" style="display:none">
                                <div class="row">
                                    <br>
                                    <div class="col-md-6">
                                                <div class="card2 mb">
                                                    <table class="table table-striped table-bordered detail-view formDinamico" border="0">
                                                        <thead>
                                                            <th class="text-center" style="background-color: #EEEEEE"><?= Yii::t('app', '') ?></th>
                                                                <?php

                                                                    $varMonthYear = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a   order by a.mesyear asc")->queryAll();

                                                                    foreach ($varMonthYear as $key => $value) {
                                                                        $varMonth = $value['CorteMes'];
                                                                        $varYear = $value['CorteYear'];
                                                                            $txtTMes = $varMonth.'- '.$varYear;
                                                                            $varTMes = Yii::$app->db->createCommand("select vozfecha from tbl_voz_fecha where anulado = 0 and cortefecha in ('$txtTMes')")->queryScalar();
                                                                ?>
                                                                    <th class="text-center" style="font-size: 15px; background-color: #EEEEEE"><?php echo $varTMes; ?></th>
                                                                <?php
                                                                    }
                                                                ?> 
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-center"><?= Yii::t('app', 'Automatica y Manual') ?></td>
                                                                <?php
                                                                    $varListMonthMed = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

                                                                    foreach ($varListMonthMed as $key => $value) {                        
                                                                        $varListYearM = $value['mesyear']; 
                                                                
                                                                        $txtQuery =  new Query;
                                                                        $txtQuery   ->select(['sum(tbl_control_volumenxclientedq.cantidadvalor)'])->distinct()
                                                                                    ->from('tbl_control_volumenxclientedq')
                                                                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                'tbl_control_volumenxclientedq.idservicio = tbl_arbols.id')
                                                                                    ->where('tbl_arbols.arbol_id in (2)')
                                                                                    ->andwhere(['between','tbl_control_volumenxclientedq.mesyear', $varListYearM, $varListYearM]);
                                                                        $command = $txtQuery->createCommand();
                                                                        $txtTotalMonth1M = $command->queryScalar();



                                                                        $txtQuery1 =  new Query;
                                                                        $txtQuery1  ->select(['sum(tbl_control_volumenxclienteds.cantidadvalor)'])->distinct()
                                                                                    ->from('tbl_control_volumenxclienteds')
                                                                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                'tbl_control_volumenxclienteds.idservicio = tbl_arbols.id')
                                                                                    ->where('tbl_arbols.arbol_id in (2)')
                                                                                    ->andwhere(['between','tbl_control_volumenxclienteds.mesyear', $varListYearM, $varListYearM]);
                                                                        $command1 = $txtQuery1->createCommand();
                                                                        $txtTotalMonth2M = $command1->queryScalar(); 

                                                                        $txtTotalKonectaM = $txtTotalMonth1M + $txtTotalMonth2M;
                                                                ?>
                                                                    <td class="text-center"><?php echo $txtTotalKonectaM; ?></td>
                                                                <?php
                                                                    }
                                                                ?>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center"><?= Yii::t('app', 'Automatica') ?></td>
                                                                <?php
                                                                    $varListMonthM = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

                                                                    foreach ($varListMonthM as $key => $value) {
                                                                        $varListYearM = $value['mesyear']; 
                                                                        
                                                                        $txtQuery1 =  new Query;
                                                                        $txtQuery1  ->select(['sum(tbl_control_volumenxclienteds.cantidadvalor)'])->distinct()
                                                                                    ->from('tbl_control_volumenxclienteds')
                                                                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                'tbl_control_volumenxclienteds.idservicio = tbl_arbols.id')
                                                                                    ->where('tbl_arbols.arbol_id in (2)')
                                                                                    ->andwhere(['between','tbl_control_volumenxclienteds.mesyear', $varListYearM, $varListYearM]);
                                                                        $command1 = $txtQuery1->createCommand();
                                                                        $txtTotalMonth2M = $command1->queryScalar();
                                                                ?>
                                                                    <td class="text-center"><?php echo $txtTotalMonth2M; ?></td>
                                                                <?php
                                                                    }
                                                                ?>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center"><?= Yii::t('app', 'Manual') ?></td>
                                                                <?php
                                                                    $varListMonthM = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

                                                                    foreach ($varListMonthM as $key => $value) {
                                                                        $varListYearM = $value['mesyear']; 

                                                                        $txtQuery =  new Query;
                                                                        $txtQuery   ->select(['sum(tbl_control_volumenxclientedq.cantidadvalor)'])->distinct()
                                                                                    ->from('tbl_control_volumenxclientedq')
                                                                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                'tbl_control_volumenxclientedq.idservicio = tbl_arbols.id')
                                                                                    ->where('tbl_arbols.arbol_id in (2)')
                                                                                    ->andwhere(['between','tbl_control_volumenxclientedq.mesyear', $varListYearM, $varListYearM]);
                                                                        $command = $txtQuery->createCommand();
                                                                        $txtTotalMonth1M = $command->queryScalar();
                                                                ?>
                                                                    <td class="text-center"><?php echo $txtTotalMonth1M; ?></td>
                                                                <?php
                                                                    }
                                                                ?>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <br>
                                                    <?= Html::a('Ver Detalle',  ['graficamanuales'], ['class' => 'btn btn-success', 'style' => 'background-color: #337ab7', 'data-toggle' => 'tooltip', 'title' => 'Ver Detalle']) 
                                                    ?> 
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card2 mb">  
                                                    <div id="conatinermedellin" class="highcharts-container" style="height: 300px;"></div>
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
    </div>
    
    <div id="capaUno" style="display: inline">
        <div id="dtbloque0" class="form-group col-sm-12" style="display: inline;">
        <hr>
            <div class="primeralinea">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card1 mb">
                            <div class="datakonecta">
                                <i class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></i><strong> Métricas de encuestas:</strong> 
                            </div>
                            <div class="datakonecta">                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card1 mb">
                                            <table class="table table-striped table-bordered detail-view formDinamico" border="0">
                                                <thead>
                                                    <th class="text-center" style="background-color: #EEEEEE"><?= Yii::t('app', '') ?></th>
                                                    <?php
                                                        $varLastDate0 = new DateTime($varLastDate);
                                                        $varLastDate0->modify('last day of this month');
                                                        $varLastDate1 = $varLastDate0->format('Y/m/d');


                                                        $varMonthYear = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a   order by a.mesyear asc")->queryAll();

                                                        foreach ($varMonthYear as $key => $value) {
                                                            $varMonth = $value['CorteMes'];
                                                            $varYear = $value['CorteYear'];

                                                            $txtTMes = $varMonth.'- '.$varYear;
                                                            $varTMes = Yii::$app->db->createCommand("select vozfecha from tbl_voz_fecha where anulado = 0 and cortefecha in ('$txtTMes')")->queryScalar();
                                                    ?>
                                                        <th class="text-center" style="font-size: 15px;  background-color: #EEEEEE"><?php echo $varTMes; ?></th>
                                                    <?php
                                                        }
                                                    ?>           
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $txtCiudades = Yii::$app->db->createCommand(" select id, name, arbol_id from tbl_arbols where id in (98, 2, 1)")->queryAll(); 

                                                        $txtTotalAVG = null;
                                                        foreach ($txtCiudades as $key => $value) {
                                                            $txtNameCity = $value['name'];
                                                            $varIdPcrc = $value['id'];

                                                            $varListMonth = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a   order by a.mesyear asc")->queryAll();                                             

                                                    ?>
                                                        <tr>
                                                            <td class="text-center"><?php echo $txtNameCity; ?></td>

                                                            <?php
                                                                $txtTotalMonth = null;
                                                                foreach ($varListMonth as $key => $value) {
                                                                    $varListYear = $value['mesyear']; 

                                                                    if ($varIdPcrc == 1) {
                                                                        $txtQuery =  new Query;
                                                                        $txtQuery   ->select(['sum(tbl_control_volumenxencuestasdq.cantidadvalor)'])->distinct()
                                                                                    ->from('tbl_control_volumenxencuestasdq')
                                                                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                'tbl_control_volumenxencuestasdq.idservicio = tbl_arbols.id')
                                                                                    ->where('tbl_arbols.arbol_id in (2, 98)')
                                                                                    ->andwhere(['between','tbl_control_volumenxencuestasdq.mesyear', $varListYear, $varListYear]);                    
                                                                        $command = $txtQuery->createCommand();
                                                                        $txtTotalMonth1 = $command->queryScalar();

                                                                        $txtTotalMonth =  $txtTotalMonth1;
                                                                    }else{
                                                                        $txtQuery =  new Query;
                                                                        $txtQuery   ->select(['sum(tbl_control_volumenxencuestasdq.cantidadvalor)'])->distinct()
                                                                                    ->from('tbl_control_volumenxencuestasdq')
                                                                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                                                                'tbl_control_volumenxencuestasdq.idservicio = tbl_arbols.id')
                                                                                    ->where('tbl_arbols.arbol_id = '.$varIdPcrc.'')
                                                                                    ->andwhere(['between','tbl_control_volumenxencuestasdq.mesyear', $varListYear, $varListYear]);                    
                                                                        $command = $txtQuery->createCommand();
                                                                        $txtTotalMonth1 = $command->queryScalar(); 

                                                                        $txtTotalMonth =  $txtTotalMonth1;
                                                                    }
                                                            ?>
                                                                <td class="text-center"><?php echo round($txtTotalMonth); ?></td>
                                                            <?php
                                                                }
                                                            ?>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                            <br>
                                            <?= Html::a('Ver Detalle',  ['graficaencuestasday'], ['class' => 'btn btn-success', 'style' => 'background-color: #337ab7', 'data-toggle' => 'tooltip', 'title' => 'Ver Detalle']) 
                                            ?> 
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card1 mb">
                                            <div id="conatinerencuestas" class="highcharts-container" style="height: 300px;"></div>
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

    <div id="capaDos" style="display: inline">
        <div id="dtbloque1" class="form-group col-sm-12" style="display: inline;">
        <hr>
            <div class="primeralinea">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card1 mb">
                            <div class="datakonecta">
                                <i class="fas fa-search" style="font-size: 20px; color: #C148D0;"></i><strong> Búsqueda por servicio:</strong> 
                                <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
                                <div class="row">
                                    <div class="col-mod-6">
                                        <?=  $form->field($model, 'arbol_id')->dropDownList(ArrayHelper::map(\app\models\Arboles::find()->distinct()->where("arbol_id in (2, 98)")->andwhere("activo = 0")->orderBy(['name'=> SORT_ASC])->all(), 'id', 'name'),
                                                    [
                                                        'prompt'=>'Seleccione Cliente...',
                                                    ]
                                                )->label('Cliente'); 
                                        ?>
                                    </div>
                                    <div class="form-group" align="center">
                                        <?= Html::submitButton(Yii::t('app', 'Buscar Servicio'),
                                                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'data-toggle' => 'tooltip',
                                                        'onclick' => 'verificaclick();',
                                                        'title' => 'Buscar Servicio']) 
                                        ?>
                                    </div>
                                </div>
                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <br>
        </div>
    </div>
</div>


<?php if ($sessiones == 2953 || $sessiones == 2911 || $sessiones == 3205 || $sessiones == 3205) { ?>
<hr>
<div class="CapaDos" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-wrench" style="font-size: 20px; color: #15aabf;"></i> Configuraciones Generales: </label>
                <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?> 
                <div class="row">
                                    
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><i class="fas fa-wrench" style="font-size: 15px; color: #15aabf;"></i> Parametrizar Gerentes: </label>                           

                                <?= Html::button('Parametrizar Gerentes', ['value' => url::to('parametrizargerentes'), 'class' => 'btn btn-success', 'id'=>'modalButton5', 'data-toggle' => 'tooltip', 'title' => 'Parametrizar Gerentes', 'style' => 'background-color: #337ab7']) 
                                ?>
                                <?php
                                    Modal::begin([
                                                'header' => '<h4>Parametrizar Gerentes </h4>',
                                                'id' => 'modal5',
                                                //'size' => 'modal-lg',
                                              ]);

                                    echo "<div id='modalContent5'></div>";
                                                                    
                                    Modal::end(); 
                                ?> 
                            
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><i class="fas fa-wrench" style="font-size: 15px; color: #15aabf;"></i> Parametrizar Encuestas: </label>                           

                                <?= Html::a('Parametrizar Encuestas',  ['parametrizarencuestas'], ['class' => 'btn btn-success',
                                    'style' => 'background-color: #4298b4',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Parametrizar Encuestas']) 
                                ?>
                            
                        </div>
                    </div>                    
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><i class="fas fa-wrench" style="font-size: 15px; color: #15aabf;"></i> Parametrizar Satu/Insatu Valoracion: </label>                           

                                <?= Html::a('Parametrizar SATU-INSATU Valoracion',  ['parametrizarindicadores'], ['class' => 'btn btn-success',
                                    'style' => 'background-color: #4298b4',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Parametrizar Indicadores SATU-INSATU Valoracion']) 
                                ?> 
                            
                        </div>
                    </div>                  
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><i class="fas fa-wrench" style="font-size: 15px; color: #15aabf;"></i> Parametrizar Rta/NoRta Valoracion: </label>                           

                                <?= Html::a('Parametrizar RTA-NO RTA Valoracion',  ['parametrizarindicadores2'], ['class' => 'btn btn-success',
                                    'style' => 'background-color: #4298b4',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Parametrizar Indicadores SOLUCION-NO SOLUCION Valoracion']) 
                                ?>  
                            
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><i class="fas fa-wrench" style="font-size: 15px; color: #15aabf;"></i> Parametrizar Satu/Insatu Encuestas: </label>                           

                                <?= Html::a('Parametrizar SATU-INSATU Encuestas',  ['parametrizarindicadores3'], ['class' => 'btn btn-success',
                                    'style' => 'background-color: #4298b4',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Parametrizar Indicadores SATU-INSATU Valoracion']) 
                                ?>  
                            
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><i class="fas fa-wrench" style="font-size: 15px; color: #15aabf;"></i> Parametrizar Rta/NoRta Encuestas: </label>                           

                                <?= Html::a('Parametrizar RTA-NO RTA Encuestas',  ['parametrizarindicadores4'], ['class' => 'btn btn-success',
                                    'style' => 'background-color: #4298b4',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Parametrizar Indicadores SOLUCION-NO SOLUCION Valoracion']) 
                                ?> 
                            
                        </div>
                    </div>
                
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<hr>
<?php  } ?>


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
}

    $(function() {

        var Listado = "<?php echo implode($varListCorte,",");?>";
        Listado = Listado.split(",");
        //console.log(Listado);

        Highcharts.setOptions({
                lang: {
                  numericSymbols: null,
                  thousandsSep: ','
                }
        });

        $('#conatinervaloraciones').highcharts({
            chart: {                
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Cantidad Valoraciones'
              }
            }, 

            title: {
              text: '',
            style: {
                    color: '#3C74AA'
              }
            },

            xAxis: {
                  categories: Listado,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Métrica Konecta',
              data: [<?= join($varListKonecta, ',')?>],
              color: '#4298B5'
            },{
              name: 'Métrica Medellín',
              data: [<?= join($varListMed, ',')?>],
              color: '#615E9B'
            },{
              name: 'Métrica Bogota',
              data: [<?= join($varListBog, ',')?>],
              color: '#FFc72C'
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

          $('#conatinerkonecta').highcharts({
            chart: {
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Cantidad Valoraciones'
              }
            }, 

            title: {
              text: '',
            style: {
                    color: '#3C74AA'
              }
            },

            xAxis: {
                  categories: Listado,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Total Valoración Nivel Konecta',
              data: [<?= join($varListKonecta, ',')?>],
              color: '#4298B5'
            },{
              name: 'Total Valoración Manual Konecta',
              data: [<?= join($varListKonectaManual, ',')?>],
              color: '#615E9B'
            },{
              name: 'Total Valoración Automático Konecta',
              data: [<?= join($varListKonectaSpeech, ',')?>],
              color: '#FFc72C'
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


          $('#conatinermedellin').highcharts({
            chart: {
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Cantidad Valoraciones'
              }
            }, 

            title: {
              text: '',
            style: {
                    color: '#3C74AA'
              }
            },

            xAxis: {
                  categories: Listado,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Total Valoración Medellín',
              data: [<?= join($varListMed, ',')?>],
              color: '#4298B5'
            },{
              name: 'Total Valoracion Manual Medellín',
              data: [<?= join($varListMedManual, ',')?>],
              color: '#615E9B'
            },{
              name: 'Total Valoración Automático Medellín',
              data: [<?= join($varListMedSpeech, ',')?>],
              color: '#FFc72C'
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

          $('#conatinerbogota').highcharts({
            chart: {
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Cantidad Valoraciones'
              }
            }, 

            title: {
              text: '',
            style: {
                    color: '#3C74AA'
              }
            },

            xAxis: {
                  categories: Listado,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Total Valoración Nivel Bogotá',
              data: [<?= join($varListBog, ',')?>],
              color: '#4298B5'
            },{
              name: 'Total Valoración Manual Bogotá',
              data: [<?= join($varListBogManual, ',')?>],
              color: '#615E9B'
            },{
              name: 'Total Valoración Automático Bogotá',
              data: [<?= join($varListBogSpeech, ',')?>],
              color: '#FFc72C'
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

          $('#conatinerencuestas').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Cantidad Encuestas'
              }
            }, 

            title: {
              text: '',
            style: {
                    color: '#3C74AA'
              }

            },

            xAxis: {
                  categories: Listado,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Total Encuestas Konecta',
              data: [<?= join($varListKonectae, ',')?>],
              color: '#4298B5'
            },{
              name: 'Total Encuestas Medellín',
              data: [<?= join($varListMede, ',')?>],
              color: '#615E9B'
            },{
              name: 'Total Encuestas Bogotá',
              data: [<?= join($varListBoge, ',')?>],
              color: '#FFc72C'
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



          Highcharts.getOptions().exporting.buttons.contextButton.menuItems.push({
            text: 'Additional Button',
            onclick: function() {
              alert('OK');
              /*call custom function here*/
            }
          });
    }); 

    function verificaclick() {
        var varseleccion = document.getElementById("vozseleccion-arbol_id").value;

        if (varseleccion == "") {
            event.preventDefault();
            swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar el servicio.","warning");
            return; 
        }
    };
</script>