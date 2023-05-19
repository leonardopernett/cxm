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

$this->title = 'Distribucion Personal - Version 3.0';//nombre del titulo de mi modulo
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
        background-image: url('../../images/Parametrizador.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
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
<div class="capaInfo" id="idCapaInfo" style="display: inline;"><!-- div principal que va llevar todo menos la imagen-->
  
    <div class="row"><!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
        <div class="col-md-6">
          <div class="card1 mb" style="background: #6b97b1; ">
              <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Subir Asesores Nuevos "?> </label><!--titulo principal de mi modulo-->
          </div>
        </div>
    </div><!-- divdel subtitilo azul principal que va llevar el nombre del modulo---------------------->

    <br>
    <?php $form = ActiveForm::begin(["method" => "post","enableClientValidation" => true,'options' => ['enctype' => 'multipart/form-data'],'fieldConfig' => ['inputOptions' => ['autocomplete' => 'off']]]) ?>

    <div class="row">

        <div class="col-md-4">
            <div class="card1">
                <label><em class="fas fa-save" style="font-size: 20px; color: #337ab7;"></em> <?= Yii::t('app', 'Seleccionar archivo') ?></label>
                <?= $form->field($model, "file[]")->fileInput(['id'=>'idinput','multiple' => false])->label('') ?>

                <br>

                <?= Html::submitButton("Subir", ["class" => "btn btn-primary", "onclick" => "cargar();"]) ?>
                
            </div>

            
            <br>
            <div class="card1">
            <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 20px; color: #337ab7;"></em> <?= Yii::t('app', 'Descargar Plantilla') ?></label> 
            <a style=" background-color: #337ab7" class="btn btn-success" rel="stylesheet" type="text/css" href="..\..\downloadfiles\Plantilla_ParametrizarNuevos.xlsx" title="Descagar Plantilla del Glosario" target="_blank">
            <em  style="font-size: 15px; color: #E6E6E6;"></em>  Descargar Plantilla</a>
            </div>
            <br>
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #707372;"></em> Cancelar y Regresar...</label><!-- label del titulo de lo que vamos a mostrar ------>
                <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',//index es para que me redireccione o sea volver a el inicio 
                                                'style' => 'background-color: #707372',//color del boton  
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar'])//titulo del boton  
                ?>
            </div>
        </div>
   

   
        <div class="col-md-8">
            <div class="card1 mb">
                <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" ><!--Titulo de la tabla no se muestra-->
                    <caption><label><em class="fas fa-list" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Asesores Nuevos') ?></label></caption><!--Titulo de la tabla si se muestra-->
                    <thead><!--Emcabezados de la tabla -->
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Usuario Red') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Nombre') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Equipo') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Acción Eliminar') ?></label></th>
                    </thead>
                    <tbody><!--Tbody de la tabla -->
                    
                         <?php
                            foreach ($datosTablaGlobal as $key => $value) {

                        ?>

                        <tr><!--Filas de la tabla -->
                        <td class="text-center"><label style="font-size: 12px;"><?php echo $value['dsusuario_red']; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo $value['name']; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo $value['name_equipo']; ?></label></td>
                        <td class="text-center"><!--boton eliminar que esta dentro de esa fila-->
                        <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deleteasesor','id'=> $value['id'],'id_general' => $id_general], 
                        ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?></td>
                        </tr>
                              
                        <?php  }   ?>

                
                
                
                
                    </tbody><!--fin Tbody de la tabla -->
                </table><!--fin  de la tabla -->
               
            
            </div>
        </div>
    

<script>  

$(document).ready( function () {
    $('#myTable').DataTable({
      responsive: true,
      fixedColumns: true,
      select: true,
      "language": {
        "lengthMenu": "Cantidad de Datos a Mostrar",
        "zeroRecords": "No se encontraron datos ",
        "info": "Mostrando p&aacute;gina Page a Pages de Max registros",
        "infoEmpty": "No hay datos aun",
        "infoFiltered": "(Filtrado un Max total)",
        "search": "Buscar:",
        "paginate": {
          "first":      "Primero",
          "last":       "Ultimo",
          "next":       "Siguiente",
          "previous":   "Anterior"
        }
      } 
    });
  });
</script>


