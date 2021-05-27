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

$this->title = 'DashBoard -- Métricas de Productividad Valoración --';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Métricas de Productividad/Valoración';

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

    // $varAnio = date('Y');
    // $varBeginYear = $varAnio.'-01-01';
    // $varLastYear = $varAnio.'-12-31';   
    $varBeginYear = '2019-01-01';
    $varLastYear = '2025-12-31';        

?>
<div class="CapaCero" style="display: inline">
    <label><p>Volúmen de Gestión...</p></label>
    <table class="table table-striped table-bordered detail-view formDinamico" border="0">
        <thead>
            <th class="text-center"><?= Yii::t('app', 'Nivel') ?></th>
            <?php
                $varMonthYear = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();

                foreach ($varMonthYear as $key => $value) {
                    $varMonth = $value['CorteMes'];
                    $varYear = $value['CorteYear'];
            ?>
                <th class="text-center"><?php echo $varMonth.' - '.$varYear; ?></th>
            <?php
                }
            ?>   
            <th class="text-center"><?= Yii::t('app', 'Promedio ') ?></th>         
        </thead>
        <tbody>
            <?php
                $txtCiudades = Yii::$app->db->createCommand(" select id, name, arbol_id from tbl_arbols where id in (98, 2, 1)")->queryAll(); 

                $varListMonth2 = Yii::$app->db->createCommand("select distinct mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' and tipocortetc not like '%$txtMes%' group by mesyear order by mesyear desc limit 6")->queryAll();                    

                $varFisrtDate = date($varListMonth2[0]['mesyear']);
                $varLastDate = date($varListMonth2[5]['mesyear']);

		$txtTotalAVG = null;

                foreach ($txtCiudades as $key => $value) {
                    $txtNameCity = $value['name'];
                    $varIdPcrc = $value['id'];

                    $varListMonth = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();

                    if ($varIdPcrc == 1) {
                        $txtQuery2 =  new Query;
                        $txtQuery2   ->select(['round(sum(tbl_control_volumenxcliente.cantidadvalor)/6)'])->distinct()
                                    ->from('tbl_control_volumenxcliente')
                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                        'tbl_control_volumenxcliente.idservicio = tbl_arbols.id')
                                    ->where('tbl_arbols.arbol_id in (2, 98)')
                                    ->andwhere(['between','tbl_control_volumenxcliente.mesyear', $varLastDate, $varFisrtDate]);                    
                        $command = $txtQuery2->createCommand();
                        $txtTotalAVG1 = $command->queryScalar();

                        $txtQuery22 =  new Query;
                        $txtQuery22   ->select(['round(sum(tbl_control_volumenxclienteS.cantidadvalorS)/6)'])->distinct()
                                    ->from('tbl_control_volumenxclienteS')
                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                        'tbl_control_volumenxclienteS.idservicio = tbl_arbols.id')
                                    ->where('tbl_arbols.arbol_id in (2, 98)')
                                    ->andwhere(['between','tbl_control_volumenxclienteS.mesyear', $varLastDate, $varFisrtDate]);                    
                        $command2 = $txtQuery22->createCommand();
                        $txtTotalAVG2 = $command2->queryScalar();  

                        $txtTotalAVG = $txtTotalAVG1 + $txtTotalAVG2;
                    }else{
                        $txtQuery2 =  new Query;
                        $txtQuery2   ->select(['round(sum(tbl_control_volumenxcliente.cantidadvalor)/6)'])->distinct()
                                    ->from('tbl_control_volumenxcliente')
                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                        'tbl_control_volumenxcliente.idservicio = tbl_arbols.id')
                                    ->where('tbl_arbols.arbol_id = '.$varIdPcrc.'')
                                    ->andwhere(['between','tbl_control_volumenxcliente.mesyear', $varLastDate, $varFisrtDate]);                    
                        $command = $txtQuery2->createCommand();
                        $txtTotalAVG1 = $command->queryScalar(); 

                        $txtQuery22 =  new Query;
                        $txtQuery22   ->select(['round(sum(tbl_control_volumenxclienteS.cantidadvalorS)/6)'])->distinct()
                                    ->from('tbl_control_volumenxclienteS')
                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                        'tbl_control_volumenxclienteS.idservicio = tbl_arbols.id')
                                    ->where('tbl_arbols.arbol_id = '.$varIdPcrc.'')
                                    ->andwhere(['between','tbl_control_volumenxclienteS.mesyear', $varLastDate, $varFisrtDate]);                    
                        $command2 = $txtQuery22->createCommand();
                        $txtTotalAVG2 = $command2->queryScalar(); 

                        $txtTotalAVG = $txtTotalAVG1 + $txtTotalAVG2;  
                    
                    }

                

            ?>
            <tr>
                <td class="text-center"><?php echo $txtNameCity; ?></td>

                <?php
                    $txtTotalMonth = null;
                    foreach ($varListMonth as $key => $value) {
                        $varListYear = $value['mesyear'];                     

                        if ($varIdPcrc == 1){
                            $txtQuery =  new Query;
                            $txtQuery   ->select(['sum(tbl_control_volumenxcliente.cantidadvalor)'])->distinct()
                                        ->from('tbl_control_volumenxcliente')
                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_control_volumenxcliente.idservicio = tbl_arbols.id')
                                        ->where('tbl_arbols.arbol_id in (2, 98)')
                                        ->andwhere(['between','tbl_control_volumenxcliente.mesyear', $varListYear, $varListYear]);                    
                            $command = $txtQuery->createCommand();
                            $txtTotalMonth1 = $command->queryScalar();

                            $txtQuery1 =  new Query;
                            $txtQuery1   ->select(['sum(tbl_control_volumenxclienteS.cantidadvalorS)'])->distinct()
                                        ->from('tbl_control_volumenxclienteS')
                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_control_volumenxclienteS.idservicio = tbl_arbols.id')
                                        ->where('tbl_arbols.arbol_id in (2, 98)')
                                        ->andwhere(['between','tbl_control_volumenxclienteS.mesyear', $varListYear, $varListYear]);                    
                            $command1 = $txtQuery1->createCommand();
                            $txtTotalMonth2 = $command1->queryScalar(); 

                            $txtTotalMonth = $txtTotalMonth1 + $txtTotalMonth2;
                        }else{

                            $txtQuery =  new Query;
                            $txtQuery   ->select(['sum(tbl_control_volumenxcliente.cantidadvalor)'])->distinct()
                                        ->from('tbl_control_volumenxcliente')
                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_control_volumenxcliente.idservicio = tbl_arbols.id')
                                        ->where('tbl_arbols.arbol_id = '.$varIdPcrc.'')
                                        ->andwhere(['between','tbl_control_volumenxcliente.mesyear', $varListYear, $varListYear]);                    
                            $command = $txtQuery->createCommand();
                            $txtTotalMonth1 = $command->queryScalar();                       

                            $txtQuery2 =  new Query;
                            $txtQuery2   ->select(['sum(tbl_control_volumenxclienteS.cantidadvalorS)'])->distinct()
                                        ->from('tbl_control_volumenxclienteS')
                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_control_volumenxclienteS.idservicio = tbl_arbols.id')
                                        ->where('tbl_arbols.arbol_id = '.$varIdPcrc.'')
                                        ->andwhere(['between','tbl_control_volumenxclienteS.mesyear', $varListYear, $varListYear]);                    
                            $command2 = $txtQuery2->createCommand();
                            $txtTotalMonth2 = $command2->queryScalar(); 

                            $txtTotalMonth = $txtTotalMonth1 + $txtTotalMonth2;
                        }                   
                ?>

                    <td class="text-center"><?php echo round($txtTotalMonth); ?></td>
                <?php
		    }
                ?>
                <td class="text-center"><?php echo $txtTotalAVG; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <div onclick="verVolumenGestion();" class="btn btn-primary" style="display:inline; width:70px; height:25px" method='post' id="botones5" >
        Ver Detalle
    </div> 
    &nbsp;
    <div onclick="verVolumenValorador();" class="btn btn-primary" style="display:inline; width:70px; height:25px; background-color: #4298b4" method='post' id="botones5" >
        Ver Volúmen x Valoradores
    </div> 
