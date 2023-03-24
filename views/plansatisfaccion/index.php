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
use app\models\Dashboardcategorias;
use app\models\Dashboardservicios;

$this->title = 'Gestor Plan de Satisfacción';
$this->params['breadcrumbs'][] = $this->title;

  $template = '<div class="col-md-12">'
  . ' {input}{error}{hint}</div>';

  $sessiones = Yii::$app->user->identity->id;

  $rol =  new Query;
  $rol    ->select(['tbl_roles.role_id'])
          ->from('tbl_roles')
          ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                	'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
          ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                  'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
          ->where(['=','tbl_usuarios.usua_id',$sessiones]);                      
  $command = $rol->createCommand();
  $roles = $command->queryScalar();

?>
<style>
  .card1 {
    height: auto;
    width: auto;
   	margin-top: auto;
    margin-bottom: auto;
    background: #FFFFFF;
    position: relative;
    display: flex;
    justify-content: center;
   	flex-direction: column;
    padding: 10px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 5px;    
   	font-family: "Nunito";
    font-size: 150%;    
    text-align: left;    
  }

  .card2 {
    height: 355px;
    width: auto;
    margin-top: auto;
    margin-bottom: auto;
    background: #FFFFFF;
    position: relative;
    display: flex;
    justify-content: center;
    flex-direction: column;
    padding: 10px;
    box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
    -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
    -moz-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
    border-radius: 5px;    
    font-family: "Nunito",sans-serif;
    font-size: 150%;    
    text-align: left;    
  }

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/satisfacioncliente1.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<!-- Data extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<script src="../../js_extensions/chart.min.js"></script>

<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >


<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>

<br>
<br>

<!-- Capa Principal -->
<div id="capaIdPrincipal" class="capaPrincipal" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones & Gráficas') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">

        <?php

        ?>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Crear Plan') ?></label>
                <?= Html::button('Aceptar', ['value' => url::to(['agregarplan']), 'class' => 'btn btn-success', 'id'=>'modalButton',
                                'data-toggle' => 'tooltip',
                                'title' => 'Crear Planes de Satisfacción']) 
                ?> 

                <?php
                    Modal::begin([
                        'header' => '<h4>Seleccionar Información Planes de Satisfacción</h4>',
                        'id' => 'modal',
                        'size' => 'modal-lg',
                    ]);

                    echo "<div id='modalContent'></div>";
                                                                                                  
                    Modal::end(); 
                ?>
            </div>
        </div>

        <?php

        ?>

        <?php

        ?>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Descargar Planes') ?></label>
                <?= Html::button('Aceptar', ['value' => url::to(['descargarplanes']), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                                'data-toggle' => 'tooltip',
                                'title' => 'Descargar Planes de Satisfacción']) 
                ?> 

                <?php
                    Modal::begin([
                        'header' => '<h4>Descargar Planes de Satisfacción</h4>',
                        'id' => 'modal1',
                    ]);

                    echo "<div id='modalContent1'></div>";
                                                                                                  
                    Modal::end(); 
                ?>
            </div>
        </div>

        <?php

        ?>

        <?php
            if($sessiones == "0") {
        ?>

        <div class="col-md-4">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Importar Planes') ?></label>
            </div>
        </div>

        <?php
            }
        ?>

    </div>

</div>

<hr>

<!-- Capa Grafica -->
<div id="capaIdGrafica" class="capaGrafica" style="display: inline;">

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Cantidades en Procesos') ?></label>
                <div id="containerA" class="highcharts-container" style="height: 150px;"></div> 
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Cantidades en Actividades') ?></label>
                <div id="containerB" class="highcharts-container" style="height: 150px;"></div> 
            </div>
        </div>
    </div>

</div>

<hr>

