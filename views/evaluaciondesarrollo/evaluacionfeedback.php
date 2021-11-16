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
$documento = $documento;
$titulos = array();
    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $vardocument = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
    $varnombre = Yii::$app->db->createCommand("select nombre_completo from  tbl_usuarios_evalua_feedback where documento in ('$vardocument')")->queryScalar();
    $varcargo = Yii::$app->db->createCommand("select distinct concat(posicion,' - ',funcion) from  tbl_usuarios_evalua_feedback where documento in ('$vardocument')")->queryScalar();
    $vartipoeva = Yii::$app->db->createCommand("select tipoevaluacion from tbl_evaluacion_tipoeval where idevaluaciontipo = 1 and anulado = 0")->queryScalar();
    $vardocumentosijefe = Yii::$app->db->createCommand("select ue.documento_jefe from tbl_usuarios_evalua_feedback ue where ue.documento_jefe = '$vardocument' group by ue.documento_jefe")->queryScalar();

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

    $query3 = Yii::$app->db->createCommand("select * from tbl_usuarios_evalua_feedback where anulado = 0 and documento_jefe = $vardocument" )->queryAll();
    $listData3 = ArrayHelper::map($query3, 'documento', 'nombre_completo');

    $varlistbloques = Yii::$app->db->createCommand("select * from tbl_evaluacion_bloques where anulado = 0")->queryAll();

    $varconteobloque = 0;
    $varconteocompetencia = 0;
    $varconteopregunta = 0;
    $txtnotafinal = null;
    $tipocoaching = '';

    $varresultado =0;
$txtNotasFinal = 0;

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
           height: 250px;
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
           height: 210px;
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
<?php
// Este proceso es una condicional utilizado para ver el modulo quienes poseen ese id if($sessiones != "3205" || $sessiones != "2953"){ 

?>


<div id="idCapa" style="display: none">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-spinner" style="font-size: 20px; color: #2CA5FF;"></em> Procesando datos</label>
                <div class="col-md-12">
                    <table>
                    <caption>Tabla datos</caption>
                        <tr>
                            <th id="loader" class="text-center"><div class="loader"> </div></td>
                            <th id="guardarDatos" class="text-center"><label><?= Yii::t('app', ' Guardando datos de la evaluaci�n realizada') ?></label></td>
                        </tr>
                    </table>                                       
                </div>
            </div>
        </div>
    </div>
</div>
<br>

