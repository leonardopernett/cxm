<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use app\models\ControlProcesosPlan;
use yii\db\Query;
use yii\helpers\ArrayHelper;

	$varRol = Yii::$app->db->createCommand("select r.role_nombre from tbl_roles r inner join rel_usuarios_roles ur on r.role_id = ur.rel_role_id  inner join tbl_usuarios u on ur.rel_usua_id = u.usua_id where u.usua_id = $varEvaluado")->queryScalar();

	$varNombre = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $varEvaluado")->queryScalar();

	$varIdtc = Yii::$app->db->createCommand("select idtc from tbl_control_procesos where anulado = 0 and id = $varId")->queryScalar();

	$varRango = Yii::$app->db->createCommand("select tipocortetc from tbl_tipocortes where anulado = 0 and idtc = $varIdtc")->queryScalar();

	$varFechas = Yii::$app->db->createCommand("select concat(fechainiciotc,' - ',fechafintc) from tbl_tipocortes where anulado = 0 and idtc = $varIdtc")->queryScalar();

	$varCorte1 = Yii::$app->db->createCommand("select diastcs from tbl_tipos_cortes where  idtc = $varIdtc and cortetcs in ('Corte 1')")->queryScalar();
	$varCorte2 = Yii::$app->db->createCommand("select diastcs from tbl_tipos_cortes where  idtc = $varIdtc and cortetcs in ('Corte 2')")->queryScalar();
	$varCorte3 = Yii::$app->db->createCommand("select diastcs from tbl_tipos_cortes where  idtc = $varIdtc and cortetcs in ('Corte 3')")->queryScalar();
	$varCorte4 = Yii::$app->db->createCommand("select diastcs from tbl_tipos_cortes where  idtc = $varIdtc and cortetcs in ('Corte 4')")->queryScalar();

	$varfechainicio = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where anulado = 0 and idtc = $varIdtc")->queryScalar();
	$varfechafin = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where anulado = 0 and idtc = $varIdtc")->queryScalar();

	$varListEscalamientos = Yii::$app->db->createCommand("select * from tbl_plan_escalamientos where anulado = 0 and tecnicolider = $varEvaluado and fechacreacion between '$varfechainicio' and '$varfechafin'")->queryAll();

	$varlistado = Yii::$app->db->createCommand("select * from tbl_control_params where anulado = 0 and evaluados_id = $varEvaluado and fechacreacion between '$varfechainicio' and '$varfechafin'")->queryAll();

	$varlistadimension = array();	
	$varlistpcrc = array();
	foreach ($varlistado as $key => $value) {
		$varDimen = $value['dimensions'];
		$varIddimen = Yii::$app->db->createCommand("select id from tbl_dimensions where name like '%$varDimen%'")->queryScalar();
		array_push($varlistadimension, $varIddimen);
		array_push($varlistpcrc, $value['arbol_id']);
	}
	$varlistdimensiones = implode(", ", $varlistadimension);
	$varlistarboles = implode(", ", $varlistpcrc);

	$varCorteInicio = Yii::$app->db->createCommand("select fechainiciotcs from tbl_tipos_cortes where  idtc = $varIdtc and cortetcs in ('Corte 1')")->queryScalar();

	$varListdias = Yii::$app->get('dbslave')->createCommand("select date_format(ef.created, '%Y/%m/%d')  as fecha, count(ef.created) as total  from tbl_ejecucionformularios ef inner join tbl_usuarios u on ef.usua_id = u.usua_id inner join tbl_dimensions d on ef.dimension_id = d.id where u.usua_id = $varEvaluado and d.id in ($varlistdimensiones) and ef.created between '$varCorteInicio 00:00:00' and '$varfechafin 23:59:59' and ef.arbol_id in ($varlistarboles) group by fecha")->queryAll();

	$varlistfecha = array();
	$varlisttotal = array();
	foreach ($varListdias as $key => $value) {
		array_push($varlistfecha, $value['fecha']);
		array_push($varlisttotal, $value['total']);
	}

?>
<div class="capaCero" style="display: inline">
    <a id="dlink" style="display:none;"></a>
    <button  class="btn btn-info" style="background-color: #4298B4" id="btn">Exportar a Excel</button>
