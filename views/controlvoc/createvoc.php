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

$this->title = 'Instrumento Escucha Focalizada - VOC -';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $valor = null;

    $varIdArbol = $txtPcrcS;
    $varIdValora = $txtValoradorS;
    $varNomArbol = $txtNomPcrc;
    $varNomValora = $txtNomValora;
    $varDimension =  $txtDimensionS;

    $txtEquipo = Yii::$app->db->createCommand("select equipo_id from tbl_equipos_evaluados where evaluado_id = '$varIdValora'")->queryScalar();

	$txtLider = Yii::$app->db->createCommand("select usua_id from tbl_equipos where id = '$txtEquipo'")->queryScalar();
	$txtNameLider = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = '$txtLider'")->queryScalar();

?>
<br>
  <div onclick="generated();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
    Guardar Informacion
  </div> 
  &nbsp;
  <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
    ?>
<br>
<div class="page-header" >
    <h3 style="text-align: center;"><?= Html::encode($this->title) ?></h3>
</div> 
<br>
<div class="formularios-form" style="display: inline" id="idBloques1">
  <link rel="stylesheet" type="text/css" href="web/../../../assets/6418f0aa/css/daterangepicker.css">
  <link rel="stylesheet" type="text/css" href="web/../../../assets/6418f0aa/css/daterangepicker-kv.css">
  <script type="text/javascript" src="web/../../../assets/6418f0aa/js/moment.js"></script>
  <script type="text/javascript" src="web/../../../assets/6418f0aa/js/daterangepicker.js"></script>

  <div class="row seccion-data" class="col-md-12">
    <div class="col-md-10">
      <label class="labelseccion">
        INFORMACION DE PARTIDA
      </label>      
    </div>    
    <div class="col-md-2">
      <?=
        Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                    "class" => "glyphicon glyphicon-chevron-downForm",
                                ]) . "", "javascript:void(0)"
                                , ["class" => "openSeccion", "id" => "bloque1"])
      ?>
    </div>
    <?php $this->registerJs('$("#bloque1").click(function () {
                                $("#dtbloque1").toggle("slow");
                            });'); ?>
  </div>

  <div id="dtbloques" style="display: none">
    <input type="text" class="form-control" id="idPcrc" value="<?php echo $varIdArbol; ?>" class="invisible">  
    <input type="text" class="form-control" id="idValora" value="<?php echo $varIdValora; ?>" class="invisible"> 
    <input type="text" class="form-control" id="idLiders" value="<?php echo $txtNameLider; ?>" class="invisible"> 
  </div>

  <div id="dtbloque1" class="col-sm-12" style="display: none">
      <table class="table table-striped table-bordered detail-view formDinamico">
      <caption>Tabla datos</caption>
        <thead>
        <th></th>
        </thead>
        <tbody>
          <tr>
            <td>
              <label for="txtPcrc">Programa o PCRsssC</label>
                    <input type="text" class="form-control" readonly="readonly" id="txtPcrc" value="<?php echo $varNomArbol; ?>" data-toggle="tooltip" title="Programa o PCRC.">              
            </td> 
            <td>
              <label for="txtValorado">Valorado</label>
              <input type="text" id="txtValorado" name="datetimes" readonly="readonly" value="<?php echo $varNomValora; ?>" class="form-control" data-toggle="tooltip" title="Valorado">
            </td>
          </tr>  
          <tr>
            <td>
              <label for="txtIDExtSp">ID Externo Speech</label>
                    <input type="text" class="form-control" id="txtIDExtSp" onkeypress="return valida(event)" data-toggle="tooltip" title="Id Externo Speech.">            
            </td> 
            <td>
              <label for="txtFechaHora">Fecha y hora</label>
                  <input type="datetime-local" id="txtFechaHora" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha & Hora">
            </td>
          </tr>   
          <tr>
            <td>
              <label for="txtUsuAge">Usuario de Agente</label>
                    <input type="text" class="form-control" id="txtUsuAge" data-toggle="tooltip" title="Usuario de Agente">              
            </td> 
            <td>
              <label for="txtDuracion">Duración</label>                    
                  <input type="text" class="form-control" id="txtDuracion" data-toggle="tooltip" title="Duracion de la llamada">
            </td>
          </tr> 
          <tr>
            <td>
              <label for="txtExtencion">Extensión</label>
                    <input type="text" class="form-control" id="txtExtencion" onkeypress="return valida(event)" data-toggle="tooltip" title="Extensión">               
            </td> 
            <td>
              <label for="txtDimension">Dimensión</label>
                    <input type="text" class="form-control" id="txtDimension" readonly="readonly" value="<?php echo $varDimension; ?>" data-toggle="tooltip" title="Dimensión">  
                    <input type="text" class="form-control" style="display: none" id="txtLider" readonly="readonly" value="<?php echo $txtLider; ?>" data-toggle="tooltip" title="Dimensión">                                                
            </td>
          </tr>       
        </tbody>
      </table> 
  </div>

  <div  class="col-md-12">
    <div class="row seccion">
      <div class="col-md-10">
        <label class="labelseccion">
          ESCUCHA FOCALIZADA
        </label>      
      </div>    
      <div class="col-md-2">
        <?=
          Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                      "class" => "glyphicon glyphicon-chevron-downForm",
                                  ]) . "", "javascript:void(0)"
                                  , ["class" => "openSeccion", "id" => "bloque2"])
        ?>
      </div>
      <?php $this->registerJs('$("#bloque2").click(function () {
                                  $("#dtbloque2").toggle("slow");
                              });'); ?>
    </div>
  </div>
  <div id="dtbloque2" class="col-sm-12" style="display: none">
    <table class="table table-striped table-bordered detail-view formDinamico">
    <caption>Tabla</caption>
      <thead>
        <th></th>
      </thead>
      <tbody>
        <tr>
          <td>
            <label for="txtIndiGlo">Indicadores Globales</label>
          </td>
          <td>
                <select class ='form-control' id="txtIndiGlo" data-toggle="tooltip" title="Indicadores Globales">                
                  <option value="" disabled selected>Elegir Indicador</option>
                  <?php
                    $dataIndi =  new Query;
                    $dataIndi   ->select(['*'])
                                ->from('tbl_controlvoc_listadopadre')
                                ->where(['tbl_controlvoc_listadopadre.idsessionvoc' => '1'])
                                ->andwhere(['tbl_controlvoc_listadopadre.arbol_id' => $varIdArbol]);                    
                    $command = $dataIndi->createCommand();
                    $data = $command->queryAll();

                    foreach ($data as $key => $value) {
                      echo "<option value = '".$value['idlistapadrevoc']."'>".$value['nombrelistap']."</option>";
                    }
                  ?>
                </select>
          </td>
        </tr>
        <tr>
          <td>
            <label for="txtVariable">Variable</label>
          </td>
          <td>
              <select class ='form-control' id="txtVariable" data-toggle="tooltip" title="Variable">
                <option value="" disabled selected>Elegir Variable</option>  
                <?php
                    $dataIndi =  new Query;
                    $dataIndi   ->select(['*'])
                                ->from('tbl_controlvoc_listadopadre')
                                ->where(['tbl_controlvoc_listadopadre.idsessionvoc' => '2'])
                                ->andwhere(['tbl_controlvoc_listadopadre.arbol_id' => $varIdArbol]);                    
                    $command = $dataIndi->createCommand();
                    $data = $command->queryAll();

                    foreach ($data as $key => $value) {
                      echo "<option value = '".$value['idlistapadrevoc']."'>".$value['nombrelistap']."</option>";
                    }
                ?>                
              </select>              
          </td> 
        </tr>
        <tr>
          <td>
            <label for="txtMotivoC">Motivo de contacto o Tipo de Servicio</label>
          </td>
          <td>
            <select class ='form-control' id="txtMotivoC" data-toggle="tooltip" title="Variable" onchange="listah();">
                <option value="" disabled selected>Elegir Motivo/Tipo</option>  
                <?php
                    $dataIndi =  new Query;
                    $dataIndi   ->select(['*'])
                                ->from('tbl_controlvoc_listadopadre')
                                ->where(['tbl_controlvoc_listadopadre.idsessionvoc' => '3'])
                                ->andwhere(['tbl_controlvoc_listadopadre.arbol_id' => $varIdArbol]);                    
                    $command = $dataIndi->createCommand();
                    $data = $command->queryAll();

                    foreach ($data as $key => $value) {
                      echo "<option value = '".$value['idlistapadrevoc']."'>".$value['nombrelistap']."</option>";
                    }
                ?>
            </select>                
          </td>
        </tr>
        <tr>
          <td>            
          </td>
          <td>            
          </td>
        </tr>
        <tr>
          <td>
            <label for="txtMotivoL">Motivos de Llamadas</label>
          </td>
          <td>            
            <select class ='form-control' id="txtMotivoL" data-toggle="tooltip" title="Motivo de Llamadas.">
              <option value="" disabled selected>Elegir Motivo</option>
            </select>   
          </td>
        </tr>
        <tr>
          <td>
            <label for="txtPuntoD">Punto de Dolor</label>
          </td>
          <td>
              <select class ='form-control' id="txtPuntoD" data-toggle="tooltip" title="Punto de Dolor.">
                <option value="" disabled selected>Elegir Punto</option> 
                <?php
                    $dataIndi =  new Query;
                    $dataIndi   ->select(['*'])
                                ->from('tbl_controlvoc_listadopadre')
                                ->where(['tbl_controlvoc_listadopadre.idsessionvoc' => '5'])
                                ->andwhere(['tbl_controlvoc_listadopadre.arbol_id' => $varIdArbol]);                    
                    $command = $dataIndi->createCommand();
                    $data = $command->queryAll();

                    foreach ($data as $key => $value) {
                      echo "<option value = '".$value['idlistapadrevoc']."'>".$value['nombrelistap']."</option>";
                    }
                ?>                                   
              </select>               
          </td>
        </tr>
        <tr>
          <td>
            <label for="txtCategorizada">Esta llamada esta bien categorizada? SI/NO</label>
          </td>
          <td>
              <select class ='form-control' id="txtCategorizada" onchange="categorizar();" data-toggle="tooltip" title="Esta llamada esta bien categorizada? SI/NO">
                <option value="" disabled selected>Elegir Categoria</option> 
                <?php
                    $dataIndi =  new Query;
                    $dataIndi   ->select(['*'])
                                ->from('tbl_controlvoc_listadopadre')
                                ->where(['tbl_controlvoc_listadopadre.idsessionvoc' => '6'])
                                ->andwhere(['tbl_controlvoc_listadopadre.arbol_id' => $varIdArbol]);                    
                    $command = $dataIndi->createCommand();
                    $data = $command->queryAll();

                    foreach ($data as $key => $value) {
                      echo "<option value = '".$value['nombrelistap']."'>".$value['nombrelistap']."</option>";
                    }
                ?>                                   
              </select>               
          </td>
        </tr>
        <tr>
          <td>
            <div id="parteOne" style="display: none">
            <label for="txtAjusteC">Ajuste de categoria / Nuevo</label>
            </div>
          </td>
          <td>
            <div id="parteTwo" style="display: none">
            <input type="text" class="form-control" id="txtAjusteC" data-toggle="tooltip" title="Ajuste de categoria / Nuevo.">
            <div class="panel panel-warning">
              <div class="panel-heading">Importante</div>
              <div class="panel-body">Recordar ingresar o registrar este ajuste de categoria en Speech.</div>
            </div>
            </div>
          </td>        
        </tr> 
        <tr>
          <td>
            <label for="txtPorcentajeAfe">% Indicador afectado de la variable o motivo o el punto de dolor</label>
          </td>
          <td>
            <input type="text" class="form-control" id="txtPorcentajeAfe" data-toggle="tooltip" title="% Indicador afectado de la variable o motivo o el punto de dolor.">
          </td>
        </tr>
        <tr>
          <td>
            <label for="txtAgente">Agente (Detalle de Responsabilidad)</label>
          </td>
          <td>         
              <select class ='form-control' id="txtAgente" data-toggle="tooltip" title="Detalle de responsabilidad - Agente -">
                <option value="" disabled selected>Elegir Agente Detalle</option> 
                <?php
                    $dataIndi =  new Query;
                    $dataIndi   ->select(['*'])
                                ->from('tbl_controlvoc_listadopadre')
                                ->where(['tbl_controlvoc_listadopadre.idsessionvoc' => '7'])
                                ->andwhere(['tbl_controlvoc_listadopadre.arbol_id' => $varIdArbol]);                    
                    $command = $dataIndi->createCommand();
                    $data = $command->queryAll();

                    foreach ($data as $key => $value) {
                      echo "<option value = '".$value['idlistapadrevoc']."'>".$value['nombrelistap']."</option>";
                    }
                ?>                                   
              </select>                
          </td>
        </tr>
        <tr>
          <td>
            <label for="txtMarca">Marca (Detalle de Responsabilidad)</label>
          </td>
          <td>  
              <select class ='form-control' id="txtMarca" data-toggle="tooltip" title="Detalle de responsabilidad - Marca -">
                <option value="" disabled selected>Elegir Marca Detalle</option> 
                <?php
                    $dataIndi =  new Query;
                    $dataIndi   ->select(['*'])
                                ->from('tbl_controlvoc_listadopadre')
                                ->where(['tbl_controlvoc_listadopadre.idsessionvoc' => '8'])
                                ->andwhere(['tbl_controlvoc_listadopadre.arbol_id' => $varIdArbol]);                    
                    $command = $dataIndi->createCommand();
                    $data = $command->queryAll();

                    foreach ($data as $key => $value) {
                      echo "<option value = '".$value['idlistapadrevoc']."'>".$value['nombrelistap']."</option>";
                    }
                ?>                                   
              </select>                         
          </td>
        </tr>
        <tr>
          <td>            
          </td>
          <td>            
          </td>
        </tr>
        <tr>
          <td>
            <label for="txtCanal">Canal (Detalle de Responsabilidad)</label>
          </td>
          <td> 
              <select class ='form-control' id="txtCanal" data-toggle="tooltip" title="Detalle de responsabilidad - Canal -">
                <option value="" disabled selected>Elegir Canal Detalle</option> 
                <?php
                    $dataIndi =  new Query;
                    $dataIndi   ->select(['*'])
                                ->from('tbl_controlvoc_listadopadre')
                                ->where(['tbl_controlvoc_listadopadre.idsessionvoc' => '9'])
                                ->andwhere(['tbl_controlvoc_listadopadre.arbol_id' => $varIdArbol]);                    
                    $command = $dataIndi->createCommand();
                    $data = $command->queryAll();

                    foreach ($data as $key => $value) {
                      echo "<option value = '".$value['idlistapadrevoc']."'>".$value['nombrelistap']."</option>";
                    }
                ?>                                   
              </select>                             
          </td>
        </tr>
