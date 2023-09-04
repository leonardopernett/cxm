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

$this->title = 'Héroes por el Cliente - Histórico de Héroes';
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
        height: 90px;
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
        background-image: url('../../images/heroes.png');
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
        <div class="col-md-6">
            <div class="card2 mb">
                <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Seleccionar Rango de Fecha Postulación') ?></label>     
                <?=
                    $form->field($model, 'fechacreacion', [
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

        <div class="col-md-6">
            <div class="card2 mb">
                <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Buscar Datos') ?></label>
                <?= Html::submitButton(Yii::t('app', 'Buscar'),
                          ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary',
                              'data-toggle' => 'tooltip',
                              'title' => 'Buscar Datos',
                              'onclick' => 'varVerificar();']) 
                ?>
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
                <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Nueva Búsqueda') ?></label>
                <?= Html::a('Nuevo',  ['descargarpostulacion'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Nueva Búsqueda']) 
                ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-arrow-left" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Cancelar & Regresar:') ?></label> 
                <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
                ?>
            </div>
        </div>

        <?php
        if ($varDataResultado_Descargar != null) {
            
        ?>
        <div class="col-md-4">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Descargar Datos') ?></label>
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
if ($varDataResultado_Descargar != null) {
    
?>

<!-- capa Resultados -->
<div class="capaResultado" id="capaIdResultado" style="display: none;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resultados & Cantidades') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Lista de Postulaciones') ?></label>

                <table id="tblListadoPostulaciones" class="table table-striped table-bordered tblResDetFreed">
                    <caption><?= Yii::t('app', ' Resultados de Postulaciones...') ?></caption>
                    <thead>
                        <tr>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Postulación') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo Postulación') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Postulador') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cargo del Postulador') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Postulado') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cliente') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Centros de Costos') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Estado') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Valorador') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                      foreach ($varDataResultado_Descargar as $key => $value) {   
                        $varPostulador = intval($value['id_postulador']);
                        $varPostulante = intval($value['id_postulante']);

                        $varNombrePostulador = (new \yii\db\Query())
                                            ->select(['tbl_usuarios.usua_nombre'])
                                            ->from(['tbl_usuarios'])
                                            ->where(['=','tbl_usuarios.usua_id',$varPostulador])
                                            ->scalar(); 
                        if ($varNombrePostulador == null) {
                            $varNombrePostulador = (new \yii\db\Query())
                                                    ->select(['tbl_evaluados.name'])
                                                    ->from(['tbl_evaluados'])
                                                    ->where(['=','tbl_evaluados.id',$varPostulador])
                                                    ->scalar();                             
                        }               


                        $varNombrePostulante = (new \yii\db\Query())
                                            ->select(['tbl_usuarios.usua_nombre'])
                                            ->from(['tbl_usuarios'])
                                            ->where(['=','tbl_usuarios.usua_id',$varPostulante])
                                            ->scalar();                   
                        if ($varNombrePostulante == null) {
                            
                            $varNombrePostulante = (new \yii\db\Query())
                                                    ->select(['tbl_evaluados.name'])
                                                    ->from(['tbl_evaluados'])
                                                    ->where(['=','tbl_evaluados.id',$varPostulante])
                                                    ->scalar();
                        }

                        $varCompruebaValoracion = (new \yii\db\Query())
                                                    ->select([
                                                      'tbl_heroes_valoracionpostulacion.id_valoracionpostulacion'
                                                    ])
                                                    ->from(['tbl_ejecucionformularios'])
                                                    ->join('LEFT OUTER JOIN', 'tbl_heroes_valoracionpostulacion',
                                                          'tbl_ejecucionformularios.id = tbl_heroes_valoracionpostulacion.id_valoracion')
                                                    ->join('LEFT OUTER JOIN', 'tbl_heroes_generalpostulacion',
                                                          'tbl_heroes_valoracionpostulacion.id_generalpostulacion = tbl_heroes_generalpostulacion.id_generalpostulacion')
                                                    ->where(['=','tbl_heroes_generalpostulacion.anulado',0 ])
                                                    ->andwhere(['=','tbl_heroes_valoracionpostulacion.anulado',0])
                                                    ->where(['=','tbl_heroes_valoracionpostulacion.id_generalpostulacion',$value['id_generalpostulacion']])
                                                    ->groupby(['tbl_heroes_valoracionpostulacion.id_valoracionpostulacion'])
                                                    ->count(); 
                                                    


                        $varNombreValorador = (new \yii\db\Query())
                                                ->select([
                                                  'tbl_usuarios.usua_nombre'
                                                ])
                                                ->from(['tbl_usuarios'])

                                                ->join('LEFT OUTER JOIN', 'tbl_ejecucionformularios',
                                                      'tbl_usuarios.usua_id = tbl_ejecucionformularios.usua_id')

                                                ->join('LEFT OUTER JOIN', 'tbl_heroes_valoracionpostulacion',
                                                      'tbl_ejecucionformularios.id = tbl_heroes_valoracionpostulacion.id_valoracion')

                                                ->join('LEFT OUTER JOIN', 'tbl_heroes_generalpostulacion',
                                                      'tbl_heroes_valoracionpostulacion.id_generalpostulacion = tbl_heroes_generalpostulacion.id_generalpostulacion')


                                                ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                                                ->andwhere(['=','tbl_heroes_generalpostulacion.id_generalpostulacion',$value['id_generalpostulacion']])
                                                ->scalar(); 

                    ?>
                        <tr>
                            <td><label style="font-size: 12px;"><?php echo  $value['id_generalpostulacion']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['fechacreacion']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['tipopostulacion']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varNombrePostulador; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['cargospostulacion']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varNombrePostulante; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['cliente']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['pcrc']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['estado']; ?></label></td>
                            <td><label style="font-size: 12px;">
                                <?php
                                if ($varNombreValorador) {
                                ?>
                                    <?php echo  $varNombreValorador; ?></label></td>
                                <?php
                                }else{
                                ?>
                                    <label style="font-size: 12px;"><?php echo '--'; ?></label>
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
                <table align="center">
                    <thead>
                        <tr>
                            <th class="text-center"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></th>
                            <th><?= Yii::t('app', '') ?></th>
                            <th class="text-justify"><h4><?= Yii::t('app', 'Actualmente CXM esta procesando la informacion de buscar la información requerida, por favor espere...') ?></h4></th>
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
        var varheroesfecha = document.getElementById("heroesgeneralpostulacion-fechacreacion").value;

        var varcapaIdGeneral = document.getElementById("capaIdGeneral");
        var varcapaIdLoader = document.getElementById("capaIdLoader");

        if (varheroesfecha == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Para seguir con la búsqueda, debe de seleccionar un rango de fecha.","warning");
            return;
        }else{
            varcapaIdGeneral.style.display = 'none';
            varcapaIdLoader.style.display = 'inline';
        }
    };

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
            document.getElementById("dlink").download = "Reporte Postulaciones de Héroes";
            document.getElementById("dlink").target = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblListadoPostulaciones', 'Archivo ', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>