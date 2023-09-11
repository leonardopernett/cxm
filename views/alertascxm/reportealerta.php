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

$this->title = 'Alertas - Reporte de Alerta';
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
   	font-family: "Nunito",sans-serif;
    font-size: 150%;    
    text-align: left;    
  }

  .card2 {
    height: 180px;
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

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Alertas-Valoración.png');
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
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => [
            'autocomplete' => 'off'
        ]
    ]
  ]); 
?>

<!-- Capa Filtros de Busqueda -->
<div class="capaBuscar" id="capaIdBuscar" style="display: inline;">
  
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Filtros & Búsquedas') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Selección de Filtros') ?></label>

                <br>

                <div class="row">
                    <div class="col-md-4">
                        <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Seleccionar Programa/Pcrc') ?></label>
                        <?=
                            $form->field($model, 'pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                            ->widget(Select2::classname(), [
                                'language' => 'es',
                                'options' => ['id'=>'varIdPcrc','placeholder' => Yii::t('app', 'Seleccionar Programa/Pcrc')],
                                'pluginOptions' => [
                                    'multiple' => true,
                                    'allowClear' => true,
                                    'minimumInputLength' => 3,
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['reportes/getarboles']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                    ],
                                    'initSelection' => new JsExpression('function (element, callback) {
                                        var id=$(element).val();
                                        if (id !== "") {
                                            $.ajax("' . Url::to(['getarboles']) . '?id=" + id, {
                                                dataType: "json",
                                                type: "post"
                                            }).done(function(data) { callback(data.results);});
                                        }
                                    }')
                                ]
                                    ]
                            );
                        ?>

                    </div>

                    <div class="col-md-4">
                        <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Seleccionar Responsable Valorador') ?></label>
                        <?=
                            $form->field($model, 'valorador', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                            ->widget(Select2::classname(), [
                                'language' => 'es',
                                'options' => ['id'=>'varIdUsuario','placeholder' => Yii::t('app', 'Seleccionar Responsable')],
                                'pluginOptions' => [
                                    'multiple' => true,
                                    'allowClear' => true,
                                    'minimumInputLength' => 4,
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['reportes/usuariolist']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                    ],
                                    'initSelection' => new JsExpression('function (element, callback) {
                                        var id=$(element).val();
                                        if (id !== "") {
                                            $.ajax("' . Url::to(['usuariolist']) . '?id=" + id, {
                                                dataType: "json",
                                                type: "post"
                                            }).done(function(data) { callback(data.results);});
                                        }
                                    }')
                                ]
                                    ]
                            );
                        ?>
                    </div>

                    <div class="col-md-4">
                        <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Seleccionar Rango de Fecha') ?></label>     
                        <?=
                                $form->field($model, 'fecha', [
                                    'labelOptions' => ['class' => 'col-md-12'],
                                    'template' => 
                                     '<div class="col-md-12"><div class="input-group">'
                                    . '<span class="input-group-addon" id="basic-addon1">'
                                    . '<i class="glyphicon glyphicon-calendar"></i>'
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
                </div>

            </div>
        </div>
    </div>

</div>

<br>

<!-- Capa Btn -->
<div class="capaBtn" id="capaIdBtn" style="display: inline;">
    
    <div class="row">
        <div class="col-md-4">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Buscar Alertas') ?></label>
                <?= Html::submitButton(Yii::t('app', 'Buscar'),
                          ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary',
                              'data-toggle' => 'tooltip',
                              'title' => 'Buscar Datos',
                              'onclick' => 'varVerificar();']) 
                ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Nueva Búsqueda') ?></label>
                <?= Html::a('Nuevo',  ['reportealerta'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Nueva Búsqueda']) 
                ?>
            </div>
        </div>

        <?php
        if ($varDataResultado != null) {
            
        ?>
        <div class="col-md-4">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Descargar Datos') ?></label>
                <a id="dlink" style="display:none;"></a>
                <button  class="btn btn-info" style="background-color: #4298B4" id="btn"><?= Yii::t('app', ' Descargar') ?></button>
            </div>
        </div>
        <?php
        }
        ?>
    </div>

</div>

<hr>

<?php
if ($varDataResultado != null) {
    
?>

<!-- capa Resultados -->
<div class="capaResultado" id="capaIdResultado" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resultados & Cantidades') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-4">
            <div class="card2 mb">
                <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Cantidad de Alertas') ?></label><br>
                <label  style="font-size: 70px; text-align: center;"><?php echo count($varDataResultado); ?></label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card2 mb">
                <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Tipos de Alertas') ?></label>
                <div id="containerA" class="highcharts-container" style="height: 150px;"></div> 
            </div>
        </div>

        <div class="col-md-4">
            <div class="card2 mb">
                <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Cantidad de Encuestas') ?></label><br>

                <div class="row">
                    <div class="col-md-6 text-center">
                        <label  style="font-size: 70px; text-align: center;"><?php echo count($varDataEncuestas); ?></label>
                    </div>

                    <div class="col-md-6">
                        <?php
                        foreach ($varDataEncuestasTipos as $value) {
                        ?>
                            <label  style="font-size: 15px; text-align: center;"><?php echo '* '.$value['tipoencuestas'].': '.$value['varCantidadEncuestas']; ?></label><br>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Resumen Proceso') ?></label>
                <div class="col-md-12 right">
                    <div onclick="opennovedadp();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbnp1" ><?= Yii::t('app', '[ Abrir + ]') ?>                                
                    </div> 
                    <div onclick="closenovedadp();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="idtbnp2" ><?= Yii::t('app', '[ Cerrar - ]') ?>                                
                    </div> 
                </div>
                <div class="capaExt" id="capa00p" style="display: none;">
                    <table id="tblListadoProcesos" class="table table-striped table-bordered tblResDetFreed">
                        <caption><?= Yii::t('app', ' Resultados de Procesos...') ?></caption>
                        <thead>
                            <tr>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cliente') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Programa/Pcrc') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cantidad de Alertas') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cantidad de Encuestas') ?></label></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($varDataProceso as $value) {                            
                                
                            ?>
                            <tr>
                                <td><label style="font-size: 11px;"><?php echo  $value['varCliente']; ?></label></td>
                                <td><label style="font-size: 11px;"><?php echo  $value['varProgramaPcrc']; ?></label></td>
                                <td><label style="font-size: 11px;"><?php echo  $value['varConteoPcrc']; ?></label></td>
                                <td><label style="font-size: 11px;"><?php echo  $value['varConteoEncuestas']; ?></label></td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Resumen Técnico') ?></label>                
                <div class="col-md-12" align="right">
                    <div onclick="opennovedad();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbnt1" ><?= Yii::t('app', '[ Abrir + ]') ?>                                
                    </div> 
                    <div onclick="closenovedad();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="idtbnt2" ><?= Yii::t('app', '[ Cerrar - ]') ?>                                
                    </div> 
                </div>

                <div class="capaExt" id="capa00t" style="display: none;">
                    <table id="tblListadoTecnicos" class="table table-striped table-bordered tblResDetFreed">
                        <caption><?= Yii::t('app', ' Resultados de Técnicos...') ?></caption>
                        <thead>
                            <tr>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Responsable Valorador') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cliente') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Programa/Pcrc') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cantidad de Alertas') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cantidad de Encuestas') ?></label></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($varDataTecnico as $value) {                            
                                
                            ?>
                            <tr>
                                <td><label style="font-size: 11px;"><?php echo  $value['varValorador_tecnico']; ?></label></td>
                                <td><label style="font-size: 11px;"><?php echo  $value['varCliente_tecnico']; ?></label></td>
                                <td><label style="font-size: 11px;"><?php echo  $value['varProgramaPcrc_tecnico']; ?></label></td>
                                <td><label style="font-size: 11px;"><?php echo  $value['varConteoPcrc_tecnico']; ?></label></td>
                                <td><label style="font-size: 11px;"><?php echo  $value['varConteoEncuestas_tecnico']; ?></label></td>
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

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Lista de Alertas') ?></label>

                <table id="tblListadoAlertas" class="table table-striped table-bordered tblResDetFreed">
                    <caption><?= Yii::t('app', ' Resultados de Alertas...') ?></caption>
                    <thead>
                        <tr>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Id') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha Alerta') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Programa/Pcrc') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Valorador') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Tipo de Alerta') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Satisfacción') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($varDataResultado as $value) {
                            $varIdAlertas = $value['id'];
                            $varFechas = $value['fecha'];
                            $varNames = $value['name'];
                            $varUsuaNombres = $value['usua_nombre'];
                            $varTipoAlertas = $value['tipo_alerta'];
                            
                            $arrayVarPeso = 0;
                            $varEncuestas = 0;
                            
                            $varPeso = (new \yii\db\Query())
                                        ->select(['tbl_alertas_tipoencuestas.peso'])
                                        ->from(['tbl_alertas_tipoencuestas'])
                                        ->join('LEFT OUTER JOIN', 'tbl_alertas_encuestasalertas',
                                                'tbl_alertas_tipoencuestas.id_tipoencuestas = tbl_alertas_encuestasalertas.id_tipoencuestas')
                                        ->where(['=','tbl_alertas_encuestasalertas.id_alerta',$varIdAlertas])
                                        ->andwhere(['=','tbl_alertas_encuestasalertas.anulado',0])
                                        ->all(); 

                            $varConteosPesos = 0;
                            if (count($varPeso)) {     
                                                  
                                foreach ($varPeso as $value) {                                    
                                    if ($value['peso'] == 4 || $value['peso'] == 5) {
                                        $arrayVarPeso = $varConteosPesos = $varConteosPesos + 1;
                                    }
                                }
                            }



                            if (count($varPeso)) {
                                $varEncuestas = round(($arrayVarPeso / count($varPeso)) * 100, 2).' %';
                            }else{
                                $varEncuestas = "--";
                            }
                            
                        ?>
                        <tr>
                            <td><label style="font-size: 11px;"><?php echo  $varIdAlertas; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $varFechas; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $varNames; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $varUsuaNombres; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $varTipoAlertas; ?></label></td>
                            <td class="text-center">
                                <?php echo $varEncuestas; ?>
                            </td>
                            <td class="text-center">

                                <?= 
                                    Html::a('<em id="idimage" class="fas fa-search" style="font-size: 15px;"></em>',
                                    'javascript:void(0)',
                                    [
                                        'title' => Yii::t('app', 'Ver Alerta'),
                                        'onclick' => "                                            
                                            $.ajax({
                                                type     :'get',
                                                cache    : false,
                                                url  : '" . Url::to(['veralerta','idalerta' => $varIdAlertas]) . "',
                                                success  : function(response) {
                                                    $('#ajax_result').html(response);
                                                }
                                            });
                                        return false;",
                                    ]);
                                ?>


                                <?php
                                $varContienePermisos = (new \yii\db\Query())
                                            ->select(['*'])
                                            ->from(['tbl_alertas_permisoseliminar'])
                                            ->where(['=','tbl_alertas_permisoseliminar.id_usuario',$sessiones])
                                            ->andwhere(['=','tbl_alertas_permisoseliminar.anulado',0])
                                            ->count();

                                if ($varContienePermisos) {
                                   
                                ?>

                                <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminaralerta','id'=> $varIdAlertas], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;  border-color: #4298b500 !important; color:#000000;", 'title' => 'Eliminar', 'target' => "_blank"]) ?>

                                <?php
                                }
                                ?>

                                <?= Html::a('<em class="fas fa-paper-plane" style="font-size: 15px; "></em>',  ['enviaralertados','id_enviados'=> $varIdAlertas], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;  border-color: #4298b500 !important; color:#000000;", 'title' => 'Enviar Alerta Emergente', 'target' => "_blank"]) ?>

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

<?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
?>

<?php
}
?>

<!-- Capa Descargar Tabla -->
<div class="capaTablas" id="capaIdTablas" style="display: none;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table id="tblListadoAlertasInvisible" class="table table-striped table-bordered tblResDetFreed">
                    <caption><?= Yii::t('app', ' Resultados de Alertas...') ?></caption>
                    <thead>
                        <tr>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Id') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha Alerta') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Programa/Pcrc') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Valorador') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Tipo de Alerta') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Satisfacción') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($varDataResultado as $value) {
                            $varIdAlertas_R = $value['id'];
                            $varFechas_R = $value['fecha'];
                            $varNames_R = $value['name'];
                            $varUsuaNombres_R = $value['usua_nombre'];
                            $varTipoAlertas_R = $value['tipo_alerta'];
                            $arrayVarPeso_R = 0;
                            $varEncuestas_R = 0;
                            
                            $varPeso_R = (new \yii\db\Query())
                                        ->select(['tbl_alertas_tipoencuestas.peso'])
                                        ->from(['tbl_alertas_tipoencuestas'])
                                        ->join('LEFT OUTER JOIN', 'tbl_alertas_encuestasalertas',
                                                'tbl_alertas_tipoencuestas.id_tipoencuestas = tbl_alertas_encuestasalertas.id_tipoencuestas')
                                        ->where(['=','tbl_alertas_encuestasalertas.id_alerta',$varIdAlertas_R])
                                        ->andwhere(['=','tbl_alertas_encuestasalertas.anulado',0])
                                        ->all(); 

                            $varConteosPesos_R = 0;
                            if (count($varPeso_R)) {     
                                                  
                                foreach ($varPeso_R as $value) {                                    
                                    if ($value['peso'] == 4 || $value['peso'] == 5) {
                                        $arrayVarPeso_R = $varConteosPesos_R = $varConteosPesos_R + 1;
                                    }
                                }
                            }



                            if (count($varPeso_R)) {
                                $varEncuestas_R = round(($arrayVarPeso_R / count($varPeso_R)) * 100, 2).' %';
                            }else{
                                $varEncuestas_R = "--";
                            }
                            
                        ?>
                        <tr>
                            <td><label style="font-size: 11px;"><?php echo  $varIdAlertas_R; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $varFechas_R; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $varNames_R; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $varUsuaNombres_R; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $varTipoAlertas_R; ?></label></td>
                            <td class="text-center">
                                <?php echo $varEncuestas_R; ?>
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


<?php $form->end() ?>

<script type="text/javascript">
    $(document).ready( function () {
        $('#tblListadoAlertas').DataTable({
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

        $('#tblListadoProcesos').DataTable({
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

        $('#tblListadoTecnicos').DataTable({
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

    function opennovedad(){
        var varidtbnt1 = document.getElementById("idtbnt1");
        var varidtbnt2 = document.getElementById("idtbnt2");
        var varidnovedadt = document.getElementById("capa00t");

        varidtbnt1.style.display = 'none';
        varidtbnt2.style.display = 'inline';
        varidnovedadt.style.display = 'inline';

    };

    function closenovedad(){
        var varidtbnt1 = document.getElementById("idtbnt1");
        var varidtbnt2 = document.getElementById("idtbnt2");
        var varidnovedadt = document.getElementById("capa00t");

        varidtbnt1.style.display = 'inline';
        varidtbnt2.style.display = 'none';
        varidnovedadt.style.display = 'none';
    };

    function opennovedadp(){
        var varidtbnp1 = document.getElementById("idtbnp1");
        var varidtbnp2 = document.getElementById("idtbnp2");
        var varidnovedadp = document.getElementById("capa00p");

        varidtbnp1.style.display = 'none';
        varidtbnp2.style.display = 'inline';
        varidnovedadp.style.display = 'inline';

    };

    function closenovedadp(){
        var varidtbnp1 = document.getElementById("idtbnp1");
        var varidtbnp2 = document.getElementById("idtbnp2");
        var varidnovedadp = document.getElementById("capa00p");

        varidtbnp1.style.display = 'inline';
        varidtbnp2.style.display = 'none';
        varidnovedadp.style.display = 'none';
    };

    function varVerificar(){
        var varalertasfecha = document.getElementById("alertas-fecha").value;
        var varIdPcrc = document.getElementById("varIdPcrc").value;
        var vars2id_varIdUsuario = document.getElementById("varIdUsuario").value;

        if (varalertasfecha == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Para seguir con la búsqueda, debe de seleccionar un rango de fecha.","warning");
            return;
        }

        if (varIdPcrc == ""  && vars2id_varIdUsuario == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Para seguir con la búsqueda, debe de seleccionar un Programa/Pcrc ó debe de seleccionar un responsable valorador.","warning");
            return;
        }
    };

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
                    foreach($varDataTipos as $value){?>
                    {
                        name: "<?php echo $value['tipo_alerta'];?>",
                        y: parseFloat("<?php echo $value['varCantidadTipo'];?>"),                            
                        dataLabels: {
                            enabled: false
                        }
                    },
                <?php }?>
                        
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
                    foreach($varDataEncuestas as $value){?>
                    {
                        name: "<?php echo $value['tipoencuestas'];?>",
                        y: parseFloat("<?php echo $value['varCantidadEncuestas'];?>"),                            
                        dataLabels: {
                            enabled: false
                        }
                    },
                <?php }?>
                        
            ]
        }]
    });

    var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta charset="utf-8"/><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64 = function (s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            }, format = function (s, c) {
                return s.replace(/{(\w+)}/g, function (m, p) {
                    return c[p];
                })
            }
        return function (table, name) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = {
                worksheet: name || 'Worksheet',
                table: table.innerHTML
            }
            console.log(uri + base64(format(template, ctx)));
            document.getElementById("dlink").href = uri + base64(format(template, ctx));
            document.getElementById("dlink").download = "Reporte de Alertas";
            document.getElementById("dlink").target = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblListadoAlertasInvisible', 'Archivo ', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);
</script>