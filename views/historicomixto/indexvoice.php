<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use app\models\ControlProcesosPlan;
use yii\db\Query;

    $this->title = 'Histórico Valoraciones Mixtas';
    $this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sesiones =Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sesiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();
    
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
            font-family: "Nunito", sans-serif;
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
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>
<!-- datatable -->
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
<div class="capaInfo" style="display: inline;">
    <div class="row">
        <div class="col-md-6">
            <div class="card2 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?= Yii::t('app', 'Ficha Técnica - '.$varNombreServicio) ?> </label>
            </div>
        </div>
    </div>
    <br>
    <div class="row">

        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Programa/Pcrc Seleccionado') ?></label>
                <label style="font-size: 15px; text-align: center;"><?php echo $varNombrePcrc; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-list" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Dimensión Seleccionado') ?></label>
                <label style="text-align: center;"><?php echo "'".$extensiones."'"; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-calendar-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Rango de Fechas') ?></label>
                <label style="text-align: center;"><?php echo $rangofecha; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-hashtag" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Cantidad Interacciones') ?></label>
                <label style="text-align: center;"><?php echo count($varDataLlamadas); ?></label>
            </div>
        </div>


    </div>
</div>
<hr>
<div class="capaBtns" style="display: inline;">
    <div class="row">
        <div class="col-md-6">
            <div class="card2 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?= Yii::t('app', 'Acciones') ?></label>
            </div>
        </div>
    </div>
    <br>
    <div class="row">

        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-download" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Interacciones Información') ?></label>                
                
                <?= Html::button('Aceptar', ['value' => url::to(['descargartabla', 'servicio' => $bolsitacxm, 'extensiones' => $extensiones, 'llamadageneral' => $varLlamadasGeneral,  'fechainicio' => $dateini, 'fechafin' => $datefin,'codigoPCRC' => $codpcrc]), 'class' => 'btn btn-success', 'id'=>'modalButton2',
                        'data-toggle' => 'tooltip',
                        'title' => 'Descargar Tabla', 'style' => 'background-color: #337ab7']) 
                ?> 

                <?php
                    Modal::begin([
                      'header' => '<h4>Procesando información...</h4>',
                      'id' => 'modal2',
                      // 'size' => 'modal-lg',
                    ]);

                    echo "<div id='modalContent2'></div>";
                                                  
                    Modal::end(); 
                ?>
                    
            </div>
        </div>
        
        <?php if ($sesiones == '2953') { ?>

        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-at" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Descargar Base') ?></label>

                <?= Html::button('Aceptar', ['value' => url::to(['descargarbase', 'arbol_idV' => $bolsitacxm, 'parametros_idV' => $extensiones, 'codparametrizar' => $varCod, 'codigoPCRC' => $codpcrc, 'indicador' => null, 'nomFechaI' => $varFechaInicioReal, 'nomFechaF' => $varFechaFinReal]), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                        'data-toggle' => 'tooltip',
                        'title' => 'Descargar Base', 'style' => 'background-color: #337ab7']) 
                ?> 

                <?php
                    Modal::begin([
                      'header' => '<h4>Envio de datos al correo corporativo...</h4>',
                      'id' => 'modal1',
                    ]);

                    echo "<div id='modalContent1'></div>";
                                                  
                    Modal::end(); 
                ?>

            </div>
        </div>

        

        <?php } ?>

        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-search" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Interacciones Focalizadas') ?></label>
                <?= Html::a('Aceptar',  ['dashboardspeechdos/llamadafocalizada', 'varprograma'=>$bolsitacxm, 'varcodigopcrc'=>$codpcrc, 'varidcategoria'=>$varLlamadasGeneral, 'varextension'=>$extensiones, 'varfechasinicio'=>$dateini, 'varfechasfin'=>$datefin, 'vartcantllamadas'=>$varCantidadLlamadas, 'varfechainireal'=>$varFechaInicioReal, 'varfechafinreal'=>$varFechaFinReal,'varcodigos'=>$varCod, 'varaleatorios' => 0], ['class' => 'btn btn-success',
                          'style' => 'background-color: #337ab7', 'target' => "_blank",
                          'data-toggle' => 'tooltip',
                          'title' => 'Interacciones Focalizadas']) 
                ?>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-minus-circle" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Nueva Búsqueda') ?></label>
                <?= Html::a('Aceptar',  ['index'], ['class' => 'btn btn-success',
                               'style' => 'background-color: #707372',                        
                                'data-toggle' => 'tooltip',
                                'title' => 'Nuevo'])
                ?>
            </div>
        </div>

    </div> 
