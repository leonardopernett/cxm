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

?>
<div id="capaUno" style="display: inline">   
    <div class="row">
    	<div class="col-md-6">
    		<a id="dlink" style="display:none;"></a>
    		<button  class="btn btn-info" style="background-color: #4298B4" id="btn">Exportar</button>
    	</div>
    </div>
</div>
<div id="capaDos" style="display: none">   
    <div class="row">
    	<div class="col-md-12">
    		<br>
    		<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
            <caption>Tabla datos</caption>
    			<thead>
    				<tr>
    					<th scope="col" colspan="5" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Listado de evaluación de desarrollo') ?></label></th>
    				</tr>
    				<tr>
    					<th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cédula Jefe') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nombre Jefe') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cédula Evaluado') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nombre Evaluado') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nota Final') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo de Feedback') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Estado') ?></label></th>
    				</tr>
    			</thead>
    			<tbody>
    				<?php 
                        
                        $varconteobloque = 0;
                        $varconteocompetencia = 0;
                        $varconteopregunta = 0;
                        $txtnotafinal = null;
                        $tipocoaching = '';
    					foreach ($varlistaseguimientofeedback as $key => $value2) {

                        $varNum = 1;
                        $varsumacompetencia = 0;
                        $varmenorcompetencia = 0;
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
                        $bloque1 = 0;
                        $bloque2 = 0;
                        $bloque3 = 0;        
                        $varcomentarios2 = '';
                        $can = 0;
                         //para el c�lculo de nota y tipo feedback
                         $documento = $value2['documento'];

                         //Validar si se le puede hacer feedback
                         $txtvardocumento = $documento;
                         $vardocumento1 = Yii::$app->db->createCommand("select documentoevaluado from tbl_evaluacion_solucionado where documentoevaluado = $txtvardocumento and idevaluaciontipo = 1 group by documentoevaluado")->queryScalar();
                         $vardocumento2 = Yii::$app->db->createCommand("select documentoevaluado from tbl_evaluacion_solucionado where documentoevaluado = $txtvardocumento and idevaluaciontipo = 3 group by documentoevaluado")->queryScalar();
                         
                         $vardocumentojefe = Yii::$app->db->createCommand("select documento_jefe from tbl_usuarios_evalua_feedback WHERE documento = $txtvardocumento")->queryScalar();
                         
                         $varevaluoaljefe = Yii::$app->db->createCommand("select documentoevaluado from tbl_evaluacion_solucionado where documento = $txtvardocumento AND documentoevaluado = $vardocumentojefe and idevaluaciontipo = 2 group by documentoevaluado")->queryScalar();
                     
                         if($vardocumentojefe > 1){
                           $varnopares = Yii::$app->db->createCommand("select count(documento) from tbl_evaluacion_novedadesgeneral WHERE documento = $txtvardocumento AND aprobado = 1")->queryScalar();
                           if($varnopares > 0){
                             $vardocumento3 = 0;
                           }else {
                             $vardocumento3 = Yii::$app->db->createCommand("select documentoevaluado from tbl_evaluacion_solucionado where documentoevaluado = $txtvardocumento and idevaluaciontipo = 3 group by documentoevaluado")->queryScalar();
                           } 
                         }else{
                             #code
                         }
                         $varcantipares = Yii::$app->db->createCommand("select COUNT(documento_jefe) from tbl_usuarios_evalua_feedback WHERE documento_jefe = $vardocumentojefe")->queryScalar();
                        
                         if($vardocumento1 && $vardocumento2 && $varevaluoaljefe){
                           



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

                            for ($i = 1; $i <= 4; $i++) {

                                $listacompetenciat = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                    FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                    ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque, ef.mensaje
                                    FROM tbl_evaluacion_solucionado es
                                    INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                    INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                    inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                    INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                    INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                    LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                    WHERE es.documentoevaluado = $documento AND es.idevaluaciontipo = $i 
                                    GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques")->queryAll();

                                foreach ($listacompetenciat as $key => $value) {
                                    $varsumacompetencia = $varsumacompetencia + $value['%Competencia'];


                                    if($i == 1){
                                        $varconteocompetencia = $varconteocompetencia + 1;

                                        if($value['idevaluacionbloques'] == 1){ 

                                        $evaluacompe1 = $evaluacompe1 + ($value['%Competencia'] * 40) / 100;
                                        $bloque1 = $bloque1 + 1;
                                        }else{
                                            #code
                                        }
                                        if($value['idevaluacionbloques'] == 2){

                                        $evaluaorgan1 = $evaluaorgan1 + ($value['%Competencia'] * 20) / 100;                        
                                        $bloque2 = $bloque2 + 1;
                                        }else{
                                            #code
                                        }
                                        if($value['idevaluacionbloques'] == 3){

                                        $evaluadese1 = $evaluadese1 + ($value['%Competencia'] * 40) / 100;
                                        $bloque3 = $bloque3 + 1;
                                        }else{
                                            #code
                                        }                 
                                    }else{
                                        #code
                                    }
                                    if($i == 3){
                                        if($value['idevaluacionbloques'] == 1){
                                        $evaluacompe2 = $evaluacompe2 + ($value['%Competencia'] * 40) / 100;
                                        }else{
                                            #code
                                        }
                                        if($value['idevaluacionbloques'] == 2){
                                        $evaluaorgan2 = $evaluaorgan2 + ($value['%Competencia'] * 20) / 100;
                                        }else{
                                            #code
                                        }
                                        if($value['idevaluacionbloques'] == 3){
                                        $evaluadese2 = $evaluadese2 + ($value['%Competencia'] * 40) / 100;
                                        }else{
                                            #code
                                        }                 
                                    }else{
                                        #code
                                    }
                                    if($i == 4){
                                        if($value['idevaluacionbloques'] == 1){
                                        $evaluacompe3 = $evaluacompe3 + ($value['%Competencia'] * 40) / 100;
                                        }else{
                                            #code
                                        }
                                        if($value['idevaluacionbloques'] == 2){
                                        $evaluaorgan3 = $evaluaorgan3 + ($value['%Competencia'] * 20) / 100;
                                        }else{
                                            #code
                                        }
                                        if($value['idevaluacionbloques'] == 3){
                                        $evaluadese3 = $evaluadese3 + ($value['%Competencia'] * 40) / 100;
                                        }else{
                                            #code
                                        }                 
                                    }else{
                                        #code
                                    }
                                    if($i == 2){
                                        if($value['idevaluacionbloques'] == 1){
                                        $evaluacompe4 = $evaluacompe4 + ($value['%Competencia'] * 40) / 100;
                                        }else{
                                            #code
                                        }
                                        if($value['idevaluacionbloques'] == 2){
                                        $evaluaorgan4 = $evaluaorgan4 + ($value['%Competencia'] * 20) / 100;
                                        }else{
                                            #code
                                        }
                                        if($value['idevaluacionbloques'] == 3){
                                        $evaluadese4 = $evaluadese4 + ($value['%Competencia'] * 40) / 100;
                                        } else{
                                            #code
                                        }                
                                    }else{
                                        #code
                                    }
                                }            
                            }
                            if ($bloque1 > 0 && $bloque2 > 0 && $bloque3 > 0){
                                if($evaluacompe1 > 0){

                                $evaluacompe1 = $evaluacompe1 / $bloque1;
                                $evaluaorgan1 = $evaluaorgan1 / $bloque2; 
                                $evaluadese1 = $evaluadese1 / $bloque3;
                                }else{
                                    #code
                                }
                                if($evaluacompe2 > 0){
                                $evaluacompe2 = $evaluacompe2 / $bloque1;
                                $evaluaorgan2 = $evaluaorgan2 / $bloque2;
                                $evaluadese2 = $evaluadese2 / $bloque3;
                                }else{
                                    #code
                                }
                                if($evaluacompe3 > 0){
                                $evaluacompe3 = $evaluacompe3 / $bloque1;
                                $evaluaorgan3 = $evaluaorgan3 / $bloque2;
                                $evaluadese3 = $evaluadese3 / $bloque3;
                                }else{
                                    #code
                                }
                                if($evaluacompe4 > 0){
                                $evaluacompe4 = $evaluacompe4 / $bloque1;
                                $evaluaorgan4 = $evaluaorgan4 / $bloque2;
                                $evaluadese4 = $evaluadese4 / $bloque3;
                                }else{
                                    #code
                                }
                            }else{
                                #code
                            }

                            $txtevalua1 = $evaluacompe1 + $evaluaorgan1 + $evaluadese1;
                            $txtevalua2 = $evaluacompe2 + $evaluaorgan2 + $evaluadese2;
                            $txtevalua3 = $evaluacompe3 + $evaluaorgan3 + $evaluadese3;
                            $txtevalua4 = $evaluacompe4 + $evaluaorgan4 + $evaluadese4;
                           
                            $txtnotafinal = null;
                            if($txtevalua1 != 0 && $txtevalua2 != 0 && $txtevalua3 == 0 && $txtevalua4 == 0) {
                            $txtnotafinal = (($txtevalua1 * 20)/100) + (($txtevalua2 * 80) /100);
                            }else{
                                #code
                            }
                            if($txtevalua1 != 0 && $txtevalua2 != 0 && $txtevalua3 == 0 && $txtevalua4 != 0) {
                            $txtnotafinal = (($txtevalua1 * 15)/100) + (($txtevalua2 * 70) /100) + (($txtevalua4 * 15) /100);             
                            }else{
                                #code
                            }
                            if($txtevalua1 != 0 && $txtevalua2 != 0 && $txtevalua3 != 0 && $txtevalua4 == 0) {
                            $txtnotafinal = (($txtevalua1 * 10)/100) + (($txtevalua2 * 60) /100) + (($txtevalua3 * 30) /100);            
                            }else{
                                #code
                            }
                            if($txtevalua1 != 0 && $txtevalua2 != 0 && $txtevalua3 != 0 && $txtevalua4 != 0) {
                            $txtnotafinal = (($txtevalua1 * 5)/100) + (($txtevalua2 * 60) /100) + (($txtevalua3 * 5) /100) + (($txtevalua4 * 30) /100);

                            }else{
                                #code
                            }

                            $txtProcentaje = $txtnotafinal;
                            if($txtProcentaje >= 85 && $varmenorcompetencia == 0) {
                            $tipocoaching = 'Opcional';
                            } else if($txtProcentaje >= 85 && $varmenorcompetencia == 1){
                            $tipocoaching = 'Obligatorio';
                            } else if($txtProcentaje <= 85 && $varmenorcompetencia == 1) {
                            $tipocoaching = 'Obligatorio';
                            }else{
                                #code
                            }
                            $txtnotafinal = number_format($txtnotafinal,2);
                        } else {
                            $txtnotafinal = 'NA';
                            $tipocoaching = 'NA';
                        }

                         //fin calculo

                    ?>

                            

    					<tr>
	      					<td><label style="font-size: 12px;"><?php echo  $value2['documento_jefe']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value2['nombre_jefe']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value2['documento']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value2['nombre_completo']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $txtnotafinal; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $tipocoaching; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value2['Estado']; ?></label></td>
	      				</tr>
    				<?php 
    					}
    				?>
    			</tbody>
    		</table>
    	</div>
    </div>
</div>

<script type="text/javascript" charset="UTF-8">
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
            document.getElementById("dlink").download = "Resultado Seguimiento Feedback";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblData', 'Listado seguimiento feedback', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>