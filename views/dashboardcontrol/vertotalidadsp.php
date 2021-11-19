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
    
    $this->title = 'Tablero de Control -- Volúmen por Cliente --';

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

    $FechaActual = date("Y-m-d");
    $MesAnterior = date("m") - 1;

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
    $varLastYear = '2019-12-31';

?>
<div class="CapaUno" style="display: inline" id="CapaUno">
    <table class="table table-striped table-bordered detail-view formDinamico" border="0">
    <caption>Resultados</caption>
        <thead>
            <tr>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Total Resultados') ?></th>
                <?php
                    $varMonthYear = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();

                    foreach ($varMonthYear as $key => $value) {
                        $varMonth = $value['CorteMes'];
                        $varYear = $value['CorteYear'];
                ?>
                    <th scope="col" class="text-center"><?php echo 'Total '.$varMonth.' - '.$varYear; ?></th>
                <?php
                    }
                ?>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Total Promedio') ?></th>                
            </tr>
        </thead>    
        <tbody>
            <tr>
                <td class="text-center"><?= Yii::t('app', '') ?></td>
            <?php
                $varMonthYear2 = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();

                $varSumPromedio = 0;
                foreach ($varMonthYear2 as $key => $value) {
                    $varMonth2 = $value['mesyear'];

                    $varRtaTotal = Yii::$app->db->createCommand("select sum(cantidadvalorS) as SumCantidad from tbl_control_volumenxclienteS where mesyear = '$varMonth2' and anuladovxcS = 0")->queryScalar();
                    $varSumPromedio = $varSumPromedio + $varRtaTotal;
            ?>   
                <td class="text-center"><?php echo $varRtaTotal; ?></td>
            <?php
                }
            ?>
                <td class="text-center"><?php echo round($varSumPromedio/6); ?></td> 
        </tbody>
    </table>
</div>