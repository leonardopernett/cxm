<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

$this->title = 'Evaluacion de desarrollo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $varidlist = Yii::$app->db->createCommand("select * from tbl_evaluacion_tipoeval where anulado = 0")->queryAll();
    $varTipos = ArrayHelper::map($varidlist, 'idevaluaciontipo', 'tipoevaluacion'); 

    $vardocument = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
    $varnombre = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $sessiones")->queryScalar();   

    $vartotal = 0;
    $varaprobadas = 0;
    $varreprobadas = 0;
    $varlistnovedades = null;
    if ($varidtipo == 1) {
        $vartotal = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadesauto where anulado = 0")->queryScalar();

        $varaprobadas = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadesauto where anulado = 0 and aprobado = 1")->queryScalar();

        $varreprobadas = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadesauto where anulado = 0 and aprobado = 0")->queryScalar();

        $varnoaprobadas = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadesauto where anulado = 0 and aprobado = 2")->queryScalar();

        $varlistnovedades = Yii::$app->db->createCommand("select * from tbl_evaluacion_novedadesauto where anulado = 0")->queryAll();
    }else{
        if ($varidtipo == 2) {
            $vartotal = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadesjefe where anulado = 0")->queryScalar();

            $varaprobadas = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadesjefe where anulado = 0 and aprobado = 1")->queryScalar();

            $varreprobadas = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadesjefe where anulado = 0 and aprobado = 0")->queryScalar();

            $varnoaprobadas = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadesjefe where anulado = 0 and aprobado = 2")->queryScalar();

            $varlistnovedades = Yii::$app->db->createCommand("select * from tbl_evaluacion_novedadesjefe where anulado = 0")->queryAll();
        }else{
            if ($varidtipo == 3) {
                $vartotal = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadescargo where anulado = 0")->queryScalar();

                $varaprobadas = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadescargo where anulado = 0 and aprobado = 1")->queryScalar();

                $varreprobadas = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadescargo where anulado = 0 and aprobado = 0")->queryScalar();

                $varnoaprobadas = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadescargo where anulado = 0 and aprobado = 2")->queryScalar();

                $varlistnovedades = Yii::$app->db->createCommand("select * from tbl_evaluacion_novedadescargo where anulado = 0")->queryAll();
            }else{
                if ($varidtipo == 4) {
                    $vartotal = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadespares where anulado = 0")->queryScalar();

                    $varaprobadas = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadespares where anulado = 0 and aprobado = 1")->queryScalar();

                    $varreprobadas = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadespares where anulado = 0 and aprobado = 0")->queryScalar();

                    $varnoaprobadas = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadespares where anulado = 0 and aprobado = 2")->queryScalar();

                    $varlistnovedades = Yii::$app->db->createCommand("select * from tbl_evaluacion_novedadespares where anulado = 0")->queryAll();
                }
            }
        }
    }

    if ($vartotal == 0) {
        $varPorcentaje = 0;
    }else{
        $varPorcentaje = round(($varaprobadas/$vartotal)*100);    
    }   
       

