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

$this->title = 'Glosario Speech - Glosario';
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
        <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Subir Glosario Speech"; ?> </label>
      </div>
    </div>
  </div>

  <br>

  <?php $form = ActiveForm::begin(["method" => "post","enableClientValidation" => true,'options' => ['enctype' => 'multipart/form-data'],'fieldConfig' => ['inputOptions' => ['autocomplete' => 'off']]]) ?>
  <div class="row">
    
    <div class="col-md-4">
      
      <div class="card1 mb">
        <label><em class="fas fa-upload" style="font-size: 20px; color: #b52aef;"></em> <?= Yii::t('app', 'Seleccionar archivo') ?></label>
        <?= $form->field($model, "file[]")->fileInput(['id'=>'idinput','multiple' => false])->label('') ?>

        <br>

        <?= Html::submitButton("Subir", ["class" => "btn btn-primary", "onclick" => "cargar();"]) ?>
      </div>
      
      <br>

      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #b52aef;"></em> Cancelar y Regresar...</label>
        <div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #707372;" method='post' id="botones2" >
          Regresar
        </div>
      </div>

      <br>

      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-calendar" style="font-size: 15px; color: #b52aef;"></em> <?= Yii::t('app', 'Última Fecha Actualización:') ?></label> 
        <label  style="font-size: 20px; text-align: center;"><?= Yii::t('app', $varfechaMax) ?></label>
      </div>

    </div>


    <?php ActiveForm::end(); ?>
    <div class="col-md-8"><!-- inicio del div de la tarjeta de tamaño 8 -->
        <div class="card1 mb"><!-- div de la carta principal donde van a ir la informacion ---( me pone la tarjeta)-------------------> 

            <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" ><!--Titulo de la tabla no se muestra-->
                <caption><label><em class="fas fa-list" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Glosario') ?></label></caption><!--Titulo de la tabla si se muestra-->
                <thead><!--Emcabezados de la tabla -->
                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Tipo Categoria') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Marca/Canal/Agente') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Nombre de la Categoria') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Descripción') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Variables y Ejemplos') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Eliminar Item') ?></label></th>
                </thead>
                <tbody><!--Tbody de la tabla -->

                <?php
                  foreach ($varData as $key => $value) {

                ?>

                    <tr><!--Filas de la tabla -->
                    <td class="text-center"><label style="font-size: 12px;"><?php echo $value['tipocategoria']; ?></label></td>
                    <td class="text-center"><label style="font-size: 12px;"><?php echo $value['marca_canal_agente']; ?></label></td>
                    <td class="text-center"><label style="font-size: 12px;"><?php echo $value['nombrecategoria']; ?></label></td>
                    <td class="text-center"><label style="font-size: 12px;"><?php echo $value['descripcioncategoria']; ?></label></td>
                    <td class="text-center"><label style="font-size: 12px;"><?php echo $value['variablesejemplos']; ?></label></td>
                    <td class="text-center"><!--boton eliminar que esta dentro de esa fila-->
                        <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deleteitemglosario','id'=> $value['id_glosario'],'txtServicioCategorias' => $varcodigopcrc], 
                        ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?></td>
                    </tr>
                <?php  }   ?>  
                
          
                </tbody><!--fin Tbody de la tabla -->
            </table><!--fin  de la tabla -->

        </div><!--fin de la tarjeta dond esta la tabla -->
    </div>
  </div><!--fin de la div donde esta el tamaña de la tarjeta -->
</div>
<hr>

<?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
?>
<script type="text/javascript">

    function regresar(){
        var varidcliente = "<?php echo $varidcliente; ?>";        
        
    window.location.href='categoriasview?txtServicioCategorias='+varidcliente;
    };
</script>
