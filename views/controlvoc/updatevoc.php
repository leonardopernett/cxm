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

$this->title = 'Instrumento Escucha Focalizada - VOC -';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Actualización del Listado Escucha Focalizada - VOC -';

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
    <h3 style="text-align: center"><?= Html::encode($this->title) ?></h3>
</div> 
<br>
<div id="dtbloque1" class="col-sm-12">
	<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?> 
		<?= $form->field($model, 'nombrelistap')->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'indicadorID'])->label('Sesiones') ?>
		<br>
		<div class="row" style="text-align: center;">  
          <?= Html::submitButton(Yii::t('app', 'Buscar Listado'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'title' => 'Buscar']) 
          ?>	

        </div>
	<br>
	<?php ActiveForm::end(); ?>
</div>

<div id="dtbloque2" class="col-sm-12">
	<br>
	<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
	<caption>Tabla datos</caption>
		<tr>
			<th id="idListado" class="text-center"><?= Yii::t('app', 'Id listado') ?></th>
			<th id="listado" class="text-center"><?= Yii::t('app', 'Listado') ?></th>
			<th id="nombreFormulario" class="text-center"><?= Yii::t('app', 'Nombre Formulario') ?></th>
			<th id="" class="text-center"><?= Yii::t('app', '') ?></th>
		</tr>
		
			<?php
			if ($txtSesiones != '4') {
				$dataProvider = Yii::$app->db->createCommand("select * from tbl_controlvoc_listadopadre where idsessionvoc = '$varSession' and anulado = 0 and arbol_id = '$vartxtPcrc'")->queryAll(); 	
			}else{
				$txtQuery2 =  new Query;
                $txtQuery2  ->select(['tbl_controlvoc_listadohijo.idlistahijovoc','tbl_controlvoc_listadohijo.nombrelistah'])->distinct()
                            ->from('tbl_controlvoc_listadopadre')
                            ->join('LEFT OUTER JOIN', 'tbl_controlvoc_listadohijo',
                            	   'tbl_controlvoc_listadopadre.idlistapadrevoc = tbl_controlvoc_listadohijo.idlistapadrevoc')
                            ->where('tbl_controlvoc_listadopadre.idsessionvoc in (3)')
                            ->andwhere('tbl_controlvoc_listadopadre.arbol_id ='.$vartxtPcrc.'');
                $command = $txtQuery2->createCommand();
                $dataProvider = $command->queryAll();
			}
				

				foreach ($dataProvider as $key => $value) {
					if ($txtSesiones != '4') {
						$varIdList = $value['idlistapadrevoc'];
						$varNomList = $value['nombrelistap'];
					}else{
						$varIdList = $value['idlistahijovoc'];
						$varNomList = $value['nombrelistah'];
					}
					
					
			?>
			<tr>
				<td class="text-center"><?php echo $varIdList; ?></td>
				<td class="text-center"><?php echo $varNomList; ?></td>
				<td class="text-center"><?php echo $txtNamePcrc; ?></td>
				<?php
					if ($txtSesiones != '4') {						
				?>
					<td class="text-center">
						<?= Html::a('<img src="../../../web/images/ico-edit.png">',  ['editarvocp','var_pcrc' => $txtIdPcrc,'var_IdList' => $varIdList], ['class' => 'btn btn-primary',
	                                    'data-toggle' => 'tooltip',
	                                    'title' => 'Modificar Listado']) 
	            				?>
            				
				<?php
					}else{
				?>
					<td class="text-center">
						<?= Html::a('<img src="../../../web/images/ico-edit.png">',  ['editarvoch','var_pcrc' => $txtIdPcrc,'var_IdList' => $varIdList], ['class' => 'btn btn-primary',
	                                    'data-toggle' => 'tooltip',
	                                    'title' => 'Modificar Listado']) 
	            				?>
            				
				<?php
					}
				?>
					<?= Html::button('<span class="fa fa-trash"></span>', ['class' => 'btn btn-danger',
	                                    'data-toggle' => 'tooltip',
	                                    'onclick' => "eliminarDato('".$varIdList."')",
	                                    'title' => 'Eliminar Listado']) 
	            			?>
				</td> 
			</tr>
			<?php
				}
			?>
		
	</table>
</div>

<script type="text/javascript">
	function eliminarDato(params1){
		var varIdList = params1;
		var varSesiones = "<?php echo $txtSesiones; ?>";
		var varPCRC = "<?php echo $txtIdPcrc; ?>";

		console.log(varIdList);

	    var opcion = confirm("Confirmar la eliminación del item de la lista...");

	    if (opcion == true){
		 $.ajax({
	                method: "post",
			url: "eliminarvoc",
	                data : {
	                    var_Idlist: varIdList,
	                    var_Sesiones: varSesiones,
	                    var_Pcrc: varPCRC,
	                },
	                success : function(response){ 
				console.log(response);
				var respuesta = JSON.parse(response);
				console.log(respuesta);
				if(respuesta == 1){
					window.location.href = "../controlvoc/updatevoc?txtPcrc="+varPCRC;
				}else{
					alert("Error al intentar eliminar la alerta");
				}
	                }
	            });
	    }		
	};

	function menuPrincipal(){	
		var varPCRC = "<?php echo $txtIdPcrc; ?>";	
        	window.open('../controlvoc/indexvoc?arbol_idV='+varPCRC ,'_self');
    	};
</script>