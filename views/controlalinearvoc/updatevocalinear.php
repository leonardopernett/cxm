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

$this->title = 'ActualizaciÃ³n del Listado Instrumento Alinear + VOC';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $valor = null;
    $txtIdPcrc = $txtPcrc;
    $vartxtPcrc = $txtPcrc;
    $txtNamePcrc = Yii::$app->db->createCommand("select name from tbl_arbols where id = '$txtIdPcrc'")->queryScalar();

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

<?php
if ($txtSesiones == 1) {
    ?>
<div id="dtbloque2" class="col-sm-12">
	<br>
	<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
		<tr>
			<th class="text-center"><?= Yii::t('app', 'Id Categoria') ?></th>
			<th class="text-center"><?= Yii::t('app', 'Sesion') ?></th>
			<th class="text-center"><?= Yii::t('app', 'Nombre pcrc') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Nombre Categoria') ?></th>
			<th class="text-center"><?= Yii::t('app', '') ?></th>
		</tr>
		
			<?php
				$txtQuery2 =  new Query;
                $txtQuery2  ->select(['tbl_categorias_alinear.id_categ_ali', 'tbl_sesion_alinear.sesion_nombre', 'tbl_arbols.name', 'tbl_categorias_alinear.categoria_nombre'])->distinct()
                            ->from('tbl_categorias_alinear')                            
                            ->join('INNER JOIN', 'tbl_arbols',
                                   'tbl_arbols.id = tbl_categorias_alinear.arbol_id')
                            ->join('INNER JOIN', 'tbl_sesion_alinear',
                            	   'tbl_sesion_alinear.sesion_id = tbl_categorias_alinear.sesion_id')
							->where('tbl_categorias_alinear.arbol_id ='.$vartxtPcrc.'')
							->andwhere('tbl_categorias_alinear.anulado = 0');
                $command = $txtQuery2->createCommand();
                $dataProvider = $command->queryAll();
			
            
				foreach ($dataProvider as $key => $value) {
					
						$varIdcategoria = $value['id_categ_ali'];
                        $varNomsesion = $value['sesion_nombre'];
                        $varpcrc = $value['name'];
						$varcategorianom = $value['categoria_nombre'];
					
					
					
			?>
			<tr>
				<td class="text-center"><?php echo $varIdcategoria; ?></td>
				<td class="text-center"><?php echo $varNomsesion; ?></td>
				<td class="text-center"><?php echo $varpcrc; ?></td>                
				<td class="text-center"><?php echo $varcategorianom; ?></td>
				
					<td class="text-center">
						<?= Html::a('<img src="../../../web/images/ico-edit.png">',  ['editaralinearvoccat','var_idcat' => $varIdcategoria,'var_nombresesion' => $varNomsesion], ['class' => 'btn btn-primary',	                                    'data-toggle' => 'tooltip',
	                                    'title' => 'Modificar Listado']) 
	            		?>            				
				
					<?= Html::button('<span class="fa fa-trash"></span>', ['class' => 'btn btn-danger',
	                                    'data-toggle' => 'tooltip',
	                                    'onclick' => "eliminarDato('".$varIdcategoria."')",
	                                    'title' => 'Eliminar Listado']) 
	            			?>
				</td> 
			</tr>
			<?php
			  }
			?>
	</table>    
</div>
<?php
		}
	?>
