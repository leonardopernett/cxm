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
use app\models\ProcesosClienteCentrocosto;

  $this->title = 'Procesos Q&S - Búsqueda Por Persona';
  $this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

  $sessiones = Yii::$app->user->identity->id;

  $rol =  new Query;
  $rol->select(['tbl_roles.role_id'])
      ->from('tbl_roles')
      ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                  'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
      ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                  'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
      ->where(['=','tbl_usuarios.usua_id',$sessiones]);                    
  $command = $rol->createCommand();
  $roles = $command->queryScalar();

?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<style type="text/css">
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
</style>

<!-- Capa Procesos Por Persona -->
<div id="capaIdProceso" class="capaProcesosPersonas" style="display: inline;">

  <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        
        <div class="row">
          <div class="col-md-6">
            <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Seleccionar Cliente') ?></label>
            <?=  $form->field($model, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechServicios::find()->distinct()->where("anulado = 0")->andwhere("id_dp_clientes != 1")->orderBy(['nameArbol'=> SORT_ASC])->all(), 'id_dp_clientes', 'nameArbol'),
                                                    [
                                                        'id' => 'txtidclientes',
                                                        'prompt'=>'Seleccionar...',
                                                        'onchange' => '
                                                            $.get(
                                                                "' . Url::toRoute('listarpcrcs') . '", 
                                                                {id: $(this).val()}, 
                                                                function(res){
                                                                    $("#requester").html(res);
                                                                }
                                                            );
                                                            
                                                        ',

                                                    ]
                )->label(''); 
            ?>
          </div>          

          <div class="col-md-6">
            <label style="font-size: 15px;"><em class="fas fa-calendar-alt" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Seleccionar Rango de Fechas') ?></label>
           <?=
              $form->field($model, 'fechacreacion', [
                                    'labelOptions' => ['class' => 'col-md-12'],
                                    'template' => 
                                     '<div class="col-md-12"><div class="input-group">'
                                    . '<span class="input-group-addon" id="basic-addon1">'
                                    . '<i class="glyphicon glyphicon-calendar"></i>'
                                    . '</span>{input}</div>{error}{hint}</div>',
                                    'inputOptions' => ['aria-describedby' => 'basic-addon1'],
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
          </div>
        </div>        

        <div class="row"> 
          <div class="col-md-6">
            <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Seleccionar Programa/Pcrc') ?></label>
            
            <?= $form->field($model,'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                        [],
                                        [
                                            'prompt' => 'Seleccionar...',
                                            'id' => 'requester',
                                            'multiple' => true,
                                        ]
                                    )->label('');
            ?>
            
          </div>

          <div class="col-md-6">
            <label style="font-size: 15px;"><em class="fas fa-hand-pointer" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Seleccionar Dimensión') ?></label>
            <?= $form->field($model, 'anulado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varextensiones, ['prompt' => 'Seleccionar...', 'id'=>'iddashboard','onclick'=>'verProcesos();']) ?>            
          </div>          
        </div>

        <br>

        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default" style="background-color: #e9f9e8;">
              <div class="panel-body">
                <label style="font-size: 14px;"><?= Yii::t('app', ' Importante: Tener presente que para seleccionar mas de un dato en el centro de costos, se debe hacer con la tecla Ctrl sostenida y dando clic a los items deseados.') ?></label>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>  

  </div>

  <hr>

  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb">
        <label  style="font-size: 15px;" ><em class="fas fa-search" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Buscar Procesos QyS - Por Persona') ?></label>
        <?= Html::submitButton(Yii::t('app', 'Buscar'),
                                      ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                          'data-toggle' => 'tooltip',
                                          'title' => 'Buscar Datos',
                                          'style' => 'display: inline;margin: 3px;height: 34px;',
                                          'id'=>'modalButton1',
                                          'onclick' => 'varVerifica();']) 
        ?>
      </div>
    </div>  

    <div class="col-md-6">
      <div class="card1 mb">
        <label  style="font-size: 15px;" ><em class="fas fa-spinner" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Nueva Búsqueda') ?></label>
                                    <?= Html::a('Aceptar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'display: inline;margin: 3px;height: 34px;display: inline;height: 34px;background-color: #707372;',                            
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Nuevo'])
                                    ?>
      </div>
    </div>
  </div>
  <?php ActiveForm::end(); ?>

</div>
<!-- Capa Mensaje -->
<div id="capaIdMensaje" class="capaPT" style="display: none;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">                
                <table class="center">
                <caption><?= Yii::t('app', '.') ?></caption>
                  <thead>
                    <tr>
                      <th scope="col" class="text-center"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></th>
                      <th scope="col" ><?= Yii::t('app', '') ?></th>
                      <th scope="col" class="text-justify"><h4><?= Yii::t('app', 'Buscando información de los filtros seleccionados, por favor espere...') ?></h4></th>
                    </tr>            
                  </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
  function varVerifica(){
    var varcapaIdProceso = document.getElementById("capaIdProceso");
    var varcapaIdMensaje = document.getElementById("capaIdMensaje");

    var vartxtidclientes = document.getElementById("txtidclientes").value;
    var varFechas = document.getElementById("speechparametrizar-fechacreacion").value;
    var variddashboard = document.getElementById("iddashboard").value;
    var varrequester = document.getElementById("requester").value;

    if (vartxtidclientes == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de seleccionar un cliente.","warning");
      return;
    }else{
      if (varFechas == "") {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Debe de seleccionar un rango de fecha.","warning");
        return;
      }else{
        if (variddashboard == "") {
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","Debe de seleccionar almenos un pcrc.","warning");
          return;
        }else{
          if (varrequester == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar una dimension.","warning");
            return;
          }else{
            varcapaIdProceso.style.display = 'none';
            varcapaIdMensaje.style.display = 'inline';
          }
        }
      }
    }

  }
</script>