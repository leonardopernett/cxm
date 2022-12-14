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
use yii\db\Query;

$sesiones =Yii::$app->user->identity->id;  

$varSinData = '--'; 
?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css" >
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
<style type="text/css">
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

    .card:hover .card1:hover {
        top: -15%;
    }

</style>
<!-- Capa Procesos Formularios -->
<div id="capaFormsId" class="capaForms" style="display: inline;">
    
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <?php
                $varCantidadValores = 0;
                foreach ($varListaIdForms as $key => $value) {
                    $varNombreAsesor = $value['name'];
                    $varconteoValoraciones = $value['conteoValora'];
                    $varIdLiders = $value['lider_id'];
                    $varIdAsesores = $value['id'];

                    $varCantidadValores += 1;
                    $varNombreTablaValores = "myTableValoracion_".$varCantidadValores;
                ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card1 mb" style="background: #6b97b1; ">
                            <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', $varNombreAsesor.'  -  Cantidad Valoraciones: '.$varconteoValoraciones) ?></label>
                        </div>
                    </div>
                </div>

                <br>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card1 mb" style="font-size: 15px;">

                            <table id="<?php echo $varNombreTablaValores; ?>" class="table table-hover table-bordered" style="margin-top:10px" >
                                <caption><label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #FFC72C;"></em> <?= Yii::t('app', 'Resultados Procesos Calidad y Consistencia') ?></label></caption>
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Procesos') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PEC') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PENC') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'SPC/FRC') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'CARIÑO/WOW') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Proceso') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Experiencia') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cumplimiento Promesa de Marca') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Desempeno del Canal') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Desempeno del Agente') ?></label></th>
                                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Habilidad Comercial') ?></label></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                        $varListaFormsId = (new \yii\db\Query())
                                                              ->select([
                                                                'tbl_ideal_tmploginreportes.id_formulario'
                                                                ])
                                                              ->from(['tbl_ideal_tmploginreportes']) 

                                                              ->where(['=','tbl_ideal_tmploginreportes.id_dp_cliente',$varclienteid])
                                                              ->andwhere(['=','tbl_ideal_tmploginreportes.lider_id',$varIdLiders])
                                                              ->andwhere(['=','tbl_ideal_tmploginreportes.asesor_id',$varIdAsesores])
                                                              ->andwhere(['=','tbl_ideal_tmploginreportes.id_dimension',$varIdExtensionIdeal])
                                                              ->all();

                                        if (count($varListaFormsId) > 1) {
                                            $varListaProcesosForms = (new \yii\db\Query())
                                                              ->select([
                                                                'tbl_ideal_tmpreportes.id_proceso',
                                                                'tbl_ideal_tmpreportes.id_acciones',
                                                                'tbl_ideal_tmpreportes.col1'
                                                                ])
                                                              ->from(['tbl_ideal_tmploginreportes']) 

                                                              ->join('LEFT OUTER JOIN', 'tbl_ideal_tmpreportes',
                                                                      'tbl_ideal_tmpreportes.usua_id = tbl_ideal_tmploginreportes.usua_id
                                                                        
                                                                        AND tbl_ideal_tmpreportes.id_formulario = tbl_ideal_tmploginreportes.id_formulario')

                                                              ->where(['=','tbl_ideal_tmploginreportes.id_dp_cliente',$varclienteid])
                                                              ->andwhere(['=','tbl_ideal_tmploginreportes.lider_id',$varIdLiders])
                                                              ->andwhere(['=','tbl_ideal_tmploginreportes.asesor_id',$varIdAsesores])
                                                              ->andwhere(['not in','tbl_ideal_tmpreportes.id_acciones',[0,1]])
                                                              ->andwhere(['=','tbl_ideal_tmpreportes.id_dimension',$varIdExtensionIdeal])
                                                              ->groupby(['tbl_ideal_tmpreportes.id_acciones'])
                                                              ->all();
                                        }else{
                                            $varListaProcesosForms = (new \yii\db\Query())
                                                              ->select([
                                                                'tbl_ideal_tmpreportes.id_proceso',
                                                                'tbl_ideal_tmpreportes.id_acciones',
                                                                'tbl_ideal_tmpreportes.col1'
                                                                ])
                                                              ->from(['tbl_ideal_tmploginreportes']) 

                                                              ->join('LEFT OUTER JOIN', 'tbl_ideal_tmpreportes',
                                                                      'tbl_ideal_tmpreportes.usua_id = tbl_ideal_tmploginreportes.usua_id
                                                                        
                                                                        AND tbl_ideal_tmpreportes.id_formulario = tbl_ideal_tmploginreportes.id_formulario')

                                                              ->where(['=','tbl_ideal_tmploginreportes.id_dp_cliente',$varclienteid])
                                                              ->andwhere(['=','tbl_ideal_tmploginreportes.lider_id',$varIdLiders])
                                                              ->andwhere(['=','tbl_ideal_tmploginreportes.asesor_id',$varIdAsesores])
                                                              ->andwhere(['=','tbl_ideal_tmploginreportes.id_dimension',$varIdExtensionIdeal])
                                                              ->andwhere(['not in','tbl_ideal_tmpreportes.id_acciones',[0,1]])
                                                              ->all();
                                        }

                                        

                                        

                                        $varArrayFormsid = array();
                                        foreach ($varListaFormsId as $key => $value) {
                                            array_push($varArrayFormsid, $value['id_formulario']);
                                        }
                                        $varFormsIdArraysM = implode("', '", $varArrayFormsid);
                                        $arrayForms_downM = str_replace(array("#", "'", ";", " "), '', $varFormsIdArraysM);
                                        $varFormsM = explode(",", $arrayForms_downM);

                                        foreach ($varListaProcesosForms as $key => $value) {
                                            $varIdAcciones = $value['id_acciones'];
                                            $varAccion = $value['col1'];
                                            $varValora = $value['id_proceso'];

                                            $varColorValora = null;
                                            if ($varValora == 1) {
                                                $varColorValora = '#C6C6C6';
                                            }
                                            if ($varValora == 2) {
                                                $varColorValora = '#a79ff9';
                                            }
                                            if ($varValora == 3) {
                                                $varColorValora = '#9fc9f9';
                                            }

                                            $varListarPorcentajes = (new \yii\db\Query())
                                                              ->select([
                                                                'ROUND(AVG(col3),2) AS PEC',
                                                                'ROUND(AVG(col4),2) AS PENC',
                                                                'ROUND(AVG(col5),2) AS SPC_FRC',
                                                                'ROUND(AVG(col6),2) AS CARINO_WOW',
                                                                'ROUND(AVG(col7),2) AS Indice_de_Proceso',
                                                                'ROUND(AVG(col8),2) AS Indice_de_Experiencia',
                                                                'ROUND(AVG(col9),2) AS Cumplimiento_Promesa_de_Marca',
                                                                'ROUND(AVG(col10),2) AS Desempeno_del_Canal',
                                                                'ROUND(AVG(col11),2) AS Desempeno_del_Agente', 
                                                                'ROUND(AVG(col12),2) AS Habilidad_Comercial'
                                                                ])
                                                              ->from(['tbl_ideal_tmpreportes'])

                                                              ->where(['=','tbl_ideal_tmpreportes.id_acciones',$varIdAcciones])
                                                              ->andwhere(['=','tbl_ideal_tmpreportes.id_dimension',$varIdExtensionIdeal])
                                                              ->andwhere(['in','tbl_ideal_tmpreportes.id_formulario',$varFormsM])
                                                              ->all();


                                            foreach ($varListarPorcentajes as $key => $value) {
                                                
                                                if ($value['PEC'] != null) {
                                                    $varPec = $value['PEC'].' %';
                                                }else{
                                                    $varPec = $varSinData;
                                                }

                                                if ($value['PENC'] != null) {
                                                    $varPenc = $value['PENC'].' %';
                                                }else{
                                                    $varPenc = $varSinData;
                                                }

                                                if ($value['SPC_FRC'] != null) {
                                                    $varSpc = $value['SPC_FRC'].' %';
                                                }else{
                                                    $varSpc = $varSinData;
                                                }

                                                if ($value['CARINO_WOW'] != null) {
                                                    $varWow = $value['CARINO_WOW'].' %';
                                                }else{
                                                    $varWow = $varSinData;
                                                }

                                                if ($value['Indice_de_Proceso'] != null) {
                                                    $varIProceso = $value['Indice_de_Proceso'].' %';
                                                }else{
                                                    $varIProceso = $varSinData;
                                                }

                                                if ($value['Indice_de_Experiencia'] != null) {
                                                    $varIExperiencia = $value['Indice_de_Experiencia'].' %';
                                                }else{
                                                    $varIExperiencia = $varSinData;
                                                }

                                                if ($value['Cumplimiento_Promesa_de_Marca'] != null) {
                                                    $varIMarca = $value['Cumplimiento_Promesa_de_Marca'].' %';
                                                }else{
                                                    $varIMarca = $varSinData;
                                                }

                                                if ($value['Desempeno_del_Canal'] != null) {
                                                    $varICanal = $value['Desempeno_del_Canal'].' %';
                                                }else{
                                                    $varICanal = $varSinData;
                                                }

                                                if ($value['Desempeno_del_Agente'] != null) {
                                                    $varDAgente = $value['Desempeno_del_Agente'].' %';
                                                }else{
                                                    $varDAgente = $varSinData;
                                                }

                                                if ($value['Habilidad_Comercial'] != null) {
                                                    $varHabilidad = $value['Habilidad_Comercial'].' %';
                                                }else{
                                                    $varHabilidad = $varSinData;
                                                }

                                    ?>

                                            <tr>
                                                <td  style="font-size: 12px; background-color: <?php echo $varColorValora; ?>"><label style="font-size: 12px; "><?= Yii::t('app', $varAccion) ?></label></td>
                                                <td class="text-center"><label style="font-size: 12px;"><?= Yii::t('app', $varPec) ?></label></td>
                                                <td class="text-center"><label style="font-size: 12px;"><?= Yii::t('app', $varPenc) ?></label></td>
                                                <td class="text-center"><label style="font-size: 12px;"><?= Yii::t('app', $varSpc) ?></label></td>
                                                <td class="text-center"><label style="font-size: 12px;"><?= Yii::t('app', $varWow) ?></label></td>
                                                <td class="text-center"><label style="font-size: 12px;"><?= Yii::t('app', $varIProceso) ?></label></td>
                                                <td class="text-center"><label style="font-size: 12px;"><?= Yii::t('app', $varIExperiencia) ?></label></td>
                                                <td class="text-center"><label style="font-size: 12px;"><?= Yii::t('app', $varIMarca) ?></label></td>
                                                <td class="text-center"><label style="font-size: 12px;"><?= Yii::t('app', $varICanal) ?></label></td>
                                                <td class="text-center"><label style="font-size: 12px;"><?= Yii::t('app', $varDAgente) ?></label></td>
                                                <td class="text-center"><label style="font-size: 12px;"><?= Yii::t('app', $varHabilidad) ?></label></td>
                                            </tr>

                                    <?php
                                            }
                                        }
                                    ?>
                                </tbody>
                          </table>

                        </div>
                    </div>
                </div>

                <hr>

                <?php
                }
                ?>
            </div>
        </div>
    </div>
    

</div>