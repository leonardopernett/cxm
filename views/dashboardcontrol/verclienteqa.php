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
    
    $this->title = 'Tablero de Control -- Formularios '.$varServicio.' --';

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

    // $varAnio = date('Y');
    // $varBeginYear = $varAnio.'-01-01';
    // $varLastYear = $varAnio.'-12-31';
    $varBeginYear = '2019-01-01';
    $varLastYear = '2025-12-31';

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

    $querys =  new Query;
    $querys     ->select(['tbl_arbols.id as ArbolID','tbl_arbols.name as ArbolName'])->distinct()
                ->from('tbl_control_volumenxcliente')
                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                            'tbl_control_volumenxcliente.idservicio = tbl_arbols.id');                    
    $command = $querys->createCommand();
    $query = $command->queryAll();

    $listData = ArrayHelper::map($query, 'ArbolID', 'ArbolName');

    $txtvarServicio = $txtIdSevicio; 


?>
<?= Html::a('Regresar',  ['vervolumengestion'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
?>
<div class="capaTres" style="display: inline">
    <?= 
      Html::button('Descargar Formularios', ['value' => url::to(['datosformularios', 'idPcrc' => $txtvarServicio]), 'class' => 'btn btn-success', 
                    'id'=>'modalButton3',
                    'data-toggle' => 'tooltip',
                    'title' => 'Descarga de Datos', 
                    'style' => 'background-color: #4298b4']) 
    ?> 

    <?php
      Modal::begin([
                      'header' => '<h4>Procesando datos en el archivo de excel... </h4>',
                      'id' => 'modal3',
                      //'size' => 'modal-lg',
                    ]);

      echo "<div id='modalContent3'></div>";
                                                  
      Modal::end(); 
    ?>   
</div>
<br>
<div class="page-header" >
    <h3><center><?= Html::encode($this->title) ?></center></h3>
</div>
<div  class="col-md-12">
    <div class="row seccion-data">
      <div class="col-md-10">
        <label class="labelseccion">
          FORMULARIOS QA
        </label>      
      </div>    
      <div class="col-md-2">
        <?=
          Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                      "class" => "glyphicon glyphicon-chevron-downForm",
                                  ]) . "", "javascript:void(0)"
                                  , ["class" => "openSeccion", "id" => "bloqueOne"])
        ?>
      </div>
      <?php $this->registerJs('$("#bloqueOne").click(function () {
                                  $("#capaKonecta").toggle("slow");
                              });'); ?>
    </div>
</div>
<div id="capaKonecta" class="col-sm-12" style="display: inline">
    <table class="table table-striped table-bordered detail-view formDinamico" border="0">
        <thead>
            <th class="text-center"><?= Yii::t('app', 'PCRC') ?></th>
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

                foreach ($varFormularios as $key => $value) {
                    $txtFormulario = $value['name'];
                    $txtIdPcrc = $value['id'];
            ?>
            <tr>
                <td class="text-center"><?php echo $txtFormulario; ?></td>
		<?php
                    $varMeses = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();

                    $varSumPromedio = 0;
                    foreach ($varMeses as $key => $value) {
                        $txtMes1 = $value['mesyear'];

                        $query = Yii::$app->db->createCommand("select totalrealizadas from tbl_control_volumenxformulario where idservicio = '$txtvarServicio' and arbol_id = '$txtIdPcrc' and mesyear = '$txtMes1' and anuladovxf = 0")->queryScalar();
                        $varSumPromedio = $varSumPromedio + $query;
                ?>
                    <td class="text-center"><?php echo $query; ?></td>
                <?php 
                    } 
                ?>
                <td class="text-center"><?php echo round($varSumPromedio/6); ?></td>
            </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
</div>
