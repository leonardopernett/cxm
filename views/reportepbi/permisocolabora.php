<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\db\Query;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\modal;

$this->title = 'Permisos para colaboradores Power BI'; 
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';
$idreportepbi = "'".$idreporte."'";
$areatrabajopbi = "'".$areatrabajo."'";
$datacolab = (array)json_decode($dataper);

?>
<br>
<?= Html::a('Regresar',  ['reporte'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
    ?>
<br>
<div class="page-header" >
    <h3 style="text-align: center;"><?= Html::encode($this->title) ?></h3>
    <h3 style="text-align: center;"><?= $nombrerepor ?></h3>
</div>

<br>
	<?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
        ]); ?>
    <div class="row">
        <div class="col-md-offset-2 col-sm-8">
        <strong>Correo Usuario </strong><?= Html::input('email','email','', $options=['class'=>'form-control', 'maxlength'=>100, 'id'=>'nombrecorreo']) ?>
        </div>
    </div>        
        
   <br>
   <br>
	<?php ActiveForm::end(); ?>
	<div class="row" style="text-align: center">      
        <div onclick="permisocolab();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          	Crear permiso
        </div>
    </div>

<br>
<div id="dtbloque2" class="col-sm-12">
	<br>
	<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
        <caption>Tabla datos</caption>
		<tr>
			<th scope="col" class="text-center"><?= Yii::t('app', 'Nombre') ?></th>
			<th scope="col" class="text-center"><?= Yii::t('app', 'Correo') ?></th>
			<th scope="col" class="text-center"><?= Yii::t('app', '') ?></th>
		</tr>
		<?php
				//foreach ($datacolab as $key => $value) {
                    for ($i = 1; $i < count($datacolab); $i++) {
                        foreach ($datacolab[$i] as $key => $value) {
                            if($key == 'emailAddress'){
                               $correo =$value;
                            }
                            if($key == 'displayName'){
                                $nombre =$value;
                             }
                         }            

			?>
			<tr>
                <td class="text-center"><?php echo $nombre; ?></td>
				<td class="text-center"><?php echo $correo; ?></td>
                <td class="text-center">
				    <?= Html::button('<span class="fa fa-trash"></span>', ['class' => 'btn btn-danger',
	                                    'data-toggle' => 'tooltip',
	                                    'onclick' => "eliminarDato('".$correo."')",
	                                    'title' => 'Eliminar']) 
	                ?>
				</td> 
			</tr>
			<?php
				}
			?>
			
		
	</table>
</div>

<hr>
<script type="text/javascript">
	
    function permisocolab(){
    var varcorreo = document.getElementById("nombrecorreo").value;
    var vararearab = "<?php echo $areatrabajo; ?>";
    alert(vararearab);
	var varnombrerep = "<?php echo $nombrerepor; ?>";
    var varreporteid = "<?php echo $idreporte; ?>";
    if (varcorreo == "" ) {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","Falta cargar el correo","warning");
			return;
      }else{
        $.ajax({
              method: "post",
              url: "add_workspace_colaborator",
              data : {              
                workspace: vararearab,
                colaborator: varcorreo,                
              },
              success : function(response){ 
                          var Rta =   JSON.parse(response);    
                          console.log(Rta);
                          if (Rta != "") {
                                        window.location.href='permisocolabora?dataper='+JSON.stringify(Rta)+'&workspace='+varareatrabajoid+'&reporte='+varreporteid+'&nombrerepor='+varnombrerep;
                                       // window.location.href='permisocolabora?dataper='+JSON.stringify(Rta)+'&workspace='+vararearab+'&nombrerepor='+varnombrerep; 
                      }
              }
        }); 
      }
    
    };
    
    function eliminarDato(params1){
		var varcolabora = params1;
        var vararearab = "<?php echo $areatrabajo; ?>";
		var varnombrerep = "<?php echo $nombrerepor; ?>";
        var varreporteid = "<?php echo $idreporte; ?>";

	    var opcion = confirm("Confirmar la eliminaci�n del item de la lista...");

	    if (opcion == true){
		 $.ajax({
	                method: "post",
			url: "delete_workspace_colaborator",
	                data : {
	                    colaborator: varcolabora,
	                    workspace: vararearab,
	                },
	                success : function(response){ 
				console.log(response);
				var Rta = JSON.parse(response);
				console.log(Rta);
				if(Rta != ""){
                    window.location.href='permisocolabora?dataper='+JSON.stringify(Rta)+'&workspace='+varareatrabajoid+'&reporte='+varreporteid+'&nombrerepor='+varnombrerep;
				}else{
					alert("Error al intentar eliminar la alerta");
				}
	                }
	            });
	    }		
	};
    
</script>