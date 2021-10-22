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

$this->title = 'Control de Dimensionamiento';
$this->params['breadcrumbs'][] = $this->title;

    

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

    $yearActual = date("Y");

    $txtCountId = Yii::$app->db->createCommand("select count(*) from tbl_control_dimensionamiento where usua_id = $sessiones")->queryScalar(); 

          $querys =  new Query;
          $querys ->select(['*'])->distinct()
                    ->from('tbl_control_dimensionamiento')  
                    ->where(['tbl_control_dimensionamiento.anulado' => 'null'])
                    ->andwhere('tbl_control_dimensionamiento.usua_id = '.$sessiones.'');
          $command = $querys->createCommand();
          $data = $command->queryAll(); 

      $txtMes = null;
      $txtvalorMes = null;
      $txtduralla = null;
      $txttimeA = null;
      $txtactuales = null;
      $txtotras_actividad = null;
      $txtturno_promedio = null;
      $txtausentismo = null;
      $txtvaca_permi_licen = null;
      $txtIdDimensionar = null;
      $txtduracion_ponde = null;
      $txtocupacion = null;
      $txthorasCNX = null;
      $txtuti_gentes = null;
      $txthoras_laboral_mes = null;
      $txtp_monit = null;
      $txtp_otras_actividad = null;
      $txtpnas_vacaciones = null;
      $txtpnas_ausentismo = null;
      $txtexceso_deficit = null;

      $varmeses = ['Enero' => 'Enero', 'Febrero' => 'Febrero', 'Marzo' => 'Marzo', 'Abril' => 'Abril', 'Mayo' => 'Mayo', 'Junio' => 'Junio', 'Julio' => 'Julio', 'Agosto' => 'Agosto', 'Septiembre' => 'Septiembre', 'Octubre' => 'Octubre', 'Noviembre' => 'Noviembre', 'Diciembre' => 'Diciembre'];

