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
use miloschuman\highcharts\Highcharts;

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
    $varLastYear = '2020-12-31';
    $varMonthYear = Yii::$app->db->createCommand("select concat_ws('- ', CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1)) as CorteTipo from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();

    $varListCorte = array();
    foreach ($varMonthYear as $key => $value) {
        $varListCort = $value['CorteTipo'];

        array_push($varListCorte, $varListCort);
    }


    $varListMonth = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a   where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();

    $txtTotalMonthS = 0;
    foreach ($varListMonth as $key => $value) {
      $varListYear1 = $value['mesyear'];

      $txtQuery2 =  new Query;
        $txtQuery2   ->select(['sum(tbl_control_volumenxclienteS.cantidadvalorS)'])->distinct()
                    ->from('tbl_control_volumenxclienteS')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                               'tbl_control_volumenxclienteS.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id in (2, 98)')
                    ->andwhere(['between','tbl_control_volumenxclienteS.mesyear', $varListYear1, $varListYear1]);                    
        $command2 = $txtQuery2->createCommand();
        $txtTotalMonth2 = $command2->queryScalar();  

        $txtTotalMonthS =  $txtTotalMonthS + $txtTotalMonth2; 
    }

    $txtRtaProcentaje = 0;
    $varListMeses = array();
    $varListMeses1 = array();
    $varListMeses2 = array();
    foreach ($varListMonth as $key => $value) {
        $varListYear = $value['mesyear'];  

        $txtQuery1 =  new Query;
        $txtQuery1   ->select(['sum(tbl_control_volumenxcliente.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxcliente')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                               'tbl_control_volumenxcliente.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id in (2, 98)')
                    ->andwhere(['between','tbl_control_volumenxcliente.mesyear', $varListYear, $varListYear]);                    
        $command1 = $txtQuery1->createCommand();
        $txtTotalMonth1 = $command1->queryScalar();   

        $txtQuery2 =  new Query;
        $txtQuery2   ->select(['sum(tbl_control_volumenxclienteS.cantidadvalorS)'])->distinct()
                    ->from('tbl_control_volumenxclienteS')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                               'tbl_control_volumenxclienteS.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id in (2, 98)')
                    ->andwhere(['between','tbl_control_volumenxclienteS.mesyear', $varListYear, $varListYear]);                    
        $command2 = $txtQuery2->createCommand();
        $txtTotalMonth2 = $command2->queryScalar();  

        $txtTotalMonth = $txtTotalMonth1 + $txtTotalMonth2; 
	$txtTotalMonthV =  $txtTotalMonth1 + $txtTotalMonth2;

		if ($txtTotalMonth2  != 0 || $txtTotalMonthS != 0) {
			$txtRtaProcentaje = round(($txtTotalMonth2 * 100) /  $txtTotalMonthS,2);
		}else{
			$txtRtaProcentaje = 0;
		}

        

        array_push($varListMeses, $txtTotalMonth);
        array_push($varListMeses1, $txtTotalMonthV);
        array_push($varListMeses2, $txtRtaProcentaje);
    }
    //var_dump($varListMeses);

    $vaListPeople = array();
    foreach ($varListMonth as $key => $value) {
        $varListYear2 = $value['mesyear'];

        $txtQuery2 =  new Query;
        $txtQuery2   ->select(['sum(tbl_control_volumenxvalorador.totalrealizadas)'])->distinct()
                    ->from('tbl_control_volumenxvalorador')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                               'tbl_control_volumenxvalorador.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id in (2, 98)')
                    ->andwhere(['between','tbl_control_volumenxvalorador.mesyear', $varListYear2, $varListYear2]);                    
        $command2 = $txtQuery2->createCommand();
        $txtTotalMonth2 = $command2->queryScalar(); 

        array_push($vaListPeople, $txtTotalMonth2);         
    }

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<div class="row">
  <div class="col-md-3 well" style="margin-right: 10px; text-align: center;">
    <div onclick="verMetricasP();" class="btn btn-primary" style="display:inline; width:70px; height:25px; background-color: #707372" method='post' id="botones1" >
          Métricas de Productividad
      </div> 
  </div>
  <div class="col-md-3 well" style="margin-right: 10px; text-align: center;">
    <div onclick="verMetricasS();" class="btn btn-primary" style="display:inline; width:70px; height:25px; background-color: #707372" method='post' id="botones2" >
          Métricas de Seguimiento
      </div> 
  </div>
