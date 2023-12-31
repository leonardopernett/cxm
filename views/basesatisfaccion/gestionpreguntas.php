<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\bootstrap\ActiveForm;

$this->title = 'Gestion Preguntas';
?>



    <?php if (Yii::$app->session->hasFlash('enviado')): ?>
		
		<div id="datosGenerales" class="col-md-offset-1 col-sm-10">
    		<table class="table table-striped table-bordered detail-view formDinamico">
			<caption>Tabla datos generales</caption>
    			<tbody>
    				<tr>
    					<th scope="col" colspan="2"><div class="alert alert-success text-center">   
    						Respuesta Guardada Satisfactoriamente.
    					</div>
    				</th>
	    			</tr>
	    			<tr>
	    				<td>
	    					<p><strong>Pregunta 1:</strong> <?=$model->pregunta1?> </p>   
	    				</td>
	    				<td>
	    					<p><strong>Pregunta 2:</strong> <?=$model->pregunta2?> </p>   
	    				</td>
	    			</tr>                     
	    			<tr>
	    			</tr>
	    			<tr>
	    				<td>
	    					<p><strong>Pregunta 3:</strong> <?=$model->pregunta3?> </p>   
	    				</td>
	    				<td>
	    					<p><strong>Pregunta 4:</strong> <?=$model->pregunta4?> </p>   
	    				</td>
	    			</tr>
	    			<tr>
	    			</tr>
	    			<tr>
	    				<td>
	    					<p><strong>Pregunta 5:</strong> <?=$model->pregunta5?> </p>   
	    				</td>
	    				<td>
	    					<p><strong>Pregunta 6:</strong> <?=$model->pregunta6?> </p>   
	    				</td>
	    			</tr>
	    			<tr>
	    			</tr>
	    			<tr>
	    				<td>
	    					<p><strong>Pregunta 7:</strong> <?=$model->pregunta7?> </p>   
	    				</td>
	    				<td>
	    					<p><strong>Pregunta 8:</strong> <?=$model->pregunta8?> </p>   
	    				</td>
	    			</tr>
	    			<tr>
	    			</tr>
	    		</tbody>
	    	</table>
	    </div>

    <?php else: ?> 
    	<div id="datosGenerales" class="col-md-offset-1 col-sm-10">
    		<table class="table table-striped table-bordered detail-view formDinamico">
			<caption>Tabla datos generales</caption>
    		<?php $form = ActiveForm::begin([
				'fieldConfig' => [
					'inputOptions' => ['autocomplete' => 'off']
				  ]
			]); ?>  
    			<tbody>
    			<div class="row seccion-data">
    			<div class="col-md-10">
                            <label class="labelseccion ">
                                  Gestion Preguntas Pliego de Cargos
                            </label>
                        </div></div>
	    			<tr>
	    				<th scope="col">
	    					<p><?= $form->field($model, 'pregunta1')->textInput(['maxlength' => 200])->label('Pregunta 1:') ?> </p>   
	    				</th>
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
	    			<td  colspan="2" style="text-align: center;"><?= Html::submitButton('Actualizar', ['id' => 'boton','class' => 'btn btn-primary', 'name' => 'contact-button']) ?></td>
	    		</tbody>
			<?php ActiveForm::end(); ?>
	    	</table>
	    </div>
    	

    <?php endif; ?>