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

    $paramssession = [':idsession' => $sesiones ];
    $varPermisos = Yii::$app->db->createCommand('
      SELECT * 
        FROM tbl_hojavida_permisosacciones pa
          WHERE 
            pa.usuario_registro = :idsession')->bindValues($paramssession)->queryAll();

    $varEliminar = null;
    $varResumen = null;
    $varInformacion = null;
    $varCargar = null;
    $varEditar = null;
    foreach ($varPermisos as $key => $value) {
      $varEliminar = $value['hveliminar'];
      $varResumen = $value['hvverresumen'];
      $varInformacion = $value['hvdatapersonal'];
      $varCargar = $value['hvcasrgamasiva'];
      $varEditar = $value['hveditar'];
    }
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

</style>
<!-- datatable -->
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">


<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
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
<div class="capaBotones" style="display: inline;">
   <div class="row">
      <?php if ($varResumen == 1) { ?>
        <div class="col-md-4">
           <div class="card1 mb">
               <label style="font-size: 15px;"><em class="fas fa-chart-bar" style="font-size: 15px; color: #559FFF;"></em> Resumen General </label>
               <?= Html::a('Aceptar',  ['resumen','id'=>Yii::$app->user->identity->id], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regumen General']) 
                ?>
           </div>
       </div>
      <?php } ?>

      <?php if($varInformacion == 1) { ?>
       <div class="col-md-4">
           <div class="card1 mb">
               <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #559FFF;"></em> Informaci&oacute;n Personal </label>
               <?= Html::a('Aceptar',  ['informacionpersonal'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Informacion personal']) 
                ?>
           </div>
       </div>
      <?php } ?>

      <?php if($varCargar == 1) { ?>

        <div class="col-md-4">
           <div class="card1 mb">
               <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #559FFF;"></em> Carga Masiva </label>
               <a href="" class="btn btn-primary" style="font-size:15px;font-weight:bold" data-toggle="modal" data-target="#exampleModal3">Aceptar</a>

           </div>
       </div>

      <?php } ?>

   </div>
</div>
<hr>
<div class="capaLista" style="display: inline;">
    <div class="row">
       <div class="col-md-12">
           <div class="card1 mb">
              <label style="font-size: 15px;"><em class="fas fa-address-book" style="font-size: 15px; color: #B833FF;"></em> Listado </label>

              <?php if($sesiones == "2953") { ?>
               <div class="row">
                 <div class="col-md-6">
                    <a href=""  class="btn btn-success" data-toggle="modal" data-target="#exampleModal4">
                       Exportar Usuarios <em class="fa fa-file-archive" aria-hidden="true"></em>
                    </a>

                    <a href=""  class="btn btn-success" data-toggle="modal" data-target="#exampleModal5">
                       Exportar Eventos <em class="fa fa-file-archive" aria-hidden="true"></em>
                    </a>
                 </div>
               </div>
               <br>
              <?php } ?>
              
               <div class="row">
                 <div class="col-md-12">
                   <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
                    <caption>.</caption>
                    <thead>
                      <tr>
                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Director') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Gerente') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cliente') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Programa') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nivel') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Contacto') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cargo') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Estado') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'pais') ?></label></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        foreach ($dataProviderhv as $key => $value) {
                          $varIdHv = $value['idHojaVida'];
                          $varCliente = $value['cliente'];
                          $varTipo = $value['tipo'];
                          $varNivel = $value['nivel'];
                          $varNombre = $value['nombre_full'];
                          $varRol = $value['rol'];
                          $varPais = $value['pais'];
                          $varEstado = $value['estado'];
                          
                          $paramspcrc = [':codpcrc' => $varIdHv ];
                          $varVerifica = Yii::$app->db->createCommand('
                            SELECT GROUP_CONCAT(cc.cod_pcrc," - ",cc.pcrc SEPARATOR "; ") AS Programa
                              FROM tbl_hojavida_datapcrc dc 
                              INNER JOIN tbl_proceso_cliente_centrocosto cc ON 
                                dc.cod_pcrc = cc.cod_pcrc
                              WHERE dc.hv_idpersonal = :codpcrc')->bindValues($paramspcrc)->queryScalar(); 

                          $varlistdirector = Yii::$app->db->createCommand('
                            SELECT  cc.director_programa  AS Programa
                              FROM tbl_hojavida_datadirector dc 
                                INNER JOIN tbl_proceso_cliente_centrocosto cc ON 
                                  dc.ccdirector = cc.documento_director
                                WHERE dc.hv_idpersonal = :codpcrc GROUP BY cc.director_programa')->bindValues($paramspcrc)->queryAll();
                          $vararraydirector = array();
                          foreach ($varlistdirector as $key => $value) {
                            array_push($vararraydirector, $value['Programa']);
                          }
                          $varDirectores = implode("; ", $vararraydirector);

                          $varlistgerente = Yii::$app->db->createCommand('
                            SELECT  cc.gerente_cuenta  AS Programagerente
                              FROM tbl_hojavida_datagerente dc 
                                INNER JOIN tbl_proceso_cliente_centrocosto cc ON 
                                  cc.documento_gerente = dc.ccgerente
                                WHERE dc.hv_idpersonal = :codpcrc GROUP BY cc.gerente_cuenta')->bindValues($paramspcrc)->queryAll();
                          $vararraygerente = array();
                          foreach ($varlistgerente as $key => $value) {
                            array_push($vararraygerente, $value['Programagerente']);
                          }
                          $varGerentes = implode("; ", $vararraygerente);

                      ?>
                        <tr>
                          <td class="text-center">
                            <?= Html::a('<em class="fas fa-search" style="font-size: 12px; color: #B833FF;"></em>',  ['viewinfo','idinfo' => $varIdHv], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700; border-color: #4298b500 !important; color:#000000;", 'title' => 'Ver datos']) ?>

                          <?php if($varEditar == 1) { ?>
                            <?= Html::a('<em class="fas fa-edit" style="font-size: 12px; color: #495057;"></em>',  ['editinfo','idinfo' => $varIdHv], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700; border-color: #4298b500 !important; color:#000000;", 'title' => 'Editar datos']) ?>
                          <?php } ?>

                          <?php if ($varEliminar == 1) { ?>

                            <?= Html::a('<em class="fas fa-trash" style="font-size: 12px; color: #FC4343;"></em>',  ['deleteinfo','idinfo' => $varIdHv], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700; border-color: #4298b500 !important; color:#000000;", 'title' => 'Eliminar datos']) ?>
                          <?php } ?>
                          </td>
                          <td><label style="font-size: 12px;"><?php echo  $varDirectores; ?></label></td>
                          <td><label style="font-size: 12px;"><?php echo  $varGerentes; ?></label></td>
                          <td><label style="font-size: 12px;"><?php echo  $varCliente; ?></label></td>
                          <td><label style="font-size: 12px;"><?php echo  $varVerifica; ?></label></td>
                          <td><label style="font-size: 12px;"><?php echo  $varTipo; ?></label></td>
                          <td><label style="font-size: 12px;"><?php echo  $varNivel; ?></label></td>
                          <td><label style="font-size: 12px;"><?php echo  $varNombre; ?></label></td>
                          <td><label style="font-size: 12px;"><?php echo  $varRol; ?></label></td>
                          <td><label style="font-size: 12px;"><?php echo  $varPais; ?></label></td>
                          <td><label style="font-size: 12px;"><?php echo  $varEstado; ?></label></td>
                        </tr>
                      <?php
                        }
                      ?>
                    </tbody>
                   </table>
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
          
            <?php $form = ActiveForm::begin(['action'=>['hojavida/export'], 'method'=>'POST', 'options'=>['enctype'=>'multipart/form-data']]) ?>
                <div class="input-area">
                      <div class="input-text" id="text">Seleccione o arrastre el archivo</div>
                      <em class="fa fa-upload"></em>
                      <?= $form->field($modelos, 'file')->fileInput(["class"=>"input-file" ,'id'=>'file']) ?>
                  </div> 


                  <div class="button">
                      <button  class="btn btn-success">Agregar <em class="fa fa-plus" style="padding-top:5px"></em> </button>
                  </div>
            <?php ActiveForm::end() ?>
      </div>
      
    </div>
  </div>
</div>

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
                                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Eventos: </label>
                                <?= Html::a('Crear',  ['eventos'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Crear Eventos']) 
                                ?>
                           </div>
                       </div>

                       <div class="col-md-2">
                           <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Pais & Ciudad: </label>
                                <?= Html::a('Crear',  ['paisciudad'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Crear Pais & Ciudad']) 
                                ?>
                           </div>
                       </div>

                       <div class="col-md-2">
                           <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Modalidad Trabajo: </label>
                                <?= Html::a('Crear',  ['crearmodalidad'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Crear Modalidad Trabajo']) 
                                ?>
                           </div>
                       </div>

                       <div class="col-md-2">
                           <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Datos Acad&eacute;micos: </label>
                                <?= Html::a('Crear',  ['academico'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Crear Modalidad Trabajo']) 
                                ?>
                           </div>
                       </div>

                       <div class="col-md-2">
                           <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Permisos: </label>
                                <?= 
                                    Html::button('Crear & Editar', ['value' => url::to(['asignarpermisos']), 'class' => 'btn btn-success', 'style' => 'background-color: #337ab7', 'id'=>'modalButton2', 'data-toggle' => 'tooltip', 'title' => 'Crear & Editar Permisos'])
                                ?>
                                <?php
                                    Modal::begin([
                                        'header' => '<h4></h4>',
                                        'id' => 'modal2',
                                    ]);

                                    echo "<div id='modalContent2'></div>";
                                                                            
                                    Modal::end(); 
                                ?> 
                           </div>
                       </div>

                       <div class="col-md-2">
                           <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Complementos: </label>
                                <?= Html::a('Crear',  ['complementoshv'], ['class' => 'btn btn-primary',                                        
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
<?php
    }
?>
<script type="text/javascript">
  $(document).ready( function () {
    $('#myTable').DataTable({
      responsive: true,
      fixedColumns: true,
      select: true,
      "language": {
        "lengthMenu": "Display _MENU_ records per page",
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

  const text = document.getElementById('text');
  const file = document.getElementById('file');

  file.addEventListener('change',()=>{
      text.innerHTML= file.files[0].name
  })


</script>