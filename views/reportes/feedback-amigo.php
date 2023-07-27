<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use app\models\Dashboardcategorias;
use app\models\Dashboardservicios;

$this->title = 'Feedback - Reporte Feeback Asesor';
$this->params['breadcrumbs'][] = $this->title;

  $template = '<div class="col-md-12">'
  . ' {input}{error}{hint}</div>';

  $sessiones = Yii::$app->user->identity->id;

  $rol =  new Query;
  $rol    ->select(['tbl_roles.role_id'])
          ->from('tbl_roles')
          ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                    'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
          ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                  'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
          ->where(['=','tbl_usuarios.usua_id',$sessiones]);                      
  $command = $rol->createCommand();
  $roles = $command->queryScalar();

  $diasMes=date("d");

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

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Gestión-Feedback.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
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

<br>
<br>

<?php
if ($varMensaje != 1) {
?>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => [
            'autocomplete' => 'off'
        ]
    ]
  ]); 
?>
<!-- Capa Consulta -->
<div class="capaConsulta" id="capaIdConsulta" style="display: inline;">
  
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones & Procesos') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-calendar" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Seleccionar Rango de Fechas') ?></label>
                <?=
                    $form->field($model, 'created', [
                        'labelOptions' => ['class' => 'col-md-12'],
                        'template' => '<div class="col-md-12">{label}</div>'
                        . '<div class="col-md-12"><div class="input-group">'
                        . '<span class="input-group-addon" id="basic-addon1">'
                        . '<i class="glyphicon glyphicon-calendar"></i>'
                        . '</span>{input}</div>{error}{hint}</div>',
                        'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                        'options' => ['class' => 'drp-container form-group']
                    ])->widget(DateRangePicker::classname(), [
                        'useWithAddon' => true,
                        'convertFormat' => true,
                        'presetDropdown' => true,
                        'readonly' => 'readonly',
                        'pluginOptions' => [
                            'timePicker' => false,
                            //'timePickerIncrement' => 15,
                            //'singleDatePicker'=>true,
                            'format' => 'Y-m-d',
                            'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                            'endDate' => date("Y-m-d"),
                            'opens' => 'center',
                            'minDate'=>date("Y-m-d", strtotime(date("Y-m-d") . "-$diasMes day -1 month")),
                            'maxDate'=>date("Y-m-d", strtotime(date("Y-m-d") . " now"))
                    ]])->label('');
                ?>

                <br>

                <?=
                    Html::submitButton(Yii::t('app', 'Buscar'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'onclick'=>'varVerificar();'])
                ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                
                <div class="row">
                    <div class="col-md-2 text-center">
                        <label style="font-size: 15px;"><em class="fas fa-thumbs-up" style="font-size: 40px; color: #C148D0;"></em></label>
                    </div>

                    <div class="col-md-10 left">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' ¡ Hola '.$varNameJarvis.' ! Te comentamos que puedes consultar tus procesos de feedback que te han ingresado. Selecciona un rango de fecha y dale clic en el botón buscar, allí el sistema te mostrará tus feedbacks.') ?></label>
                    </div>
                </div> 

                <br>

                <?php
                if ($varDataList != null) {
                ?>
                <div class="row">
                    <div class="col-md-2 text-center">
                        <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 40px; color: #C148D0;"></em></label>
                    </div>

                    <div class="col-md-10 left">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' Actualmente y de acuerdo a la búsqueda que realizaste, tienes un total de '.count($varDataList).' feedbacks.') ?></label>
                    </div>
                </div> 
                <?php
                }
                ?>

            </div>
        </div>
    </div>

</div>

<hr>

