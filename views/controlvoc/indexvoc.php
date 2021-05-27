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
    $txtIdPcrc = $varArbol;
    $txtNamePcrc = Yii::$app->db->createCommand("select name from tbl_arbols where id = '$txtIdPcrc'")->queryScalar();

?>
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
<style>
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
<br>
<!-- <div class="page-header" >
    <h3><center><?= Html::encode($this->title) ?></center></h3>
</div> --!> 
<br>
<div class="formularios-form" style="display: inline" id="idBloques0">

  <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="row">
      <div class="col-md-offset-2 col-sm-8">
        <?= $form->field($model, 'arbol_id')->textInput(['maxlength' => 10, 'id'=>"pcrcid", 'class'=>'hidden', 'value'=> $txtIdPcrc ]) ?>        
      </div>
    </div>
    <div class="row">
      <div class="col-md-offset-2 col-sm-8">
        <center><h4><?php echo $txtNamePcrc; ?></h4></center>
        <br>
      </div>
    </div>
    <div class="row">
      <div class="col-md-offset-2 col-sm-8">
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
    </div>
    <div class="row">
      <div class="col-md-offset-2 col-sm-8">
        <?= $form->field($model, 'dimensions')->textInput(['maxlength' => 200, 'id'=>"dimensionsid", 'readonly'=>'readonly', 'value'=>'Escucha Focalizada VOC', 'label'=>""])->label('Dimension') ?>
      </div>
    </div>
    <br>
    <div class="row" align="center">      
      <?= Html::submitButton(Yii::t('app', 'Valorar'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'id' => 'botonvalorar', 
                        'style' => 'display:none',
                        'title' => 'Varificar']) 
                ?>  
      &nbsp;
      <?= Html::a('Nuevo Programa',  ['index'], ['class' => 'btn btn-success',
                          'style' => 'background-color: #707372',
                          'data-toggle' => 'tooltip',
                          'title' => 'Agregar Valorado']) 
      ?>
    </div>

  <?php ActiveForm::end(); ?>
</div>
<br>
<br>
<?php
if ($sessiones == "2953" || $sessiones == "7" || $sessiones == "1525" || $sessiones == "438" || $sessiones == "3595" || $sessiones == "312" || $sessiones == "3205" || $sessiones == "1023" || $sessiones == "64" || $sessiones == "3042" || $sessiones == "1659" || $sessiones == "20" || $sessiones == "2215" || $sessiones == "3260") {
?>
    <div class="panel panel-primary">
      <div class="panel-heading">Ingresar listado</div>
      <div class="panel-body">
      <?php if ($sessiones == "2953" || $sessiones == "7" || $sessiones == "3205") {?>
        <?= Html::button('Agregar Sesiones', ['value' => url::to('sessionesvoc'), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                              'data-toggle' => 'tooltip',
                              'title' => 'Agregar', 'style' => 'background-color: #337ab7']) 
        ?> 

        <?php

          Modal::begin([
                'header' => '<h4>Crear sesiones - VOC - </h4>',
                'id' => 'modal1',
                //'size' => 'modal-lg',
              ]);

          echo "<div id='modalContent1'></div>";
                                    
          Modal::end(); 
        ?>   
      <?php } ?>

        <?= Html::button('Agregar Listados', ['value' => url::to('listadosvoc'), 'class' => 'btn btn-success', 'id'=>'modalButton2',
                              'data-toggle' => 'tooltip',
                              'title' => 'Agregar', 'style' => 'background-color: #337ab7']) 
        ?> 

        <?php
          Modal::begin([
                'header' => '<h4>Crear Listados - VOC - </h4>',
                'id' => 'modal2',
                //'size' => 'modal-lg',
              ]);

          echo "<div id='modalContent2'></div>";
                                    
          Modal::end(); 
        ?>   

        <?= Html::button('Adicionar Motivos', ['value' => url::to(['motivovoc', 'txtArbol' => $txtIdPcrc]), 'class' => 'btn btn-success', 'id'=>'modalButton3',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Adicionar Motivos', 'style' => 'background-color: #337ab7']) 
        ?> 

        <?php
                    Modal::begin([
                      'header' => '<h4>Agregar Motivos</h4>',
                      'id' => 'modal3',
                      //'size' => 'modal-lg',
                    ]);

                    echo "<div id='modalContent3'></div>";
                                          
                    Modal::end(); 
        ?>                

        <?= Html::a('Modificar Listado',  ['updatevoc','txtPcrc' => $txtIdPcrc], ['class' => 'btn btn-danger',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Modificar Listado']) 
        ?> 

      </div>
    </div>

    <div class="panel panel-info">
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
                   $dataListaP = Yii::$app->db->createCommand("select * from tbl_controlvoc_listadopadre where idsessionvoc = '$varIdSesiones' and arbol_id = '$txtIdPcrc'")->queryAll();

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
    </div>
<?php 
} 
?>