</div>
<hr>
<div class="capaListado" style="display: inline;">
    <div class="row">
        <div class="col-md-6">
            <div class="card2 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?= Yii::t('app', 'Resultados') ?></label>
            </div>
        </div>
    </div>
    <br>

    <?php
        if ($varVerificaServicio == 0) {        
    ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
                        <caption>.</caption>
                        <thead>
                            <tr>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Id Interacción') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Asesor Speech') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Dato Asesor') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Lider') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Resultados Automatico Agente') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Resultados Calidad  y Consistencia') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Resultados Score') ?></label></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($varDataLlamadas as $key => $value) {
                                    $varFechaReal = $value['fechareal'];                                
                                    $varBolsita = $value['servicio'];
                                    $varCallid = $value['callId'];
                                    $varLogueo = $value['login_id'];
                                    $paramsRed = [':varUsua'=>$value['login_id']];
                                    $varLider = null;

                                    if (is_numeric($value['login_id'])) {
                                        $varDocumento = Yii::$app->db->createCommand('
                                        SELECT e.dsusuario_red FROM tbl_evaluados e 
                                            WHERE 
                                                e.identificacion IN (:varUsua)
                                            GROUP BY e.identificacion')->bindValues($paramsRed)->queryScalar();

                                        $varLider= Yii::$app->db->createCommand('
                                        SELECT u.usua_nombre FROM tbl_usuarios u 
                                            INNER JOIN tbl_equipos eq ON
                                                u.usua_id = eq.usua_id
                                            INNER JOIN tbl_equipos_evaluados ee ON 
                                                eq.id = ee.equipo_id
                                            INNER JOIN tbl_evaluados e ON 
                                                ee.evaluado_id = e.id
                                            WHERE 
                                                e.identificacion IN (:varUsua)
                                            GROUP BY u.usua_id')->bindValues($paramsRed)->queryScalar();
                                    }else{
                                        $varDocumento = Yii::$app->db->createCommand('
                                        SELECT e.identificacion FROM tbl_evaluados e 
                                            WHERE 
                                                e.dsusuario_red IN (:varUsua)
                                            GROUP BY e.identificacion')->bindValues($paramsRed)->queryScalar();

                                        $varLider= Yii::$app->db->createCommand('
                                        SELECT u.usua_nombre FROM tbl_usuarios u 
                                            INNER JOIN tbl_equipos eq ON
                                                u.usua_id = eq.usua_id
                                            INNER JOIN tbl_equipos_evaluados ee ON 
                                                eq.id = ee.equipo_id
                                            INNER JOIN tbl_evaluados e ON 
                                                ee.evaluado_id = e.id
                                            WHERE 
                                                e.dsusuario_red IN (:varUsua)
                                            GROUP BY u.usua_id')->bindValues($paramsRed)->queryScalar();
                                    }
                                    

                                    $paramsCategorias = [':varPcrc'=>$codpcrc,':varCategoria'=>2,':varResponsabilidad'=>1];
                                    $varListCategorias = Yii::$app->db->createCommand('
                                        SELECT idcategoria, orientacionsmart, responsable, programacategoria FROM tbl_speech_categorias 
                                            WHERE 
                                                cod_pcrc IN (:varPcrc)
                                                    AND idcategorias IN (:varCategoria)
                                                        AND responsable IN (:varResponsabilidad)')->bindValues($paramsCategorias)->queryAll();

                                    $varResultadosIDA = 0;
                                    $varContarNegativas = 0;
                                    $varTotalNegativas = 0;
                                    $varConteoNegativas = 0;
                                    $varContarPositivas = 0;
                                    $varTotalPositivas = 0;
                                    $varConteoPositivas = 0;

                                    foreach ($varListCategorias as $key => $value) {
                                        $varorientaciones = $value['orientacionsmart'];
                        
                                        $paramsBuscarCategorias = [':varIdCategoria'=>$value['idcategoria'],':varProgramaCategoria'=>$value['programacategoria'],':varAnulado'=>0,':varCallid'=>$varCallid];
                                                            
                                        if ($varorientaciones == '2') {
                                            $varContarNegativas += 1;
                                            $varTotalNegativas = Yii::$app->db->createCommand('
                                                SELECT COUNT(sg.idvariable) FROM tbl_speech_general sg
                                                    WHERE
                                                        sg.anulado = :varAnulado AND sg.callid IN (:varCallid)
                                                            AND sg.programacliente IN (:varProgramaCategoria)
                                                                AND sg.idvariable IN (:varIdCategoria)')->bindValues($paramsBuscarCategorias)->queryScalar();
                        
                                            if ($varTotalNegativas == 1) {
                                                $varConteoNegativas += 1;
                                            }
                        
                                        }else{
                                            $varContarPositivas += 1;
                                            $varTotalPositivas = Yii::$app->db->createCommand('
                                                SELECT COUNT(sg.idvariable) FROM tbl_speech_general sg
                                                    WHERE
                                                        sg.anulado = :varAnulado AND sg.callid IN (:varCallid)
                                                            AND sg.programacliente IN (:varProgramaCategoria)
                                                                AND sg.idvariable IN (:varIdCategoria)')->bindValues($paramsBuscarCategorias)->queryScalar();
                        
                                            if ($varTotalPositivas == 1) {
                                                $varConteoPositivas += 1;
                                            }
                                        }
                                    }
                        
                                    if (count($varListCategorias) != 0 && $varConteoNegativas != 0) {
                                        $varResultadosIDA = round(((($varConteoPositivas + ($varContarNegativas - $varConteoNegativas)) / count($varListCategorias))),2);
                                    }else{
                                        $varResultadosIDA = 0;
                                    }

                                    $varScore = (new \yii\db\Query())
                                                ->select(['round(tbl_ejecucionformularios.score,2)'])
                                                ->from(['tbl_ejecucionformularios'])
                                                ->join('LEFT OUTER JOIN', 'tbl_speech_mixta',
                                                'tbl_ejecucionformularios.id = tbl_speech_mixta.formulario_id')
                                                ->where(['=','tbl_speech_mixta.callid',$varCallid])
                                                ->andwhere(['=','tbl_speech_mixta.fechareal',$varFechaReal])
                                                ->andwhere(['=','tbl_speech_mixta.anulado',0])
                                                ->scalar(); 

                                    if ($varScore) {
                                        $varScoreValoracion = $varScore;
                                    }else{
                                        $varScoreValoracion = "--";
                                    }
                                    
                                    
                                    if ($varScoreValoracion != 0) {
                                        if ($varResultadosIDA != 0) {
                                            $varPromedioScore = round(((($varResultadosIDA + $varScoreValoracion) / 2)),2);
                                        }else{
                                            $varPromedioScore = $varScoreValoracion;
                                        }
                                    }else{
                                        $varPromedioScore = $varResultadosIDA;
                                    }
                            ?>
                                <tr>
                                    <td><label style="font-size: 12px;"><?php echo  $varFechaReal; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $varCallid; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $varLogueo; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $varDocumento; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $varLider; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $varResultadosIDA; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $varScoreValoracion; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $varPromedioScore; ?></label></td>
                                </tr>
                            <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php 
        }else{
    ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
                        <caption>.</caption>
                        <thead>
                            <tr>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Id Interacción') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Asesor Speech') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Dato Asesor') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Lider') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Resultados Automatico Agente') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Resultados Automatico PEC') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Resultados Calidad  y Consistencia') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Resultados Score') ?></label></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($varDataLlamadas as $key => $value) {
                                    $varFechaReal = $value['fechareal'];                                
                                    $varBolsita = $value['servicio'];
                                    $varCallid = $value['callId'];
                                    $varLogueo = $value['login_id'];
                                    $paramsRed = [':varUsua'=>$value['login_id']];
                                    $varLider = null;

                                    if (is_numeric($value['login_id'])) {
                                        $varDocumento = Yii::$app->db->createCommand('
                                        SELECT e.dsusuario_red FROM tbl_evaluados e 
                                            WHERE 
                                                e.identificacion IN (:varUsua)
                                            GROUP BY e.identificacion')->bindValues($paramsRed)->queryScalar();

                                        $varLider= Yii::$app->db->createCommand('
                                        SELECT u.usua_nombre FROM tbl_usuarios u 
                                            INNER JOIN tbl_equipos eq ON
                                                u.usua_id = eq.usua_id
                                            INNER JOIN tbl_equipos_evaluados ee ON 
                                                eq.id = ee.equipo_id
                                            INNER JOIN tbl_evaluados e ON 
                                                ee.evaluado_id = e.id
                                            WHERE 
                                                e.identificacion IN (:varUsua)
                                            GROUP BY u.usua_id')->bindValues($paramsRed)->queryScalar();
                                    }else{
                                        $varDocumento = Yii::$app->db->createCommand('
                                        SELECT e.identificacion FROM tbl_evaluados e 
                                            WHERE 
                                                e.dsusuario_red IN (:varUsua)
                                            GROUP BY e.identificacion')->bindValues($paramsRed)->queryScalar();

                                        $varLider= Yii::$app->db->createCommand('
                                        SELECT u.usua_nombre FROM tbl_usuarios u 
                                            INNER JOIN tbl_equipos eq ON
                                                u.usua_id = eq.usua_id
                                            INNER JOIN tbl_equipos_evaluados ee ON 
                                                eq.id = ee.equipo_id
                                            INNER JOIN tbl_evaluados e ON 
                                                ee.evaluado_id = e.id
                                            WHERE 
                                                e.dsusuario_red IN (:varUsua)
                                            GROUP BY u.usua_id')->bindValues($paramsRed)->queryScalar();
                                    }
                                    

                                    $paramsCategorias = [':varPcrc'=>$codpcrc,':varCategoria'=>2,':varResponsabilidad'=>1];
                                    $varListCategorias = Yii::$app->db->createCommand('
                                        SELECT idcategoria, orientacionsmart, responsable, programacategoria FROM tbl_speech_categorias 
                                            WHERE 
                                                cod_pcrc IN (:varPcrc)
                                                    AND idcategorias IN (:varCategoria)
                                                        AND responsable IN (:varResponsabilidad)')->bindValues($paramsCategorias)->queryAll();

                                    $varResultadosIDA = 0;
                                    $varContarNegativas = 0;
                                    $varTotalNegativas = 0;
                                    $varConteoNegativas = 0;
                                    $varContarPositivas = 0;
                                    $varTotalPositivas = 0;
                                    $varConteoPositivas = 0;

                                    foreach ($varListCategorias as $key => $value) {
                                        $varorientaciones = $value['orientacionsmart'];
                        
                                        $paramsBuscarCategorias = [':varIdCategoria'=>$value['idcategoria'],':varProgramaCategoria'=>$value['programacategoria'],':varAnulado'=>0,':varCallid'=>$varCallid];
                                                            
                                        if ($varorientaciones == '2') {
                                            $varContarNegativas += 1;
                                            $varTotalNegativas = Yii::$app->db->createCommand('
                                                SELECT COUNT(sg.idvariable) FROM tbl_speech_general sg
                                                    WHERE
                                                        sg.anulado = :varAnulado AND sg.callid IN (:varCallid)
                                                            AND sg.programacliente IN (:varProgramaCategoria)
                                                                AND sg.idvariable IN (:varIdCategoria)')->bindValues($paramsBuscarCategorias)->queryScalar();
                        
                                            if ($varTotalNegativas == 1) {
                                                $varConteoNegativas += 1;
                                            }
                        
                                        }else{
                                            $varContarPositivas += 1;
                                            $varTotalPositivas = Yii::$app->db->createCommand('
                                                SELECT COUNT(sg.idvariable) FROM tbl_speech_general sg
                                                    WHERE
                                                        sg.anulado = :varAnulado AND sg.callid IN (:varCallid)
                                                            AND sg.programacliente IN (:varProgramaCategoria)
                                                                AND sg.idvariable IN (:varIdCategoria)')->bindValues($paramsBuscarCategorias)->queryScalar();
                        
                                            if ($varTotalPositivas == 1) {
                                                $varConteoPositivas += 1;
                                            }
                                        }
                                    }
                        
                                    if (count($varListCategorias) != 0 && $varConteoNegativas != 0) {
                                        $varResultadosIDA = round(((($varConteoPositivas + ($varContarNegativas - $varConteoNegativas)) / count($varListCategorias))),2);
                                    }else{
                                        $varResultadosIDA = 0;
                                    }

                                    $varScore = (new \yii\db\Query())
                                                ->select(['round(tbl_ejecucionformularios.score,2)'])
                                                ->from(['tbl_ejecucionformularios'])
                                                ->join('LEFT OUTER JOIN', 'tbl_speech_mixta',
                                                'tbl_ejecucionformularios.id = tbl_speech_mixta.formulario_id')
                                                ->where(['=','tbl_speech_mixta.callid',$varCallid])
                                                ->andwhere(['=','tbl_speech_mixta.fechareal',$varFechaReal])
                                                ->andwhere(['=','tbl_speech_mixta.anulado',0])
                                                ->scalar(); 

                                    if ($varScore) {
                                        $varScoreValoracion = $varScore;

                                        if ($varScore == 1) {
                                            $varScoreValoracion = 100;
                                        }
                                        if ($varScore == 0) {
                                            $varScoreValoracion = 0;
                                        }

                                    }else{
                                        $varScoreValoracion = "--";
                                    }

                                    $varPecProceso = (new \yii\db\Query())
                                                      ->select(['tbl_speech_pecservicios.id_variable'])
                                                      ->from(['tbl_speech_pecservicios'])
                                                      ->join('LEFT OUTER JOIN', 'tbl_speech_general',
                                                                'tbl_speech_pecservicios.id_variable = tbl_speech_general.idvariable')
                                                      ->where(['=','tbl_speech_general.callid',$varCallid])
                                                      ->andwhere(['=','tbl_speech_pecservicios.cod_pcrc',$codpcrc])
                                                      ->count(); 
                                    if ($varPecProceso == null) {
                                        $varPecProceso = 0;
                                    }

                                    if ($varPecProceso == 1) {
                                        $varResultPec = 0;
                                    }else{
                                        $varResultPec = 100;
                                    }

                                    if ($varScoreValoracion != 0 && $varResultPec != 0) {
                                        $varPromedioScore = 100;
                                    }else{
                                        if ($varScoreValoracion == '--' && $varResultPec != 0) {
                                            $varPromedioScore = $varResultPec;
                                        }else{
                                            $varPromedioScore = 0;
                                        }                
                                    }

                                    
                            ?>
                                <tr>
                                    <td><label style="font-size: 12px;"><?php echo  $varFechaReal; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $varCallid; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $varLogueo; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $varDocumento; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $varLider; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $varResultadosIDA; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $varResultPec; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $varScoreValoracion; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $varPromedioScore; ?></label></td>
                                </tr>
                            <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        }
    ?>
</div>
<hr>

<script type="text/javascript">
    $(document).ready( function () {
        $('#myTable').DataTable({
            responsive: true,
            fixedColumns: true,
            select: false,
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
</script>