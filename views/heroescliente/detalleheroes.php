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

$this->title = 'Historico Heroes por Cliente - Detalle';//nombre del titulo de mi modulo
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
        background-image: url('../../images/detalleHeroes.png');
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

<div class="capaInfo" id="idCapaInfo" style="display: inline;">
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <div class="row"><!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Detalle de la Postulación"; ?> </label><!--titulo principal de mi modulo-->
            </div>
        </div>
    </div>

    <br><hr><br>

    <div class="row">
      <div class="col-md-12" >
        <div class="card1 mb" >
          <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px;"><!--Titulo de la tabla no se muestra-->
            <caption><label><em class="fas fa-list" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Detalle') ?></label></caption><!--Titulo de la tabla si se muestra-->
            <thead><!--Emcabezados de la tabla -->
              <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;" ><?= Yii::t('app', 'Tipo de Postulación') ?></label></th>
              <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;;"><?= Yii::t('app', 'Nombre de Quién Postula') ?></label></th>
              <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;" ><?= Yii::t('app', 'Cargo de Quién Postula') ?></label></th>
              <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Embajador/ Persona a Postular') ?></label></th>
              <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;" ><?= Yii::t('app', 'Tipo de Cliente') ?></label></th>
              <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha / Hora de la Interacción') ?></label></th>
              <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cuidad') ?></label></th>
              <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Extensión de la Interacción') ?></label></th>
              <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Nombre del Usuario que Vive la Experiencia') ?></label></th>
              <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Experiencia') ?></label></th>
              <th scope="col" class="text-center" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Idea') ?></label></th>
            </thead>

            <tbody><!--Tbody de la tabla -->
                    <?php

                            foreach ($varDatosDetalle as $key => $value) {

                    ?>
                    
              <tr><!--Filas de la tabla -->
                <td class="text-center"><label style="font-size: 12px;"><?php echo $value['tipodepostulacion']; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo $varNombre; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo $value['cargopostula']; ?></label></td>                
                <td class="text-center"><label style="font-size: 12px;"><?php echo $varNombrePostulador ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo $varPcrc ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo $value['fechahorapostulacion']; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo $value['ciudad']; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo $value['extensioniteracion']; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo $value['usuariovivexperiencia']; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo $value['historiabuenagente']; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo $value['idea']; ?></label></td>

                          
              </tr>
                    <?php  }   ?> 
        
                     
                             
                    

                        
                        
                        
                        
            </tbody><!--fin Tbody de la tabla -->
          </table>
        </div>
      </div>
    </div>

    
    <br><hr><br>

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