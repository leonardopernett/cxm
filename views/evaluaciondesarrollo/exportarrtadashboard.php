<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;

$this->title = 'Extractar resultados - Evaluacion de desarrollo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $varlistbloques = Yii::$app->db->createCommand("select * from tbl_evaluacion_bloques where anulado = 0")->queryAll();
    $can = 0;
    $varcomentarios2 = null;
    $varcomentariosfeedback = null;
    $vartextos = null;
    if ($vardocumento != null) {
        $countCC = Yii::$app->db->createCommand("SELECT COUNT(er.documento) FROM tbl_evaluacion_resulta_feedback er WHERE er.anulado = 0 AND  er.documento IN ($vardocumento)")->queryScalar();
        if ($countCC == "1") {
            $vartextos = "Resultados del feedback ya estan guardados en CXM";
        }else{
            $vartextos = "Resultados del feedback no estan guardados en CXM";
        }

        $varcomentarios = Yii::$app->db->createCommand("select es.comentarios FROM tbl_evaluacion_solucionado es WHERE es.documentoevaluado = $vardocumento AND  es.comentarios != ''")->queryAll();
        foreach ($varcomentarios as $key => $value) {
            $can = $can + 1;
            $varcomentarios2 = $varcomentarios2.' '.$can.'-. '.$value['comentarios'];            
        }

        $varcomentariosfeedback = Yii::$app->db->createCommand("SELECT er.observacion_feedback FROM tbl_evaluacion_resulta_feedback er WHERE er.anulado = 0 AND  er.documento IN ($vardocumento)")->queryScalar();
    }else{
        $vartextos = "--";
    }

