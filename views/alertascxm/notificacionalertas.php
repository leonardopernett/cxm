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

$this->title = 'Alertas - Notificación de Alertas';
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
    height: 150px;
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
    background-image: url('../../images/Alerta-Resumen.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

  .lds-ring {
      display: inline-block;
      position: relative;
      width: 100px;
      height: 100px;
    }
    .lds-ring div {
      box-sizing: border-box;
      display: block;
      position: absolute;
      width: 80px;
      height: 80px;
      margin: 10px;
      border: 10px solid #3498db;
      border-radius: 70%;
      animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
      border-color: #3498db transparent transparent transparent;
    }
    .lds-ring div:nth-child(1) {
      animation-delay: -0.45s;
    }
    .lds-ring div:nth-child(2) {
      animation-delay: -0.3s;
    }
    .lds-ring div:nth-child(3) {
      animation-delay: -0.15s;
    }
    @keyframes lds-ring {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
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
<!-- Capa General que contiene todas las capas -->
<div class="capaGeneral" id="capaIdGeneral" style="display: inline;">

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => [
            'autocomplete' => 'off'
        ]
    ]
  ]); 
?>

<!-- Capa Informativa -->
<div class="capaInformativa" id="capaIdInformativa" style="display: inline;">
  
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Notas Informativas') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card2 mb">
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

                <br>

                <?= Html::submitButton(Yii::t('app', 'Buscar'),
                          ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary',
                              'data-toggle' => 'tooltip',
                              'title' => 'Buscar Datos',
                              'onclick' => 'varVerificar();']) 
                ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card2 mb">
                <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Información Alertas') ?></label>
                <label  style="font-size: 15px; text-align: left;"><?= Yii::t('app', ' Hola '.$varNameJarvis_Notas.' te comentamos que el actual módulo es para verificar las alertas que te han hecho y estés enterado de tus procesos. El resultado de las alertas en este módulo es sobre mes actual.') ?></label>
            </div>
        </div>
    </div>

</div>

<hr>

<?php
if ($varDataResultado_Notas != null) {
?>

<!-- Capa Resultados -->
<div  class="capaResultados" id="capaIdResultados" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resultados & Cantidades') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-3">
            <div class="card2 mb">
                <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Cantidad de Alertas') ?></label>
                <label  style="font-size: 50px; text-align: center;"><?php echo count($varDataResultado_Notas); ?></label>
            </div>
        </div>

        <?php
            if ($roles != '272') {                                        
        ?>
                                    
        <div class="col-md-3">
            <div class="card2 mb">
                <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Cantidad de Encuestas') ?></label>
                <label  style="font-size: 50px; text-align: center;"><?php echo $varCantidadEncuestas_Notas; ?></label>
            </div>
        </div>

        <?php
            }
        ?>       

        
    </div>

    <br>

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
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Asunto de Alerta') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Encuesta') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($varDataResultado_Notas as $value) {
                            $varIdAlertas = $value['id'];
                            
                            $varFechas = $value['fecha'];
                            $varNames = $value['name'];
                            $varUsuaNombres = $value['usua_nombre'];
                            $varTipoAlertas = $value['tipo_alerta'];
                            $varAsuntoAlertas = $value['asunto'];

                            $varMesFecha = date('m', strtotime($varFechas));
                            $varMesActual = date('m');
                            
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
                                $varEncuestas = "NA";
                            }
                            
                        ?>
                        <tr>
                            <td><label style="font-size: 11px;"><?php echo  $varIdAlertas; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $varFechas; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $varNames; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $varUsuaNombres; ?></label></td>
                            <td><label style="font-size: 11px;"><?php echo  $varTipoAlertas; ?></label></td>                            
                            <td><label style="font-size: 11px;"><?php echo  $varAsuntoAlertas; ?></label></td>
                            <td class="text-center">

                                <?php
                                if ($roles == '272') {                                        
                                ?>
                                    <?php echo '--'; ?>

                                <?php
                                }else{  
                                ?>

                                    <?php
                                    if ($varEncuestas == "NA") {
                                        
                                    ?>
                                        <?php
                                            if ($varMesFecha == $varMesActual) {
                                               
                                        ?>                            

                                            <?= Html::a('<em class="fas fa-paper-plane" style="font-size: 15px; "></em>',  ['alertaencuesta','id_alerta'=> $varIdAlertas], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;  border-color: #4298b500 !important; color:#000000;", 'title' => 'Encuestar Alerta', 'target' => "_blank"]) ?>

                                        <?php
                                            }else{
                                        ?>
                                                <?php echo '--'; ?>
                                        <?php
                                            }
                                        ?>
                                        
                                    <?php
                                    }else{
                                    ?>
                                        <?php echo $varEncuestas; ?>
                                    <?php
                                    }
                                    ?>

                                <?php
                                }
                                ?>
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

<?php $form->end() ?>

</div>

<!-- Capa Mensajes de Espera -->
<div class="capaLoader" id="capaIdLoader" style="display: none;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table class="center">
                    <thead>
                        <tr>
                            <th class="text-center"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></th>
                            <th><?= Yii::t('app', '') ?></th>
                            <th class="text-justify"><h4><?= Yii::t('app', 'Actualmente CXM esta procesando la información de tus alertas de la búsqueda correspondiente a la fecha seleccionada...') ?></h4></th>
                        </tr>            
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <hr>
</div>

<script type="text/javascript">
    function varVerificar(){
        var varfechas = document.getElementById("alertas-fecha").value;

        if (varfechas == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Para seguir con la búsqueda, debe de seleccionar un rango de fecha.","warning");
            return;
        }

        var varcapaIdGeneral = document.getElementById("capaIdGeneral");
        var varcapaIdLoader = document.getElementById("capaIdLoader");

        varcapaIdGeneral.style.display = 'none';
        varcapaIdLoader.style.display = 'inline';
    };
</script>