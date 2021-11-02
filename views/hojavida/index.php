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

$this->title = 'Hoja de Vida';
$this->params['breadcrumbs'][] = $this->title;

    $sesiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
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
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

    .button {
        margin-top:10px;
        text-align:center;
    }

    .input-area{
        width:350px;
        height:100px;
        border:2px #000 dotted;
        display:flex;
        justify-content:center;
        align-items:center;
        position:relative;
        text-align:center;
        margin:0 auto;
    }
    .file {
        opacity:0;
        width:100%;
        height:100%;
        position:absolute;
        cursor:pointer;
    }
    .input-text{
        font-size:15px;
        padding-left:10px;
        text-align:center;
    }

</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<!-- toastr -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
 

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
<div class="capaBotones" style="display: inline;">
   <div class="row">

       <div class="col-md-4">
           <div class="card1 mb">
               <label style="font-size: 15px;"><em class="fas fa-chart-bar" style="font-size: 15px; color: #559FFF;"></em> Resumen General </label>
               <?= Html::a('Aceptar',  ['resumen','id'=>Yii::$app->user->identity->id], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regumen General']) 
                ?>
           </div>
       </div>

       <div class="col-md-4">
           <div class="card1 mb">
               <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #559FFF;"></em> Informaci&oacute;n Personal </label>
               <?= Html::a('Aceptar',  ['categoriascxm'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Informacion personal']) 
                ?>
           </div>
       </div>

       <div class="col-md-4">
           <div class="card1 mb">
               <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 15px; color: #559FFF;"></em> Carga Masiva </label>
                <a href="" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal3">Carga Masiva</a>
           </div>
       </div>

   </div>
</div>
<hr>
<div class="capaLista" style="display: inline;">
    <div class="row">
       <div class="col-md-12">
           <div class="card1 mb">
               <label style="font-size: 15px;"><em class="fas fa-address-book" style="font-size: 15px; color: #B833FF;"></em> Listado </label>
           </div>
       </div>
   </div>
</div>
<hr>
<?php
    if ($roles == "270") {
?>
    <div class="capaAdministrador" style="display: inline;">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                   <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 15px; color: #FFC72C;"></em> Acciones Administrativas: </label>

                   <div class="row">

                       <div class="col-md-2">
                           <div class="card1 mb">
                                <label style="font-size: 15px;"><i class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></i> Eventos: </label>
                                <?= Html::a('Crear',  ['eventos'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Crear Eventos']) 
                                ?>
                           </div>
                       </div>

                       <div class="col-md-2">
                           <div class="card1 mb">
                                <label style="font-size: 15px;"><i class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></i> Pais & Ciudad: </label>
                                <?= Html::a('Crear',  ['paisciudad'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Crear Pais & Ciudad']) 
                                ?>
                           </div>
                       </div>

                       <div class="col-md-2">
                           <div class="card1 mb">
                                <label style="font-size: 15px;"><i class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></i> Modalidad Trabajo: </label>
                                <?= Html::a('Crear',  ['crearmodalidad'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Crear Modalidad Trabajo']) 
                                ?>
                             
                           </div>
                       </div>

                       <div class="col-md-2">
                           <div class="card1 mb">
                                <label style="font-size: 15px;"><i class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></i> Datos Acad&eacute;micos: </label>
                                <?= Html::a('Crear',  ['categoriascxm'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Crear Modalidad Trabajo']) 
                                ?>
                           </div>
                       </div>

                       <div class="col-md-2">
                           <div class="card1 mb">
                                <label style="font-size: 15px;"><i class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></i> Permisos: </label>
                                <?= Html::a('Crear',  ['categoriascxm'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Crear Modalidad Trabajo']) 
                                ?>
                           </div>
                       </div>

                       <div class="col-md-2">
                           <div class="card1 mb">
                                <label style="font-size: 15px;"><i class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></i> Permisos: </label>
                                <?= Html::a('Crear',  ['categoriascxm'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Crear Modalidad Trabajo']) 
                                ?>
                           </div>
                       </div>


                   </div>

                </div>
            </div>
        </div>
    </div>
    <hr>  

    <div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalLabel">Agregar Carga Masiva</h3>

      </div>
      <div class="modal-body body">
          
            <?php $form = ActiveForm::begin(['action'=>['hvinfopersonal/export'], 'method'=>'POST', 'options'=>['enctype'=>'multipart/form-data']]) ?>
                <div class="input-area">
                      <div class="input-text" id="text">Seleccione o arrastre el archivo</div>
                      <input type="file" id="file" class="file form-control">
                  </div> 
                  <div class="button">
                      <button  class="btn btn-success">Agregar <em class="fa fa-plus" style="padding-top:5px"></em> </button>
                  </div>
            <?php ActiveForm::end() ?>

      <!-- <a href="../../archivos/ClienteCXM.xlsx" download>Descargar Plantilla de ejemplo</a><i class="fa fa-upload"></i> -->
      </div>
      
    </div>
  </div>
</div>
<?php
    }
?>

<script>
   const text = document.getElementById('text')

   document.getElementById('file').addEventListener('change',(e)=>{
        text.innerText= e.target.files[0].name
   })
</script>
