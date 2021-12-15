<?php

use yii\helpers\Html;
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

?>

    <?= Html::a('Cerrar',  ['formglobal'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
    ?>

    <button  class="btn btn-info" style="background-color: #4298B4" onclick="exportTableToExcel('tblData', 'Detalle Equipo Realizadas')">Exportar a Excel</button><br>

<div class="control-procesos-index">
<br>
    <table class="text-center" border="1" class="egt table table-hover table-striped table-bordered">
	<caption>Detalle</caption>
        <tr style="font-size:16px;">
            <th scope="col" class="text-center" ><strong>-- Detalle del Equipo - Mes Actual --</strong></th>
        </tr>
    </table>  
<br>
  <table id="tblData" class="table table-striped table-hover table-bordered">  	
  <caption>Detalle</caption>
  	<?php
  		$month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));			



        $fechainiC = date('Y-m-d 00:00:00', mktime(0,0,0, $month, 1, $year));
        
        $fechafinC = date('Y-m-d 23:59:59', mktime(0,0,0, $month, $day, $year)); 
        

  		$listData = $data;
        foreach ($listData as $key => $value) { 	
        	$varUsuaId = $value['evaluados_id'];

        	$querys =  new Query;
	        	$querys ->select(['count(tbl_ejecucionformularios.created) as total', 'date_format(tbl_ejecucionformularios.created, "%Y/%m/%d")  as fecha'])
	                    ->from('tbl_ejecucionformularios')
	                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
	                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
	                    ->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
	                    ->andwhere('tbl_usuarios.usua_id = '.$varUsuaId.'')
	                    //->andwhere( " tbl_usuarios.usua_id in ($varUsuaId)")
	                    ->groupBy('fecha');
	            	$command = $querys->createCommand();
	            	$data1 = $command->queryScalar(); 

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
	  			<th scope="col" class="text-center" style="font-size:12px;" colspan="3"><?php echo $value['usua_nombre'];?></th>
	  		</tr>
	  		<tr>
				<th scope="col" class="text-center"><?= Yii::t('app', 'Ver Historico Formulario') ?></th>
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
					<td><a href="../reportes/historicoformularios" target="_blank"><img src="../../../web/images/ico-view.png" style="cursor:hand" alt="image"></a></td>		
		  			<td class="text-center"><?php echo $varIdArbol = $value2['fecha'];?></td>
		  			<td class="text-center"><?php echo $varIdArbol = $value2['total'];?></td>
		  		</tr>
		  	<?php
		  		}
		  	?>
	  		<tr>
				<td class="text-center"><strong></strong></td>
	  			<td class="text-center"><strong>Total Realizadas</strong></td>
	  			<td class="text-center" ><strong><?=  $query3; ?></strong></td>	  			
	  		</tr>
			<tr>
				<td class="text-center" colspan="3"><strong></strong></td>
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