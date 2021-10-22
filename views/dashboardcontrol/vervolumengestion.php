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

    // $varAnio = date('Y');
    // $varBeginYear = $varAnio.'-01-01';
    // $varLastYear = $varAnio.'-12-31';
    $varBeginYear = '2019-01-01';
    $varLastYear = '2025-12-31';
?>
<br>
<div  style="display: inline" id="CapaCero">
    <div onclick="verGeneral();" class="btn btn-primary" style="display:inline; width:70px; height:25px; background-color: #4298b4" method='post' id="botones1" >
        Ver Detalle General
    </div> 
    &nbsp;
    <div onclick="verQa();" class="btn btn-primary" style="display:inline; width:70px; height:25px" method='post' id="botones2" >
        Ver Detalle QA
    </div> 
    &nbsp;
    <div onclick="verSpeech();" class="btn btn-primary" style="display:inline; width:70px; height:25px" method='post' id="botones3" >
        Ver Detalle Speech
    </div>       
</div>

<div class="page-header" >
    <h3 class="text-center"><?= Html::encode($this->title) ?></h3>
</div>

<div class="CapaUno" style="display: none" id="CapaUno">
    <?= Html::button('Ver totalidad', ['value' => url::to(['vertotalidadqa']), 'class' => 'btn btn-success', 
        'id'=>'modalButton1',
        'data-toggle' => 'tooltip',
        'title' => 'Ver totalidad', 
        'style' => 'background-color: #337ab7']) ?> 

  <?php
    Modal::begin([
      'header' => '<h4>Ver Totalidad Tablero de Control - QA - </h4>',
      'id' => 'modal1',
      'size' => 'modal-lg',
    ]);

    echo "<div id='modalContent1'></div>";
                                  
    Modal::end(); 
  ?> 

<br>
<br>
    <table class="table table-striped table-bordered detail-view formDinamico" border="0">
    <caption>Tablero de Control</caption>
        <thead>
            <tr>
                <th scope="col" class="text-center" colspan="10"><h4><?= Yii::t('app', 'Tablero de Control - QA -') ?></h4></th>
            </tr>
            <tr>
		<th scope="col" class="text-center"><?= Yii::t('app', '') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Ciudad') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Cliente') ?></th>
                <?php
                    $varMonthYear = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();

                    foreach ($varMonthYear as $key => $value) {
                        $varMonth = $value['CorteMes'];
                        $varYear = $value['CorteYear'];
                ?>
                    <th scope="col" class="text-center"><?php echo $varMonth.' - '.$varYear; ?></th>
                <?php
                    }
                ?>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Promedio ') ?></th>
            </tr>
        </thead>    
        <tbody>
            <?php
                $varPCRCPadres = Yii::$app->db->createCommand("select * from tbl_arbols where snhoja = 0 and arbol_id in (98, 2) and activo = 0")->queryAll();

                foreach ($varPCRCPadres as $key => $value) {
                    $varNameCityID = $value['arbol_id'];
                    $varNameCity = Yii::$app->db->createCommand("select name from tbl_arbols where id = '$varNameCityID'")->queryScalar();
                    $varNamePcrc = $value['name'];  
                    $varIdPcrc = $value['id'];                                     

                    $varControl = Yii::$app->db->createCommand("select cantidadvalor, mesyear from (select cantidadvalor, mesyear from tbl_control_volumenxcliente where idservicio = '$varIdPcrc' and anuladovxc = 0 order by mesyear desc limit 6) a order by a.mesyear asc")->queryAll();

                    $varPromedio = Yii::$app->db->createCommand("select avg(cantidadvalor) from (select cantidadvalor, mesyear from tbl_control_volumenxcliente where idservicio = '$varIdPcrc' and anuladovxc = 0 order by mesyear desc limit 6) a order by a.mesyear asc")->queryScalar();

            ?>
            <tr>
                <td class="text-center">
                    <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span>',  ['verclienteqa', 'idservicio' => $varIdPcrc], ['class' => 'btn btn-success',
                            'style' => 'background-color: #4298b4',
                            'data-toggle' => 'tooltip',
                            'title' => 'Ver Formulario']) 
                    ?>                     
                </td>
                <td class="text-center"><?php echo $varNameCity; ?></td>
                <td class="text-center"><?php echo $varNamePcrc; ?></td>

                <?php
                    foreach ($varControl as $key => $value) {
                        $varCantValue = $value['cantidadvalor']; 
                ?>   

                    <td class="text-center"><?php echo $varCantValue; ?></td>
                <?php
                    }
                ?>

                <td class="text-center"><?php echo round($varPromedio); ?></td>
            </tr>
            <?php			
                }
            ?>
        </tbody>
    </table>
