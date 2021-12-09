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

$this->title = 'Ver la Valoración';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $variantes = $nameVal;
    $varNametc = $txtNametc;
    $txtName = $varName;
    $txtCantidad = $varCant;
    $txtTotal = $varTotal;
?>
<br>
<div class="page-header" >
    <h3 style="text-align: center;"><?= Html::encode($this->title) ?></h3>
</div> 
<div class="control-procesos-index">
	<?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

		<?= $form->field($model, "evaluados_id")->textInput(['readonly' => 'readonly', 'id' => 'txtevaluados_id'])->label('Valorado') ?> 

		<?= $form->field($model, "Dedic_valora")->textInput(['readonly' => 'readonly', 'id' => 'txtdedic_valora'])->label('% de Dedicación Valorador') ?> 

		<?= $form->field($model, "cant_valor")->textInput(['readonly' => 'readonly', 'id' => 'txtcant_valor'])->label('Total de Valoraciones') ?> 

		<?= $form->field($model, "tipo_corte")->textInput(['readonly' => 'readonly', 'id' => 'txttipo_corte'])->label('Tipo Corte') ?> 

		<input value="<?php echo $nameVal ?>" id="txtiddelevaluado"  class="invisible">  
		&nbsp;&nbsp;

	<div align="center">  
	&nbsp;&nbsp;
		    <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Agregar Valorado']) 
    		?>
    &nbsp;&nbsp;
            <button  class="btn btn-info" style="background-color: #4298B4" onclick="exportTableToExcel('tblData', 'Detalle Vista Valorador')">Exportar a Excel</button>
	</div>

	    <?= GridView::widget([
	        'dataProvider' => $dataProvider,
	        //'filterModel' => $searchModel,
	        'columns' => [
	            [
	                'attribute' => 'PCRC',
	                'value' => 'arboles.name',
	            ],
	            [
	                'attribute' => 'Dimensiones',
	                'value' => 'dimensions',
	            ],
	            [
	                'attribute' => 'Cantidad de Valoraciones',
	                'value' => 'cant_valor',
	            ],
	            [
	                'attribute' => 'Justificación',
	                'value' => 'argumentos',
	            ],
	        ],
	    ]); 
	    ?>

    <?php $form->end() ?>
