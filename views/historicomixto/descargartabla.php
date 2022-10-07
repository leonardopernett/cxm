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

    $this->title = 'Histórico Valoraciones Mixtas - Descargar';
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
<div id="capaTableId" class="capaTable" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">

                <table id="myTableVariable" class="table table-hover table-bordered" style="margin-top:10px" >
                    <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #615E9B;"></em> <?= Yii::t('app', 'Resultados Historico Mixto - '.$varServicioNombre) ?></label></caption>
                    <thead>
                        <tr>
                            <th scope="col" colspan="<?php echo $varConteoListarCategorias; ?>" class="text-center" style="background-color: #6152fd;"><label style="font-size: 15px;color: #fffcfc;"><?= Yii::t('app', 'KONECTA - QA MANAGEMENT') ?></label></th>
                        </tr>                            
                        <tr>
                            <th scope="col" colspan="<?php echo $varConteoListarCategorias; ?>" class="text-center" style="background-color: #fffcfc;"><label style="font-size: 20px;"><?= Yii::t('app', 'Resultados Historico Mixto - '.$varServicioNombre) ?></label></th>
                        </tr>
                        <tr>
                            <th scope="col" colspan="<?php echo $varConteoListarCategorias; ?>" class="text-center" style="background-color: #6152fd;"><label style="font-size: 15px;color: #fffcfc;"><?= Yii::t('app', 'Información General') ?></label></th>
                        </tr> 
                        <tr>
                            <th scope="col" colspan="7" class="text-center" style="background-color: #cfcfcf;"><label style="font-size: 15px;color: #cfcfcf;"><?= Yii::t('app', '') ?></label></th>
                            <?php
                                foreach ($varListarCategorias as $key => $value) {
                                    $varResponsable = $value['responsable'];
                                
                                    if ($varResponsable == 1) {
                                        $varResponsabilidad = "Agente";
                                    }
                                    if ($varResponsable == 2) {
                                        $varResponsabilidad = "Canal";
                                    }
                                    if ($varResponsable == 3) {
                                        $varResponsabilidad = "Marca";
                                    }
                                    if ($varResponsable == null) {
                                        $varResponsabilidad = "N/A";
                                    }
                            ?>
                                <th scope="col" class="text-center" style="background-color: #cfcfcf;"><label style="font-size: 15px;"><?= Yii::t('app', $varResponsabilidad) ?></label></th>
                            <?php
                                }  
                            ?>
                            <th scope="col" colspan="4" class="text-center" style="background-color: #cfcfcf;"><label style="font-size: 15px;color: #cfcfcf;"><?= Yii::t('app', '') ?></label></th>
                        </tr>
                        <tr>
                            <th scope="col" colspan="7" class="text-center" style="background-color: #cfcfcf;"><label style="font-size: 15px;"><?= Yii::t('app', $varNombrePcrcs) ?></label></th>
                            <?php
                                foreach ($varListarCategorias as $key => $value) {
                                    $varOrientacionSmart = $value['orientacionsmart'];
                                    $varidcategoria = $value['idcategoria'];
                                
                                    if ($varOrientacionSmart == 1) {
                                        $txtOrientacion = "Positivo";
                                    }else{
                                        $txtOrientacion = "Negativo";
                                    }

                                    $varInfoOrientacion = $varidcategoria.' - '.$txtOrientacion;
                            ?>
                                <th scope="col" class="text-center" style="background-color: #cfcfcf;"><label style="font-size: 15px;"><?= Yii::t('app', $varInfoOrientacion) ?></label></th>
                            <?php
                                }  
                            ?>
                            <th scope="col" colspan="4" class="text-center" style="background-color: #cfcfcf;"><label style="font-size: 15px;color: #cfcfcf;"><?= Yii::t('app', '') ?></label></th>
                        </tr>
                        <tr>
                            <th scope="col" class="text-center" style="background-color: #cfcfcf;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha Llamada') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #cfcfcf;"><label style="font-size: 15px;"><?= Yii::t('app', 'Id Llamada') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #cfcfcf;"><label style="font-size: 15px;"><?= Yii::t('app', 'Parametros') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #cfcfcf;"><label style="font-size: 15px;"><?= Yii::t('app', 'Duración Seg.') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #cfcfcf;"><label style="font-size: 15px;"><?= Yii::t('app', 'Etiquetados') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #cfcfcf;"><label style="font-size: 15px;"><?= Yii::t('app', 'Datos Asesor') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #cfcfcf;"><label style="font-size: 15px;"><?= Yii::t('app', 'Datos Lider') ?></label></th>
                            <?php
                                foreach ($varListarCategorias as $key => $value) {
                                    $vartipocategoria = $value['idcategorias'];
                                    $varCategoriaNombres = $value['nombre'];
                                    $varTipoindicador = $value['tipoindicador'];
                                
                                    if ($vartipocategoria == 1) {
                                        $txtColor = "#337ab7";
                                    }else{
                                        $txtColor = "#6b97b1";
                                    }

                                    if ($vartipocategoria == 2) {
                                        $varCategoriaNombres = $varCategoriaNombres.' ('.$varTipoindicador.')';
                                    }
                                    
                            ?>
                                <th scope="col" class="text-center" style="background-color: <?php echo $txtColor; ?>;"><label style="font-size: 15px;"><?= Yii::t('app', $varCategoriaNombres) ?></label></th>
                            <?php
                                }  
                            ?>
                            <th scope="col" class="text-center" style="background-color: #FFC72C;"><label style="font-size: 15px;"><?= Yii::t('app', 'Resultado Automatico Agente') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #FFC72C;"><label style="font-size: 15px;"><?= Yii::t('app', 'Resultado Automatico PEC') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #FFC72C;"><label style="font-size: 15px;"><?= Yii::t('app', 'Resultado Calidad & Consistencia') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #FFC72C;"><label style="font-size: 15px;"><?= Yii::t('app', 'Resultado Score') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($varListarCallid as $key => $value) {
                                $varFechallamadas = $value['fechallamada'];
                                $varLlamadaId = $value['callId'];
                                $varParametros = $value['extension'];
                                $varDuracion = $value['callduracion'];
                                $varEtiquetado = $value['login_id'];

                                if (is_numeric($varEtiquetado)) {
                                    $varAsesor = $value['dsusuario_red'];
                                }else{
                                    $varAsesor = $value['identificacion'];
                                }

                                if ($varAsesor != 'NA') {
                                    $varLider = (new \yii\db\Query())
                                                    ->select(['tbl_usuarios.usua_nombre'])
                                                    ->from(['tbl_usuarios']) 

                                                    ->join('LEFT OUTER JOIN', 'tbl_equipos',
                                                      'tbl_usuarios.usua_id = tbl_equipos.usua_id ')

                                                    ->join('LEFT OUTER JOIN', 'tbl_equipos_evaluados',
                                                      'tbl_equipos.id = tbl_equipos_evaluados.equipo_id')

                                                    ->join('LEFT OUTER JOIN', 'tbl_evaluados',
                                                      'tbl_equipos_evaluados.evaluado_id = tbl_evaluados.id')

                                                    ->where(['=','tbl_evaluados.identificacion',$value['identificacion']])
                                                    ->Scalar();
                                }else{
                                    $varLider = 'NA';
                                }

                                
                                

                                 
                        ?>
                            <tr>
                                <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $varFechallamadas) ?></label></td>
                                <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $varLlamadaId) ?></label></td>
                                <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $varParametros) ?></label></td>
                                <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $varDuracion) ?></label></td>
                                <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $varEtiquetado) ?></label></td>
                                <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $varAsesor) ?></label></td>
                                <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $varLider) ?></label></td>
                                <?php
                                    $varConteoCategoria = null;
                                    $arrayConteos = array();
                                    foreach ($varListarCategorias as $key => $value) {
                                        $vartipocategoria = $value['idcategoria'];

                                        $varConteoCategoria = (new \yii\db\Query())
                                                            ->select(['callid'])
                                                            ->from(['tbl_speech_general'])           
                                                            ->where(['=','fechallamada',$varFechallamadas])
                                                            ->andwhere(['=','extension',$varParametros])
                                                            ->andwhere(['=','idvariable',$vartipocategoria])
                                                            ->andwhere(['=','callid',$varLlamadaId])
                                                            ->count();

                                        if ($varConteoCategoria != 0) {
                                            array_push($arrayConteos,$varConteoCategoria);
                                        }
                                ?>
                                    <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $varConteoCategoria) ?></label></td>

                                <?php
                                    }
                                ?>
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