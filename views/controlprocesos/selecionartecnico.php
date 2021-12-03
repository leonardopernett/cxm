<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;

$this->title = 'Equipo de Trabajo';
$this->params['breadcrumbs'][] = $this->title;

    $sessiones = Yii::$app->user->identity->id;
?>
<div class="capaUno" style="display: inline;">
	<div class="row">
		<div class="col-md-12">
			<?php $form = ActiveForm::begin([
				'layout' => 'horizontal',
				'fieldConfig' => [
					'inputOptions' => ['autocomplete' => 'off']
				  ]
				]); ?>
				<?=
		            $form->field($model, 'evaluados_id')->label(Yii::t('app',''))
		                ->widget(Select2::classname(), [
		                            'id' => 'ButtonSelect',
		                            'name' => 'BtnSelectes',
		                            'language' => 'es',
		                            'options' => ['placeholder' => Yii::t('app', 'Seleccionar tecnico...')],
		                            'pluginOptions' => [
		                                'allowClear' => true,
		                                'minimumInputLength' => 4,
		                                'ajax' => [
		                                    'url' => \yii\helpers\Url::to(['reportes/usuariolist']),
		                                    'dataType' => 'json',
		                                    'data' => new JsExpression('function(term,page) { return {search:term}; }'),
		                                    'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
		                                ],
		                                'initSelection' => new JsExpression('function (element, callback) {
		                                            var id=$(element).val();
		                                            if (id !== "") {
		                                                $.ajax("' . Url::to(['reportes/usuariolist']) . '?id=" + id, {
		                                                    dataType: "json",
		                                                    type: "post"
		                                                }).done(function(data) { callback(data.results[0]);});
		                                            }
		                                        }')
		                            ]
		                                ]
		                );
		        ?>
		    <?php $form->end() ?> 
		</div>
		<div class="col-md-12">
			<div onclick="buscart();" class="btn btn-primary" style="display:inline; width:auto; background: #337ab7;" method='post' id="botones5" >
                Agregar tecnico
            </div>
		</div>
	</div>
</div>
<div class="capaDos" style="display: none;" id="capaDos">
	<label style="font-size: 13px;"><em class="fas fa-exclamation-circle" style="font-size: 15px; color: #FF3F33;"></em> El tecnico seleccionado, pertenece al equipo de  <label style="font-size: 13px;" id="resultadoid"></label></label>
</div><br>
<div class="capaTres" style="display: none;" id="capaTres">
	<label><em class="fas fa-exclamation" style="font-size: 20px; color: #FFD733;"></em> El tecnico seleccionado, ya pertenece a su equipo.</label>
</div>
<script type="text/javascript">
	function buscart(){
		var varid = document.getElementById("controlparams-evaluados_id").value;
		var varlider = "<?php echo $sessiones; ?>";
		var varcapados = document.getElementById("capaDos");
		var varcapatres = document.getElementById("capaTres");

		if (varid != "") {
            $.ajax({
                method: "get",
                url: "selecteduser",
                data: {
                    txtvarid : varid,
                    txtvarlider : varlider
                },
                success : function(response){
                    var numRta =   JSON.parse(response);
                    console.log(numRta);

                    if (numRta == 1) {
                    	varcapados.style.display = 'inline';
                    	varcapatres.style.display = 'none';
                    	$.ajax({
                    		method: "get",
			                url: "lideruser",
			                data: {
			                    txtvarid : varid
			                },
			                success : function(response){
			                	 var numRta2 =   JSON.parse(response);
			                	 console.log(numRta2);
			                	 document.getElementById("resultadoid").innerHTML = numRta2;
			                }
                    	});
                    }else{
                    	if (numRta == 2) {
                    		varcapados.style.display = 'none';
                    		varcapatres.style.display = 'inline';
                    	}else{
                    		if (numRta == 0) {
                    			window.open('../controlprocesos/create?usua_id='+varid,'_self');
                    		}
                    	}
                    }
                }
            });
        }else{
        	event.preventDefault();
            swal.fire("!!! Advertencia !!!","No se ha seleccionado a ningún tecnico.","warning");
            return;
        }

	};
</script>