$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='popover']").popover(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);
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
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }

    .card3 {
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
    .card4 {
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

</style>
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
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
<?php if ($sessiones = "6777" || $sessiones == "3205" || $sessiones == "3468" || $sessiones == "3229" || $sessiones == "2953" || $sessiones == "852" || $sessiones == "1483"|| $sessiones == "4201"|| $sessiones == "258"|| $sessiones == "4465" || $sessiones == "6080" || $sessiones == "57" || $sessiones == "69") { ?>

<div class="capaPP" style="display: inline;">
    <div class="row">
        
        <div class="col-md-3">
            <?php $form = ActiveForm::begin([
                'options' => ["id" => "buscarMasivos"],
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'inputOptions' => ['autocomplete' => 'off']
                  ]
                ]); ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 15px; color: #C178G9;"></em> Buscar Resultado Por Usuario: </label>
                        <?= $form->field($model, 'idevaluador')->textInput(['maxlength' => 300, 'id'=>'txtidusuario', 'placeholder'=>'Documento de Identidad'])?>
                        <br>
                        <?= Html::submitButton(Yii::t('app', 'Buscar'),
                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Numero de Documento',
                                    'onclick' => 'busca();']) 
                        ?>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #559FFF;"></em> Descargar Informaci&oacute;n Opci&oacute;n 1: </label>
                        <?= Html::button('Descargar', ['value' => url::to('enviararchivouno'), 'class' => 'btn btn-success', 'id'=>'modalButton5',
                                'data-toggle' => 'tooltip',
                                'title' => 'Desargar', 'style' => 'background-color: #337ab7']) 
                            ?>  
                            <?php
                                Modal::begin([
                                    'header' => '<h4>__</h4>',
                                    'id' => 'modal5',
                                    ]);

                                    echo "<div id='modalContent5'></div>";
                                                                                
                                Modal::end(); 
                            ?>
                    </div>
                </div>
            </div>

            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #559FFF;"></em> Descargar Informaci&oacute;n Opci&oacute;n 2: </label>
                        <?= Html::button('Descargar', ['value' => url::to('enviararchivodos'), 'class' => 'btn btn-success', 'id'=>'modalButton6',
                                'data-toggle' => 'tooltip',
                                'title' => 'Desargar', 'style' => 'background-color: #337ab7']) 
                            ?>  
                            <?php
                                Modal::begin([
                                    'header' => '<h4>..</h4>',
                                    'id' => 'modal6',
                                    ]);

                                    echo "<div id='modalContent6'></div>";
                                                                                
                                Modal::end(); 
                            ?>
                    </div>
                </div>
            </div>
            <?php if ($vardocumento != null) { ?>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-table" style="font-size: 15px; color: #fd14f6;"></em> Descargar Datos de la Tabla: </label>
                        <a id="dlink" style="display:none;"></a>
                        <button  class="btn btn-info" style="background-color: #4298B4" id="btn">Descargar</button>
                        <br>
                        <label style="font-size: 15px;"> "<?php echo $vartextos; ?>" </label>
                        
                    </div>
                </div>
            </div>
        <?php } ?>

            <?php  ?>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #981F40;"></em> Descargar General opci&oacute;n 1: </label>
                        <?= Html::button('Descargar', ['value' => url::to('enviargeneral'), 'class' => 'btn btn-danger', 'id'=>'modalButton1',
                                'data-toggle' => 'tooltip',
                                'title' => 'Desargar']) 
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
	    
	    <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #981F40;"></em> Descargar General opci&oacute;n 2: </label>
                        <?= Html::button('Descargar', ['value' => url::to('enviargeneraldos'), 'class' => 'btn btn-danger', 'id'=>'modalButton2',
                                'data-toggle' => 'tooltip',
                                'title' => 'Desargar']) 
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

        <br>

            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 15px; color: #5cb85c;"></em> Subir Documentos: </label>
                        <?= Html::a('Importar',  ['importardocumentos'], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'title' => 'Importar Documentos', 'style' => 'background-color: #5cb85c']) 
                        ?>
                    </div>
                </div>
            </div>

            <?php  ?>

            <?php $form->end() ?> 
        </div>

        <div class="col-md-9">
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">                        
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #fd7e14;"></em> Lista de Resultados: </label>

                        <?php if ($varlistrtadesarrollo != null) {  ?>

                            <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                            <caption>Tabla datos</caption>
                            
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center" colspan="5" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?php echo 'Usuario: '.$varnombrec.' -  Rol: '.$varrol; ?></label></th>
                                    </tr>
                                    <tr>
                                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?php echo 'Auto Evaluaci&oacute;n'; ?></label></th>
                                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?php echo 'Evaluaci&oacute;n Jefe'; ?></label></th>
                                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?php echo 'Evaluaci&oacute;n Pares'; ?></label></th>
                                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?php echo 'Evaluaci&oacute;n A Cargo'; ?></label></th>
                                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?php echo 'Nota Final'; ?></label></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>                                        
                                        <td><label style="font-size: 13px;"><?php echo $varrtaA; ?></label></td>
                                        <td><label style="font-size: 13px;"><?php echo $varrtaJ; ?></label></td>
                                        <td><label style="font-size: 13px;"><?php echo $varrtaP; ?></label></td>
                                        <td><label style="font-size: 13px;"><?php echo $varrtaC; ?></label></td>
                                        <td><label style="font-size: 13px;"><?php echo $txtProcentaje.'%'; ?></label></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center" colspan="5"  style="background-color: #C6C6C6;"></td>
                                    </tr>
                                    <?php 
                                        $varconteobloque = null;
                                        $varArraySumaB = array();
                                        foreach ($varlistbloques as $key => $value) {
                                            $varidbloque = $value['idevaluacionbloques'];  
                                            $varconteobloque = $varconteobloque + 1;

                                            $varcolor = null;
                                            if ($varidbloque == 1) {
                                                $varcolor = "background-color: #F9BD4C;";
                                            }else{
                                                if ($varidbloque == 2) {
                                                    $varcolor = "background-color: #22D7CF;";
                                                }else{
                                                    if ($varidbloque == 3) {
                                                        $varcolor = "background-color: #49de70;";
                                                    }
                                                }
                                            }
                                    ?>
                                        <tr>
                                            <td class="text-center" colspan="5" style="<?php echo $varcolor; ?>"><label style="font-size: 15px; color: white;"><?php echo 'Bloque '.$value['namebloque']; ?></label></td>
                                        </tr>
                                        <?php 
                                            $totalcomp = 0;
                                            $varArrayPromedio = array();
                                              
                                            
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
                                                        WHERE es.documentoevaluado = $vardocumento AND es.idevaluaciontipo = 1 AND eb.idevaluacionbloques = $varidbloque
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();

                                            $varconteocompetencia = 0;
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
                                                        WHERE es.documentoevaluado = $vardocumento AND es.idevaluaciontipo = 1 AND ec.namecompetencia = '$nombrecompetencias'
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
                                                        WHERE es.documentoevaluado = $vardocumento AND es.idevaluaciontipo = 3 AND ec.namecompetencia = '$nombrecompetencias'
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
                                                        WHERE es.documentoevaluado = $vardocumento AND es.idevaluaciontipo = 2 AND ec.namecompetencia = '$nombrecompetencias'
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
                                                        WHERE es.documentoevaluado = $vardocumento AND es.idevaluaciontipo = 4 AND ec.namecompetencia = '$nombrecompetencias'
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

                                                array_push($varArrayPromedio, $txtnotafinal1);
                                        ?>
                                            <tr>
                                                <td colspan="3" ><label style="font-size: 15px; "><?php echo $nombrecompetencias; ?></label></td>
                                                <td class="text-center" colspan="2">
                                                    <label style="font-size: 13px;">
                                                        <?php echo $txtnotafinal1.'% '; ?>                     
                                                    </label></td>
                                            </tr>
                                        <?php
                                            }
                                            $txtsum = array_sum($varArrayPromedio);
                                            $txtrtafinal = round((array_sum($varArrayPromedio) / $varconteocompetencia), 1);

                                            if ($varidbloque == 1) {
                                                $varrtafinal = round(($txtrtafinal * (40 / 100)),2);
                                                $rtapornetaje = (40 / 100);
                                            }
                                            if ($varidbloque == 3) {
                                                $varrtafinal = round(($txtrtafinal * (40 / 100)),2);
                                                $rtapornetaje = (40 / 100);
                                            }
                                            if ($varidbloque == 2) {
                                                $varrtafinal = round(($txtrtafinal * (20 / 100)),2);
                                                $rtapornetaje = (20 / 100);
                                            }
                                        ?>
                                        <tr>
                                            <td class="text-center" colspan="3" ><label style="font-size: 15px;">
                                                <?php
                                                            echo Html::tag('span', '<i class="fas fa-info-circle" style="font-size: 18px; color: #C178G9;"></i>', [
                                                                          'data-title' => Yii::t("app", ""),
                                                                          'data-content' => 'Este resultado proviene del siguiente calculo: ('.$txtrtafinal.'*'.$rtapornetaje.') = '.$varrtafinal,
                                                                          'data-toggle' => 'popover',
                                                                          'style' => 'cursor:pointer;'
                                                            ]);
                                                ?>
                                                <?php echo 'Nota del Bloque'; ?></label>
                                            </td>
                                            <td class="text-center" colspan="2">
                                                <label style="font-size: 13px;"> 
                                                    <?php echo $varrtafinal.'% '; ?>                        
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center" colspan="5"  style="background-color: #C6C6C6;"></td>
                                        </tr>
                                    <?php
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?php echo 'Comentarios'; ?></label></td>
                                        <td class="text-center" colspan="4"><label style="font-size: 13px;"><?php echo $varcomentarios2; ?></label></td>                                        
                                    </tr>
                                    <tr>
                                        <td class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?php echo 'Comentarios Feedback'; ?></label></td>
                                        <td class="text-center" colspan="4"><label style="font-size: 13px;"><?php echo $varcomentariosfeedback; ?></label></td>                                        
                                    </tr>
                                </tbody>
                                

                            </table>

                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<hr>
<?php }else{ ?>
<br><br>
<div class="capaTwo" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #3339fb;"></em> Informacion General</label>
                <br>
                <label style="font-size: 15px;">No tienes permisos para ver este modulo, solo los administradores del proceso tienen el permiso.</label>   
            </div>
        </div>
    </div>
</div>
<hr>
<?php } ?>

<script type="text/javascript">
    function busca(){
        var vartxtidusuario = document.getElementById("txtidusuario").value;

        if (vartxtidusuario == "") {
            event.preventDefault();
            swal.fire("��� Advertencia !!!","Debe de ingresar el numero de documento para realizazr la busqueda.","warning");
            return; 
        }   
    };

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
            document.getElementById("dlink").download = "Archivo Datos del Feedback";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblData', 'Archivo del FEedback Evaluacion Desarrollo', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);
</script>