</div>

<div class="capaMetricas" id="metricasID" style="display: none">
<br>
<div class="page-header" >
    <h3><center>Métricas de Productividad/Valoración</center></h3>
</div> 

<div class="capaCero" style="display: inline">
    <?= Html::button('Tablero de Control', ['value' => url::to(['tablerocontrol']), 'class' => 'btn btn-success', 
        'id'=>'modalButton1',
        'data-toggle' => 'tooltip',
        'title' => 'Tablero de Control', 
        'style' => 'background-color: #337ab7']) ?> 

  <?php
    Modal::begin([
      'header' => '<h4>Tablero de Control... </h4>',
      'id' => 'modal1',
      'size' => 'modal-lg',
    ]);

    echo "<div id='modalContent1'></div>";
                                  
    Modal::end(); 
  ?>   
</div>

<?php
    if ($sessiones == '7' || $sessiones == '1525' || $sessiones == '2953' || $sessiones == '3205' || $sessiones == '223' || $sessiones == '3205') {
?>
        <div class="capaUno" style="display: inline">

                <?= Html::button('Parametrizar Datos', ['value' => url::to(['parametrizardatos']), 'class' => 'btn btn-success', 
                    'id'=>'modalButton2',
                    'data-toggle' => 'tooltip',
                    'title' => 'Parametrizar Datos', 
                    'style' => 'background-color: #337ab7']) 
                ?> 

                <?php
                    Modal::begin([
                      'header' => '<h4>Paramaterizar Datos </h4>',
                      'id' => 'modal2',
                      //'size' => 'modal-lg',
                    ]);

                    echo "<div id='modalContent2'></div>";
                                                  
                    Modal::end(); 
                ?>  
        </div>
<?php
    }
?>

<div class="capaTres" style="display: inline">
    <?= 
      Html::button('Descarga de Datos', ['value' => url::to(['datosmetricas']), 'class' => 'btn btn-success', 
                    'id'=>'modalButton3',
                    'data-toggle' => 'tooltip',
                    'title' => 'Descarga de Datos', 
                    'style' => 'background-color: #4298b4']) 
    ?> 

    <?php
      Modal::begin([
                      'header' => '<h4>Procesando datos en el archivo de excel... </h4>',
                      'id' => 'modal3',
                      'size' => 'modal-lg',
                    ]);

      echo "<div id='modalContent3'></div>";
                                                  
      Modal::end(); 
    ?>   
</div>

<hr>

