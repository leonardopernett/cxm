<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;

$this->title = 'Fuentes de Información';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $varfecha = Yii::$app->db->createCommand("SELECT distinct fechacreacion from tbl_tmpvaloradosdistribucion ")->queryScalar();

    $varcantidadasesorjarvis = Yii::$app->db->createCommand("SELECT count(t.documento) cantidad_jarvis FROM tbl_tmpvaloradosdistribucion t
                                            WHERE t.cargo = 'Representante De Servicio' AND t.nombrepcrc NOT IN ('Vodafone Ono Sau','Enel Chile','Konecta BTO','Centro de mensajeria')")->queryScalar();
    $varcantidadasesornuevo = Yii::$app->db->createCommand("SELECT count(lista.documento) valorado_nuevo from
                                            (SELECT t.documento, t.nombreempleado, t.usuario_red, t.idpcrc, t.nombrepcrc
                                            FROM tbl_tmpvaloradosdistribucion t
                                            WHERE t.cargo = 'Representante De Servicio' AND t.usuario_red IS NOT NULL AND 
                                            t.nombrepcrc NOT IN ('Vodafone Ono Sau','Enel Chile','Konecta BTO','Centro de mensajeria')) lista
                                            WHERE lista.usuario_red NOT IN (SELECT e.dsusuario_red FROM tbl_evaluados e)")->queryScalar();

    $varcantidadasesornuevo = Yii::$app->db->createCommand("SELECT COUNT(dsusuario_red) from  tbl_evaluados WHERE fechacreacion = (SELECT MAX(fechacreacion) FROM tbl_evaluados)")->queryScalar();
    $varcantidasasesorcxm = $varcantidadasesorjarvis - $varcantidadasesornuevo;

    $varcantidadvaloradoscxm = Yii::$app->db->createCommand("SELECT COUNT(dsusuario_red) FROM tbl_evaluados")->queryScalar();
    $varcantidadvaloradoscxm = $varcantidadvaloradoscxm - $varcantidadasesorjarvis;

    
     
    $varcantidadasesoresactaulizados = Yii::$app->db->createCommand("SELECT COUNT(dsusuario_red) from  tbl_evaluados WHERE fechacreacion = (SELECT MAX(fechacreacion) FROM tbl_evaluados)")->queryScalar();
    
    $varcantidadasesoresjarvinosusua = Yii::$app->db->createCommand("SELECT count(documento) FROM tbl_tmpvaloradosdistribucion WHERE usuario_red Is null")->queryScalar();
    $varcantidadliderjarvinosusua = Yii::$app->db->createCommand("SELECT count(documentojefe) FROM tbl_tmpvaloradosdistribucion WHERE usuario_redjefe Is null")->queryScalar();
    $varvalornoatualizado = $varcantidadasesoresjarvinosusua + $varcantidadliderjarvinosusua;

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
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .card2 {
            height: 170px;
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
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>
<script src="../../web/js_exporting/jquery-2.1.1.min.js"></script>
<script src="../../web/js_exporting/highcharts/highcharts.js"></script>
<script src="../../web/js_exporting/chart.min.js"></script>
<script src="../../web/js_exporting/highcharts/exporting.js"></script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
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
<?php
if($sessiones == "3205" || $sessiones == "3229" || $sessiones == "2953"){ ?>
<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 13px;"><em class="fas fa-upload" style="font-size: 15px; color: #FFC72C;"></em> Subir información: </label>
                            <?= Html::a('Actualiza Valorados',  ['usuarios_valorado'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #f28e41',
                                            'data-toggle' => 'tooltip',
                                            'data' => [
                                                'confirm' => "Este proceso dura unos minutos para copiar la base de distribución de personal, desea continuar con el proceso?",
                                                'method' => 'post',
                                            ],                                            
                                            'title' => 'Actualiza Valorados de la Distrib. Personal'])
                            ?>                             
                        </div>
			</div>    
                     <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 13px;"><em class="fas fa-upload" style="font-size: 15px; color: #FFC72C;"></em> Procesar nuevos de la distribución: </label>
                            <?= Html::a('Nuevos distribución',  ['nuevosdistribucion'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #f28e41',
                                            'data-toggle' => 'tooltip',
					    'data' => [
                                                'confirm' => "Este proceso crea los lider nuevos y los evaluados de la distribución de personal, desea continuar con el proceso?",
                                                'method' => 'post',
                                            ],
                                            'title' => 'Agrega nuevos lideres, equipos y valorados de la distribución'])
                            ?>
                             
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 13px;"><em class="fas fa-upload" style="font-size: 15px; color: #FFC72C;"></em> Proceso de actualización de equipos: </label>
                            <?= Html::a('Actualizar equipos',  ['actualizacionequipos'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #f28e41',
                                            'data-toggle' => 'tooltip',
					    'data' => [
                                                'confirm' => "Este proceso actualiza los equipos según la distribución de personal, desea continuar con el proceso?",
                                                'method' => 'post',
                                            ],
                                            'title' => 'Actualiza equipos según la distribución de Personal'])
                            ?>
                             
                        </div>
                    </div>

		    
	             <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 13px;"><em class="fas fa-download" style="font-size: 15px; color: #FFC72C;"></em> Exportar Pcrc: </label>
                            <?= Html::button('Exportar', ['value' => url::to('exportarpcrc'), 'class' => 'btn btn-success', 'id'=>'modalButton2', 'data-toggle' => 'tooltip', 'title' => 'Descargar', 'style' => 'background-color: #337ab7'])
                               ?>
                               <?php
                                   Modal::begin([
                                       'header' => '<h4>Descargando listado...</h4>',
                                       'id' => 'modal2',
                                       //'size' => 'modal-lg',
                                   ]);

                                   echo "<div id='modalContent2'></div>";
                                                                   
                                   Modal::end();
                               ?>
                             
                        </div>
                    </div>                    
                </div>
	         <br>
                <div class="row">
                <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 13px;"><em class="fas fa-download" style="font-size: 15px; color: #FFC72C;"></em> Exportar valorado sin usuario de red: </label>
                            <?= Html::button('Exportar', ['value' => url::to('exportarsinusuariored'), 'class' => 'btn btn-success', 'id'=>'modalButton3', 'data-toggle' => 'tooltip', 'title' => 'Descargar', 'style' => 'background-color: #337ab7'])
                               ?>
                               <?php
                                   Modal::begin([
                                       'header' => '<h4>Descargando listado...</h4>',
                                       'id' => 'modal3',
                                       //'size' => 'modal-lg',
                                   ]);

                                   echo "<div id='modalContent3'></div>";
                                                                   
                                   Modal::end();
                               ?>
                             
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 13px;"><em class="fas fa-download" style="font-size: 15px; color: #FFC72C;"></em> Exportar lider sin usuario de red: </label>
                            <?= Html::button('Exportar', ['value' => url::to('exportarsinusuarioredlider'), 'class' => 'btn btn-success', 'id'=>'modalButton5', 'data-toggle' => 'tooltip', 'title' => 'Descargar', 'style' => 'background-color: #337ab7'])
                               ?>
                               <?php
                                   Modal::begin([
                                       'header' => '<h4>Descargando listado...</h4>',
                                       'id' => 'modal5',
                                       //'size' => 'modal-lg',
                                   ]);

                                   echo "<div id='modalContent5'></div>";
                                                                   
                                   Modal::end();
                               ?>
                             
                        </div>
                    </div>
	             <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 13px;"><em class="fas fa-download" style="font-size: 15px; color: #FFC72C;"></em> Exportar Lider: </label>
                            <?= Html::button('Exportar', ['value' => url::to('exportarlider'), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Descargar', 'style' => 'background-color: #337ab7'])
                               ?>
                               <?php
                                   Modal::begin([
                                       'header' => '<h4>Descargando listado...</h4>',
                                       'id' => 'modal1',
                                       //'size' => 'modal-lg',
                                   ]);

                                   echo "<div id='modalContent1'></div>";
                                                                   
                                   Modal::end();
                               ?>
                             
                        </div>
                    </div>
	             <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 13px;"><em class="fas fa-upload" style="font-size: 15px; color: #FFC72C;"></em> Actualización de equipos a pcrc: </label>
                            <?= Html::a('Actualizar equipos pcrc',  ['actualizacionequipospcrc'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #337ab7',
                                            'data-toggle' => 'tooltip',
					    'data' => [
                                                'confirm' => "Este proceso actualiza los equipos de todos los PCRC, según la distribución de personal, desea continuar con el proceso?",
                                                'method' => 'post',
                                            ],
                                            'title' => 'Actualizar equipos para todos los pcrc del Arbol'])
                            ?>
                             
                        </div>
                    </div> 
                </div>
               <br>
            <div class="row">
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 13px;"><em class="fas fa-download" style="font-size: 15px; color: #FFC72C;"></em> Exportar lista de equipos actualizados: </label>
                            <?= Html::button('Exportar', ['value' => url::to('exportarequiposactual'), 'class' => 'btn btn-success', 'id'=>'modalButton6', 'data-toggle' => 'tooltip', 'title' => 'Descargar', 'style' => 'background-color: #337ab7'])
                               ?>
                               <?php
                                   Modal::begin([
                                       'header' => '<h4>Descargando listado...</h4>',
                                       'id' => 'modal6',
                                       //'size' => 'modal-lg',
                                   ]);

                                   echo "<div id='modalContent6'></div>";
                                                                   
                                   Modal::end();
                               ?>
                             
                        </div>
                    </div>
		    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 13px;"><em class="fas fa-download" style="font-size: 15px; color: #FFC72C;"></em> Exportar valorados de CXM no encontrados: </label>
                            <?= Html::button('Exportar', ['value' => url::to('exportarvaloradonocxm'), 'class' => 'btn btn-success', 'id'=>'modalButton7', 'data-toggle' => 'tooltip', 'title' => 'Descargar', 'style' => 'background-color: #337ab7'])
                               ?>
                               <?php
                                   Modal::begin([
                                       'header' => '<h4>Descargando listado...</h4>',
                                       'id' => 'modal7',
                                       //'size' => 'modal-lg',
                                   ]);

                                   echo "<div id='modalContent7'></div>";
                                                                   
                                   Modal::end();
                               ?>
                             
                        </div>
                    </div>
		    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #FFC72C;"></em> Formato actualiza manualmente valorado:</label>
                            <?= Html::button('Exportar formato', ['value' => url::to('exportarformatovalorado'), 'class' => 'btn btn-success', 'id'=>'modalButton8', 'data-toggle' => 'tooltip', 'title' => 'Descargar Formato', 'style' => 'background-color: #337ab7'])
                               ?>
                               <?php
                                   Modal::begin([
                                       'header' => '<h4>Descargando formato...</h4>',
                                       'id' => 'modal8',
                                       //'size' => 'modal-lg',
                                   ]);

                                   echo "<div id='modalContent8'></div>";
                                                                   
                                   Modal::end();
                               ?>
                             
                        </div>
                    </div>
		    <div class="col-md-3">
                        <div class="card1 mb">
		            <label style="font-size: 13px;"><em class="fas fa-upload" style="font-size: 15px; color: #FFC72C;"></em> Importar formato de valorados: </label> 
                            <?= Html::button('Importar Valorados', ['value' => url::to('nuevosformatodistribucion'), 'class' => 'btn btn-success', 'id'=>'modalButton9',
                                'data-toggle' => 'tooltip',
                                'data' => [
                                    'confirm' => "Este proceso crea los lider nuevos y los evaluados de la distribución de personal, desea continuar con el proceso?",
                                    'method' => 'post',
                                ],
                                 'title' => 'Importar formato nuevos evaluados', 'style' => 'background-color: #337ab7']) 
                            ?>  
                            <?php
                                 Modal::begin([
                                    'header' => '<h4>Importar Valorados, el formato se carga en CSV </h4>',
                                    'id' => 'modal9',
                                     //'size' => 'modal-lg',
                                     ]);

                                    echo "<div id='modalContent9'></div>";
                                                                                
                                Modal::end(); 
                            ?>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>
<hr>
<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-file-alt" style="font-size: 20px; color: #559FFF;"></em> Actualización DP: </label>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card1 mb">
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered detail-view formDinamico" id="tablacate">
                                        <caption>Tabla datos</caption>
                                        <thead>
                                            <tr>
                                            <th scope="col" class="text-center"><?= Yii::t('app', 'Fecha de Ejecución') ?></th>
                                            <th scope="col" class="text-center"><?= Yii::t('app', 'Valores encontrados en Jarvis') ?></th>
                                            <th scope="col" class="text-center"><?= Yii::t('app', 'Valores de Jarvis encontados en CXM') ?></th>
                                            <th scope="col" class="text-center"><?= Yii::t('app', 'Valores de CXM no encontrados en Jarvis') ?></th>
                                            <th scope="col" class="text-center"><?= Yii::t('app', 'Valores Actualizados') ?></th>
                                            <th scope="col" class="text-center"><?= Yii::t('app', 'Valores no actualizados') ?></th>
                                            </tr>      
                                        </thead>
                                        <tbody>    
                                            <?php
                                                
                                            ?>
                                            <tr>
                                            <td class="text-center"><?php echo $varfecha; ?></td>
                                            <td class="text-center"><?php echo $varcantidadasesorjarvis; ?></td>
                                            <td class="text-center"><?php echo $varcantidasasesorcxm; ?></td>
                                            <td class="text-center"><?php echo $varcantidadvaloradoscxm; ?></td>
                                            <td class="text-center"><?php echo $varcantidadasesoresactaulizados; ?></td>
                                            <td class="text-center"><?php echo $varvalornoatualizado; ?></td>
                                            </tr>                                            
                                        </tbody>    
                                    </table>
                                </div>
                            </div>
                        </div>    
                    </div>    
            </div>
        </div>
    </div>
</div>
<hr>
<?php } else { ?>
  <div class="Seis">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
              <label><em class="fas fa-info-circle" style="font-size: 20px; color: #1e8da7;"></em> Información:</label>
              <label style="font-size: 14px;">No tiene los permisos para ingresar a esta opción.</label>
                </div><br>
            </div>
        </div>  
    </div>
  </div><br>

  <?php } ?>