<?php
if ($varDataList != null) {
?>

<!-- Capa Resultados -->
<div class="capaResultados" id="capaIdResultados" style="display:  inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resultados') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Lista de Feedbacks') ?></label>

                <table id="tblListadoPFeedbacks" class="table table-striped table-bordered tblResDetFreed">
                    <caption><?= Yii::t('app', ' Resultados...') ?></caption>
                    <thead>
                        <tr>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha de Creación') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Gestionado') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Valorador') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Lider') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Asesor') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Formulario') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Feedback') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '¿Confirma que conoce los compromisos adquiridos en este feedback?') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($varDataList as $key => $value) {
                            
                        ?>
                        <tr>
                            <td><label style="font-size: 12px;"><?php echo  $value['created']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['Gestionado']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['Valorador']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['Lider']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['Asesor']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['Formulario']; ?></label></td>
                            <td class="text-center">
                                <?= 
                                    Html::a('<em id="idfeedbacks" class="fas fa-edit" style="font-size: 17px; color: #C148D0;"></em>',
                                                    'javascript:void(0)',
                                        [
                                            'title' => Yii::t('app', 'Ver Feedback'),
                                            'onclick' => "                       
                                                $.ajax({
                                                    type     :'get',
                                                    cache    : false,
                                                    url  : '" . Url::to(['viewfeedback',
                                                                'idfeedback' => $value['id']]) . "',
                                                    success  : function(response) {
                                                        $('#ajax_result').html(response);
                                                    }
                                                });
                                            return false;",
                                        ]);
                                ?>
                            </td>
                            
                            <td class="text-center">
                                <?php
                                    $varValida =(new \yii\db\Query())
                                                ->select([
                                                    'tbl_ejecucion_compromisofeedback.confirmacion'
                                                ])
                                                ->from(['tbl_ejecucion_compromisofeedback'])
                                                ->where(['=','tbl_ejecucion_compromisofeedback.id_feeback',$value['id']])
                                                ->count(); 

                                    $varTxtComentarios = null;
                                    if ($varValida == 0) {
                                        
                                ?>
                                    <?= Html::a('<em class="fas fa-thumbs-down" style="font-size: 15px; color: #FC4343;"></em>',  ['confirmacionfeedback','id_feedacks'=>$value['id'],'idConfirma'=> 1,'evaluado_usuared'=>$evaluado_usuared], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'No Confirma']) ?>

                                    <?= Html::a('<em class="fas fa-thumbs-up" style="font-size: 15px; color: #2cdc5a;"></em>',  ['confirmacionfeedback','id_feedacks'=>$value['id'],'idConfirma'=> 2,'evaluado_usuared'=>$evaluado_usuared], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Confirmar']) ?>

                                <?php
                                    }else{
                                        $varTxtComentarios = (new \yii\db\Query())
                                                            ->select([
                                                                'tbl_ejecucion_compromisofeedback.comentarios'
                                                            ])
                                                            ->from(['tbl_ejecucion_compromisofeedback'])
                                                            ->where(['=','tbl_ejecucion_compromisofeedback.id_feeback',$value['id']])
                                                            ->scalar(); 
                                        
                                ?>
                                        <label style="font-size: 12px;"><?php echo  $varTxtComentarios; ?></label>
                                <?php
                                    }
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                    $ejecucion = \app\models\Ejecucionformularios::findOne(["id"=>$value['Formid']]);
                                    if (isset($ejecucion->basesatisfaccion_id)) {
                                        $modelBase = app\models\BaseSatisfaccion::findOne($ejecucion->basesatisfaccion_id);
                                    }  
                    
                                    if (!isset($ejecucion->basesatisfaccion_id)) {
                                ?>
                                        <?= Html::a('<em class="fas fa-search" style="font-size: 15px; color: #FC4343;"></em>',  ['formularios/showformulariodiligenciadoamigo', 'form_id' => base64_encode($value['Formid'])], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'ver formulario', 'target' => "_blank"])?>
                                <?php                   
                                    }else { 
                                ?>
                                        <?= Html::a('<em class="fas fa-search" style="font-size: 15px; color: #FC4343;"></em>',  ['basesatisfaccion/showformulariogestionamigo','basesatisfaccion_id' => base64_encode($modelBase->id),'preview'=>1,'fill_values'=>true], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'ver formulario', 'target' => "_blank"])?>
                                <?php
                                    }
                                ?>
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

</div>

<hr>

<?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
?>

<?php
}
?>

<?php $form->end() ?>


<?php
}else{
?>

<!-- Capa Mensaje -->
<div class="capaInformativa" id="capaIdInformativa" style="display: inline;">
  
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Notas Informativas') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">

                <div class="row">
                    <div class="col-md-2 text-center">
                        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 50px; color: #C148D0;"></em></label>
                    </div>

                    <?php
                    if ($varMensaje == 2) {
                        
                    ?>
                        <div class="col-md-10 left">
                            <label style="font-size: 15px;"><?= Yii::t('app', ' ¡ Hola '.$varNameJarvis.' ! Te comentamos que puedes el actual módulo esta diseñado para que los asesores vean sus correspondiente feedbacks. Tú rol en CXM es administrativo por lo tanto no puedes ver los feedbacks.') ?></label>
                        </div>
                    <?php
                    }else{
                    ?>
                        <div class="col-md-10 left">
                            <label style="font-size: 15px;"><?= Yii::t('app', ' Importante: el usuario que esta tratando de ingresar no existe en nuestra base de datos de CXM. Por favor generar la validación con el administrador de la herramienta.') ?></label>
                        </div>
                    <?php
                    }
                    ?>
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
        $('#tblListadoPFeedbacks').DataTable({
          responsive: true,
          fixedColumns: true,
          select: true,
          "language": {
            "lengthMenu": "Cantidad de Datos a Mostrar _MENU_",
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

    function varVerificar(){
        var varfechas = document.getElementById("ejecucionfeedbacks-created").value;

        if (varfechas == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar un rango de fechas.","warning");
            return;
        }
    };
</script>