<?php 
        if ($varIdArbol=='2931' || $varIdArbol == '2985') {
       ?>         
        <tr>
          <td>
            <label for="txtResponsabilidadU">Responsabilidad del usuario final</label>
          </td>
          <td>
              <select class ='form-control' id="txtatributos" data-toggle="tooltip" title="Responsabilidad del usuario final">
                <option value="" disabled selected>Elegir Responsabilidad</option> 
                <?php
                    $dataIndi =  new Query;
                    $dataIndi   ->select(['*'])
                                ->from('tbl_controlvoc_listadopadre')
                                ->where(['tbl_controlvoc_listadopadre.idsessionvoc' => '14'])
                                ->andwhere(['tbl_controlvoc_listadopadre.arbol_id' => $varIdArbol]);                    
                    $command = $dataIndi->createCommand();
                    $data = $command->queryAll();

                    foreach ($data as $key => $value) {
                      echo "<option value = '".$value['idlistapadrevoc']."'>".$value['nombrelistap']."</option>";
                    }
                ?>                                   
              </select>              
          </td>
        </tr>

        <?php }
    ?> 
        <tr>
          <td>
            <label for="txtDcualitativo">Detalle cualitativo (Detalle de Responsabilidad)</label>
          </td>
          <td>
            <input type="text" class="form-control" id="txtDcualitativo" data-toggle="tooltip" title="Detalle de responsabilidad - Detalle cualitativo -">
          </td>
        </tr>
        <tr>
          <td>
            <label for="txtMapa1">Mapa de Interesados</label>
          </td>
          <td>   
              <select class ='form-control' id="txtMapa1" data-toggle="tooltip" title="Mapa de Interesados">
                <option value="" disabled selected>Elegir Mapa</option> 
                <?php
                    $dataIndi =  new Query;
                    $dataIndi   ->select(['*'])
                                ->from('tbl_controlvoc_listadopadre')
                                ->where(['tbl_controlvoc_listadopadre.idsessionvoc' => '10'])
                                ->andwhere(['tbl_controlvoc_listadopadre.arbol_id' => $varIdArbol]);                    
                    $command = $dataIndi->createCommand();
                    $data = $command->queryAll();

                    foreach ($data as $key => $value) {
                      echo "<option value = '".$value['idlistapadrevoc']."'>".$value['nombrelistap']."</option>";
                    }
                ?>                                   
              </select>                         
          </td>
        </tr>
        <tr>
          <td>
            <label for="txtMapa2">Mapa de Interesados</label>
          </td>
          <td>
              <select class ='form-control' id="txtMapa2" data-toggle="tooltip" title="Mapa de Interesados">
                <option value="" disabled selected>Elegir Mapa</option> 
                <?php
                    $dataIndi =  new Query;
                    $dataIndi   ->select(['*'])
                                ->from('tbl_controlvoc_listadopadre')
                                ->where(['tbl_controlvoc_listadopadre.idsessionvoc' => '11'])
                                ->andwhere(['tbl_controlvoc_listadopadre.arbol_id' => $varIdArbol]);                    
                    $command = $dataIndi->createCommand();
                    $data = $command->queryAll();

                    foreach ($data as $key => $value) {
                      echo "<option value = '".$value['idlistapadrevoc']."'>".$value['nombrelistap']."</option>";
                    }
                ?>                                   
              </select>              
          </td>
        </tr>
        <tr>
          <td>
            <label for="txtatributos">Atributos de Calidad</label>
          </td>
          <td>
              <select class ='form-control' id="txtatributos" data-toggle="tooltip" title="Atributos de Calidad">
                <option value="" disabled selected>Elegir Atributos</option> 
                <?php
                    $dataIndi =  new Query;
                    $dataIndi   ->select(['*'])
                                ->from('tbl_controlvoc_listadopadre')
                                ->where(['tbl_controlvoc_listadopadre.idsessionvoc' => '12'])
                                ->andwhere(['tbl_controlvoc_listadopadre.arbol_id' => $varIdArbol]);                    
                    $command = $dataIndi->createCommand();
                    $data = $command->queryAll();

                    foreach ($data as $key => $value) {
                      echo "<option value = '".$value['idlistapadrevoc']."'>".$value['nombrelistap']."</option>";
                    }
                ?>                                   
              </select>              
          </td>
        </tr>
	
      </tbody>
    </table>
  </div>

  <div  class="col-md-12">
    <div class="row seccion">
      <div class="col-md-10">
        <label class="labelseccion">
          CALIDAD
        </label>      
      </div>    
      <div class="col-md-2">
        <?=
          Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                      "class" => "glyphicon glyphicon-chevron-downForm",
                                  ]) . "", "javascript:void(0)"
                                  , ["class" => "openSeccion", "id" => "bloque3"])
        ?>
      </div>
      <?php $this->registerJs('$("#bloque3").click(function () {
                                  $("#dtbloque3").toggle("slow");
                              });'); ?>
    </div>
  </div>
  <div id="dtbloque3" class="col-sm-12" style="display: none">  
    <table class="table table-striped table-bordered detail-view formDinamico">
    <caption>Tabla datos</caption>
      <thead>
        <tr>
          <th id="atributosCalidad" style="text-align: center;">
            <label for="txtAtributos">Atributos de Calidad</label>
          </th>
          <th id="FeedbackAfectacion" style="text-align: center;">
            <label for="txtFeedback">Feedback en caso de una afectación de calidad</label>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <label for="txtInfoOk">Informacion correcto y completo</label>
          </td>
          <td>
            <?= Html::button('Crear Feedback', ['value' => url::to('indexfeedback'), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                              'data-toggle' => 'tooltip',
                              'title' => 'Crear Dimensionamiento', 'style' => 'background-color: #4298b4']) 
            ?> 

            <?php
              Modal::begin([
                'header' => '<h4>Crear Feedback</h4>',
                'id' => 'modal1',
                //'size' => 'modal-lg',
              ]);

              echo "<div id='modalContent1'></div>";
                                    
              Modal::end(); 
            ?>      
          </td>
        </tr>
        <tr>
          <td>
            <label for="txtProceOk">Procedimientos correctos y completos</label>
          </td>
          <td>
            <?= Html::button('Crear Feedback', ['value' => url::to('indexfeedback'), 'class' => 'btn btn-success', 'id'=>'modalButton2',
                              'data-toggle' => 'tooltip',
                              'title' => 'Crear Dimensionamiento', 'style' => 'background-color: #4298b4']) 
            ?> 

            <?php
              Modal::begin([
                'header' => '<h4>Crear Feedback</h4>',
                'id' => 'modal2',
                //'size' => 'modal-lg',
              ]);

              echo "<div id='modalContent2'></div>";
                                    
              Modal::end(); 
            ?>                         
          </td>
        </tr>
        <tr>
          <td>
            <label for="txtEscucha">Escucha</label>
          </td>
          <td>
            <?= Html::button('Crear Feedback', ['value' => url::to('indexfeedback'), 'class' => 'btn btn-success', 'id'=>'modalButton3',
                              'data-toggle' => 'tooltip',
                              'title' => 'Crear Dimensionamiento', 'style' => 'background-color: #4298b4']) 
            ?> 

            <?php
              Modal::begin([
                'header' => '<h4>Crear Feedback</h4>',
                'id' => 'modal3',
                //'size' => 'modal-lg',
              ]);

              echo "<div id='modalContent3'></div>";
                                    
              Modal::end(); 
            ?>              
          </td>
        </tr>
        <tr>
          <td>
            <label for="txtTono">Tono de voz</label>
          </td>
          <td>
            <?= Html::button('Crear Feedback', ['value' => url::to('indexfeedback'), 'class' => 'btn btn-success', 'id'=>'modalButton7',
                              'data-toggle' => 'tooltip',
                              'title' => 'Crear Dimensionamiento', 'style' => 'background-color: #4298b4']) 
            ?> 

            <?php
              Modal::begin([
                'header' => '<h4>Crear Feedback</h4>',
                'id' => 'modal7',
                //'size' => 'modal-lg',
              ]);

              echo "<div id='modalContent7'></div>";
                                    
              Modal::end(); 
            ?>              
          </td>
        </tr>
        <tr>
          <td>
            <label for="txtNecesidad">Entiende la necesidad del usuario</label>
          </td>
          <td>
            <?= Html::button('Crear Feedback', ['value' => url::to('indexfeedback'), 'class' => 'btn btn-success', 'id'=>'modalButton5',
                              'data-toggle' => 'tooltip',
                              'title' => 'Crear Dimensionamiento', 'style' => 'background-color: #4298b4']) 
            ?> 

            <?php
              Modal::begin([
                'header' => '<h4>Crear Feedback</h4>',
                'id' => 'modal5',
                //'size' => 'modal-lg',
              ]);

              echo "<div id='modalContent5'></div>";
                                    
              Modal::end(); 
            ?>              
          </td>
        </tr>
      </tbody>
    </table>
  </div>


  <div  class="col-md-12">
    <div class="row seccion">
      <div class="col-md-10">
        <label class="labelseccion">
          ALERTA
        </label>      
      </div>    
      <div class="col-md-2">
        <?=
          Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                      "class" => "glyphicon glyphicon-chevron-downForm",
                                  ]) . "", "javascript:void(0)"
                                  , ["class" => "openSeccion", "id" => "bloque4"])
        ?>
      </div>
      <?php $this->registerJs('$("#bloque4").click(function () {
                                  $("#dtbloque4").toggle("slow");
                              });'); ?>
    </div>
  </div>
  <div id="dtbloque4" class="col-sm-12" style="display: none">
    <div onclick="enviar();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
      Crear Alerta 
    </div> 
    <br> 
  </div>
