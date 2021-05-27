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
use yii\bootstrap\modal;
use app\models\ControlvocListadopadre;

$this->title = 'Creacion de Atributos Alinear + VOC'; 
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $fechaactual = date("Y-m-d");
    $varidAbol = $idAbol;
   $dataIndi =  new Query;
                $dataIndi   ->select(['tbl_categorias_alinear.id_categ_ali', 'CONCAT_WS(" - ", tbl_sesion_alinear.sesion_nombre, tbl_categorias_alinear.categoria_nombre) AS categoria'])
                            ->from('tbl_categorias_alinear')
                            ->join('LEFT OUTER JOIN', 'tbl_sesion_alinear',
                                        'tbl_categorias_alinear.sesion_id = tbl_sesion_alinear.sesion_id')
                            ->where(['tbl_categorias_alinear.arbol_id' => $varidAbol]);
                $command = $dataIndi->createCommand();
                $variables = $command->queryAll();

    $listData = ArrayHelper::map($variables, 'id_categ_ali', 'categoria');


?>
<br>
<div class="formularios-form" style="display: inline" id="dtbloque1">
	<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
		

		<?php 
			echo $form->field($model3, 'id_categ_ali')->dropDownList($listData, ['prompt' => 'Seleccione...',  'id'=>"selectedID2"])->label('Sesion') 
		?> 

		<?php 
			echo $form->field($model3, 'atributo_nombre')->textInput(['maxlength' => 200, 'id'=>"nomSesionId2"])->label("Ingresar Nombre")
		?>

		<?php 
			echo $form->field($model3, 'fechacreacion')->textInput(['maxlength' => 200, 'value'=> $fechaactual, 'class'=>'hidden', 'id'=>"fechaId2"])
		?>

		<?php 
			echo $form->field($model3, 'anulado')->textInput(['maxlength' => 1, 'value'=> '0', 'class'=>'hidden', 'id'=>"anuladoId2"])
		?>			

	<?php ActiveForm::end(); ?>
	<div class="row" align="center">      
		<div onclick="generated1();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          	Crear Atributo
        </div>    
    </div>
</div>
<br>
<hr>
<script type="text/javascript">
	
	
	function generated1(){
		var varSesion = document.getElementById("selectedID2").value;
		var varName = document.getElementById("nomSesionId2").value;
		var varFechas = document.getElementById("fechaId2").value;
		var varAnular = document.getElementById("anuladoId2").value;

		if (varSesion == "" || varName == "") {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","No hay datos a registrar.","warning");
			return;
		}else{
			$.ajax({
			        method: "post",
			        //url: "https://172.20.100.50/qa/web/index.php/controlvoc/createlistavoc",
			        url: "createatributoalinearvoc",
			        data : {
			          txtvsesion : varSesion,
			          txtvname : varName,
			          txtvfechas : varFechas,
			          txtvanular : varAnular,
			        },
			        success : function(response){ 
			                    var numRta =   JSON.parse(response);    
			                    console.log(numRta);

			                    if (numRta != 0) {
									$("#modal5").modal("hide");
			                    }else{
			                    	event.preventDefault();
							        	swal.fire("!!! Advertencia !!!","No se pueden guardar los datos.","warning");
							      	return;
			                    }
			                }
			});
		}
	};
</script>