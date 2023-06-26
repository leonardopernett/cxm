<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\db\Query;

$this->title = 'Historico Heroes por Cliente ';//nombre del titulo de mi modulo
$this->params['breadcrumbs'][] = $this->title;

$sesiones =Yii::$app->user->identity->id;   

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';


$rol =  new Query;
$rol    ->select(['tbl_roles.role_id'])
        ->from('tbl_roles')
        ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
        ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
        ->where('tbl_usuarios.usua_id = '.$sesiones.'');                    
$command = $rol->createCommand();
$roles = $command->queryScalar();

$paramsBusqueda = [':varSesion' => $sesiones, ':anulado' => 0];

$varConteoExist = Yii::$app->db->createCommand('
    SELECT d.iddashservicio FROM tbl_dashboardpermisos d 
      WHERE d.usuaid = :varSesion 
        AND anulado = :anulado
      GROUP BY d.iddashservicio')->bindValues($paramsBusqueda)->queryAll();

$varlistiddpclientes = array();
$varservicios = null;
  if (count($varConteoExist) != 0) {
    foreach ($varConteoExist as $key => $value) {
      array_push($varlistiddpclientes, $value['iddashservicio']);
    }
    $varservicios = implode(", ", $varlistiddpclientes);
  }

$varTotal  = (new \yii\db\Query())
        ->select(['estado, COUNT(estado) AS Total'])
        ->from(['tbl_postulacion_heroes'])
        ->groupBy('estado'); 

$varConteo = (new \yii\db\Query())
        ->select(['*'])
        ->from(['tbl_postulacion_heroes'])
        ->count();
 
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
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/dasheroes.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

    .loader {
      border: 16px solid #f3f3f3;
      border-radius: 50%;
      border-top: 16px solid #3498db;
      width: 80px;
      height: 80px;
      -webkit-animation: spin 2s linear infinite; /* Safari */
      animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
      0% { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

</style>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>

<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br>
<div class="capaInfo" id="idCapaInfo" style="display: inline;">
    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <br>
    <div class="row"><!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Filtro & Acciones"; ?> </label><!--titulo principal de mi modulo-->
            </div>
        </div>
    </div><!-- divdel subtitilo azul principal que va llevar el nombre del modulo---------------------->
    <br>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em><?= Yii::t('app', ' Selección de Filtros') ?></label>
                        <br><br>
                        <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 20px; color: #559FFF;"></em><?= Yii::t('app', ' Tipo de Postulación') ?></label>
                        <?= $form->field($model,'tipodepostulacion',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($varTipoPostu ,['prompt'=>'Seleccionar...'])?>

                        <br>
                        <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 20px; color: #559FFF;"></em><?= Yii::t('app', ' Tipo de Estado') ?></label>
                        <?= $form->field($model,'estado',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($varTipoEstado,['prompt'=>'Seleccionar...'])?>
            
                        <br>

                        <label style="font-size: 15px;"><em class="fas fa-calendar" style="font-size: 20px; color: #559FFF;"></em><?= Yii::t('app', ' Rango de Fecha') ?></label>
                        <?=
                            $form->field($model, 'fechahorapostulacion', [
                                'labelOptions' => ['class' => 'col-md-12'],
                                'template' => 
                                '<div class="col-md-12"><div class="input-group">'
                                . '<span class="input-group-addon" id="basic-addon1">'
                                . '<em class="glyphicon glyphicon-calendar"></em>'
                                . '</span>{input}</div>{error}{hint}</div>',
                                'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                                'options' => ['class' => 'drp-container form-group']
                            ])->label('')->widget(DateRangePicker::classname(), [
                                'useWithAddon' => true,
                                'convertFormat' => true,
                                'presetDropdown' => true,
                                'readonly' => 'readonly',
                                'pluginOptions' => [
                                    'timePicker' => false,
                                    'format' => 'Y-m-d',
                                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                    'endDate' => date("Y-m-d"),
                                    'opens' => 'right',
                            ]]);
                        ?> 

                        
                    </div>

                    <div class="col-md-6">

                        <br><br>
                        <label><em class="fas fa-check" style="font-size: 20px; color: #559FFF;"></em> Seleccionar Cliente: </label>
                            <?php
                                if (count($varConteoExist) != 0) {                                  
                            ?>
                                <?=  $form->field($model, 'pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechServicios::find()->distinct()->where("anulado = 0")->andwhere("id_dp_clientes in ($varservicios)")->orderBy(['nameArbol'=> SORT_ASC])->all(), 'id_dp_clientes', 'nameArbol'),
                                                    [
                                                        'id' => 'txtidclientes',
                                                        'prompt'=>'Seleccionar ',
                                                        'onchange' => '
                                                            $.get(
                                                                "' . Url::toRoute('listarpcrcs') . '", 
                                                                {id: $(this).val()}, 
                                                                function(res){
                                                                    $("#requester").html(res);
                                                                }
                                                            );
                                                            
                                                        ',

                                                    ]
                                        )->label(''); 
                                ?>
                            <?php
                                } else{
                            ?>
                                <?=  $form->field($model, 'pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechServicios::find()->distinct()->where("anulado = 0")->andwhere("id_dp_clientes != 1")->orderBy(['nameArbol'=> SORT_ASC])->all(), 'id_dp_clientes', 'nameArbol'),
                                                    [
                                                        'id' => 'txtidclientes',
                                                        'prompt'=>'Seleccionar',
                                                        'onchange' => '
                                                            $.get(
                                                                "' . Url::toRoute('listarpcrcs') . '", 
                                                                {id: $(this).val()}, 
                                                                function(res){
                                                                    $("#requester").html(res);
                                                                }
                                                            );
                                                            
                                                        ',

                                                    ]
                                        )->label(''); 
                                ?>
                            <?php                                    
                                }                                 
                            ?>


              
                <br>

                <label><em class="fas fa-check" style="font-size: 20px; color: #559FFF;"></em> Seleccionar Programa/Pcrc: </label>
                            <?= $form->field($model,'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                                [],
                                                [
                                                    
                                                    'prompt' => 'Seleccionar...',
                                                    'id' => 'requester',
                                                    'multiple' => true,
                                                    'onclick' => 'carga_programa();',
                                                ]
                                            )->label('');
                            ?> 
                <br><br>



                        <?= Html::submitButton(Yii::t('app', 'Buscar'),//nombre del boton
                                        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                            'data-toggle' => 'tooltip',
                                            'onClick'=> 'varVerificar();',//funcion de JS que al dar clic verifique que estoy enviando 
                                            'title' => 'Buscar']) 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
    <br><hr>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table style="width:100%">
                    <th scope="col" class="text-center" style="width: 100px;">
                    <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 25px; color: #559FFF;"></em> <?= Yii::t('app', 'Total de Postulaciones por Tipo de Estado') ?></label>
                    <div id="Containertotal" class="highcharts-container" style="height: 150px;"></div> 
                    </th>
                </table>
            </div>     
            
            <br><hr>
        
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #1993a5;"></em> <?= Yii::t('app', 'Restaurar General') ?></label>
                    <?= Html::a('Buscar',  ['heroescliente/dasheroes'], ['class' => 'btn btn-success',
                                                        'style' => 'background-color: #707372',
                                                        'data-toggle' => 'tooltip',
                                                        'title' => 'Buscar General']) 
                    ?>
            </div>
        </div>
    </div>

    <br><hr>
    
    <div class="row"><!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Resumen General"; ?> </label><!--titulo principal de mi modulo-->
            </div>
        </div>
    </div><!-- divdel subtitilo azul principal que va llevar el nombre del modulo---------------------->
    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" ><!--Titulo de la tabla no se muestra-->
                    <caption><label><em class="fas fa-list" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Postulaciones') ?></label></caption><!--Titulo de la tabla si se muestra-->
                    <thead><!--Emcabezados de la tabla -->
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Fecha  de Postulación') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', ' Nombre de Quién Postula') ?></label></th>
                    <?php if ($roles == '270' || $roles == '272' || $roles == '294'|| $roles == '298' || $roles == '307' ) { ?>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Valorar') ?></label></th><?php  }?>
                    <?php if ('estado' != 'Cerrado') {?>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Estado') ?></label></th><?php }?>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Valorador') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Detalle') ?></label></th>

                    </thead>
                    <tbody><!--Tbody de la tabla -->
                    <?php foreach ($varData as $value) : ?>
                    
                            <tr><!--Filas de la tabla -->
                            <td class="text-center"><label style="font-size: 12px;"><?php echo $value['fechacreacion']; ?></label></td>
                            <td class="text-center"><label style="font-size: 12px;"><?php echo $value['nombrepostula'];?></label></td>
                        <?php if ($roles == '270' || $roles == '272' || $roles == '294'|| $roles == '298' || $roles == '307') { ?>
                            <td class="text-center"><!--boton eliminar que esta dentro de esa fila-->
                        <?=   $value['estado'] === 'Abierto'  ? Html::a('<em class="fas fa-edit" style="font-size: 25px; color: #559FFF;"></em>',  ['heroescliente/interaccionmanual','embajadorpostular'=> $value['embajadorpostular']], ['target' => '_blank',]) :'<em class="fas fa-edit" style="font-size: 25px; color: red;"></em>' ?></td><?php  }?>
                            <td class="text-center"><label style="font-size: 12px;"><?php echo $value['estado']; ?></td>                                             
                            <td class="text-center"><label style="font-size: 12px;"><?php echo $value['valorador']; ?></td>
                            <td class="text-center"><!--boton eliminar que esta dentro de esa fila-->
                            <?= Html::a('<em class="fas fa-eye" style="font-size: 25px; color: #559FFF;"></em>',  ['detalleheroes','id_postulacion'=> $value['id_postulacion']], ['target' => '_blank'])?></td>
                        </tr>

                        <?php endforeach; ?>         
                    </tbody><!--fin Tbody de la tabla -->
                </table>
            </div>
        </div>
    </div>
  
    <br><hr><br>


</div>

<script>
    $(document).ready( function () {
    $('#myTable').DataTable({
      responsive: true,
      fixedColumns: true,
      select: true,
      "language": {
        "lengthMenu": "Cantidad de Datos a Mostrar",
        "zeroRecords": "No se encontraron datos ",
        "info": "Mostrando p&aacute;gina Page a Pages de Max registros",
        "infoEmpty": "No hay datos aun",
        "infoFiltered": "(Filtrado un Max total)",
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

  $('#Containertotal').highcharts({
        chart: {                
            type: 'bar'
        },

        yAxis: {
            title: {
                text: 'Cantidad de Postulaciones por Estado'
            },
            allowDecimals: false
        }, 

        title: {
            text: '',
            style: {
                color: '#3C74AA'
            }
        },

        xAxis: {
            categories: " ",
            title: {
                text: null
            }
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

        series: [   
               <?php                         
                        foreach($varTotal->each() as $total){?>
                        {
                            name: "<?php echo $total['estado'];?>",
                            data: [<?php echo $total['Total'];?> ]                        
                        },
                 <?php }?>      
            
        ]
    });


    function varVerificar() {

        var fechahorapostula = document.getElementById("postulacionheroes-fechahorapostulacion").value;  

        if (fechahorapostula == "") {
        event.preventDefault();
                swal.fire("!!! Advertencia !!!","Se debe seleccionar rango de fecha","warning");
                return;
        }
    }

</script>


