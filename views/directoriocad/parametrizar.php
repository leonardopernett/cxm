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

$this->title = 'Directorio CAD - Parametrizador';//nombre del titulo de mi modulo
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

$varSectortwo = (new \yii\db\Query())
                ->select(['id_sectorcad','nombre'])
                ->from(['tbl_sector_cad'])
                ->where(['=','tbl_sector_cad.anulado',0])
                ->all(); 
  
  $listData2 = ArrayHelper::map($varSectortwo, 'id_sectorcad', 'nombre');

  $varProveedorestwo = (new \yii\db\Query())
            ->select(['id_proveedorescad','name'])
            ->from(['tbl_proveedores_cad'])
            ->where(['=','tbl_proveedores_cad.anulado',0])
            ->all(); 

  $listData3 = ArrayHelper::map($varProveedorestwo, 'id_proveedorescad', 'name');


  $varTipotwo = (new \yii\db\Query())
            ->select(['id_tipocad','nombre'])
            ->from(['tbl_tipo_cad'])
            ->where(['=','tbl_tipo_cad.anulado',0])
            ->all(); 

  $listData4 = ArrayHelper::map($varTipotwo, 'id_tipocad', 'nombre');


  $varTipoCanaltwo = (new \yii\db\Query())
            ->select(['id_tipocanalcad','nombre'])
            ->from(['tbl_tipocanal_cad'])
            ->where(['=','tbl_tipocanal_cad.anulado',0])
            ->all(); 

  $listData5 = ArrayHelper::map($varTipoCanaltwo, 'id_tipocanalcad', 'nombre');

  
  $varEtapatwo = (new \yii\db\Query())
            ->select(['id_etapacad','nombre'])
            ->from(['tbl_etapa_cad'])
            ->where(['=','tbl_etapa_cad.anulado',0])
            ->all(); 

  $listData6 = ArrayHelper::map($varEtapatwo, 'id_etapacad', 'nombre');

  $varSociedadtwo = (new \yii\db\Query())
            ->select(['id_sociedadcad','nombre'])
            ->from(['tbl_sociedad_cad'])
            ->where(['=','tbl_sociedad_cad.anulado',0])
            ->all(); 

  $listData9 = ArrayHelper::map($varSociedadtwo, 'id_sociedadcad', 'nombre');

  $varCiudadtwo = (new \yii\db\Query())
            ->select(['id_ciudad_cad','nombre'])
            ->from(['tbl_ciudad_cad'])
            ->where(['=','tbl_ciudad_cad.anulado',0])
            ->all(); 

  $listData7 = ArrayHelper::map($varCiudadtwo, 'id_ciudad_cad', 'nombre');

  $varVicetwo = (new \yii\db\Query())
            ->select(['id_vicepresidentecad','nombre'])
            ->from(['tbl_vicepresidente_cad'])
            ->where(['=','tbl_vicepresidente_cad.anulado',0])
            ->all();

  $listData8 = ArrayHelper::map($varVicetwo, 'id_vicepresidentecad', 'nombre');

  $varCantidades = (new \yii\db\Query())
              ->select(['COUNT(tipo) as cantidad','tipo'])
              ->from(['tbl_directorio_cad'])
              ->where(['=','tbl_directorio_cad.anulado',0])
              ->groupBy(['tipo'])
              ->all(); 

  $canClientes = (new \yii\db\Query())
              ->select(['*'])
              ->from(['tbl_directorio_cad'])
              ->where(['=','tbl_directorio_cad.anulado',0])
              ->count();

  $varTotalCAD = (new \yii\db\Query())
              ->select(['count(distinct(cliente))'])
              ->from(['tbl_directorio_cad'])
              ->where(['=','tbl_directorio_cad.anulado',0])
              ->scalar();
              
  $canRedes  = (new \yii\db\Query())
              ->select(['tipo'])
              ->from(['tbl_directorio_cad'])
              ->where(['=','tipo',1])
              ->andwhere(['=','tbl_directorio_cad.anulado',0])
              ->count();

  $canCanales = (new \yii\db\Query())
              ->select(['tipo'])
              ->from(['tbl_directorio_cad'])
              ->where(['=','tipo',2])
              ->andwhere(['=','tbl_directorio_cad.anulado',0])
              ->count();


  $varClientestwo = (new \yii\db\Query())
              ->select(['distinct(cliente)'])
              ->from(['tbl_proceso_cliente_centrocosto'])
              ->where(['=','tbl_proceso_cliente_centrocosto.anulado',0])
              ->all();

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
        background-image: url('../../images/Parametrizador.png');
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
      <div class="col-15 text-center">
      </div>
    </div>
  </div>
