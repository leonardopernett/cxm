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

$this->title = 'Instrumento Alinear + VOC';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Actualización del Listado Instrumento Alinear + VOC';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $valor = null;
    $txtIdPcrc = $vartxtPcrc;
    $txtNamePcrc = Yii::$app->db->createCommand("select name from tbl_arbols where id = '$txtIdPcrc'")->queryScalar();


    $variables = Yii::$app->db->createCommand("select * from tbl_controlvoc_sessionlista where anulado = 0")->queryAll();
    $listData = ArrayHelper::map($variables, 'idsessionvoc', 'nombresession');

    $txtSesiones = $varSession;

?>	
&nbsp;
<div onclick="menuPrincipal();" class="btn btn-primary" style="display:inline; width:70px; height:25px; background-color: #707372" method='post' id="botones2" >
	Regresar
</div> 
<br>
<div class="page-header" >
    <h3><center><?= Html::encode($this->title) ?></center></h3>
</div> 
<br>
<div id="dtbloque1" class="col-sm-12" style="align-items: center;  display: flex; justify-content: center;">
	<div id="dtbloque2" class="col-sm-6" >
		<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
		<label for="txtTipo_accion" style="text-align:left !important;">Listado:</label> 
		<select id="txtTipo_accion" class ='form-control' >Listado:
					<option value="" disabled selected>Seleccione...</option>
					<option value="1">Categorias</option>
					<option value="2">Atributos</option>
		</select>
		<br>
		<br>                      
		<div onclick="enviar();" class="btn btn-primary" style="display:inline; height: 34px; text-align:center;" method='post' id="botones2" >
						Buscar listado
		</div>
		<br>
		<?php ActiveForm::end(); ?>
	</div>
</div><script type="text/javascript">

function enviar(){
    var varPcrc = "<?php echo $vartxtPcrc; ?>";
    var varSelec = document.getElementById("txtTipo_accion").value;

		console.log(varSelec);

	    
		 $.ajax({
	                method: "post",
			url: "actualiza",
	                data : {
	                    var_Pcrc: varPcrc,						
	                    var_Sesiones: varSelec,
	                },
	                success : function(response){ 
				console.log(response);
				var respuesta = JSON.parse(response);
				console.log(respuesta);
				if(respuesta != 0){
					//window.location.href = "https://172.20.100.50/qa/web/index.php/controlalinearvoc/updatevocalinear?txtPcrc="+varPcrc+'&varSession='+varSelec;
					window.location.href='updatevocalinear?txtPcrc='+varPcrc+'&varSession='+varSelec;

				}
	                }
	            });
		
	};
	
	function menuPrincipal(){	
		var varPCRC = "<?php echo $txtIdPcrc; ?>";
		window.location.href='indexvoc?arbol_idV='+varPCRC ,'_self';	
        	//window.open('http://qa.allus.com.co/qa_managementv2/web/index.php/controlalinearvoc/indexvoc?arbol_idV='+varPCRC ,'_self');
			//window.location.href='updatevocalinear?txtPcrc='+varPcrc+'&varSession='+varSelec;
    	};
</script>