<?php
if ($txtSesiones == 2) {
    ?>
<div id="dtbloque2" class="col-sm-12">
	<br>
	<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
		<tr>
			<th class="text-center"><?= Yii::t('app', 'Id Atributo') ?></th>
			<th class="text-center"><?= Yii::t('app', 'Id Categoria') ?></th>
			<th class="text-center"><?= Yii::t('app', 'Nombre Categoria') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Nombre Atributo') ?></th>
			<th class="text-center"><?= Yii::t('app', '') ?></th>
		</tr>
		
			<?php
			    $txtQuery2 =  new Query;
                $txtQuery2  ->select(['tbl_atributos_alinear.id_atrib_alin', 'tbl_categorias_alinear.id_categ_ali', 'tbl_categorias_alinear.categoria_nombre', 'tbl_atributos_alinear.atributo_nombre'])->distinct()
                            ->from('tbl_atributos_alinear')
                            ->join('INNER JOIN', 'tbl_categorias_alinear',
                                   'tbl_categorias_alinear.id_categ_ali = tbl_atributos_alinear.id_categ_ali')                            
                            ->join('INNER JOIN', 'tbl_arbols',
                                   'tbl_arbols.id = tbl_categorias_alinear.arbol_id')
                            ->join('INNER JOIN', 'tbl_sesion_alinear',
                            	   'tbl_sesion_alinear.sesion_id = tbl_categorias_alinear.sesion_id')
							->where('tbl_categorias_alinear.arbol_id ='.$vartxtPcrc.'')
							->andwhere('tbl_atributos_alinear.anulado = 0');
                $command = $txtQuery2->createCommand();
                $dataProvider = $command->queryAll();			
            
				foreach ($dataProvider as $key => $value) {
					
						$varIdatributo = $value['id_atrib_alin'];
                        			$varIdcategoria = $value['id_categ_ali'];
                        			$varcategorianom = $value['categoria_nombre'];
						$varatributonom = $value['atributo_nombre'];
					
			?>
			<tr>
				<td class="text-center"><?php echo $varIdatributo; ?></td>
				<td class="text-center"><?php echo $varIdcategoria; ?></td>
				<td class="text-center"><?php echo $varcategorianom ; ?></td>                
				<td class="text-center"><?php echo $varatributonom ; ?></td>
				
					<td class="text-center">
						<?= Html::a('<img src="../../../web/images/ico-edit.png">',  ['editaralinearvocatri','var_idatri' => $varIdatributo, 'var_nombrecateg' => $varcategorianom, 'var_idcateg' => $varIdcategoria], ['class' => 'btn btn-primary',
	                                    
					    'data-toggle' => 'tooltip',
	                                    'title' => 'Modificar Listado']) 
	            				?>
            				
				
					<?= Html::button('<span class="fa fa-trash"></span>', ['class' => 'btn btn-danger',
	                                    'data-toggle' => 'tooltip',
	                                    'onclick' => "eliminarDato('".$varIdatributo."')",
	                                    'title' => 'Eliminar Listado']) 
	            			?>
				</td> 
			</tr>	
		       <?php
			  }
			?>
	  </table>
    
    </div>
    <?php
		}
	?>
<script type="text/javascript">

	function eliminarDato(params1){
		var varIdList = params1;
		var varSesiones = "<?php echo $txtSesiones; ?>";
		var varPCRC = "<?php echo $txtIdPcrc; ?>";

	    var opcion = confirm("Confirmar la eliminación del item de la lista...");

	    if (opcion == true){
			if(varSesiones == 1){
				$.ajax({
							method: "post",
					url: "eliminaralinearvoccat",
							data : {
								var_Idlist: varIdList,
								var_Pcrc: varPCRC,
							},
							success : function(response){ 
						console.log(response);
						var respuesta = JSON.parse(response);
						console.log(respuesta);
						if(respuesta == 1){
							//window.location.href = "http://qa.allus.com.co/qa_managementv2/web/index.php/controlalinearvoc/updatevoc?txtPcrc="+varPCRC;
							//window.location.href='updatevocalinear?txtPcrc='+varPCRC'&varSession='+varSesiones;
							window.location.href='updatevocalinear?txtPcrc='+varPCRC+'&varSession='+varSesiones;
						}else{
							alert("Error al intentar eliminar la alerta");
						}
							}
						});
			} else {
				$.ajax({
							method: "post",
					url: "eliminaralinearvocatri",
							data : {
								var_Idlist: varIdList,
								var_Pcrc: varPCRC,
							},
							success : function(response){ 
						console.log(response);
						var respuesta = JSON.parse(response);
						console.log(respuesta);
						if(respuesta == 1){
							//window.location.href = "http://qa.allus.com.co/qa_managementv2/web/index.php/controlalinearvoc/updatevoc?txtPcrc="+varPCRC;
							window.location.href='updatevocalinear?txtPcrc='+varPCRC+'&varSession='+varSesiones;
						}else{
							alert("Error al intentar eliminar la alerta");
						}
							}
						});
			} 
	    }		
	};

	function menuPrincipal(){	
		var varPCRC = "<?php echo $txtIdPcrc; ?>";
		window.location.href='updatevoc?txtPcrc='+varPCRC;	
        	//window.open('http://qa.allus.com.co/qa_managementv2/web/index.php/controlalinearvoc/indexvoc?arbol_idV='+varPCRC ,'_self');
    	};
</script>