?>
<style>
    @import url('https://fonts.googleapis.com/css?family=Nunito');

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
            height: 174px;
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
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br>
<div id="idCapaUno" style="display: inline">
    <div id="capaUno" style="display: inline">
        <div class="row">            
            
            <div class="col-md-3">
                <div class="card2 mb">
                    <label><i class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></i></i> Tipo de evaluación:</label>
                    <label style="font-size: 16px;"><?php echo $varnombretipo; ?></label>
                    <br>
                    <br>
                    <?= Html::button('Seleccionar tipo evaluación', ['value' => url::to(['evaluaciondesarrollo/novedadgeneral']), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Verficar']) 
                    ?> 

                    <?php
                        Modal::begin([
                            'header' => '<h4></h4>',
                            'id' => 'modal1',
                            //'size' => 'modal-lg',
                        ]);

                        echo "<div id='modalContent1'></div>";
                                                                              
                        Modal::end(); 
                    ?>                           
                    <br>                    
                </div>
            </div>
            <div class="col-md-6">
                <div class="card2 mb">
                    <label><i class="fas fa-info-circle" style="font-size: 20px; color: #C148D0;"></i></i> Novedades aprobadas y sin aprobar de evaluación <?php echo $varnombretipo; ?>:</label>
                    <div class="row">
                        <div class="col-md-5">
                            <br>
                            <label><i class="fas fa-square" style="font-size: 20px; color: #59DE49;"></i></i> Novedades aprobadas</label>
                            <label><i class="fas fa-square" style="font-size: 20px; color: #f7b9b9;"></i></i> Novedades no aprobadas</label>
                            <label><i class="fas fa-square" style="font-size: 20px; color: #FFC251;"></i></i> Novedades en espera</label>
                        </div>
                        <div class="col-md-7">
                            <div id="containerG" class="highcharts-container" style="height: 120px;"></div>      
                        </div>
                    </div>
                    
                </div>
            </div>            
            <div class="col-md-3">
                <div class="card2 mb">
                    <label><i class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></i> Cantidad de novedades de evaluación <?php echo $varnombretipo; ?>:</label>
                    <label  style="font-size: 70px; text-align: center;"><?php echo $vartotal; ?></label>
                </div>
            </div>
            
        </div>
    </div>
</div>
<hr>
<div id="idCapaDos" style="display: inline">
    <div id="capaDos" style="display: inline">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label><i class="fas fa-list" style="font-size: 20px; color: #00968F;"></i> Listado de novedades</label>
                    <br>
                    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                        <thead>
                            <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Id"; ?></label></th>
                            <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Solicitante Novedad"; ?></label></th>
                            <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Asunto"; ?></label></th>
                            <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Descripción"; ?></label></th>
                            <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Estado"; ?></label></th>
                            <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Aprobar"; ?></label></th>
                            <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "No Aprobar"; ?></label></th>
                        </thead>
                        <tbody>

                                <?php foreach ($varlistnovedades as $key => $value) {
                                    $varidnovedad = null;
                                    if ($varidtipo == 1) {
                                        $varidnovedad = $value['idnovedades'];
                                        $varcomenta = $value['comentarios'];
                                    }else{
                                        if ($varidtipo == 2) {
                                            $varidnovedad = $value['idnovedadesj'];
                                            $varcomenta = $value['comentarios'];
                                        }else{
                                            if ($varidtipo == 3) {
                                                $varidnovedad = $value['idnovedadesc'];

                                                $varidpp = $varidnovedad;
                                                $vartipo = Yii::$app->db->createCommand("select distinct tipo from tbl_evaluacion_novedadescargo where anulado = 0 and idnovedadesc = $varidpp")->queryScalar();
                                                if ($vartipo == 1) {
                                                    $varcomenta = $value['comentarios'];
                                                }else{
                                                    $varcname = $value['cambios'];
                                                    $varcomenta = Yii::$app->db->createCommand("select nombre_completo from tbl_usuarios_evalua where anulado = 0 and documento = $varcname")->queryScalar();
                                                }
                                            }else{
                                                if ($varidtipo == 4) {
                                                    $varidnovedad = $value['idnovedadesp'];
                                                    $varcomenta = $value['comentarios'];
                                                }
                                            }
                                        }
                                    }
                                    
                                    $varidnombre = $value['documento'];
                                    $varnombresolicitante = Yii::$app->db->createCommand("select nombre_completo from tbl_usuarios_evalua where anulado = 0 and documento = $varidnombre")->queryScalar();
                                    $varidestado = $value['aprobado'];
                                    $varestados = null;
                                    if ($varidestado == 1) {
                                        $varestados = "Aprobado";
                                    }else{
                                        if ($varidestado == 2) {
                                            $varestados = "No Aprobado";
                                        }else{
                                            if ($varidestado == 0) {
                                                $varestados = "En espera";
                                            }
                                        }
                                    }

                                    
                                    
                                ?>
                                    <tr>
                                        <td><label style="font-size: 12px;"><?php echo  $varidnovedad; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $varnombresolicitante; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $value['asunto']; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $varcomenta.' - '.$value['cambios']; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $varestados; ?></label></td>
                                        <?php if ($varidestado == 1 || $varidestado == 2) {?>
                                            <td><label style="font-size: 12px;"><?php echo  "---"; ?></label></td>
                                            <td><label style="font-size: 12px;"><?php echo  "---"; ?></label></td>
                                        <?php }else{ ?>
                                            <td class="text-center">
                                                    <?= Html::a('<i class="fas fa-thumbs-up" style="font-size: 20px; color: #2cdc5a;"></i>',  ['editarplannovedad','idtipos'=>$varidtipo,'varidplan' => $varidnovedad, 'varestado' => 1,'varcambios'=>$value['cambios'],'varsolicitante'=>$varidnombre], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Aprobar']) ?>
                                            </td>
                                            <td class="text-center">
                                                <?= Html::a('<i class="fas fa-thumbs-down" style="font-size: 20px; color: #ff3838;"></i>',  ['editarplannovedad','idtipos'=>$varidtipo,'varidplan' => $varidnovedad, 'varestado' => 2,'varcambios'=>$value['cambios'],'varsolicitante'=>$varidnombre], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'No Aprobar']) ?>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>

                        </tbody>                        
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div id="idCapaTres" style="display: inline">
    <div id="capaTres" style="display: inline">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 17px;"><i class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></i> Acciones: </label>
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><i class="fas fa-minus-circle" style="font-size: 17px; color: #FFC72C;"></i> Cancelar y regresar: </label> 
                                <?= Html::a('Regresar',  ['evaluaciondesarrollo/gestionnovedades'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
                                ?>                            
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<script type="text/javascript">
    $(function() {
        Highcharts.chart('containerG', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '<label style="font-size: 20px;"><?php echo $varPorcentaje.'%'; ?></label>',
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
                    {
                        name: 'Aprobadas',
                        y: parseFloat("<?php echo $varaprobadas;?>"),
                        color: '#59DE49',
                        dataLabels: {
                            enabled: false
                        }
                    },{
                        name: 'No Aprobadas',
                        y: parseFloat("<?php echo $varnoaprobadas;?>"),
                        color: '#f7b9b9',
                        dataLabels: {
                            enabled: false
                        }
                    },{
                        name: 'Sin aprobar',
                        y: parseFloat("<?php echo $varreprobadas;?>"),
                        color: '#FFC251',
                        dataLabels: {
                            enabled: false
                        }
                    }
                ]
            }]
        });



    }); 
</script>