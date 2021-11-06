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

$this->title = 'Hoja de Vida - Eventos';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

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

</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br>
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>


<div class="capaPrincipal" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-info" style="font-size: 15px; color: #FFC72C;"></em> Ingresar Registros: </label>
      
        <div class="row">
          <div class="col-md-6">
            <label style="font-size: 15px;">Nombre del evento: </label>
            <?= $form->field($model, 'nombre_evento', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idnombreevento', 'placeholder'=>'Ingresar Nombre del Evento'])?>
            
            <label style="font-size: 15px;">Ciudad del evento: </label>
            <?=  $form->field($model, 'hv_idciudad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvCiudad::find()->orderBy(['hv_idciudad'=> SORT_DESC])->all(), 'hv_idciudad', 'ciudad'),
                                        [
                                            'prompt'=>'Seleccione Ciudad...',
                                        ]
                                )->label(''); 
            ?>
          </div>

          <div class="col-md-6">  
            <label style="font-size: 15px;">Tipo de evento: </label>
            <?= $form->field($model, 'tipo_evento', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idtipoevento', 'placeholder'=>'Tipo del Evento'])?>
            
            <label style="font-size: 15px;">Tipo de evento: </label>
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

          <div class="col-md-12"> 
              <label style="font-size: 15px;">Comentarios: </label>
              <?= $form->field($model, 'asistencia', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idasistencia', 'placeholder'=>'Ingresar Comentarios sobre el evento'])?>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
<hr>
<div class="capaBotone" style="display: inline;">
  <div class="row">

    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
        ?>
      </div>  
    </div>

    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Guardar Evento: </label>
        <?= Html::submitButton(Yii::t('app', 'Guardar'),
                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Guardar Evento',
                                    'onclick' => 'validar();']) 
        ?> 
      </div>  
    </div>

  </div>
</div>

<hr>
<div class="capaLista" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #FFC72C;"></em> Lista de Eventos: </label>
        <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
          <caption><?php echo "Total Resultados: ".count($dataProvider); ?></caption>
            <thead>
                <tr>
                  <th scope="col" colspan="7" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Listado de eventos') ?></label></th>
                </tr>
                <tr>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nombre Evento') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo Evento') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Ciudad Evento') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Inicio') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Fin') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Comentarios') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Eliminar') ?></label></th>
                </tr>
            </thead>
            <tbody>
              <?php
                
                foreach ($dataProvider as $key => $value) {
                                
              ?>
                <tr>
                  <td><label style="font-size: 12px;"><?php echo  $value['nombre_evento']; ?></label></td>
                  <td><label style="font-size: 12px;"><?php echo  $value['tipo_evento']; ?></label></td>
                  <td><label style="font-size: 12px;"><?php echo  $value['ciudad']; ?></label></td>
                  <td><label style="font-size: 12px;"><?php echo  $value['fecha_evento_inicio']; ?></label></td>
                  <td><label style="font-size: 12px;"><?php echo  $value['fecha_evento_fin']; ?></label></td>
                  <td><label style="font-size: 12px;"><?php echo  $value['asistencia']; ?></label></td>
                  <td class="text-center">
                    <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarevento','ideventos' => $value['hv_ideventos']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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
<?php $form->end() ?>
<hr>

<script type="text/javascript">
  function validar(){
    var varidnombreevento = document.getElementById("idnombreevento").value;
    var varciudad = document.getElementById("hojavidaeventos-hv_idciudad").value;
    var varidtipoevento = document.getElementById("idtipoevento").value;

    if (varidnombreevento == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar un nombre de evento","warning");
      return;
    }else{
      if (varciudad == "") {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Debe de ingresar la ciudad del evento","warning");
        return;
      }else{
        if (varidtipoevento == "") {
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","Debe de ingresar el tipo de evento","warning");
          return;
        }
      }
    }

  };
</script>