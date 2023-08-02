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

$this->title = 'Procesos Voc - Actualizar Llamadas Speech';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Procesos Voc - Actualizar Llamadas Speech';

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
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
    .lds-ring {
      display: inline-block;
      position: relative;
      width: 100px;
      height: 100px;
    }
    .lds-ring div {
      box-sizing: border-box;
      display: block;
      position: absolute;
      width: 80px;
      height: 80px;
      margin: 10px;
      border: 10px solid #3498db;
      border-radius: 70%;
      animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
      border-color: #3498db transparent transparent transparent;
    }
    .lds-ring div:nth-child(1) {
      animation-delay: -0.45s;
    }
    .lds-ring div:nth-child(2) {
      animation-delay: -0.3s;
    }
    .lds-ring div:nth-child(3) {
      animation-delay: -0.15s;
    }
    @keyframes lds-ring {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
    }

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
            font-family: "Nunito";
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
<br>
<br>
<div class="CapaInfo" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card2 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Funciones Speech - CXM"; ?> </label>
      </div>
    </div>
  </div>

  <br>

  <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
  <div class="row">
    
    <div class="col-md-4">

      <div class="card1 mb">
        <label><em class="fas fa-save" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Preparar Base de Datos CXM') ?></label>

        <label style="font-size: 15px;"><?= Yii::t('app', '* Seleccionar Cliente') ?></label>
        <?=  $form->field($model, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechServicios::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'nameArbol'),
                                            [
                                                'id' => 'idCliente',
                                                'prompt'=>'Seleccionar...',
                                            ]
                                )->label(''); 
        ?>

        <br>

        <label style="font-size: 15px;"><?= Yii::t('app', '* Seleccionar Rango de Fecha') ?></label>
        <?=
                            $form->field($model, 'fechacreacion', [
                                'labelOptions' => ['class' => 'col-md-12'],
                                'template' => 
                                '<div class="col-md-12"><div class="input-group">'
                                . '<span class="input-group-addon" id="basic-addon1">'
                                . '<i class="glyphicon glyphicon-calendar"></i>'
                                . '</span>{input}</div>{error}{hint}</div>',                                
                                'inputOptions' => ['id'=>'idLlamadas','aria-describedby' => 'basic-addon1'],
                                'options' => ['class' => 'drp-container form-group']
                            ])->label('')->widget(DateRangePicker::classname(), [
                                'useWithAddon' => true,
                                'convertFormat' => true,
                                'presetDropdown' => true,
                                'readonly' => 'readonly',
                                'pluginOptions' => [
                                    'timePicker' => false,
                                    'format' => 'Y-m-d',
                                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                    'endDate' => date("Y-m-d"),
                                    'opens' => 'right',
                            ]]);
        ?>

        <br>
        
        <?= Html::submitButton(Yii::t('app', 'Aceptar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'activar();',
                                'title' => 'Buscar']) 
        ?>        
      </div> 

    </div>

    <div class="col-md-4">

      <div class="card2 mb">
        <label><em class="fas fa-database" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Base de Datos Ideal') ?></label>
        <?php if ($sessiones == '2953' || $sessiones == '3205' || $sessiones == '1543') { ?>
        <?= Html::button('Actualizar base ideal', ['value' => url::to(['actualizabaseideal']), 'class' => 'btn btn-danger', 'id'=>'modalButton',
                                'data-toggle' => 'tooltip',
                                'title' => 'Actualizar base ideal']) ?> 

        <?php
            Modal::begin([
              'header' => '<h4>Actualizar base ideal</h4>',
              'id' => 'modal',
              //'size' => 'moda2-lg',
            ]);

            echo "<div id='modalContent'></div>";
                                                              
            Modal::end(); 
        ?>
        <?php }else{ ?>
          <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #ff453c;"></em> <?= Yii::t('app', 'Importante: Boton actualmente fuera de servicio por mantenimiento') ?></label>
        <?php } ?>  

        <hr>

        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #ff453c;"></em> <?= Yii::t('app', 'Importante: Esta opción permite actualizar el proceso de las categorias de un servicio con su fecha en especifico. Es recomendado generar el proceso por mes actual y mes pasado, pero no otros diferentes para no generar conflictos en los valores de los porcentajes.') ?></label>

        <br>
        
      </div>

    </div>



    <div class="col-md-4">
      <div class="card1 mb" style="background: #e6edff;">
        <label><em class="fas fa-upload" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Actualizar Llamadas (WIA & SAE) - CXM') ?></label>
        <?= Html::button('Actualizar llamadas (WIA & SAE)', ['value' => url::to(['actualizacomdata']), 'class' => 'btn btn-success', 'id'=>'modalButton3',
                                'data-toggle' => 'tooltip',
                                'style' => 'background: #707372;',
                                'title' => 'Actualizar Especiales (WIA & SAE)']) ?> 

        <?php
            Modal::begin([
              'header' => '<h4>Actualizar llamadas (WIA & SAE) a CXM</h4>',
              'id' => 'modal3',
              //'size' => 'moda2-lg',
            ]);

            echo "<div id='modalContent3'></div>";
                                                              
            Modal::end(); 
        ?>

        <hr>

        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #ff453c;"></em> <?= Yii::t('app', 'Importante: Esta opción permite actualizar el proceso de las categorias de un servicio con su fecha en especifico. Es recomendado generar el proceso por mes actual y mes pasado, pero no otros diferentes para no generar conflictos en los valores de los porcentajes. Este proceso pertenece a WIA & SAE. Debe estar parametrizado.') ?></label>
      </div>
    </div>



  </div>

  <br>

  <div class="row">

    <div class="col-md-4">
    
      <div class="card1 mb">
        <label><em class="fas fa-upload" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Actualizar llamadas Speech a CXM') ?></label>
        <?= Html::button('Actualizar llamadas', ['value' => url::to(['actualizaspeech']), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                                'data-toggle' => 'tooltip',
                                'title' => 'Actualizar']) ?> 

        <?php
            Modal::begin([
              'header' => '<h4>Actualizar llamadas Speech a CXM</h4>',
              'id' => 'modal1',
              //'size' => 'moda2-lg',
            ]);

            echo "<div id='modalContent1'></div>";
                                                              
            Modal::end(); 
        ?>
      </div>

    </div>

    <div class="col-md-4">
      
      <div class="card2 mb">
        <label><em class="fas fa-upload" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Actualizar llamadas Speech a CXM - Especiales') ?></label>
        <?= Html::button('Actualizar llamadas especiales', ['value' => url::to(['actualizaspeechespecial']), 'class' => 'btn btn-danger', 'id'=>'modalButton2',
                                'data-toggle' => 'tooltip',
                                'title' => 'Actualizar Especiales']) ?> 

        <?php
            Modal::begin([
              'header' => '<h4>Actualizar llamadas Speech a CXM - Especiales</h4>',
              'id' => 'modal2',
              //'size' => 'moda2-lg',
            ]);

            echo "<div id='modalContent2'></div>";
                                                              
            Modal::end(); 
        ?>
      </div>    

    </div>

    <div class="col-md-4">
      
      <div class="card1 mb">
        <label><em class="fas fa-minus-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Cancelar y Regresar') ?></label>
        <?= Html::a('Aceptar',  ['index'], ['class' => 'btn btn-success',
                               'style' => 'background-color: #707372',                        
                                'data-toggle' => 'tooltip',
                                'title' => 'Nuevo'])
        ?>
      </div>

    </div>





  </div>
  <?php ActiveForm::end(); ?>

</div>

<hr>

<script type="text/javascript">
  function activar(){
    var varidCliente = document.getElementById("idCliente").value;
    var varFechas = document.getElementById("speechparametrizar-fechacreacion").value;

    if (varidCliente == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Se debe de seleccionar un cliente","warning");
      return;
    }

    if (varFechas == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Se debe de seleccionar un rango de fecha","warning");
      return;
    }
  };
</script>