<?php

use yii\helpers\Html;
/*use yii\widgets\ActiveForm;*/
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


?>
<div class="page-header" >
    <h3><center>Actualizar Correos</center></h3>
</div> 
<div class="control-procesos-index">
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
	<div class="row">
        <?php
                    echo    $form->field($model, 'usua_id')->label(Yii::t('app','Valorador'))
                        ->widget(Select2::classname(), [
                            //'data' => array_merge(["" => ""], $data),
                            'language' => 'es',
                            'options' => ['id'=>'selectUsuaId','placeholder' => Yii::t('app', 'Select ...')],
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
        &nbsp;&nbsp;
        <div align="center">
	        <?= Html::submitButton(Yii::t('app', 'Buscar'),
	                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',                
	                    'data-toggle' => 'tooltip',
	                    'title' => 'Buscar',
	                    'onclick' => 'limpiar();',
	                    'id'=>'ButtonSearch']) 
	        ?>
        </div>
	</div>
	<br>	
	<div class="row">
		<?php  echo $form->field($model, "usua_email")->textInput(['id' => 'txtEmail', 'value' => $varEmail])->label('Correo') ?>

	</div>
	<br>
	<div class="row" align="center">
        <div onclick="generated();" class="btn btn-primary" style="display:inline;" method='post' id="botones2">
            Actualizar
        </div>  
	</div>


<?php $form->end() ?>   
</div>

<script type="text/javascript">
	function limpiar(){
		document.getElementById("txtEmail").value = "";
	};

	function generated(){
		var varUsua = document.getElementById("selectUsuaId").value;
		var varCorreo = document.getElementById("txtEmail").value;

        var varWord1 = "allus";
        var varWord2 = "multienlace";

        var nvarWord1 = varCorreo.indexOf(varWord1);
        var nvarWord2 = varCorreo.indexOf(varWord2);


        if (nvarWord1 >= 0 || nvarWord2 >= 0) {
            event.preventDefault();
                swal.fire("!!! Advertencia !!!","No es posible actualizar correo, verificar Email.","warning");
            return; 
        }else{
            if (varUsua == null || varUsua == "" || varCorreo == null || varCorreo == "") {
                event.preventDefault();
                    swal.fire("!!! Advertencia !!!","No se registran datos en los campos, no es posible realizar ninguna accion.","warning");
                return;             
            }else{
                $.ajax({
                    method: "post",
                    url: "pruebaactualizar",

                    data : {
                        varusuarios : varUsua,
                        varcorreos : varCorreo,                      
                    },
                    success : function(response){ 
                        var numRta =   JSON.parse(response); 
                        console.log(numRta);
                        document.getElementById("txtEmail").value = "";
                        document.getElementById("selectUsuaId").value = "";
                        $('#selectUsuaId').val(null).trigger('change');
                        jQuery(function(){
                            swal.fire({type: "success",
                                    title: "!!! OK !!!",
                                    text: "El correo se actualizo correctamente."
                            }).then(function() {
                                    window.close();
                            });
                        });
                        // event.preventDefault();
                        //     swal.fire("!!! OK !!!","El correo se actualizo correctamente.","success");  
                        return; 
                    }
                });
            }               
        }

	};
</script>