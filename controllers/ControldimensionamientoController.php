<?php

namespace app\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\db\Query;
use app\models\Controldimensionar;
use app\models\Controldimensionamiento;

	class ControldimensionamientoController extends \yii\web\Controller {

		public function behaviors(){
			return[
				'access' => [
						'class' => AccessControl::classname(),
						'only' => ['index','createdimensionar','updatedimensionar','enviararchivo'],
						'rules' => [
							[
								'allow' => true,
								'roles' => ['@'],
								'matchCallback' => function() {
                            return Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo();
                        },
							],
						]
					],
				'verbs' => [					
					'class' => VerbFilter::className(),
					'actions' => [
						'delete' => ['post'],
					],
				],
			];
		}
		
		public function actionIndex(){
			$model = new Controldimensionamiento();
			$varListresult = null;
			$yearActual = date("Y");
			$varListresult = null;
			$varusuario = Yii::$app->user->identity->id;

			$form = Yii::$app->request->post();
			if($model->load($form)){
				
				$varmes = $model->month;

				$rol =  new Query;
			    $rol     ->select(['tbl_roles.role_id'])
			                ->from('tbl_roles')
			                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
			                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
			                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
			                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
			                ->where('tbl_usuarios.usua_id = '.$varusuario.'');                    
			    $command = $rol->createCommand();
			    $roles = $command->queryScalar();

			    if ($roles == '270' || $roles == '309') {
			    	$varListresult = Yii::$app->db->createCommand("SELECT u.usua_nombre, cd.year 'Annio', cd.month 'Mes', cd.cant_valor 'CantValor', cd.tiempo_llamada 'TiempoLlamada', cd.tiempoadicional 'TiempoAdicional', cd.actuales 'TecnicosActua', cd.otras_actividad 'OtrasActivi', cd.turno_promedio 'TurnoPromedio', cd.ausentismo 'Ausentismos', cd.vaca_permi_licen 'Vacaciones', c.duracion_ponde, c.ocupacion, c.carga_trabajo, c.horasCNX, c.uti_gentes, c.horas_nomina_monit, c.horas_laboral_mes, c.FTE, c.p_monit, c.p_otras_actividad, c.personas, c.pnas_vacaciones, c.pnas_ausentismo, c.exceso_deficit FROM tbl_control_dimensionar c INNER JOIN tbl_control_dimensionamiento cd ON  c.iddimensionamiento = cd.iddimensionamiento INNER JOIN tbl_usuarios u ON u.usua_id = cd.usua_id WHERE 	cd.anulado = 0 AND cd.year = '$yearActual' AND cd.month = '$varmes'")->queryAll();
			    }else{
			    	$varListresult = Yii::$app->db->createCommand("SELECT u.usua_nombre, cd.year 'Annio', cd.month 'Mes', cd.cant_valor 'CantValor', cd.tiempo_llamada 'TiempoLlamada', cd.tiempoadicional 'TiempoAdicional', cd.actuales 'TecnicosActua', cd.otras_actividad 'OtrasActivi', cd.turno_promedio 'TurnoPromedio', cd.ausentismo 'Ausentismos', cd.vaca_permi_licen 'Vacaciones', c.duracion_ponde, c.ocupacion, c.carga_trabajo, c.horasCNX, c.uti_gentes, c.horas_nomina_monit, c.horas_laboral_mes, c.FTE, c.p_monit, c.p_otras_actividad, c.personas, c.pnas_vacaciones, c.pnas_ausentismo, c.exceso_deficit FROM tbl_control_dimensionar c INNER JOIN tbl_control_dimensionamiento cd ON  c.iddimensionamiento = cd.iddimensionamiento INNER JOIN tbl_usuarios u ON u.usua_id = cd.usua_id WHERE 	cd.anulado = 0 AND cd.usua_id = '$varusuario' AND cd.year = '$yearActual' AND cd.month = '$varmes'")->queryAll();
			    }
				 
			}else{				
				$MesActual = date("Y-m-d");
				$MesActual1 = date("Y-m-d",strtotime($MesActual."+ 1 month"));
				$MesActual2 = date("m",strtotime($MesActual1));

				$varmonth = null;
			      if ($MesActual2 == '01') {
			        $varmonth = "Enero";
			      }
			      if ($MesActual2 == '02') {
			        $varmonth = "Febrero";
			      }
			      if ($MesActual2 == '03') {
			        $varmonth = "Marzo";
			      }
			      if ($MesActual2 == '04') {
			        $varmonth = "Abril";
			      }
			      if ($MesActual2 == '05') {
			        $varmonth = "Mayo";
			      }
			      if ($MesActual2 == '06') {
			        $varmonth = "Junio";
			      }
			      if ($MesActual2 == '07') {
			        $varmonth = "Julio";
			      }
			      if ($MesActual2 == '08') {
			        $varmonth = "Agosto";
			      }
			      if ($MesActual2 == '09') {
			        $varmonth = "Septiembre";
			      }
			      if ($MesActual2 == '10') {
			        $varmonth = "Octubre";
			      }
			      if ($MesActual2 == '11') {
			        $varmonth = "Noviembre";
			      }
			      if ($MesActual2 == '12') {
			        $varmonth = "Diciembre";
			      }

			      $varListresult = Yii::$app->db->createCommand("SELECT u.usua_nombre, cd.year 'Annio', cd.month 'Mes', cd.cant_valor 'CantValor', cd.tiempo_llamada 'TiempoLlamada', cd.tiempoadicional 'TiempoAdicional', cd.actuales 'TecnicosActua', cd.otras_actividad 'OtrasActivi', cd.turno_promedio 'TurnoPromedio', cd.ausentismo 'Ausentismos', cd.vaca_permi_licen 'Vacaciones', c.duracion_ponde, c.ocupacion, c.carga_trabajo, c.horasCNX, c.uti_gentes, c.horas_nomina_monit, c.horas_laboral_mes, c.FTE, c.p_monit, c.p_otras_actividad, c.personas, c.pnas_vacaciones, c.pnas_ausentismo, c.exceso_deficit FROM tbl_control_dimensionar c INNER JOIN tbl_control_dimensionamiento cd ON  c.iddimensionamiento = cd.iddimensionamiento INNER JOIN tbl_usuarios u ON u.usua_id = cd.usua_id WHERE 	cd.anulado = 0 AND cd.usua_id = '$varusuario' AND cd.year = '$yearActual' AND cd.month = '$varmonth'")->queryAll();
			}

			return $this->render('index',[
				'model' => $model,
				'varListresult' => $varListresult,
			]);
		}


		public function actionCreatedimensionar(){	
			$model = new Controldimensionamiento();	

			$form = Yii::$app->request->post();
			if($model->load($form)){
				$txtusuaid = Yii::$app->user->identity->id;
				$txtyear  = $model->year;
				$txtmonth  = $model->month;
				$txtcantvalor  = $model->cant_valor;
				$txttiempollamada  = $model->tiempo_llamada;
				$txttiempoadicional  = $model->tiempoadicional;
				$txtactuales  = $model->actuales;
				$txtotrasactividad  = $model->otras_actividad;
				$txtturnopromedio  = $model->turno_promedio;
				$txtausentismo  = $model->ausentismo;
				$txtvacapermilicen  = $model->vaca_permi_licen;

				$varConteo = Yii::$app->db->createCommand("SELECT COUNT(cd.usua_id) FROM tbl_control_dimensionamiento cd WHERE cd.anulado = 0  AND cd.usua_id = $txtusuaid AND cd.year = '$txtyear' AND cd.month = '$txtmonth'")->queryScalar(); 

				if ($varConteo == 0) {
					Yii::$app->db->createCommand()->insert('tbl_control_dimensionamiento',[
	                                'usua_id' => $txtusuaid,
	                                'year' => $txtyear,
	                                'month' => $txtmonth,
	                                'cant_valor' => $txtcantvalor,
	                                'tiempo_llamada' => $txttiempollamada,
	                                'tiempoadicional' => $txttiempoadicional,
	                                'actuales' => $txtactuales,
	                                'otras_actividad' => $txtotrasactividad,
	                                'turno_promedio' => $txtturnopromedio,
	                                'ausentismo' => $txtausentismo,
	                                'vaca_permi_licen' => $txtvacapermilicen,
	                                'fechacreacion' => date("Y-m-d"),
	                                'anulado' => 0,
	                            ])->execute(); 

					$txtdimensionId = Yii::$app->db->createCommand("select iddimensionamiento from tbl_control_dimensionamiento where usua_id = $txtusuaid and year = '$txtyear' and month = '$txtmonth' and anulado = 0")->queryScalar(); 

					$txtduracion_ponde = $txttiempollamada + $txttiempoadicional;
		            $txtocupacion = 80 / 100;
		            $txtcarga_trabajo = $txtcantvalor * $txtduracion_ponde  / 3600;
		            $txthorasCNX = $txtcarga_trabajo / $txtocupacion;
		            $txtuti_gentes = 92 / 100;
		            $txthoras_nomina_monit = $txthorasCNX  / $txtuti_gentes;
		            $txthoras_laboral_mes = 192;
		            $txtFTE = $txthoras_nomina_monit / $txthoras_laboral_mes;
		            $txtp_monit = ($txtFTE * 48 / $txtturnopromedio) * (1 + ($txtausentismo / 100));
		            $txtp_otras_actividad = ($txthoras_nomina_monit *  ($txtotrasactividad / 100) / $txthoras_laboral_mes) * (48 / $txtturnopromedio);
		            $txtpersonas = $txtp_monit + $txtp_otras_actividad;	            
		            $txtpnas_vacaciones = $txtp_monit * ($txtvacapermilicen / 100);
		            $txtpnas_ausentismo = ($txtausentismo / 100) * $txtp_monit;
		            $txtexceso_deficit = $txtactuales - $txtpersonas;


		            Yii::$app->db->createCommand()->insert('tbl_control_dimensionar',[
	                                'iddimensionamiento' => $txtdimensionId,
	                                'duracion_ponde' => $txtduracion_ponde,
	                                'ocupacion' => $txtocupacion,
	                                'carga_trabajo' => $txtcarga_trabajo,
	                                'horasCNX' => $txthorasCNX,
	                                'uti_gentes' => $txtuti_gentes,
	                                'horas_nomina_monit' => $txthoras_nomina_monit,
	                                'horas_laboral_mes' => $txthoras_laboral_mes,
	                                'FTE' => $txtFTE,
	                                'p_monit' => $txtp_monit,
	                                'p_otras_actividad' => $txtp_otras_actividad,
	                                'personas' => $txtpersonas,
	                                'pnas_vacaciones' => $txtpnas_vacaciones,
	                                'pnas_ausentismo' => $txtpnas_ausentismo,
	                                'exceso_deficit' => $txtexceso_deficit,
	                                'fechacreacion' => date("Y-m-d"),
	                                'anulado' => 0,
	                            ])->execute(); 

				}

				return $this->redirect('index');
			}else{
				#code
			}
			
			return $this->renderAjax('creardimensionamiento',[
				'model' => $model,
			]);
		}

		public function actionUpdatedimensionar(){	
			$model = new Controldimensionamiento();

			$form = Yii::$app->request->post();
			if($model->load($form)){
				$varsession = Yii::$app->user->identity->id;
				$varyear = date("Y");
				$varmonth = $model->month;

				$varconteoresult = Yii::$app->db->createCommand("SELECT count(cd.iddimensionamiento) FROM tbl_control_dimensionamiento cd WHERE cd.anulado = 0 AND cd.usua_id = $varsession AND cd.year = '$varyear' AND cd.month = '$varmonth'")->queryScalar();

				if ($varconteoresult != 0) {
					return $this->redirect(array('actualizadimensiona','varmonth'=>$varmonth));
				}else{
					return $this->redirect('index');
				}				
			}else{
				#code
			}

			return $this->renderAjax('updatedimensionar',[
				'model' => $model,
			]);
		}

		public function actionActualizadimensiona($varmonth){
			$varsession = Yii::$app->user->identity->id;
			$varyear = date("Y");
			$varmes = $varmonth;
			$model = new Controldimensionamiento();

			$varlistaresult = Yii::$app->db->createCommand("SELECT * FROM tbl_control_dimensionamiento cd WHERE cd.anulado = 0 AND cd.usua_id = $varsession AND cd.year = '$varyear' AND cd.month = '$varmes'")->queryAll();

			$variddimensiona = Yii::$app->db->createCommand("SELECT DISTINCT cd.iddimensionamiento FROM tbl_control_dimensionamiento cd WHERE cd.anulado = 0 AND cd.usua_id = $varsession AND cd.year = '$varyear' AND cd.month = '$varmes'")->queryScalar();

			$form = Yii::$app->request->post();
			if($model->load($form)){

				Yii::$app->db->createCommand("DELETE FROM tbl_control_dimensionamiento WHERE anulado = 0 AND iddimensionamiento = '$variddimensiona'")->execute();

				Yii::$app->db->createCommand("DELETE FROM tbl_control_dimensionar WHERE anulado = 0 AND iddimensionamiento = '$variddimensiona'")->execute();


				$txtusuaid = Yii::$app->user->identity->id;
				$txtyear  = $model->year;
				$txtmonth  = $model->month;
				$txtcantvalor  = $model->cant_valor;
				$txttiempollamada  = $model->tiempo_llamada;
				$txttiempoadicional  = $model->tiempoadicional;
				$txtactuales  = $model->actuales;
				$txtotrasactividad  = $model->otras_actividad;
				$txtturnopromedio  = $model->turno_promedio;
				$txtausentismo  = $model->ausentismo;
				$txtvacapermilicen  = $model->vaca_permi_licen;

				$varConteo = Yii::$app->db->createCommand("SELECT COUNT(cd.usua_id) FROM tbl_control_dimensionamiento cd WHERE cd.anulado = 0  AND cd.usua_id = $txtusuaid AND cd.year = '$txtyear' AND cd.month = '$txtmonth'")->queryScalar(); 

				if ($varConteo == 0) {
					Yii::$app->db->createCommand()->insert('tbl_control_dimensionamiento',[
	                                'usua_id' => $txtusuaid,
	                                'year' => $txtyear,
	                                'month' => $txtmonth,
	                                'cant_valor' => $txtcantvalor,
	                                'tiempo_llamada' => $txttiempollamada,
	                                'tiempoadicional' => $txttiempoadicional,
	                                'actuales' => $txtactuales,
	                                'otras_actividad' => $txtotrasactividad,
	                                'turno_promedio' => $txtturnopromedio,
	                                'ausentismo' => $txtausentismo,
	                                'vaca_permi_licen' => $txtvacapermilicen,
	                                'fechacreacion' => date("Y-m-d"),
	                                'anulado' => 0,
	                            ])->execute(); 

					$txtdimensionId = Yii::$app->db->createCommand("select iddimensionamiento from tbl_control_dimensionamiento where usua_id = $txtusuaid and year = '$txtyear' and month = '$txtmonth' and anulado = 0")->queryScalar(); 

					$txtduracion_ponde = $txttiempollamada + $txttiempoadicional;
		            $txtocupacion = 80 / 100;
		            $txtcarga_trabajo = $txtcantvalor * $txtduracion_ponde  / 3600;
		            $txthorasCNX = $txtcarga_trabajo / $txtocupacion;
		            $txtuti_gentes = 92 / 100;
		            $txthoras_nomina_monit = $txthorasCNX  / $txtuti_gentes;
		            $txthoras_laboral_mes = 192;
		            $txtFTE = $txthoras_nomina_monit / $txthoras_laboral_mes;
		            $txtp_monit = ($txtFTE * 48 / $txtturnopromedio) * (1 + ($txtausentismo / 100));
		            $txtp_otras_actividad = ($txthoras_nomina_monit *  ($txtotrasactividad / 100) / $txthoras_laboral_mes) * (48 / $txtturnopromedio);
		            $txtpersonas = $txtp_monit + $txtp_otras_actividad;	            
		            $txtpnas_vacaciones = $txtp_monit * ($txtvacapermilicen / 100);
		            $txtpnas_ausentismo = ($txtausentismo / 100) * $txtp_monit;
		            $txtexceso_deficit = $txtactuales - $txtpersonas;


		            Yii::$app->db->createCommand()->insert('tbl_control_dimensionar',[
	                                'iddimensionamiento' => $txtdimensionId,
	                                'duracion_ponde' => $txtduracion_ponde,
	                                'ocupacion' => $txtocupacion,
	                                'carga_trabajo' => $txtcarga_trabajo,
	                                'horasCNX' => $txthorasCNX,
	                                'uti_gentes' => $txtuti_gentes,
	                                'horas_nomina_monit' => $txthoras_nomina_monit,
	                                'horas_laboral_mes' => $txthoras_laboral_mes,
	                                'FTE' => $txtFTE,
	                                'p_monit' => $txtp_monit,
	                                'p_otras_actividad' => $txtp_otras_actividad,
	                                'personas' => $txtpersonas,
	                                'pnas_vacaciones' => $txtpnas_vacaciones,
	                                'pnas_ausentismo' => $txtpnas_ausentismo,
	                                'exceso_deficit' => $txtexceso_deficit,
	                                'fechacreacion' => date("Y-m-d"),
	                                'anulado' => 0,
	                            ])->execute(); 

				}

				return $this->redirect('index');

			}else{
				#code
			}


			return $this->render('actualizadimensiona',[
				'model' => $model,
				'varlistaresult' => $varlistaresult,
			]);
		}

		public function actionEnviararchivo(){			
			$model = new Controldimensionamiento();
			$yearActual = date("Y");
			$varusuario = Yii::$app->user->identity->id;

			$form = Yii::$app->request->post();
			if($model->load($form)){
				$varcorreo = $model->month;

				$varListresult = Yii::$app->db->createCommand("SELECT u.usua_nombre, cd.year 'Annio', cd.month 'Mes', cd.cant_valor 'CantValor', cd.tiempo_llamada 'TiempoLlamada', cd.tiempoadicional 'TiempoAdicional', cd.actuales 'TecnicosActua', cd.otras_actividad 'OtrasActivi', cd.turno_promedio 'TurnoPromedio', cd.ausentismo 'Ausentismos', cd.vaca_permi_licen 'Vacaciones', c.duracion_ponde, c.ocupacion, c.carga_trabajo, c.horasCNX, c.uti_gentes, c.horas_nomina_monit, c.horas_laboral_mes, c.FTE, c.p_monit, c.p_otras_actividad, c.personas, c.pnas_vacaciones, c.pnas_ausentismo, c.exceso_deficit FROM tbl_control_dimensionar c INNER JOIN tbl_control_dimensionamiento cd ON  c.iddimensionamiento = cd.iddimensionamiento INNER JOIN tbl_usuarios u ON u.usua_id = cd.usua_id WHERE 	cd.anulado = 0 AND cd.usua_id = '$varusuario' AND cd.year = '$yearActual'")->queryAll();


				$phpExc = new \PHPExcel();
          		$phpExc->getProperties()
                  ->setCreator("Konecta")
                  ->setLastModifiedBy("Konecta")
                  ->setTitle("Archivo Control de dimensionamiento")
                  ->setSubject("Archivo Control de dimensionamiento")
                  ->setDescription("Este archivo contiene el proceso del control de dimensionamiento")
                  ->setKeywords("Archivo Control de dimensionamiento");
          		$phpExc->setActiveSheetIndex(0);

          		$phpExc->getActiveSheet()->setShowGridlines(False);

          		$styleArray = array(
                  'alignment' => array(
                      'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                  ),
              	);

          		$styleColor = array( 
                  'fill' => array( 
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                      'color' => array('rgb' => '28559B'),
                  )
              	);

          		$styleArrayTitle = array(
                  'font' => array(
                    'bold' => false,
                    'color' => array('rgb' => 'FFFFFF')
                  )
              	);

          		$styleArraySubTitle = array(              
                  'fill' => array( 
                          'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                          'color' => array('rgb' => '4298B5'),
                  )
              	);


          		// ARRAY STYLE FONT COLOR AND TEXT ALIGN CENTER
          		$styleArrayBody = array(
                  'font' => array(
                      'bold' => false,
                      'color' => array('rgb' => '2F4F4F')
                  ),
                  'borders' => array(
                      'allborders' => array(
                          'style' => \PHPExcel_Style_Border::BORDER_THIN,
                          'color' => array('rgb' => 'DDDDDD')
                      )
                  )
              	);

		        $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);

		        $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
		        $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
		        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
		        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
		        $phpExc->setActiveSheetIndex(0)->mergeCells('A1:D1');

		        $numCell = 3;
		        foreach ($varListresult as $key => $value) {

		        	$phpExc->getActiveSheet()->SetCellValue('A2','Dimensionamiento creado por: '.$value['usua_nombre'].' - Fecha del Dimensionamiento: '.$value['Mes'].' - '.$value['Annio']);
			        $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);
			        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArrayTitle);
			        $phpExc->setActiveSheetIndex(0)->mergeCells('A2:D2');

			        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'Valoraciones al mes');
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['CantValor']);

			        $phpExc->getActiveSheet()->SetCellValue('C'.$numCell,'Duracion llamadas muestreo (En segundos)');
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $value['TiempoLlamada'].' (Segundos)');

			        $numCell = $numCell + 1;
			        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'Tiempo adicional al muestreo (En segundos)');
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['TiempoAdicional'].' (Segundos)');

			        $phpExc->getActiveSheet()->SetCellValue('C'.$numCell,'Tecnicos Cx actuales (incluye encargos y oficiales)');
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $value['TecnicosActua']);

			        $numCell = $numCell + 1;
			        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'%  del tiempo de tecnico que invierte a en otras actividades');
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['OtrasActivi'].'%');

			        $phpExc->getActiveSheet()->SetCellValue('C'.$numCell,'Turno Promedio en la semana del tecnico');
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $value['TurnoPromedio']);

			        $numCell = $numCell + 1;
			        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'% Ausentismo');
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['Ausentismos'].'%');

			        $phpExc->getActiveSheet()->SetCellValue('C'.$numCell,'% Vacaciones, permisos y licencias');
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $value['Vacaciones'].'%');

			        $numCell = $numCell + 1;
			        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'Duracion ponderada de actividades (En Segundos)');
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['duracion_ponde']);

			        $phpExc->getActiveSheet()->SetCellValue('C'.$numCell,'Ocupaciones');
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('D'.$numCell, round($value['ocupacion']*100,2).'%');

			        $numCell = $numCell + 1;
			        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'Carga de trabajo');
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('B'.$numCell, round($value['carga_trabajo'],0));

			        $phpExc->getActiveSheet()->SetCellValue('C'.$numCell,'Horas de conexion');
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('D'.$numCell, round($value['horasCNX'],0));

			        $numCell = $numCell + 1;
			        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'Utilizacion de agentes');
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('B'.$numCell, round($value['uti_gentes']*100,2).'%');

			        $phpExc->getActiveSheet()->SetCellValue('C'.$numCell,'Horas minimas de monitoreo');
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('D'.$numCell, round($value['horas_nomina_monit'],0));

			        $numCell = $numCell + 1;
			        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'Horas laborales del mes');
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('B'.$numCell, round($value['horas_laboral_mes'],0));

			        $phpExc->getActiveSheet()->SetCellValue('C'.$numCell,'FTE');
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('D'.$numCell, round($value['FTE'],0));

			        $numCell = $numCell + 1;
			        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'Tecnicos en monitoreo');
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('B'.$numCell, round($value['p_monit'],0));

			        $phpExc->getActiveSheet()->SetCellValue('C'.$numCell,'Tecnicos en otras actividades');
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('D'.$numCell, round($value['p_otras_actividad'],0));

			        $numCell = $numCell + 1;
			        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'Tecnicos CX requeridos');
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('B'.$numCell, round($value['personas'],0));

			        $phpExc->getActiveSheet()->SetCellValue('C'.$numCell,'Tecnicos CX para vacaciones');
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('D'.$numCell, round($value['pnas_vacaciones'],0));

			        $numCell = $numCell + 1;
			        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'Tecnicos CX en ausentismos');
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('B'.$numCell, round($value['pnas_ausentismo'],0));

			        $phpExc->getActiveSheet()->SetCellValue('C'.$numCell,'Tecnicos en exceso/deficit');
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->getFont()->setBold(true);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleColor);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArraySubTitle);
			        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArrayTitle);
			        $phpExc->getActiveSheet()->setCellValue('D'.$numCell, round($value['exceso_deficit'],0));


			        $numCell = $numCell + 1;
			        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'');
			        $phpExc->setActiveSheetIndex(0)->mergeCells('A'.$numCell.':D'.$numCell);
		        }



				$hoy = getdate();
	          	$hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."_ArchivoControlDimensionamiento";
	                
	          	$objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
	                  
	         	$tmpFile = tempnam(sys_get_temp_dir(), $hoy);
	          	$tmpFile.= ".xls";

	          	$objWriter->save($tmpFile);

	          	$message = "<html><body>";
	          	$message .= "<h3>Se ha realizado el envio correcto del archivo del dimensionamiento</h3>";
	          	$message .= "</body></html>";

	          	Yii::$app->mailer->compose()
	                          ->setTo($varcorreo)
	                          ->setFrom(Yii::$app->params['email_satu_from'])
	                          ->setSubject("Archivo control de dimensionamiento")
	                          ->attach($tmpFile)
	                          ->setHtmlBody($message)
	                          ->send();

	            return $this->redirect('index');
			}else{
				#code
			}

			return $this->renderAjax('enviararchivo',[
        		'model' => $model,
      		]);
		}

		


	}