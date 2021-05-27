<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\Dashboardcategorias;

$this->title = 'Dashboard -- VOC --';
$this->params['breadcrumbs'][] = $this->title;

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

    // var_dump($varCantidadllamadas);
    // var_dump($varArbol_idV);
    // var_dump($varParametros_idV);
    // var_dump($varCodparametrizar);
    // var_dump($varFechaI);
    // var_dump($varFechaF);

    $txtvDatosMotivos = "select distinct sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 3 and sc.cod_pcrc in ('$varCodigPcrc') and sc.programacategoria in ('$varArbol_idV')";

    if ($varCodparametrizar == 1) {
        $txtvDatosMotivos = Yii::$app->db->createCommand($txtvDatosMotivos." and sp.rn in ('$varParametros_idV')")->queryAll();
    }else{
        if ($varCodparametrizar == 2) {
            $txtvDatosMotivos = Yii::$app->db->createCommand($txtvDatosMotivos." and sp.ext in ('$varParametros_idV')")->queryAll();
        }else{
            $txtvDatosMotivos = Yii::$app->db->createCommand($txtvDatosMotivos." and sp.usuared in ('$varParametros_idV')")->queryAll();
        }
    }

    $varMotivos = null;
    $varIdCatagoria = null;
    $txtvCantMotivos = null;
    $txtParticipación2 = null;
    $txtvCantSeg2 = null;
    $txtcoincidencia1 = null;
    $txtcoincidencia = null;
    $txtRtaVar = null;
    $txtRtaVariable = null;
    $txtTotalLlamadas = $varCantidadllamadas;
    $varListName =  array();
    $varListDuracion = array();
    $varListCantidad = array();
    foreach ($txtvDatosMotivos as $key => $value) {
        $varMotivos = $value['nombre'];               
        $varIdCatagoria = $value['idcategoria'];

        $txtvCantMotivos1 = Yii::$app->db->createCommand("select count(idcategoria) from tbl_dashboardspeechcalls  where idcategoria = '$varIdCatagoria' and servicio in ('$varArbol_idV') and extension in ('$varParametros_idV') and fechallamada between '$varFechaI' and '$varFechaF' and anulado = 0")->queryScalar();
        $txtvCantMotivos = intval($txtvCantMotivos1);

        if ($txtvCantMotivos != 0 && $txtTotalLlamadas != 0) {
            $txtParticipación2 = round(($txtvCantMotivos / $txtTotalLlamadas) * 100);
        }else{
            $txtParticipación2 = 0;
        } 

        $txtvCantSeg2 = Yii::$app->db->createCommand("select AVG(callduracion) from tbl_dashboardspeechcalls   where idcategoria = '$varIdCatagoria' and servicio in ('$varArbol_idV') and extension in ('$varParametros_idV') and fechallamada between '$varFechaI' and '$varFechaF' and anulado = 0")->queryScalar(); 
        
        array_push($varListName, $varMotivos);
        array_push($varListDuracion, round($txtvCantSeg2));
        array_push($varListCantidad, $txtvCantMotivos);
    }
?>
<div id="containerTMO" class="highcharts-container" style="height: 400px;">    
</div>

<script type="text/javascript">
    $(function() {
        var Listado = "<?php echo implode($varListName,",");?>";
        Listado = Listado.split(",");

        Highcharts.setOptions({
                lang: {
                  numericSymbols: null,
                  thousandsSep: ','
                }
        });

        $('#containerTMO').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'column'
            },

            yAxis: {
              title: {
                text: 'Cantidad de Llamadas'
              }
            }, 

            title: {
              text: '',
            style: {
                    color: '#3C74AA'
              }

            },

            xAxis: {
                  categories: Listado,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Cantidad de llamadas',
              data: [<?= join($varListCantidad, ',')?>],
              color: '#4298B5'
            },{
              name: 'Duración de llamadas',
              data: [<?= join($varListDuracion, ',')?>],
              color: '#FFc72C',
              type: 'line'
            }],


            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }

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