<br>
<br>
</div>

<script type="text/javascript">
  function valida(e){
        tecla = (document.all) ? e.keyCode : e.which;
        //Tecla de retroceso para borrar, siempre la permite
        if (tecla==8){
            return true;
        }            
        // Patron de entrada, en este caso solo acepta numeros
        patron =/[0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
  };


  $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
  });

  var varBloque0 = document.getElementById("idBloques0");
  var varBloque1 = document.getElementById("idBloques1");

  function categorizar(){
    var varRta = document.getElementById("txtCategorizada").value;
    var varPartO = document.getElementById("parteOne");
    var varPartT = document.getElementById("parteTwo");

    if (varRta == "No") {
      varPartT.style.display = 'inline';
      varPartO.style.display = 'inline';
    }else{
      varPartT.style.display = 'none';
      varPartO.style.display = 'none';
    }    
  };

  function listah(){
    var varMotivo = document.getElementById("txtMotivoC").value;
   
      $.ajax({
              method: "post",
              url: "listashijo",
              data : {
                txtvmotivo : varMotivo,
              },
              success : function(response){ 
                          var Rta =   JSON.parse(response);    
                          console.log(Rta);
                          document.getElementById("txtMotivoL").innerHTML = "";
                          for (var i = 0; i < Rta.length; i++) {
                              var node = document.createElement("OPTION");
                              node.setAttribute("value", Rta[i].idlistahijovoc);
                              var textnode = document.createTextNode(Rta[i].nombrelistah);
                              node.appendChild(textnode);
                              document.getElementById("txtMotivoL").appendChild(node);
                          }
                      }
      });    
  };

  function enviar(){
    window.open('../basesatisfaccion/alertas','_blank');
  };

  function generated(){
    var varArbol = "<?php echo $varIdArbol; ?>";
    var varValoraddo = "<?php echo $varIdValora; ?>";
    var varSpeech = document.getElementById("txtIDExtSp").value;
    var varFH = document.getElementById("txtFechaHora").value;
    var varuSUAgente = document.getElementById("txtUsuAge").value;
    var varDuracion = document.getElementById("txtDuracion").value;
    var varExt = document.getElementById("txtExtencion").value;
    var varDimension = document.getElementById("txtDimension").value;
    var varLider = document.getElementById("txtLider").value;

    var varIndicadorG = document.getElementById("txtIndiGlo").value;
    var varVariable = document.getElementById("txtVariable").value;
    var varMotivoC = document.getElementById("txtMotivoC").value;
    var varMotivoL = document.getElementById("txtMotivoL").value;
    var varPuntoD = document.getElementById("txtPuntoD").value;
    var varCategoria = document.getElementById("txtCategorizada").value;
    var varAjustesC = document.getElementById("txtAjusteC").value;
    var varIndicador = document.getElementById("txtPorcentajeAfe").value;
    var varAgente = document.getElementById("txtAgente").value;
    var varMarca = document.getElementById("txtMarca").value;
    var varCanal = document.getElementById("txtCanal").value;
    var varDetalle = document.getElementById("txtDcualitativo").value;
    var varMapa1 = document.getElementById("txtMapa1").value;
    var varMapa2 = document.getElementById("txtMapa2").value;
    var varInteresados = document.getElementById("txtatributos").value;

    if (varIndicadorG == ""){
        varIndicadorG = 'N/A';
       }
    if (varVariable == ""){
        varVariable = 'N/A';
       }
    if (varMotivoC == ""){
        varMotivoC = 'N/A';
       }
    if (varMotivoL == ""){
        varMotivoL = 'N/A';
       }
    if (varPuntoD == ""){
        varPuntoD = 'N/A';
       }
    if (varCategoria == ""){
        varCategoria = 'N/A';
       }
    if (varAjustesC == ""){
        varAjustesC = 'N/A';
       }
    if (varIndicador == ""){
        varIndicador = 'N/A';
       }
    if (varAgente == ""){
        varAgente = 'N/A';
       }
    if (varMarca == ""){
        varMarca = 'N/A';
       }
    if (varAgente == ""){
        varAgente = 'N/A';
       }
    if (varCanal == ""){
        varCanal = 'N/A';
       }
    if (varDetalle == ""){
        varDetalle = 'N/A';
       }
    if (varMapa1 == ""){
        varMapa1 = 'N/A';
       }
    if (varMapa2 == ""){
        varMapa2 = 'N/A';
       }

   if (varArbol =='2931' || varArbol  == '2985') {
      if (varSpeech == "" || varFH == "" || varuSUAgente == "" || varDuracion == "" || varExt == "" || varDimension == "" || varInteresados == "") {
         event.preventDefault();
         swal.fire("!!! Advertencia !!!","Verifique que todos los datos esten diligenciados.","warning");        
         return;
         var control = 0;
      };

     }
      else {
      if (varSpeech == "" || varFH == "" || varuSUAgente == "" || varDuracion == "" || varExt == "" || varDimension == "") {
         event.preventDefault();
         swal.fire("!!! Advertencia !!!","Verifique que todos los datos esten diligenciados.","warning");
         return;
         var control = 0;
       };
    };

   if (control = 1) {
      $.ajax({
              method: "post",
              url: "createfocalizada",
              data : {
                txtvArbol : varArbol,
                txtvValorado : varValoraddo,
                txtvSpeech : varSpeech,
                txtvFH : varFH,
                txtvAgenteu : varuSUAgente,
                txtvDuracion : varDuracion,
                txtvExt : varExt,
                txtvDimension : varDimension,
                txtvLider : varLider,

                txtvIndicadorG    : varIndicadorG,
                txtvVariable      : varVariable,                
                txtvMotivoC       : varMotivoC,
                txtvMotivoL       : varMotivoL,
                txtvPuntoD        : varPuntoD,
                txtvCategoria     : varCategoria,
                txtvAjusteC       : varAjustesC,
                txtvIndicador     : varIndicador,
                txtvAgente        : varAgente,
                txtvMarca         : varMarca,
                txtvCanal         : varCanal,
                txtvDetalle       : varDetalle,
                txtvMapa1         : varMapa1,
                txtvMapa2         : varMapa2,
		txtvInteresados   : varInteresados,
              },
              success : function(response){ 
                          var numRta =   JSON.parse(response);    
                          console.log(numRta);

                          if (numRta != 0) {
                            jQuery(function(){
                                swal.fire({type: "success",
                                    title: "!!! OK !!!",
                                    text: "Datos guardados correctamente."
                                }).then(function() {
				    window.location.href = '../controlvoc/index';
                                });
                            });
                          }else{
                            event.preventDefault();
                              swal.fire("!!! Advertencia !!!","No se pueden guardar los datos.","warning");
                            return;
                          }
                      }
      });
    }
  };
</script>
