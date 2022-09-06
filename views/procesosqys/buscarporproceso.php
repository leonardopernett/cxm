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
            <?=
                    $form->field($model, 'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->checkboxList(
                        [],
                        [
                            "id" =>"requester",
                            'item'=>function ($index, $label, $name, $checked, $value)
                            {
                                return '<div class="col-md-12">
                                            <input type="checkbox"/>'.$label.'
                                        </div>';
                            }

                      ])->label('');
            ?>
            <?= $form->field($model, 'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 350, 'class' => 'hidden',  'id'=>'txtIdCod_pcrc'])?>
          </div>

          <div class="col-md-6">
            <label style="font-size: 15px;"><em class="fas fa-hand-pointer" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Seleccionar Dimensión') ?></label>
            <?= $form->field($model, 'anulado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varextensiones, ['prompt' => 'Seleccionar...', 'id'=>'iddashboard','onclick'=>'verProcesos();']) ?>            
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
                                          'onclick' => 'verifica();']) 
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
                <table align="center">
                  <thead>
                    <tr>
                      <th class="text-center"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></th>
                      <th><?= Yii::t('app', '') ?></th>
                      <th class="text-justify"><h4><?= Yii::t('app', 'Buscando información de los filtros seleccionados, por favor espere...') ?></h4></th>
                    </tr>            
                  </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
  function verifica(){
    var cant = document.getElementById("requester").querySelectorAll(".listach");
       
    var varpcrc = "";
    for (var x = 0; x < cant.length; x++) {
      if(document.getElementById("lista_"+(x+1)).checked){
        varpcrc = varpcrc + "'" + document.getElementById("lista_"+(x+1)).value + "'" + ",";
      }
    }
    varpcrc = varpcrc.substring(0,varpcrc.length - 2);
    varpcrc = varpcrc.substring(1);

    document.getElementById("txtIdCod_pcrc").value = varpcrc;

    var varcapaOne = document.getElementById("capaIdProceso");
    var varcapaTwo = document.getElementById("capaIdMensaje");

    var varidDocDirector = document.getElementById("txtidclientes").value;
    var varidDimension = document.getElementById("iddashboard").value;
    var varFechas = document.getElementById("speechparametrizar-fechacreacion").value;

    if (varidDocDirector == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Se debe de seleccionar un Director","warning");
      return;
    }else{
      if (varidDimension == "") {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Se debe de seleccionar una dimensión","warning");
        return;
      }else{
        if (varFechas == "") {
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","Se debe de seleccionar un rango de fecha","warning");
          return;
        }else{
          varcapaOne.style.display = 'none';
          varcapaTwo.style.display = 'inline';
        }
      }
    }
  }
</script>