</div>
<br>
<div style="display: none;">
	<table id="tblDatas" class="table table-striped table-bordered tblResDetFreed">
	<caption>...</caption>
		<thead>
			<tr>
				<th scope="col" class="text-center" colspan="17" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 15px;"><?= Yii::t('app', 'Seguimiento Equipo de Trabajo - CXM -') ?></label></th>
			</tr>
			<tr>
				<th scope="col" class="text-center" colspan="7" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 13px;"><?= Yii::t('app', 'Tecnico/Lider') ?></label></th>
				<th scope="col" class="text-center" colspan="5" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 13px;"><?= Yii::t('app', 'Rol') ?></label></th>
				<th scope="col" class="text-center" colspan="5" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 13px;"><?= Yii::t('app', 'Corte seleccionado') ?></label></th>
			</tr>
			<tr>
				<th scope="col" class="text-center" colspan="7" ><label style="font-size: 13px;"><?php echo $varNombre; ?></label></th>
				<th scope="col" class="text-center" colspan="5"><label style="font-size: 13px;"><?php echo $varRol; ?></label></th>
				<th scope="col" class="text-center" colspan="5"><label style="font-size: 13px;"><?php echo $varRango; ?></label></th>
			</tr>
			<tr>
				<th scope="col" class="text-center" colspan="17" style="background-color: #D3D3D3;"><label style="color: #FDFDFD;font-size: 15px;"><?= Yii::t('app', '') ?></label></th>
			</tr>
			<tr>
				<th scope="col" class="text-center" colspan="7" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 13px;"><?= Yii::t('app', 'Programa/PCRC') ?></label></th>
				<th scope="col" class="text-center" colspan="5" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 13px;"><?= Yii::t('app', 'Dimensiones') ?></label></th>
				<th scope="col" class="text-center" colspan="5" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 13px;"><?= Yii::t('app', 'Meta') ?></label></th>
			</tr>
			<?php 
                foreach ($varlistado as $key => $value) {    
                	$varArbol = $value['arbol_id'];
                	$varNombreArbol = Yii::$app->db->createCommand("select name from tbl_arbols where id = $varArbol")->queryScalar();
            ?>
                <tr>
                	<th scope="col" class="text-center" colspan="7" ><label style="font-size: 13px;"><?php echo $varNombreArbol; ?></label></th>
                	<th scope="col" class="text-center" colspan="5"><label style="font-size: 13px;"><?php echo $value['dimensions']; ?></label></th>
                	<th scope="col" class="text-center" colspan="5"><label style="font-size: 13px;"><?php echo $value['cant_valor']; ?></label></th>
                </tr>
            <?php 
               	}
            ?>
            <tr>
				<th scope="col" class="text-center" colspan="17" style="background-color: #D3D3D3;"><label style="color: #FDFDFD;font-size: 15px;"><?= Yii::t('app', '') ?></label></th>
			</tr>
			<tr>
				<th scope="col" class="text-center" colspan="17" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 15px;"><?php echo $varFechas; ?></label></th>
			</tr>
			<tr>
		        <th scope="col" class="text-center" style="background-color: #337ab7;">  </th>          
				<th scope="col" class="text-center" colspan="4" style="background-color: #337ab7;"><label style="color: #FDFDFD; font-size: 13px;"><?php echo $varCorte1; ?></label></th>
				<th scope="col" class="text-center" colspan="4" style="background-color: #337ab7;"><label style="color: #FDFDFD; font-size: 13px;"><?php echo $varCorte2; ?></label></th>
				<th scope="col" class="text-center" colspan="4" style="background-color: #337ab7;"><label style="color: #FDFDFD; font-size: 13px;"><?php echo $varCorte3; ?></label></th>
				<th scope="col" class="text-center" colspan="4" style="background-color: #337ab7;"><label style="color: #FDFDFD; font-size: 13px;"><?php echo $varCorte4; ?></label></th>
		    </tr>
		    <tr>
		        <th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', '-- PCRC --') ?></label></th>
				<th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', 'Meta') ?></label></th>
				 <th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', 'Realizadas') ?></label></th>
				 <th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', 'Gestión') ?></label></th>
				<th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', 'Cumplimiento') ?></label></th>
				<th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', 'Meta') ?></label></th>
				<th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', 'Realizadas') ?></label></th>
				<th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', 'Gestión') ?></label></th>
				<th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', 'Cumplimiento') ?></label></th>
				<th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', 'Meta') ?></label></th>
				<th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', 'Realizadas') ?></label></th>
				<th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', 'Gestión') ?></label></th>
				<th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', 'Cumplimiento') ?></label></th>
				<th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', 'Meta') ?></label></th>
				<th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', 'Realizadas') ?></label></th>
				<th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', 'Gestión') ?></label></th>
				<th scope="col" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 12px;"><?= Yii::t('app', 'Cumplimiento') ?></label></th>
		    </tr>
		    <?php 		              

									$varCorte1I = Yii::$app->db->createCommand("select fechainiciotcs from tbl_tipos_cortes where  idtc = $varIdtc and cortetcs in ('Corte 1')")->queryScalar();
									$txtfechaini1 = $varCorte1I.' 00:00:00';
									$varCorte1F = Yii::$app->db->createCommand("select fechafintcs from tbl_tipos_cortes where  idtc = $varIdtc and cortetcs in ('Corte 1')")->queryScalar();
									$txtfechafin1 = $varCorte1F.' 23:59:59';

									$varCorte2I = Yii::$app->db->createCommand("select fechainiciotcs from tbl_tipos_cortes where  idtc = $varIdtc and cortetcs in ('Corte 2')")->queryScalar();
									$txtfechaini2 = $varCorte2I.' 00:00:00';
									$varCorte2F = Yii::$app->db->createCommand("select fechafintcs from tbl_tipos_cortes where  idtc = $varIdtc and cortetcs in ('Corte 2')")->queryScalar();
									$txtfechafin2 = $varCorte2F.' 23:59:59';

									$varCorte3I = Yii::$app->db->createCommand("select fechainiciotcs from tbl_tipos_cortes where  idtc = $varIdtc and cortetcs in ('Corte 3')")->queryScalar();
									$txtfechaini3 = $varCorte3I.' 00:00:00';
									$varCorte3F = Yii::$app->db->createCommand("select fechafintcs from tbl_tipos_cortes where  idtc = $varIdtc and cortetcs in ('Corte 3')")->queryScalar();
									$txtfechafin3 = $varCorte3F.' 23:59:59';

									$varCorte4I = Yii::$app->db->createCommand("select fechainiciotcs from tbl_tipos_cortes where  idtc = $varIdtc and cortetcs in ('Corte 4')")->queryScalar();
									$txtfechaini4 = $varCorte4I.' 00:00:00';
									$varCorte4F = Yii::$app->db->createCommand("select fechafintcs from tbl_tipos_cortes where  idtc = $varIdtc and cortetcs in ('Corte 4')")->queryScalar();
									$txtfechafin4 = $varCorte4F.' 23:59:59';
	                			  			
									$txtcant = null;
									$varescalado = 0;
		                			foreach ($varlistado as $key => $value) {    
		                				$varArbol = $value['arbol_id'];
		                				$varNombreArbol = Yii::$app->db->createCommand("select name from tbl_arbols where id = $varArbol")->queryScalar();
		                				$vardimension = $value["dimensions"];
                						$iddimensions = Yii::$app->db->createCommand("select id from tbl_dimensions where name like '%$vardimension%'")->queryScalar();

                						$varMetas = $value["cant_valor"];
                						$varrtaMeta = round($varMetas / 4);

                						$querys1 =  new Query;
						                $querys1     ->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
						                            ->from('tbl_ejecucionformularios')
						                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
						                                    'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
						                            ->where(['between','tbl_ejecucionformularios.created', $txtfechaini1, $txtfechafin1])
						                            ->andwhere(['in','tbl_ejecucionformularios.dimension_id',[$iddimensions]])
						                            ->andwhere(['in','tbl_ejecucionformularios.arbol_id',[$varArbol]])
						                            ->andwhere('tbl_usuarios.usua_id = '.$varEvaluado.'');                            
						                $command1 = $querys1->createCommand();
						                $queryss1 = $command1->queryAll();   

						                $query1 = count($queryss1);

										if($varrtaMeta != 0){
								                $varContar1 = round(($query1 / $varrtaMeta) * 100);
										}
										else
										{$varContar1 = 0;}

						                $querys2 =  new Query;
						                $querys2     ->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
						                            ->from('tbl_ejecucionformularios')
						                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
						                                    'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
						                            ->where(['between','tbl_ejecucionformularios.created', $txtfechaini2, $txtfechafin2])
						                            ->andwhere(['in','tbl_ejecucionformularios.dimension_id',[$iddimensions]])
						                            ->andwhere(['in','tbl_ejecucionformularios.arbol_id',[$varArbol]])
						                            ->andwhere('tbl_usuarios.usua_id = '.$varEvaluado.'');                            
						                $command2 = $querys2->createCommand();
						                $queryss2 = $command2->queryAll();   

						                $query2 = count($queryss2);

										if($varrtaMeta != 0){
								                $varContar2 = round(($query2 / $varrtaMeta) * 100);
										}
										else
										{$varContar2 = 0;}		

						                $querys3 =  new Query;
						                $querys3     ->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
						                            ->from('tbl_ejecucionformularios')
						                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
						                                    'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
						                            ->where(['between','tbl_ejecucionformularios.created', $txtfechaini3, $txtfechafin3])
						                            ->andwhere(['in','tbl_ejecucionformularios.dimension_id',[$iddimensions]])
						                            ->andwhere(['in','tbl_ejecucionformularios.arbol_id',[$varArbol]])
						                            ->andwhere('tbl_usuarios.usua_id = '.$varEvaluado.'');                            
						                $command3 = $querys3->createCommand();
						                $queryss3 = $command3->queryAll();   

						                $query3 = count($queryss3);

										if($varrtaMeta != 0){
								                $varContar3 = round(($query3 / $varrtaMeta) * 100);  
										}
										else
										{$varContar3 = 0;}

						                $querys4 =  new Query;
						                $querys4     ->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
						                            ->from('tbl_ejecucionformularios')
						                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
						                                    'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
						                            ->where(['between','tbl_ejecucionformularios.created', $txtfechaini4, $txtfechafin4])
						                            ->andwhere(['in','tbl_ejecucionformularios.dimension_id',[$iddimensions]])
						                            ->andwhere(['in','tbl_ejecucionformularios.arbol_id',[$varArbol]])
						                            ->andwhere('tbl_usuarios.usua_id = '.$varEvaluado.'');                            
						                $command4 = $querys4->createCommand();
						                $queryss4 = $command4->queryAll();   

						                $query4 = count($queryss4);

										if($varrtaMeta != 0){
								                $varContar4 = round(($query4 / $varrtaMeta) * 100); 
										}
										else
										{$varContar4 = 0;}

										$txtcant = $txtcant + 1;  						                
			?>
				<tr>
	      			<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $varNombreArbol; ?></label></th>
					<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $varrtaMeta; ?></label></th>
					<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $query1; ?></label></th>
					<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $varescalado; ?></label></th>
					<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $varContar1; ?> %</label></th>
					<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $varrtaMeta; ?></label></th>
					<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $query2; ?></label></th>
					<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $varescalado; ?></label></th>
					<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $varContar2; ?> %</label></th>
					<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $varrtaMeta; ?></label></th>
					<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $query3; ?></label></th>
					<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $varescalado; ?></label></th>
					<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $varContar3; ?> %</label></th>
					<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $varrtaMeta; ?></label></th>
					<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $query4; ?></label></th>
					<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $varescalado; ?></label></th>
					<th scope="col" class="text-center"  ><label style="font-size: 12px;"><?php echo  $varContar4; ?> %</label></th>
				</tr>
		    <?php 
		        }
		    ?>
		    <tr>
				<th scope="col" class="text-center" colspan="17" style="background-color: #D3D3D3;"><label style="color: #FDFDFD;font-size: 15px;"><?= Yii::t('app', '') ?></label></th>
			</tr>
			<tr>
				<th scope="col" class="text-center" colspan="17" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 15px;"><?= Yii::t('app', 'Datos de las valoraciones realizadas') ?></label></th>
			</tr>
			<tr>
				<th scope="col" class="text-center" colspan="10" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 15px;"><?= Yii::t('app', 'Fechas de valoracione realizadas') ?></label></th>
				<th scope="col" class="text-center" colspan="7" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 15px;"><?= Yii::t('app', 'Cantidad de valoraciones realizadas') ?></label></th>
			</tr>
			<?php
				foreach ($varListdias as $key => $value) {					
			?>
				<tr>
	      			<th scope="col" class="text-center" colspan="10" ><label style="font-size: 12px;"><?php echo  $value['fecha']; ?></label></th>
	      			<th scope="col" class="text-center" colspan="7" ><label style="font-size: 12px;"><?php echo  $value['total']; ?></label></th>
	      		</tr>
			<?php
				}
			?>
			<tr>
				<th scope="col" class="text-center" colspan="17" style="background-color: #D3D3D3;"><label style="color: #FDFDFD;font-size: 15px;"><?= Yii::t('app', '') ?></label></th>
			</tr>
			<tr>
				<th scope="col" class="text-center" colspan="17" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 15px;"><?= Yii::t('app', 'Escalamientos') ?></label></th>
			</tr>
			<tr>
				<th scope="col" class="text-center" colspan="3" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 13px;"><?php echo "Corte"; ?></label></th>
    			<th scope="col" class="text-center" colspan="3" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 13px;"><?php echo "Tipo Corte"; ?></label></th>
    			<th scope="col" class="text-center" colspan="4" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 13px;"><?php echo "Justificacion"; ?></label></th>
    			<th scope="col" class="text-center" colspan="4" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 13px;"><?php echo "Comentarios"; ?></label></th>
    			<th scope="col" class="text-center" colspan="3" style="background-color: #337ab7;"><label style="color: #FDFDFD;font-size: 13px;"><?php echo "Estado"; ?></label></th> 
			</tr>
    		<?php
    			foreach ($varListEscalamientos as $key => $value) {
    					$varidtcts = $value['idtcs'];
    					$vanameidtcs = Yii::$app->db->createCommand("select diastcs from tbl_tipos_cortes where idtcs = $varidtcts")->queryScalar();
    					$varEstado = $value['Estado'];
    					$txtestado = null;
    					if ($varEstado == 0) {
    						$txtestado = "Abierto";
    					}else{
    						if ($varEstado == 1) {
    							$txtestado = "Aprobado";
    						}else{
    							if ($varEstado == 2) {
    								$txtestado = "No Aprobado";
    							}
    						}
    					}
    		?>
    			<tr>
	      			<th scope="col" class="text-center" colspan="3" ><label style="font-size: 12px;"><?php echo  $varRango; ?></label></th>
	      			<th scope="col" class="text-center" colspan="3" ><label style="font-size: 12px;"><?php echo  $vanameidtcs; ?></label></th>
	      			<th scope="col" class="text-center" colspan="4" ><label style="font-size: 12px;"><?php echo  $value['justificacion']; ?></label></th>
	      			<th scope="col" class="text-center" colspan="4" ><label style="font-size: 12px;"><?php echo  $value['comentarios']; ?></label></th>
	      			<th scope="col" class="text-center" colspan="3" ><label style="font-size: 12px;"><?php echo  $txtestado; ?></label></th>
	      		</tr>
    		<?php
    			}
    		?>			
		</thead>
	</table>
</div>
<script type="text/javascript" charset="UTF-8">
var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta charset="utf-8"/><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64 = function (s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            }, format = function (s, c) {
                return s.replace(/{(\w+)}/g, function (m, p) {
                    return c[p];
                })
            }
        return function (table, name) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = {
                worksheet: name || 'Worksheet',
                table: table.innerHTML
            }
            console.log(uri + base64(format(template, ctx)));
            document.getElementById("dlink").href = uri + base64(format(template, ctx));
            document.getElementById("dlink").download = "Seguimiento Equipo de trabajo";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblDatas', 'Archivo Seguimiento', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>