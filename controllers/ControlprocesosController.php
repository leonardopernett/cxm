<?php

namespace app\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\Response;
use \app\models\Evaluados;
use app\models\BaseSatisfaccion;
use app\models\BaseSatisfaccionSearch;
use app\models\ControlProcesosEquipos;
use app\models\ControlProcesos;
use app\models\ControlParams;
use app\models\ArbolesSearch;
use app\models\Arboles;
use app\models\ControlTipoCortes;
use app\models\Tiposdecortes;
use app\models\Tipocortes;
use app\models\ControlDesvincular;
use yii\filters\AccessControl;
use yii\db\Query;
use app\models\SpeechCategorias;
use app\models\ControlFocalizada;


class ControlprocesosController extends \yii\web\Controller {

		public function behaviors(){
			return[
				'access' => [
						'class' => AccessControl::classname(),
						'only' => ['create','createparameters','createcontrol','update','update2','update3','delete','delete2', 'gestionenviovaloracion', 'enviocorreo', 'vercortes', 'viewcortes','createparameters2','cortegrupal','desvincular','usuariolista','selecciontecnico','selecteduser','buscaridtc','guardarplan','clonargrupo','indexeliminar','lideruser','createfocalizada','updatespeech','$id,$evaluadoId','createfocalizadaup','updatetwospeech','deletetwospeech'],
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
		
		/**
		*Accion que permite ir al formulario principal del modulo - Permite Buscar el valorado y devolver resultado.
		*@return mixed
		*/
		public function actionIndex(){
			$model = new ControlProcesosEquipos();
			
			$dataProvider = $model->search1(Yii::$app->request->post());

			return $this->render('index',[
				'model'=>$model,
				'dataProvider'=>$dataProvider,
				
				]);
		}

		/**
		*Accion que permite ir al formulario de Create y alli buscar al valorado para Registrarlo.
		*@return mixed
		*/
		public function actionCreate($usua_id){
			$model2 = new controlprocesos();
			$model = new Controlparams();	
			$id_valorado = $usua_id;
			$count1 = null;
			$count2 = null;
			$count3 = null;
			$count4 = 0;
			$txtusua_id = $usua_id;

			$dataProvider = $model->Obtener($id_valorado);

			return $this->render('create', [
									'model'=>$model,
									'model2'=>$model2,
									'dataProvider'=>$dataProvider,
									'id_valorado'=>$id_valorado,
									'count1'=>$count1,
									'count2'=>$count2,
									'count3'=>$count3,
									'count4'=>$count4,
									'txtusua_id'=>$txtusua_id,
					]);		

		}
		
		/**
		*Accion que permite ir a la ventana emergente para agregar las dimensiones
		*@return mixed
		*/
		public function actionCreateparameters($usua_id){
		 	$searchModel = new BaseSatisfaccionSearch;
        	$txtusua_id = $usua_id;

         	$model = new Controlparams;
         	$model2 = new SpeechCategorias;

         	$formData = Yii::$app->request->post();

         	if ($model->load($formData)) {
         		if ($model->save()) {
         			Yii::$app->getsession()->setFlash('message','Registro agregado correctamente.');
         			return $this->redirect(array('create','usua_id'=>$txtusua_id));
         		}
         		else{
         			Yii::$app->getseesion()->setFlash('message','Fallo al agregar registro.');
         		}

         	}

		 	return $this->renderAjax('createparameters', [
		 			'searchModel' => $searchModel,
		 			'model' => $model,
		 			'txtusua_id' => $txtusua_id,
		 			'model2' => $model2,
		 		]);	
		}

		/**
		*Accion que permite realizar las Query para el resultado numerico de las Llamadas.
		*@return mixed
		*/
		public function actionPrueba(){
			$cCO1 = Yii::$app->request->post("arbol_id");
			$pcrcTxt = Yii::$app->request->post("pcrc_text");
			$numLetras = strlen($cCO1);

			if ($cCO1 != null) {					
				if ($numLetras > 6) {
					$cCO = substr($cCO1, 0, -1);
				}
				else
				{
					$cCO = $cCO1;
				}

		        $month = date('m');
	      		$year = date('Y');
	      		$rtaFecha  = date('Y-m-d', mktime(0,0,0, ($month - 1), 1, $year));

		        $month = (string)date('m');
	      		$year = (string)date('Y');
	      		$rtaFecha2  = (string)date('Y-m-d', mktime(0,0,0, $month, -1, $year));

				$contestadas = null;			
				$salidas = null;
				$rtaP = 50/100;
				$rtaQ = 50/100;
				$rtaZ = 196/100;
				$rtaError = 5/100;
				$rtaNull = 30;

				$arregloText = null;

				if ($cCO == "111214" || $cCO == "111311" || $cCO == "111313" || $cCO == "122213" || $cCO == "122214" 
					|| $cCO == "122223" || $cCO == "122225" || $cCO == "122230" || $cCO == "122231" || $cCO == "122237"
					|| $cCO == "122238" || $cCO == "122240" || $cCO == "122417" || $cCO == "122419" || $cCO == "122420"
					|| $cCO == "124213" || $cCO == "181211" || $cCO == "181215" || $cCO == "181216" || $cCO == "181411"
					|| $cCO == "232211" || $cCO == "232411") {

					$arregloText = Yii::$app->db->createCommand("select name from tbl_arbols where dsname_full = '$pcrcTxt'")->queryScalar();

					if ($arregloText == "122230 CLIENTE PREFERENCIAL WEB CALLBACK" || $arregloText == "122230 CLIENTE PREFERENCIAL CLIC TO CALL") {
						$arbol_id = 10;
						die(json_encode($arbol_id));
					}
					else
						if ($arregloText == "122420 GAC BACK OFFICE TELEFONICO" || $arregloText == "122420 GAC BACK OFFICE DOCUMENTAL" || $arregloText == "111313 RENTING USADOS") {
							$arbol_id = 15;
							die(json_encode($arbol_id));
						}
						else
							if ($arregloText == "122214-2 BANCAEMPRESAS CLIC TO CALL" || $arregloText == "122417 BACK OFFICE PREFERENCIAL" || $arregloText == "122223 ALQUILER DE INFRAEST. GERENCIA PREFERENCIAL" || $arregloText == "122223 ALQUILER DE INFRAESTRUCTURA FÍSICA Y TÉCNICA/ECA INMOBILIARIA" || $arregloText == "122214-2 BANCAEMPRESAS WEB CALLBACK" || $arregloText == "122223 ALQUILER DE INFRAEST. FÍSICA Y TÉCNICA PREFERENCIAL DIGITAL" || $arregloText == "122237 ALQUILER DE INFRAEST. FÍSICA Y TÉCNICA / CUOTA DE MANEJO" || $arregloText == "181211-1 VALORES ECA" || $arregloText == "181215 CONFIRMACION DE TRANSACCIONES VALORES" || $arregloText == "181211-4 VALORES SERVICIOS VIRTUALES" || $arregloText == "232411 BACK OFFICE SUFI" || $arregloText == "181411 CUV CENTRO UNICO DE VINCULACIÓN" || $arregloText == "111313 RENTING BKO" || $arregloText == "124213 Negocios Fiduciarios" || $arregloText == "232211 SUFI LEVANTAMIENTO DE PRENDA" || $arregloText == "122238 BANCOLOMBIA LINEA ECA" || $arregloText == "232411 SUFI BKO DOCUMENTAL" || $arregloText == "181211 Valores ECA BKO" || $arregloText == "232211 SUFI VENTAS") {
								$arbol_id = 30;
								die(json_encode($arbol_id));
							}
							else
								if ($arregloText == "122419 - GESTOR DE SOLUCIONES" || $arregloText == "122230 CLIENTE PREFERENCIAL CHAT" || $arregloText == "111214 ALQUILER DE INFRAESTRUCTURA FÍSICA Y TÉCNICA/LOCALIZA" || $arregloText == "111311 RENTING SAC IE") {
									$arbol_id = 50;
									die(json_encode($arbol_id));
								}
								else
									if ($arregloText == "122238 ESTABLECIMIENTOS AFILIADOS") {
										$arbol_id = 54;
										die(json_encode($arbol_id));
									}
									else
										if ($arregloText == "122231 LES BÁSICO SALIDA") {
											$arbol_id = 70;
											die(json_encode($arbol_id));
										}
										else
											if ($arregloText == "122213 CENTRO DE CONSERVACIÓN/TMK" || $arregloText == "122230 CLIENTE PREFERENCIAL DIGITAL") {
												$arbol_id = 100;
												die(json_encode($arbol_id));
											}
											else
												if ($arregloText == " 111311 RENTING SAC - Encuesta") {
													$arbol_id = 120;
													die(json_encode($arbol_id));
												}
												else
													if ($arregloText == "122420 GAC ACLARACIONES") {
														$arbol_id = 125;
														die(json_encode($arbol_id));
													}
													else
														if ($arregloText == "122223 ALQUILER DE INFRAESTRUCTURA FÍSICA Y TÉCNICA/LES FRAUDES" || $arregloText == "181216 ALQUILER DE INFRAESTRUCTURA FÍSICA Y TÉCNICA/MESA VIRTUAL ASESORÍA") {
															$arbol_id = 150;
															die(json_encode($arbol_id));
														}
														else
															if ($arregloText == "122225 CENTRALIZACIÓN DE LLAMADAS") {
																$arbol_id = 157;
																die(json_encode($arbol_id));
															}
															else
																if ($arregloText == "122231 LES BÁSICO") {
																	$arbol_id = 170;
																	die(json_encode($arbol_id));
																}
																else
																	if ($arregloText == "181211 VALORES SAC") {
																		$arbol_id = 180;
																		die(json_encode($arbol_id));
																	}
																	else
																		if ($arregloText == "122214 BANCA EMPRESARIAL SAC") {
																			$arbol_id = 200;
																			die(json_encode($arbol_id));
																		}
																		else
																			if ($arregloText == "232211 SUFI SAC") {
																				$arbol_id = 210;
																				die(json_encode($arbol_id));
																			}
																			else
																				if ($arregloText == "122214-2 CHAT BANCAEMPRESAS") {
																					$arbol_id = 280;
																					die(json_encode($arbol_id));
																				}
																				else
																					if ($arregloText == "122240 ALERTAS, NOTIFICACIONES Y ALM") {
																						$arbol_id = 285;
																						die(json_encode($arbol_id));
																					}
																					else
																						if ($arregloText == "122213 CENTRO DE CONSERVACIÓN") {
																							$arbol_id = 400;
																							die(json_encode($arbol_id));
																						}
																						else
																							if ($arregloText == "122230 CLIENTE PREFERENCIAL") {
																								$arbol_id = 450;
																								die(json_encode($arbol_id));
																							}
																							else
																								if ($arregloText == "122237 LÍNEA ESPECIALIZADA DE QyR") {
																									$arbol_id = 480;
																									die(json_encode($arbol_id));
																								}
				}
				else
				{
					$contestadas = Yii::$app->get('dbTeo2')->createCommand("Select Sum(contestadas) from reporting_rgd.v_consolidado_experiencias Where centro_costos = '$cCO' AND fecha between '$rtaFecha' AND '$rtaFecha2' ")->queryScalar();	

					$salidas = Yii::$app->get('dbTeo2')->createCommand("Select Sum(salida) from reporting_rgd.v_consolidado_experiencias Where centro_costos = '$cCO' AND fecha between '$rtaFecha' AND '$rtaFecha2' ")->queryScalar();		

					if ($contestadas <= $rtaNull || $salidas <= $rtaNull) {
							$arbol_id = $rtaNull;
						}	
						else
						{
							if ($contestadas > $salidas) {
								$arbol_id = round((($rtaZ * $rtaZ) * $rtaP * $rtaQ * $contestadas) / (($rtaError * $rtaError) * ($contestadas - 1) + (($rtaZ * $rtaZ) * $rtaP * $rtaQ)));
							}
							else
							{
								$arbol_id = round((($rtaZ * $rtaZ) * $rtaP * $rtaQ * $salidas) / (($rtaError * $rtaError) * ($salidas - 1) + (($rtaZ * $rtaZ) * $rtaP * $rtaQ)));
							}
						}

					die(json_encode($arbol_id));					
				}

			}
			else
			{
				$arbol_id = 30;
				die(json_encode($arbol_id));
			}	
		}

		/**
		*Accion que permite ir a la ventana emergente para guardar los parametros del formulario de Agregar Valorado.
		*@return mixed
		*/
		public function actionCreatecontrol(){	
         	$model2 = new controlprocesos;
         	$sessiones = Yii::$app->user->identity->id;

			$formData = Yii::$app->request->post();
			var_dump($formData);

	            if ($model2->load($formData)) {
	                if ($model2->save()) {
	                    Yii::$app->getsession()->setFlash('message','Registro agregado correctamente');
	                    return $this->redirect(['index']);
	                }
	                else
	                {
	                    Yii::$app->getsession()->setFlash('message','Fallo al agregar registro.');
	                }
	            }

			return $this->renderAjax('createcontrol', [
		 			'model2' => $model2,
		 			'sessiones' => $sessiones,
		 		]);	
		}

		/**
		*Accion que permite realizarla confirmacion de la salida del formulario de Agregar valorado.
		*@return mixed
		*/
		public function actionConfirmacion(){
			return $this->renderAjax('confirmacion');
		}

		/**
		*Accion que permite realizarla confirmacion de la salida del formulario de Agregar valorado.
		*@return mixed
		*/
		public function actionUpdate($id, $evaluadoId){
			$txtvarevaluadoId = $evaluadoId;
			
			$model = $this->findModel($id);
			if ($model->load(Yii::$app->request->post()) && $model->save()) {
			    Yii::$app->session->setFlash('success', Yii::t('app', 'Successful update!'));            
			    return $this->redirect(array('create','usua_id'=>$evaluadoId));
			}


			if (Yii::$app->request->get('id')) {
				$id_params = Html::encode(Yii::$app->request->get('id'));

				if ((int) $id_params) {
					$table = ControlParams::findOne($id_params);

					if ($table) {
						$model->id = $table->id;
						$model->arbol_id = $table->arbol_id;
						$model->dimensions = $table->dimensions;
						$model->cant_valor = $table->cant_valor;
						$model->evaluados_id = $table->evaluados_id;
						$model->argumentos = $table->argumentos;
					}
					else
					{
						return $this->redirect(array('create','usua_id'=>$evaluadoId));
					}
				}
				else
				{
					return $this->redirect(array('create','usua_id'=>$evaluadoId));
				}
			}
			else
			{
				return $this->redirect(array('create','usua_id'=>$evaluadoId));
			}

			return $this->render('update', [
				'model' => $model,
				'txtvarevaluadoId' => $txtvarevaluadoId,
				]);
		}

		/**
		*Accion que permite eliminar un usuario del Agregar Valorado.
		*@return mixed
		*/
		public function actionDelete($id){
			$model = $this->findModel($id);			

			if ($model == null) {
				throw new NotFoundHttpException('El registro no existe.'); 
			}
			else
			{
				
				$txtfechaCreacion = Yii::$app->db->createCommand("select distinct fechacreacion from tbl_control_params where anulado = 0 and id = $id")->queryScalar();

			    	$txtevaluados = Yii::$app->db->createCommand("select distinct evaluados_id from tbl_control_params where anulado = 0 and id = $id")->queryScalar();

			    	$txtidprocesos = Yii::$app->db->createCommand("select distinct id from tbl_control_procesos where anulado = 0 and fechacreacion between '$txtfechaCreacion' and '$txtfechaCreacion' and evaluados_id = $txtevaluados")->queryScalar();
				$model->delete();
			    	return $this->redirect(array('update2','id'=>$txtidprocesos,'evaluados_id'=>$txtevaluados));
			}
		}
		protected function findModel($id){
	        if (($model = Controlparams::findOne($id)) !== null) {
	            return $model;
	        } else {
	            throw new NotFoundHttpException('The requested page does not exist.');
	        }
	    }

		/**
		*Accion que permite eliminar un usuario del index y sus dimensiones.
		*@return mixed
		*/
		public function actionDelete2($id, $evaluados_id){
			$model = $this->findModel2($id);
			$varId = $id;
			$varEvaluados = $evaluados_id;

			if ($model == null) {
				throw new NotFoundHttpException('El registro no existe.'); 
			}
			else
			{
				
				$varidtc = Yii::$app->db->createCommand("select idtc from tbl_control_procesos where anulado = 0 and id = $varId")->queryScalar();
				$varDate = Yii::$app->db->createCommand("select mesyear from tbl_tipocortes where anulado = 0 and idtc = $varidtc")->queryScalar();
				$varEndDate = date("Y-m-t", strtotime($varDate));

				Yii::$app->db->createCommand()->delete('tbl_control_params', 'anulado = :param1 AND evaluados_id = :param2 AND fechacreacion >= :param3 AND fechacreacion <= :param4', array(':param2' => $varEvaluados, ':param1'=>0, ':param3' => $varDate, ':param4' => $varEndDate))->execute();

				$model->delete();
				return $this->redirect(['index']);
			}
		}
		protected function findModel2($id){
	        if (($model = ControlProcesos::findOne($id)) !== null) {
	            return $model;
	        } else {
	            throw new NotFoundHttpException('The requested page does not exist.');
	        }
	    }

		/**
		*Accion que permite ver un usuario y sus dimensiones.
		*@return mixed
		*/
		public function actionView($id, $evaluados_id){
			$model = new ControlProcesos;
			$model2 = new Controlparams;
			$dataProvider = null;
			$nameVal = null;
			$txtId = $id;

			if (Yii::$app->request->get('id')) {
				$id_params = Html::encode(Yii::$app->request->get('id'));

				if ((int) $id_params) {
					$table = ControlProcesos::findOne($id_params);

					if ($table) {
						$txtcorte = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where evaluados_id ='.$evaluados_id.' and id ='.$id.'')->queryScalar();
				        	$fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar();
				        	$fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar(); 

						$model->id = $table->id;
						$nameVal = $table->evaluados_id;
						$model->evaluados_id = Yii::$app->db->createCommand('select usua_nombre from tbl_usuarios where usua_id ='.$nameVal.'')->queryScalar();
						$model->salario = $table->salario;
						$model->tipo_corte = $table->tipo_corte;
						$model->responsable = $table->responsable;
						$model->cant_valor = Yii::$app->db->createCommand("select sum(cant_valor) from tbl_control_params where anulado = 0 and evaluados_id = $nameVal and fechacreacion between '$fechainiC' and '$fechafinC'")->queryScalar();
						$model->Dedic_valora = $table->Dedic_valora;

						$dataProvider = $model2->Obtener2($id, $evaluados_id);
					}
					else
					{
						return $this->redirect(['index']);
					}
				}
				else
				{
					return $this->redirect(['index']);
				}
			}
			else
			{
				return $this->redirect(['index']);
			}


			return $this->render('view', [
				'model' => $model,
				'dataProvider' => $dataProvider,
				'nameVal' => $nameVal,
				'txtId' => $txtId,
				]);
		}

		/**
		*Accion que permite ver un usuario y sus dimensiones.
		*@return mixed
		*/
		public function actionUpdate2($id, $evaluados_id){
			$model = new \app\models\ControlProcesos();
			$model2 = new Controlparams();
			$dataProvider = null;
			$nameVal = null;
			$varIdusua = $evaluados_id;
			$varCortes = null;
			$txtProcesos = $id;
			$varName = Yii::$app->db->createCommand('select usua_nombre from tbl_usuarios where usua_id ='.$varIdusua.'')->queryScalar();
			$fechaActual = date("Y-m-d");
			$anulados = 0;			

			$formData = Yii::$app->request->post();


	            if ($model->load($formData)) {
	                if ($model->save()) {

	                	$txtcorte = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where evaluados_id ='.$evaluados_id.' and id ='.$id.'')->queryScalar();
				        $fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar();
				        $fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar();  

	                	$querys =  new Query;
				        $querys     ->select('*')
				                    ->from('tbl_control_params')
				                    ->where(['tbl_control_params.anulado' => 0])
				                    ->andwhere('tbl_control_params.evaluados_id ='.$varIdusua.'')
				                    ->andwhere(['between','tbl_control_params.fechacreacion',$fechainiC, $fechafinC]);                    
				        $command = $querys->createCommand();
				        $query = $command->queryAll();

					   	foreach ($query as $key => $value) {
					       	$txtarbol = $value['arbol_id'];
					       	$txtdimensiones = $value['dimensions'];
					       	$txtcantvalor = $value['cant_valor'];
					       	$txtevaluados = $value['evaluados_id'];
					       	$txtargumentos = $value['argumentos'];


				        	Yii::$app->db->createCommand()->insert('tbl_control_params',[
				        			'arbol_id' => $txtarbol,
				        			'dimensions' => $txtdimensiones,
				        			'cant_valor' => $txtcantvalor,
				        			'evaluados_id' => $txtevaluados,
				        			'argumentos' => $txtargumentos,

				        			'fechacreacion' => $fechaActual,
				        			'anulado' => $anulados,
				        		])->execute();

				        }

	                    Yii::$app->getsession()->setFlash('message','Registro agregado correctamente');
	                    return $this->redirect(['index']);
	                }
	                else
	                {
	                    Yii::$app->getsession()->setFlash('message','Fallo al agregar registro.');
	                }
	            }

			if (Yii::$app->request->get('id')) {
				$id_params = Html::encode(Yii::$app->request->get('id'));

				if ((int) $id_params) {
					$table = ControlProcesos::findOne($id_params);

					if ($table) {
						$txtcorte = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where evaluados_id ='.$evaluados_id.' and id ='.$id.'')->queryScalar();
				        	$fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar();
				        	$fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar(); 

						$model->id = $table->id;
						$nameVal = $table->evaluados_id;
						$model->evaluados_id = Yii::$app->db->createCommand('select name from tbl_evaluados where id ='.$nameVal.'')->queryScalar();
						$model->salario = $table->salario;
						$model->tipo_corte = $table->tipo_corte;
						$varCortes = $table->tipo_corte;
						$model->cant_valor = Yii::$app->db->createCommand("select sum(cant_valor) from tbl_control_params where anulado = 0 and evaluados_id = $nameVal and fechacreacion between '$fechainiC' and '$fechafinC'")->queryScalar();
						$model->Dedic_valora = $table->Dedic_valora;

						$dataProvider = $model2->Obtener2($id, $evaluados_id);

					}
					else
					{
						return $this->redirect(['index']);
					}
				}
				else
				{
					return $this->redirect(['index']);
				}
			}
			else
			{
				return $this->redirect(['index']);
			}


			return $this->render('update2', [
				'model' => $model,
				'varIdusua' => $varIdusua,
				'varName' => $varName,
				'varCortes' => $varCortes,
				'dataProvider' => $dataProvider,
				'txtProcesos' => $txtProcesos,
				]);
		}

		/**
		*Accion que permite realizar el envio de los datos a correos.
		*@return mixed
		*/
		public function actionGestionenviovaloracion($txtiddelevaluado,$txtId){
			$model = new \app\models\ControlProcesos();
			$variddelevaluado = $txtiddelevaluado;
			$vartxtId = $txtId;

			return $this->renderAjax('email', [
					'model' => $model,
					'variddelevaluado' => $variddelevaluado,
					'vartxtId' => $vartxtId,
				]);
		}

		public function actionEnviocorreo(){
			$varDestino = Yii::$app->request->post("var_bdestino");
			$varId = Yii::$app->request->post("var_bId");
			$var_bicontrol = Yii::$app->request->post("var_bicontrol");


			$varnombrevalorador = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios u 	inner join tbl_control_procesos cp on u.usua_id = cp.evaluados_id where cp.id = '$var_bicontrol'")->queryScalar();

			$vardedicadion = Yii::$app->db->createCommand("select Dedic_valora from tbl_control_procesos where id = '$var_bicontrol'")->queryScalar();

			 $txtcorte = Yii::$app->db->createCommand("select idtc from tbl_control_procesos where id = '$var_bicontrol'")->queryScalar();
			 $fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where idtc = '$txtcorte'")->queryScalar();
			 $fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where idtc = '$txtcorte'")->queryScalar(); 

			 $varcantidadvalora = Yii::$app->db->createCommand("select sum(cant_valor) from tbl_control_params where evaluados_id = '$varId' and fechacreacion between '$fechainiC' and '$fechafinC'")->queryScalar();

			 $vartipocorte = Yii::$app->db->createCommand("select tipo_corte from tbl_control_procesos where id ='$var_bicontrol'")->queryScalar();

			 $varlistparame = Yii::$app->db->createCommand("select * from tbl_control_params where evaluados_id = '$varId' and fechacreacion between '$fechainiC' and '$fechafinC'")->queryAll();

			 $vardiascorte = Yii::$app->db->createCommand("select diastc from tbl_tipocortes where idtc = '$txtcorte'")->queryScalar();

			 $vardiashabiles = Yii::$app->db->createCommand("select sum(cantdiastcs) from tbl_tipos_cortes where idtc = '$txtcorte'")->queryScalar(); 

			 $varlistcortes = Yii::$app->db->createCommand("select * from tbl_tipos_cortes where idtc = '$txtcorte'")->queryAll(); 

			 $phpExc = new \PHPExcel();

             $phpExc->getProperties()
                         ->setCreator("Konecta")
                         ->setLastModifiedBy("Konecta")
                         ->setTitle("Valoraciones QA")
                         ->setSubject("Valoraciones QA")
                         ->setDescription("Este archivo contiene el proceso de los envios de las valoraciones realizadas en QA para los tecnicos.")
                         ->setKeywords("Valoraciones QA");
             $phpExc->setActiveSheetIndex(0);

             $numCell = 1;
             $phpExc->getActiveSheet()->setCellValue('A'.$numCell, 'VALORADOR');
             $phpExc->getActiveSheet()->setCellValue('B'.$numCell, '% DEDICACION');
             $phpExc->getActiveSheet()->setCellValue('C'.$numCell, 'TOTAL VALORACIONES');
             $phpExc->getActiveSheet()->setCellValue('D'.$numCell, 'TIPO DE CORTE');
                 $numCell = $numCell++ + 1;   
             $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $varnombrevalorador);
             $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $vardedicadion);
             $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $varcantidadvalora);
             $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $vartipocorte);
                 $numCell = $numCell + 2;  
             $phpExc->getActiveSheet()->setCellValue('A'.$numCell, 'PCRC');
             $phpExc->getActiveSheet()->setCellValue('B'.$numCell, 'DIMENSION');
             $phpExc->getActiveSheet()->setCellValue('C'.$numCell, 'CANTIDAD DE VALORACIONES');
             $phpExc->getActiveSheet()->setCellValue('D'.$numCell, 'JUSTIFICACION');
                 $numCell = $numCell++ + 1;  