</div>

<div class="CapaDos" style="display: none" id="CapaDos">
    <?= Html::button('Ver totalidad', ['value' => url::to(['vertotalidadsp']), 'class' => 'btn btn-success', 
        'id'=>'modalButton2',
        'data-toggle' => 'tooltip',
        'title' => 'Ver totalidad', 
        'style' => 'background-color: #337ab7']) ?> 

  <?php
    Modal::begin([
      'header' => '<h4>Ver Totalidad Tablero de Control - Speech - </h4>',
      'id' => 'modal2',
      'size' => 'modal-lg',
    ]);

    echo "<div id='modalContent2'></div>";
                                  
    Modal::end(); 
  ?> 
  <br>
  <br>
    <table class="table table-striped table-bordered detail-view formDinamico" border="0">
    <caption>Tablero de Control</caption>
        <thead>
            <tr>
                <th scope="col" class="text-center" colspan="10"><h4><?= Yii::t('app', 'Tablero de Control - Speech -') ?></h4></th>
            </tr>
            <tr>
		<th scope="col" class="text-center"><?= Yii::t('app', '') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Ciudad') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Cliente') ?></th>
                <?php
                    $varMonthYear = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();

                    foreach ($varMonthYear as $key => $value) {
                        $varMonth = $value['CorteMes'];
                        $varYear = $value['CorteYear'];
                ?>
                    <th scope="col" class="text-center"><?php echo $varMonth.' - '.$varYear; ?></th>
                <?php
                    }
                ?>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Promedio ') ?></th>
            </tr>
        </thead>    
        <tbody>
            <?php
                $varPCRCPadres = Yii::$app->db->createCommand("select * from tbl_arbols where snhoja = 0 and arbol_id in (98, 2) and activo = 0")->queryAll();

                foreach ($varPCRCPadres as $key => $value) {
                    $varNameCityID = $value['arbol_id'];
                    $varNameCity = Yii::$app->db->createCommand("select name from tbl_arbols where id = '$varNameCityID'")->queryScalar();
                    $varNamePcrc = $value['name'];  
                    $varIdPcrc = $value['id'];                                     


                    $txtResultado = Yii::$app->db->createCommand("select count(*) from tbl_dashboardservicios where arbol_id = '$varIdPcrc'")->queryScalar();
			
                    if ($txtResultado != 0) {                        

                    $varControl = Yii::$app->db->createCommand("select cantidadvalorS, mesyear from (select cantidadvalorS, mesyear from tbl_control_volumenxclienteS where idservicio = '$varIdPcrc' and anuladovxcS = 0 order by mesyear desc limit 6) a order by a.mesyear asc")->queryAll();

                    $varPromedio = Yii::$app->db->createCommand("select avg(cantidadvalorS) from (select cantidadvalorS, mesyear from tbl_control_volumenxclienteS where idservicio = '$varIdPcrc' and anuladovxcS = 0 order by mesyear desc limit 6) a order by a.mesyear asc")->queryScalar();
            ?>
            <tr>
                <td class="text-center">
                    <?= Html::a('<span class="glyphicon glyphicon-eye-open"></span>',  ['verclientesp', 'idservicio' => $varIdPcrc], ['class' => 'btn btn-success',
                            'style' => 'background-color: #4298b4',
                            'data-toggle' => 'tooltip',
                            'title' => 'Ver Formulario']) 
                    ?>         
                </td> 
                <td class="text-center"><?php echo $varNameCity; ?></td>
                <td class="text-center"><?php echo $varNamePcrc; ?></td>
                <?php
                    foreach ($varControl as $key => $value) {
                        $varCantValue = $value['cantidadvalorS']; 


                        if (!isset($varCantValue) || empty($varCantValue)) {
                            $varCantValue = "0";
                        }
                ?>   
                    <td class="text-center"><?php echo $varCantValue; ?></td>
                <?php                    
                    }
                ?>
                <td class="text-center"><?php echo round($varPromedio); ?></td>                
            </tr>     
            <?php } } ?>  
        </tbody>
    </table>
