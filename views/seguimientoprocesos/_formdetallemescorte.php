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
use \app\models\ControlProcesos;
use \app\models\ControlParams;
use kartik\export\ExportMenu;
use yii\db\Query;

$this->title = 'Vista Valorador';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $fechaActual = date("Y-m-d");

    		$sessiones = Yii::$app->user->identity->id;
			$varMes = date("n");
			$txtMes = null;
			switch ($varMes) {
				case '1':
					$txtMes = "Enero";
					break;
				case '2':
					$txtMes = "Febrero";
					break;
				case '3':
					$txtMes = "Marzo";
					break;
				case '4':
					$txtMes = "Abril";
					break;
				case '5':
					$txtMes = "Mayo";
					break;
				case '6':
					$txtMes = "Junio";
					break;
				case '7':
					$txtMes = "Julio";
					break;
				case '8':
					$txtMes = "Agosto";
					break;
				case '9':
					$txtMes = "Septiembre";
					break;
				case '10':
					$txtMes = "Octubre";
					break;
				case '11':
					$txtMes = "Noviembre";
					break;
				case '12':
					$txtMes = "Diciembre";
					break;
				default:
					# code...
					break;
			}

		$varCero = 0;
        	$txtcorte = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where anulado = 0 and responsable ='.$sessiones.' and tipo_corte like "%'.$txtMes.'%" and anulado ='.$varCero.'')->queryScalar();
        	$fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte' and anulado = 0")->queryScalar();
        	$fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte' and anulado = 0")->queryScalar(); 

?>

    <?= Html::a('Cerrar',  ['formglobal2'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
    ?>
    
    <button  class="btn btn-info" style="background-color: #4298B4" onclick="exportTableToExcel('tblData', 'Detalle Equipo Realizadas')">Exportar a Excel</button>
<br>

<div class="control-procesos-index">
<br>
    <table align="center" border="1" class="egt table table-hover table-striped table-bordered">
	<caption>Detalle</caption>
        <tr style="font-size:16px;">
            <th scope="col" class="text-center" ><strong>-- Detalle del Equipo - <?php echo $txtcorte; ?> --</strong></th>
        </tr>
    </table>  
<br>
  <table id="tblData" class="table table-striped table-hover table-bordered">  	
  <caption>Detalle</caption>
  	<?php
     	
  		$listData = $data;
        foreach ($listData as $key => $value) { 	
        	$varUsuaId = $value['evaluados_id'];

        	$querys2 =  new Query;
	        	$querys2 ->select(['date_format(tbl_ejecucionformularios.created, "%Y/%m/%d")  as fecha', 'count(tbl_ejecucionformularios.created) as total'])
	                    ->from('tbl_ejecucionformularios')
	                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
	                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
	                    ->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
	                    ->andwhere('tbl_usuarios.usua_id = '.$varUsuaId.'')
	                    //->andwhere( " tbl_usuarios.usua_id in ($varUsuaId)")
	                    ->groupBy('fecha');
	            	$command2 = $querys2->createCommand();
	            	$data2 = $command2->queryAll();	     


	        $querys3 =  new Query;
	        $querys3     ->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
	                    ->from('tbl_ejecucionformularios')
	                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
	                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
	                    ->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
	                    ->andwhere('tbl_usuarios.usua_id = '.$varUsuaId.'');
	                    
	        $command3 = $querys3->createCommand();
	        $queryss3 = $command3->queryAll();   

	        $query3 = count($queryss3);	            	       	
	            	

  	?>
  		<thead>
	  		<tr>
	  			<th scope="col" class="text-center" style="font-size:12px;" colspan="2"><?php echo $value['usua_nombre'];?></th>
	  		</tr>
	  		<tr>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Fechas') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Cantidad Realizadas') ?></th>
            </tr>
	  	</thead>
	  	<tbody>
	  		<?php
	  			$listado2 = $data2;
	  			foreach ($listado2 as $key => $value2) {
	  		?>
		  		<tr>
		  			<td class="text-center"><?php echo $varIdArbol = $value2['fecha'];?></td>
		  			<td class="text-center"><?php echo $varIdArbol = $value2['total'];?></td>
		  		</tr>
		  	<?php
		  		}
		  	?>
	  		<tr>
	  			<td class="text-center"><strong>Total Realizadas</strong></td>
	  			<td class="text-center" ><strong><?=  $query3; ?></strong></td>	  			
	  		</tr>
			<tr>
				<td class="text-center" colspan="2"><strong></strong></td>
			</tr>
	  	</tbody>
	<?php
		}
	?>
  </table>
</div>

<script type="text/javascript">
	function exportTableToExcel(tableID, filename = ''){
	    var downloadLink;
	    var dataType = 'application/vnd.ms-excel';
	    var tableSelect = document.getElementById(tableID);
	    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
	    
	    // Specify file name
	    filename = filename?filename+'.xls':'excel_data.xls';
	    
	    // Create download link element
	    downloadLink = document.createElement("a");
	    
	    document.body.appendChild(downloadLink);
	    
	    if(navigator.msSaveOrOpenBlob){
	        var blob = new Blob(['\ufeff', tableHTML], {
	            type: dataType
	        });
	        navigator.msSaveOrOpenBlob( blob, filename);
	    }else{
	        // Create a link to the file
	        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
	    
	        // Setting the file name
	        downloadLink.download = filename;
	        
	        //triggering the function
	        downloadLink.click();
	    }
	}
</script>