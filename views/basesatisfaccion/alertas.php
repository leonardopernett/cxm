<?php
//include '../views/plantillasForm/plantilla' . $data->formulario->id_plantilla_form . '.php';

//echo "<pre>";
//print_r($detallesseccion_id);
//echo "</pre>";
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;


//print_r($prueba); die;

use yii\bootstrap\ActiveForm;

$this->title = 'Alertas Valorador CX';
$template = '<div class="col-md-3">{label}</div><div class="col-xs-9">'
        . ' {input}{error}{hint}</div>';
?>


<?php $this->params['breadcrumbs'][] = $this->title; ?>
<style type="text/css">
    
    .form-group {
        margin: 11px !important; 
        padding: 15px !important;
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
<br><br>
    <?php if ($listo == 1){ ?>
        <div class="col-md-offset-2 col-sm-8 alert alert-success">Alerta Guardada Satisfactoriamente</div>
    <?php }elseif ($listo == 2) { ?>
        <div class="col-md-offset-2 col-sm-8 alert alert-danger">Ocurrio un Error al Guardar la Alerta, Recuerda que el archivo debe ser .pdf - .jpg o .png</div>
    <?php } ?>
        <div id="datosGenerales" class="col-md-offset-2 col-sm-8" style="">
            <table class="table table-striped table-bordered detail-view formDinamico">
            <caption>Tabla alerta</caption>
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?> 
                <tbody>


                    <form class="form-horizontal">
                        <div class="form-group">
                        <div class="col-md-3"><label class="control-label">Tipo de Alerta</label></div>
                        <div class="col-xs-9">
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
                            
                        <div class="form-group">
                            <label class="control-label col-xs-3">Adjuntar Archivo</label>
                            <div class="col-xs-9">
                                <div style="position:relative;">
                                    <div class="field-uploadform-archivo_adjunto">
                                        <input type="hidden" name="UploadForm[archivo_adjunto][]" value=""><input type="file" id="uploadform-archivo_adjunto" name="UploadForm[archivo_adjunto][]" accept="image/*, application/pdf" required>

                                        <p class="help-block help-block-error"></p>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-xs-3">Destinatario:</label>
                            <div class="col-xs-9">
                                <input type="email" id="destino" name="remitentes" class="form-control" placeholder="Destinatario" multiple required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-xs-3">Asunto:</label>
                            <div class="col-xs-9">
                                <input type="text" name="asunto" class="form-control" placeholder="Asunto" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-xs-3">Comentario:</label>
                            <div class="col-xs-9">
                                <textarea rows="3" name="comentario" class="form-control" placeholder="Comentario" required></textarea>
                            </div>
                        </div>
                        <br>
                                                
                        <div class="form-group">
                            <div class="col-xs-offset-3 col-xs-9">
                                <input type="submit" class="btn btn-primary" value="Enviar">
                                <input type="reset" class="btn btn-default" value="Limpiar">
				<?= Html::button('Correo Grupal', ['value' => url::to('correogrupal'), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Correo Grupal', 'style' => 'background-color: #4298b4']) ?> 

                                <?php
				use yii\bootstrap\Modal;
                                    Modal::begin([
                                            'header' => '<h4>Correo Grupal</h4>',
                                            'id' => 'modal1',
                                            //'size' => 'modal-lg',
                                        ]);

                                    echo "<div id='modalContent1'></div>";
                                
                                    Modal::end(); 
                                ?> 
                            </div>
                        </div>
                    </form>


                
                </tbody>
            <?php ActiveForm::end(); ?>
            </table>
        </div>
<br>
<div class="col-md-offset-2 col-sm-8 panel panel-default">
  <div class="panel-body"><center>
    <p>En el campo de destinatarios se ingresan los correos electronicos de los interesados en la alerta. <strong><p style="color: #FE562C">Cada dato debe estar separado por una ","</p></strong></p></center>
  </div>
</div>
    