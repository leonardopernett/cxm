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

$this->title = 'Heroes por Cliente';//nombre del titulo de mi modulo
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
        background-image: url('../../images/heroes.png');
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
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><hr><br>
<div class="capaInfo" id="idCapaInfo" style="display: inline;">
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
  <div class="row"><!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
      <div class="col-md-6">
          <div class="card1 mb">
              <label style="font-size: 17px;"><em class="fas fa-hand-point-right" style="font-size: 25px; color: #C31CB4;"></em> <?= Yii::t('app', '¡Qué bueno que estés aquí!  Cuando sentimos pasión por el servicio hacemos que nuestros usuarios queden tranquilos y satisfechos con nuestra labor Demuéstranos aquí en tu postulación, que lo más importante es que la experiencia de nuestros usuarios se transforme positivamente y genere memorabilidad hacía la marca que estás representando.') ?></label>
          </div>
      </div>

      <div class="col-md-3">
        <div class="card1 mb">
          <label class="text-center" style="font-size: 15px;""><em class="fas fa-hashtag" style="font-size: 25px; color: #C31CB4;"></em> Cantidad de Postulaciones : </label>
                        
            <label style="font-size: 20px;" class="text-center" ><?php
                                $var = (new \yii\db\Query())
                                    ->select(['count(*)'])
                                    ->from(['tbl_postulacion_heroes'])
                                    ->where(['=','embajadorpostular',$])
                                    ->scalar();
                                    echo $var
                                ?></label>
        </div>
      </div>
      
      
      <div class="col-md-3">
        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #C31CB4;"></em> Enviar y Guardar </label> 
            <?= Html::submitButton(Yii::t('app', 'Enviar y Guardar'),//nombre del boton
                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'onClick'=> 'varVerificar();',//funcion de JS que al dar clic verifique que estoy enviando 
                                    'title' => 'Enviar']) 
              ?>
        </div>
      </div>
  </div>  

  </div>
  <br><hr><br>

  <div class="row"><!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
    <div class="col-md-12">
      <div class="card1 mb">

        <div class="row">
          <div class="col-md-6">
               
            <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Tipo de Postulación:') ?></label>
            <?= $form->field($model,'tipodepostulacion',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($varTipoPostu ,['prompt'=>'Seleccionar...', "onchange" => 'varValida();', 'id' => 'tipodepostulacion'])?>

            <br>
              
            <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Nombre de Quién Postula:') ?></label> 
            
            <?= $form->field($model, 'nombrepostula', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['disabled' => true,'value' => $modelEvaluado1])->label('') ?>

            <br>
              
            <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Cargo de Quién Postula:') ?></label> 

            <?= $form->field($model,'cargopostula',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($varTipoCargo,['prompt'=>'Seleccionar...', 'id' => 'cargopostula'])?>

            <br>
              
            <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Embajador/ Persona a Postular:') ?></label> 
            <?=
                    $form->field($model, 'embajadorpostular', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'multiple' => true,
                            'allowClear' => true,
                            'minimumInputLength' => 4,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['reportes/evaluadolistmultiple']),
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

            <br>

            <label><em class="fas fa-check" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Tipo de Cliente') ?></label> <!-- label  del titulo de lo que vamos a mostrar ------>
                <?=  $form->field($model, 'pcrc', 
                ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                ->dropDownList(ArrayHelper::map(\app\models\ProcesosVolumendirector::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                                [
                                                    'prompt'=>'Seleccionar...',//placeholder de lo que se va a mostrar
                                                ]
                                        )->label('');  // para que tome el lavel de arriba 
            ?>

            <br>

            <div id="IdBloque2" style="display:inline;">
              
              <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Cuéntanos la historia que merece ser reconocida como gente buena, buena gente:') ?></label> 
                <?= $form->field($model, 'historiabuenagente', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textarea()?> 
            </div>
              
            <br>
          
          </div>
          <div class="col-md-6"> 
            <div id="IdBloque3">      
            
              <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Fecha / Hora de la Interacción:') ?></label>
              <?= $form->field($model, 'fechahorapostulacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'fechahorapostulacion', 'placeholder'=>'AA-MM-DD HH:MM:SS','title' => 'Importante AÑO-MES-DIA HORA:MIN:SEG'])->label('') ?>

              <br>

                
              <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Extensión de la Interacción:') ?></label> 
              <?= $form->field($model, 'extensioniteracion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'extensioniteracion', 'placeholder'=>'Extensión de la Interacción ...'])->label('') ?>

          
                
              <br>

                
              <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Nombre del Usuario que Vive la Experiencia:') ?></label> 
              <?= $form->field($model, 'usuariovivexperiencia', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'usuariovivexperiencia', 'placeholder'=>'Nombre del Usuario que Vive la Experiencia ...'])->label('') ?>
            </div>
            <br>

            <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Ciudad:') ?></label> 
            <?= $form->field($model,'ciudad',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($varCiudad,['prompt'=>'Seleccionar...', 'id' => 'ciudad'])?>           
                       
            <br>

            <div id="IdBloque1" style="display:none;">
              
              <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Cuéntanos esa idea que nos ayudará a mejorar las experiencias y  fortalecer  la cultura de relacionamiento:') ?></label> 
              <?= $form->field($model, 'idea', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textarea()?> 
            </div>
  
              
            <br>
          </div>   
        </div>
      </div>  
    </div>
  </div>
 
  <?php ActiveForm::end(); ?>

  <br><hr><br>
</div>

<script>
  function varValida(){
    var varidSeleccionar = document.getElementById("tipodepostulacion").value;
    var varBloque1 =  document.getElementById("IdBloque1");
    var varBloque2 =  document.getElementById("IdBloque2");
    var varBloque3 =  document.getElementById("IdBloque3");

    if (varidSeleccionar == "Embajadores que Konectan") {
      varBloque1.style.display='none';
      varBloque2.style.display='none';
      varBloque3.style.display='inline';
    }

    if (varidSeleccionar == "Gente Buena,Buena Gente") {
      varBloque1.style.display='none';
      varBloque3.style.display='none';
      varBloque2.style.display='inline';
    }

    if (varidSeleccionar == "Eureka") {
      varBloque1.style.display='inline';
      varBloque2.style.display='none';
      varBloque3.style.display='none';
    }
  }

  function varVerificar() {

    var tipodepostulacion = document.getElementById("tipodepostulacion").value;  
    var nombrepostula = document.getElementById("s2id_autogen1").value;
    var cargopostula = document.getElementById("cargopostula").value;  
    var embajadorpostular = document.getElementById("s2id_autogen2").value;
    var ciudad = document.getElementById("ciudad").value;  
   

    if (tipodepostulacion == "") {
      event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe seleccionar tipo de postulacion","warning");
            return;
    }
    if (s2id_autogen1 == "") {
      event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe ingresar nombre de quien postula","warning");
            return;
    }
    if (cargopostula == "") {
      event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe seleccionar cargo de la persona","warning");
            return;
    }
    if (s2id_autogen2 == "") {
      event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe ingresar embajador a postular","warning");
            return;
    }
    if (ciudad == "") {
      event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe seleccionar la ciudad","warning");
            return;
    }
    
    
  }
</script>