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
use app\models\Hojavidaroles;

$this->title = 'Gestor de Clientes - Requerimientos del Contrato';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Gestor de Clientes - Requerimientos del Contrato';

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

  $varRespuesta = ['1'=>'Si','2'=>'No'];
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
    font-family: "Nunito";
    font-size: 150%;    
    text-align: left;    
  }

  .card2 {
    height: 200px;
    width: auto;
    margin-top: auto;
    margin-bottom: auto;
    background: #FFFFFF;
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
    background-image: url('../../images/Gestor_de_Clientes.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<!-- Data extensiones -->

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

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<!-- Capa Proceso Informacion General -->
<div id="capaIdGeneral" class="capaGeneral" style="display: inline;">

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?= Yii::t('app', ' Ficha Técnica') ?></label>
            </div>
        </div>
    </div>

    <br>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Servicio') ?></label>
                <label style="font-size: 15px; text-align: center;"><?= Yii::t('app', $varNombreCliente ) ?></label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Lista de Pcrc') ?></label>

                <div class="col-md-12 text-center">          
                    <div onclick="opennovedadpcrc();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbnpcrc1" >
                        <label style="text-align: center;"><?= Yii::t('app', 'Abrir Listado Pcrc [ + ]') ?></label>
                    </div> 
                                    
                    <div onclick="closenovedadpcrc();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="idtbnpcrc2" >
                        <label style="text-align: center;"><?= Yii::t('app', 'Cerrar Listado Pcrc [ - ]') ?></label>
                    </div> 
                </div>

                <div class="capaListapcrcs" id="capaListapcrcs" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <table border="0">
                                <?php 
                                    foreach ($varNombrePcrcs as $key => $value) {
                                        $varPcrcNombre = $value['pcrc'];
                                        $varCodPcrc = $value['cod_pcrc'];
                                ?>
                                <tr>
                                    <td><label style="font-size: 15px; text-align: center;"><?= Yii::t('app', '* '.$varCodPcrc.' - '.$varPcrcNombre ) ?></label></td>
                                </tr>                                    
                                <?php 
                                    }
                                ?>
                            </table>                            
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-4">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Lista de Director') ?></label>

                <div class="col-md-12 text-center">          
                    <div onclick="opennovedaddir();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbndir1" >
                        <label style="text-align: center;"><?= Yii::t('app', 'Abrir Listado Diretores [ + ]') ?></label>
                    </div> 
                                    
                    <div onclick="closenovedaddir();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="idtbndir2" >
                        <label style="text-align: center;"><?= Yii::t('app', 'Cerrar Listado Directores [ - ]') ?></label>
                    </div> 
                </div>

                <div class="capaListadir" id="capaListadir" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <table border="0">
                                <?php 
                                    foreach ($varNombreDirectores as $key => $value) {
                                        $varDirNombre = $value['director_programa'];
                                ?>
                                <tr>
                                    <td><label style="font-size: 15px; text-align: center;"><?= Yii::t('app', '* '.$varDirNombre ) ?></label></td>
                                </tr>                                    
                                <?php 
                                    }
                                ?>
                            </table>                            
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<hr>

<!-- Capa Proceso Persona -->
<div id="capaIdPersona" class="capaPersona" style="display: inline;">

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?= Yii::t('app', ' Requerimientos del Contrato') ?></label>
            </div>
        </div>
    </div>

    <br>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Requerimientos sobre los roles') ?></label>

                <div class="row">
                    <div class="col-md-6">
                        <?= Html::button('Agregar Datos', ['value' => url::to(['importarpersona','id'=>$id_contrato]), 'class' => 'btn btn-success', 'id'=>'modalButton',
                                'data-toggle' => 'tooltip',
                                'title' => 'Selección del Cliente']) ?> 

                        <?php
                            Modal::begin([
                                'header' => '<h4>Agregar Información Persona</h4>',
                                'id' => 'modal',
                                'size' => 'modal-lg',
                            ]);

                            echo "<div id='modalContent'></div>";
                                                                                                  
                            Modal::end(); 
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table id="tblDataPersona" class="table table-striped table-bordered tblResDetFreed">
                          <caption><?= Yii::t('app', 'Resultados') ?></caption>
                          <thead>
                            <tr>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Rol') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Perfil') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Salario') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Variable') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Total Salario') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Anexo') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php
                            foreach ($vardataProviderPersona as $key => $value) {  

                                $varRolNombre = (new \yii\db\Query())
                                              ->select(['hvroles'])
                                              ->from(['tbl_hojavida_roles'])
                                              ->where(['=','tbl_hojavida_roles.id_hvroles',$value['id_hvroles']])
                                              ->andwhere(['=','tbl_hojavida_roles.anulado',0])
                                              ->Scalar();                  
                          ?>
                            <tr>
                                <td><label style="font-size: 12px;"><?php echo  $value['id_bloquepersona']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varRolNombre; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $value['perfil']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  '$ '.$value['salario']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  '$ '.$value['variable']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  '$ '.$value['totalsalario']; ?></label></td>
                                <td class="text-center">
                                    <?php if ($value['rutaanexo'] != "") { ?>
                                        <label><em class="fas fa-check" style="font-size: 20px; color: #26cd33;"></em><?= Yii::t('app', '') ?></label>
                                    <?php }else{ ?>
                                        <label><em class="fas fa-times" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', '') ?></label>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarbloquepersona','id'=> $value['id_bloquepersona'],'id_contrato'=>$id_contrato], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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

    <br>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Requerimientos sobre los entregables') ?></label>

                <div class="row">
                    <div class="col-md-6">
                        <?= Html::button('Agregar Datos', ['value' => url::to(['importarentregables','id'=>$id_contrato]), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                                'data-toggle' => 'tooltip',
                                'title' => 'Selección del Informes']) ?> 

                        <?php
                            Modal::begin([
                                'header' => '<h4>Agregar Información de Entregables</h4>',
                                'id' => 'modal1',
                                'size' => 'modal-lg',
                            ]);

                            echo "<div id='modalContent1'></div>";
                                                                                                  
                            Modal::end(); 
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table id="tblDataEntregable" class="table table-striped table-bordered tblResDetFreed">
                          <caption><?= Yii::t('app', 'Resultados') ?></caption>
                          <thead>
                            <tr>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Entregable') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Alcance') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Periocidad') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Anexo') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php
                            foreach ($vardataProviderentregable as $key => $value) {  

                                $varNombreInforme = (new \yii\db\Query())
                                              ->select(['hvinforme'])
                                              ->from(['tbl_hojavida_informe'])
                                              ->where(['=','tbl_hojavida_informe.id_hvinforme',$value['id_hvinforme']])
                                              ->andwhere(['=','tbl_hojavida_informe.anulado',0])
                                              ->Scalar();   

                                $varPeriocidad = (new \yii\db\Query())
                                              ->select(['hvperiocidad'])
                                              ->from(['tbl_hojavida_periocidad'])
                                              ->where(['=','tbl_hojavida_periocidad.id_hvperiocidad',$value['id_hvperiocidad']])
                                              ->andwhere(['=','tbl_hojavida_periocidad.anulado',0])
                                              ->Scalar();              
                          ?>
                            <tr>
                              <td><label style="font-size: 12px;"><?php echo  $value['id_bloqueinformes']; ?></label></td>
                              <td><label style="font-size: 12px;"><?php echo  $varNombreInforme; ?></label></td>
                              <td><label style="font-size: 12px;"><?php echo  $value['alcance']; ?></label></td>
                              <td><label style="font-size: 12px;"><?php echo  $varPeriocidad; ?></label></td>
                              <td class="text-center">
                                    <?php if ($value['rutaanexoinforme'] != "") { ?>
                                        <label><em class="fas fa-check" style="font-size: 20px; color: #26cd33;"></em><?= Yii::t('app', '') ?></label>
                                    <?php }else{ ?>
                                        <label><em class="fas fa-times" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', '') ?></label>
                                    <?php } ?>
                              </td>
                              <td class="text-center">
                                <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarbloqueentregable','id'=> $value['id_bloqueinformes'],'id_contrato'=>$id_contrato], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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

    <br>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Requerimientos sobre las herramientas') ?></label>

                <div class="row">
                    <div class="col-md-6">
                        <?= Html::button('Agregar Datos', ['value' => url::to(['importarherramientas','id'=>$id_contrato]), 'class' => 'btn btn-success', 'id'=>'modalButton2',
                                'data-toggle' => 'tooltip',
                                'title' => 'Agregar Información de Herramientas']) ?> 

                        <?php
                            Modal::begin([
                                'header' => '<h4>Agregar Información de Herramientas</h4>',
                                'id' => 'modal2',
                                'size' => 'modal-lg',
                            ]);

                            echo "<div id='modalContent2'></div>";
                                                                                                  
                            Modal::end(); 
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table id="tblDataHerramientas" class="table table-striped table-bordered tblResDetFreed">
                          <caption><?= Yii::t('app', 'Resultados') ?></caption>
                          <thead>
                            <tr>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Alcance') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Funcionalidades') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Detalle') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Anexo') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php
                            foreach ($vardataProviderherramientas as $key => $value) {  
           
                          ?>
                            <tr>
                              <td><label style="font-size: 12px;"><?php echo  $value['id_bloqueherramienta']; ?></label></td>
                              <td><label style="font-size: 12px;"><?php echo  $value['alcance']; ?></label></td>
                              <td><label style="font-size: 12px;"><?php echo  $value['funcionalidades']; ?></label></td>
                              <td><label style="font-size: 12px;"><?php echo  $value['detalle']; ?></label></td>
                              <td class="text-center">
                                    <?php if ($value['rutaanexoherramienta'] != "") { ?>
                                        <label><em class="fas fa-check" style="font-size: 20px; color: #26cd33;"></em><?= Yii::t('app', '') ?></label>
                                    <?php }else{ ?>
                                        <label><em class="fas fa-times" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', '') ?></label>
                                    <?php } ?>
                              </td>
                              <td class="text-center">
                                <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarbloqueherramienta','id'=> $value['id_bloqueherramienta'],'id_contrato'=>$id_contrato], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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
    
    <br>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Requerimientos sobre las métricas/KPI') ?></label>

                <div class="row">
                    <div class="col-md-6">
                        <?= Html::button('Agregar Datos', ['value' => url::to(['importarmetricas','id'=>$id_contrato]), 'class' => 'btn btn-success', 'id'=>'modalButton3',
                                'data-toggle' => 'tooltip',
                                'title' => 'Selección del Métricas']) ?> 

                        <?php
                            Modal::begin([
                                'header' => '<h4>Agregar Información de Métricas</h4>',
                                'id' => 'modal3',
                                'size' => 'modal-lg',
                            ]);

                            echo "<div id='modalContent3'></div>";
                                                                                                  
                            Modal::end(); 
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table id="tblDataMetricas" class="table table-striped table-bordered tblResDetFreed">
                          <caption><?= Yii::t('app', 'Resultados') ?></caption>
                          <thead>
                            <tr>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Métrica') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Objetivo') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Penalización') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Rango de Penalización') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Anexo') ?></label></th>
                              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php
                            foreach ($vardataProvidermetricas as $key => $value) {  

                                $varNombreMetrica = (new \yii\db\Query())
                                              ->select(['hvmetrica'])
                                              ->from(['tbl_hojavida_metricas'])
                                              ->where(['=','tbl_hojavida_metricas.id_hvmetrica',$value['id_hvmetrica']])
                                              ->andwhere(['=','tbl_hojavida_metricas.anulado',0])
                                              ->Scalar();   

                                $varPenaliza = null;
                                if ($value['penalizacion'] == "1") {
                                    $varPenaliza = "Si";
                                }else{
                                    $varPenaliza = "No";
                                }            
                          ?>
                            <tr>
                              <td><label style="font-size: 12px;"><?php echo  $value['id_bloquekpis']; ?></label></td>
                              <td><label style="font-size: 12px;"><?php echo  $varNombreMetrica; ?></label></td>
                              <td><label style="font-size: 12px;"><?php echo  $value['obtjetivo']; ?></label></td>
                              <td><label style="font-size: 12px;"><?php echo  $varPenaliza; ?></label></td>
                              <td><label style="font-size: 12px;"><?php echo  $value['rango']; ?></label></td>
                              <td class="text-center">
                                    <?php if ($value['rutaanexokpis'] != "") { ?>
                                        <label><em class="fas fa-check" style="font-size: 20px; color: #26cd33;"></em><?= Yii::t('app', '') ?></label>
                                    <?php }else{ ?>
                                        <label><em class="fas fa-times" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', '') ?></label>
                                    <?php } ?>
                              </td>
                              <td class="text-center">
                                <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarbloquekpis','id'=> $value['id_bloquekpis'],'id_contrato'=>$id_contrato], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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

    <br>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-hand-pointer" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Requerimientos sobre los recursos fisicos') ?></label>
                
                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Tiene Salas Exclusivas') ?></label>
                        <?= $form->field($model, "cliente", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varRespuesta, ['prompt' => 'Seleccionar...', 'id'=>"idsalas"]) ?> 
                    </div>
                    <div class="col-md-6">
                        <label style="font-size: 15px;"> <?= Yii::t('app', ' Comentarios') ?></label>
                        <?= $form->field($model, 'director', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idcomentario', 'placeholder'=>'Ingresar Comentarios'])?> 
                    </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <table id="tblDataMetricas" class="table table-striped table-bordered tblResDetFreed">
                      <caption><?= Yii::t('app', 'Resultados') ?></caption>
                      <thead>
                        <tr>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Salas Exclusivas') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Comentarios') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          foreach ($vardataExclusivas as $key => $value) {  
                            
                            $varExclusiva = null;
                            if ($value['exclusivas'] == "1") {
                              $varExclusiva = "Si";
                            }else{
                              if ($value['exclusivas'] == "2") {
                                $varExclusiva = "No";
                              }else{
                                $varExclusiva = null;
                              }                                    
                            }            
                          ?>
                            <tr>
                              <td><label style="font-size: 12px;"><?php echo  $value['id_bloquesalas']; ?></label></td>
                              <td><label style="font-size: 12px;"><?php echo  $varExclusiva; ?></label></td>
                              <td><label style="font-size: 12px;"><?php echo  $value['comentarios']; ?></label></td>
                              <td class="text-center">
                                <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarbloqueexclusiva','id'=> $value['id_bloquesalas'],'id_contrato'=>$id_contrato], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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

<hr>

<!-- Capa Acciones Botones -->
<div id="capaIdBtnAccion" class="capaBtnAccion" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FC4343;"></em><?= Yii::t('app', ' Guardar Información Contrato') ?></label>
                <?= Html::submitButton(Yii::t('app', 'Aceptar'),
                          ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                              'data-toggle' => 'tooltip',
                              'title' => 'Aceptar Informacion',
                              'onclick' => 'verificardata();',
                              'id'=>'modalButton']) 
                ?> 
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FC4343;"></em><?= Yii::t('app', ' Cancelar y Regresar') ?></label>
                <?= Html::a('Cancelar',  ['index'], ['class' => 'btn btn-success',
                                                    'style' => 'background-color: #707372',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Regresar']) 
                ?>
            </div>
        </div>
    </div>

</div>

<hr>

<?php ActiveForm::end(); ?>

<script type="text/javascript">    

    function opennovedadpcrc(){
        var varidtbnpcrc1 = document.getElementById("idtbnpcrc1");
        var varidtbnpcrc2 = document.getElementById("idtbnpcrc2");
        var varidnovedadpcrc = document.getElementById("capaListapcrcs");

        varidtbnpcrc1.style.display = 'none';
        varidtbnpcrc2.style.display = 'inline';
        varidnovedadpcrc.style.display = 'inline';
    };

    function closenovedadpcrc(){
        var varidtbnpcrc1 = document.getElementById("idtbnpcrc1");
        var varidtbnpcrc2 = document.getElementById("idtbnpcrc2");
        var varidnovedadpcrc = document.getElementById("capaListapcrcs");

        varidtbnpcrc1.style.display = 'inline';
        varidtbnpcrc2.style.display = 'none';
        varidnovedadpcrc.style.display = 'none';
    };

    function opennovedaddir(){
        var varidtbndir1 = document.getElementById("idtbndir1");
        var varidtbndir2 = document.getElementById("idtbndir2");
        var varidnovedaddir = document.getElementById("capaListadir");

        varidtbndir1.style.display = 'none';
        varidtbndir2.style.display = 'inline';
        varidnovedaddir.style.display = 'inline';
    };

    function closenovedaddir(){
        var varidtbndir1 = document.getElementById("idtbndir1");
        var varidtbndir2 = document.getElementById("idtbndir2");
        var varidnovedaddir = document.getElementById("capaListadir");

        varidtbndir1.style.display = 'inline';
        varidtbndir2.style.display = 'none';
        varidnovedaddir.style.display = 'none';
    };

    function verificardata(){
        var varidsalas = document.getElementById("idsalas").value;

        if (varidsalas == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar si tiene salas exclusivas","warning");
            return;
        }
    };
</script>