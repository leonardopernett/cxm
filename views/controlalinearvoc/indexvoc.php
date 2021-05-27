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

$this->title = 'Instrumento Alinear + VOC';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $valor = null;
    $txtIdPcrc = $varArbol;
    $txtNamePcrc = Yii::$app->db->createCommand("select name from tbl_arbols where id = '$txtIdPcrc'")->queryScalar();

    $dataIndi =  new Query;
                    $dataIndi   ->select(['*'])
                                ->from('tbl_sesion_alinear');                    
                    $command = $dataIndi->createCommand();
                    $data = $command->queryAll();
    $listData = ArrayHelper::map($data, 'sesion_id', 'sesion_nombre');

?>
<style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Nunito');

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
        background-image: url('../../images/Inst.-Escucha-Focalizada.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<script>
 function arbol_id2(){
        var selectValorado = document.getElementById("tecnicosid").value;
       
        if (selectValorado != "") 
        {
           botonvalorar.style.display = 'inline';
        }
        else
        {
           botonvalorar.style.display = 'none';            
        }            
    };

</script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
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
<div class="formularios-form" style="display: inline" id="idBloques0">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label><i class="fas fa-book" style="font-size: 20px; color: #827DF9;"></i> <?php echo $txtNamePcrc; ?></label>
        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
          <?= $form->field($model, 'arbol_id')->textInput(['maxlength' => 10, 'id'=>"pcrcid", 'class'=>'hidden', 'value'=> $txtIdPcrc ]) ?> 
          <div class="row">
            <div class="col-mod-6">
              <?=
                $form->field($model, 'tecnico_id')->label(Yii::t('app','Valorado'))
                      ->widget(Select2::classname(), [
                          //'data' => array_merge(["" => ""], $data),
                          'language' => 'es',
                          'options' => ['onclick'=>'arbol_id2();', 'id'=>"tecnicosid",'placeholder' => Yii::t('app', 'Select ...')],
                          'pluginOptions' => [
                              'allowClear' => true,
                              'minimumInputLength' => 4,
                              'ajax' => [
                                  'url' => \yii\helpers\Url::to(['evaluadolistmultiple']),
                                  'dataType' => 'json',
                                  'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                  'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                              ],
                              'initSelection' => new JsExpression('function (element, callback) {
                                  var id=$(element).val();
                                  if (id !== "") {
                                      $.ajax("' . Url::to(['evaluadolistmultiple']) . '?id=" + id, {
                                          dataType: "json",
                                          type: "post"
                                      }).done(function(data) { callback(data.results);});
                                  }
                              }')
                          ]
                              ]
                );
              ?>
            </div>
            <div class="col-mod-6">
                <?= $form->field($model, 'extencion')->dropDownList($listData, ['prompt' => 'Seleccione...', 'id'=>'TipoSecion', ])->label('Sesión') ?> 
            </div>
            <div class="col-mod-6">
                <?= $form->field($model, 'dimensions')->textInput(['maxlength' => 200, 'id'=>"dimensionsid", 'readonly'=>'readonly', 'value'=>'Alinear + VOC', 'label'=>""])->label('Dimensión') ?>
            </div>
          </div>
    
          <div class="row" align="center">      
              <?= Html::submitButton(Yii::t('app', 'Valorar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'id' => 'botonvalorar', 
                                'style' => 'display:none', 
                                'title' => 'Verificar']) 
                        ?>  

              &nbsp;
              <?= Html::a('Nueva Sesión',  ['index'], ['class' => 'btn btn-success',
                                  'style' => 'background-color: #707372',
                                  'data-toggle' => 'tooltip',
                                  'title' => 'Nueva Sesion']) 
              ?>
          </div>

        <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>
</div>
<br>
<br>
<?php
if ($sessiones == "2953" || $sessiones == "7" || $sessiones == "1525" || $sessiones == "438" || $sessiones == "3595" || $sessiones == "312" || $sessiones == "3205" || $sessiones == "1023" || $sessiones == "64" || $sessiones == "3042" || $sessiones == "1659" || $sessiones == "20" || $sessiones == "2215" || $sessiones == "3260" || $sessiones == "2915") {
?>
<div class="formularios-form">
    <div class="row">
      <div class="col-md-12">
        <div class="card1 mb">
          <label><i class="fas fa-pen-square" style="font-size: 20px; color: #827DF9;"></i> Registrar items:</label>
            <div class="row">
              <div class="col-mod-4">
   
                    <?php if ($sessiones == "2953" || $sessiones == "7" || $sessiones == "3205" || $sessiones == "2915") {?>
                      <?= Html::button('Agregar Sesiones', ['value' => url::to('sessionesalinear'), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Agregar', 'style' => 'background-color: #337ab7']) 
                      ?> 

                      <?php

                        Modal::begin([
                              'header' => '<h4>Creacion de Sesiones Alinear + VOC</h4>',
                              'id' => 'modal1',
                              //'size' => 'modal-lg',
                            ]);

                        echo "<div id='modalContent1'></div>";
                                                  
                        Modal::end(); 
                      ?>   
                    <?php } ?>

                      <?= Html::button('Agregar Participantes', ['value' => url::to('participantealinear'), 'class' => 'btn btn-success', 'id'=>'modalButton2',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Agregar', 'style' => 'background-color: #337ab7']) 
                      ?> 

                      <?php
                        Modal::begin([
                              'header' => '<h4>Creacion de Participantes Alinear + VOC </h4>',
                              'id' => 'modal2',
                              //'size' => 'modal-lg',
                            ]);

                        echo "<div id='modalContent2'></div>";
                                                  
                        Modal::end(); 
                      ?> 

                <?= Html::button('Agregar Categorias', ['value' => url::to('categoriaalinear'), 'class' => 'btn btn-success', 'id'=>'modalButton3',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Agregar', 'style' => 'background-color: #337ab7']) 
                      ?> 

                      <?php
                        Modal::begin([
                              'header' => '<h4>Creacion de Categorias Alinear + VOC </h4>',
                              'id' => 'modal3',
                              //'size' => 'modal-lg',
                            ]);

                        echo "<div id='modalContent3'></div>";
                                                  
                        Modal::end(); 
                  ?>
                  <?= Html::button('Agregar Atributos', ['value' => url::to(['atributoalinear','idAbol'=>$txtIdPcrc]), 'class' => 'btn btn-success', 'id'=>'modalButton5',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Agregar', 'style' => 'background-color: #337ab7']) 
                      ?> 

                      <?php
                        Modal::begin([
                              'header' => '<h4>Creacion de Atributos Alinear + VOC </h4>',
                              'id' => 'modal5',
                              //'size' => 'modal-lg',
                            ]);

                        echo "<div id='modalContent5'></div>";
                                                  
                        Modal::end(); 
                  ?>                
                    
		  <?= Html::a('Modificar Listado',  ['updatevoc','txtPcrc' => $txtIdPcrc], ['class' => 'btn btn-danger',
                                          'data-toggle' => 'tooltip',
                                          'title' => 'Modificar Listado']) 
                  ?>
                    </div>
                  </div>

                  <!--<div class="panel panel-info">
                    <div class="panel-heading">Ver listado</div>
                      <div class="panel-body">
                        <div id="capaUno" style="display: inline">
                          <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                            <thead>
                            </thead>
                            <tbody>
                            
                            <?php
                              $dataSesiones = Yii::$app->db->createCommand("select * from tbl_controlvoc_sessionlista")->queryAll();

                              foreach ($dataSesiones as $key => $value) {
                                $varIdSesiones = $value['idsessionvoc'];
                                $varTitulos = $value['nombresession'];
                            ?>
                            <tr>
                              <th class="text-center"><?php echo $varTitulos; ?></th>
                              <?php
                                $dataListaP = Yii::$app->db->createCommand("select * from tbl_controlvoc_listadopadre where idsessionvoc = '$varIdSesiones'")->queryAll();

                                foreach ($dataListaP as $key => $value) {
                                  $varNombre = $value['nombrelistap'];
                              ?>
                                <td class="text-center"><?php echo $varNombre; ?></td>
                              <?php 
                                }
                              ?>
                            </tr>
                            <?php
                              }
                            ?>  

                            </tbody>
                          </table>  
                        </div>                 
                      </div>
                  </div>-->
                  </div>
            </div>
        </div>
      </div>
    </div>
  </div>
<br>
<?php 
} 
?>