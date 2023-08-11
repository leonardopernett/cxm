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

$this->title = 'Alertas - Procesos Parametrizador';
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
    background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
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

                <div class="row">
                    <div class="col-md-2" align="text-center">
                        <label style="font-size: 15px;"><em class="fas fa-envelope" style="font-size: 50px; color: #FFC72C;"></em></label>
                    </div>

                    <div class="col-md-10" align="left">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' ¡Qué bueno que estés aquí! Te comentamos que el módulo actual permite el re-envio de alertas al correo con su respectiva encuesta.') ?></label>
                    </div>
                </div>          

            </div>        
        </div>

        <div class="col-md-6">
            <div class="card2 mb">

                <div class="row">
                    <div class="col-md-2" align="text-center">
                        <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 50px; color: #FFC72C;"></em></label>
                    </div>

                    <div class="col-md-10" align="left">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' Es importante recordar...') ?></label>  <label style="font-size: 15px; color: #981F40"> <?= Yii::t('app', ' Si se envia una alerta a varios correos, estos deben estar separados por una coma (,).') ?></label>
                    </div>
                </div>   
            </div>
        </div>
    </div>

</div>

<hr>

<?php $form = ActiveForm::begin(); ?>

<!-- Capa Envio Alerta y Encuesta -->
<div class="capaReenvio" id="capaIdReenvio" style="display: inline;">

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones & Procesos') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Ingresar Correos Destinatarios') ?></label>
                <?= $form->field($model, 'remitentes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id' => 'varIdReenvio','placeholder'=>'Ingresar Correos Destinatarios'])?>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Re-enviar Alerta') ?></label> 
                <?= Html::submitButton(Yii::t('app', 'Re-enviar'),
                        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',                
                        'data-toggle' => 'tooltip',
                        'title' => 'Buscar',
                        'onclick' => 'varVerificar();',
                        'id'=>'ButtonSearch']) 
                ?>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-arrow-left" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Cancelar & Regresar') ?></label> 
                <?= Html::a('Regresar',  ['reportealerta'], ['class' => 'btn btn-success',
                                                    'style' => 'background-color: #707372',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Regresar']) 
                ?>
            </div>
        </div>
    </div>

</div>

<hr>

<!-- Capa Información -->

<!-- Capa Alertas -->
<div class="capaAlerta" id="capaIdAlerta" style="display: inline;">

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Información Alertas') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">               

                <table id="myTableInfo" class="table table-hover table-bordered" style="margin-top:12px">
                    <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Datos de la Alerta') ?></label></caption>
                    <tr>
                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha de Envio') ?></label></th>
                        <td><label style="font-size: 12px;"><?php echo $varFecha_enviados; ?></label></td>

                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Programa PCRC') ?></label></th>
                        <td><label style="font-size: 12px;"><?php echo $varName_enviados; ?><</label>/td>         
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Tipo de Alerta') ?></label></th>
                        <td><label style="font-size: 12px;"><?php echo $varTipoAlerta_enviados; ?></label></td>

                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Valorador') ?></label></th>
                        <td><label style="font-size: 12px;"><?php echo $varUsuaNombre_enviados; ?></label></td>      
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Asunto') ?></label></th>
                        <td><label style="font-size: 12px;"><?php echo $varAsunto_enviados; ?></label></td>

                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Destinatarios') ?></label></th>
                        <td ><label style="font-size: 12px;"><?php echo $varRemitentes_enviados; ?></label></td>         
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Comentarios') ?></label></th>
                        <td colspan="3"><label style="font-size: 12px;"><?php echo $varComentarios_enviados; ?></label></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>

<hr>

<?php $form->end() ?>

<script type="text/javascript">
    function varVerificar(){
        var varIdReenvio = document.getElementById("varIdReenvio").value;

        if (varIdReenvio == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresa el correo para el envio de la alerta.","warning");
            return;
        }
    };
</script>