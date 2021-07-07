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


$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='tooltip']").tooltip(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);
$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='popover']").popover(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);


$this->title = 'Evaluacion de desarrollo';
$this->params['breadcrumbs'][] = $this->title;
//$documento = $documento;
$titulos = array();
    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $vardocument = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
    
    //$vardocument = 1152214703;
    $documento = $vardocument;
    

    $varnombre = Yii::$app->db->createCommand("select nombre_completo from  tbl_usuarios_evalua where documento in ('$documento')")->queryScalar();
    $varcargo = Yii::$app->db->createCommand("select distinct concat(posicion,' - ',funcion) from  tbl_usuarios_evalua where documento in ('$vardocument')")->queryScalar();
    $vartipoeva = Yii::$app->db->createCommand("select tipoevaluacion from tbl_evaluacion_tipoeval where idevaluaciontipo = 1 and anulado = 0")->queryScalar();
    $varobservacion = Yii::$app->db->createCommand("select observacion_feedback from  tbl_evaluacion_resulta_feedback where documento in ('$documento')")->queryScalar();
    $txtvalidadocumento = Yii::$app->db->createCommand("select count(documento) from tbl_evaluacion_resulta_feedback WHERE documento = $vardocument")->queryScalar();
    $last_word_start = strrpos($varnombre, ' ') + 1;
    $last_word = substr($varnombre, $last_word_start);

    $catidad = count(explode(" ", $varnombre));
    $posicion_espacio=strrpos($varnombre, " ");
    $longitud=strlen($varnombre);
    $nombre1=substr($varnombre,$posicion_espacio);
    $nombre3=substr($varnombre,0,$posicion_espacio);
    $posicion_espacio=strrpos($nombre3, " ");
    $longitud=strlen($nombre3);
    $nombre2=substr($nombre3,$posicion_espacio +1);
    if($catidad == 4){
        $last_word = $nombre2.$nombre1;
    }

    $query2 = Yii::$app->db->createCommand("select * from tbl_evaluacion_respuestas2 where anulado = 0")->queryAll();
    $listData2 = ArrayHelper::map($query2, 'idevaluacionrespuesta', 'namerespuesta');

    $query3 = Yii::$app->db->createCommand("select * from tbl_usuarios_evalua where anulado = 0 and documento_jefe = $vardocument" )->queryAll();
    $listData3 = ArrayHelper::map($query3, 'documento', 'nombre_completo');

    $varlistbloques = Yii::$app->db->createCommand("select * from tbl_evaluacion_bloques where anulado = 0")->queryAll();

    $varconteobloque = 0;
    $varconteocompetencia = 0;
    $varconteopregunta = 0;
    $txtnotafinal = null;

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
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }
    .card2 {
           height: 190px;
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
   .card3 {
           height: 160px;
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
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

    .loader {
      border: 16px solid #f3f3f3;
      border-radius: 50%;
      border-top: 16px solid #3498db;
      width: 80px;
      height: 80px;
      -webkit-animation: spin 2s linear infinite; /* Safari */
      animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
      0% { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<link rel="stylesheet" href="https://qa.grupokonecta.local/qa_managementv2/web/font_awesome_local/css.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<!-- Full Page Image Header with Vertically Centered Content -->
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
<div id="idCapa" style="display: none">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-spinner" style="font-size: 20px; color: #2CA5FF;"></i> Procesando datos</label>
                <div class="col-md-12">
                    <table>
                        <tr>
                            <td class="text-center"><div class="loader"> </div></td>
                            <td class="text-center"><label><?= Yii::t('app', ' Guardando datos de la evaluaci�n realizada') ?></label></td>
                        </tr>
                    </table>                                       
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<?php
if($txtvalidadocumento > 0) { ?>
<div id="idCapaUno" style="display: inline"> 
    
    <div id="capaDos" style="display: inline">
        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>   
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label><i class="fas fa-exclamation" style="font-size: 20px; color: #827DF9;"></i> Notificaciones:</label>
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-body" style="background-color: #f0f8ff;">Hola <?php echo $last_word; ?>, Aquí encuentras los resultados consolidados de tu evaluación.
                                    </div>
                                </div>
                            </div>  

                    </div>
                </div>                
            </div>
        <?php ActiveForm::end(); ?>
    </div>    
    <hr> 

    <?php 
    //if ($documento != 0) { 
        //quitar este documento        
        //$documento = 1128435828;
        $varNum = 1;
        $varresulta = 'Resultados ';
        $varsumacompetencia = 0;
        $varmenorcompetencia = 0;
        $prueba = "doughnut-chart".$varNum;
	    $evaluacompe1 = 0;
        $evaluaorgan1 = 0;
        $evaluadese1 = 0;
        $evaluacompe2 = 0;
        $evaluaorgan2 = 0;
        $evaluadese2 = 0;
        $evaluacompe3 = 0;
        $evaluaorgan3 = 0;
        $evaluadese3 = 0;
        $evaluacompe4 = 0;
        $evaluaorgan4 = 0;
        $evaluadese4  = 0;
	    $bloque1 =0;
        $bloque2 =0;
        $bloque3 =0;        
         
        $varnombrec = Yii::$app->db->createCommand("select nombre_completo from tbl_usuarios_evalua where documento = $documento")->queryScalar();
        $varrol = Yii::$app->db->createCommand("select distinct concat(posicion,' - ',funcion) from  tbl_usuarios_evalua where documento in ('$documento')")->queryScalar();
        $varnivel = Yii::$app->db->createCommand("select en.nivel FROM tbl_evaluacion_solucionado es inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia 
                                INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel WHERE es.documentoevaluado = $documento GROUP BY es.idevaluacioncompetencia LIMIT 1")->queryScalar();
        $varcomentarios = Yii::$app->db->createCommand("select es.comentarios FROM tbl_evaluacion_solucionado es WHERE es.documentoevaluado = $documento GROUP BY es.idevaluacioncompetencia LIMIT 1")->queryScalar();
       
//para c�lculo de tipo de coaching
      /*  $listacompetenciat2 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                    FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                    ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque
                                                    FROM tbl_evaluacion_solucionado es
                                                    INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                    INNER JOIN tbl_usuarios_evalua ue ON es.documentoevaluado = ue.documento
                                                    inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                    INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                    INNER JOIN tbl_evaluacion_bloques eb ON ec.idevaluacionbloques = eb.idevaluacionbloques
                                                    WHERE es.documentoevaluado = $documento 
                                                    GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques")->queryAll();

        foreach ($listacompetenciat2 as $key => $value) {
            if($value['%Competencia'] < 85){
                $varmenorcompetencia = 1;
            }
        }*/

        for ($i = 1; $i <= 4; $i++) {
            
            $listacompetenciat = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque, ef.mensaje
                                                FROM tbl_evaluacion_solucionado es
                                                INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                INNER JOIN tbl_usuarios_evalua ue ON es.documentoevaluado = ue.documento
                                                inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                WHERE es.documentoevaluado = $documento AND es.idevaluaciontipo = $i 
                                                GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques")->queryAll();

            foreach ($listacompetenciat as $key => $value) {
                $varsumacompetencia = $varsumacompetencia + $value['%Competencia'];
                
                if($value['%Competencia'] < 85){
                    $varmenorcompetencia = 1;
                }
                if($i == 1){
                    $varconteocompetencia = $varconteocompetencia + 1;
                    
                    if($value['idevaluacionbloques'] == 1){ 

                        $evaluacompe1 = $evaluacompe1 + ($value['%Competencia'] * 40) / 100;
                        $bloque1 = $bloque1 + 1;
                    }
                    if($value['idevaluacionbloques'] == 2){

                        $evaluaorgan1 = $evaluaorgan1 + ($value['%Competencia'] * 20) / 100;                        
                        $bloque2 = $bloque2 + 1;
                    }
                    if($value['idevaluacionbloques'] == 3){
                        
                        $evaluadese1 = $evaluadese1 + ($value['%Competencia'] * 40) / 100;
                        $bloque3 = $bloque3 + 1;
                    }                 
                }
                if($i == 3){
                    if($value['idevaluacionbloques'] == 1){
                        $evaluacompe2 = $evaluacompe2 + ($value['%Competencia'] * 40) / 100;
                    }
                    if($value['idevaluacionbloques'] == 2){
                        $evaluaorgan2 = $evaluaorgan2 + ($value['%Competencia'] * 20) / 100;
                    }
                    if($value['idevaluacionbloques'] == 3){
                        $evaluadese2 = $evaluadese2 + ($value['%Competencia'] * 40) / 100;
                    }                 
                }
                if($i == 4){
                    if($value['idevaluacionbloques'] == 1){
                        $evaluacompe3 = $evaluacompe3 + ($value['%Competencia'] * 40) / 100;
                    }
                    if($value['idevaluacionbloques'] == 2){
                        $evaluaorgan3 = $evaluaorgan3 + ($value['%Competencia'] * 20) / 100;
                    }
                    if($value['idevaluacionbloques'] == 3){
                        $evaluadese3 = $evaluadese3 + ($value['%Competencia'] * 40) / 100;
                    }                 
                }
                if($i == 2){
                    if($value['idevaluacionbloques'] == 1){
                        $evaluacompe4 = $evaluacompe4 + ($value['%Competencia'] * 40) / 100;
                    }
                    if($value['idevaluacionbloques'] == 2){
                        $evaluaorgan4 = $evaluaorgan4 + ($value['%Competencia'] * 20) / 100;
                    }
                    if($value['idevaluacionbloques'] == 3){
                        $evaluadese4 = $evaluadese4 + ($value['%Competencia'] * 40) / 100;
                    }                 
                }
            }            
        }
	if($evaluacompe1 > 0){
            
            $evaluacompe1 = $evaluacompe1 / $bloque1;
            $evaluaorgan1 = $evaluaorgan1 / $bloque2; 
            $evaluadese1 = $evaluadese1 / $bloque3;
        }
        if($evaluacompe2 > 0){
            $evaluacompe2 = $evaluacompe2 / $bloque1;
            $evaluaorgan2 = $evaluaorgan2 / $bloque2;
            $evaluadese2 = $evaluadese2 / $bloque3;
        }
        if($evaluacompe3 > 0){
            $evaluacompe3 = $evaluacompe3 / $bloque1;
            $evaluaorgan3 = $evaluaorgan3 / $bloque2;
            $evaluadese3 = $evaluadese3 / $bloque3;
        }
        if($evaluacompe4 > 0){
            $evaluacompe4 = $evaluacompe4 / $bloque1;
            $evaluaorgan4 = $evaluaorgan4 / $bloque2;
            $evaluadese4 = $evaluadese4 / $bloque3;
        }

        $txtevalua1 = $evaluacompe1 + $evaluaorgan1 + $evaluadese1;
        $txtevalua2 = $evaluacompe2 + $evaluaorgan2 + $evaluadese2;
        $txtevalua3 = $evaluacompe3 + $evaluaorgan3 + $evaluadese3;
        $txtevalua4 = $evaluacompe4 + $evaluaorgan4 + $evaluadese4;
        //var_dump($txtevalua1 );
 	    //var_dump($txtevalua2);
         $txtnotafinal = null;
        if($txtevalua1 != 0 && $txtevalua2 != 0 && $txtevalua3 == 0 && $txtevalua4 == 0) {
            $txtnotafinal = (($txtevalua1 * 20)/100) + (($txtevalua2 * 80) /100);
        }
        if($txtevalua1 != 0 && $txtevalua2 != 0 && $txtevalua3 == 0 && $txtevalua4 != 0) {
            $txtnotafinal = (($txtevalua1 * 15)/100) + (($txtevalua2 * 70) /100) + (($txtevalua4 * 15) /100);             
        }
        if($txtevalua1 != 0 && $txtevalua2 != 0 && $txtevalua3 != 0 && $txtevalua4 == 0) {
            $txtnotafinal = (($txtevalua1 * 10)/100) + (($txtevalua2 * 60) /100) + (($txtevalua3 * 30) /100);            
        }
        if($txtevalua1 != 0 && $txtevalua2 != 0 && $txtevalua3 != 0 && $txtevalua4 != 0) {


        }

        $txtProcentaje = $txtnotafinal;
        if($txtProcentaje >= 85 && $varmenorcompetencia == 0) {
            $tipocoaching = 'Opcional';
        } else if($txtProcentaje >= 85 && $varmenorcompetencia == 1){
                $tipocoaching = 'Por Competencia';
            } else if($txtProcentaje <= 85 && $varmenorcompetencia == 1) {
                $tipocoaching = 'General';
                }
        
        array_push($titulos, $txtProcentaje);
    ?>    
        <div id="CapaTres" style="display: inline">
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label><i class="far fa-file-alt" style="font-size: 18px; color: #C148D0;"></i> Resultados: </label>                        
                        <div class="row"> 
                            <div class="col-md-4">
                                <div class="card2 mb">
                                    <label style="font-size: 17px;"><i class="far fa-id-badge" style="font-size: 20px; color: #C148D0;"></i> Nombre: </label>
                                    <label style="font-size: 20px;">&nbsp;&nbsp;&nbsp; <?php echo $varnombrec; ?> </label><br>
                                    <label style="font-size: 17px;"><i class="fas fa-male" style="font-size: 20px; color: #C148D0;"></i> Rol: </label>
                                    <label style="font-size: 20px;">&nbsp;&nbsp;&nbsp; <?php echo $varrol; ?> </label>                                                                         
                                </div>
                            </div>   
                            <div class="col-md-4">
                                <div class="card2 mb">
                                    <label style="font-size: 17px;"><i class="fas fa-bars" style="font-size: 18px; color: #C148D0;"></i> Calificación Final </label>
                                    <table style="width:100%">
                                        <td class="text-center" width="100"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="<?php echo $prueba; ?>"></canvas></div><span style="font-size: 15px;"><?php echo round($txtProcentaje,2).' %'; ?></span></td> 
                                    </table> 
                                </div>
                            </div>                            
                            <div class="col-md-4">
                                <div class="card2 mb">
                                    <label style="font-size: 17px;" ><i class="far fa-comment-alt" style="font-size: 18px; color: #C148D0;"></i> Observaciones </label>
                                    <textarea type="text" class="form-control" readonly="readonly" id="txtobserva" value="<?php echo $varcomentarios; ?>" data-toggle="tooltip" title="Observaciones"><?php echo $varcomentarios; ?></textarea>
                                    <br>                                                               
                                    <label style="font-size: 17px;"><i class="fas fa-check" style="font-size: 18px; color: #C148D0;"></i> Tipo Coaching </label>                            
                                    <label style="font-size: 20px; ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $tipocoaching; ?> </label>
                                </div>                            
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
    </div> 
    <?php
        
        foreach ($varlistbloques as $key => $value) {
            $varidbloque = $value['idevaluacionbloques'];  
            $varconteobloque = $varconteobloque + 1;

            $varcolor = null;
            if ($varidbloque == 1) {
                $varcolor = "color: #F9BD4C;";
            }else{
                if ($varidbloque == 2) {
                    $varcolor = "color: #22D7CF;";
                }else{
                    if ($varidbloque == 3) {
                        $varcolor = "color: #49de70;";
                    }
                }
            }
    ?>
        <hr>
        <div id="capaCuatro" style="display: inline">
          <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label><i class="fas fa-cubes" style="font-size: 20px; <?php echo $varcolor; ?>"></i> <?php echo $varresulta.$value['namebloque'].': '; ?></label>
                        
                        <div class="row">                    
                            <?php 
                                $listacompetencia = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                    FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                    ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque, ef.mensaje, ef.mensajepos
                                                    FROM tbl_evaluacion_solucionado es
                                                    INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                    INNER JOIN tbl_usuarios_evalua ue ON es.documentoevaluado = ue.documento
                                                    inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                    INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                    INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                    LEFT JOIN tbl_evaluacion_mensaje_resul ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                    WHERE es.documentoevaluado = $documento and ec.idevaluacionbloques = $varidbloque 
                                                    GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques")->queryAll();
                                
                                foreach ($listacompetencia as $key => $value) {
                                    $varidcompetencia = $value['idevaluacioncompetencia'];
                                    $varconteocompetencia = $varconteocompetencia + 1;
                                    $varcolor2 = null;
                                    if($value['%Competencia'] < 85){
                                        $varcolor2 = "color: #f5500f;";
                                    }else{
                                        $varcolor2 = "color: #0d0d0d;";
                                    }
                            ?>
                                    
                                    <div class="col-md-6">
                                        <div class="card1 mb">  
                                            <div class="row">                                    
                                                <div class="col-md-6">
                                                    <label style="font-size: 16px;"><i class="fas fa-bookmark" style="font-size: 15px; <?php echo $varcolor; ?>"></i> <?php echo $value['namecompetencia'].'.'; ?></label>   
                                                </div>
                                                <div class="col-md-6">
                                                   <label id="<?php echo 'idtext'.$varconteocompetencia.$varconteobloque; ?>" style="font-size: 15px; <?php echo $varcolor2; ?>"> <?php echo $value['%Competencia'].'% '; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                                                   
                                                   <?php if($value['%Competencia'] < 85 && $value['idevaluacionbloques'] == 1){?>
                                                        <div onclick="openmensaje(<?php echo $varconteocompetencia.$varconteobloque; ?>);" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="<?php echo 'idtbn1'.$varconteocompetencia.$varconteobloque; ?>" >
                                                            <span class="fas fa-caret-down" style="font-size: 30px; color: #f7b9b9;" ></span>
                                                        </div>
                                                        <div onclick="closemensaje(<?php echo $varconteocompetencia.$varconteobloque; ?>);" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="<?php echo 'idtbn2'.$varconteocompetencia.$varconteobloque; ?>" >
                                                             <span class="fas fa-caret-up" style="font-size: 30px; color: #f7b9b9;" ></span>
                                                        </div>
                                                   <?php } ?>
                                                
                                                   <?php if($value['%Competencia'] >= 85 && $value['idevaluacionbloques'] == 1){?>
                                                        <div onclick="openmensaje85(<?php echo $varconteocompetencia.$varconteobloque.'85'; ?>);" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="<?php echo 'idtbn1'.$varconteocompetencia.$varconteobloque.'85'; ?>" >
                                                            <span class="fas fa-caret-down" style="font-size: 30px; color: #63e665;" ></span>
                                                        </div>
                                                        <div onclick="closemensaje85(<?php echo $varconteocompetencia.$varconteobloque.'85'; ?>);" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="<?php echo 'idtbn2'.$varconteocompetencia.$varconteobloque.'85'; ?>" >
                                                             <span class="fas fa-caret-up" style="font-size: 30px; color: #63e665;" ></span>
                                                        </div>
                                                   <?php } ?>
                                                </div>
                                                <div class="col-md-12">
                                                    <div id="<?php echo 'idmensaje'.$varconteocompetencia.$varconteobloque; ?>" style="display: none">
                                                        <div class="panel panel-default">
                                                            <div class="panel-body" style="background-color: #f0f8ff; font-size: 16px;"><?php echo $value['mensaje']; ?>
                                                            </div>
                                                        </div>
                                                    </div>                
                                                </div>
                                                <div class="col-md-12">
                                                    <div id="<?php echo 'idmensaje'.$varconteocompetencia.$varconteobloque.'85'; ?>" style="display: none">
                                                        <div class="panel panel-default">
                                                            <div class="panel-body" style="background-color: #f0f8ff; font-size: 16px;"><?php echo $value['mensajepos']; ?>
                                                            </div>
                                                        </div>
                                                    </div>                
                                                </div>
                                                
                                            </div> 
                                        </div>
                                        <br>
                                    </div>
                                <?php 
                                    } 
                                ?>
                        </div>
                    </div>
                </div>
            </div>
          <?php ActiveForm::end(); ?>
        </div> 
    <?php } ?>
    <hr>
    <div id="capaSiete" style="display: inline"> 
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <div class="col-md-12">
                            <label style="font-size: 17px;"><i class="fas fa-exclamation-circle" style="font-size: 17px; color: #8B70FA;"></i> Metodología 70-20-10 </label>
                            
                            <div onclick="openobserva2();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idmensaje12" >
                                <span class="fas fa-info-circle" style="font-size: 20px; color: #f5900c;" ></span>
                            </div>
                            <div onclick="closeobserva2();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="idmensaje22" >
                                <span class="fas fa-info-circle" style="font-size: 20px; color: #28c916;" ></span>
                            </div>                            
                        </div>
                        <div class="col-md-12">
                            <div id="idpanelobserva2" style="display: none">
                                <div class="panel panel-default">
                                    <div class="panel-body" style="background-color: #f0f8ff; font-size: 16px;"> Identificamos el alto potencial que aportas a la compañia,  así como las oportunidades de desarrollo en las que te podremos acompañar, a través de un plan de desarrollo 70-20-10: 70% Autodesarrollo y autogestión 20% Mentoring y acompañamiento de tu jefe inmediato 10% Formación especifica proporcIonada por la compañia para tu rol Te sugerimos iniciar por las 3 competencias más lejos del umbral esperado y que sean estrategicas para tu proceso
                                    </div>
                                </div>
                            </div>                
                        </div>
                    </div>
                </div>
            </div>
        </div>     
    <hr>   
        <div id="capaCinco" style="display: inline"> 
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <div class="col-md-12">
                            <label style="font-size: 17px;"><i class="fas fa-comments" style="font-size: 17px; color: #8B70FA;"></i> Descripción del feedback realizado por el jefe </label>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-body" style="background-color: #f0f8ff;"> <?php echo $varobservacion; ?>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    <hr>
      
    
    <div id="capaSeis" style="display: inline"> 
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 17px;"><i class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></i> Acciones: </label>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><i class="fas fa-minus-circle" style="font-size: 17px; color: #FFC72C;"></i> Biblioteca de Conocimiento: </label> 
                                                            
                                <a href="https://paco.grupokonecta.co/course/view.php?id=880" target="_blank" class="btn btn-success">Ir a Paco (Jefes - Personas)</a>                            
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
</div>
    <?php 
   // }    
    ?>    
    
    
</div>
<?php } else { ?>
  <div class="Seis">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
              <label><i class="fas fa-info-circle" style="font-size: 20px; color: #1e8da7;"></i> Información:</label>
              <label style="font-size: 14px;">No se ha completado el proceso de resultados de las evaluaciones</label>
                </div><br>
            </div>
        </div>  
    </div>
  </div><br>

  <?php } ?>


<script type="text/javascript">
var vardocumento = '<?php echo $documento; ?>';

    var varDataNum = [<?= join($titulos, ',')?>];
    var dato = varDataNum[0];
    var color = "";
    if(dato < 85){
        color = "#f28022";
    } else {
        color = "#05b526";
    }
    console.log(dato);
    $(document).ready(function(){
    console.log(varDataNum.length);
    for (var i = 0; i < varDataNum.length; i++) {  
        var varNume = i + 1;  
        var nombre_Chart = "doughnut-chart" + varNume;
        // console.log(nombre_Chart);
        var array_Temp = [];
        array_Temp.push(varDataNum[i]);
        array_Temp.push(100-varDataNum[i]);
        console.log(array_Temp);
        new Chart(document.getElementById(nombre_Chart), {
            type: 'doughnut',
            data: {      
            datasets: [
                {         
                labels : ['Porcentaje: '],
                backgroundColor: [color],
                data: array_Temp
                }
            ]
            }
        });  
    }
  
});
    function carguedato(){
       
	    var vardocumento = document.getElementById("idlistaevaluado").value;
        var varmodelo= 0;
        $.ajax({
                method : "get",
                url : "validaevaluado",
                data : {
                    txtvardocumento : vardocumento,
                       },
                success : function(response){
                    var numRta =   JSON.parse(response);
                    if (numRta == 0) {
                                jQuery(function(){
                                    swal.fire({type: "warning",
                                        title: "!!! Información !!!",
                                        text: "No ha completado el mínimo del proceso de evaluación"
                                    }).then(function() {
                                        return;
                                    });
                                });
                              }else{
                                event.preventDefault();
                                  window.location.href='evaluacionfeedback?model='+varmodelo+'&documento='+vardocumento;
                                
                              }
                     
                }   

            });         
      }

    function generated(){
        var varconteobloque = '<?php echo $varconteobloque; ?>';
        var varconteocompetencia = '<?php echo $varconteocompetencia; ?>';
        var varconteopregunta = '<?php echo $varconteopregunta; ?>';
        var vardocumento = '<?php echo $vardocument; ?>';
        var numRta = 0;
        var varidCapa = document.getElementById("idCapa");
        var varidCapaUno = document.getElementById("idCapaUno");

        varidbloque = 0;
        for (var i = 0; i < varconteobloque; i++) {
            varbloque = i + 1;
            var varidbloque = document.getElementById('Idbloque'+varbloque).value;
            // console.log('El bloque es: '+varidbloque);

            varcompetencia = 0;
            for (var j = 0; j < varconteocompetencia; j++) {
                varcompetencia = j + 1;
                var varcompara = document.getElementById('IdCompetencia'+varcompetencia+varidbloque);                
                
                if (varcompara != null) {
                    var varidcompetencia = document.getElementById('IdCompetencia'+varcompetencia+varidbloque).value;
                    // console.log('La competencia es: '+varidcompetencia);

                    for (var k = 0; k < varconteopregunta; k++) {
                        varpreguntas = k + 1;

                        var varpreg = document.getElementById('Idpre'+varpreguntas+varcompetencia+varidbloque);

                        if (varpreg != null) {
                            var varidtext = document.getElementById('idtext'+varpreguntas+varcompetencia+varidbloque).innerHTML;
                            // console.log(varidtext);
                            var varidpreg = document.getElementById('Idpre'+varpreguntas+varcompetencia+varidbloque).value;
                            // console.log('La pregunta es: '+varidpreg);
                            var varidrta = document.getElementById('Idrta'+varpreguntas+varcompetencia+varidbloque).value;
                            // console.log('La respuesta es: '+varidrta);

                            if (varidrta == "") {
                                event.preventDefault();
                                swal.fire("!!! Advertencia !!!","Ingrese una respuesta a la pregunta: "+varidtext,"warning");
                                document.getElementById('Idrta'+varpreguntas+varcompetencia+varidbloque).style.backgroundColor = '#f7b9b9';
                                return; 
                            }else{
                                document.getElementById('Idrta'+varpreguntas+varcompetencia+varidbloque).style.backgroundColor = '#fff';

                                $.ajax({
                                    method: "get",
                                    url: "createautoeva",
                                    data: {
                                        txtvardocumento : vardocumento,
                                        txtvaridbloque : varidbloque,
                                        txtvaridcompetencia : varidcompetencia,
                                        txtvaridpreg : varidpreg,
                                        txtvaridrta : varidrta,
                                    },
                                    success : function(response){ 
                                            numRta =   JSON.parse(response);    
                                            
                                    }
                                });
                            }
                        }
                    }
                }               
                
            }
        }
        

        varidCapa.style.display = 'inline';
        varidCapaUno.style.display = 'none';

        var varocmentario = document.getElementById("Idcomentarios").value;
        $.ajax({
            method: "get",
            url: "createautodesarrollo",
            data: {
                txtvarocmentario : varocmentario,
                txtvardocumento : vardocumento,
            },
            success : function(response){
                numRta2 =   JSON.parse(response);
                // window.open('https://qa.grupokonecta.local/qa_managementv2/web/index.php/evaluaciondesarrollo/index','_self');
                window.open('https://172.20.100.50/qa/web/index.php/evaluaciondesarrollo/index','_self');
            }
        });
    };
    
    function guardaenvia(){
        var varobservafeedback = document.getElementById('Idcomentarios').value;
		var varNotafinal = '<?php echo $txtnotafinal; ?>';
        var vardocumento = '<?php echo $documento; ?>';		
		var vardocumentojefe = '<?php echo $vardocument; ?>';
        var varmodelo = 0;
        var vardocumentoblanco = 0;
        if(!varobservafeedback){
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","No hay datos a registrar en descripción del feedback.","warning");
          document.getElementById("Idcomentarios").style.border = '1px solid #ff2e2e';
          return;
      } else {

		    $.ajax({
                method: "get",
                url: "crearresultadofb",
                data : {         
                	varobservafeedback : varobservafeedback,
                	varNotafinal : varNotafinal,
                	vardocumento : vardocumento,
                    vardocumentojefe  : vardocumentojefe,
                },
                success : function(response){ 
                    var numRta =   JSON.parse(response); 
                    // console.log("a",numRta);                    
                    if (numRta == 1) {
                        jQuery(function(){
                                    swal.fire({type: "success",
                                        title: "!!! OK !!!",
                                        text: "Datos guardados correctamente y enviados."
                                    }).then(function() {
                        window.location.href='evaluacionfeedback?model='+varmodelo+'&documento='+vardocumentoblanco;
                        });
                       });
                    }else if (numRta == 2) {
                        event.preventDefault();
                            swal.fire("!!! Advertencia !!!","No se pudo guardar la información, esta persona ya tiene datos guardados","warning");
                        return;
                    } else if (numRta == 0) {
                        event.preventDefault();
                            swal.fire("!!! Advertencia !!!","No es posible realizar dicha acción.","warning");
                        return;
                    }
                }
            });
      }
	};

    function openmensaje(competencia){
       var varcompe = competencia;       
       var varidtbn1 = document.getElementById('idtbn1'+varcompe);
       var varidtbn2 = document.getElementById('idtbn2'+varcompe);
       var varidmensaje = document.getElementById('idmensaje'+varcompe);

       varidtbn1.style.display = 'none';
       varidtbn2.style.display = 'inline';
       varidmensaje.style.display = 'inline';

   };

   function closemensaje(competencia){
       var varcompe = competencia;       
       var varidtbn1 = document.getElementById('idtbn1'+varcompe);
       var varidtbn2 = document.getElementById('idtbn2'+varcompe);
       var varidmensaje = document.getElementById('idmensaje'+varcompe);

       varidtbn1.style.display = 'inline';
       varidtbn2.style.display = 'none';
       varidmensaje.style.display = 'none';
   };
   function openmensaje85(competencia){
       var varcompe = competencia;       
       var varidtbn1 = document.getElementById('idtbn1'+varcompe);
       var varidtbn2 = document.getElementById('idtbn2'+varcompe);
       var varidmensaje = document.getElementById('idmensaje'+varcompe);

       varidtbn1.style.display = 'none';
       varidtbn2.style.display = 'inline';
       varidmensaje.style.display = 'inline';

   };

   function closemensaje85(competencia){
       var varcompe = competencia;       
       var varidtbn1 = document.getElementById('idtbn1'+varcompe);
       var varidtbn2 = document.getElementById('idtbn2'+varcompe);
       var varidmensaje = document.getElementById('idmensaje'+varcompe);

       varidtbn1.style.display = 'inline';
       varidtbn2.style.display = 'none';
       varidmensaje.style.display = 'none';
   };
   function openobserva(){
              
       var varidtbn1 = document.getElementById('idmensaje1');
       var varidtbn2 = document.getElementById('idmensaje2');
       var varidmensaje = document.getElementById('idpanelobserva');

       varidtbn1.style.display = 'none';
       varidtbn2.style.display = 'inline';
       varidmensaje.style.display = 'inline';

   };

   function closeobserva(){
              
       var varidtbn1 = document.getElementById('idmensaje1');
       var varidtbn2 = document.getElementById('idmensaje2');
       var varidmensaje = document.getElementById('idpanelobserva');

       varidtbn1.style.display = 'inline';
       varidtbn2.style.display = 'none';
       varidmensaje.style.display = 'none';
   };
   function openobserva2(){
              
              var varidtbn1 = document.getElementById('idmensaje12');
              var varidtbn2 = document.getElementById('idmensaje22');
              var varidmensaje = document.getElementById('idpanelobserva2');
       
              varidtbn1.style.display = 'none';
              varidtbn2.style.display = 'inline';
              varidmensaje.style.display = 'inline';
       
          };
       
    function closeobserva2(){
                     
        var varidtbn1 = document.getElementById('idmensaje12');
        var varidtbn2 = document.getElementById('idmensaje22');
        var varidmensaje = document.getElementById('idpanelobserva2');
       
        varidtbn1.style.display = 'inline';
        varidtbn2.style.display = 'none';
        varidmensaje.style.display = 'none';
    };

</script>
