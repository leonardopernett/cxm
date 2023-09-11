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

$this->title = 'Alertas - Encuesta de Satisfacción';
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
    height: 462px;
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
    background-image: url('../../images/satisfaccion2.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<!-- Data extensiones -->
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

<?php
if ($varConteoEncuestas == 0) {
  
?>

<?php
if ($varMensajes_encuestas != 0) {
?>

<!-- Capa Sin Encuestas -->
<div class="capaSinEncuestas" id="capaIdSinEncuestas" style="display: inline;">

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
            <div class="card1 mb">

                <div class="row">
                    <div class="col-md-2 text-center">
                        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 50px; color: #FFC72C;"></em></label>
                    </div>

                    <div class="col-md-10 left">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' En hora buena '.$varNameJarvis.'! Te comentamos que la encuesta ya fue guardada, te invitamos a revisar el histórico de alertas.') ?></label>
                    </div>
                </div>          

            </div>        
        </div>

        <div class="col-md-6">
            <div class="card1 mb">

                <div class="row">
                    <div class="col-md-2 text-center">
                        <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 50px; color: #FFC72C;"></em></label>
                    </div>

                    <div class="col-md-10 left">
                        <label style="font-size: 15px;"><em class="fas fa-arrow-left" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Reportes de Alertas') ?></label> 
                        <?= Html::a('Reportes',  ['reportealerta'], ['class' => 'btn btn-success',
                                                            'style' => 'background-color: #707372',
                                                            'data-toggle' => 'tooltip',
                                                            'title' => 'Reporte de Alertas']) 
                        ?>
                    </div>
                </div>          

            </div>        
        </div>
    </div>

</div>

<hr>

<?php
}else{
?>

<?php $form = ActiveForm::begin(); ?>


<!-- Capa Procesos -->
<div class="capaProcesos" id="capaIdProcesos" style="display: inline;">
	
	<div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones E Información') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-7">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Datos de la Alerta') ?></label> 
                <table id="myTableInfo" class="table table-hover table-bordered" style="margin-top:12px">
                    <caption><label style="font-size: 15px;"><?= Yii::t('app', ' ...') ?></label></caption>
                    <tr>
                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha de Envio') ?></label></th>
                        <td><label style="font-size: 12px;"><?php echo $varFecha_encuesta; ?></label></td>

                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Programa PCRC') ?></label></th>
                        <td><label style="font-size: 12px;"><?php echo $varName_encuesta; ?></label></td>         
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Tipo de Alerta') ?></label></th>
                        <td><label style="font-size: 12px;"><?php echo $varTipoAlerta_encuesta; ?></label></td>

                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Valorador') ?></label></th>
                        <td><label style="font-size: 12px;"><?php echo $varUsuaNombre_encuesta; ?></label></td>      
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Asunto') ?></label></th>
                        <td><label style="font-size: 12px;"><?php echo $varAsunto_encuesta; ?></label></td>

                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Destinatarios') ?></label></th>
                        <td ><label style="font-size: 12px;"><?php echo $varRemitentes_encuesta; ?></label></td>         
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Comentarios') ?></label></th>
                        <td colspan="3"><label style="font-size: 12px;"><?php echo $varComentarios_encuesta; ?></label></td>
                    </tr>
                </table>
            </div>

            <br>

            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-star" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Seleccionar Indicador de Satisfacción') ?></label> 

                <table id="tblListadoSatu" style="width:100%">
                    <caption><?= Yii::t('app', ' .') ?></caption>
                        <tr>
                          <th scope="col" class="text-center" style="width: 100px;">
                            <img src='../../images/satisfecho.png' alt="satisafecho">
                          </th>
                          <th scope="col" class="text-center" style="width: 100px;">
                            <img src='../../images/mediosatisfecho.png' class="img-responsive" alt="medio">
                          </th>
                          <th scope="col" class="text-center" style="width: 100px;">
                            <img src='../../images/neutro.png' class="img-responsive" alt="neutro">
                          </th>
                          <th scope="col" class="text-center" style="width: 100px;">
                            <img src='../../images/medioinsatisfecho.png' class="img-responsive" alt="medioinsatu">
                          </th>
                          <th scope="col" class="text-center" style="width: 100px;">
                            <img src='../../images/insatisfecho.png' class="img-responsive" alt="insatu">
                          </th>
                        </tr>
                        <tr>
                        <?php
                        foreach ($varListEncuestas as $value) {
                        ?>
                        
                            <td  class="text-center" style="width: 100px;">
                                <div class="d-md-flex justify-content-left align-content-left flex-column text-left">
                                    <?= $form->field($model, 'id_tipoencuestas', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->radio(['style'=>'font-size: smaller;', 'value' => $value['id_tipoencuestas'], 'uncheck' => null])->label($value['peso'].' - '.$value['tipoencuestas']);?> 
                                </div>
                            </td>
                        
                        <?php
                        }
                        ?>
                        </tr>
                </table>
                <?= $form->field($model, 'comentarios', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['id'=>'varIdComentarios','class'=>'hidden','value'=>$varComentarios_encuesta])?>

                <br>

                <?= Html::submitButton(Yii::t('app', 'Guardar Encuesta'),
                        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',                
                        'data-toggle' => 'tooltip',
                        'title' => 'Guardar Encuesta',
                        'id'=>'ButtonSearch']) 
                ?>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card1 mb">
                <?php
                if ($varConteoUrl == "png" || $varConteoUrl == "jpg" || $varConteoUrl == "bmp" || $varConteoUrl == "gif") {
                    
                ?>
                    <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Archivo Adjunto Tipo Imagen') ?></label>
                    <img src="<?= Url::to("@web/alertas/".$varUrlArchivo.""); ?>" alt="Card image cap" style="height: 550px;">
                    <br>
                    <?= Html::button('Ver imagen', ['value' => url::to(['verimagenalerta','varArchivo'=>$varUrlArchivo]), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Imagen de la alerta']) 
                    ?> 

                    <?php
                        Modal::begin([
                            'header' => '<h4>Ver Imagen de Alerta</h4>',
                            'id' => 'modal1',
                        ]);

                        echo "<div id='modalContent1'></div>";
                                                                                                      
                        Modal::end(); 
                    ?>
                <?php
                }else{
                ?>
                    <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Archivo Adjunto Tipo Archivo') ?></label>
                    <br>
                    <a style="font-size: 18px;" rel="stylesheet" type="text/css" href="<?= Url::to("@web/alertas/".$varUrlArchivo.""); ?>" target="_blank"><?= Yii::t('app', ' Descargar Archivo') ?></a>
                <?php
                }
                ?>
            </div>
        </div>
    </div>

</div>

<hr>

<?php $form->end() ?>

<?php
}
?>

<?php
}else{
?>

<!-- Capa Sin Encuestas -->
<div class="capaSinEncuestas" id="capaIdSinEncuestas" style="display: inline;">

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
            <div class="card1 mb">

                <div class="row">
                    <div class="col-md-2 text-center">
                        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 50px; color: #FFC72C;"></em></label>
                    </div>

                    <div class="col-md-10 left">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' ¡Hola '.$varNameJarvis.'! Te comentamos que la encuesta ya ha sido diligencia por ti, te invitamos a revisar el histórico de alertas.') ?></label>
                    </div>
                </div>          

            </div>        
        </div>
    </div>

</div>

<hr>

<?php
}
?>

