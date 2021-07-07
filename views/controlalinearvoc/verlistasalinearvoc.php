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

	$this->title = 'Vista Alinear + VOC';
	$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

?>

<style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Nunito');

    .card {
            height: 500px;
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

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../../images/Reporte-Alinear-+-Voc.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<link rel="stylesheet" href="https://qa.grupokonecta.local/qa_managementv2/web/css/font-awesome/css/font-awesome.css"  >
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
<div class="BloqueUno" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label><i class="fas fa-paperclip" style="font-size: 20px; color: #2CA5FF;"></i> Información de partida: </label>
        <div class="row">
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Programa o PCRC: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtArbol; ?></label>
          </div>
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Valorado: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtNombreTecnico; ?></label>
          </div>
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> ID Externo Speecho: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtSpeech; ?></label>
          </div>
        </div><hr>
        <div class="row">
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Fecha y hora: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtFecha; ?></label>
          </div>
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Usuario de Agente: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtAgente; ?></label>
          </div>
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Duración en Segundos: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtDureacion; ?></label>
          </div>
        </div><hr>
        <div class="row">
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Extensión: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtExtension; ?></label>
          </div>
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Dimensión: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtDimensiones; ?></label>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<hr>



<div class="SegundoBloque" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label><i class="fas fa-tags" style="font-size: 20px; color: #49941e;"></i> Categorías:</label>
        <div class="row">
          <div class="col-md-12">
           <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #49941e;"></i> Participantes: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtNombrePartic; ?></label>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-md-12">
            <table class="table table-striped table-bordered detail-view formDinamico">
              <thead>
                <tr>        
                  <th class="text-center"><i class="fas fa-asterisk" style="font-size: 10px; color: #49941e;"></i><?= Yii::t('app', ' Servicio') ?></th>
                  <th class="text-center"><i class="fas fa-asterisk" style="font-size: 10px; color: #49941e;"></i><?= Yii::t('app', ' Nombre Sesión') ?></th>
                  <th class="text-center"><i class="fas fa-asterisk" style="font-size: 10px; color: #49941e;"></i><?= Yii::t('app', ' Nombre Categoría') ?></th>
                  <th class="text-center"><i class="fas fa-asterisk" style="font-size: 10px; color: #49941e;"></i><?= Yii::t('app', ' Atributos') ?></th>
                  <th class="text-center"><i class="fas fa-asterisk" style="font-size: 10px; color: #49941e;"></i><?= Yii::t('app', ' Medición') ?></th>
                  <th class="text-center"><i class="fas fa-asterisk" style="font-size: 10px; color: #49941e;"></i><?= Yii::t('app', ' Conclusiones') ?></th>
                </tr>      
              </thead>
              <tbody>    
                <?php
                $txtValorPrecision=null;
                $txtQuery2 =  new Query;
                          if($txtSesion2 == 3){
                            $txtValorPrecision=$indicadorPrecic1;
                            $txtQuery2  ->select(['tbl_arbols.name', 'tbl_sesion_alinear.sesion_nombre', 'tbl_categorias_alinear.categoria_nombre', 'tbl_atributos_alinear.atributo_nombre', 'tbl_atributos_alinear.id_atrib_alin'])
                                        ->from('tbl_categorias_alinear')
                                        ->join('INNER JOIN', 'tbl_atributos_alinear',
                                            'tbl_categorias_alinear.id_categ_ali = tbl_atributos_alinear.id_categ_ali')
                                        ->join('INNER JOIN', 'tbl_arbols',
                                            'tbl_categorias_alinear.arbol_id = tbl_arbols.id')
                                        ->join('INNER JOIN', 'tbl_sesion_alinear',
                                            'tbl_categorias_alinear.sesion_id = tbl_sesion_alinear.sesion_id')
                                        ->where(['tbl_arbols.id' => $varArbol]);
                          } else{
                            $txtValorPrecision=$indicadorPrecic2;
                            $txtQuery2  ->select(['tbl_arbols.name', 'tbl_sesion_alinear.sesion_nombre', 'tbl_categorias_alinear.categoria_nombre', 'tbl_atributos_alinear.atributo_nombre', 'tbl_atributos_alinear.id_atrib_alin'])
                                      ->from('tbl_categorias_alinear')
                                      ->join('INNER JOIN', 'tbl_atributos_alinear',
                                          'tbl_categorias_alinear.id_categ_ali = tbl_atributos_alinear.id_categ_ali')
                                      ->join('INNER JOIN', 'tbl_arbols',
                                          'tbl_categorias_alinear.arbol_id = tbl_arbols.id')
                                      ->join('INNER JOIN', 'tbl_sesion_alinear',
                                          'tbl_categorias_alinear.sesion_id = tbl_sesion_alinear.sesion_id')
                                      ->where(['tbl_arbols.id' => $varArbol])
                                      ->andwhere(['tbl_sesion_alinear.sesion_id' => $txtSesion2]);
                          }
                                      
                          $command = $txtQuery2->createCommand();
                          $dataProvider = $command->queryAll();
                  
                  
                  foreach ($dataProvider as $key => $value) {
                      $varNomList = $value['name'];         
                      $varSesionnomList = $value['sesion_nombre'];
                      $varCategonomList = $value['categoria_nombre'];
                      $varAtribunomList = $value['atributo_nombre'];
                      $varIdAtributoList = $value['id_atrib_alin'];
                    // $varMedicionnomList = $value['medicion'];
                    // $varAcuerdoList = $value['acuerdo'];
                      $varMedicionnomList = null;
                      $varAcuerdoList = null;
                    $txtQuery3 =  new Query;
                      $txtQuery3  ->select(['tbl_medir_alinear.medicion', 'tbl_medir_alinear.acuerdo'])
                                      ->from('tbl_categorias_alinear')
                                      ->join('INNER JOIN', 'tbl_atributos_alinear',
                                          'tbl_categorias_alinear.id_categ_ali = tbl_atributos_alinear.id_categ_ali')
                                      ->join('INNER JOIN', 'tbl_arbols',
                                          'tbl_categorias_alinear.arbol_id = tbl_arbols.id')
                                      ->join('INNER JOIN', 'tbl_sesion_alinear',
                                          'tbl_categorias_alinear.sesion_id = tbl_sesion_alinear.sesion_id')
                                      ->join('INNER JOIN', 'tbl_medir_alinear',
                                          'tbl_medir_alinear.id_atrib_alin = tbl_atributos_alinear.id_atrib_alin')
                                      ->where(['tbl_arbols.id' => $varArbol])
                                      ->andwhere(['tbl_medir_alinear.id_idvalorado' => $varTecnico])
                                      ->andwhere(['tbl_medir_alinear.id_atrib_alin' => $varIdAtributoList])
                                      ->andwhere(['tbl_medir_alinear.idbloque1' => $txtIdVoc]);
                          $command1 = $txtQuery3->createCommand();
                          $dataProvider2 = $command1->queryAll();

                          foreach ($dataProvider2 as $key => $value1) {
                            $varMedicionnomList = $value1['medicion'];
                            $varAcuerdoList = $value1['acuerdo'];
                            }

                ?>
                <tr>
                  <td class="text-center"><?php echo $varNomList; ?></td>
                  <td class="text-center"><?php echo $varSesionnomList; ?></td>
                  <td class="text-center"><?php echo $varCategonomList; ?></td>
                  <td class="text-center"><?php echo $varAtribunomList; ?></td>
                  <td class="text-center"><?php echo $varMedicionnomList; ?></td>
                  <td class="text-center"><?php echo $varAcuerdoList; ?></td>               
                </tr>
                <?php
                  }
                ?>
                <tr>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center"></td>
                  <td class="text-center" style="font-size: 15px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #49941e;"></i><b> Indicador de Precisión</b></td>
                  <td class="text-center"><?php echo $txtValorPrecision; ?>%</td>
                  <td class="text-center"></td>               
                </tr>       
              </tbody>    
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<hr>

<div class="BloqueUno" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label><i class="fas fa-tasks" style="font-size: 20px; color: #C148D0;"></i> Plan de acción: </label>
        <div class="row">
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Concepto de mejora: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtConcepto_mejora; ?></label>
          </div>
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Análisis de Causa: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtAnalisis_causa; ?></label>
          </div>
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Acción a Seguir: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtAccion_seguir; ?></label>
          </div>
        </div><hr>
        <div class="row">
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Tipo de acción: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtTipo_accion; ?></label>
          </div>
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Responsable: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtResponsable; ?></label>
          </div>
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Fecha Plan: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtFecha_plan; ?></label>
          </div>
        </div><hr>
        <div class="row">
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Fecha de Implementación: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtFecha_implementa; ?></label>
          </div>
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Estado: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtEstado; ?></label>
          </div>
          <div class="col-md-4">
            <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Observaciones: </label><br>
            <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtObservacion; ?></label>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<hr>
<div class="CuartoBloque" style="display: inline;">
  <div class="row">
    <div class="col-md-4">
      <div class="card1 mb">
        <label><i class="fas fa-cogs" style="font-size: 20px; color: #1e8da7;"></i> Acciones:</label>
          <div class="row">
            <div class="col-md-8">
              <div class="card1 mb">  
              <?= Html::a('Regresar',  ['reportealinearvoc'], ['class' => 'btn btn-success',
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
</div>
<br>