</div>
<div class="CapaTres" style="display: inline" id="CapaTres">
    <?= Html::button('Ver totalidad', ['value' => url::to(['vertotalidadgeneral']), 'class' => 'btn btn-success', 
        'id'=>'modalButton5',
        'data-toggle' => 'tooltip',
        'title' => 'Ver totalidad', 
        'style' => 'background-color: #337ab7']) ?> 

  <?php
    Modal::begin([
      'header' => '<h4>Ver Totalidad Tablero de Control - General - </h4>',
      'id' => 'modal5',
      'size' => 'modal-lg',
    ]);

    echo "<div id='modalContent5'></div>";
                                  
    Modal::end(); 
  ?> 
  <br>
  <br>
    <table class="table table-striped table-bordered detail-view formDinamico" border="0">
    <caption>Tablero de Control</caption>
        <thead>
            <tr>
                <th scope="col" class="text-center" colspan="9"><h4><?= Yii::t('app', 'Tablero de Control - General -') ?></h4></th>
            </tr>
            <tr>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Ciudad') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Cliente') ?></th>
                <?php
                    $varMonthYear = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();

                    foreach ($varMonthYear as $key => $value) {
                        $varMonth = $value['CorteMes'];
                        $varYear = $value['CorteYear'];
                ?>
                    <th scope="col" class="text-center"><?php echo $varMonth.' - '.$varYear; ?></th>
                <?php
                    }
                ?>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Promedio ') ?></th>
            </tr>
        </thead>    
        <tbody>
            <?php
                $varPCRCPadres = Yii::$app->db->createCommand("select * from tbl_arbols where snhoja = 0 and arbol_id in (98, 2) and activo = 0")->queryAll();

                foreach ($varPCRCPadres as $key => $value) {
                    $varNameCityID = $value['arbol_id'];
                    $varNameCity = Yii::$app->db->createCommand("select name from tbl_arbols where id = '$varNameCityID'")->queryScalar();
                    $varNamePcrc = $value['name'];  
                    $varIdPcrc = $value['id']; 

                    $varMeses = Yii::$app->db->createCommand("select  mesyear from (select cantidadvalor, mesyear from tbl_control_volumenxcliente where idservicio = '$varIdPcrc' and anuladovxc = 0 order by mesyear desc limit 6) a order by a.mesyear asc")->queryAll();

                    $varPromedio = Yii::$app->db->createCommand("select avg(cantidadvalor) from (select cantidadvalor, mesyear from tbl_control_volumenxcliente where idservicio = '$varIdPcrc' and anuladovxc = 0 order by mesyear desc limit 6) a order by a.mesyear asc")->queryScalar();


                    $varPromedio1 = Yii::$app->db->createCommand("select avg(cantidadvalorS) from (select cantidadvalorS, mesyear from tbl_control_volumenxclienteS where idservicio = '$varIdPcrc' and anuladovxcS = 0 order by mesyear desc limit 6) a order by a.mesyear asc")->queryScalar();

                    $txtRtaPromedio = $varPromedio + $varPromedio1;


            ?>
            <tr>
                <td class="text-center"><?php echo $varNameCity; ?></td>
                <td class="text-center"><?php echo $varNamePcrc; ?></td> 
                <?php
                foreach ($varMeses as $key => $value) {
                    $txtvarMes = $value['mesyear'];

                    $varControl = Yii::$app->db->createCommand("select cantidadvalor from tbl_control_volumenxcliente where mesyear = '$txtvarMes' and idservicio = '$varIdPcrc' and anuladovxc = 0")->queryScalar();

                    $varControl1 = Yii::$app->db->createCommand("select cantidadvalorS from tbl_control_volumenxclienteS where mesyear = '$txtvarMes' and idservicio = '$varIdPcrc' and anuladovxcS = 0")->queryScalar();

                    $txtRtaControl = $varControl + $varControl1;
                    
                ?>  
                    <td class="text-center"><?php echo $txtRtaControl; ?></td>
                <?php } ?>
                <td class="text-center"><?php echo round($txtRtaPromedio); ?></td>   
            </tr>     
            <?php } ?>   
        </tbody>
    </table>
</div>

<script type="text/javascript">
    function verGeneral(){
        var tblQA = document.getElementById("CapaUno");
        var tblSpeech = document.getElementById("CapaDos");
        var tblGeneral = document.getElementById("CapaTres");

        tblQA.style.display = 'none';
        tblSpeech.style.display = 'none';
        tblGeneral.style.display = 'inline';
    };

    function verQa(){
        var tblQA = document.getElementById("CapaUno");
        var tblSpeech = document.getElementById("CapaDos");
        var tblGeneral = document.getElementById("CapaTres");

        tblQA.style.display = 'inline';
        tblSpeech.style.display = 'none';
        tblGeneral.style.display = 'none';
    };

    function verSpeech(){
        var tblQA = document.getElementById("CapaUno");
        var tblSpeech = document.getElementById("CapaDos");
        var tblGeneral = document.getElementById("CapaTres");

        tblQA.style.display = 'none';
        tblSpeech.style.display = 'inline';
        tblGeneral.style.display = 'none';
    }; 

</script>