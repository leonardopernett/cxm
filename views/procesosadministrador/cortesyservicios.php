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

$this->title = 'Procesos Administrador - Parametrizar Servicios en Cortes';//nombre del titulo de mi modulo
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
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
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
        <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Parametrizar Servicios en Cortes"; ?> </label><!--titulo principal de mi modulo-->
      </div>
    </div>
  </div><!-- divdel subtitilo azul principal que va llevar el nombre del modulo---------------------->

  <br>

  <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
  <div class="row"><!--incio del  div que divide todo en una fila  -->
    
    <div class="col-md-4"><!-- div que me divide en 4 la carta ----------------------> 
      <div class="card1 mb"><!-- div de la carta principal donde van a ir la informacion ----( me pone la tarjeta)------------------> 
        
        <label><em class="fas fa-check" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Seleccionar Corte') ?></label> <!-- label  del titulode lo que vamos a mostrar ------> 
        <?=  $form->field($model, 'id_corte', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Grupocortes::find()->distinct()->where("anulado = 0")->all(), 'idgrupocorte', 'nomgrupocorte'),
                                        [
                                            'prompt'=>'Seleccione Corte...', //placeholder de lo que se va a mostrar
                                        ]
                                )->label(''); // para que tome el lavel de arriba 
        ?>
        <br>
    
        <label><em class="fas fa-check" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Seleccionar Servicio') ?></label> <!-- label  del titulo de lo que vamos a mostrar ------>
        <?=  $form->field($model, 'id_servicio', 
        ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
        ->dropDownList(ArrayHelper::map(\app\models\ProcesosClienteCentrocosto::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                        [
                                            'prompt'=>'Seleccione Servicio...',//placeholder de lo que se va a mostrar
                                        ]
                                )->label('');  // para que tome el lavel de arriba 
        ?>
        <br>
        <?= Html::submitButton(Yii::t('app', 'Guardar'),//nombre del boton
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onClick'=> 'varVerificar();', //funcion de JS que al dar clic verifique que estoy enviando 
                                'title' => 'Guardar']) 
        ?>
      </div> 
      <br>
      <div class="card1 mb"><!-- div de la carta principal donde van a ir la informacion ----------------------> 
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #ffc034;"></em> Cancelar y Regresar...</label><!-- label del titulo de lo que vamos a mostrar ------>
        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',//index es para que me redireccione o sea volver a el inicio 
                                        'style' => 'background-color: #707372',//color del boton  
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar'])//titulo del boton  
        ?>
       </div><!-- fin del div del boton regresar -->
    </div><!-- fin del div de la tarjeta de tamaño 4 -->
  
  <?php ActiveForm::end(); ?> <!-- finalizacion del formulario-->
    <div class="col-md-8"><!-- inicio del div de la tarjeta de tamaño 8 -->
      
      <div class="card1 mb"><!-- div de la carta principal donde van a ir la informacion ---( me pone la tarjeta)-------------------> 

        <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" ><!--Titulo de la tabla no se muestra-->
          <caption><label><em class="fas fa-list" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Corte y Servicio') ?></label></caption><!--Titulo de la tabla si se muestra-->
          <thead><!--Emcabezados de la tabla -->
            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Corte') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Servicio') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Acción Eliminar') ?></label></th>
          </thead>
          <tbody><!--Tbody de la tabla -->
          
          <?php
          foreach ($varData as $key => $value) {

          ?>

            <tr><!--Filas de la tabla -->
            <td class="text-center"><label style="font-size: 12px;"><?php echo $value['nomgrupocorte']; ?></label></td>
            <td class="text-center"><label style="font-size: 12px;"><?php echo $value['cliente']; ?></label></td>
            <td class="text-center"><!--boton eliminar que esta dentro de esa fila-->
              <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deleteserviciocorte','id'=> $value['id_corteservicios']], 
              ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?></td>
          </tr>
          <?php  }   ?>      
         

             
              
              
               
          </tbody><!--fin Tbody de la tabla -->
        </table><!--fin  de la tabla -->

      </div><!--fin de la tarjeta dond esta la tabla -->

    </div><!--fin de la div donde esta el tamaña de la tarjeta -->

  </div><!--fin del div donde divide todo en una fila -->


</div><!--fin del div capInf la principal  -->
<hr>

<script>
  function varVerificar() {

    var corte = document.getElementById("corteservicios-id_corte").value;  
    var servicio = document.getElementById("corteservicios-id_servicio").value;

    if (corte == "") {
      event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe seleccionar un corte","warning");
            return;
    }
    if (servicio == "") {
      event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe seleccionar un servicio","warning");
            return;
    }
    
  }
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