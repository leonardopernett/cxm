
<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\modal;
use app\models\Correogrupal;
use kartik\export\ExportMenu;
use app\models\Controlcorreogrupal;
use yii\helpers\ArrayHelper;

$fechaActual = date('Y-m-d');

    $variables = Correogrupal::find()
                ->select(['nombre'])
                ->all();
    $listData = ArrayHelper::map($variables, 'nombre', 'nombre');

?>
<div class="formularios-form">
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
      ]
    ]); ?>

    
        <div id="parte1" style="display: none;">            
                <?php echo $form->field($model2, 'nombre')->textInput(['maxlength' => 150, 'id'=>'txtnameteam',  'style' => 'width:100%'])->label('Nombre del Grupo') ?>

                <?php
                    echo $form->field($model2, 'usua_id')->label('Usuarios')->widget(Select2::classname(), [
                        'language' => 'es',
                        'name' => 'subi_calculo',
                        'options' => [
                            'placeholder' => Yii::t('app', 'Select ...'),
                            'id' => 'subi_calculo'
                        ],
                        'pluginOptions' => [
                            'multiple' => true,
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'maximumSelectionSize' => 5,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['usuariolista']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                                var id=$(element).val();
                                                if (id !== "") {
                                                    $.ajax("' . Url::to(['usuariolista']) . '?id=" + id, {
                                                        dataType: "json",
                                                        type: "post"
                                                    }).done(function(data) { callback(data.results);});
                                                }
                                            }')
                        ]
                    ]);
                ?>              

                <?php echo $form->field($model2, 'fechacreacion')->textInput(['maxlength' => 200, 'value' => $fechaActual, 'class'=>"hidden", 'label'=>""]) ?>   

                <div class="panel panel-success" id="panel1" style="display: none">
			      <div class="panel-heading">OK, Correos verificados correctamente</div>
			      <div class="panel-body"></div>
			    </div>
			    <div class="panel panel-danger" id="panel2" style="display: none">
			      <div class="panel-heading">KO, Correos con problemas, actualizar los correos de los usuarios previamente seleccionados.</div>
			      <div class="panel-body"><textarea style="display: none" id="IdtextArea" readonly="readonly" cols="70" rows="5"></textarea></div>
			    </div>			    


                <div onclick="verificar();" class="btn btn-primary" style="display:inline; width:70px; height:25px" method='post' id="botones5" >
                    Verificar Correos
                </div> 
                &nbsp;
                <?= Html::submitButton(Yii::t('app', 'Crear Grupo'),
                            ['class' => $model2->isNewRecord ? 'btn btn-success' : 'btn btn-primary',                
                            'data-toggle' => 'tooltip',
                            'title' => 'Crear',
                            'style' => 'background-color: #4298b4; width:120px; height:30px; display: none',
                            'id'=>'modalButton6']) 
                ?>         
                &nbsp;
                <div onclick="ocultar();" class="btn btn-primary" style="display:inline; width:70px; height:25px; background-color: #707372" method='post' id="botones3" >
                    Regresar
                </div>              
            <br>
            <br>
        </div>
        <div id="parte2"  style="display: inline;">   
            <div onclick="selection();" class="btn btn-primary" style="display:inline;" method='post' id="botones1" >
                Seleccionar Grupo
            </div> 
            &nbsp;&nbsp;
            <div onclick="generated();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
                Crear Grupo
            </div> 
            <br>
            <br>

            <?= $form->field($model2, 'nombre2')->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'nomGrup'])->label('Nombre del Grupo') ?> 
            <br>
            <?= GridView::widget([
                    'dataProvider' => $dataProvider,        
                    //'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'attribute' => 'Nombre del Grupo',
                            'value' => 'nombre',
                        ],
                        [
                            'attribute' => 'Fecha Creacion',
                            'value' => 'fechacreacion',
                        ],             
                    ],
                ]);   
            ?>
        </div>    
    <br>
    
<?php $form->end() ?>   
</div>

<script type="text/javascript">
    function generated(){
        var capaParte1 = document.getElementById("parte1");
        var capaParte2 = document.getElementById("parte2");
        capaParte1.style.display = 'inline';
        capaParte2.style.display = 'none';
    };

    function ocultar(){
    	var pnlPanel = document.getElementById("panel1");
		var pnlPanel2 = document.getElementById("panel2");		
		var txtAreas = document.getElementById("IdtextArea");
		pnlPanel.style.display = 'none';
		pnlPanel2.style.display = 'none';
		txtAreas.style.display = 'none';		

        var capaParte1 = document.getElementById("parte1");
        var capaParte2 = document.getElementById("parte2");
        capaParte1.style.display = 'none';
        capaParte2.style.display = 'inline';

        document.getElementById("txtnameteam").value = "";
        $('#subi_calculo').val(null).trigger('change');
    };

    function selection(){
        var selectes = document.getElementById("nomGrup").value;
        //console.log(selectes);

        if (selectes == 'Seleccione ...' || selectes == undefined || selectes == null || selectes == "") {
            event.preventDefault();
                swal.fire("!!! Advertencia !!!","No se ha seleccionado ningún grupo.","warning");
            return; 
        }
        else
        {           
             $.ajax({
                method: "post",
                url: "prueba",
                data : {
                    varcorreos : selectes,                    
                },
                success : function(response){ 
                    var numRta =   JSON.parse(response);  
 
                    document.getElementById("destino").value = numRta;
                    $("#modal1").modal("hide");
                    console.log("a",numRta);
                }
            });
        }
    };

    function verificar(){
    	var varUsu = document.getElementById("subi_calculo").value;
		var varNom = document.getElementById("txtnameteam").value;
		var btnCrear = document.getElementById("modalButton6");
		var btnVerificar = document.getElementById("botones5");
		var pnlPanel = document.getElementById("panel1");
		var pnlPanel2 = document.getElementById("panel2");
		var txtAreas = document.getElementById("IdtextArea");

		pnlPanel.style.display = 'none';
		pnlPanel2.style.display = 'none';
		txtAreas.style.display = 'none';

		if (varUsu == "Select ..." || varUsu == null || varUsu == "" || varNom == null || varNom == "") {
			event.preventDefault();
                swal.fire("!!! Advertencia !!!","No es posible crear el grupo, existen campos vacios.","warning");
            return; 
		}
		else
		{
             $.ajax({
                method: "post",
                url: "comprobacion",
                data : {
                    varcorreos : varUsu,                    
                },
                success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    console.log(numRta);

		            if (numRta == 0) {
						btnCrear.style.display = 'inline';
						btnVerificar.style.display = 'none';
						pnlPanel.style.display = 'inline';
		            }
		            else
		            {
		            	event.preventDefault();
							pnlPanel2.style.display = 'inline';

							$.ajax({
				                method: "post",
				                url: "comprobacionlista",
				                data : {
				                    varcorreos : varUsu,                    
				                },
				                success : function(response){ 
				                    var numRta2 =   JSON.parse(response);    
				                    console.log(numRta2);
				                    var varDatos = numRta2.toString().replace(/,/g,'\n\r');
				                    document.getElementById("IdtextArea").value = varDatos;
				                    txtAreas.style.display = 'inline';
				                }
				            });

		                	jQuery(function(){
					            swal.fire({type: "warning",
					                title: "!!! Advertencia !!!",
					                text: "Algunos correos no se encontraron, favor actualizar."
					            }).then(function() {
					                window.open('../basesatisfaccion/actualizarcorreos','_blank');
					            });
					        });
		            	return; 		                	
		            }	

                }
            });		


		}

    };
</script>