             foreach ($varlistparame as $key => $value) {
             	$vararbolid = $value['arbol_id'];
             	$varnombre = Yii::$app->db->createCommand("select name from tbl_arbols where id = $vararbolid")->queryScalar(); 

             	$phpExc->getActiveSheet()->setCellValue('A'.$numCell, $varnombre);
                 $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['dimensions']);
                 $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $value['cant_valor']);      
                 $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $value['argumentos']);
                 $numCell++;
             }
             $numCell = $numCell + 3; 
             $phpExc->getActiveSheet()->setCellValue('A'.$numCell, 'TIPO DE CORTE');
             $phpExc->getActiveSheet()->setCellValue('B'.$numCell, 'DIAS DEL CORTE');
             $phpExc->getActiveSheet()->setCellValue('C'.$numCell, 'FECHA INICIO');
             $phpExc->getActiveSheet()->setCellValue('D'.$numCell, 'FECHA FIN');
             $phpExc->getActiveSheet()->setCellValue('E'.$numCell, 'CANTIDAD DE DIAS HABILES');
                 $numCell = $numCell++ + 1;
             $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $vartipocorte);
             $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $vardiascorte);
             $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $fechainiC);
             $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $fechafinC);
             $phpExc->getActiveSheet()->setCellValue('e'.$numCell, $vardiashabiles);
                 $numCell = $numCell + 2; 
             $phpExc->getActiveSheet()->setCellValue('A'.$numCell, 'CORTE');
             $phpExc->getActiveSheet()->setCellValue('B'.$numCell, 'FECHA INICIO');
             $phpExc->getActiveSheet()->setCellValue('C'.$numCell, 'FECHA FIN');
             $phpExc->getActiveSheet()->setCellValue('D'.$numCell, 'DIAS');
             $phpExc->getActiveSheet()->setCellValue('E'.$numCell, 'CANT. DIAS');   
                 $numCell = $numCell++ + 1; 
             foreach ($varlistcortes as $key => $value) {
             	$phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['cortetcs']);
                 $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['fechainiciotcs']);
                 $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $value['fechafintcs']);      
                 $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $value['diastcs']);
                 $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $value['cantdiastcs']);
                     $numCell++;
             }



             $hoy = getdate();
             $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."_".$hoy['hours']."_".$hoy['minutes'];
              
             $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
                
             $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
             $tmpFile.= ".xls";

             $objWriter->save($tmpFile);

             $message = "<html><body>";
             $message .= "<h3>Se ha realizado el envio correcto de las valoraciones.</h3>";
             $message .= "</body></html>";

             Yii::$app->mailer->compose()
                         ->setTo($varDestino)
                         ->setFrom(Yii::$app->params['email_satu_from'])
                         ->setSubject("Envio valoraciones QA")
                         ->attach($tmpFile)
                         ->setHtmlBody($message)
                         ->send();

			$rtaenvio = 1;
            die(json_encode($rtaenvio));


		}


		/**
		*Accion que permite ver un usuario y sus dimensiones.
		*@return mixed
		*/
		public function actionVercortes(){
			$model = new ControlTipoCortes();
			$dataProvider = $model->searchcortes(Yii::$app->request->post());			
			
			return $this->render('_formviewcort',[
				'model'=>$model,
				'dataProvider'=>$dataProvider,
				]);
		}

		/**
		*Accion que permite ver el corte general y sus cortes
		*@return mixed
		*/
		public function actionViewcortes($idtc){
			$model = new Tipocortes();			
			$model2 = new Tiposdecortes();
			$dataProvider = null;
			$numdias = null;

			if (Yii::$app->request->get('idtc')) {
				$id_params = Html::encode(Yii::$app->request->get('idtc'));

				if ((int) $id_params) {
					$table = Tipocortes::findOne($id_params);

					if ($table) {
						$model->idtc = $table->idtc;
						$model->tipocortetc = $table->tipocortetc;
						$model->diastc = $table->diastc;
						$model->fechainiciotc = $table->fechainiciotc;
						$model->fechafintc = $table->fechafintc;
						$model->cantdiastc = Yii::$app->db->createCommand('select sum(cantdiastcs) from tbl_tipos_cortes where idtc = '.$idtc.'')->queryScalar();
						$numdias = Yii::$app->db->createCommand('select sum(cantdiastcs) from tbl_tipos_cortes where idtc = '.$idtc.'')->queryScalar();

						$dataProvider = $model2->ObtenerCorte2($idtc);
					}
					else
					{
						return $this->redirect(['index']);
					}
				}
				else
				{
					return $this->redirect(['index']);
				}
			}
			else
			{
				return $this->redirect(['index']);
			}


			return $this->render('_formviewcortes', [
				'model' => $model,
				'dataProvider' => $dataProvider,
				'numdias' => $numdias,
				]);
		}



		/**
		*Accion que permite realizarla confirmacion de la salida del formulario de Agregar valorado.
		*@return mixed
		*/
		public function actionUpdate3($id){

			$model = $this->findModel($id);
			if ($model->load(Yii::$app->request->post()) && $model->save()) {
			    Yii::$app->session->setFlash('success', Yii::t('app', 'Successful update!')); 

			    $txtfechaCreacion = Yii::$app->db->createCommand("select distinct fechacreacion from tbl_control_params where anulado = 0 and id = $id")->queryScalar();

			    $txtevaluados = Yii::$app->db->createCommand("select distinct evaluados_id from tbl_control_params where anulado = 0 and id = $id")->queryScalar();

			    $txtidprocesos = Yii::$app->db->createCommand("select distinct id from tbl_control_procesos where anulado = 0 and fechacreacion between '$txtfechaCreacion' and '$txtfechaCreacion' and evaluados_id = $txtevaluados")->queryScalar();

			    return $this->redirect(array('update2','id'=>$txtidprocesos,'evaluados_id'=>$txtevaluados));            
			    
			} else {
			        return $this->render('update3', [
			        	'model' => $model,
			        ]);
			        }

			if (Yii::$app->request->get('id')) {
				$id_params = Html::encode(Yii::$app->request->get('id'));

				if ((int) $id_params) {
					$table = ControlParams::findOne($id_params);

					if ($table) {
						$model->id = $table->id;
						$model->arbol_id = $table->arbol_id;
						$model->dimensions = $table->dimensions;
						$model->cant_valor = $table->cant_valor;
						$model->evaluados_id = $table->evaluados_id;
						$model->argumentos = $table->argumentos;
					}
					else
					{
						return $this->redirect(['index']);
					}
				}
				else
				{
					return $this->redirect(['index']);
				}
			}
			else
			{
				return $this->redirect(['index']);
			}

			return $this->render('update3', [
				'model' => $model,
				]);
		}

		/**
		*Accion que permite realizarla confirmacion de la salida del formulario de Agregar valorado.
		*@return mixed
		*/
		public function actionCreateparameters2($id, $evaluados_id){
			$model = new Controlparams();
			$varIdusua  = $evaluados_id;
			$IDvar = $id;

			$formData = Yii::$app->request->post();

         	if ($model->load($formData)) {
         		var_dump("Aqui");
         		if ($model->save()) {
         			Yii::$app->getsession()->setFlash('message','Registro agregado correctamente.');
         			return $this->redirect(array('update2','id'=>$id,'evaluados_id'=>$evaluados_id));
         		}
         		else{
         			Yii::$app->getseesion()->setFlash('message','Fallo al agregar registro.');
         		}

         	}

			return $this->render('_formdimensiones',[
				'model' => $model,
				'varIdusua' => $varIdusua,
				'IDvar' => $IDvar,
				]);
		}


		public function actionCortegrupal(){
			$sessiones = Yii::$app->user->identity->id;

			$varMes = date("n") - 1;
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

			$data =  Yii::$app->db->createCommand("select * from tbl_control_procesos where responsable = $sessiones and anulado = 0 and tipo_corte like '%$txtMes%'")->queryAll();

			$varFecha = date("Y-m-d");

			$varBancolombia = 'Grupo Bancolombia';
			$varDirectv = 'Directv';
			$varGeneral = 'General Konecta';
			$varRespuesta = null;
			$varResultados = null;
			$varIdCorte = null;
			

			foreach ($data as $key => $value) {
				$varEvaluadosId = $value['evaluados_id'];
				$varSalario = $value['salario'];
				(string)$varTipoCorte = $value['tipo_corte'];
				$varResponsable = $value['responsable'];
				$varCantValor = $value['cant_valor'];
				$varDedicValor = $value['Dedic_valora'];
				

				    $txtWord1 = strpos($varTipoCorte, $varBancolombia);
				
                    $txtWord2 = strpos($varTipoCorte, $varDirectv);
                
                    $txtWord3 = strpos($varTipoCorte, $varGeneral);
                

                        if ($txtWord1 == true) {
                            $varRespuesta = Yii::$app->db->createCommand("select tipocortetc from tbl_tipocortes where tipocortetc like '%$varBancolombia%' and anulado = 0")->queryScalar(); 
                            $varIdCorte = Yii::$app->db->createCommand("select idtc from tbl_tipocortes where tipocortetc like '%$varBancolombia%' and anulado = 0")->queryScalar(); 
                        }else{
                            if ($txtWord2 == true) {
                                $varRespuesta = Yii::$app->db->createCommand("select tipocortetc from tbl_tipocortes where tipocortetc like '%$varDirectv%' and anulado = 0")->queryScalar(); 
                                $varIdCorte = Yii::$app->db->createCommand("select idtc from tbl_tipocortes where tipocortetc like '%$varDirectv%' and anulado = 0")->queryScalar(); 
                            }else{
                                if ($txtWord3 == true) {
                                    $varRespuesta = Yii::$app->db->createCommand("select tipocortetc from tbl_tipocortes where tipocortetc like '%$varGeneral%' and anulado = 0")->queryScalar(); 
                                    $varIdCorte = Yii::$app->db->createCommand("select idtc from tbl_tipocortes where tipocortetc like '%$varGeneral%' and anulado = 0")->queryScalar();
                                }
                            }
                        }

				Yii::$app->db->createCommand()->insert('tbl_control_procesos',[
				        			'evaluados_id' => $varEvaluadosId,
				        			'salario' => $varSalario,
				        			'tipo_corte' => $varRespuesta,
				        			'responsable' => $varResponsable,
				        			'cant_valor' => $varCantValor,
				        			'Dedic_valora' => $varDedicValor,
				        			'idtc' => $varIdCorte,
				        			'fechacreacion' => $varFecha,
				        			'anulado' => 0,
				        		])->execute();

				$fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$varTipoCorte'")->queryScalar();
				$fechafinC = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$varTipoCorte'")->queryScalar(); 


				$query = Yii::$app->db->createCommand("select * from tbl_control_params where evaluados_id = $varEvaluadosId and anulado = 0 and fechacreacion between '$fechainiC' and '$fechafinC'")->queryAll();


					   	foreach ($query as $key => $value) {
					       	$txtarbol = $value['arbol_id'];
					       	$txtdimensiones = $value['dimensions'];
					       	$txtcantvalor = $value['cant_valor'];
					       	$txtevaluados = $value['evaluados_id'];
					       	$txtargumentos = $value['argumentos'];
					       

				        	Yii::$app->db->createCommand()->insert('tbl_control_params',[
				        			'arbol_id' => $txtarbol,
				        			'dimensions' => $txtdimensiones,
				        			'cant_valor' => $txtcantvalor,
				        			'evaluados_id' => $txtevaluados,
				        			'argumentos' => $txtargumentos,
				        			'fechacreacion' => $varFecha,
				        			'anulado' => 0,
				        		])->execute();

				        }				        
			}
			
			$varResultados = 1;
			die(json_encode($varResultados));

		}



		public function actionUsuariolista($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => true];
                if (!is_null($search)) {
                    $data = \app\models\Usuarios::find()
                            ->select(['id' => 'tbl_usuarios.usua_id', 'text' => 'UPPER(usua_nombre)'])
                            ->where('usua_nombre LIKE "%' . $search . '%"')
                            ->orderBy('usua_nombre')
                            ->asArray()
                            ->all();
                    //agrego el usuario no definido solo para la visualizacion  en la inbox
                    //$data[] = ['id' => '1', 'text' => 'NO DEFINIDO'];
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Usuarios::find()
                            ->select(['id' => 'tbl_usuarios.usua_id', 'text' => 'UPPER(usua_nombre)'])
                            ->where('usua_usuario = "' . $id . '"')
                            ->asArray()
                            ->all();

                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

		public function actionDesvincular(){
			$model = new \app\models\ControlDesvincular();

         	$formData = Yii::$app->request->post();


         	if ($model->load($formData)) {
         		$varmotivo = $model->motivo;
         		$varevaluados_id = $model->evaluados_id;
         		$varcorreo = $model->correo;
         		$varsolicitante_id = $model->solicitante_id;
         		$varfechacreacion = $model->fechacreacion;

         		$varCoordinador = Yii::$app->db->createCommand("select distinct responsable from tbl_control_procesos where evaluados_id = $varevaluados_id and responsable is not null")->queryScalar();

         		Yii::$app->db->createCommand()->insert('tbl_control_desvincular',[
                                'solicitante_id' => $varsolicitante_id,
                                'evaluados_id' => $varevaluados_id,
                                'responsable' => $varCoordinador,
                                'motivo' => $varmotivo,
                                'correo' => $varcorreo,
                                'fechacreacion' => $varfechacreacion,
                                'anulado' => 0,
                            ])->execute(); 

         		$message = "<html><body>";
                $message .= "<h3>Existe nueva petición para desvincular el técnico de  equipo. Por favor verificar en QA, módulo -Peticiones Desvinculación Equipos-.</h3>";
                $message .= "</body></html>";


                return $this->redirect('index');
         	}

			return $this->renderAjax('_formdesvincular',[
					'model' => $model,
				]);
		}

		public function actionSelecciontecnico(){
			$model = new Controlparams();

			return $this->renderAjax('selecionartecnico',[
				'model' => $model,
				]);
		}

		public function actionSelecteduser(){
			$txtvarid = Yii::$app->request->get("txtvarid");
			$txtvarlider = Yii::$app->request->get("txtvarlider");
			$txtrta = 0;

			$varResponsable = Yii::$app->db->createCommand("select distinct responsable from tbl_control_procesos where evaluados_id = $txtvarid")->queryScalar();

			if ($varResponsable == $txtvarlider) {
				$txtrta = 2;
			}else{
				$varCountRespon = Yii::$app->db->createCommand("select count(responsable) from tbl_control_procesos where evaluados_id = $txtvarid")->queryScalar();

				if ($varCountRespon != 0) {
					$txtrta = 1;
				}else{
					$txtrta = 0;
				}
			}

			die(json_encode($txtrta));
		}

		public function actionLideruser(){
			$txtvarid = Yii::$app->request->get("txtvarid");

			$txtrta = Yii::$app->db->createCommand("select distinct tbl_usuarios.usua_nombre from tbl_usuarios inner join tbl_control_procesos on tbl_usuarios.usua_id = tbl_control_procesos.responsable where 		tbl_control_procesos.evaluados_id = $txtvarid")->queryScalar();

			die(json_encode($txtrta));
		}

		public function actionBuscaridtc(){
			$txtvarTipoCort2 = Yii::$app->request->get("txtvarTipoCort2");

			$txtrta = Yii::$app->db->createCommand("select tipocortetc from tbl_tipocortes where anulado = 0 and idtc = $txtvarTipoCort2")->queryScalar();

			die(json_encode($txtrta));
		}

		public function actionGuardarplan(){
			$txtvarValoradoid = Yii::$app->request->get("txtvarValoradoid");
			$txtvarvarTotal = Yii::$app->request->get("txtvarvarTotal");
			$txtvarDedicValor = Yii::$app->request->get("txtvarDedicValor");
			$txtvarTipoCort2 = Yii::$app->request->get("txtvarTipoCort2");
			$txtvarIdcortes = Yii::$app->request->get("txtvarIdcortes");
			$txtvFechacreacion = date("Y-m-d");

			Yii::$app->db->createCommand()->insert('tbl_control_procesos',[
                                'evaluados_id' => $txtvarValoradoid,
                                'salario' => null,
                                'tipo_corte' => $txtvarIdcortes,
                                'responsable' => Yii::$app->user->identity->id,
                                'cant_valor' => $txtvarvarTotal,
                                'Dedic_valora' => $txtvarDedicValor,
                                'idtc' => $txtvarTipoCort2,
                                'fechacreacion' => $txtvFechacreacion,
                                'anulado' => 0,
                            ])->execute(); 

			$txtrta = 1;
			die(json_encode($txtrta));

		}

		public function actionClonargrupo(){
			$txtvarCoordi = Yii::$app->request->get("txtvarCoordi");
			$txtrta = 0;
			$txtIdtc = 0;
			$varListProcesos = null;
			$varListParams = null;
			$txtvFechacreacion = date("Y-m-d");
            $txtGrupo = 0;
            $txtGrupoNombre = 0;

			$txtrta = Yii::$app->db->createCommand("select count(evaluados_id) from tbl_control_procesos where anulado = 0 and responsable = $txtvarCoordi")->queryScalar();

			if ($txtrta != 0) {
				$txtIdtc = Yii::$app->db->createCommand("select count(idtc) from tbl_control_procesos where anulado = 0 and responsable = $txtvarCoordi")->queryScalar();

				if ($txtIdtc >= 1) {
					$txtGrupo = Yii::$app->db->createCommand("select idgrupocorte from tbl_tipocortes t inner join tbl_control_procesos cp on t.idtc = cp.idtc where cp.anulado = 0 and cp.responsable = $txtvarCoordi")->queryScalar();

					$txtGrupoNombre = Yii::$app->db->createCommand("select tipocortetc from tbl_tipocortes where anulado = 0 and idgrupocorte = $txtGrupo")->queryScalar();

					$txtvaridtc = Yii::$app->db->createCommand("select idtc from tbl_tipocortes where anulado = 0 and idgrupocorte = $txtGrupo")->queryScalar();

					$varListProcesos = Yii::$app->db->createCommand("select * from tbl_control_procesos where anulado = 0 and responsable = $txtvarCoordi")->queryAll();

					foreach ($varListProcesos as $key => $value) {
						Yii::$app->db->createCommand()->insert('tbl_control_procesos',[
                                'evaluados_id' => $value['evaluados_id'],
                                'salario' => $value['salario'],
                                'tipo_corte' => $txtGrupoNombre,
                                'responsable' => $txtvarCoordi,
                                'cant_valor' => $value['cant_valor'],
                                'Dedic_valora' => $value['Dedic_valora'],
                                'idtc' => $txtvaridtc,
                                'fechacreacion' => $txtvFechacreacion,
                                'anulado' => 0,
                            ])->execute(); 

						$txtevaluado = $value['evaluados_id'];
						$varListParams = Yii::$app->db->createCommand("select * from tbl_control_params where anulado = 0 and evaluados_id = $txtevaluado")->queryAll();

						foreach ($varListParams as $key => $value) {
							Yii::$app->db->createCommand()->insert('tbl_control_params',[
                                'arbol_id' => $value['arbol_id'],
                                'dimensions' => $value['dimensions'],
                                'cant_valor' => $value['cant_valor'],
                                'evaluados_id' => $value['evaluados_id'],
                                'argumentos' => $value['argumentos'],
                                'fechacreacion' => $txtvFechacreacion,
                                'anulado' => 0,
                            ])->execute(); 
						}
					}

				}
			}

			die(json_encode($txtIdtc));
		}

		public function actionIndexeliminar($evaluadoID){
			$txtvarfechacreacion = date("Y-m-d");

			Yii::$app->db->createCommand()->delete('tbl_control_params', 'anulado = :param1 AND evaluados_id = :param2 AND fechacreacion >= :param3', array(':param2' => $evaluadoID, ':param1'=>0, ':param3' => $txtvarfechacreacion))->execute();

			return $this->redirect(['index']);
		}

		public function actionCreatefocalizada(){
			$txtvarcliente = Yii::$app->request->get("txtvarcliente");
          	$txtvarservicio = Yii::$app->request->get("txtvarservicio");
          	$txtvardimension = Yii::$app->request->get("txtvardimension");
          	$txtvarcantidad = Yii::$app->request->get("txtvarcantidad");
          	$txtvarusuario = Yii::$app->request->get("txtvarusuario");

          	Yii::$app->db->createCommand()->insert('tbl_control_focalizada',[
                                                           'arbol_id' => $txtvarcliente,
                                                           'cod_pcrc' => $txtvarservicio,
                                                           'dimensions' => $txtvardimension,
                                                           'cant_valor' => $txtvarcantidad,
                                                           'evaluados_id' => $txtvarusuario,
                                                           'argumentos' => null,
                                                           'fechacreacion' => date("Y-m-d"),
                                                           'anulado' => 0,
                                                        ])->execute();

          	die(json_encode($txtvarusuario));
		}

		public function actionCreatefocalizadaup(){
			$txtvarcliente = Yii::$app->request->get("txtvarcliente");
          	$txtvarservicio = Yii::$app->request->get("txtvarservicio");
          	$txtvardimension = Yii::$app->request->get("txtvardimension");
          	$txtvarcantidad = Yii::$app->request->get("txtvarcantidad");
          	$txtvarusuario = Yii::$app->request->get("txtvarusuario");
          	$txtvarfechacreacion = Yii::$app->request->get("txtvarfechacreacion");

          	Yii::$app->db->createCommand()->insert('tbl_control_focalizada',[
                                                           'arbol_id' => $txtvarcliente,
                                                           'cod_pcrc' => $txtvarservicio,
                                                           'dimensions' => $txtvardimension,
                                                           'cant_valor' => $txtvarcantidad,
                                                           'evaluados_id' => $txtvarusuario,
                                                           'argumentos' => null,
                                                           'fechacreacion' => $txtvarfechacreacion,
                                                           'anulado' => 0,
                                                        ])->execute();

          	die(json_encode($txtvarusuario));
		}

		public function actionUpdatespeech($id,$evaluadoId){
			$varevaluadoId = $evaluadoId;
			$varid = $id;

			$model = $this->findModelspeech($varid);
			if ($model->load(Yii::$app->request->post()) && $model->save()) {
			    Yii::$app->session->setFlash('success', Yii::t('app', 'Successful update!'));            
			    return $this->redirect(array('create','usua_id'=>$varevaluadoId));
			}

			return $this->render('updatespeech',[
				'model' => $model,
				'varevaluadoId' => $varevaluadoId,
				'varid' => $varid,
				]);
		}
		public function actionUpdatetwospeech($id,$varfecha,$evaluadoId){
			$varevaluadoId = $evaluadoId;
			$varid = $id;
			$varselectfecha = $varfecha;

			$varsoluciones = Yii::$app->db->createCommand("select id from tbl_control_procesos where anulado = 0 and evaluados_id = $varevaluadoId and fechacreacion = '$varselectfecha'")->queryScalar();			


			$model = $this->findModelspeech($varid);
			if ($model->load(Yii::$app->request->post()) && $model->save()) {
			    Yii::$app->session->setFlash('success', Yii::t('app', 'Successful update!'));            
			    return $this->redirect(array('update2','id'=>$varsoluciones,'evaluados_id'=>$varevaluadoId));
			}

			return $this->render('updatetwospeech',[
				'model' => $model,
				'varevaluadoId' => $varevaluadoId,
				'varid' => $varid,
				'varsoluciones'=> $varsoluciones,
				]);
		}
		protected function findModelspeech($varid){
	        if (($model = ControlFocalizada::findOne($varid)) !== null) {
	            return $model;
	        } else {
	            throw new NotFoundHttpException('The requested page does not exist.');
	        }
	    }

	    public function actionDeletespeech($id,$evaluadoId){
	    	$varevaluadoId = $evaluadoId;
			$varid = $id;

	    	Yii::$app->db->createCommand("delete from tbl_control_focalizada where idcontrolfocalizada = $varid")->execute();

	    	return $this->redirect(array('create','usua_id'=>$varevaluadoId));
	    }

	    public function actionDeletetwospeech($id,$varfecha,$evaluadoId){
	    	$varevaluadoId = $evaluadoId;
			$varid = $id;
			$varselectfecha = $varfecha;

			$varsoluciones = Yii::$app->db->createCommand("select id from tbl_control_procesos where anulado = 0 and evaluados_id = $varevaluadoId and fechacreacion = '$varselectfecha'")->queryScalar();	

	    	Yii::$app->db->createCommand("delete from tbl_control_focalizada where idcontrolfocalizada = $varid")->execute();

	    	return $this->redirect(array('update2','id'=>$varsoluciones,'evaluados_id'=>$varevaluadoId));
	    }



	}
