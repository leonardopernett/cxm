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
use app\models\ControlProcesosPlan;
use yii\db\Query;

$this->title = 'Gestor de Clientes - Datos Academicos';
$this->params['breadcrumbs'][] = $this->title;

?>
   
<style>
    .card1 {
            height: auto;
            width: 100%;
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

    table.table tbody tr td,
            table.table tbody tr td a,
            table.table thead tr th a{    
                font-size: 15px !important ;
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
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

    
    .input-area{
            width: 400px;
            height: 100px;
            border: 2px dotted #002855;
            margin: 0 auto;
            position:relative;
          }

          .input-text{
            
            display: flex;
            height: 100%;
            justify-content: center;
            align-items: center;
            font-size: 16px;
            color:#002855

          }

          .input-file{
            position: absolute;
            left:0;
            right:0;
            top:0;
            bottom:0;
            opacity:0 ;
            width: 100%;
            height:100%;
            cursor:pointer;
          }

          .button{
            text-align: center;
            margin-top: 15px;
            display: block;
            position:relative;
           }

           .tbody {
            overflow-y:scroll;
            height:300px;
            display:block;
            width:100%;
           }

</style>
<!-- datatable -->
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">


<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>

<div class="breadcrumb">
    
    <a href=" <?php echo Url::to(['hojavida/index']) ?> ">Inicio</a>
    
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <div class="card1">
            <?php ActiveForm::begin(['action'=>['/hojavida/profesion'], 'method'=>'POST']);  ?>
                  <div class="form-group row">
                      <div class="col-md-10">
                      <label for="">Agregar Profesi&oacute;n</label>
                      </div>
                      <div class="col-md-8">
                        <input type="text" class="form-control" placeholder="Profesion" name="profesion" required>
                      </div>

                      <div class="col-md-2">
                        <button class="btn btn-success" >Agregar</button>
                      </div>
                  </div>
              <?php ActiveForm::end();  ?>
             
            </div>
         </div>

           <div class="col-md-3">
               <div class="card1">
               <?php ActiveForm::begin(['action'=>['hojavida/especializacion'], 'method'=>'post']);  ?>
                    <div class="form-group row">
                        <div class="col-md-10">
                        <label for="">Agregar Especializaci&oacute;n</label>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="especializacion" class="form-control" placeholder="Especializacion" required>
                        </div>

                        <div class="col-md-2">
                          <button class="btn btn-success">Agregar</button>
                        </div>
                    </div>
                <?php ActiveForm::end();  ?>

             
               </div>
           </div>

           <div class="col-md-3">
              <div class="card1">
              <?php ActiveForm::begin(['action'=>['hojavida/maestria'], 'method'=>'post']);  ?>
                    <div class="form-group row">
                        <div class="col-md-10">
                        <label for="">Agregar Maestr&iacute;a</label>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="maestria" class="form-control" placeholder="Maestria" required>
                        </div>

                        <div class="col-md-2">
                          <button class="btn btn-success">Agregar</button>
                        </div>
                    </div>
                <?php ActiveForm::end();  ?>


               
              </div>
           </div>

            <div class="col-md-3">
               <div class="card1">
               <?php ActiveForm::begin(['action'=>['hojavida/doctorado'], 'method'=>'post']);  ?>
                    <div class="form-group row">
                        <div class="col-md-10">
                        <label for="">Agregar Doctorado</label>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="doctorado" class="form-control" placeholder="Doctorado" required>
                        </div>

                        <div class="col-md-2">
                          <button class="btn btn-success">Agregar</button>
                        </div>
                    </div>
                <?php ActiveForm::end();  ?>
               </div>
            </div>


        </div>
    </div>
</div>

<div class="container-fluid" style="margin:20px">
    <div class="row">
        <div class="col-md-3">
            <div class="card1">
                <table id="tblDataprofesion" class="table table-striped table-bordered tblResDetFreed">
                    <caption>Resultados</caption>
                    <thead>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Profesión') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($profesion as $pro) { ?>
                             <tr>

                                 <td>
                                     <?php echo $pro['hv_cursos'] ?>
                                 </td>
                                 <td style="text-align:center">                                          
                                     <a href="<?php echo Url::to(['hojavida/eliminarprofesion', 'id' => $pro['idhvcursosacademico']]) ?>" style="color:#981F40 !important">
                                         <em class="fa fa-trash"></em>
                                     </a>
                           
                                 </td>
                             </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1">
                <table id="tblDataespecializacion" class="table table-striped table-bordered tblResDetFreed">
                    <caption>Resultados</caption>
                    <thead>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Especialización') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($especializacion as $pro) { ?>
                             <tr>
                                 <td>
                                     <?php echo $pro['hv_cursos'] ?>
                                 </td>

                                 <td style="text-align:center">                                          
                                     <a href="<?php echo Url::to(['hojavida/eliminarespecializacion', 'id' => $pro['idhvcursosacademico']]) ?>" style="color:#981F40 !important">
                                         <em class="fa fa-trash"></em>
                                     </a>
                           
                                 </td>
                             </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1">
                <table id="tblDatamaestria" class="table table-striped table-bordered tblResDetFreed">
                    <caption>Resultados</caption>
                    <thead>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Maestría') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($maestria as $pro) { ?>
                             <tr>
                                 <td>
                                     <?php echo $pro['hv_cursos'] ?>
                                 </td>

                                 <td style="text-align:center">                                          
                                     <a href="<?php echo Url::to(['hojavida/eliminarespecializacion', 'id' => $pro['idhvcursosacademico']]) ?>" style="color:#981F40 !important">
                                         <em class="fa fa-trash"></em>
                                     </a>
                           
                                 </td>
                             </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1">
                <table id="tblDatadoctorado" class="table table-striped table-bordered tblResDetFreed">
                    <caption>Resultados</caption>
                    <thead>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Doctorado') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($doctorado)) { ?>
                             <tr>
                                 <td colspan="2" style="text-align:center">No hay datos aun</td>
                             </tr>
                        <?php }else { ?>
                            <?php foreach ($doctorado as $pro) { ?>
                             <tr>
                                 <td>
                                     <?php echo $pro['hv_cursos'] ?>
                                 </td>

                                 <td style="text-align:center">                                          
                                     <a href="<?php echo Url::to(['hojavida/eliminarespecializacion', 'id' => $pro['idhvcursosacademico']]) ?>" style="color:#981F40 !important">
                                         <em class="fa fa-trash"></em>
                                     </a>
                           
                                 </td>
                             </tr>
                        <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<div class="container-fluid">
    <div class="row">
    <div class="col-md-3">
    <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
            <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Regresar']) 
            ?>
        </div> 
    </div>
    </div>
</div>