</div>
<hr>
<div class="CapaUno" style="display: inline">
    <label><p>Costo x Gestión...</p></label>
    <table class="table table-striped table-bordered detail-view formDinamico" border="0">
        <thead>
            <th class="text-center"><?= Yii::t('app', 'Nivel') ?></th>
            <?php
                $varMonthYear = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();

                foreach ($varMonthYear as $key => $value) {
                    $varMonth = $value['CorteMes'];
                    $varYear = $value['CorteYear'];
            ?>
                <th class="text-center"><?php echo $varMonth.' - '.$varYear; ?></th>
            <?php
                }
            ?>   
            <th class="text-center"><?= Yii::t('app', 'Promedio ') ?></th>         
        </thead>
        <tbody>
	
            <?php
                $txtCiudades = Yii::$app->db->createCommand(" select id, name, arbol_id from tbl_arbols where id in (98, 2, 1)")->queryAll(); 

                foreach ($txtCiudades as $key => $value) {
                    $txtNameCity = $value['name'];
                    $varIdPcrc = $value['id'];

            ?>
		<tr>
                <td class="text-center"><?php echo $txtNameCity; ?></td>
		</tr>
            <?php } ?>

        </tbody>
    </table>
    <div onclick="verVolumenCosto();" class="btn btn-primary" style="display:inline; width:70px; height:25px" method='post' id="botones5" >
        Ver Detalle
    </div> 
    &nbsp;
    <div onclick="verVolumenValorador();" class="btn btn-primary" style="display:inline; width:70px; height:25px; background-color: #4298b4" method='post' id="botones5" >
        Ver Volúmen x Valoradores
    </div> 
</div>

<script type="text/javascript">
    function verVolumenGestion(){
        window.open('http://qa.allus.com.co/qa_managementv2/web/index.php/dashboardcontrol/vervolumengestion','_blank');
    };

    function verVolumenCosto(){
        window.open('http://qa.allus.com.co/qa_managementv2/web/index.php/dashboardcontrol/vervolumencostos','_blank');
    };

    function verVolumenValorador(){
        window.open('http://qa.allus.com.co/qa_managementv2/web/index.php/dashboardcontrol/vervolumenvalorador','_blank');
    };
</script>