$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='popover']").popover(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);
//echo Html::jsFile("js/qa.js") 
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
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
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
<div class="capaPP" style="display: inline;">
  <div class="row">
    <div class="col-md-3">
            <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 15px; color: #9F9AE1;"></em> Buscar Dimensionamiento: </label>
                        
                        
                            <?= $form->field($model, 'month')->dropDownList($varmeses, ['prompt' => 'Seleccione...', 'id'=>"txtmesesid" ])->label('') ?>
                            <br>
                            <?= Html::submitButton(Yii::t('app', 'Buscar'),
                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Buscar Dimensionamiento',
                                    'onclick' => 'busca();']) 
                            ?>
                        
                    </div>
                </div>
            </div>
            <?php $form->end() ?> 
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #C148D0;"></em> Crear Dimensionamiento: </label>
                        <?=  Html::a(Yii::t('app', 'Crear'),
                                                'javascript:void(0)',
                                                [
                                                    'class' =>  'btn btn-primary',
                                                    'title' => Yii::t('app', 'Crear Dimensionamiento'),
                                                    //'data-pjax' => '0',
                                                    'onclick' => "                                                                                
                                                        $.ajax({
                                                            type     :'get',
                                                            cache    : false,
                                                            url  : '" . Url::to(['createdimensionar']) . "',
                                                            success  : function(response) {
                                                                $('#ajax_result').html(response);
                                                            }
                                                        });
                                                    return false;",
                                                ]);
                        ?>
                    </div>
                </div>
            </div>  
            <br>
            <?php // if ($sessiones == '0') { ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 15px; color: #559FFF;"></em> Actualizar Dimensionamiento: </label>
                        <?=  Html::a(Yii::t('app', 'Actualizar'),
                                                'javascript:void(0)',
                                                [
                                                    'class' =>  'btn btn-primary',
                                                    'title' => Yii::t('app', 'Actualizar Dimensionamiento'),
                                                    //'data-pjax' => '0',
                                                    'onclick' => "                                                                                
                                                        $.ajax({
                                                            type     :'get',
                                                            cache    : false,
                                                            url  : '" . Url::to(['updatedimensionar']) . "',
                                                            success  : function(response) {
                                                                $('#ajax_result').html(response);
                                                            }
                                                        });
                                                    return false;",
                                                ]);
                        ?>
                    </div>
                </div>   
            </div>  
            <br>
            <?php // } ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #559FFF;"></em> Descargar Informaci&oacute;n: </label>
                        <?= Html::button('Descargar', ['value' => url::to('enviararchivo'), 'class' => 'btn btn-success', 'id'=>'modalButton6',
                                'data-toggle' => 'tooltip',
                                'title' => 'Desargar', 'style' => 'background-color: #337ab7']) 
                            ?>  
                            <?php
                                Modal::begin([
                                    'header' => '<h4></h4>',
                                    'id' => 'modal6',
                                       //'size' => 'modal-lg',
                                    ]);

                                    echo "<div id='modalContent6'></div>";
                                                                                
                                Modal::end(); 
                            ?>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #fd7e14;"></em> Lista de Resultados: </label>
                    <?php 
                        if (count($varListresult) != 0) {
                            foreach ($varListresult as $key => $value) {
                                
                                $varAnnio = $value['Annio'];
                                $varMes = $value['Mes'];

                                $txtFecha = $varMes.' - '.$varAnnio;
                    ?>
                    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                        <caption>Resultados</caption>
                        <thead>
                            <th scope="col" class="text-center" colspan="4" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo 'Dimensionamiento creado por: '.$value['usua_nombre'].' - Fecha del Dimensionamiento: '.$txtFecha; ?></label></th>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Valoraciones al mes"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo $value['CantValor']; ?></label></td>

                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Duraci&oacute;n llamadas muestreo (En segundos)"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo $value['TiempoLlamada'].' (Segundos)'; ?></label></td>
                            </tr>
                            <tr>
                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Tiempo adicional al muestreo (En segundos)"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo $value['TiempoAdicional'].' (Segundos)'; ?></label></td>

                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "T&eacute;cnicos Cx actuales (incluye encargos y oficiales)"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo $value['TecnicosActua']; ?></label></td>
                            </tr>
                            <tr>
                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "%  del tiempo de t&eacute;cnico que invierte a en otras actividades"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo $value['OtrasActivi'].'%'; ?></label></td>
                            
                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Turno Promedio en la semana del t&eacute;cnico"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo $value['TurnoPromedio']; ?></label></td>
                            </tr>
                            <tr>
                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo " % Ausentismo"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo $value['Ausentismos'].'%'; ?></label></td>
                            
                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "% Vacaciones, permisos y licencias"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo $value['Vacaciones'].'%'; ?></label></td>
                            </tr>                            
                            <tr>
                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Duraci&oacute;n ponderada de actividades (En Segundos)"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo $value['duracion_ponde']; ?></label></td>

                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Ocupaciones"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo round($value['ocupacion']*100,2).'%'; ?></label></td>
                            </tr>                            
                            <tr>
                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Carga de trabajo"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo round($value['carga_trabajo'],0); ?></label></td>

                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Horas de conexi&oacute;n"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo round($value['horasCNX'],0); ?></label></td>
                            </tr>                            
                            <tr>
                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Utilizaci&oacute;n de agentes"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo round($value['uti_gentes']*100,2).'%'; ?></label></td>

                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Horas minimas de monitoreo"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo round($value['horas_nomina_monit'],0); ?></label></td>
                            </tr>                            
                            <tr>
                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Horas laborales del mes"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo round($value['horas_laboral_mes'],0); ?></label></td>

                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "FTE"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo round($value['FTE'],0); ?></label></td>
                            </tr>                            
                            <tr>
                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "T&eacute;cnicos en monitoreo"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo round($value['p_monit'],0); ?></label></td>

                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "T&eacute;cnicos en otras actividades"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo round($value['p_otras_actividad'],0); ?></label></td>
                            </tr>                            
                            <tr>
                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "T&eacute;cnicos CX requeridos"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo round($value['personas'],0); ?></label></td>

                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "T&eacute;cnicos CX para vacaciones"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo round($value['pnas_vacaciones'],0); ?></label></td>
                            </tr>                            
                            <tr>
                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "T&eacute;cnicos CX en ausentismos"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo round($value['pnas_ausentismo'],0); ?></label></td>

                                <td style="background-color: #C6C6C6;"><label style="font-size: 13px;">
                                <?php
                                    echo Html::tag('span', '<i class="fas fa-info-circle" style="font-size: 18px; color: #C178G9;"></i>', [
                                                'data-title' => Yii::t("app", ""),
                                                'data-content' => '
                                                * Si este número es menor a cero significa que la operaci&oacute;n tiene un déficit.
                                                * Si este número es mayor a 0 significa que hay un exceso de técnicos.
                                                * Si este número es cero, significa que la capacidad de técnicos esta bien.
                                                ',
                                                'data-toggle' => 'popover',
                                                'style' => 'cursor:pointer;'
                                    ]);
                                ?>
                                <?php echo "T&eacute;cnicos en exceso/deficit"; ?></label></td>
                                <td><label style="font-size: 13px;"><?php echo round($value['exceso_deficit'],0); ?></label></td>
                            </tr>
                        </tbody>

                    </table>
                    <?php
                            }
                        }
                    ?>
                        
            </div>
        </div>
  </div>  
</div>
<hr>
<?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
?>
<script type="text/javascript">
  function busca(){
    var vartxtmesesid = document.getElementById("txtmesesid").value;

    if (vartxtmesesid == "") {
      event.preventDefault();
      swal.fire("Advertencia","Debe de seleccionar un mes para buscar","warning");
      return;
    }
  };
</script>