</header>
<br><hr><br>

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Agregar y Eliminar Atributos"; ?> </label>
      </div>
    </div>
  </div>
<br>

<div class="capaInfo" id="idCapaInfo" style="display: inline;"><!-- div principal que va llevar todo menos la imagen-->
  <div class="row">
    <div class="col-md-12">
        <div class="row">
          <div class="col-md-6">
            <div class="card1 mb">
              <label style="font-size: 15px;"><em class="fas fa-user" style="font-size: 15px; color: #827DF9;"></em><?= Yii::t('app', ' Parametrizar Vicepresidente') ?></label>
              <?= $form->field($modelVicepresidente, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'vicepresidente', 'placeholder'=>'Ingrese nuevo VicepresidenteResultados...'])->label('') ?>
              <div onclick="generarvicepresidente();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                <?= Yii::t('app', ' Guardar') ?>
              </div>
              <br>
              <table style="height:350px;" id="vicepresidentes" class="table table-striped table-bordered tblResDetFreed">
                <caption><?= Yii::t('app', 'Resultados...') ?></caption>
                <thead>
                  <tr>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Vicepresidente') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                  </tr>
                </thead>
                <tbody>

                  <?php 
                  foreach ($varVicetwo as $key => $value) { 
                  ?>
                    
                  
                  
                  <tr>
                    <td><label style="font-size: 12px;"><?php echo  $value['nombre']; ?></label></td>
                    <td class="text-center">

                      <?= 
                      Html::a('<em class="fas fa-times" style="font-size: 15px; color: red;"></em>', ['eliminarvicepresidente','id_vicepresidentecad'=> $value['id_vicepresidentecad']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar Cuenta'])  ?>
                    </td>
                  </tr>
                  <?php }?>
                </tbody>
              </table>

              
            </div>
          </div>
          <div class="col-md-6">
            <div class="card1 mb">
              <label style="font-size: 15px;"><em class="fas fa-clone" style="font-size: 15px; color: #827DF9;"></em><?= Yii::t('app', ' Parametrizar Etapas') ?></label>
              <?= $form->field($modelEtapa, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'etapa', 'placeholder'=>'Ingrese nueva EtapaResultados...'])->label('') ?>
              <div onclick="generaretapa();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                <?= Yii::t('app', ' Guardar') ?>
              </div>
              <br>
              <table style="height:300px;" id="etapass" class="table table-striped table-bordered tblResDetFreed">
                <caption><?= Yii::t('app', 'Resultados...') ?></caption>
                <thead>
                  <tr>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Etapas') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                  </tr>
                </thead>
                <tbody>
                  
                  <?php 
                  foreach ($varEtapatwo as $key => $value) { 
                  ?>
                  
                  <tr>
                    <td><label style="font-size: 15px;"></label><?php echo  $value['nombre']; ?></td>
                    <td class="text-center">

                      <?= 
                      Html::a('<em class="fas fa-times" style="font-size: 15px; color: red;"></em>', ['eliminaretapa','id_etapacad'=> $value['id_etapacad']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar Cuenta'])  ?>
                    </td>
                  </tr>
                  <?php }?>
                    
                </tbody>
              </table>
            </div>
          </div>           
          
        </div>

        <hr>

        <div class="row">
          <div class="col-md-6">
            <div class="card1 mb">
              <label style="font-size: 15px;"><em class="fas fa-globe" style="font-size: 15px; color: #827DF9;"></em><?= Yii::t('app', ' Parametrizar Cuidad') ?></label>
              <?= $form->field($modelCiudad, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'ciudad', 'placeholder'=>'Ingrese nueva Ciudad...'])->label('') ?>
              <div onclick="generarciudad();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                <?= Yii::t('app', ' Guardar') ?>
              </div>
              <br>
              <table id="ciudades" class="table table-striped table-bordered tblResDetFreed">
                <caption><?= Yii::t('app', 'Resultados...') ?></caption>
                <thead>
                  <tr>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Ciudad') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                  </tr>
                </thead>
                <tbody>

                  <?php 
                  foreach ($varCiudadtwo as $key => $value) { 
                  ?>
                  
                  <tr>
                    <td><label style="font-size: 15px;"></label><?php echo  $value['nombre']; ?></td>
                    <td class="text-center">

                      <?= 
                      Html::a('<em class="fas fa-times" style="font-size: 15px; color: red;"></em>', ['eliminarciudad','id_ciudad_cad'=> $value['id_ciudad_cad']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar Cuenta'])  ?>
                    </td>
                  </tr>
                  <?php }?>
                </tbody>
              </table>
            </div>
            

          </div>

          <div class="col-md-6">
            <div class="card1 mb">
              <label style="font-size: 15px;"><em class="fas fa-tag" style="font-size: 15px; color: #827DF9;"></em><?= Yii::t('app', ' Parametrizar Tipo') ?></label>
              <?= $form->field($modelTipo, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'tipo', 'placeholder'=>'Ingrese nuevo Tipo...', ])->label('') ?>
              <div onclick="generartipo();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                <?= Yii::t('app', ' Guardar') ?>
              </div>
              <br>
              <table  id="tipos" class="table table-striped table-bordered tblResDetFreed">
                <caption><?= Yii::t('app', 'Resultados...') ?></caption>
                <thead>
                  <tr>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo ') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                  </tr>
                </thead>
                <tbody>

                  <?php 
                  foreach ($varTipotwo as $key => $value) { 
                  ?>
                  
                  <tr>
                    <td><label style="font-size: 15px;"></label><?php echo  $value['nombre']; ?></td>
                    <td class="text-center">

                      <?= 
                      Html::a('<em class="fas fa-times" style="font-size: 15px; color: red;"></em>', ['eliminartipo','id_tipocad'=> $value['id_tipocad']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar Cuenta'])  ?>
                    </td>
                  </tr>
                  <?php }?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <hr>

        <div class="row">
          <div class="col-md-6">
            <div class="card1 mb">
              <label style="font-size: 15px;"><em class="fas fa-comment" style="font-size: 15px; color: #827DF9;"></em><?= Yii::t('app', ' Parametrizar Sector') ?></label>
              <?= $form->field($modelSector, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'sector', 'placeholder'=>'Ingrese nuevo Sector...'])->label('') ?>
              <div onclick="generarsector();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                <?= Yii::t('app', ' Guardar') ?>
              </div>
              <br>
              <table  style="height:300px;" id="sectores" class="table table-striped table-bordered tblResDetFreed">
                <caption><?= Yii::t('app', 'Resultados...') ?></caption>
                <thead>
                  <tr>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Sector') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                  </tr>
                </thead>
                <tbody>

                  <?php 
                  foreach ($varSectortwo as $key => $value) { 
                  ?>
                  
                  <tr>
                    <td><label style="font-size: 15px;"></label><?php echo  $value['nombre']; ?></td>
                    <td class="text-center">

                      <?= 
                      Html::a('<em class="fas fa-times" style="font-size: 15px; color: red;"></em>', ['eliminarsector','id_sectorcad'=> $value['id_sectorcad']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar Cuenta'])  ?>
                    </td>
                  </tr>
                  <?php }?>
                </tbody>
              </table>
            </div>
            

          </div>

          <div class="col-md-6">
            <div class="card1 mb">
              <label style="font-size: 15px;"><em class="fas fa-tag" style="font-size: 15px; color: #827DF9;"></em><?= Yii::t('app', ' Parametrizar Tipo Canal') ?></label>
              <?= $form->field($modelTipo_Canal, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'tipo_canal', 'placeholder'=>'Ingrese nuevo Tipo Canal...'])->label('') ?>
              <div onclick="generartipo_canal();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                <?= Yii::t('app', ' Guardar') ?>
              </div>
              <br>
              <table style="height:300px;" id="tipos_canal" class="table table-striped table-bordered tblResDetFreed">
                <caption><?= Yii::t('app', 'Resultados...') ?></caption>
                <thead>
                  <tr>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo Canal') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                  </tr>
                </thead>
                <tbody>

                  <?php 
                  foreach ($varTipoCanaltwo as $key => $value) { 
                  ?>
                  
                  <tr>
                    <td><label style="font-size: 15px;"></label><?php echo  $value['nombre']; ?></td>
                    <td class="text-center">

                      <?= 
                      Html::a('<em class="fas fa-times" style="font-size: 15px; color: red;"></em>', ['eliminartipocanal','id_tipocanalcad'=> $value['id_tipocanalcad']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar Cuenta'])  ?>
                    </td>
                  </tr>
                  <?php }?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <hr>

        <div class="row">
          <div class="col-md-6">
            <div class="card1 mb">
              <label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 15px; color: #827DF9;"></em><?= Yii::t('app', ' Parametrizar Proveedores') ?></label>
              <?= $form->field($modelProveedores, 'name', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'proveedores', 'placeholder'=>'Ingrese nuevo Proveedor...'])->label('') ?>
              <div onclick="generarproveedores();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                <?= Yii::t('app', ' Guardar') ?>
              </div>
              <br>
              <table id="proveedoress" class="table table-striped table-bordered tblResDetFreed">
                <caption><?= Yii::t('app', 'Resultados...') ?></caption>
                <thead>
                  <tr>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Proveedores') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                  </tr>
                </thead>
                <tbody>
                  
                  <?php 
                  foreach ($varProveedorestwo as $key => $value) { 
                  ?>
                  
                  <tr>
                    <td><label style="font-size: 15px;"></label><?php echo  $value['name']; ?></td>
                    <td class="text-center">

                      <?= 
                      Html::a('<em class="fas fa-times" style="font-size: 15px; color: red;"></em>', ['eliminarproveedores','id_proveedorescad'=> $value['id_proveedorescad']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar Cuenta'])  ?>
                    </td>
                  </tr>
                  <?php }?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card1 mb">
              <label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 15px; color: #827DF9;"></em><?= Yii::t('app', ' Parametrizar Sociedad') ?></label>
              <?= $form->field($modelSociedad, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'sociedad', 'placeholder'=>'Ingrese nueva Sociedad...'])->label('') ?>
              <div onclick="generarsociedad();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                <?= Yii::t('app', ' Guardar') ?>
              </div>
              <br>
              <table id="sociedades" class="table table-striped table-bordered tblResDetFreed">
                <caption><?= Yii::t('app', 'Resultados...') ?></caption>
                <thead>
                  <tr>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Sociedad') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                  </tr>
                </thead>
                <tbody>
                  
                  <?php 
                  foreach ($varSociedadtwo as $key => $value) { 
                  ?>
                  
                  <tr>
                    <td><label style="font-size: 15px;"></label><?php echo  $value['nombre']; ?></td>
                    <td class="text-center">

                      <?= 
                      Html::a('<em class="fas fa-times" style="font-size: 15px; color: red;"></em>', ['eliminarsociedad','id_sociedadcad'=> $value['id_sociedadcad']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar Cuenta'])  ?>
                    </td>
                  </tr>
                  <?php }?>
                </tbody>
              </table>
            </div>
          </div>

          
        </div>

   
        <br><hr><br>
        

        <div class="row">
          <div class="col-md-12">
            <div class="card1 mb">
              <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #827DF9;"></em> Cancelar y regresar: </label> 
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

<br><hr>
<script>


  $(document).ready( function () {
    $('#sociedades').DataTable({
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

  $(document).ready( function () {
    $('#vicepresidentes').DataTable({
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
    $('.dataTables_wrapper select, .dataTables_length label, .dataTables_filter label, .dataTables_paginate a, .dataTables_info').css('font-size', '15px');
  });


  $(document).ready( function () {
    $('#tipos_canal').DataTable({
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
    $('.dataTables_wrapper select, .dataTables_length label, .dataTables_filter label, .dataTables_paginate a, .dataTables_info').css('font-size', '15px');

  });


  $(document).ready( function () {
    $('#tipos').DataTable({
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
    $('.dataTables_wrapper select, .dataTables_length label, .dataTables_filter label, .dataTables_paginate a, .dataTables_info').css('font-size', '15px');

  });


  $(document).ready( function () {
    $('#ciudades').DataTable({
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
    $('.dataTables_wrapper select, .dataTables_length label, .dataTables_filter label, .dataTables_paginate a, .dataTables_info').css('font-size', '15px');

  });


  $(document).ready( function () {
    $('#proveedoress').DataTable({
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
    $('.dataTables_wrapper select, .dataTables_length label, .dataTables_filter label, .dataTables_paginate a, .dataTables_info').css('font-size', '15px');

  });


  $(document).ready( function () {
    $('#sectores').DataTable({
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
    $('.dataTables_wrapper select, .dataTables_length label, .dataTables_filter label, .dataTables_paginate a, .dataTables_info').css('font-size', '15px');

  });


  $(document).ready( function () {
    $('#etapass').DataTable({
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
    $('.dataTables_wrapper select, .dataTables_length label, .dataTables_filter label, .dataTables_paginate a, .dataTables_info').css('font-size', '15px');

  });


  function generarsector(){
        var varnombresector = document.getElementById("sector").value;

        if (varnombresector == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar un sector","warning");
            return;
        }else{
            $.ajax({
                method: "get",
                url: "ingresarsector",
                data: {
                  txtvaridsector : varnombresector,
                },
                success : function(response){
                  numRta =   JSON.parse(response);          
                  location.reload();
                }
            });
        }
    };


  function generartipo(){
      var varnombretipo = document.getElementById("tipo").value;

      if (varnombretipo == "") {
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","Debe de ingresar un tipo","warning");
          return;
      }else{
          $.ajax({
              method: "get",
              url: "ingresartipo",
              data: {
                txtvaridtipo : varnombretipo,
              },
              success : function(response){
                numRta =   JSON.parse(response);          
                location.reload();
              }
          });
      }
  };

  function generartipo_canal(){
        var varnombretipo_canal = document.getElementById("tipo_canal").value;

        if (varnombretipo_canal == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar un tipo canal","warning");
            return;
        }else{
            $.ajax({
                method: "get",
                url: "ingresartipocanal",
                data: {
                  txtvaridtipo_canal : varnombretipo_canal,
                },
                success : function(response){
                  numRta =   JSON.parse(response);          
                  location.reload();
                }
            });
        }
  };

  function generarproveedores(){
        var varnombreproveedores = document.getElementById("proveedores").value;

        if (varnombreproveedores == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar un proveedor","warning");
            return;
        }else{
            $.ajax({
                method: "get",
                url: "ingresarproveedores",
                data: {
                  txtvaridproveedores : varnombreproveedores,
                },
                success : function(response){
                  numRta =   JSON.parse(response);          
                  location.reload();
                }
            });
        }
  };

  function generarsociedad(){
        var varnombresociedad = document.getElementById("sociedad").value;

        if (varnombresociedad == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar una sociedad","warning");
            return;
        }else{
            $.ajax({
                method: "get",
                url: "ingresarsociedad",
                data: {
                  txtvaridsociedad : varnombresociedad,
                },
                success : function(response){
                  numRta =   JSON.parse(response);          
                  location.reload();
                }
            });
        }
  };

  function generarciudad(){
        var varnombreciudad = document.getElementById("ciudad").value;

        if (varnombreciudad == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar una ciudad","warning");
            return;
        }else{
            $.ajax({
                method: "get",
                url: "ingresarciudad",
                data: {
                  txtvaridciudad : varnombreciudad,
                },
                success : function(response){
                  numRta =   JSON.parse(response);          
                  location.reload();
                }
            });
        }
  };
  function generaretapa(){
        var varnombretapa = document.getElementById("etapa").value;

        if (varnombretapa == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar una etapa","warning");
            return;
        }else{
            $.ajax({
                method: "get",
                url: "ingresaretapa",
                data: {
                  txtvaridetapa : varnombretapa,
                },
                success : function(response){
                  numRta =   JSON.parse(response);          
                  location.reload();
                }
            });
        }
  };

  function generarvicepresidente(){
        var varnombrevicepresidente = document.getElementById("vicepresidente").value;

        if (varnombrevicepresidente == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar un vicepresidente","warning");
            return;
        }else{
            $.ajax({
                method: "get",
                url: "ingresarvicepresidente",
                data: {
                  txtvaridvicepresidente : varnombrevicepresidente,
                },
                success : function(response){
                  numRta =   JSON.parse(response);          
                  location.reload();
                }
            });
        }
  };
</script>