<?php
if($vardocumentosijefe){ ?>
<div id="idCapaUno" style="display: inline"> 
    <div id="capaUno" style="display: inline">   
        <div class="row">
            <div class="col-md-4">
                <div class="card1 mb">
                    <label><em class="fas fa-user-circle" style="font-size: 20px; color: #2CA5FF;"></em> Usuario:</label>
                    <label style="font-size: 20px;"><?php echo $varnombre; ?></label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card1 mb">
                    <label><em class="fas fa-question-circle" style="font-size: 20px; color: #2CA5FF;"></em> Cargo:</label>
                    <label style="font-size: 20px;"><?php echo $varcargo; ?></label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card1 mb">
                    <label><em class="fas fa-info-circle" style="font-size: 20px; color: #2CA5FF;"></em> Gestión:</label>
                    <label style="font-size: 20px;">Reporte de Feedback</label>
                </div>
            </div>            
        </div> 
    </div>
    <hr>
    <div id="capaDos" style="display: inline">
        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>   
            <div class="row">
                <div class="col-md-6">
                    <div class="card3 mb">
                        <label><em class="fas fa-exclamation" style="font-size: 20px; color: #827DF9;"></em> Notificaciones:</label>
                            <div class="col-md-12">
                               <?php 
                                if ($documento != 0) {?>

                                <div class="panel panel-default">
                                    <div class="panel-body " style="background-color: #f0f8ff; text-align: center; font-size: 15px;">Para ti que eres Gente Ko <br>
                                   Llegó el momento de conversar con feeling y generar un plan de desarrollo, enfocado a potenciar personal y profesionalmente a tu equipo. <br>
                                   Adicionalmente, te entregamos algunas acciones sugeridas para construirlo <br>
                                   En Konecta somos Gente Buena - Buena Gente 
                                    </div>
                                </div>
        <?php 
                                } else {?>
        <div class="panel panel-default">
                                    <div class="panel-body " style="background-color: #f0f8ff;">El feedback debe ser documentado por el jefe actual de la persona
                                    </div>
                                </div>
        <?php 
                                }?>
                            </div>  

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card3 mb">
                        <label><em class="fas fa-search" style="font-size: 20px; color: #827DF9;"></em> Busqueda:</label>
                        
                            <div class="col-md-12">
                                <label for="txtFechar" style="font-size: 18px;">Persona a cargo:</label>                                    
                                <?php  echo $form->field($model, 'documento')->dropDownList($listData3, ['prompt' => 'Seleccionar evaluado', 'id'=>'idlistaevaluado', 'onchange'=>'carguedato();'])?>  
                            </div> <br>

                    </div>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>    
    <hr> 

    <?php 
    if ($documento != 0) { 
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
        $varcomentarios2 = '';
        $can = 0; 
        $varnombrec = Yii::$app->db->createCommand("select nombre_completo from tbl_usuarios_evalua_feedback where documento = $documento")->queryScalar();
        $varrol = Yii::$app->db->createCommand("select distinct concat(posicion,' - ',funcion) from  tbl_usuarios_evalua_feedback where documento in ('$documento')")->queryScalar();
        $varnivel = Yii::$app->db->createCommand("select en.nivel FROM tbl_evaluacion_solucionado es inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia 
                                INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel WHERE es.documentoevaluado = $documento GROUP BY es.idevaluacioncompetencia LIMIT 1")->queryScalar();
        
        $varcomentarios = Yii::$app->db->createCommand("select es.comentarios FROM tbl_evaluacion_solucionado es WHERE es.documentoevaluado = $documento AND  es.comentarios != ''")->queryAll();
        foreach ($varcomentarios as $key => $value) {
            $can = $can + 1;
            $varcomentarios2 = $varcomentarios2.' '.$can.'-. '.$value['comentarios'];
            
        }
//para c�lculo de tipo de coaching
        $listacompetenciat2 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                    FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                    ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque, ef.mensaje
                                                    FROM tbl_evaluacion_solucionado es
                                                    INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                    INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                    inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                    INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                    INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                    LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                    WHERE es.documentoevaluado = $documento and ec.idevaluacionbloques = 1 
                                                    GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques")->queryAll();

        foreach ($listacompetenciat2 as $key => $value) {
            if($value['%Competencia'] < 85){
                $varmenorcompetencia = 1;
            }
        }


        $varArraySumaB = array();

        foreach ($varlistbloques as $key => $value) {
            $varidbloque = $value['idevaluacionbloques'];  
            $varconteobloque = $varconteobloque + 1;

            $totalcomp = 0;
                              $varArrayPromedio = array();
                              
                            //se calcula los % de las competencias mediante los pesos que est�n en los requerimientos
                                $valortotal1Auto = 0;
                                $valortotal2Jefe = 0;
                                $valortotal3Cargo = 0;
                                $valortotal4Pares = 0;
                                $listadocompetencias = Yii::$app->db->createCommand("select ec.namecompetencia, eb.idevaluacionbloques, eb.namebloque, es.idevaluacioncompetencia
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = $documento AND es.idevaluaciontipo = 1 AND eb.idevaluacionbloques = $varidbloque
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();

                                foreach ($listadocompetencias as $key => $value) {


                                        
                                    $nombrecompetencias = $value['namecompetencia'];
                                    $varidevaluacionbloques = $value['idevaluacionbloques'];
                                    $varidcompetencia = $value['idevaluacioncompetencia'];                                        
                                    $varconteocompetencia = $varconteocompetencia + 1;
                                    $varcolor2 = null;




                                   
                                    $listacompetencia1 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                        FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque,et.tipoevaluacion ,ef.mensaje, count(es.idevaluacioncompetencia) canti
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = $documento AND es.idevaluaciontipo = 1 AND ec.namecompetencia = '$nombrecompetencias'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();
                                    
                                    foreach ($listacompetencia1 as $key => $value1) {
                                       
                                        $valortotal1Auto = $value1['%Competencia'];
                                        $varmensaje = $value1['mensaje'];
                                    }



                                    $listacompetencia2 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                        FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque,et.tipoevaluacion ,ef.mensaje, count(es.idevaluacioncompetencia) canti
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = $documento AND es.idevaluaciontipo = 3 AND ec.namecompetencia = '$nombrecompetencias'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();
                                    
                                    foreach ($listacompetencia2 as $key => $value2) {
                                        $valortotal2Jefe = $value2['%Competencia'];
                                    }    
                                    $listacompetencia3 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                        FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque,et.tipoevaluacion ,ef.mensaje, count(es.idevaluacioncompetencia) canti
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = $documento AND es.idevaluaciontipo = 2 AND ec.namecompetencia = '$nombrecompetencias'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();
                                    
                                    foreach ($listacompetencia3 as $key => $value3) {
                                        $valortotal3Cargo = $value3['%Competencia'];
                                    }

                                    $listacompetencia4 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                        FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque,et.tipoevaluacion ,ef.mensaje, count(es.idevaluacioncompetencia) canti
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = $documento AND es.idevaluaciontipo = 4 AND ec.namecompetencia = '$nombrecompetencias'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();
                                   
                                    foreach ($listacompetencia4 as $key => $value4) {
                                        $valortotal4Pares = $value4['%Competencia'];
                                    }


                                    $txtnotafinal1 = null;
                                    if($valortotal1Auto != 0 && $valortotal2Jefe != 0 && $valortotal4Pares == 0 && $valortotal3Cargo == 0) {
                                        $txtnotafinal1 = number_format((($valortotal1Auto * 20)/100) + (($valortotal2Jefe * 80) /100),2);
                                        
                                    }
                                    if($valortotal1Auto != 0 && $valortotal2Jefe != 0 && $valortotal4Pares != 0 && $valortotal3Cargo == 0) {
                                        $txtnotafinal1 = number_format((($valortotal1Auto * 15)/100) + (($valortotal2Jefe * 70) /100) + (($valortotal4Pares * 15) /100),2);  
                                                      
                                    }
                                    if($valortotal1Auto != 0 && $valortotal2Jefe != 0 && $valortotal4Pares == 0 && $valortotal3Cargo != 0) {
                                        $txtnotafinal1 = number_format((($valortotal1Auto * 10)/100) + (($valortotal2Jefe * 60) /100) + (($valortotal3Cargo * 30) /100),2); 
                                                  
                                    }
                                    if($valortotal1Auto != 0 && $valortotal2Jefe != 0 && $valortotal4Pares != 0 && $valortotal3Cargo != 0) {
                                        $txtnotafinal1 = number_format((($valortotal1Auto * 5)/100) + (($valortotal2Jefe * 60) /100) + (($valortotal3Cargo * 5) /100) + (($valortotal4Pares * 30) /100),2);    
                                                             
                                    }


                                    $totalcomp = $totalcomp + 1;
                                    array_push($varArrayPromedio, $txtnotafinal1);
                                }         
        if ($totalcomp != 0) {    
  $varPromedios = round(array_sum($varArrayPromedio) / $totalcomp,2);
  }else{
  $varPromedios = 0;
  }


                                    if ($varidbloque == 1) {
                                      $vartotalb = round(($varPromedios * (40 / 100)),2);
                                    }else{
                                      if ($varidbloque == 2) {
                                        $vartotalb = round(($varPromedios * (20 / 100)),2);
                                      }else{
                                        if ($varidbloque == 3) {
                                          $vartotalb = round(($varPromedios * (40 / 100)),2);
                                        }    
                                      }
                                    }              


                                    array_push($varArraySumaB, $vartotalb);            
        }

        
        
        
        $txtProcentaje =  round(array_sum($varArraySumaB),2);
        if($txtProcentaje >= 85 && $varmenorcompetencia == 0) {
            $tipocoaching = 'Opcional';
        } else if($txtProcentaje >= 85 && $varmenorcompetencia == 1){
                $tipocoaching = 'Obligatorio';
            } else if($txtProcentaje <= 85 && $varmenorcompetencia == 1) {
                $tipocoaching = 'Obligatorio';
                }
        $txtNotasFinal = round($txtProcentaje,2);
        array_push($titulos, $txtProcentaje);
    ?>    <div id="CapaTres" style="display: inline">
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label><em class="far fa-file-alt" style="font-size: 18px; color: #C148D0;"></em> Resultados: </label>                        
                        <div class="row"> 
                            <div class="col-md-4">
                                <div class="card2 mb">
                                    <label style="font-size: 17px;"><em class="far fa-id-badge" style="font-size: 20px; color: #C148D0;"></em> Nombre: </label>
                                    <label style="font-size: 20px;">&nbsp;&nbsp;&nbsp; <?php echo $varnombrec; ?> </label><br>
                                    <label style="font-size: 17px;"><em class="fas fa-male" style="font-size: 20px; color: #C148D0;"></em> Rol: </label>
                                    <label style="font-size: 20px;">&nbsp;&nbsp;&nbsp; <?php echo $varrol; ?> </label>                                                                         
                                </div>
                            </div>   
                            <div class="col-md-4">
                                <div class="card2 mb">
                                    <label style="font-size: 17px;"><em class="fas fa-bars" style="font-size: 18px; color: #C148D0;"></em> Calificación Final </label>
                                    <table style="width:100%">
                                    <caption>...</caption>
                                        <th scope="col" class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="<?php echo $prueba; ?>"></canvas></div><span style="font-size: 15px;"><?php echo round($txtProcentaje,2).' %'; ?></span></td> 
                                    </table> 
                                </div>
                            </div>                            
                            <div class="col-md-4">
                                <div class="card2 mb">
                                    <label style="font-size: 17px;" ><em class="far fa-comment-alt" style="font-size: 18px; color: #C148D0;"></em> Observaciones </label>
                                    <textarea type="text" class="form-control" readonly="readonly" id="txtobserva" rows="3" value="<?php echo $varcomentarios2; ?>" data-toggle="tooltip" title="Observaciones"><?php echo $varcomentarios2; ?></textarea>
                                                                                                   
                                    <label style="font-size: 17px;"><em class="fas fa-check" style="font-size: 18px; color: #C148D0;"></em> Tipo Coaching </label>                            
                                    <label style="font-size: 20px; ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $tipocoaching; ?> </label>
                                </div>                            
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
    </div> 
    <?php
        $varArraySumaB = array();

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
                        <label><em class="fas fa-cubes" style="font-size: 20px; <?php echo $varcolor; ?>"></em> <?php echo $varresulta.$value['namebloque'].': '; ?></label>
                        
                        <div class="row">                    
                            <?php 

                              $totalcomp = 0;
                              $varArrayPromedio = array();
                              
                            //se calcula los % de las competencias mediante los pesos que est�n en los requerimientos
                                $valortotal1Auto = 0;
                                $valortotal2Jefe = 0;
                                $valortotal3Cargo = 0;
                                $valortotal4Pares = 0;
                                $listadocompetencias = Yii::$app->db->createCommand("select ec.namecompetencia, eb.idevaluacionbloques, eb.namebloque, es.idevaluacioncompetencia
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = $documento AND es.idevaluaciontipo = 1 AND eb.idevaluacionbloques = $varidbloque
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();

                                foreach ($listadocompetencias as $key => $value) {
                                        
                                    $nombrecompetencias = $value['namecompetencia'];
                                    $varidevaluacionbloques = $value['idevaluacionbloques'];
                                    $varidcompetencia = $value['idevaluacioncompetencia'];                                        
                                    $varconteocompetencia = $varconteocompetencia + 1;
                                    $varcolor2 = null;




                                   
                                    $listacompetencia1 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                        FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque,et.tipoevaluacion ,ef.mensaje, count(es.idevaluacioncompetencia) canti
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = $documento AND es.idevaluaciontipo = 1 AND ec.namecompetencia = '$nombrecompetencias'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();
                                    
                                    foreach ($listacompetencia1 as $key => $value1) {
                                       
                                        $valortotal1Auto = $value1['%Competencia'];
                                        $varmensaje = $value1['mensaje'];
                                    }

                                    $listacompetencia2 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                        FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque,et.tipoevaluacion ,ef.mensaje, count(es.idevaluacioncompetencia) canti
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = $documento AND es.idevaluaciontipo = 3 AND ec.namecompetencia = '$nombrecompetencias'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();
                                    
                                    foreach ($listacompetencia2 as $key => $value2) {
                                        $valortotal2Jefe = $value2['%Competencia'];
                                    }    
                                    $listacompetencia3 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                        FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque,et.tipoevaluacion ,ef.mensaje, count(es.idevaluacioncompetencia) canti
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = $documento AND es.idevaluaciontipo = 2 AND ec.namecompetencia = '$nombrecompetencias'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();
                                    
                                    foreach ($listacompetencia3 as $key => $value3) {
                                        $valortotal3Cargo = $value3['%Competencia'];
                                    }

                                    $listacompetencia4 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                        FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque,et.tipoevaluacion ,ef.mensaje, count(es.idevaluacioncompetencia) canti
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = $documento AND es.idevaluaciontipo = 4 AND ec.namecompetencia = '$nombrecompetencias'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();
                                   
                                    foreach ($listacompetencia4 as $key => $value4) {
                                        $valortotal4Pares = $value4['%Competencia'];
                                    }


                                    $txtnotafinal1 = null;
                                    if($valortotal1Auto != 0 && $valortotal2Jefe != 0 && $valortotal4Pares == 0 && $valortotal3Cargo == 0) {
                                        $txtnotafinal1 = number_format((($valortotal1Auto * 20)/100) + (($valortotal2Jefe * 80) /100),2);

                                    }
                                    if($valortotal1Auto != 0 && $valortotal2Jefe != 0 && $valortotal4Pares != 0 && $valortotal3Cargo == 0) {
                                        $txtnotafinal1 = number_format((($valortotal1Auto * 15)/100) + (($valortotal2Jefe * 70) /100) + (($valortotal4Pares * 15) /100),2);  
         
                                    }
                                    if($valortotal1Auto != 0 && $valortotal2Jefe != 0 && $valortotal4Pares == 0 && $valortotal3Cargo != 0) {
                                        $txtnotafinal1 = number_format((($valortotal1Auto * 10)/100) + (($valortotal2Jefe * 60) /100) + (($valortotal3Cargo * 30) /100),2); 
          
                                    }
                                    if($valortotal1Auto != 0 && $valortotal2Jefe != 0 && $valortotal4Pares != 0 && $valortotal3Cargo != 0) {
                                        $txtnotafinal1 = number_format((($valortotal1Auto * 5)/100) + (($valortotal2Jefe * 60) /100) + (($valortotal4Pares * 5) /100) + (($valortotal3Cargo * 30) /100),2);                            
                                    }

                                        if($txtnotafinal1 < 85){
                                            $varcolor2 = "color: #f5500f;";
                                        }else{
                                            $varcolor2 = "color: #0d0d0d;";
                                        }
                            ?>
                                    
                                    <div class="col-md-6">
                                        <div class="card1 mb">  
                                            <div class="row">                                    
                                                <div class="col-md-6">
                                                    <label style="font-size: 16px;"><em class="fas fa-bookmark" style="font-size: 15px; <?php echo $varcolor; ?>"></em> <?php echo $nombrecompetencias.'.'; ?></label>   
                                                </div>
                                                <div class="col-md-6">
                                                   <label id="<?php echo 'idtext'.$varconteocompetencia.$varconteobloque; ?>" style="font-size: 15px; <?php echo $varcolor2; ?>"> <?php echo $txtnotafinal1.'% '; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                                                   
                                                   <?php if($txtnotafinal1 < 85 && $varidevaluacionbloques == 1){?>
                                                        <div onclick="openmensaje(<?php echo $varconteocompetencia.$varconteobloque; ?>);" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="<?php echo 'idtbn1'.$varconteocompetencia.$varconteobloque; ?>" >
                                                            <span class="fas fa-caret-down" style="font-size: 30px; color: #f7b9b9;" ></span>
                                                        </div>
                                                        <div onclick="closemensaje(<?php echo $varconteocompetencia.$varconteobloque; ?>);" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="<?php echo 'idtbn2'.$varconteocompetencia.$varconteobloque; ?>" >
                                                             <span class="fas fa-caret-up" style="font-size: 30px; color: #f7b9b9;" ></span>
                                                        </div>
                                                   <?php } ?>
                                                </div>
                                                <div class="col-md-12">
                                                    <div id="<?php echo 'idmensaje'.$varconteocompetencia.$varconteobloque; ?>" style="display: none">
                                                        <div class="panel panel-default">
                                                            <div class="panel-body" style="background-color: #f0f8ff; font-size: 16px;"><?php echo $varmensaje; ?>
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
        <?php 
          }

        ?>
    
        <div id="capaSiete" style="display: none"> 
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <div class="col-md-12">
                            <label style="font-size: 17px;"><em class="fas fa-exclamation-circle" style="font-size: 17px; color: #8B70FA;"></em> Información Importante 70-20-10 </label>
                            
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
                            <label style="font-size: 17px;"><em class="fas fa-comments" style="font-size: 17px; color: #8B70FA;"></em> Descripción del feedback </label>
                            
                            
                        </div>
                        <div class="col-md-12">
                            <?= $form->field($model, 'comentarios')->textarea(['maxlength' => 500, 'placeholder'=>'Escribe aquí las acciones sugeridas que ayudarán a la persona a fortalecer sus competencias', 'id'=>'Idcomentarios']) ?>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    <hr>
     
    <div id="capaSiete" style="display: inline"> 
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 17px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><em class="fas fa-save" style="font-size: 17px; color: #FFC72C;"></em> Guardar feedback: </label> 
                                <div onclick="guardaenvia();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                                  Guardar y Enviar
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" style="display: none">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><em class="fas fa-minus-circle" style="font-size: 17px; color: #FFC72C;"></em> Biblioteca de Conocimiento: </label> 
                                                            
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
<?php } ?>

    <div class="CapaSeis" style="display: inline;">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #3339fb;"></em> Información General</label>
                    <br>
                    <label style="font-size: 15px;">Si tienes alguna novedad o dificultad que requiera otro tipo de gestión, escribe tu caso al correo maria.vera@grupokonecta.com</label>                        
                </div>
            </div>
        </div>
    </div>
   
    <hr>
<?php } else { ?>
  <div class="Seis">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
              <label><em class="fas fa-info-circle" style="font-size: 20px; color: #1e8da7;"></em> Información:</label>
              <label style="font-size: 14px;">No tiene personal a cargo para realizar este proceso</label>
                </div><br>
            </div>
        </div>  
    </div>
  </div><br>

  <?php } ?>

<?php 
// Procesos con el condicional desde el principio } else { 
if($sessiones != "3205" || $sessiones != "2953"){ 
?>
  <div class="Nueve">
    <div class="row">
        
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

                window.open('../evaluaciondesarrollo/index','_self');
            }
        });
    };
    
    function guardaenvia(){
        var varobservafeedback = document.getElementById('Idcomentarios').value;
      var varNotafinal = '<?php echo $txtNotasFinal; ?>';
  // var varDataNum = [<?= join($titulos, ',')?>];
      // var varNotafinal = varDataNum[0];

        var vardocumento = '<?php echo $documento; ?>';   
    var vardocumentojefe = '<?php echo $vardocument; ?>';
  var vartipocoaching = '<?php echo $tipocoaching; ?>';
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
      vartipocoaching  : vartipocoaching,
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