<!-- Capa Resultados -->
<div id="capaIdResultados" class="capaResultados" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Lista de Planes') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Listado de Planes de Satisfacción') ?></label>

                <table id="tblDataPlanes" class="table table-striped table-bordered tblResDetFreed">
                  <caption><?= Yii::t('app', 'Resultados') ?></caption>
                  <thead>
                    <tr>
                      <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Procesos') ?></label></th>
                      <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Actividad') ?></label></th>
                      <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Dirección') ?></label></th>
                      <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Responsable') ?></label></th>
                      <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Rol Responsable') ?></label></th>
                      <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Estado') ?></label></th>
                      <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Anexos') ?></label></th>
                      <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Seguimiento') ?></label></th>
                      <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      foreach ($varListPlanes as $key => $value) { 
                        $varDirecionString = null;

                        if ($value['id_dp_clientes'] != "") {
                            $varDirecionString = (new \yii\db\Query())
                                    ->select([
                                      'tbl_usuarios_evalua.clientearea'
                                    ])
                                    ->from(['tbl_plan_generalsatu'])
                                    ->join('LEFT OUTER JOIN', 'tbl_usuarios_evalua',
                                      'tbl_usuarios_evalua.idusuarioevalua = tbl_plan_generalsatu.id_dp_clientes')
                                    ->where(['=','tbl_plan_generalsatu.anulado',0])
                                    ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$value['id_generalsatu']])
                                    ->scalar();
                        }else{
                            if ($value['id_dp_area'] != "") {
                                
                                $varDirecionString = (new \yii\db\Query())
                                                ->select([
                                                  'tbl_areasapoyo_gptw.nombre'
                                                ])
                                                ->from(['tbl_plan_generalsatu'])
                                                ->join('LEFT OUTER JOIN', 'tbl_areasapoyo_gptw',
                                                  'tbl_areasapoyo_gptw.id_areaapoyo = tbl_plan_generalsatu.id_dp_area')
                                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$value['id_generalsatu']])
                                                ->scalar();
                                
                            }else{
                                $varDirecionString = 'N/A';
                            }
                        }

                             

                        $varRolResponsable = (new \yii\db\Query())
                                ->select([
                                  'tbl_usuarios_jarvis_cliente.posicion'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_usuarios_jarvis_cliente',
                                  'tbl_usuarios_jarvis_cliente.idusuarioevalua = tbl_plan_generalsatu.cc_responsable')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$value['id_generalsatu']])
                                ->scalar();

                        $varAnexos = (new \yii\db\Query())
                                ->select([
                                  'tbl_plan_subirarchivos.id_subirarchivos'
                                ])
                                ->from(['tbl_plan_subirarchivos'])
                                ->where(['=','tbl_plan_subirarchivos.anulado',0])
                                ->andwhere(['=','tbl_plan_subirarchivos.id_generalsatu',$value['id_generalsatu']])
                                ->count();
                    ?>
                      <tr>
                        <td><label style="font-size: 12px;">
                            <?php
                                $varConteoFaltante = (new \yii\db\Query())
                                                ->select([
                                                  '*'
                                                ])
                                                ->from(['tbl_plan_secundariosatu'])
                                                ->where(['=','tbl_plan_secundariosatu.anulado',0])
                                                ->andwhere(['=','tbl_plan_secundariosatu.id_generalsatu',$value['id_generalsatu']])
                                                ->count();

                                if ($varConteoFaltante == 0) {
                              
                            ?>
                                <span class="d-inline-block" tabindex="0" data-toggle="tooltip" data-trigger="hover" title="Información incompleta en el plan de satisfacción actual">
                                    <em class="fas fa-info-circle" style="font-size: 18px; color: #ef7c05;pointer-events: none;"></em>

                                </span>
                            <?php
                                }
                            ?>
                            <?php echo  $value['proceso']; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $value['varActividad']; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $varDirecionString; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $value['nombre_completo']; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $varRolResponsable; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $value['varEstado']; ?></label></td>
                        <td class="text-center">
                            <?php if ($varAnexos != 0) { ?>
                                <label><em class="fas fa-check" style="font-size: 20px; color: #26cd33;"></em><?= Yii::t('app', '') ?></label>
                            <?php }else{ ?>
                                <label><em class="fas fa-times" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', '') ?></label>
                            <?php } ?>
                        </td>
                        <td class="text-center">
                            <?php
                                if ($value['estado'] == 1) {                                
                            ?>
                                    <?= Html::a('<em class="fas fa-plus-square" style="font-size: 12px; color: #d95416;"></em>',  ['agregarsatisfaccion','id_plan'=> $value['id_generalsatu']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Agregar Eficacia']) ?>   
                            <?php
                                }else{
                            ?>    
                                    <label style="font-size: 12px;"><?= Yii::t('app', ' -- ') ?></label>
                            <?php
                                }
                            ?>                      
                        </td>
                        <td class="text-center">
                            <?= Html::a('<em class="fas fa-search" style="font-size: 12px; color: #B833FF;"></em>',  ['verplan','id_plan'=> $value['id_generalsatu']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Ver Plan']) 
                            ?> 

                            <?php
                              if ($value['estado'] == 1) {                                
                            ?>
                                <?= Html::a('<em class="fas fa-edit" style="font-size: 12px; color: #4298B4;"></em>',  ['modificarplan','id_plan'=> $value['id_generalsatu']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Editar Plan']) ?> 
                            <?php
                              }
                            ?>
                        </td>
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

<hr>

<script type="text/javascript">
    $(document).ready( function () {
        $('#tblDataPlanes').DataTable({
          responsive: true,
          fixedColumns: true,
          select: true,
          "language": {
            "lengthMenu": "Cantidad de Datos a Mostrar _MENU_",
            "zeroRecords": "No se encontraron datos ",
            "info": "Mostrando p&aacute;gina _PAGE_ a _PAGES_ de _MAX_ registros",
            "infoEmpty": "No hay datos aun",
            "infoFiltered": "(Filtrado un _MAX_ total)",
            "search": "Buscar:",
            "paginate": {
              "first":      "Primero",
              "last":       "Ultimo",
              "next":       "Siguiente",
              "previous":   "Anterior"
            }
          } 
        });
    });

    Highcharts.chart('containerA', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false
        },
        title: {
            text: '<label style="font-size: 20px;"><?php echo ''; ?></label>',
            align: 'center',
            verticalAlign: 'middle',
            y: 60
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    distance: -50,
                    style: {
                        fontWeight: 'bold',
                        color: 'white'
                    }
                },
                startAngle: -90,
                endAngle: 90,
                center: ['50%', '110%'],
                size: '220%',
                width: '200%'
            }
        },
        series: [{
            type: 'pie',
            name: '',
            innerSize: '50%',
            data: [
                <?php           
                    foreach ($varCantidadProcesos as $key => $value) {
                        $varColor = null;

                        if ($value['id_procesos'] == 1) {
                            $varColor = '#4298B5';
                        }
                        if ($value['id_procesos'] == 2) {
                            $varColor = '#FBCE52';
                        }
                ?>
                    {
                        name: "<?php echo $value['proceso'];?>",
                        y: parseFloat("<?php echo $value['varCantidad'];?>"),
                        color: "<?php echo $varColor; ?>",
                        dataLabels: {
                            enabled: false
                        }
                    },
                <?php 
                    }
                ?>
                        
            ]
        }]
    });

    Highcharts.chart('containerB', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false
        },
        title: {
            text: '<label style="font-size: 20px;"><?php echo ''; ?></label>',
            align: 'center',
            verticalAlign: 'middle',
            y: 60
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    distance: -50,
                    style: {
                        fontWeight: 'bold',
                        color: 'white'
                    }
                },
                startAngle: -90,
                endAngle: 90,
                center: ['50%', '110%'],
                size: '220%',
                width: '200%'
            }
        },
        series: [{
            type: 'pie',
            name: '',
            innerSize: '50%',
            data: [
                <?php           
                    foreach ($varCantidadActividad as $key => $value) {
                        $varColor = null;

                        if ($value['id_actividad'] == 1) {
                            $varColor = '#4298B5';
                        }
                        if ($value['id_actividad'] == 2) {
                            $varColor = '#FBCE52';
                        }
                ?>
                    {
                        name: "<?php echo $value['varActivdad'];?>",
                        y: parseFloat("<?php echo $value['varCantidadActividad'];?>"),
                        color: "<?php echo $varColor; ?>",
                        dataLabels: {
                            enabled: false
                        }
                    },
                <?php 
                    }
                ?>
                        
            ]
        }]
    });
</script>