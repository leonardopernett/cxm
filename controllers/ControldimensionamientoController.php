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
						'only' => ['createdimensionar','updatedimensionar'],
						'rules' => [
							[
								'allow' => true,
								'roles' => ['@'],
								'matchCallback' => function() {
                            return Yii::$app->user->identity->isControlProcesoCX();
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
			return $this->render('index');
		}


		public function actionCreatedimensionar(){			
			return $this->renderAjax('creardimensionamiento');
		}

		public function actionCrearpruebas(){
			$txtusuaid = Yii::$app->request->post("txtusuaid");
			$txtyear  = Yii::$app->request->post("txtyear");
			$txtmonth  = Yii::$app->request->post("txtmonth");
			$txtcantvalor  = Yii::$app->request->post("txtcantvalor");
			$txttiempollamada  = Yii::$app->request->post("txttiempollamada");
			$txttiempoadicional  = Yii::$app->request->post("txttiempoadicional");
			$txtactuales  = Yii::$app->request->post("txtactuales");
			$txtotrasactividad  = Yii::$app->request->post("txtotrasactividad");
			$txtturnopromedio  = Yii::$app->request->post("txtturnopromedio");
			$txtausentismo  = Yii::$app->request->post("txtausentismo");
			$txtvacapermilicen  = Yii::$app->request->post("txtvacapermilicen");
			$txtfechacreacion  = Yii::$app->request->post("txtfechacreacion");
			$txtanulado  = Yii::$app->request->post("txtanulado");

			$varResultados = null;

			if ($txtusuaid != null) {	

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
	                                'fechacreacion' => $txtfechacreacion,
	                                'anulado' => $txtanulado,
	                            ])->execute(); 

	            $txtdimensionId = Yii::$app->db->createCommand("select iddimensionamiento from tbl_control_dimensionamiento where usua_id = $txtusuaid and year like '$txtyear' and month like '$txtmonth' and anulado = 0")->queryScalar(); 

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
	                                'fechacreacion' => $txtfechacreacion,
	                                'anulado' => $txtanulado,
	                            ])->execute(); 

	            $varResultados = 0;

			}else{
				$varResultados = 1;
			}

			die(json_encode($varResultados));
			
		}


		public function actionUpdatedimensionar(){			
			return $this->renderAjax('actualizardimensionamiento');
		}

		public function actionCrearpruebas2(){			
			$txtmeSes= Yii::$app->request->post("txtmeSes");
			$txtusuaid = Yii::$app->user->identity->id;
			$txtYear = date("Y");

			$txtMes = null;
			switch ($txtmeSes) {
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

			$data = Yii::$app->db->createCommand("select iddimensionamiento from tbl_control_dimensionamiento where usua_id = $txtusuaid and anulado = 0 and year like '$txtYear' and month like '$txtMes'")->queryScalar();
			
			$varcantvalor = Yii::$app->db->createCommand("select cant_valor from tbl_control_dimensionamiento where iddimensionamiento = $data")->queryScalar();
	        $vartimellama = Yii::$app->db->createCommand("select tiempo_llamada from tbl_control_dimensionamiento where iddimensionamiento = $data")->queryScalar();
	        $vartimeadici = Yii::$app->db->createCommand("select tiempoadicional from tbl_control_dimensionamiento where iddimensionamiento = $data")->queryScalar();
	        $varactualess = Yii::$app->db->createCommand("select actuales from tbl_control_dimensionamiento where iddimensionamiento = $data")->queryScalar();
	        $varotractivi = Yii::$app->db->createCommand("select otras_actividad from tbl_control_dimensionamiento where iddimensionamiento = $data")->queryScalar();
	        $varturnprome = Yii::$app->db->createCommand("select turno_promedio from tbl_control_dimensionamiento where iddimensionamiento = $data")->queryScalar();
	        $varausentism = Yii::$app->db->createCommand("select ausentismo from tbl_control_dimensionamiento where iddimensionamiento = $data")->queryScalar();
	        $varvacaperli = Yii::$app->db->createCommand("select vaca_permi_licen from tbl_control_dimensionamiento where iddimensionamiento = $data")->queryScalar();

			$arrayUsu = array("cant_valor"=>$varcantvalor,"tiempo_llamada"=>$vartimellama,"tiempoadicional"=>$vartimeadici,"actuales"=>$varactualess,"otras_actividad"=>$varotractivi,"turno_promedio"=>$varturnprome,"ausentismo"=>$varausentism,"vaca_permi_licen"=>$varvacaperli,"month"=>$txtMes,"iddimensionamiento"=>$data);
			

			die(json_encode($arrayUsu));

		}


		public function actionCrearpruebas3(){		
			$txtvarMeses = Yii::$app->request->post("txtvarMeses");

			$txtIdDimension  = Yii::$app->request->post("txtIdDimension");
			$txtcantvalor  = Yii::$app->request->post("txtcantvalor");
			$txttiempollamada  = Yii::$app->request->post("txttiempollamada");
			$txttiempoadicional  = Yii::$app->request->post("txttiempoadicional");
			$txtactuales  = Yii::$app->request->post("txtactuales");
			$txtotrasactividad  = Yii::$app->request->post("txtotrasactividad");
			$txtturnopromedio  = Yii::$app->request->post("txtturnopromedio");
			$txtausentismo  = Yii::$app->request->post("txtausentismo");
			$txtvacapermilicen  = Yii::$app->request->post("txtvacapermilicen");

			$txtYear = date("Y");
			$txtusuaid = Yii::$app->user->identity->id;
			$varResultados = null;

			if ($txtusuaid != null) {

				Yii::$app->db->createCommand()->update('tbl_control_dimensionamiento',[
					                                'cant_valor' => $txtusuaid,
					                                'cant_valor' => $txtcantvalor,
					                                'tiempo_llamada' => $txttiempollamada,
					                                'tiempoadicional' => $txttiempoadicional,
					                                'actuales' => $txtactuales,
					                                'otras_actividad' => $txtotrasactividad,
					                                'turno_promedio' => $txtturnopromedio,
					                                'ausentismo' => $txtausentismo,
					                                'vaca_permi_licen' => $txtvacapermilicen,
					                            ],'iddimensionamiento ='.$txtIdDimension.'')->execute(); 


				$txtdimensionId = Yii::$app->db->createCommand("select iddimensionamiento from tbl_control_dimensionamiento where usua_id = $txtusuaid and year like '$txtYear' and month like '$txtvarMeses' and anulado = 0")->queryScalar(); 

	            $txtduracion_ponde = $txttiempollamada + $txttiempoadicional;
	            $txtocupacion = 80 / 100;
	            $txtcarga_trabajo = $txtcantvalor * $txtduracion_ponde  / 3600;
	            $txthorasCNX = $txtcarga_trabajo / $txtocupacion;
	            $txtuti_gentes = 92 / 100;
	            $txthoras_nomina_monit = $txthorasCNX  / $txtuti_gentes;
	            $txthoras_laboral_mes = 192;
	            $txtFTE = $txthoras_nomina_monit / $txthoras_laboral_mes;
	            $txtp_monit = ($txtFTE * 48 / $txtturnopromedio) * (1 + ($txtausentismo / 100));
	            $txtp_otras_actividad = ($txthoras_nomina_monit  * ($txtotrasactividad / 100) / $txthoras_laboral_mes) * (48 / $txtturnopromedio);
	            $txtpersonas = $txtp_monit + $txtp_otras_actividad;	            
	            $txtpnas_vacaciones = $txtp_monit * ($txtvacapermilicen / 100);
	            $txtpnas_ausentismo = ($txtausentismo / 100) * $txtp_monit;
	            $txtexceso_deficit = $txtactuales - $txtpersonas;

	            Yii::$app->db->createCommand()->update('tbl_control_dimensionar',[
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
	                            ],'iddimensionamiento ='.$txtdimensionId.'')->execute(); 

	            $varResultados = 0;

			}else{
				$varResultados = 1;
			}

			die(json_encode($varResultados));
	
		}

		public function actionCrearpruebas4(){
			$txtmeSes= Yii::$app->request->post("txtmeSes");
			
			$txtusuaid = Yii::$app->user->identity->id;
			$txtYear = date("Y");

			$txtMes = null;
			switch ($txtmeSes) {
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

			$data = Yii::$app->db->createCommand("select count(*) from tbl_control_dimensionamiento where usua_id = $txtusuaid and anulado = 0 and year like '$txtYear' and month like '$txtMes'")->queryScalar();


			die(json_encode($data));
		}


	}