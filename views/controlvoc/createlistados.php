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

$this->title = 'Creacion de Sesiones VOC';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $fechaactual = date("Y-m-d");

	$variables = Yii::$app->db->createCommand("select * from tbl_controlvoc_sessionlista where anulado = 0")->queryAll();
    $listData = ArrayHelper::map($variables, 'idsessionvoc', 'nombresession');
?>
<br>
<div class="formularios-form" style="display: inline" id="dtbloque1">
	<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
		<?= $form->field($model3, 'arbol_id')->textInput(['maxlength' => 10, 'id'=>"pcrc_id", 'class'=>'hidden']) ?> 

		<?php 
			echo $form->field($model3, 'idsessionvoc')->dropDownList($listData, ['prompt' => 'Seleccione...',  'id'=>'selectedID'])->label('Bloque o sesion') 
		?> 

		<?php 
			echo $form->field($model3, 'nombrelistap')->textInput(['maxlength' => 200, 'id'=>"nomSesionId"])->label("Ingresar Nombre")
		?>

		<?php 
			echo $form->field($model3, 'fechacreacion')->textInput(['maxlength' => 200, 'value'=> $fechaactual, 'class'=>'hidden', 'id'=>"fechaId"])
		?>

		<?php 
			echo $form->field($model3, 'anulado')->textInput(['maxlength' => 1, 'value'=> '0', 'class'=>'hidden', 'id'=>"anuladoId"])
		?>			

	<?php ActiveForm::end(); ?>
	<div class="row" align="center">      
		<div onclick="generated();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          	Crear Lista
        </div>    
    </div>
</div>
<br>
<hr>
<div class="formularios-form" style="display: none" id="dtbloque3">
	<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
        <thead>
            <tr>
                <th class="text-center">Bloque</th>
                <th class="text-center">Nombre Atributo</th>
                <th class="text-center">Pcrc Relacionado</th>
            </tr>
        </thead>
        <tbody>                    
            <?php 
            	$dataIndi =  new Query;
                $dataIndi   ->select(['tbl_controlvoc_sessionlista.nombresession','tbl_controlvoc_listadopadre.nombrelistap','tbl_arbols.name'])
                            ->from('tbl_controlvoc_listadopadre')
                            ->join('LEFT OUTER JOIN', 'tbl_controlvoc_sessionlista',
                                        'tbl_controlvoc_listadopadre.idsessionvoc = tbl_controlvoc_sessionlista.idsessionvoc')
                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                        'tbl_controlvoc_listadopadre.arbol_id = tbl_arbols.id');
                $command = $dataIndi->createCommand();
                $data = $command->queryAll();
                

                foreach ($data as $key => $value) {
                    $txtiD = $value['nombresession'];
                    $txtNams = $value['nombrelistap'];
                    $txtTree = $value['name'];
            ?> 
                <tr>                  
                    <td class="text-center"><?php echo $txtiD; ?></td>
                    <td class="text-center"><?php echo $txtNams; ?></td>
                    <td class="text-center"><?php echo $txtTree; ?></td>
                </tr>
            <?php } ?>                    
        </tbody>
    </table>
</div>
<script type="text/javascript">
	document.getElementById("pcrc_id").value = document.getElementById("pcrcid").value;
	
	function generated(){
		var varSesion = document.getElementById("selectedID").value;
		var varArbol = document.getElementById("pcrc_id").value;
		var varName = document.getElementById("nomSesionId").value;
		var varFechas = document.getElementById("fechaId").value;
		var varAnular = document.getElementById("anuladoId").value;

		if (varSesion == "" || varArbol == "" || varName == "") {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","No hay datos a registrar.","warning");
			return;
		}else{
			$.ajax({
			        method: "post",
			        url: "createlistavoc",
			        data : {
			          txtvsesion : varSesion,
			          txtvarbol : varArbol,
			          txtvname : varName,
			          txtvfechas : varFechas,
			          txtvanular : varAnular,
			        },
			        success : function(response){ 
			                    var numRta =   JSON.parse(response);    
			                    console.log(numRta);

			                    if (numRta != 0) {
									$("#modal2").modal("hide");
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