<div  class="col-md-12">
    <div class="row seccion-data">
      <div class="col-md-10">
        <label class="labelseccion">
          KONECTA -- VOLUMEN X CLIENTE (QA + SPEECH)
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
<div id="capaKonecta" class="col-sm-12" style="display: none">
    <table class="table table-striped table-bordered detail-view formDinamico" border="0">
        <thead>
            <tr>
                <th class="text-center"><?= Yii::t('app', 'Volúmen x Cliente (QA + Speech)') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div id="container" style="height: 400px; margin-top: 1em"></div>
                </td>
            </tr>
            <tr>
                <td>
                    <?= Html::button('Medellin', ['value' => url::to(['medellinvolumencliente']), 'class' => 'btn btn-success', 
                        'id'=>'modalButton7',
                        'data-toggle' => 'tooltip',
                        'title' => 'Nivel Medellín', 
                        'style' => 'background-color: #337ab7']) ?> 

                  <?php
                    Modal::begin([
                      'header' => '<h4>Nivel Medellín... </h4>',
                      'id' => 'modal7',
                      'size' => 'modal-lg',
                    ]);

                    echo "<div id='modalContent7'></div>";
                                                  
                    Modal::end(); 
                  ?>  
                    <?= Html::button('Bogotá', ['value' => url::to(['bogotavolumencliente']), 'class' => 'btn btn-success', 
                        'id'=>'modalButton5',
                        'data-toggle' => 'tooltip',
                        'title' => 'Nivel Bogotá', 
                        'style' => 'background-color: #337ab7']) ?> 

                    <?php
                        Modal::begin([
                          'header' => '<h4>Nivel Bogotá... </h4>',
                          'id' => 'modal5',
                          'size' => 'modal-lg',
                        ]);

                        echo "<div id='modalContent5'></div>";
                                                      
                        Modal::end(); 
                    ?>
                         
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div  class="col-md-12">
    <div class="row seccion-data">
      <div class="col-md-10">
        <label class="labelseccion">
          KONECTA -- COSTO X CLIENTE
        </label>      
      </div>    
      <div class="col-md-2">
        <?=
          Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                      "class" => "glyphicon glyphicon-chevron-downForm",
                                  ]) . "", "javascript:void(0)"
                                  , ["class" => "openSeccion", "id" => "bloqueTwo"])
        ?>
      </div>
      <?php $this->registerJs('$("#bloqueTwo").click(function () {
                                  $("#capaKonecta2").toggle("slow");
                              });'); ?>
    </div>
</div>
<div id="capaKonecta2" class="col-sm-12" style="display: none">
    <table class="table table-striped table-bordered detail-view formDinamico" border="0">
        <thead>
            <tr>
                <th class="text-center"><?= Yii::t('app', 'Costo x Cliente') ?></th>
            </tr>            
        </thead>
        <tbody>
            
        </tbody>
    </table>
</div>

<div  class="col-md-12">
    <div class="row seccion-data">
      <div class="col-md-10">
        <label class="labelseccion">
          KONECTA -- VOLUMEN X VALORADORES
        </label>      
      </div>    
      <div class="col-md-2">
        <?=
          Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                      "class" => "glyphicon glyphicon-chevron-downForm",
                                  ]) . "", "javascript:void(0)"
                                  , ["class" => "openSeccion", "id" => "bloqueThree"])
        ?>
      </div>
      <?php $this->registerJs('$("#bloqueThree").click(function () {
                                  $("#capaKonecta3").toggle("slow");
                              });'); ?>
    </div>
</div>
<div id="capaKonecta3" class="col-sm-12" style="display: none">

</div>
</div>