</div>
<br>
<div class="control-procesos-index">
    <?php 
        $txtcorte = Yii::$app->db->createCommand("select idtc from tbl_tipocortes where tipocortetc like '$varNametc'")->queryScalar();

        $txtcorte1 = Yii::$app->db->createCommand("select diastcs from tbl_tipos_cortes where cortetcs = 'Corte 1' and idtc = '$txtcorte'")->queryScalar();
        $txtfechaini1 = Yii::$app->db->createCommand("select fechainiciotcs from tbl_tipos_cortes where idtc = '$txtcorte' and diastcs like '%$txtcorte1%'")->queryScalar();
        $txtfechafin1 = Yii::$app->db->createCommand("select fechafintcs from tbl_tipos_cortes where idtc = '$txtcorte' and diastcs like '%$txtcorte1%'")->queryScalar();


        $txtcorte2 = Yii::$app->db->createCommand("select diastcs from tbl_tipos_cortes where cortetcs = 'Corte 2' and idtc = '$txtcorte'")->queryScalar();
        $txtfechaini2 = Yii::$app->db->createCommand("select fechainiciotcs from tbl_tipos_cortes where idtc = '$txtcorte' and diastcs like '%$txtcorte2%'")->queryScalar();
        $txtfechafin2 = Yii::$app->db->createCommand("select fechafintcs from tbl_tipos_cortes where idtc = '$txtcorte' and diastcs like '%$txtcorte2%'")->queryScalar();


        $txtcorte3 = Yii::$app->db->createCommand("select diastcs from tbl_tipos_cortes where cortetcs = 'Corte 3' and idtc = '$txtcorte'")->queryScalar();
        $txtfechaini3 = Yii::$app->db->createCommand("select fechainiciotcs from tbl_tipos_cortes where idtc = '$txtcorte' and diastcs like '%$txtcorte3%'")->queryScalar();
        $txtfechafin3 = Yii::$app->db->createCommand("select fechafintcs from tbl_tipos_cortes where idtc = '$txtcorte' and diastcs like '%$txtcorte3%'")->queryScalar();


        $txtcorte4 = Yii::$app->db->createCommand("select diastcs from tbl_tipos_cortes where cortetcs = 'Corte 4' and idtc = '$txtcorte'")->queryScalar();
        $txtfechaini4 = Yii::$app->db->createCommand("select fechainiciotcs from tbl_tipos_cortes where idtc = '$txtcorte' and diastcs like '%$txtcorte4%'")->queryScalar();
        $txtfechafin4 = Yii::$app->db->createCommand("select fechafintcs from tbl_tipos_cortes where idtc = '$txtcorte' and diastcs like '%$txtcorte4%'")->queryScalar();

    ?>

    <table align="center" border="1" class="egt table table-hover table-striped table-bordered">
        <caption>Tabla datos</caption>
    	<thead>
            <th scope="col" class="text-center" ><?= Yii::t('app', 'Corte 1') ?></th>
            <th scope="col" class="text-center" ><?= Yii::t('app', 'Corte 2') ?></th>
            <th scope="col" class="text-center" ><?= Yii::t('app', 'Corte 3') ?></th>
            <th scope="col" class="text-center" ><?= Yii::t('app', 'Corte 4') ?></th>    		
    	</thead>   
    	<tbody>
    		<td class="text-center" ><?php echo $txtcorte1; ?></td>
            <td class="text-center" ><?php echo $txtcorte2; ?></td>
            <td class="text-center" ><?php echo $txtcorte3; ?></td>
            <td class="text-center" ><?php echo $txtcorte4; ?></td>
    	</tbody> 	               
    </table>
</div>
<div style="display: none">
    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
        <caption>Tabla datos</caption>
        <thead>
            <tr>
                <th scope="col" class="text-center" ><?= Yii::t('app', 'Valorado') ?></th>
                <th scope="col" class="text-center" ><?= Yii::t('app', '% de Dedicacion Valorador') ?></th>
                <th scope="col" class="text-center" ><?= Yii::t('app', 'Total Valoraciones') ?></th>
                <th scope="col" class="text-center" ><?= Yii::t('app', 'Tipo de Corte') ?></th>  
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" ><?php echo $txtName; ?></td>
                <td class="text-center" ><?php echo $txtCantidad; ?></td>
                <td class="text-center" ><?php echo $varTotal; ?></td>
                <td class="text-center" ><?php echo $varNametc; ?></td>
            </tr>
            <tr>
                <th scope="col" class="text-center" ><?= Yii::t('app', 'PCRC') ?></th>
                <th scope="col" class="text-center" ><?= Yii::t('app', 'Dimensiones') ?></th>
                <th scope="col" class="text-center" ><?= Yii::t('app', 'Cantidad de Valoraciones') ?></th>
                <th scope="col" class="text-center" ><?= Yii::t('app', 'Justificacion') ?></th>  
            </tr>
            <?php
                $dataList = $dataProvider->getModels();

                foreach ($dataList as $key => $value) {
                    $txtPcrc1 = $value['arbol_id'];
                    $txtPcrc = Yii::$app->db->createCommand("select name from tbl_arbols where id = '$txtPcrc1'")->queryScalar();
                    $txtDimensiones = $value['dimensions'];
                    $txtCantidadVar = $value['cant_valor'];
                    $txtJustificacion = $value['argumentos'];
            ?>
                <tr>
                    <td class="text-center" ><?= $txtPcrc; ?></td>
                    <td class="text-center" ><?= $txtDimensiones; ?></td>
                    <td class="text-center" ><?= $txtCantidadVar; ?></td>
                    <td class="text-center" ><?= $txtJustificacion; ?></td>
                </tr>
            <?php } ?>
        </tbody>
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