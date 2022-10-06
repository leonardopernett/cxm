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

$this->title = 'Dashboard - Parametrizar PEC -';
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

    .card2 {
            height: auto;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #ffe6e6;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
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

</style>

<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css">
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>

<!-- Capa Seleccion -->
<div id="capaSeleccionId" class="capaSeleccion" style="display: inline;">

    <div class="row">
        <div class="col-md-12">
            <div class="card2 mb">
                <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #ff453c;"></em> <?= Yii::t('app', 'Importante: Este modulo presenta aquellos indicadores que son de tipo auditoria para indicar que variables son los que se vana  parametrizar.') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-4">
            <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #FFC72C;"></em> <?= Yii::t('app', 'Indicadores') ?> </label> 
                <?=  $form->field($model, 'id_indicador', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechCategorias::find()->distinct()->where("anulado = 0")->andwhere(['in','cod_pcrc',$varListaCodPcrcVoice])->andwhere(['=','idcategorias',1])->andwhere(['=','tipoparametro',1])->orderBy(['nombre'=> SORT_ASC])->all(), 'idspeechcategoria', 'nombre'),
                                          [
                                              'prompt'=>'Seleccionar Indicador...',
                                              'id' => 'idIndicador2',
                                              'onchange' => '
                                                  $.get(
                                                      "' . Url::toRoute('listarvariables') . '", 
                                                      {id: $(this).val()}, 
                                                      function(res){
                                                          $("#requester").html(res);
                                                      }
                                                  );
                                              ',

                                          ]
                                  )->label(''); 
                ?>
                <br>
                <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 15px; color: #FFC72C;"></em> <?= Yii::t('app', 'Variables') ?> </label> 
                <?= $form->field($model,'id_variable', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                                  [],
                                                  [
                                                      
                                                      'prompt' => 'Seleccionar Variable...',
                                                      'id' => 'requester',
                                                  ]
                                              )->label('');
                ?>
                <br>
                <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 15px; color: #FFC72C;"></em> <?= Yii::t('app', 'Comentarios') ?> </label> 
                <?= $form->field($model, 'comentarios', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id' => 'idcomentarios', 'placeholder'=>'Ingresar Comentarios']) ?>
                <br>
                <?= Html::submitButton(Yii::t('app', 'Guardar Proceso'),
                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'onclick' => 'varVerifica();',
                                    'title' => 'Guardar Proceso']) 
                ?> 
            </div>
            <br>
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #ffc034;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?> </label>
                <?= Html::a('Regresar',  ['categoriasview', 'txtServicioCategorias'=>$txtIdDpClientes], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
                ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>

        <div class="col-md-8">
            <div class="card1 mb">
                <table id="myTableProcesos" class="table table-hover table-bordered" style="margin-top:10px" >
                    <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 15px; color: #ffc034;"></em> <?= Yii::t('app', 'Lista de Procesos') ?> </label></caption>
                    <thead>
                        <tr>
                            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cod_Pcrc') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Pcrc') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Indicador') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Variable') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($varListarProcesos as $key => $value) {
                                $varids = $value['id_pecservicios'];
                                $varcodigos = $value['cod_pcrc'];
                        ?>
                        <tr>
                            <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $varcodigos) ?></label></td>
                            <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $value['pcrc']) ?></label></td>
                            <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $value['tipoindicador']) ?></label></td>
                            <td  class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $value['nombre']) ?></label></td>
                            <td class="text-center">
                                        <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deletepecservicio','id'=> $varids,'codpcrc'=> $varcodigos], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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

<script type="text/javascript">
    function varVerifica(){
        var varidIndicador2 = document.getElementById("idIndicador2").value;
        var varrequester = document.getElementById("requester").value;

        if (varidIndicador2 == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un indicador","warning");
            return;
        }
        if (varrequester == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar una variable","warning");
            return;
        }
    }
</script>