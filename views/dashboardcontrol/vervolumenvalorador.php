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
use yii\bootstrap\modal;
    
    $this->title = 'Tablero de Control -- Volúmen por Valorador --';

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

    $querys =  new Query;
    $querys     ->select(['tbl_arbols.id as ArbolID','tbl_arbols.name as ArbolName'])->distinct()
                ->from('tbl_control_volumenxcliente')
                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                            'tbl_control_volumenxcliente.idservicio = tbl_arbols.id');                    
    $command = $querys->createCommand();
    $query = $command->queryAll();

    $listData = ArrayHelper::map($query, 'ArbolID', 'ArbolName');

    $txtvarServicio = $varIdServicio;


    $varBeginYear = '2019-01-01';
    $varLastYear = '2025-12-31';

?>

<div class="page-header" >
    <h3 class="text-center"><?= Html::encode($this->title) ?></h3>
</div>
<div class="CapaUno" style="display: inline">
    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ],
        'layout' => 'horizontal'
        ]); ?>

        <?= $form->field($model, 'idservicio')->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'TipoArbol'])->label('Servicio/PCRC') ?> 

    <div class="row" style="text-align: center;"> 
        <?= Html::submitButton(Yii::t('app', 'Buscar Valoradores'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                'data-toggle' => 'tooltip',
                'title' => 'Buscar Valoradores']) 
        ?>
    </div>
        
    <?php $form->end() ?>
</div>
<hr>
<div class="CapaDos" style="display: inline">
    <table class="table table-striped table-bordered detail-view formDinamico" border="0">
    <caption>Valorador</caption>
        <thead>
            <tr>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Ciudad') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Cliente') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Identificación') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Valorador') ?></th>
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

                $querys =  new Query;
                $querys     ->select(['aa.name as Ciudad', 'a.name as Servicio', 'cv.usua_id as IdUsu', 'cv.identificacion as Identificacion','cv.nombres as Nombres'])->distinct()
                            ->from('tbl_control_volumenxvalorador cv')
                            ->join('LEFT OUTER JOIN', 'tbl_arbols a',
                                    'cv.idservicio = a.id')
                            ->join('LEFT OUTER JOIN', 'tbl_arbols aa',
                                    'aa.id = a.arbol_id')                            
                            ->where('a.id = '.$txtvarServicio.'');                    
                $command = $querys->createCommand();
                $query = $command->queryAll();

                foreach ($query as $key => $value) {
                    $varCiudad = $value['Ciudad'];
                    $varServicios = $value['Servicio'];
                    $varUsuID = $value['IdUsu'];
                    $varIdentidad = $value['Identificacion'];
                    $varNombres = $value['Nombres'];

                    $varMonthYear2 = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();

		    $varPromedio = Yii::$app->db->createCommand("select round(sum(totalrealizadas)/6) from tbl_control_volumenxvalorador where idservicio = '$txtvarServicio' and usua_id = '$varUsuID' and anuladovxv = 0")->queryScalar();
            ?>
            <tr>
                <td class="text-center"><?php echo $varCiudad; ?></td>
                <td class="text-center"><?php echo $varServicios; ?></td>
                <td class="text-center"><?php echo $varIdentidad; ?></td>
                <td class="text-center"><?php echo $varNombres; ?></td>
                <?php
                    $varRealizadas = 0;
                    foreach ($varMonthYear2 as $key => $value) {
                        $varMesYear = $value['mesyear'];

                        $varControl = Yii::$app->db->createCommand("select totalrealizadas from tbl_control_volumenxvalorador where idservicio = '$txtvarServicio' and usua_id = '$varUsuID' and mesyear = '$varMesYear' and anuladovxv = 0")->queryScalar();

                        if ($varControl != null) {
                            $varRealizadas = $varControl;
                        }else{
                            $varRealizadas = 0;
                        } 
                ?>
                    <td class="text-center"><?php echo $varRealizadas; ?></td>
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