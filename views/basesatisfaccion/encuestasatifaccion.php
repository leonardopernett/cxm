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

$this->title = 'Encuesta de Satisfacción';//nombre del titulo de mi modulo
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
        background-image: url('../../images/satisfaccion2.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
    .prueba{
		height : 50%;
		width : 50%;
	}

	.prueba:hover{
		height: 100%;
		width: 100%;
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
<br><br>

<?php
  if ($varValidaEncuesta == 0) {    
?>
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?> 

<!-- Capa Procesos -->
<div class="capaProcesos" id="capaIdProcesos" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Encuesta de Satisfacción') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-8">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-smile" style="font-size: 20px; color: #1993a5;"></em><?= Yii::t('app', ' Elegir Indicador de Satisfacción') ?></label>

        <div class="row" style="display:flex;justify-content:center;align-items:center;">
          <div class="col-md-2" style="display: grid;place-items:center;">
            <img src='../../images/satisfecho.png' class="img-responsive" alt="satisafecho">
            <?= $form->field($modelo, 'resp_encuesta_saf', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->radio(['label' => '', 'value' => 5, 'uncheck' => null])->label('Satisfecho')?> 
          </div>

          <div class="col-md-2" style="display: grid;place-items:center;">
            <img src='../../images/mediosatisfecho.png' class="img-responsive" alt="medio">
            <?= $form->field($modelo, 'resp_encuesta_saf', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->radio(['label' => '', 'value' => 4, 'uncheck' => null])->label('Medio Satisfecho')?> 
           </div>

          <div class="col-md-2" style="display: grid;place-items:center;">
            <img src='../../images/neutro.png' class="img-responsive" alt="neutro">
            <?= $form->field($modelo, 'resp_encuesta_saf', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->radio(['label' => '', 'value' => 3, 'uncheck' => null])->label('Neutro')?> 
          </div>

          <div class="col-md-2" style="display: grid;place-items:center;">
            <img src='../../images/medioinsatisfecho.png' class="img-responsive" alt="medioinsatu">
            <?= $form->field($modelo, 'resp_encuesta_saf', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->radio(['label' => '', 'value' => 2, 'uncheck' => null])->label('Medio Insatisfecho')?> 
          </div>

          <div class="col-md-2" style="display: grid;place-items:center;">
            <img src='../../images/insatisfecho.png' class="img-responsive" alt="insatu">
            <?= $form->field($modelo, 'resp_encuesta_saf', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->radio(['label' => '', 'value' => 1, 'uncheck' => null])->label('Insatisfecho');?> 
          </div>
        </div>

        <hr>

        <label style="font-size: 15px;"><em class="fas fa-paperclip" style="font-size: 20px; color: #1993a5;"></em><?= Yii::t('app', ' Ingresar Comentarios') ?></label>
        <?= $form->field($modelo, 'comentario_saf', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textarea(['rows' => '3'])?> 

        <hr>

        <?= Html::submitButton(Yii::t('app', 'Guardar Encuesta'),
                            ['class' => $modelo->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'title' => 'Guardar'])
        ?>          

      </div>
    </div>

    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 20px; color: #1993a5;"></em><?= Yii::t('app', ' Archivo Adjuntado') ?></label>
        <img src="../../../web/alertas/<?php echo $model ?>" alt="Image.png"> 
      </div>
    </div>
  </div>

</div>

<hr>

<?php ActiveForm::end(); ?>

<?php
  }else{
?>

<!-- Capa Mensaje -->
<div class="capaMensaje" id="capaIdMensaje" style="display: inline;">

  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Información') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #ff453c;"></em> <?= Yii::t('app', 'De acuerdo a nuestro sistema, ya realizaste la encuesta asociada a la alerta actual con id '.$id.', Gracias. Te invitamos a seguir gestionando otras encuestas con sus alertas.') ?></label>
      </div>
    </div>
  </div>

  
</div>

<?php
  }
?>

