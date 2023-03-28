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

$this->title = 'Procesos Administrador - Respuestas Automáticas Q&R';
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
<br><br>
<div class="capaInfo" id="idCapaInfo" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Respuestas Automáticas para Quejas y Reclamos"; ?> </label>
      </div>
    </div>
  </div>

  <br>

  <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
  <div class="row">
    
    <div class="col-md-4">
      
      <div class="card1 mb">
      <label style="font-size: 15px;">* Seleccionar Estado...</label>
                        <?=  $form->field($model, 'id_estado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Estadosqyr::find()->distinct()->where("anulado = 0")->orderBy(['nombre'=> SORT_ASC])->all(), 'id_estado', 'nombre'),
                                        [
                                            'prompt'=>'Seleccione Estado...',
                                        ]
                                )->label(''); 
                        ?>
        <br>

            <?= $form->field($model, 'asunto', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 250, 'id' => 'idTexto', 'placeholder'=>'Ingresar Asunto'])->label('') ?>

        <br>
            <?= $form->field($model, 'comentario', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'idTexto2', 'placeholder'=>'Ingresar Comentario'])->label('') ?>

        <br>

         <?= Html::submitButton(Yii::t('app', 'Guardar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'verificar();',
                                'title' => 'Guardar Respuesta']) 
        ?>

      </div>

      <br>

      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #b52aef;"></em> Cancelar y Regresar...</label>
        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
        ?>
      </div>

    </div>

    <div class="col-md-8">
      
      <div class="card1 mb">
        <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
          <caption><label><em class="fas fa-list" style="font-size: 20px; color: #b52aef;"></em> <?= Yii::t('app', 'Listado de Respuestas Automáticas') ?></label></caption>
          <thead>
            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Id Resp.') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Asunto') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Comentario') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Estado') ?></label></th> 
            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Acción Eliminar') ?></label></th>
          </thead>
          <tbody>
            <?php
              foreach ($varListRespuesta as $key => $value) {
                $varId = $value['id_respuesta'];
                $varNombre = $value['asunto'];
                $varidEstado = $value['id_estado'];
                $varComentario = $value['comentario'];
                $varEstado = $value['anulado'];

                $varnombrestado = (new \yii\db\Query())
                          ->select(['nombre'])
                          ->from(['tbl_qr_estados'])
                          ->where(['=','id_estado',$varidEstado])
                          ->scalar(); 
            ?>
              <tr>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varId; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varNombre; ?></label></td>                
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varComentario; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varnombrestado; ?></label></td>
                <td class="text-center">
                  <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deleterespuestaqyr','id'=> $value['id_respuesta']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                </td>
              </tr>
            <?php
              }
            ?>
          </tbody>
        </table>
      </div>

    </div>

  </div>
  <?php ActiveForm::end(); ?>

</div>
<hr>

<script type="text/javascript">
  function verificar(){
    var varidEquipo = document.getElementById("idTexto").text;
    var varidcomentario = document.getElementById("idTexto2").text;

    if (varidEquipo == "" || varidcomentario =="") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Se debe llenar las respuestas","warning");
      return;
    }
  };

  $(document).ready( function () {
    $('#myTable').DataTable({
      responsive: true,
      fixedColumns: true,
      select: false,
      "language": {
        "lengthMenu": "Cantidad de Datos a Mostrar _MENU_ ",
        "zeroRecords": "No se encontraron datos ",
        "info": "Mostrando p&aacute;gina _PAGE_ a _PAGES_ de _MAX_ registros",
        "infoEmpty": "No hay datos aun",
        "infoFiltered": "(Filtrado un _MAX_ total)",
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