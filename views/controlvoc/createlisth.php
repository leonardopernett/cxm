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
use app\models\ControlvocListadopadre;

$this->title = 'Creacion de Sesiones VOC';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $fechaactual = date("Y-m-d");

    $txtPcrc = $vartxtArbol;

    $listasPadre = Yii::$app->db->createCommand("select idsessionvoc from tbl_controlvoc_sessionlista where nombresession like '%Motivo de contacto o Tipos de Servicio%' and anulado = 0")->queryScalar();    

    $variables = Yii::$app->db->createCommand("select * from tbl_controlvoc_listadopadre where idsessionvoc = '$listasPadre' and anulado = 0 and arbol_id = '$txtPcrc'")->queryAll();
    $listData = ArrayHelper::map($variables, 'idlistapadrevoc', 'nombrelistap');

    $idBloque = Yii::$app->db->createCommand("select idsessionvoc from tbl_controlvoc_sessionlista where nombresession like '%Motivos de Llamadas%' and anulado = 0")->queryScalar();
?>
<br>
<div class="formularios-form" style="display: inline" id="dtbloque1">
	<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
		<?php 
			echo $form->field($model4, 'idsessionvoc')->textInput(['maxlength' => 200, 'value'=>$idBloque, 'id'=>"nomSesionId", 'class'=>'hidden'])
		?>

		<?php 
			echo $form->field($model4, 'idlistapadrevoc')->dropDownList($listData, ['prompt' => 'Seleccione...',  'id'=>'selectedID'])->label('Bloque o sesion') 
		?> 

		<?php 
			echo $form->field($model4, 'nombrelistah')->textInput(['maxlength' => 200, 'id'=>"nomnId"])->label("Ingresar Nombre Item")
		?>

		<?php 
			echo $form->field($model4, 'fechacreacion')->textInput(['maxlength' => 200, 'value'=> $fechaactual, 'class'=>'hidden', 'id'=>"fechaId"])
		?>

		<?php 
			echo $form->field($model4, 'anulado')->textInput(['maxlength' => 1, 'value'=> '0', 'class'=>'hidden', 'id'=>"anuladoId"])
		?>	

	<?php ActiveForm::end(); ?>
	<div class="row" style="text-align: center;">      
		<div onclick="generated();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          	Crear Motivo 
        </div>    
    </div>
</div>
<script type="text/javascript">
	function generated(){
		var varSesiones = "<?php echo $idBloque; ?>";
		var varListaP = document.getElementById("selectedID").value;
		var varNames = document.getElementById("nomnId").value;
		var varfechas = document.getElementById("fechaId").value;
		var varanular = document.getElementById("anuladoId").value;
		

		if (varListaP == "" || varNames == "") {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","No hay datos a registrar.","warning");
			return;
		}else{
			$.ajax({
			        method: "post",
			        url: "createmotivo",
			        data : {
			          txtvsesion : varSesiones,
			          txtvarbol : varListaP,
			          txtvname : varNames,
			          txtvfechas : varfechas,
			          txtvanular : varanular,
			        },
			        success : function(response){ 
			                    var numRta =   JSON.parse(response);    
			                    console.log(numRta);

			                    if (numRta != 0) {
									$("#modal3").modal("hide");
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