<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\modal;
use \app\models\ControlProcesos;
use \app\models\ControlParams;
use kartik\export\ExportMenu;
use yii\db\Query;

$this->title = 'Vista Valorador';

$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

$fechaActual = date("Y-m-d");


?>

<?= Html::a('Comparar Equipo',  ['comparar'], [
    'class' => 'btn btn-success',
    'style' => 'background-color: #337ab7',
    'data-toggle' => 'tooltip',
    'title' => 'Comparar Equipo'
])
?>
<?= Html::a('Regresar',  ['index'], [
    'class' => 'btn btn-success',
    'style' => 'background-color: #707372',
    'data-toggle' => 'tooltip',
    'title' => 'Regresar'
])
?>
<br>
<div class="page-header">
    <h3 style="text-align: center;"><?= Html::encode($this->title) ?></h3>
</div>

<table class="text-center" border="1" class="egt table table-hover table-striped table-bordered">
    <caption>Tabla datos</caption>
    <tr>
        <th scope="col"

>
            <p>Valorador: </p><?php echo $nomValorador; ?>
        </th>
        <th scope="col"

>
            <p>Rango de Corte: </p><?php echo $varNametc; ?> \\ <?php echo $fechainiC; ?> - <?php echo $fechafinC; ?> //
        </th>
    </tr>
</table>
<hr>
<br>
<?= GridView::widget([
    'dataProvider' => $dataProvider2,
    'columns' => [
        [
            'attribute' => 'PCRC',
            'value' => 'arboles.name',
        ],
        [
            'attribute' => 'Dimensiones',
            'value' => 'dimensions',
        ],
        [
            'attribute' => 'Metas',
            'value' => 'cant_valor',
        ],
    ],
]);

?>
<br>
<br>
<?php
$txtcorte = Yii::$app->db->createCommand("select idtc from tbl_tipocortes where tipocortetc like '$varNametc' and anulado = 0")->queryScalar();

$txtcorte1 = Yii::$app->db->createCommand("select diastcs from tbl_tipos_cortes where cortetcs = 'Corte 1' and idtc = '$txtcorte'")->queryScalar();
$txtfechasini1 = Yii::$app->db->createCommand("select fechainiciotcs from tbl_tipos_cortes where idtc = '$txtcorte' and diastcs like '%$txtcorte1%'")->queryScalar();
$txtfechaini1 = $txtfechasini1 . " 00:00:00";
$txtfechasfin1 = Yii::$app->db->createCommand("select fechafintcs from tbl_tipos_cortes where idtc = '$txtcorte' and diastcs like '%$txtcorte1%'")->queryScalar();
$txtfechafin1 = $txtfechasfin1 . " 23:59:59";

$txtcorte2 = Yii::$app->db->createCommand("select diastcs from tbl_tipos_cortes where cortetcs = 'Corte 2' and idtc = '$txtcorte'")->queryScalar();
$txtfechasini2 = Yii::$app->db->createCommand("select fechainiciotcs from tbl_tipos_cortes where idtc = '$txtcorte' and diastcs like '%$txtcorte2%'")->queryScalar();
$txtfechaini2 = $txtfechasini2 . " 00:00:00";
$txtfechasfin2 = Yii::$app->db->createCommand("select fechafintcs from tbl_tipos_cortes where idtc = '$txtcorte' and diastcs like '%$txtcorte2%'")->queryScalar();
$txtfechafin2 = $txtfechasfin2 . " 23:59:59";

$txtcorte3 = Yii::$app->db->createCommand("select diastcs from tbl_tipos_cortes where cortetcs = 'Corte 3' and idtc = '$txtcorte'")->queryScalar();
$txtfechasini3 = Yii::$app->db->createCommand("select fechainiciotcs from tbl_tipos_cortes where idtc = '$txtcorte' and diastcs like '%$txtcorte3%'")->queryScalar();
$txtfechaini3 = $txtfechasini3 . " 00:00:00";
$txtfechasfin3 = Yii::$app->db->createCommand("select fechafintcs from tbl_tipos_cortes where idtc = '$txtcorte' and diastcs like '%$txtcorte3%'")->queryScalar();
$txtfechafin3 = $txtfechasfin3 . " 23:59:59";

