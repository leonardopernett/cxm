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

$this->title = 'Gestor de Clientes - Complementos';
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
      </div>
    </div>
  </div>
</header>
<br><br>
<?php $form = ActiveForm::begin([
  'layout' => 'horizontal',
  'fieldConfig' => [
    'inputOptions' => ['autocomplete' => 'off']
  ]
  ]); ?>

<div class="capaPrincipal" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      
      <div class="row">
        <div class="col-md-6">
          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-external-link-square-alt" style="font-size: 15px; color: #FFC72C;"></em> Estado Civil </label>
            
            <?= $form->field($model, 'estadocivil', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idcivil', 'placeholder'=>'Ingresar Estado Civil'])?>

            <div onclick="generatedcivil();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                  Guardar
            </div>
            <br>
            <table id="tblDataCivil" class="table table-striped table-bordered tblResDetFreed">
              <caption>Resultados</caption>
              <thead>
                <tr>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'ID') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Estado Civil') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                </tr>
              </thead>
              <tbody>
                <?php
                  foreach ($dataProviderCivil as $key => $value) {                    
                ?>
                  <tr>
                    <td><label style="font-size: 12px;"><?php echo  $value['hv_idcivil']; ?></label></td>
                    <td><label style="font-size: 12px;"><?php echo  $value['estadocivil']; ?></label></td>
                    <td class="text-center">
                      <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarcivil','id'=> $value['hv_idcivil']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                    </td>
                  </tr>
                <?php
                  }
                ?>
              </tbody>
            </table>

          </div>
        </div>
      
        <div class="col-md-6">
          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-external-link-square-alt" style="font-size: 15px; color: #FFC72C;"></em> Dominancia Cerebral </label>

             <?= $form->field($model1, 'dominancia', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'iddominancia', 'placeholder'=>'Ingresar Dominancia Cerebral'])?>

            <div onclick="generatedominancia();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                  Guardar
            </div>
            <br>
            <table id="tblDataDominancia" class="table table-striped table-bordered tblResDetFreed">
              <caption>Resultados</caption>
              <thead>
                <tr>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'ID') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Dominancia') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                </tr>
              </thead>
              <tbody>
                <?php
                  foreach ($dataProviderDominancias as $key => $value) {                    
                ?>
                  <tr>
                    <td><label style="font-size: 12px;"><?php echo  $value['iddominancia']; ?></label></td>
                    <td><label style="font-size: 12px;"><?php echo  $value['dominancia']; ?></label></td>
                    <td class="text-center">
                      <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminardominancia','id'=> $value['iddominancia']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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
      <br>
      <div class="row">
        <div class="col-md-6">
          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-external-link-square-alt" style="font-size: 15px; color: #FFC72C;"></em> Estilo Social </label>

            <?= $form->field($model2, 'estilosocial', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idestio', 'placeholder'=>'Ingresar Estilo Social'])?>

            <div onclick="generatedestilo();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                  Guardar
            </div>
            <br>
            <table id="tblDataestilo" class="table table-striped table-bordered tblResDetFreed">
              <caption>Resultados</caption>
              <thead>
                <tr>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'ID') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Estilo Social') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                </tr>
              </thead>
              <tbody>
                <?php
                  foreach ($dataProviderEstilo as $key => $value) {                    
                ?>
                  <tr>
                    <td><label style="font-size: 12px;"><?php echo  $value['idestilosocial']; ?></label></td>
                    <td><label style="font-size: 12px;"><?php echo  $value['estilosocial']; ?></label></td>
                    <td class="text-center">
                      <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarsocial','id'=> $value['idestilosocial']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                    </td>
                  </tr>
                <?php
                  }
                ?>
              </tbody>
            </table>

          </div>
        </div>
      
        <div class="col-md-6">
          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-external-link-square-alt" style="font-size: 15px; color: #FFC72C;"></em> Hobbies </label>

            <?= $form->field($model3, 'text', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idhobbies', 'placeholder'=>'Ingresar el Hobbie'])?>

            <div onclick="generatedhobbies();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                  Guardar
            </div>
            <br>
            <table id="tblDataHobbie" class="table table-striped table-bordered tblResDetFreed">
              <caption>Resultados</caption>
              <thead>
                <tr>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'ID') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Hobbie') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                </tr>
              </thead>
              <tbody>
                <?php
                  foreach ($dataProviderHobbies as $key => $value) {                    
                ?>
                  <tr>
                    <td><label style="font-size: 12px;"><?php echo  $value['id']; ?></label></td>
                    <td><label style="font-size: 12px;"><?php echo  $value['text']; ?></label></td>
                    <td class="text-center">
                      <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarhobbie','id'=> $value['id']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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
      <br>
      <div class="row">   
        <div class="col-md-6">
          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-external-link-square-alt" style="font-size: 15px; color: #FFC72C;"></em> Intereses / Gustos </label>

            <?= $form->field($model4, 'text', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idgustos', 'placeholder'=>'Ingresar Gustos'])?>

            <div onclick="generatedgustos();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                  Guardar
            </div>
            <br>
            <table id="tblDatagustos" class="table table-striped table-bordered tblResDetFreed">
              <caption>Resultados</caption>
              <thead>
                <tr>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'ID') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Hobbie') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                </tr>
              </thead>
              <tbody>
                <?php
                  foreach ($dataProvidergustos as $key => $value) {                    
                ?>
                  <tr>
                    <td><label style="font-size: 12px;"><?php echo  $value['id']; ?></label></td>
                    <td><label style="font-size: 12px;"><?php echo  $value['text']; ?></label></td>
                    <td class="text-center">
                      <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminargustos','id'=> $value['id']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                    </td>
                  </tr>
                <?php
                  }
                ?>
              </tbody>
            </table>


          </div>
        </div>

        <div class="col-md-6">
          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-external-link-square-alt" style="font-size: 15px; color: #FFC72C;"></em> Clasificacion Konecta </label>

            <?= $form->field($model5, 'ciudadclasificacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idclasificar', 'placeholder'=>'Ingresar Ciudad de Clasificacion'])?>

            <div onclick="generatedclasificacion();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                  Guardar
            </div>
            <br>
            <table id="tblDataclasificacion" class="table table-striped table-bordered tblResDetFreed">
              <caption>Resultados</caption>
              <thead>
                <tr>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'ID') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Ciudad de Clasificacion') ?></label></th>
                  <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                </tr>
              </thead>
              <tbody>
                <?php
                  foreach ($dataProviderClasificacion as $key => $value) {                    
                ?>
                  <tr>
                    <td><label style="font-size: 12px;"><?php echo  $value['hv_idclasificacion']; ?></label></td>
                    <td><label style="font-size: 12px;"><?php echo  $value['ciudadclasificacion']; ?></label></td>
                    <td class="text-center">
                      <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarclasificacion','id'=> $value['hv_idclasificacion']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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
  </div>
</div>

<?php $form->end() ?>
<hr>
<div class="capaDos" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      
      <div class="row">
        <div class="col-md-6">
          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
            <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Regresar']) 
            ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script type="text/javascript">
  function generatedcivil(){
    var varidcivil = document.getElementById("idcivil").value;
    
    if (varidcivil == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar un estado civil","warning");
      return;
    }else{
      $.ajax({
        method: "get",
        url: "ingresarcivil",
        data: {
          txtvaridcivil : varidcivil,
        },
        success : function(response){
          numRta =   JSON.parse(response);          
          location.reload();
        }
      });
    }
  };

  function generatedominancia(){
    var iddominancia = document.getElementById("iddominancia").value;
    
    if (iddominancia == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar un listado de dominancias separados por comas","warning");
      return;
    }else{
      $.ajax({
        method: "get",
        url: "ingresardominancia",
        data: {
          txtiddominancia : iddominancia,
        },
        success : function(response){
          numRta =   JSON.parse(response);          
          location.reload();
        }
      });
    }
  };

  function generatedestilo(){
    var varidestio = document.getElementById("idestio").value;
    
    if (iddominancia == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar un estilo de vida","warning");
      return;
    }else{
      $.ajax({
        method: "get",
        url: "ingresarestilo",
        data: {
          txtvaridestio : varidestio,
        },
        success : function(response){
          numRta =   JSON.parse(response);          
          location.reload();
        }
      });
    }
  };

  function generatedhobbies(){
    var varidhobbies = document.getElementById("idhobbies").value;
    
    if (iddominancia == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar Hobbie","warning");
      return;
    }else{
      $.ajax({
        method: "get",
        url: "ingresarhobbie",
        data: {
          txtvaridhobbies : varidhobbies,
        },
        success : function(response){
          numRta =   JSON.parse(response);          
          location.reload();
        }
      });
    }
  };

  function generatedgustos(){
    var varidgustos = document.getElementById("idgustos").value;
    
    if (iddominancia == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar Gustos","warning");
      return;
    }else{
      $.ajax({
        method: "get",
        url: "ingresargustos",
        data: {
          txtvaridgustos : varidgustos,
        },
        success : function(response){
          numRta =   JSON.parse(response);          
          location.reload();
        }
      });
    }
  };

  function generatedclasificacion(){
    var varidclasificar = document.getElementById("idclasificar").value;
    
    if (varidclasificar == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar la ciudad de clasificacion","warning");
      return;
    }else{
      $.ajax({
        method: "get",
        url: "ingresarclasificar",
        data: {
          txtvaridclasificar : varidclasificar,
        },
        success : function(response){
          numRta =   JSON.parse(response);          
          location.reload();
        }
      });
    }
  };
</script>