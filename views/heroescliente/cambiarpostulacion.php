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

$this->title = 'Héroes por el Cliente - Ver Postulación de Héroes';
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

    .card2 {
        height: 180px;
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
        background-image: url('../../images/heroes.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>
<!-- Data extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<script src="../../js_extensions/chart.min.js"></script>

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

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => [
            'autocomplete' => 'off'
        ]
    ]
  ]); 
?>
<!-- Capa Datos -->
<div class="capaInformacion" id="capaIdInformacion" style="display: inline;">
  
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Datos Postulación') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">

                <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Formulario Diligenciado de Postulación') ?></label>
                <br>

                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Seleccionar Tipo de Postulación: ') ?></label>                        
                        <?= $form->field($model, 'id_tipopostulacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'id_tipopostulacion', 'readonly'=>'readonly', 'value'=>$vartipopostulacion_Editar])?>

                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Nombre de Quién Postula: ') ?></label>
                        <?= $form->field($model, 'id_postulador', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'id_postulador', 'readonly'=>'readonly', 'value'=>$varNombrePotulador_Editar])?>

                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Seleccionar Cargo de Quién Postula: ') ?></label>                        
                        <?= $form->field($model, 'id_cargospostulacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'id_cargospostulacion', 'readonly'=>'readonly', 'value'=>$varcargospostulacion_Editar])?>


                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Seleccionar Embajador/Persona a Postular: ') ?></label>
                        <?= $form->field($model, 'id_postulante', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'id_postulante', 'readonly'=>'readonly','value'=>$varNombrePostulado_Editar])?>


                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Seleccionar Cliente: ') ?></label>                        
                        <?= $form->field($model, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'id_dp_clientes', 'readonly'=>'readonly', 'value'=>$varcliente_Editar])?>


                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Seleccionar Centro de Costos: ') ?></label>
                        <?= $form->field($model, 'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'requester', 'readonly'=>'readonly', 'value'=>$varcod_pcrc_Editar])?>
                    </div>

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Seleccionar Ciudad de Postulación: ') ?></label>
                        <?= $form->field($model, 'id_ciudadpostulacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'id_ciudadpostulacion', 'readonly'=>'readonly', 'value'=>$varciudadpostulacion_Editar])?>

                        <?php 
                        if ($varid_tipopostulacion_Editar == 2) {
                        ?>                        
                            <label style="font-size: 15px;"> <?= Yii::t('app', ' Ingresar fecha & hora de la Interacción: Ej: 2023-05-26 22:37:06 ') ?></label>
                            <?= $form->field($model, 'fecha_interaccion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idfecha_interaccion','value'=>$varfecha_interaccion_Editar])?> 
                        
                            <label style="font-size: 15px;"> <?= Yii::t('app', ' Ingresar Extensión de la Interacción: ') ?></label>
                            <?= $form->field($model, 'ext_interaccion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idext_interaccion','value'=>$varext_interaccion_Editar])?>   
                            
                            <label style="font-size: 15px;"> <?= Yii::t('app', ' Nombre del Usuario que Vive la Experiencia: ') ?></label>
                            <?= $form->field($model, 'usuario_interaccion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idusuario_interaccion','value'=>$varusuario_interaccion_Editar])?>   
                        <?php
                        }
                        ?>                    

                        <?php 
                        if ($varid_tipopostulacion_Editar == 3) {
                        ?> 
                            <label style="font-size: 15px;"> <?= Yii::t('app', ' Cuéntanos: (La historia que merece ser reconocida como gente buena, buena gente) ') ?></label>
                            <?= $form->field($model, 'historia_interaccion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idhistoria_interaccion','value'=>$varhistoria_interaccion_Editar])?>
                        <?php
                        }
                        ?> 

                        <?php 
                        if ($varid_tipopostulacion_Editar == 1) {
                        ?> 
                            <label style="font-size: 15px;"> <?= Yii::t('app', ' Cuéntanos: (Esa idea que nos ayudará a mejorar las experiencias y fortalecer la cultura de relacionamiento) ') ?></label>
                            <?= $form->field($model, 'idea_postulacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'ididea_postulacion','value'=>$varidea_postulacion_Editar])?>
                        <?php 
                        }
                        ?> 
                        
                    </div>
                </div>

            </div>        
        </div>
    </div>

</div>

<hr>

<!-- Capa Botones -->
<div class="capaBtn" id="capaIdBtn" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Editar Postulación:') ?></label> 
                <?= Html::submitButton(Yii::t('app', 'Editar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'title' => 'Editar Datos']) 
                ?>
            </div>
        </div>

    </div>

</div>

<hr>
<?php $form->end() ?>

<hr>

