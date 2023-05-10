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

$this->title = 'Detalle Alerta';//nombre del titulo de mi modulo
$this->params['breadcrumbs'][] = $this->title;

$sesiones =Yii::$app->user->identity->id;   

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

$varServicio = (new \yii\db\Query())
                                ->select([
                                  'aa.name'
                                ])
                                ->from(['tbl_arbols aa'])
                                ->join('LEFT OUTER JOIN', 'tbl_arbols a',
                                  'aa.id = a.arbol_id')
                                ->join('LEFT OUTER JOIN', 'tbl_alertascx cx',
                                  'a.id = cx.pcrc')
                                ->where(['=','cx.id',$id])
                                ->scalar(); 


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
        background-image: url('../../images/Alertas-Valoración.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>

<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >

<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>

<br><br>

<!-- Capa Informacion -->
<div class="capaInfo" id="idCapaInfo" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Detalle de la Alerta') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">

        <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Listado de la Alerta') ?></label>
        
        <table id="myTableInfo" class="table table-hover table-bordered" style="margin-top:12px">
        <caption><?= Yii::t('app', '...') ?></caption>
          <tr>
            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha de Envio') ?></label></th>
            <td><?php echo $model['Fecha'] ?></td>

            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Programa PCRC') ?></label></th>
            <td><?php echo $model['Programa'] ?></td>         
          </tr>
          <tr>
            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cliente') ?></label></th>
            <td><?php echo $varServicio ?></td>

            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Valorador') ?></label></th>
            <td><?php echo $model['Tecnico'] ?></td>      
          </tr>
          <tr>
            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Tipo de Alerta') ?></label></th>
            <td><?php echo $model['Tipoalerta'] ?></td>

            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Destinatarios') ?></label></th>
            <td style="max-width:300px;word-wrap: break-word;"><?php echo $model['Destinatarios'] ?></td>         
          </tr>
          <tr>
            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Asunto') ?></label></th>
            <td><?php echo $model['Asunto'] ?></td>

            <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Comentarios') ?></label></th>
            <td style="max-width:300px;"><?php echo $model['Comentario'] ?></td>
          </tr>
        </table>

      </div>
    </div>
  </div>

</div>

<hr>

<!-- Capa Detalle de la Encuesta -->
<div class="capaEncuesta" id="capaIdEncuesta" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Detalle de la Encuesta & Adjunto') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb">

        <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #707372;"></em><?= Yii::t('app', ' Id de la Alerta') ?></label>
        <label style="font-size: 15px;"><?= Yii::t('app', $model['id']) ?></label>

        <br>

        <label style="font-size: 15px;"><em class="fas fa-info" style="font-size: 20px; color: #707372;"></em><?= Yii::t('app', ' Ver Detalle') ?></label>
        <?php
          $varVerificarEncuesta = (new \yii\db\Query())
                                  ->select('*')
                                  ->from(['tbl_encuesta_saf'])
                                  ->join('INNER JOIN', 'tbl_respuesta_encuesta_saf resp', 'resp.id_respuesta = tbl_encuesta_saf.resp_encuesta_saf')
                                  ->where(['=','id_alerta',$id])
                                  ->count();

          if ($varVerificarEncuesta != 0) {
           
        ?>

          <?= Html::a('Ver Detalle',  ['totalcomensaf','id'=>$id], ['class' => 'btn btn-success',                     
                      'data-toggle' => 'tooltip',
                      'title' => 'Ver'])
          ?>

        <?php
          }else{
        ?>
          <label style="font-size: 15px;"><?= Yii::t('app', 'Actualmente no tiene encuesta asociada.') ?></label>
        <?php
          }
        ?>

      </div>
    </div>

    <div class="col-md-6">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #707372;"></em> <?= Yii::t('app', ' Archivo Adjunto de la Alerta') ?></label>
        <img src="../../../alertas/<?php echo $model['Adjunto'] ?>" alt="Image.png">
      </div>
    </div>
  </div>

</div>

<hr>

<!-- Capa Botones -->
<div class="capaBtns" id="capaIdBtns" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #707372;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?></label>
        <?= Html::a('Regresar',  ['basesatisfaccion/alertasvaloracion'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
        ?>
      </div>      
    </div>
  </div>

</div>

<hr>