<div class="capaSeguimientos" id="seguimientoID" style="display: none">
<br>
  <div class="page-header" >
      <h3><center>Métricas de Seguimiento/Valoración</center></h3>
  </div> 
  <div  class="col-md-12">
    <div class="row seccion-data">
        <div class="col-md-10">
          <label class="labelseccion">
            KONECTA -- CANTIDAD DE VALORACIONES
          </label>      
        </div>    
        <div class="col-md-2">
          <?=
            Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                        "class" => "glyphicon glyphicon-chevron-downForm",
                                    ]) . "", "javascript:void(0)"
                                    , ["class" => "openSeccion", "id" => "bloqueFour"])
          ?>
        </div>
        <?php $this->registerJs('$("#bloqueFour").click(function () {
                                    $("#capaKonecta4").toggle("slow");
                                });'); ?>
    </div>
  </div>
  <div id="capaKonecta4" class="col-sm-12" style="display: none">
    <div id="container1" style="height: 400px; margin-top: 1em"></div>
  </div>  

  <div  class="col-md-12">
    <div class="row seccion-data">
        <div class="col-md-10">
          <label class="labelseccion">
            KONECTA -- AVANCE AUTOMATIZACION
          </label>      
        </div>    
        <div class="col-md-2">
          <?=
            Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                        "class" => "glyphicon glyphicon-chevron-downForm",
                                    ]) . "", "javascript:void(0)"
                                    , ["class" => "openSeccion", "id" => "bloqueFive"])
          ?>
        </div>
        <?php $this->registerJs('$("#bloqueFive").click(function () {
                                    $("#capaKonecta5").toggle("slow");
                                });'); ?>
    </div>
  </div>
  <div id="capaKonecta5" class="col-sm-12" style="display: none">
    <div id="container2" style="height: 400px; margin-top: 1em"></div>
  </div>  

  <div  class="col-md-12">
    <div class="row seccion-data">
        <div class="col-md-10">
          <label class="labelseccion">
            KONECTA -- CONTROL DE PLANTA
          </label>      
        </div>    
        <div class="col-md-2">
          <?=
            Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                        "class" => "glyphicon glyphicon-chevron-downForm",
                                    ]) . "", "javascript:void(0)"
                                    , ["class" => "openSeccion", "id" => "bloqueSix"])
          ?>
        </div>
        <?php $this->registerJs('$("#bloqueSix").click(function () {
                                    $("#capaKonecta6").toggle("slow");
                                });'); ?>
    </div>
  </div>
  <div id="capaKonecta6" class="col-sm-12" style="display: none">
    
  </div>  

</div>


<script type="text/javascript">

    function verMetricasP(){
      var varMetrica = document.getElementById("metricasID");
      var varBoton1 = document.getElementById("botones1");
      var varSeguimiento = document.getElementById("seguimientoID");
      var varBoton2 = document.getElementById("botones2");

      varMetrica.style.display = 'inline';
      varBoton1.style.backgroundColor = '#4298b4';
      varSeguimiento.style.display = 'none';
      varBoton2.style.backgroundColor = '#707372';
    };

    function verMetricasS(){
      var varMetrica = document.getElementById("metricasID");
      var varBoton1 = document.getElementById("botones1");
      var varSeguimiento = document.getElementById("seguimientoID");
      var varBoton2 = document.getElementById("botones2");

      varMetrica.style.display = 'none';
      varBoton1.style.backgroundColor = '#707372';
      varSeguimiento.style.display = 'inline';
      varBoton2.style.backgroundColor = '#4298b4';
    };

    $(function() {

        var Listado = "<?php echo implode($varListCorte,",");?>";
        Listado = Listado.split(",");
        console.log(Listado);

          Highcharts.setOptions({
                lang: {
                  numericSymbols: null,
                  thousandsSep: ','
                }
          }); 

          $('#container').highcharts({
            chart: {
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Cantidad Valoraciones Realizadas Nivel Konecta'
              }
            },     

            title: {
              text: 'Detalle en Grafica -- Nivel Konecta --'
            },

            xAxis: {
                  categories: Listado,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Cantidad ',
              data: [<?= join($varListMeses, ',')?>]
            }]
          });

          $('#container1').highcharts({
            chart: {
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Cantidad de Valoraciones'
              }
            },     

            title: {
              text: 'Detalle en Grafica -- Cantidad de Valoraciones --'
            },

            xAxis: {
                  categories: Listado,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Totalidad: ',
              data: [<?= join($varListMeses1, ',')?>]
            }]
          });

          $('#container2').highcharts({
            chart: {
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Porcentaje de automatización'
              }
            },     

            title: {
              text: 'Detalle en Grafica -- Avance Automatización --'
            },

            xAxis: {
                  categories: Listado,
                  title: {
                      text: null
                  }
                },

        tooltip: {
           pointFormat: '{series.name}: <b>{point.y}</b> %<br/>'
        },

            series: [{
              name: 'Totalidad: ',
              //data: [<?= join($varListMeses2, ',')?>]      
              data: [<?= join($varListMeses2, ',')?>]
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