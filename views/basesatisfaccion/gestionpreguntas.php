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

$this->title = 'Gestion Preguntas';
?>



    <?php if (Yii::$app->session->hasFlash('enviado')): ?>
		
		<div id="datosGenerales" class="col-md-offset-1 col-sm-10" style="">
    		<table class="table table-striped table-bordered detail-view formDinamico">
    			<tbody>
    				<tr>
    					<td colspan="2"><div class="alert alert-success">   
    						<center>Respuesta Guardada Satisfactoriamente.</center>
    					</div>
    				</td>
	    			</tr>
	    			<tr>
	    				<td>
	    					<p><b>Pregunta 1:</b> <?=$model->pregunta1?> </p>   
	    				</td>
	    				<td>
	    					<p><b>Pregunta 2:</b> <?=$model->pregunta2?> </p>   
	    				</td>
	    			</tr>                     
	    			<tr>
	    			</tr>
	    			<tr>
	    				<td>
	    					<p><b>Pregunta 3:</b> <?=$model->pregunta3?> </p>   
	    				</td>
	    				<td>
	    					<p><b>Pregunta 4:</b> <?=$model->pregunta4?> </p>   
	    				</td>
	    			</tr>
	    			<tr>
	    			</tr>
	    			<tr>
	    				<td>
	    					<p><b>Pregunta 5:</b> <?=$model->pregunta5?> </p>   
	    				</td>
	    				<td>
	    					<p><b>Pregunta 6:</b> <?=$model->pregunta6?> </p>   
	    				</td>
	    			</tr>
	    			<tr>
	    			</tr>
	    			<tr>
	    				<td>
	    					<p><b>Pregunta 7:</b> <?=$model->pregunta7?> </p>   
	    				</td>
	    				<td>
	    					<p><b>Pregunta 8:</b> <?=$model->pregunta8?> </p>   
	    				</td>
	    			</tr>
	    			<tr>
	    			</tr>
	    		</tbody>
	    	</table>
	    </div>

    <?php else: ?> 
    	<div id="datosGenerales" class="col-md-offset-1 col-sm-10" style="">
    		<table class="table table-striped table-bordered detail-view formDinamico">
    		<?php $form = ActiveForm::begin(); ?>  
    			<tbody>
    			<div class="row seccion-data">
    			<div class="col-md-10">
                            <label class="labelseccion ">
                                  Gestion Preguntas Pliego de Cargos
                            </label>
                        </div></div>
	    			<tr>
	    				<td>
	    					<p><?= $form->field($model, 'pregunta1')->textInput(['maxlength' => 200])->label('Pregunta 1:') ?> </p>   
	    				</td>
	    				<td>
	    					<p><?= $form->field($model, 'pregunta2')->textInput(['maxlength' => 200])->label('Pregunta 2:') ?> </p> 
	    				</td>
	    			</tr>                     
	    			<tr>
	    			</tr>
	    			<tr>
	    				<td>
	    					<p><?= $form->field($model, 'pregunta3')->textInput(['maxlength' => 200])->label('Pregunta 3:') ?> </p> 
	    				</td>
	    				<td>
	    					<p><?= $form->field($model, 'pregunta4')->textInput(['maxlength' => 200])->label('Pregunta 4:') ?> </p> 
	    				</td>
	    			</tr>
	    			<tr>
	    			</tr>
	    			<tr>
	    				<td>
	    					<p><?= $form->field($model, 'pregunta5')->textInput(['maxlength' => 200])->label('Pregunta 5:') ?> </p> 
	    				</td>
	    				<td>
	    					<p><?= $form->field($model, 'pregunta6')->textInput(['maxlength' => 200])->label('Pregunta 6:') ?> </p> 
	    				</td>
	    			</tr>
	    			<tr>
	    			</tr>
	    			<tr>
	    				<td>
	    					<p><?= $form->field($model, 'pregunta7')->textInput(['maxlength' => 200])->label('Pregunta 7:') ?> </p> 
	    				</td>
	    				<td>
	    					<p><?= $form->field($model, 'pregunta8')->textInput(['maxlength' => 200])->label('Pregunta 8:') ?> </p> 
	    				</td>
	    			</tr>
	    			<tr>
	    			</tr>
	    			<td  colspan="2" align="center"><?= Html::submitButton('Actualizar', ['id' => 'boton','class' => 'btn btn-primary', 'name' => 'contact-button']) ?></td>
	    		</tbody>
			<?php ActiveForm::end(); ?>
	    	</table>
	    </div>
    	

    <?php endif; ?>