$txtcorte4 = Yii::$app->db->createCommand("select diastcs from tbl_tipos_cortes where cortetcs = 'Corte 4' and idtc = '$txtcorte'")->queryScalar();
$txtfechasini4 = Yii::$app->db->createCommand("select fechainiciotcs from tbl_tipos_cortes where idtc = '$txtcorte' and diastcs like '%$txtcorte4%'")->queryScalar();
$txtfechaini4 = $txtfechasini4 . " 00:00:00";
$txtfechasfin4 = Yii::$app->db->createCommand("select fechafintcs from tbl_tipos_cortes where idtc = '$txtcorte' and diastcs like '%$txtcorte4%'")->queryScalar();
$txtfechafin4 = $txtfechasfin4 . " 23:59:59";
?>
<table class="text-center" border="1" class="egt table table-hover table-striped table-bordered">
    <caption>Tabla datos</caption>
    <thead>
        <tr>
            <th scope="col" class="text-center"> </th>
            <th scope="col" class="text-center" colspan="3"><?php echo $txtcorte1; ?></th>
            <th scope="col" class="text-center" colspan="3"><?php echo $txtcorte2; ?></th>
            <th scope="col" class="text-center" colspan="3"><?php echo $txtcorte3; ?></th>
            <th scope="col" class="text-center" colspan="3"><?php echo $txtcorte4; ?></th>
        </tr>
        <tr>
            <th scope="col"><?= Yii::t('app', 'PCRC') ?></th>
            <th scope="col"><?= Yii::t('app', 'Meta') ?></th>
            <th scope="col"><?= Yii::t('app', 'Realizadas') ?></th>
            <th scope="col"><?= Yii::t('app', '% Cumplimiento') ?></th>
            <th scope="col"><?= Yii::t('app', 'Meta') ?></th>
            <th scope="col"><?= Yii::t('app', 'Realizadas') ?></th>
            <th scope="col"><?= Yii::t('app', '% Cumplimiento') ?></th>
            <th scope="col"><?= Yii::t('app', 'Meta') ?></th>
            <th scope="col"><?= Yii::t('app', 'Realizadas') ?></th>
            <th scope="col"><?= Yii::t('app', '% Cumplimiento') ?></th>
            <th scope="col"><?= Yii::t('app', 'Meta') ?></th>
            <th scope="col"><?= Yii::t('app', 'Realizadas') ?></th>
            <th scope="col"><?= Yii::t('app', '% Cumplimiento') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $variable = $dataProvider2->getModels();

        $txtcant = null;
        foreach ($variable as $key => $value) {
            $arbolId = $value["arbol_id"];
            $arbolName = Yii::$app->db->createCommand('select name from tbl_arbols where id =' . $arbolId . '')->queryScalar();

            $varMetas = $value["cant_valor"];
            $varrtaMeta = round($varMetas / 4);

            $vardimension = $value["dimensions"];
            $iddimensions = Yii::$app->db->createCommand("select id from tbl_dimensions where name like '%$vardimension%'")->queryScalar();

            $querys1 =  new Query;
            $querys1->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
                ->from('tbl_ejecucionformularios')
                ->join(
                    'LEFT OUTER JOIN',
                    'tbl_usuarios',
                    'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id'
                )
                ->where(['between', 'tbl_ejecucionformularios.created', $txtfechaini1, $txtfechafin1])
                ->andwhere(['in', 'tbl_ejecucionformularios.dimension_id', [$iddimensions]])
                ->andwhere(['in', 'tbl_ejecucionformularios.arbol_id', [$arbolId]])
                ->andwhere('tbl_usuarios.usua_id = ' . $idValorador . '');
            $command1 = $querys1->createCommand();
            $queryss1 = $command1->queryAll();

            $query1 = count($queryss1);

            if ($varrtaMeta != 0) {
                $varContar1 = round(($query1 / $varrtaMeta) * 100);
            } else {
                $varContar1 = 0;
            }

            $querys2 =  new Query;
            $querys2->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
                ->from('tbl_ejecucionformularios')
                ->join(
                    'LEFT OUTER JOIN',
                    'tbl_usuarios',
                    'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id'
                )
                ->where(['between', 'tbl_ejecucionformularios.created', $txtfechaini2, $txtfechafin2])
                ->andwhere(['in', 'tbl_ejecucionformularios.dimension_id', [$iddimensions]])
                ->andwhere(['in', 'tbl_ejecucionformularios.arbol_id', [$arbolId]])
                ->andwhere('tbl_usuarios.usua_id = ' . $idValorador . '');
            $command2 = $querys2->createCommand();
            $queryss2 = $command2->queryAll();

            $query2 = count($queryss2);
            if ($varrtaMeta != 0) {
                $varContar2 = round(($query2 / $varrtaMeta) * 100);
            } else {
                $varContar2 = 0;
            }

            $querys3 =  new Query;
            $querys3->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
                ->from('tbl_ejecucionformularios')
                ->join(
                    'LEFT OUTER JOIN',
                    'tbl_usuarios',
                    'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id'
                )
                ->where(['between', 'tbl_ejecucionformularios.created', $txtfechaini3, $txtfechafin3])
                ->andwhere(['in', 'tbl_ejecucionformularios.dimension_id', [$iddimensions]])
                ->andwhere(['in', 'tbl_ejecucionformularios.arbol_id', [$arbolId]])
                ->andwhere('tbl_usuarios.usua_id = ' . $idValorador . '');
            $command3 = $querys3->createCommand();
            $queryss3 = $command3->queryAll();

            $query3 = count($queryss3);
            if ($varrtaMeta != 0) {
                $varContar3 = round(($query3 / $varrtaMeta) * 100);
            } else {
                $varContar3 = 0;
            }

            $querys4 =  new Query;
            $querys4->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
                ->from('tbl_ejecucionformularios')
                ->join(
                    'LEFT OUTER JOIN',
                    'tbl_usuarios',
                    'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id'
                )
                ->where(['between', 'tbl_ejecucionformularios.created', $txtfechaini4, $txtfechafin4])
                ->andwhere(['in', 'tbl_ejecucionformularios.dimension_id', [$iddimensions]])
                ->andwhere(['in', 'tbl_ejecucionformularios.arbol_id', [$arbolId]])
                ->andwhere('tbl_usuarios.usua_id = ' . $idValorador . '');
            $command4 = $querys4->createCommand();
            $queryss4 = $command4->queryAll();

            $query4 = count($queryss4);
            if ($varrtaMeta != 0) {
                $varContar4 = round(($query4 / $varrtaMeta) * 100);
            } else {
                $varContar4 = 0;
            }

            $txtcant = $txtcant + 1;

        ?>
            <tr>
                <td><?= $arbolName; ?></td>
                <td><?= $varrtaMeta; ?></td>
                <td><?= $query1; ?></td>
                <td><?= $varContar1; ?> %</td>
                <td><?= $varrtaMeta; ?></td>
                <td><?= $query2; ?></td>
                <td><?= $varContar2; ?> %</td>
                <td><?= $varrtaMeta; ?></td>
                <td><?= $query3; ?></td>
                <td><?= $varContar3; ?> %</td>
                <td><?= $varrtaMeta; ?></td>
                <td><?= $query4; ?></td>
                <td><?= $varContar4; ?> %</td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?= Html::a('Justificar Rendimiento',  ['justificar', 'nomVar' => $idValorador], [
    'class' => 'btn btn-success',
    'style' => 'background-color: #a94444',
    'data-toggle' => 'tooltip',
    'title' => 'Justificar Rendimiento'
])
?>

<button class="btn btn-info" style="background-color: #4298B4" onclick="exportTableToExcel('tblData', 'Detalle Vista Valorador')">Exportar a Excel</button>

<div style="display: none;">
    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
    <caption>Tabla datos</caption>
        <thead>
            <tr>
                <th scope="col"><?= Yii::t('app', 'Valorador') ?></th>
                <th scope="col"><?= Yii::t('app', 'Rango de Corte') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $nomValorador; ?></td>
                <td><?php echo $varNametc; ?> \\ <?php echo $fechainiC; ?> - <?php echo $fechafinC; ?> //</td>
            </tr>
            <tr>
                <th scope="col"><?= Yii::t('app', 'PCRC') ?></th>
                <th scope="col"><?= Yii::t('app', 'Dimensiones') ?></th>
                <th scope="col"><?= Yii::t('app', 'Metas') ?></th>
            </tr>
            <?php
            $variable = $dataProvider2->getModels();
            foreach ($variable as $key => $value) {
                $arbolId = $value["arbol_id"];
                $arbolName = Yii::$app->db->createCommand('select name from tbl_arbols where id =' . $arbolId . '')->queryScalar();
            ?>
                <tr>
                    <td><?php echo $arbolName; ?></td>
                    <td><?= $value["dimensions"]; ?></td>
                    <td><?= $value["cant_valor"]; ?></td>
                </tr>
            <?php } ?>
            <tr>
                <th scope="col" class="text-center"><?= Yii::t('app', '') ?></th>
                <th scope="col" class="text-center" colspan="3"><?= Yii::t('app', 'Corte 1') ?></th>
                <th scope="col" class="text-center" colspan="3"><?= Yii::t('app', 'Corte 2') ?></th>
                <th scope="col" class="text-center" colspan="3"><?= Yii::t('app', 'Corte 3') ?></th>
                <th scope="col" class="text-center" colspan="3"><?= Yii::t('app', 'Corte 4') ?></th>
            </tr>
            <tr>
                <td class="text-center"></td>
                <td class="text-center" colspan="3"><?php echo $txtcorte1; ?></td>
                <td class="text-center" colspan="3"><?php echo $txtcorte2; ?></td>
                <td class="text-center" colspan="3"><?php echo $txtcorte3; ?></td>
                <td class="text-center" colspan="3"><?php echo $txtcorte4; ?></td>
            </tr>
            <tr>
                <th scope="col"><?= Yii::t('app', 'PCRC') ?></th>
                <th scope="col"><?= Yii::t('app', 'Meta') ?></th>
                <th scope="col"><?= Yii::t('app', 'Realizadas') ?></th>
                <th scope="col"><?= Yii::t('app', '% Cumplimiento') ?></th>
                <th scope="col"><?= Yii::t('app', 'Meta') ?></th>
                <th scope="col"><?= Yii::t('app', 'Realizadas') ?></th>
                <th scope="col"><?= Yii::t('app', '% Cumplimiento') ?></th>
                <th scope="col"><?= Yii::t('app', 'Meta') ?></th>
                <th scope="col"><?= Yii::t('app', 'Realizadas') ?></th>
                <th scope="col"><?= Yii::t('app', '% Cumplimiento') ?></th>
                <th scope="col"><?= Yii::t('app', 'Meta') ?></th>
                <th scope="col"><?= Yii::t('app', 'Realizadas') ?></th>
                <th scope="col"><?= Yii::t('app', '% Cumplimiento') ?></th>
            </tr>
            <?php
            $variable = $dataProvider2->getModels();
            $txtcant = null;
            foreach ($variable as $key => $value) {
                $arbolId = $value["arbol_id"];
                $arbolName = Yii::$app->db->createCommand('select name from tbl_arbols where id =' . $arbolId . '')->queryScalar();

                $varMetas = $value["cant_valor"];
                $varrtaMeta = round($varMetas / 4);

                $vardimension = $value["dimensions"];
                $iddimensions = Yii::$app->db->createCommand("select id from tbl_dimensions where name like '%$vardimension%'")->queryScalar();

                $querys1 =  new Query;
                $querys1->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
                    ->from('tbl_ejecucionformularios')
                    ->join(
                        'LEFT OUTER JOIN',
                        'tbl_usuarios',
                        'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id'
                    )
                    ->where(['between', 'tbl_ejecucionformularios.created', $txtfechaini1, $txtfechafin1])
                    ->andwhere(['in', 'tbl_ejecucionformularios.dimension_id', [$iddimensions]])
                    ->andwhere(['in', 'tbl_ejecucionformularios.arbol_id', [$arbolId]])
                    ->andwhere('tbl_usuarios.usua_id = ' . $idValorador . '');
                $command1 = $querys1->createCommand();
                $queryss1 = $command1->queryAll();

                $query1 = count($queryss1);

                if ($varrtaMeta != 0) {
                    $varContar1 = round(($query1 / $varrtaMeta) * 100);
                } else {
                    $varContar1 = 0;
                }



                $querys2 =  new Query;
                $querys2->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
                    ->from('tbl_ejecucionformularios')
                    ->join(
                        'LEFT OUTER JOIN',
                        'tbl_usuarios',
                        'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id'
                    )
                    ->where(['between', 'tbl_ejecucionformularios.created', $txtfechaini2, $txtfechafin2])
                    ->andwhere(['in', 'tbl_ejecucionformularios.dimension_id', [$iddimensions]])
                    ->andwhere(['in', 'tbl_ejecucionformularios.arbol_id', [$arbolId]])
                    ->andwhere('tbl_usuarios.usua_id = ' . $idValorador . '');
                $command2 = $querys2->createCommand();
                $queryss2 = $command2->queryAll();

                $query2 = count($queryss2);

                if ($varrtaMeta != 0) {
                    $varContar2 = round(($query2 / $varrtaMeta) * 100);
                } else {
                    $varContar2 = 0;
                }



                $querys3 =  new Query;
                $querys3->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
                    ->from('tbl_ejecucionformularios')
                    ->join(
                        'LEFT OUTER JOIN',
                        'tbl_usuarios',
                        'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id'
                    )
                    ->where(['between', 'tbl_ejecucionformularios.created', $txtfechaini3, $txtfechafin3])
                    ->andwhere(['in', 'tbl_ejecucionformularios.dimension_id', [$iddimensions]])
                    ->andwhere(['in', 'tbl_ejecucionformularios.arbol_id', [$arbolId]])
                    ->andwhere('tbl_usuarios.usua_id = ' . $idValorador . '');
                $command3 = $querys3->createCommand();
                $queryss3 = $command3->queryAll();

                $query3 = count($queryss3);

                if ($varrtaMeta != 0) {
                    $varContar3 = round(($query3 / $varrtaMeta) * 100);
                } else {
                    $varContar3 = 0;
                }



                $querys4 =  new Query;
                $querys4->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
                    ->from('tbl_ejecucionformularios')
                    ->join(
                        'LEFT OUTER JOIN',
                        'tbl_usuarios',
                        'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id'
                    )
                    ->where(['between', 'tbl_ejecucionformularios.created', $txtfechaini4, $txtfechafin4])
                    ->andwhere(['in', 'tbl_ejecucionformularios.dimension_id', [$iddimensions]])
                    ->andwhere(['in', 'tbl_ejecucionformularios.arbol_id', [$arbolId]])
                    ->andwhere('tbl_usuarios.usua_id = ' . $idValorador . '');
                $command4 = $querys4->createCommand();
                $queryss4 = $command4->queryAll();

                $query4 = count($queryss4);

                if ($varrtaMeta != 0) {
                    $varContar4 = round(($query4 / $varrtaMeta) * 100);
                } else {
                    $varContar4 = 0;
                }


                $txtcant = $txtcant + 1;

            ?>
                <tr>
                    <td><?= $arbolName; ?></td>
                    <td><?= $varrtaMeta; ?></td>
                    <td><?= $query1; ?></td>
                    <td><?= $varContar1; ?> %</td>
                    <td><?= $varrtaMeta; ?></td>
                    <td><?= $query2; ?></td>
                    <td><?= $varContar2; ?> %</td>
                    <td><?= $varrtaMeta; ?></td>
                    <td><?= $query3; ?></td>
                    <td><?= $varContar3; ?> %</td>
                    <td><?= $varrtaMeta; ?></td>
                    <td><?= $query4; ?></td>
                    <td><?= $varContar4; ?> %</td>
                </tr>
            <?php } ?>
        </tbody>

    </table>
</div>

<script type="text/javascript">
    function exportTableToExcel(tableID, filename = '') {
        var downloadLink;
        var dataType = 'application/vnd.ms-excel';
        var tableSelect = document.getElementById(tableID);
        var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

        // Specify file name
        filename = filename ? filename + '.xls' : 'excel_data.xls';

        // Create download link element
        downloadLink = document.createElement("a");

        document.body.appendChild(downloadLink);

        if (navigator.msSaveOrOpenBlob) {
            var blob = new Blob(['\ufeff', tableHTML], {
                type: dataType
            });
            navigator.msSaveOrOpenBlob(blob, filename);
        } else {
            // Create a link to the file
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

            // Setting the file name
            downloadLink.download = filename;

            //triggering the function
            downloadLink.click();
        }
    }
</script>