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
use app\models\HvPais;

$this->title = 'Hoja de Vida - Pais & Ciudad';
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
      </div>
    </div>
  </div>
</header>
<br><br>
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>


<div class="capaPrincipal" style="display: inline;">
  <div class="row">

    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Registro de Paises: </label>
      
            <?= 
                Html::button('Crear Pais', ['value' => url::to(['creapais']), 'class' => 'btn btn-success', 'style' => 'background-color: #337ab7', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Crear pais'])
            ?>
            <?php
                Modal::begin([
                    'header' => '<h4></h4>',
                    'id' => 'modal1',
                ]);

                echo "<div id='modalContent1'></div>";
                                                        
                Modal::end(); 
            ?> 

      </div>
    </div>

    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Registros de Ciudades: </label>
      
            <?= 
                Html::button('Crear Ciudad', ['value' => url::to(['creaciudad']), 'class' => 'btn btn-success', 'style' => 'background-color: #337ab7', 'id'=>'modalButton2', 'data-toggle' => 'tooltip', 'title' => 'Crear Ciudad'])
            ?>
            <?php
                Modal::begin([
                    'header' => '<h4></h4>',
                    'id' => 'modal2',
                ]);

                echo "<div id='modalContent2'></div>";
                                                        
                Modal::end(); 
            ?> 

      </div>
    </div>

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

  </div>
</div>
<hr>
<div class="capaLista" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
        <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #FFC72C;"></em> Lista de Paises y Ciudades: </label>

            <div class="row">
                <div class="col-md-6">
                    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                        <caption><?php echo "Total Resultados: ".count($dataProviderPais); ?></caption>
                        <thead>
                            <tr>
                              <th scope="col" colspan="2" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Listado de paises') ?></label></th>
                            </tr>
                            <tr>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Pais') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Eliminar') ?></label></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                
                                foreach ($dataProviderPais as $key => $value) {
                                                
                              ?>
                                <tr>
                                  <td><label style="font-size: 12px;"><?php echo  $value['pais']; ?></label></td>
                                  <td class="text-center">
                                    <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarpais','idpais' => $value['hv_idpais']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                                  </td>
                                </tr>
                              <?php
                                }
                              ?>
                        </tbody>
                    </table>
                </div>

                <div class="col-md-6">
                    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                        <caption><?php echo "Total Resultados: ".count($dataProviderCiudad); ?></caption>
                        <thead>
                            <tr>
                              <th scope="col" colspan="3" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Listado de Ciudades') ?></label></th>
                            </tr>
                            <tr>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Pais') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Ciudad') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Eliminar') ?></label></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                
                                foreach ($dataProviderCiudad as $key => $value) {
                                    $piseslist = HvPais::findOne($value['pais_id']);
                                    $varnombrepais = $piseslist->pais;
                              ?>
                                <tr>
                                  <td><label style="font-size: 12px;"><?php echo  $varnombrepais; ?></label></td>
                                  <td><label style="font-size: 12px;"><?php echo  $value['ciudad']; ?></label></td>
                                  <td class="text-center">
                                    <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarciudad','idciudad' => $value['hv_idciudad']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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
