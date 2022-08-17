<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;

$this->title = 'Gestor de Clientes - Modalidad';
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    .text{
        font-size:15px !important;
    }
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

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

    .mb{
   margin-top:25px;
    }

    .btn-success{
        border:none;
    }

</style>

<script src="../../js_extensions/jquery-2.1.3.min.js"></script>

<!-- toastr -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="../../js_extensions/cloudflare/toastr.min.js"></script>
 

<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>

<nav class="breadcrumb">
  
    <a href="<?php echo Url::to(['/hojavida/index']) ?> ">Inicio</a>

</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card1">
               <p>
                 <em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> <span> Modalidad de trabajo:</span> 
               </p>  
               <?php ActiveForm::begin([ 'action' => ['hojavida/guardarmodalidad'] , 'method' => 'post' ])  ?>

                   <input type="text" class="form-control" name="modalidad" placeholder="agregar modalidad"><br>
                   <button class="btn  btn-success">Guardar modalidad de trabajo</button>
                <?php ActiveForm::end()  ?>

                
            </div>
            <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
            <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Regresar']) 
            ?>
        </div> 
            </div>

        <div class="col-md-6">
            <div class="card1">
                <p> <em class="fas fa-list" style="font-size: 15px; color: #FFC72C;"></em> Lista de modalidad de trabajo</p>
                <table id="tblDatamodalidad" class="table table-striped table-bordered tblResDetFreed">
                    <caption>Resultados</caption>
                    <thead>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo modalidad') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($modalidad as $modal) : ?>
                          <tr>
                              <td  style="text-align:center">
                                <?= $modal['modalidad'] ?>
                              </td>
                              <td  style="text-align:center">
                                  <a href="<?php echo Url::to(['eliminarmodalidad','id'=> $modal['hv_idmodalidad']]) ?>">
                                      <em class="fa fa-trash" style="font-size: 15px;  color: #C51616;"></em>
                                    </a>
                              </td>
                          </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php if(Yii::$app->session->hasFlash('info')): ?>
   <script>
     toastr.info('<?php echo Yii::$app->session->getFlash('info') ?>').toUpperCase()
   </script>
<?php endif ?>