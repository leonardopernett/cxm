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

    
    $varBeginYear = '2019-01-01';
    $varLastYear = '2025-12-31';   
    
    $varMonthYear = Yii::$app->db->createCommand("select concat_ws('- ', CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1)) as CorteTipo from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();

    $varListCorte = array();
    foreach ($varMonthYear as $key => $value) {
        $varListCort = $value['CorteTipo'];

        array_push($varListCorte, $varListCort);
    }


    $varListMonth = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a   where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();


    $varListMeses = array();
    foreach ($varListMonth as $key => $value) {
        $varListYear = $value['mesyear'];  

        $txtQuery1 =  new Query;
        $txtQuery1   ->select(['sum(tbl_control_volumenxcliente.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxcliente')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                               'tbl_control_volumenxcliente.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id in (2)')
                    ->andwhere(['between','tbl_control_volumenxcliente.mesyear', $varListYear, $varListYear]);                    
        $command1 = $txtQuery1->createCommand();
        $txtTotalMonth1 = $command1->queryScalar();

        $txtQuery2 =  new Query;
        $txtQuery2   ->select(['sum(tbl_control_volumenxclienteS.cantidadvalorS)'])->distinct()
                    ->from('tbl_control_volumenxclienteS')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                               'tbl_control_volumenxclienteS.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id in (2)')
                    ->andwhere(['between','tbl_control_volumenxclienteS.mesyear', $varListYear, $varListYear]);                    
        $command2 = $txtQuery2->createCommand();
        $txtTotalMonth2 = $command2->queryScalar(); 

        $txtTotalMonth = $txtTotalMonth1 + $txtTotalMonth2;      

        array_push($varListMeses, $txtTotalMonth);
    }                  

?>


<div class="capaCero" style="display: inline">
	<table class="table table-striped table-bordered detail-view formDinamico" border="0">
    <caption>Volumen</caption>
		<thead>
            <tr>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Volúmen x Cliente') ?></th>
            </tr>			
		</thead>
		<tbody>
            <tr>
                <td>
                    <div id="containerMed" style="height: 400px; margin-top: 1em"></div>
                </td>
            </tr>			
		</tbody>
	</table>
</div>


<script type="text/javascript">

    $(function() {

        var Listado = "<?php echo implode($varListCorte,",");?>";
        Listado = Listado.split(",");
        console.log(Listado);

          $('#containerMed').highcharts({
            chart: {
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Cantidad Valoraciones Realizadas Nivel Medellin'
              }
            },     

            title: {
              text: 'Detalle en Grafica -- Nivel Medellin --'
            },

            xAxis: {
                  categories: Listado,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Cantidad ',
              data: [<?= implode($varListMeses, ',')?>]
            }]
          });

          Highcharts.getOptions().exporting.buttons.contextButton.menuItems.push({
            text: 'Additional Button',
            onclick: function() {
              alert('OK');
              /*call custom function here*/
            }
          });
    });    
</script>