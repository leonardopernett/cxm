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

$this->title = 'Crear Alertas';//nombre del titulo de mi modulo
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
        background-image: url('../../images/Crear-Alerta.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
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
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>

<div class="capaInfo" id="idCapaInfo" style="display: inline;">



    <div class="row"><!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Información"; ?> </label><!--titulo principal de mi modulo-->
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">

                <label><em class="fas fa-minus-circle" style="font-size: 20px; color: #EE3109;"></em> <?= Yii::t('app',  '  Alerta  ') ?></label> <!-- label  del titulo de lo que vamos a mostrar ------>

                <br>

                <label> <?= Yii::t('app',' En el campo de destinatarios se ingresan los correos electronicos de los interesados en la alerta. ') ?></label> <!-- label  del titulo de lo que vamos a mostrar ------>

                <br>
                
                <label style="color:#EE3109" > <?= Yii::t('app',  ' Cada correo debe estar separado por una coma ( , )') ?></label> <!-- label  del titulo de lo que vamos a mostrar ------>
            </div>
        </div>    
    </div>
    <br>
    <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
                'fieldConfig' => [
                    'inputOptions' => ['autocomplete' => 'off']
                  ]
    ]) ?> 
    <div class="row"><!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Creación de Alertas"; ?> </label><!--titulo principal de mi modulo-->
            </div>
        </div>
    </div>

    <br>
<br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">

                <div class="row">  
                    <br>
                    <div class="col-md-4">
                        <label><em class="fas fa-check" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Seleccionar Tipo de Alerta:') ?></label> 
                        <div class="col-12">
                                <select class="form-control" name="tipo_alerta" id="sel1" placeholder="sad" required>
                                    <option value="" disabled selected>Seleccione ...</option>
                                    <option value="Felicitación">Felicitación</option>
                                    <option value="Para la mejora">Para la mejora</option>
                                    <option value="Seguimiento">Seguimiento</option>
                                    <option value="Seguimiento Heroes por el Cliente FNA">Seguimiento  Heroes por el Cliente FNA</option>
                                    <option value="Seguimiento Heroes por el Cliente Banco Pichincha">Seguimiento  Heroes por el Cliente Banco Pichincha</option>
                                </select>
                        </div>
                        
                    </div>
                    
                    <div class="col-md-4">           
                        <label><em class="fas fa-check" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Seleccionar Programa/PCRC:') ?></label> 
                        <?=
                            $form->field($searchModel, 'pcrc', ['template' => $template])
                            ->widget(Select2::classname(), [
                                'language' => 'es',
                                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                                'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'ajax' => [
                                'url' => \yii\helpers\Url::to(['basesatisfaccion/getarbolesbypcrc']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                ],
                                'initSelection' => new JsExpression('function (element, callback) {
                                    var id=$(element).val();
                                    if (id !== "") {
                                        $.ajax("' . Url::to(['basesatisfaccion/getarbolesbypcrc']) . '?id=" + id, {
                                            dataType: "json",
                                            type: "post"
                                        }).done(function(data) { callback(data.results[0]);});
                                    }
                                }')
                                ]
                                ]
                                );
                        ?>                     
                    </div>                
                    
                    <div class="col-md-4">
                        <label><em class="fas fa-check" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Destinarios:') ?></label> 
                        <?= $form->field($modelup, 'remitentes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'destino', 'name'=>'remitentes', 'placeholder'=>'Destinatario ...'])->label('') ?>
                    </div>  

                </div>

                <br> 

                <div class="row">  

                    <div class="col-md-4">
                        <label><em class="fas fa-check" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Asunto:') ?></label> 
                        <?= $form->field($modelup, 'asunto', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 250, 'id' => 'asunto', 'name'=>'asunto', 'placeholder'=>'Asunto ...'])->label('') ?>
                    </div>
                    
                    <div class="col-md-4">           
                        <label><em class="fas fa-check" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Comentarios:') ?></label> 
                        <?= $form->field($modelup, 'comentario', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 1000, 'id' => 'comentario', 'name'=>'comentario', 'placeholder'=>'Comentarios ...'])->label('') ?>
                    </div>                
                    <br> 
                    <div class="col-md-4">
                        <div style="position:relative;">
                            <div class="field-uploadform-archivo_adjunto">
                                <input type="hidden" name="UploadForm[archivo_adjunto][]" value=""><input type="file" id="uploadform-archivo_adjunto" name="UploadForm[archivo_adjunto][]" accept="image/*, application/pdf" required>

                                <p class="help-block help-block-error"></p>
                            </div>
                        </div>
                    </div>  

                    
                <br><br>
                </div> 

                       
            </div>
        </div>


    
    </div>

    <br>
    <hr>
    <div class="capaBtn" style="display: inline;">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #ffc034;"></em><?= Yii::t('app', ' Enviar información...') ?></label>
                            <input type="submit" class="btn btn-primary" value="Enviar" onclick="varVerificar();">
                         </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 20px; color: #ffc034;"></em><?= Yii::t('app', ' Crear Correo Grupal...') ?></label>
                        <?= Html::button('Correo Grupal', ['value' => url::to(['correogrupal']), 'class' => 'btn btn-success', 'id'=>'modalButton1', 
                                        'data-toggle' => 'tooltip',
                                         'title' => 'Correo Grupal', 'style' => 'background-color: #4298b4']) ?> 
                                <?php
                                    Modal::begin([
                                        'header' => '<h4>Correo Grupal</h4>',
                                        'id' => 'modal1',
                                    ]);

                                    echo "<div id='modalContent1'></div>";
                                                                    
                                    Modal::end(); 
                                ?>
                                 
                        </div>
                                    
                    </div>

                    <div class="col-md-4">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #ffc034;"></em><?= Yii::t('app', ' Limpiar Proceso...') ?></label>
                            
                            <?= Html::a('Limpiar',  ['basesatisfaccion/alertas'], ['class' => 'btn btn-success',
                                                        'style' => 'background-color: #707372',
                                                        'data-toggle' => 'tooltip',
                                                        'title' => 'Regresar']) 
                            ?> 
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>

                </div>
                

                <br><hr>
            </div>
        </div>
    </div>
</div>

<!-- Capa Espera -->
<div class="capaLoader" id="idCapa" style="display: none;">
  
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <table class="text-center">
        <caption><?= Yii::t('app', '.') ?></caption>
          <thead>
            <tr>
              <th scope="col" class="text-center">
                  <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
              </th>
              <th scope="col"><?= Yii::t('app', '') ?>                
              </th>
              <th scope="col" class="text-justify">
                  <h4><?= Yii::t('app', 'Actualmente CXM esta enviando la alerta al correo indicado, por favor espere...') ?></h4>
              </th>
            </tr>            
          </thead>
        </table>
      </div>
    </div>
  </div>
  <hr>
</div>

<script type="text/javascript">
  function varVerificar(){
      var varidCapa = document.getElementById("idCapa");
      var varidCapaInfo = document.getElementById("idCapaInfo");

      varidCapa.style.display = 'inline';
      varidCapaInfo.style.display = 'none';
  };
</script>